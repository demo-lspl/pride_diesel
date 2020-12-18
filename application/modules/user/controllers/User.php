<?php
class User extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		//$this->settings['css'][] = 'assets/css/tags.scss';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';		
		
		$this->scripts['js'][] = 'assets/modules/user/js/script.js';
		$this->scripts['js'][] = 'assets/js/tags.js';
		$this->scripts['js'][] = 'assets/modules/user/pricelist/js/script.js';
		$this->load->model('user_model');
		$this->load->library('form_validation');
	}
	
	public function index($pagination_offset=0){
		$this->settings['title'] = 'All Companies';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('All Companies', base_url() . 'user/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->load->library('pagination');
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}
		$this->data['allUserData'] = $this->user_model->get_users($where);
        // pagination
        $config['base_url'] = site_url('user/index');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allUserData']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['allUserData'] = $this->user_model->get_pagination($config['per_page'], $page, $where);		
		$this->_render_template('index', $this->data);
	}	
	
	public function edit($id = null){
		$this->load->model('sales_person/sales_person_model');
		//$this->data['salesPersons'] = $this->sales_person_model->get_sales_executives();			
		$this->data['salesPersons'] = $this->user_model->get_sales_executives();			
		$this->data['companyType'] = $this->user_model->get_c_type();			
		$this->data['products'] = $this->user_model->get_products();			
		if($id){
			$this->settings['title'] = 'Company Edit';
			$this->breadcrumb->mainctrl("user");
			$this->breadcrumb->add('Company Edit', base_url() . 'user/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
			$this->data['company'] = $this->user_model->get_by_id($id);		
		}else{
			$this->settings['title'] = 'Add Company';
			$this->breadcrumb->mainctrl("user");
			$this->breadcrumb->add('Add Company', base_url() . 'user/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();

			$this->data['company'] = $this->user_model->get_new();
						
		}
		$id == NULL || $this->data['company'] = $this->user_model->get_by_id($id);
		
		$rules = $this->user_model->rules;

		$id || $rules['company_password']['rules'] .= '|required';	
		$id || $rules['company_email']['rules'] .= '|is_unique[users.company_email]';	
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			/* if($id==null){
				$userExist = $this->db->where('company_email', $this->input->post('company_email'))->get('users')->row();
				if(count($userExist)>0){
					echo "<script>alert('Email already exists')</script>";
					exit;
				}
			} */
			
            $data = $this->user_model->array_from_post(array('company_location', 'company_type', 'company_type_ca', 'company_type_ca_husky', 'sales_person', 'company_name', 'moreEmails', 'address', 'city', 'province', 'postal_code','company_email', 'invoice_schedule', 'usa_pricing', 'cad_pricing', 'cad_pricing_husky', 'sms_notification', 'allowMoneyCode', 'company_password', 'role'));
			
			if($data['company_password'] == ''){
				unset($data['company_password']);
			}else
            { 
                $data['company_password'] = md5($data['company_password']);
            }
			(!empty($this->input->post('customer_id'))) ? $data['customer_id'] = $this->input->post('customer_id'):$data['customer_id'] = NULL;
			(!empty($this->input->post('efs_policy_id'))) ? $data['efs_policy_id'] = $this->input->post('efs_policy_id'):$data['efs_policy_id'] = NULL;
			if($this->input->post('product_id') != null && $this->input->post('fix_price') == 1){
				$data['fix_price'] = $this->input->post('fix_price');
				
			$fixPriceProduct_Length = count($_POST['product_id']);
				if($fixPriceProduct_Length >0){
					$arr = [];
					$i = 0;
					while($i < $fixPriceProduct_Length) {	
						$jsonArrayObject = (array('fix_cost_product' =>$_POST['product_id'][$i],'fix_cost_product_amt' => $_POST['amount'][$i]));
						$arr[$i] = $jsonArrayObject;
						$i++;				
					}
					$fix_cost_data = json_encode($arr);
				}else{
					$fix_cost_data = '';
				}
				$data['fix_cost_data'] = $fix_cost_data;
			}else{
				$data['fix_price'] = 0;
				$data['fix_cost_data'] = '';			
			}	
			
			if($id == NULL){
				$data['status'] = 0;
				$data['date_created'] = date('Y-m-d H:i:s');
			}
			//pre($data);die;
			$cid = $this->user_model->create_user($data, $id);
			
			if($cid){

				$this->session->set_flashdata('success_msg', 'Changes Saved.');
				
				redirect(base_url('user/edit/'.$cid), 'refresh');				
			}
			
		}		
		//pre(validation_errors());die;
		$this->_render_template('edit', $this->data);
	}
	
	public function all_products(){
		$this->db->select('product_name as id, product_name as text');
		$this->db->from('products');

		if(!empty($this->input->get("searchTerm"))){		
		$table_field_name = $this->input->get("fieldname");	
			$this->db->like(($table_field_name), $this->input->get("searchTerm"));
		}		
		$query = $this->db->get();
		$json = $query->result();
		echo json_encode($json);
	}	
	
	public function edit_profile($id=null){
		$this->settings['title'] = 'Edit Profile';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Edit Profile', base_url() . 'user/edit-profile');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		if($id){
			$this->data['getDetails'] = $this->user_model->get_by_id($id);
		}		
		
		$rules = $this->user_model->rules_new_company;
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			$data = $this->user_model->array_from_post(array('company_name', 'address', 'company_email'));
		
			if(!empty($this->input->post('company_password'))){
				$data['company_password'] = md5($this->input->post('company_password'));
			}

			$data['date_modified'] = date('Y-m-d h:i:s');
			
			$id = $this->user_model->update_user($data, $id);
			if($id){
				$this->session->set_flashdata('success_msg', 'Profile Updated');
				redirect(base_url('user/edit_profile/'.$id), 'refresh');
			}
		}
			
		
		$this->_render_template('edit-profile', $this->data);
	}
	
	public function delete($id=null){
		$userDeleted = $this->user_model->delete($id);
		
		if($userDeleted == true){
			$this->session->set_flashdata('success_msg', 'User deleted');
			redirect(base_url('user/index'), 'refresh');
		}
	}
	
	public function exportUser(){
		//File Name
		$fileName = 'user-data-'.time().'.csv'; //format should be .xlsx , .csv

		$this->load->library('excel');
		$empInfo = $this->user_model->exportusers();
		
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'First Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Last Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Email ID');


        // set Row
        $rowCount = 2;
        foreach ($empInfo as $element) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element['first_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $element['last_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element['user_email']);

            $rowCount++;
        }

       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
			        header('Content-Type: application/vnd.ms-excel');
			       header("Content-Disposition: attachment;filename=Exportcontacts.xlsx");
			         ob_end_clean();
			        $object_writer->save('php://output');		
	}
	
	public function importUser(){
		
		   if(!empty($_FILES['uploadFile']['name'])!=''){
			    
                $path = 'assets/modules/user/excel_for_users/';
                require_once APPPATH . "/third_party/PHPExcel.php";
                $config['upload_path'] = $path;
                $config['allowed_types'] = "csv|xls|xlsx";
                $config['remove_spaces'] = true;
                $this->load->library('upload', $config);
				
                $this->upload->initialize($config); 
					
                if (!$this->upload->do_upload('uploadFile')) {
                    $error = array('error' => $this->upload->display_errors());
                } else {
                    $data = array('upload_data' => $this->upload->data());
                }
				
                if(empty($error)){
                  if (!empty($data['upload_data']['file_name'])) {
                    $import_xls_file = $data['upload_data']['file_name'];
                } else {
                    $import_xls_file = 0;
                }
                $inputFileName = $path . $import_xls_file;

                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(true, true, true, true, true);
                    $flag = true;
                    $i=0;
                    foreach ($allDataInSheet as $value) {
                      if($flag){
                        $flag =false;
                        continue;
                      }
                      $insertdata[$i]['first_name'] = $value['A'];
                     
                      $insertdata[$i]['last_name'] = $value['B'];
                     
                      $insertdata[$i]['email'] = $value['C'];
                     
						$i++;
                    }               
                    $result = $this->user_model->importUsers($insertdata);   
                    if($result){
                      //echo "Imported successfully";

                    }else{
                      echo "ERROR !";
                    }             
      
              } catch (Exception $e) {
                   die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                            . '": ' .$e->getMessage());
                }
              }else{
                  echo $error['error'];
                }
                 
         
        	$this->session->set_flashdata('success_msg', 'Users Imported Successfully');
       		 redirect(base_url().'user/index', 'refresh');

    }

		echo"<script>alert('Please Select the File to Upload')</script>";
		redirect(base_url().'user/index', 'refresh');
	}
	
	public function create_users_pdf(){
      require_once(APPPATH.'libraries/tcpdf/tcpdf.php');  
      $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("User Data");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(TRUE, 10);  
      $obj_pdf->SetFont('helvetica', '', 12);  
      $obj_pdf->AddPage();  
      $content = '';  
      $content .= '  
      <h3 align="center">User Data</h3><br /><br />  
      <table border="1" cellspacing="0" cellpadding="5">  
           <tr>  
                <th width="5%">ID</th>  
                <th width="30%">First Name</th>  
                <th width="30%">Last Name</th>  
                <th width="35%">Email ID</th>   
           </tr>  
      ';  
      //$content .= fetch_data();  
      $userData = $this->user_model->exportuserspdf();
		foreach($userData as $users){
			$content .= "<tr>
			<td>".$users->id."</td>
			<td>".$users->first_name."</td><td>
			".$users->last_name."</td>
			<td>".$users->user_email."</td>"
			."</tr>";
		}	  
      $content .= '</table>';  
      $obj_pdf->writeHTML($content);  
      $obj_pdf->Output('sample.pdf', 'I');		
		
		/* $this->load->library('Pdf');
		$dataPdf = $this->user_model->get_data_byId('users','id',$id);
		
		create_pdf($dataPdf,'modules/user/views/view_user_pdf.php');
		$this->load->view('sale_orders/view_saleOrder_pdf'); */
	}

	/********************	Company		**************************/
	public function company_index(){
		$this->settings['title'] = 'All Company';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('All Company', base_url() . 'user/company_index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type($where);
        // pagination
        $config['base_url'] = site_url('user/company_index');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['companyTypeResult']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['companyTypeResult'] = $this->user_model->get_pagination_company_type($config['per_page'], $page, $where);			

		$this->_render_template('company/index', $this->data);
	}
	
	public function company_types(){
		$this->db->select('id as id, company_type as text');
		$this->db->from('company_types');

		if(!empty($this->input->get("searchTerm"))){		
		$table_field_name = $this->input->get("fieldname");	
			$this->db->like(($table_field_name), $this->input->get("searchTerm"));
		}		
		$query = $this->db->get();
		$json = $query->result();
		echo json_encode($json);
	}
	
	public function company_edit($id=NULL){
		$this->settings['title'] = 'Edit Company';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Edit Company', base_url() . 'user/company_edit');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		if($id){
			$this->data['company'] = $this->user_model->get_c_type_by_id($id);
		}else{
			$this->data['company'] = $this->user_model->get_c_type_new();
		}
		$id == NULL || $this->data['company'] = $this->user_model->get_c_type_by_id($id);
		$rules = $this->user_model->c_type_rules;
		$this->form_validation->set_rules($rules);
		if($this->form_validation->run() == true){
			$data = $this->user_model->array_from_post(array('company_type'));
			if($id){
				$data['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data['date_created'] = date('Y-m-d h:i:s');
			}
			$id = $this->user_model->save_c_type($data, $id);
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('user/company_edit/').$id, 'refresh');
			}
		}

		$this->_render_template('company/edit', $this->data);
	}

	public function company_delete($id=NULL){
		$deleted = $this->user_model->delete_c_type($id);
		if($deleted){
			redirect(base_url('user/company_index'), 'refresh');
		}
	}
	
	/********************** Price List	******************/
	public function pricelist_index(){
		$this->settings['title'] = 'All Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('All Price List', base_url() . 'user/pricelist_index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['pricelist'] = $this->user_model->get_pricelist();

		$this->_render_template('pricelist/index', $this->data);		
	}	
	
	public function edit_pricelist($id = NULL, $divID = null){
		$this->settings['title'] = 'Edit Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Edit Price List', base_url() . 'user/edit_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['usProducts'] = $this->user_model->get_dailypricelist_pro();		
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist();
		$this->data['dailyEditPriceList'] = $this->user_model->get_dailyEditpricelist_US();		
		if($id){
			$this->data['pricelist'] = $this->user_model->get_pricelist_by_id($id);
		}else{
			$this->data['pricelist'] = $this->user_model->get_pricelist_new();
		}
		
		$id == NULL || $this->data['pricelist'] = $this->user_model->get_pricelist_by_id($id);
		
		$rules = $this->user_model->pricelist_rules;
		$this->form_validation->set_rules($rules);
		
		$allPricing = $this->user_model->get_dailypricelist_by_product();
		
		if($this->form_validation->run() == true){
			if(!empty($_POST['retail_prices'])){
			/* Set Retail Price in pricelist table */
			$price_descrLength = count($_POST['rp_product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$m = 0;
					while($m < $price_descrLength) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'rp_product_name' && $key != 'retail_prices' && $key != 'rp_submit'){
									$sub_array[$key] = array($value[$m]);
									/*Save DEFD Price in pricelist_ca table*/	
									if(array_key_exists('2', $value) && $_POST['rp_product_name'][$m] == 'DEFD'){
										$sub_array['defd_'.$key] = array($value[2]);									
									}									
							}	
						}
						$arr[$m] = (array($_POST['rp_product_name'][$m] => array($sub_array)));
						$m++;				
					}
					foreach($this->data['companyTypeResult'] as $companyTypeRows){
						
						$getCompanyType = strtolower($companyTypeRows->company_type);
						
						$arr2[$getCompanyType] = $_POST['defd_'.$getCompanyType];	
						
						
					}					
					$priceDescr_data = json_encode($arr);
					$defdPriceDescr_data = json_encode(array('DEFD'=>$arr2));					
				}else{
					$priceDescr_data = '';
					$defdPriceDescr_data = '';					
				}			
				/* Set Retail Price in pricelist_edit_us table */	
				$allPrice_descrLength = count($allPricing);
				if($allPrice_descrLength >0){
					$arrrp_Price = []; 
						foreach($allPricing as $key=>$value){ 
						$productArrayFlip = array_flip($_POST['rp_product_name']);
						if(array_key_exists($value->product, $productArrayFlip)){
							foreach($_POST as $keypost=>$postvalue){
								if($keypost != 'rp_product_name' && $keypost != 'retail_prices' && $keypost != 'rp_submit' && $keypost != 'rp_product_name'){
									if(array_key_exists($productArrayFlip[$value->product], $postvalue)){
									$jsonConvert = floatval($value->retail_price) - floatval($postvalue[$productArrayFlip[$value->product]]);
										if(strpos($keypost, 'defd_') === false && $keypost !== 'aoe_defd'){
											$sub_array_edit[$keypost] = array((STRING)$jsonConvert);
										}
									}
								}
							}
							$sub_array_edit['state'] = array($value->state);							
							$sub_array_edit['gas_station'] = array(trim($value->station_id));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array_edit)));	
						}
					$editedPriceDescr_data = json_encode($arrrp_Price);
				}else{
					$editedPriceDescr_data = '';
				}
			}
			
			/* Set Retail Price in pricelist_edit_us table */
			if(!empty($_POST['retail_prices_edit'])){			
			$retail_prices_edit_Length = count($_POST['retail_prices_edit_product']);//pre($retail_prices_edit_Length);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {/* pre(count($_POST)); */	
						foreach($_POST as $key=>$value){ 
							if($key != 'retail_prices_edit' && $key != 'retail_prices_edit_product' && $key != 'retail_prices_edit_id' && $key != 'rp_submit' && $key != 'retail_prices_edit_state' && $key != 'retail_prices_edit_gas_station'){
								$sub_array[$key] = array($value[$m]);
							}
							$sub_array['state'] = array($_POST['retail_prices_edit_state'][$m]);							
							$sub_array['gas_station'] = array(trim($_POST['retail_prices_edit_gas_station'][$m]));							
						}
						$arrayID = array_values(array_unique($_POST['retail_prices_edit_id']));

						$arr[$arrayID[$m]] = (array($_POST['retail_prices_edit_product'][$m] => array($sub_array)));
						$m++;				
					}
					$editedPriceDescr_data = json_encode($arr);
				}else{
					$editedPriceDescr_data = '';
				}
			}
			if(!empty($_POST['retail_cost_percent_prices'])){
			
			/* Retail Cost Pricelist table */	
			$retail_prices_edit_Length = count($_POST['rcpp_product_name']);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'retail_cost_percent_prices' && $key != 'rcpp_product_name'  && $key != 'rcpp_submit'){
								$sub_array[$key] = array($value[$m]);
									/*Save DEFD Price in pricelist_ca table*/	
									if(array_key_exists('2', $value) && $_POST['rcpp_product_name'][$m] == 'DEFD'){
										$sub_array['defd_'.$key] = array($value[2]);									
									}								
							}	
						}
						$arr[$m] = (array($_POST['rcpp_product_name'][$m] => array($sub_array)));
						$m++;
					}
					$editedPriceDescr_data = json_encode($arr);
				}else{
					$editedPriceDescr_data = '';
				}
					
				/* PriceList Edit table */	
				$allPrice_descrLength = count($allPricing);
				if($allPrice_descrLength >0){
					$arrrp_Price = []; 
						foreach($allPricing as $key=>$value){ 
							$productArrayFlip = array_flip($_POST['rcpp_product_name']);
							if(array_key_exists($value->product, $productArrayFlip)){
								foreach($_POST as $keypost=>$postvalue){
									if($keypost != 'rcpp_product_name' && $keypost != 'retail_cost_percent_prices' && $keypost != 'rcpp_submit' && $keypost != 'rcpp_product_name'){
										if(array_key_exists($productArrayFlip[$value->product], $postvalue)){
											$afterDecRetailPride = floatval($value->retail_price) - floatval($value->pride_price);
											
											$calcPercent = floatval($afterDecRetailPride) * str_replace('%','',$postvalue[$productArrayFlip[$value->product]]) / 100;
											$amtAfterDec =  floatval($value->retail_price) - $calcPercent;
											//$jsonConvert = floatval($value->retail_price) - floatval($amtAfterDec);
										//pre($amtAfterDec);
											$sub_array[$keypost] = array((string)$amtAfterDec);
										}
									}
								}$sub_array['state'] = array($value->state);							
								$sub_array['gas_station'] = array(trim($value->station_id));							
							}
							$arrrp_Price[$value->id] = (array($value->product => array($sub_array)));	
						}
					$editedRetailPriceDescr_data = json_encode($arrrp_Price);
				}else{
					$editedRetailPriceDescr_data = '';
				}
			} 

			/* Retail+Cost %age PriceList Edit table */
			if(!empty($_POST['retail_cost_percent_prices_edit'])){			
			$retail_prices_edit_Length = count($_POST['retailCost_prices_edit_product']);//pre($retail_prices_edit_Length);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {/* pre(count($_POST)); */	
						foreach($_POST as $key=>$value){ 
							if($key != 'retail_cost_percent_prices_edit' && $key != 'retailCost_prices_edit_product' && $key != 'retailCost_prices_edit_id' && $key != 'rcpp_submit' && $key != 'retailCost_prices_edit_gas_station' && $key != 'retailCost_prices_edit_state'){
								$sub_array[$key] = array($value[$m]);
							}
							$sub_array['state'] = array($_POST['retailCost_prices_edit_state'][$m]);							
							$sub_array['gas_station'] = array(trim($_POST['retailCost_prices_edit_gas_station'][$m]));							
						}
						$arrayID = array_values(array_unique($_POST['retailCost_prices_edit_id']));

						$arr[$arrayID[$m]] = (array($_POST['retailCost_prices_edit_product'][$m] => array($sub_array)));
						$m++;				
					}
					$editedCostpercentPriceDescr_data = json_encode($arr);
				}else{
					$editedCostpercentPriceDescr_data = '';
				}
			}			

			/* Add on EFS pricelist table */
			if(!empty($_POST['add_on_efs'])){	
			$price_descrLength = count($_POST['aoe_product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$m = 0;
					while($m < $price_descrLength) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'aoe_product_name' && $key != 'add_on_efs' && $key != 'aoe_submit'){
								$sub_array[$key] = array($value[$m]);
									/*Save DEFD Price in pricelist_ca table*/	
									if(array_key_exists('2', $value) && $_POST['aoe_product_name'][$m] == 'DEFD'){
										$sub_array['defd_'.$key] = array($value[2]);									
									}								
							}	
						}
						$arr[$m] = (array($_POST['aoe_product_name'][$m] => array($sub_array)));
						$m++;				
					}
					$priceDescr_data = json_encode($arr);
				}else{
					$priceDescr_data = '';
				}
				
			/* Save data in pricelis_edit_us table */	
			$allPrice_descrLength = count($allPricing);
				if($allPrice_descrLength >0){
					$arrrp_Price = []; 
						foreach($allPricing as $key=>$value){ 
						$productArrayFlip = array_flip($_POST['aoe_product_name']);
						if(array_key_exists($value->product, $productArrayFlip)){
							foreach($_POST as $keypost=>$postvalue){
								if($keypost != 'aoe_product_name' && $keypost != 'add_on_efs' && $keypost != 'aoe_submit' ){
									if(array_key_exists($productArrayFlip[$value->product], $postvalue)){
									/* $jsonConvert = floatval($value->retail_price) - floatval($postvalue[$productArrayFlip[$value->product]]); */
									$jsonConvert = floatval($value->pride_price) + floatval($postvalue[$productArrayFlip[$value->product]]);
									
									$sub_array[$keypost] = array((string)$jsonConvert);
									}
								}
							}$sub_array['state'] = array($value->state);							
							$sub_array['gas_station'] = array(trim($value->station_id));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array)));	
						}
					$saveAOEDescr_data = json_encode($arrrp_Price);
				}else{
					$saveAOEDescr_data = '';
				}				
			}
			
			/* Add On EFS PriceList Edit table */
			if(!empty($_POST['aoe_prices_edit'])){			
			$retail_prices_edit_Length = count($_POST['aoe_prices_edit_product']);//pre($retail_prices_edit_Length);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {/* pre(count($_POST)); */	
						foreach($_POST as $key=>$value){ 
							if($key != 'aoe_prices_edit' && $key != 'aoe_prices_edit_product' && $key != 'aoe_prices_edit_id' && $key != 'aeo_submit' && $key != 'aoe_prices_edit_gas_station' && $key != 'aoe_prices_edit_state'){
								$sub_array[$key] = array($value[$m]);
							}
							$sub_array['state'] = array($_POST['aoe_prices_edit_state'][$m]);							
							$sub_array['gas_station'] = array(trim($_POST['aoe_prices_edit_gas_station'][$m]));							
						}
						$arrayID = array_values(array_unique($_POST['aoe_prices_edit_id']));

						$arr[$arrayID[$m]] = (array($_POST['aoe_prices_edit_product'][$m] => array($sub_array)));
						$m++;				
					}
					$editedAEOPriceDescr_data = json_encode($arr);
				}else{
					$editedAEOPriceDescr_data = '';
				}
			}			
			/* Set Fix Price in pricelist table */
			if(!empty($_POST['fix_price'])){	
			$price_descrLength = count($_POST['fp_product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$m = 0;
					while($m < $price_descrLength) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'fp_product_name' && $key != 'fix_price' && $key != 'fp_submit'){
								$sub_array[$key] = array($value[$m]);
									/*Save DEFD Price in pricelist_ca table*/	
									if(array_key_exists('2', $value) && $_POST['fp_product_name'][$m] == 'DEFD'){
										$sub_array['defd_'.$key] = array($value[2]);									
									}								
							}	
						}
						$arr[$m] = (array($_POST['fp_product_name'][$m] => array($sub_array)));
						$m++;				
					}
					$priceDescr_data = json_encode($arr);
				}else{
					$priceDescr_data = '';
				}
				
			/* Save data in pricelis_edit_us table */	
			$allPrice_descrLength = count($allPricing);
				if($allPrice_descrLength >0){
					$arrrp_Price = []; 
						foreach($allPricing as $key=>$value){ 
						$productArrayFlip = array_flip($_POST['fp_product_name']);
						if(array_key_exists($value->product, $productArrayFlip)){
							foreach($_POST as $keypost=>$postvalue){
								if($keypost != 'fp_product_name' && $keypost != 'fix_price' && $keypost != 'fp_submit' ){
									if(array_key_exists($productArrayFlip[$value->product], $postvalue)){
									$jsonConvert = floatval($postvalue[$productArrayFlip[$value->product]]);
									
									$sub_array[$keypost] = array((string)$jsonConvert);
									}
								}
							}$sub_array['state'] = array($value->state);							
							$sub_array['gas_station'] = array(trim($value->station_id));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array)));	
						}
					$saveFPDescr_data = json_encode($arrrp_Price);
				}else{
					$saveFPDescr_data = '';
				}				
			}
		
			/* Add On EFS PriceList Edit table */
			if(!empty($_POST['fp_prices_edit'])){			
			$retail_prices_edit_Length = count($_POST['fp_prices_edit_product']);//pre($retail_prices_edit_Length);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {/* pre(count($_POST)); */	
						foreach($_POST as $key=>$value){ 
							if($key != 'fp_prices_edit' && $key != 'fp_prices_edit_product' && $key != 'fp_prices_edit_id' && $key != 'fp_submit' && $key != 'fp_prices_edit_station' && $key != 'fp_prices_edit_state'){
								$sub_array[$key] = array($value[$m]);
							}
							$sub_array['state'] = array($_POST['fp_prices_edit_state'][$m]);							
							$sub_array['gas_station'] = array(trim($_POST['fp_prices_edit_station'][$m]));								
						}
						$arrayID = array_values(array_unique($_POST['fp_prices_edit_id']));

						$arr[$arrayID[$m]] = (array($_POST['fp_prices_edit_product'][$m] => array($sub_array)));
						$m++;				
					}
					$editedFPPriceDescr_data = json_encode($arr);
				}else{
					$editedFPPriceDescr_data = '';
				}
			}			
			
			//JSON encoded data of description of products rows
			if(!empty($_POST['retail_prices'])){
				$data['retail_price'] = $priceDescr_data;
				$data1['defd_price'] = $defdPriceDescr_data;				
				$data1['retail_price'] = $editedPriceDescr_data;				
			}
			if(!empty($_POST['retail_cost_percent_prices'])){
				$data['retail_cost_percent'] = $editedPriceDescr_data;
				$data1['retail_cost_percent'] = $editedRetailPriceDescr_data;				
			}
			if(!empty($_POST['add_on_efs'])){
				$data['add_on_efs'] = $priceDescr_data;				
				$data1['add_on_efs'] = $saveAOEDescr_data;				
			}
			if(!empty($_POST['fix_price'])){
				$data['fix_price'] = $priceDescr_data;				
				$data1['fix_price'] = $saveFPDescr_data;				
			}

			if(!empty($_POST['retail_prices_edit'])){
				$data1['retail_price'] = $editedPriceDescr_data;
			}

			if(!empty($_POST['retail_cost_percent_prices_edit'])){
				$data1['retail_cost_percent'] = $editedCostpercentPriceDescr_data;
			}			
			
			if(!empty($_POST['aoe_prices_edit'])){
				$data1['add_on_efs'] = $editedAEOPriceDescr_data;
			}			
			
			if(!empty($_POST['fp_prices_edit'])){
				$data1['fix_price'] = $editedFPPriceDescr_data;
			}			

			if($id){
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data1['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data['date_created'] = date('Y-m-d h:i:s');
				$data1['date_modified'] = date('Y-m-d h:i:s');
				$data1['date_created'] = date('Y-m-d h:i:s');				
			}

			$this->user_model->save_pricelist_edit($data1, $id);
			$id = $this->user_model->save_pricelist($data, $id);
			
			#################### Email Pricing to Associated Companies ####################
			$getAllUsers = $this->user_model->get_users();

			$srno = 1; $csvdata = ''; 
			$productName = 'ULSD';
			$cnt = 0;
			/* $emailList = array('amandeep@lastingerp.com', 'pooja@lastingerp.com', 'jagdish@lastingerp.com', 'dharamveersingh@lastingerp.com', 'rohit@lastingerp.com', 'vipin@lastingerp.com'); */
			$emailList = array('jagdish@lastingerp.com');
			foreach($getAllUsers as $getAllUsersItems){
				if($getAllUsersItems->role == 'company'){
				$companyEmail = $getAllUsersItems->company_email;
				$companyTypeId = $getAllUsersItems->company_type;
				$getCompanyType = $this->db->where('id', $companyTypeId)->get('company_types')->row();
				if(is_object($getCompanyType) && !empty($getCompanyType)){
					$type = lcfirst($getCompanyType->company_type);
				}else{
					$type = 'bronze';
				}

				$cadPricingEFS = $getAllUsersItems->usa_pricing;

				$combinePricingType = array('usa_pricing' =>$cadPricingEFS);

				$shootEmail = 0;	
				if(in_array($companyEmail,$emailList) && $shootEmail === 1){
				
						
				foreach($combinePricingType as $key=>$combinePricingTypeItems){
					
					if(!empty($combinePricingTypeItems) && $combinePricingTypeItems != 'no'){
						$pricingCompany = null;

						if($key == 'usa_pricing' && !empty($_POST['retail_prices'])){
							$getUpdatedPricing = $this->user_model->get_efs_edited_pricing_us();
							$pricingCompany = 'rp-EFS-US';
						}else if($key == 'usa_pricing' && !empty($_POST['retail_cost_percent_prices'])){
							$getUpdatedPricing = $this->user_model->get_efs_edited_pricing_us();
							$pricingCompany = 'rcpp-EFS-US';
						}else if($key == 'usa_pricing' && !empty($_POST['add_on_efs'])){
							$getUpdatedPricing = $this->user_model->get_efs_edited_pricing_us();
							$pricingCompany = 'aoe-EFS-US';							
						}else if($key == 'usa_pricing' && !empty($_POST['fix_price'])){
							$getUpdatedPricing = $this->user_model->get_efs_edited_pricing_us();
							$pricingCompany = 'fp-EFS-US';								
						}
						$filename = 'todays-pricing-'.$pricingCompany.'.csv'; //format .xlsx , .csv
						$fd = fopen (FCPATH."assets/modules/invoices/".$filename, "w");

						if($getUpdatedPricing->$combinePricingTypeItems){
							
							$decodePrices = json_decode($getUpdatedPricing->$combinePricingTypeItems);

							foreach($decodePrices as $key =>$decodePricesRows){
								$getPrice = $decodePricesRows->$productName[0]->$type[0];
								$getGasStation = $decodePricesRows->$productName[0]->gas_station[0];
								$getState = $decodePricesRows->$productName[0]->state[0];
								$csvdata .= $srno.','.$getGasStation.','.$getState.',ULSD,'.$getPrice.','.date('Y-m-d') ."\n";
								$srno++;	
							}									
						}

						$csvheader = "Sr No.,Gas Station Id,State,Product,Price,Date\n";
						
						$fileContent = $csvheader.$csvdata;
						fputs($fd, $fileContent);
						fclose($fd);
						//ob_end_clean();

						$csvFilePath = FCPATH ."assets/modules/invoices/".$filename;
						$ca_subject = "Today's Pricing ".$pricingCompany;
						$ca_body = 'Check attachment';
					
						$this->load->library('email');
						$result = $this->email
										->from('info@pridediesel.com', 'From Pride Diesel')
										->to($companyEmail)
										->subject($ca_subject)
										->message($ca_body)
										->attach($csvFilePath)
										->send();
						$this->email->clear($csvFilePath);				
						//ob_get_clean();
					if($result) {
						//echo "Send";
						unlink($csvFilePath); //for delete generated pdf file. 
					}else{
						//echo $this->email->print_debugger();
					}						
					}
					$cnt++;
					}
				}				
			}
			}
			$activeTab = null;
			if (!empty($this->uri->segment(4))) {
				$activeTab = $this->uri->segment(4);
			}			
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('user/edit_pricelist/').$id."/$activeTab", 'refresh');
			}
		}
		

		$this->_render_template('pricelist/edit', $this->data);		
	}
	
	public function edit_pricelist_ca($id = NULL, $tab=null){
		$this->settings['title'] = 'Edit Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Edit Price List', base_url() . 'user/edit_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['usProducts'] = $this->user_model->get_dailypricelist_pro();		
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist_ca();		
		$this->data['dailyPriceListHusky'] = $this->user_model->get_husky_dailypricelist_ca();		
		$this->data['dailyEditPriceList'] = $this->user_model->get_dailyEditpricelist_CA();		
		if($id){
			$this->data['pricelist'] = $this->user_model->get_CApricelist_by_id($id);
		}else{
			$this->data['pricelist'] = $this->user_model->get_pricelist_new();
		}
		
		$id == NULL || $this->data['pricelist'] = $this->user_model->get_CApricelist_by_id($id);
		$id == NULL || $this->data['pricelistHusky'] = $this->user_model->get_husky_CApricelist_by_id($id);
		
		$rules = $this->user_model->pricelist_rules;
		$this->form_validation->set_rules($rules);
		
		$allPricing = $this->user_model->get_dailyCApricelist_by_product();
		
		if($this->form_validation->run() == true){
			/* Add on EFS data */
			if(!empty($_POST['add_on_efs'])){	
			$price_descrLength = count($_POST['aoe_product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$sub_array1=[];
					$defd_array =[];
					$m = 0;
					while($m < $price_descrLength) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'aoe_product_name' && $key != 'aoe_defd' && $key != 'defd_price' && $key != 'add_on_efs' && $key != 'aoe_submit'){
								$sub_array[$key] = array($value[$m]);								
							}
													
						}
						
						$arr[$m] = (array($_POST['aoe_product_name'][$m] => array($sub_array)));
						//$arr2[$m] = (array('DEFD' => array($defd_array)));
						$m++;				
					}
					foreach($this->data['companyTypeResult'] as $companyTypeRows){
						
						$getCompanyType = strtolower($companyTypeRows->company_type);
						
						$arr2[$getCompanyType] = $_POST['defd_'.$getCompanyType];	
						
						
					}
					$priceDescr_data = json_encode($arr);
					$defdPriceDescr_data = json_encode(array('DEFD'=>$arr2));
				}else{
					$priceDescr_data = '';
					$defdPriceDescr_data = '';
				}

			/* Save data in pricelist_edit_ca table */	
			$allPrice_descrLength = count($allPricing);
				if($allPrice_descrLength >0){
					$arrrp_Price = []; 
						foreach($allPricing as $key=>$value){ 
						$productArrayFlip = array_flip($_POST['aoe_product_name']);
						if(array_key_exists($value->product, $productArrayFlip)){
							foreach($_POST as $keypost=>$postvalue){
								if($keypost != 'aoe_product_name' && $keypost != 'add_on_efs' && $keypost != 'aoe_submit' ){
									if(array_key_exists($productArrayFlip[$value->product], $postvalue)){
									$jsonConvert = floatval($value->pride_price) - floatval($postvalue[$productArrayFlip[$value->product]]);
									//pre($keypost);
										if(strpos($keypost, 'defd_') === false && $keypost !== 'aoe_defd'){
											$sub_array_edited[$keypost] = array((STRING)$jsonConvert);
										}
									}
									/* if(array_key_exists('2', $postvalue)){
										$sub_array['defd_'.$keypost] = array($postvalue[2]);
									} */
								}
							}$sub_array_edited['state'] = array($value->state);							
							$sub_array_edited['gas_station'] = array(str_replace(' ', '-', trim($value->gas_station)));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array_edited)));	
						}
					$saveAOEDescr_data = json_encode($arrrp_Price);
				}else{
					$saveAOEDescr_data = '';
				}				
			}
		
			
			/* Add On EFS PriceList Edit table */
			if(!empty($_POST['aoe_prices_edit'])){			
			$retail_prices_edit_Length = count($_POST['aoe_prices_edit_product']);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {
						foreach($_POST as $key=>$value){ 
							if($key != 'aoe_prices_edit' && $key != 'aoe_prices_edit_product' && $key != 'aoe_prices_edit_id' && $key != 'aeo_submit' && $key != 'aoe_prices_edit_gas_station' && $key != 'aoe_prices_edit_state'){
								$sub_array[$key] = array($value[$m]);
							}
							$sub_array['state'] = array($_POST['aoe_prices_edit_state'][$m]);							
							$sub_array['gas_station'] = array(trim($_POST['aoe_prices_edit_gas_station'][$m]));							
						}
						$arrayID = array_values(array_unique($_POST['aoe_prices_edit_id']));

						$arr[$arrayID[$m]] = (array($_POST['aoe_prices_edit_product'][$m] => array($sub_array)));
						$m++;				
					}
					$editedAEOPriceDescr_data = json_encode($arr);
				}else{
					$editedAEOPriceDescr_data = '';
				}
			}			
			/* Fix Price data pricelist_ca table */
			if(!empty($_POST['fix_price'])){	
			$price_descrLength = count($_POST['fp_product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$m = 0;
					while($m < $price_descrLength) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'fp_product_name' && $key != 'fix_price' && $key != 'fp_submit'){
								$sub_array[$key] = array($value[$m]);
									/*Save DEFD Price in pricelist_ca table*/	
									if(array_key_exists('2', $value) && $_POST['fp_product_name'][$m] == 'DEFD'){
										$sub_array['defd_'.$key] = array($value[2]);									
									}								
							}	
						}
						$arr[$m] = (array($_POST['fp_product_name'][$m] => array($sub_array)));
						$m++;				
					}
					$priceDescr_data = json_encode($arr);
				}else{
					$priceDescr_data = '';
				}
				
			/* Save data in pricelis_edit_ca table */	
			$allPrice_descrLength = count($allPricing);
				if($allPrice_descrLength >0){
					$arrrp_Price = []; 
						foreach($allPricing as $key=>$value){ 
						$productArrayFlip = array_flip($_POST['fp_product_name']);
						if(array_key_exists($value->product, $productArrayFlip)){
							foreach($_POST as $keypost=>$postvalue){
								if($keypost != 'fp_product_name' && $keypost != 'fix_price' && $keypost != 'fp_submit' ){
									if(array_key_exists($productArrayFlip[$value->product], $postvalue)){
									$jsonConvert = floatval($postvalue[$productArrayFlip[$value->product]]);
									if(strpos($keypost, 'defd_') === false){
										$sub_array[$keypost] = array((STRING)$jsonConvert);
										}
									}
								}
							}$sub_array['state'] = array($value->state);							
							$sub_array['gas_station'] = array(trim($value->gas_station));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array)));	
						}
					$saveFPDescr_data = json_encode($arrrp_Price);
				}else{
					$saveFPDescr_data = '';
				}				
			}
			
			/* Fix Price pricelist_edit_ca table */
			if(!empty($_POST['fp_prices_edit'])){			
			$retail_prices_edit_Length = count($_POST['fp_prices_edit_product']);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {
						foreach($_POST as $key=>$value){ 
							if($key != 'fp_prices_edit' && $key != 'fp_prices_edit_product' && $key != 'fp_prices_edit_id' && $key != 'fp_submit' && $key != 'fp_prices_edit_station' && $key != 'fp_prices_edit_state'){
								$sub_array[$key] = array($value[$m]);
							}
							$sub_array['state'] = array($_POST['fp_prices_edit_state'][$m]);							
							$sub_array['gas_station'] = array(trim($_POST['fp_prices_edit_station'][$m]));								
						}
						$arrayID = array_values(array_unique($_POST['fp_prices_edit_id']));

						$arr[$arrayID[$m]] = (array($_POST['fp_prices_edit_product'][$m] => array($sub_array)));
						$m++;				
					}
					$editedFPPriceDescr_data = json_encode($arr);
				}else{
					$editedFPPriceDescr_data = '';
				}
			}			
			
			//JSON encoded data of description of products rows
			if(!empty($_POST['add_on_efs'])){
				$data['add_on_efs'] = $priceDescr_data;
				$data1['defd_price'] = $defdPriceDescr_data;	
				$data1['add_on_efs'] = $saveAOEDescr_data;				
			}
			if(!empty($_POST['fix_price'])){
				$data['fix_price'] = $priceDescr_data;				
				$data1['fix_price'] = $saveFPDescr_data;				
			}

			if(!empty($_POST['aoe_prices_edit'])){
				$data1['add_on_efs'] = $editedAEOPriceDescr_data;
			}			
			
			if(!empty($_POST['fp_prices_edit'])){
				$data1['fix_price'] = $editedFPPriceDescr_data;
			}			

			if($id){
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data1['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data['date_created'] = date('Y-m-d h:i:s');
				$data1['date_modified'] = date('Y-m-d h:i:s');
				$data1['date_created'] = date('Y-m-d h:i:s');				
			}

			$this->user_model->save_CApricelist_edit($data1, $id);
			
			$id = $this->user_model->save_ca_pricelist($data, $id);
			#################### Email Pricing to Associated Companies ####################
			$getAllUsers = $this->user_model->get_users();

			$srno = 1; $csvdata = '';
			//$setPricingType = 'add_on_husky'; 
			$productName = 'ULSD';
			$cnt = 0;
			/* $emailList = array('amandeep@lastingerp.com', 'pooja@lastingerp.com', 'jagdish@lastingerp.com', 'dharamveersingh@lastingerp.com', 'rohit@lastingerp.com', 'vipin@lastingerp.com'); */
			$emailList = array('jagdish@lastingerp.com');
			foreach($getAllUsers as $getAllUsersItems){
				if($getAllUsersItems->role == 'company'){
				$companyEmail = $getAllUsersItems->company_email;
				$companyTypeId = $getAllUsersItems->company_type_ca;
				$getCompanyType = $this->db->where('id', $companyTypeId)->get('company_types')->row();
				if(is_object($getCompanyType) && !empty($getCompanyType)){
					$type = lcfirst($getCompanyType->company_type);
				}else{
					$type = 'bronze';
				}
				//if(!empty($_POST['add_on_efs'])){
					$cadPricingEFS = $getAllUsersItems->cad_pricing;
				//}
				$combinePricingType = array('cad_pricing' =>$cadPricingEFS);
				
				//pre(count($combinePricingType));
				//for($cnt=0; $cnt<count($combinePricingType); $cnt++){
				$shootEmail = 0;	
				if(in_array($companyEmail,$emailList) && $shootEmail === 1){
				
						
				foreach($combinePricingType as $key=>$combinePricingTypeItems){
					
					if(!empty($combinePricingTypeItems) && $combinePricingTypeItems != 'no'){
						$pricingCompany = null;

						if($key == 'cad_pricing' && !empty($_POST['add_on_efs'])){
							//pre("AddOnEfs");
							$getUpdatedPricing = $this->user_model->get_efs_edited_pricing_ca();
							$pricingCompany = 'ao-EFS-CA';
						}else  if($key == 'cad_pricing' && !empty($_POST['fix_price'])){
							//pre("FixPrice");
							$getUpdatedPricing = $this->user_model->get_efs_edited_pricing_ca();
							$pricingCompany = 'fp-EFS-CA';
						}
						//$pricingCompany = 'HUSKY';
						$filename = 'todays-pricing-'.$pricingCompany.'.csv'; //format .xlsx , .csv
						$fd = fopen (FCPATH."assets/modules/invoices/".$filename, "w");
						//echo $companyEmail.": ".$combinePricingTypeItems[$cnt]."<br>";
						//echo $companyEmail."<br>";
						if($getUpdatedPricing->$combinePricingTypeItems){
							
							$decodePrices = json_decode($getUpdatedPricing->$combinePricingTypeItems);
							//pre($decodePrices);
							foreach($decodePrices as $key =>$decodePricesRows){
								$getPrice = $decodePricesRows->$productName[0]->$type[0];
								$getGasStation = $decodePricesRows->$productName[0]->gas_station[0];
								$getState = $decodePricesRows->$productName[0]->state[0];
								$csvdata .= $srno.','.$getGasStation.','.$getState.',ULSD,'.$getPrice.','.date('Y-m-d') ."\n";
								$srno++;	
							}									
						}
						//pre($csvdata);die;
						$csvheader = "Sr No.,Gas Station Id,State,Product,Price,Date\n";
						
						$fileContent = $csvheader.$csvdata;
						fputs($fd, $fileContent);
						fclose($fd);
						//ob_end_clean();

						$csvFilePath = FCPATH ."assets/modules/invoices/".$filename;
						$ca_subject = "Today's Pricing ".$pricingCompany;
						$ca_body = 'Check attachment';
						//pre($ca_subject.$ca_body);
						//die;						
						$this->load->library('email');
						$result = $this->email
										->from('info@pridediesel.com', 'From Pride Diesel')
										->to($companyEmail)
										->subject($ca_subject)
										->message($ca_body)
										->attach($csvFilePath)
										->send();
						//pre($this->email->print_debugger());
						$this->email->clear($csvFilePath);				
						//ob_get_clean();
					if($result) {
						//echo "Send";
						unlink($csvFilePath); //for delete generated pdf file. 
					}else{
						//echo $this->email->print_debugger();
					}						
					}
					$cnt++;
					}
				}				
			}
			}
			$activeTab = null;
			if (!empty($this->uri->segment(4))) {
				$activeTab = $this->uri->segment(4);
			}
			
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('user/edit_pricelist_ca/').$id."/".$activeTab, 'refresh');
			}
		}
		
		$this->_render_template('pricelist/edit-cad', $this->data);		
	}
	
	###Husky Pricelist Edit
	public function edit_pricelist_husky_ca($id = NULL, $tab=null){
		$this->settings['title'] = 'Edit Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Edit Price List', base_url() . 'user/edit_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		
		$id == NULL || $this->data['pricelist'] = $this->user_model->get_husky_CApricelist_by_id($id);
		
		$rules = $this->user_model->pricelist_rules;
		$this->form_validation->set_rules($rules);
		
		$allPricing = $this->user_model->get_husky_dailyCApricelist_by_product();
		
		if($this->form_validation->run() == true){
			/* Add on Husky data */
			if(!empty($_POST['add_on_husky'])){	
				$price_descrLength = count($_POST['aoh_product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$sub_array1=[];
					$defd_array =[];
					$m = 0;
					while($m < $price_descrLength) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'aoh_product_name' && $key != 'aoh_defd' && $key != 'defd_price' && $key != 'add_on_husky' && $key != 'aoh_submit'){
								//if(strpos($key, 'defd_') === false){
									$sub_array[$key] = array($value[$m]);
								//}								
							}							
						}
						$arr[$m] = (array($_POST['aoh_product_name'][$m] => array($sub_array)));
						$m++;				
					}
					foreach($this->data['companyTypeResult'] as $companyTypeRows){
						$getCompanyType = strtolower($companyTypeRows->company_type);
						$arr2[$getCompanyType] = $_POST['defd_'.$getCompanyType];	
					}
					$priceDescr_data = json_encode($arr);
					$defdPriceDescr_data = json_encode(array('DEFD'=>$arr2));
				}else{
					$priceDescr_data = '';
					$defdPriceDescr_data = '';
				}
				
				/* Save data in pricelist_ca_husky table */	
				$allPrice_descrLength = count($allPricing);
				if($allPrice_descrLength >0){
					$arrrp_Price = []; 
						foreach($allPricing as $key=>$value){ 
						$productArrayFlip = array_flip($_POST['aoh_product_name']);
						if(array_key_exists($value->product, $productArrayFlip)){
							foreach($_POST as $keypost=>$postvalue){
								if($keypost != 'aoh_product_name' && $keypost != 'add_on_husky' && $keypost != 'aoh_submit' ){
									if(array_key_exists($productArrayFlip[$value->product], $postvalue)){
										$doTotal = floatval($value->pride_price) + floatval($postvalue[$productArrayFlip[$value->product]]);
									$jsonConvert = $doTotal;
									//pre($keypost);
									if(strpos($keypost, 'defd_') === false && $keypost !== 'aoh_defd'){
										//$sub_array[$keypost] = array($jsonConvert, 4);
										$sub_array[$keypost] = array((STRING)$jsonConvert);
									}
									}
								}
							}$sub_array['state'] = array($value->state);							
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($value->gas_station)));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array)));	
						}
					$saveAOEDescr_data = json_encode($arrrp_Price);
				}else{
					$saveAOEDescr_data = '';
				}				
			}
			//pre($saveAOEDescr_data);
			//die;
			/* pricelist_edit_ca_husky table */
			if(!empty($_POST['aoh_prices_edit'])){			
			$retail_prices_edit_Length = count($_POST['aoh_prices_edit_product']);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {
						foreach($_POST as $key=>$value){ 
							if($key != 'aoh_prices_edit' && $key != 'aoh_prices_edit_product' && $key != 'aoh_prices_edit_id' && $key != 'aeh_submit' && $key != 'aoh_prices_edit_gas_station' && $key != 'aoh_prices_edit_state'){
								//pre($value[$m]);
								//if(strpos($key, 'defd_') == false){
									$sub_array[$key] = array($value[$m]);
								//}
							}
							$sub_array['state'] = array($_POST['aoh_prices_edit_state'][$m]);							
							$sub_array['gas_station'] = array(trim($_POST['aoh_prices_edit_gas_station'][$m]));
						}
						$arrayID = array_values(array_unique($_POST['aoh_prices_edit_id']));

						$arr[$arrayID[$m]] = (array($_POST['aoh_prices_edit_product'][$m] => array($sub_array)));
						$m++;				
					}
					$editedAEOPriceDescr_data = json_encode($arr);
				}else{
					$editedAEOPriceDescr_data = '';
				}
			}

			//JSON encoded data of description of products rows
			if(!empty($_POST['add_on_husky'])){
				$data['add_on_husky'] = $priceDescr_data;
				$data1['defd_price'] = $defdPriceDescr_data;	
				$data1['add_on_husky'] = $saveAOEDescr_data;				
			}

			if(!empty($_POST['aoh_prices_edit'])){
				$data1['add_on_husky'] = $editedAEOPriceDescr_data;
			}						

			if($id){
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data1['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data['date_created'] = date('Y-m-d h:i:s');
				$data1['date_modified'] = date('Y-m-d h:i:s');
				$data1['date_created'] = date('Y-m-d h:i:s');				
			}
			###### Save Data in pricelist_edit_ca_husky table ########
			$this->user_model->save_CApricelist_edit_husky($data1, $id);
			
			###### Save Data in pricelist_ca_husky table ########
			$id = $this->user_model->save_ca_pricelist_husky($data, $id);
			
			#################### Email Pricing to Associated Companies ####################
			$getAllUsers = $this->user_model->get_users();
			
			
			//ob_start();
			//$filename = 'todays-pricing-'.time().'.csv'; //format .xlsx , .csv

			//ob_start();

			$srno = 1; $csvdata = '';
			//$setPricingType = 'add_on_husky'; 
			$productName = 'ULSD';
			$cnt = 0;
			/* $emailList = array('amandeep@lastingerp.com', 'pooja@lastingerp.com', 'jagdish@lastingerp.com', 'dharamveersingh@lastingerp.com', 'rohit@lastingerp.com', 'vipin@lastingerp.com'); */
			$emailList = array('jagdish@lastingerp.com');
			foreach($getAllUsers as $getAllUsersItems){
				if($getAllUsersItems->role == 'company'){
				$companyEmail = $getAllUsersItems->company_email;
				$companyTypeId = $getAllUsersItems->company_type_ca_husky;
				$getCompanyType = $this->db->where('id', $companyTypeId)->get('company_types')->row();
				if(is_object($getCompanyType) && !empty($getCompanyType)){
					$type = $getCompanyType->company_type;
				}else{
					$type = 'bronze';
				}
				$cadPricingHusky = $getAllUsersItems->cad_pricing_husky;
				$combinePricingType = array('cad_pricing_husky' =>$cadPricingHusky);
				//pre($companyEmail);
				//pre(count($combinePricingType));
				//for($cnt=0; $cnt<count($combinePricingType); $cnt++){
				$shootEmail = 0;	
				if(in_array($companyEmail,$emailList) && $shootEmail === 1){
				
						
				foreach($combinePricingType as $key=>$combinePricingTypeItems){
					
					if(!empty($combinePricingTypeItems) && $combinePricingTypeItems != 'no'){
						$pricingCompany = null;

						$getUpdatedPricing = $this->user_model->get_husky_edited_pricing_ca();
						$pricingCompany = 'HUSKY';
						$filename = 'todays-pricing-'.$pricingCompany.'.csv'; //format .xlsx , .csv
						$fd = fopen (FCPATH."assets/modules/invoices/".$filename, "w");
						//echo $companyEmail.": ".$combinePricingTypeItems[$cnt]."<br>";
						//echo $companyEmail."<br>";
						if($getUpdatedPricing->$combinePricingTypeItems){
							
							$decodePrices = json_decode($getUpdatedPricing->$combinePricingTypeItems);
							//pre($decodePrices);
							foreach($decodePrices as $key =>$decodePricesRows){
								$getPrice = $decodePricesRows->$productName[0]->$type[0];
								$getGasStation = $decodePricesRows->$productName[0]->gas_station[0];
								$getState = $decodePricesRows->$productName[0]->state[0];
								$csvdata .= $srno.','.$getGasStation.','.$getState.',ULSD,'.$getPrice.','.date('Y-m-d') ."\n";
								$srno++;	
							}									
						}
					//pre($csvdata);die;
						$csvheader = "Sr No.,Gas Station Id,State,Product,Price,Date\n";
						
						$fileContent = $csvheader.$csvdata;
						fputs($fd, $fileContent);
						fclose($fd);
						//ob_end_clean();

						$csvFilePath = FCPATH ."assets/modules/invoices/".$filename;
						$ca_subject = "Today's Pricing ".$pricingCompany;
						$ca_body = 'Check attachment';
						//pre($ca_subject.$ca_body);
						//die;						
						$this->load->library('email');
						$result = $this->email
										->from('info@pridediesel.com', 'From Pride Diesel')
										->to($companyEmail)
										->subject($ca_subject)
										->message($ca_body)
										->attach($csvFilePath)
										->send();
						//pre($this->email->print_debugger());
						$this->email->clear($csvFilePath);				
						//ob_get_clean();
					if($result) {
						//echo "Send";
						unlink($csvFilePath); //for delete generated pdf file. 
					}else{
						//echo $this->email->print_debugger();
					}						
					}
					$cnt++;
					}
				}				
			}
			}	
			$activeTab = null;
			if (!empty($this->uri->segment(4))) {
				$activeTab = $this->uri->segment(4);
			}
			
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('user/edit_pricelist_ca/').$id."/".$activeTab, 'refresh');
			}
		}
		

		$this->_render_template('pricelist/edit-cad', $this->data);		
	}	
		
	
	public function daily_pricing($id = NULL){
		$this->settings['title'] = 'Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Price List', base_url() . 'user/daily_pricing');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist();
		
		$this->_render_template('pricelist/daily_pricing', $this->data);	
	}		
	
	/* View Pricelist by Company */
	public function company_pricelist(){
		$this->settings['title'] = 'Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Price List', base_url() . 'user/import_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['products'] = $this->user_model->get_prodcuts();
		$this->data['pricelist'] = $this->user_model->get_pricelist_by_id(1);

		$this->_render_template('pricelist/view', $this->data);			
	}
	
	/* View Pricelist by Company */
	public function company_pricelist_view(){
		$this->settings['title'] = 'Price List USA';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Price List USA', base_url() . 'user/import_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['products'] = $this->user_model->get_prodcuts();
		$this->data['pricelist'] = $this->user_model->get_com_pricelist_by_id(1);
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist();

		$this->_render_template('company/view', $this->data);			
	}

	/* View Pricelist by Company */
	public function company_CApricelist_view(){
		$this->settings['title'] = 'Price List Canada';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Price List Canada', base_url() . 'user/import_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['products'] = $this->user_model->get_prodcuts();
		$this->data['pricelist'] = $this->user_model->get_CAcom_pricelist_by_id(1);
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist_ca();

		$this->_render_template('company/view-ca', $this->data);			
	}

	/* View Husky Pricelist by Company */
	public function company_husky_pricelist_view(){
		$this->settings['title'] = 'Price List Canada Husky';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Price List Canada Husky', base_url() . 'user/company_husky_pricelist_view');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['products'] = $this->user_model->get_prodcuts();
		$this->data['pricelist'] = $this->user_model->get_CAcom_pricelist_by_id(1);
		$this->data['dailyPriceList'] = $this->user_model->get_husky_dailypricelist_ca();

		$this->_render_template('company/view-husky-ca', $this->data);			
	}	
	
	public function import_pricelist(){
		ini_set('memory_limit', -1);
		$this->settings['title'] = 'Import Price List USA';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Import Price List USA', base_url() . 'user/import_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		if(isset($_POST['import'])){	
		   if(!empty($_FILES['import_pricelist']['name'])!=''){
			    
                $path = 'assets/modules/user/pricelist/excel_for_pricelist/';
                require_once APPPATH . "/third_party/PHPExcel.php";
                $config['upload_path'] = $path;
                //$config['allowed_types'] = "xlsx|csv|xls|ods|xl|word|docx";
                $config['allowed_types'] = "xlsx|xls";
				//$config['allowed_types']        = '*';
				//$config['detect_mime']          = false;
				$config['max_size'] = '100000'; 
                $config['remove_spaces'] = true;
                $this->load->library('upload', $config);
				
                $this->upload->initialize($config); 
					
                if (!$this->upload->do_upload('import_pricelist')) {
                    $error = array('error' => $this->upload->display_errors());
                } else {
                    $data = array('upload_data' => $this->upload->data());
                }
				 
                if(empty($error)){
                  if (!empty($data['upload_data']['file_name'])) {
                    $import_xls_file = $data['upload_data']['file_name'];
                } else {
                    $import_xls_file = 0;
                }
                $inputFileName = $path . $import_xls_file;
					
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
					
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
					$this->db->truncate('retail_pricing');
					for($i=8;$i<=$arrayCount;$i++)
					{                   
						if(!empty($allDataInSheet[$i]["C"])){
							$insertdata[$i]['gas_station'] = $allDataInSheet[$i]["C"];
							$insertdata[$i]['station_id'] = $allDataInSheet[$i]["E"];
							$insertdata[$i]['city'] = $allDataInSheet[$i]["F"];
							$insertdata[$i]['state'] = $allDataInSheet[$i]["D"];
							
							$words = preg_split("/[\s^0-9,_#%-]+/", $allDataInSheet[$i]['H']);
							$acronym = "";

							//print_r($words);
							foreach ($words as $w) {
								if(!empty($w[0]))
									$acronym .= $w[0];
							}
							/* if(){
								$acronym = 'ULSR';
							} */
							if($acronym == 'DTLU' || $acronym == 'DULS' || $acronym == 'DCULS' || $acronym == 'DBUL' || $acronym == 'DBULC' || $acronym == 'TLUB'){
								$acronym = 'ULSD';
							}else{
								$acronym = 'ULSD';
							}							
							/* if(){
								$acronym = 'DEFD';
							} */							
							$insertdata[$i]['product'] = $acronym;
							$insertdata[$i]['retail_price'] = $allDataInSheet[$i]['U'];
							$insertdata[$i]['pride_price'] = $allDataInSheet[$i]['X'];
							$insertdata[$i]['date'] = date('Y-m-d', strtotime($allDataInSheet[$i]['J']));	
							$insertdata[$i]['date_created'] = date('Y-m-d h:i:s');	
							$insertdata[$i]['date_modified'] = date('Y-m-d h:i:s');
						}					
					}				
                    /* $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(true, true, true, true, true);
                    $flag = true;
                    $i=7;
                    foreach ($allDataInSheet as $value) {
                      if($flag){
                        $flag =false;
                        continue;
                      }

                      $insertdata[$i]['gas_station'] = $value['C'];
                      $insertdata[$i]['city'] = $value['F'];
                      $insertdata[$i]['state'] = $value['D'];
                      $insertdata[$i]['product'] = $value['H'];
                      $insertdata[$i]['retail_price'] = $value['U'];
                      $insertdata[$i]['pride_price'] = $value['X'];
                      $insertdata[$i]['date'] = $value['J'];
                     
					$i++;
                    } */               
                    $result = $this->user_model->importTApriceList($insertdata);   
                    if($result){
                      //echo "Imported successfully";
						$this->session->set_flashdata('success', 'Pricelist Imported Successfully');
                    }else{
                      echo "ERROR !";
                    }             
      
              } catch (Exception $e) {
                   die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                            . '": ' .$e->getMessage());
                }
              }else{
                  //echo $error['error'];
				  $this->session->set_flashdata('error', $error['error']);
                }
                 
         
        	
       		 redirect(base_url().'user/import_pricelist', 'refresh');

		}else{

			echo"<script>alert('Please Select the File to Upload')</script>";		
		}
	}
		$this->_render_template('pricelist/import-price-list', $this->data);		
	}

	public function import_pricelist_ca(){
		ini_set('memory_limit', -1);
		$this->settings['title'] = 'Import Price List Canada';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Import Price List Canada', base_url() . 'user/import_pricelist_ca');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		if(isset($_POST['import'])){	
		   if(!empty($_FILES['import_pricelist']['name'])!=''){
			    
                $path = 'assets/modules/user/pricelist/excel_for_pricelist/';
                require_once APPPATH . "/third_party/PHPExcel.php";
                $config['upload_path'] = $path;
                //$config['allowed_types'] = "xlsx|csv|xls|ods|xl|word|docx";
                $config['allowed_types'] = "xlsx|xls";
				//$config['allowed_types']        = '*';
				//$config['detect_mime']          = false;
				$config['max_size'] = '100000'; 
                $config['remove_spaces'] = true;
                $this->load->library('upload', $config);
				
                $this->upload->initialize($config); 
					
                if (!$this->upload->do_upload('import_pricelist')) {
                    $error = array('error' => $this->upload->display_errors());
                } else {
                    $data = array('upload_data' => $this->upload->data());
                }
				 
                if(empty($error)){
                  if (!empty($data['upload_data']['file_name'])) {
                    $import_xls_file = $data['upload_data']['file_name'];
                } else {
                    $import_xls_file = 0;
                }
                $inputFileName = $path . $import_xls_file;
				$insertdata = array();	
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
					
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
					$this->db->truncate('retail_pricing_ca');
					
					for($i=2;$i<=$arrayCount;$i++)
					{                   
						if(!empty($allDataInSheet[$i]["A"])){
							$words = preg_split("/[\s^0-9,_#%-]+/", $allDataInSheet[$i]['D']);
							$acronym = "";

							foreach ($words as $w) {
								if(!empty($w[0]))
									$acronym .= $w[0];
							}
							if($acronym == 'DL'){							
								$insertdata[$i]['gas_station'] = $allDataInSheet[$i]["A"];
								$insertdata[$i]['city'] = $allDataInSheet[$i]["B"];
								$insertdata[$i]['state'] = $allDataInSheet[$i]["C"];
								$acronym = 'ULSD';
							
								$insertdata[$i]['product'] = $acronym;
								//$insertdata[$i]['retail_price'] = $allDataInSheet[$i]['U'];trim(
								$insertdata[$i]['pride_price'] = $allDataInSheet[$i]['M'] / 100;
								$makeDateFormat = str_replace('/', '-', $allDataInSheet[$i]['O']);
								$insertdata[$i]['date'] = date('Y-m-d', strtotime($makeDateFormat));	
								$insertdata[$i]['date_created'] = date('Y-m-d h:i:s');	
								$insertdata[$i]['date_modified'] = date('Y-m-d h:i:s');
							}
						}					
					}
					
					if(count($insertdata) < 1){
						$this->session->set_flashdata('error', 'Product Name "Diesel LS" not found');
						redirect(base_url().'user/import_pricelist_ca', 'refresh');
						exit;			
					}	
                    $result = $this->user_model->importCApriceList($insertdata);   
                    if($result){
                      //echo "Imported successfully";
						$this->session->set_flashdata('success', 'Pricelist Imported Successfully');
                    }else{
                      echo "ERROR !";
                    }             
      
              } catch (Exception $e) {
                   die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                            . '": ' .$e->getMessage());
                }
              }else{
                  //echo $error['error'];
				  $this->session->set_flashdata('error', $error['error']);
                }
                 
         
        	
       		 redirect(base_url().'user/import_pricelist_ca', 'refresh');

		}else{

			echo"<script>alert('Please Select the File to Upload')</script>";		
		}
	}
		$this->_render_template('pricelist/import-price-list-ca', $this->data);		
	}	
	
	public function import_pricelist_ca_husky(){
		ini_set('memory_limit', -1);
		$this->settings['title'] = 'Import Price List Canada';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Import Price List Canada', base_url() . 'user/import_pricelist_ca');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		if(isset($_POST['import'])){	
		   if(!empty($_FILES['import_pricelist']['name'])!=''){
			    
                $path = 'assets/modules/user/pricelist/excel_for_pricelist/';
                require_once APPPATH . "/third_party/PHPExcel.php";
                $config['upload_path'] = $path;
                //$config['allowed_types'] = "xlsx|csv|xls|ods|xl|word|docx";
                $config['allowed_types'] = "xlsx|xls|csv";
				//$config['allowed_types']        = '*';
				//$config['detect_mime']          = false;
				$config['max_size'] = '100000'; 
                $config['remove_spaces'] = true;
                $this->load->library('upload', $config);
				
                $this->upload->initialize($config); 
					
                if (!$this->upload->do_upload('import_pricelist')) {
                    $error = array('error' => $this->upload->display_errors());
                } else {
                    $data = array('upload_data' => $this->upload->data());
                }
				 
                if(empty($error)){
                  if (!empty($data['upload_data']['file_name'])) {
                    $import_xls_file = $data['upload_data']['file_name'];
                } else {
                    $import_xls_file = 0;
                }
                $inputFileName = $path . $import_xls_file;
				$insertdata = array();	
                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
					
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
					$this->db->truncate('retail_pricing_husky_ca');
					
					for($i=2;$i<=$arrayCount;$i++)
					{                   
						if(!empty($allDataInSheet[$i]["A"])){
							$words = preg_split("/[\s^0-9,_#%-(]+/", $allDataInSheet[$i]['D']);
							$acronym = "";

							foreach ($words as $w) {
								if(!empty($w[0]))
									$acronym .= $w[0];
							}//pre($acronym);
							if($acronym == 'DUL'){							
								$insertdata[$i]['gas_station'] = $allDataInSheet[$i]["A"];
								$insertdata[$i]['city'] = $allDataInSheet[$i]["B"];
								$insertdata[$i]['state'] = $allDataInSheet[$i]["C"];
								$acronym = 'ULSD';
							
								$insertdata[$i]['product'] = $acronym;
								//$insertdata[$i]['retail_price'] = $allDataInSheet[$i]['U'];trim(
								$insertdata[$i]['pride_price'] = $allDataInSheet[$i]['K'];
								$makeDateFormat = str_replace('/', '-', $allDataInSheet[$i]['L']);
								$insertdata[$i]['date'] = date('Y-m-d', strtotime($makeDateFormat));	
								$insertdata[$i]['date_created'] = date('Y-m-d h:i:s');	
								$insertdata[$i]['date_modified'] = date('Y-m-d h:i:s');								
								
								//$result = $this->user_model->importHuskyCApriceList($insertdata);
							}
						}					
					}
					
					if(count($insertdata) < 1){
						$this->session->set_flashdata('error', 'Product Name "DIESEL 2 ULSD (LED)" not found');
						redirect(base_url().'user/import_pricelist_ca_husky', 'refresh');
						exit;			
					}
					
                    $result = $this->user_model->importHuskyCApriceList($insertdata);   
                    if($result){
                      //echo "Imported successfully";
						$this->session->set_flashdata('success', 'Pricelist Imported Successfully');
                    }else{
                      echo "ERROR !";
                    }             
      
              } catch (Exception $e) {
                   die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                            . '": ' .$e->getMessage());
                }
              }else{
                  //echo $error['error'];
				  $this->session->set_flashdata('error', $error['error']);
                }
                 
         
        	
       		 redirect(base_url().'user/import_pricelist_ca_husky', 'refresh');

		}else{

			echo"<script>alert('Please Select the File to Upload')</script>";		
		}
	}
		$this->_render_template('pricelist/import-price-list-ca-husky', $this->data);		
	}	
	
	public function import_pricelist_old(){
		$this->settings['title'] = 'Import Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Import Price List', base_url() . 'user/import_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		if(isset($_POST['import'])){	
		   if(!empty($_FILES['import_pricelist']['name'])!=''){
			    
                $path = 'assets/modules/user/pricelist/excel_for_pricelist/';
                require_once APPPATH . "/third_party/PHPExcel.php";
                $config['upload_path'] = $path;
                $config['allowed_types'] = "csv|xls|xlsx";
                $config['remove_spaces'] = true;
                $this->load->library('upload', $config);
				
                $this->upload->initialize($config); 
					
                if (!$this->upload->do_upload('import_pricelist')) {
                    $error = array('error' => $this->upload->display_errors());
                } else {
                    $data = array('upload_data' => $this->upload->data());
                }
				 
                if(empty($error)){
                  if (!empty($data['upload_data']['file_name'])) {
                    $import_xls_file = $data['upload_data']['file_name'];
                } else {
                    $import_xls_file = 0;
                }
                $inputFileName = $path . $import_xls_file;

                try {
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(true, true, true, true, true);
                    $flag = true;
                    //$i=0;
                    //foreach ($allDataInSheet as $value) {
                      /* if($flag){
                        $flag =false;
                        continue;
                      } */

			$productPrice_Length = count($allDataInSheet);
				if($productPrice_Length >0){
					$arr = [];
					$i = 0;

					foreach($allDataInSheet as $allDataInSheets){
						if($flag){
						$flag =false;
						continue;
						}
						$values = array_values($allDataInSheets);
						$jsonArrayObject = (array($values[0] => array('efs_amt'=>$values[1], 'retail_amt'=>$values[2])));
						$arr[$i] = $jsonArrayObject;
						$i++;
					}
					$products_pricelist_array = json_encode($arr);
				}else{
					$products_pricelist_array = '';
				}			
			//JSON encoded data of description of products rows
			$data['efs_price'] = $products_pricelist_array;					  

                      $insertdata['efs_price'] = $data['efs_price'];
             
                    $result = $this->user_model->importPricelist($insertdata);   
                    if($result){
                      //echo "Imported successfully";

                    }else{
                      echo "ERROR !";
                    }             
      
              } catch (Exception $e) {
                   die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                            . '": ' .$e->getMessage());
                }
              }else{
                  //echo $error['error'];
				  $this->session->set_flashdata('error', $error['error']);
                }
                 
         
        	$this->session->set_flashdata('success', 'Pricelist Imported Successfully');
       		 redirect(base_url().'user/import_pricelist', 'refresh');

		}else{

			echo"<script>alert('Please Select the File to Upload')</script>";		
		}
	}
		$this->_render_template('pricelist/import-price-list', $this->data);		
	}
	
	public function export_blank_pricelist_excel(){
		//File Name
		$fileName = 'pricelist-blank-excel-'.date('Y-m-d'); //format should be .xlsx , .csv

		$this->load->library('excel');
		
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Product Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'EFS Price');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Retail Price');


       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
			        header('Content-Type: application/vnd.ms-excel');
			       //header("Content-Disposition: attachment;filename=".$fileName.".xlsx");
			       header("Content-Disposition: attachment;filename=".$fileName.".xlsx");
			         ob_end_clean();
			        $object_writer->save('php://output');		
	}
	
	public function money_code_issued(){
		//$this->load->model('user/user_model');
		$this->settings['title'] = 'Money Code Issued';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Issued', base_url() . 'user/money_code_issued');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}
		$this->load->library('pagination');
		$this->data['getCards'] = $this->user_model->get_users($where);
        // pagination
        $config['base_url'] = site_url('user/money_code_issued');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['getCards']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['getCards'] = $this->user_model->get_pagination($config['per_page'], $page, $where);		

		$this->_render_template('money-code/index', $this->data);		
	}
	
	public function money_code_issue($id, $mid=null){
		$this->load->library("NuSoap_lib");
		$this->soapclient = new nusoap_client(site_url('/EFS_WS/index/wsdl'), true);		
		if($mid){
			$this->settings['title'] = 'Issue Money Code';
			$this->breadcrumb->mainctrl("user");
			$this->breadcrumb->add('Issue Money Code', base_url() . 'user/money_code_issue');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
			$this->data['getCompanyName'] = $this->user_model->get_by_id($id);		
			$this->data['moneyCode'] = $this->user_model->get_moneyCode_by_id($mid);		
		}else{
			$this->settings['title'] = 'Issue Money Code';
			$this->breadcrumb->mainctrl("user");
			$this->breadcrumb->add('Issue Money Code', base_url() . 'user/money_code_issue');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
			$this->data['getCompanyName'] = $this->user_model->get_by_id($id);		
			$this->data['moneyCode'] = $this->user_model->get_moneyCode_new();			
		}
		
		$mid == NULL || $this->data['moneyCode'] = $this->user_model->get_moneyCode_by_id($mid);

		$rules = $this->user_model->moneyCodeIssueRules;
	
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
            $data = $this->user_model->array_from_post(array('companyId', 'contractId', 'masterContractId', 'amount', 'feeType', 'issuedTo', 'notes', 'currency'));
			$contractId = $this->input->post('contractId');
			$masterContractId = $this->input->post('masterContractId');
			$amount = $this->input->post('amount');
			$feeType = $this->input->post('feeType');
			$issuedTo = $this->input->post('issuedTo');
			$notes = $this->input->post('notes');
			$currency = $this->input->post('currency');
			$clientToken = $this->soapclient->call('login', array('user'=>'HSINGH3', 'password' => 'Harry0044'));
			$xmlData =	"<clientId>$clientToken</clientId>
						 <contractId>".$contractId."</contractId>
						 <masterContractId>".$masterContractId."</masterContractId>
						 <amount>".$amount."</amount>
						 <feeType>".$feeType."</feeType>
						 <issuedTo>".$issuedTo."</issuedTo>
						 <notes>".$notes."</notes>
						 <currency>".$currency."</currency>
			";
			$callStatus = $this->soapclient->call('issueMoneyCode', $xmlData);

			$data['moneyCode'] = $callStatus['code'];
			$data['moneyCodeId'] = $callStatus['id'];
			if($mid == NULL){
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['date_modified'] = date('Y-m-d H:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d H:i:s');
			}

			$mid = $this->user_model->issue_money_code($data, $mid);
			
			if($mid){
				$this->session->set_flashdata('success', 'Changes Saved.');
				redirect(base_url('user/money_code_issue/'.$id.'/'.$mid), 'refresh');				
			}
			
		}		

		$this->_render_template('money-code/edit', $this->data);
	}
	
	public function issueCode(){
		$userSessDetails = $this->session->userdata('userdata');
		$this->load->library("NuSoap_lib");
		$this->soapclient = new nusoap_client(site_url('/EFS_WS/index/wsdl'), true);		

		$this->settings['title'] = 'Issue Money Code';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Issue Money Code', base_url() . 'user/money_code_issue');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
	
		$this->data['getCompanyName'] = $this->user_model->get_by_id($userSessDetails->id);		
		//$this->data['moneyCode'] = $this->user_model->get_moneyCode_by_id();
		

		$rules = $this->user_model->moneyCodeIssueRules;
	
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
            $data = $this->user_model->array_from_post(array('companyId', 'contractId', 'masterContractId', 'driver_name', 'unit_number', 'amount', 'feeType', 'issuedTo', 'notes', 'currency'));
			$contractId = $this->input->post('contractId');
			$masterContractId = $this->input->post('masterContractId');
			$amount = $this->input->post('amount');
			$feeType = $this->input->post('feeType');
			$issuedTo = $this->input->post('issuedTo');
			$notes = $this->input->post('notes');
			$currency = $this->input->post('currency');
			$clientToken = $this->soapclient->call('login', array('user'=>'HSINGH3', 'password' => 'Harry0044'));
			$xmlData =	"<clientId>$clientToken</clientId>
						 <contractId>".$contractId."</contractId>
						 <masterContractId>".$masterContractId."</masterContractId>
						 <amount>".$amount."</amount>
						 <feeType>".$feeType."</feeType>
						 <issuedTo>".$issuedTo."</issuedTo>
						 <notes>".$notes."</notes>
						 <currency>".$currency."</currency>
			";
			$callStatus = $this->soapclient->call('issueMoneyCode', $xmlData);

			$data['moneyCode'] = $callStatus['code'];
			$data['moneyCodeId'] = $callStatus['id'];

			$data['date_created'] = date('Y-m-d H:i:s');
			$data['date_modified'] = date('Y-m-d H:i:s');
			
			
			$id = $this->user_model->issueMoneyCode($data, $userSessDetails->id);
			
			if($id){
				$this->session->set_flashdata('success', 'Money Code Generated.');
				redirect(base_url('user/issuedCodes/'), 'refresh');				
			}
			
		}		

		$this->_render_template('money-code/company-edit', $this->data);
	}

	public function issuedCodes(){
		$userSessDetails = $this->session->userdata('userdata');
		$this->settings['title'] = 'Money Code Issued';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Issued', base_url() . 'user/issuedCodes');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}
		$this->load->library('pagination');
		$this->data['getCards'] = $this->user_model->getMoneyCodes($userSessDetails->id,$where);
        // pagination
        $config['base_url'] = site_url('user/issuedCodes');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['getCards']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['getCards'] = $this->user_model->getMoneyCode_pagination($userSessDetails->id,$config['per_page'], $page, $where);		

		$this->_render_template('money-code/company-money-code-view', $this->data);		
	}	

	public function money_codes(){
		//$this->load->model('user/user_model');
		$this->settings['title'] = 'Money Code Issued';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Issued', base_url() . 'user/money_codes');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}
		$this->load->library('pagination');
		$this->data['getCards'] = $this->user_model->get_money_codes($where);
        // pagination
        $config['base_url'] = site_url('user/money_codes');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['getCards']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['getCards'] = $this->user_model->get_moneyCode_pagination($config['per_page'], $page, $where);		

		$this->_render_template('money-code/money-code-view', $this->data);		
	}

	public function moneyCodeInvoice(){
		$this->settings['title'] = 'Money Code Issued';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Issued', base_url() . 'user/moneyCodeInvoice');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}
		$this->load->library('pagination');
		$this->data['invoicePending'] = $this->user_model->getNonInvoicedMoneyCodes($where);
        // pagination
        $config['base_url'] = site_url('user/moneyCodeInvoice');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['invoicePending']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['invoicePending'] = $this->user_model->getNonInvoicedMoneyCodes_pagination($config['per_page'], $page, $where);		

		$this->_render_template('money-code/invoice-index', $this->data);		
	}
	
	public function moneyCodeInvoices(){
		$this->settings['title'] = 'Money Code Invoices';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Invoices', base_url() . 'user/moneyCodeInvoices');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}
		$this->load->library('pagination');
		$this->data['invoicePending'] = $this->user_model->getInvoicedMoneyCodes($where);
        // pagination
        $config['base_url'] = site_url('user/moneyCodeInvoices');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['invoicePending']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['invoicePending'] = $this->user_model->getInvoicedMoneyCodes_pagination($config['per_page'], $page, $where);		

		$this->_render_template('money-code/moneycode-invoiced', $this->data);		
	}	

	public function moneyCodeTrans($cid){
		$this->settings['title'] = 'Money Code Transactions';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Issued', base_url() . 'user/moneyCodeTrans');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['moneyCodeTrans'] = $this->user_model->get_companyMoneyCodeTrans($cid);		

		$this->_render_template('money-code/invoice-view', $this->data);		
	}
	
	public function moneyCodeTransDelete($id){
		$this->settings['title'] = 'Money Code Transactions';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Issued', base_url() . 'user/moneyCodeTrans');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$deleteStatus = $this->user_model->deleteMoneyCodeTrans($id);		
		if($deleteStatus){
			$this->session->set_flashdata('success', 'Transaction Deleted');
		}
		$this->_render_template('money-code/invoice-view', $this->data);		
	}	

	public function generateMoneyInvoice($cid){
		ob_start();
		require_once(APPPATH.'libraries/tcpdf/tcpdf.php');
		$custom_layout = array('250', '250');
		$obj_pdf = new tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);  
		$obj_pdf->SetTitle("Money Code Invoice");  
		$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
		$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
		$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
		$obj_pdf->SetDefaultMonospacedFont('helvetica');  
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
		//$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '2', PDF_MARGIN_RIGHT);
		$obj_pdf->SetMargins(5, 20, 5, true);	
		$obj_pdf->setPrintHeader(false);  
		$obj_pdf->setPrintFooter(true);  
		$obj_pdf->SetAutoPageBreak(TRUE, 10);  
		$obj_pdf->SetFont('helvetica', '', 9);
		
		$maxInvoiceId = $this->db->select_max('id')->get('money_codes_invoices')->row();
		empty($maxInvoiceId->id)?$invoiceID = 1:$invoiceID = $maxInvoiceId->id + 1;
		//ob_start();
		$invoiceData = $this->user_model->get_companyMoneyCodeTrans($cid);
		$getCompanyEmail = $this->db->select('*')->where('id', $cid)->get('users')->row();
		$company_email_ca = $getCompanyEmail->company_email;
		
		$obj_pdf->AddPage();
		$image = FCPATH.'assets/images/pride-diesel-logo.png';
		//$image = $obj_pdf->Image('assets/images/pride-diesel-logo.png', 50, 50, 100, '', '', '', '', false, 300);
		//echo '<img src="'.preg_replace('/\\\\/', '/', $image).'">';
		
		$content = '';
		$content .= '<h1 style="text-align: center;">Money Code Invoice</h1>';
			$content .= '<style>
				table{width:100%; font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;}
				 .custom-css th, .custom-css td{ border: 1px solid #D9D9D9; padding:10px;}
				 .remove-border{border:0px !important;}
				 img{width:85%; padding:20px;}
				 .pride {
							width: 25%;
							vertical-align: top;
						}
				.pride td {
					border: 0px;
				}
				.pride tr p{margin-bottom: 1px !important;}
				.custom-css tr th, .custom-css tr td{font-size: 9px;}
				.header-section-bot{margin-bottom: 15px;}
				.client-info t{padding: 10px !important;}
			</style>

      <table class="header-section" border="0" style="border-spacing: 0; border-collapse: collapse;" cellspacing="0" cellpadding="5">
	  <tbody>
	      <tr>
		       <td class="pride">
			      <img src="'.$image.'" alt="company_logo" width="180" height="60" border="0"/>  
			   </td>
		       <td class="pride">
			        <table>
						<tr>
							<td>
							<h4>Pride Diesel Inc.</h4><br />
							Corporate office: 10862 Steeles Ave East,<br/>Milton,ON L9T 2X8<br />
							Fax: (877)867-8922<br />
							Ph. 1888-909-7117<br />
							www.pridediesel.com<br />
							Email: info@pridediesel.com<br />
							GST/HST # 808170526RT0001
							</td>
						</tr>
					</table>
			   </td>
		       <td class="pride">
			        <table class="client-info" >
						<tr>
							<td>
							Customer #<br />
							Customer:<br />
							Address:<br />
							


							</td>
							<td>
							'.$getCompanyEmail->id .'<br />
							<strong>'.$getCompanyEmail->company_name .'</strong><br />
							'.$getCompanyEmail->address .'<br />
							</td>
						</tr>
						<tr>
							<td>
							Invoice #<br />
							Invoice Date<br />							
							</td>
							<td>
							'.$invoiceID.'<br />
							'.date('d/m/Y').'<br />							
							</td>							
						</tr>
					</table>
			   </td>
		       <td class="pride">Page-'.$obj_pdf->getPage().'<br />Confidential information</td>
		  </tr>
         
		</tbody>
		</table>';		
		
		$content .= '<style>.table-css{border-collapse: collpase;}
		 .no-border{border-left: 0px #fff; border-top: 0px #fff; border-bottom: 0px #fff;}
		 .border-right{}
		</style>';
		$content .= '<table class="custom-css table-css header-section" border="0" cellpadding="3" style="border: 1px solid #D9D9D9 ; border-spacing: 0; border-collapse: collapse;">
						<thead><tr>
							<th><strong>Money Code</strong></th>
							<th><strong>Driver Name</strong></th>
							<th><strong>Unit Number</strong></th>
							<th><strong>Amount</strong></th>
							<th><strong>Reason</strong></th>
							<th><strong>Created</strong></th>
						</tr></thead>';
		$subTotal = 0;
		$transCount = count($invoiceData);	
		foreach($invoiceData as $invoiceDataRows){
		$content .= '<tr>
						<td>'.$invoiceDataRows->moneyCode .'</td>
						<td>'.$invoiceDataRows->driver_name .'</td>
						<td>'.$invoiceDataRows->unit_number .'</td>
						<td>'.$invoiceDataRows->amount .'</td>
						<td>'.$invoiceDataRows->notes .'</td>
						<td>'.$invoiceDataRows->date_created .'</td>
					</tr>';
			$this->user_model->updateMoneyCodeInvoiceStatus($invoiceDataRows->id);		
			$subTotal += $invoiceDataRows->amount;			
		}
		$prideTotal = $transCount * 5;
		$grandTotal = floatval($subTotal) + $prideTotal;
		$content .= '<tr><td colspan="3" class=""></td><td class=" "></td><td>Sub Total</td><td>'.$subTotal.'</td></tr>';
		$content .= '<tr><td colspan="3" class=""></td><td class=""></td><td>Pride Diesel Charge</td><td>'.$prideTotal.'</td></tr>';
		$content .= '<tr><td colspan="3" class=""></td><td class=""></td><td>Total</td><td>'.$grandTotal.'</td></tr>';
		$content .= '</table>';
		$obj_pdf->writeHTML($content);
		$data = array('invoiceId'=>$invoiceID, 'companyId'=>$cid, 'date_created'=>date('Y-m-d H:i:s'), 'date_modified'=>date('Y-m-d H:i:s'));
		
		$this->db->insert('money_codes_invoices', $data);
		
		//$obj_pdf->Output('sample.pdf', 'I');
		$obj_pdf->Output(FCPATH . 'assets/modules/invoices/money_code_trans_'.$invoiceID."_".date('Y-m-d')."_".$cid.'.pdf', 'F');

		//Send generated invoice to company and then delete pdf
		$this->load->library('email');
		$ca_subject = 'Money Code Transactions Invoice';
		$ca_body = 'Money Code Transactions.';

		$pdfFilePath = FCPATH . 'assets/modules/invoices/money_code_trans_'.$invoiceID."_".date('Y-m-d')."_".$cid.'.pdf';
		$result = $this->email
					->from('info@pridediesel.com', 'From Pride Diesel')
					//->to('jagdishchander6373@gmail.com')
					->to($company_email_ca)
					->cc('abhinavdua1435@gmail.com')
					->subject($ca_subject)
					->message($ca_body)
					->attach($pdfFilePath)
					->send();
		$this->email->clear($pdfFilePath);
		if($result) {
			//echo "Send";
			//unlink($pdfFilePath); //for delete generated pdf file. 
		}
		ob_get_clean();
		$this->session->set_flashdata('success', 'Generated and Sent');
		redirect(base_url('user/moneyCodeInvoice'), 'refresh');
	}
	
	public function viewMoneycodeInvoice($invid){
		require_once(APPPATH.'libraries/tcpdf/tcpdf.php');
		$custom_layout = array('250', '250');
		$obj_pdf = new tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);  
		$obj_pdf->SetTitle("Money Code Invoice");  
		$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
		$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
		$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
		$obj_pdf->SetDefaultMonospacedFont('helvetica');  
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
		//$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '2', PDF_MARGIN_RIGHT);
		$obj_pdf->SetMargins(5, 20, 5, true);	
		$obj_pdf->setPrintHeader(false);  
		$obj_pdf->setPrintFooter(true);  
		$obj_pdf->SetAutoPageBreak(TRUE, 10);  
		$obj_pdf->SetFont('helvetica', '', 9);
		
		ob_start();
		$getInvoiceData = $this->user_model->get_companyMoneyCodeTransInvoiced($invid);
		foreach($getInvoiceData as $invoiceData){
			$invoiceData = $invoiceData;
		}
		
		
		$obj_pdf->AddPage();
		$imagenew = FCPATH ."assets/images/logo.png";
		
		//$image = $obj_pdf->Image('assets/images/pride-diesel-logo.png', 50, 50, 100, '', '', '', '', false, 300);
		//echo '<img src="'.preg_replace('/\\\\/', '/', $image).'">';
		
		$content = '';
		$content .= '<h1 style="text-align: center;">Money Code Invoice</h1>';
			$content .= '<style>
				table{width:100%; font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;}
				 .custom-css th, .custom-css td{ border: 1px solid #D9D9D9; padding:10px;}
				 .remove-border{border:0px !important;}
				 img{width:85%; padding:20px;}
				 .pride {
							width: 25%;
							vertical-align: top;
						}
				.pride td {
					border: 0px;
				}
				.pride tr p{margin-bottom: 1px !important;}
				.custom-css tr th, .custom-css tr td{font-size: 9px;}
				.header-section-bot{margin-bottom: 15px;}
				.client-info t{padding: 10px !important;}
			</style>

      <table class="header-section" border="0" style="border-spacing: 0; border-collapse: collapse;" cellspacing="0" cellpadding="5">
	  <tbody>
	      <tr>
		       <td class="pride">
			      <img src="" alt="company_logo" width="180" height="60" />  
			   </td>
		       <td class="pride">
			        <table>
						<tr>
							<td>
							<h4>Pride Diesel Inc.</h4><br />
							Corporate office: 10862 Steeles Ave East,<br/>Milton,ON L9T 2X8<br />
							Fax: (877)867-8922<br />
							Ph. 1888-909-7117<br />
							www.pridediesel.com<br />
							Email: info@pridediesel.com<br />
							GST/HST # 808170526RT0001
							</td>
						</tr>
					</table>
			   </td>
		       <td class="pride">
			        <table class="client-info" >
						<tr>
							<td>
							Customer #<br />
							Customer:<br />
							Address:<br />
							


							</td>
							<td>
							'.$invoiceData->companyId .'<br />
							<strong>'.$invoiceData->company_name .'</strong><br />
							'.$invoiceData->address .'<br />
							</td>
						</tr>
						<tr>
							<td>
							Invoice #<br />
							Invoice Date<br />							
							</td>
							<td>
							'.$invoiceData->id.'<br />
							'.date('d/m/Y', strtotime($invoiceData->invoicedate)).'<br />							
							</td>							
						</tr>
					</table>
			   </td>
		       <td class="pride">Page-'.$obj_pdf->getPage().'<br />Confidential information</td>
		  </tr>
         
		</tbody>
		</table>';		
		
		$content .= '<style>.table-css{border-collapse: collpase;}
		 .no-border{border-left: 0px #fff; border-top: 0px #fff; border-bottom: 0px #fff;}
		 .border-right{}
		</style>';
		$content .= '<table class="custom-css table-css header-section" border="0" cellpadding="3" style="border: 1px solid #D9D9D9 ; border-spacing: 0; border-collapse: collapse;">
						<thead><tr>
							<th><strong>Money Code</strong></th>
							<th><strong>Driver Name</strong></th>
							<th><strong>Unit Number</strong></th>
							<th><strong>Amount</strong></th>
							<th><strong>Reason</strong></th>
							<th><strong>Created</strong></th>
						</tr></thead>';
		//$subTotal = 0;
		//$transCount = count($invoiceData);	
		//foreach($invoiceData as $invoiceDataRows){
		$content .= '<tr>
						<td>'.$invoiceData->moneyCode .'</td>
						<td>'.$invoiceData->driver_name .'</td>
						<td>'.$invoiceData->unit_number .'</td>
						<td>'.$invoiceData->amount .'</td>
						<td>'.$invoiceData->notes .'</td>
						<td>'.$invoiceData->date_created .'</td>
					</tr>';
			//$this->user_model->updateMoneyCodeInvoiceStatus($invoiceDataRows->id);		
			//$subTotal += $invoiceDataRows->amount;			
		//}
		$prideTotal = '5.00';
		$grandTotal = floatval($invoiceData->amount) + $prideTotal;
		$content .= '<tr><td colspan="3" class=""></td><td class=" "></td><td>Sub Total</td><td>'.$invoiceData->amount.'</td></tr>';
		$content .= '<tr><td colspan="3" class=""></td><td class=""></td><td>Pride Diesel Charge</td><td>'.$prideTotal.'</td></tr>';
		$content .= '<tr><td colspan="3" class=""></td><td class=""></td><td>Total</td><td>'.floor($grandTotal*100)/100 .'</td></tr>';
		$content .= '</table>';
		$obj_pdf->writeHTML($content);
		//$obj_pdf->lastPage();
		ob_end_clean();
		//ob_end_flush(); 
		$obj_pdf->Output('moneycodeinvoice.pdf', 'I');		
	}
		
}