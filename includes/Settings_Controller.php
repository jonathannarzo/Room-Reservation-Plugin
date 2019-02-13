<?php
namespace RoomReservationApp\Includes;

class Settings_Controller
{
	const use_table = 'table4'; // `rr_rooms` table

	public static function form()
	{
		// Add new record
		if (isset($_POST['add-record'])) self::process_add();
		
		// Update record
		if (isset($_POST['update-record'])) self::process_update();


		// ****************************************************************************************

		$title = 'Settings';
		$type = 'add-record'; // Add New Record trigger
		$button_title = 'Save Settings';
		$record_id = '';

		$all_pages = get_pages();

		$input = self::find();

		if ((isset($_GET['action']) && $_GET['action'] == 'edit') || ! empty($input)) {
			if (isset($_GET['frominsert'])) echo Common_Helper::alert_message('success', 'Settings saved');
			$id = (int) (isset($_GET['record'])) ? $_GET['record'] : $input->id;
			
			$title = 'Edit Settings';
			$type = 'update-record'; // Update Record trigger
			$button_title = 'Update Settings';
			$record_id = '<input type="hidden" name="record_id" value="'.$id.'" />';
		}

		// View file
		include(JMN_RR_DIR.'pages/forms/settings.php');
	}

	private static function process_add($redirect = true)
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$values = array(
			null,
			$_POST['booking_page'],
			$_POST['paypal_email'],
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
		);
		$query = "INSERT INTO `$table` VALUES (%s,%s,%s,%s,%s)";
		if ($wpdb->query($wpdb->prepare($query, $values))) {
			$record_id = (int) $wpdb->insert_id;
			$record_url = '?page='.esc_attr($_REQUEST['page']).'&action=edit&record='.$record_id.'&frominsert=true';
			if ($redirect)
			{
				wp_redirect($record_url);
				exit;
			}
		}
	}

	private static function process_update()
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$id = (int) $_POST['record_id'];
		$values = array(
			'data' => array(
				'booking_page' => $_POST['booking_page'],
				'paypal_email' => $_POST['paypal_email']
			),
			'data_type' => array('%s'),
		);
		$q = $wpdb->update(
			$table,
			$values['data'],
			array('id' => $id),
			$values['data_type'],
			array('%d')
		);
		if ($q) echo Common_Helper::alert_message('success', 'Settings saved');
	}

	public static function find()
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$query = "SELECT * FROM `$table`";
		$result = $wpdb->get_results($query);
		return (count($result) > 0) ? $result[0] : array();
	}

	public static function create_settings($booking_page, $email)
	{
		$_POST['booking_page'] = $booking_page;
		$_POST['paypal_email'] = $email;
		self::process_add(false);
	}
}