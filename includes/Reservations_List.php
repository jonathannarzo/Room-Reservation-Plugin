<?php
namespace RoomReservationApp\Includes;

use WP_List_Table;

class Reservations_List extends WP_List_Table
{
	const use_table = 'table3'; // `rr_room_reservations` table
	const use_table_t = 'table1'; // `rr_room_types` table
	const use_table_r = 'table2'; // `rr_rooms` table

	public function __construct()
	{
		parent::__construct([
			'singular' => __('Record'),
			'plural'   => __('Records'),
			'ajax'     => false
		]);
	}

	public static function get_records($per_page = 5, $page_number = 1)
	{
		global $wpdb;
		$search = (!empty($_REQUEST['s'])) ? $_REQUEST['s'] : false;
		$do_search = ( $search ) ? $wpdb->prepare("WHERE CONCAT(re.first_name, ' ', re.last_name) LIKE '%%%s%%'", $search ) : '';

		$table = Plugin_Tables::tables(self::use_table); // `rr_room_reservations` table
		$table_t = Plugin_Tables::tables(self::use_table_t); // `rr_room_types` table
		$table_r = Plugin_Tables::tables(self::use_table_r); // `rr_rooms` table
		$sql = "SELECT re.*, CONCAT(re.first_name, ' ', re.last_name) fullname, r.room_number, rt.room_type 
		FROM {$table} re 
		JOIN {$table_r} r ON re.room_id = r.id 
		JOIN {$table_t} rt ON r.room_type_id = rt.id {$do_search}";
		if (!empty($_REQUEST['orderby'])) {
			$sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
			$sql .= ! empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		}
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ($page_number - 1) * $per_page;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}

	public static function delete_record($id)
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$wpdb->delete($table, ['id' => $id], ['%d']);
	}

	public static function record_count()
	{
		global $wpdb;
		$table = Plugin_Tables::tables(self::use_table);
		$sql = "SELECT COUNT(*) FROM {$table}";
		return $wpdb->get_var($sql);
	}

	public function no_items()
	{
		_e('No Record found.');
	}

	// Render a column when no column specific method exist.
	public function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'fullname':
			case 'room_number':
			case 'room_type':
			case 'arrival':
			case 'departure':
			case 'number_of_nights':
			case 'rate':
			case 'status':
			case 'confirmation_code':
				return $item[$column_name];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb($item)
	{
		return '<input type="checkbox" name="bulk-delete[]" value="'.$item['id'].'" />';
	}

	function column_fullname($item)
	{
		$delete_nonce = wp_create_nonce('nonce_delete_record');
		$link = '?page='.esc_attr($_REQUEST['page']).'&action=view&record='.absint($item['id']);
		$title = '<strong><a href="'.$link.'">'.$item['fullname'].'</a></strong>';
		$actions = [
			'view' => '<a href="'.$link.'">View</a>',
			'delete' => '<a href="?page='.esc_attr($_REQUEST['page']).'&action=delete&record='.absint($item['id']).'&_wpnonce='.$delete_nonce.'">Delete</a>',
		];
		return $title . $this->row_actions( $actions );
	}

	function column_status($item)
	{
		$delete_nonce = wp_create_nonce('nonce_delete_record');
		$title = '<strong><a href="#">'.$item['status'].'</a></strong>';

		$active_link ='?page='.esc_attr($_REQUEST['page']).'&action=active&record='.absint($item['id']);
		$cancel_link ='?page='.esc_attr($_REQUEST['page']).'&action=cancel&record='.absint($item['id']);
		$paid_link ='?page='.esc_attr($_REQUEST['page']).'&action=paid&record='.absint($item['id']);

		$actions = [
			'active' => "<a href='{$active_link}'>Active</a>",
			'cancel' => "<a href='{$cancel_link}'>Cancel</a>",
			'paid' => "<a href='{$paid_link}'>Paid</a>",
		];

		unset($actions[$item['status']]);

		return $title . $this->row_actions( $actions );
	}

	function column_confirmation_code($item)
	{
		$link = '?page='.esc_attr($_REQUEST['page']).'&action=viewbyconfirmation&record='.$item['confirmation_code'];
		$title = '<strong><a href="'.$link.'">'.$item['confirmation_code'].'</a></strong>';
		$actions = [
			'view' => '<a href="'.$link.'">View</a>',
		];
		return $title . $this->row_actions( $actions );
	}

	function get_columns()
	{
		$columns = [
			'cb' => '<input type="checkbox" />',
			'fullname' => __('Name'),
			'room_number' => __('Room'),
			'room_type' => __('Type'),
			'arrival' => __('Start'),
			'departure' => __('End'),
			'number_of_nights' => __('Nights'),
			'rate' => __('Amount'),
			'status' => __('Status'),
			'confirmation_code' => __('CCode'),
		];
		return $columns;
	}

	public function get_sortable_columns()
	{
		$sortable_columns = array(
			'fullname' => array('first_name', false),
			'room_number' => array('room_number', false),
			'room_type' => array('room_type', false),
			'arrival' => array('arrival', true),
			'departure' => array('departure', true),
			'number_of_nights' => array('number_of_nights', true),
			'rate' => array('rate', true),
			'status' => array('status', true),
			'confirmation_code' => array('confirmation_code', false),
		);
		return $sortable_columns;
	}

	public function get_bulk_actions()
	{
		$actions = [
			'bulk-delete' => 'Delete'
		];
		return $actions;
	}

	// Handles data query and filter, sorting, and pagination.
	public function prepare_items()
	{
		$this->_column_headers = $this->get_column_info();

		// Bulk action
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page('records_per_page', 5);
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args([
			'total_items' => $total_items,
			'per_page'    => $per_page
		]);

		$this->items = self::get_records($per_page, $current_page);
	}

	public function process_bulk_action()
	{
		$url = array();
		foreach ($_GET as $key => $value) if('page' == $key || 'paged' == $key) $url[] = $key.'='.$value;
		$redirect_url = '?'.implode('&', $url);

		if ( 'delete' === $this->current_action() ) {
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if (!wp_verify_nonce( $nonce, 'nonce_delete_record')) {
				die('zZz...');
			} else {
				self::delete_record( absint( $_GET['record'] ) );
				wp_redirect($redirect_url);
				exit;
			}
		}

		// Bulk action
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			foreach ( $delete_ids as $id ) self::delete_record($id);
			wp_redirect($redirect_url);
			exit;
		}
	}

	public function search_box($text, $input_id)
	{
		echo '
		<p class="search-box">
			<label class="screen-reader-text" for="'.$input_id.'">'.$text.'</label>';
			echo '<input type="search" id="'.$input_id.'" name="s" value="'; _admin_search_query(); echo '" />';
			submit_button($text, 'button', false, false, array('id' => 'search-submit'));
		echo '
		</p>';
	}

}