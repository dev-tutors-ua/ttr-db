<?php

class TTR_Search_Widget extends WP_Widget {
	
	function __construct() {
		parent::__construct(
			'ttr_search_widget', // Base ID
			__( 'Tutor Search', 'ttr-db' ), // Name
			array( 'description' => __( 'Sidebar Tutor Search Widget', 'ttr-db' ), ) // Args
		);
	
	}

	// Front End
	public function widget ($args, $instance) {
		echo $args["before_widget"];
		echo $args["before_title"] . __("Find Tutor","ttr-db") . $args["after_title"];

		$s_url = get_site_url()."/tutor_search"

	?>
		<form class="search_widget_form" action="<?php echo $s_url; ?>">
			<!-- Province Group -->
			<div class="form-group">
				<label for="province"><?php _e("Province","ttr-db") ?>:</label>
				<select name="province" id="province" class="form-control">
					<option value="-1"><?php _e("-- SELECT --", "ttr-db") ?></option>
				</select>
			</div>

			<!-- City Group -->
			<div class="form-group">
				<label for="city"><?php _e("City","ttr-db") ?>:</label>
				<select name="city" id="city" class="form-control" disabled></select>
			</div>

			<!-- Subject Group -->
			<div class="form-group">
				<label for="subject"><?php _e("Subject","ttr-db") ?>:</label>

				<select class="form-control" id="subject" name="subject">
					<option value="-1"><?php _e("-- SELECT --", "ttr-db") ?></option>
				</select>
			</div>

			<!-- Age Group -->
			<div class="form-group">
				<label for="age"><?php _e("Age","ttr-db") ?>:</label>
				<!--<input type="text" class="form-control" id="age" name="age" value="<?php echo $_REQUEST['age'] ?>">-->
				<select class="form-control age" id="age" name="age">
					<option value="-1"><?php _e("-- SELECT --", "ttr-db") ?></option>
				</select>
			</div>

			<button type="submit" id="search_btn" style="float:right;" class="btn btn-default" name="action" value="search" disabled>
				<?php _e("Search", "ttr-db"); ?> <span class="glyphicon glyphicon-search"></span>
			</button>
		</form>

		<!-- Added Admin Scripts -->
		<?php 
			if (isset($_REQUEST['action']) && $_REQUEST['action'] === "search") {
				ttr_db_admin_script($_REQUEST['city'],$_REQUEST['province'],$_REQUEST['subject'],$_REQUEST['age']); 
			} else {
				ttr_db_admin_script(); 
			}
		?>

	<?php
		echo $args["after_widget"];
	}

	// Admin End
	public function form( $instance ) {
	}

	// Saving Function
	public function update( $new_instance, $old_instance ) {
	}
}

?>
