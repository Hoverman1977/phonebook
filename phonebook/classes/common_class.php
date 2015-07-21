<?php

class common {
	function retmax( $num, $max ){
   		if( $num < $max )
   		{
       		return $num;
   		}
		else
		{
		return $max;
		}
	} // end of retmax()

	function GetColumnLength($res_info, $fieldname) {
		$cols = mysql_num_fields($res_info);
		for ($x = 0; $x < $cols; $x++) {
			if ($fieldname == mysql_field_name($res_info, $x)) {
				return mysql_field_len($res_info, $x);
			}
		}
		return 0;
	}	

	function GetTextField($res_info, $fieldtext, $fieldname, $tabindex, $maxlength = 0, $isArea = 0, $size = 0, $num = 0, $required = 0, $onchange = "") {
		$cols = mysql_num_fields($res_info);
		for ($x = 0; $x < $cols; $x++) {
			if ($fieldname == mysql_field_name($res_info, $x)) {
				if ($size == 0) $size = mysql_field_len($res_info, $x);
				break;
			}
		}
		if (isset($_POST[$fieldname])) {
			$value = $_POST[$fieldname];
		}
		else {
			$rows = mysql_num_rows($res_info);
			if ($rows>=1) $value = mysql_result($res_info, 0, $fieldname);
			else $value = "";
		}
		if ($isArea == 1) {
			$maxlength = $this->GetColumnLength;
			$size = $this->retmax($this->GetColumnLength($res_info, $fieldname),37);
			$output = '<div class="row"><span class="label">'.$fieldtext.':</span>';		
    	    $output = $output.'<span class="formw">
			<textarea name="'.$fieldname.'" class="textbox" id="'.$fieldname.'" maxlength="'.$maxlength.'" tabindex="'.$tabindex.'"';
			$output = $output.' cols="'.$size.'" rows="5">'.$value.'</textarea>
			</span></div>';
		} else {
			if ($size > 50) $size = $this->retmax($this->GetColumnLength($res_info, $fieldname),50);
			
			if ($maxlength == 0) {
				$maxlength = $size;
			}
			$output = '<div class="row"><span class="label">
			';
			if ($required == 1) {
				$output = $output.'<span class="authText">
				';
			}
			$output = $output.$fieldtext.':';
			if ($required == 1) {
				$output = $output.'</span>';
			}
			$output = $output.'</span>';		
    	    $output = $output.'<span class="formw">
			<input name="'.$fieldname.'" type="text" id="'.$fieldname.'" class="textbox" onchange="'.$onchange.'" ';
			if ($num == 1) {
				$output = $output.' onkeypress="return num();" ';
			}
			$output = $output.'value="'.$value.'" tabindex="'.$tabindex.'" size="'.$size.'" maxlength="'.$maxlength.'"/>
			</span></div>';
		}
		return $output;
	} //end of GetTextField()

	function get_listbox($res_info, $fieldname, $name, $firstitem, $selected = 0, $tabindex = 50, $onchange = "") {
		$rows = mysql_num_rows($res_info);
		$output = '<div class="row"><span class="label">'.$fieldname.':</span>';		
        	$output = $output.'<span class="formw">';
        	$output = $output.'<select name="'.$name.'" id="'.$name.'" class="textbox" tabindex="'.$tabindex.'" onchange="'.$onchange.'">';
		if ($firstitem != "") $output = $output.'<option value="0">'.$firstitem;
		
		for($i = 0; $i < $rows; $i++) { 
			$value = mysql_result($res_info, $i, 3);
			$text  = mysql_result($res_info, $i, 3);
			if ($value == $selected) {
				$output = $output.'
				<option value="'.$value.'" selected>'.$text;
			} else {
				$output = $output.'
				<option value="'.$value.'">'.$text;
			}
		}
		$output = $output.'</select>';
		$output = $output.'</span></div>';
		return $output;
	} // end of get_listbox()
	
	function get_checkbox($value = 'n', $title, $name, $tabindex = 50) {
		$output = '
		<div class="row"><span class="label">'.$title.':</span>';		
		$output = $output.'<span class="formw">';
		$output = $output.'<input type="checkbox" name="'.$name.'" value="y"';
		if ($value == 'y') { $output = $output.' checked="checked"'; }
		
		$output = $output.' />';
		$output = $output.'</span></div>
		';
		return $output;
	}

    function isOdd($num){
        return ($num%2) ? TRUE : FALSE;
    }

	function display_textbox($fieldtext, $fieldname, $value, $size = 37, $id, $tabindex, $maxlength, $onchange = "", $onkeypress = "") {
		$output = '<div class="row"><span class="label">'.$fieldtext.':</span>';
        	$output .= '<span class="formw">';
		$output .= '<input type="text" name="'.$id.'" id="'.$id.'" class="textbox" ';
		$output .= 'value="'.$value.'" tabindex="'.$tabindex.'" size="'.$size.'" maxlength="'.$maxlength.'" onchange="'.$onchange.'" onkeypress="'.$onkeypress.'" />';
		$output .= '</span>';
		$output .= '</div>';
		return $output;
	}

	function display_label($fieldtext, $fieldname, $value, $size = 37, $id, $tabindex, $maxlength, $onchange = "", $onkeypress = "") {
		$output = '<div class="row"><span class="label">'.$fieldtext.':</span>';
        	$output .= '<span class="formw">';
		$output .= '<input type="text" name="'.$id.'" id="'.$id.'" class="textbox" ';
		$output .= 'value="'.$value.'" tabindex="'.$tabindex.'" size="'.$size.'" maxlength="'.$maxlength.'" onchange="'.$onchange.'" onkeypress="'.$onkeypress.'" disabled/>&nbsp;';
		$output .= '<span id="message">Read only</span>';
		$output .= '</span>';
		$output .= '</div>';
		return $output;
	}


        function display_table_radio($res_info, $title = "", $tablename = "default", $vertical = false, $admin_check = "False", $licence_table = false) {
	        $rows = $res_info['count'];
                $cols = 7; //HARD CODED BECAUSE LDAP IS RUBBISH
		$locations = $this->get_locations();
                if ($rows == 0) {
                	print ('<strong>No results found</strong>');
                	return false;
		}
		print('<table id="'.$tablename.'">');
                print('<tr><th colspan="'.$cols.'">'.$title.'</th></tr>');
                print ('<tr>');
		        //HARD CODED BECAUSE LDAP IS RUBBISH
		        print ('<td><strong>Name</strong></td>');
		        print ('<td><strong>Title</strong></td>');
		        print ('<td><strong>Location</strong></td>');
		        print ('<td><strong>Email</strong></td>');
		        print ('<td><strong>Phone</strong></td>');
		        print ('<td><strong>Mobile</strong></td>');
			print ('<td><strong>Fax</strong></td>');
			//END HARD CODED RUBBISH
		print ('</tr>');
                for($i = 0; $i < $rows; $i++) {
                        if ($this->isOdd($i)) {
                               print ('<tr>');
                        } else {
                               print ('<tr class="tablecelleven">');
                        }

			// HARD CODED BECAUSE LDAP IS RUBBISH	
			if (isset($res_info[$i]['fullname'][0])) { 
				print ('<td>'.$res_info[$i]['fullname'][0].'</td>');
			} else {
				print ('<td>-</td>'); 
			}
			if (isset($res_info[$i]['title'][0])) { 
				print ('<td>'.$res_info[$i]['title'][0].'</td>');
			} else {
				print ('<td>-</td>'); 
			}
			$rows2 = 0;
			if (isset($res_info[$i]['l'])) {
				$location = $res_info[$i]['l'][0];
				$rows2 = mysql_num_rows($locations);
				$address = $location;
				for ($r = 0; $r < $rows2; $r++) {
					$l = mysql_result($locations, $r, 'description');
					if ($l == $location) {
						$address .= '<br />'.mysql_result($locations, $r, 'address');
						break;
					}
				}
				print ('<td>'.$address.'</td>');
			} else {
				print ('<td>-</td>');
			
			}
			if (isset($res_info[$i]['mail'][0])) { 
				print ('<td>'.$res_info[$i]['mail'][0].'</td>');
			} else {
				print ('<td>-</td>'); 
			}
			if (isset($res_info[$i]['telephonenumber'][0])) {
				print ('<td>'.$res_info[$i]['telephonenumber'][0].'</td>');
			} else {
				print ('<td>-</td>'); 
			}
			if (isset($res_info[$i]['mobile'][0])) {
				print ('<td>'.$res_info[$i]['mobile'][0].'</td>');
			} else {
				print ('<td>-</td>'); 
			}
			if (isset($res_info[$i]['facsimiletelephonenumber'][0])) {
				print ('<td>'.$res_info[$i]['facsimiletelephonenumber'][0].'</td>');
			} else {
				print ('<td>-</td>'); 
			}
			//END HARD CODED RUBBISH
                        print('</tr>');
                }
                print("</table>");
                return true;
                
        } // end of display_table_radio()


        function edit_person($dn, $post) {
		$user_title = $post['title'];
		$telephonenumber = $post['telephonenumber'];
		$mobile = $post['mobile'];
		$facsimiletelephonenumber = $post['facsimiletelephonenumber'];
		$l = $post['l'];
		// Create a new array with only the data we want in case someone injects something they shouldn't.
		$data = Array('title' => $post['title'], 'telephonenumber' => $post['telephonenumber'], 
				'mobile' => $post['mobile'], 'facsimiletelephonenumber' => $post['facsimiletelephonenumber'], 'l' => $post['l']);
        	$return_msg = "ERROR: No changes saved. You should never see this message. (Error in common->edit_person() function)";
		if ($l == "0") {
        		$return_msg = "ERROR (23): You must select a location. Your changes have not been saved.";
		}
		else if ($user_title == "") {
			$return_msg = "ERROR (23): You must enter a title. Your changes have not been saved.";
        	} else {
			$ldapconn = ldap_connect(LDAP_SERVER) or die("Could not connect to LDAP server - ".LDAP_SERVER);
			if (!$ldapconn) {
				$return_msg = 'Cannot connect to LDAP server '.LDAP_SERVER;
				return $return_msg;
			} else {
				$ldapBind = ldap_bind($ldapconn,$dn, $_SESSION['password']);
			}
			if (!$ldapBind) {
				$return_msg = 'Cannot connect to LDAP server '.LDAP_SERVER.' as '.$dn;
				return $return_msg;
			} else {
				$result = ldap_modify($ldapconn, $dn, $data);
			}
			if ($result) {
				$return_msg = "SUCCESS: Your details have been saved.";
			} else {
				$return_msg = "ERROR: There was an error saving your details - ". ldap_error($ldapconn);
			}
		}
		return $return_msg;
	}// end of edit_person()

		
        function get_locations($column = "", $value = "") {
        	$location_sql = "SELECT location_id, CONCAT(site_code, ' - ', description) site, site_code, description, address FROM locations WHERE deleted = 'n'";
        	if ($column != "" && $value != ""){
				$location_sql .= " AND ".$column;
				if (is_string($value)) {
					$location_sql .= " LIKE '%".$value."%'";
				} else {
					$location_sql .= " = ".$value;	
				}
			}
			$location_sql .= " ORDER BY site";
        	$location_info = mysql_query($location_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$location_sql);
        	if ($location_info){
        		return $location_info;
        	} else {
        		return false;	
        	}
        }// end of get_locations()
        
} // End of Class

?>
