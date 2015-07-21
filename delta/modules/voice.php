<?php
if (!session_id()) session_start();
$_SESSION['module_name'] = 'voice';
$module_name = "voice";
$title = "The Voice";
if (isset($_REQUEST['action'])) {
	$voice_admin = "True";
} else {
	$voice_admin = "False";	
}
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/voice_class.php");
$common = new common();
$voice = new voice();
$tabindex = 1;
$voice_id = 0;


if (!isset($message)) $message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$service_number = $_POST['service_number'];
		$description = $_POST['description'];
		$location_id = $_POST['location_id'];
		$voice_type_id = $_POST['voice_type_id'];
		$cost_centre = $_POST['cost_centre'];
		$fund = $_POST['fund'];
		$gl_account = $_POST['gl_account'];
		$deleted = 'n';
		$message = $voice->add_voice($service_number, $location_id, $voice_type_id, $description, 
				$cost_centre, $fund, $gl_account);
	} else if ($_POST['action'] == 'edit') {
		$voice_id = $_POST['voice_id'];
		$service_number = $_POST['service_number'];
		$description = $_POST['description'];
		$location_id = $_POST['location_id'];
		$voice_type_id = $_POST['voice_type_id'];
		$cost_centre = $_POST['cost_centre'];
		$fund = $_POST['fund'];
		$gl_account = $_POST['gl_account'];
		if (isset($_POST['deactive']) && $_POST['deactive'] != '') {
			$deactive = 'y';
		} else {
			$deactive = 'n';			
		}
		$message = $voice->edit_voice($voice_id, $service_number, $location_id, $voice_type_id, $description,
				$cost_centre, $fund, $gl_account, $deactive);
	} else if ($_POST['action'] == 'add_comment') {
		$voice_id = $_POST['voice_id'];
		$notes_text = $_POST['notes_text'];
		$message = $voice->add_voice_comment($voice_id, $notes_text);
	} else if ($_POST['action'] == 'del') {
		$voice_id = $_POST['voice_id'];
		$message = $voice->del_voice($voice_id);
	}
}

print ('<div id="message">'.$message.'</div>');

if (isset($_GET['service_number']) && $_GET['service_number'] != ""){
	$service_number = $_GET['service_number'];
	$title = "Service numbers containing '".$service_number."'";
	$voice_info = $voice->get_voice_view("service_number", $service_number);
	$count = mysql_num_rows($service_info);
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	$location_info = $common->get_locations();
	$cost_centres = $common->get_cost_centres();
	$voice_type_info = $voice->get_voice_types();
	print ('<form method="post" action="" id="addform"><div class="form">');
    	$submit_js = "submitform('addform')";
	print ($common->display_textbox("Service Number", "service_number", $service_number, 50, "service_number", $tabindex++, 50, "", "return true"));
    	print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 100, "", "return true"));
    	print ($common->get_listbox($location_info, "Location", "location_id", "Select a Location", $location_id, $tabindex++));    	
	print ($common->get_listbox($voice_type_info, "Service Type", "voice_type_id", "Select a Service Type", $voice_type_id, $tabindex++));
    	print ($common->get_listbox($cost_centres, "Cost Centre", "cost_centre", "Select a Cost Centre", $cost_centre, $tabindex++));
    	print ($common->display_textbox("Fund", "fund", $fund, 10, "fund", $tabindex++, 10, "", "return CheckNumeric(event)"));
    	print ($common->display_textbox("GL Account", "gl_account", $gl_account, 10, "gl_account", $tabindex++, 10, "", "return CheckNumeric(event)"));
    	//print ($common->get_listbox($pool_owner_info, "Pool", "pool_owner_id", "None", $pool_owner_id, $tabindex++));
    	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add SIM Number" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$voice_id = $_GET['id']; 	
	settype($voice_id, "integer");
	$voice_info = $voice->get_voice('voice_id', $voice_id);
	$location_info = $common->get_locations();
	$cost_centres = $common->get_cost_centres();
	$voice_type_info = $voice->get_voice_types();
	//$voice_comments = $voice->get_voice_comments();
	if ($voice_info) $service_count = mysql_num_rows($voice_info);
	if ($service_count>0) {
		if (!isset($_GET['id'])) $voice_id = mysql_result($voice_info, 0, "voice_id");
		if (!isset($_POST['service_number'])) $service_number = mysql_result($voice_info, 0, "service_number");
		if (!isset($_POST['description'])) $description = mysql_result($voice_info, 0, "description");
		if (!isset($_POST['location_id'])) $location_id = mysql_result($voice_info, 0, "location_id");
		if (!isset($_POST['voice_type_id'])) $voice_type_id = mysql_result($voice_info, 0, "voice_type_id");
		if (!isset($_POST['cost_centre'])) $cost_centre = mysql_result($voice_info, 0, "cost_centre");
		if (!isset($_POST['fund'])) $fund = mysql_result($voice_info, 0, "fund");
		if (!isset($_POST['gl_account'])) $gl_account = mysql_result($voice_info, 0, "gl_account");
		if (!isset($_POST['deactive'])) $deactive = mysql_result($voice_info, 0, "deactive");
	}
	print ('<form method="post" action="" id="editform"><div class="form">');
    	$submit_js = "submitform('editform')";
	print ($common->display_textbox("Service Number", "service_number", $service_number, 50, "service_number", $tabindex++, 50, "", "return true"));
    	print ($common->display_textbox("Description", "description", $description, 50, "description", $tabindex++, 100, "", "return true"));
    	print ($common->get_listbox($location_info, "Location", "location_id", "Select a Location", $location_id, $tabindex++));    	
	print ($common->get_listbox($voice_type_info, "Service Type", "voice_type_id", "Select a Service Type", $voice_type_id, $tabindex++));
    	print ($common->get_listbox($cost_centres, "Cost Centre", "cost_centre", "Select a Cost Centre", $cost_centre, $tabindex++));
    	print ($common->display_textbox("Fund", "fund", $fund, 10, "fund", $tabindex++, 10, "", "return CheckNumeric(event)"));
    	print ($common->display_textbox("GL Account", "gl_account", $gl_account, 10, "gl_account", $tabindex++, 10, "", "return CheckNumeric(event)"));
	print ($common->get_checkbox($deactive, "Deactive", "deactive", $tabindex++));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="voice_id" value="'.$voice_id.'" />');
		print ('<div class="row"><input type="button" class="button" id="btnedit" value="Update Service" onclick="'.$submit_js.'" /></div>');
		print ('</div></form>');
	    print ('<p><p><form method="post" action="" id="delform"><div class="form">');
	    //DELETE FUNCTIONALITY
	    $submit_js = "submitform('delform')";
	    $delete_js = "document.getElementById( 'confbtndel' ).style.visibility = 'visible';document.getElementById( 'btndel' ).style.visibility = 'hidden';";
	    print ('<input type="hidden" name="action" value="del" />');
	    print ('<input type="hidden" name="service_id" value="'.$service_id.'" />');
	    print ('<input type="button" class="delbutton" id="btndel" value="DELETE" onclick="'.$delete_js.'" />');
	    print ('<input type="button" class="confdelbutton" id="confbtndel" value="CONFIRM DELETE" onclick="'.$submit_js.'" />');
	    print ('</div></form></p></p>');	
	    //print ($common->display_table_voice($voice_comments, 'Comments', 'voice_comments', 'True'));
	unset($voice_info);
} else if (isset($_GET['action']) && $_GET['action'] == "add_comment" && isset($_GET['id'])) {
	$voice_id = $_GET['id'];
	settype($voice_id, "integer");	
	$voice_comments = $voice->get_voice_comments('voice_id', $voice_id);
	print ('<form method="post" action="" id="addcommentform"><div class="form">');
	$submit_js = "submitform('addcommentform')";
	print ($common->display_textbox("Comment", "notes_text", "", 75, "notes_text", $tabindex++, 300, "", ""));
	print ('<input type="hidden" name="action" value="add_comment" />');
	print ('<input type="hidden" name="voice_id" value="'.$voice_id.'" />');
	print ('<input type="button" class="button" id="addcomment" value="Add Comment" onclick="'.$submit_js.'" />');
	print ('</div></form></p></p>');	
	print ($common->display_table_voice($voice_comments, 'Comments', 'voice_comments', 'True'));
} else {
	$voice_info = $voice->get_voice_view();
	$count = mysql_num_rows($voice_info);
}
?>
           
                        <?php
                        		
                                if (isset($voice_info)){
	                                print ('<form method="get" action="" id="srchform" ><div class="form">');
    	                            $js = "submitform('srchform');";
    	                            //$dltelstra_js = "window.open('dl.php?type=telstra');";
    	                            //$dlall_js = "window.open('dl.php?type=sims');";
    	                            print ($common->display_textbox("Number", "Number", "", 25, "service_number", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
				    print ('<input type="submit" class="button" id="search" value="Search Number" />');
					//print ('<div class="row">&nbsp;</div>');				    
					//print ($common->display_textbox("User", "User", "", 25, "user", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
				    //print ('<input type="submit" class="button" id="srch_user" value="Search User" />');
					print ('<div class="row">&nbsp;</div>');
									print ('<div class="row">');
										//print ('<input type="button" class="button" id="dload_telstra" value="Download Telstra" onclick="'.$dltelstra_js.'" />');
										//print ('<input type="button" class="button" id="dload_data" value="Download All" onclick="'.$dlall_js.'" />');
                    	            					print ('</div>');
									print ('</div><div class="row">');
										$common->display_table_voice($voice_info, $title, "voice_details", "True");
                        	        				print ('</div>');
					print ('</form>');
                            	    $js = "document.getElementById('service_number').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
