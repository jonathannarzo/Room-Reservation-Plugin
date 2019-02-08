<?php
/**
 * Plugin Name: Room Reservation Plugin
 * Plugin URI: http://github.com/jonathannarzo
 * Description: Room reservation plugin for hotel
 * Version: 1.0
 * Author: Jonathan Narzo
 * Author URI: http://github.com/jonathannarzo
 */

if (!defined('WPINC')) die;

DEFINE('JMN_RR_DIR', plugin_dir_path( __FILE__ ));
DEFINE('JMN_RR_URL', plugins_url('/', __FILE__));
DEFINE('JMN_RR_PLUGIN_SHORTCODE', 'room_reservation_plugin');

# to identifying if user is logged-in or not
include(ABSPATH . 'wp-includes/pluggable.php');

# includes
spl_autoload_register(function ($class) {
    $parts = explode('\\', $class);
    $file = JMN_RR_DIR .'includes/'. end($parts) . '.php';
    if (file_exists($file)) include $file;
});

# Plugin Activation
register_activation_hook(__FILE__, 'RoomReservationApp\Includes\Room_Reservation::activate_plugin');

# Plugin Deactivation
register_deactivation_hook(__FILE__, 'RoomReservationApp\Includes\Room_Reservation::deactivate_plugin');

# Start plugin
add_action('plugins_loaded', function () { RoomReservationApp\Includes\Room_Reservation::get_instance(); });

add_action('widgets_init', 'RoomReservationApp\Includes\Room_Reservation::widget_view');