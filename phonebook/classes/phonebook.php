<?php
class phonebook {

        function phonebook() {
        
        }


function get_people($att = 'givenname', $srch = "", $singleuser = 0) {
		$ldapconn = ldap_connect(LDAP_SERVER) or die("Could not connect to LDAP server - ".LDAP_SERVER);
		if ($ldapconn) {
                    $ldapanon = ldap_bind($ldapconn);
                    $attributes = array("givenname", "dn", "mail", "cn", "fullName", "l", "mobile", "sn", "mail", "telephonenumber", "title", "ou", "facsimileTelephoneNumber");
                    $size_limit = 0;
		    if ($srch != "") {
				if ($singleuser == 1) {
					$ldap_res = ldap_search($ldapconn, "o=djj", '(&(objectclass=person)('.$att.'='.$srch.')(!(logindisabled=true))(!(description=tag:no_show_in_eguide)))', $attributes, 0, $size_limit);
				} else {
					$ldap_res = ldap_search($ldapconn, "o=djj", '(&(objectclass=person)('.$att.'='.$srch.'*)(!(logindisabled=true))(!(description=tag:no_show_in_eguide)))', $attributes, 0, $size_limit);
				}
	        } else {
				$ldap_res = ldap_search($ldapconn, "o=djj", '(&(objectclass=person)(!(logindisabled=true)))(!(description=tag:no_show_in_eguide))', $attributes, 0, $size_limit);
		    }
		    ldap_sort($ldapconn, $ldap_res, 'fullname');
                    $info = ldap_get_entries($ldapconn, $ldap_res);
                    ldap_unbind($ldapconn);
                    $count = $info['count'];
                     
                    if ($count == 0) {
                            return false;
                    } else {
			    return $info;
		    }
                  
		}

}// end of get_people()

		
}// end of class

?>
