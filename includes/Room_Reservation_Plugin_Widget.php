<?php
namespace RoomReservationApp\Includes;

use WP_Widget;

class Room_Reservation_Plugin_Widget extends WP_Widget
{
	public function __construct () {
		$widget_options = array(
			'classname' => '', // css
			'description' => 'Plugin description'
		);
		$this->WP_Widget('new_plugin', 'Start Plugin', $widget_options);
	}

	// Show form
	public function form($instance) {
		global $wpdb;
		$defaults = array(
			'theplugintitle' => '',
			'settings_display' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$theplugintitle = esc_attr($instance['theplugintitle']);
		$settings_display = esc_attr($instance['settings_display']);
		echo '<p>Title : <input type="text" class="widefat" name="'.$this->get_field_name('theplugintitle').'" value="'.$theplugintitle.'" /></p>';
		echo '<p>Display # : <input type="text" class="widefat" name="'.$this->get_field_name('settings_display').'" value="'.$settings_display.'" /></p>';
	}

	// Save form
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['theplugintitle'] = strip_tags($new_instance['theplugintitle']);
		$instance['settings_display'] = strip_tags($new_instance['settings_display']);		
		return $instance;
	}

	// Show widget in page
	public function widget($args, $instance) {
		global $wpdb;
		extract($args);
		$theplugintitle = apply_filters('widget_title', $instance['theplugintitle']);
		$settings_display = apply_filters('widget_title', $instance['settings_display']);

		echo $before_widget;
		echo $before_title.$theplugintitle.$after_title;

		$settings = array(
			'display' => intval($settings_display)
		);

		/* widget content */
		Frontend::widgetView($settings);

		echo $after_widget;
	}
}