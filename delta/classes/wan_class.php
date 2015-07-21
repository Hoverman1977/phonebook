<?php
class wan {

        function wan() {
        
        }

        function get_wan_view($column = "", $value = "", $sort = "") {
        	$column = addslashes($column);
            $wan_sql = "SELECT wan_id, CONCAT(site_code, ' - ', location) location, description, ip_fnn, link_fnn, management_fnn, wan_speed, address, wan_type, cost_per_month, link_cost, management_cost, total_cost, comments, deactive FROM vw_wan";

			if ($column != "" && $value != ""){
				$wan_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$wan_sql .= " LIKE '%".$value."%'";
				} else {
					$wan_sql .= " = ".$value;	
				}	
			}
			if ($sort =="") {
				$wan_sql .= " ORDER BY management_fnn";
			} else {
				$wan_sql .= " ORDER BY ".$sort;
			}
			$wan_info = mysql_query($wan_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$wan_sql);
			if ($wan_info) {
        		return $wan_info;
			} else {
        		return false;
			}
        }
        
        function get_wan($column, $value){
        	$column = addslashes($column);
            $wan_sql = "SELECT * FROM wan";
			if ($column != "" && $value != ""){
				$wan_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$wan_sql .= " LIKE '%".$value."%'";
				} else {
					$wan_sql .= " = ".$value;	
				}
			}
			$wan_sql .= " ORDER BY wan_id";
			$wan_info = mysql_query($wan_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$wan_sql);
			if ($wan_info) {
        		return $wan_info;
			} else {
        		return false;
			}
        }
        
        function get_wan_comments($column, $value){
        	$column = addslashes($column);
            $wan_sql = "SELECT * FROM wan_notes";
			if ($column != "" && $value != ""){
				$wan_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$value = addslashes($value);
        			$wan_sql .= " LIKE '%".$value."%'";
				} else {
					$wan_sql .= " = ".$value;	
				}
			}
			$wan_sql .= " ORDER BY notes_id";
			$wan_info = mysql_query($wan_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$wan_sql);
			if ($wan_info) {
        		return $wan_info;
			} else {
        		return false;
			}
        }

        function get_wan_types($column = "", $value = "") {
        	$wan_sql = "SELECT wan_type_id, CONCAT(description, ' - ',wan_speed,' - $', cost_per_month) as type, description, cost_per_month, wan_speed FROM wan_types WHERE deactive = 'n'";
        	if ($column != "" && $value != ""){
				$wan_sql .= " AND ".$column;
				if (is_string($value)) {
					$wan_sql .= " LIKE '%".$value."%'";
				} else {
					$wan_sql .= " = ".$value;	
				}
			}
			$wan_sql .= " ORDER BY type, cost_per_month";
        	$wan_info = mysql_query($wan_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$wan_sql);
        	if ($wan_info){
        		return $wan_info;
        	} else {
        		return false;	
        	}
        }
             
        function edit_wan_type($wan_type_id = 0, $description, $cost_per_month, $wan_speed = "", $deactive = 'n') { 
		//$illegal_chars = array("-", ",", "$", "'", ";", "(", ")", "%");
        	$description = addslashes($description);
		//$cost_per_month = addslashes($cost_per_month);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
		if ($description == "") {
        		$return_msg = "ERROR: You must provide a description for the wan type.";
        		return $return_msg;
        	} else {
        		//$wan_type_check_sql = "SELECT wan_type_id FROM wan_types WHERE wan_type_id <> ".$wan_type_id." AND (description = '".$description."')";
        		//print($person_check_sql);
        		//$wan_type_check_info = mysql_query($wan_type_check_sql) or die(mysql_error());
        		//$wan_type_count = mysql_num_rows($wan_type_check_info);
        		//if ($wan_type_count>0) {
        			//$return_msg = "ERROR: There is already a wan type in the database called '".$description."'";
				//return $return_msg;	
        		//}
        		$wan_type_sql = "UPDATE wan_types SET description = '".$description."', cost_per_month = ".$cost_per_month.", wan_speed = '".$wan_speed."', deactive = '".$deactive."' WHERE 					wan_type_id = ".$wan_type_id;
        		$wan_type_info = mysql_query($wan_type_sql) or die(mysql_error());
        		if (!$wan_type_info) return mysql_error();
			$return_msg = "SUCCESS: ".$description." successfully updated.";
			//$return_msg = $person_sql;
			return $return_msg;	        
		}		
	}

        function add_wan_type($description = "", $cost_per_month = "", $wan_speed = "") {
        	//$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$description = addslashes($description);
        	$cost_per_month = addslashes($cost_per_month);
        	$wan_speed = addslashes($wan_speed);

        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
        	if ($description == "") {
        		$return_msg = "ERROR: You must provide a description.";
        		return $return_msg;
        	} else {
        		//$wan_type_check_sql = "SELECT wan_type_id FROM wan_types WHERE description = '".$description."'";
        		//$wan_type_check_info = mysql_query($wan_type_check_sql) or die(mysql_error());
        		//$wan_type_count = mysql_num_rows($wan_type_check_info);
        		//if ($wan_type_count>0) {
        			//$return_msg = "ERROR: There is already a WAN type in the database with description '".$description."'";
					//return $return_msg;	
        		//}
				$wan_type_sql = "INSERT INTO wan_types (description, cost_per_month, wan_speed) VALUES ('".$description."', ".$cost_per_month.", '".$wan_speed."')";
        		$wan_type_info = mysql_query($wan_type_sql) or die(mysql_error());
        		if (!$wan_type_info) return mysql_error();
				$return_msg = "SUCCESS: WAN Type successfully created.";
				return $return_msg;	
        	}
        	
        	return $return_msg;
        }

	function get_bills_by_month($month = "", $year = 12){
        	$month = addslashes($month);
            $bill_sql = "SELECT _id, service_type, service_number, other_charges, total_cost, gst_cost, gst_free, SUM((total_cost)-(gst_free)-(gst_cost)) AS total_gst_exc, account_num, month, year FROM vw_wan_bills";
			if ($month != "" && $year != 0){
				$bill_sql .= " WHERE month = '".$month."' AND year = ".$year;
				
				
			}
			$bill_sql .= " GROUP BY _id ORDER BY service_type, service_number";
			$bill_info = mysql_query($bill_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$bill_sql);
			if ($bill_info) {
        			return $bill_info;
			} else {
        			return false;
			}
        }
        
	function get_bills($column = "", $value = ""){
        	$column = addslashes($column);
           	$bill_sql = "SELECT _id, service_type, service_number, other_charges, total_cost, gst_cost, gst_free, SUM((total_cost)-(gst_free)-(gst_cost)) AS total_gst_exc, account_num, month, year FROM wan_bills";
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
		$sql = "SELECT DISTINCT month '_id', month FROM wan_bills ORDER BY month";
		$bill_month_info = mysql_query($sql) or die(mysql_error());
		if ($bill_month_info) {
			return $bill_month_info;
		} else {
			return false;
		}
	}

	function get_bill_years() {
		$sql = "SELECT DISTINCT year '_id', year FROM wan_bills ORDER BY year";
		$bill_year_info = mysql_query($sql) or die(mysql_error());
		if ($bill_year_info) {
			return $bill_year_info;
		} else {
			return false;
		}
	}



        function edit_wan ($wan_id = 0, $ip_fnn, $location_id = 0, $wan_type_id = 0, $description, $link_fnn, $management_fnn, $link_cost = 0,$management_cost = 0, $deactive = 'n') {
		$description = addslashes($description);
		$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
		$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($location_id == 0 || $wan_type_id == 0) {
        			$return_msg = "ERROR: You must provide a location and wan type.";
        			return $return_msg;
        		} else {
				$wan_check_sql = "SELECT wan_id FROM wan WHERE wan_id <> ".$wan_id." AND (ip_fnn = '".$ip_fnn."')";
				$wan_check_info = mysql_query($wan_check_sql) or die(mysql_error());
				$wan_count = mysql_num_rows($wan_check_info);
				if ($wan_count>0) {
					$return_msg = "ERROR: There is already a WAN Link (".$wan_id.") in the database with IP FNN '".$ip_fnn."'";
					return $return_msg;	
				}
				$wan_sql = "UPDATE wan SET ip_fnn = '".$ip_fnn."', location_id = ".$location_id.", wan_type_id = ".$wan_type_id.", description = '".$description."',
				link_fnn = '".$link_fnn."',management_fnn = '".$management_fnn."', link_cost = ".$link_cost.", management_cost = ".$management_cost.",
				deactive = '".$deactive."' WHERE wan_id = ".$wan_id;
        			$wan_info = mysql_query($wan_sql) or die(mysql_error());
			}
        		if (!$wan_info) return mysql_error();
			$return_msg = "SUCCESS: WAN entry successfully updated.";
			return $return_msg;	
					
		}
		
	function add_wan($ip_fnn, $location_id = 0, $wan_type_id = 0, $description, $link_fnn, $management_fnn, $link_cost = 0,$management_cost = 0) {
			
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        		$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
		if ($wan_type_id == "" || $location_id == 0) {
        		$return_msg = "ERROR: You must provide a wan type and location.";
        		return $return_msg;
        	} else {
        		$wan_check_sql = "SELECT wan_id FROM wan WHERE (ip_fnn = '".$ip_fnn."')";
        		$wan_check_info = mysql_query($wan_check_sql) or die(mysql_error());
        		$wan_count = mysql_num_rows($wan_check_info);
        		if ($wan_count>0) {
        			$return_msg = "ERROR: There is already a WAN link in the database with number '".$ip_fnn."'";
				return $return_msg;	
        		}
        		
        		$wan_sql = "INSERT INTO wan (ip_fnn, location_id, wan_type_id, description, link_fnn, management_fnn, link_cost, management_cost) VALUES ('"
        				.$ip_fnn."', ".$location_id.", ".$wan_type_id.", '".$description."', '".$link_fnn."', '".$management_fnn."', ".$link_cost.", ".$management_cost.",)";
        		$wan_info = mysql_query($wan_sql) or die(mysql_error());
        		if (!$wan_info) return mysql_error();
				$return_msg = "SUCCESS: Service entry successfully added.";
				return $return_msg;	
        
			}		
		}


		function add_wan_comment($wan_id = 0, $notes_text = '') {
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			
			if ($notes_text == '') {
				$return_msg = "ERROR: You need to actually type something in the comment field.";
			} else {
				$add_comment_sql = "INSERT INTO wan_notes (wan_id, notes_text, entered_by) VALUES (".$wan_id.", '".addslashes($notes_text)."', '".$_SESSION['username']."')";
				$comment_info = mysql_query($add_comment_sql) or die(mysql_error());
				if (!$comment_info) return mysql_error();
				$return_msg = "SUCCESS: Comment added!";
			}
			return $return_msg;
		}

		function del_wan($wan_id) {
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			$del_wan_sql = "DELETE FROM wan  WHERE wan_id = ".$wan_id;
			//print ($del_sim_sql);			
			$wan_info = mysql_query($del_wan_sql) or die(mysql_error());
			if (!wan_info) return mysql_error();
			$return_msg = "SUCCESS: ID ".$wan_id." successfully deleted.";
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
						$sql = "DELETE FROM wan_bills WHERE month = '".$month."' AND year = ".$year;
						$del_info = mysql_query($sql) or die(mysql_error())." -- ".$sql;
					} else if ($line[0] == "Account Number") {
						$account_num = $line[1];
					} else if ($line[0] == "Service Type") {
						$start_processing = 1;
					} else if ($start_processing == 1) {
						$service_type = $line[0];
						$service_number = $line[1];
						$has_space = strpos($service_number, " ");						
						if ($has_space > 0) {
							$service_number = substr($service_number, 0, $has_space);
						}
						if ((substr($service_number, 0, 1) != '1') && (substr($service_number, 0, 1) != 'N') && (substr($service_number, 0, 1) != '0')) {
							$service_number = "0".$service_number;
						}
						$other_charges = $line[3];
						$total_cost = $line[6];
						$gst_cost = $line[7];
						$gst_free = intval((($total_cost*100)/11)-($gst_cost*100))/100;						
						//$gst_free = intval((($total_cost*100)/11) - ($gst_cost*100)/100);
						if ($gst_free*100 < 10) $gst_free = 0;						
						//$gst_free = ((intval($total_cost*100/11) - ($gst_cost*100))/100);
						//$total_gst_inc = ((intval($total_cost*100) - ($gst_free*100))/100);
						$sql = "INSERT INTO wan_bills (service_type, service_number, other_charges, total_cost, gst_cost, gst_free, month, year, account_num) VALUES ('".
							$service_type."', '".$service_number."', ".$other_charges.", ".$total_cost.", ".$gst_cost.", ".$gst_free.",'".$month."', ".$year.", $account_num)";
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


}// end of class

?>
