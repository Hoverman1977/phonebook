<?php
$exempt_page = true;
$module_name = "home";
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");
if (isset($_SESSION['username'])) {
?>

        <h2>Click a link to use the system.</h2>
            <p>You will only be able to see the modules you have been given access to.</p>
                        <p>Please contact the Helpdesk to arrange access to this system if you require it.</p>
<?php
} else {
	session_unset();
?>
        <h2>Please log in using your network username and password.</h2>
<?php
}
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
