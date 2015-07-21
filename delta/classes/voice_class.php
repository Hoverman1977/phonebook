<?php
class voice {

        function voice() {
        
        }

	function get_voice_view($column = "", $value = "") {
        	$column = addslashes($column);
            $voice_sql = "SELECT voice_id, service_number, description, service_address, service_type, location, cc_description, manager, cost_centre, comments, deactive FROM vw_voice";
			//$sim_sql = "SELECT * FROM vw_sims";
			if ($column != "" && $value != ""){
				$voice_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$voice_sql .= " LIKE '%".$value."%'";
				} else {
					$voice_sql .= " = ".$value;	
				}	
			}
			$voice_sql .= " ORDER BY service_number";
			$voice_info = mysql_query($voice_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$voice_sql);
			if ($voice_info) {
        		return $voice_info;
			} else {
        		return false;
			}
        }
        
        function get_voice($column, $value){
        	$column = addslashes($column);
            $voice_sql = "SELECT * FROM voice";
			if ($column != "" && $value != ""){
				$voice_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$voice_sql .= " LIKE '%".$value."%'";
				} else {
					$voice_sql .= " = ".$value;	
				}
			}
			$voice_sql .= " ORDER BY voice_id";
			$voice_info = mysql_query($voice_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$voice_sql);
			if ($voice_info) {
        		return $voice_info;
			} else {
        		return false;
			}
        }
        
        function get_voice_comments($column, $value){
        	$column = addslashes($column);
            $voice_sql = "SELECT * FROM voice_notes";
			if ($column != "" && $value != ""){
				$voice_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$voice_sql .= " LIKE '%".$value."%'";
				} else {
					$voice_sql .= " = ".$value;	
				}
			}
			$voice_sql .= " ORDER BY notes_id";
			$voice_info = mysql_query($voice_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$voice_sql);
			if ($voice_info) {
        		return $voice_info;
			} else {
        		return false;
			}
        }


        function get_voice_types($column = "", $value = "") {
        	$voice_sql = "SELECT voice_type_id, description, created_by FROM voice_types WHERE deleted = 'n'";
        	if ($column != "" && $value != ""){
				$voice_sql .= " AND ".$column;
				if (is_string($value)) {
					$voice_sql .= " LIKE '%".$value."%'";
				} else {
					$voice_sql .= " = ".$value;	
				}
			}
			$voice_sql .= " ORDER BY description";
        	$voice_info = mysql_query($voice_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$voice_sql);
        	if ($voice_info){
        		return $voice_info;
        	} else {
        		return false;	
        	}
        }
             
        function edit_voice_type($voice_type_id = 0, $description) { 
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$description = addslashes($description);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($description == "") {
        		$return_msg = "ERROR: You must provide a description for the service type.";
        		//$return_msg .= "<br />SIM ID: ".$sim_id." sim_number: ".$sim_number." pool_owner_id: ". $pool_owner_id." user_id: ".$user_id." cost_centre:".$cost_centre;
				return $return_msg;
        	} else {
        		$voice_type_check_sql = "SELECT voice_type_id FROM voice_types WHERE voice_type_id <> ".$voice_type_id." AND (description = '".$description."')";
        		//print($person_check_sql);
        		$voice_type_check_info = mysql_query($voice_type_check_sql) or die(mysql_error());
        		$voice_type_count = mysql_num_rows($voice_type_check_info);
        		if ($voice_type_count>0) {
        			$return_msg = "ERROR: There is already a service type in the database called '".$description."'";
					return $return_msg;	
        		}
        		$voice_type_sql = "UPDATE voice_types SET description = '".$description."' WHERE voice_type_id = ".$voice_type_id;
        		$voice_type_info = mysql_query($voice_type_sql) or die(mysql_error());
        		if (!$voice_type_info) return mysql_error();
				$return_msg = "SUCCESS: ".$description." successfully updated.";
				//$return_msg = $person_sql;
				return $return_msg;	        
			}		
		}


	function get_bills_by_month($month = "", $year = 12){
        	$month = addslashes($month);
            $bill_sql = "SELECT _id, service_number, manager, total_cost, gst_cost, gst_free, SUM((total_cost)-(gst_free)-(gst_cost)) AS total_gst_exc, code, fund, GL, location, account_num, month, year, deactive FROM vw_voice_bills";
			if ($month != "" && $year != 0){
				$bill_sql .= " WHERE month = '".$month."' AND year = ".$year;
				
				
			}
			$bill_sql .= " GROUP BY _id ORDER BY service_number, code";
			$bill_info = mysql_query($bill_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$bill_sql);
			if ($bill_info) {
        			return $bill_info;
			} else {
        			return false;
			}
        }
        
	function get_bills($column = "", $value = ""){
        	$column = addslashes($column);
           	$bill_sql = "SELECT _id, service_number, total_cost, gst_cost, gst_free, SUM((total_cost)-(gst_free)-(gst_cost)) AS total_gst_exc, account_num, month, year FROM voice_bills";
			if ($column != "" && $value != ""){
				$bill_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
					$bill_sql .= " LIKE '%".$value."%'";
				} else {
					$bill_sql .= " = ".$value;	
				}
			}
			$bill_sql .= " GROUP BY _id ORDER BY service_number, year, month";
			$bill_info = mysql_query($bill_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$bill_sql);
			if ($bill_info) {
        			return $bill_info;
			} else {
        			return false;
			}
        }

	function get_bill_months() {
		$sql = "SELECT DISTINCT month '_id', month FROM voice_bills ORDER BY month";
		$bill_month_info = mysql_query($sql) or die(mysql_error());
		if ($bill_month_info) {
			return $bill_month_info;
		} else {
			return false;
		}
	}

	function get_bill_years() {
		$sql = "SELECT DISTINCT year '_id', year FROM voice_bills ORDER BY year";
		$bill_year_info = mysql_query($sql) or die(mysql_error());
		if ($bill_year_info) {
			return $bill_year_info;
		} else {
			return false;
		}
	}

	
        function edit_voice($voice_id = 0, $service_number, $location_id, $voice_type_id, $description, 
				$cost_centre, $fund, $gl_account, $deactive = 'n') {
			
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        		$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($service_number == "" || $cost_centre == "") {
        		$return_msg = "ERROR: You must provide a service number and cost centre.";
        		return $return_msg;
        	} else {
        		$voice_check_sql = "SELECT voice_id FROM voice WHERE voice_id <> ".$voice_id." AND (service_number = '".$service_number."')";
        		//print ($sim_check_sql);
        		$voice_check_info = mysql_query($voice_check_sql) or die(mysql_error());
        		$voice_count = mysql_num_rows($voice_check_info);
        		if ($voice_count>0) {
        			$return_msg = "ERROR: There is already a service (".$voice_id.") in the database with number '".$service_number."'";
				return $return_msg;	
        		}
        		$voice_sql = "UPDATE voice SET service_number = '".$service_number."', location_id = ".$location_id.
					", voice_type_id = '".$voice_type_id."', description = '".$description."', cost_centre = ".$cost_centre.", fund = '".$fund.
					"', gl_account = '".$gl_account."', deactive = '".$deactive."' WHERE voice_id = ".$voice_id;
        		$voice_info = mysql_query($voice_sql) or die(mysql_error());
        		if (!$voice_info) return mysql_error();
				$return_msg = "SUCCESS: Service entry successfully updated.";
				//$return_msg = $sim_sql;
				return $return_msg;	
        
			}		
		}
		
	function add_voice($service_number, $location_id, $voice_type_id, $description, $service_address,
				$cost_centre, $fund, $gl_account) {
			
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        		$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($service_number == "" || $cost_centre == "") {
        		$return_msg = "ERROR: You must provide a service number and cost centre.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
			return $return_msg;
        	} else {
        		$voice_check_sql = "SELECT voice_id FROM voice WHERE (service_number = '".$service_number."')";
        		$voice_check_info = mysql_query($voice_check_sql) or die(mysql_error());
        		$voice_count = mysql_num_rows($voice_check_info);
        		if ($voice_count>0) {
        			$return_msg = "ERROR: There is already a service in the database with number '".$service_number."'";
				return $return_msg;	
        		}
        		
        		$voice_sql = "INSERT INTO voice (service_number, location_id, voice_type_id, description, cost_centre," .
        				" fund, gl_account) VALUES ('"
        				.$service_number."', ".$location_id.", ".$voice_type_id.", '".$description."', ".$cost_centre.", '".$fund."', '".$gl_account."')";
        		//print ($sim_sql);
        		$voice_info = mysql_query($voice_sql) or die(mysql_error());
        		if (!$voice_info) return mysql_error();
				$return_msg = "SUCCESS: Service entry successfully added.";
				//$return_msg = $asset_sql;
        			//$sim_info = mysql_query($sim_sql) or die(mysql_error());
        			//if (!$sim_info) return mysql_error();
				//$return_msg = "SUCCESS: Asset successfully updated.";
				//$return_msg = $sim_sql;
				return $return_msg;	
        
			}		
		}


		function add_voice_comment($voice_id = 0, $notes_text = '') {
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			
			if ($notes_text == '') {
				$return_msg = "ERROR: You need to actually type something in the comment field.";
			} else {
				$add_comment_sql = "INSERT INTO voice_notes (voice_id, notes_text, entered_by) VALUES (".$voice_id.", '".addslashes($notes_text)."', '".$_SESSION['username']."')";
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
				$start_processing = 0;
				foreach($data_entries as $line){
					if ($line[0] == "Bill Issue Date") {
						$date = $line[1];
						$month = substr($date, 3, 3);
						$year = substr($date, 7, 2);
						$sql = "DELETE FROM voice_bills WHERE month = '".$month."' AND year = ".$year;
						$del_info = mysql_query($sql) or die(mysql_error())." -- ".$sql;
					} else if ($line[0] == "Account Number") {
						$account_num = $line[1];
					} else if ($line[0] == "Service Type") {
						$start_processing = 1;
					} else if ($start_processing == 1) {
						$service_number = $line[1];
						$has_space = strpos($service_number, " ");						
						if ($has_space > 0) {
							$service_number = substr($service_number, 0, $has_space);
						}
						if ((substr($service_number, 0, 1) != '1') && (substr($service_number, 0, 1) != 'N') && (substr($service_number, 0, 1) != '0')) {
							$service_number = "0".$service_number;
						}
						$total_cost = $line[6];
						$gst_cost = $line[7];
						$gst_free = intval((($total_cost*100)/11)-($gst_cost*100))/100;						
						//$gst_free = intval((($total_cost*100)/11) - ($gst_cost*100)/100);
						if ($gst_free*100 < 10) $gst_free = 0;						
						//$gst_free = ((intval($total_cost*100/11) - ($gst_cost*100))/100);
						//$total_gst_inc = ((intval($total_cost*100) - ($gst_free*100))/100);
						$sql = "INSERT INTO voice_bills (service_number, total_cost, gst_cost, gst_free, month, year, account_num) VALUES ('".
							$service_number."', ".$total_cost.", ".$gst_cost.", ".$gst_free.",'".$month."', ".$year.", $account_num)";
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

				//header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
				//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				//header("Pragma: public");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				//header("Cache-Control: public");
				header("Content-Type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"$filename\";");
				//header("Content-length: " . strlen($header."\n".$data));
				print "$header\n$data";
		}
}// end of class

?>
