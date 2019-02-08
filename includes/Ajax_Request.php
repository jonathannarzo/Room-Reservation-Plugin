<?php
namespace RoomReservationApp\Includes;

class Ajax_Request
{
	public static function process_ajax()
	{
		ob_clean();

		if (isset($_POST['process_method']))
		{
			$process_method = $_POST['process_method'];
		}
		else if (isset($_GET['process_method']))
		{
			$process_method = $_GET['process_method'];
		}
		else
		{
			$process_method = null;
		}

		if ( ! is_null($process_method) && method_exists(__CLASS__, $process_method))
		{
			call_user_func("self::{$process_method}");
		}
		else
		{
			echo 'Invalid.';
		}

		die();
	}

	private static function process_name()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			die('Invalid request method.');
		}

		echo $_POST['pangalan'];
	}

	public static function add_to_cart()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			die('Invalid request');
		}

		session_start();

		$data = $_POST['data'];

		if ( ! isset($_SESSION['reservation_cart']))
		{
			$_SESSION['reservation_cart'] = array();
		}
		else
		{
			$_SESSION['reservation_cart'][] = $data;
		}
		
		$result = array(
			'id' => $data['id'],
			'success' => true,
		);

		echo json_encode($result);
		exit;
	}

	public static function get_room_cart()
	{
		session_start();

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];

		include(JMN_RR_DIR.'pages/frontend/room_cart.php');
	}

	public static function remove_room_from_cart()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			die('Invalid request');
		}

		session_start();

		$key_val = $_POST['key'];
		$selected_rooms = $_SESSION['reservation_cart'];
		$room_id = $selected_rooms[$key_val]['id'];
		
		unset($selected_rooms[$key_val]);
		
		$_SESSION['reservation_cart'] = $selected_rooms;

		$result = array(
			'success' => true,
			'room_id' => $room_id,
		);

		echo json_encode($result);
		exit;
	}

}