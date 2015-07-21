<?php
class sim_db {

        function sim_db() {
        
        }


		function get_ldap($guid = "", $cn = "") {
			return false;
			$ldapconn = ldap_connect(LDAP_SERVER) or die("Could not connect to LDAP server - ".LDAP_SERVER);
			if ($ldapconn) {
                    $ldapanon = ldap_bind($ldapconn);
                    $attributes = array("dn", "mail", "cn", "fullName", "ou", "l", "description");
                    $size_limit = 0;
                    $ldap_res = ldap_search($ldapconn, "o=djj", '(&(objectclass=person))', $attributes, 0, $size_limit);
                    $info = ldap_get_entries($ldapconn, $ldap_res);
                    ldap_unbind($ldapconn);
                    $count = $info['count'];
                    if ($count == 0) {
                            return false;
                    }
                    $sql = array(); 
                   //print ($info[0]['description'][0]);
                    $people = 0;
                    foreach ($info as $row) {
                    	$person = false;
                    	if (($row['description'][0] == '') || (strpos($row['description'][0], "person_jj") >0) ) {
                    		$person = true;
                    	}
                    	if ($row['cn'][0] != '' && $row['fullname'][0] != '' && $row['mail'] != '' && $person) {
                    		$people++;
                    		$sql[] = '("'.mysql_real_escape_string($row['dn']).'", "'.$row['fullname'][0].'", "'.$row['l'][0].'", "'.$row['mail'][0].'")';	
                    	}              	
                    }
                    print ("Found ".$count." records - ".$people." are actual people<br />");
                    
                    //$res = mysql_query('TRUNCATE TABLE people') or die("Unable to truncate people table.");
					//$insert_sql = 'INSERT INTO people (cn, fullname, location, mail) VALUES '.implode(',', $sql);
					//$res = mysql_query($insert_sql) or die("Unable to bulk insert data from LDAP. Computer says 'No.'<br />".mysql_error()."<br />".$insert_sql);
					//return $res;
			}
		}

        function get_sim_view($column = "", $value = "") {
        	$column = addslashes($column);
            $sim_sql = "SELECT sim_id, sim_number, user, manager, CONCAT(pool_name, ' - ', pool_comment) as pool, cost_centre, fund, location, comments, deactive FROM vw_sims";
			//$sim_sql = "SELECT * FROM vw_sims";
			if ($column != "" && $value != ""){
				$sim_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$sim_sql .= " LIKE '%".$value."%'";
				} else {
					$sim_sql .= " = ".$value;	
				}	
			}
			$sim_sql .= " ORDER BY sim_number";
			$sim_info = mysql_query($sim_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$sim_sql);
			if ($sim_info) {
        		return $sim_info;
			} else {
        		return false;
			}
        }
        
        function get_sims($column, $value){
        	$column = addslashes($column);
            $sim_sql = "SELECT * FROM sims";
			if ($column != "" && $value != ""){
				$sim_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$sim_sql .= " LIKE '%".$value."%'";
				} else {
					$sim_sql .= " = ".$value;	
				}
			}
			$sim_sql .= " ORDER BY sim_id";
			$sim_info = mysql_query($sim_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$sim_sql);
			if ($sim_info) {
        		return $sim_info;
			} else {
        		return false;
			}
        }
        
        function get_sim_comments($column, $value){
        	$column = addslashes($column);
            $sim_sql = "SELECT * FROM sims_notes";
			if ($column != "" && $value != ""){
				$sim_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$sim_sql .= " LIKE '%".$value."%'";
				} else {
					$sim_sql .= " = ".$value;	
				}
			}
			$sim_sql .= " ORDER BY notes_id";
			$sim_info = mysql_query($sim_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$sim_sql);
			if ($sim_info) {
        		return $sim_info;
			} else {
        		return false;
			}
        }
        
      
        function get_pool_owners($column = "", $value = ""){
        	$column = addslashes($column);
            $pool_owner_sql = "SELECT owner_id, CONCAT(pool_name, ' - ', comment) AS description, pool_name, comment FROM pool_owners";
			if ($column != "" && $value != ""){
				$pool_owner_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
					$pool_owner_sql .= " LIKE '%".$value."%'";
				} else {
					$pool_owner_sql .= " = ".$value;	
				}
			}
			$pool_owner_sql .= " ORDER BY pool_name";
			$pool_owner_info = mysql_query($pool_owner_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$pool_owner_sql);
			if ($pool_owner_info) {
        		return $pool_owner_info;
			} else {
        		return false;
			}
        }
        

	function get_bills_by_month($month = "", $year = 12){
        	$month = addslashes($month);
            $bill_sql = "SELECT _id, sim_number, user, total_cost, gst_cost, gst_free, SUM((total_cost)-(gst_free)-(gst_cost)) AS total_gst_exc, manager, code, fund, GL, account_num, month, year, deactive FROM vw_sim_bills";
			if ($month != "" && $year != 0){
				$bill_sql .= " WHERE month = '".$month."' AND year = ".$year;
				
				
			}
			$bill_sql .= " GROUP BY _id ORDER BY sim_number, code";
			$bill_info = mysql_query($bill_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$bill_sql);
			if ($bill_info) {
        			return $bill_info;
			} else {
        			return false;
			}
        }
        
	function get_bills($column = "", $value = ""){
        	$column = addslashes($column);
           	$bill_sql = "SELECT _id, sim_number, total_cost, gst_cost, gst_free, SUM((total_cost)-(gst_free)-(gst_cost)) AS total_gst_exc, account_num, month, year FROM sim_bills";
			if ($column != "" && $value != ""){
				$bill_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
					$bill_sql .= " LIKE '%".$value."%'";
				} else {
					$bill_sql .= " = ".$value;	
				}
			}
			$bill_sql .= " GROUP BY _id ORDER BY sim_number, year, month";
			$bill_info = mysql_query($bill_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$bill_sql);
			if ($bill_info) {
        			return $bill_info;
			} else {
        			return false;
			}
        }

	function get_bill_months() {
		$sql = "SELECT DISTINCT month '_id', month FROM sim_bills ORDER BY month";
		$bill_month_info = mysql_query($sql) or die(mysql_error());
		if ($bill_month_info) {
			return $bill_month_info;
		} else {
			return false;
		}
	}

	function get_bill_years() {
		$sql = "SELECT DISTINCT year '_id', year FROM sim_bills ORDER BY year";
		$bill_year_info = mysql_query($sql) or die(mysql_error());
		if ($bill_year_info) {
			return $bill_year_info;
		} else {
			return false;
		}
	}

	
        function edit_sim($sim_id = 0, $sim_number, $pool_owner_id, 
				$user_id, $puk, $imei, $cost_centre, $fund, $gl_account, $deactive = 'n') {
			
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($sim_number == "" || $cost_centre == "" || $user_id == "") {
        		$return_msg = "ERROR: You must provide a sim number, cost centre and a user.";
        		//$return_msg .= "<br />SIM ID: ".$sim_id." sim_number: ".$sim_number." pool_owner_id: ". $pool_owner_id." user_id: ".$user_id." cost_centre:".$cost_centre;
				return $return_msg;
        	} else {
        		$sim_check_sql = "SELECT sim_id FROM sims WHERE sim_id <> ".$sim_id." AND (sim_number = '".$sim_number."' OR imei = '".$imei."') AND (imei <> '')";
        		//print ($sim_check_sql);
        		$sim_check_info = mysql_query($sim_check_sql) or die(mysql_error());
        		$sim_count = mysql_num_rows($sim_check_info);
        		if ($sim_count>0) {
        			$return_msg = "ERROR: There is already a sim (".$sim_id.") in the database with number '".$sim_number."' or an IMEI of '".$imei."'";
					return $return_msg;	
        		}
        		$sim_sql = "UPDATE sims SET sim_number = '".$sim_number."', pool_owner_id = ".$pool_owner_id.
					", user_id = '".$user_id."', puk = '".$puk."', imei = '".$imei."', cost_centre = ".$cost_centre.", fund = '".$fund.
					"', gl_account = '".$gl_account."', deactive = '".$deactive."' WHERE sim_id = ".$sim_id;
        		$sim_info = mysql_query($sim_sql) or die(mysql_error());
        		if (!$sim_info) return mysql_error();
				$return_msg = "SUCCESS: SIM entry successfully updated.";
				//$return_msg = $sim_sql;
				return $return_msg;	
        
			}		
		}
		
		function add_sim($sim_number, $pool_owner_id, 
				$user_id, $puk, $imei, $cost_centre, $fund, $gl_account) {
			
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($sim_number == "" || $cost_centre == "" || $user_id == "") {
        		$return_msg = "ERROR: You must provide a sim number, cost centre and a user.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	} else {
        		$sim_check_sql = "SELECT sim_id FROM sims WHERE (sim_number = '".$sim_number."' OR imei = '".$imei."')  AND (imei <> '')";
        		$sim_check_info = mysql_query($sim_check_sql) or die(mysql_error());
        		$sim_count = mysql_num_rows($sim_check_info);
        		if ($sim_count>0) {
        			$return_msg = "ERROR: There is already a sim in the database with number '".$sim_number."' or IMEI '".$imei."'";
					return $return_msg;	
        		}
        		
        		$sim_sql = "INSERT INTO sims (sim_number, user_id, pool_owner_id, imei, puk, cost_centre," .
        				" fund, gl_account) VALUES ('"
        				.$sim_number."', '".$user_id."', ".$pool_owner_id.", '".$imei."', '".$puk."', ".$cost_centre.", '".$fund."', '".$gl_account."')";
        		//print ($sim_sql);
        		$sim_info = mysql_query($sim_sql) or die(mysql_error());
        		if (!$sim_info) return mysql_error();
				$return_msg = "SUCCESS: SIM entry successfully added.";
				//$return_msg = $asset_sql;
        		//$sim_info = mysql_query($sim_sql) or die(mysql_error());
        		//if (!$sim_info) return mysql_error();
				//$return_msg = "SUCCESS: Asset successfully updated.";
				//$return_msg = $sim_sql;
				return $return_msg;	
        
			}		
		}


		function add_pool($pool_name, $comment) {
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$pool_name = addslashes($pool_name);
        	$comment = addslashes($comment);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($pool_name == "" || $comment == "") {
        		$return_msg = "ERROR: You must provide a Pool Name and Comment.";
        		//$return_msg .= "<br />SIM ID: ".$sim_id." sim_number: ".$sim_number." pool_owner_id: ". $pool_owner_id." user_id: ".$user_id." cost_centre:".$cost_centre;
				return $return_msg;
        	} else {
        		$pool_sql = "INSERT INTO pool_owners (pool_name, comment) VALUES ('".$pool_name."', '".$comment."')";
        		$pool_info = mysql_query($pool_sql) or die(mysql_error());
        		if (!$pool_info) return mysql_error();
				$return_msg = "SUCCESS: ".$pool_name." - ".$comment." successfully created.";
				//$return_msg = $pool_sql;
				return $return_msg;	
     
			}		
		}

		function edit_pool($owner_id = 0, $pool_name, $comment) { 
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$pool_name = addslashes($pool_name);
        	$comment = addslashes($comment);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($pool_name == "" || $comment == "") {
        		$return_msg = "ERROR: You must provide a Pool Name and Comment.";
        		//$return_msg .= "<br />SIM ID: ".$sim_id." sim_number: ".$sim_number." pool_owner_id: ". $pool_owner_id." user_id: ".$user_id." cost_centre:".$cost_centre;
				return $return_msg;
        	} else {
        		$pool_sql = "UPDATE pool_owners SET pool_name = '".$pool_name."', comment = '".$comment."' WHERE _id = ".$owner_id;
        		$pool_info = mysql_query($pool_sql) or die(mysql_error());
        		if (!$pool_info) return mysql_error();
				$return_msg = "SUCCESS: ".$pool_name." - ".$comment." successfully updated.";
				//$return_msg = $pool_sql;
				return $return_msg;	        
			}		
		}

		

		function add_sim_comment($sim_id = 0, $notes_text = '') {
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			$add_comment_sql = "INSERT INTO sims_notes (sim_id, notes_text, entered_by) VALUES (".$sim_id.", '".addslashes($notes_text)."', '".$_SESSION['username']."')";
			if ($sim_id == 0 || $notes_text == '') {
				$return_msg = "ERROR: You need to supply the sim_id or actually type something in the comment field.";
			} else {
				$comment_info = mysql_query($add_comment_sql) or die(mysql_error());
				if (!$comment_info) return mysql_error();
				$return_msg = "SUCCESS: Comment added!";
			}
			return $return_msg;
		}

		function del_sim($sim_id) {
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			$del_sim_sql = "DELETE FROM sims WHERE sim_id = ".$sim_id;
			//print ($del_sim_sql);			
			$sim_info = mysql_query($del_sim_sql) or die(mysql_error());
			if (!sim_info) return mysql_error();
			$return_msg = "SUCCESS: ID ".$sim_id." successfully deleted.";
			return $return_msg;
		}

		function process_file($target_path) {
			$return_msg = "ERROR: You should never see this message. The software is havening a problem. -- target_path = ".$target_path;
			//$path = "yourfile.csv";
			$row = 0;
			if (($handle = fopen($target_path, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$row++;
					$data_entries[] = $data;
				}
				fclose($handle);
				$date = "";
				$month = "";
				$year = "";
				$account_num = "";
				foreach($data_entries as $line){
					if ($line[0] == "Bill Issue Date") {
						$date = $line[1];
						$month = substr($date, 3, 3);
						$year = substr($date, 7, 2);
						$sql = "DELETE FROM sim_bills WHERE month = '".$month."' AND year = ".$year;
						$del_info = mysql_query($sql) or die(mysql_error())." -- ".$sql;
					} else if ($line[0] == "Account Number") {
						$account_num = $line[1];
					} else if ($line[0] == "Mobile") {
						$sim_number = $line[1];
						$has_space = strpos($sim_number, " ");						
						if ($has_space > 0) {
							$sim_number = substr($sim_number, 0, $has_space);
						}
						if (substr($sim_number, 0, 1) != '0') {
							$sim_number = "0".$sim_number;
						}
						$total_cost = $line[4];
						$gst_cost = $line[5];
						$gst_free = (intval(($total_cost*100) - ($gst_cost*100)-($gst_cost*1000))/100);
						if ($gst_free*100 < 10) $gst_free = 0;						
						//$gst_free = ((intval($total_cost*100/11) - ($gst_cost*100))/100);
						//$total_gst_inc = ((intval($total_cost*100) - ($gst_free*100))/100);
						$sql = "INSERT INTO sim_bills (sim_number, total_cost, gst_cost, gst_free, month, year, account_num) VALUES ('".
							$sim_number."', ".$total_cost.", ".$gst_cost.", ".$gst_free.",'".$month."', ".$year.", $account_num)";
						//print ($sql."<br />");
						$bill_info = mysql_query($sql) or die(mysql_error()."-- ".$sql);
						if ($bill_info) {
							$return_msg = "The file ". $target_path . " has been uploaded for the month ".$month." with year ".$year;
						} else {
							$return_msg = "There was an error while processing the file ". $target_path .
									 " for the month ".$month." with year ".$year." and inserting the data into the database.";
							return $return_msg;
						}
					}
				}
			}
		    	return $return_msg;
		}

		function export($export, $filename = "bill.csv") {

				$header = "";
				$data = "";
				$fields = mysql_num_fields ( $export );
				for ( $i = 0; $i < $fields; $i++ )
				{
				    $header .= mysql_field_name( $export , $i ) . ",";
				}

				while( $row = mysql_fetch_row( $export ) )
				{
				    $line = '';
				    foreach( $row as $value )
				    {                                            
					if ( ( !isset( $value ) ) || ( $value == "" ) )
					{
					    $value = ",";
					}
					else
					{
					    $value = str_replace( '"' , '""' , $value );
					    $value = '"' . $value . '"' . ",";
					}
					$line .= $value;
				    }
				    $data .= trim( $line ) . "\n";
				}
				$data = str_replace( "\r" , "" , $data );

				if ( $data == "" )
				{
				    $data = "\n(0) Records Found!\n";                        
				}

				header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Pragma: public");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				//header("Cache-Control: public");
				header("Content-Type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"$filename\";");
				header("Content-length: " . strlen($header."\n".$data));
				print "$header\n$data";
		}
}// end of class

?>
