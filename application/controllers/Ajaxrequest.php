<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ajaxrequest extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library(array('form_validation'));
		$this->load->helper(array('language','layout_helper','functions_helper','database_helper'));
		$this->data['db_prefix'] = 'b0318_';
		
	}
	
	public function get_all_products($searchTerm=""){
		if(!empty($this->input->get("q"))){
		//$data = $this->account_model->get_products();
		$this->db->like('product_name', $this->input->get("q"));
		$data = $this->db->get('products')->result();
		
		}
		echo json_encode($data);		
	}
	
		# Function to get address from zipcode
	public function fetch_address_by_zipcode() {	
		$array = '';
		if(isset($_POST['zipcode']) && $_POST['zipcode'] != '') {
			$array =  fetch_address($_POST['zipcode']); 	
		}
		print_r($array);
	}
	
	# Function to fetch the table data via ajax
	public function fetch_tbl_data(){
		$data = fetch_tbl_data($_POST['table'], $_POST['field'] ,$_POST['value']);
		print_r($data);
	}
	
	public function ajaxSelect2search(){
		$json = [];
		if(!empty($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->role == 3){
			$this->db->select($this->input->get("field").' as id,'.$this->input->get("fieldname").' as text');    
			$this->db->from($this->input->get("table"));		
			if(!empty($this->input->get("q"))){		
			$table_field_name = $this->input->get("fieldname");	
				$this->db->like(($table_field_name), $this->input->get("q"));
			}
			if($this->input->get("fieldwhere")!=''){
				#$this->db->where('('.$this->input->get("fieldwhere").')');
				
				/* company group start code */
					if(isset($_SESSION['companyGroupSessionId']) && $_SESSION['companyGroupSessionId'] !='' && $_SESSION['companyGroupSessionId'] !=0){						
						$whereCondition = $this->input->get("fieldwhere");
						$whereCondition = str_replace("created_by_cid=".$_SESSION['loggedInUser']->c_id,"created_by_cid=".$_SESSION['companyGroupSessionId'],$whereCondition);
					}							
				/* company group end code */
				#$dynamicdb->where('('.$this->input->get("fieldwhere").')');
				$this->db->where('('.$whereCondition.')');
				
				
			}
			$qry = $this->db->get();  
		
		}else{
			$dynamicdb = $this->load->database('dynamicdb', TRUE);	
			$dynamicdb->select($this->input->get("field").' as id,'.$this->input->get("fieldname").' as text');    
			$dynamicdb->from($this->input->get("table"));		
			if(!empty($this->input->get("q"))){		
			$table_field_name = $this->input->get("fieldname");	
				$dynamicdb->like(($table_field_name), $this->input->get("q"));
			}
			if($this->input->get("fieldwhere")!=''){
				$whereCondition = $this->input->get("fieldwhere");
				/* company group start code */
					if(isset($_SESSION['companyGroupSessionId']) && $_SESSION['companyGroupSessionId'] !='' && $_SESSION['companyGroupSessionId'] !=0){						
						$whereCondition = $this->input->get("fieldwhere");
						$whereCondition = str_replace("created_by_cid=".$_SESSION['loggedInUser']->c_id,"created_by_cid=".$_SESSION['companyGroupSessionId'],$whereCondition);
					}							
				/* company group end code */
				#$dynamicdb->where('('.$this->input->get("fieldwhere").')');
				$dynamicdb->where('('.$whereCondition.')');
			}
			
			$qry = $dynamicdb->get();  
			
		}
		$result = $qry->result();	
		echo json_encode($result);			
	}
	//Function for get two table data
	public function ajaxSelect2search_for_join(){
		if(!empty($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->role == 3){
			$this->db->select('id, name');
			$this->db->from('account_group');
			$this->db->where('created_by',$_REQUEST['login_id']);
			$this->db->or_where('created_by',0);
			$qry1 = $this->db->get();			
			$this->db->select('id, name, created_by');
			$this->db->from('parent_group');
			$this->db->where('created_by',$_REQUEST['login_id']);
			$this->db->or_where('created_by',0);
			$qry2 = $this->db->get();
		}else{
			$dynamicdb = $this->load->database('dynamicdb', TRUE);	
			$dynamicdb->select('id, name');
			$dynamicdb->from('account_group');
			$dynamicdb->where('created_by',$_REQUEST['login_id']);
			$dynamicdb->or_where('created_by',0);
			$qry1 = $dynamicdb->get();
			
			$dynamicdb->select('id, name, created_by');
			$dynamicdb->from('parent_group');
			$dynamicdb->where('created_by',$_REQUEST['login_id']);
			$dynamicdb->or_where('created_by',0);
			$qry2 = $dynamicdb->get();
		}		
		$result1 = $qry1->result();	
		$result2 = $qry2->result();
		$merge_Data = array_merge($result1,$result2);
		echo json_encode($merge_Data);	
	}
//Function for get two table data
	public function ajaxTagSearch(){
		$json = [];
		if(!empty($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->role == 3){			
			$this->load->database();
			if(!empty($this->input->get("q"))){
				$this->db->like('name', $this->input->get("q"));
				$query = $this->db->select('id,name as text')
							->get("tags");
				$json = $query->result();
			}
		}else{
			$dynamicdb = $this->load->database('dynamicdb', TRUE);				
			$this->load->database();
			if(!empty($this->input->get("q"))){
				$dynamicdb->like('name', $this->input->get("q"));
				$query = $dynamicdb->select('id,name as text')
							->get("tags");
				$json = $query->result();
			}
		}
		echo json_encode($json);
	}
	
#Function to mark as Read notifications
	public function readNotifications(){	
		echo readNotifications();
	}
	
#Function to get user tasks listing
	public function Tasks_listing(){	
		$result = user_tasks();		
	}
	
#Function to save Timer data
	public function save_timer(){
		if($this->input->post("taskid")){
			$res = save_timer_data($this->input->post("taskid"),$this->input->post("timer_note"));	
			if($res){
				$timer = task_timer();
				if(!empty($timer)){
					$time = time();
					$total_time = $time - (int)$timer->start_time;
					$time_diff = date("H:s",(int)$total_time);
					echo '<div class="notification-messages info text-center">
							<p>Started at '.date('Y-m-y H:i', $timer->start_time).'</p>	
							<p>Total logged time : '.$time_diff.'</p>	
							<button class="btn btn-danger stop_timer"><i class="fa fa-clock-o fa-fw fa-lg" aria-hidden="true"></i> Stop Timer</button>	
							<a href="<?php echo base_url();?>users/timesheets">View all timesheets</a>
							<div class="clearfix"></div>
						</div>	';	
				}
			}else {
				echo 'error';	
			}
			die;
		}
	}
	
	#Function to Track Timer 
	public function track_timer(){
		$timer = task_timer();
		if(!empty($timer)){
			$time = time();
			$total_time = $time - (int)$timer->start_time;
			$time_diff = date("H:i",(int)$total_time);
			echo '<div class="notification-messages info text-center">
							<p>Started at '.date('Y-m-y H:i', $timer->start_time).'</p>	
							<p>Total logged time : '.$time_diff.'</p>	
							<button class="btn btn-danger stop_timer"><i class="fa fa-clock-o fa-fw fa-lg" aria-hidden="true"></i> Stop Timer</button>	
							<a href="<?php echo base_url();?>users/timesheets">View all timesheets</a>
							<div class="clearfix"></div>
						</div>	';	
		}else {
			echo '<div class="notification-messages info text-center">
								<p>No started timers found</p>	
								<button class="btn btn-success" data-toggle="modal" data-target="#task_timer_modal"><i class="fa fa-clock-o fa-fw fa-lg" aria-hidden="true"></i> Start Timer</button>	
								<a href="<?php echo base_url();?>users/timesheets">View all timesheets</a>
								<div class="clearfix"></div>
							</div>	';	
		}
	}
	
#Function to Stop Timer
	public function stop_timer(){
		$res = stop_timer();	
		if($res){	
			echo 'success';
		}else{
			echo 'error';
		}
		die;
	}
 
#Function to delete multiple rows
    public function delete_multiple(){		
	   $del_ids 		= $this->input->post('ids');
	   $main_tbl_name 	= $this->input->post('tbl');
	   $key 			= $this->input->post('key');
	   $del_id 			= [];
	   foreach($del_ids as $id){	
	  //helper function to check deleted element relation before delete
	    if(relation_check($main_tbl_name, $id)){
		   array_push($del_id,$id);		  
		  }
       }   
      //check if del_id array 
	    $num_row_deleted = 0; 
       if(!empty($del_id)){		   
		   //finally delete rows
		    $num_row_deleted = delete_rows($main_tbl_name, $del_id, $key);		   
			echo json_encode(array('rows_deleted'=>$num_row_deleted,'Total_requested_rows'=>count($del_ids)));
	    }
	   else{
		    echo json_encode(array('rows_deleted'=>$num_row_deleted,'Total_requested_rows'=>count($del_ids)));	   
	       }	
	 
	}	
	
	
	
	public function app_menus_listing($parent_menu_id=''){
		$status =true;
		$result = array();
		$menusResult = array();
		if($status){
		 // $menus = get_menus_listing(array("is_mainmenu" => 1, "status" => 1, 'parent_id' => 0));
		  //$menus = get_menus_listing(array("is_mainmenu" => 1, "status" => 1, 'id' => $_POST['parent_id']));
		  $menus = get_menus_listing(" is_mainmenu = 1 and status = 1 and (id= ".$_POST['parent_id']." or id=1)");
		}
		else{
			//$menus = get_menus_listing(array("is_mainmenu" => 1,'parent_id' => 0));
			//$menus = get_menus_listing(array("is_mainmenu" => 1, 'id' => $_POST['parent_id']));
			$menus = get_menus_listing(" is_mainmenu = 1 and (id= ".$_POST['parent_id']." or id=1)");
		} 	
		if(!empty($menus)){		
			foreach($menus as $menu) {	
				//pre($menu);
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
				
				$menu['submenu'] = $submenus;	
				$result[] = $menu;
			}
		}	
		$menusResult = $result ;
		
		$menuHtml = '';
		$menuHtml .= '<ul class="nav side-menu">';				
				//if(isset($_SESSION['loggedInCompany']) && $_SESSION['loggedInCompany']->role == 1){ echo '<li><a href="'.base_url().'company/businessProof">Business Proof</a>'; } 
	if(($_SESSION['loggedInUser']->role == 1 && $_SESSION['loggedInCompany']->business_status == 1) || ($_SESSION['loggedInUser']->role == 2 ) || ($_SESSION['loggedInUser']->role == 3 )){
				//	pre($_SESSION['loggedInUser']->role);
					//$menus = menus_listing();			
					#$parent_menu = (isset($parent_menu) && $parent_menu != '')?$parent_menu:'';
					#$active_menu = (isset($active_menu) && $active_menu != '')?$active_menu:'';				
					//pre($menus);
				
				if(!empty($menusResult)){					
					foreach($menusResult as $menuRes){							
						if(($menuRes['has_child'] == 1)  && (!empty($menuRes['submenu']))){
								$menuHtml .= '<li><a><i class=" '.$menuRes["icon"].' "></i> '.$menuRes["title"].' <span class="fa fa-chevron-down"></span></a>';							
							if(!empty($menuRes['submenu'])) {	
								//$menuHtml .= '<ul class="nav child_menu active">';
								$menuHtml .= '<ul class="nav child_menu">';
								
								foreach($menuRes['submenu'] as $submenu) {										
									if($_SESSION['loggedInUser']->role == 2 ){	
										//pre($submenu);									
										if($submenu['has_child'] == 0 && empty($submenu['submenu'])){
											//pre($submenu);											
											$subMenuPermission = permissions_byMenu($submenu["url"]);
											#pre($subMenuPermission);
											if(!empty($subMenuPermission)){
												//if(($subMenuPermission->is_view !=0 && $subMenuPermission->sub_module_name !='leads') || ($subMenuPermission->sub_module_name =='leads')){
												if(($subMenuPermission->is_view !=0 && ($subMenuPermission->sub_module_name !='leads' || $subMenuPermission->sub_module_name !='contacts' || $subMenuPermission->sub_module_name !='accounts' || $subMenuPermission->sub_module_name !='sale orders' || $subMenuPermission->sub_module_name !='sale targets' || $subMenuPermission->sub_module_name !='proforma invoice' || $subMenuPermission->sub_module_name != 'dashboard')) || ($subMenuPermission->sub_module_name =='leads' || $subMenuPermission->sub_module_name =='contacts' || $subMenuPermission->sub_module_name =='accounts' || $subMenuPermission->sub_module_name =='sale orders' || $subMenuPermission->sub_module_name =='sale targets' || $subMenuPermission->sub_module_name =='proforma invoice' || $subMenuPermission->sub_module_name == 'dashboard')){
													//$menuHtml .= '<li><a href="'.base_url().$submenu["url"].'" id="'.$submenu['parent_id'].'" class="module_menu"> '.$submenu["title"].' </a></li>';
													$menuHtml .= '<li><a href="javascript:void(0)" data-href="'.base_url().$submenu["url"].'" data-parent_id="'.$submenu['parent_id'].'" class="module_menu" id="'.$submenu['parent_id'].'"> '.$submenu["title"].' </a></li>';
												}
											}
										}else if(!empty($submenu['submenu'])){
										#	pre($submenu);
												$title = '';
												//pre($submenu['submenu']);
												#foreach($submenu['submenu'] as $sub_sub_mnu){
													//$permissionForChildMenu = permissions_byMenu($sub_sub_mnu["url"]);
													#$permissionForChildMenu = permissions_byMenu($submenu["url"]);
													//pre($permissionForChildMenu);
													//echo count($permissionForChildMenu).'<br>';
													#if(!empty($permissionForChildMenu)){
														#if($permissionForChildMenu->is_view ==1){
															#if(count($permissionForChildMenu)){
																$title = '<li><a>'.$submenu["title"].' <span class="fa fa-chevron-down"></span></a>';
																$menuHtml .= $title;
															#}
														#}
													#}															
												#}
												//echo $title;
												$menuHtml .= '<ul class="nav child_menu">';
												foreach($submenu['submenu'] as $sub_sub_mnu){
													$permissionForChildMenu = permissions_byMenu($sub_sub_mnu["url"]);
													//pre($permissionForChildMenu);
													if(!empty($permissionForChildMenu)){
														#if($permissionForChildMenu->is_view !=0){
														if(($permissionForChildMenu->is_view !=0 && ($permissionForChildMenu->sub_module_name !='leads' || $permissionForChildMenu->sub_module_name !='contacts' || $permissionForChildMenu->sub_module_name !='accounts' || $permissionForChildMenu->sub_module_name !='sale orders' || $permissionForChildMenu->sub_module_name !='sale targets' || $permissionForChildMenu->sub_module_name !='proforma invoice' || $permissionForChildMenu->sub_module_name != 'dashboard')) || ($permissionForChildMenu->sub_module_name =='leads' || $permissionForChildMenu->sub_module_name =='contacts' || $permissionForChildMenu->sub_module_name =='accounts' || $permissionForChildMenu->sub_module_name =='sale orders' || $permissionForChildMenu->sub_module_name =='sale targets' || $permissionForChildMenu->sub_module_name =='proforma invoice' || $permissionForChildMenu->sub_module_name == 'dashboard')){
															$menuHtml .= '<li class="sub_menu"><a href="'.base_url().$sub_sub_mnu["url"].'" >'.$sub_sub_mnu['title'].'</a></li>';
														}
													}
													
												}
												$menuHtml .='</ul></li>';
											}										
									}else if($_SESSION['loggedInUser']->role == 3 ){												
												#pre($submenu);
												if(empty($submenu['submenu']) ){ 
													//$menuHtml .='<li><a href="'.base_url().$submenu["url"].'" id='.$submenu["parent_id"].' class="module_menu"> '.$submenu["title"].' </a></li>';
													if(($submenu['parent_id'] == 54 && $submenu['id'] == 55 )|| ($submenu['parent_id'] == 85 && $submenu['id'] == 4 ) || $submenu['parent_id'] == 113){
														$menuHtml .='<li><a href="javascript:void(0)" data-href="'.base_url().$submenu["url"].'" data-parent_id='.$submenu["parent_id"].' class="module_menu" id="'.$submenu['parent_id'].'"> '.$submenu["title"].' </a></li>';
													}
														
												}else{
													$menuHtml .='<li><a>'.$submenu["title"].' <span class="fa fa-chevron-down"></span></a>';
													$menuHtml .= '<ul class="nav child_menu">';
													foreach($submenu['submenu'] as $sub_sub_mnu){
														$menuHtml .= '<li class="sub_menu"><a href="'.base_url().$sub_sub_mnu["url"].'">'.$sub_sub_mnu['title'].'</a></li>';
													}
													$menuHtml .= '</ul></li>';
												}
										}else{											
											if(empty($submenu['submenu'])){													
												//$menuHtml .='<li><a href="'.base_url().$submenu["url"].'" id='.$submenu["parent_id"].' class="module_menu"> '.$submenu["title"].' </a></li>';
												$menuHtml .='<li><a href="javascript:void(0)" data-href="'.base_url().$submenu["url"].'" data-parent_id='.$submenu["parent_id"].' class="module_menu" id="'.$submenu['parent_id'].'"> '.$submenu["title"].' </a></li>';
											}											
											if($submenu['submenu']){
												$menuHtml .='<li><a>'.$submenu["title"].' <span class="fa fa-chevron-down"></span></a>';
												$menuHtml .= '<ul class="nav child_menu">';
												foreach($submenu['submenu'] as $sub_sub_mnu){
												$menuHtml .= '<li class="sub_menu"><a href="'.base_url().$sub_sub_mnu["url"].'">'.$sub_sub_mnu['title'].'</a></li>';
												}
												$menuHtml .= '</ul></li>';
											}	
										}
								}
							}
							$menuHtml .= '</ul>';
							$menuHtml .= '</li>';
						}else{
							if($_SESSION['loggedInUser']->role == 3  &&  $menuRes["id"] == 1){
								
							}else{								
								$menuHtml .= '<li><a href="'.base_url().$menuRes["url"].'"><i class=" '.$menuRes["icon"].' "></i>'.$menuRes["title"].' </a></li>';   
							}
						}
					}
				} 
				}                
                $menuHtml .= '</ul>';
		
		//return $result;
		echo $menuHtml;
	} 
	
	
	public function fetchNotification(){		
		fetchNotification($_POST);
	}
	public function createGroupSession(){
		if(!empty($_POST) && isset($_POST['id'])){
			$this->session->set_userdata('companyGroupSessionId',$_POST['id']);
			echo json_encode(array('success' => 1));
		}else{
			echo json_encode(array('success' => 0));
		}
	}
	





public function ajaxSelect2searchso(){
		$json = [];
		if(!empty($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->role == 3){
			$this->db->select($this->input->get("field").' as id,'.$this->input->get("fieldname").' as text');    
			$this->db->from($this->input->get("table"));		
			if(!empty($this->input->get("q"))){		
			$table_field_name = $this->input->get("fieldname");	
				$this->db->like(($table_field_name), $this->input->get("q"));
			}
			if($this->input->get("fieldwhere")!=''){
				#$this->db->where('('.$this->input->get("fieldwhere").')');
				
				/* company group start code */
					if(isset($_SESSION['companyGroupSessionId']) && $_SESSION['companyGroupSessionId'] !='' && $_SESSION['companyGroupSessionId'] !=0){						
						$whereCondition = $this->input->get("fieldwhere");
						$whereCondition = str_replace("created_by_cid=".$_SESSION['loggedInUser']->c_id,"created_by_cid=".$_SESSION['companyGroupSessionId'],$whereCondition);
					}							
				/* company group end code */
				#$dynamicdb->where('('.$this->input->get("fieldwhere").')');
				$this->db->where('('.$whereCondition.')');
				
				
			}
			$qry = $this->db->get();  
		
		}else{
			$dynamicdb = $this->load->database('dynamicdb', TRUE);	
			$dynamicdb->select($this->input->get("field").' as id,'.$this->input->get("fieldname").' as text');    
			$dynamicdb->from($this->input->get("table"));		
			if(!empty($this->input->get("q"))){		
			$table_field_name = $this->input->get("fieldname");	
				$dynamicdb->like(($table_field_name), $this->input->get("q"));
			}
			if($this->input->get("fieldwhere")!=''){
				$whereCondition = $this->input->get("fieldwhere");
				/* company group start code */
					if(isset($_SESSION['companyGroupSessionId']) && $_SESSION['companyGroupSessionId'] !='' && $_SESSION['companyGroupSessionId'] !=0){						
						$whereCondition = $this->input->get("fieldwhere");
						$whereCondition = str_replace("created_by_cid=".$_SESSION['loggedInUser']->c_id,"created_by_cid=".$_SESSION['companyGroupSessionId'],$whereCondition);
					}							
				/* company group end code */
				#$dynamicdb->where('('.$this->input->get("fieldwhere").')');
				$dynamicdb->where('('.$whereCondition.')');
			}
			
			$qry = $dynamicdb->get();  
			
		}
		#ini_set('error_reporting', E_STRICT);
		$result = $qry->result();
		
				$i = 0;
				$newProduct = array();

			foreach ($result as $rr) {
				
					$tt = json_decode($rr->text);

					if(!empty($tt)){
						foreach ($tt as $oo) {
								$ss =	getNameByIdso('material', $oo->product,'id');
								$oo->id  = $ss->id;
								$oo->text = $ss->material_name;
								$newProduct[$i] = $oo;
								$i++;
											
					}
					echo json_encode($newProduct);
				}

			}
									
	}

}
?>