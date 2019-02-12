<?php
namespace RoomReservationApp\Includes;

class Common_Helper
{
	public static function array_set_key($datas, $key)
	{
		if ( ! empty($datas))
		{
			$return = array();
			foreach ($datas as $data)
			{
				$data = (array) $data;
				$return[$data[$key]] = $data;
			}
			return $return;
		}
		return false;
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

	public static function alert_message($type = 'success', $message = 'Process successfull')
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