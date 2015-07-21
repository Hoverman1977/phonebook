<?php
class software {

        function software() {
        
        }

		function get_pidkey($asset_tag = "", $identifier = "") {
				$return_msg = "ERROR: The software is havening a problem. You should never see this message.";
				if ($asset_tag == "" || $identifier == "") {
					$return_msg = "ERROR: You must provide a valid Asset Tag and product identifier.";
					return $return_msg;
				}
				$asset_tag = addslashes($asset_tag);
				$identifier = addslashes($identifier);
				$asset_check_sql = "SELECT asset_id FROM assets WHERE deleted = 'n' AND asset_tag = '".$asset_tag."'";
				$asset_info = mysql_query($asset_check_sql) or die(mysql_error());
				$ident_check_sql = "SELECT product_id FROM products WHERE deleted = 'n' AND identifier = '".$identifier."'";
				$ident_info = mysql_query($asset_check_sql)or die(mysql_error());
				$asset_count = mysql_num_rows($asset_info);
				$asset_tag = stripslashes($asset_tag);
				if ($asset_count<1) {
					$return_msg = "ERROR: Asset Tag '".$asset_tag."' not found in database.";
					return $return_msg;
				}
				$ident_count = mysql_num_rows($ident_info);
				$identifier = stripslashes($identifier);
				if ($ident_count<1) {
					$return_msg = "ERROR: Identifier '".$identifier."' not found in database.";
					return $return_msg;	
				}
				$pidkey_sql = "SELECT pidkey FROM vw_licences WHERE asset_tag = '".$asset_tag."' AND identifier = '".$identifier."'";
				$pidkey_info = mysql_query($pidkey_sql) or die(mysql_error());
				$pidkey_count = mysql_num_rows($pidkey_info);
				
				if ($pidkey_count==0){
					$return_msg = "ERROR: Licence not found for identifier '".$identifier."' and Asset Tag '".$asset_tag."'in database";
					return $return_msg;
				} else if ($pidkey_count>1){
					$return_msg = "ERROR: Too many licences found in the database for Asset Tag '".$asset_tag."' and identifier '".$identifier."'";
					return $return_msg;
				} else if ($pidkey_count==1) {
					$pidkey = mysql_result($pidkey_info, 0, 'pidkey');
					$return_msg = $pidkey;
					return $return_msg;	
				}
				return $return_msg;
		}

        function get_licences_view($column = "", $value = "") {
        	$value = addslashes($value);
        	$column = addslashes($column);
            $licence_sql = "SELECT licence_id, asset_tag 'Asset Tag', identifier 'Product Code', long_description 'Description', location_description 'Location', PIDKEY 'PIDKEY' FROM vw_licences";
			if ($column != "" && $value != ""){
				$licence_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$licence_sql .= " LIKE '%".$value."%'";
				} else {
					$licence_sql .= " = ".$value;	
				}	
			}
			$licence_sql .= " ORDER BY asset_tag";
			$licence_info = mysql_query($licence_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$licence_sql);
			if ($licence_info) {
        		return $licence_info;
			} else {
        		return false;
			}
        }
        
        function get_licences($column, $value){
        	$value = addslashes($value);
        	$column = addslashes($column);
            $licence_sql = "SELECT * FROM licences WHERE deleted = 'n'";
			if ($column != "" && $value != ""){
				$licence_sql .= " AND ".$column;
				if (is_string($value)) {
					$licence_sql .= " LIKE '%".$value."%'";
				} else {
					$licence_sql .= " = ".$value;	
				}
			}
			$licence_sql .= " ORDER BY licence_id";
			$licence_info = mysql_query($licence_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$licence_sql);
			if ($licence_info) {
        		return $licence_info;
			} else {
        		return false;
			}
        }
        
        function get_assets($column = "", $value = "") {
        	$asset_sql = "SELECT asset_id, asset_tag, description, owner, location, location_id FROM vw_assets";
        	if ($column != "" && $value != ""){
				$asset_sql .= " WHERE ".$column;
				if (is_string($value)) {
					$asset_sql .= " LIKE '%".$value."%'";
				} else {
					$asset_sql .= " = ".$value;	
				}
			}
        	$asset_sql .= " ORDER BY asset_tag";
        	$asset_info = mysql_query($asset_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$asset_sql);
        	if ($asset_info){
        		return $asset_info;
        	} else {
        		return false;	
        	}
        }
        
        function get_products($column = "", $value = "") {
        	$product_sql = "SELECT product_id, identifier, long_description FROM products WHERE deleted = 'n'";
        	if ($column != "" && $value != ""){
				$product_sql .= " AND ".$column;
				if (is_string($value)) {
					$product_sql .= " LIKE '%".$value."%'";
				} else {
					$product_sql .= " = ".$value;	
				}
			}
			$product_sql .= " ORDER BY identifier";
        	$product_info = mysql_query($product_sql) or die(mysql_error()." <br /> QUERY: <br /> ".$product_sql);
        	if ($product_info){
        		return $product_info;
        	} else {
        		return false;	
        	}
        }
        

        function add_licence($asset_id = 0, $product_id = 0, $pidkey = ""){
        	$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
			//Get rid of illegal characters from the pidkey string.
			$pidkey = str_replace($illegal_chars, "", $pidkey);
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
        	//$output = "Asset Tag: ".$asset_id." | Product ID: ".$product_id." | PIDKEY: ".$pidkey;
        	if ($asset_id == 0 || $product_id == 0 || $pidkey == "") {
        		$return_msg = "ERROR: You must select a product, asset and provide a pidkey to add a licence.";
        		return $return_msg;
        	}
        	//Check if there is an existing licence for the same asset, product and pidkey
			$licence_check_sql = "SELECT asset_id, asset_tag, product_id, pidkey FROM vw_licences WHERE asset_id = ".$asset_id." AND product_id = ".$product_id." AND pidkey = '".$pidkey."'";
        	//Check if there is an existing licence for the same asset and product, irrespective of the pidkey
			$product_check_sql = "SELECT asset_id, asset_tag, pidkey, identifier FROM vw_licences WHERE asset_id <> 897 AND product_id = ".$product_id." AND asset_id = '".$asset_id."'";
        	//Check for a spare licence already entered
        	$spare_check_sql = "SELECT licence_id, pidkey, asset_tag FROMvw_licences WHERE asset_id = 897 AND product_id = ".$product_id." AND pidkey = '".$pidkey."'";
			//Check if the same pidkey exists for another product
        	$product_check_diff_sql = "SELECT product_id, pidkey, identifier FROM vw_licences WHERE pidkey = '".$pidkey."'";
			//Check if the product needs to be unique for each machine (Mainly OEM licences)
			$unique_sql = "SELECT unique_flag FROM products WHERE product_id = ".$product_id;
			//Check the number of pidkeys for the specified product. Useful if the licence is supposed to be unique.
        	$unique_check_sql = "SELECT asset_tag, pidkey FROM vw_licences WHERE pidkey = '".$pidkey."'";
        	$unique_info = mysql_query($unique_sql) or die(mysql_error()." ".$unique_sql);
        	if (!$unique_info) return mysql_error();
        		$pidkey_info = mysql_query($unique_check_sql)or die(mysql_error());
        	if (!$pidkey_info) return mysql_error();
        		$pidkey_count = mysql_num_rows($pidkey_info);
			if (mysql_result($unique_info, 0, "unique_flag") == 'y') {
        		$unique_key = true;
        	} else {
        		$unique_key = false;
        	}
			$licence_info = mysql_query($licence_check_sql) or die(mysql_error());
        	if (!$licence_info) return mysql_error();
        	$product_info = mysql_query($product_check_sql) or die(mysql_error());
        	if (!$product_info) return mysql_error();
        	$licence_count = mysql_num_rows($licence_info);
        	$product_count = mysql_num_rows($product_info);
        	//Check if licence already exists for given product, asset and pidkey (They can only exist once - obviously)
			if ($licence_count>0) {
        		$lic_asset = mysql_result($licence_info, 0, "asset_tag");
        		$return_msg = "Existing licence found for Asset Tag '".$lic_asset."' and '".$pidkey."'";
        		$return_msg .= "<br />Please use the existing licence.";
        		//$return_msg .= "<br />".$licence_check_sql."<br />".$product_sql;
				return $return_msg; 
			//Check if the supplied pidkey already exists for a unique product
        	} else if ($pidkey_count>0 && $unique_key == true) {
        		//$return_msg = "";
        		return $return_msg;
        	//Check if there is already a pidkey for the product given and  for the asset tag
			} else if ($product_count>0) {
        		$existing_pidkey = mysql_result($product_info, 0, "pidkey");
        		$asset_tag = mysql_result($product_info, 0, "asset_tag");
        		$existing_product_ident = mysql_result($product_info, 0, "identifier");
				$return_msg = "ERROR: existing PIDKEY - ".$existing_pidkey." - found for ".$asset_tag." and product ".$existing_product_ident;
				if ($asset_tag!="Spare") {
					return $return_msg;
				}
			//If all checks fail, then we should enter the licence into the database.
			} else {
				$asset_id = addslashes($asset_id);
				$product_id = addslashes($product_id);
				$pidkey = addslashes($pidkey);
				$licence_sql = "INSERT INTO licences (asset_id, product_id, date_created, created_by, pidkey) VALUES (".
								$asset_id.", ".$product_id.", now(), '".$_SESSION['username']."', '".$pidkey."')";
				//For now, just return what WOULDhave been done.
				$return_msg = $licence_sql;
				// After all the checks have succeeded we can enter the data into the database.
				$licence_info = mysql_query($licence_sql) or die(mysql_error());
				if (!$licence_info) return mysql_error();
				$return_msg = "SUCCESS: Licence successfully created.";
				return $return_msg;	
			}	
        	return $return_msg;
        }
        
        function edit_licence($licence_id = 0, $asset_id = 0, $product_id = 0, $pidkey = "") {
        	$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
		//Get rid of illegal characters from the pidkey string.
		$pidkey = str_replace($illegal_chars, "", $pidkey);
		$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
        	//$output = "Asset Tag: ".$asset_id." | Product ID: ".$product_id." | PIDKEY: ".$pidkey;
        	if ($licence_id == 0 || $asset_id == 0 || $product_id == 0 || $pidkey == "") {
        		$return_msg = "ERROR: You must select a licence, and provide product, asset tag and a pidkey to update a licence.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	}
        	//Check if there is an existing licence for the same asset, product and pidkey
			$licence_check_sql = "SELECT asset_id, asset_tag, product_id, pidkey FROM vw_licences WHERE asset_id <> 897 AND asset_id = ".$asset_id." AND product_id = ".$product_id." AND pidkey = '".$pidkey."'";
        	//Check if there is an existing licence for the same asset and product, irrespective of the pidkey
			$product_check_sql = "SELECT licence_id, asset_id, asset_tag, pidkey, identifier FROM vw_licences WHERE product_id = ".$product_id." AND asset_id = '".$asset_id."'";
        	//Check if the same pidkey exists for another product
        	$product_check_diff_sql = "SELECT product_id, pidkey, identifier FROM vw_licences WHERE licence_id <> ".$licence_id." AND pidkey = '".$pidkey."'";
			//Check if the product needs to be unique for each machine (Mainly OEM licences)
			$unique_sql = "SELECT unique_flag FROM products WHERE product_id = ".$product_id;
			//Check the number of pidkeys for the specified product. Useful if the licence is supposed to be unique.
        	$unique_check_sql = "SELECT licence_id, asset_tag, asset_id, product_id, pidkey FROM vw_licences WHERE pidkey = '".$pidkey."'";
        	$unique_info = mysql_query($unique_sql) or die(mysql_error()." ".$unique_sql);
        	if (!$unique_info) return mysql_error();
        		$pidkey_info = mysql_query($unique_check_sql)or die(mysql_error());
        	if (!$pidkey_info) return mysql_error();
        		$pidkey_count = mysql_num_rows($pidkey_info);
        		$pidkey_licence = mysql_result($pidkey_info, 0, "licence_id");
			if (mysql_result($unique_info, 0, "unique_flag") == 'y') {
        		$unique_key = true;
        	} else {
        		$unique_key = false;
        	}
			$licence_info = mysql_query($licence_check_sql) or die(mysql_error());
        	if (!$licence_info) return mysql_error();
        	$product_info = mysql_query($product_check_sql) or die(mysql_error());
        	if (!$product_info) return mysql_error();

        	$licence_count = mysql_num_rows($licence_info);
        	$product_count = mysql_num_rows($product_info);
        	//Check if licence already exists for given product, asset and pidkey - Do nothing for an update.
			if ($licence_count>0) {
        		//$lic_asset = mysql_result($licence_info, 0, "asset_tag");
        		$return_msg = "Existing licence left unchanged.";
        		//$return_msg .= "<br />".$licence_check_sql."<br />".$product_sql;
				return $return_msg; 
			//Check if the supplied pidkey already exists for a unique product
        	} else if ($pidkey_count>0 && $unique_key == true && ($pidkey_licence != $licence_id)) {
        		$return_msg = "ERROR: This PIDKEY already exists and must be a unique PIDKEY.";
        		return $return_msg;
        	//Check if there is already a pidkey for the product given and  for the asset tag
			} else if ($product_count>0) {
        		$existing_pidkey = mysql_result($product_info, 0, "pidkey");
        		$asset_tag = mysql_result($product_info, 0, "asset_tag");
        		$existing_product_ident = mysql_result($product_info, 0, "identifier");
        		$existing_licence_id = mysql_result($product_info, 0, "licence_id");
				if (($asset_tag!="Spare") && ($licence_id != $existing_licence_id)) {
					$return_msg = "ERROR: existing PIDKEY - ".$existing_pidkey." - found in licences for ".$asset_tag." and product ".$existing_product_ident;
					return $return_msg;
				}
				$asset_id = $asset_id;
				$product_id = $product_id;
				$pidkey = addslashes($pidkey);
				$licence_sql = "UPDATE licences SET asset_id = ".$asset_id.", product_id = ".$product_id.", last_modified = now(), modified_by = '".$_SESSION['username']."', pidkey = '".$pidkey."'
								WHERE licence_id = ".$licence_id;
				//For now, just return what WOULDhave been done.
				$licence_info = mysql_query($licence_sql) or die(mysql_error());
				if (!$licence_info) return mysql_error();
				$return_msg = "SUCCESS: Licence successfully updated.";
				//$return_msg = $licence_sql;
				return $return_msg;		
			//If all checks fail, then we should enter the licence into the database.
			} else {
				$asset_id = $asset_id;
				$product_id = $product_id;
				$pidkey = addslashes($pidkey);
				$licence_sql = "UPDATE licences SET asset_id = ".$asset_id.", product_id = ".$product_id.", last_modified = now(), modified_by = '".$_SESSION['username']."', pidkey = '".$pidkey."'
								WHERE licence_id = ".$licence_id;
				// After all the checks have succeeded we can update the database.
				$licence_info = mysql_query($licence_sql) or die(mysql_error());
				if (!$licence_info) return mysql_error();
				$return_msg = "SUCCESS: Licence successfully updated.";
				//$return_msg = $licence_sql;
				return $return_msg;	
			}	
        	return $return_msg;	
        }
        
        function add_asset($asset_tag = "", $location_id = 0, $description = "", $owner = "") {
        	$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$description = addslashes($description);
        	$owner = addslashes($owner);
        	$asset_tag = str_replace($illegal_chars, "", $asset_tag);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
        	if ($asset_tag == "" || $location_id == 0 || $description == "") {
        		$return_msg = "ERROR: You must provide an asset tag, location and a description to add an asset.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	} else {
        		$asset_check_sql = "SELECT asset_id FROM assets WHERE asset_tag = '".$asset_tag."'";
        		$asset_check_info = mysql_query($asset_check_sql) or die(mysql_error());
        		$asset_count = mysql_num_rows($asset_check_info);
        		if ($asset_count>0) {
        			$return_msg = "ERROR: There is already an asset in the database with asset tag '".$asset_tag."'";
					return $return_msg;	
        		}
				$asset_sql = "INSERT INTO assets (asset_tag, location_id, description, owner, date_created, created_by) VALUES ('".$asset_tag."', ".$location_id.", '".$description."', '".$owner."', now(), '".$_SESSION['username']."')";
        		$asset_info = mysql_query($asset_sql) or die(mysql_error());
        		if (!$asset_info) return mysql_error();
				$return_msg = "SUCCESS: Asset successfully created.";
				//$return_msg = $asset_sql;
				return $return_msg;	
        	}
        	
        	return $return_msg;
        }
		
		function edit_asset($asset_id = 0, $asset_tag = "", $location_id = 0, $description = "", $owner = "") {
			$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$description = addslashes($description);
        	$owner = addslashes($owner);
        	$asset_tag = str_replace($illegal_chars, "", $asset_tag);
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($asset_tag == "" || $location_id == 0 || $description == "") {
        		$return_msg = "ERROR: You must provide an asset tag, location and a description to add an asset.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	} else {
        		$asset_check_sql = "SELECT asset_id FROM assets WHERE asset_id <> ".$asset_id." AND asset_tag = '".$asset_tag."'";
        		$asset_check_info = mysql_query($asset_check_sql) or die(mysql_error());
        		$asset_count = mysql_num_rows($asset_check_info);
        		if ($asset_count>0) {
        			$return_msg = "ERROR: There is already an asset in the database with asset tag '".$asset_tag."'";
					return $return_msg;	
        		}
        		$asset_sql = "UPDATE assets SET asset_tag = '".$asset_tag."', location_id = ".$location_id.", description = '".$description."', owner = '".$owner."', last_modified = now(), modified_by = '".$_SESSION['username']."' WHERE asset_id = ".$asset_id;
        		$asset_info = mysql_query($asset_sql) or die(mysql_error());
        		if (!$asset_info) return mysql_error();
				$return_msg = "SUCCESS: Asset successfully updated.";
				//$return_msg = $asset_sql;
				return $return_msg;	
        	}
        	
        	return $return_msg;
		}

        function add_product($identifier = "", $long_description = "") {
        	//$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$long_description = addslashes($long_description);
        	$identifier = addslashes($identifier);
        	$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
        	if ($identifier == "" || $long_description == "") {
        		$return_msg = "ERROR: You must provide an identifier and a description to add a product.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	} else {
        		$product_check_sql = "SELECT product_id FROM products WHERE identifier = '".$identifier."'";
        		$product_check_info = mysql_query($product_check_sql) or die(mysql_error());
        		$product_count = mysql_num_rows($product_check_info);
        		if ($product_count>0) {
        			$return_msg = "ERROR: There is already a product in the database with identifier '".$identifier."'";
					return $return_msg;	
        		}
				$product_sql = "INSERT INTO products (identifier, long_description, date_created, created_by) VALUES ('".$identifier."', '".$long_description."', now(), '".$_SESSION['username']."')";
        		$product_info = mysql_query($product_sql) or die(mysql_error());
        		if (!$product_info) return mysql_error();
				$return_msg = "SUCCESS: Product successfully created.";
				//$return_msg = $product_sql;
				return $return_msg;	
        	}
        	
        	return $return_msg;
        }

		function edit_product($product_id = 0, $identifier = "", $long_description = "") {
			//$illegal_chars = array("-", ",", ".", "'", ";", "(", ")", "%");
        	$long_description = addslashes($long_description);
        	$identifier = addslashes($identifier);
			$return_msg = "ERROR: You should never see this message. The software is havening a problem.";
			if ($identifier == "" || $product_id == 0 || $long_description == "") {
        		$return_msg = "ERROR: You must provide an identifier and a description when editing a product.";
        		//$return_msg .= "<br />Licence ID:".$licence_id." AssetID:".$asset_id." Product ID:".$product_id." PIDKEY:".$pidkey;
				return $return_msg;
        	} else {
        		$product_check_sql = "SELECT product_id FROM products WHERE identifier = '".$identifier."'";
        		$product_check_info = mysql_query($product_check_sql) or die(mysql_error());
        		$product_count = mysql_num_rows($product_check_info);
        		$existing_product_id = mysql_result($product_check_info, 0, 'product_id');
        		if ($product_count>0 && $existing_product_id != $product_id) {
        			$return_msg = "ERROR: There is already a product in the database with identifier '".$identifier."'";
					return $return_msg;	
        		}
        		$product_sql = "UPDATE products SET identifier = '".$identifier."', long_description = '".$long_description."', last_modified = now(), modified_by = '".$_SESSION['username']."' WHERE product_id = ".$product_id;
        		$product_info = mysql_query($product_sql) or die(mysql_error());
        		if (!$product_info) return mysql_error();
				$return_msg = "SUCCESS: Product successfully updated.";
				//$return_msg = $product_sql;
				return $return_msg;	
        	}
        	return $return_msg;
		}


}// end of class

?>
