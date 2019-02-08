<?php
namespace RoomReservationApp\Includes;

class Rooms_Controller
{
	const use_table = 'table2'; // `rr_rooms` table

	public static function form()
	{
		// Add new record
		if (isset($_POST['add-record'])) self::process_add();
		
		// Update record
		if (isset($_POST['update-record'])) self::process_update();


		// ****************************************************************************************

		$title = 'Add Room';
		$type = 'add-record'; // Add New Record trigger
		$button_title = 'Add New Room';
		$record_id = '';
		$room_types = self::getRoomTypes();

		if (isset($_GET['action']) && $_GET['action'] == 'edit') {
			if (isset($_GET['frominsert'])) echo self::alert_message('success', 'Record saved');
			$id = (int) $_GET['record'];
			$input = self::find($id);
			$title = 'Edit Room <a href="?page='.$_GET['page'].'" class="page-title-action">Add New</a>';
			$type = 'update-record'; // Update Record trigger
			$button_title = 'Update Record';
			$record_id = '<input type="hidden" name="record_id" value="'.$id.'" />';
		}

		// View file
		include(JMN_RR_DIR.'pages/forms/room.php');
	}

	private static function process_add()
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$values = array(
			null,
			$_POST['room_type_id'],
			$_POST['description'],
			$_POST['rate'],
			$_POST['room_number'],
			$_POST['image'],
			null,
			null,
		);
		$query = "INSERT INTO `$table` VALUES (%s,%d,%s,%s,%s,%s,%s,%s)";
		if ($wpdb->query($wpdb->prepare($query, $values))) {
			$record_id = (int) $wpdb->insert_id;
			$record_url = '?page='.esc_attr($_REQUEST['page']).'&action=edit&record='.$record_id.'&frominsert=true';
			wp_redirect($record_url);
			exit;
		}
	}

	private static function process_update()
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$id = (int) $_POST['record_id'];
		$values = array(
			'data' => array(
				'room_type_id' => $_POST['room_type_id'],
				'description' => $_POST['description'],
				'rate' => $_POST['rate'],
				'room_number' => $_POST['room_number'],
				'image' => $_POST['image'],
			),
			'data_type' => array('%d','%s','%s','%s','%s'),
		);
		$q = $wpdb->update(
			$table,
			$values['data'],
			array('id' => $id),
			$values['data_type'],
			array('%d')
		);
		if ($q) echo self::alert_message('success', 'Record saved');
	}

	private static function find($id)
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$query = "SELECT * FROM `$table` WHERE id=%d";
		return $wpdb->get_results($wpdb->prepare($query, array($id)))[0];
	}

	private static function alert_message($type = 'success', $message = 'Process successfull')
	{
		$class = 'notice-success';
		$type_title = 'Success';
		if ($type == 'error') {
			$class = 'notice-error';
			$type_title = 'Error';
		}
		return "<div class='notice $class is-dismissible'><p><b>{$type_title}:</b> {$message}</p></div>";
	}

	private static function getRoomTypes()
	{
		global $wpdb;
		$table = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$query = "SELECT * FROM `$table`";
		return $wpdb->get_results($query);
	}
}