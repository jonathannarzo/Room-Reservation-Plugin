<?php
namespace RoomReservationApp\Includes;

class Plugin_Tables
{

	public static function tables($key = '')
	{
		global $wpdb;
		$tables = array(
			'table1' => "{$wpdb->prefix}rr_room_types",
			'table2' => "{$wpdb->prefix}rr_rooms",
			'table3' => "{$wpdb->prefix}rr_room_reservations",
			'table4' => "{$wpdb->prefix}rr_room_reservation_settings"
		);
		return empty($key) ? $tables : $tables[$key];
	}

	private function get_create_tbl_query($table, $key)
	{
		$queries = array();
		$queries['table1'] = "CREATE TABLE `$table` (
			`id` int (11) NOT NULL AUTO_INCREMENT,
			`room_type` varchar(64) NOT NULL,
			`description` varchar(128) NOT NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		);";

		$queries['table2'] = "CREATE TABLE `$table` (
			`id` int (11) NOT NULL AUTO_INCREMENT,
			`room_type_id` int(11) NOT NULL,
			`description` varchar(128) NOT NULL,
			`rate` decimal(10,2) NOT NULL,
			`qty` int(11) NOT NULL,
			`image` text NOT NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		);";

		$queries['table3'] = "CREATE TABLE `$table` (
			`id` int (11) NOT NULL AUTO_INCREMENT,
			`room_id` int(11) NOT NULL,
			`first_name` varchar(64) NOT NULL,
			`last_name` varchar(64) NOT NULL,
			`city` varchar(32) NOT NULL,
			`address` varchar(64) NOT NULL,
			`country` varchar(32) NOT NULL,
			`email` varchar(64) NOT NULL,
			`contact_no` varchar(32) NOT NULL,
			`arrival` date NOT NULL,
			`departure` date NOT NULL,
			`number_of_nights` int(11) NOT NULL,
			`rate` decimal(10,2) NOT NULL,
			`qty` int(11) NOT NULL,
			`status` varchar(16) NOT NULL DEFAULT 'active',
			`confirmation_code` varchar(16) NOT NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		);";

		$queries['table4'] = "CREATE TABLE `$table` (
			`id` int (11) NOT NULL AUTO_INCREMENT,
			`booking_page` int (11) NOT NULL,
			`paypal_email` varchar(128) NOT NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		);";
		return isset($queries[$key]) ? $queries[$key] : null;
	}

	public static function init_tables()
	{
		global $wpdb;
		$tables = self::tables();
		if (empty($tables)) die('No database tables found for the plugin.');

		foreach ($tables as $key => $table) {
			if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
				$table_query = self::get_create_tbl_query($table, $key);
				if ($table_query !== null) self::create_table($table_query);
				else die("Create table query for $table is not defined.");
			}
		}
	}

	private function create_table($sql)
	{
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function drop_tables()
	{
		global $wpdb;
		$tables = self::tables();
		foreach ($tables as $table) {
			if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
				$sql = "DROP TABLE `$table`;";
				$wpdb->query($sql);
			}
		}
	}

}