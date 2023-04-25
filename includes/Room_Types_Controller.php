<?php
namespace RoomReservationApp\Includes;

class Room_Types_Controller
{
	const use_table = 'table1'; // `rr_room_types` table

	public static function form()
	{
		// Add new record
		if (isset($_POST['add-record'])) self::process_add();
		
		// Update record
		if (isset($_POST['update-record'])) self::process_update();


		// ****************************************************************************************

		$title = 'Add Room Type';
		$type = 'add-record'; // Add New Record trigger
		$button_title = 'Add New Room Type';
		$record_id = '';

		if (isset($_GET['action']) && $_GET['action'] == 'edit') {
			if (isset($_GET['frominsert'])) echo Common_Helper::alert_message('success', 'Record saved');
			$id = (int) $_GET['record'];
			$input = self::find($id);
			$title = 'Edit Room Type <a href="?page='.$_GET['page'].'" class="page-title-action">Add New</a>';
			$type = 'update-record'; // Update Record trigger
			$button_title = 'Update Record';
			$record_id = '<input type="hidden" name="record_id" value="'.$id.'" />';
		}

		// View file
		include(JMN_RR_DIR.'pages/forms/room_type.php');
	}

	private static function process_add()
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$values = array(
			null,
			$_POST['room_type'],
			$_POST['description'],
			null,
			null,
		);
		$query = "INSERT INTO `$table` VALUES (%s,%s,%s,%s,%s)";
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
				'room_type' => $_POST['room_type'],
				'description' => $_POST['description'],
			),
			'data_type' => array('%s','%s'),
		);
		$q = $wpdb->update(
			$table,
			$values['data'],
			array('id' => $id),
			$values['data_type'],
			array('%d')
		);
		if ($q) echo Common_Helper::alert_message('success', 'Record saved');
	}

	private static function find($id)
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$query = "SELECT * FROM `$table` WHERE id=%d";
		return $wpdb->get_results($wpdb->prepare($query, array($id)))[0];
	}

}
