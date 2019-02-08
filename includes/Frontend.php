<?php
namespace RoomReservationApp\Includes;

class Frontend
{
	public static function view()
	{
		if (isset($_POST['submit_checkout']))
		{
			self::submit_checkout();
		}
		else if (isset($_GET['checkout_confirm'], $_GET['confirmation']))
		{
			self::checkout_confirm();
		}
		else if (isset($_GET['checkoutroom'], $_GET['start_date'], $_GET['end_date']))
		{
			self::checkout_room();
		}
		else
		{
			include(JMN_RR_DIR.'pages/frontend/check_availability_form.php');

			if (isset($_GET['reset_room_cart']))
			{
				self::reset_room_cart();
			}
			else if (isset($_GET['check_room_availability'], $_GET['start_date'], $_GET['end_date']))
			{
				self::check_room_availability();
			}
		}
	}

	public static function check_room_availability()
	{
		session_start();

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
		SELECT r.*, rt.room_type, re.room_id, re.arrival, re.departure 
		FROM {$rooms} r 
		LEFT JOIN {$room_types} rt ON r.room_type_id = rt.id 
		LEFT JOIN {$reservations} re ON r.id = re.room_id AND re.status IN ('active','paid') AND ((re.arrival BETWEEN %s AND %s) OR (re.departure BETWEEN %s AND %s)) 
		WHERE re.room_id IS NULL";

		$data = $wpdb->get_results($wpdb->prepare($query, $values));

		// Re-check cart if chosen room is still available. on change of date on Check availability form
		if ( ! empty($_SESSION['reservation_cart']))
		{
			$data_array = json_decode(json_encode($data), true);
			$data_ids = array_column($data_array, 'id');
			$selected_rooms = array();
			foreach ($_SESSION['reservation_cart'] as $cart_key => $cart_item)
			{
				// stack all available selected rooms
				if (in_array($cart_item['id'], $data_ids))
				{
					$selected_rooms[] = $cart_item;
				}
			}
			$_SESSION['reservation_cart'] = $selected_rooms;

			if ( ! empty($selected_rooms))
			{
				$selected_rooms_id = array_column($selected_rooms, 'id');
				$selected_rooms_str = implode(',', $selected_rooms_id);

				$query = "
				SELECT r.*, rt.room_type, re.room_id, re.arrival, re.departure 
				FROM {$rooms} r 
				LEFT JOIN {$room_types} rt ON r.room_type_id = rt.id 
				LEFT JOIN {$reservations} re ON r.id = re.room_id AND re.status IN ('active','paid') AND ((re.arrival BETWEEN %s AND %s) OR (re.departure BETWEEN %s AND %s)) 
				WHERE r.id NOT IN ({$selected_rooms_str}) AND re.room_id IS NULL";

				$data = $wpdb->get_results($wpdb->prepare($query, $values));
			}
		}

		include(JMN_RR_DIR.'pages/frontend/available_rooms.php');
	}

	public static function reset_room_cart()
	{
		session_start();
		$_SESSION['reservation_cart'] = [];
		$url = $_GET;
		unset($url['reset_room_cart']);

		$url_param = array();
		foreach ($url as $key => $value) $url_param[] = $key.'='.$value;

		$location = '?'.implode('&', $url_param);
		header('location:'.$location);
	}

	public static function checkout_room()
	{
		session_start();
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
		include(JMN_RR_DIR.'pages/frontend/checkout.php');
	}

	public static function submit_checkout()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			die('Invalid request');
		}

		session_start();

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

		$number_of_nights = self::get_num_nights($arrival, $departure);
		$status = 'active';
		$confirmation_code = self::create_confirmation_code();

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

		if ($insert_count == $data_count)
		{
			// Success
			
			$_SESSION['reservation_cart'] = [];

			header('location:?checkout_confirm=true&confirmation='.$confirmation_code);
		}
	}

	public static function checkout_confirm()
	{
		global $wpdb;

		$c_code = $_GET['confirmation'];

		$rooms = Plugin_Tables::tables('table2'); // `rr_rooms` table
		$room_types = Plugin_Tables::tables('table1'); // `rr_room_types` table
		$reservations = Plugin_Tables::tables('table3'); // `rr_room_reservations` table

		$query = "
		SELECT re.*, r.room_number, rt.room_type FROM `$reservations` re 
		JOIN `{$rooms}` r ON re.room_id = r.id 
		JOIN `{$room_types}` rt ON rt.id = r.room_type_id 
		WHERE re.confirmation_code=%s";
		$data = $wpdb->get_results($wpdb->prepare($query, array($c_code)));

		include(JMN_RR_DIR.'pages/frontend/room-reserve.php');
	}

	public static function create_confirmation_code()
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= 10)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}

	public static function get_num_nights($start, $end)
	{
		$u_start_date = strtotime($start);
		$u_end_date = strtotime($end);
		$date_diff = $u_end_date - $u_start_date;
		$total_days = date('j', $date_diff);

		$number_of_nights = $total_days - 1;

		return $number_of_nights;
	}

	public static function widgetView($settings = array())
	{
		global $wpdb;

		$display = 5;
		if ( ! empty($settings))
		{
			if (isset($settings['display']) && is_int($settings['display']))
			{
				$display = $settings['display'];
			}
		}

		$table = Plugin_Tables::tables('table2');
		$query = "SELECT * FROM `$table`";

		if (isset($display) && $display > 0)
		{
			$query .= " LIMIT {$display}";
		}

		$data = $wpdb->get_results($query);

		// View file
		include(JMN_RR_DIR.'pages/frontend_widget.php');
	}

}