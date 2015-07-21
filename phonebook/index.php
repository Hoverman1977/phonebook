<?php
$exempt_page = true;
$module_name = "phonebook";
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/phonebook.php");
$common = new common();
$phonebook = new phonebook();

if (isset($_SESSION['username'])) {
?>

       

<?php
	$user_info = $phonebook->get_people("cn", $_SESSION['username'], 1);
	if (isset($user_info)) {
		$common->display_table_radio($user_info, "Your details", "user_details", false, "True");
	}

}

$tabindex = 1;
$givenname = "";
$sn = "";

if (!isset($message))$message = "";

print ('<div id="message">'.$message.'</div>');
if (isset($_POST['givenname']) && $_POST['givenname'] != ""){
	$givenname = $_POST['givenname'];
	$people_info = $phonebook->get_people("givenname", $givenname);
	$title = "People with a first name '".$givenname."'";
	$count = $people_info['count'];
    $title = $title." | ".$count." total";
} else if (isset($_POST['action']) && $_POST['action'] == "edit" && isset($_POST['cn'])) {
	$cn = $_POST['cn'];
	$people_info = $phonebook->get_people("givenname", $givenname);
	
}

?>
<p>
          <h2>Search for a person:</h2>
</p>
<?php
print ('<form method="post" action="" id="srchform" onsubmit=""><div class="form">');
$js = "submitform('srchform');";
print ($common->display_textbox("First Name", "givenname", "", 50, "givenname", $tabindex++, 50, "", "return CheckAlphaNumeric(event)"));
print ('<input type="button" class="button" id="search" value="Search" onclick="'.$js.'" />');
print ('</div>');
print ('<div>');
if (isset($people_info)){
	$common->display_table_radio($people_info, $title, "people_details", false, "True");
}
print ('</div></form>');
$js = "document.getElementById('givenname').focus();";
print ('<script type="text/javascript">'.$js.'</script>');
                                           
					
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
