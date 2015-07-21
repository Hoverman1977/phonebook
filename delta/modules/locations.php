<?php

if (!session_id()) session_start();
if (!isset($_SESSION['module_name'])) {
	$module_name = "software";
} else {
	$module_name = $_SESSION['module_name'];
}
$title = "The Locations";


include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
$common = new common();
$tabindex = 1;
$site_code = "";
$description = "";
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$site_code = $_POST['site_code'];
		$description = $_POST['description'];
		$address = $_POST['address'];
		$message = $common->add_location($site_code, $description, $address);	
	} else if ($_POST['action'] == 'edit') {
		$location_id = $_POST['location_id'];
		$site_code = $_POST['site_code'];
		$description = $_POST['description'];
		$address = $_POST['address'];
		$message = $common->edit_location($location_id, $site_code, $description, $address);	
	}
}
print ('<div id="message">'.$message.'</div>');
if (isset($_GET['description']) && $_GET['description'] != ""){
	$description = $_GET['description'];
	$title = $title." for Site Description containing '".$description."'";
	$location_info = $common->get_locations("description", $description);
	$count = mysql_num_rows($location_info);
    $title = $title." | ".$count." total";
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
	print ($common->display_textbox("Site Code", "site_code", $site_code, 35, "site_code", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
	print ($common->display_textbox("Description", "description", $description, 35, "description", $tabindex++, 50, "", ""));
	print ($common->display_textbox("Address", "address", $address, 50, "address", $tabindex++, 200, "", ""));
    print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add Location" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$location_id = $_GET['id'];
	settype($location_id, "integer");	
	$location_info = $common->get_locations('location_id', $location_id);
	if ($location_info) $location_count = mysql_num_rows($location_info);
	if ($location_count>0) {
		if (!isset($_POST['site_code'])) $site_code = mysql_result($location_info, 0, "site_code");
		if (!isset($_POST['description'])) $description = mysql_result($location_info, 0, "description");
		if (!isset($_POST['address'])) $address = mysql_result($location_info, 0, "address");
		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
	    print ($common->display_textbox("Site Code", "site_code", $site_code, 35, "site_code", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
	    print ($common->display_textbox("Description", "description", $description, 35, "description", $tabindex++, 50, "", ""));
	    print ($common->display_textbox("Address", "address", $address, 50, "address", $tabindex++, 200, "", ""));
		print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="location_id" value="'.$location_id.'" />');
		print ('<input type="button" class="button" id="btnedit" value="Update Location" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($location_info);
} else {
	$location_info = $common->get_locations();
	$count = mysql_num_rows($location_info);
    $title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($location_info)){
	                               	print ('<form method="get" action="#" id="srchform" onsubmit="return false;"><div class="form">');
    	                            $js = "submitform('srchform');return false;";
    	                            print ($common->display_textbox("Description", "description", "", 25, "description", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
									print ('<input type="button" class="button" id="search" value="Search Locations" onclick="'.$js.'" />');
                    	            print ('</div>');
									print ('<div>');
									$common->display_table_radio($location_info, $title, "location_details", false, "True");
                        	        print ('</div></form>');
                            	    $js = "document.getElementById('description').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
