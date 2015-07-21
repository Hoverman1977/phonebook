<?php

class module {
	var $module_id = 0;
	var $module_name;
	var $menu_icon;
	
	function module() {
		if (isset($_GET['module'])) {
			$this->module_id = $_GET['module'];
		}
		if ($this->module_id > 0) {
			$module_sql = "SELECT module_id, module_name, menu_icon FROM modules WHERE module_id = ".$this->module_id;
			$module_info = mysql_query($module_sql) or die (mysql_error());
			if ($module_info) {
				$this->module_name = mysql_result($module_info, 0, 'module_name');
				$this->menu_icon = mysql_result($module_info, 0, 'menu_icon');
			}
		} elseif ($_SERVER['SCRIPT_NAME'] == '/login.php') {
			$this->module_name = "Login";
			$this->menu_icon = "locked.png";
		} else {
			$this->module_name = "Home";
			$this->menu_icon = "icon_home.gif";
		}
	}

	function enabled_modules() {
		$module_sql = "SELECT module_id, module_name, menu_text, href FROM modules WHERE enabled = 'y'";
		$module_info = mysql_query($module_sql) or die(mysql_error());
		if ($module_info) {
			return $module_info;
		} else {
			return false;
		}
	}
	
	function nav_menu() {
		if ($this->module_id == 0) return false;
		$module_sql = "SELECT item_name, item_text, href FROM nav_menu_items WHERE enabled = 'y' AND module_id = ".$this->module_id." ORDER BY display_order";
		$module_info = mysql_query($module_sql) or die(mysql_error());
		if ($module_info) {
			return $module_info;
		} else {
			return false;
		}
	}
	
	function content_menu($nav_menu_id) {
		//if ($nav_menu_id == 0) return false;
		$module_sql = "SELECT item_name, item_text, href, icon FROM content_menu_items WHERE enabled = 'y' AND module_id = ".$this->module_id." ORDER BY display_order";
		//print $module_sql;
		$module_info = mysql_query($module_sql) or die(mysql_error());
		if ($module_info) {
			return $module_info;
		} else {
			return false;
		}
	}

	
} // end of class

?>