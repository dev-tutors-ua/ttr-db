<?php

/*
 * Plugin Name: Tutor Database 
 * Description: Stores, searches tutors from database
 * Version: 1.0.0
 * Author: Fedor Bobylev
 * Author URI: http://techblogogy.tk/
 * GitHub Plugin URI: https://github.com/dev-tutors-ua/ttr-db
 * GitHub Branch: master
 */

// Admin Table
require_once (plugin_dir_path(__FILE__)."ttr-db-admin-tbl.php");

// Admin Page Design
require_once (plugin_dir_path(__FILE__)."ttr-db-admin.php");

// Search Widget
require_once (plugin_dir_path(__FILE__)."ttr-db-widget.php");

// Search Page Support
require_once (plugin_dir_path(__FILE__)."ttr-db-page.php");

register_activation_hook(__FILE__, 'TTR_db::setup_db');
add_action('admin_menu','TTR_db::setup_plugin_menu');
add_action('widgets_init', 'TTR_db::setup_widgets');
add_action('plugins_loaded', 'TTR_db::add_txt_domain');
//add_action('init', 'TTR_db::tutor_post_type');

// Main ttr-db class
class TTR_db {

	// Creates Database For ttr-db
	public static function setup_db() {
		global $wpdb, $tbl_name;

		$table_name = $wpdb->prefix."tutors";
		$charset = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			fname varchar(50) NOT NULL,
			lname varchar(50) NOT NULL,
			mname varchar(50) NOT NULL,
			min_age int(11) NOT NULL,
			max_age int(11) NOT NULL,
			province varchar(200) NOT NULL,
			city varchar(200) NOT NULL,
			subject varchar(255) NOT NULL,
			description text NOT NULL,
			PRIMARY KEY  (id)
		) $charset";

		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function add_txt_domain() {
		load_plugin_textdomain( 'ttr-db', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
	}

	// Sets Up Admin Panel Menu
	public static function setup_plugin_menu() {
		add_object_page(__("Tutor DB","ttr-db"), __("Tutor DB","ttr-db"), "manage_options", "tutor-database", "ttr_db_admin_render");
	}

	// Sets Up Sidebar Search Widget
	public static function setup_widgets() {
		register_widget('TTR_Search_Widget');
	}

	// Returns JSON Of Cities
	public static function get_cities() {
		return file_get_contents(plugin_dir_path(__FILE__)."json/cities.json");
	}

	// Returns JSON Of Subjects
	public static function get_subjects() {
		return file_get_contents(plugin_dir_path(__FILE__)."json/subjects.json");
	}
	// Saves JSON Subjects
	public static function save_subjects($itm) {
		$json = json_encode($itm);
		file_put_contents(plugin_dir_path(__FILE__)."json/subjects.json",$json);
	}

	// Returns JSON Of Ages
	public static function get_ages() {
		return file_get_contents(plugin_dir_path(__FILE__)."json/ages.json");
	}
	
	//Get Tutors /w pagination
	public static function get_tutors($per_page = 10, $page = 1) {
		global $wpdb;
		
		$sql = "SELECT * FROM {$wpdb->prefix}tutors "; 

		//TODO Add Sorting

		$sql .= "LIMIT $per_page OFFSET ". ($page-1) * $per_page;

		return $wpdb->get_results($sql, ARRAY_A);
	}

	// Adds Tutor To Database
	public static function add_tutor($fname, $mname, $lname, $min_age, $max_age, $province, $city, $subject, $descp) {
		global $wpdb, $tbl_name;
		$tbl = $wpdb->prefix."tutors";
		
		$wpdb->insert(
				$tbl, 
				array (
					'fname' => $fname,
					'mname' => $mname,
					'lname' => $lname,
					'min_age' => $min_age,
					'max_age' => $max_age,
					'province' => $province,
					'city' => $city,
					'subject' => $subject,
					'description' => $descp
				),
				array (
					'%s','%s','%s',
					'%d',
					'%s','%s'
				));	
	}

	// Updates Tutor Data
	public static function upt_tutor($id, $fname, $mname, $lname, $min_age, $max_age, $province, $city, $subject, $descp) {
		global $wpdb, $tbl_name;
		$tbl = $wpdb->prefix."tutors";
		
		$wpdb->update(
				$tbl, 
				array (
					'fname' => $fname,
					'mname' => $mname,
					'lname' => $lname,
					'min_age' => $min_age,
					'max_age' => $max_age,
					'province' => $province,
					'city' => $city,
					'subject' => $subject,
					'description' => $descp
				),
				array ( 
					'id' => $id 
				),
				array (
					'%s','%s','%s',
					'%d',
					'%s','%s'
				));	
	}


	// Import From CSV
	public static function import_from_csv($f_path) {
		
	}

	// Get Tutor
	public static function get_tutor($id) {
		global $wpdb, $tbl_name;
		$tbl = $wpdb->prefix."tutors";

		$id = esc_attr($id);
		$sql = "SELECT * FROM {$tbl} WHERE id={$id}";

		return $wpdb->get_row($sql, ARRAY_A);
	}

	//Remove Tutor 
	public static function rm_tutor($id) {
		global $wpdb;
		$wpdb->delete("{$wpdb->prefix}tutors", ['id' => $id], ['%d']);
	}

	//Get Rows Count
	public static function get_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}tutors";
		return $wpdb->get_var($sql);
	}

	// Creates Subjects List
	public static function merge_subjects($sbj) {
		$s_sbj = "";
		foreach($sbj as $i) {
			$s_sbj .= $i.", ";
		}
		return $s_sbj;
	}

	// Find Tutor
	public static function find_tutor($province,$city,$age,$subject,$per_page=5,$page=1) {
		global $wpdb;

		$sql = $wpdb->prepare("
			SELECT * FROM {$wpdb->prefix}tutors
			WHERE 
				province=%s AND 
				city=%s AND 
				min_age <= %d AND
				%d <= max_age AND
				subject LIKE %s
			LIMIT %d OFFSET %d",
			$province, $city, $age,$age, "%".$subject.", %", $per_page,($page-1) * $per_page);

		return $wpdb->get_results($sql, ARRAY_A);
	}
}

?>
