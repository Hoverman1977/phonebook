<?php
$module_name = "software";
$title = "Products";
if (isset($_GET['action'])) {
	$req_admin = "True";
} else {
	$req_admin = "False";	
}
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/software_class.php");
$common = new common();
$software = new software();
$tabindex = 1;
$product_id = 0;
$identifier = "";
$long_description = "";
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == "add"){
		$identifier = $_POST['identifier'];
		$long_description = $_POST['long_description'];
		$message = $software->add_product($identifier, $long_description);	
	} else if ($_POST['action'] == 'edit') {
		$product_id = $_POST['product_id'];
		$identifier = $_POST['identifier'];
		$long_description = $_POST['long_description'];
		$message = $software->edit_product($product_id, $identifier, $long_description);	
	}
}
print ('<div id="message">'.$message.'</div>');
if (isset($_GET['action']) && $_GET['action'] == "add") {
	//$product_info = $software->get_products();
	print ('<form method="post" action="" id="addform"><div class="form">');
    $submit_js = "submitform('addform')";
	print ($common->display_textbox("Identifier", "identifier", $identifier, 35, "identifier", $tabindex++, 50, "", ""));
	print ($common->display_textbox("Description", "long_description", $long_description, 35, "long_description", $tabindex++, 50, "", ""));
	print ('<input type="hidden" name="action" value="add" />');
	print ('<input type="button" class="button" id="btnadd" value="Add Product" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['id'])) {
	$product_id = $_GET['id'];
	settype($product_id, "integer");	
	$product_info = $software->get_products('product_id', $product_id);
	if ($product_info) $product_count = mysql_num_rows($product_info);
	if ($product_count>0) {
		if (!isset($_POST['identifier'])) $identifier = mysql_result($product_info, 0, "identifier");
		if (!isset($_POST['long_description'])) $long_description = mysql_result($product_info, 0, "long_description");
		print ('<form method="post" action="" id="editform"><div class="form">');
	    $submit_js = "submitform('editform')";
	    print ($common->display_textbox("Identifier", "identifier", $identifier, 35, "identifier", $tabindex++, 50, "", ""));
	    print ($common->display_textbox("Description", "long_description", $long_description, 35, "long_description", $tabindex++, 150, "", ""));
	    print ('<input type="hidden" name="action" value="edit" />');
	    print ('<input type="hidden" name="product_id" value="'.$product_id.'" />');
		print ('<input type="button" class="button" id="btnedit" value="Update Product" onclick="'.$submit_js.'" />');
		print ('</div></form>');
	}
	unset($product_info);
} else {
	$product_info = $software->get_products();
	$count = mysql_num_rows($product_info);
    $title = $title." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($product_info)){
	                                /*
									print ('<form method="get" action="#" id="srchform" onsubmit="return false;"><div class="form">');
    	                            $js = "submitform('srchform');return false;";
    	                            print ($common->display_textbox("Identifier", "product", "", 25, "product", $tabindex++, 50, "", ""));
									print ('<input type="button" class="button" id="search" value="Search Products" onclick="'.$js.'" />');
                    	            print ('</div>');
                    	            */
									print ('<div>');
									$common->display_table_radio($product_info, $title, "product_details", false, "True");
                        	        print ('</div>');
									/*
									print ('</form>');
                            	    $js = "document.getElementById('identifier').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                	*/
                                }
                                
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
