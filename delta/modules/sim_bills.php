<?php
$module_name = "sim_db";
$title = "The SIM Bills";

include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/sim_db_class.php");
$common = new common();
$sim_db = new sim_db();
$tabindex = 1;
$today = getdate();
$month = substr($today['month'], 0, 3);
$year = substr($today['year'], 2, 2);
$comment = "";

if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if ($_POST['action'] == 'upload') {
		print ("UPLOADING FILE...<br />");		
		// Where the file is going to be placed 
		$target_path = "uploads/";

		/* Add the original filename to our target path.  
		Result is "uploads/filename.extension" */
		$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			$message = $sim_db->process_file($target_path);
		} else {
			$message = "There was an error uploading ".$_FILES['uploadedfile']['tmp_name'].", please try again!";
		}
	}
}

print ('<div id="message">'.$message.'</div>');
if (isset($_GET['month']) && $_GET['month'] != "" && (!isset($_GET['action']))){
	$month = $_GET['month'];
	$year = $_GET['year'];	
	$bill_info = $sim_db->get_bills_by_month($month, $year);
	$count = mysql_num_rows($bill_info);
} else if (isset($_GET['action']) && $_GET['action'] == "upload") {
	print ('<form enctype="multipart/form-data" id="upload_form" action="" method="POST">');
	print ('<input type="hidden" name="MAX_FILE_SIZE" value="100000" />');
	print ('<input type="hidden" name="action" value="upload" />');	
	print ('Choose a file to upload: <input name="uploadedfile" type="file" /><br />');
	print ('<input type="submit" value="Upload File" />');
	print ('</form>');
} else {
	$bill_info = $sim_db->get_bills_by_month($month, $year);
	$count = mysql_num_rows($bill_info);
    	$title = $title." for ".$month.", ".$year." | ".$count." total";
}
?>
           
                        <?php
                                if (isset($bill_info)){
					$month_info = $sim_db->get_bill_months();
					$count = mysql_num_rows($bill_info);
					$year_info = $sim_db->get_bill_years();
	                                print ('<form method="get" action="" id="srchform" onsubmit="return false;"><div class="form">');
    	                            $js = "submitform('srchform');";
				    $dlbizlink_js = "window.open('dl.php?module=sim_db&type=bizlink&month=".$month."&year=".$year."');";
    	                            print ($common->get_listbox($month_info, "Month", "month", "Select a month", $month, $tabindex++, ""));
				    print ($common->get_listbox($year_info, "Year", "year", "Select a year", $year, $tabindex++, ""));
				    //print ($common->display_textbox("Billing Month", "month", "", 3, "month", $tabindex++, 200, "", "return CheckAlpha(event)"));
									print ('<div class="row"><input type="button" class="button" id="search" value="Get Bill" onclick="'.$js.'" />');
                    	            					if ($count > 0) {
										print ('<input type="button" class="button" id="dlbill" value="Download Bizlink" onclick="'.$dlbizlink_js.'" />');
									}
									print ('</div></div>');
									print ('<div>');
									$common->display_table_bills($bill_info, $title, "bill_details", false);
                        	        print ('</div></form>');
                            	    	$js = "document.getElementById('month').focus();";
                                	print ('<script type="text/javascript">'.$js.'</script>');
                                }
                        ?>
            
<?php
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
