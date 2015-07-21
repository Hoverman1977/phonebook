<?php

class security {
        function security() {
        }
        
	function logout(){
		session_destroy();
	}

        function check_login() {
                if (!isset($_SESSION['username'])) {
				$this->logout();
                                header("Location: /login.php");
                }
        }
        
        function login($username = "", $password = "") {

                if (($username != "") && ($password != "")) {
			session_regenerate_id(true);
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
					$return_msg = "Error: " . ldap_error($ldapconn);
					return $return_msg;
                                        //return false;
                                }
                                $attributes = array("dn", "mail", "groupMembership", "fullname");
                                $ldap_res = ldap_search($ldapconn, "o=djj", '(&(objectclass=inetOrgPerson)(cn='.$username.'))', $attributes);
                                $info = ldap_get_entries($ldapconn, $ldap_res);
                                $len = stripos($dn, ',');
                                $_SESSION['groups'] = $info[0]['groupmembership'];
                                $_SESSION['username'] = substr($dn, 3, $len-3);
                                $_SESSION['dn'] = $dn;
                                $_SESSION['mail'] = $info[0]['mail'][0];
				$_SESSION['fullname'] = $info[0]['fullname'][0];
                                $_SESSION['password'] = $password;
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
