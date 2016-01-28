<?php

	class TTR_db_page {
		
		public static $tutor = array();
		public static $tutors = array();
		public static $tutors_length = 0;
		public static $cur_id = -1;

		public static $tutor_per_page = 15;
		public static $current_page = 1;

		public static $max_chars = 260;
		
		// Check If Tutors Fond and sets them
		public static function have_tutors() {
			switch ($_REQUEST['action']){
				case "search":
					return self::search_loop();
				break;
				case "view":
					return self::view_loop();
				break;
			}
		}

		// Search Tutor Loop
		public static function search_loop() {
			if (self::$cur_id == -1) {
				if (isset($_REQUEST["tp_id"])) self::$current_page = esc_attr($_REQUEST["tp_id"]);

				self::$tutors = TTR_db::find_tutor(
						$_REQUEST['province'],
						$_REQUEST['city'],
						$_REQUEST['age'],
						$_REQUEST['subject'],
					self::$tutor_per_page, self::$current_page);

				self::$tutors_length = count(self::$tutors);
				if (self::$tutors_length <= 0) {
					_e("Results Not Found","ttr-db");
					return false;
				} else {
					self::$cur_id = 0;
					return true;
				}
			} else if (self::$cur_id < self::$tutors_length) {
				self::$tutor = self::$tutors[self::$cur_id];
				self::$cur_id++;

				return true;
			} else {
				self::$tutor = array();
				self::$cur_id = -1;

				return false;
			}
		}

		// View Tutor Loop
		public static function view_loop() {
			if (self::$cur_id == -1) {
				if (!isset($_REQUEST['tutor_id'])) {
					_e("Tutor Not Found","ttr-db");
					return false;
				}

				self::$tutor = TTR_db::get_tutor($_REQUEST['tutor_id']);
				if (!isset(self::$tutor)) {
					_e("Tutor Not Found","ttr-db");
					return false;
				} else {
					self::$cur_id++;
					return true;
				}
			} else if (self::$cur_id == 0){
				self::$cur_id++;
				return true;
			} else {
				self::$cur_id = -1;
				return false;
			}
		}

		// Returns Page Type
		public static function get_page_type() {
			return $_REQUEST["action"];
		}
		// Echoes Page Name
		public static function get_page_name() {
			_e("Tutor Search","ttr-db");
		}

		// Echo Tutor Name
		public static function get_name() {
			echo stripslashes(self::$tutor['lname']." ".self::$tutor['fname']." ".self::$tutor['mname']);
		}
		// Echo Tutor Age Range
		public static function get_age() {
			echo stripslashes(self::$tutor['min_age']." - ".self::$tutor["max_age"]);
		}
		// Echo Tutor Subjects
		public static function get_subject() {
			echo substr(stripslashes(self::$tutor['subject']),0,-2);
		}
		// Echo Tutor Province, City
		public static function get_location() {
			echo stripslashes(self::$tutor['province'].", ".self::$tutor['city']);
		}
		// Echo Tutor Description
		public static function get_description() {
			echo nl2br(stripslashes(self::$tutor['description']));
		}
		// Echo Tutor Description (Short)
		public static function get_description_short() {
			echo substr(nl2br(stripslashes(self::$tutor['description'])), 0,self::$max_chars )."[...]";
		}
		// Echo Tutor Link
		public static function get_link() {
			echo "?tutor_id=".self::$tutor['id']."&action=view";
		}

		// Echoes Pagination Bar
		public static function get_pagination() {
			$larr = "";
			$rarr = "";
			$r_url = get_site_url().$_SERVER["REQUEST_URI"];

			if (self::$tutors_length == self::$tutor_per_page) {
				$rarr = sprintf("<li class=\"next\"><a href=\"%s&tp_id=%d\">Наступна &rarr;</a></li>", $r_url, self::$current_page+1);
			}
			if (self::$current_page != 1) {
				$larr = sprintf("<li class=\"previous\"><a href=\"%s&tp_id=%d\">&larr; Попередня</a></li>", $r_url, self::$current_page-1);
			}
			
			echo sprintf("<nav><ul class=\"pager\">%s %s</ul></nav>",$larr,$rarr);
		}
	}
?>
