<?php
$module_name = "sim_db";
$title = "The Pools";
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
$owner_id = 0;
$pool_name = "";
$comment = "";

if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$pool_name = $_POST['pool_name'];
		$comment = $_POST['comment'];
		$message = $sim_db->add_pool($pool_name, $comment);
	} else if ($_POST['action'] == 'edit') {
		$owner_id = $_POST['owner_id'];
		$pool_name = $_POST['pool_name'];
		$comment = $_POST['comment'];
		$message = $sim_db->edit_pool($owner_id, $pool_name, $comment);
	}
}

print ('<div id="message">'.$message.'</div>');
if (isset($_GET['pool_name']) && $_GET['pool_name'] != "" && (!isset($_GET['action']))){
	$pool_name = $_GET['pool_name'];
	$pool_info = $sim_db->get_pool_owners("pool_name", $pool_name);
	$count = mysql_num_rows($pool_info);
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
	
	print ($common->display_textbox("Pool Name", "pool_name", $pool_name, 50, "pool_name", $tabindex++, 200, "", ""));
	print ($common->display_textbox("Comment", "comment", $comment, 50, "comment", $tabindex++, 200, "", ""));
	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add Pool" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$owner_id = $_GET['id'];
	settype($owner_id, "integer");	
	$pool_info = $sim_db->get_pool_owners("owner_id", $owner_id);
	
	if ($pool_info) $pool_count = mysql_num_rows($pool_info);
	if ($pool_count>0) {
		if (!isset($_POST['pool_name'])) $pool_name = mysql_result($pool_info, 0, "pool_name");
		if (!isset($_POST['comment'])) $comment = mysql_result($pool_info, 0, "comment");
		
		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
		print ($common->display_textbox("Pool Name", "pool_name", $pool_name, 50, "pool_name", $tabindex++, 200, "", ""));
		print ($common->display_textbox("Comment", "comment", $comment, 50, "comment", $tabindex++, 200, "", ""));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="owner_id" value="'.$owner_id.'" />');
	print ('<input type="button" class="button" id="btnedit" value="Update Pool" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($pool_info);
} else {
	$pool_info = $sim_db->get_pool_owners();
	$count = mysql_num_rows($pool_info);
    $title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($pool_info)){
	                                print ('<form method="get" action="" id="srchform" onsubmit="return false;"><div class="form">');
    	                            $js = "submitform('srchform');return false;";
    	                            print ($common->display_textbox("Pool Name", "pool_name", "", 50, "pool_name", $tabindex++, 200, "", "return CheckAlphaNumeric(event)"));
									print ('<input type="button" class="button" id="search" value="Search Pool Names" onclick="'.$js.'" />');
                    	            print ('</div>');
									print ('<div>');
									$common->display_table_radio($pool_info, $title, "pool_details", false, "False", false);
                        	        print ('</div></form>');
                            	    $js = "document.getElementById('pool_name').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
