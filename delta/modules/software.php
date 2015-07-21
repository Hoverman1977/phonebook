<?php
if (!session_id()) session_start();
$_SESSION['module_name'] = 'software';
$module_name = "software";
$title = "The Software";
if ((isset($_GET['action'])) || (isset($_POST['action']))) {
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
$product_id = 0;
$pidkey = "";
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$asset_id = $_POST['asset_tag'];
		$product_id = $_POST['product'];
		$pidkey = $_POST['pidkey'];
		$message = $software->add_licence($asset_id, $product_id, $pidkey);	
	} else if ($_POST['action'] == 'edit') {
		$asset_id = $_POST['asset_tag'];
		$licence_id = $_POST['licence_id'];
		$product_id = $_POST['product'];
		$pidkey = $_POST['pidkey'];
		$message = $software->edit_licence($licence_id, $asset_id, $product_id, $pidkey);	
	}
}
print ('<div id="message">'.$message.'</div>');
if (isset($_GET['asset_tag']) && $_GET['asset_tag'] != ""){
	$asset_tag = $_GET['asset_tag'];
	$title = $title." for Asset Tag containing '".$asset_tag."'";
	$licence_info = $software->get_licences_view("asset_tag", $asset_tag);
	$count = mysql_num_rows($licence_info);
    $spare = 0;
    for ($i = 0; $i < $count; $i++) {
    	if (mysql_result($licence_info, $i, "Asset Tag") == "Spare") $spare++;
    }
    $title = $title." | ".$count." total | ".$spare." marked as 'Spare'";
} elseif (isset($_GET['action']) && $_GET['action'] == "add") {
	$asset_info = $software->get_assets();
	$location_info = $common->get_locations();
	$product_info = $software->get_products();
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
    print ($common->get_listbox($asset_info, "Asset Tag", "asset_tag", "Select an Asset", $asset_id, $tabindex++));
	//print ($common->get_listbox($location_info, "Location", "location", "Select a ", 0, $tabindex++));
    print ($common->get_listbox($product_info, "Product Code", "product", "Select a Product", $product_id, $tabindex++));
    print ($common->display_textbox("PIDKEY", "pidkey", "", 35, "pidkey", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
    print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add Licence" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$licence_id = $_GET['id']; 	
	$asset_info = $software->get_assets();
	$product_info = $software->get_products();
	$licence_info = $software->get_licences("licence_id", $licence_id);
	if ($licence_info) $licence_count = mysql_num_rows($licence_info);
	if ($licence_count>0) {
		if (!isset($_POST['asset_tag'])) $asset_id = mysql_result($licence_info, 0, "asset_id");
		if (!isset($_POST['product'])) $product_id = mysql_result($licence_info, 0, "product_id");
		if (!isset($_POST['pidkey'])) $pidkey = mysql_result($licence_info, 0, "pidkey");
		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
	    print ($common->get_listbox($asset_info, "New Asset Tag", "asset_tag", "Select an Asset", $asset_id, $tabindex++));
	    print ($common->get_listbox($product_info, "New Product Code", "product", "Select a Product", $product_id, $tabindex++));
	    print ($common->display_textbox("PIDKEY", "pidkey", $pidkey, 35, "pidkey", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="licence_id" value="'.$licence_id.'" />');
		print ('<input type="button" class="button" id="btnedit" value="Update Licence" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($licence_info);
} else {
	$licence_info = $software->get_licences_view();
	$count = mysql_num_rows($licence_info);
    $spare = 0;
    for ($i = 0; $i < $count; $i++) {
    	if (mysql_result($licence_info, $i, "Asset Tag") == "Spare") $spare++;
    }
    $title = $title." | ".$count." total | ".$spare." marked as 'Spare'";
}
?>
           
                        <?php
                                if (isset($licence_info)){
	                                print ('<form method="get" action="" id="srchform" ><div class="form">');
    	                            $js = "submitform('srchform');";
    	                            print ($common->display_textbox("Asset Tag", "asset_tag", "", 25, "asset_tag", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
									print ('<input type="submit" class="button" id="search" value="Search Assets" />');
                    	            print ('</div>');
									print ('<div>');
									$common->display_table_radio($licence_info, $title, "licence_details", false, "True", true);
                        	        print ('</div></form>');
                            	    $js = "document.getElementById('asset_tag').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
