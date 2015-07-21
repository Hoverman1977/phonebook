<?php

if (!session_id()) session_start();
if (!isset($_SESSION['module_name'])) {
	$module_name = "net";
} else {
	$module_name = $_SESSION['module_name'];
}
$title = "The WAN Types";


include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/wan_class.php");

$common = new common();
$wan = new wan();
$tabindex = 1;
$site_code = "";
$description = "";
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$description = $_POST['description'];
		$cost_per_month = $_POST['cost_per_month'];
		$wan_speed = $_POST['wan_speed'];
		$message = $wan->add_wan_type($description, $cost_per_month, $wan_speed);	
	} else if ($_POST['action'] == 'edit') {
		$wan_type_id = $_POST['wan_type_id'];
		$description = $_POST['description'];
		$cost_per_month = $_POST['cost_per_month'];
		$wan_speed = $_POST['wan_speed'];
		$message = $wan->edit_wan_type($wan_type_id, $description, $cost_per_month, $wan_speed);	
	}
}
print ('<div id="message">'.$message.'</div>');
if (isset($_GET['description']) && $_GET['description'] != ""){
	$description = $_GET['description'];
	$title = $title." for WAN Description containing '".$description."'";
	$wan_type_info = $wan->get_wan_types("description", $description);
	$count = mysql_num_rows($wan_type_info);
    $title = $title." | ".$count." total";
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	print ('<form method="post" action="" id="addform"><div class="form">');
    	$submit_js = "submitform('addform')";
	print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 100, "", ""));
	print ($common->display_textbox("Cost per Month", "cost_per_month", $cost_per_month, 35, "cost_per_month", $tabindex++, 50, "", "return CheckDecimal(event)"));
	print ($common->display_textbox("WAN Speed", "wan_speed", $wan_speed, 35, "wan_speed", $tabindex++, 50, "", ""));
	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add WAN Type" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$wan_type_id = $_GET['id'];
	settype($wan_type_id, "integer");	
	$wan_type_info = $wan->get_wan_types('wan_type_id', $wan_type_id);
	if ($wan_type_info) $wan_type_count = mysql_num_rows($wan_type_info);
	if ($wan_type_count>0) {
	if (!isset($_POST['description'])) $description = mysql_result($wan_type_info, 0, "description");
	if (!isset($_POST['cost_per_month'])) $cost_per_month = mysql_result($wan_type_info, 0, "cost_per_month");
	if (!isset($_POST['wan_speed'])) $wan_speed = mysql_result($wan_type_info, 0, "wan_speed");
	print ('<form method="post" action="" id="editform"><div class="form">');
    	$submit_js = "submitform('editform')";
    	print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 100, "", ""));
	print ($common->display_textbox("Cost per Month", "cost_per_month", $cost_per_month, 35, "cost_per_month", $tabindex++, 50, "", "return CheckDecimal(event)"));
	print ($common->display_textbox("WAN Speed", "wan_speed", $wan_speed, 35, "wan_speed", $tabindex++, 50, "", ""));
	print ('<input type="hidden" name="action" value="edit" />');
    	print ('<input type="hidden" name="wan_type_id" value="'.$wan_type_id.'" />');
	print ('<input type="button" class="button" id="btnedit" value="Save Changes" onclick="'.$submit_js.'" />');
	print ('</div></form>');
	}
	unset($wan_type_info);
} else {
	$wan_type_info = $wan->get_wan_types();
	$count = mysql_num_rows($wan_type_info);
    $title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($wan_type_info)){
			                print ('<form method="get" action="#" id="srchform" onsubmit="return false;"><div class="form">');
	    	                        $js = "submitform('srchform');return false;";
	    	                        print ($common->display_textbox("Description", "description", "", 50, "description", $tabindex++, 100, "", ""));
					print ('<input type="button" class="button" id="search" value="Search WAN Types" onclick="'.$js.'" />');
		            	        print ('</div>');
					print ('<div>');
					$common->display_table_radio($wan_type_info, $title, "wan_type_details", false, "True");
                        	        print ('</div></form>');
                            	        $js = "document.getElementById('description').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
