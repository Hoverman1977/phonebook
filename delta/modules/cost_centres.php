<?php
if (!session_id()) session_start();
if (!isset($_SESSION['module_name'])) {
	$module_name = "cost_centres";
} else {
	$module_name = $_SESSION['module_name'];
}
$title = "The Cost Centres";

include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
$common = new common();
$tabindex = 1;
$cost_centre_id = 0;
$code = "";
$location = "";
$manager_id = 0;

if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$code = $_POST['code'];
		$location = $_POST['location'];
		$manager_id = $_POST['manager_id'];
		$message = $common->add_cost_centre($code, $location, $manager_id);
	} else if ($_POST['action'] == 'edit') {
		$_id = $_POST['id'];
		$code = $_POST['code'];
		$location = $_POST['location'];
		$manager_id = $_POST['manager_id'];
		$manager_info = $common->get_users("_id", $manager_id);
		$message = $common->edit_cost_centre($_id, $code, $location, $manager_id);
	}
}

print ('<div id="message">'.$message.'</div>');
if (isset($_GET['location']) && $_GET['location'] != "" && (!isset($_GET['action']))){
	$location = $_GET['location'];
	//settype($_id, "integer");
	$cost_centre_info = $common->get_cost_centres("location", $location);
	$count = mysql_num_rows($cost_centre_info);
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	print ('<form method="post" action="" id="addform"><div class="form">');
    	$submit_js = "submitform('addform')";
	$manager_info = $common->get_users();
	print ($common->display_textbox("Code", "Code", $code, 4, "code", $tabindex++, 4, "", ""));
	print ($common->display_textbox("Location", "location", $location, 50, "location", $tabindex++, 100, "", ""));
	print ($common->get_listbox($manager_info, "Manager", "manager_id", "Select a Manager", $manager_id, $tabindex++));
	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add Cost Centre" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$_id = $_GET['id'];
	settype($_id, "integer");	
	$cost_centre_info = $common->get_cost_centres("_id", $_id);
	
	if ($cost_centre_info) $cost_centre_count = mysql_num_rows($cost_centre_info);
	if ($cost_centre_count>0) {
		if (!isset($_POST['code'])) $code = mysql_result($cost_centre_info, 0, "code");
		if (!isset($_POST['location'])) $location = mysql_result($cost_centre_info, 0, "location");
		if (!isset($_POST['manager_id'])) {
			$manager = mysql_result($cost_centre_info, 0, "manager");
			$manager_info = $common->get_users("fullname", $manager);
			$manager_id = mysql_result($manager_info, 0, "_id");
		} else {
			$manager_id = $_POST['manager_id'];
		}
		print ('<form method="post" action="" id="editform"><div class="form">');
	    	$submit_js = "submitform('editform')";
		print ($common->display_textbox("Code", "code", $code, 4, "code", $tabindex++, 4, "", ""));
		print ($common->display_textbox("Location", "location", $location, 50, "location", $tabindex++, 100, "", ""));
		//print ("ID - ".$manager_id);
	    	print ($common->get_listbox($manager_info, "Manager", "manager_id", "Select a Manager", $manager_id, $tabindex++));
	    	print ('<input type="hidden" name="action" value="edit" />');
	    	print ('<input type="hidden" name="id" value="'.$_id.'" />');
		print ('<input type="button" class="button" id="btnedit" value="Update Cost Centre" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($cost_centre_info);
} else {
	$cost_centre_info = $common->get_cost_centres();
	$count = mysql_num_rows($cost_centre_info);
    	$title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($cost_centre_info)){
	                                $count = mysql_num_rows($cost_centre_info);
					print ('<form method="get" action="" id="srchform" onsubmit="return false;"><div class="form">');
    	                            $js = "submitform('srchform');return false;";
    	                            $dlcc_js = "window.open('dl.php?type=cost_centres');";
    	                            print ($common->display_textbox("Location", "location", "", 50, "location", $tabindex++, 100, "", "return CheckAlphaNumeric(event)"));
									print ('<div class="row"><input type="button" class="button" id="search" value="Search Cost Centres" onclick="'.$js.'" />');
                    	            					if ($count > 0) {
										print ('<input type="button" class="button" id="dlbill" value="Download Cost Centres" onclick="'.$dlcc_js.'" />');
									}
									print ('</div></div>');
									print ('<div>');
									$common->display_table_radio($cost_centre_info, $title, "cost_centre_details", false, "False", false);
                        	        print ('</div></form>');
                            	    $js = "document.getElementById('location').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
