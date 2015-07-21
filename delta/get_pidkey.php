<?php
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_config.inc");
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/software_class.php");
$db_conn = new db_connect();
$software = new software();
$asset_tag = "";
$identifier = "";
if (isset($_GET['asset_tag'])) $asset_tag = $_GET['asset_tag'];
if (isset($_GET['identifier'])) $identifier = $_GET['identifier'];
print ($software->get_pidkey($asset_tag, $identifier));
?>