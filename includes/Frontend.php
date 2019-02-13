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
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];

		$data = Reservation_Controller::get_available_rooms();

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
		
		$conflict_count = Reservation_Controller::recheck_cart($start_date, $end_date);

		include(JMN_RR_DIR.'pages/frontend/checkout.php');
	}

	public static function submit_checkout()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			die('Invalid request');
		}

		session_start();

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		$conflict_count = Reservation_Controller::recheck_cart($start_date, $end_date);

		if ($conflict_count > 0 && ! isset($_POST['confirmed']))
		{

		}

		$save_checkout = Reservation_Controller::save_checkout();

		if ($save_checkout['success'])
		{
			$_SESSION['reservation_cart'] = [];

			header('location:?checkout_confirm=true&confirmation='.$save_checkout['confirmation_code']);
		}
	}

	public static function checkout_confirm()
	{
		$data = Reservation_Controller::get_checkout_confirm_data();

		$settings = Settings_Controller::find();
		
		include(JMN_RR_DIR.'pages/frontend/room-reserve.php');
	}

	public static function widgetView()
	{
		global $wpdb;

		$settings = Settings_Controller::find();



		// View file
		include(JMN_RR_DIR.'pages/frontend/frontend_widget.php');
	}

}