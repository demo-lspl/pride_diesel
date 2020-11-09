<?php

class Cronjob extends MY_Controller
{
	
    public function __construct()
    {
        parent::__construct();
		date_default_timezone_set( 'Asia/Kolkata' );
    }
    
	public function index()
	{
		echo 'Hello, cronjob here!';
        exit();
	}

    public function generate_transaction_invoice()
    {
		
		error_reporting(E_ALL^E_NOTICE);
		/* $data = array('val1'=>'I', 'val2'=>'am', 'val3'=>'jagdish');
		$this->db->insert('testcron', $data); */
		$this->load->model('account/account_model');
		$this->load->model('user/user_model');
		$this->load->model('card/card_model');
		
		//$expiration_to_date = date('Y-m-d H:i:s', time()-2*86400);
		$this->db->select('users.invoice_schedule, users.id,  transactions.date_created as transinvoicedate');
		/* $this->db->select('transaction_invoice.date_created as lastinvoicedate, users.invoice_schedule, users.id,  transactions.date_created as transinvoicedate'); */
		//$this->db->join('transaction_invoice', 'transaction_invoice.company_id=users.id', 'right');
		$this->db->join('cards', 'cards.company_id=users.id', 'right');
		$this->db->join('transactions', 'transactions.card_number=cards.card_number', 'right');

		$this->db->where('role !=', 'admin');
		$this->db->group_by('users.id');
		//$this->db->order_by('transaction_invoice.id', 'DESC');
		$query = $this->db->get('users');
		$results = $query->result();
		
		$users = $this->user_model->get_users();
		$newDate = new DateTime(date('Y-m-d H:i:s'));
		$firstDate = strtotime(date('Y-m-d H:i:s'));
		$secondDate = strtotime('2020-07-20 12:11:10');
		$datediff = abs($secondDate - $firstDate);
		$day = floor($datediff/(60*60*24));
		
		foreach($results as $usersRow){
			$this->db->select('date_created');
			$this->db->where('company_id', $usersRow->id);
			$this->db->order_by('id', 'DESC');
			$transDate = $this->db->get('transaction_invoice')->row();
			$invoiceSchedule = $usersRow->invoice_schedule;
			
			if($invoiceSchedule == 'daily'){
				if($transDate->date_created != NULL){
				$latsInvoiceDate = new DateTime($transDate->date_created);
				$lastInvoiced = $newDate->diff($latsInvoiceDate);//pre($lastInvoiced->days);die;
				//echo $usersRow->lastinvoicedate."=".$lastInvoiced->days."<br>";
				//pre($lastInvoiced);
				if($lastInvoiced->days > 0 ){
					$this->generate_trans_invoice($usersRow->id);
					echo "Not Empty lastinvoicedate".$usersRow->id;					
				}
				//echo $invoiceSchedule;
				}else{
					$this->generate_trans_invoice($usersRow->id);
					echo "Empty lastinvoicedate".$usersRow->id;
				}
				
			}
			
			if($invoiceSchedule == 'weekly'){
				if($transDate->date_created != NULL){
				$latsInvoiceDate = new DateTime($transDate->date_created);
				$lastInvoiced = $newDate->diff($latsInvoiceDate);
				//echo $usersRow->lastinvoicedate."=".$lastInvoiced->days."<br>";
				if($lastInvoiced->days > 6 ){
					$this->generate_trans_invoice($usersRow->id);
					echo "Not Empty lastinvoicedate".$usersRow->id;					
				}
				//echo $invoiceSchedule;
				}else{
					$this->generate_trans_invoice($usersRow->id);
					echo "Empty lastinvoicedate".$usersRow->id;
				}
				
			}			

		}

		exit();
	}

	public function generate_trans_invoice($cid){
		$this->load->model('account/account_model');
		$this->load->model('user/user_model');
		$this->load->model('card/card_model');		
		require_once(APPPATH.'libraries/tcpdf/tcpdf.php');  
		//$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
		$count=0;
		$vv = 0;
		$this->data['cardsTransData'] = $this->account_model->get_card_trans_by_cid($cid);
		foreach($this->data['cardsTransData'] as $key=>$transValues){
			if($transValues->billing_currency == 'USD'){
				$usdTrans[$vv] = $transValues;
			}
			if($transValues->billing_currency == 'CAD'){
				$cadTrans[$vv] = $transValues;
			}			
			$vv++;
		}
	
		if(!empty($usdTrans)){
		$custom_layout = array('350', '350');
		$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
		
		$obj_pdf->SetCreator(PDF_CREATOR);  
		$obj_pdf->SetTitle("Transaction Invoice Data");  
		$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
		$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
		$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
		$obj_pdf->SetDefaultMonospacedFont('helvetica');  
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
		$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
		$obj_pdf->setPrintHeader(false);  
		$obj_pdf->setPrintFooter(true);  
		$obj_pdf->SetAutoPageBreak(TRUE, 10);  
		$obj_pdf->SetFont('helvetica', '', 9);
		
		$image = FCPATH .'assets/images/pride-diesel-logo.png';
		$maxInvoiceId = $this->db->select_max('id')->get('transaction_invoice')->row();
		empty($maxInvoiceId->id)?$invoiceID = 1:$invoiceID = $maxInvoiceId->id + 1;
		//Insert invoice data in transaction invoice table
		$data['invoice_id'] = "CL".$invoiceID;
		$data['company_id'] = $cid;
		$data['invoice_date'] = date('Y-m-d');

		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_modified'] = date('Y-m-d H:i:s');
		
		$this->db->join('company_types', 'company_types.id = users.company_type');
		$getCompanyType = $this->db->where('users.id', $cid)->get('users')->row();

		//$this->db->insert('transaction_invoice', $data);
		$companyDetails = $this->db->where('id', $cid)->get('users')->row();	
		$obj_pdf->AddPage();
		
			$content = '';
			$content .= '<div style="width:1200px; margin:0px auto;">';
			$content .= '
			<table border="0">
				<tr>
					<td style="width: 20%"><img src="'.$image.'" alt="company_logo" width="160" height="50" border="0"/></td>
					<td style="width:40%;">
						<span style="font-size: 18px; font-weight: 600;margin:0;">Pride Diesel</span><br /><br />
						Address: 6050 Dixie Road, Mississauga, <br />ON L5T 1A6<br />
						Office: 888-558-3745<br />
						Fax: (866) 865-4596<br />
						www.pridediesel.com<br />
						Email: info@pridediesel.com
					</td>
					<td style="width:40%;">
						<span style="font-size: 25px; font-weight: 600; ">Client info</span><br /><br />
						<span style="font-size: 13px; font-weight: 600; ">'.$companyDetails->company_name .'</span><br />
						<span style="margin:0;">Address: '.$companyDetails->address .'</span><br />
						
						<span style="margin:0;">Email: '.$companyDetails->company_email .'</span>
					</td>
				</tr>
			</table><div style="margin-top: 20px; clear:both;"></div>';
			$company_email_usd = $companyDetails->company_email;
			$dueDate = date('Y-m-d', strtotime('+3 days'));
			$content .= '
			<table border="0">
				<tr>
					<td style="width:50%; float:left;">
						<table style="border: 1px solid #dcdcdc; clear: both; margin-bottom:20px; border-collapse:collapse; width: 100%; font-size: 10px;" cellspacing="0" cellpadding="3">  
						   <tr style="background-color:#CECECE;">  
								<th width="20%" style="font-size:10px;border-bottom: 1px solid #dcdcdc;">Invoice Number</th>  
								<th width="20%" style="font-size:10px;border-bottom: 1px solid #dcdcdc;">Invoice Date</th>    
								<th width="20%" style="font-size:10px;border-bottom: 1px solid #dcdcdc;">Due Date</th>   
						   </tr>  
							<tr>
								<td style="border-right: 1px solid #dcdcdc;">'.$data['invoice_id'].'</td>
								<td style="border-right: 1px solid #dcdcdc;">'.$data['invoice_date'].'</td>
								<td>'.$dueDate.'</td>
							</tr>
						</table>
					</td>
					<td style="50%">
					</td>
				</tr>	
			</table><br /></div>';
			//$this->db->select('cards.*');		
			$this->db->join('users', 'users.id = cards.company_id');
			$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'USD', 'transactions.invoice_status'=>0));
			$this->db->group_by('transactions.card_number');
			$cardCount = $this->db->get('cards')->result();
			foreach($cardCount as $cardRows){
			$this->db->select('cards.*, users.*, transactions.id as transid, transactions.*');		
			$this->db->join('users', 'users.id = cards.company_id');
			$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('transactions.card_number'=>$cardRows->card_number, 'cards.company_id'=> $cid, 'transactions.billing_currency'=> 'USD', 'transactions.invoice_status'=>0));
			$usdCardValues = $this->db->get('cards')->result();
			//foreach($usdCardValues as $cardRows){
			$explodeDateTime = explode(' ', $cardRows->transaction_date);	
			$transactionDate = $explodeDateTime[0];		
			$transactionTime = $explodeDateTime[1];		
			$content .= '<h3>Transactions for card: '.$cardRows->card_number.'</h3>
			  <table style="border: 1px solid #dcdcdc; border-collapse:collapse; font-size: 8px;" cellpadding="3"> 
				<tr  style="background-color:#CECECE;">  
					<th colspan="5" style="font-size:9px;text-align:center;border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Transaction Details</th>  
					<th colspan="7" style="font-size:9px;text-align:center;border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Fuel</th>  
					<th colspan="4" style="font-size:9px;text-align:center;border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Non Fuel Products(With Tax)</th>  
					<th  style="font-size:11px;border-bottom: 1px solid #dcdcdc;"></th> 
				</tr>
				   <tr style="background-color:#CECECE;">  
						<th width="11%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Card #</th>  
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Driver Name</th>  
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">DATE </th>  
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Site Name</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Site#</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Product</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Retail Price</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Savings rate</th>     
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Trac qty</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">RFR qty</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Sales tax</th>   
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Total qty</th>   
						<th width="5%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Def qty</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Def Tax AMT</th>
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Other Code</th>			
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Cash</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;">Final AMT </th>   
				   </tr>
				   <tr  style="background-color:#CECECE;">  
						<th width="11%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Transaction#</th>  
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Vehicle#</th>  
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Time(EDT)</th>  
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Site city</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">State</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">UOM</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Billed Price</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Discount,$</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Trac PreTax</th>   
						<th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">RFR PreTax</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">QST</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Total AMT</th>   
						<th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">DEF Price</th>   
						<th width="5%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">DEF AMT</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Other AMT</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Dash Cash</th>   
						<th width="6%" style="border-bottom: 1px solid #dcdcdc;">Currency</th>   
				   </tr><style>.even-bg {
			background-color: #ededed;
		}</style>';
		$companyPricingType = $this->db->where('id', $cid)->get('users')->row();
		$usdCount = 1;
		$totalDiscount = 0;$grandTotalAmount = 0; $totalQuantity = 0;
		foreach($usdCardValues as $usdCardRows){
		$jsonDecodeIncrement = 0;	
		$jsonDecodeAmount 				= json_decode($usdCardRows->amount);   
		$jsonDecodeProduct 				= json_decode($usdCardRows->category);   
		$jsonDecodeProductCategory 		= json_decode($usdCardRows->group_category);   
		$jsonDecodeUnitPrice 			= json_decode($usdCardRows->unit_price);   
		$jsonDecodeQuantity 			= json_decode($usdCardRows->quantity);
		
		/* Set Invoice_status as 1 */
		$this->db->where('id', $usdCardRows->transid);
		$this->db->set('invoice_status', 1);
		$this->db->update('transactions');   		

		for($usdProduct=0; $usdProduct<count($jsonDecodeProduct); $usdProduct++){
		/* Fetch Retail Price */
		$productName = $jsonDecodeProduct[$jsonDecodeIncrement];

		$productPriceList = $this->db->where('product', $productName)->get('retail_pricing')->row();
		$priceListUS = $this->db->where('id', 1)->get('pricelist_edit_us')->row();
		
		if(count((array)$productPriceList)>0){
		$retailsPrice = $productPriceList->retail_price;
		}
		if(count((array)$companyPricingType)>0){
		$pricingType = $companyPricingType->pricing_type;
		}
		
		/* 	foreach($pricelistDecoded as $key=>$pricelistDecodedRow){
			if(array_key_exists($jsonDecodeProduct[$jsonDecodeIncrement], $pricelistDecodedRow)){
			//pre($pricelistDecodedRow->$productName);
			unset($pricelistDecodedRow->$productName->efs_amt);
				$retailsPrice = $pricelistDecodedRow->$productName->retail_price;
			}
		} */
		if(empty($retailsPrice)){
			$retailsPrice = 0;
		}		
		if(empty($pricingType)){
			$pricingType = 'custom';
		}
		if(count((array)$priceListUS)>0){
		$companyWisePriceList = $priceListUS->$pricingType;
		}
		$decodeCWisePricing = json_decode($companyWisePriceList);

		$companyTypeVal = strtolower($getCompanyType->company_type);
			foreach($decodeCWisePricing as $key=>$decValuesrows){
				foreach($decValuesrows as $k=>$decValuesrows2){
					//pre($k);pre($decValuesrows2);
					if($k == $jsonDecodeProduct[$jsonDecodeIncrement]){
						//pre($decValuesrows2[0]->$typeOfCompany[0]);
						if($usdCardRows->gas_station_state == $decValuesrows2[0]->state[0] && str_replace(' ', '-', trim($usdCardRows->gas_station_name)) == $decValuesrows2[0]->gas_station[0]){
							$priceListUSPrice = $decValuesrows2[0]->$companyTypeVal[0];
						}
					}
				}
			}
		//$finalPricingAmount = floatval($retailsPrice) - floatval($priceListUSPrice);		
		$finalPricingAmount = floatval($priceListUSPrice);		
		//pre($finalPricingAmount);die;
		//$totalSavings = number_format(abs(floatval($retailsPrice) - $jsonDecodeUnitPrice[$jsonDecodeIncrement]), 4);
		//$totalSavings = number_format(abs(floatval($retailsPrice) - $finalPricingAmount), 4);
		$totalSavings = number_format(abs(floatval($retailsPrice) - $finalPricingAmount), 4);
		if(!empty($totalSavings)){
			$discount = number_format($totalSavings * $jsonDecodeQuantity[$jsonDecodeIncrement], 4);
		}
		/* Exclude Company Type Price */
		/* $getGasStation = $this->db->where('name', $usdCardRows->gas_station_name)->get('gas_stations')->row();
		if(!empty($getGasStation->exclude_pack_price) && $getGasStation->exclude_pack_price == 1){
			$companyTypePrice = 0;
		} */
		$total_amount = floatval($finalPricingAmount) * $jsonDecodeQuantity[$jsonDecodeIncrement];
		if($usdCount % 2 == 0){
			$content .= '<tr class="even-bg">';		
		}else{
			$content .= '<tr>';
		}			
		$content .= '
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->card_number.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$transactionDate.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_name.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_id.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$jsonDecodeProduct[$jsonDecodeIncrement].'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$retailsPrice.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$totalSavings.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$jsonDecodeQuantity[$jsonDecodeIncrement].'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$jsonDecodeQuantity[$jsonDecodeIncrement].'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;">'.number_format($total_amount, 2).'</td>
		</tr>';	
		if($usdCount % 2 == 0){
			$content .= '<tr class="even-bg">';
		}else{
			$content .= '<tr>';
		}
			$content .= '<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->transaction_id.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$transactionTime.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_city .'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_state .'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">G</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$finalPricingAmount.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($discount, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($total_amount, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">';
			if($jsonDecodeProductCategory[$jsonDecodeIncrement] == 'DEF'){
				
			}else{
				echo 0;
			}
			$content .= '</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td>'.$usdCardRows->billing_currency.'</td>
		</tr>';$totalDiscount += $discount; $grandTotalAmount += number_format($total_amount, 2);
		$totalQuantity = $jsonDecodeQuantity[$jsonDecodeIncrement];
		$totalDiscountFinal += $discount;$grandTotalAmountFinal += number_format($total_amount, 2);
		
			/*trans_data field*/
			$transJsonArrayObject = (array($usdCardRows->card_number => array('product_name'=>$jsonDecodeProduct[$jsonDecodeIncrement], 'quantity'=>$jsonDecodeQuantity[$jsonDecodeIncrement], 'unit_price'=>number_format($jsonDecodeUnitPrice[$jsonDecodeIncrement], 4), 'amountwithouttax' => number_format($total_amount, 2))));
			$transationDetails[$usdCount] = $transJsonArrayObject;
			/*invoice_data field*/
			$jsonArrayObject = (array('card_number' =>$usdCardRows->card_number,'transaction_date' => $usdCardRows->transaction_date, 'transaction_id' => $usdCardRows->transaction_id));
			$arr[$usdCount] = $jsonArrayObject;			
		$jsonDecodeIncrement++;$usdCount++;
		}
		$transactionDetails = json_encode($transationDetails);
		$invoice_data = json_encode($arr);		
		}//die;	
	$content .= '</table><br />
	<h3>Total for card: '.$usdCardRows->card_number.'</h3>
    <table class="table" border="" style="border: 1px solid #dcdcdc; font-size:8px; margin:20px auto; border-collapse:collapse;" cellspacing="0" cellpadding="3">  
           <tr  style="background-color:#CECECE;">  
                <th width="8%" style="text-align:center;border-bottom: 1px solid #dcdcdc;">Total Trans Amt</th>  
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Total Cash Trans Amt</th>  
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Total Other Code</th>  
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total Fees</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total DEF Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total Tractor Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total Reefer Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total HST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total GST</th>   
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Total QST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total PST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total Discount</th>   
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Total Final Amt</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Currency</th> 
           </tr>  
		 <tr>
			<td>'.floor($grandTotalAmount * 100)/100 .'</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>'.number_format($totalQuantity, 2).'</td>
			<td>0</td>
			<td>'.number_format($totalQuantity, 2).'</td>
			<td>0</td>
			<td>0</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>'.number_format($totalDiscount, 2).'</td>
			<td>'.floor($grandTotalAmount * 100)/100 .'</td>
			<td>USD</td>
		</tr>
		 </table>';
	}
	//}	
		$content .= '<br /><h3>Grand Totals</h3>
		 <table class="table" border="" style="font-size:8px;border: 1px solid #dcdcdc;  margin:20px auto; border-collapse:collapse;" cellspacing="0" cellpadding="3">  
           <tr style="background-color:#CECECE;">  
                <th width="6%" style="text-align:center;border-bottom: 1px solid #dcdcdc;">Trans Amt</th>  
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Cash Trans Amt</th>  
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Other Code</th>  
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Fees</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">DEF Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">DEF TAX </th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Tractor Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Reefer Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">HST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">GST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">QST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">PST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Manual Amt</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Discount</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Final Amt </th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;">Currency</th>   
           </tr>  
		 <tr>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($totalDiscountFinal, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.floor($grandTotalAmountFinal*100)/100 .'</td>
			<td style="border-bottom: 1px solid #dcdcdc;">USD</td>
		</tr>
		<tr><td colspan="17" style="text-align:right; border-bottom: 1px solid #dcdcdc;">Payment Recieved 0.00</td></tr>
		<tr><td colspan="17" style="text-align:right;">Amount Due '.floor($grandTotalAmountFinal*100)/100 .'</td></tr>
		 </table>

		<p style="">QST# 1221749509TQ0001</p>'; 
		  $data['invoice_id'] = "CL".$invoiceID;
		  $data['company_id'] = $cid;
		  $data['invoice_date'] = date('Y-m-d');
		  $data['invoice_data'] = $invoice_data;
		  $data['trans_data'] = $transactionDetails;		  
		  $data['grand_total'] = number_format($grandTotalAmountFinal, 2);
		  $data['date_created'] = date('Y-m-d H:i:s');
		  $data['date_modified'] = date('Y-m-d H:i:s');
		  $this->db->insert('transaction_invoice', $data);
		  $obj_pdf->writeHTML($content);	

	ob_end_clean();

    //$obj_pdf->Output('sample.pdf', 'I');	die;	
    $obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_USD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
	
	//Send generated invoice to company and then delete pdf
	$this->load->library('email');
	$usd_subject = 'USD Transactions Invoice';
	$usd_body = 'Currency USD';
	$pdfFilePath = APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_USD_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
	$usdresult = $this->email
				->from('info@pridediesel.com', 'From Pride Diesel')
				//->to('jagdishchander6373@gmail.com')
				->to($company_email_usd)
				->subject($usd_subject)
				->message($usd_body)
				->attach($pdfFilePath)
				->send();
	$this->email->clear($pdfFilePath);

	if($usdresult) {
		//echo "Send";
		unlink($pdfFilePath); //for delete generated pdf file. 
	}
	unset($cardDetail, $pdfFilePath);
		}
		
	/* CAD Transaction Invoice Generate Code */
	$cadcount = 0;
	if(!empty($cadTrans)){
		$custom_layout = array('350', '350');
		$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
		
		$obj_pdf->SetCreator(PDF_CREATOR);  
		$obj_pdf->SetTitle("Transaction Invoice Data");  
		$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
		$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
		$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
		$obj_pdf->SetDefaultMonospacedFont('helvetica');  
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
		$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
		$obj_pdf->setPrintHeader(false);  
		$obj_pdf->setPrintFooter(true);  
		$obj_pdf->SetAutoPageBreak(TRUE, 10);  
		$obj_pdf->SetFont('helvetica', '', 9);
		
		$image = FCPATH .'assets/images/pride-diesel-logo.png';
		$maxInvoiceId = $this->db->select_max('id')->get('transaction_invoice')->row();
		empty($maxInvoiceId->id)?$invoiceID = 1:$invoiceID = $maxInvoiceId->id + 1;
		//Insert invoice data in transaction invoice table
		$data['invoice_id'] = "CL".$invoiceID;
		$data['company_id'] = $cid;
		$data['invoice_date'] = date('Y-m-d');

		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_modified'] = date('Y-m-d H:i:s');
		
		$this->db->join('company_types', 'company_types.id = users.company_type');
		$getCompanyType = $this->db->where('users.id', $cid)->get('users')->row();

		//$this->db->insert('transaction_invoice', $data);
		$companyDetails = $this->db->where('id', $cid)->get('users')->row();	
		$obj_pdf->AddPage();
		
			$content = '';
			$content .= '<div style="width:1200px; margin:0px auto;">';
			$content .= '
			<table border="0">
				<tr>
					<td style="width: 20%"><img src="'.$image.'" alt="company_logo" width="160" height="50" border="0"/></td>
					<td style="width:40%;">
						<span style="font-size: 18px; font-weight: 600;margin:0;">Pride Diesel</span><br /><br />
						Address: 6050 Dixie Road, Mississauga, <br />ON L5T 1A6<br />
						Office: 888-558-3745<br />
						Fax: (866) 865-4596<br />
						www.pridediesel.com<br />
						Email: info@pridediesel.com
					</td>
					<td style="width:40%;">
						<span style="font-size: 25px; font-weight: 600; ">Client info</span><br /><br />
						<span style="font-size: 13px; font-weight: 600; ">'.$companyDetails->company_name .'</span><br />
						<span style="margin:0;">Address: '.$companyDetails->address .'</span><br />
						
						<span style="margin:0;">Email: '.$companyDetails->company_email .'</span>
					</td>
				</tr>
			</table><div style="margin-top: 20px; clear:both;"></div>';
			$company_email_ca = $companyDetails->company_email;
			$dueDate = date('Y-m-d', strtotime('+3 days'));
			$content .= '
			<table border="0">
				<tr>
					<td style="width:50%; float:left;">
						<table style="border: 1px solid #dcdcdc; clear: both; margin-bottom:20px; border-collapse:collapse; width: 100%; font-size: 10px;" cellspacing="0" cellpadding="3">  
						   <tr style="background-color:#CECECE;">  
								<th width="20%" style="font-size:10px;border-bottom: 1px solid #dcdcdc;">Invoice Number</th>  
								<th width="20%" style="font-size:10px;border-bottom: 1px solid #dcdcdc;">Invoice Date</th>    
								<th width="20%" style="font-size:10px;border-bottom: 1px solid #dcdcdc;">Due Date</th>   
						   </tr>  
							<tr>
								<td style="border-right: 1px solid #dcdcdc;">'.$data['invoice_id'].'</td>
								<td style="border-right: 1px solid #dcdcdc;">'.$data['invoice_date'].'</td>
								<td>'.$dueDate.'</td>
							</tr>
						</table>
					</td>
					<td style="50%">
					</td>
				</tr>	
			</table><br /></div>';
	//$this->db->select('cards.*');		
	$this->db->join('users', 'users.id = cards.company_id');
	$this->db->join('transactions', 'transactions.card_number = cards.card_number');
	$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0));
	$this->db->group_by('transactions.card_number');
	$cardCount = $this->db->get('cards')->result();
	$gstByCard =0; $qstByCard=0; $pstByCard=0; $grandGST=0; $grandTransAmount =0;
	foreach($cardCount as $cardRows){
	$this->db->select('cards.*, users.*, transactions.id as transid, transactions.*');		
	$this->db->join('users', 'users.id = cards.company_id');
	$this->db->join('transactions', 'transactions.card_number = cards.card_number');
	$this->db->where(array('transactions.card_number'=>$cardRows->card_number, 'cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0));
	$cadCardValues = $this->db->get('cards')->result();
	//foreach($usdCardValues as $cardRows){
	$explodeDateTime = explode(' ', $cardRows->transaction_date);	
	$transactionDate = $explodeDateTime[0];		
	$transactionTime = $explodeDateTime[1];		
	$content .= '<h3>Transactions for card: '.$cardRows->card_number.'</h3>
      <table style="border: 1px solid #dcdcdc; border-collapse:collapse; font-size: 8px;" cellpadding="3"> 
	    <tr  style="background-color:#CECECE;">  
			<th colspan="5" style="font-size:9px;text-align:center;border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Transaction Details</th>  
			<th colspan="7" style="font-size:9px;text-align:center;border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Fuel</th>  
			<th colspan="4" style="font-size:9px;text-align:center;border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Non Fuel Products(With Tax)</th>  
			<th  style="font-size:11px;border-bottom: 1px solid #dcdcdc;"></th> 
		</tr>
           <tr style="background-color:#CECECE;">  
                <th width="11%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Card #</th>  
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Driver Name</th>  
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">DATE </th>  
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Site Name</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Site#</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Product</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Retail Price</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Savings rate</th>     
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Trac qty</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">RFR qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Sales tax</th>   
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Total qty</th>   
                <th width="5%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Def qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Def Tax AMT</th>
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Other Code</th>			
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Cash</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Final AMT </th>   
           </tr>
           <tr  style="background-color:#CECECE;">  
                <th width="11%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Transaction#</th>  
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Vehicle#</th>  
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Time(EDT)</th>  
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Site city</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">State</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">UOM</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Billed Price</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Discount,$</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Trac PreTax</th>   
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">RFR PreTax</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">QST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Total AMT</th>   
                <th width="7%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">DEF Price</th>   
                <th width="5%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">DEF AMT</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Other AMT</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">Dash Cash</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Currency</th>   
           </tr><style>.even-bg {
					background-color: #ededed;
				}</style>';
		$companyPricingType = $this->db->where('id', $cid)->get('users')->row();
		$usdCount = 1;
		$totalDiscount = 0;$grandTotalAmount = 0; $totalQuantity = 0; $gstCount=0; $pstCount=0; $qstCount=0;
		$totalTransAmount=0; 
		foreach($cadCardValues as $usdCardRows){
		$jsonDecodeIncrement = 0;	
		$jsonDecodeAmount 				= json_decode($usdCardRows->amount);   
		$jsonDecodeProduct 				= json_decode($usdCardRows->category);   
		$jsonDecodeProductCategory 		= json_decode($usdCardRows->group_category);   
		$jsonDecodeUnitPrice 			= json_decode($usdCardRows->unit_price);   
		$jsonDecodeQuantity 			= json_decode($usdCardRows->quantity);
		
		/* Set Invoice_status as 1 */
		$this->db->where('id', $usdCardRows->transid);
		$this->db->set('invoice_status', 1);
		$this->db->update('transactions');   		

		for($usdProduct=0; $usdProduct<count($jsonDecodeProduct); $usdProduct++){
		/* Fetch Retail Price */
		$productName = $jsonDecodeProduct[$jsonDecodeIncrement];
		
		/* Product Taxes End */		
		$taxapplicable = $this->db->where('product_name', $productName)->get('products')->row();
		
		/* Exclude Company Type Price */
		/* $getGasStation = $this->db->where('name', $usdCardRows->gas_station_name)->get('gas_stations')->row();
		pre($getGasStation);die;
		if($getGasStation->exclude_pack_price == 1){
			$companyTypePriceCheck = 0;
		} */	
			$companyTypePriceCheck = 0;
			if(!empty($taxapplicable->tax) ){
			$taxArray = json_decode($taxapplicable->tax);


			foreach($taxArray as $key=>$taxArrayRow){	
			$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>$usdCardRows->gas_station_state))->get('tax')->row();
			
			//FET || PCT || PFT								
			if(!empty($taxOutput['pft']->tax_rate)){$pft = str_replace('%', '', $taxOutput['pft']->tax_rate);}else{$pft = 0;}
			if(!empty($taxOutput['pct']->tax_rate)){$pct = str_replace('%', '', $taxOutput['pct']->tax_rate);}else{$pct = 0;}
			if(!empty($taxOutput['fet']->tax_rate)){$fet = str_replace('%', '', $taxOutput['fet']->tax_rate);}else{$fet = 0;}
			//GST/HST/FNT || PST/QST
			if(!empty($taxOutput['gst']->tax_rate)){$gst = str_replace('%', '', $taxOutput['gst']->tax_rate);}else{$gst = 0;}
			if(!empty($taxOutput['pst']->tax_rate)){$pst = str_replace('%', '', $taxOutput['pst']->tax_rate);}else{$pst = 0;}
			if(!empty($taxOutput['qst']->tax_rate)){$qst = str_replace('%', '', $taxOutput['qst']->tax_rate);}else{$qst = 0;}			
			}
			$ppfTot = $pft+$pct+$fet;
			}
			if(empty($ppfTot)){$ppfTot = 0;}
			/* $tax3withprice = $jsonDecodeUnitPrice[$jsonDecodeIncrement] + floor($companyTypePriceCheck) + floor($ppfTot*100)/100; */
			$tax3withprice = $jsonDecodeUnitPrice[$jsonDecodeIncrement] + floor($companyTypePriceCheck);
			$amtAfterTax = $jsonDecodeQuantity[$jsonDecodeIncrement] * $tax3withprice;
			if(empty($gst)){$gst = 0;}
			if(empty($pst)){$pst = 0;}
			if(empty($qst)){$qst = 0;}
			$includeGST=0; $includePST=0; $includeQST=0;
			$includeGST = $amtAfterTax * $gst / 100;
			$gstByCard += $includeGST;
			$gstCount += $includeGST;
			if($usdCardRows->gas_station_state != 'QC'){
			$includePST = $amtAfterTax * $pst / 100;
			$pstByCard += $includePST;
			$pstCount += $includePST;
			}else{
			$includeQST = $amtAfterTax * $qst / 100;
			$qstByCard += $includeQST;				
			$qstCount += $includeQST;				
			}
			/* Product Taxes End */
			$qtyByCard += $jsonDecodeQuantity[$jsonDecodeIncrement];	
		//$productPriceList = $this->db->where('product', $productName)->get('retail_pricing')->row();
		$priceListUS = $this->db->where('id', 1)->get('pricelist')->row();
		$companyTypeVal = strtolower($getCompanyType->company_type);
		$productPriceList = $this->db->get('pricelist')->row();
		$pricelistDecoded = json_decode($productPriceList->price_descr);
		foreach($pricelistDecoded as $key=>$pricelistDecodedRow){
			if(array_key_exists($productName, $pricelistDecodedRow)){
			unset($pricelistDecodedRow->$productName[0]->efs_amt);
			$companyTypePrice = $pricelistDecodedRow->$productName[0]->$companyTypeVal[0];
			}
		}
		$totalSavings = 0; $discount = 0; 
		$amountTotal = $amtAfterTax + $companyTypePrice;
		$subTotal = $amtAfterTax + $includeGST + $includePST + $includeQST + $companyTypePrice;
		$totalTransAmount += $amountTotal;
		$grandTransAmount += $amountTotal;
		if($usdCount % 2 == 0){
			$content .= '<tr class="even-bg">';		
		}else{
			$content .= '<tr>';
		}			
		$content .= '
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->card_number.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$transactionDate.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_name.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_id.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$jsonDecodeProduct[$jsonDecodeIncrement].'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$jsonDecodeUnitPrice[$jsonDecodeIncrement].'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$totalSavings.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$jsonDecodeQuantity[$jsonDecodeIncrement].'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$jsonDecodeQuantity[$jsonDecodeIncrement].'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;">'.number_format($amountTotal, 2).'</td>
		</tr>';	
		if($usdCount % 2 == 0){
			$content .= '<tr class="even-bg">';
		}else{
			$content .= '<tr>';
		}
			$content .= '<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->transaction_id.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$transactionTime.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_city .'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$usdCardRows->gas_station_state .'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">L</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$tax3withprice.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($discount, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($amountTotal, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">';
			if($jsonDecodeProductCategory[$jsonDecodeIncrement] == 'DEF'){
				
			}else{
				echo 0;
			}
			$content .= '</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td>'.$usdCardRows->billing_currency.'</td>
		</tr>';$totalDiscount += $discount; 
		$grandTotalAmountByCard += number_format($subTotal, 2);
		$grandTotalAmount += number_format($subTotal, 2);
		$totalQuantity = $jsonDecodeQuantity[$jsonDecodeIncrement];
		$totalDiscountFinal += $discount;
		$grandTotalAmountFinal += number_format($subTotal, 2);
		
			/*trans_data field*/
			$transJsonArrayObject = (array($usdCardRows->card_number => array('product_name'=>$jsonDecodeProduct[$jsonDecodeIncrement], 'quantity'=>$jsonDecodeQuantity[$jsonDecodeIncrement], 'unit_price'=>$jsonDecodeUnitPrice[$jsonDecodeIncrement], 'GST'=>$gstByCard, 'PST'=>$pstByCard, 'QST'=>$qstByCard, 'amountwithouttax' => number_format($subTotal, 2), 'grand_total'=> $grandTotalAmountFinal)));
			$transationDetails[$usdCount] = $transJsonArrayObject;
			/*invoice_data field*/
			$jsonArrayObject = (array('card_number' =>$usdCardRows->card_number,'transaction_date' => $usdCardRows->transaction_date, 'transaction_id' => $usdCardRows->transaction_id));
			$arr[$usdCount] = $jsonArrayObject;			
		$jsonDecodeIncrement++;$usdCount++;
		}
		$transactionDetails = json_encode($transationDetails);
		$invoice_data = json_encode($arr);		
		}//die;	
	$content .= '</table><br />
	<h3>Total for card: '.$usdCardRows->card_number.'</h3>
    <table class="table" border="" style="border: 1px solid #dcdcdc; font-size:8px; margin:20px auto; border-collapse:collapse;" cellspacing="0" cellpadding="3">  
           <tr  style="background-color:#CECECE;">  
                <th width="8%" style="text-align:center;border-bottom: 1px solid #dcdcdc;">Total Trans Amt</th>  
                <th width="10%" style="border-bottom: 1px solid #dcdcdc;">Total Cash Trans Amt</th>  
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Total Other Code</th>  
                <th width="5%" style="border-bottom: 1px solid #dcdcdc;">Total Fees</th>   
                <th width="5%" style="border-bottom: 1px solid #dcdcdc;">Total Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total DEF Qty</th>   
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Total Tractor Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total Reefer Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total HST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total GST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total QST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total PST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Total Discount</th>   
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Total Final Amt</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Currency</th> 
           </tr>  
		 <tr>
			<td>'.floor($totalTransAmount * 100)/100 .'</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>'.$qtyByCard.'</td>
			<td>0</td>
			<td>'.$qtyByCard.'</td>
			<td>0</td>
			<td>0</td>
			<td>'.number_format($gstByCard, 2).'</td>
			<td>'.number_format($qstByCard, 2).'</td>
			<td>'.number_format($pstByCard, 2).'</td>
			<td>'.number_format($totalDiscount, 2).'</td>
			<td>'.floor($grandTotalAmount * 100)/100 .'</td>
			<td>CAD</td>
		</tr>
		 </table>';
		 $grandGST += $gstByCard;
	}
	
	//}	
		$content .= '<br /><h3>Grand Totals</h3>
		 <table class="table" border="" style="font-size:8px;border: 1px solid #dcdcdc;  margin:20px auto; border-collapse:collapse;" cellspacing="0" cellpadding="3">  
           <tr style="background-color:#CECECE;">  
                <th width="6%" style="text-align:center;border-bottom: 1px solid #dcdcdc;">Trans Amt</th>  
                <th width="8%" style="border-bottom: 1px solid #dcdcdc;">Cash Trans Amt</th>  
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Other Code</th>  
                <th width="4%" style="border-bottom: 1px solid #dcdcdc;">Fees</th>   
                <th width="5%" style="border-bottom: 1px solid #dcdcdc;">Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">DEF Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">DEF TAX </th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Tractor Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Reefer Qty</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">HST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">GST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">QST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">PST</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Manual Amt</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Discount</th>   
                <th width="6%" style="border-bottom: 1px solid #dcdcdc;">Final Amt </th>   
                <th width="5%" style="border-bottom: 1px solid #dcdcdc;">Currency</th>   
           </tr>  
		 <tr>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($grandTransAmount, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($grandGST, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($qstCount, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($pstCount, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0.00</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.number_format($totalDiscountFinal, 2).'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.floor($grandTotalAmountFinal*100)/100 .'</td>
			<td style="border-bottom: 1px solid #dcdcdc;">CAD</td>
		</tr>
		<tr><td colspan="17" style="text-align:right; border-bottom: 1px solid #dcdcdc;">Payment Recieved 0.00</td></tr>
		<tr><td colspan="17" style="text-align:right;">Amount Due '.floor($grandTotalAmountFinal*100)/100 .'</td></tr>
		 </table>

		<p style="">QST# 1221749509TQ0001</p>'; 
		  $data['invoice_id'] = "CL".$invoiceID;
		  $data['company_id'] = $cid;
		  $data['invoice_date'] = date('Y-m-d');
		  $data['invoice_data'] = $invoice_data;
		  $data['trans_data'] = $transactionDetails;		  
		  $data['grand_total'] = number_format($grandTotalAmountFinal, 2);
		  $data['date_created'] = date('Y-m-d H:i:s');
		  $data['date_modified'] = date('Y-m-d H:i:s');
		  $this->db->insert('transaction_invoice', $data);
		$obj_pdf->writeHTML($content);
	ob_end_clean();

    //$obj_pdf->Output('sample.pdf', 'I'); die;		
    $obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
	
	//Send generated invoice to company and then delete pdf
	$this->load->library('email');
	$ca_subject = 'CAD Transactions Invoice';
	$ca_body = 'Currency CAD';
	$pdfFilePath = APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
	$result = $this->email
				->from('info@pridediesel.com', 'From Pride Diesel')
				//->to('jagdishchander6373@gmail.com')
				->to($company_email_ca)
				->subject($ca_subject)
				->message($ca_body)
				->attach($pdfFilePath)
				->send();
	$this->email->clear($pdfFilePath);

	if($result) {
		//echo "Send";
		unlink($pdfFilePath); //for delete generated pdf file. 
	} 
	unset($cardDetail, $pdfFilePath);	
	}		
	}	
	
}