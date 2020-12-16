<?php
set_time_limit(0);
ini_set('memory_limit', '256M');
class Soap_client extends MY_controller {
	
/* 	protected $username = 'WS352369';
	protected $password = 'WEX2020'; */
/*Live Credenttials*/
	protected $username = 'HSINGH3';
	protected $password = 'Harry0044';	
    function __construct() {
        parent::__construct();
		date_default_timezone_set('America/Toronto');
        $this->load->library("NuSoap_lib");

        //$this->soapclient = new soapclient(site_url('EFS_WS/index/wsdl'), true); // Work with PHP Version Latest
        $this->soapclient = new nusoap_client(site_url('/EFS_WS/index/wsdl'), true);

        $err = $this->soapclient->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';

        }		
    }
	
    function index() {
		$clientToken = $this->soapclient->call('login', array('user'=>$this->username, 'password' => $this->password));
		$cardSummResult = $this->soapclient->call('getCardSummaries', array('clientId'=>$clientToken, 'request' => 'all'));

        // Check for a fault
        if ($this->soapclient->fault) {
            echo '<h2>Fault</h2><pre>';
            print_r($cardSummResult);
            echo '</pre>';
        } else {
            // Check for errors
            $err = $this->soapclient->getError();
            if ($err) {
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                // Display the result
                echo '<h2>Result</h2><pre>';
                print_r($cardSummResult);
            echo '</pre>';
            }
        }
    }
	
    function import_cards() {
		//ob_start();
		$this->load->model("card/card_model");
		
		$clientToken = $this->soapclient->call('login', array('user'=>$this->username, 'password' => $this->password));
		$cardSummResult = $this->soapclient->call('getCardSummaries', array('clientId'=>$clientToken, 'request' => 'all'));
		//echo $clientToken;
        // Check for a fault
        if ($this->soapclient->fault) {
            echo '<h2>Fault</h2><pre>';
            print_r($cardSummResult);
            echo '</pre>';
        } else {
            // Check for errors
            $err = $this->soapclient->getError();
            if ($err) {
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                // Display the result
                echo '<h2>Result</h2><pre>';
                //print_r($cardSummResult);
            echo '</pre>';
            }
        }
		//pre(count($cardSummResult['value']));die;
		$cardids = array();
				$allCardData = $this->db->select('card_number')->get('cards')->result();

				foreach($allCardData as $allCardDatas){
					$cardids[] = $allCardDatas->card_number;
				}
		//pre($cardSummResult['value']);die;		
		//Import Cards
		if(!empty($cardSummResult['value'])){
			for($i=0; $i<count($cardSummResult['value']);$i++){	
	
				if(in_array($cardSummResult['value'][$i]['cardNumber'], $cardids) == FALSE){
					$data['cardCompany'] = 'EFS';
					$data['card_number'] = $cardSummResult['value'][$i]['cardNumber'];
					$data['policy_number'] = $cardSummResult['value'][$i]['policyNumber'];
					if($cardSummResult['value'][$i]['status'] == 'ACTIVE'){
						$data['card_status'] = '1';
					}else if($cardSummResult['value'][$i]['status'] == 'INACTIVE'){
						$data['card_status'] = '0';
					}else if($cardSummResult['value'][$i]['status'] == 'HOLD'){
						$data['card_status'] = '2';
					}
					$getCompanyId = $this->db->where('efs_policy_id', $cardSummResult['value'][$i]['policyNumber'])->get('users')->row();
					if(!empty($getCompanyId->id)){
						$data['company_id'] = $getCompanyId->id;
					}
					//$data['unit_number'] = $cardSummResult['value'][$i]['unitNumber'];
					$data['date_created'] = date('Y-m-d h:i:s');
					$data['date_modified'] = date('Y-m-d h:i:s');
					$getSingleCard = $this->db->select('card_number')->where('card_number', $data['card_number'])->get('cards')->row();
					
					if(count($getSingleCard) < 1){
						//echo "Less than one";
						$this->db->insert('cards', $data);
					}
					//echo"Insert Section<br/>";pre($data);
					//pre($data);
					unset($data);
				}else{
					
					$data['policy_number'] = $cardSummResult['value'][$i]['policyNumber'];
					//$data['card_number'] = $cardSummResult['value'][$i]['cardNumber'];
					if($cardSummResult['value'][$i]['status'] == 'ACTIVE'){
						$data['card_status'] = '1';
					}else if($cardSummResult['value'][$i]['status'] == 'INACTIVE'){
						$data['card_status'] = '0';
					}else if($cardSummResult['value'][$i]['status'] == 'HOLD'){
						$data['card_status'] = '2';
					}
					$getCompanyId = $this->db->where('efs_policy_id', $cardSummResult['value'][$i]['policyNumber'])->get('users')->row();
					if(!empty($getCompanyId->id)){
						$data['company_id'] = $getCompanyId->id;
					}					
					$data['date_modified'] = date('Y-m-d h:i:s');				

					$this->db->set($data);
					$this->db->where('card_number', $cardSummResult['value'][$i]['cardNumber']);
					$this->db->update('cards');
					//echo"Update Section<br/>";pre($data);
					//pre($data);
					unset($data);
				}
				
			}
		
			
	}//die;
	//ob_end_clean();
	//die;
		$this->session->set_flashdata('success_msg', 'Import Complete');
		redirect(base_url().'card/index', 'refresh');
		//$this->load->view('modules/card/import', $this->data);	
    } 	
	
	function get_transaction_summ(){
		
		/* $usersNow = new DateTime('now', new DateTimeZone('+0320'));
		$makeEndDate = explode('+', $usersNow->format(DateTime::RFC3339)); */
		//pre(gmmktime());die;		
        $this->load->model("account/account_model");
		$lastInsertedTransaction = $this->db->select_max('transaction_date')->where('transactionAt', 'EFS')->get('transactions')->row();		
		$clientToken = $this->soapclient->call('login', array('user'=>$this->username, 'password' => $this->password));
		//$clientToken = $this->soapclient->call('login', array('user'=>'WS352369', 'password' => 'WEX2020'));
		$endDate = str_replace(" ", "T", date('Y-m-d H:i:s')).'.000-05:00';
		
		$makeDate = strtotime("-1 day", strtotime(date('Y-m-d H:i:s')));
		//$makeDate = '2020-09-20T23:59:59';
		//$endDate = str_replace(" ", "T", date("Y-m-d H:i:s"));
		//$endDate = $makeEndDate[0];
		//$endDate = date('Y-m-dTH:i:s');
		//echo $endDate;die;
		if(!empty($lastInsertedTransaction->transaction_date)){
			//$begDate = str_replace(" ", "T", $lastInsertedTransaction->date_modified);
			$begDate = str_replace(" ", "T", $lastInsertedTransaction->transaction_date.'.000-05:00');
		}else{
			$makeBgDate = strtotime("-1 day", strtotime(date('Y-m-d H:i:s', $makeDate)));
			$mkendDate = str_replace(" ", "T", date("Y-m-d H:i:s", $makeBgDate));
			$begDate = '2020-09-20T23:59:59.000-05:00';
			//$begDate = $mkendDate;
		}
		//$endDate = 	'2020-09-28T02:00:59.000-05:00';	
		//pre($mkendDate);die;
		/* $transSummResult = $this->soapclient->call('getTransExtLoc', array('clientId'=>$clientToken, 'begDate' => '2020-06-01T01:01:01', 'endDate' => '2020-07-15T01:01:01')); */
		$transSummResult = $this->soapclient->call('getMCTransExtLocV2', array('clientId'=>$clientToken, 'begDate' => $begDate, 'endDate' => $endDate));
		//echo $begDate."<br>";
		//echo $endDate;
		//print_r($clientToken);
        // Check for a fault
        if ($this->soapclient->fault) {
            echo '<h2>Fault</h2><pre>';
            print_r($transSummResult);
            echo '</pre>';
        } else {
            // Check for errors
            $err = $this->soapclient->getError();
            if ($err) {
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                // Display the result
                echo '<h2>Result</h2><pre>';
                //print_r($transSummResult);
				echo '</pre>';
            }
        }
		//pre($transSummResult['value'][0]['infos']);
		/* foreach($transSummResult['value'][0]['infos'] as $infoItems){
			if(!empty($infoItems['type']) && $infoItems['type'] == 'UNIT'){
				echo $infoItems['value'];
			}
		} */
		//die;
		//Get All transactions
		
		$allTransactionData = $this->account_model->getLastTransaction();
		if(!empty($allTransactionData)){
			foreach($allTransactionData as $allTransData){
				$transids[] = $allTransData->transaction_id;
				$transdate[] = $allTransData->transaction_date;
			}
		}
		//pre($transSummResult);
		//pre($this->is_multi($transSummResult['value'][0]));
		//die;
		//if(!empty($transSummResult['value']) && $this->is_multi($transSummResult['value']) === true){
		if(isset($transSummResult['value'][0])){
		for($i=0; $i<count($transSummResult['value']);$i++){
			$setCACompanyType = 'bronze';
			$setUSCompanyType = 'bronze';
			//pre($transSummResult['value'][$i]['cardNumber']);
			if($transSummResult['value'][$i]['billingCurrency'] == 'CAD'){
				/* Get Company Type and Company Pricing Type */
				$this->db->select('company_types.company_type, users.cad_pricing, users.usa_pricing');
				$this->db->join('cards', 'cards.company_id = users.id', 'LEFT');
				$this->db->join('company_types', 'company_types.id = users.company_type', 'LEFT');
				$this->db->where('cards.card_number', $transSummResult['value'][$i]['cardNumber']);
				$getCompanyType = $this->db->get('users')->row();
				//pre($getCompanyType);
				if(!empty($getCompanyType)){
					//$setCompanyType = strtolower($getCompanyType->company_type);
					//pre($this->db->last_query());
					$setCACompanyType = strtolower($getCompanyType->company_type);

					if($transSummResult['value'][$i]['billingCurrency'] == 'CAD'){
						//$setPricingType = 'add_on_efs';
						$setPricingType = strtolower($getCompanyType->cad_pricing);
					}
				}				
				if(empty($setPricingType)){
					$setPricingType = 'add_on_efs';
				}
			}
			if($transSummResult['value'][$i]['billingCurrency'] == 'USD'){
				/* Get Company Type and Company Pricing Type */
				$this->db->select('company_types.company_type, users.cad_pricing, users.usa_pricing');
				$this->db->join('cards', 'cards.company_id = users.id');
				$this->db->join('company_types', 'company_types.id = users.company_type');
				$this->db->where('cards.card_number', $transSummResult['value'][$i]['cardNumber']);
				$getCompanyType = $this->db->get('users')->row();
				
				if(!empty($getCompanyType)){
					//$setCompanyType = strtolower($getCompanyType->company_type);
					
					$setUSCompanyType = strtolower($getCompanyType->company_type);


					if($transSummResult['value'][$i]['billingCurrency'] == 'USD'){
						//$setPricingType = 'retail_price';
						$setPricingType = strtolower($getCompanyType->usa_pricing);
					}
				}				
					if(empty($setPricingType)){
						$setPricingType = 'retail_price';
					}
			}

			if(empty($setUSCompanyType)){
				$setUSCompanyType = 'bronze';
			}
			if(empty($setCACompanyType)){
				$setCACompanyType = 'bronze';
			}
			//pre($setCACompanyType);die;
			if(in_array($transSummResult['value'][$i]['transactionId'], $transids) == FALSE){
			//if($transSummResult['value'][$i]['transactionId'] !== $transid){
				
				/* --------------------------------  Insert Transactions --------------------------------*/
				//if(!empty($transSummResult['value'][$i]['transactionId']) && $transSummResult['value'][$i]['transactionId'] !== $transid){
					$unitNum = NULL;
					$data['billing_currency'] = $transSummResult['value'][$i]['billingCurrency'];
					$data['card_number'] = $transSummResult['value'][$i]['cardNumber'];
					$data['carrier_id'] = $transSummResult['value'][$i]['carrierId'];
					$data['contract_id'] = $transSummResult['value'][$i]['contractId'];
					$data['country'] = $transSummResult['value'][$i]['country'];
					$data['invoice'] = $transSummResult['value'][$i]['invoice'];
					if(!empty($transSummResult['value'][$i]['infos'])){
						foreach($transSummResult['value'][$i]['infos'] as $infoItems){
							if(!empty($infoItems['type']) && $infoItems['type'] == 'UNIT'){
								$unitNum = $infoItems['value'];
							}
						}	
					}		
					$data['unit_number'] = $unitNum;

					if(empty($transSummResult['value'][$i]['lineItems']['amount'])){
						$lineItems_Length = count($transSummResult['value'][$i]['lineItems']);
					}else{
						$lineItems_Length = 0;
					}
					if($lineItems_Length >0){
						$arrAmount = [];
						$arrCategory = [];
						$arrGroupCategory = [];
						$arrPpu = [];
						$arrPridePricePpu = [];
						$arrQuantity = [];
						$j = 0;
						while($j < $lineItems_Length) {
							$jsonAmountArrayObject = $transSummResult['value'][$i]['lineItems'][$j]['amount'];
							$jsonCategoryArrayObjectActual = $transSummResult['value'][$i]['lineItems'][$j]['category'];
							if($transSummResult['value'][$i]['lineItems'][$j]['category'] == 'ULSR'){
								$jsonCategoryArrayObject = 'ULSD';
							}else{
								$jsonCategoryArrayObject = $transSummResult['value'][$i]['lineItems'][$j]['category'];
							}
							//$productNames = $transSummResult['value'][$i]['lineItems'][$j]['category'];
							$exactCompanyPrice = 0;
							$exactDefdCompanyPrice=0;
							if($data['billing_currency'] == 'CAD' && !empty($setCACompanyType)){	
								$getPricingCA = $this->db->get('pricelist_edit_ca')->row();
								if($getPricingCA->$setPricingType){
									$decodePrices = json_decode($getPricingCA->$setPricingType);//pre($decodePrices);
									foreach($decodePrices as $decodePricesRows){
										if(array_key_exists($jsonCategoryArrayObject, $decodePricesRows)){
										$gasStationState = trim($decodePricesRows->$jsonCategoryArrayObject[0]->state[0]);
												if($gasStationState == $transSummResult['value'][$i]['locationState'] && $decodePricesRows->$jsonCategoryArrayObject[0]->gas_station[0] == $transSummResult['value'][$i]['locationId']){
														$defdProd = 'defd_'.$setCACompanyType;
														$exactDefdCompanyPrice = $decodePricesRows->$jsonCategoryArrayObject[0]->$defdProd[0];							
														$exactCompanyPrice = $decodePricesRows->$jsonCategoryArrayObject[0]->$setCACompanyType[0];
												}
										}
									}
									if($getPricingCA->defd_price){
										$decodeDefdPrices = json_decode($getPricingCA->defd_price);
										foreach($decodeDefdPrices as $decodeDefdPricesRows){
											if(array_key_exists($setCACompanyType, $decodeDefdPricesRows)){
												$defdOurPrice = $decodeDefdPricesRows->$setCACompanyType[0];
												if($jsonCategoryArrayObject == 'DEFD'){
													$exactCompanyPrice = $transSummResult['value'][$i]['lineItems'][$j]['retailPPU'] + $defdOurPrice;
												}
											}
										}
									}									
								}
							}else{
								$getPricingUS = $this->db->get('pricelist_edit_us')->row();
								if($getPricingUS->$setPricingType){
									$decodePrices = json_decode($getPricingUS->$setPricingType);//pre($decodePrices);
									foreach($decodePrices as $decodePricesRows){
										if(array_key_exists($jsonCategoryArrayObject, $decodePricesRows)){
											
										$gasStationState = trim($decodePricesRows->$jsonCategoryArrayObject[0]->state[0]);
										$gasStationName = $transSummResult['value'][$i]['locationId'];
												if($gasStationState == $transSummResult['value'][$i]['locationState'] && $decodePricesRows->$jsonCategoryArrayObject[0]->gas_station[0] == $gasStationName){										
													$exactCompanyPrice = $decodePricesRows->$jsonCategoryArrayObject[0]->$setUSCompanyType[0];
												}
										}
									}
									if($getPricingUS->defd_price){
										$decodeDefdPrices = json_decode($getPricingUS->defd_price);
										foreach($decodeDefdPrices as $decodeDefdPricesRows){
											if(array_key_exists($setUSCompanyType, $decodeDefdPricesRows)){
												$defdOurPrice = $decodeDefdPricesRows->$setUSCompanyType[0];
												if($jsonCategoryArrayObject == 'DEFD'){
													$exactCompanyPrice = number_format($transSummResult['value'][$i]['lineItems'][$j]['retailPPU'] + $defdOurPrice, 3);
												}
											}
										}
									}									
								}					
							}	
							
							$jsonGroupCategoryArrayObject = $transSummResult['value'][$i]['lineItems'][$j]['groupCategory'];
							$jsonPpuArrayObject = $transSummResult['value'][$i]['lineItems'][$j]['retailPPU'];

							if($exactCompanyPrice == 0){
								$exactCompanyPrice = $transSummResult['value'][$i]['lineItems'][$j]['retailPPU'];
							}
							$prideDiesel_ppu = $exactCompanyPrice;
							
							$jsonQuantityArrayObject = $transSummResult['value'][$i]['lineItems'][$j]['quantity'];
							//unset($exactCompanyPrice);
							$arrAmount[$j] = $jsonAmountArrayObject;
							$arrCategory[$j] = $jsonCategoryArrayObjectActual;
							$arrGroupCategory[$j] = $jsonGroupCategoryArrayObject;
							$arrPpu[$j] = $jsonPpuArrayObject;
							$arrPridePricePpu[$j] = $prideDiesel_ppu;
							$arrQuantity[$j] = $jsonQuantityArrayObject;
							
							$j++;
								
						}
						$descr_of_amount_array = json_encode($arrAmount);
						$descr_of_category_array = json_encode($arrCategory);
						$descr_of_groupcategory_array = json_encode($arrGroupCategory);
						$descr_of_ppu_array = json_encode($arrPpu);
						$descr_of_pride_price_ppu_array = json_encode($arrPridePricePpu);
						$descr_of_quantity_array = json_encode($arrQuantity);
					}else{
						$descr_of_amount_array = json_encode(array($transSummResult['value'][$i]['lineItems']['amount']));
						$descr_of_category_array = json_encode(array($transSummResult['value'][$i]['lineItems']['category']));
						if($transSummResult['value'][$i]['lineItems']['category'] == 'ULSR'){
							$productName = 'ULSD';
						}else{
							$productName = $transSummResult['value'][$i]['lineItems']['category'];
						}
						
							$exactCompanyPrice = 0;
							if($data['billing_currency'] == 'CAD' && !empty($setCACompanyType)){					
								$getPricingCA = $this->db->get('pricelist_edit_ca')->row();
								if($getPricingCA->$setPricingType){
									$decodePrices = json_decode($getPricingCA->$setPricingType);
									foreach($decodePrices as $decodePricesRows){
										if(array_key_exists($productName, $decodePricesRows)){
											$gasStationState = trim($decodePricesRows->$productName[0]->state[0]);
											if($gasStationState == $transSummResult['value'][$i]['locationState'] && $decodePricesRows->$productName[0]->gas_station[0] == $transSummResult['value'][$i]['locationId']){
												$exactCompanyPrice = $decodePricesRows->$productName[0]->$setCACompanyType[0];
											}
										}
									}
									if($getPricingCA->defd_price){
										$decodeDefdPrices = json_decode($getPricingCA->defd_price);
										foreach($decodeDefdPrices as $decodeDefdPricesRows){
											if(array_key_exists($setCACompanyType, $decodeDefdPricesRows)){
												$defdOurPrice = $decodeDefdPricesRows->$setCACompanyType[0];
												if($productName == 'DEFD'){
												$exactCompanyPrice = $transSummResult['value'][$i]['lineItems']['retailPPU'] + $defdOurPrice;
												}
											}
										}
									}	
								}
							}
							if($data['billing_currency'] == 'USD' && !empty($setUSCompanyType)){
								$getPricingUS = $this->db->get('pricelist_edit_us')->row();
								if($getPricingUS->$setPricingType){
									$decodePrices = json_decode($getPricingUS->$setPricingType);
									foreach($decodePrices as $decodePricesRows){
										if(array_key_exists($productName, $decodePricesRows)){
											$gasStationState = trim($decodePricesRows->$productName[0]->state[0]);
											if($gasStationState == $transSummResult['value'][$i]['locationState'] && $decodePricesRows->$productName[0]->gas_station[0] == $transSummResult['value'][$i]['locationId']){											
												$exactCompanyPrice = $decodePricesRows->$productName[0]->$setUSCompanyType[0];
											}
										}
									}
									if($getPricingUS->defd_price){
										$decodeDefdPrices = json_decode($getPricingUS->defd_price);
										foreach($decodeDefdPrices as $decodeDefdPricesRows){
											if(array_key_exists($setUSCompanyType, $decodeDefdPricesRows)){
												$defdOurPrice = $decodeDefdPricesRows->$setUSCompanyType[0];
												if($productName == 'DEFD'){
													$exactCompanyPrice = $transSummResult['value'][$i]['lineItems']['retailPPU'] + $defdOurPrice;
												}
											}
										}
									}									
								}					
							}
						if($exactCompanyPrice == 0){
							$exactCompanyPrice = $transSummResult['value'][$i]['lineItems']['retailPPU'];
						}		
						$descr_of_groupcategory_array = json_encode(array($transSummResult['value'][$i]['lineItems']['groupCategory']));

						$efsRetailPrice = $transSummResult['value'][$i]['lineItems']['retailPPU'];

						$prideDiesel_ppu = $exactCompanyPrice;
						//unset($exactCompanyPrice);
						$descr_of_pride_price_ppu_array = json_encode(array(number_format($prideDiesel_ppu, 3)));
						$descr_of_ppu_array = json_encode(array($efsRetailPrice));
						$descr_of_quantity_array = json_encode(array($transSummResult['value'][$i]['lineItems']['quantity']));					
					}
					//$this->db->where()
				
					$data['transactionAt'] = 'EFS';				
					$data['amount'] = $descr_of_amount_array;				
					$data['category'] = $descr_of_category_array;
					$data['group_category'] = $descr_of_groupcategory_array;
					$data['unit_price'] = $descr_of_ppu_array;
					$data['pride_price'] = $descr_of_pride_price_ppu_array;
					$data['quantity'] = $descr_of_quantity_array;
					$data['gas_station_id'] = $transSummResult['value'][$i]['locationId'];
					$data['gas_station_name'] = $transSummResult['value'][$i]['locationName'];
					$data['gas_station_state'] = $transSummResult['value'][$i]['locationState'];
					$data['gas_station_city'] = $transSummResult['value'][$i]['locationCity'];
					$dateTimeExplode = explode('T', $transSummResult['value'][$i]['transactionDate']);
					$dateTimeExplode2 = explode('.', $dateTimeExplode[1]);
					$data['transaction_date'] = $dateTimeExplode[0]." ".$dateTimeExplode2[0];
					$data['transaction_id'] = $transSummResult['value'][$i]['transactionId'];
					$data['transaction_type'] = $transSummResult['value'][$i]['transactionType'];
					$data['date_created'] = date('Y-m-d h:i:s');
					$data['date_modified'] = date('Y-m-d h:i:s');
					//pre($data);die;
					/* Insert all transactions in Transactions table */
					$this->db->insert('transactions', $data);
					echo "Success";
				}
			//}
		}
	}else{
		echo "No Transaction Pending.";
	}
				
	}	
	
    function set_card_pin($cardNum, $cardPin) {
		//$clientToken = $this->soapclient->call('login', array('user'=>$this->username, 'password' => $this->password));
		$setPin = $this->soapclient->call('setCardPin', array('clientId'=>$clientToken, 'cardNum' => $cardNum, 'newPin' => $cardPin));
		
        // Check for a fault
        if ($this->soapclient->fault) {
            echo '<h2>Fault</h2><pre>';
            print_r($setPin);
            echo '</pre>';
        } else {
            // Check for errors
            $err = $this->soapclient->getError();
            if ($err) {
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                // Display the result
                echo '<h2>Result</h2><pre>';
                print_r($setPin);
            echo '</pre>';
            }
        }	
    }

    function get_single_card() {
		$clientToken = $this->soapclient->call('login', array('user'=>$this->username, 'password' => $this->password));
		
        $cardResult = $this->soapclient->call('getCard', array('clientId'=>$clientToken, 'cardNumber' => '7083052035236900033'));
		
        // Check for a fault
        if ($this->soapclient->fault) {
            echo '<h2>Fault</h2><pre>';
            print_r($cardResult);
            echo '</pre>';
        } else {
            // Check for errors
            $err = $this->soapclient->getError();
            if ($err) {
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                // Display the result
                echo '<h2>Result</h2><pre>';
                print_r($cardResult);
            echo '</pre>';
            }
        }	
    }

    function get_products() {
		$clientToken = $this->soapclient->call('login', array('user'=>$this->username, 'password' => $this->password));
		
        $cardResult = $this->soapclient->call('getProducts', array('clientId'=>$clientToken));
		
        // Check for a fault
        if ($this->soapclient->fault) {
            echo '<h2>Fault</h2><pre>';
            print_r($cardResult);
            echo '</pre>';
        } else {
            // Check for errors
            $err = $this->soapclient->getError();
            if ($err) {
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                // Display the result
                echo '<h2>Result</h2><pre>';
                //print_r($cardResult);
				$output = $cardResult['value'];
					?>
					<table border="1">
						<tr><th>CODE</th><th>Full Name</th></tr>
					<?php				
				foreach($output as $outputrows){
					echo '<tr><td>'.$outputrows['code'].'</td><td>'.$outputrows['description'].'</td></tr>';
				}
					?>
						
					</table>
					<?php				
            echo '</pre>';
            }
        }	
    }	


}