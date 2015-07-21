<?php
$module_name = "software";
$title = "Assets";
if (isset($_REQUEST['action'])) {
	$software_admin = "True";
} else {
	$software_admin = "False";	
}
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/software_class.php");
$common = new common();
$software = new software();
$tabindex = 1;
$asset_id = 0;
$description = "";
$asset_tag = "";
$owner = "";
$location_id = 0;
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$asset_tag = $_POST['asset_tag'];
		$location_id = $_POST['location_id'];
		$description = $_POST['description'];
		$owner = $_POST['owner'];
		$message = $software->add_asset($asset_tag, $location_id, $description, $owner);	
	} else if ($_POST['action'] == 'edit') {
		$asset_id = $_POST['asset_id'];
		$asset_tag = $_POST['asset_tag'];
		$location_id = $_POST['location_id'];
		$description = $_POST['description'];
		$owner = $_POST['owner'];
		$message = $software->edit_asset($asset_id, $asset_tag, $location_id, $description, $owner);	
	}
}
print ('<div id="message">'.$message.'</div>');
if (isset($_GET['asset_tag']) && $_GET['asset_tag'] != ""){
	$asset_tag = $_GET['asset_tag'];
	$title = $title." for Asset Tag containing '".$asset_tag."'";
	$asset_info = $software->get_assets("asset_tag", $asset_tag);
	$count = mysql_num_rows($asset_info);
    $title = $title." | ".$count." total";
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	$location_info = $common->get_locations();
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
	print ($common->display_textbox("Asset Tag", "asset_tag", $asset_tag, 35, "asset_tag", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
	print ($common->display_textbox("Description", "description", $description, 35, "description", $tabindex++, 50, "", ""));
	print ($common->display_textbox("Owner", "owner", $owner, 35, "owner", $tabindex++, 50, "", ""));
	print ($common->get_listbox($location_info, "Location", "location_id", "Select a location", $location_id, $tabindex++));
    print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add Asset" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$asset_id = $_GET['id'];
	settype($asset_id, "integer");	
	$asset_info = $software->get_assets('asset_id', $asset_id);
	$location_info = $common->get_locations();
	if ($asset_info) $asset_count = mysql_num_rows($asset_info);
	if ($asset_count>0) {
		if (!isset($_POST['asset_tag'])) $asset_tag = mysql_result($asset_info, 0, "asset_tag");
		if (!isset($_POST['description'])) $description = mysql_result($asset_info, 0, "description");
		if (!isset($_POST['location'])) $location_id = mysql_result($asset_info, 0, "location_id");
		if (!isset($_POST['owner'])) $owner = mysql_result($asset_info, 0, "owner");
		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
	    print ($common->display_textbox("Asset Tag", "asset_tag", $asset_tag, 35, "asset_tag", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
	    print ($common->display_textbox("Description", "description", $description, 35, "description", $tabindex++, 50, "", ""));
		print ($common->display_textbox("Owner", "owner", $owner, 35, "owner", $tabindex++, 50, "", ""));
		print ($common->get_listbox($location_info, "Location", "location_id", "Select a location", $location_id, $tabindex++));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="asset_id" value="'.$asset_id.'" />');
		print ('<input type="button" class="button" id="btnedit" value="Update Asset" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($asset_info);
} else {
	$asset_info = $software->get_assets();
	$count = mysql_num_rows($asset_info);
    $title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($asset_info)){
	                                print ('<form method="get" action="" id="srchform" ><div class="form">');
    	                            $js = "submitform('srchform');return false;";
    	                            print ($common->display_textbox("Asset Tag", "asset_tag", "", 25, "asset_tag", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
									print ('<input type="button" class="button" id="search" value="Search Assets" onclick="'.$js.'" />');
                    	            print ('</div>');
									print ('<div>');
									$common->display_table_radio($asset_info, $title, "asset_details", false, "True");
                        	        print ('</div></form>');
                            	    $js = "document.getElementById('asset_tag').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
