<?php

class security {
        function security() {
        }
        
	function logout(){
		/*		
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		    );
		}
		*/
		// Finally, destroy the session.
		session_destroy();
	}

        function check_login() {
                if (!isset($_SESSION['username'])) {
				$this->logout();
                                header("Location: /login.php");
                }
        }
        function check_software_access() {
                if (!isset($_SESSION['software_admin']) || $_SESSION['software_admin'] != "True") {
				$this->logout();
                                header("Location: /index.php");
                }
        }
        function check_sim_access() {
       		if (!isset($_SESSION['sim_admin']) || $_SESSION['sim_admin'] != "True") {
				$this->logout();
                                header("Location: /index.php");
                }
        }
        function check_voice_access() {
       		if (!isset($_SESSION['voice_admin']) || $_SESSION['voice_admin'] != "True") {
			$this->logout();
			header("Location: /index.php");
	        }
        }
	function check_wan_access() {
       		if (!isset($_SESSION['wan_admin']) || $_SESSION['wan_admin'] != "True") {
			$this->logout();
			header("Location: /index.php");
	        }
        }       
        function login($username = "", $password = "") {
		//$this->logout();
		//session_start();
                if (($username != "") && ($password != "")) {
			//session_start();
			session_regenerate_id(true); 
			//$_SESSION = array();
                        $ldapconn = ldap_connect(LDAP_SERVER) or die("Could not connect to LDAP server.");
                        if ($ldapconn) {
				
                                $ldapanon = ldap_bind($ldapconn);
                                $attributes = array("dn");
                                $ldap_res = ldap_search($ldapconn, "o=djj", '(&(objectclass=inetOrgPerson)(cn='.$username.'))', $attributes);
                                $info = ldap_get_entries($ldapconn, $ldap_res);
                                ldap_unbind($ldapconn);
                                if ($info['count'] == 0) {
                                        return false;
                                }
                                $dn =  $info[0]['dn'];
				//ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
                                $ldapconn = ldap_connect(LDAP_SERVER) or die("Could not connect to LDAP server.");
				ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_start_tls($ldapconn);
                                $ldapauth = ldap_bind($ldapconn, $dn, $password);
                                if (!$ldapauth) {
                                        return false;
                                }
                                $attributes = array("dn", "mail", "groupMembership");
                                $ldap_res = ldap_search($ldapconn, "o=djj", '(&(objectclass=inetOrgPerson)(cn='.$username.'))', $attributes);
                                $info = ldap_get_entries($ldapconn, $ldap_res);
                                $len = stripos($dn, ',');
                                $_SESSION['groups'] = $info[0]['groupmembership'];
                                $_SESSION['username'] = substr($dn, 3, $len-3);
                                $_SESSION['dn'] = $dn;
                                $_SESSION['mail'] = $info[0]['mail'][0];
                                if (in_array("cn=Software_Admins,o=DJJ", $_SESSION['groups'])) {
                                        $_SESSION['software_admin'] = "True";
                                } else {
                                        $_SESSION['software_admin'] = "False";
                                }
                                if (in_array("cn=SIM_Admins,o=DJJ", $_SESSION['groups'])) {
                                        $_SESSION['sim_admin'] = "True";
                                } else {
                                        $_SESSION['sim_admin'] = "False";
                                }
				if (in_array("cn=Voice_Admins,o=DJJ", $_SESSION['groups'])) {
                                        $_SESSION['voice_admin'] = "True";
                                } else {
                                        $_SESSION['voice_admin'] = "False";
                                }
				if (in_array("cn=WAN_Admins,o=DJJ", $_SESSION['groups'])) {
                                        $_SESSION['wan_admin'] = "True";
                                } else {
                                        $_SESSION['wan_admin'] = "False";
                                }
                                ldap_unbind($ldapconn);
                        }
                }
        }

        function audit($username = "", $audit_text = "") {
                if ($username != "" && $audit_text != "") {
                        $audit_sql = "INSERT INTO audit (username, audit_text, audit_timestamp) VALUES ('".$username."', '".$audit_text."', now())";
                        $audit_res = mysql_query($audit_sql) or die(mysql_error());
                }
        }
}//end of class

?>
