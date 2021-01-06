<?php
set_time_limit(0);
class Soap_client_husky extends MY_controller {
	
    function __construct() {
        parent::__construct();
		//date_default_timezone_set('America/Toronto');
    }
	
    function index() {
		error_reporting(0);
		$this->load->model("card/card_model");
		$this->load->model("settings/settings_model");
		//Get All cards
		$allCardData = $this->card_model->get_cards();
		$cardids = array();
		foreach($allCardData as $allCardDatas){
			$cardids[] = $allCardDatas->cardToken;
		}
		$getCredentials = $this->settings_model->get('husky');
		$HuskyUser = $HuskyPassword = null;
		foreach($getCredentials as $getCredentialsItems){
			$HuskyUser = $getCredentialsItems->username;
			$HuskyPassword = $getCredentialsItems->password;
		}	
		/* $getHuskyUsers = $this->db->select('customer_id')->where('customer_id != ""')->get('users')->result();
		foreach($getHuskyUsers as $getHuskyUsersRows){
			$customerIds[] = $getHuskyUsersRows->customer_id;
		}
		$customerIdMult = array_chunk($customerIds, 2); */
		//pre($getCredentials);die;
		$ws_url = 'https://api.iconnectdata.com:443/FleetCreditWS/services/FleetCreditWS0200';
		$headers = array(
			"Content-type: application/xml;charset=utf-8",
			
			'Authorization: Basic '. base64_encode($HuskyUser.':'.$HuskyPassword),
			'Connection: keep-alive'.
			'Content-Encoding: gzip',
			"SOAPAction: http://fleetCredit02.comdata.com/FleetCreditWS0200/cardListing",
		);
		
	//for($count=0; $count<count($customerIdMult); $count++){
		//$xml_post_string1 = '';	

		
		$xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:main="http://fleetCredit02.comdata.com/maintenance/">
		  <soapenv:Header>
			<wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				<wsse:UsernameToken>
					<wsse:Username>'.$HuskyUser.'</wsse:Username>
					<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$HuskyPassword.'</wsse:Password>
				</wsse:UsernameToken>
			</wsse:Security>
		</soapenv:Header>
		   <soapenv:Body>
			  <main:CardListingRequest>
				 <acctCustidList>
					<!--0 to 20 repetitions:-->
					<acctCustidElement>
					   <accountCode>1-14O</accountCode>
					   <custIdList>
						  <!--0 to 20 repetitions:-->';
			/* foreach($customerIdMult[$count] as $expids){
				//$xml_post_string1 .=  "<customerId>$expids</customerId>\n";
				
			} */
			//echo $xml_post_string1;
			//pre($xml_post_string);die;
			/* $xml_post_string .= '<customerId>CKHPX</customerId>
                  <customerId>CKHPY</customerId>
			   <customerId>CKHPZ</customerId>';	 */		
			$xml_post_string .= '</custIdList>
					</acctCustidElement>
				 </acctCustidList>
				 <maskCardFlag/>
				 <status/>
				 <sortOption/>
				 <maxRows/>
				 <pageNumber/>
			  </main:CardListingRequest>
		   </soapenv:Body>
		</soapenv:Envelope>';
			/* foreach($customerIdMult[$count] as $expids){
				$xml_post_string1 .=  "<customerId>$expids</customerId>";
				
			} */		
		$curl = curl_init();
		//pre($xml_post_string1); 
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $ws_url,
			CURLOPT_HTTPHEADER => $headers,
		  CURLOPT_RETURNTRANSFER => true,
		  //CURLOPT_ENCODING => "",
		  //CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  //CURLOPT_FOLLOWLOCATION => true,
		  //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>$xml_post_string,

		));

		$response = curl_exec($curl);

		curl_close($curl);
		// converting
            $response1 = str_replace('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><soapenv:Header/><soapenv:Body>',"",$response);
            $response2 = str_replace("</soapenv:Body></soapenv:Envelope>","",$response1);
		// convertingc to XML
            $parser = simplexml_load_string($response2);
			
			
	//}
	
		//foreach($parser->records->card as $cardRows){
		for($i=0; $i<count($parser->records->card);$i++){	
			if(in_array($parser->records->card[$i]->cardToken, $cardids) == FALSE){
					
					$cardNum = $parser->records->card[$i]->cardNumber;
					$cardToken = $parser->records->card[$i]->cardToken;
					$cardStatus = $parser->records->card[$i]->cardStatus;
					$policyNumber = $parser->records->card[$i]->customerId;

						$data = array(
								'cardCompany' => 'HUSKY',
								'card_number'=>(string)$cardNum,
								'cardToken'=>(string)$cardToken,
								'policy_number'=>(string)$policyNumber,
								'date_created' => date('Y-m-d h:i:s'),
								'date_modified' => date('Y-m-d h:i:s')
										
								);
				$data['card_status'] = '';				
				if((string)$cardStatus == 'A'){
					/*Active*/
					$data['card_status'] .= '1';
				}else if((string)$cardStatus == 'B'){
					/*Blocked*/
					$data['card_status'] .= '3';
				}else if((string)$cardStatus == 'C'){
					/*Clear*/
					$data['card_status'] .= '4';
				}else if((string)$cardStatus == 'F'){
					/*Fraud*/
					$data['card_status'] .= '5';
				}else if((string)$cardStatus == 'L'){
					/*Lost*/
					$data['card_status'] .= '6';
				}else if((string)$cardStatus == 'S'){
					/*Stolen*/
					$data['card_status'] .= '7';
				}else if((string)$cardStatus == 'X'){
					/*Permanent Blocked*/
					$data['card_status'] .= '8';
				}								
				/*Insert Husky cards data in cards table*/
				$this->db->insert('cards', $data);
			}else{
					$cardNum = $parser->records->card[$i]->cardNumber;
					$cardToken = $parser->records->card[$i]->cardToken;
					$cardStatus = $parser->records->card[$i]->cardStatus;
					$policyNumber = $parser->records->card[$i]->customerId;

						$data = array(
								'cardCompany' => 'HUSKY',
								'card_number'=>(string)$cardNum,
								'cardToken'=>(string)$cardToken,
								'policy_number'=>(string)$policyNumber,
								'date_created' => date('Y-m-d h:i:s'),
								'date_modified' => date('Y-m-d h:i:s')
										
								);
				$data['card_status'] = '';				
				if((string)$cardStatus == 'A'){
					/*Active*/
					$data['card_status'] .= '1';
				}else if((string)$cardStatus == 'B'){
					/*Blocked*/
					$data['card_status'] .= '3';
				}else if((string)$cardStatus == 'C'){
					/*Clear*/
					$data['card_status'] .= '4';
				}else if((string)$cardStatus == 'F'){
					/*Fraud*/
					$data['card_status'] .= '5';
				}else if((string)$cardStatus == 'L'){
					/*Lost*/
					$data['card_status'] .= '6';
				}else if((string)$cardStatus == 'S'){
					/*Stolen*/
					$data['card_status'] .= '7';
				}else if((string)$cardStatus == 'X'){
					/*Permanent Blocked*/
					$data['card_status'] .= '8';
				}								
				/*Insert Husky cards data in cards table*/
				//$this->db->insert('cards', $data);
					$this->db->set($data);
					$this->db->where('card_number', $cardNum);
					$this->db->update('cards');				
			}
				
		}
		$this->session->set_flashdata('success_msg', 'Import Complete');
		redirect(base_url().'card/index', 'refresh');		
    }
	
	function updateCardStatus(){
		$this->load->model("card/card_model");
		
	}

	function importHuskyTrans(){
		$this->load->model("card/card_model");
		//Get All cards
		$allCardData = $this->card_model->get_cards();
		$cardids = array();
		foreach($allCardData as $allCardDatas){
			$cardids[] = $allCardDatas->cardToken;
		}		
		/* $getHuskyUsers = $this->db->select('customer_id')->where('customer_id != ""')->get('users')->result();
		foreach($getHuskyUsers as $getHuskyUsersRows){
			$customerIds[] = $getHuskyUsersRows->customer_id;
		}
		$customerIdMult = array_chunk($customerIds, 2); */
		
		$ws_url = 'https://w6.iconnectdata.com/FleetCreditWS/services/FleetCreditWS0200';
		$headers = array(
			"Content-type: application/xml;charset=utf-8",
			
			'Authorization: Basic '. base64_encode('EXT1-14OPROD:W!ZGg7CQ'),
			'Connection: keep-alive'.
			'Content-Encoding: gzip',
			"SOAPAction: http://fleetCredit02.comdata.com/FleetCreditWS0200/cardListing",
		);
		
	//for($count=0; $count<count($customerIdMult); $count++){
		//$xml_post_string1 = '';	

		
		$xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:main="http://fleetCredit02.comdata.com/maintenance/">
		  <soapenv:Header>
			<wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				<wsse:UsernameToken>
					<wsse:Username>EXT1-14OPROD</wsse:Username>
					<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">W!ZGg7CQ</wsse:Password>
				</wsse:UsernameToken>
			</wsse:Security>
		</soapenv:Header>
		   <soapenv:Body>
			        <main:RtTransactionRequest>
					 <criteria>
						<accountCode>1-14O</accountCode>
						<customerIds>
						   <!--Zero or more repetitions:-->
						</customerIds>
						<transactionStatuses>
						   <!--Zero or more repetitions:-->
						</transactionStatuses>
						<sortByDateType>Transaction</sortByDateType>
						<selectionDateType>Transaction</selectionDateType>
						<startDate>2020-09-25</startDate>
						<startTime></startTime>
						<endDate></endDate>
						<endTime></endTime>
					 </criteria>
					 <pageNbr>1</pageNbr>
				  </main:RtTransactionRequest>
		   </soapenv:Body>
		</soapenv:Envelope>';
			/* foreach($customerIdMult[$count] as $expids){
				$xml_post_string1 .=  "<customerId>$expids</customerId>";
				
			} */		
		$curl = curl_init();
//pre($xml_post_string1); 
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $ws_url,
			CURLOPT_HTTPHEADER => $headers,
		  CURLOPT_RETURNTRANSFER => true,
		  //CURLOPT_ENCODING => "",
		  //CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  //CURLOPT_FOLLOWLOCATION => true,
		  //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>$xml_post_string,

		));

		$response = curl_exec($curl);

		curl_close($curl);
			// converting
            $response1 = str_replace('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><soapenv:Header/><soapenv:Body>',"",$response);
            $response2 = str_replace("</soapenv:Body></soapenv:Envelope>","",$response1);
			// convertingc to XML
            $parser = simplexml_load_string($response2);
	
			pre($parser);	
	}
}

