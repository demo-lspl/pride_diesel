<?php
class User extends MY_Controller {
	public function __construct(){
		parent::__construct();
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';		
		
		$this->scripts['js'][] = 'assets/modules/user/js/script.js';
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
		$this->data['salesPersons'] = $this->sales_person_model->get_sales_executives();			
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
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
            $data = $this->user_model->array_from_post(array('company_type', 'sales_person', 'company_name', 'address', 'city', 'province', 'postal_code','company_email', 'invoice_schedule', 'pricing_type', 'sms_notification', 'company_password', 'role'));
			
			if($data['company_password'] == ''){
				unset($data['company_password']);
			}else
            {
                
                $data['company_password'] = md5($data['company_password']);
            }
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

			$cid = $this->user_model->create_user($data, $id);
			
			if($cid){

				$this->session->set_flashdata('success_msg', 'Changes Saved.');
				
				redirect(base_url('user/edit/'.$cid), 'refresh');				
			}
			
		}		
		
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
		print_r($dataPdf);die;
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
		/* $this->load->helper('url');
		$exploded_url = explode( "#", $_SERVER['QUERY_STRING'] ); 
		pre(parse_url(current_url()));die; */
		$this->settings['title'] = 'Edit Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Edit Price List', base_url() . 'user/edit_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['usProducts'] = $this->user_model->get_dailypricelist_pro();		
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist();		
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
			/* PriceList table */
			$price_descrLength = count($_POST['rp_product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$m = 0;
					while($m < $price_descrLength) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'rp_product_name' && $key != 'retail_prices' && $key != 'rp_submit'){
									$sub_array[$key] = array($value[$m]);
							}	
						}
						$arr[$m] = (array($_POST['rp_product_name'][$m] => array($sub_array)));
						$m++;				
					}
					$priceDescr_data = json_encode($arr);
				}else{
					$priceDescr_data = '';
				}			
			/* PriceList Edit table */	
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
									
									$sub_array[$keypost] = array(number_format($jsonConvert, 4));
									}
								}
							}
							$sub_array['state'] = array($value->state);							
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($value->gas_station)));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array)));	
						}
					$editedPriceDescr_data = json_encode($arrrp_Price);
				}else{
					$editedPriceDescr_data = '';
				}
			}
			
			/* Retail PriceList Edit table */
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
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($_POST['retail_prices_edit_gas_station'][$m])));							
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
			//pre($editedPriceDescr_data);die;
			if(!empty($_POST['retail_cost_percent_prices'])){
			
			/* Retail PriceList Edit table */	
			$retail_prices_edit_Length = count($_POST['rcpp_product_name']);
				if($retail_prices_edit_Length >0){
					$arr = [];
					$m = 0;
					while($m < $retail_prices_edit_Length) {	
						foreach($_POST as $key=>$value){ 
							if($key != 'retail_cost_percent_prices' && $key != 'rcpp_product_name'  && $key != 'rcpp_submit'){
								$sub_array[$key] = array($value[$m]);
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
										$calcPercent = $afterDecRetailPride * floatval($postvalue[$productArrayFlip[$value->product]]) / 100;
										$amtAfterDec =  floatval($value->retail_price) - $calcPercent;
									$jsonConvert = floatval($value->retail_price) - floatval($amtAfterDec);
									
									$sub_array[$keypost] = array(number_format($jsonConvert, 4));
									}
								}
							}$sub_array['state'] = array($value->state);							
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($value->gas_station)));							
						}
						$arrrp_Price[$value->id] = (array($value->product => array($sub_array)));	
						}
					$editedRetailPriceDescr_data = json_encode($arrrp_Price);
				}else{
					$editedRetailPriceDescr_data = '';
				}//pre($editedRetailPriceDescr_data);
			} //die;

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
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($_POST['retailCost_prices_edit_gas_station'][$m])));							
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

			/* Add on EFS data */
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
									$jsonConvert = floatval($value->retail_price) - floatval($postvalue[$productArrayFlip[$value->product]]);
									
									$sub_array[$keypost] = array(number_format($jsonConvert, 4));
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
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($_POST['aoe_prices_edit_gas_station'][$m])));							
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
			/* Fix Price data */
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
									
									$sub_array[$keypost] = array(number_format($jsonConvert, 4));
									}
								}
							}$sub_array['state'] = array($value->state);							
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($value->gas_station)));							
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
							$sub_array['gas_station'] = array(str_replace(' ', '-', trim($_POST['fp_prices_edit_station'][$m])));								
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
			//$data['efs_price'] = $efsPrice_data;
			if(!empty($_POST['retail_prices'])){
				$data['retail_price'] = $priceDescr_data;				
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
			//pre($editedAEOPriceDescr_data);die;
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
			/* pre($data);
			die; */
			$this->user_model->save_pricelist_edit($data1, $id);
			$id = $this->user_model->save_pricelist($data, $id);
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('user/edit_pricelist/').$id, 'refresh');
			}
		}
		

		$this->_render_template('pricelist/edit', $this->data);		
	}
	
	public function edit_pricelist_ca($id = NULL){
		$this->settings['title'] = 'Edit Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Edit Price List', base_url() . 'user/edit_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['products'] = $this->user_model->get_prodcuts();		
		if($id){
			$this->data['pricelist'] = $this->user_model->get_ca_pricelist_by_id($id);
		}else{
			$this->data['pricelist'] = $this->user_model->get_pricelist_new();
		}
		
		$id == NULL || $this->data['pricelist'] = $this->user_model->get_ca_pricelist_by_id($id);
		
		$rules = $this->user_model->pricelist_rules;
		$this->form_validation->set_rules($rules);

		if($this->form_validation->run() == true){

			$price_descrLength = count($_POST['product_name']);
				if($price_descrLength >0){
					$arr = [];
					$arr2 = [];
					$i = 0;
					while($i < $price_descrLength) {	
						foreach($_POST as $key=>$value){
							if($key != 'product_name' && $key != 'efs_amt' && $key != 'retail_amt'){
								$sub_array[$key] = array($value[$i]);
							}
							//if($key == 'efs_amt' || $key == 'retail_amt'){
							//if(array_key_exists('efs_amt', $value) || array_key_exists('retail_amt', $value)){
							if($key == 'efs_amt' ){	
								$sub_array2[$key] = $value[$i];
							}
							if($key == 'retail_amt'){	
								$sub_array2[$key] = $value[$i];
							}							
						}
						$arr[$i] = (array($_POST['product_name'][$i] => array($sub_array)));
						$arr2[$i] = array($_POST['product_name'][$i] => $sub_array2);
						$i++;				
					}

					$priceDescr_data = json_encode($arr);
					$efsPrice_data = json_encode($arr2);
				}else{
					$priceDescr_data = '';
					$efsPrice_data = '';
				}	

			//JSON encoded data of description of products rows
			$data['efs_price'] = $efsPrice_data;
			$data['price_descr'] = $priceDescr_data;
			if($id){
				$data['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data['date_created'] = date('Y-m-d h:i:s');
			}
			
			$id = $this->user_model->save_ca_pricelist($data, $id);
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('user/edit_pricelist_ca/').$id, 'refresh');
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
		$this->settings['title'] = 'Price List';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Price List', base_url() . 'user/import_pricelist');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['companyTypeResult'] = $this->user_model->get_c_type();
		$this->data['products'] = $this->user_model->get_prodcuts();
		$this->data['pricelist'] = $this->user_model->get_com_pricelist_by_id(1);
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist();

		$this->_render_template('company/view', $this->data);			
	}	
	
	public function import_pricelist(){
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
                //$config['allowed_types'] = "xls";
				$config['allowed_types']        = '*';
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
							$insertdata[$i]['city'] = $allDataInSheet[$i]["F"];
							$insertdata[$i]['state'] = $allDataInSheet[$i]["D"];
							
							$words = preg_split("/[\s^0-9,_#%-]+/", $allDataInSheet[$i]['H']);
							$acronym = "";

							//print_r($words);
							foreach ($words as $w) {
								if(!empty($w[0]))
									$acronym .= $w[0];
							}
							if($acronym == 'DBUL' || $acronym == 'DBULC'){
								$acronym = 'ULSR';
							}
							if($acronym == 'DTLU' || $acronym == 'DULS' || $acronym == 'DCULS'){
								$acronym = 'ULSD';
							}							
							if($acronym == 'TLUB'){
								$acronym = 'DEFD';
							}							
							$insertdata[$i]['product'] = $acronym;
							$insertdata[$i]['retail_price'] = $allDataInSheet[$i]['U'];
							$insertdata[$i]['pride_price'] = $allDataInSheet[$i]['X'];
							$insertdata[$i]['date'] = date('Y-m-d', strtotime($allDataInSheet[$i]['J']));	
							$insertdata[$i]['date_created'] = date('Y-m-d h:i:s');	
							$insertdata[$i]['date_modified'] = date('Y-m-d h:i:s');
						}					
					}//die;					
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
                    } */ //pre($insertdata);die;              
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
                //$config['allowed_types'] = "xls";
				$config['allowed_types']        = '*';
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
					$this->db->truncate('retail_pricing_ca');
					
					for($i=2;$i<=$arrayCount;$i++)
					{                   
						if(!empty($allDataInSheet[$i]["A"])){
							$insertdata[$i]['gas_station'] = $allDataInSheet[$i]["A"];
							$insertdata[$i]['city'] = $allDataInSheet[$i]["B"];
							$insertdata[$i]['state'] = $allDataInSheet[$i]["C"];
							
							$words = preg_split("/[\s^0-9,_#%-]+/", $allDataInSheet[$i]['D']);
							$acronym = "";

							foreach ($words as $w) {
								if(!empty($w[0]))
									$acronym .= $w[0];
							}
							if($acronym == 'B' || $acronym == 'DLD' || $acronym == 'DL' || $acronym == 'DEL' || $acronym == 'R' || $acronym == 'DR'){
								$acronym = 'ULSR';
							}
							if($acronym == 'EM' || $acronym == 'ERD' || $acronym == 'ER' || $acronym == 'M' || $acronym == 'PD' || $acronym == 'P'){
								$acronym = 'ULSD';
							}							
							if($acronym == 'TLUB'){
								$acronym = 'DEFD';
							}							
							$insertdata[$i]['product'] = $acronym;
							//$insertdata[$i]['retail_price'] = $allDataInSheet[$i]['U'];trim(
							$insertdata[$i]['pride_price'] = $allDataInSheet[$i]['E'];
							$makeDateFormat = str_replace('/', '-', $allDataInSheet[$i]['O']);
							$insertdata[$i]['date'] = date('Y-m-d', strtotime($makeDateFormat));	
							$insertdata[$i]['date_created'] = date('Y-m-d h:i:s');	
							$insertdata[$i]['date_modified'] = date('Y-m-d h:i:s');
						}					
					} //pre($insertdata);die;            
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
		$this->settings['title'] = 'Money Code Issued';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Money Code Issued', base_url() . 'user/money_code_issued');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->_render_template('money-code/index', $this->data);		
	}
	
	public function money_code_issue(){
		$this->settings['title'] = 'Issue Money Code';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Issue Money Code', base_url() . 'user/money_code_issue');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->_render_template('money-code/edit', $this->data);		
	}	
		
}