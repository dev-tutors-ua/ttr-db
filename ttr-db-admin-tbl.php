<?php

// Add WP_List_Table
if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// Admin Tutor Table
class Tutor_list extends WP_List_Table {

	public function __construct() {
		
		parent::__construct([
			'singular' => __("Tutor", "ttr-db"),
			'plural' => __("Tutors", "ttr-db"),
			'ajax' => false
		]);
	}

	/* DISPLAY METHODS */

	// Empty Table Message
	public function no_items() {
		_e("No Tutors Found", "ttr-db");
	}

	// Default Column Display Method
	public function column_default($item, $column_name) {
		return stripslashes($item[$column_name]);
	}

	// Name Column Method
	public function column_name($item) {
		
		$name = stripslashes($item['lname']." ".$item['fname']." ".$item['mname']);

		// Remove Item
		$url_del = sprintf("?page=%s&action=%s&id=%s", esc_attr($_REQUEST['page']), 'del-itm', $item['id']);
		$url_del = wp_nonce_url($url_del, 'del-tutor_'.$item['id']);

		$actions = [
			"edit" => sprintf("<a href=\"?page=%s&type=%s&id=%s\">".__("Edit", "ttr-db")."</a>", esc_attr($_REQUEST['page']), 'edit', $item['id']),
			"delete" => "<a href=\"{$url_del}\">".__("Delete","ttr-db")."</a>"
		];

		return $name.$this->row_actions($actions);
	}

	// Age Column Method
	public function column_age($item) {
		return $item['min_age']." - ".$item['max_age'];
	}

	// Checkbox Column Method
	public function column_cb($item) {
		return sprintf("<input type=\"checkbox\" name=\"bulk-sel[]\" value=\"%s\">", $item['id']);
	}

	// @returns column slugs and titles
	function get_columns() {
		$columns = [
			'cb' => "<input type=\"checkbox\" >",
			'name' => __("Name", "ttr-db"),
			'age' => __("Age", "ttr-db"),
			'province' => __("Province","ttr-db"),
			'city' => __("City","ttr-db"),
			'subject' => __("Subject", "ttr-db")
		];
		return $columns;
	}

	// @returns row actions array
	public function get_bulk_actions() {
		$act = [
			'bulk-delete' => __('Delete','ttr-db')
		];
		return $act;
	}

	// Prosses Requests
	public function process_bulk_action () {
		$post_action = $this->current_action();

		switch ($post_action) {
			case "add":
				$nonce = esc_attr($_REQUEST['_wpnonce']);
				if (!wp_verify_nonce($nonce, "add-new-tutor")) {
					die("Failed Security Check");
				} else {
					TTR_db::add_tutor(
								$_POST['fname'], 
								$_POST['mname'], 
								$_POST['lname'], 
								$_POST['min_age'], 
								$_POST['max_age'], 
								//$_POST['address'], 
								$_POST['province'], 
								$_POST['city'], 
								TTR_db::merge_subjects($_POST['subject']),
								$_POST['descp']
							);
				}
				break;
			case "upt":
				$nonce = esc_attr($_REQUEST['_wpnonce']);
				if (!wp_verify_nonce($nonce, "edit-tutor-data_".$_POST['id'])) {
					die("Failed Security Check");
				} else {
					TTR_db::upt_tutor(
								$_POST['id'],
								$_POST['fname'], 
								$_POST['mname'], 
								$_POST['lname'], 
								$_POST['min_age'], 
								$_POST['max_age'], 
								//$_POST['address'], 
								$_POST['province'], 
								$_POST['city'], 
								TTR_db::merge_subjects($_POST['subject']),
								$_POST['descp']
							);
				}
				break;
			case "del-itm":
				$nonce = esc_attr($_REQUEST['_wpnonce']);
				if (!wp_verify_nonce($nonce, 'del-tutor_'.$_GET['id'])) {
					die("Failed Security Check");
				} else {
					TTR_db::rm_tutor($_GET['id']);
				}
				break;
			case "import_csv":
				echo get_attached_file($_POST['f_id']);			
				break;
			case "bulk-delete":
				$del_ids = esc_sql($_POST['bulk-sel']);
				foreach ($del_ids as $i) {
					TTR_db::rm_tutor($i);
				}

				break;
		}
	}

	public function prepare_items() {
		/* Generate Headers */
		$this->_column_headers = array(
			$this->get_columns(), // (Array) Column Slugs and Titles
			[], // (Array) Hidden Fields
			[], // (Array) Sortable Columns
			'fname' // (String) Slug of column which displays actions (edit, view, etc.)
		);

		// Write Bulk Action Prossesing
		$this->process_bulk_action();
		
		// Sets Pagination Data
		$per_page = 10; //TODO make changable
		$cur_page = $this->get_pagenum();
		
		$this->set_pagination_args([
			'total_items' => TTR_db::get_count(),
			'per_page' => $per_page
		]);

		// Sets Tutors For Database
		$this->items = TTR_db::get_tutors($per_page, $cur_page);
	}
}

?>
