<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account extends MY_Controller {
	public function __construct(){
		parent::__construct();
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }			
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';
		$this->settings['css'][] = 'assets/plugins/daterangepicker/daterangepicker.css';

		$this->scripts['js'][] = 'assets/modules/account/invoice/js/script.js';	
		$this->scripts['js'][] = 'assets/modules/account/tax/js/script.js';	
		$this->scripts['js'][] = 'assets/plugins/daterangepicker/daterangepicker.js';	
		$this->load->model('account_model');
		$this->load->helper('misc_helper');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->library('efs_api');
	}
	
	public function index(){

	}
	//Ledger
	public function ledgers(){
		$this->settings['title'] = 'All Account';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('All Account', base_url() . 'account/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();		
		
		$this->data['allLedger'] = $this->account_model->get_ledgers();
		$this->_render_template('ledger/index', $this->data);
	}
	
	/**
	 * @param id
	 * @return array
	 */	
	public function ledger_edit($id=null){
		if($id){
			$this->settings['title'] = 'Edit Ledger';
			$this->breadcrumb->mainctrl("account");
			$this->breadcrumb->add('Edit Ledger', base_url() . 'account/ledger_edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
			
			//$this->data['ledgerGroups'] = $this->account_model->get_account_group();
			$this->data['regCompanies'] = $this->account_model->get_companies();
			$this->data['ledgerDetails'] = $this->account_model->get_ledger_by_id($id);
		}else{
			$this->settings['title'] = 'Add Ledger';
			$this->breadcrumb->mainctrl("account");
			$this->breadcrumb->add('Add Ledger', base_url() . 'account/ledger_edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
			
			$this->data['regCompanies'] = $this->account_model->get_companies();
			$this->data['ledgerDetails'] = $this->account_model->get_new_ledger_fields();
		}
		
		$id == NULL || $this->data['ledgerDetails'] = $this->account_model->get_ledger_by_id($id);
		
		$rules = $this->account_model->ledger_rules;
		$this->form_validation->set_rules($rules);
		if($this->form_validation->run() == true){
			$data = $this->account_model->array_from_post(array('name', 'phone', 'opening_balance', 'email', 'gstin'));
			
			if($id){
				$data['modified_date'] = date('Y-m-d h:i:s');
			}else{
				$data['modified_date'] = date('Y-m-d h:i:s');
				$data['created_date'] = date('Y-m-d h:i:s');
			}
			$id = $this->account_model->edit_ledger($data, $id);
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('account/ledger_edit/').$id ,'refresh');			
		}
		
		$this->_render_template('ledger/edit', $this->data);
	}
	
	public function delete_ledger($id=null){
		$recordDeleted = $this->account_model->ledger_delete($id);
		if($recordDeleted == true){
			$this->session->set_flashdata('success', 'Record Deleted');
			redirect(base_url('account/ledgers'), 'refresh');
		}
	}	
	
	//Invoice
	public function invoice(){
		$this->settings['title'] = 'All Invoices';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('All Invoices', base_url() . 'account/invoice');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();		
		
		$this->data['allInvoices'] = $this->account_model->get_invoices();
		$this->_render_template('invoice/index', $this->data);		
	}
	
	public function invoice_edit($id=null){
		
		$this->data['company_names'] = $this->account_model->get_users();	
		$this->data['products'] = $this->account_model->get_products();	
		$this->data['maxId'] = $this->account_model->get_max_id();	

		if($id){
			$this->settings['title'] = 'Edit Ledger';
			$this->breadcrumb->mainctrl("account");
			$this->breadcrumb->add('Edit Ledger', base_url() . 'account/ledger_edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
			
			$this->data['invoiceDetails'] = $this->account_model->get_invoice_by_id($id);
		}else{
			$this->settings['title'] = 'Create Invoice';
			$this->breadcrumb->mainctrl("account");
			$this->breadcrumb->add('Create Invoice', base_url() . 'account/invoice_edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
			
			$this->data['invoiceDetails'] = $this->account_model->get_new_invoice_fields();
		}
		
		$id == NULL || $this->data['invoiceDetails'] = $this->account_model->get_invoice_by_id($id);
		
		$rules = $this->account_model->invoice_rules;
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			
			$data = $this->account_model->array_from_post(array('party_name', 'party_address', 'invoice_number',  'gst', 'final_total', 'invoice_date'));
			
			$product_quantityLength = count($_POST['quantity']);
				if($product_quantityLength >0){
					$arr = [];
					$i = 0;
					while($i < $product_quantityLength) {	
						$jsonArrayObject = (array('driver_id' =>$_POST['driver_id'][$i],'unit' => $_POST['unit'][$i],'product_id' => $_POST['product_id'][$i], 'quantity' => $_POST['quantity'][$i], 'fuel_taxes' => $_POST['fuel_taxes'][$i], 'price_unit' => $_POST['price_unit'][$i],'sub_total'=> $_POST['sub_total'][$i]));
						$arr[$i] = $jsonArrayObject;
						$i++;				
					}
					$descr_of_products_array = json_encode($arr);
				}else{
					$descr_of_products_array = '';
				}			
			//JSON encoded data of description of products rows
			$data['descr_of_products'] = $descr_of_products_array;
			$data['sub_total'] = $this->input->post('sub_total1');
			
			if($id == null){
				$data['date_created'] = date('Y-m-d h:i:s');
				$data['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d h:i:s');
			}
			
			$id = $this->account_model->save_invoice($data, $id);
			
			redirect(base_url('account/invoice_edit/').$id ,'refresh');
		}
		$this->_render_template('invoice/edit', $this->data);
	}
	
	public function invoice_view($id){
		$this->settings['title'] = 'View Invoice';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Edit Invoice', base_url() . 'account/invoice_view');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['fetchInvoice'] = $this->account_model->get_invoice_by_id($id);
		
		
		$this->_render_template('invoice/view', $this->data);
	}	
	
	//Product
	public function get_all_products(){
		$this->db->select('id as id, product_name as text');
		$this->db->from('products');

		if(!empty($this->input->get("searchTerm"))){		
		$table_field_name = $this->input->get("fieldname");	
			$this->db->like(($table_field_name), $this->input->get("searchTerm"));
		}		
		$query = $this->db->get();
		$json = $query->result();
		echo json_encode($json);		
	}	
	
	public function get_product_details($id){
		$data = $this->account_model->get_product_by_id($id);
		echo json_encode($data);
	}
	
	public function company_address($id){
		$data = $this->account_model->get_comp_addr($id);
		echo json_encode($data);
	}
	
	public function get_comp_drivers($cid){
		$this->db->select('id, name');
		$this->db->where('id', $id);
		$query = $this->db->get('drivers');
		foreach($query->result() as $row){
			$comp_addr = $row;
		}
		echo array('did'=>$comp_addr->id,'dname'=>$comp_addr->name);
	}	

	public function generate_invoice_pdf($id){

      require_once(APPPATH.'libraries/tcpdf/tcpdf.php');  
      $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("Invoice Data");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(TRUE, 10);  
      $obj_pdf->SetFont('helvetica', '', 9);
	  $obj_pdf->SetMargins(10, 20, 10, true);
	  $obj_pdf->Ln(5);
      $obj_pdf->AddPage();
	  $fetchInvoiceData = $this->account_model->export_invoice_pdf($id);
	  $jsonDecodedData = json_decode($fetchInvoiceData->descr_of_products);
	  
	  $fetchInvoiceValues = $fetchInvoiceData;
	  $image = base_url().'assets/images/pride-diesel-logo.png';
      $content = '';  
      $content .= '  

		<table border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td width="50%"><img src="'.$image.'" /></td>
				<td width="50%">
				<table border="1" cellspacing="0" cellpadding="3">
					<tr><td colspan="3" align="center">INVOICE</td></tr>
					<tr><th align="center">DATE</th><th align="center">INVOICE</th><th align="center">PAGE</th></tr>
					<tr><td align="center">'.date_format(date_create($fetchInvoiceValues->invoice_date), 'Y/m/d').'</td><td align="center">'.$fetchInvoiceValues->invoice_number.'</td><td align="center">'.$obj_pdf->getAliasNumPage().'</td></tr>
					<tr><td align="center">Account#</td><td align="center">1063</td></tr>
					<tr><td colspan="3">GST/HST # 808170526RT0001</td></tr>
				</table>
				</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td>Bill To:</td><td></td>
			</tr>
			<tr>
				<td width="30%">'.$fetchInvoiceValues->company_name.'<br />'.strtoupper($fetchInvoiceValues->party_address).'</td>
				<td width="80%">

				</td>
			</tr>
		</table>
		<div style="margin-top: 20px;"></div>
      <table class="table" border="" style="border: 1px solid #1e1e1e;" cellspacing="0" cellpadding="3">  
           <tr >  
                <th width="23%" style="font-size:9px;text-align:center;border-bottom: 1px solid #1e1e1e;">SITE</th>  
                <th width="18%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">DRIVER NAME</th>  
                <th width="15%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">DATE & TIME</th>  
                <th width="10%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">QTY</th>   
                <th width="12%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FUEL TAXES</th>   
                <th width="12%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PRICE UNIT</th>   
                <th width="10%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">AMOUNT</th>   
           </tr>  
      ';  
      //$content .= fetch_data();  
      		foreach($jsonDecodedData as $jsonDecodedDataRows){
				//Fetch Driver Name from drivers table
				$this->db->select('name');
				$this->db->where('id', $jsonDecodedDataRows->driver_id);
				$driverName = $this->db->get('drivers')->row();
				//Fetch Product Name from products table
				$this->db->select('product_name');
				$this->db->where('id', $jsonDecodedDataRows->product_id);
				$productName = $this->db->get('products')->row();

				//Fetch Card Number from cards table
				$this->db->select('card_number');
				$this->db->where('driver_id', $jsonDecodedDataRows->driver_id);
				$cardNumber = $this->db->get('cards')->row();
				
			$content .= "<tr>
			<td>CARD: ".$cardNumber->card_number."</td>
			<td></td>
			<td></td>
			<td></td>"
			."</tr><tr><td>UNIT: ".$jsonDecodedDataRows->unit."</td></tr>
			<tr><td>PRODUCT: ".$productName->product_name." </td><td>".$driverName->name."</td><td>".date_format(date_create($fetchInvoiceValues->invoice_date), 'Y/m/d h:i:s')."</td><td>".$jsonDecodedDataRows->quantity."</td><td>".$jsonDecodedDataRows->fuel_taxes."</td><td>".$jsonDecodedDataRows->price_unit."</td><td>".$jsonDecodedDataRows->sub_total."</td></tr>
			<tr><td>TOTAL FOR PRODUCT:  </td><td></td><td></td><td>".$jsonDecodedDataRows->quantity."</td><td></td><td></td><td>".$jsonDecodedDataRows->sub_total."</td></tr>
			<tr><td>TOTAL FOR UNIT:  </td><td></td><td></td><td>".$jsonDecodedDataRows->quantity."</td><td></td><td></td><td>".$jsonDecodedDataRows->sub_total."</td></tr>
			<tr><td>TOTAL FOR CARD:  </td><td></td><td></td><td>".$jsonDecodedDataRows->quantity."</td><td></td><td></td><td>".$jsonDecodedDataRows->sub_total."</td></tr>";
			  
		  $content .= '<hr />';
			}
		  $content .= '</table>';
		  $content .= '<table border="0">
		<tr>
			<td width="70%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
			<td width="30%">
			<table border="1" cellpadding="3"> 
				<tr><td>SUB-TOTAL</td><td>'.$fetchInvoiceValues->sub_total.'</td></tr>
				<tr><td>G.S.T.</td><td>'.$fetchInvoiceValues->GST.'</td></tr>
				<tr><td>P.S.T.</td><td>0.00</td></tr>
				<tr><td style="border-right: 1px solid transparent;">TOTAL</td><td style="border-left: 1px solid transparent;">'.$fetchInvoiceValues->final_total.'</td></tr>
			</table>
			</td>
		</tr>	
		</table>
		<div style="margin-top: 20px;"></div>
		<table border="0" >
		<tr>
			<td width="30%">Please remit payment to:<br /><strong>PRIDE DIESEL INC.</strong><br />6050 Dixie Rd<br />Missisauga ON L5T 1A6</td>
			<td width="40%"></td>
			<td width="30%" align="right"><strong>ACCOUNTS RECIEVABLE</strong><br />OFFICE 647-618-7184<br />x 244<br />FAX 866-867-8922<br />EMAIL info@pridediesel.com</td>
		</tr>	
		</table><h3 style="text-align: center;">Thank you for your business.</h3>';
	
      $obj_pdf->writeHTML($content);  
      $obj_pdf->Output('sample.pdf', 'I');		
		
		/* $this->load->library('Pdf');
		$dataPdf = $this->user_model->get_data_byId('users','id',$id);
		print_r($dataPdf);die;
		create_pdf($dataPdf,'modules/user/views/view_user_pdf.php');
		$this->load->view('sale_orders/view_saleOrder_pdf'); */
	}
	
	//Tax Structure
	public function tax(){
		$this->settings['title'] = 'Taxes';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Taxes', base_url() . 'account/tax');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['results'] = $this->account_model->get_taxes();

		$this->_render_template('tax/index', $this->data);
	}
	
	public function edit_tax($id = NULL){
		if($id){
			$this->settings['title'] = 'Edit Tax';
			$this->breadcrumb->mainctrl("account");
			$this->breadcrumb->add('Edit Tax', base_url() . 'account/tax');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
			$this->data['tax'] = $this->account_model->get_tax_by_id($id);
		}else{
			$this->settings['title'] = 'Add Tax';
			$this->breadcrumb->mainctrl("account");
			$this->breadcrumb->add('Add Tax', base_url() . 'account/tax');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();

			$this->data['tax'] = $this->account_model->get_new_tax();
		}
		
		$id == NULL || $this->data['tax'] = $this->account_model->get_tax_by_id($id);
		
		$rules = $this->account_model->tax_rules;
		if($this->input->post('isfederaltax')){
			//$rules['federal_tax']['rules'] .= '|required';
		}else{
			$rules['state']['rules'] .= 'required';
			$rules['tax_type']['rules'] .= 'required';
			//$rules['tax_rate']['rules'] .= '|required';
		}
		
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			$data = $this->account_model->array_from_post(array('state', 'tax_type', 'is_per_is_val', 'tax_rate'));

			if($this->input->post('isfederaltax')){
				$data['isfederaltax'] = $this->input->post('isfederaltax');
				$data['state'] = '';
				$data['tax_type'] = '';
				//unset($data['state'], $data['tax_type']);
			}else{
				$data['isfederaltax'] = 0;
			}
			
			if($id){
				$data['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_created'] = date('Y-m-d h:i:s');
				$data['date_modified'] = date('Y-m-d h:i:s');
			}
			
			$id = $this->account_model->save_tax($data, $id);
			
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('account/edit_tax/').$id, 'refresh');
			}
		}
		
		$this->_render_template('tax/edit', $this->data);		
	}
	
	public function delete_tax_record($id=null){
		$recordDeleted = $this->account_model->delete_tax($id);
		if($recordDeleted == true){
			$this->session->set_flashdata('success', 'Record Deleted');
			redirect(base_url('account/tax'), 'refresh');
		}
	}

	/*************************	Transaction		*************/
	public function transactions(){
		$this->settings['title'] = 'All Transactions';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('All Transactions', base_url() . 'account/transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->data['transactionData'] = $this->account_model->get_transactions();
		
		$this->_render_template('transaction/index', $this->data);
	}
	
	public function transaction_edit(){
		$this->settings['title'] = 'Edit Transaction';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Edit Transaction', base_url() . 'account/transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();		
		
		$this->_render_template('transaction/edit', $this->data);
	}
	
	public function transaction_view_by_cid($cid=NULL){
		$this->settings['title'] = 'View Transaction';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transaction', base_url() . 'account/transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['cardsTransData'] = $this->account_model->get_card_trans_by_cid($cid);
		$this->data['maxInvId'] = $this->account_model->get_max_trans_inv_id();
		//print_r($this->data['cardsTransData']);die;
		$this->_render_template('transaction/view', $this->data);
	}	

	public function card_transactions($cardNumber=NULL){
		$this->settings['title'] = 'View Transactions';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transactions', base_url() . 'account/card_transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->data['cardDetails'] = $this->account_model->get_card_transactions($cardNumber);
		$this->data['driverDetails'] = $this->account_model->get_card_driver($cardNumber);
		
		$this->_render_template('transaction/card_trans_view', $this->data);		
	}
	
	public function import_transactions(){
		$this->settings['title'] = 'Import Card';
		$this->breadcrumb->mainctrl("card");
		$this->breadcrumb->add('Import Card', base_url() . 'card/import');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();		
		//Get All transactions
		$allTransactionData = $this->account_model->get_transactions();
		$transids = array();
		foreach($allTransactionData as $allTransData){
			$transids[] = $allTransData->transaction_id;
		}
		//echo"<pre>";print_r($transids);die;
		$clientId = 'Kt2THyad1IdeZ2uyJn8ddFrXCkDbHOL9';

		$url = 'https://test.efsllc.com/axis2/services/CardManagementWS/getTransExtLoc';
		$cardSummField = array('clientId'=>$clientId, 'begDate'=>'2020-06-01T23:30:59', 'endDate'=> '2020-07-07T23:30:59');
		$output = $this->efs_api->api_init($url, $cardSummField, 'GET');

		//echo"<pre>";print_r((string)$output->result->value->cardNumber);die;
		//foreach($output->result->value as $allCardDatass){
			//$cardids[] = $allCardDatas->card_number;
			//if((string)$output->result->value->cardNumber == '7083052035236900009'){
				for($i=0; $i<count($output->result->value);$i++){
					if(in_array((string)$output->result->value[$i]->transactionId, $transids) == FALSE){
				$data['billing_currency'] = (string)$output->result->value->billingCurrency;
				$data['card_number'] = (string)$output->result->value[$i]->cardNumber;
				$data['carrier_id'] = (string)$output->result->value[$i]->carrierId;
				$data['contract_id'] = (string)$output->result->value[$i]->contractId;
				$data['country'] = (string)$output->result->value[$i]->country;
				$data['invoice'] = (string)$output->result->value[$i]->invoice;
				$data['amount'] = (string)$output->result->value[$i]->lineItems->amount;
				$data['category'] = (string)$output->result->value[$i]->lineItems->category;
				$data['group_category'] = (string)$output->result->value[$i]->lineItems->groupCategory;
				$data['unit_price'] = (string)$output->result->value[$i]->lineItems->ppu;
				$data['quantity'] = (string)$output->result->value[$i]->lineItems->quantity;
				$data['gas_station_id'] = (string)$output->result->value[$i]->locationId;
				$data['gas_station_name'] = (string)$output->result->value[$i]->locationName;
				$data['gas_station_state'] = (string)$output->result->value[$i]->locationState;
				//$allCardDatass->lineItems->amount  
				//$jsonValues = array('quantity'=>(string)$allCardDatass->lineItems->quantity,'price_unit'=>(string)$allCardDatass->lineItems->ppu);
				
				//$data['descr_of_products'] = json_encode($jsonValues);
				$dateTimeExplode = explode('T', $output->result->value[$i]->transactionDate);
				$dateTimeExplode2 = explode('.', $dateTimeExplode[1]);
				$data['transaction_date'] = $dateTimeExplode[0]." ".$dateTimeExplode2[0];
				$data['transaction_id'] = (string)$output->result->value[$i]->transactionId;
				$data['transaction_type'] = (string)$output->result->value[$i]->transactionType;
				$data['date_created'] = date('Y-m-d h:i:s');
				$data['date_modified'] = date('Y-m-d h:i:s');
				//echo"<pre>";print_r($jsonValues);
				$this->db->insert('transactions', $data);
				}else{
				$data['billing_currency'] = (string)$output->result->value->billingCurrency;
				$data['card_number'] = (string)$output->result->value[$i]->cardNumber;
				$data['carrier_id'] = (string)$output->result->value[$i]->carrierId;
				$data['contract_id'] = (string)$output->result->value[$i]->contractId;
				$data['country'] = (string)$output->result->value[$i]->country;
				$data['invoice'] = (string)$output->result->value[$i]->invoice;
				$data['amount'] = (string)$output->result->value[$i]->lineItems->amount;
				$data['category'] = (string)$output->result->value[$i]->lineItems->category;
				$data['group_category'] = (string)$output->result->value[$i]->lineItems->groupCategory;
				$data['unit_price'] = (string)$output->result->value[$i]->lineItems->ppu;
				$data['quantity'] = (string)$output->result->value[$i]->lineItems->quantity;
				$data['gas_station_id'] = (string)$output->result->value[$i]->locationId;
				$data['gas_station_name'] = (string)$output->result->value[$i]->locationName;
				$data['gas_station_state'] = (string)$output->result->value[$i]->locationState;
				//$allCardDatass->lineItems->amount  
				//$jsonValues = array('quantity'=>(string)$allCardDatass->lineItems->quantity,'price_unit'=>(string)$allCardDatass->lineItems->ppu);
				
				//$data['descr_of_products'] = json_encode($jsonValues);
				$dateTimeExplode = explode('T', $output->result->value[$i]->transactionDate);
				$dateTimeExplode2 = explode('.', $dateTimeExplode[1]);
				$data['transaction_date'] = $dateTimeExplode[0]." ".$dateTimeExplode2[0];
				$data['transaction_id'] = (string)$output->result->value[$i]->transactionId;
				$data['transaction_type'] = (string)$output->result->value[$i]->transactionType;
				$data['date_created'] = date('Y-m-d h:i:s');
				$data['date_modified'] = date('Y-m-d h:i:s');
				//echo"<pre>";print_r($jsonValues);
				$this->db->set($data);
				$this->db->where('transaction_id', $data['transaction_id']);
				$this->db->update('transactions');					
				}
				}
			//}
			
		//}
		//echo"<pre>";print_r($data);		
echo "Imported";
		//die;
		/* for($i=0; $i<count($output->result->value);$i++){
			if(in_array($output->result->value[$i]->cardNumber, $cardids) == FALSE){
				$data['card_number'] = $output->result->value[$i]->cardNumber;
				$data['policy_number'] = $output->result->value[$i]->policyNumber;
				if($output->result->value[$i]->status == 'ACTIVE'){
					$data['card_status'] = '1';
				}else if($output->result->value[$i]->status == 'INACTIVE'){
					$data['card_status'] = '0';
				}else if($output->result->value[$i]->status == 'HOLD'){
					$data['card_status'] = '2';
				}
				$data['date_created'] = date('Y-m-d h:i:s');
				$data['date_modified'] = date('Y-m-d h:i:s');
				$this->db->insert('cards', $data);
			}else{
				
				$data['policy_number'] = $output->result->value[$i]->policyNumber;
				if($output->result->value[$i]->status == 'ACTIVE'){
					$data['card_status'] = '1';
				}else if($output->result->value[$i]->status == 'INACTIVE'){
					$data['card_status'] = '0';
				}else if($output->result->value[$i]->status == 'HOLD'){
					$data['card_status'] = '2';
				}
				$data['date_modified'] = date('Y-m-d h:i:s');				
				//print_r($data);
				$this->db->set($data);
				$this->db->where('card_number', $output->result->value[$i]->cardNumber);
				$this->db->update('cards');
				//print_r($this->db->last_query());
			}
		} */
		//$this->session->set_flashdata('success_msg', 'Completed/Updated');
		//$this->_render_template('import', $this->data);
		//echo "Import Complete";
	}

	public function generate_invoice($cardNum){

      require_once(APPPATH.'libraries/tcpdf/tcpdf.php');  
      $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("Invoice Data");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(TRUE, 10);  
      $obj_pdf->SetFont('helvetica', '', 9);
	  $obj_pdf->SetMargins(10, 20, 10, true);
	  $obj_pdf->Ln(5);
      $obj_pdf->AddPage();
	  $fetchInvoiceData = $this->account_model->export_invoice($cardNum);
	  //$jsonDecodedData = json_decode($fetchInvoiceData->descr_of_products);
	  foreach($fetchInvoiceData as $transactionhissingle){
		  $singletrans = $transactionhissingle;
	  }
	  $fetchInvoiceValues = $fetchInvoiceData;
	  $image = base_url().'assets/images/pride-diesel-logo.png';
      $content = '';  
      $content .= '  

		<table border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td width="50%"><img src="'.$image.'" /></td>
				<td width="50%">
				<table border="1" cellspacing="0" cellpadding="3">
					<tr><td colspan="3" align="center">INVOICE</td></tr>
					<tr><th align="center">DATE</th><th align="center">INVOICE</th><th align="center">PAGE</th></tr>
					<tr><td align="center">'.date_format(date_create($fetchInvoiceValues->invoice_date), 'Y/m/d').'</td><td align="center">'.$fetchInvoiceValues->invoice_number.'</td><td align="center">'.$obj_pdf->getAliasNumPage().'</td></tr>
					<tr><td align="center">Account#</td><td align="center">1063</td></tr>
					<tr><td colspan="3">GST/HST # 808170526RT0001</td></tr>
				</table>
				</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td>Bill To:</td><td></td>
			</tr>
			<tr>
				<td width="30%">'.$fetchInvoiceValues->company_name.'<br />'.strtoupper($fetchInvoiceValues->party_address).'</td>
				<td width="80%">

				</td>
			</tr>
		</table>
		<div style="margin-top: 20px;"></div>
      <table class="table" border="" style="border: 1px solid #1e1e1e;" cellspacing="0" cellpadding="3">  
           <tr >  
                <th width="23%" style="font-size:9px;text-align:center;border-bottom: 1px solid #1e1e1e;">SITE</th>  
                <th width="18%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">DRIVER NAME</th>  
                <th width="15%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">DATE & TIME</th>  
                <th width="10%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">QTY</th>   
                <th width="12%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FUEL TAXES</th>   
                <th width="12%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PRICE UNIT</th>   
                <th width="10%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">AMOUNT</th>   
           </tr>  
      ';  
      //$content .= fetch_data();  
      		foreach($fetchInvoiceData as $transactionhis){
				//Fetch Driver Name from drivers table
				/* $this->db->select('name');
				$this->db->where('id', $jsonDecodedDataRows->driver_id);
				$driverName = $this->db->get('drivers')->row();
				//Fetch Product Name from products table
				$this->db->select('product_name');
				$this->db->where('id', $jsonDecodedDataRows->product_id);
				$productName = $this->db->get('products')->row();*/

				//Fetch Card Number from cards table
				$this->db->select('card_number');
				$this->db->where('card_number', $transactionhis->card_number);
				$cardNumber = $this->db->get('cards')->row();
				
			$content .= "<tr>
			<td>CARD: ".$transactionhis->card_number."</td>
			<td></td>
			<td></td>
			<td></td>"
			."</tr><tr><td>UNIT: ".$transactionhis->unit_price."</td></tr>
			<tr><td>PRODUCT: ".$transactionhis->category." </td><td>".$driverName->name."</td><td>".$transactionhis->invoice_date."</td><td>".$transactionhis->quantity."</td><td>".$transactionhis->fuel_taxes."</td><td>".$transactionhis->price_unit."</td><td>".$transactionhis->amount."</td></tr>
			<tr><td>TOTAL FOR PRODUCT:  </td><td></td><td></td><td>".$transactionhis->quantity."</td><td></td><td></td><td>".$transactionhis->amount."</td></tr>
			<tr><td>TOTAL FOR UNIT:  </td><td></td><td></td><td>".$transactionhis->quantity."</td><td></td><td></td><td>".$transactionhis->amount."</td></tr>
			<tr><td>TOTAL FOR CARD:  </td><td></td><td></td><td>".$transactionhis->quantity."</td><td></td><td></td><td>".$transactionhis->amount."</td></tr>";
			  
		  $content .= '<hr />';
			}
		  $content .= '</table>';
		  $content .= '<table border="0">
		<tr>
			<td width="70%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
			<td width="30%">
			<table border="1" cellpadding="3"> 
				<tr><td>SUB-TOTAL</td><td>'.$transactionhis->amount.'</td></tr>
				<tr><td>G.S.T.</td><td>'.$transactionhis->GST.'</td></tr>
				<tr><td>P.S.T.</td><td>0.00</td></tr>
				<tr><td style="border-right: 1px solid transparent;">TOTAL</td><td style="border-left: 1px solid transparent;">'.$transactionhis->final_total.'</td></tr>
			</table>
			</td>
		</tr>	
		</table>
		<div style="margin-top: 20px;"></div>
		<table border="0" >
		<tr>
			<td width="30%">Please remit payment to:<br /><strong>PRIDE DIESEL INC.</strong><br />6050 Dixie Rd<br />Missisauga ON L5T 1A6</td>
			<td width="40%"></td>
			<td width="30%" align="right"><strong>ACCOUNTS RECIEVABLE</strong><br />OFFICE 647-618-7184<br />x 244<br />FAX 866-867-8922<br />EMAIL info@pridediesel.com</td>
		</tr>	
		</table><h3 style="text-align: center;">Thank you for your business.</h3>';
	
      $obj_pdf->writeHTML($content);  
      $obj_pdf->Output('sample.pdf', 'I');		
		
		/* $this->load->library('Pdf');
		$dataPdf = $this->user_model->get_data_byId('users','id',$id);
		print_r($dataPdf);die;
		create_pdf($dataPdf,'modules/user/views/view_user_pdf.php');
		$this->load->view('sale_orders/view_saleOrder_pdf'); */
	}

	public function generate_trans_invoice($cid){
		
		require_once(APPPATH.'libraries/tcpdf/tcpdf.php');  
		//$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
		$custom_layout = array('300', '300');
		//$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
		
		$obj_pdf->SetCreator(PDF_CREATOR);  
		$obj_pdf->SetTitle("Invoice Data");  
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
		
		$image = base_url().'assets/images/pride-diesel-logo.png';

		$count=0;
		$this->data['cardsTransData'] = $this->account_model->get_card_trans_by_cid($cid);
		
		foreach($this->data['cardsTransData'] as $cardDetails){
			

		  $decodeCardCat = json_decode($cardDetails->category);
		  $multi_trans = 0;$subTotal = 0; $gstCount = 0; $pstCount = 0; $productprice = 0;
		  
							foreach($decodeCardCat as $cat_vals){
								$cat_values = $cat_vals;
		  $company_name = $cardDetails->company_name;
		  $accountId = $cid;
		  $address = $cardDetails->address;
		  $cardDetail[$count]['card_number_'.$count] = $cardDetails->card_number;
		  $taxapplicable = $this->db->where('product_name', $cat_values)->get('products')->row();								
								
								$decodeUnitPrice = json_decode($cardDetails->unit_price);
								$unitprice_values = $decodeUnitPrice[$multi_trans];

								$decodeQuantity = json_decode($cardDetails->quantity);
								$qty_values = $decodeQuantity[$multi_trans];
						
								if(!empty($taxapplicable->tax) ){
								$taxArray = json_decode($taxapplicable->tax);

								foreach($taxArray as $key=>$taxArrayRow){	

								$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>'ontario'))->get('tax')->row();

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
								
								
								$decodeCategory = json_decode($cardDetails->category);
								$decodeQuantity = json_decode($cardDetails->quantity);
								$decodeAmount = json_decode($cardDetails->amount);
								$decodeUnitPrice = json_decode($cardDetails->unit_price);								
								$companyTypeResult = $this->db->where('id', $cardDetails->company_type)->get('company_types')->row();
								$typeOfCompany = strtolower($companyTypeResult->company_type);								
								$productName = $cat_values;
								$productPriceList = $this->db->get('pricelist')->row();
								$pricelistDecoded = json_decode($productPriceList->price_descr);								
								foreach($pricelistDecoded as $key=>$pricelistDecodedRow){
									//print_r($cat_values->category);
									//if(array_key_exists($card->category, $pricelistDecodedRow)){
									if(array_key_exists($decodeCategory[$multi_trans], $pricelistDecodedRow)){
									unset($pricelistDecodedRow->$productName[$productprice]->efs_amt);
									$companyTypePrice = $pricelistDecodedRow->$productName[$productprice]->$typeOfCompany[$productprice];
									}
								}//pre($ppfTot);die;								
								//$tax3withprice = number_format($cardDetails->unit_price, 4) + number_format($ppfTot, 4);
								$tax3withprice = number_format($unitprice_values+$companyTypePrice, 4) + number_format($ppfTot, 4);
								//$amtAfterTax = $cardDetails->quantity * number_format($tax3withprice, 4);
								$amtAfterTax = $qty_values * number_format($tax3withprice, 4);
								if(empty($gst)){$gst = 0;}
								if(empty($pst)){$pst = 0;}
								
								$includeGST = $amtAfterTax * $gst / 100;
								$gstCount += $includeGST;
								
								$includePST = $amtAfterTax * $pst / 100;
								$pstCount += $includePST;
								
								//echo $amtAfterTax;
								$GrandTotalwithTax = $amtAfterTax + $includeGST + $includePST;

								

								
								
		  $cardDetail[$count]['grandamount_'.$count] = $GrandTotalwithTax;
		  $cardDetail[$count]['fet_'.$count] = $fet;
		  $cardDetail[$count]['pct_'.$count] = $pct;
		  $cardDetail[$count]['pft_'.$count] = $pft;
		  $cardDetail[$count]['gst_'.$count] = $gst;
		  $cardDetail[$count]['pst_'.$count] = $pst;
		  $cardDetail[$count]['ppftotal_'.$count] = $ppfTot;
		  $cardDetail[$count]['category_'.$count] = $decodeCategory[$multi_trans];
		  $cardDetail[$count]['transaction_date_'.$count] = $cardDetails->transaction_date;
		  $cardDetail[$count]['quantity_'.$count] = $decodeQuantity[$multi_trans];
		  $cardDetail[$count]['amount_'.$count] = $decodeAmount[$multi_trans];
		  $cardDetail[$count]['unit_price_'.$count] = $decodeUnitPrice[$multi_trans];
		  $cardDetail[$count]['gas_station_name_'.$count] = $cardDetails->gas_station_name;
		  $subTotal += $GrandTotalwithTax;
		  
		  unset($taxOutput, $ppfTot, $gst, $pst);
		  $count++;$multi_trans++;
		 
		  }
		  pre($count);
		}die;
		
			$card_numberLength = count($_POST['card_number']);
				if($card_numberLength >0){
					$arr = [];
					$i = 0;
					while($i < $card_numberLength) {	
						$jsonArrayObject = (array('card_number' =>$_POST['card_number'][$i],'transaction_date' => $_POST['transaction_date'][$i]));
						$arr[$i] = $jsonArrayObject;
						$i++;				
					}
					$invoice_data = json_encode($arr);
				}else{
					$invoice_data = '';
				}			
			//JSON encoded data of description of products rows
		  $maxInvId = $this->account_model->get_max_trans_inv_id();
		  empty($maxInvId->id)?$invoiceID = 1:$invoiceID = $maxInvId->id + 1;
		  //Insert invoice data in transaction invoice table
		  $data['invoice_id'] = $invoiceID;
		  $data['company_id'] = $cid;
		  $data['invoice_date'] = date('Y-m-d');
		  $data['invoice_data'] = $invoice_data;		  
		  $data['sub_total'] = number_format($subTotal, 2);
		  $data['gst_total'] = number_format($gstCount, 2);
		  $data['pst_total'] = number_format($pstCount, 2);
		  $data['grand_total'] = number_format($subTotal + $gstCount + $pstCount, 2);
		  $data['date_created'] = date('Y-m-d h:i:s');
		  $data['date_modified'] = date('Y-m-d h:i:s');;
		  $this->db->insert('transaction_invoice', $data);
		 
		$divide = $count/7;

		$after_divide =  round($divide);
		/* if(strpos($after_divide, ".") !== true){
			$after_divide += 1;
		} */
//pre($after_divide); die;
		if($after_divide <=  1){
			$after_divide = 1;
		}
	//print_r($GrandTotalwithTax);die;
	if ( $count >= 0 ){  //If there are more than 0 transations

	$k =0;
		for ($j = 0; $j < $after_divide; $j++){
		$obj_pdf->AddPage();
		
			$content = '';
			//$content .= $taxapplicable;
				$content .='<table border="0" cellspacing="0" cellpadding="5">
							<tr>
								<td width="50%"><img src="'.$image.'" alt="company_logo" width="200" height="70" border="0"/></td>
								<td width="50%">
								<table border="1" cellspacing="0" cellpadding="3">
									<tr><td colspan="3" align="center">INVOICE</td></tr>
									<tr><th align="center">DATE</th><th align="center">INVOICE</th><th align="center">PAGE</th></tr>
									<tr><td align="center">'.$this->input->post('invoice_date').'</td><td align="center">'.$this->input->post('invoice_id').'</td><td align="center">'.$obj_pdf->getAliasNumPage().'</td></tr>
									<tr><td align="center">Account#</td><td align="center">'.$accountId.'</td></tr>
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
						<td width="30%">'.$company_name.'<br />'.strtoupper($address).'</td>
						<td width="80%">
						</td>
					</tr>
				</table>
				<div style="margin-top: 20px;"></div>';

			  $content .= '<table class="table" border="" style="border: 1px solid #1e1e1e; height: 400px;" cellspacing="0" cellpadding="1" nobr="true" height="400">  
				   <tr >  
						<th width="20%" style="font-size:9px;text-align:center;border-bottom: 1px solid #1e1e1e;">SITE</th>  
						<th width="10%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">GAS STATION</th>  
						<th width="13%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">DATE & TIME</th>  
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FET</th>  
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PFT</th>  
						<th width="5%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PCT</th>  
						<th width="6%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">QTY</th>   
						<th width="8%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">FUEL TAXES</th>   
						<th width="8%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PRICE UNIT</th>   
						<th width="6%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">PST</th>   
						<th width="6%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">GST</th>   
						<th width="8%" style="font-size:9px;border-bottom: 1px solid #1e1e1e;">AMOUNT</th>   
				   </tr>  
			  ';  
  //pre($companyTypePrice);die;
			for($i = 0 ;$i<5;$i++){
				if($cardDetail[$k]['quantity_'.$k] != ''){				
					$content .= "<tr>
					<td>CARD: ".$cardDetail[$k]['card_number_'.$k]."</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>"
					."</tr><tr><td>UNIT: ".$cardDetail[$k]['unit_price_'.$k]."</td></tr>
					<tr><td>PRODUCT: ".$cardDetail[$k]['category_'.$k]." </td><td>".$cardDetail[$k]['gas_station_name_'.$k]."</td><td>".$cardDetail[$k]['transaction_date_'.$k]."</td><td>".number_format($cardDetail[$k]['fet_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pft_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pct_'.$k], 4)."</td><td>".$cardDetail[$k]['quantity_'.$k]."</td><td>".number_format($cardDetail[$k]['ppftotal_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['unit_price_'.$k], 3)."</td><td>".number_format($cardDetail[$k]['pst_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['gst_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['grandamount_'.$k], 2)."</td></tr>
					";
					  $total_sub += $cardDetail[$k]['amount_'.$k];
					$content .= '<hr />';
				  $k++;
				}//pre($cardDetail[$k]['amount_'.$k]);
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
					<td width="70%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
					<td width="30%">
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

    $obj_pdf->Output('sample.pdf', 'I');		
		
		/* $this->load->library('Pdf');
		$dataPdf = $this->user_model->get_data_byId('users','id',$id);
		print_r($dataPdf);die;
		create_pdf($dataPdf,'modules/user/views/view_user_pdf.php');
		$this->load->view('sale_orders/view_saleOrder_pdf'); */
	}
	
}