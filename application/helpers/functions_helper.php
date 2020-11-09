<?php
/* Custom Helper for extra common functions */
		
#Count Rows		
function total_rows($table, $where = array(), $group =  array(), $where1 = array()){
    $CI =& get_instance();
    if (is_array($where)) {
        if (sizeof($where) > 0) {
            $CI->db->where($where);
        }
    } elseif (strlen($where) > 0) {
        $CI->db->where($where);
    }
	
	
	
	if (is_array($group)) {
		if (sizeof($group) > 0) {
			 $CI->db->group_by($group);
		}
	}elseif (strlen($group) > 0) {
        $CI->db->group_by($group);
    }	
	
	//echo $CI->db->last_query();
    return  $CI->db->count_all_results($table);
	
	
}		

#Count Rows		
function total_column_value_counts($table, $column, $where = array()){
    $CI =& get_instance();
    if (is_array($where)) {
        if (sizeof($where) > 0) {
            $CI->db->where($where);
        }
    } elseif (strlen($where) > 0) {
        $CI->db->where($where);
    }
	
	$CI->db->select_sum($column);
	$CI->db->from($table);
	$amount = $CI->db->get()->row()->$column;
	
    return ($amount > 0 ? $amount : '0.00');
}		
		
/* Function to return edit permissions */	
function edit_permissions() {
	$CI =& get_instance();
	$can_edit = false;
	$permissions = $CI->data['permissions'];
	if(!empty($permissions) && $permissions->is_all != 1) { 
		if($permissions->is_edit == 1) {
		$can_edit = true;
		} 
	} else {
		$can_edit = true;
	}
	return $can_edit;		
}


/* Function to return add permissions */	
function add_permissions() {
	$CI =& get_instance();
	$can_add = false;
	$permissions = $CI->data['permissions'];
	if(!empty($permissions) && $permissions->is_all != 1) { 
		if($permissions->is_add == 1) {
		$can_add = true;
		} 
	} else {
		$can_add = true;
	}
	return $can_add;		
}


/* Function to return add permissions */	
function view_permissions() {
	$CI =& get_instance();
	$can_view = false;
	$permissions = $CI->data['permissions'];
	if(!empty($permissions) && $permissions->is_all != 1) { 
		if($permissions->is_view == 1) {
		$can_view = true;
		} 
	} else {
		$can_view = true;
	}
	return $can_view;		
}

/* Function to return delete permissions */	
function delete_permissions() {
	$CI =& get_instance();
	$can_delete = false;
	$permissions = $CI->data['permissions'];
	if(!empty($permissions) && $permissions->is_all != 1) { 
		if($permissions->is_delete == 1) {
		$can_delete = true;
		} 
	} else {
		$can_delete = true;
	}
	return $can_delete;		
}

/* Function to return delete permissions */	
function validate_permissions() {
	$CI =& get_instance();
	$can_validate = false;
	$permissions = $CI->data['permissions'];
	if(!empty($permissions) && $permissions->is_all != 1) { 
	
		if($permissions->is_validate == 1) {
		$can_validate = true;
		} 
	} else {
		$can_validate = true;
	}
	return $can_validate;		
}


function fetch_tbl_data($table , $field , $value){
	$result_data = '';
    $CI =& get_instance();
	$CI->db->select('*');   
	$CI->db->from($table);
	$CI->db->like($field, $value);
	$qry = $CI->db->get();	
	$result = $qry->result_array();
	return $result;
}

/* Let's validate if all required fields are in the ajax request */
function validate_fields($fields = array(), $required = array()){	
	$output = array();	
	if(!empty($fields) && isset($fields['save_status']) && $fields['save_status'] !=0 ){
		foreach($required as $val){			
			if(isset($fields[$val]) && $fields[$val] == ''){
				$output[$val] = '<p>' . ucwords(str_replace('_', ' ', $val)) . ' is an required field! </p>';
			}			
			if(!isset($fields[$val])){
				$output[$val] = '<p>' . ucwords(str_replace('_', ' ', $val)) . ' is missing from your request! </p>';
			}			
		}
	}else{
		$output = array();
	}
	return $output;
}

/* Function to Format data before saving in db */  
function format_data_to_be_added($all_fields = array(), $post_data = array()){        
	$data = array();        
	foreach($all_fields as $v){
		$data[$v] = (isset($post_data[$v]))?$post_data[$v]:'';
	}        
	return $data;        
}	
/* Function to check valid fields while save/edit */
function valid_fields($is_valid = ''){
	$output = '';
	foreach ($is_valid as $v) {
		$output .= $v;
	}
	$result = array(
		'msg' => $output,
		'status' => 'error',
		'code' => 'C514'
	);
	echo json_encode($result);
	die;
}



function send_mail($to = '', $subject = '', $message = '') { 
	 $CI =& get_instance();
	/* $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.yourdomainname.com.',
            'smtp_port' => 465,
            'smtp_user' => 'erp@busybanda.com', // change it to yours
            'smtp_pass' => '******', // change it to yours
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE,
            'newline' => "\r\n"
         );	
	 $CI->load->library('email', $config);*/
	
	$header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; margin: 0; padding: 0;">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width" />
		</head>
		<body style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; width: 100% !important; height: 100%; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; text-align: center; background: #efefef; margin: 0; padding: 40px 0;" bgcolor="#efefef">
			<table class="body-wrap text-center" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; width: 100% !important; height: 100%; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; text-align: center; background: #efefef; margin: 0; padding: 0;" bgcolor="#efefef">
				<tr style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; margin: 0; padding: 0;">
					<td class="container" align="center" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; display: block !important; clear: both !important; max-width: 580px !important; margin: 0 auto; padding: 0;">
						<!-- Message start -->
						<table style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; width: 100% !important; border-collapse: collapse; margin: 0; padding: 0;">
							<tr style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; margin: 0; padding: 0;">
								<td align="center" class="masthead" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; color: white; background: #099a8c; margin: 0; padding: 30px 0;     border-radius: 4px 4px 0 0;" bgcolor="#099a8c"> <img src="'.base_url().'assets/images/logo.png" alt="logo" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; max-width: 100%; display: block; margin: 0 auto; padding: 0;" /></td>
							</tr>';					
			$footer = '<tr style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; margin: 0; padding: 0;">
					<td class="container" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; display: block !important; clear: both !important; max-width: 580px !important; margin: 0 auto; padding: 0;">
						<!-- Message start -->
						<table style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; width: 100% !important; border-collapse: collapse; margin: 0; padding: 0;">
							<tr style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; margin: 0; padding: 0;">
								<td class="content footer" align="center" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; background: white none; margin: 0; padding: 30px 35px;     border-radius: 0 0 4px 4px;" bgcolor="white">							
									<p style="font-size: 14px; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; font-weight: normal; color: #888; text-align: center; margin: 0; padding: 0;" align="center"><a href="'. base_url() .'" style="font-size: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; color: #888; text-decoration: none; font-weight: bold; margin: 0; padding: 0;">ERP</a></p>
									<p style="font-size: 14px; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; font-weight: normal; color: #888; text-align: center; margin: 0; padding: 0;" align="center">Support: dev@lastingerp.com</p>
									<p style="font-size: 14px; font-family: Helvetica, Arial, sans-serif; line-height: 1.65; font-weight: normal; color: #888; text-align: center; margin: 0; padding: 0;" align="center">Phase 1 Industrial Area Panchkula Plot No 39, India - 134109</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
	</html>';
	$messageContent = $header.$message.$footer;	
	// $CI->email->set_mailtype("html");
	// $CI->email->set_newline("\r\n");	
	// $CI->email->to($to);
	// $CI->email->from('info@lastingerp.com', "Admin Team");
	// $CI->email->subject($subject);
	// $CI->email->message($messageContent);	
	// $CI->email->send();
	//$this->load->library('email');
	$config=array(
			'charset'=>'utf-8',
			'wordwrap'=> TRUE,
			'mailtype' => 'html',
			'newline' => "\r\n"
		);
		
	$CI->load->library('email', $config);
		$CI->email->initialize($config);
	// from address
		$CI->email->from('info@lastingerp.com', "Admin Team");
		$CI->email->to($to); // to Email address
		$CI->email->subject('Email Subscribed'); // email Subject 
		$CI->email->message($messageContent); // email Body or Message 
		$CI->email->send();
		/* if($CI->email->send()){
			 echo 'email Send';
		 }else{
			 echo 'Email Not send';
		 } 	*/
	
	
	
 }
 
 /* function to display success message */
 function flashMessage($message = '', $status = ''){
	 
	 $messageclass =  (isset($status) && $status == "error")?'alert-danger':'alert-success';
	 $message = '<div id="alert_float_1" class="custom_after_event_message float-alert animated fadeInRight col-xs-11 col-sm-4 alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><span class="fa fa-bell-o" data-notify="icon"></span><span class="alert-title">'.$message.'</span></div>';
	 return $message;
 }

/* Function to redirect permissions */
function permissions_redirect($type_check = ''){
	$CI =& get_instance();
	#echo $type_check;
	$permissions_data = $CI->data['permissions'];
	#pre($permissions_data); die;
	if(!empty($permissions_data) && $permissions_data->is_all == 1) {
	} else {
		if(isset($permissions_data->$type_check) && $permissions_data->$type_check == 0){
			redirect('dashboard', 'refresh');
		}
	}
}

/* Get Menu permission by user Group */
function menu_permissions($menuUrl = '') {
	$CI =& get_instance();
	$user_id = $_SESSION['loggedInUser']->id;
	$result = array();
		if(isset($menuUrl)){			
			$CI->db->select('per.*,sub_module.sub_module_name,sub_module.slug');
			$CI->db->from('permissions as per');
			$CI->db->join('sub_module', 'per.sub_module_id = sub_module.id', 'left');
			$CI->db->join('menus', 'sub_module.slug = menus.url', 'left');
			#$CI->db->join('menus', 'per.slug = menus.url', 'left');
			$CI->db->where(array('per.user_id' => $user_id, 'sub_module.slug' => $menuUrl));
			$query = $CI->db->get(); 			
			$result = $query->row();		
		}	
		
	return $result;
}

/* Function to redirect permissions by menu slug*/
function permissions_byMenu($menuid = 0){
	$menu_permissions = menu_permissions($menuid);
	return $menu_permissions;
}



function pre($data){
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}
function is_login(){ 
      if(isset($_SESSION['loggedInUser'])){
          return true;
      }else{
         redirect( base_url(), 'refresh');
      }
}
function is_company(){
  if(isset($_SESSION['loggedInUser'])){
	if($_SESSION['loggedInUser']->user_id == 0){
		return true;
	}
	}
 }
 
 
function is_admin(){
	 if(isset($_SESSION['loggedInUser'])){
		if(($_SESSION['loggedInUser']->id == 1) && ($_SESSION['loggedInUser']->user_id == 0 )){
			return true;
			}
	}
}
function is_user(){
	if(isset($_SESSION['loggedInUser'])){
		if($_SESSION['loggedInUser']->user_id != 0 ){
			return true;
		}
	}
}

	
	//Encryption function
function easy_crypt($string) {
    return base64_encode($string . "_@#!@");
}

//Decodes encryption
function easy_decrypt($str) {
    $str = base64_decode($str);
    return str_replace("_@#!@", "", $str);
}




 function CheckPermission(){
	$CI = get_instance();
    $rolePermission = isset($_SESSION['loggedInUser'])?$_SESSION['loggedInUser']->role:'';
    if(isset($rolePermission) && $rolePermission != "" ){
        if($rolePermission == 0 || $rolePermission == 1){
          return (object) array('is_all'=>1 , 'is_add'=>1 , 'is_edit'=>1 , 'is_view'=>1 , 'is_delete'=>1 , 'is_validate'=>1);
        } else {	
			$getPermission = array();
			$getPermissions = getRowByTableColomId('permissions',$_SESSION['loggedInUser']->id,'user_id');
			#pre($getPermissions);
			$subModuleUrl = $CI->uri->segment(1).'/'.$CI->uri->segment(2);
			if(!empty($getPermissions)){
			foreach($getPermissions as $getPermission){
				#pre($getPermission);
				if ($getPermission->slug == $subModuleUrl){
					return $getPermission;
				}
			}
			}
       }
    }else{
      return false;
    }
  }
  
  
  	function getRowByTableColomId($tableName='',$id='',$colom='user_id'){ 
		$CI = get_instance();
		$CI->db->select('permissions.*,sub_module.sub_module_name,sub_module.slug');
		$CI->db->from($tableName);
		$CI->db->join('sub_module', 'permissions.sub_module_id = sub_module.id', 'left');
		$CI->db->where($colom , $id);
		$query = $CI->db->get();
		$result = $query->result();
			if(!empty($result))
			{	
				if(!empty($whichColom)){
				 $result = $result->$whichColom;
				 return $result;
				}
				else
				{
					return $result;
				}
			}
			else
			{
				return false;
			}
	
	}
	



/*
Menus and Submenus Listing
*/
function menus_listing_all($status = true){
	$CI = & get_instance();
	$result = array();
	if($status){
	 $menus = get_menus_listing(array("is_mainmenu" => 1, "status" => 1, 'parent_id' => 0));
	 // $menus = get_menus_listing(array("is_mainmenu" => 1, "status" => 1, 'id' => 113));
	}
    else{
		$menus = get_menus_listing(array("is_mainmenu" => 1,'parent_id' => 0));
		//$menus = get_menus_listing(array("is_mainmenu" => 1,'id' => 113));
	 } 
	// 
	if(!empty($menus)){		
		foreach($menus as $menu) {
			
				$submenus = array();
				$submenus = get_menus_listing(array("parent_id" => $menu['id'], "status" => 1));		
				$i = 0;
				foreach($submenus as $submenu) {
					$submenu2 = array();
					$submenu22 = array();
					if($submenu['has_child'] == 1) {					
						$submenu2 = get_menus_listing(array("parent_id" => $submenu['id'], "status" => 1));
					}
					if($submenu['has_child'] == 2) {					
						$submenu22 = get_menus_listing(array("parent_id" => $submenu['id'], "status" => 1));
					}
					$submenus[$i]['submenu'] = $submenu2;	
					//$submenus[$i]['submenu'] = $submenu22;	
					$i++;
		   		 }
				 //pre($submenus);
			$menu['submenu'] = $submenus;	
			$result[] = $menu;
		}
	}
	
	return $result;
} 


	
	
	
/*
Menus and Submenus Listing
*/
function menus_listing($status = true){
	$CI = & get_instance();
	$result = array();
	if($status){
	 //$menus = get_menus_listing(array("is_mainmenu" => 1, "status" => 1, 'parent_id' => 0));
	  //$menus = get_menus_listing(array("is_mainmenu" => 1, "status" => 1, 'id' => 113));
	  $menus = get_menus_listing(" is_mainmenu = 1 and status = 1 and (id=113 or id=1)");
	}
    else{
	//	$menus = get_menus_listing(array("is_mainmenu" => 1,'parent_id' => 0));
		//$menus = get_menus_listing(array("is_mainmenu" => 1,'id' => 113));
		$menus = get_menus_listing(" is_mainmenu = 1  and (id=113 or id=1)");
	 } 
	// 
	if(!empty($menus)){		
		foreach($menus as $menu) {
			
				$submenus = array();
				$submenus = get_menus_listing(array("parent_id" => $menu['id'], "status" => 1));		
				$i = 0;
				foreach($submenus as $submenu) {
					$submenu2 = array();
					$submenu22 = array();
					if($submenu['has_child'] == 1) {					
						$submenu2 = get_menus_listing(array("parent_id" => $submenu['id'], "status" => 1));
					}
					if($submenu['has_child'] == 2) {					
						$submenu22 = get_menus_listing(array("parent_id" => $submenu['id'], "status" => 1));
					}
					$submenus[$i]['submenu'] = $submenu2;	
					//$submenus[$i]['submenu'] = $submenu22;	
					$i++;
		   		 }
				 //pre($submenus);
			$menu['submenu'] = $submenus;	
			$result[] = $menu;
		}
	}
	
	return $result;
} 
	
	# Get menus List
	function get_menus_listing($where = array()){
		
		$CI = & get_instance();
		$CI->db->select('*');
		$CI->db->from('menus');
		$CI->db->where($where);
		$CI->db->order_by("order", "asc");
		
		$query = $CI->db->get();
		
		//pre($query->result_array());
		return $query->result_array();
	}
	
	function get_tags_html($id, $rel_type){
		$tags = '';
		$CI =& get_instance();
		$CI->db->select('tags.name as tagname');   
		$CI->db->from('tags_in');
		$CI->db->join("tags","tags_in.tag_id = tags.id", 'left');
		$CI->db->where(array('rel_id' => $id, 'rel_type' => $rel_type));
		$qry = $CI->db->get();	
		$result = $qry->result_array();	
		if(!empty($result)) {
		$tags .= '<p>';
			foreach($result as $tag) {
				$tags .= '<span class="tag-span">'.$tag['tagname'].'  </span>';
			}   
			$tags .= '</p>';
		}
		return $tags;
	}
	
	function getUom(){
		
		$uom = array('ACRES','AMPERE','BAG','BUSHELS','BUCKET','BUNDLE','BOWL','BOX','BLOCK','BOARD','BOTTLE','BULK','CAN','COIL','CARTIDGE','CARD','COVER','CELSIUS','CENTIMETER','CARTON','CENTIGRAM','CUBE','CARAT','DECIGRAM','DEGREE','DECILITER','DECIMETER','DOZEN','FEET','FOOT','FAHRENHEIT','GALLON','GROSS','GRAM','HD','HERTZ','HECTARE','INCH','KIT','KELVIN','KILOGRAM','KILOMETER','KILOHERTZ','LITER','LOT','METER','MILLIMETER','MEGAHERTZ','NOS','OUNCES','OHMS','PC','POUND','PACK','PAIR','QUART','RACK','ROLL','SET','SQ.INCH','SQ.FEET','SHEET','SQ.METER','YARD','TUBE','TON','TONNE','TEETH','UNIT','UNITS','VOLTS','WATT');
		return $uom;
	}
	
	
	function measurementUnits(){
		$measurementUnits = array('ACRES','AMPERE','BAG','BUSHELS','BUCKET','BUNDLE','BOWL','BOX','BLOCK','BOARD','BOTTLE','BULK','CAN','COIL','CARTIDGE','CARD','COVER','CELSIUS','CENTIMETER','CARTON','CENTIGRAM','CUBE','CARAT','DECIGRAM','DEGREE','DECILITER','DECIMETER','DOZEN','FEET','FOOT','FAHRENHEIT','GALLON','GROSS','GRAM','HD','HERTZ','HECTARE','INCH','KIT','KELVIN','KILOGRAM','KILOMETER','KILOHERTZ','LITER','LOT','METER','MILLIMETER','MEGAHERTZ','NOS','OUNCES','OHMS','PC','POUND','PACK','PAIR','QUART','RACK','ROLL','SET','SQ.INCH','SQ.FEET','SHEET','SQ.METER','YARD','TUBE','TON','TONNE','TEETH','UNIT','UNITS','VOLTS','WATT');
		return $measurementUnits;
	}
