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
		SELECT re.*, r.qty, rt.room_type FROM `$reservations` re 
		JOIN `{$rooms}` r ON re.room_id = r.id 
		JOIN `{$room_types}` rt ON rt.id = r.room_type_id 
		WHERE re.id=%d";
		$data = $wpdb->get_results($wpdb->prepare($query, array($id)))[0];

		include(JMN_RR_DIR.'pages/forms/view_reservation.php');
	}

	public static function view_by_confirmation()
	{
		global $wpdb;

		$c_code = $_GET['record'];

		$rooms = Plugin_Tables::tables('table2'); // `rr_rooms` table
		$room_types = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$reservations = Plugin_Tables::tables('table3'); // `rr_room_reservations` table

		$query = "
		SELECT re.*, r.qty, rt.room_type FROM `$reservations` re 
		JOIN `{$rooms}` r ON re.room_id = r.id 
		JOIN `{$room_types}` rt ON rt.id = r.room_type_id 
		WHERE re.confirmation_code=%s";
		$data = $wpdb->get_results($wpdb->prepare($query, array($c_code)));

		include(JMN_RR_DIR.'pages/forms/view_by_confirmation.php');
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
		if ($q) echo Common_Helper::alert_message('success', "Reservation status successfully updated to {$status}");
	}

	public static function get_available_rooms()
	{
		global $wpdb;

		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
		$values = array(
			$start_date,
			$end_date,
			$start_date,
			$end_date,
		);

		$rooms = Plugin_Tables::tables('table2'); // `rr_rooms` table
		$room_types = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$reservations = Plugin_Tables::tables('table3'); // `rr_room_reservations` table
		$query = "
		SELECT r.*, (IFNULL(r.qty, 0) - SUM(IFNULL(re.qty, 0))) AS remaining, rt.room_type, re.room_id, re.arrival, re.departure 
		FROM {$rooms} r 
		LEFT JOIN {$room_types} rt ON r.room_type_id = rt.id 
		LEFT JOIN {$reservations} re ON r.id = re.room_id AND re.status IN ('active','paid') AND ((re.arrival BETWEEN %s AND %s) OR (re.departure BETWEEN %s AND %s)) 
		GROUP BY r.id
		HAVING (IFNULL(r.qty, 0) - SUM(IFNULL(re.qty, 0))) > 0";

		$data = $wpdb->get_results($wpdb->prepare($query, $values));
		$data_with_key = Common_Helper::array_set_key($data, 'id');

		// Re-check cart if chosen room is still available. on change of date on Check availability form
		if ( ! empty($_SESSION['reservation_cart']))
		{
			$selected_rooms = array();
			foreach ($_SESSION['reservation_cart'] as $cart_key => $cart_item)
			{
				// stack all available selected rooms
				if (array_key_exists($cart_item['id'], $data_with_key))
				{
					$data_get = $data_with_key[$cart_item['id']];
					if ($data_get['remaining'] > 0)
					{
						$selected_rooms[] = $cart_item;
						$data_with_key[$cart_item['id']]['remaining'] -= 1; // subtract 1

						if ($data_with_key[$cart_item['id']]['remaining'] <= 0)
						{
							unset($data_with_key[$cart_item['id']]);
						}
					}
				}
			}
			$_SESSION['reservation_cart'] = $selected_rooms;
		}

		return $data_with_key;
	}

	public static function get_checkout_confirm_data()
	{
		global $wpdb;

		$c_code = $_GET['confirmation'];

		$rooms = Plugin_Tables::tables('table2'); // `rr_rooms` table
		$room_types = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$reservations = Plugin_Tables::tables('table3'); // `rr_room_reservations` table

		$query = "
		SELECT re.*, re.qty AS reserved_qty, rt.room_type FROM `$reservations` re 
		JOIN `{$rooms}` r ON re.room_id = r.id 
		JOIN `{$room_types}` rt ON rt.id = r.room_type_id 
		WHERE re.confirmation_code=%s";

		return $wpdb->get_results($wpdb->prepare($query, array($c_code)));
	}

	public static function save_checkout()
	{
		if (empty($_SESSION['reservation_cart']))
		{
			die('Invalid request');	
		}

		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$city = $_POST['city'];
		$address = $_POST['address'];
		$country = $_POST['country'];
		$email = $_POST['email'];
		$contact_no = $_POST['contact_no'];
		$arrival = $_POST['start_date'];
		$departure = $_POST['end_date'];

		$number_of_nights = Common_Helper::get_num_nights($arrival, $departure);
		$status = 'active';
		$confirmation_code = Common_Helper::create_confirmation_code();

		global $wpdb;
		$table = Plugin_Tables::tables('table3'); // `rr_room_reservations` table
		$data_cart = $_SESSION['reservation_cart'];
		$data_count = count($data_cart);
		$insert_count = 0;
		foreach ($data_cart as $item)
		{
			$data_vals = array(
				null, // id AI
				$item['id'],
				$first_name,
				$last_name,
				$city,
				$address,
				$country,
				$email,
				$contact_no,
				$arrival,
				$departure,
				$number_of_nights,
				$item['rate'] * $number_of_nights,
				$item['reserve_qty'],
				$status,
				$confirmation_code,
				date('Y-m-d H:i:s'), // created_at
				date('Y-m-d H:i:s'), // updated_at
			);
			$data_keys = array(
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
			);
			$data_keys = implode(',', $data_keys);

			$query = "INSERT INTO `$table` VALUES ({$data_keys})";

			if ($wpdb->query($wpdb->prepare($query, $data_vals)))
			{
				$insert_count++;
			}
		}

		$data_return = array(
			'success' => ($insert_count == $data_count),
			'confirmation_code' => $confirmation_code
		);

		return $data_return;
	}

	public static function recheck_cart($start_date, $end_date)
	{
		global $wpdb;

		$values = array(
			$start_date,
			$end_date,
			$start_date,
			$end_date,
		);

		$selected_room_ids = array_column($_SESSION['reservation_cart'], 'id');
		$selected_room_ids_str = implode(',', $selected_room_ids);

		$rooms = Plugin_Tables::tables('table2'); // `rr_rooms` table
		$room_types = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$reservations = Plugin_Tables::tables('table3'); // `rr_room_reservations` table
		$query = "
		SELECT r.*, (IFNULL(r.qty, 0) - SUM(IFNULL(re.qty, 0))) AS remaining, rt.room_type, re.room_id, re.arrival, re.departure 
		FROM {$rooms} r 
		LEFT JOIN {$room_types} rt ON r.room_type_id = rt.id 
		LEFT JOIN {$reservations} re ON r.id = re.room_id AND re.status IN ('active','paid') AND ((re.arrival BETWEEN %s AND %s) OR (re.departure BETWEEN %s AND %s)) 
		WHERE r.id IN ({$selected_room_ids_str}) 
		GROUP BY r.id
		HAVING (IFNULL(r.qty, 0) - SUM(IFNULL(re.qty, 0))) > 0";

		$data = $wpdb->get_results($wpdb->prepare($query, $values));
		$data_with_key = Common_Helper::array_set_key($data, 'id');

		// Re-check cart if chosen room is still available. on change of date on Check availability form
		$selected_rooms = array();
		$conflict_count = 0;
		foreach ($_SESSION['reservation_cart'] as $cart_key => $cart_item)
		{
			// stack all available selected rooms
			if (array_key_exists($cart_item['id'], $data_with_key))
			{
				$data_get = $data_with_key[$cart_item['id']];
				if ($data_get['remaining'] > 0)
				{
					$selected_rooms[] = $cart_item;
					$data_with_key[$cart_item['id']]['remaining'] -= 1; // subtract 1
				}
				else
				{
					$conflict_count++;
				}
			}
		}
		$_SESSION['reservation_cart'] = $selected_rooms;

		return $conflict_count;
	}
}
