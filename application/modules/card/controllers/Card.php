<?php
class Card extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }		
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';
		
		$this->scripts['js'][] = 'assets/modules/card/js/script.js';		

		$this->load->model('card_model');
		$this->load->model('user/user_model');
		$this->load->library('form_validation');
		$this->load->library('efs_api');
	}
	
	public function index(){
		$this->settings['title'] = 'All Card';
		$this->breadcrumb->mainctrl("card");
		$this->breadcrumb->add('View Card', base_url() . 'card/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->load->library('pagination');
		$this->load->model('user_model');
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		$where2 = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}
		if(!empty($_GET['company_name'])){
			$where2 = $_GET['company_name'];
		}		
		$this->data['allCardData'] = $this->card_model->get_cards($where, $where2);
		$this->data['searchCount'] = count($this->data['allCardData']);
        // pagination
        $config['base_url'] = site_url('card/index');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allCardData']);
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

        $this->data['allCardData'] = $this->card_model->get_pagination($config['per_page'], $page, $where, $where2);		
		$this->_render_template('index', $this->data);
	}	
	
	public function edit($id = null){
		$this->load->library("NuSoap_lib");
		$this->soapclient = new nusoap_client(site_url('/EFS_WS/index/wsdl'), true);	

		$companySession = $this->session->userdata('userdata');
		if($companySession->role == 'company'){
			$this->load->model('driver/driver_model');
			$companyId = $companySession->id;
			$this->data['companydrivers'] = $this->driver_model->get_driver_by_cid($companyId);
		}
		if($id){
			$this->settings['title'] = 'Edit Card';
			$this->breadcrumb->mainctrl("card");
			$this->breadcrumb->add('Edit Card', base_url() . 'card/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();			
			
			$this->data['card'] = $this->card_model->get_by_id($id);
			$this->data['getuserdata'] = $this->user_model->get_users();
			
		}else{
			$this->settings['title'] = 'Add Card';
			$this->breadcrumb->mainctrl("card");
			$this->breadcrumb->add('Add Card', base_url() . 'card/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();			

			$this->data['card'] = $this->card_model->get_new();	
		}
		
		$id == NULL || $this->data['card'] = $this->card_model->get_by_id($id);
		
		$rules = $this->card_model->rules;
		$this->form_validation->set_rules($rules);
		//print_r($this->data['card']);die;
		if($this->form_validation->run() == true){
			//pre($_POST);die;
			$data = $this->card_model->array_from_post(array('card_limit', 'policy_number', 'card_status', 'card_pin', 'unit_number', 'odometer'));

			!empty($this->input->post('card_assign'))?$data['company_id'] = $this->input->post('card_assign'):	$data['company_id'] = 0;
			!empty($this->input->post('card_assign_driver'))?$data['driver_id'] = $this->input->post('card_assign_driver'):	$data['driver_id'] = NULL;
			/* Update card status Husky */
			if($this->input->post('card_status') == '1' || $this->input->post('card_status') == '3' || $this->input->post('card_status') == '4' && !empty($this->input->post('cardToken'))){
			if($this->input->post('card_status') == '1'){$cardStatusHusky = 'A';}else if($this->input->post('card_status') == '3'){$cardStatusHusky = 'B';}else if($this->input->post('card_status') == '4'){$cardStatusHusky = 'C';}				
		$ws_url = 'https://api.iconnectdata.com:443/FleetCreditWS/services/FleetCreditWS0200';
		$headers = array(
			"Content-type: application/xml;charset=utf-8",
			'Authorization: Basic '. base64_encode('HS1-14OHL:Pride@3963'),
			'Connection: keep-alive'.
			'Content-Encoding: gzip',
			"SOAPAction: http://fleetCredit02.comdata.com/FleetCreditWS0200/cardListing",
		);		
		$xmlData = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:main="http://fleetCredit02.comdata.com/maintenance/">
		  <soapenv:Header>
			<wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				<wsse:UsernameToken>
					<wsse:Username>HS1-14OHL</wsse:Username>
					<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">Pride@3963</wsse:Password>
				</wsse:UsernameToken>
			</wsse:Security>
		</soapenv:Header>
		<soapenv:Body>
			        <main:UpdateCardRequestV02>
						 <cardIdentifierType>T</cardIdentifierType>
						 <cardIdentifier>'.$this->input->post('cardToken').'</cardIdentifier>
						 <cardDetails>
							<accountCode>1-14O</accountCode>
							<customerId>'.$this->input->post('policy_number').'</customerId>
							<cardStatus>'.$cardStatusHusky.'</cardStatus>

						</cardDetails>
					</main:UpdateCardRequestV02>
		</soapenv:Body>
		</soapenv:Envelope>';

		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $ws_url,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS =>$xmlData,
		));

		$response = curl_exec($curl);
		
		curl_close($curl);
		/* print_r($response);die;
            $response1 = str_replace('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><soapenv:Header/><soapenv:Body>',"",$response);
            $response2 = str_replace("</soapenv:Body></soapenv:Envelope>","",$response1);
			// convertingc to XML
            $parser = simplexml_load_string($response2); */
						
			}	
			
			/* Update card status EFS */
			if(empty($this->input->post('cardToken'))){
			$clientToken = $this->soapclient->call('login', array('user'=>'HSINGH3', 'password' => 'Harry0044'));
			$cardResult['infos'] = null;
			$cardResult = $this->soapclient->call('getCard', array('clientId'=>$clientToken, 'cardNumber' => $this->input->post('card_number')));
			
			//Get card data from EFS LLC
			if($this->input->post('card_status') == '0' || $this->input->post('card_status') == '1' || $this->input->post('card_status') == '2'){
				if($this->input->post('card_status') == '0'){$cardStatus = 'INACTIVE';}else if($this->input->post('card_status') == '1'){$cardStatus = 'ACTIVE';}else if($this->input->post('card_status') == '2'){$cardStatus = 'HOLD';}
				if(!empty($cardResult['header'])){
				if(!empty($cardResult['header'])){
				$xmlData =	"<clientId>$clientToken</clientId>
				 <card>
					<cardNumber>".$this->input->post('card_number')."</cardNumber>
					<header>
					   <companyXRef>".$cardResult['header']['companyXRef']."</companyXRef>            
					   <handEnter>".$cardResult['header']['handEnter']."</handEnter>
					   <infoSource>".$cardResult['header']['infoSource']."</infoSource>
					   <limitSource>".$cardResult['header']['limitSource']."</limitSource>
					   <locationOverride>".$cardResult['header']['locationOverride']."</locationOverride>
					   <locationSource>".$cardResult['header']['locationSource']."</locationSource>
					   <overrideAllLocations>".$cardResult['header']['overrideAllLocations']."</overrideAllLocations>
					   <originalStatus>".$cardResult['header']['originalStatus']."</originalStatus>               
					   <payrollStatus>".$cardResult['header']['payrollStatus']."</payrollStatus>
					   <override>".$cardResult['header']['override']."</override>
					   <policyNumber>".$cardResult['header']['policyNumber']."</policyNumber>
					   <status>$cardStatus</status>
					   <timeSource>".$cardResult['header']['timeSource']."</timeSource>
					   <lastUsedDate>".$cardResult['header']['lastUsedDate']."</lastUsedDate>               
					   <lastTransaction>".$cardResult['header']['lastTransaction']."</lastTransaction>
					   <payrollUse>".$cardResult['header']['payrollUse']."</payrollUse>
					   <payrollAtm>".$cardResult['header']['payrollAtm']."</payrollAtm>
					   <payrollChk>".$cardResult['header']['payrollChk']."</payrollChk>
					   <payrollAch>".$cardResult['header']['payrollAch']."</payrollAch>
					   <payrollWire>".$cardResult['header']['payrollWire']."</payrollWire>
					   <payrollDebit>".$cardResult['header']['payrollDebit']."</payrollDebit>
					</header>";
					if($this->input->post('odometer') == '1'){
								$xmlData .=	"<infos>
								   <infoId>ODRD</infoId>
								   <lengthCheck>false</lengthCheck>
								   <matchValue></matchValue>
								   <maximum>5</maximum>
								   <minimum>1</minimum>
								   <reportValue></reportValue>
								   <validationType>REPORT_ONLY</validationType>
								   <value>0</value>
								</infos>
								";									
					}
					if(!empty($this->input->post('unit_number'))){
								$xmlData .=	"<infos>
								   <infoId>UNIT</infoId>
								   <lengthCheck>false</lengthCheck>
								   <matchValue></matchValue>
								   <maximum>0</maximum>
								   <minimum>0</minimum>
								   <reportValue></reportValue>
								   <validationType>NUMERIC</validationType>
								   <value>".$this->input->post('unit_number')."</value>
								</infos>
								";									
					}
					if(!empty($this->input->post('card_pin'))){
								$xmlData .=	"<infos>
								   <infoId>DRID</infoId>
								   <lengthCheck>false</lengthCheck>
								   <matchValue>".$this->input->post('card_pin')."</matchValue>
								   <maximum>0</maximum>
								   <minimum>0</minimum>
								   <reportValue/>
								   <validationType>EXACT_MATCH</validationType>
								   <value>0</value>
								</infos>
								";
					}
					//pre(is_array($cardResult['infos']));
					//pre($cardResult['infos']);die;					
					//if(!empty($cardResult['infos'][0])){
						if(isset($cardResult['infos'][0]) && !empty($cardResult['infos'][0]) && count($cardResult['infos'])>0){
							for($info=0; $info<count($cardResult['infos']); $info++){
								if($cardResult['infos'][$info]['infoId'] != 'ODRD' && $cardResult['infos'][$info]['infoId'] != 'UNIT' && $cardResult['infos'][$info]['infoId'] != 'DRID'){
								$xmlData .=	"<infos>
											   <infoId>".$cardResult['infos'][$info]['infoId']."</infoId>
											   <lengthCheck>".$cardResult['infos'][$info]['lengthCheck']."</lengthCheck>
											   <matchValue>".$cardResult['infos'][$info]['matchValue']."</matchValue>
											   <maximum>".$cardResult['infos'][$info]['maximum']."</maximum>
											   <minimum>".$cardResult['infos'][$info]['minimum']."</minimum>
											   <reportValue>".$cardResult['infos'][$info]['reportValue']."</reportValue>
											   <validationType>".$cardResult['infos'][$info]['validationType']."</validationType>
											   <value>".$cardResult['infos'][$info]['value']."</value>
											</infos>
											";
								}
							}
						}else{
						if(isset($cardResult['infos']) && count($cardResult['infos'])==1){
							//for($info=0; $info<count($cardResult['infos']); $info++){
								if(is_array($cardResult['infos'])){
									if($cardResult['infos']['infoId'] != 'ODRD' || $cardResult['infos']['infoId'] != 'UNIT' || $cardResult['infos'][$info]['infoId'] != 'DRID'){
									$xmlData .=	"<infos>
												   <infoId>".$cardResult['infos']['infoId']."</infoId>
												   <lengthCheck>".$cardResult['infos']['lengthCheck']."</lengthCheck>
												   <matchValue>".$cardResult['infos']['matchValue']."</matchValue>
												   <maximum>".$cardResult['infos']['maximum']."</maximum>
												   <minimum>".$cardResult['infos']['minimum']."</minimum>
												   <reportValue>".$cardResult['infos']['reportValue']."</reportValue>
												   <validationType>".$cardResult['infos']['validationType']."</validationType>
												   <value>".$cardResult['infos']['value']."</value>
												</infos>
												";
									}
								}
							}
						}						
					//}
					$xmlData .=	"
				 </card>";
				 //pre($xmlData);
				}//die;
				
				$callStatus = $this->soapclient->call('setCard', $xmlData);
				//pre($callStatus);
				//pre($xmlData);die;
			}
			
			}
			}
			if($id){
				if($companySession->role == 'company'){
					unset($data['company_id']);
				}
				if($companySession->role == 'admin'){
					unset($data['driver_id']);
				}	
				$data['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_created'] = date('Y-m-d h:i:s');
				$data['date_modified'] = date('Y-m-d h:i:s');
			}			
			if(!empty($this->input->post('card_pin'))){
				//$clientToken = $this->soapclient->call('login', array('user'=>'WS352369', 'password' => 'WEX2020'));
				//$this->soapclient->call('setCardPin', array('clientId'=>$clientToken, 'cardNum' => $this->input->post('card_number'), 'newPin' => $this->input->post('card_pin')));
			}
			
			$id = $this->card_model->edit($data, $id);
			if($id){
				$this->session->set_flashdata('success_msg', 'Changes Saved');
				
				redirect(base_url('card/edit/'.$id), 'refresh');
			}
			
		}		
		
		$this->_render_template('edit', $this->data);
	}
	
	public function get_my_card(){
		$this->settings['title'] = 'My Card';
		$this->breadcrumb->mainctrl("card");
		$this->breadcrumb->add('My Card', base_url() . 'card/my_card');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		
			
		$userSessDetails = $this->session->userdata('userdata');	
		$this->data['allCardData'] = $this->card_model->get_card_by_id($userSessDetails->id, $where);
		$this->data['cardCount'] = count($this->data['allCardData']);
        // pagination
        $config['base_url'] = site_url('card/get_my_card');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allCardData']);
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

        $this->data['allCardData'] = $this->card_model->get_pagination_company_cards($userSessDetails->id,$config['per_page'], $page, $where);			
		
		$this->_render_template('company-index', $this->data);
	}
	
	public function delete($id=null){
		$cardDeleted = $this->card_model->delete($id);
		
		if($cardDeleted == true){
			$this->session->set_flashdata('success_msg', 'Card deleted');
			redirect(base_url('card/index'), 'refresh');
		}
	}
	
	public function import(){
		$this->settings['title'] = 'Import Card';
		$this->breadcrumb->mainctrl("card");
		$this->breadcrumb->add('Import Card', base_url() . 'card/import');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();		
		
		$this->_render_template('import', $this->data);
	}
	
	public function importCard(){
		$this->settings['title'] = 'Import Card';
		$this->breadcrumb->mainctrl("card");
		$this->breadcrumb->add('Import Card', base_url() . 'card/import');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();		
		
		   if(!empty($_FILES['uploadFile']['name'])!=''){
			    
                $path = 'assets/modules/card/excel_for_cards/';
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
                 //print_r($data);die;

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

                     // die();

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
                 
         
        	$this->session->set_flashdata('success_msg', 'Cards Imported Successfully');
       		 redirect(base_url().'card/import', 'refresh');

    }

    echo"<script>alert('Please Select the File to Upload')</script>";
    redirect(base_url().'card/import', 'refresh');
	}
	
	public function exportBlankCardsExcel(){
		//File Name
		$fileName = 'cards-blank-excel-'.date('Y-m-d'); //format should be .xlsx , .csv

		$this->load->library('excel');
		$cardInfo = $this->card_model->exportcards();
		
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Card Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Card Limit');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Policy Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Card Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Card PIN');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Date Created');		


       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
			        header('Content-Type: application/vnd.ms-excel');
			       header("Content-Disposition: attachment;filename=".$fileName.".xlsx");
			         ob_end_clean();
			        $object_writer->save('php://output');		
	}	

	public function exportCards(){
		//File Name
		$fileName = 'cards-data-'.date('Y-m-d'); //format should be .xlsx , .csv

		$this->load->library('excel');
		$cardInfo = $this->card_model->exportcards();
		
		$objPHPExcel = new PHPExcel();		
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Card Number'); 		
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Card Limit');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Policy Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Card Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Card PIN');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Date Created');


        // set Row
        $rowCount = 2;
        foreach ($cardInfo as $element) {
			if($element['card_status'] == 0){
				$cardStatus = 'Inactive';
			}else if($element['card_status'] == 1){
				$cardStatus = 'Active';
			}else if($element['card_status'] == 2){
				$cardStatus = 'Hold';
			}
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'. $rowCount, $element['card_number'], PHPExcel_Cell_DataType::TYPE_STRING);
            //$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element['card_number']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $element['card_limit']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element['policy_number']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $cardStatus);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $element['card_pin']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $element['date_created']);

            $rowCount++;
        } 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);
			       // header('Content-Type: application/vnd.ms-excel');
			      
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
 header("Content-Disposition: attachment;filename=".$fileName.".xlsx");
		   
			         ob_end_clean();
			        $object_writer->save('php://output');		
	}	
		
}