<?php
//session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_config.inc");
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/security_class.php");
$db_conn = new db_connect();
$security = new security();
$exempt_page = true;
$module_name = "Login";
include($_SERVER['DOCUMENT_ROOT']."/topnav.php");

if (isset($_GET['logout'])){
        $security->logout();
        header("Location: /");
}

if (isset($_POST['login'])) {
        $message = $security->login($_POST['username'], $_POST['password']); // call the login method
}

if (isset($_SESSION['username'])) {
                header("Location: /");
}
//print ($_POST['username']);
//print_r($_POST);
//print_r($_SESSION);

if (!isset($message))$message = "";
print ('<div id="message">'.$message.'</div>');
?>

              <h2>Login</h2>
            <p>Please enter your username and password.</p>
                        <div id="login_form">
                        <div id="login_form_inner">
					<form id="login" action="/login.php" method="post">
                                                <p>Username:<br />
                                                <input type="text" name="username" class="textbox" /><br />
                                                Password:<br />
                                                <input type="password" name="password" class="textbox" /><br />
                                                <input type="submit" name="login" value="login" />
                                                </p>
                                        </form>
                        </div>
                        </div>
                                <p>It is an offence to gain unauthorised access to this system</p>
                                <p>If you do not have a username and password you will not be able to edit your details.</p>

<?php
//print_r($_SESSION);
include($_SERVER['DOCUMENT_ROOT']."/bottomnav.php");
?>
