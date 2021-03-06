<?php
namespace RoomReservationApp\Includes;

use WP_List_Table;

class Room_Types_List extends WP_List_Table
{
	const use_table = 'table1'; // `rr_room_types` table

	public function __construct()
	{
		parent::__construct([
			'singular' => __('Room Type'),
			'plural'   => __('Room Types'),
			'ajax'     => false
		]);
	}

	public static function get_records($per_page = 5, $page_number = 1)
	{
		global $wpdb;
		$search = (!empty($_REQUEST['s'])) ? $_REQUEST['s'] : false;
		$do_search = ( $search ) ? $wpdb->prepare("WHERE room_type LIKE '%%%s%%'", $search ) : '';

		$table = Plugin_Tables::tables(self::use_table);
		$sql = "SELECT * FROM {$table} {$do_search}";
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
			case 'room_type':
			case 'description':
				return $item[$column_name];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb($item)
	{
		return '<input type="checkbox" name="bulk-delete[]" value="'.$item['id'].'" />';
	}

	/* column_[ db table column name ] */
	function column_room_type($item)
	{
		$delete_nonce = wp_create_nonce('nonce_delete_record');
		$edit_link = '?page='.esc_attr($_REQUEST['page']).'_form&action=edit&record='.absint($item['id']);
		$title = '<strong><a href="'.$edit_link.'">'.$item['room_type'].'</a></strong>';
		$actions = [
			'edit' => '<a href="'.$edit_link.'">Edit</a>',
			'delete' => '<a href="?page='.esc_attr($_REQUEST['page']).'&action=delete&record='.absint($item['id']).'&_wpnonce='.$delete_nonce.'">Delete</a>',
		];
		return $title . $this->row_actions( $actions );
	}

	function get_columns()
	{
		$columns = [
			'cb' => '<input type="checkbox" />',
			'room_type' => __('Room Type'),
			'description' => __('Description')
		];
		return $columns;
	}

	public function get_sortable_columns()
	{
		$sortable_columns = array(
			'room_type' => array('room_type', true),
			'description' => array('description', false)
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