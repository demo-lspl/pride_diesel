<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

     

class Api extends REST_Controller {

    

	  /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function __construct() {

       parent::__construct();
		date_default_timezone_set('America/Toronto');
       $this->load->database();
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *');
    }
	
	public function index_get()
	{
		echo 'Hello, API here!';
        exit();
	}	

    /**

     * Get login from this method.

     *

     * @return Response

    */ 
    public function login_get(){
        //echo "test";die;
        $this->load->model('auth/auth_model', '', true);
        if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
            $result = $this->auth_model->login_check($_REQUEST['email'], md5($_REQUEST['password']));
            //print_r($result);die;
            if (!empty($result) && $result != 'Wrong user and password') {
					$uemail = $_REQUEST['email'];
					$vercode = mt_rand(100000, 999999);
					$this->db->set(['verification_code'=> $vercode, 'ver_code_sent' => date('Y-m-d H:i:s')])->where('id', $result->id)->update('users');
					$this->send_varification_email($uemail, $vercode);
                    $this->session->set_userdata('loggedInUser', $result);

                    $user_data   = array(
                        "user_id" => $result->id,
                        "company_name" => $result->company_name,
                        "company_type" => $result->company_type,
                        "company_email" => $result->company_email,
                        "pricing_type" => $result->pricing_type,
                        "allowMoneyCode" => $result->allowMoneyCode,
                        "role" => $result->role,
                        "otp" => $vercode,
                        "time_stamp" => date('Y-m-d H:i:s')
                    );
                    //$data         = json_encode($user_data);
                    //echo '{"Status":"true","Data":' . $data . '}';
					$output = (array("Status"=> 'true', 'results'=>$user_data));
					$this->response($output, REST_Controller::HTTP_OK);
            } else {
                echo '{"Status":"false", "Data":[{"result":"The credentials you supplied are not correct"}]}';
            }
			
        }
    }
	public function send_varification_email($uemail, $vercode){
		$this->load->library('email');
		/* $config = Array(        
		'protocol' => 'sendmail',
		'smtp_host' => 'ssl://smtp.googlemail.com',
		'smtp_port' => 465,
		'smtp_user' => 'jagdish@lastingerp.com',
		'smtp_pass' => 'dKhWX=3GJOSHI',
		'smtp_timeout' => '4',
		'mailtype'  => 'html', 
		'charset'   => 'utf-8',
		'wordwrap' => TRUE
		); */
		//$this->load->library('email', $config);
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('info@pridediesel.com');				
		$this->email->to($uemail);
		$this->email->subject('Pride Diesel login verification code');
		
		$message = '<p>Your 2 factor verification code is below:-</p>';
		$message .= "Verification Code: ".$vercode;
		$message .= "<p>Note: Code will expire within 10 minutes</p>";
		$this->email->message($message);
		
		$this->email->send();
				
	}

	public function resend_verification_email_get(){
		$this->load->library('email');
		$uid = $_REQUEST['uid'];
		$uemail = $_REQUEST['uemail'];
		//$getResults = $this->db->where('id', $uid)->get('users')->row();
		
		$vercode = mt_rand(100000, 999999);
		$this->db->set(['verification_code'=> $vercode, 'ver_code_sent' => date('Y-m-d H:i:s')])->where('id', $uid)->update('users');
		//$this->data['userId'] = $getResults->id;
		//$this->data['userEmail'] = $getResults->company_email;
		
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('info@pridediesel.com');				
		$this->email->to($uemail);
		$this->email->subject('Pride Diesel login verification code');
		
		$message = '<p>Your 2 factor verification code is below:-</p>';
		$message .= "Verification Code: ".$vercode;
		$message .= "<p>Note: Code will expire within 10 minutes</p>";
		$this->email->message($message);
		
		$this->email->send();
                    $user_data   = array(
                        "otp" => $vercode,
                        "time_stamp" => date('Y-m-d H:i:s')
                    );		
					$output = (array("Status"=> 'true', 'results'=>$user_data));
					$this->response($output, REST_Controller::HTTP_OK);
	}
	
    public function login_post(){
        //echo "test";die;
        $this->load->model('auth/auth_model', '', true);
        if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
            $result = $this->auth_model->login_check($_REQUEST['email'], md5($_REQUEST['password']));
            //print_r($result);die;
            if (!empty($result) && $result != 'Wrong user and password') {
 
                    $this->session->set_userdata('loggedInUser', $result);

                    $user_data   = array(
                        "user_id" => $result->id,
                        "company_name" => $result->company_name,
                        "company_type" => $result->company_type,
                        "company_email" => $result->company_email,
                        "pricing_type" => $result->pricing_type,
                        "role" => $result->role
                    );
                    //$data         = json_encode($user_data);
                    //echo '{"Status":"true","Data":' . $data . '}';
					$output = (array("Status"=> 'true', 'results'=>$user_data));
					$this->response($output, REST_Controller::HTTP_OK);
            } else {
                echo '{"Status":"false", "Data":[{"result":"The credentials you supplied are not correct"}]}';
            }
			
        }
    }
	
	public function getCards_get($card_id = 0){
		$this->load->model('card/card_model', '', true);
		
        if(!empty($card_id)){

            $data = $this->db->get_where("cards", ['id' => $card_id])->row_array();

        }else{

            $getCards = $this->db->order_by('id', 'DESC')->get("cards")->result();
			foreach($getCards as $getCardsItems){
				if($getCardsItems->company_id != 0){
					$getCompany = $this->db->select('company_name')->where('id', $getCardsItems->company_id)->get('users')->row();
					$data[] = array('id' => $getCardsItems->id,
									'cardCompany' => $getCardsItems->cardCompany,
									'cardToken' => $getCardsItems->cardToken,
									'card_number' => $getCardsItems->card_number,
									'card_status' => $getCardsItems->card_status,
									'policy_number' => $getCardsItems->policy_number,
									'card_pin' => $getCardsItems->card_pin,
									'driver_id' => $getCardsItems->driver_id,
									'unit_number' => $getCardsItems->unit_number,
									'odometer' => $getCardsItems->odometer,
									'company_assigned' => $getCompany->company_name,
									'company_id' => $getCardsItems->company_id,
									'date_created' => $getCardsItems->date_created,
									);
				}else{
					$data[] = array('id' => $getCardsItems->id,
									'cardCompany' => $getCardsItems->cardCompany,
									'cardToken' => $getCardsItems->cardToken,
									'card_number' => $getCardsItems->card_number,
									'card_status' => $getCardsItems->card_status,
									'policy_number' => $getCardsItems->policy_number,
									'card_pin' => $getCardsItems->card_pin,
									'driver_id' => $getCardsItems->driver_id,
									'unit_number' => $getCardsItems->unit_number,
									'odometer' => $getCardsItems->odometer,									
									'company_assigned' => null,
									'company_id' => 0,
									'date_created' => $getCardsItems->date_created,									
									);					
				}
			}
			//print_r($data);die;

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
     
        //echo json_encode($data);
       //exit();
        $this->response($output, REST_Controller::HTTP_OK);		
	}
	
	public function getCardsByCid_get($cid){
		$this->load->model('card/card_model', '', true);

        $data = $this->db->get_where("cards", ['company_id' => $cid])->result();

		$output = (array("Status"=> 'true', 'results'=>$data));
     
        //echo json_encode($data);
       //exit();
        $this->response($output, REST_Controller::HTTP_OK);		
	}	
	
    public function editCard_get(){
		
		$this->load->library("NuSoap_lib");
		$this->soapclient = new nusoap_client(site_url('/EFS_WS/index/wsdl'), true);
        //$input = $this->put();
		
			/* Update card status EFS */
			if(isset($_REQUEST['card_status']) && $_REQUEST['cardCompany'] == 'EFS'){
			$clientToken = $this->soapclient->call('login', array('user'=>'HSINGH3', 'password' => 'Harry0044'));
			$cardResult['infos'] = null;
			$cardResult = $this->soapclient->call('getCard', array('clientId'=>$clientToken, 'cardNumber' => $_REQUEST['card_number']));
			
			//Get card data from EFS LLC
			if($_REQUEST['card_status'] == '0' || $_REQUEST['card_status'] == '1' || $_REQUEST['card_status'] == '2'){
				if($_REQUEST['card_status'] == '0'){$cardStatus = 'INACTIVE';}else if($_REQUEST['card_status'] == '1'){$cardStatus = 'ACTIVE';}else if($_REQUEST['card_status'] == '2'){$cardStatus = 'HOLD';}
				if(!empty($cardResult['header'])){
				if(!empty($cardResult['header'])){
				$xmlData =	"<clientId>$clientToken</clientId>
				 <card>
					<cardNumber>".$_REQUEST['card_number']."</cardNumber>
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
					if($_REQUEST['odometer'] == '1'){
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
					if(!empty($_REQUEST['unit_number'])){
								$xmlData .=	"<infos>
								   <infoId>UNIT</infoId>
								   <lengthCheck>false</lengthCheck>
								   <matchValue></matchValue>
								   <maximum>0</maximum>
								   <minimum>0</minimum>
								   <reportValue></reportValue>
								   <validationType>NUMERIC</validationType>
								   <value>".$_REQUEST['unit_number']."</value>
								</infos>
								";									
					}
					if(!empty($_REQUEST['card_pin'])){
								$xmlData .=	"<infos>
								   <infoId>DRID</infoId>
								   <lengthCheck>false</lengthCheck>
								   <matchValue>".$_REQUEST['card_pin']."</matchValue>
								   <maximum>0</maximum>
								   <minimum>0</minimum>
								   <reportValue/>
								   <validationType>EXACT_MATCH</validationType>
								   <value>0</value>
								</infos>
								";
					}
					//pre(is_array($cardResult['infos']));
										
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
				 
				}//die;
				
				$callStatus = $this->soapclient->call('setCard', $xmlData);
			}
			
			}
			}
			//print_r($_REQUEST['card_status']);
			/* Update card status Husky */
			if($_REQUEST['card_status'] == '1' || $_REQUEST['card_status'] == '3' || $_REQUEST['card_status'] == '4' && $_REQUEST['cardCompany'] == 'HUSKY'){
				if($_REQUEST['card_status'] == '1'){$cardStatusHusky = 'A';}else if($_REQUEST['card_status'] == '3'){$cardStatusHusky = 'B';}else if($_REQUEST['card_status'] == '4'){$cardStatusHusky = 'C';}				
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
								 <cardIdentifier>'.$_REQUEST['cardToken'].'</cardIdentifier>
								 <cardDetails>
									<accountCode>1-14O</accountCode>
									<customerId>'.$_REQUEST['policy_number'].'</customerId>
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
			}			
		$data = array(
			'card_number' =>$_REQUEST['card_number'],
			'card_limit' =>$_REQUEST['card_limit'],
			'policy_number' =>$_REQUEST['policy_number'],
			'card_status' =>$_REQUEST['card_status'],
			'card_pin' =>$_REQUEST['card_pin'],
			'company_id' =>$_REQUEST['company_id'],
			'driver_id' =>$_REQUEST['driver_id'],
			'unit_number' =>$_REQUEST['unit_number'],
			'odometer' =>$_REQUEST['odometer'],
			'date_modified' =>$_REQUEST['date_modified']
		);

		$this->db->set($data);
		$this->db->where(array('id'=>$_REQUEST['id']));
        //$this->db->update('drivers', $input, array('id'=>$id));
        $this->db->update('cards');
		
        $this->response(['Card updated successfully.'], REST_Controller::HTTP_OK);

    }	
	
	public function getUsers_get($card_id = 0){
		$this->load->model('user/user_model', '', true);
		
        if(!empty($card_id)){

            $data = $this->db->get_where("users", ['id' => $card_id])->row_array();

        }else{

            $data = $this->db->order_by('id', 'DESC')->get("users")->result();

        }

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}

	public function getTransactions_get($card_id =0,$daterange=null){
		/* $this->load->model('account/account_model', '', true);
			$data = $this->db->order_by('id', 'DESC')->get("transactions")->result();
			//pre($getTranss);die;
        if(!empty($card_id)){

            $data = $this->db->get_where("transactions", ['card_number' => $card_id])->row_array();

        }else if(!empty($daterange)){
			$where = null;
			$where2 = null;
			$explodeDateRange = explode('to', $daterange);			
			$where2 = $explodeDateRange;			
			$data = $this->account_model->get_transactions_api($where, $where2);
		}
		else{
			$getTrans = $this->db->order_by('id', 'DESC')->get("transactions")->result();
			pre($getTrans);die;
            //$data = $this->db->order_by('id', 'DESC')->get("transactions")->result();

        }//print_r($this->db->last_query());die; */
		$data = NULL;
		if(!empty($card_id)){
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			if(!empty($card_id)){
				$this->db->like('cards.card_number', $card_id);
			}
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
            //$data = $this->db->get_where("transactions", ['card_number' => $card_id])->row_array();
        }else if(!empty($daterange)){
			$where = null;
			$where2 = null;
			$explodeDateRange = explode('to', $daterange);			
			$where2 = $explodeDateRange;
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			if(!empty($where)){
				$this->db->like('cards.card_number', $where);
			}
			if(!empty($where2)){
				$start_date=$where2[0];
				$end_date=$where2[1];

				$this->db->where('transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
			}
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
			//$data = $this->account_model->get_transactions_api($where, $where2);
		}
		else{
			$getTrans = $this->db->order_by('id', 'DESC')->get("transactions")->result();
		}
		//print_r($getTrans);die;
			foreach($getTrans as $getTransItems){
				$categoryJsonDecode = json_decode($getTransItems->category);
				$pridePriceJsonDecode = json_decode($getTransItems->pride_price);
				$quantityJsonDecode = json_decode($getTransItems->quantity);
				$inc = 0;
				$transTotal = 0;
				//$amt = array();
				//print_r($categoryJsonDecode);
				//foreach($categoryJsonDecode as $categoryJsonDecodeItems){
					$commaSepVal = '';
				for($trans=0; $trans<count($categoryJsonDecode); $trans++){	
					
					$productName = $categoryJsonDecode[$inc];
					$pridePrice = $pridePriceJsonDecode[$inc];
					$productQuantity = $quantityJsonDecode[$inc];
					$totalAfterMultiplication = $productQuantity * floatval($pridePrice);
					//$amt[$inc] = $totalAfterMultiplication.",";
					$commaSepVal .= $pridePriceJsonDecode[$inc].", ";
					$transTotal += floor($totalAfterMultiplication*100)/100;
					$inc++;
				}
				
				$data[] = array(
								'cardNumber' => $getTransItems->card_number, 
								'billingCurrency' => $getTransItems->billing_currency, 
								'gasStationName' => $getTransItems->gas_station_name, 
								'gasStationCity' => $getTransItems->gas_station_city, 
								'gasStationState' => $getTransItems->gas_station_state, 
								'productName' => $getTransItems->category, 
								'price' => rtrim($commaSepVal, ", "), 
								'quantity' => $getTransItems->quantity, 
								'transactionDate' => $getTransItems->transaction_date, 
								'transactionId' => $getTransItems->transaction_id, 
								'transactionTotal' => $transTotal,
								//'amt' => $amt
								);
				
			}
			//print_r($data);
			//die;
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK); 		
	}
	
	public function getTransactionsByCid_get($cid, $daterange=null){
		if(!empty($daterange)){
			$where = null;
			$where2 = null;
			$explodeDateRange = explode('to', $daterange);			
			$where2 = $explodeDateRange;
			//$this->db->select('cards.card_number, drivers.name, cards.card_status, transactions.transaction_date');
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			if(!empty($where)){
				$this->db->like('cards.card_number', $where);
			}
			$this->db->where('cards.company_id', $cid);
			if(!empty($where2)){
				$start_date=$where2[0];
				$end_date=$where2[1];

				$this->db->where('transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
			}
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
		}
		else{
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			$this->db->where('cards.company_id', $cid);
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
		}
			$data = null;
			foreach($getTrans as $getTransItems){
				$categoryJsonDecode = json_decode($getTransItems->category);
				$pridePriceJsonDecode = json_decode($getTransItems->pride_price);
				$quantityJsonDecode = json_decode($getTransItems->quantity);
				$inc = 0;
				$transTotal = 0;
				foreach($categoryJsonDecode as $categoryJsonDecodeItems){
					$productName = $categoryJsonDecode[$inc];
					$pridePrice = $pridePriceJsonDecode[$inc];
					$productQuantity = $quantityJsonDecode[$inc];
					$totalAfterMultiplication = $productQuantity * floatval($pridePrice);
					$transTotal += floor($totalAfterMultiplication*100)/100;
					//$data[] = array('price' => $pridePrice,);
				}
				$commaSepVal = '';
				for($pinc=0; $pinc<count($pridePriceJsonDecode); $pinc++){
					
					$commaSepVal .= $pridePriceJsonDecode[$pinc].", ";
					
				}
				$data[] = array(
								'cardNumber' => $getTransItems->card_number, 
								'billingCurrency' => $getTransItems->billing_currency, 
								'gasStationName' => $getTransItems->gas_station_name, 
								'gasStationCity' => $getTransItems->gas_station_city, 
								'gasStationState' => $getTransItems->gas_station_state, 
								'productName' => $getTransItems->category, 
								'price' => rtrim($commaSepVal,", "), 
								'quantity' => $getTransItems->quantity, 
								'transactionDate' => $getTransItems->transaction_date, 
								'transactionId' => $getTransItems->transaction_id, 
								'transactionTotal' => $transTotal
								);
				//$inc++;
				
			}		
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK); 		
	}	

	public function getDrivers_get($name = null){
		$this->load->model('driver/driver_model', '', true);
		
        if(!empty($name)){

            $data = $this->db->get_where("drivers", ['name' => $name])->row_array();

        }else{

            $getDrivers = $this->db->order_by('id', 'DESC')->get("drivers")->result();
			foreach($getDrivers as $getDriversItems){
				if($getDriversItems->company_id != 0){
					$getCompany = $this->db->select('company_name')->where('id', $getDriversItems->company_id)->get('users')->row();
					$data[] = array('id' => $getDriversItems->id,
									'name' => $getDriversItems->name,
									'address' => $getDriversItems->address,
									'state' => $getDriversItems->state,
									'country' => $getDriversItems->country,
									'postal_code' => $getDriversItems->postal_code,
									'email' => $getDriversItems->email,
									'phone' => $getDriversItems->phone,
									'unit_number' => $getDriversItems->unit_number,
									'licence_number' => $getDriversItems->licence_number,
									'date_created' => $getDriversItems->date_created,
									'company_assigned' => $getCompany->company_name,
									'company_id' => $getDriversItems->company_id,
									);
				}
			}			

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}
	
	public function getDriversByCid_get($cid){
		$this->load->model('driver/driver_model', '', true);

        $data = $this->db->get_where("drivers", ['company_id' => $cid])->result();

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}	

	public function getGasStations_get($station_name = null){
		$this->load->model('gas_station/gas_station_model', '', true);
		
        if(!empty($station_name)){

            $data = $this->db->get_where("gas_stations", ['name' => $station_name])->row_array();

        }else{

            $data = $this->db->order_by('id', 'DESC')->get("gas_stations")->result();

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}

	public function getLedgers_get($where = null){
		$this->load->model('account/account_model', '', true);
		
        if(!empty($where)){

            $data = $this->account_model->get_ledgers($where);

        }else{

            $data = $this->account_model->get_ledgers($where);

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}
	
	public function createDriver_get(){
		$this->load->model('account/account_model', '', true);
		
        $input = $this->input->post();

		$data = array(
			'name' =>$_REQUEST['name'],
			'address' =>$_REQUEST['address'],
			'state' =>$_REQUEST['state'],
			'country' =>$_REQUEST['country'],
			'postal_code' =>$_REQUEST['postal_code'],
			'email' =>$_REQUEST['email'],
			'phone' =>$_REQUEST['phone'],
			'unit_number' =>$_REQUEST['unit_number'],
			'licence_number' =>$_REQUEST['licence_number'],
			'company_id' =>$_REQUEST['company_id'],
			'date_created' =>$_REQUEST['date_created'],
			'date_modified' =>$_REQUEST['date_modified'],
		);
        $this->db->insert('drivers',$data);
		$output = (array("Status"=> 'true', 'results'=>'Driver created successfully.'));
        $this->response($output, REST_Controller::HTTP_OK);		
	}	

	public function createDriver_post(){
		$this->load->model('account/account_model', '', true);
		
        $input = $this->input->post();

        $this->db->insert('drivers',$input);

        $this->response(['Driver created successfully.'], REST_Controller::HTTP_OK);		
	}
	
    public function editDriver_get(){

        $input = $this->put();
		$data = array(
			'name' =>$_REQUEST['name'],
			'address' =>$_REQUEST['address'],
			'state' =>$_REQUEST['state'],
			'country' =>$_REQUEST['country'],
			'postal_code' =>$_REQUEST['postal_code'],
			'email' =>$_REQUEST['email'],
			'phone' =>$_REQUEST['phone'],
			'unit_number' =>$_REQUEST['unit_number'],
			'licence_number' =>$_REQUEST['licence_number'],
			'company_id' =>$_REQUEST['company_id'],
			'date_modified' =>$_REQUEST['date_modified'],
		);
		$this->db->set($data);
		$this->db->where(array('id'=>$_REQUEST['id']));
        //$this->db->update('drivers', $input, array('id'=>$id));
        $this->db->update('drivers');

        $this->response(['Driver updated successfully.'], REST_Controller::HTTP_OK);

    }	

    public function editDriver_put(){

        $input = $this->put();

        $this->db->update('drivers', $input, array('id'=>$id));

        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);

    }

    public function downloadInvoice_get($where=null){

		$this->load->model('account/account_model', '', true);
		
        if(!empty($where)){

            $data = $this->account_model->get_card_trans_by_cid($where);

        }else{

            $data = $this->account_model->get_card_trans_by_cid($where);

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);

    }

	/* public function downloadInvoiceLink_get($cid=null){

		$output = (array("Status"=> 'true', 'results'=>array($pdfFilePath, $pdfFilePath2)));
        $this->response($output, REST_Controller::HTTP_OK);

    } */
    public function invoiced_trans_get($cid=null){
		$where = null;
		$this->load->model('account/account_model', '', true);
		if(!empty($cid)){
			$data = $this->db->where('company_id', $cid)->order_by('id', 'DESC')->get("transaction_invoice")->result();
		}else{
			$getInvoices = $this->db->order_by('id', 'DESC')->get("transaction_invoice")->result();
			foreach($getInvoices as $getInvoicesItems){
				if($getInvoicesItems->company_id != 0){
					$getCompany = $this->db->select('company_name')->where('id', $getInvoicesItems->company_id)->get('users')->row();
					$data[] = array('id' => $getInvoicesItems->id,
									'billingOn' => $getInvoicesItems->billingOn,
									'invoice_id' => $getInvoicesItems->invoice_id,
									'invoice_date' => $getInvoicesItems->invoice_date,
									'billingCurrency' => $getInvoicesItems->billingCurrency,
									'status' => $getInvoicesItems->status,
									'grand_total' => $getInvoicesItems->grand_total,
									'date_created' => $getInvoicesItems->date_created,
									'company_assigned' => $getCompany->company_name,
									'company_id' => $getInvoicesItems->company_id,
									);
				}
			}			
		}	
        

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);

    }	

    public function tax_rate_get(){
        $data = $this->db->order_by('id', 'DESC')->get("tax")->result();

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}

	#################################### Money Code #########################################
    public function generateMoneyCode_get(){
		$this->load->library("NuSoap_lib");
		$this->soapclient = new nusoap_client(site_url('/EFS_WS/index/wsdl'), true);

		$clientToken = $this->soapclient->call('login', array('user'=>'HSINGH3', 'password' => 'Harry0044'));
		$xmlData =	"<clientId>$clientToken</clientId>
					 <contractId>".$_REQUEST['contractId']."</contractId>
					 <masterContractId>-1</masterContractId>
					 <amount>".$_REQUEST['amount']."</amount>
					 <feeType>0</feeType>
					 <issuedTo>".$_REQUEST['issuedTo']."</issuedTo>
					 <notes>".$_REQUEST['notes']."</notes>
					 <currency>".$_REQUEST['currency']."</currency>
		";
		$callStatus = $this->soapclient->call('issueMoneyCode', $xmlData);
		if(!empty($callStatus['code'])){	
			$data = array(
						'companyId' => $_REQUEST['companyId'], 
						'contractId' => $_REQUEST['contractId'], 
						'masterContractId' => '-1', 
						'driver_name' => $_REQUEST['driver_name'], 
						'unit_number' => $_REQUEST['unit_number'], 
						'amount' => $_REQUEST['amount'], 
						'issuedTo' => $_REQUEST['issuedTo'], 
						'notes' => $_REQUEST['notes'], 
						'currency' => $_REQUEST['currency'], 
						'moneyCode' => $callStatus['code'], 
						'moneyCodeId' => $callStatus['id'],						
						'invoice_status' => 0, 
						'date_created' => $_REQUEST['date_created'], 
						'date_modified' => $_REQUEST['date_modified']
						);
			$response = $this->db->insert('money_codes', $data);
		}
		if(isset($response)){
			$result = 'Generated';
		}else{
			$result = 'Something went wrong';
		}

		$output = (array("Status"=> 'true', 'results'=>$result));
        $this->response($output, REST_Controller::HTTP_OK);		
	}

	public function getMoneyCode_get(){
		$data = $this->db->select('moneyCode')->where('companyId', $_REQUEST['cid'])->order_by('id', 'DESC')->limit(1)->get("money_codes")->row();
		
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);			
	}
	
	public function getMoneyCodeInvoices_get(){
		if(!empty($_REQUEST['cid'])){
			$this->db->select('money_codes_invoices.*, users.company_name, users.company_email');
			$this->db->join('users', 'users.id=money_codes_invoices.companyId', 'LEFT');
			$data = $this->db->where(['money_codes_invoices.companyId'=> $_REQUEST['cid']])->order_by('money_codes_invoices.id', 'DESC')->get("money_codes_invoices")->result();
		}else{
			$this->db->select('money_codes_invoices.*, users.company_name, users.company_email');
			$this->db->join('users', 'users.id=money_codes_invoices.companyId', 'LEFT');
			$data = $this->db->order_by('money_codes_invoices.id', 'DESC')->get("money_codes_invoices")->result();
		}
		
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);			
	}

	public function getMoneyCodeNoninvoiced_get(){
		if(!empty($_REQUEST['cid'])){
			$data = $this->db->where(['companyId'=> $_REQUEST['cid']])->order_by('id', 'DESC')->get("money_codes")->result();
		}else{
			$data = $this->db->order_by('id', 'DESC')->get("money_codes")->result();			
		}
		
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);			
	}

	public function forgot_password_step_1_get(){
		$this->load->library('email');
		$this->load->model('auth/auth_model');
		$email_exists = $this->auth_model->validate_email(trim($_GET['email']));
		//print_r($email_exists);
		if(!empty($email_exists)){
				$vercode = mt_rand(100000, 999999);
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from('info@pridediesel.com');		
				$this->email->to($_GET['email']);
				$this->email->subject('Forgot Password Email');
				
				$message = '<p>Change/recover password OTP is below:-</p>';
				$message .= $vercode;
				$this->email->message($message);
				$this->email->send();
			foreach($email_exists as $email_existsItems){
				$uid = $email_existsItems->id;
			}				
			$data = array('id'=>$uid, 'vercode'=>$vercode);
		}else{
			$data = "email_doesnot_exists";
		}
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);			
	}
	
	public function forgot_password_step_2_get(){
		$this->load->model('auth/auth_model');
		$id = $_GET['id'];
		$password = $_GET['password'];
		
		$updateStatus = $this->auth_model->update_user_pass($id, md5($password));
		if($updateStatus == true){
			$data = 'password_updated';
		}else{
			$data = "error";
		}
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);			
	}	

}
?>