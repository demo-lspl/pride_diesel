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
		//die;
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
			
		$subTotal = 0; $gstCount = 0; $pstCount = 0; $productprice = 0; $jsonArray = 0;
		$transationDetails = []; $jsonInc = 0;
		foreach($usdTrans as $cardDetails){

		$company_name = $cardDetails->company_name;
		$accountId = $cardDetails->id;
		$address = $cardDetails->address;

		$decodeCategory = json_decode($cardDetails->category);
		$decodeQuantity = json_decode($cardDetails->quantity);
		$decodeAmount = json_decode($cardDetails->amount);
		$decodeUnitPrice = json_decode($cardDetails->unit_price);		  
		
		$companyTypeResult = $this->db->where('id', $cardDetails->company_type)->get('company_types')->row();
		$typeOfCompany = strtolower($companyTypeResult->company_type);
		
		
		/* Multiple Categoty check loop */
		$catcount = 0;
		for($catcnt=0; $catcnt<count($decodeCategory); $catcnt++){
		//$productName = $cardDetails->category;
		$productName = $decodeCategory[$catcount];
		$productPriceList = $this->db->get('pricelist')->row();
		$pricelistDecoded = json_decode($productPriceList->price_descr);
		$this->db->select('users.fix_cost_data');
		$this->db->from('cards');		
		$this->db->join('users', 'users.id=cards.company_id');
		$this->db->where('cards.card_number', $cardDetails->card_number);
		$getFixPriceStatus = $this->db->get()->row();		
		$finalProductPrice = 0;
		if(!empty($getFixPriceStatus->fix_cost_data)){		
		$decodeFixPriceProduct = json_decode($getFixPriceStatus->fix_cost_data);

		foreach($decodeFixPriceProduct as $decodeFixPriceProductRows){
			if($decodeFixPriceProductRows->fix_cost_product == $productName){
				$finalProductPrice = $decodeFixPriceProductRows->fix_cost_product_amt;
			}
		}
		}		

		if($finalProductPrice == ''){
			$finalProductPrice = $productPriceSaved;
		}		
		foreach($pricelistDecoded as $key=>$pricelistDecodedRow){
			//if(array_key_exists($cardDetails->category, $pricelistDecodedRow)){
			if(array_key_exists($productName, $pricelistDecodedRow)){
			unset($pricelistDecodedRow->$productName[$productprice]->efs_amt);
			$companyTypePrice = $pricelistDecodedRow->$productName[$productprice]->$typeOfCompany[$productprice];
			}
		}		  
		  
		/* Exclude Company Type Price */
		$getGasStation = $this->db->where('name', $cardDetails->gas_station_name)->get('gas_stations')->row();
		if(!empty($getGasStation->exclude_pack_price) && $getGasStation->exclude_pack_price == 1){
			$companyTypePrice = 0;
		}
		$taxapplicable = $this->db->where('product_name', $productName)->get('products')->row();
		  
			if(!empty($taxapplicable->tax) ){
			$taxArray = json_decode($taxapplicable->tax);


			foreach($taxArray as $key=>$taxArrayRow){	
			$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>$cardDetails->gas_station_state))->get('tax')->row();

			//PFT || PCT || FET								
			if(!empty($taxOutput['pft']->tax_rate)){$pft = str_replace('%', '', $taxOutput['pft']->tax_rate);}else{$pft = 0;}
			if(!empty($taxOutput['pct']->tax_rate)){$pct = str_replace('%', '', $taxOutput['pct']->tax_rate);}else{$pct = 0;}
			if(!empty($taxOutput['fet']->tax_rate)){$fet = str_replace('%', '', $taxOutput['fet']->tax_rate);}else{$fet = 0;}
			//GST || PST
			if(!empty($taxOutput['gst']->tax_rate)){$gst = str_replace('%', '', $taxOutput['gst']->tax_rate);}else{$gst = 0;}
			if(!empty($taxOutput['pst']->tax_rate)){$pst = str_replace('%', '', $taxOutput['pst']->tax_rate);}else{$pst = 0;}
			}
			$ppfTot = $pft+$pct+$fet;
			}
			if(empty($ppfTot)){$ppfTot = 0;}
			$tax3withprice = number_format($finalProductPrice+$companyTypePrice, 4) + number_format($ppfTot, 4);
			$amtAfterTax = $decodeQuantity[$catcount] * number_format($tax3withprice, 4);
			$priceByCompany = number_format($finalProductPrice+$companyTypePrice, 4);
			if(empty($gst)){$gst = 0;}
			if(empty($pst)){$pst = 0;}
			
			$includeGST = $amtAfterTax * $gst / 100;
			$gstCount += $includeGST;
			
			$includePST = $amtAfterTax * $pst / 100;
			$pstCount += $includePST;
			
			$amoutWithoutTax = number_format($finalProductPrice+$companyTypePrice, 4) * $decodeQuantity[$catcount];
			$GrandTotalwithTax = $amtAfterTax + $includeGST + $includePST;

			$cardDetail[$count]['fet_'.$count] = $fet;
			$cardDetail[$count]['pct_'.$count] = $pct;
			$cardDetail[$count]['pft_'.$count] = $pft;
			$cardDetail[$count]['gst_'.$count] = $gst;
			$cardDetail[$count]['pst_'.$count] = $pst;			
			$cardDetail[$count]['amountwithouttax_'.$count] = $amoutWithoutTax;
			$cardDetail[$count]['grandamount_'.$count] = $GrandTotalwithTax;
			$cardDetail[$count]['ppftotal_'.$count] = $ppfTot;
			//$cardDetail['transid'] = $cardDetails->transid;
			$company_email_usd = $cardDetails->company_email;
			$billing_currency = $cardDetails->billing_currency;
			$cardDetail[$count]['card_number_'.$count] = $cardDetails->card_number;			
			$cardDetail[$count]['category_'.$count] = $productName;
			$cardDetail[$count]['transaction_date_'.$count] = $cardDetails->transaction_date;
			$cardDetail[$count]['quantity_'.$count] = $decodeQuantity[$catcount];
			$cardDetail[$count]['amount_'.$count] = $decodeAmount[$catcount];
			$cardDetail[$count]['unit_price_'.$count] = $priceByCompany;
			$cardDetail[$count]['gas_station_name_'.$count] = $cardDetails->gas_station_name;
			
			/*trans_data field*/
			$transJsonArrayObject = (array($cardDetails->card_number => array('product_name'=>$productName, 'quantity'=>$productQuantity, 'unit_price'=>number_format($priceByCompany, 4), 'fet' => $fet, 'pct'=>$pct, 'pft'=>$pft, 'gst'=>$gst, 'pst'=>$pst, 'amountwithouttax' => number_format($amoutWithoutTax, 2) , 'amountwithtax' => number_format($GrandTotalwithTax, 2))));
			$transationDetails[$jsonInc] = $transJsonArrayObject;
			/*invoice_data field*/
			$jsonArrayObject = (array('card_number' =>$cardDetails->card_number,'transaction_date' => $cardDetails->transaction_date, 'transaction_id' => $cardDetails->transaction_id));
			$arr[$jsonInc] = $jsonArrayObject;				
			$subTotal += $amtAfterTax;
			unset($taxOutput, $ppfTot, $gst, $pst);
			$count++;$catcount++;$jsonInc++;
			}$transactionDetails = json_encode($transationDetails);$invoice_data = json_encode($arr);
		  }
		$this->data['maxInvId'] = $this->account_model->get_max_trans_inv_id();

		$this->db->select('transactions.*, transactions.id as transid, users.*, cards.*');
		$this->db->from('cards');
		$this->db->join('users', 'users.id = cards.company_id');
		$this->db->join('transactions', 'transactions.card_number = cards.card_number');
		$this->db->where(array('cards.company_id'=> $cid, 'transactions.invoice_status'=>0));
		
		$allResults = $this->db->get()->result();
			
			/* $card_numberLength = count($allResults);
				if($card_numberLength >0){
					$arr = [];
					$invoicedatainc = 0;
					while($invoicedatainc < $card_numberLength) {
						
						$jsonArrayObject = (array('card_number' =>$allResults[$invoicedatainc]->card_number,'transaction_date' => $allResults[$invoicedatainc]->transaction_date, 'transaction_id' => $allResults[$invoicedatainc]->transaction_id));
						$arr[$invoicedatainc] = $jsonArrayObject;
						$invoicedatainc++;				
					}
					$invoice_data = json_encode($arr);
				}else{
					$invoice_data = '';
				} */			
				for($upd =0; $upd <count($allResults); $upd++){
					$this->db->where('id', $allResults[$upd]->transid);
					$this->db->set('invoice_status', 1);
					$this->db->update('transactions');
				}

		  empty($this->data['maxInvId']->id)?$invoiceID = 1:$invoiceID = $this->data['maxInvId']->id + 1;
		  //Insert invoice data in transaction invoice table
		  $data['invoice_id'] = "CL".$invoiceID;
		  $data['company_id'] = $cid;
		  $data['invoice_date'] = date('Y-m-d');
		  $data['invoice_data'] = $invoice_data;
		  $data['trans_data'] = $transactionDetails;		  
		  $data['sub_total'] = number_format($subTotal, 2);
		  $data['gst_total'] = number_format($gstCount, 2);
		  $data['pst_total'] = number_format($pstCount, 2);
		  $data['grand_total'] = number_format($subTotal + $gstCount + $pstCount, 2);
		  $data['date_created'] = date('Y-m-d H:i:s');
		  $data['date_modified'] = date('Y-m-d H:i:s');;
		  $this->db->insert('transaction_invoice', $data);
		  
		$divide = $count/10;
		//$after_divide =  round($divide);
		$after_divide =  ceil($divide);

		if($after_divide <=  1){
			$after_divide = 1;
		}
	if ( $count >= 0 ){  //If there are more than 0 transations

	$k =0;
		for ($j = 0; $j < $after_divide; $j++){
		$obj_pdf->AddPage();
		
			$content = '';
				$content .='<table border="0" cellspacing="0" cellpadding="5">
							<tr>
								<td width="70%"><img src="'.$image.'" alt="company_logo" width="200" height="70" border="0"/></td>
								<td width="30%">
								<table border="1" cellspacing="0" cellpadding="3">
									<tr><td colspan="3" align="center">INVOICE</td></tr>
									<tr><th align="center">DATE</th><th align="center">INVOICE</th>
									<th align="center">PAGE</th></tr>
									<tr><td align="center">'.$data['invoice_date'].'</td><td align="center">'.$data['invoice_id'].'</td><td align="center">'.$obj_pdf->getAliasNumPage().'</td></tr>
									<tr><td align="center">Account#</td><td align="center">'.$data['company_id'].'</td></tr>
									<tr><td colspan="3">GST/HST # 808170526RT0001</td></tr>
								</table>
								</td>
							</tr>
						</table>';	  
			  $content .= '  
				<table border="0" cellspacing="0" cellpadding="5" nobr="true">
					<tr>
						<td>Bill To:</td><td></td>
					</tr>
					<tr>
						<td width="20%">'.$company_name.'<br />'.strtoupper($address).'</td>
						<td width="80%">
						</td>
					</tr>
				</table>
				<div style="margin-top: 20px;"></div>';

			  $content .= '<table class="table" border="" style="border: 1px solid #1e1e1e;" cellspacing="0" cellpadding="3" nobr="true">  
				   <tr >  
						<th width="16%" style="font-size:9px;text-align:center;border-bottom: 1px solid #1e1e1e;">SITE</th>  
						<th width="10%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">GAS STATION</th>  
						<th width="11%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">DATE & TIME</th>
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FET</th>  
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PFT</th>  
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PCT</th> 						
						<th width="4%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">QTY</th>   
						<th width="8%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FUEL TAXES</th>   
						<th width="8%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PRICE UNIT</th>
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PST</th>   
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">GST</th> 						
						<th width="9%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">AMT WITHOUT TAX</th>   
						<th width="9%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">AMT WITH TAX</th>   
				   </tr>  
			  ';  
  
			for($i = 0 ;$i<10;$i++){
				//if($cardDetail[$k]['quantity_'.$k] != ''){
				if($k < count($cardDetail) && !empty($cardDetail)){	
					$content .= "<tr>
					<td>CARD: ".$cardDetail[$k]['card_number_'.$k]."</td>
					<td></td>
					<td></td>
					<td></td>"
					."</tr><tr><td>UNIT: ".$cardDetail[$k]['unit_price_'.$k]."</td></tr>
					<tr><td>PRODUCT: ".$cardDetail[$k]['category_'.$k]." </td><td>".$cardDetail[$k]['gas_station_name_'.$k]."</td><td>".$cardDetail[$k]['transaction_date_'.$k]."</td><td>".number_format($cardDetail[$k]['fet_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pft_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pct_'.$k], 4)."</td><td>".$cardDetail[$k]['quantity_'.$k]."</td><td>".number_format($cardDetail[$k]['ppftotal_'.$k], 4)."</td><td>".$cardDetail[$k]['unit_price_'.$k]."</td><td>".number_format($cardDetail[$k]['pst_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['gst_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['amountwithouttax_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['grandamount_'.$k], 2)."</td></tr>
					";
					  $total_sub += $cardDetail[$k]['amount_'.$k];
				  $k++;
				}
			}
			$content .= '</table>';
			if($j == $after_divide-1){			
				$afterGST = $total_sub * 13 /100;		
				$content .= '<table border="0" nobr="true">
				<tr>
					<td width="70%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
					<td width="30%">
					<table border="1" cellpadding="3" nobr="true"> 
						<tr><td>SUB-TOTAL</td><td>'.number_format($subTotal, 2).'</td></tr>
						<tr><td>G.S.T.</td><td>'.number_format($gstCount, 2).'</td></tr>
						<tr><td>P.S.T.</td><td>'.number_format($pstCount, 2).'</td></tr>
						<tr><td >TOTAL</td><td style="border-left: 1px solid transparent;">'.number_format($subTotal + $gstCount + $pstCount, 2).'</td></tr>
					</table>
					</td>
				</tr>	
				</table>
				<div style="margin-top: 20px;"></div>
				<table border="0" nobr="true">
				<tr>
					<td width="30%">Please remit payment to:<br /><strong>PRIDE DIESEL INC.</strong><br />6050 Dixie Rd<br />Missisauga ON L5T 1A6</td>
					<td width="40%"></td>
					<td width="30%" align="right"><strong>ACCOUNTS RECIEVABLE</strong><br />OFFICE 647-618-7184<br />x 244<br />FAX 866-867-8922<br />EMAIL info@pridediesel.com</td>
				</tr>	
				</table><h3 style="text-align: center;">Thank you for your business.</h3>';
			}
			
			if($j != $after_divide-1){
				$content .= '<table border="0" nobr="true">
				<tr>
					<td width="80%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
					<td width="20%">
					<table border="1" cellpadding="3" nobr="true"> 
						<tr><td>SUB-TOTAL</td><td></td></tr>
						<tr><td></td><td></td></tr>
						<tr><td></td><td></td></tr>
						<tr><td style="border-right: 1px solid transparent;">TOTAL</td><td style="border-left: 1px solid transparent;"></td></tr>
					</table>
					</td>
				</tr>	
				</table>
				<div style="margin-top: 20px;"></div>
				<table border="0" nobr="true">
				<tr>
					<td width="30%">Please remit payment to:<br /><strong>PRIDE DIESEL INC.</strong><br />6050 Dixie Rd<br />Missisauga ON L5T 1A6</td>
					<td width="40%"></td>
					<td width="30%" align="right"><strong>ACCOUNTS RECIEVABLE</strong><br />OFFICE 647-618-7184<br />x 244<br />FAX 866-867-8922<br />EMAIL info@pridediesel.com</td>
				</tr>	
				</table><h3 style="text-align: center;">Thank you for your business.</h3>';
			}
			$obj_pdf->writeHTML($content);	
		}
	}		
	ob_end_clean();

    //$obj_pdf->Output('sample.pdf', 'I');		
    $obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_'.$billing_currency."_".$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
	
	//Send generated invoice to company and then delete pdf
	$this->load->library('email');
	$usd_subject = 'USD Transactions Invoice';
	$usd_body = 'Currency USD';
	$pdfFilePath = APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_'.$billing_currency."_".$data['invoice_date']."_".$data['company_id'].'.pdf';
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
	
		$subTotal = 0; $gstCount = 0; $pstCount = 0; $productprice = 0; $jsonArray = 0;
		$transationDetails = []; $jsonIncCAD = 0;
		foreach($cadTrans as $cardDetails){

		$company_name = $cardDetails->company_name;
		$accountId = $cardDetails->id;
		$address = $cardDetails->address;

		$decodeCategory = json_decode($cardDetails->category);
		$decodeQuantity = json_decode($cardDetails->quantity);
		$decodeAmount = json_decode($cardDetails->amount);
		$decodeUnitPrice = json_decode($cardDetails->unit_price);		  
		
		$companyTypeResult = $this->db->where('id', $cardDetails->company_type)->get('company_types')->row();
		$typeOfCompany = strtolower($companyTypeResult->company_type);		
		
		/* Multiple Categoty check loop */
		$catcount = 0;
		for($catcnt=0; $catcnt<count($decodeCategory); $catcnt++){
		$productName = $decodeCategory[$catcount];
		$productPriceList = $this->db->get('pricelist')->row();
		$pricelistDecoded = json_decode($productPriceList->price_descr);

		$this->db->select('users.fix_cost_data');
		$this->db->from('cards');		
		$this->db->join('users', 'users.id=cards.company_id');
		$this->db->where('cards.card_number', $cardDetails->card_number);
		$getFixPriceStatus = $this->db->get()->row();		
		$finalProductPrice = 0;
		if(!empty($getFixPriceStatus->fix_cost_data)){		
		$decodeFixPriceProduct = json_decode($getFixPriceStatus->fix_cost_data);

		foreach($decodeFixPriceProduct as $decodeFixPriceProductRows){
			if($decodeFixPriceProductRows->fix_cost_product == $productName){
				$finalProductPrice = $decodeFixPriceProductRows->fix_cost_product_amt;
			}
		}
		}
		if($finalProductPrice == ''){
			$finalProductPrice = $productPriceSaved;
		}		
		foreach($pricelistDecoded as $key=>$pricelistDecodedRow){
			if(array_key_exists($productName, $pricelistDecodedRow)){
			unset($pricelistDecodedRow->$productName[$productprice]->efs_amt);
			$companyTypePrice = $pricelistDecodedRow->$productName[$productprice]->$typeOfCompany[$productprice];
			}
		}		  
		/* Exclude Company Type Price */
		$getGasStation = $this->db->where('name', $cardDetails->gas_station_name)->get('gas_stations')->row();
		if(!empty($getGasStation->exclude_pack_price) && $getGasStation->exclude_pack_price == 1){
			$companyTypePrice = 0;
		}		
		$taxapplicable = $this->db->where('product_name', $productName)->get('products')->row();
		  
			if(!empty($taxapplicable->tax) ){
			$taxArray = json_decode($taxapplicable->tax);

			foreach($taxArray as $key=>$taxArrayRow){
			$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>$cardDetails->gas_station_state))->get('tax')->row();

			//PFT || PCT || FET								
			if(!empty($taxOutput['pft']->tax_rate)){$pft = str_replace('%', '', $taxOutput['pft']->tax_rate);}else{$pft = 0;}
			if(!empty($taxOutput['pct']->tax_rate)){$pct = str_replace('%', '', $taxOutput['pct']->tax_rate);}else{$pct = 0;}
			if(!empty($taxOutput['fet']->tax_rate)){$fet = str_replace('%', '', $taxOutput['fet']->tax_rate);}else{$fet = 0;}
			//GST || PST
			if(!empty($taxOutput['gst']->tax_rate)){$gst = str_replace('%', '', $taxOutput['gst']->tax_rate);}else{$gst = 0;}
			if(!empty($taxOutput['pst']->tax_rate)){$pst = str_replace('%', '', $taxOutput['pst']->tax_rate);}else{$pst = 0;}
			}
			$ppfTot = $pft+$pct+$fet;
			}
			if(empty($ppfTot)){$ppfTot = 0;}
			$tax3withprice = number_format($finalProductPrice+$companyTypePrice, 4) + number_format($ppfTot, 4);
			$amtAfterTax = $decodeQuantity[$catcount] * number_format($tax3withprice, 4);
			$priceByCompany = number_format($finalProductPrice+$companyTypePrice, 4);			
			if(empty($gst)){$gst = 0;}
			if(empty($pst)){$pst = 0;}
			
			$includeGST = $amtAfterTax * $gst / 100;
			$gstCount += $includeGST;
			
			$includePST = $amtAfterTax * $pst / 100;
			$pstCount += $includePST;
			
			$amoutWithoutTax = number_format($finalProductPrice+$companyTypePrice, 4) * $decodeQuantity[$catcount];
			$GrandTotalwithTax = $amtAfterTax + $includeGST + $includePST;
			
			$cardDetail[$cadcount]['fet_'.$cadcount] = $fet;
			$cardDetail[$cadcount]['pct_'.$cadcount] = $pct;
			$cardDetail[$cadcount]['pft_'.$cadcount] = $pft;
			$cardDetail[$cadcount]['gst_'.$cadcount] = $gst;
			$cardDetail[$cadcount]['pst_'.$cadcount] = $pst;			
			$cardDetail[$cadcount]['amountwithouttax_'.$cadcount] = $amoutWithoutTax;
			$cardDetail[$cadcount]['grandamount_'.$cadcount] = $GrandTotalwithTax;
			$cardDetail[$cadcount]['ppftotal_'.$cadcount] = $ppfTot;
			//$cardDetail['transid'] = $cardDetails->transid;
			$company_email_ca = $cardDetails->company_email;
			$billing_currency = $cardDetails->billing_currency;
			$cardDetail[$cadcount]['card_number_'.$cadcount] = $cardDetails->card_number;			
			$cardDetail[$cadcount]['category_'.$cadcount] = $productName;
			$cardDetail[$cadcount]['transaction_date_'.$cadcount] = $cardDetails->transaction_date;
			$cardDetail[$cadcount]['quantity_'.$cadcount] = $decodeQuantity[$catcount];
			$cardDetail[$cadcount]['amount_'.$cadcount] = $decodeAmount[$catcount];
			$cardDetail[$cadcount]['unit_price_'.$cadcount] = $priceByCompany;
			$cardDetail[$cadcount]['gas_station_name_'.$cadcount] = $cardDetails->gas_station_name;
			/*trans_data field*/
			$transJsonArrayObject = (array($cardDetails->card_number => array('product_name'=>$productName, 'quantity'=>$productQuantity, 'unit_price'=>number_format($priceByCompany, 4), 'fet' => $fet, 'pct'=>$pct, 'pft'=>$pft, 'gst'=>$gst, 'pst'=>$pst, 'amountwithouttax' => number_format($amoutWithoutTax, 2) , 'amountwithtax' => number_format($GrandTotalwithTax, 2))));
			$transationDetails[$jsonIncCAD] = $transJsonArrayObject;
			/*invoice_data field*/
			$jsonArrayObject = (array('card_number' =>$cardDetails->card_number,'transaction_date' => $cardDetails->transaction_date, 'transaction_id' => $cardDetails->transaction_id));
			$arr[$jsonInc] = $jsonArrayObject;			
			$subTotal += $amtAfterTax;
			unset($taxOutput, $ppfTot, $gst, $pst);
			$cadcount++;$catcount++;$jsonIncCAD++;
			}
		  }
		$transactionDetails = json_encode($transationDetails);$invoice_data = json_encode($arr);		  
		$this->data['maxInvId'] = $this->account_model->get_max_trans_inv_id();
		
		$this->db->select('transactions.*, transactions.id as transid, users.*, cards.*');
		$this->db->from('cards');

		$this->db->join('users', 'users.id = cards.company_id');
		$this->db->join('transactions', 'transactions.card_number = cards.card_number');
		$this->db->where(array('cards.company_id'=> $cid, 'transactions.invoice_status'=>0, 'transactions.billing_currency'=>'CAD'));
		
		$CAallResults = $this->db->get()->result();
			
			/* $card_numberLength = count($CAallResults);
				if($card_numberLength >0){
					$arr = [];
					$invoicedatacainc = 0;
					while($invoicedatacainc < $card_numberLength) {	
						$jsonArrayObject = (array('card_number' =>$CAallResults[$invoicedatacainc]->card_number,'transaction_date' => $CAallResults[$invoicedatacainc]->transaction_date, 'transaction_id' => $CAallResults[$invoicedatacainc]->transaction_id));
						$arr[$invoicedatacainc] = $jsonArrayObject;
						$invoicedatacainc++;				
					}
					$invoice_data = json_encode($arr);
				}else{
					$invoice_data = '';
				} */			
				for($upd =0; $upd <count($CAallResults); $upd++){
					$this->db->where('id', $CAallResults[$upd]->transid);
					$this->db->set('invoice_status', 1);
					$this->db->update('transactions');
				}

		  empty($this->data['maxInvId']->id)?$invoiceID = 1:$invoiceID = $this->data['maxInvId']->id + 1;
		  //Insert invoice data in transaction invoice table
		  $data['invoice_id'] = "CL".$invoiceID;
		  $data['company_id'] = $cid;
		  $data['invoice_date'] = date('Y-m-d');
		  $data['invoice_data'] = $invoice_data;
		  $data['trans_data'] = $transactionDetails;		  
		  $data['sub_total'] = number_format($subTotal, 2);
		  $data['gst_total'] = number_format($gstCount, 2);
		  $data['pst_total'] = number_format($pstCount, 2);
		  $data['grand_total'] = number_format($subTotal + $gstCount + $pstCount, 2);
		  $data['date_created'] = date('Y-m-d H:i:s');
		  $data['date_modified'] = date('Y-m-d H:i:s');;
		  $this->db->insert('transaction_invoice', $data);
		  
		$divide = $cadcount/10;
		//$after_divide =  round($divide);
		$after_divide =  ceil($divide);

		if($after_divide <=  1){
			$after_divide = 1;
		}
	if ( $cadcount >= 0 ){  //If there are more than 0 transations

	$k =0;
		for ($j = 0; $j < $after_divide; $j++){
		$obj_pdf->AddPage();
		
			$content = '';
				$content .='<table border="0" cellspacing="0" cellpadding="5">
							<tr>
								<td width="70%"><img src="'.$image.'" alt="company_logo" width="200" height="70" border="0"/></td>
								<td width="30%">
								<table border="1" cellspacing="0" cellpadding="3">
									<tr><td colspan="3" align="center">INVOICE</td></tr>
									<tr><th align="center">DATE</th><th align="center">INVOICE</th>
									<th align="center">PAGE</th></tr>
									<tr><td align="center">'.$data['invoice_date'].'</td><td align="center">'.$data['invoice_id'].'</td><td align="center">'.$obj_pdf->getAliasNumPage().'</td></tr>
									<tr><td align="center">Account#</td><td align="center">'.$data['company_id'].'</td></tr>
									<tr><td colspan="3">GST/HST # 808170526RT0001</td></tr>
								</table>
								</td>
							</tr>
						</table>';	  
			  $content .= '  
				<table border="0" cellspacing="0" cellpadding="5" nobr="true">
					<tr>
						<td>Bill To:</td><td></td>
					</tr>
					<tr>
						<td width="20%">'.$company_name.'<br />'.strtoupper($address).'</td>
						<td width="80%">
						</td>
					</tr>
				</table>
				<div style="margin-top: 20px;"></div>';

			  $content .= '<table class="table" border="" style="border: 1px solid #1e1e1e;" cellspacing="0" cellpadding="3" nobr="true">  
				   <tr >  
						<th width="16%" style="font-size:9px;text-align:center;border-bottom: 1px solid #1e1e1e;">SITE</th>  
						<th width="10%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">GAS STATION</th>  
						<th width="11%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">DATE & TIME</th>
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FET</th>  
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PFT</th>  
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PCT</th> 						
						<th width="4%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">QTY</th>   
						<th width="8%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FUEL TAXES</th>   
						<th width="8%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PRICE UNIT</th>
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PST</th>   
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">GST</th> 						
						<th width="9%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">AMT WITHOUT TAX</th>   
						<th width="9%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">AMT WITH TAX</th>   
				   </tr>  
			  ';  

			for($i = 0 ;$i<10;$i++){
				if($k < count($cardDetail) && !empty($cardDetail)){				
					$content .= "<tr>
					<td>CARD: ".$cardDetail[$k]['card_number_'.$k]."</td>
					<td></td>
					<td></td>
					<td></td>"
					."</tr><tr><td>UNIT: ".$cardDetail[$k]['unit_price_'.$k]."</td></tr>
					<tr><td>PRODUCT: ".$cardDetail[$k]['category_'.$k]." </td><td>".$cardDetail[$k]['gas_station_name_'.$k]."</td><td>".$cardDetail[$k]['transaction_date_'.$k]."</td><td>".number_format($cardDetail[$k]['fet_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pft_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pct_'.$k], 4)."</td><td>".$cardDetail[$k]['quantity_'.$k]."</td><td>".number_format($cardDetail[$k]['ppftotal_'.$k], 4)."</td><td>".$cardDetail[$k]['unit_price_'.$k]."</td><td>".number_format($cardDetail[$k]['pst_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['gst_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['amountwithouttax_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['grandamount_'.$k], 2)."</td></tr>
					";
					  $total_sub += $cardDetail[$k]['amount_'.$k];
				  $k++;
				}
			}
			$content .= '</table>';
			if($j == $after_divide-1){			
				$afterGST = $total_sub * 13 /100;		
				$content .= '<table border="0" nobr="true">
				<tr>
					<td width="70%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
					<td width="30%">
					<table border="1" cellpadding="3" nobr="true"> 
						<tr><td>SUB-TOTAL</td><td>'.number_format($subTotal, 2).'</td></tr>
						<tr><td>G.S.T.</td><td>'.number_format($gstCount, 2).'</td></tr>
						<tr><td>P.S.T.</td><td>'.number_format($pstCount, 2).'</td></tr>
						<tr><td >TOTAL</td><td style="border-left: 1px solid transparent;">'.number_format($subTotal + $gstCount + $pstCount, 2).'</td></tr>
					</table>
					</td>
				</tr>	
				</table>
				<div style="margin-top: 20px;"></div>
				<table border="0" nobr="true">
				<tr>
					<td width="30%">Please remit payment to:<br /><strong>PRIDE DIESEL INC.</strong><br />6050 Dixie Rd<br />Missisauga ON L5T 1A6</td>
					<td width="40%"></td>
					<td width="30%" align="right"><strong>ACCOUNTS RECIEVABLE</strong><br />OFFICE 647-618-7184<br />x 244<br />FAX 866-867-8922<br />EMAIL info@pridediesel.com</td>
				</tr>	
				</table><h3 style="text-align: center;">Thank you for your business.</h3>';
			}
			
			if($j != $after_divide-1){
				$content .= '<table border="0" nobr="true">
				<tr>
					<td width="80%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
					<td width="20%">
					<table border="1" cellpadding="3" nobr="true"> 
						<tr><td>SUB-TOTAL</td><td></td></tr>
						<tr><td></td><td></td></tr>
						<tr><td></td><td></td></tr>
						<tr><td style="border-right: 1px solid transparent;">TOTAL</td><td style="border-left: 1px solid transparent;"></td></tr>
					</table>
					</td>
				</tr>	
				</table>
				<div style="margin-top: 20px;"></div>
				<table border="0" nobr="true">
				<tr>
					<td width="30%">Please remit payment to:<br /><strong>PRIDE DIESEL INC.</strong><br />6050 Dixie Rd<br />Missisauga ON L5T 1A6</td>
					<td width="40%"></td>
					<td width="30%" align="right"><strong>ACCOUNTS RECIEVABLE</strong><br />OFFICE 647-618-7184<br />x 244<br />FAX 866-867-8922<br />EMAIL info@pridediesel.com</td>
				</tr>	
				</table><h3 style="text-align: center;">Thank you for your business.</h3>';
			}
			$obj_pdf->writeHTML($content);	
		}
	}		
	ob_end_clean();

    //$obj_pdf->Output('sample.pdf', 'I');		
    $obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_'.$billing_currency."_".$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
	
	//Send generated invoice to company and then delete pdf
	$this->load->library('email');
	$ca_subject = 'CAD Transactions Invoice';
	$ca_body = 'Currency CAD';
	$pdfFilePath = APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_'.$billing_currency."_".$data['invoice_date']."_".$data['company_id'].'.pdf';
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