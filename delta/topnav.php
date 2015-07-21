<?php
if($_SERVER["HTTPS"] != "on") {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
   exit();
}

if (!session_id()) session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_config.inc");
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/security_class.php");
if (isset($_GET['module'])) {
        $module = $_GET['module'];
} else {
        $module = 0;
}

$db_conn = new db_connect();
$security = new security();

if (!isset($exempt_page) || $exempt_page != true) {
        $security->check_login();
}
if ($module_name == "software") {
	$security->check_software_access();	
}
if ($module_name == "sims") {
	$security->check_sim_access();
}
if ($module_name == "voice") {
	$security->check_voice_access();
}
if (!isset($title))$title = "Home";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="/css/main.css" />
<link rel="stylesheet" type="text/css" href="/css/display.css" />
<script src="/js/main.js" type="text/javascript"></script>


<title><?php print($title .' - '.SYSTEM_NAME); ?></title>
</head>
<body>
<div id="main_wrapper">
    <div id="logo_top">
                <div id="cust_image">
                <img src="/site_images/djj_logo.jpg" alt="<?php print (SITE_OWNER); ?>" />
        </div>
        <div id="cust_top">
                <p>
                        <?php print (SITE_OWNER); ?><br />
                        <?php print (SITE_ADDRESS1); ?><br />
                        <?php print (SITE_ADDRESS2); ?>
                        </p>
        </div>
        <div id="login_bar">
        <img src="/site_images/icon-sharkfin.gif" alt="" />
        <div id="login_details">
                        <?php
                        if (isset($_SESSION['username'])) {
                        ?>
                        Logged in as: <?php print ($_SESSION['username']); 
				if ($_SESSION['software_admin'] == "True") print (" (Software)"); 
				if ($_SESSION['sim_admin'] == "True") print (" (SIM)");
				if ($_SESSION['voice_admin'] == "True") print (" (Voice)");
				?> - <a href="/login.php?logout">Log Out</a>
                        <?php
                        } else {
                                ?>
                                Not logged in - <a href="/login.php">Log In</a>
                                <?php
                        }
                        ?>
        </div>
        </div>
    </div>
    <div id="nav_topbar">
    <div id="nav_topbar_inner">
            <div id="nav_topbar_links">
		<?php if (isset($_SESSION)) {
                        if (($_SESSION['software_admin'] == "True")){?>
			<a href="/modules/software.php">The Software</a>&nbsp;&nbsp;
			<?php }?>
                        <?php if (($_SESSION['sim_admin'] == "True")) {?>
                        	|&nbsp;&nbsp;<a href="/modules/sim_db.php">The SIMS</a>
                        <?}?>
			<?php if (($_SESSION['voice_admin'] == "True")) {?>
                        	|&nbsp;&nbsp;<a href="/modules/voice.php">The Voice</a>
                        <?}?>
			<?php if (($_SESSION['wan_admin'] == "True")) {?>
                        	|&nbsp;&nbsp;<a href="/modules/net.php">The Net</a>
                        <?}?>
                        	&nbsp;|&nbsp;<?php if (isset($_SESSION['username'])) print ('<a href="/login.php?logout">Log Out</a>'); ?>
		<?php }?>

        </div>
        <div id="nav_topbar_breadcrumb">
            Powered by: <?php print(SYSTEM_NAME); ?>
        </div>
    </div>
    </div>
    <div id="nav_left">
        <h4><?php print ($title); ?></h4>
        <hr />
        <div id="module_icon">
	<img src="/site_images/logo_<?php print($module_name); ?>.jpg" alt="Home" />
					

        </div>
    <div id="nav_left_inner">
        <div id="navMenu">
                        <?php 
                        if (isset($_SESSION['username'])) {
                        ?>
            <ul>
                    <?php
                    if ($module_name == 'software') {
                    ?>
                    	<li class="top"></li>
	                    <?php if ($_SESSION['software_admin'] == "True") {?>
	                    <li><a class="noline" href="/modules/assets.php">Assets</a></li>
	                    <li><a href="/modules/products.php">Products</a></li>
	                    <li><a href="/modules/locations.php">Locations</a></li>
	                    <?php } ?>
	                    <li class="bottom"></li>
                    <?php
                    } else if ($module_name == 'sim_db') {
                    ?>
                    	<li class="top"></li>
	                    <?php if ($_SESSION['sim_admin'] == "True") {?>
	                    <li><a class="noline" href="/modules/people.php">The People</a></li>
	                    <li><a href="/modules/cost_centres.php">The Cost Centres</a></li>
	                    <li><a href="/modules/pools.php">The Pools</a></li>
	                    <li><a href="/modules/sim_bills.php">The SIM Bills</a></li>
			    <?php } ?>
	                    <li class="bottom"></li>
                    <?php
                    } else if ($module_name == 'voice') {
                    ?>
                    	<li class="top"></li>
	                	<?php if ($_SESSION['voice_admin'] == "True") {?>
	                    	<li><a class="noline" href="/modules/people.php">The People</a></li>
	                	<li><a href="/modules/cost_centres.php">The Cost Centres</a></li>
				<li><a href="/modules/locations.php">The Locations</a></li>
				<li><a href="/modules/voice_types.php">The Service Types</a></li>
				<li><a href="/modules/voice_bills.php">The Voice Bills</a></li>
			    	<?php } ?>
	                    	<li class="bottom"></li>
                    <?php
		} else if ($module_name == 'net') {
                    ?>
                    	<li class="top"></li>
	                	<?php if ($_SESSION['wan_admin'] == "True") {?>
	                    	<li><a class="noline" href="/modules/wan_types.php">The Wan Types</a></li>
				<li><a href="/modules/locations.php">The Locations</a></li>
				<li><a href="/modules/wan_bills.php">The WAN Bills</a></li>		    	
			    	<?php } ?>
	                    	<li class="bottom"></li>
                    <?php
		}
                    ?>
            </ul>
                        <?php
                        } elseif ($_SERVER['SCRIPT_NAME'] != '/login.php') {
                                ?>
                                        <form id="login" action="/login.php" method="post">
                                                <p>Username:<br />
                                                <input type="text" name="username" class="textbox" /><br />
                                                Password:<br />
                                                <input type="password" name="password" class="textbox" /><br />
                                                <input type="submit" name="login" value="login" />
                                                </p>
                                        </form>
                                <?php
                        } ?>
        </div>
    </div>
    </div>
    <div id="site_content">
        <div id="nav_content">
        <div id="nav_content_inner">
                        <?php
				//print_r($_SESSION);
                           if (isset($_SESSION['software_admin']) && $_SESSION['software_admin'] == "True" && $module_name != 'home') {
                        ?>
                                                        <div class="content_menu_item">
                                                                <a href="/modules/<?php print($module_name); ?>.php">
                                                                
                                                                        Home
                                                                
                                                                </a>&nbsp;|
                                                        </div>
                                                        <?php
                                                        	if (!isset($_REQUEST['action'])){
                                                        ?>
								<div class="content_menu_item">
                                                                <?php if (($title != "The SIM Bills") && ($title != "The Voice Bills") && ($title != "The WAN Bills")) { ?>
									<a href="?action=add" onclick="">
                                                                	Add
                                                                	</a>&nbsp;|
								<?php } else { ?>
									<a href="?action=upload" onclick="">
                                                                	Upload
                                                                	</a>&nbsp;|
								<?php } ?>
                                                        	</div>
                                                        <?php
                           								}
                                                        ?>
                                                        
                                <?php
                                } else {
                                ?>
                                                        <div class="content_menu_item">
                                                                <a href="/index.php">
                                                                
                                                                        Home
                                                                
                                                                </a>
                                                        </div>
                                <?php
                                }
                                                            
                                ?>
        </div>
        </div>
        <div id="page_content">
        <div id="page_content_inner">
                
