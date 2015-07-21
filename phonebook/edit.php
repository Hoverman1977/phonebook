<?php
$exempt_page = false;
$module_name = "phonebook";
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/phonebook.php");
$common = new common();
$phonebook = new phonebook();
$locations = $common->get_locations();

if (isset($_SESSION['username'])) {
$user_title = "";
$telephonenumber = "";
$l = "";
$facsimiletelephonenumber = "";
$mobile = "";
$dn = $_SESSION['dn'];
?>

       

<?php
	$user_info = $phonebook->get_people("cn", $_SESSION['username']);

$tabindex = 1;
if (!isset($message))$message = "";

if (isset($_POST['action'])){
	if (($_POST['action'] == 'edit')) {
		$message = $common->edit_person($_SESSION['dn'], $_POST);
		$user_title = $_POST['title'];
		$telephonenumber = $_POST['telephonenumber'];
		$l = $_POST['l'];
		$facsimiletelephonenumber = $_POST['facsimiletelephonenumber'];
		$mobile = $_POST['mobile'];
	}
}
	print ('<p><div id="message">'.$message.'</div></p>');
	$givenname = $user_info[0]['givenname'][0];
	$sn = $user_info[0]['sn'][0];
	$mail = $user_info[0]['mail'][0];
	if (!(isset($_POST['telephonenumber'])) && (isset($user_info[0]['telephonenumber'][0]))) $telephonenumber = $user_info[0]['telephonenumber'][0];
	if (!(isset($_POST['facsimiletelephonenumber'])) && (isset($user_info[0]['facsimiletelephonenumber'][0]))) $facsimiletelephonenumber = $user_info[0]['facsimiletelephonenumber'][0];
	if (!(isset($_POST['mobile'])) && (isset($user_info[0]['mobile'][0]))) $mobile = $user_info[0]['mobile'][0];
	if ($user_title == "" && (isset($user_info[0]['title'][0]))) $user_title = $user_info[0]['title'][0];
	if ($l == "" && (isset($user_info[0]['l'][0]))) $l = $user_info[0]['l'][0];
	print ('<h2>Your details</h2>');
	print ('<form method="post" action="" id="editform"><div class="form">');
	$submit_js = "submitform('editform')";
	print ($common->display_label("Given Name", "givenname", $givenname, 50, "givenname", $tabindex++, 50, "", ""));
	print ($common->display_label("Surname", "sn", $sn, 50, "sn", $tabindex++, 50, "", ""));
	print ($common->display_label("Email", "mail", $mail, 50, "mail", $tabindex++, 50, "", ""));
	print ($common->display_textbox("Title", "title", $user_title, 50, "title", $tabindex++, 100, "", "return CheckAlphaNumeric(event)"));
	print ($common->display_textbox("Telephone", "telephonenumber", $telephonenumber, 20, "telephonenumber", $tabindex++, 20, "", "return CheckNumeric(event)"));
	print ($common->display_textbox("Mobile", "mobile", $mobile, 20, "mobile", $tabindex++, 20, "", "return CheckNumeric(event)"));
	print ($common->display_textbox("Fax", "facsimiletelephonenumber", $facsimiletelephonenumber, 20, "facsimiletelephonenumber", $tabindex++, 20, "", "return CheckNumeric(event)"));
	print ($common->get_listbox($locations, "Location", "l", "Select a Location", $l, $tabindex++));
	print ('<input type="hidden" name="action" value="edit" />');
	print ('<input type="hidden" name="dn" value="'.$dn.'" />');
	print ('<input type="button" class="button" id="btnedit" value="Save" onclick="'.$submit_js.'" />');
	print ('</div></form>');
} else {

?>

<p>You must be logged in to be able to edit your details.</p>     

<?php
}                                        					
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
