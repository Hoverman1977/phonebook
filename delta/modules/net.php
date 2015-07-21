<?php
if (!session_id()) session_start();
$_SESSION['module_name'] = 'net';
$module_name = "net";
$title = "The Net";
if (isset($_REQUEST['action'])) {
	$wan_admin = "True";
} else {
	$wan_admin = "False";	
}
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/wan_class.php");
$common = new common();
$wan = new wan();
$tabindex = 1;
$wan_id = 0;
$sort = 'ip_fnn';

if (!isset($message)) $message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$ip_fnn = $_POST['ip_fnn'];
		$description = $_POST['description'];
		$location_id = $_POST['location_id'];
		$wan_type_id = $_POST['wan_type_id'];
		$management_fnn = $_POST['management_fnn'];
		$link_fnn = $_POST['link_fnn'];
		$link_cost = $_POST['link_cost'];
		$management_cost = $_POST['management_cost'];
		$deleted = 'n';
		$message = $wan->add_wan($ip_fnn, $location_id, $wan_type_id, $description, $link_fnn, $management_fnn, $link_cost,$management_cost);
	} else if ($_POST['action'] == 'edit') {
		$wan_id = $_POST['wan_id'];
		$ip_fnn = $_POST['ip_fnn'];
		$description = $_POST['description'];
		$location_id = $_POST['location_id'];
		$wan_type_id = $_POST['wan_type_id'];
		$link_fnn = $_POST['link_fnn'];
		$management_fnn = $_POST['management_fnn'];
		$link_cost = $_POST['link_cost'];
		$management_cost = $_POST['management_cost'];
		$description = $_POST['description'];
		$wan_speed = $_POST['wan_speed'];
		if (isset($_POST['deactive']) && $_POST['deactive'] != '') {
			$deactive = 'y';
		} else {
			$deactive = 'n';			
		}
		$message = $wan->edit_wan($wan_id, $ip_fnn, $location_id, $wan_type_id, $description, $link_fnn, $management_fnn, $link_cost,$management_cost, $deactive);
	} else if ($_POST['action'] == 'add_comment') {
		$wan_id = $_POST['wan_id'];
		$notes_text = $_POST['notes_text'];
		$message = $wan->add_wan_comment($wan_id, $notes_text);
	} else if ($_POST['action'] == 'del') {
		//return;
		$wan_id = $_POST['wan_id'];
		$message = $wan->del_wan($wan_id);
	}
}

print ('<div id="message">'.$message.'</div>');
if (isset($_GET['sort'])) {
	$sort = $_GET['sort'];
}
if (isset($_GET['ip_fnn']) && $_GET['ip_fnn'] != ""){
	$ip_fnn = $_GET['ip_fnn'];
	$title = "IP FNN containing '".$ip_fnn."'";
	$wan_info = $wan->get_wan_view("ip_fnn", $ip_fnn);
	$count = mysql_num_rows($wan_info);
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	$location_info = $common->get_locations();
	$wan_type_info = $wan->get_wan_types();
	print ('<form method="post" action="" id="addform"><div class="form">');
    	$submit_js = "submitform('addform')";
	print ($common->display_textbox("IP FNN", "ip_fnn", $ip_fnn, 50, "ip_fnn", $tabindex++, 50, "", "return true"));
    	print ($common->get_listbox($location_info, "Location", "location_id", "Select a Location", $location_id, $tabindex++));    	
	print ($common->get_listbox($wan_type_info, "WAN Type", "wan_type_id", "Select a WAN Type", $wan_type_id, $tabindex++));
	print ($common->display_textbox("Link (Copper) FNN", "link_fnn", $link_fnn, 50, "link_fnn", $tabindex++, 50, "", "return true"));
	print ($common->display_textbox("Link (Copper) Cost", "link_fnn", $link_cost, 10, "link_cost", $tabindex++, 10, "", "return true"));
	print ($common->display_textbox("Management FNN", "management_fnn", $management_fnn, 50, "management_fnn", $tabindex++, 50, "", "return true"));
	print ($common->display_textbox("Management Cost", "management_cost", $management_cost, 10, "management_cost", $tabindex++, 10, "", "return true"));
	print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 50, "", "return true"));
    	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add WAN Link" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$wan_id = $_GET['id']; 	
	settype($wan_id, "integer");
	$wan_info = $wan->get_wan('wan_id', $wan_id);
	$location_info = $common->get_locations();
	$wan_type_info = $wan->get_wan_types();
	if ($wan_info) $service_count = mysql_num_rows($wan_info);
	if ($service_count>0) {
		if (!isset($_GET['id'])) $wan_id = mysql_result($wan_info, 0, "wan_id");
		if (!isset($_POST['ip_fnn'])) $ip_fnn = mysql_result($wan_info, 0, "ip_fnn");
		if (!isset($_POST['link_fnn'])) $link_fnn = mysql_result($wan_info, 0, "link_fnn");
		if (!isset($_POST['management_fnn'])) $management_fnn = mysql_result($wan_info, 0, "management_fnn");
		if (!isset($_POST['link_cost'])) $link_cost = mysql_result($wan_info, 0, "link_cost");
		if (!isset($_POST['management_cost'])) $management_cost = mysql_result($wan_info, 0, "management_cost");
		if (!isset($_POST['description'])) $description = mysql_result($wan_info, 0, "description");
		if (!isset($_POST['location_id'])) $location_id = mysql_result($wan_info, 0, "location_id");
		if (!isset($_POST['wan_type_id'])) $wan_type_id = mysql_result($wan_info, 0, "wan_type_id");
		if (!isset($_POST['deactive'])) $deactive = mysql_result($wan_info, 0, "deactive");
	}
	print ('<form method="post" action="" id="editform"><div class="form">');
    	$submit_js = "submitform('editform')";
	print ($common->display_textbox("IP FNN", "ip_fnn", $ip_fnn, 50, "ip_fnn", $tabindex++, 50, "", "return true"));
    	print ($common->get_listbox($location_info, "Location", "location_id", "Select a Location", $location_id, $tabindex++));    	
	print ($common->get_listbox($wan_type_info, "WAN Type", "wan_type_id", "Select a WAN Type", $wan_type_id, $tabindex++));
	print ($common->display_textbox("Link (Copper) FNN", "link_fnn", $link_fnn, 50, "link_fnn", $tabindex++, 50, "", "return true"));
	print ($common->display_textbox("Link (Copper) Cost", "link_fnn", $link_cost, 10, "link_cost", $tabindex++, 10, "", "return true"));
	print ($common->display_textbox("Management FNN", "management_fnn", $management_fnn, 50, "management_fnn", $tabindex++, 50, "", "return true"));
	print ($common->display_textbox("Management Cost", "management_cost", $management_cost, 10, "management_cost", $tabindex++, 10, "", "return true"));
	print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 50, "", "return true"));
    	print ($common->get_checkbox($deactive, "Deactive", "deactive", $tabindex++));
	print ('<input type="hidden" name="action" value="edit" />');
	print ('<input type="hidden" name="wan_id" value="'.$wan_id.'" />');
	print ('<div class="row"><input type="button" class="button" id="btnedit" value="Save Changes" onclick="'.$submit_js.'" /></div>');
	print ('</div></form>');
    print ('<p><p><form method="post" action="" id="delform"><div class="form">');
    //DELETE FUNCTIONALITY
    $submit_js = "submitform('delform')";
    $delete_js = "document.getElementById( 'confbtndel' ).style.visibility = 'visible';document.getElementById( 'btndel' ).style.visibility = 'hidden';";
    print ('<input type="hidden" name="action" value="del" />');
    print ('<input type="hidden" name="wan_id" value="'.$wan_id.'" />');
    print ('<input type="button" class="delbutton" id="btndel" value="DELETE" onclick="'.$delete_js.'" />');
    print ('<input type="button" class="confdelbutton" id="confbtndel" value="CONFIRM DELETE" onclick="'.$submit_js.'" />');
    print ('</div></form></p></p>');	
    //print ($common->display_table_wan($wan_comments, 'Comments', 'wan_comments', 'True'));
	unset($wan_info);
} else if (isset($_GET['action']) && $_GET['action'] == "add_comment" && isset($_GET['id'])) {
	$wan_id = $_GET['id'];
	settype($wan_id, "integer");	
	$wan_comments = $wan->get_wan_comments('wan_id', $wan_id);
	print ('<form method="post" action="" id="addcommentform"><div class="form">');
	$submit_js = "submitform('addcommentform')";
	print ($common->display_textbox("Comment", "notes_text", "", 75, "notes_text", $tabindex++, 300, "", ""));
	print ('<input type="hidden" name="action" value="add_comment" />');
	print ('<input type="hidden" name="wan_id" value="'.$wan_id.'" />');
	print ('<input type="button" class="button" id="addcomment" value="Add Comment" onclick="'.$submit_js.'" />');
	print ('</div></form></p></p>');	
	print ($common->display_table_wan($wan_comments, 'Comments', 'wan_comments', 'True'));
} else {
	$wan_info = $wan->get_wan_view("", "", $sort);
	$count = mysql_num_rows($wan_info);
}
?>
           
                        <?php
                        		
                                if (isset($wan_info)){
	                                print ('<form method="get" action="" id="srchform" ><div class="form">');
    	                            $js = "submitform('srchform');";
    	                            print ($common->display_textbox("IP FNN", "IP FNN", "", 25, "ip_fnn", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
				    print ('<input type="submit" class="button" id="search" value="Search IP FNN" />');
					print ('<div class="row">&nbsp;</div>');
					print ('<div class="row">');
					print ('</div>');
					print ('</div><div class="row">');
					$common->display_table_wan($wan_info, $title, "wan_details", "True");
                        	        print ('</div>');
					print ('</form>');
                            	    $js = "document.getElementById('ip_fnn').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
