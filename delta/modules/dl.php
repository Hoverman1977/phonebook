<?php
//$module_name = "sim_db";
$title = "The Bills";
$req_admin = "True";
if (!session_id()) session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_config.inc");
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_conn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/security_class.php");
$module_name = $_GET['module'];
$db_conn = new db_connect();
$security = new security();

if (!isset($exempt_page) || $exempt_page != true) {
        $security->check_login();
}

if ($module_name == "sim_db") {
	$security->check_sim_access();
}
if (!isset($title))$title = "Download";

include($_SERVER['DOCUMENT_ROOT']."/classes/common_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/sim_db_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/voice_class.php");
include($_SERVER['DOCUMENT_ROOT']."/classes/wan_class.php");
$common = new common();
$sim_db = new sim_db();
$voice = new voice();

$tabindex = 1;
$today = getdate();
//print_r($_GET);
if (!isset($_GET['month'])) {
	$month = substr($today['month'], 0, 3);
} else {
	$month = $_GET['month'];
}
if (!isset($_GET['year'])) {
	$year = substr($today['year'], 2, 2);
} else {
	$year = $_GET['year'];
}
if (!isset($_GET['type']) && ($_GET['type'] != "")) {
	$type = "bizlink";
} else {
	$type = $_GET['type'];
}
//print_r($_GET);

$filename = $type."_".$month."-".$year.".csv";
if ($module_name == "sim_db") {
	if ($type == "bizlink") {
		$bill_sql = "SELECT CODE AS 'COST CENTRE', fund AS 'FUND', GL, SUM(gst_free) AS 'Amount GST FREE', SUM(total_cost-gst_free) AS 'Amount GST Inc' FROM vw_sim_bills WHERE MONTH = '".
				$month."' AND year = ".$year." GROUP BY CODE, FUND, GL ORDER BY CODE";
	} else if ($type == "telstra") {
		$bill_sql = "SELECT sim_number AS 'Service', user AS 'User', user_email AS 'User Email', manager AS 'Manager', manager_email AS 'Manager Email', cost_centre as 'Cost Centre', fund AS 'Fund', comments AS 'Comments' FROM vw_sims WHERE deactive = 'n'";
	} else if ($type == "sims") {
		$bill_sql = "SELECT * FROM vw_sims";
	} else if ($type == 'cost_centres') {
		$bill_sql = "SELECT code AS 'CODE', location AS 'Location', manager AS 'Manager', manager_email AS 'Manager Email' FROM vw_cost_centres";
	}
} else if ($module_name == "voice") {

	$bill_sql = "SELECT CODE AS 'COST CENTRE', fund AS 'FUND', GL, SUM(gst_free) AS 'Amount GST FREE', SUM(total_cost-gst_free) AS 'Amount GST Inc' FROM vw_voice_bills WHERE MONTH = '".
				$month."' AND year = ".$year." GROUP BY CODE, FUND, GL ORDER BY CODE";

} else if ($module_name == "wan") {
	$bill_sql = "SELECT * FROM vw_wan_bills WHERE MONTH = '".
				$month."' AND year = ".$year." ORDER BY service_type, service_number";
}

$export = mysql_query($bill_sql) or die(mysql_error()." -- ".$bill_sql." -- ".$type." -- ".$module_name);
//print ($bill_sql);
print ($sim_db->export($export, $filename));
