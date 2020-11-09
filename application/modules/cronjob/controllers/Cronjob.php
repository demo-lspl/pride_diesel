<?php

class Cronjob extends MY_Controller
{
	
    public function __construct()
    {
        parent::__construct();
		//date_default_timezone_set( 'Asia/Kolkata' );
		$this->load->model('account/account_model');
		$this->load->model('user/user_model');
		$this->load->model('card/card_model');		
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
					$this->generateUSInvoice($cid);
					$this->generateCanadianInvoice($cid);
					//$this->generate_trans_invoice($usersRow->id);
					echo "Not Empty lastinvoicedate".$usersRow->id;					
				}
				//echo $invoiceSchedule;
				}else{
					//$this->generate_trans_invoice($usersRow->id);
					echo "Empty lastinvoicedate".$usersRow->id;
				}
				
			}
			
			if($invoiceSchedule == 'weekly'){
				if($transDate->date_created != NULL){
				$latsInvoiceDate = new DateTime($transDate->date_created);
				$lastInvoiced = $newDate->diff($latsInvoiceDate);
				//echo $usersRow->lastinvoicedate."=".$lastInvoiced->days."<br>";
				if($lastInvoiced->days > 6 ){
					$this->generateUSInvoice($cid);
					$this->generateCanadianInvoice($cid);					
					//$this->generate_trans_invoice($usersRow->id);
					echo "Not Empty lastinvoicedate".$usersRow->id;					
				}
				//echo $invoiceSchedule;
				}else{
					//$this->generate_trans_invoice($usersRow->id);
					echo "Empty lastinvoicedate".$usersRow->id;
				}
				
			}			

		}

		exit();
	}

	public function generateUSInvoice($cid){
		//if(!empty($usdTrans)){
			ob_start();
			$custom_layout = array('350', '350');
			$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
			
			$obj_pdf->SetCreator(PDF_CREATOR);  
			$obj_pdf->SetTitle("Transaction Invoice Data");  
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
			
			$image = FCPATH .'assets/images/pride-diesel-logo.png';
			$maxInvoiceId = $this->db->select_max('id')->get('transaction_invoice')->row();
			empty($maxInvoiceId->id)?$invoiceID = 1:$invoiceID = $maxInvoiceId->id + 1;
			//ob_start();			
			$obj_pdf->AddPage();

			$content = '';
			$datContent = '';
			
			$this->db->join('users', 'users.id = cards.company_id');
			$this->db->join('drivers', 'drivers.company_id = cards.company_id', 'LEFT');
			$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'USD', 'transactions.invoice_status'=>0));
			$this->db->group_by('transactions.card_number');
			$begEndDates = $this->db->get('cards')->result();
			for($begEnd=0; $begEnd<count($begEndDates); $begEnd++){
				//pre($begEnd);
				$makePeriodBeg = $begEndDates[0]->transaction_date;
				$periodBeg = strtotime($makePeriodBeg);
				if($begEnd == count($begEndDates) - 1){
					$makePeriodEnd = $begEndDates[$begEnd]->transaction_date;
					$periodEnd = strtotime($makePeriodEnd);
				}
			}	
			
			$companyDetails = $this->db->where('id', $cid)->get('users')->row();	
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
							HST #1221749509TQ0001
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
							
							Invoice #<br />
							Invoice Date<br />
							Due Date<br />
							Period beginning<br />
							Period ending<br />
							Payment terms<br />
							</td>
							<td>
							'.$companyDetails->id .'<br />
							<strong>'.$companyDetails->company_name .'</strong><br />
							'.$companyDetails->address .'<br />
							
							CL'.$invoiceID.'<br />
							'.date('d/m/Y').'<br />
							<strong>'.date('d/m/Y', strtotime('+7 days')).'</strong><br />
							'.date('d/m/Y', $periodBeg).'<br />
							'.date('d/m/Y', $periodEnd).'<br />
							Net 7 days
							</td>
						</tr>
					</table>
			   </td>
		       <td class="pride">Page-'.$obj_pdf->getPage().'<br />Confidential information</td>
		  </tr>
         
		</tbody>
		</table><div class="header-section-bot"></div><br />';
		$company_email_usd = $companyDetails->company_email;
			$seqNum=str_pad($invoiceID, 10, "0", STR_PAD_LEFT);
			$dateTime = date('Ymdhis');
			/*HH Code Started*/		  
			$datContent .= "HH09".$seqNum.$dateTime."ALL ";/*Record Type(2),Version(2),Sequence Number(10),File Date(14),File Type(4)*/
			$datContent .= "PROD";/*File Use(4)*/
			$datContent .= "                                                                                                                                                                                                                                                                        ";/*Filler(264)*/
			/*HH Code Ended*/		
		$content .='<table class="custom-css" style="border: 1px solid #D9D9D9 ; clear: both; border-spacing: 0; border-collapse: collapse;" cellpadding="3">
		<tbody>		
           <tr>
		      <th style="width:12%;">Card #</th>
		      <th style="width:8%;">Driver name</th>
			  <th style="width:7%;">Transaction #</th>
			  <th style="width:7%;">Site name</th>
			  <th style="width:4%;">Site #</th>
			  <th style="width:6%;">City</th>
			  <th style="width:3%;">State</th>
			  <th style="width:6%;">Date</th>
			  <th style="width:5%;">Time</th>
			  <th style="width:4%;">Unit #</th>
			  <th style="width:5%;">Product</th>
			  <th style="width:3%;">UOM</th>
			  <th style="width:7%;">Price per unit</th>
			  <th style="width:7%;">Total quantity</th>
			  <th style="width:6%;">HST/GST</th>
			  <th style="width:5%;">Total</th>
			  <th style="width:5%;">Currency</th>
           </tr>';
			$this->db->select('users.*, drivers.*, transactions.*, cards.unit_number, cards.driver_id');
			$this->db->join('users', 'users.id = cards.company_id');
			$this->db->join('drivers', 'drivers.company_id = cards.company_id', 'LEFT');
			$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'USD', 'transactions.invoice_status'=>0));
			//$this->db->group_by('transactions.card_number');
			$cardCount = $this->db->get('cards')->result();
			$totalQuantity = 0; $grandTotal = 0;
			$jsonInc = 0;
			$driverName = "-";
			$transactionCount=0;
			
			foreach($cardCount as $cardCountRows){
				$productNameJsonDecode = json_decode($cardCountRows->category);
				$pridePriceJsonDecode = json_decode($cardCountRows->pride_price);
				$EFSpriceJsonDecode = json_decode($cardCountRows->unit_price);
				$productQuantityJsonDecode = json_decode($cardCountRows->quantity);
				$explodeDateTime = explode(' ', $cardCountRows->transaction_date);	
				$transactionDate = $explodeDateTime[0];		
				$transactionTime = $explodeDateTime[1];
				$driverid = $cardCountRows->driver_id;
				if(!empty($driverid)){
					$getDriverName = $this->db->select('name')->where('id', $driverid)->get('drivers')->row();
					$driverName = $getDriverName->name;
				}				
				
				$rowCount=0; 
				foreach($productNameJsonDecode as $productCountJsonDecodeRows){
					$calcProduct = $productQuantityJsonDecode[$rowCount] * $pridePriceJsonDecode[$rowCount];
					$total = floor($calcProduct*100)/100;
					$transactionCount++;
					//pre($cardCount);		   
					$content .='<tr>
						<td style="width:12%;">'.$cardCountRows->card_number .'</td>
						<td style="width:8%;">'.$driverName.'</td>
						<td style="width:7%;">'.$cardCountRows->transaction_id .'</td>
						<td style="width:7%;">'.$cardCountRows->gas_station_name .'</td>
						<td style="width:4%;">'.$cardCountRows->gas_station_id .'</td>
						<td style="width:6%;">'.$cardCountRows->gas_station_city .'</td>
						<td style="width:3%;">'.$cardCountRows->gas_station_state .'</td>
						<td style="width:6%;">'.$transactionDate.'</td>
						<td style="width:5%;">'.$transactionTime.'</td>
						<td style="width:4%;">'.$cardCountRows->unit_number .'</td>
						<td style="width:5%;">'.$productNameJsonDecode[$rowCount].'</td>
						<td style="width:3%;">G</td>
						<td style="width:7%;">$ '.$pridePriceJsonDecode[$rowCount].'</td>
						<td style="width:7%;">'.$productQuantityJsonDecode[$rowCount].'</td>
						<td style="width:6%;">$ 0.00</td>
						<td style="width:5%;">$ '.$total.'</td>
						<td style="width:5%;">'.$cardCountRows->billing_currency .'</td>
					</tr>';
					$totalQuantity += $productQuantityJsonDecode[$rowCount];
					$grandTotal += floor($total*100)/100;
					/*trans_data field*/
					$transJsonArrayObject = (array($cardCountRows->card_number => array('product_name'=>$productNameJsonDecode[$rowCount], 'quantity'=>$productQuantityJsonDecode[$rowCount], 'unit_price'=>$pridePriceJsonDecode[$rowCount], 'amountwithouttax' => $total)));
					$transationDetails[$jsonInc] = $transJsonArrayObject;
					/*invoice_data field*/
					$jsonArrayObject = (array('card_number' =>$cardCountRows->card_number,'transaction_date' => $cardCountRows->transaction_date, 'transaction_id' => $cardCountRows->transaction_id));
					$arr[$jsonInc] = $jsonArrayObject;

						/*DAT file content */
					$transid = str_pad($cardCountRows->transaction_id, 10, "0", STR_PAD_LEFT);
					$cardNumber = str_pad($cardCountRows->card_number, 25, " ", STR_PAD_RIGHT);
					$invoiceNum = str_pad($cardCountRows->invoice, 12, " ", STR_PAD_RIGHT);
					$locId = str_pad($cardCountRows->gas_station_id, 10, "0", STR_PAD_LEFT);
					$transDateTime = date('Ymdhi', strtotime($cardCountRows->transaction_date));
					$authCode = '0000000000';
					$contractId = '0000000000';
					$issuerId = '0000000000';
					$carrierId = '0000000000';
					$supplierId = '0000000000';
					$reportedCarrier = date('Ymdhis', strtotime($cardCountRows->transaction_date));
					$handEntered = 'N';
					$overrideCard = 'N';
					$totDiscountAmt = '00000000000';
					$carrierFee = '00000000000';
					$supplierFee = '00000000000';
					$settlementId = '00000000000';
					$statementId = '00000000000';
					$dicountType = 'W';
					$POSdate = date('Ymdhi', strtotime($cardCountRows->transaction_date));
					$currencyCode = 'USD';
					$conversionRate = '00000000000';
					$preferTot = '00000000000';
					$country = 'U';
					$biilingCountry = 'U';
					$invoiceTot = '00000000000';
					$transType  = 'F';
					$isCredit = 'D';
					$settleAmount = '00000000000';
					$locType = '0000';
					$transSysType = '000';
					$cardPolicy = '0000';
					$netTotDollars = '00000000000';
					$fundedTot = '00000000000';
					$authCode = '0000000000';
					/*TA Code Started*/
					$datContent .= "TA09".$seqNum.$transid.$cardNumber.$invoiceNum.$locId.$transDateTime.$authCode.$contractId.$issuerId;/*Record Type(2),Version(2),Sequence Number(10),card number(25),invoiceNum(12), locationId(10), transDateTime(12), authCode(10), contractId(10), issuerId(10)*/
					$datContent .= $carrierId.$supplierId.$reportedCarrier;/*carrierId(10), supplierId(10), reportedCarrier(14)*/
					$datContent .= $handEntered.$overrideCard.$totDiscountAmt;/*handEntered(1), overrideCard(1), totDiscountAmt(11)*/
					$datContent .= $carrierFee.$supplierFee.$settlementId;/*carrierFee(11), supplierFee(11), settlementId(11)*/
					$datContent .= $statementId.$dicountType.$POSdate;/*statementId(11), dicountType(1), POSdate(14)*/
					$datContent .= $currencyCode.$conversionRate.$preferTot;/*currencyCode(3), conversionRate(11), preferTot(11)*/
					$datContent .= $country.$biilingCountry.$invoiceTot;/*country(1), biilingCountry(1), invoiceTot(11)*/
					$datContent .= $transType.$isCredit.$settleAmount;/*transType(1), isCredit(1), settleAmount(11)*/
					//$datContent .= $locType/*locType(4)*/
					//$datContent .= "       ";/*Filler(7)*/
					$datContent .= $transSysType;/*Transaction System Type(3)*/
					$datContent .= $cardPolicy;/*Card Policy(4)*/
					$datContent .= "                        ";/*Filler(24)*/
					//$datContent .= $transSysType.$cardPolicy;/*Transaction System Type(3), Card Policy(4)*/
					//$datContent .= $netTotDollars.$fundedTot;/*Net Total Dollars(11), Funded Total(11)*/
					//$datContent .= $authCode;/*Authorization Code(10)*/
					//$datContent .= "                                                                                           ";/*Filler(91)*/
					/*TA Code Ended*/
					$lineId = '00';
					$productCat = '0000';
					$quantity = str_pad(str_replace('.','',$productQuantityJsonDecode[$rowCount]), 7, "0", STR_PAD_LEFT);
					$amount = str_pad(str_replace('.','',number_format($pridePriceJsonDecode[$rowCount], 4)), 10, "0", STR_PAD_LEFT);
					$retailAmt = str_pad(str_replace('.','',$EFSpriceJsonDecode[$rowCount]), 10, "0", STR_PAD_LEFT);
					$fuelType = '00000000';
					$serviceType = '00';
					$fuelUseType = '00';
					$ppuDiscounted = '0000000';
					$retailPPU = str_pad(str_replace('.','',$EFSpriceJsonDecode[$rowCount]), 10, "0", STR_PAD_LEFT);
					$productNum = '000';
					$unitOdMeasure = 'G';
					$discountAmt = '00000000000';
					/*TL Code Started*/
					$datContent .= "TL09".$seqNum.$transid.$lineId;/*Record Type(2),Version(2),Sequence Number(10), Transaction Id(10), Line Id(2)*/
					$datContent .= $productCat.$quantity.$amount;/*Product Category(4), Quantity(7), Amount (Discounted)(11)*/
					$datContent .= $retailAmt.$fuelType.$serviceType.$fuelUseType;/*Retail Amount(11), Fuel Type(8), Service Type(2), Fuel Use Type(2)*/
					$datContent .= $ppuDiscounted.$retailPPU.$productNum;/*PPU(Discounted)(7), Retail PPU(7), Product Number(3)*/
					$datContent .= $unitOdMeasure.$discountAmt;/*Unit of measurement(1), Discount Amount(11)*/
					/*TL Code Ended*/
					$taxDisc = '0000000000';
					$taxCode = '0000000000';
					$taxAmount = '00000000000';
					$grossNetFlag = '0';
					$exemptFlag = '0';
					$taxRateType = '0';
					$taxRate = '00000000000';
					/*TE Code Started*/
					$datContent .= "TE09".$seqNum.$transid.$lineId;/*Record Type(2), Version(2), Sequence Number(10), Transaction Id(10), Line Id(2)*/
					$datContent .= $taxDisc.$taxCode.$taxAmount.$grossNetFlag;/*Tax Desc.(10), Tax Code(10), Tax Amount(11), Gross Net Flag(1) */
					$datContent .= $exemptFlag.$taxRateType.$taxRate;/*Exempt Flag(1), Tax Rate Type(1), Tax Rate(11) */
					$datContent .= "                                                                                                                                                                                                                                     ";/*Filler(229)*/
					/*TE Code Ended*/
					$locationId = '0000000000';
					$locType = '0000';
					$locName = '0000000000000000000000000';
					$locCity = '0000000000000000000000000';
					$locState = '00';
					$locCountry = '0';
					$opisId = '00000000000';
					$POStimeZone = 'C';
					$chainCode = '000';
					$transId = '0000000000';
					$address1 = '00000000000000000000000000000000000000000000000000';
					$postalCode = '0000000000';
					$latitude = '00000000000000000';
					$longitude = '00000000000000000';
					$pumpNumber = '0000000';
					/*LL Code Started*/
					$datContent .= "LL09".$seqNum.$locationId.$locType;/*Record Type(2),Version(2),Sequence Number(10), Location Id(10), Location Type
					Code(4)*/
					$datContent .= $locName.$locCity.$locState.$locCountry;/*Loc. Name(25), Loc. City(25), Loc. State(2), Loc. Country(1) */
					$datContent .= $opisId.$POStimeZone.$chainCode.$transId;/*Opis ID(11), POS Time Zone(1), Chain Code(3), Transaction Id(10) */
					$datContent .= $address1.$postalCode.$latitude.$longitude;/*Address1(50), Postal Code (zip)(10), Latitude(17), Longitude(17) */
					$datContent .= $pumpNumber;/*Pump Number(7) */
					$datContent .= "                                                                                             ";/*Filler(93)*/
					/*LL Code Ended*/
					$infoIdCode = '0000';
					$infoValue = '0000000000000000000000000';
					/*TI Code Started*/
					$datContent .= "TI09".$seqNum.$transId;/*Record Type(2),Version(2),Sequence Number(10), Transaction Id(10)*/
					$datContent .= $infoIdCode.$infoValue;/*Info Id Code(4), Info Value(25) */
					$datContent .= "                                                                                                                                                                                                                                                       ";/*Filler(247)*/
					/*TI Code Ended*/
					$totInfoRecord = '0000000000';
					$totLineRecords = '0000000000';
					$totTransAmtSign = '0';
					$totTransAmt = '00000000000';
					/*TT Code Started*/
					$datContent .= "TT09".$seqNum.$totInfoRecord;/*Record Type(2),Version(2),Sequence Number(10), Total Info Records(10) */
					$datContent .= $totLineRecords.$totTransAmtSign;/*Total Line Records(10), Total Transaction Amount Sign(1)  */
					$datContent .= $totTransAmt.$transId;/*Total Transaction Amount(11), Transaction Id(10) */
					$datContent .= "                                                                                                                                                                                                                                                    ";/*Filler(244)*/
					/*TT Code Ended*/		
					$rowCount++;$jsonInc++;
				}
				/* Set Invoice_status as 1 */
				$this->db->where('transaction_id', $cardCountRows->transaction_id);
				$this->db->set('invoice_status', 1);
				$this->db->update('transactions');				
				$transactionDetails = json_encode($transationDetails);
				$invoice_data = json_encode($arr);
			}
			$content .='<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td colspan="2">Subtotal</td>
							<td>'.$totalQuantity.'</td>
							<td>$ 0.00</td>
							<td>$ '.$grandTotal.'</td>
							<td>USD</td>
						</tr>
					</tbody>
					</table>';
		$totNumOfTrans = str_pad(str_replace('.','',$transactionCount), 10, "0", STR_PAD_LEFT);
		$totNumOfRecs = str_pad(str_replace('.','',$transactionCount), 10, "0", STR_PAD_LEFT);
		$totTransAmtSign = '+';
		$totTransAmt = str_pad(str_replace('.','',$grandTotal), 11, "0", STR_PAD_LEFT);
 
		/*HT Code Started*/
		$datContent .= "HT09".$seqNum;/*Record Type(2),Version(2),Sequence Number(10)*/
		$datContent .= $totNumOfTrans.$totNumOfRecs.$totTransAmtSign;/*Total Number of Trans.(10), Total Number of Recs.(10), Total Transaction Amount Sign(1) */
		$datContent .= $totTransAmt;/*Total Transaction Amount(11) */
		$datContent .= "                                                                                                                                                                                                                                                              ";/*Filler(254)*/
		/*HT Code Ended*/
			//Insert invoice data in transaction invoice table			
			$data['invoice_id'] = "CL".$invoiceID;
			$data['company_id'] = $cid;
			$data['invoice_date'] = date('Y-m-d');
			$data['billingCurrency'] = 'USD';
			$data['invoice_data'] = $invoice_data;
			$data['trans_data'] = $transactionDetails;		  
			$data['status'] = 0;	  
			$data['grand_total'] = $grandTotal;
			$data['date_created'] = date('Y-m-d H:i:s', time() + 1 * 28 * 60);
			$data['date_modified'] = date('Y-m-d H:i:s', time() + 1 * 28 * 60);
			$this->db->insert('transaction_invoice', $data);
			//pre($content);die;
			$obj_pdf->writeHTML($content);	
			$file = fopen(APPPATH ."modules/cronjob/invoice_pdf/Pridediesel_110_".$invoiceID."_USD_".date('Ymd').".dat","w");
			//fwrite($file,"$a\n$b$c\n$e$d$f$k$g$l$h$m\n$n$o$p$q$r$s$t\n$u$v");
			fwrite($file,"$datContent");
			fclose($file);
			ob_end_clean();
			//if (ob_get_contents()) ob_end_clean();

			//$obj_pdf->Output('sample.pdf', 'I');	die;	
			//$obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_USD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
			$obj_pdf->Output(FCPATH . 'assets/modules/invoices/trans_invoice_USD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
			
			//Send generated invoice to company and then delete pdf
			$this->load->library('email');
			$usd_subject = 'USD Transactions Invoice';
			$usd_body = 'Currency USD';
			//$pdfFilePath = APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_USD_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
			$pdfFilePath = FCPATH . 'assets/modules/invoices/trans_invoice_USD_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
			$usdresult = $this->email
						->from('info@pridediesel.com', 'From Pride Diesel')
						//->to('jagdishchander6373@gmail.com')
						->to($company_email_usd)
						->subject($usd_subject)
						->message($usd_body)
						->attach($pdfFilePath)
						->send();
			//$this->email->clear($pdfFilePath);

			//if($usdresult) {
				//echo "Send";
				//unlink($pdfFilePath); //for delete generated pdf file. 
			//}
			//unset($cardDetail, $pdfFilePath);
			//unset($cardCountRows);
		//}
	}		
	
	public function generateCanadianInvoice($cid){
	/* CAD Transaction Invoice Generate Code */
	//if(!empty($cadTrans)){
		ob_start();
		$custom_layout = array('350', '350');
		$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
		$obj_pdf->SetCreator(PDF_CREATOR);  
		$obj_pdf->SetTitle("Transaction Invoice Data");  
		$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
		$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
		$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
		$obj_pdf->SetDefaultMonospacedFont('helvetica');  
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
		//$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
		$obj_pdf->SetMargins(5, 20, 5, true);	
		$obj_pdf->setPrintHeader(false);  
		$obj_pdf->setPrintFooter(true);  
		$obj_pdf->SetAutoPageBreak(TRUE, 10);  
		$obj_pdf->SetFont('helvetica', '', 9);
		
		$image = FCPATH .'assets/images/pride-diesel-logo.png';
		$maxInvoiceId = $this->db->select_max('id')->get('transaction_invoice')->row();
		empty($maxInvoiceId->id)?$invoiceID = 1:$invoiceID = $maxInvoiceId->id + 1;
		//ob_start();
		$obj_pdf->AddPage();
			$content = '';
			$datContent = '';
			
			$this->db->join('users', 'users.id = cards.company_id');
			$this->db->join('drivers', 'drivers.company_id = cards.company_id', 'LEFT');
			$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0));
			//$this->db->group_by('transactions.card_number');
			$begEndDates = $this->db->get('cards')->result();
			for($begEnd=0; $begEnd<count($begEndDates); $begEnd++){
				//pre($begEnd);
				$makePeriodBeg = $begEndDates[0]->transaction_date;
				$periodBeg = strtotime($makePeriodBeg);
				if($begEnd == count($begEndDates) - 1){
					$makePeriodEnd = $begEndDates[$begEnd]->transaction_date;
					$periodEnd = strtotime($makePeriodEnd);
				}
			}	
			
			$companyDetails = $this->db->where('id', $cid)->get('users')->row();	
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
							HST #1221749509TQ0001
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
							
							Invoice #<br />
							Invoice Date<br />
							Due Date<br />
							Period beginning<br />
							Period ending<br />
							Payment terms<br />
							</td>
							<td>
							'.$companyDetails->id .'<br />
							<strong>'.$companyDetails->company_name .'</strong><br />
							'.$companyDetails->address .'<br />
							
							CL'.$invoiceID.'<br />
							'.date('d/m/Y').'<br />
							<strong>'.date('d/m/Y', strtotime('+7 days')).'</strong><br />
							'.date('d/m/Y', $periodBeg).'<br />
							'.date('d/m/Y', $periodEnd).'<br />
							Net 7 days
							</td>
						</tr>
					</table>
			   </td>
		       <td class="pride">Page-'.$obj_pdf->getPage().'<br />Confidential information</td>
		  </tr>
         
		</tbody>
		</table><div class="header-section-bot"></div><br />';
			$company_email_ca = $companyDetails->company_email;
			$seqNum=str_pad($invoiceID, 10, "0", STR_PAD_LEFT);
			$dateTime = date('Ymdhis');
			/*HH Code Started*/		  
			$datContent .= "HH09".$seqNum.$dateTime."ALL ";/*Record Type(2),Version(2),Sequence Number(10),File Date(14),File Type(4)*/
			$datContent .= "PROD";/*File Use(4)*/
			$datContent .= "                                                                                                                                                                                                                                                                        ";/*Filler(264)*/
			/*HH Code Ended*/		
		$content .='<table class="custom-css" style="border: 1px solid #D9D9D9 ; clear: both; border-spacing: 0; border-collapse: collapse;" cellpadding="3">
		<tbody>		
           <tr>
		      <th style="width:12%;">Card #</th>
		      <th style="width:8%;">Driver name</th>
			  <th style="width:7%;">Transaction #</th>
			  <th style="width:7%;">Site name</th>
			  <th style="width:4%;">Site #</th>
			  <th style="width:6%;">City</th>
			  <th style="width:3%;">State</th>
			  <th style="width:6%;">Date</th>
			  <th style="width:5%;">Time</th>
			  <th style="width:4%;">Unit #</th>
			  <th style="width:5%;">Product</th>
			  <th style="width:3%;">UOM</th>
			  <th style="width:7%;">Price per unit</th>
			  <th style="width:7%;">Total quantity</th>
			  <th style="width:6%;">HST/GST</th>
			  <th style="width:5%;">Total</th>
			  <th style="width:5%;">Currency</th>
           </tr>';
			$this->db->select('users.*, drivers.*, transactions.*, cards.unit_number, cards.driver_id');
			$this->db->join('users', 'users.id = cards.company_id');
			$this->db->join('drivers', 'drivers.company_id = cards.company_id', 'LEFT');
			$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0));
			$this->db->group_by('transactions.card_number');
			$CAcardCount = $this->db->get('cards')->result();
			$totalQuantity = 0; $grandTotal = 0; $grandTax=0;
			$jsonInc = 0;
			$driverName = "-";
			$transactionCount = 0;
			foreach($CAcardCount as $cardCountRows){
				$productNameJsonDecode = json_decode($cardCountRows->category);
				$pridePriceJsonDecode = json_decode($cardCountRows->pride_price);
				$EFSpriceJsonDecode = json_decode($cardCountRows->unit_price);
				$productQuantityJsonDecode = json_decode($cardCountRows->quantity);
				$explodeDateTime = explode(' ', $cardCountRows->transaction_date);	
				$transactionDate = $explodeDateTime[0];		
				$transactionTime = $explodeDateTime[1];
				$driverid = $cardCountRows->driver_id;//pre($cardCountRows->driver_id);die;
				if(!empty($driverid)){
					$getDriverName = $this->db->select('name')->where('id', $driverid)->get('drivers')->row();
					$driverName = $getDriverName->name;
				}				
				
				$rowCount=0; $finalGST=0; $finalPST=0; $finalQST=0; 
				foreach($productNameJsonDecode as $productCountJsonDecodeRows){
					$calcProduct = $productQuantityJsonDecode[$rowCount] * $pridePriceJsonDecode[$rowCount];
					$total = floor($calcProduct*100)/100;
					$transactionCount++;
					/* Product Taxes Start */		
					$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $cardCountRows->gas_station_state)->get('tax')->result();
					
					foreach($getTaxRate as $taxTypeRows){
						
						if($taxTypeRows->tax_type == 'gst'){
							$gstRate = str_replace('%', '', $taxTypeRows->tax_rate);
							$finalGST = $total * $gstRate / 100;
						}
						if($taxTypeRows->tax_type == 'pst'){
							$pstRate = str_replace('%', '', $taxTypeRows->tax_rate);
							$finalPST = $total * $pstRate / 100;
						}
						if($taxTypeRows->tax_type == 'qst'){
							$qstRate = str_replace('%', '', $taxTypeRows->tax_rate);
							$finalQST = $total * $qstRate / 100;
						}
						$combineTaxes = $finalGST + $finalPST + $finalQST;	
						$totalTaxAmount = floor($combineTaxes*100)/100;
					}		   
					$content .='<tr>
						<td style="width:12%;">'.$cardCountRows->card_number .'</td>
						<td style="width:8%;">'.$driverName.'</td>
						<td style="width:7%;">'.$cardCountRows->transaction_id .'</td>
						<td style="width:7%;">'.$cardCountRows->gas_station_name .'</td>
						<td style="width:4%;">'.$cardCountRows->gas_station_id .'</td>
						<td style="width:6%;">'.$cardCountRows->gas_station_city .'</td>
						<td style="width:3%;">'.$cardCountRows->gas_station_state .'</td>
						<td style="width:6%;">'.$transactionDate.'</td>
						<td style="width:5%;">'.$transactionTime.'</td>
						<td style="width:4%;">'.$cardCountRows->unit_number .'</td>
						<td style="width:5%;">'.$productNameJsonDecode[$rowCount].'</td>
						<td style="width:3%;">L</td>
						<td style="width:7%;">$ '.$pridePriceJsonDecode[$rowCount].'</td>
						<td style="width:7%;">'.$productQuantityJsonDecode[$rowCount].'</td>
						<td style="width:6%;">$ '.$totalTaxAmount.'</td>
						<td style="width:5%;">$ '.$total.'</td>
						<td style="width:5%;">'.$cardCountRows->billing_currency .'</td>
					</tr>';
					$totalQuantity += $productQuantityJsonDecode[$rowCount];
					$grandTax += $totalTaxAmount;
					$grandTotal += floor($total*100)/100;
					/*trans_data field*/
					$CAtransJsonArrayObject = (array($cardCountRows->card_number => array('product_name'=>$productNameJsonDecode[$rowCount], 'quantity'=>$productQuantityJsonDecode[$rowCount], 'unit_price'=>$pridePriceJsonDecode[$rowCount], 'taxamount'=>$totalTaxAmount, 'amountwithouttax' => $total)));
					$transationDetails[$jsonInc] = $CAtransJsonArrayObject;
					/*invoice_data field*/
					$CAjsonArrayObject = (array('card_number' =>$cardCountRows->card_number,'transaction_date' => $cardCountRows->transaction_date, 'transaction_id' => $cardCountRows->transaction_id));
					$arr[$jsonInc] = $CAjsonArrayObject;

					/*DAT file content */
					$transid = str_pad($cardCountRows->transaction_id, 10, "0", STR_PAD_LEFT);
					$cardNumber = str_pad($cardCountRows->card_number, 25, " ", STR_PAD_RIGHT);
					$invoiceNum = str_pad($cardCountRows->invoice, 12, " ", STR_PAD_RIGHT);
					$locId = str_pad($cardCountRows->gas_station_id, 10, "0", STR_PAD_LEFT);
					$transDateTime = date('Ymdhi', strtotime($cardCountRows->transaction_date));
					$authCode = '0000000000';
					$contractId = '0000000000';
					$issuerId = '0000000000';
					$carrierId = '0000000000';
					$supplierId = '0000000000';
					$reportedCarrier = date('Ymdhis', strtotime($cardCountRows->transaction_date));
					$handEntered = 'N';
					$overrideCard = 'N';
					$totDiscountAmt = '00000000000';
					$carrierFee = '00000000000';
					$supplierFee = '00000000000';
					$settlementId = '00000000000';
					$statementId = '00000000000';
					$dicountType = 'W';
					$POSdate = date('Ymdhi', strtotime($cardCountRows->transaction_date));
					$currencyCode = 'USD';
					$conversionRate = '00000000000';
					$preferTot = '00000000000';
					$country = 'C';
					$biilingCountry = 'C';
					$invoiceTot = '00000000000';
					$transType  = 'F';
					$isCredit = 'D';
					$settleAmount = '00000000000';
					$locType = '0000';
					$transSysType = '000';
					$cardPolicy = '0000';
					$netTotDollars = '00000000000';
					$fundedTot = '00000000000';
					$authCode = '0000000000';
					/*TA Code Started*/
					$datContent .= "TA09".$seqNum.$transid.$cardNumber.$invoiceNum.$locId.$transDateTime.$authCode.$contractId.$issuerId;/*Record Type(2),Version(2),Sequence Number(10),card number(25),invoiceNum(12), locationId(10), transDateTime(12), authCode(10), contractId(10), issuerId(10)*/
					$datContent .= $carrierId.$supplierId.$reportedCarrier;/*carrierId(10), supplierId(10), reportedCarrier(14)*/
					$datContent .= $handEntered.$overrideCard.$totDiscountAmt;/*handEntered(1), overrideCard(1), totDiscountAmt(11)*/
					$datContent .= $carrierFee.$supplierFee.$settlementId;/*carrierFee(11), supplierFee(11), settlementId(11)*/
					$datContent .= $statementId.$dicountType.$POSdate;/*statementId(11), dicountType(1), POSdate(14)*/
					$datContent .= $currencyCode.$conversionRate.$preferTot;/*currencyCode(3), conversionRate(11), preferTot(11)*/
					$datContent .= $country.$biilingCountry.$invoiceTot;/*country(1), biilingCountry(1), invoiceTot(11)*/
					$datContent .= $transType.$isCredit.$settleAmount;/*transType(1), isCredit(1), settleAmount(11)*/
					//$datContent .= $locType/*locType(4)*/
					//$datContent .= "       ";/*Filler(7)*/
					$datContent .= $transSysType;/*Transaction System Type(3)*/
					$datContent .= $cardPolicy;/*Card Policy(4)*/
					$datContent .= "                        ";/*Filler(24)*/
					//$datContent .= $transSysType.$cardPolicy;/*Transaction System Type(3), Card Policy(4)*/
					//$datContent .= $netTotDollars.$fundedTot;/*Net Total Dollars(11), Funded Total(11)*/
					//$datContent .= $authCode;/*Authorization Code(10)*/
					//$datContent .= "                                                                                           ";/*Filler(91)*/
					/*TA Code Ended*/
					$lineId = '00';
					$productCat = '0000';
					$quantity = str_pad(str_replace('.','',$productQuantityJsonDecode[$rowCount]), 7, "0", STR_PAD_LEFT);
					$amount = str_pad(str_replace('.','',number_format($pridePriceJsonDecode[$rowCount], 4)), 10, "0", STR_PAD_LEFT);
					$retailAmt = str_pad(str_replace('.','',$EFSpriceJsonDecode[$rowCount]), 10, "0", STR_PAD_LEFT);
					$fuelType = '00000000';
					$serviceType = '00';
					$fuelUseType = '00';
					$ppuDiscounted = '0000000';
					$retailPPU = str_pad(str_replace('.','',$EFSpriceJsonDecode[$rowCount]), 10, "0", STR_PAD_LEFT);
					$productNum = '000';
					$unitOdMeasure = 'L';
					$discountAmt = '00000000000';
					/*TL Code Started*/
					$datContent .= "TL09".$seqNum.$transid.$lineId;/*Record Type(2),Version(2),Sequence Number(10), Transaction Id(10), Line Id(2)*/
					$datContent .= $productCat.$quantity.$amount;/*Product Category(4), Quantity(7), Amount (Discounted)(11)*/
					$datContent .= $retailAmt.$fuelType.$serviceType.$fuelUseType;/*Retail Amount(11), Fuel Type(8), Service Type(2), Fuel Use Type(2)*/
					$datContent .= $ppuDiscounted.$retailPPU.$productNum;/*PPU(Discounted)(7), Retail PPU(7), Product Number(3)*/
					$datContent .= $unitOdMeasure.$discountAmt;/*Unit of measurement(1), Discount Amount(11)*/
					/*TL Code Ended*/
					$taxDisc = '0000000000';
					$taxCode = '0000000000';
					$taxAmount = '00000000000';
					$grossNetFlag = '0';
					$exemptFlag = '0';
					$taxRateType = '0';
					$taxRate = '00000000000';
					/*TE Code Started*/
					$datContent .= "TE09".$seqNum.$transid.$lineId;/*Record Type(2), Version(2), Sequence Number(10), Transaction Id(10), Line Id(2)*/
					$datContent .= $taxDisc.$taxCode.$taxAmount.$grossNetFlag;/*Tax Desc.(10), Tax Code(10), Tax Amount(11), Gross Net Flag(1) */
					$datContent .= $exemptFlag.$taxRateType.$taxRate;/*Exempt Flag(1), Tax Rate Type(1), Tax Rate(11) */
					$datContent .= "                                                                                                                                                                                                                                     ";/*Filler(229)*/
					/*TE Code Ended*/
					$locationId = '0000000000';
					$locType = '0000';
					$locName = '0000000000000000000000000';
					$locCity = '0000000000000000000000000';
					$locState = '00';
					$locCountry = '0';
					$opisId = '00000000000';
					$POStimeZone = 'C';
					$chainCode = '000';
					$transId = '0000000000';
					$address1 = '00000000000000000000000000000000000000000000000000';
					$postalCode = '0000000000';
					$latitude = '00000000000000000';
					$longitude = '00000000000000000';
					$pumpNumber = '0000000';
					/*LL Code Started*/
					$datContent .= "LL09".$seqNum.$locationId.$locType;/*Record Type(2),Version(2),Sequence Number(10), Location Id(10), Location Type
					Code(4)*/
					$datContent .= $locName.$locCity.$locState.$locCountry;/*Loc. Name(25), Loc. City(25), Loc. State(2), Loc. Country(1) */
					$datContent .= $opisId.$POStimeZone.$chainCode.$transId;/*Opis ID(11), POS Time Zone(1), Chain Code(3), Transaction Id(10) */
					$datContent .= $address1.$postalCode.$latitude.$longitude;/*Address1(50), Postal Code (zip)(10), Latitude(17), Longitude(17) */
					$datContent .= $pumpNumber;/*Pump Number(7) */
					$datContent .= "                                                                                             ";/*Filler(93)*/
					/*LL Code Ended*/
					$infoIdCode = '0000';
					$infoValue = '0000000000000000000000000';
					/*TI Code Started*/
					$datContent .= "TI09".$seqNum.$transId;/*Record Type(2),Version(2),Sequence Number(10), Transaction Id(10)*/
					$datContent .= $infoIdCode.$infoValue;/*Info Id Code(4), Info Value(25) */
					$datContent .= "                                                                                                                                                                                                                                                       ";/*Filler(247)*/
					/*TI Code Ended*/
					$totInfoRecord = '0000000000';
					$totLineRecords = '0000000000';
					$totTransAmtSign = '0';
					$totTransAmt = '00000000000';
					/*TT Code Started*/
					$datContent .= "TT09".$seqNum.$totInfoRecord;/*Record Type(2),Version(2),Sequence Number(10), Total Info Records(10) */
					$datContent .= $totLineRecords.$totTransAmtSign;/*Total Line Records(10), Total Transaction Amount Sign(1)  */
					$datContent .= $totTransAmt.$transId;/*Total Transaction Amount(11), Transaction Id(10) */
					$datContent .= "                                                                                                                                                                                                                                                    ";/*Filler(244)*/
					/*TT Code Ended*/		
					$rowCount++;$jsonInc++;
				}
				/* Set Invoice_status as 1 */
				$this->db->where('transaction_id', $cardCountRows->transaction_id);
				$this->db->set('invoice_status', 1);
				$this->db->update('transactions'); 				
				$transactionDetails = json_encode($transationDetails);
				$invoice_data = json_encode($arr);
			}
		$content .='<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td colspan="2">Subtotal</td>
			<td>'.$totalQuantity.'</td>
			<td>$ '.$grandTax.'</td>
			<td>$ '.$grandTotal.'</td>
			<td>CAD</td>
		</tr>
	</tbody>
	</table>';
		$totNumOfTrans = str_pad(str_replace('.','',$transactionCount), 10, "0", STR_PAD_LEFT);
		$totNumOfRecs = str_pad(str_replace('.','',$transactionCount), 10, "0", STR_PAD_LEFT);
		$totTransAmtSign = '+';
		$totTransAmt = str_pad(str_replace('.','',$grandTotal), 11, "0", STR_PAD_LEFT);
		/*HT Code Started*/
		$datContent .= "HT09".$seqNum;/*Record Type(2),Version(2),Sequence Number(10)*/
		$datContent .= $totNumOfTrans.$totNumOfRecs.$totTransAmtSign;/*Total Number of Trans.(10), Total Number of Recs.(10), Total Transaction Amount Sign(1) */
		$datContent .= $totTransAmt;/*Total Transaction Amount(11) */
		$datContent .= "                                                                                                                                                                                                                                                              ";/*Filler(254)*/
		/*HT Code Ended*/ 

		//Insert invoice data in transaction invoice table			
		$data['invoice_id'] = "CL".$invoiceID;
		$data['company_id'] = $cid;
		$data['invoice_date'] = date('Y-m-d');
		$data['billingCurrency'] = 'CAD';
		$data['invoice_data'] = $invoice_data;
		$data['trans_data'] = $transactionDetails;
		$data['status'] = 0;	
		$data['grand_total'] = $grandTotal;
		$data['date_created'] = date('Y-m-d H:i:s', time() + 1 * 28 * 60);
		$data['date_modified'] = date('Y-m-d H:i:s', time() + 1 * 28 * 60);
		$this->db->insert('transaction_invoice', $data);
		//pre($content);die;
		$obj_pdf->writeHTML($content);	
		$file = fopen(APPPATH ."modules/cronjob/invoice_pdf/EFS_352771_110_".$invoiceID."_CAD_".date('Ymd').".dat","w");
		//fwrite($file,"$a\n$b$c\n$e$d$f$k$g$l$h$m\n$n$o$p$q$r$s$t\n$u$v");
		fwrite($file,"$datContent");
		fclose($file);
		ob_end_clean();
		//if (ob_get_contents()) ob_end_clean();	

		//$obj_pdf->Output('sample.pdf', 'I'); die;		
		//$obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
		$obj_pdf->Output(FCPATH . 'assets/modules/invoices/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
		
		//Send generated invoice to company and then delete pdf
		$this->load->library('email');
		$ca_subject = 'CAD Transactions Invoice';
		$ca_body = 'Currency CAD';
		//$pdfFilePath = APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
		$pdfFilePath = FCPATH . 'assets/modules/invoices/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
		$result = $this->email
					->from('info@pridediesel.com', 'From Pride Diesel')
					//->to('jagdishchander6373@gmail.com')
					->to($company_email_ca)
					->subject($ca_subject)
					->message($ca_body)
					->attach($pdfFilePath)
					->send();
		//$this->email->clear($pdfFilePath);

		//if($result) {
			//echo "Send";
			//unlink($pdfFilePath); //for delete generated pdf file. 
		//} 
		//unset($cardDetail, $pdfFilePath);	
		//}		
	}	
	
}