<?php
if (!session_id()) session_start();
$_SESSION['module_name'] = 'sim_db';
$module_name = "sim_db";
$title = "The SIMS";
if (isset($_GET['action'])) {
	$req_admin = "True";
} else {
	$req_admin = "False";	
}
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/sim_db_class.php");
$common = new common();
$sim_db = new sim_db();
$tabindex = 1;
$sim_id = 0;


if (!isset($message)) $message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$sim_number = $_POST['sim_number'];
		$pool_owner_id = $_POST['pool_owner_id'];
		$user_id = $_POST['user_id'];
		$imei = $_POST['imei'];
		$puk = $_POST['puk'];
		$cost_centre = $_POST['cost_centre'];
		$fund = $_POST['fund'];
		$gl_account = $_POST['gl_account'];
		$deleted = 'n';
		$message = $sim_db->add_sim($sim_number, $pool_owner_id, 
				$user_id, $puk, $imei, $cost_centre, $fund, $gl_account);
	} else if ($_POST['action'] == 'edit') {
		$sim_id = $_POST['sim_id'];
		$sim_number = $_POST['sim_number'];
		$pool_owner_id = $_POST['pool_owner_id'];
		$user_id = $_POST['user_id'];
		$imei = $_POST['imei'];
		$puk = $_POST['puk'];
		$cost_centre = $_POST['cost_centre'];
		$fund = $_POST['fund'];
		$gl_account = $_POST['gl_account'];
		if (isset($_POST['deactive']) && $_POST['deactive'] != '') {
			$deactive = 'y';
		} else {
			$deactive = 'n';			
		}
		$message = $sim_db->edit_sim($sim_id, $sim_number, $pool_owner_id, 
		$user_id, $puk, $imei, $cost_centre, $fund, $gl_account, $deactive);
	} else if ($_POST['action'] == 'add_comment') {
		$sim_id = $_POST['sim_id'];
		$notes_text = $_POST['notes_text'];
		$message = $sim_db->add_sim_comment($sim_id, $notes_text);
	} else if ($_POST['action'] == 'del') {
		$sim_id = $_POST['sim_id'];
		$message = $sim_db->del_sim($sim_id);
	}
}

print ('<div id="message">'.$message.'</div>');

if (isset($_GET['sim_number']) && $_GET['sim_number'] != ""){
	$sim_number = $_GET['sim_number'];
	$title = "SIM numbers containing '".$sim_number."'";
	$sim_info = $sim_db->get_sim_view("sim_number", $sim_number);
	$count = mysql_num_rows($sim_info);
    
} elseif (isset($_GET['user']) && $_GET['user'] != '') {
	$user = $_GET['user'];
	$title = "SIMs with user containing '".$user."'";
	$sim_info = $sim_db->get_sim_view("user", $user);
	$count = mysql_num_rows($sim_info);

} elseif (isset($_GET['sim_number']) && $_GET['sim_number'] == '') {
	$sim_info = $sim_db->get_sim_view();

} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	$user_info = $common->get_users();
	$pool_owner_info = $sim_db->get_pool_owners();
	$cost_centres = $common->get_cost_centres();
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
	print ($common->display_textbox("SIM Number", "sim_number", $sim_number, 10, "sim_number", $tabindex++, 10, "", "return CheckNumeric(event)"));
    print ($common->display_textbox("PUK", "puk", $puk, 10, "puk", $tabindex++, 10, "", "return CheckNumeric(event)"));
    print ($common->display_textbox("IMEI", "imei", $imei, 20, "imei", $tabindex++, 20, "", "return CheckNumeric(event)"));
    print ($common->display_textbox("Comments", "sim_comments", $sim_comments, 50, "sim_comments", $tabindex++, 200, "", "return true"));  
    print ($common->get_listbox($user_info, "User", "user_id", "Select a User", $user_id, $tabindex++));
    print ($common->get_listbox($cost_centres, "Cost Centre", "cost_centre", "Select a Cost Centre", $cost_centre, $tabindex++));
    print ($common->display_textbox("Fund", "fund", $fund, 10, "fund", $tabindex++, 10, "", "return CheckNumeric(event)"));
    print ($common->display_textbox("GL Account", "gl_account", $gl_account, 10, "gl_account", $tabindex++, 10, "", "return CheckNumeric(event)"));
    print ($common->get_listbox($pool_owner_info, "Pool", "pool_owner_id", "None", $pool_owner_id, $tabindex++));
    print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add SIM Number" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$sim_id = $_GET['id']; 	
	settype($sim_id, "integer");
	$sim_info = $sim_db->get_sims('sim_id', $sim_id);
	$user_info = $common->get_users();
	$pool_owner_info = $sim_db->get_pool_owners();
	$cost_centres = $common->get_cost_centres();
	$sim_comments = $sim_db->get_sim_comments('sim_id', $sim_id);
	if ($sim_info) $sim_count = mysql_num_rows($sim_info);
	if ($sim_count>0) {
		if (!isset($_GET['id'])) $sim_id = mysql_result($sim_info, 0, "sim_id");
		if (!isset($_POST['sim_number'])) $sim_number = mysql_result($sim_info, 0, "sim_number");
		if (!isset($_POST['user_id'])) $user_id = mysql_result($sim_info, 0, "user_id");
		if (!isset($_POST['pool_owner_id'])) $pool_owner_id = mysql_result($sim_info, 0, "pool_owner_id");
		if (!isset($_POST['imei'])) $imei = mysql_result($sim_info, 0, "imei");
		if (!isset($_POST['puk'])) $puk = mysql_result($sim_info, 0, "puk");
		if (!isset($_POST['cost_centre'])) $cost_centre = mysql_result($sim_info, 0, "cost_centre");
		if (!isset($_POST['fund'])) $fund = mysql_result($sim_info, 0, "fund");
		if (!isset($_POST['gl_account'])) $gl_account = mysql_result($sim_info, 0, "gl_account");
		if (!isset($_POST['deactive'])) $deactive = mysql_result($sim_info, 0, "deactive");

		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
	    /// MAKE THE SIM EDIT FORM HERE
	    print ($common->display_textbox("SIM Number", "sim_number", $sim_number, 10, "sim_number", $tabindex++, 10, "", "return CheckNumeric(event)"));
	    print ($common->display_textbox("PUK", "puk", $puk, 10, "puk", $tabindex++, 10, "", "return CheckNumeric(event)"));
	    print ($common->display_textbox("IMEI", "imei", $imei, 20, "imei", $tabindex++, 20, "", "return CheckNumeric(event)"));
	    //print ($common->display_textbox("Comments", "sim_comments", $sim_comments, 50, "sim_comments", $tabindex++, 200, "", "return true"));
	    
	    print ($common->get_listbox($user_info, "User", "user_id", "Select a User", $user_id, $tabindex++));
	    print ($common->get_listbox($cost_centres, "Cost Centre", "cost_centre", "Select a Cost Centre", $cost_centre, $tabindex++));
	    print ($common->display_textbox("Fund", "fund", $fund, 10, "fund", $tabindex++, 10, "", "return CheckNumeric(event)"));
	    print ($common->display_textbox("GL Account", "gl_account", $gl_account, 10, "gl_account", $tabindex++, 10, "", "return CheckNumeric(event)"));
	    print ($common->get_listbox($pool_owner_info, "Pool", "pool_owner_id", "None", $pool_owner_id, $tabindex++));
	    print ($common->get_checkbox($deactive, "Deactive", "deactive", $tabindex++));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="sim_id" value="'.$sim_id.'" />');
		print ('<div class="row"><input type="button" class="button" id="btnedit" value="Update SIM Entry" onclick="'.$submit_js.'" /></div>');
		print ('</div></form>');
	    print ('<p><p><form method="post" action="" id="delform"><div class="form">');
	    //DELETE FUNCTIONALITY
	    $submit_js = "submitform('delform')";
	    $delete_js = "document.getElementById( 'confbtndel' ).style.visibility = 'visible';document.getElementById( 'btndel' ).style.visibility = 'hidden';";
	    print ('<input type="hidden" name="action" value="del" />');
	    print ('<input type="hidden" name="sim_id" value="'.$sim_id.'" />');
	    print ('<input type="button" class="delbutton" id="btndel" value="DELETE" onclick="'.$delete_js.'" />');
	    print ('<input type="button" class="confdelbutton" id="confbtndel" value="CONFIRM DELETE" onclick="'.$submit_js.'" />');
	    print ('</div></form></p></p>');	
	    print ($common->display_table_sims($sim_comments, 'Comments', 'sim_comments', 'True'));
	}
	unset($sim_info);
} else if (isset($_GET['action']) && $_GET['action'] == "add_comment" && isset($_GET['id'])) {
	$sim_id = $_GET['id'];
	settype($sim_id, "integer");	
	$sim_comments = $sim_db->get_sim_comments('sim_id', $sim_id);
	
	print ('<form method="post" action="" id="addcommentform"><div class="form">');
	$submit_js = "submitform('addcommentform')";
	print ($common->display_textbox("Comment", "notes_text", "", 75, "notes_text", $tabindex++, 300, "", ""));
	print ('<input type="hidden" name="action" value="add_comment" />');
	print ('<input type="hidden" name="sim_id" value="'.$sim_id.'" />');
	print ('<input type="button" class="button" id="addcomment" value="Add Comment" onclick="'.$submit_js.'" />');
	print ('</div></form></p></p>');	
	print ($common->display_table_sims($sim_comments, 'Comments', 'sim_comments', 'True'));
} else {
	$sim_info = $sim_db->get_sim_view();
	$count = mysql_num_rows($sim_info);
}
?>
           
                        <?php
                        		
                                if (isset($sim_info)){
	                                print ('<form method="get" action="" id="srchform" ><div class="form">');
    	                            $js = "submitform('srchform');";
    	                            $dltelstra_js = "window.open('dl.php?type=telstra');";
    	                            $dlall_js = "window.open('dl.php?type=sims');";
    	                            print ($common->display_textbox("SIM Number", "SIM Number", "", 25, "sim_number", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
				    print ('<input type="submit" class="button" id="search" value="Search Number" />');
					print ('<div class="row">&nbsp;</div>');				    
					print ($common->display_textbox("User", "User", "", 25, "user", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
				    print ('<input type="submit" class="button" id="srch_user" value="Search User" />');
					print ('<div class="row">&nbsp;</div>');
									print ('<div class="row">');									
										print ('<input type="button" class="button" id="dload_telstra" value="Download Telstra" onclick="'.$dltelstra_js.'" />');
										print ('<input type="button" class="button" id="dload_data" value="Download All" onclick="'.$dlall_js.'" />');
                    	            					print ('</div>');
									print ('</div><div class="row">');
										$common->display_table_sims($sim_info, $title, "sim_details", "True");
                        	        				print ('</div>');
					print ('</form>');
                            	    $js = "document.getElementById('sim_number').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
