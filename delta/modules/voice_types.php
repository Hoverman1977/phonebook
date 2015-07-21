<?php

if (!session_id()) session_start();
if (!isset($_SESSION['module_name'])) {
	$module_name = "voice_types";
} else {
	$module_name = $_SESSION['module_name'];
}
$title = "The Voice Types";
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/voice_class.php");
$voice = new voice();
$common = new common();
$tabindex = 1;
$description = "";
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$description = $_POST['description'];
		$message = $voice->add_voice_type($description);
	} else if ($_POST['action'] == 'edit') {
		$voice_type_id = $_POST['id'];
		$description = $_POST['description'];
		$message = $voice->edit_voice_type($voice_type_id, $description);
	}
}
print ('<div id="message">'.$message.'</div>');
if (isset($_GET['description'])){
	$description = $_GET['description'];
	$voice_type_info = $voice->get_voice_types("description", $description);
	$count = mysql_num_rows($voice_type_info);
    $title = $title." | ".$count." total";
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
	print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 100, "", ""));
	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add Service Type" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$voice_type_id = $_GET['id'];
	settype($voice_type_id, "integer");	
	$voice_type_info = $voice->get_voice_types("voice_type_id", $voice_type_id);
	if ($voice_type_info) $voice_type_count = mysql_num_rows($voice_type_info);
	if ($voice_type_count>0) {
		if (!isset($_POST['description'])) $description = mysql_result($voice_type_info, 0, "description");
		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
		print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 100, "", ""));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="id" value="'.$voice_type_id.'" />');
	print ('<input type="button" class="button" id="btnedit" value="Update Service Type" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($voice_type_info);
} else {
	$voice_type_info = $voice->get_voice_types();
	$count = mysql_num_rows($voice_type_info);
    $title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($voice_type_info)){
	                                print ('<form method="get" action="" id="srchform" onsubmit="return false;"><div class="form">');
    	                            $js = "submitform('srchform');return false;";
    	                            print ($common->display_textbox("Description", "description", "", 50, "description", $tabindex++, 1000, "", "return true"));
									print ('<input type="button" class="button" id="search" value="Search Types" onclick="'.$js.'" />');
                    	            print ('</div>');
									print ('<div>');
									$common->display_table_radio($voice_type_info, $title, "voice_type_details", false, "False", false);
                        	        print ('</div></form>');
                            	    $js = "document.getElementById('description').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
