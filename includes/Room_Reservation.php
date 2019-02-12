<?php
namespace RoomReservationApp\Includes;

class Room_Reservation
{
	static $instance;
	public $records_obj; // Records WP_List_Table object

	public function __construct()
	{
		ob_start(); // Fix header error
		add_filter('set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3);
		add_action('admin_menu', [$this, 'plugin_menu']);
		add_shortcode(JMN_RR_PLUGIN_SHORTCODE, array(__CLASS__, 'shortcode_view'));

		# Styles and Scripts
		if (is_admin()) add_action('admin_enqueue_scripts', array(__CLASS__, 'load_scripts'));
		else add_action('wp', array(__CLASS__, 'load_scripts'));

		# Ajax
		if (is_user_logged_in()) add_action('wp_ajax_plugin_ajax', 'RoomReservationApp\Includes\Ajax_Request::process_ajax');
		else add_action('wp_ajax_nopriv_plugin_ajax', 'RoomReservationApp\Includes\Ajax_Request::process_ajax');
	}

	public static function set_screen($status, $option, $value)
	{
		return $value;
	}

	public static function get_instance()
	{
		if (!isset( self::$instance )) self::$instance = new self();
		return self::$instance;
	}

	public static function activate_plugin()
	{
		Plugin_Tables::init_tables();
	}

	public static function deactivate_plugin() {}

	public function plugin_menu()
	{
		add_menu_page('Room Reservation','Room Reservation','manage_options','jmnrrmenu', array($this, 'rooms_page'), 'dashicons-calendar');
		
		$hook = add_submenu_page('jmnrrmenu', 'List of Rooms', 'Rooms', 'manage_options', 'jmnrrmenu', array($this, 'rooms_page'));
		add_action("load-$hook", array($this, 'so_rooms_page'));

		add_submenu_page('jmnrrmenu', '', 'Add Room', 'manage_options', 'jmnrrmenu_form', 'RoomReservationApp\Includes\Rooms_Controller::form');

		
		$hook = add_submenu_page('jmnrrmenu', 'List of Room Types', 'Room Types', 'manage_options', 'jmnrrmenu2', array($this, 'room_types_page'));
		add_action("load-$hook", array($this, 'so_room_types_page'));

		add_submenu_page('jmnrrmenu', '', 'Add Room Type', 'manage_options', 'jmnrrmenu2_form', 'RoomReservationApp\Includes\Room_Types_Controller::form');
		
		$hook = add_submenu_page('jmnrrmenu', 'List of Reservations', 'Reservations', 'manage_options', 'jmnrrmenu3', array($this, 'reservation_page'));
		add_action("load-$hook", array($this, 'so_reservation_page'));

		add_submenu_page('jmnrrmenu', '', 'Settings', 'manage_options', 'jmnrrmenu4', 'RoomReservationApp\Includes\Settings_Controller::form');
	}

	public function so_rooms_page()
	{
		$option = 'per_page';
		$args   = [
			'label'   => 'Rooms',
			'default' => 5,
			'option'  => 'records_per_page'
		];
		add_screen_option($option, $args);
		$this->records_obj = new Rooms_List();
	}

	public function rooms_page()
	{
		// View file
		include(JMN_RR_DIR.'pages/list/rooms.php');
	}

	public function so_room_types_page()
	{
		$option = 'per_page';
		$args   = [
			'label'   => 'Room Types',
			'default' => 5,
			'option'  => 'records_per_page'
		];
		add_screen_option($option, $args);
		$this->records_obj = new Room_Types_List();
	}

	public function room_types_page()
	{
		// View file
		include(JMN_RR_DIR.'pages/list/room_types.php');
	}

	public function so_reservation_page()
	{
		$option = 'per_page';
		$args   = [
			'label'   => 'Reservations',
			'default' => 5,
			'option'  => 'records_per_page'
		];
		add_screen_option($option, $args);
		$this->records_obj = new Reservations_List();
	}

	public function reservation_page()
	{
		if (isset($_GET['action'], $_GET['record']) && $_GET['action'] == 'view')
		{
			\RoomReservationApp\Includes\Reservation_Controller::view_reservation();
		}
		else if (isset($_GET['action'], $_GET['record']) && $_GET['action'] == 'viewbyconfirmation')
		{
			\RoomReservationApp\Includes\Reservation_Controller::view_by_confirmation();
		}
		else
		{
			if (isset($_GET['action'], $_GET['record']) && in_array($_GET['action'], ['active','cancel','paid']))
			{
				\RoomReservationApp\Includes\Reservation_Controller::update_status();
			}

			include(JMN_RR_DIR.'pages/list/reservation.php');
		}
	}

	public static function load_scripts()
	{
		wp_enqueue_style('style', JMN_RR_URL.'assets/css/style.css');

		wp_register_script('rr_script', JMN_RR_URL.'assets/js/script.js', array('jquery'), '', true);
		wp_enqueue_script('rr_script');

		if (is_admin()) {
			# WordPress Media Uploader
			wp_enqueue_media();
			wp_enqueue_script('media-uploader', JMN_RR_URL.'assets/js/media-upload.js', array('jquery'), '', true);
		} else {
			wp_register_script('datepicker', JMN_RR_URL.'assets/js/jquery.datetimepicker.full.min.js', array('jquery'), '', true);
			wp_enqueue_script('datepicker');

			wp_register_script('datepicker_script', JMN_RR_URL.'assets/js/datepicker_script.js', '', '', true);
			wp_enqueue_script('datepicker_script');

			wp_enqueue_style('datepicker', JMN_RR_URL.'assets/css/jquery.datetimepicker.css');
		}

		# Ajax Request
		wp_register_script('plugin-ajax', JMN_RR_URL.'assets/js/ajax.js', array('jquery'), '', true);
		wp_localize_script('plugin-ajax', 'ajaxRequest', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'ajaxfunction' => 'plugin_ajax',
				'pageadmin' => is_admin(),
				'userid' => get_current_user_id()
			)
		);
		wp_enqueue_script('plugin-ajax');
	}

	public static function shortcode_view($args, $content)
	{
		Frontend::view();
	}

	public static function widget_view()
	{
		register_widget('RoomReservationApp\Includes\Room_Reservation_Plugin_Widget');
	}

}