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
	}

	
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
		// first get column length!
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
	}

	function get_listbox($res_info, $fieldname, $name, $firstitem, $selected = 0, $tabindex = 50, $onchange = "") {
		$rows = mysql_num_rows($res_info);
		$output = '<div class="row"><span class="label">'.$fieldname.':</span>';		
        $output = $output.'<span class="formw">';
        $output = $output.'<select name="'.$name.'" id="'.$name.'" class="textbox" tabindex="'.$tabindex.'" onchange="'.$onchange.'">';
		if ($firstitem != "") $output = $output.'<option value="0">'.$firstitem;
		
		for($i = 0; $i < $rows; $i++) { 
			$value = mysql_result($res_info, $i, 0);
			$text  = mysql_result($res_info, $i, 1);
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
	}
	
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

    function display_combo($res_info, $title = "") {
        print('<select name="'.$title.'">');
        $i = 0;
    	$num_rows = mysql_num_rows($res_info);
        for ($i=0; $i<$num_rows; $i++) {
                print('<option "'.mysql_result($res_info, $i, 'rating_type_id').'">'.mysql_result($res_info, $i, "description").'</option><br />');
        }
        print('</select>');
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

        function display_table_bills($res_info, $title = "", $tablename = "default", $vertical = false) {
                if ($vertical == true) {
                        print('<table id="'.$tablename.'">\n');
                        $rows = mysql_num_rows($res_info);
                        $cols = mysql_num_fields($res_info);
                        print('<tr><th colspan="'.$cols.'">'.$title.'<br /><span class="deactive">Blue = Deactive</span><br /><span class="nomanager">Red = No Manager</span>'.'</th></tr>');
                        print ('<tr>');
                        for ($x = 1; $x < $cols; $x++) {
                                $fieldname = mysql_field_name($res_info, $x);
                                print ('<td><strong>'.$fieldname.'</strong></td>');
                        }
                        print ('</tr>');
                        for($i = 0; $i < $rows; $i++) {
				$manager = mysql_result($res_info, $i, 'manager');
				$deactive = mysql_result($res_info, $i, 'deactive');
				if ($manager == '') {
					print ('<tr class="nomanager">');
				} else if ($deactive == 'y') {
					print ('<tr class="deactive">');
				} else if ($this->isOdd($i)) {
		                       print ('<tr>');
		                } else {
		                       print ('<tr class="tablecelleven">');
		                }                                

                                for ($j = 1; $j < $cols; $j++) {
                                        $field = mysql_result($res_info, $i, $j);
					if ((mysql_field_name($res_info, $j) == 'gst_free') && ($field > 0)) {
						print ('<td class="gst_free">'.$field.'</td>');
					} else {
                                        	print ('<td>'.$field.'</td>');
					}
                                }
                                print('</tr>');
                        }
                        print("</table>");
                } else {
                        print('<table id="'.$tablename.'">');
                        $rows = mysql_num_rows($res_info);
                        $cols = mysql_num_fields($res_info);
			$total_cost_total = 0;
			$gst_cost_total = 0;
			$gst_free_total = 0;
			$total_gst_exc_total = 0;
                        print('<th colspan="'.$cols.'">'.$title.'<br /><span class="deactive">Blue = Deactive</span><br /><span class="nomanager">Red = No Manager</span>'.'</th>');
                        print ('<tr>');
                        for ($x = 1; $x < $cols; $x++) {
                                $fieldname = mysql_field_name($res_info, $x);
                                print ('<td><strong>'.$fieldname.'</strong></td>');
                        }
                        print ('</tr>');
                        for($i = 0; $i < $rows; $i++) {
                                $manager = mysql_result($res_info, $i, 'manager');
				$total_cost_total = (intval($total_cost_total + mysql_result($res_info, $i, 'total_cost')*100));
				$gst_cost_total = (intval($gst_cost_total + mysql_result($res_info, $i, 'gst_cost')*100));
				$gst_free_total = (intval($gst_free_total + mysql_result($res_info, $i, 'gst_free')*100));
				$total_gst_exc_total = (intval($total_gst_exc_total + mysql_result($res_info, $i, 'total_gst_exc')*100));
				$deactive = mysql_result($res_info, $i, 'deactive');
				if ($manager == '') {
					print ('<tr class="nomanager">');
				} else if ($deactive == 'y') {
					print ('<tr class="deactive">');
				} else if ($this->isOdd($i)) {
                                       print ('<tr>');
                                } else {
                                       print ('<tr class="tablecelleven">');
                                }
                                for ($j = 1; $j < $cols; $j++) {
                                        $field = mysql_result($res_info, $i, $j);
                                        if ((mysql_field_name($res_info, $j) == 'gst_free') && ($field > 0)) {
						print ('<td class="gst_free">'.$field.'</td>');
					} else {
                                        	print ('<td>'.$field.'</td>');
					}
                                }
                                print('</tr>');
                        }
			for ($x = 1; $x < $cols; $x++) {
                                $fieldname = mysql_field_name($res_info, $x);
                                print ('<td><strong>'.$fieldname.'</strong></td>');
                        }
			print ('<tr class="deactive"><td>&nbsp;</td><td>&nbsp;</td><td>'.($total_cost_total/100).'</td><td>'.($gst_cost_total/100).'</td><td>'.($gst_free_total/100).'</td><td>'.($total_gst_exc_total/100).'</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>');
                        print("</table>");
                }
        }

        function display_table_radio($res_info, $title = "", $tablename = "default", $vertical = false, $admin_check = "False", $licence_table = false) {
                $rows = mysql_num_rows($res_info);
                $cols = mysql_num_fields($res_info);
                if ($rows == 0) {
                	print ('<strong>No results found</strong>');
                	return false;
				}
                if ($vertical == true) {
                        print('<table id="'.$tablename.'">');
                        print('<tr><th colspan="'.$cols.'">'.$title.'</th></tr>');
                        print ('<tr>');
                        for ($x = 0; $x < $cols; $x++) {
                                $fieldname = mysql_field_name($res_info, $x);
                                print ('<td><strong>'.$fieldname.'</strong></td>');
                        }
                        print ('</tr>');
                        for($i = 0; $i < $rows; $i++) {
                                if ($this->isOdd($i)) {
                                       print ('<tr>');
                                } else {
                                       print ('<tr class="tablecelleven">');
                                }
                                for ($j = 0; $j < $cols; $j++) {
                                        $field = mysql_result($res_info, $i, $j);
                                        print ('<td>'.$field.'</td>');
                                }
                                print('</tr>');
                        }
                        print("</table>");
                } else {
                        print('<table id="'.$tablename.'">');
                        print('<tr><th colspan="'.$cols.'">'.$title.'</th></tr>');
                        print ('<tr>');
                        for ($x = 0; $x < $cols; $x++) {
                                if ($x == 0) {
                                		if (($admin_check == "False") || ($admin_check == "True" && $_SESSION['software_admin'] == "True")){
                                        	$fieldname = "Select";
                                        	print ('<td><strong>'.$fieldname.'</strong></td>');
                                		}
                                } else {
                                        $fieldname = mysql_field_name($res_info, $x);
                                        print ('<td><strong>'.$fieldname.'</strong></td>');
                                }
                                
                        }
                        print ('</tr>');
                        for($i = 0; $i < $rows; $i++) {
                                if ($this->isOdd($i)) {
                                       print ('<tr>');
                                } else {
                                       print ('<tr class="tablecelleven">');
                                }
                                for ($j = 0; $j < $cols; $j++) {
                                        $field = mysql_result($res_info, $i, $j);
                                        if ($j == 0) {
                                        	if (($admin_check == "False") || ($admin_check == "True" && $_SESSION['software_admin'] == "True")){
                                        		print ('<td><a href="?action=edit&id='.$field.'">Edit</a></td>');
												//print ('<td><input type="radio" name="licence_id" value="'.$field.'" /></td>');
                                			}
                                        } else {
	                                        	if ($licence_table && ($j == $cols-1)) {
		                                        	print ('<td class="pidkey">'.$field.'</td>');	
	                                        	} else {
                                                	print ('<td>'.$field.'</td>');
                                            	}
                                        }
                                }
                                print('</tr>');
                        }
                        print("</table>");
                        return true;
                }
        }
        function display_table_sims($res_info, $title = "", $tablename = "default", $admin_check = "False") {
                $rows = mysql_num_rows($res_info);
                $cols = mysql_num_fields($res_info);
                if ($rows == 0) {
                	print ('<strong>No results found</strong>');
                	return false;
		}
                
                print('<table id="'.$tablename.'">');
                print('<tr><th colspan="'.$cols.'">'.$title.'<br /><span class="deactive">Blue = Deactive</span><br /><span class="nomanager">Red = No Manager</span>'.'</th></tr>');
                print ('<tr>');
                for ($x = 0; $x < $cols; $x++) {
                        if ($x == 0 && $tablename != 'sim_comments') {
                        		if (($admin_check == "False") || ($admin_check == "True" && $_SESSION['sim_admin'] == "True")){
                                	$fieldname = "Select";
                                	print ('<td><strong>'.$fieldname.'</strong></td>');
                        		}
                        } else {
                                $fieldname = mysql_field_name($res_info, $x);
				if ($fieldname == "deactive") {
				//DO NOTHING!
				} else {                                
					print ('<td><strong>'.$fieldname.'</strong></td>');
				}
                        }
                        
                }
                print ('</tr>');
                for($i = 0; $i < $rows; $i++) {
			$deactive = mysql_result($res_info, $i, 'deactive');
			$manager = mysql_result($res_info, $i, 'manager');
			if ($manager == '') {
				print ('<tr class="nomanager">');
			} else if ($deactive == 'y') {
				print ('<tr class="deactive">');
			} else if ($this->isOdd($i)) {
                               print ('<tr>');
                        } else {
                               print ('<tr class="tablecelleven">');
                        }
                        for ($j = 0; $j < $cols; $j++) {
				$fieldname = mysql_field_name($res_info, $j);
				$sim_id = mysql_result($res_info, $i, 0);
				if ($fieldname == "deactive") {
					//DO NOTHING!
				} else {                                
					$field = mysql_result($res_info, $i, $j);                       		
					print ('<td>');
				
		                        if ($j == 0 && $tablename != 'sim_comments') {
		                        	if (($admin_check == "False") || ($admin_check == "True" && $_SESSION['sim_admin'] == "True")) {
						
							print ('<a href="?action=edit&id='.$field.'">Edit</a></td>');
							//print ('<td><input type="radio" name="licence_id" value="'.$field.'" /></td>');
		                		}
					} else if ($fieldname == 'comments') {
							$comment_js = "window.open('sim_db.php?action=add_comment&id=$sim_id', '_self');";
							print ('<input type="button" class="button" id="add_comment" value="Add" onclick="'.$comment_js.'" />'.$field);
					} else {
						//print ($deactive);	
		                        	print ($field.'</td>');
		                        }
                               }
                        }
                        print('</tr>');
                }
                print("</table>");
                //return true;

        }

        function display_table_voice($res_info, $title = "", $tablename = "default", $admin_check = "False") {
                $rows = mysql_num_rows($res_info);
                $cols = mysql_num_fields($res_info);
                if ($rows == 0) {
                	print ('<strong>No results found</strong>');
                	return false;
		}
                
                print('<table id="'.$tablename.'">');
                print('<tr><th colspan="'.$cols.'">'.$title.'<br /><span class="deactive">Blue = Deactive</span><br /><span class="nomanager">Red = No Manager</span>'.'</th></tr>');
                print ('<tr>');
                for ($x = 0; $x < $cols; $x++) {
                        if ($x == 0 && $tablename != 'voice_comments') {
                        		if (($admin_check == "False") || ($admin_check == "True" && $_SESSION['voice_admin'] == "True")){
                                	$fieldname = "Select";
                                	print ('<td><strong>'.$fieldname.'</strong></td>');
                        		}
                        } else {
                                $fieldname = mysql_field_name($res_info, $x);
				if ($fieldname == "deactive") {
				//DO NOTHING!
				} else {                                
					print ('<td><strong>'.$fieldname.'</strong></td>');
				}
                        }
                        
                }
                print ('</tr>');
                for($i = 0; $i < $rows; $i++) {
			$manager = mysql_result($res_info, $i, 'manager');
			$deactive = mysql_result($res_info, $i, 'deactive');
			if ($deactive == 'y') {
				print ('<tr class="deactive">');
			} else if ($manager == '') {
				print ('<tr class="nomanager">');
			} else if ($this->isOdd($i)) {
                               print ('<tr>');
                        } else {
                               print ('<tr class="tablecelleven">');
                        }
                        for ($j = 0; $j < $cols; $j++) {
				$fieldname = mysql_field_name($res_info, $j);
				$voice_id = mysql_result($res_info, $i, 0);
				if ($fieldname == "deactive") {
					//DO NOTHING!
				} else {                                
					$field = mysql_result($res_info, $i, $j);                       		
					print ('<td>');
				
		                        if ($j == 0 && $tablename != 'voice_comments') {
		                        	if (($admin_check == "False") || ($admin_check == "True" && $_SESSION['voice_admin'] == "True")) {
						
							print ('<a href="?action=edit&id='.$field.'">Edit</a></td>');
							//print ('<td><input type="radio" name="licence_id" value="'.$field.'" /></td>');
		                		}
					} else if ($fieldname == 'comments') {
							$comment_js = "window.open('voice.php?action=add_comment&id=$voice_id', '_self');";
							print ('<input type="button" class="button" id="add_comment" value="Add" onclick="'.$comment_js.'" />'.$field);
					} else {
						//print ($deactive);	
		                        	print ($field.'</td>');
		                        }
                               }
                        }
                        print('</tr>');
                }
                print("</table>");
                //return true;

        }

        function display_table_wan($res_info, $title = "", $tablename = "default", $admin_check = "False") {
                $rows = mysql_num_rows($res_info);
                $cols = mysql_num_fields($res_info);
                if ($rows == 0) {
                	print ('<strong>No results found</strong>');
                	return false;
		}
                
                print('<table id="'.$tablename.'">');
                print('<tr><th colspan="'.$cols.'">'.$title.'<br /><span class="deactive">Blue = Deactive</span>'.'</th></tr>');
                print ('<tr>');
                for ($x = 0; $x < $cols; $x++) {
                        if ($x == 0 && $tablename != 'net_comments') {
                        	if ($_SESSION['wan_admin'] == "True"){
                                	$fieldname = "Select";
                                	print ('<td><strong>'.$fieldname.'</strong></td>');
                        	}
                        } else {
                                $fieldname = mysql_field_name($res_info, $x);
				if ($fieldname == "deactive") {
				//DO NOTHING!
				} else {                                
					print ('<td><strong><a href="?sort='.$fieldname.'">'.$fieldname.'</a></strong></td>');
				}
                        }
                        
                }
                print ('</tr>');
                for($i = 0; $i < $rows; $i++) {
			$deactive = mysql_result($res_info, $i, 'deactive');
			if ($deactive == 'y') {
				print ('<tr class="deactive">');
			} else if ($this->isOdd($i)) {
                               print ('<tr>');
                        } else {
                               print ('<tr class="tablecelleven">');
                        }
                        for ($j = 0; $j < $cols; $j++) {
				$fieldname = mysql_field_name($res_info, $j);
				$wan_id = mysql_result($res_info, $i, 0);
				if ($fieldname == "deactive") {
					//DO NOTHING!
				} else {                                
					$field = mysql_result($res_info, $i, $j);                       		
					print ('<td>');
				
		                        if ($j == 0 && $tablename != 'wan_comments') {
		                        	if ($_SESSION['wan_admin'] == "True") {
							print ('<a href="?action=edit&id='.$field.'">Edit</a></td>');
						}
					} else if ($fieldname == 'comments') {
							$comment_js = "window.open('net.php?action=add_comment&id=$wan_id', '_self');";
							print ('<input type="button" class="button" id="add_comment" value="Add" onclick="'.$comment_js.'" />'.$field);
					} else {
						//print ($deactive);	
		                        	print ($field.'</td>');
		                        }
                               }
                        }
                        print('</tr>');
                }
                print("</table>");
                //return true;

        }


        function get_users($column = "", $value = ""){
        	$column = addslashes($column);
            	$user_sql = "SELECT _id, fullname, mail FROM people";
			if ($column != "" && $value != ""){
				$user_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$user_sql .= " LIKE '%".$value."%'";
				} else {
					$user_sql .= " = ".$value;	
				}
			}
			$user_sql .= " ORDER BY fullname";
			$user_info = mysql_query($user_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$user_sql);
			//print($user_sql);
			if ($user_info) {
        			return $user_info;
			} else {
        			return false;
			}
        }

        function edit_person($_id = 0, $fullname, $mail) { 
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$fullname = addslashes($fullname);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($fullname == "" || $mail == "") {
        		$return_msg = "ERROR: You must provide a fullname and email address for the person.";
        		//$return_msg .= "<br />SIM ID: ".$sim_id." sim_number: ".$sim_number." pool_owner_id: ". $pool_owner_id." user_id: ".$user_id." cost_centre:".$cost_centre;
				return $return_msg;
        	} else {
        		$person_check_sql = "SELECT _id FROM people WHERE _id <> ".$_id." AND (mail = '".$mail."')";
        		//print($person_check_sql);
        		$person_check_info = mysql_query($person_check_sql) or die(mysql_error());
        		$person_count = mysql_num_rows($person_check_info);
        		if ($person_count>0) {
        			$return_msg = "ERROR: There is already a person in the database with email address '".$mail."'";
					return $return_msg;	
        		}
        		$person_sql = "UPDATE people SET fullname = '".$fullname."', mail = '".$mail."' WHERE _id = ".$_id;
        		$person_info = mysql_query($person_sql) or die(mysql_error());
        		if (!$person_info) return mysql_error();
				$return_msg = "SUCCESS: ".$fullname." successfully updated.";
				//$return_msg = $person_sql;
				return $return_msg;	        
			}		
		}
		
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
        }
        
        function add_location($site_code = "", $description = "", $address = "") {
        	//$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$description = addslashes($description);
        	$site_code = addslashes($site_code);
        	$address = addslashes($address);

        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
        	if ($site_code == "" || $description == "" || $address == '') {
        		$return_msg = "ERROR: You must provide a site code, description and an address to add a location.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	} else {
        		$location_check_sql = "SELECT location_id FROM locations WHERE site_code = '".$site_code."'";
        		$location_check_info = mysql_query($location_check_sql) or die(mysql_error());
        		$location_count = mysql_num_rows($location_check_info);
        		if ($location_count>0) {
        			$return_msg = "ERROR: There is already a location in the database with site_code '".$site_code."'";
					return $return_msg;	
        		}
				$location_sql = "INSERT INTO locations (site_code, description, address, date_created, created_by) VALUES ('".$site_code."', '".$description."', '".$address."', now(), '".$_SESSION['username']."')";
        		$location_info = mysql_query($location_sql) or die(mysql_error());
        		if (!$location_info) return mysql_error();
				$return_msg = "SUCCESS: Location successfully created.";
				//$return_msg = $product_sql;
				return $return_msg;	
        	}
        	
        	return $return_msg;
        }

		function edit_location($location_id = 0, $site_code = "", $description = "", $address = "") {
			//$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$description = addslashes($description);
        	$site_code = addslashes($site_code);
        	$address = addslashes($address);
		$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
		if ($site_code == "" || $location_id == 0 || $description == "" || $address == "") {
        		$return_msg = "ERROR: You must provide a site code, description and address when editing a location.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
			return $return_msg;
        	} else {
        		$location_check_sql = "SELECT location_id FROM locations WHERE deleted = 'n' AND site_code = '".$site_code."' AND location_id <> ".$location_id;
        		$location_check_info = mysql_query($location_check_sql) or die(mysql_error());
        		$location_count = mysql_num_rows($location_check_info);
        		$existing_location_id = mysql_result($location_check_info, 0, 'location_id');
        		if ($location_count>0 && $existing_location_id != $location_id) {
        			$return_msg = "ERROR: There is already a location in the database with site code '".$site_code."'";
					return $return_msg;	
        		}
        		$location_sql = "UPDATE locations SET site_code = '".$site_code."', description = '".$description."', address = '".$address."', last_modified = now(), modified_by = '".$_SESSION['username']."' WHERE location_id = ".$location_id;
        		$location_info = mysql_query($location_sql) or die(mysql_error());
        		if (!$location_info) return mysql_error();
				$return_msg = "SUCCESS: Location successfully updated.";
				//$return_msg = $product_sql;
				return $return_msg;	
        	}
        	return $return_msg;
		}
        
        function get_cost_centres($column = "", $value = ""){
        	$column = addslashes($column);
            $cost_centre_sql = "SELECT _id, CONCAT(code, ' - ', location, ' - ', manager) AS cost_centre, code, location, manager, manager FROM vw_cost_centres";
			if ($column != "" && $value != ""){
				$cost_centre_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
					$cost_centre_sql .= " LIKE '%".$value."%'";
				} else {
					$cost_centre_sql .= " = ".$value;	
				}
			}
			$cost_centre_sql .= " ORDER BY code, location";
			$cost_centre_info = mysql_query($cost_centre_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$cost_centre_sql);
			if ($cost_centre_info) {
        		return $cost_centre_info;
			} else {
        		return false;
			}
        }

		function add_cost_centre($code, $location, $manager_id) {
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$location = addslashes($location);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($location == "" || $code == "") {
        		$return_msg = "ERROR: You must provide a code and location for the cost centre.";
        		//$return_msg .= "<br />SIM ID: ".$sim_id." sim_number: ".$sim_number." pool_owner_id: ". $pool_owner_id." user_id: ".$user_id." cost_centre:".$cost_centre;
				return $return_msg;
        	} else {
        		$cost_centre_sql = "INSERT INTO cost_centres (code, location, manager_id) VALUES ('".$code."', '".$location."', ".$manager_id.")";
        		$cost_centre_info = mysql_query($cost_centre_sql) or die(mysql_error());
        		if (!$cost_centre_info) return mysql_error();
				$return_msg = "SUCCESS: ".$code." - ".$location." successfully created.";
				//$return_msg = $cost_centre_sql;
				return $return_msg;	
     
			}		
		}

		function edit_cost_centre($_id = 0, $code, $location, $manager_id) { 
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$location = addslashes($location);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($location == "" || $code == "") {
        		$return_msg = "ERROR: You must provide a code and location for the cost centre.";
        		//$return_msg .= "<br />SIM ID: ".$sim_id." sim_number: ".$sim_number." pool_owner_id: ". $pool_owner_id." user_id: ".$user_id." cost_centre:".$cost_centre;
				return $return_msg;
        	} else {
        		$cost_centre_sql = "UPDATE cost_centres SET code = '".$code."', location = '".$location."', manager_id = ".$manager_id." WHERE _id = ".$_id;
        		$cost_centre_info = mysql_query($cost_centre_sql) or die(mysql_error());
        		if (!$cost_centre_info) return mysql_error();
				$return_msg = "SUCCESS: ".$code." - ".$location." successfully updated.";
				//$return_msg = $cost_centre_sql;
				return $return_msg;	        
			}		
		}

		function add_person($fullname, $mail) {
			
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$fullname = addslashes($fullname);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($fullname == "" || $mail == "") {
        		$return_msg = "ERROR: You must provide a Full Name and Email for each user.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	} else {
        		$person_check_sql = "SELECT _id FROM people WHERE mail = '".$mail."'";
        		//print($person_check_sql);
        		$person_check_info = mysql_query($person_check_sql) or die(mysql_error());
        		$person_count = mysql_num_rows($person_check_info);
        		if ($person_count>0) {
        			$return_msg = "ERROR: There is already a person in the database with Email '".$mail."'";
					return $return_msg;	
        		}
        		$person_sql = "INSERT INTO people (fullname, mail) VALUES ('".$fullname."', '".$mail."')";
        		$person_info = mysql_query($person_sql) or die(mysql_error());
        		if (!$person_info) return mysql_error();
				$return_msg = "SUCCESS: ".$fullname." successfully created.";
				//$return_msg = $person_sql;
				return $return_msg;	
     
			}		
		}
   

}

?>
