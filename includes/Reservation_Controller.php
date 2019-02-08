<?php
namespace RoomReservationApp\Includes;

class Reservation_Controller
{

	public static function view_reservation()
	{
		global $wpdb;

		$id = $_GET['record'];

		$rooms = Plugin_Tables::tables('table2'); // `rr_rooms` table
		$room_types = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$reservations = Plugin_Tables::tables('table3'); // `rr_room_reservations` table

		$query = "
		SELECT re.*, r.room_number, rt.room_type FROM `$reservations` re 
		JOIN `{$rooms}` r ON re.room_id = r.id 
		JOIN `{$room_types}` rt ON rt.id = r.room_type_id 
		WHERE re.id=%d";
		$data = $wpdb->get_results($wpdb->prepare($query, array($id)))[0];

		include(JMN_RR_DIR.'pages/backend_forms/view_reservation.php');
	}

	public static function view_by_confirmation()
	{
		global $wpdb;

		$c_code = $_GET['record'];

		$rooms = Plugin_Tables::tables('table2'); // `rr_rooms` table
		$room_types = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$reservations = Plugin_Tables::tables('table3'); // `rr_room_reservations` table

		$query = "
		SELECT re.*, r.room_number, rt.room_type FROM `$reservations` re 
		JOIN `{$rooms}` r ON re.room_id = r.id 
		JOIN `{$room_types}` rt ON rt.id = r.room_type_id 
		WHERE re.confirmation_code=%s";
		$data = $wpdb->get_results($wpdb->prepare($query, array($c_code)));

		include(JMN_RR_DIR.'pages/backend_forms/view_by_confirmation.php');
	}

	public static function update_status()
	{
		global $wpdb;
		$table = Plugin_Tables::tables('table3'); // `rr_room_reservations` table
		$status = $_GET['action'];
		$id = (int) $_GET['record'];
		$values = array(
			'data' => array(
				'status' => $status,
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
		if ($q) echo self::alert_message('success', "Reservation status successfully updated to {$status}");
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
}