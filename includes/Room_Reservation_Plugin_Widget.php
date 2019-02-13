<?php
namespace RoomReservationApp\Includes;

use WP_Widget;

class Room_Reservation_Plugin_Widget extends WP_Widget
{
	public function __construct () {
		$widget_options = array(
			'classname' => '', // css
			'description' => 'Room reservation plugin for hotels and etc..'
		);
		$this->WP_Widget('jmn_rr_plugin', 'Room Reservation Plugin', $widget_options);
	}

	// Show form
	public function form($instance) {
		global $wpdb;
		$defaults = array(
			'theplugintitle' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$theplugintitle = esc_attr($instance['theplugintitle']);
		echo '<p>Title : <input type="text" class="widefat" name="'.$this->get_field_name('theplugintitle').'" value="'.$theplugintitle.'" /></p>';
	}

	// Save form
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['theplugintitle'] = strip_tags($new_instance['theplugintitle']);
		return $instance;
	}

	// Show widget in page
	public function widget($args, $instance) {
		global $wpdb;
		
		extract($args);
		$theplugintitle = apply_filters('widget_title', $instance['theplugintitle']);

		echo $before_widget;
		echo $before_title.$theplugintitle.$after_title;

		/* widget content */
		Frontend::widgetView();

		echo $after_widget;
	}
}