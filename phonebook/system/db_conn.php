<?php
require_once($_SERVER['DOCUMENT_ROOT']."/system/db_config.inc");

class db_connect {
// Database connectivity usefullness.
        function db_connect() {
                $conn_str = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
                if ($conn_str) {
                        mysql_select_db(DB_NAME);
                }
        }

}

?>
