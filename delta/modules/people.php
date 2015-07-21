<?php

if (!session_id()) session_start();
if (!isset($_SESSION['module_name'])) {
	$module_name = "people";
} else {
	$module_name = $_SESSION['module_name'];
}

$title = "The People";

include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
$common = new common();
$tabindex = 1;
$asset_id = 0;
$description = "";
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$fullname = $_POST['fullname'];
		$mail = $_POST['mail'];
		$message = $common->add_person($fullname, $mail);
	} else if ($_POST['action'] == 'edit') {
		$_id = $_POST['id'];
		$fullname = $_POST['fullname'];
		$mail = $_POST['mail'];
		$message = $common->edit_person($_id, $fullname, $mail);
	}
}
print ('<div id="message">'.$message.'</div>');
if (isset($_GET['fullname']) && $_GET['fullname'] != "" && (!isset($_GET['action']))){
	$fullname = $_GET['fullname'];
	$people_info = $common->get_users("fullname", $fullname);
	$count = mysql_num_rows($people_info);
    $title = $title." | ".$count." total";
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
	print ($common->display_textbox("Full Name", "fullname", $fullname, 50, "fullname", $tabindex++, 100, "", ""));
	print ($common->display_textbox("Email", "mail", $mail, 50, "mail", $tabindex++, 100, "", ""));
	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add person" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$_id = $_GET['id'];
	settype($_id, "integer");	
	$people_info = $common->get_users("_id", $_id);
	if ($people_info) $people_count = mysql_num_rows($people_info);
	if ($people_count>0) {
		if (!isset($_POST['fullname'])) $fullname = mysql_result($people_info, 0, "fullname");
		if (!isset($_POST['mail'])) $mail = mysql_result($people_info, 0, "mail");
		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
		print ($common->display_textbox("Full Name", "fullname", $fullname, 50, "fullname", $tabindex++, 100, "", ""));
		print ($common->display_textbox("Email", "mail", $mail, 50, "mail", $tabindex++, 100, "", ""));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="id" value="'.$_id.'" />');
	print ('<input type="button" class="button" id="btnedit" value="Update Person" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($people_info);
} else {
	$people_info = $common->get_users();
	$count = mysql_num_rows($people_info);
    $title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($people_info)){
	                                print ('<form method="get" action="" id="srchform" onsubmit="return false;"><div class="form">');
    	                            $js = "submitform('srchform');return false;";
    	                            print ($common->display_textbox("Name", "fullname", "", 30, "fullname", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
									print ('<input type="button" class="button" id="search" value="Search People" onclick="'.$js.'" />');
                    	            print ('</div>');
									print ('<div>');
									$common->display_table_radio($people_info, $title, "people_details", false, "False", false);
                        	        print ('</div></form>');
                            	    $js = "document.getElementById('fullname').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
