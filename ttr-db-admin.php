<?php

function ttr_db_admin_script($city="",$province="",$subject="",$age="",$min_age="",$max_age="") {
?>
	<script>
		jQuery(document).ready(function ($) {

			var cities = (<?php echo TTR_db::get_cities(); ?>);
			var subjects = (<?php echo TTR_db::get_subjects(); ?>);
			var ages = (<?php echo TTR_db::get_ages(); ?>);

			// Add Subjects To Select
			function add_subjects(sel) {
				if (typeof sel === "undefined")sel="";
				var txt_opt = "";
				for (var i=0; i<subjects.length; i++) {
					if (subjects[i] == sel) {
						txt_opt += "<option selected>"+subjects[i]+"</option>";
					} else {
						txt_opt += "<option>"+subjects[i]+"</option>";
					}
				}
				return txt_opt;
			}

			// Selection Input
			function change_input() {
				$($($(this).parent()).find("input")).val($(this).val());
			}
			// Delete Subject
			function del_sbj() {
				$($(this).parent()).remove();
			}
			$(".del_sbj").on("click", del_sbj);

			// Add Subject
			function add_sbj(vals) {
				$("#sbj_rw").append("<div class=\"rw\"><select class=\"sbj_sel\">"+add_subjects(vals)+"</select><button type=\"button\" class=\"del_sbj\">-</button><input type=\"hidden\" name=\"subject[]\" value=\""+vals+"\"></div>");
				$(".del_sbj").on("click", del_sbj);
				$(".sbj_sel").on("change", change_input);
			}
			$("#new_sbj").on("click",function () { add_sbj(subjects[0]); });

			// Add Cities To Select
			for (var i=0; i<cities.length; i++) {
				$("#province").append("<option c_id='"+i+"'>"+cities[i].province+"</option>");
			}

			// Add Subjects To Select
			//add_subjects("#subject");
			$("#subject").append(add_subjects());

			// Add Ages To Select
			for (var i=0; i<ages.length; i++) {
				$(".age").append("<option value='"+(i+1)+"'>"+ages[i]+"</option>");
			}

			$(".sbj_sel").on("change", change_input);

			$("#province").on("change", function () {
				$("#city").empty();
				$("#city").prop("disabled",false);

				var c_id = $("#province option").filter(":selected").attr("c_id");
				for (var i=0;i<cities[c_id].cities.length;i++){
					$("#city").append("<option>"+cities[c_id].cities[i]+"</option>");
				}
			});

			$(".form-group select").on("change", function () {
				if ( $("#province").val() == -1 || $("#city").val() == -1 || $("#age").val() == -1 || $("#subject").val() == -1 ) {
					$("#search_btn").prop("disabled",true);
				} else {
					$("#search_btn").prop("disabled",false);
				}
			});

			var p_subject = "<?php echo $subject; ?>";
			if (p_subject != "") {
				var a_sbjs = p_subject.split(", ");
				console.log(a_sbjs);
				$("#subject").val(a_sbjs[0]);
				$("#sbj_sel_itm").val(a_sbjs[0]);
				for (var i=1;i<a_sbjs.length-1; i++) {
					add_sbj(a_sbjs[i]);
				}
			}

			var p_age = "<?php echo $age; ?>";
			if (p_age != "") {
				$("#age").val(p_age);
			}

			var p_min_age = "<?php echo $min_age; ?>";
			var p_max_age = "<?php echo $max_age; ?>";
			if (p_min_age != "" && p_max_age != "") {
				$("#min_age").val(p_min_age);
				$("#max_age").val(p_max_age);
			}

			var p_city = "<?php echo $city; ?>";
			var p_province = "<?php echo $province; ?>";
			if (p_city != "" && p_province !="") {
				$("#province").val(p_province);
				$("#province").trigger("change");
				$("#city").val(p_city);
			}
			
		});
	</script>
<?php
}

function ttr_db_admin_style() {
?>
	<style>
		.warp {
			margin-right: 20px;
		}

		.form-edit label {
			display: inline-block;

			margin-top: 8px;
			width: 100%;
			height: 100%;
			
			text-align: right;
			vertical-align: text-top;
			font-weight: bold;
		}
		.form-edit input, .form-edit select, .form-edit textarea {
			width: 400px;
		}
		.form-edit textarea {
			height: 200px;
			text-align: left;
		}
		#sbj_rw {
			width: 400px;
		}
		#sbj_rw .rw > select{
			width: 370px;
		}
		.form-edit .age {
			width: 80px;
		} 

		.btn-default {
			background-color: #e0e0e0;

			margin-top: 10px;
			padding: 8px;

			border-radius: 5px;
			border: none;

			color: #2a73aa;
			font-size: 12pt;
			font-weight: bold;
		}
		.btn-default:hover {
			background-color: #2a73aa;
			color: #fff;

			cursor: pointer;
		}
		.btn-head {
			font-size: 10pt;
			text-decoration: none;
		}


		/* Subject Editor */
		#sbj-editor li{
			background-color: #fff;
			width: 400px;
			font-size: 14pt;
			padding: 10px;
		}
		#sbj-editor li:hover{
			cursor: move;
		}
		#sbj-editor .del-btn {
			background-color: #e2e2e2;
			border: none;
		}
		#sbj-list > li {
			overflow: auto;
		}
		#sbj-list > li > .sbj {
			float:left;
			line-height: 30px;
		}
		#sbj-list > li > .del-btn {
			float:right;
		}
		#sbj-list > li > .del-btn:hover {
			cursor:pointer;
		}

		#sbj_name_in {
			width: 365px;
			height: 35px;
		}
	</style>
<?php
}

function ttr_db_admin_form($id=-1,$fname="",$lname="",$mname="",$min_age="",$max_age="",$province="",$city="",$subject="",$descp="") {
	$lst_page = get_site_url()."/wp-admin/admin.php?page=tutor-database";

	ttr_db_admin_script($city,$province,$subject,"",$min_age,$max_age);
?>	

	<div class="warp">
		<h1><?php _e("Add Tutor","ttr-db") ?></h1>
		<div id="tutor-form">
			<form method="post" action="<?php echo $lst_page; ?>">
				
				<table class="form-edit">
					<!-- Last, First, Middle Names -->
					<tr>
						<td>
							<label for="lname"><?php _e("Last Name","ttr-db"); ?>:</label>
						</td>
						<td>
							<input type="text" name="lname" class="form-obj" id="lname" value="<?php echo $lname; ?>">
						</td>
					</tr>
					<tr>
						<td>
							<label for="fname" class="form-obj"><?php _e("First Name","ttr-db"); ?>:</label>
						</td>
						<td>
							<input type="text" name="fname" class="form-obj" id="fname" value="<?php echo $fname; ?>">
						</td>
					</tr>
					<tr>
						<td>
							<label for="mname" class="form-obj"><?php _e("Middle Name","ttr-db"); ?>:</label>
						</td>
						<td>
							<input type="text" name="mname" class="form-obj" id="mname" value="<?php echo $mname; ?>">
						</td>
					</tr>

					<!-- Province And City -->
					<tr>
						<td>
							<label for="province"><?php _e("Province","ttr-db"); ?>:</label>
						</td>
						<td>
							<select id="province" name="province">
								<option disabled><?php _e("-- SELECT --", "ttr-db") ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="city"><?php _e("City","ttr-db"); ?>:</label>
						</td>
						<td>
							<select id="city" name="city"></select>
						</td>
					</tr>
					
					<!-- Age -->
					<tr>
						<td>
							<label for="age"><?php _e("Age","ttr-db"); ?>:</label>
						</td>
						<td>
							<select class="age" name="min_age" id="min_age">
								<option disabled><?php _e("-- SELECT --", "ttr-db") ?></option>
							</select>
							 - 
							<select class="age" name="max_age" id="max_age">
								<option disabled><?php _e("-- SELECT --", "ttr-db") ?></option>
							</select>
						</td>
					</tr>
					
					<!-- Subject -->
					<tr>
						<td>
							<label for="subject"><?php _e("Subject","ttr-db"); ?>:</label>
						</td>
						<td id="sbj_rw">
							<div class="rw">
								<select id="subject" class="sbj_sel">
									<option disabled><?php _e("-- SELECT --", "ttr-db") ?></option>
								</select>
								<button type="button" id="new_sbj">+</button>
								<input type="hidden" id="sbj_sel_itm" name="subject[]">
							</div>
							<!--<div class="rw">
								<select class="sbj_sel">
									<option disabled><?php _e("-- SELECT --", "ttr-db") ?></option>
								</select>
								<button type="button" class="del_sbj">-</button>
								<input type="hidden" name="subject[]">
							</div>-->
						</td>
					</tr>

					<!-- Description Textbox-->
					<tr>
						<td>
							<label for="descp"><?php _e("Description","ttr-db"); ?>:</label>
						</td>
						<td>
							<textarea name="descp" id="descp"><?php echo $descp; ?></textarea>
						</td>
					</tr>

				</table>

				<?php if ($id == -1) { ?>
					<?php wp_nonce_field('add-new-tutor') ?>
					<button id="add_tutor" class="btn-default" name="action" value="add" type="submit"><?php _e("Add","ttr-db"); ?></button>
				<?php } else { ?>
					<?php wp_nonce_field('edit-tutor-data_'.$id) ?>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<button id="add_tutor" class="btn-default" name="action" value="upt" type="submit"><?php _e("Update","ttr-db"); ?></button>
				<?php } ?>
			</form>
		</div>
	</div>
<?php
}

function ttr_db_admin_table() {
	$tutor_tbl = new Tutor_list();

	$add_page = get_site_url()."/wp-admin/admin.php?page=tutor-database&type=add";
	$sbj_page = get_site_url()."/wp-admin/admin.php?page=tutor-database&type=sbj";
	$csv_page = get_site_url()."/wp-admin/admin.php?page=tutor-database&type=csv";
?>
	<div class="warp">
		<h1><?php _e("Tutor DB","ttr-db") ?></h1>
		<h4><a href="<?php echo $add_page; ?>"><?php _e("Add New","ttr-db") ?></a> | 
			<a href="<?php echo $sbj_page; ?>"><?php _e("Subjects Editor","ttr-db"); ?></a></h4>
		<div class="metabox-sortables">
			<form method="post">
				<?php 
					$tutor_tbl->prepare_items();
					$tutor_tbl->display();
				?>
			</form>
		</div>
	</div>
<?php
}

function ttr_db_subject_editor() {

	if (isset($_POST['action']) && $_POST['action'] == "upt") {
		TTR_db::save_subjects($_POST['sbj']);
	}
	
	$j_sbj = TTR_db::get_subjects();
	$sjb = json_decode($j_sbj);
?>
	<div class="warp">
		<h1><?php _e("Subjects Editor","ttr-db") ?></h1>
		<div id="sbj-editor">
			<input type="text" id="sbj_name_in">
			<button type="button" id="sbj_add_btn" class="btn-default"><?php _e("Add","ttr-db"); ?></button>
			
			<form method="post">
				<ul id="sbj-list">
					<?php foreach($sjb as $i) { ?>
						<li>
							<span class="sbj"><?php echo stripslashes_deep($i); ?></span> 
							<button type="button" class="del-btn">-</button> 
							<input type="hidden" name="sbj[]" value="<?php echo esc_attr($i); ?>">
						</li>
					<?php } ?>
				</ul>

				<button class="btn-default" name="action" value="upt" type="submit"><?php _e("Update","ttr-db"); ?></button>
			</form>
		</div>
	</div>

	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		jQuery(document).ready(function ($) {
			$("#sbj-list").sortable();
			$("#sbj-list").disableSelection();

			$(".del-btn").on("click", function () {
				$($(this).parent()).remove();
			});

			$("#sbj_add_btn").on("click", function () {
				var txt = $("#sbj_name_in").val();
				var c_txt = addslashes(txt);
				$("#sbj-list").append("<li><span class='sbj'>"+txt+"</span><button type='button' class='del-btn'>-</button><input type='hidden' name='sbj[]' value=\""+c_txt+"\"></li>");

				$(".del-btn").on("click", function () {
					$($(this).parent()).remove();
				});

			});
		});

		function addslashes( str ) {
			return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
		}
	</script>
<?php
}

function ttr_db_admin_csv () {
	
	$p_url = get_site_url()."/wp-admin/admin.php?page=tutor-database";
?>
	<div class="warp">
		<h1><?php _e("Import CSV","ttr-db") ?></h1>

		<p><?php _e("Select CSV File","ttr-db") ?>: 
			<button id="add_media" class="btn-default" type="button"><?php _e("Media","ttr-carousel"); ?></button>
			<span id="csv_title"></span></p> 

		<form method="post" action="<?php echo $p_url; ?>">
			<input type="hidden" name="f_id" id="f_path" value="">
			<button type="submit" class="btn-default" name="action" value="import_csv">Import CSV</button>
		</form>

		<script>
			jQuery(document).ready(function ($) {
				var frame;

				$("#add_media").on("click", function () {
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media({
						title: '<?php _e("Select CSV File","ttr-db"); ?>',
						button: {
							text: '<?php _e("Select","ttr-db"); ?>'
						},
						multiple: false
					});
					frame.on('select', function () {
						var img_dat = frame.state().get('selection').first().toJSON();
						console.log(img_dat);

						$("#csv_title").text(img_dat.title);
						$("#f_path").val(img_dat.id);
					});
					frame.open();
				});
			});
		</script>
	</div>
<?php
}

function ttr_db_admin_render() {

	ttr_db_admin_style();

	if (!isset($_GET["type"])) $_GET["type"] = "list";
	switch ($_GET["type"]) {
		case "add":
			ttr_db_admin_form();
			break;
		case "edit":
			$itm = TTR_db::get_tutor($_GET['id']);
			ttr_db_admin_form(
					$itm['id'], 
					stripslashes($itm['fname']),
					stripslashes($itm['lname']),
					stripslashes($itm['mname']),
					stripslashes($itm['min_age']),
					stripslashes($itm['max_age']),
					//stripslashes($itm['address']),
					stripslashes($itm['province']),
					stripslashes($itm['city']),
					stripslashes($itm['subject']),
					stripslashes($itm['description'])
				);
			break;
		case "sbj":
			ttr_db_subject_editor();
			break;
		case "csv":
			ttr_db_admin_csv();
			break;
		default: 
			ttr_db_admin_table();
			break;
	}
}

?>
