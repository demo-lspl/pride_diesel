<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }			
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';
		//$this->settings['css'][] = 'assets/plugins/daterangepicker/daterangepicker.css';

		$this->scripts['js'][] = 'assets/modules/account/invoice/js/script.js';	
		$this->scripts['js'][] = 'assets/modules/account/tax/js/script.js';	
		$this->scripts['js'][] = 'assets/modules/account/js/script.js';	
		//$this->scripts['js'][] = 'assets/plugins/daterangepicker/daterangepicker.js';	
		$this->scripts['js'][] = 'assets/plugins/daterangepicker/moment.min.js';	
		//$this->scripts['js'][] = 'assets/plugins/jquery/jquery.min.js';	
		$this->load->model('account_model');
		$this->load->helper('misc_helper');
		$this->load->helper('functions_helper');
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
		$this->load->model('user/user_model');
		$this->load->library('pagination');
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = [];
		if(!empty($_GET['company_name'])){
			$where = array('users.company_name' => $_GET['company_name']);
		}
		if(!empty($_GET['invoice_status'])){
			if($_GET['invoice_status'] == 'invoiced'){
				$where = array('transactions.invoice_status' => 1);
			}
			if($_GET['invoice_status'] == 'non-invoiced'){
				$where = array('transactions.invoice_status' => 0);
			}	
		}		
		$this->data['allLedger'] = $this->account_model->get_ledgers($where);
        // pagination
        $config['base_url'] = site_url('account/ledgers');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allLedger']);
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

        $this->data['allLedger'] = $this->account_model->get_pagination($config['per_page'], $page, $where);		
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
			$data = $this->account_model->array_from_post(array('name', 'phone', 'opening_balance', 'email'));
			
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

		$this->load->library('pagination');

		//Get all data of company
		$where = array();
		if(!empty($_GET['search'])){
			$where = array('transaction_invoice.id' => $_GET['search']);
		}
		if(!empty($_GET['transactions_from'])){
			$where = array('transaction_invoice.billingOn' => $_GET['transactions_from']);
		}		
		
		$this->data['allInvoices'] = $this->account_model->get_trans_invoices($where);
		
        /* Pagination */
        $config['base_url'] = site_url('account/invoice');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_pagination_trans_invoice($config['per_page'], $page, $where);		
		$this->_render_template('invoice/invoice-index', $this->data);		
	}
	
	public function update_invoice_status($invoiceid, $pagenum=null){
		$this->account_model->set_invoice_paid_status($invoiceid);
		if(!empty($pagenum)){
			redirect(base_url('account/invoice/'.$pagenum), 'refresh');
		}else{
			redirect(base_url('account/invoice'), 'refresh');
		}
		
		//$this->_render_template('invoice/invoice-index', $this->data);
	}
	
	public function invoiced($id){
		$this->settings['title'] = 'All Invoices';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('All Invoices', base_url() . 'account/invoice');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		
		
		$this->data['allInvoices'] = $this->account_model->get_invoiced_transactions($id, $where);
		
        /* Pagination */
        $config['base_url'] = site_url('account/invoice');
        $config['uri_segment'] = 4;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['allInvoices'] = $this->account_model->get_pagination_invoiced_trans($config['per_page'], $page, $id, $where);
		//pre($this->db->last_query());	
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
	
	public function invoice_view($cid){
		$this->settings['title'] = 'View Invoice';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Edit Invoice', base_url() . 'account/invoice_view');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['cardsTransData'] = $this->account_model->get_invoiced_trans_by_cid($cid);
		//$this->data['fetchInvoice'] = $this->account_model->get_trans_invoice_by_id($id);

		$this->_render_template('invoice/view', $this->data);
	}	
	
	//Product Code
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
		
		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		
		$this->data['results'] = $this->account_model->get_taxes($where);
        // pagination
        $config['base_url'] = site_url('account/tax');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['results']);
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

        $this->data['results'] = $this->account_model->get_pagination_tax($config['per_page'], $page, $where);		

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
		}else{
			$rules['state']['rules'] .= 'required';
			$rules['tax_type']['rules'] .= 'required';
		}
		
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			$data = $this->account_model->array_from_post(array('state', 'tax_type', 'is_per_is_val', 'tax_rate'));

			if($this->input->post('isfederaltax')){
				$data['isfederaltax'] = $this->input->post('isfederaltax');
				$data['state'] = '';
				$data['tax_type'] = '';
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
		$this->load->library('pagination');
		$this->load->model('user/user_model');
		$this->data['getuserdata'] = $this->user_model->get_users();
		//Get all data of company
		$where = '';
		$where2 = '';
		if(!empty($_GET['card_search'])){
			$where = $_GET['card_search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}		

		$this->data['transactionData'] = $this->account_model->get_transactions($where, $where2);
        // pagination
        $config['base_url'] = site_url('account/transactions');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['transactionData']);
        //$config['total_rows'] = $this->account_model->get_transactions($where, $where2);
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

        $this->data['transactionData'] = $this->account_model->get_pagination_transactions($config['per_page'], $page, $where, $where2);		
		
		$this->_render_template('transaction/index', $this->data);
	}
	
	public function import_transactions_husky(){
		ob_start();
		
		$this->settings['title'] = 'Import Transactions Husky';
		$this->breadcrumb->mainctrl("user");
		$this->breadcrumb->add('Import Transactions Husky', base_url() . 'user/import_transactions_husky');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		if(isset($_POST['import'])){	
		   if(!empty($_FILES['import_transactions_husky']['name'])!=''){
			    
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
					
                if (!$this->upload->do_upload('import_transactions_husky')) {
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
					$getPricing = $this->db->get('retail_pricing_husky_ca')->result();
					if(empty($getPricing) && !is_object($getPricing)){
						$this->session->set_flashdata('error', 'Please import pricing before import transactions.');
						redirect(base_url().'account/import_transactions_husky', 'refresh');
					}
					//$this->db->truncate('retail_pricing_husky_ca');
					
					$insertdata = null;
					for($i=2;$i<=$arrayCount;$i++)
					{   
						
						if(!empty($allDataInSheet[$i]["A"])){
							$makeDateFormat = str_replace('/', '-', date('Y-m-d', strtotime($allDataInSheet[$i]["C"])));
							/* $transIdExists = $this->db->where('transaction_id', $allDataInSheet[$i]["B"])->get('transactions')->result(); */							
							$transIdExists = $this->db->where(['transaction_id'=> (int)$allDataInSheet[$i]["B"], 'transaction_date' => date('Y-m-d H:i:s',strtotime($makeDateFormat." ".$allDataInSheet[$i]["P"]))])->get('transactions')->result();
							#pre(count($transIdExists));die;
							if(count($transIdExists)>0){
								continue;
							}
							
							/* Get Company Type and Company Pricing Type */
							$cardNumOutput = $allDataInSheet[$i]["A"];
							
							$this->db->select('company_types.company_type, users.cad_pricing_husky, cards.card_number');
							$this->db->join('cards', 'cards.company_id = users.id', 'LEFT');
							$this->db->join('company_types', 'company_types.id = users.company_type_ca_husky', 'LEFT');
							$this->db->where("cards.card_number LIKE '%$cardNumOutput%'");
							$this->db->where("cards.cardCompany", "HUSKY");
							$getCompanyType = $this->db->get('users')->row();
							
							$getCardNum = $this->db->where("card_number LIKE '%$cardNumOutput%'")->where("cards.cardCompany", "HUSKY")->get('cards')->row();
							//pre($this->db->last_query());die;
							if(!is_object($getCardNum)){
								echo '<script language="javascript">';
								//echo "<script language='javascript'>alert(Card Number {$cardNumOutput} Doesn't exists in portal. Re-import file.);</script>";
								echo "alert('Card Number {$cardNumOutput} Does not exists in portal. Please Re-import file.')";
								echo '</script>';
								//echo "{$cardNumOutput}";
								//die;
								redirect(base_url().'account/import_transactions_husky', 'refresh');
							}
							$cardPrefix = substr($getCardNum->card_number, 0, -4);
							if(is_object($getCompanyType) && !empty($getCompanyType->cad_pricing_husky)){
								$setPricingType = $getCompanyType->cad_pricing_husky;
							}else{
								$setPricingType = 'add_on_husky';
							}
							
							
							if(is_object($getCompanyType) && !empty($getCompanyType->company_type)){
								$setCACompanyType = strtolower($getCompanyType->company_type);
							}else{
								$setCACompanyType = 'bronze';
							}
							#pre($setCACompanyType);die;
							
										$productName = null;
										if(trim($allDataInSheet[$i]["G"]) == 'DIESEL'){
											$productName = 'ULSD';
										}
										if(trim($allDataInSheet[$i]["G"]) == 'BULK DEF'){
											$productName = 'DEFD';
										}
										
										$exactCompanyPrice = null;
											$getPricingCA = $this->db->get('pricelist_edit_ca_husky')->row();
											if($getPricingCA->$setPricingType){
												$decodePrices = json_decode($getPricingCA->$setPricingType);//pre($decodePrices);
												foreach($decodePrices as $decodePricesRows){
													
													if(array_key_exists($productName, $decodePricesRows)){
													$gasStationState = trim($decodePricesRows->$productName[0]->state[0]);
															if($gasStationState == $allDataInSheet[$i]["R"] && $decodePricesRows->$productName[0]->gas_station[0] == $allDataInSheet[$i]["O"]){
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
																$exactCompanyPrice = $allDataInSheet[$i]["L"] + $defdOurPrice;
															}
														}
													}
												}									
											}				
										if(trim($allDataInSheet[$i]["G"]) == 'SCALE' || trim($allDataInSheet[$i]["G"]) == 'NOTAVAIL'){
											$exactCompanyPrice = $allDataInSheet[$i]["N"];
										}										
										$insertdata[$i]['transactionAt'] = 'HUSKY';
										$insertdata[$i]['billing_currency'] = 'CAD';
										$insertdata[$i]['card_number'] = $cardPrefix.$allDataInSheet[$i]["A"];
										$insertdata[$i]['unit_number'] = $allDataInSheet[$i]["D"];
										$insertdata[$i]['carrier_id'] = $allDataInSheet[$i]["W"];
										$insertdata[$i]['contract_id'] = $allDataInSheet[$i]["W"];
										$insertdata[$i]['country'] = 'CA';
										$insertdata[$i]['invoice'] = 0;
										$totalAmount = "".(float)$allDataInSheet[$i]["N"]."";
										$insertdata[$i]['amount'] = json_encode(array("".$totalAmount.""));
										$productNameHusky = null;
										if(trim($allDataInSheet[$i]["G"]) == 'DIESEL'){
											$productNameHusky = 'ULSD';
										}
										if(trim($allDataInSheet[$i]["G"]) == 'BULK DEF'){
											$productNameHusky = 'DEFD';
										}
										if(trim($allDataInSheet[$i]["G"]) == 'SCALE' || trim($allDataInSheet[$i]["G"]) == 'NOTAVAIL'){
											$productNameHusky = 'SCALE';
										}										
										$insertdata[$i]['category'] = json_encode(array($productNameHusky));
										$insertdata[$i]['group_category'] = json_encode(array($productNameHusky));
										$insertdata[$i]['unit_price'] = json_encode(array("".$allDataInSheet[$i]["L"].""));
										$insertdata[$i]['pride_price'] = json_encode(array("".$exactCompanyPrice.""));
										$insertdata[$i]['quantity'] = json_encode(array("".$allDataInSheet[$i]["H"].""));
										$insertdata[$i]['gas_station_id'] = $allDataInSheet[$i]["O"];
										$insertdata[$i]['gas_station_name'] = $allDataInSheet[$i]["F"];
										$insertdata[$i]['gas_station_city'] = $allDataInSheet[$i]["Q"];
										$insertdata[$i]['gas_station_state'] = $allDataInSheet[$i]["R"];

										$insertdata[$i]['transaction_date'] = date('Y-m-d H:i:s',strtotime($makeDateFormat." ".$allDataInSheet[$i]["P"]));
										$insertdata[$i]['transaction_id'] = (int)$allDataInSheet[$i]["B"];
										$insertdata[$i]['transaction_type'] = 1;
										$insertdata[$i]['invoice_status'] = 0;
																	
										//$insertdata[$i]['date'] = date('Y-m-d', strtotime($allDataInSheet[$i]['J']));	
										$insertdata[$i]['date_created'] = date('Y-m-d h:i:s');	
										$insertdata[$i]['date_modified'] = date('Y-m-d h:i:s');
						}					
					}
					/* echo "Work";
					pre($insertdata);
					die; */
					$result = null;
					if(!empty($insertdata)){
						$result = $this->account_model->importHuskyTransactions($insertdata);
					}	
                    if($result){
                      //echo "Imported successfully";
						$this->session->set_flashdata('success', 'Transactions Imported Successfully');
                    }else{
						$this->session->set_flashdata('error', 'Something went wrong or already imported');
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

       		 redirect(base_url().'account/import_transactions_husky', 'refresh');

		}else{

			echo"<script>alert('Please Select the File to Upload')</script>";		
		}
	}
		ob_get_clean ();
		$this->_render_template('transaction/import-trans-husky', $this->data);		
	}

	public function makeFile(){
		//$file = $_FILES['ith']['tmp_name'];
		//$catalog = simplexml_load_file($file);
		//$data = fopen("c:\\folder\\testing.txt", "r");
		//move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
		//$this->load->library('upload');
		
 		
		//$config['allowed_types'] = "xlsx|xls|csv";
		//$this->upload->initialize($config);
		$fileExt = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
		if($fileExt == 'csv'){
		$myfile = fopen($_FILES['file']['tmp_name'], "r");
		
		//pre($myfile);die;		
		$content = '';
		$content .= '<table border="1" class="border-collapse" width="100%">
		<tr>
		<th>Status</th>
		<th>Card Number</th>
		<th>Transaction Id</th>
		<th>Date</th>
		<th>Product Name</th>
		<th>Reason</th>
		</tr>';
		$delmtdDataNewLine = str_getcsv(stream_get_contents($myfile), "\n");
		//while(!feof($myfile)) {
		  //$content .= "<tr><td>".fgets($myfile) . "</td></tr>";
		  //echo fgets($myfile) . "<br>";
		//}
		$problemInCards = $noProblemInCards = 0; 
		for($i=0; $i<count($delmtdDataNewLine); $i++){
			$RowOutput = str_getcsv($delmtdDataNewLine[$i], ","); //parse the items in rows
				if($i == 0){
					$cardNumKey = array_search('CARD_NUMBER', $RowOutput); // $key = 0;
					$transactionKey = array_search('REFERENCE', $RowOutput);   // $key = 1;
					$transactionDateKey = array_search('TRANSACTION_DATE', $RowOutput);   // $key = 2;
					$productNameKey = array_search('PRODUCT_DESCRIPTION', $RowOutput);   // $key = 6;

					continue;
				}
				$cardNumOutput = $RowOutput[$cardNumKey];
			$getCardNum = $this->db->where("card_number LIKE '%$cardNumOutput%'")->where("cards.cardCompany", "HUSKY")->get('cards')->row();
			if(strlen($RowOutput[$cardNumKey]) < 4){
				$cardNumOutput = 0 .$RowOutput[$cardNumKey];
			}
			$reason = ''; $symbol = '';
			if(!is_object($getCardNum)){
				$symbol = 'cancel';
				$reason = "Card Doesn't exist";
				$problemInCards++;
			}else if($getCardNum->company_id == 0){
				$symbol = 'cancel';
				$reason = 'Card Not Assigned to Company';
				$problemInCards++;				
			}else{
				$symbol = 'check';
				$reason = 'Ok';
				$noProblemInCards++;
			}
			/* else if(strlen($RowOutput[$cardNumKey]) < 4){
				$symbol = 'cancel';
				$reason = 'Invalid Card Number';
				$problemInCards++;
			} */		
			$content .= '<tr><td><img class="fileicons" src="'.base_url("assets/images/$symbol.png").'" width="30" /></td>';
			//print_r($RowOutput);
			$content .= "<td>".$cardNumOutput."</td>";
			$content .= "<td>".$RowOutput[$transactionKey]."</td>";
			$content .= "<td>".$RowOutput[$transactionDateKey]."</td>";
			$content .= "<td>".$RowOutput[$productNameKey]."</td>";
			$content .= "<td>".$reason."</td>";
			$content .= "<tr>";
		}
		
		fclose($myfile);
		$totalTransactions = count($delmtdDataNewLine) - 1;
		$content .= "</table>";
		$content .= '<div class="file-messages"><h3>Transaction count: '.$totalTransactions .'</h3><h3 class="text-success">Good to go: '.$noProblemInCards.' </h3><h3 class="text-danger">Issues in transactions: '.$problemInCards.'</h3></div>';
		//pre($_FILES['file']['tmp_name']);

		$jsonEncodeVar = array('result' => $content, 'errorCount' => $problemInCards);
		echo json_encode($jsonEncodeVar);
		}else{
			$content = 'You can import your Transaction EXCEL in (.csv) format only.';
			$jsonEncodeVar = array('result' => $content, 'errorCount' => 1);
			echo json_encode($jsonEncodeVar);			
		}
		
	}
	
	public function company_transactions(){
		$this->settings['title'] = 'All Transactions';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('All Transactions', base_url() . 'account/transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		$this->load->library('pagination');
		$userSessDetails = $this->session->userdata('userdata');
		//Get all data of company
		$cid = $userSessDetails->id;	
		$where = '';
		$where2 = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}
		$this->data['transactionData'] = $this->account_model->get_comp_transactions($where, $where2, $cid);
        // pagination
        $config['base_url'] = site_url('account/company_transactions');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['transactionData']);
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

        $this->data['transactionData'] = $this->account_model->get_comp_pagination_transactions($config['per_page'], $page, $where, $where2, $cid);		
		
		$this->_render_template('transaction/company_index', $this->data);
	}
	
	public function exportTransactionsByCompany(){
		ob_start();
		$this->load->model('user/user_model');//pre($_POST);die;
		//File Name
		$filename = 'transactions-data-'.time().'.csv'; //format should be .xlsx , .csv		
		$fd = fopen ('php://output', "w");
		!empty($_POST['cid'])?$cid = $_POST['cid']:$cid = "";
		!empty($_POST['acid'])?$acid = $_POST['acid']:$acid = "";
		//$cid = 28;
		$daterange = $_POST['daterange'];
		$preDate = strtotime("-30 day", strtotime(date('Y-m-d H:i:s')));
			//$this->db->select('users.*, transactions.*, cards.unit_number, cards.driver_id');
			$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
			//$this->db->join('users', 'users.id = cards.company_id');
			if(!empty($cid)){
				$this->db->where(array('cards.company_id'=> $cid));
			}			
			if(!empty($acid)){
				$this->db->where(array('cards.company_id'=> $acid));
			}
			if(!empty($daterange)){
				$expDateRange = explode(' - ', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}else{
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date("Y-m-d H:i:s", $preDate). '" and "'. date('Y-m-d H:i:s').'"');				
			}
			$CAcardCount = $this->db->get('transactions')->result();
			//pre($this->db->last_query());die;
			foreach($CAcardCount as $cardCountRows){
				$productNameJsonDecode = json_decode($cardCountRows->category);
				$pridePriceJsonDecode = json_decode($cardCountRows->pride_price);
				$EFSpriceJsonDecode = json_decode($cardCountRows->unit_price);
				$productQuantityJsonDecode = json_decode($cardCountRows->quantity);
				$explodeDateTime = explode(' ', $cardCountRows->transaction_date);
				$rowCount=0;
				foreach($productNameJsonDecode as $productCountJsonDecodeRows){
					$calcProduct = $productQuantityJsonDecode[$rowCount] * $pridePriceJsonDecode[$rowCount];
					$total = floor($calcProduct*100)/100;
					/* Product Taxes Start */
				/* GST/PST/QST Start */						
				$taxapplicable = $this->db->where('product_name', $productNameJsonDecode[$rowCount])->get('products')->row();					
				if(!empty($taxapplicable->tax) ){
				$taxArray = json_decode($taxapplicable->tax);
					foreach($taxArray as $key=>$taxArrayRow){	
					$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>$cardCountRows->gas_station_state))->get('tax')->row();

					//GST/HST/FNT || PST/QST
					if(!empty($taxOutput['gst']->tax_rate)){$gst = str_replace('%', '', $taxOutput['gst']->tax_rate);}else{$gst = 0;}
					if(!empty($taxOutput['pst']->tax_rate)){$pst = str_replace('%', '', $taxOutput['pst']->tax_rate);}else{$pst = 0;}
					if(!empty($taxOutput['qst']->tax_rate)){$qst = str_replace('%', '', $taxOutput['qst']->tax_rate);}else{$qst = 0;}			
					}
					
				}
				
				if(empty($gst)){$gst = 0;}
				if(empty($pst)){$pst = 0;}
				if(empty($qst)){$qst = 0;}
				$totalTax = $gst + $pst + $qst;
				if(strlen($totalTax) < 2){
					$revTotalTaxAmt = '1.0'.$totalTax;
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;					
				}elseif(strpos($totalTax, '.')!==false){
					$revTotalTaxAmt = '1.'.str_replace(".","",$totalTax);
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;
				}else{
					$revTotalTaxAmt = '1.'.$totalTax;
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;
				}
				if($cardCountRows->billing_currency == 'USD'){
					$UOM = 'G';
				}else{
					$UOM = 'L';
				}
				$toalAfterFloor = floor($total*100)/100;
				$csvdata[] = array($cardCountRows->transaction_id.'",'.date('m/d/Y', strtotime($cardCountRows->transaction_date)).",".date('H:i:s', strtotime($cardCountRows->transaction_date)).','.$cardCountRows->card_number .'",'.$cardCountRows->gas_station_name.','.$cardCountRows->gas_station_city.','.$cardCountRows->gas_station_state .','.$productNameJsonDecode[$rowCount].",".$productQuantityJsonDecode[$rowCount].','.$UOM.','.$pridePriceJsonDecode[$rowCount]." ,".$finalTaxAmt." ,".$toalAfterFloor.','.$cardCountRows->billing_currency ."\n");					
				$rowCount++;	
				}
			}
			//pre($csvdata);die;
			$csvheader = array("Receipt_Number,Transaction_Date,Transaction_Time,Card_Number,Gas_Station,City,Province_State,Product,Quantity,Measure,Price,Tax,Total,Currency\n");
			if(!empty($csvdata)){
				fputcsv($fd, $csvheader);
				foreach ($csvdata as $line) {
					fputcsv($fd, $line, ',');
				}
				
				fclose($fd);			
				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename="'.$filename.'"');
			}else{
				echo "false";
			}
			exit();
ob_get_clean();			
	}	

	public function exportTransactionByCompany($cid=null, $acid=null, $daterange=null, $cur=null){
		$this->load->model('user/user_model');
		//File Name
		//$fileName = 'transactions-data-'.time().'.xlsx'; //format should be .xlsx , .csv
		//pre($acid);die;
		$this->load->library('excel');
		//$empInfo = $this->account_model->exportTransByComp($cid);
		($cid != 'undefined' || !empty($cid))?$cid = $cid:$cid = "";
		($acid != 'undefined' || !empty($acid))?$acid = $acid:$acid = "";
		//$cid = 28;
		//$daterange = $daterange;
		$preDate = strtotime("-30 day", strtotime(date('Y-m-d H:i:s')));
			$this->db->select('transactions.*');
			$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
			//$this->db->join('users', 'users.id = cards.company_id');
			if(!empty($cid) && $cid != 'undefined'){
				$this->db->where(array('cards.company_id'=> $cid));
			}			
			if(!empty($cur) && $cur != 'undefined'){
				$this->db->where(array('transactions.billing_currency'=> $cur));
			}
		
			if(!empty($acid) && $acid != 'undefined'){
				
				//$this->db->where(array('cards.company_id'=> $acid));
				//$names = array('35', '32');
				//$expl = explode(',',$acid);
				$makeArray = [];
				$exp = explode(',', $acid);
				for($i=0; $i<count($exp); $i++){
					$makeArray[] = $exp[$i];
				}
				//$strsls = stripslashes($makeArray);
				//pre($makeArray);die;
				//$imp = implode("','", $exp);
				//$str = implode(',', array_map(function($val){return sprintf("'%s'", $val);}, $acid));
				$this->db->where_in('cards.company_id', $makeArray);
			}
			//die;
				//$names = array('35', '32');
				//$this->db->where_in('cards.company_id', $names);			
			//$this->db->where(array('cards.company_id'=> $acid));
			if(!empty($daterange) && $daterange != 'undefined'){
				//$expDateRange = explode('%20-%20', $daterange);
				$expDateRange = explode('%20-%20', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				/* $this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"'); */
				$this->db->where("DATE(transactions.transaction_date) >='" . date('Y-m-d H:i:s', strtotime($startDate)) . "' AND transactions.transaction_date <='" . date('Y-m-d H:i:s', strtotime($endDate)). "'");
			}else{
				/* $this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date("Y-m-d H:i:s", $preDate). '" and "'. date('Y-m-d H:i:s').'"'); */
				$this->db->where("DATE(transactions.transaction_date) >='" . date('Y-m-d H:i:s',$preDate) . "' AND transactions.transaction_date <='" . date('Y-m-d H:i:s'). "'");	
			}
			$CAcardCount = $this->db->get('transactions')->result();
			//pre($this->db->last_query());die;
			//pre(is_object($CAcardCount));die;
			//if(count($CAcardCount) < 1){	
			if(count($CAcardCount) < 1){	
				echo "notransaction";
				exit();
			}
						
			//pre($CAcardCount);die;
			/* if(count($CAcardCount) > 0){
				//pre(count($CAcardCount));die;
				//echo "<script>alert(No transaction available for export.);</script>";
				//continue;
			}else{ */

			
		//$dailyPriceList = $this->user_model->get_dailypricelist();
		
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Billing Currency');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Card Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Unit Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Invoice');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Amount');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Category');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Unit Price');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'GST/PST/QST');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Gas Station Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Gas Station City');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Gas Station State');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Transaction Date');
        //$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Date Created');

        // set Row
        $rowCount = 2;
        foreach ($CAcardCount as $element) {
			$decUnit_price = json_decode($element->unit_price);
			$decPride_price = json_decode($element->pride_price);
			$productNameJsonDecode = json_decode($element->category);
			$productQuantityJsonDecode = json_decode($element->quantity);

			$totalTaxAmount = 0;
			for($cnt=0; $cnt<count($productNameJsonDecode); $cnt++){
			$amoutQtyTotal = $decPride_price[$cnt] * $productQuantityJsonDecode[$cnt];
			$grandTotal = floor($amoutQtyTotal*100)/100;				
			if($element->billing_currency == 'CAD'){
				$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $element->gas_station_state)->get('tax')->result();
				$finalGST=0;$finalPST=0;$finalQST=0;
				foreach($getTaxRate as $taxTypeRows){
					if($taxTypeRows->tax_type == 'gst'){
						$gstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalGST = $grandTotal * $gstRate / 100;
					}
					if($taxTypeRows->tax_type == 'pst'){
						$pstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalPST = $grandTotal * $pstRate / 100;
					}
					if($taxTypeRows->tax_type == 'qst'){
						$qstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalQST = $grandTotal * $qstRate / 100;
					}				
					$totalTaxAmount = number_format($finalGST + $finalPST + $finalQST, 2);
				}				
			}
	
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element->billing_currency);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount, $element->card_number, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element->unit_number);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $element->invoice);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $grandTotal);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $productNameJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $decPride_price[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $productQuantityJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $totalTaxAmount);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $element->gas_station_name);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $element->gas_station_city);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $element->gas_station_state);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $element->transaction_date);
            //$objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $element['date_created']);

            $rowCount++;
			}
        }
		//die;
       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);//pre($object_writer);die;
			        header('Content-Type: application/vnd.ms-excel');
/* header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download"); */					
			       header("Content-Disposition: attachment;filename=Transactions_".date('Ymd').".xlsx");
			         ob_end_clean();
			        $object_writer->save('php://output');
exit;					
	}
	
	public function exportTransactionByCompanyTransPlus($cid=null, $acid=null, $daterange=null, $cur=null){
		$this->load->model('user/user_model');
		//File Name
		//$fileName = 'transactions-data-'.time().'.xlsx'; //format should be .xlsx , .csv
		//pre($acid);die;
		$this->load->library('excel');
		//$empInfo = $this->account_model->exportTransByComp($cid);
		($cid != 'undefined' || !empty($cid))?$cid = $cid:$cid = "";
		($acid != 'undefined' || !empty($acid))?$acid = $acid:$acid = "";
		//$cid = 28;
		//$daterange = $daterange;
		$preDate = strtotime("-30 day", strtotime(date('Y-m-d H:i:s')));
			$this->db->select('transactions.*');
			$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
			//$this->db->join('users', 'users.id = cards.company_id');
			if(!empty($cid) && $cid != 'undefined'){
				$this->db->where(array('cards.company_id'=> $cid));
			}			
			if(!empty($cur) && $cur != 'undefined'){
				$this->db->where(array('transactions.billing_currency'=> $cur));
			}			
			if(!empty($acid) && $acid != 'undefined'){
				$this->db->where(array('cards.company_id'=> $acid));
			}
			if(!empty($daterange) && $daterange != 'undefined'){
				//$expDateRange = explode('%20-%20', $daterange);
				$expDateRange = explode('%20-%20', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}else{
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date("Y-m-d H:i:s", $preDate). '" and "'. date('Y-m-d H:i:s').'"');				
			}
			$CAcardCount = $this->db->get('transactions')->result();
			//pre(is_object($CAcardCount));die;
			//if(count($CAcardCount) < 1){	
			if(count($CAcardCount) < 1){	
				echo "notransaction";
				exit();
			}
			//pre($this->db->last_query());die;			
			//pre($CAcardCount);die;
			/* if(count($CAcardCount) > 0){
				//pre(count($CAcardCount));die;
				//echo "<script>alert(No transaction available for export.);</script>";
				//continue;
			}else{ */

			
		//$dailyPriceList = $this->user_model->get_dailypricelist();
		
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Receipt_Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Transaction_Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Transaction_Time');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Method');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Vendor');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Card_Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Supplier');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'City');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Province_State');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Truck_Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Trailer_Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Item_Type');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Measure');
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Price');
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Tax');
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Total');

        // set Row
        $rowCount = 2;
        foreach ($CAcardCount as $element) {
			$decUnit_price = json_decode($element->unit_price);
			$decPride_price = json_decode($element->pride_price);
			$productNameJsonDecode = json_decode($element->category);
			$productQuantityJsonDecode = json_decode($element->quantity);

			$totalTaxAmount = 0;
			for($cnt=0; $cnt<count($productNameJsonDecode); $cnt++){
			$amoutQtyTotal = $decPride_price[$cnt] * $productQuantityJsonDecode[$cnt];
			$grandTotal = floor($amoutQtyTotal*100)/100;				
			if($element->billing_currency == 'CAD'){
				$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $element->gas_station_state)->get('tax')->result();
				$finalGST=0;$finalPST=0;$finalQST=0;
				foreach($getTaxRate as $taxTypeRows){
					if($taxTypeRows->tax_type == 'gst'){
						$gstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalGST = $grandTotal * $gstRate / 100;
					}
					if($taxTypeRows->tax_type == 'pst'){
						$pstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalPST = $grandTotal * $pstRate / 100;
					}
					if($taxTypeRows->tax_type == 'qst'){
						$qstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalQST = $grandTotal * $qstRate / 100;
					}				
					$totalTaxAmount = number_format($finalGST + $finalPST + $finalQST, 2);
				}				
			}
			if($element->billing_currency == 'USD'){
				$UOM = 'G';
			}else{
				$UOM = 'L';
			}
					$truckNumber = null; $trailerNumber = null;
					if($productNameJsonDecode[$cnt] == 'ULSD'){
						$itemCode = 1;
						if(!empty($element->unit_number)){
							$truckNumber = '"'.trim($element->unit_number).'"';
						}
					}
					if($productNameJsonDecode[$cnt] == 'ULSR'){
						$itemCode = 2;
						if(!empty($element->unit_number)){
							$trailerNumber = '"'.trim($element->unit_number).'"';
						}
					}
					if($productNameJsonDecode[$cnt] == 'DEFD'){
						$itemCode = 3;
						if(!empty($element->unit_number)){
							$truckNumber = '"'.trim($element->unit_number).'"';
						}
					}	         
					if(strpos(date("G:i a", strtotime($element->transaction_date)), 'pm') !== false){
						$transtime = date("h:i", strtotime($element->transaction_date))." PM";
					}else{
						$transtime = date("h:i", strtotime($element->transaction_date))." AM";
					}
					
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element->transaction_id);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, date('m/d/Y', strtotime($element->transaction_date)));
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $transtime);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, '"Credit"');
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, "Pride Diesel Inc."); 
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $rowCount, '"'.$element->card_number.'"', PHPExcel_Cell_DataType::TYPE_STRING);			
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, '"'.trim($element->gas_station_name).'"');
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, '"'.trim($element->gas_station_city).'"');
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, '"'.trim($element->gas_station_state).'"');
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $truckNumber);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $trailerNumber);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $productNameJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $productQuantityJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, '"'.$UOM.'"');
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $decPride_price[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $totalTaxAmount);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $grandTotal);

            $rowCount++;
			}
        }
		//die;
       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);//pre($object_writer);die;
			        header('Content-Type: application/vnd.ms-excel');
/* header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download"); */					
			       header("Content-Disposition: attachment;filename=Transactions_".date('Ymd').".xlsx");
			         ob_end_clean();
			        $object_writer->save('php://output');
exit;					
	}	
	
	public function exportTransactionBySingleCard($cardNum=null, $daterange = null){
		$this->load->model('user/user_model');
		$this->load->library('excel');

		$preDate = strtotime("-30 day", strtotime(date('Y-m-d H:i:s')));
			$this->db->select('transactions.*');
			$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
			//$this->db->join('users', 'users.id = cards.company_id');
			if(!empty($cardNum)){
				$this->db->where(array('cards.card_number'=> $cardNum));
			}			

			if(!empty($daterange) && $daterange != 'undefined'){
				//$expDateRange = explode('%20-%20', $daterange);
				$expDateRange = explode('%20-%20', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}else{
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date("Y-m-d H:i:s", $preDate). '" and "'. date('Y-m-d H:i:s').'"');				
			}
			$CAcardCount = $this->db->get('transactions')->result();
			//pre($CAcardCount);die;
			//if(count($CAcardCount) < 1){	
			if(count($CAcardCount) < 1){	
				echo "notransaction";
				exit();
			}
			//pre($this->db->last_query());die;			
			//pre($CAcardCount);die;
			/* if(count($CAcardCount) > 0){
				//pre(count($CAcardCount));die;
				//echo "<script>alert(No transaction available for export.);</script>";
				//continue;
			}else{ */

			
		//$dailyPriceList = $this->user_model->get_dailypricelist();
		
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Billing Currency');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Card Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Unit Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Invoice');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Amount');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Category');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Unit Price');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'GST/PST/QST');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Gas Station Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Gas Station City');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Gas Station State');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Transaction Date');
        //$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Date Created');

        // set Row
        $rowCount = 2;
        foreach ($CAcardCount as $element) {
			$decUnit_price = json_decode($element->unit_price);
			$decPride_price = json_decode($element->pride_price);
			$productNameJsonDecode = json_decode($element->category);
			$productQuantityJsonDecode = json_decode($element->quantity);

			$totalTaxAmount = 0;
			for($cnt=0; $cnt<count($productNameJsonDecode); $cnt++){
			$amoutQtyTotal = $decPride_price[$cnt] * $productQuantityJsonDecode[$cnt];
			$grandTotal = floor($amoutQtyTotal*100)/100;				
			if($element->billing_currency == 'CAD'){
				$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $element->gas_station_state)->get('tax')->result();
				$finalGST=0;$finalPST=0;$finalQST=0;
				foreach($getTaxRate as $taxTypeRows){
					if($taxTypeRows->tax_type == 'gst'){
						$gstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalGST = $grandTotal * $gstRate / 100;
					}
					if($taxTypeRows->tax_type == 'pst'){
						$pstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalPST = $grandTotal * $pstRate / 100;
					}
					if($taxTypeRows->tax_type == 'qst'){
						$qstRate = str_replace('%', '', $taxTypeRows->tax_rate);
						$finalQST = $grandTotal * $qstRate / 100;
					}				
					$totalTaxAmount = number_format($finalGST + $finalPST + $finalQST, 2);
				}				
			}
	
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element->billing_currency);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount, $element->card_number, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element->unit_number);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $element->invoice);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $grandTotal);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $productNameJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $decPride_price[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $productQuantityJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $totalTaxAmount);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $element->gas_station_name);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $element->gas_station_city);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $element->gas_station_state);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $element->transaction_date);
            //$objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $element['date_created']);

            $rowCount++;
			}
        }
		//die;
       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);//pre($object_writer);die;
			        header('Content-Type: application/vnd.ms-excel');
/* header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download"); */					
			       header("Content-Disposition: attachment;filename=Transactions_".date('Ymd').".xlsx");
			         ob_end_clean();
			        $object_writer->save('php://output');
exit;					
	}	

	public function export_company_trans_csv($cid){
		$this->load->model('user/user_model');
		$filename = 'company_trans_'.date('Ymd').'.csv';
		$usersData = $this->account_model->exportTransByComp($cid);	
		$data = "";
		$data1 = "Billing Currency,Card Number,Invoice,Amount,Category,Unit Price,Quantity,GST/PST/QST,Gas Station Name,Gas Station City,Gas Station State,Transaction Date,Date Created\n";
		foreach ($usersData as $key=>$element){
			$productNameJsonDecode = json_decode($element['category']);
			$pridePriceJsonDecode = json_decode($element['pride_price']);
			$productQuantityJsonDecode = json_decode($element['quantity']);
			$rowinc=0; $totalTaxAmount=0;	$finalGST=0;$finalPST=0;$finalQST=0;
			foreach($productNameJsonDecode as $productNameJsonDecodeRows){
			$productName = $productNameJsonDecode[$rowinc];
			$pridePrice = $pridePriceJsonDecode[$rowinc];
			$productQuantity = $productQuantityJsonDecode[$rowinc];
			$calcAmount = $pridePrice * $productQuantity;
			$amount = floor($calcAmount*100)/100;
			
									if($element['billing_currency'] == 'CAD'){
										$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $element['gas_station_state'])->get('tax')->result();
										
										foreach($getTaxRate as $taxTypeRows){

											if($taxTypeRows->tax_type == 'gst'){
												$gstRate = str_replace('%', '', $taxTypeRows->tax_rate);
												$finalGST = $amount * $gstRate / 100;
											}
											if($taxTypeRows->tax_type == 'pst'){
												$pstRate = str_replace('%', '', $taxTypeRows->tax_rate);
												$finalPST = $amount * $pstRate / 100;
											}
											if($taxTypeRows->tax_type == 'qst'){
												$qstRate = str_replace('%', '', $taxTypeRows->tax_rate);
												$finalQST = $amount * $qstRate / 100;
											}
											$combineTaxes = $finalGST + $finalPST + $finalQST;	
											$totalTaxAmount = floor($combineTaxes*100)/100;
										}
									}
			$cardNumber = $element['card_number'];
			$data .= $element['billing_currency'].",".$cardNumber.",".$element['invoice'].",".$amount.",".$productName.",".$pridePrice.",".$productQuantity.",".$totalTaxAmount.",".$element['gas_station_name'].",".$element['gas_station_city'].",".$element['gas_station_state'].",".$element['transaction_date'].",".$element['date_created']."\n";
			$rowinc++;
			}
		}
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		echo $data1.$data; 
		exit();
	}
	function arrayToCSV($inputArray)
	{
		$csvFieldRow = array();
		foreach ($inputArray as $CSBRow) {
			$csvFieldRow[] = str_putcsv($CSBRow);
		}
		$csvData = implode("\n", $csvFieldRow);
		return $csvData;
	}	
	
	public function transaction_edit(){
		$this->settings['title'] = 'Edit Transaction';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Edit Transaction', base_url() . 'account/transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();		
		
		$this->_render_template('transaction/edit', $this->data);
	}
	
	public function transaction_view_by_cid($cid=NULL, $daterange=null, $company_name=null){
		$this->settings['title'] = 'View Transaction';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transaction', base_url() . 'account/transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		if(!empty($_GET['date_range'])){
			$daterange = $_GET['date_range'];
		}
		if(!empty($_GET['company_name'])){
			$company_name = $_GET['company_name'];
		}		
		$this->data['cardsTransData'] = $this->account_model->get_card_trans_by_cid($cid, $daterange, $company_name);
		#pre($this->db->last_query());die;
		$this->data['maxInvId'] = $this->account_model->get_max_trans_inv_id();

		$this->_render_template('transaction/view', $this->data);
	}

	public function updateTransactionPrice(){
		//echo $_POST['rowid'];
		$getTransData = $this->db->select('pride_price')->where('id', $_POST['transid'])->get('transactions')->row();
		//$getTransData = $this->db->select('pride_price')->where('id', 39)->get('transactions')->row();
		$priceDecode = json_decode($getTransData->pride_price);
		for($i=0;$i<count($priceDecode);$i++){
			if($i == $_POST['rowid']){
				$editedPrice = $_POST['editedval'];
			}else{
				$editedPrice = $priceDecode[$i];
			}
			$finalEditedPrice[] = $editedPrice;
			
		}
		//echo json_encode($finalEditedPrice);
		$this->db->set('pride_price', json_encode($finalEditedPrice));
		$this->db->where('id', $_POST['transid']);
		$this->db->update('transactions');
	}
	
	public function updateTransactionProductName(){
		//echo $_POST['rowid'];
		$getTransData = $this->db->select('category')->where('id', $_POST['transid'])->get('transactions')->row();
		//$getTransData = $this->db->select('pride_price')->where('id', 39)->get('transactions')->row();
		$priceDecode = json_decode($getTransData->category);
		for($i=0;$i<count($priceDecode);$i++){
			if($i == $_POST['rowid']){
				$editedPrice = $_POST['editedval'];
			}else{
				$editedPrice = $priceDecode[$i];
			}
			$finalEditedPrice[] = $editedPrice;
			
		}
		//echo json_encode($finalEditedPrice);
		$this->db->set('category', json_encode($finalEditedPrice));
		$this->db->where('id', $_POST['transid']);
		$this->db->update('transactions');
	}	

	public function card_transactions($transid=NULL){
		$this->settings['title'] = 'View Transactions';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transactions', base_url() . 'account/card_transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		//$this->data['cardDetails'] = $this->account_model->get_card_transactions($cardNumber, $daterange);
		$this->data['cardsTransData'] = $this->account_model->get_card_transactions($transid);
		//pre($this->db->last_query());
		//$this->data['driverDetails'] = $this->account_model->get_card_driver($cardNumber);
		//die;
		$this->_render_template('transaction/card_trans_view', $this->data);		
	}
	
	public function comp_card_transactions($cardNumber=NULL, $daterange=null){
		$this->load->model('user/user_model');
		$this->settings['title'] = 'View Transactions';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transactions', base_url() . 'account/card_transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		$card = null;
		if(!empty($_GET['date_range'])){
			$daterange = $_GET['date_range'];
			$card = $_GET['card'];
		}
		$this->data['cardDetails'] = $this->account_model->get_card_transactions($cardNumber, $daterange, $card);
		$this->data['driverDetails'] = $this->account_model->get_card_driver($cardNumber);
		$this->data['dailyPriceList'] = $this->user_model->get_dailypricelist();
		
		$this->_render_template('transaction/card_trans_view_company', $this->data);		
	}

	public function invoice_pdf(){
		$this->settings['title'] = 'All Invoices';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('All Invoices', base_url() . 'account/invoice_pdf');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$userSessDetails = $this->session->userdata('userdata');
		$companyId = $userSessDetails->id;
		//$this->data['allInvoices'] = $this->account_model->get_company_invoices($companyId);
		
		$this->load->library('pagination');
		//Get all data of company
		$where = '';
		if(!empty($_GET['invoiceid'])){
			$where = $_GET['invoiceid'];
		}
		$this->data['allInvoices'] = $this->account_model->get_company_invoices($companyId, $where);
        // pagination
        $config['base_url'] = site_url('account/invoice_pdf');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_company_invoice_pagination($config['per_page'], $page, $companyId, $where);		
		$this->_render_template('invoice/company-index', $this->data);		
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
      		foreach($fetchInvoiceData as $transactionhis){
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
	
	public function view_invoiced_trans($invoiceid){
		require_once(APPPATH.'libraries/tcpdf/tcpdf.php');
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
		$invoiceData = $this->db->where('id', $invoiceid)->get('transaction_invoice')->row();
		$cid = $invoiceData->company_id;
		
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
			$oldDate = strtotime($invoiceData->invoice_date);
			$newDate = strtotime("+7 day", $oldDate);
			$dueDate = date('Y-m-d', $newDate);
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
								<td style="border-right: 1px solid #dcdcdc;">'.$invoiceData->invoice_id.'</td>
								<td style="border-right: 1px solid #dcdcdc;">'.$invoiceData->invoice_date.'</td>
								<td>'.$dueDate.'</td>
							</tr>
						</table>
					</td>
					<td style="50%">
					</td>
				</tr>	
			</table><br /></div>';

			
		$invoiceDataJsonDecode = json_decode($invoiceData->invoice_data);
		foreach($invoiceDataJsonDecode as $invoiceDataJsonDecodeRows){
			$allCards[$invoiceDataJsonDecodeRows->card_number] = $invoiceDataJsonDecodeRows->card_number;
			$allCards1[] = $invoiceDataJsonDecodeRows->card_number;
			$transaction_ids[$invoiceDataJsonDecodeRows->card_number][] = $invoiceDataJsonDecodeRows->transaction_id;
			
		}
		$cardNumberOrg = array_keys($transaction_ids);
		$inc=0;
		$grandDiscountAllCards = 0;	
		$grandTotalByAllCards = 0;	
		foreach($transaction_ids as $allCardsRows){
			
				
			$content .= '<h3>Transactions for card: '.$cardNumberOrg[$inc].'</h3>
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


		$usdCount = 1;
		$grandTotalByCard = 0; 
		$grandTotalQty = 0;
		$grandDiscount = 0;
		for($numOfTrans=0; $numOfTrans<count($allCardsRows); $numOfTrans++){
			$arrayOne = 0;
			$this->db->select('cards.*, users.*, transactions.id as transid, transactions.*');		
			$this->db->join('users', 'users.id = cards.company_id');
			$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.transaction_id'=>$allCardsRows[$numOfTrans]));
			$CardValues = $this->db->get('cards')->result();			
			foreach($CardValues as $CardValuesRows){
				
				if($usdCount % 2 == 0){
					$content .= '<tr class="even-bg">';		
				}else{
					$content .= '<tr>';
				}
				$explodeDateTime = explode(' ', $CardValuesRows->transaction_date);	
				$transactionDate = $explodeDateTime[0];		
				$transactionTime = $explodeDateTime[1];
				$productName = json_decode($CardValuesRows->category);	
				$quantity = json_decode($CardValuesRows->quantity);
				$EFSprice = json_decode($CardValuesRows->unit_price);
				$PridePrice = json_decode($CardValuesRows->pride_price);
				
				$efsRetailPrice = 0;
				$totalSavings = 0;
				$discountAmount = 0;
				$priceByPrideDiesel = 0;
				$priceByPrideDiesel = $PridePrice[$arrayOne] * $quantity[$arrayOne];
				if($productName[$arrayOne] != 'DEFD'){
					$efsRetailPrice = $EFSprice[$arrayOne];
					$priceByEFS = $efsRetailPrice * $quantity[$arrayOne];
					
					$DiscAmtCalc = $priceByEFS - $priceByPrideDiesel;
					$discountAmount = floor($DiscAmtCalc*1000)/1000;
					$totalSavings = $efsRetailPrice - $PridePrice[$arrayOne];
				}
				
				$prideDieselPriceFinal = floor($priceByPrideDiesel*100)/100;
				if($CardValuesRows->billing_currency == 'USD'){
					$measureUnit = 'G';
				}else{
					$measureUnit = 'L';
				}	
				$content .= '
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$CardValuesRows->card_number .'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$transactionDate.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$CardValuesRows->gas_station_name.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$CardValuesRows->gas_station_id.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$productName[$arrayOne].'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$efsRetailPrice.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$totalSavings.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$quantity[$arrayOne].'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$quantity[$arrayOne].'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;">'.$prideDieselPriceFinal.'</td>
				</tr>';	
				if($usdCount % 2 == 0){
					$content .= '<tr class="even-bg">';
				}else{
					$content .= '<tr>';
				}
					$content .= '<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$CardValuesRows->transaction_id .'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$transactionTime.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$CardValuesRows->gas_station_city .'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$CardValuesRows->gas_station_state .'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$measureUnit.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$PridePrice[$arrayOne].'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$discountAmount.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$prideDieselPriceFinal.'</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">0</td>
					<td>'.$CardValuesRows->billing_currency .'</td>
				</tr>';
				$grandTotalByCard += $prideDieselPriceFinal;
				$grandTotalQty += $quantity[$arrayOne];
				$grandDiscount += $discountAmount;
			}
			
		$usdCount++;
		}
	$content .= '</table><br />
	<h3>Total for card: '.$cardNumberOrg[$inc].'</h3>
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
			<td>'.$grandTotalByCard.'</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>'.$grandTotalQty.'</td>
			<td>0</td>
			<td>'.$grandTotalQty.'</td>
			<td>0</td>
			<td>0</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>0.00</td>
			<td>'.$grandDiscount.'</td>
			<td>'.$grandTotalByCard.'</td>
			<td>'.$CardValuesRows->billing_currency .'</td>
		</tr>
		 </table>';
		$billingCurrency = $CardValuesRows->billing_currency;
		$grandDiscountAllCards += $grandDiscount;
		$grandTotalByAllCards += $grandTotalByCard;
		 
		$inc++;
		}
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
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.floor($grandDiscountAllCards*100)/100 .'</td>
			<td style="border-bottom: 1px solid #dcdcdc;border-right: 1px solid #dcdcdc;">'.$grandTotalByAllCards.'</td>
			<td style="border-bottom: 1px solid #dcdcdc;">'.$billingCurrency.'</td>
		</tr>
		<tr><td colspan="17" style="text-align:right; border-bottom: 1px solid #dcdcdc;">Payment Recieved 0.00</td></tr>
		<tr><td colspan="17" style="text-align:right;">Amount Due '.$grandTotalByAllCards.'</td></tr>
		 </table>

		<p style="">QST# 1221749509TQ0001</p>'; 

		$obj_pdf->writeHTML($content);	
	//print_r($content);
	ob_end_clean();
	

    $obj_pdf->Output('sample.pdf', 'I');	die;	
    //$obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_USD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
	
	//unlink($pdfFilePath);
	//unset($cardDetail, $pdfFilePath);		
	}

	public function generate_trans_invoice($cid){
		//ob_start();
		$this->load->model('user/user_model');
		$this->load->model('card/card_model');			
		require_once(APPPATH.'libraries/tcpdf/tcpdf.php');  
		//$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$daterange = null;
		if(!empty($this->input->post('daterange'))){
			$daterange = $this->input->post('daterange');
		}
		$company_name = null;
		if(!empty($this->input->post('company_name'))){
			$company_name = $this->input->post('company_name');
		}		
		$count=0;
		$i = 0;
		$this->data['cardsTransData'] = $this->account_model->get_card_trans_by_cid($cid, $daterange, $company_name);
		foreach($this->data['cardsTransData'] as $key=>$transValues){
			if($transValues->billing_currency == 'USD' && $transValues->transactionAt == 'EFS'){
				$usdTrans[$i] = $transValues;
			}
			if($transValues->billing_currency == 'CAD' && $transValues->transactionAt == 'EFS'){
				$cadTrans[$i] = $transValues;
			}
			if($transValues->billing_currency == 'CAD' && $transValues->transactionAt == 'HUSKY'){
				$cadTransHusky[$i] = $transValues;
			}			
			$i++;
		}
		if(!empty($usdTrans)){
			$this->generateUSInvoice($cid,$daterange);
		}
		if(!empty($cadTrans)){
			$this->generateCanadianInvoice($cid,$daterange);
		}
		if(!empty($cadTransHusky)){
			$this->generateCanadianInvoiceHusky($cid,$daterange);
		}		
		
		//ob_end_flush();
		echo"<script>alert('Invoice generated and sent.')</script>";	
		redirect(base_url().'account/ledgers', 'refresh');
	}
	
	public function generateUSInvoice($cid,$daterange){
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
			$obj_pdf->SetFont('helvetica', '', 12);
			
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
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'USD', 'transactions.invoice_status'=>0, 'transactions.transactionAt'=> 'EFS'));
			if(!empty($daterange)){
				$expDateRange = explode(' - ', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}			
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
							<td>';
							if($companyDetails->company_location == 'ca'){
								$content .= '<h4>Pride Diesel Inc.</h4><br />
								Corporate office: 10862 Steeles Ave East,<br/>Milton,ON L9T 2X8<br />';
							}else{
								$content .= '<h4>Pride Diesel USA Inc.</h4><br />
								Corporate office: 1100 Brickell Bay DR Unit 310747,<br/>Miami, FL 33231<br />';
							}
							$content .= 'Fax: (877)867-8922<br />
							Ph. 1888-909-7117<br />
							www.pridediesel.com<br />
							Email: info@pridediesel.com<br />';
							if($companyDetails->company_location == 'ca'){
								$content .= 'GST/HST # 808170526RT0001';
							}
							$content .= '</td>
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
							Period beginning<br />
							Period ending<br />
							</td>
							<td>
							'.$companyDetails->id .'<br />
							<strong>'.$companyDetails->company_name .'</strong><br />
							'.$companyDetails->address .'<br />
							
							CL'.$invoiceID.'<br />
							'.date('d/m/Y').'<br />
							'.date('d/m/Y', $periodBeg).'<br />
							'.date('d/m/Y', $periodEnd).'<br />
							Due on Receipt
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
			$datContent .= "Receipt_NumberTransaction_DateMethodVendorCard_NumberProvince_StateItem_TypeQuantityMeasurePriceTotal\n"; 
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
			  <th style="width:3%;">Province</th>
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
			$this->db->select('users.*, transactions.*, cards.driver_id');
			$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
			$this->db->join('users', 'users.id = cards.company_id');
			//$this->db->join('drivers', 'drivers.company_id = cards.company_id', 'LEFT');
			//$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'USD', 'transactions.invoice_status'=>0, 'transactions.transactionAt'=> 'EFS'));
			if(!empty($daterange)){
				$expDateRange = explode(' - ', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}			
			//$this->db->group_by('transactions.card_number');
			$cardCount = $this->db->get('transactions')->result();
			//pre($cardCount);die;
			$totalQuantity = 0; $grandTotal = 0;
			$jsonInc = 0;
			$driverName = "-";
			$transactionCount=0;
			$csvdata = "";

			foreach($cardCount as $cardCountRows){
				$productNameJsonDecode = json_decode($cardCountRows->category);
				$pridePriceJsonDecode = json_decode($cardCountRows->pride_price);
				$EFSpriceJsonDecode = json_decode($cardCountRows->unit_price);
				$productQuantityJsonDecode = json_decode($cardCountRows->quantity);
				$explodeDateTime = explode(' ', $cardCountRows->transaction_date);	
				$transactionDate = $explodeDateTime[0];		
				$transactionTime = $explodeDateTime[1];
				$driverid = $cardCountRows->driver_id;
				$driverName = "-";
				if(!empty($driverid)){
					$getDriverName = $this->db->select('name')->where('id', $driverid)->get('drivers')->row();
					$driverName = $getDriverName->name;
				}				
				
				$rowCount=0; 
				foreach($productNameJsonDecode as $productCountJsonDecodeRows){
					$calcProduct = $productQuantityJsonDecode[$rowCount] * $pridePriceJsonDecode[$rowCount];
					//$total = floor($calcProduct*100)/100;
					$total = $calcProduct;
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
						<td style="width:5%;">$ '.floor($total*100)/100 .'</td>
						<td style="width:5%;">'.$cardCountRows->billing_currency .'</td>
					</tr>';
					$totalQuantity += $productQuantityJsonDecode[$rowCount];
					$grandTotal += $total;
					/*trans_data field*/
					$transJsonArrayObject = (array($cardCountRows->card_number => array('product_name'=>$productNameJsonDecode[$rowCount], 'quantity'=>$productQuantityJsonDecode[$rowCount], 'unit_price'=>$pridePriceJsonDecode[$rowCount], 'amountwithouttax' => $total)));
					$transationDetails[$jsonInc] = $transJsonArrayObject;
					/*invoice_data field*/
					$jsonArrayObject = (array('card_number' =>$cardCountRows->card_number,'transaction_date' => $cardCountRows->transaction_date, 'transaction_id' => $cardCountRows->transaction_id));
					$arr[$jsonInc] = $jsonArrayObject;

					/* CSV for TransPlus Software*/
					$filename = 'company_trans_plus_USD_'.$cid."_".date('Ymd').'.csv';
					$truckNumber = null; $trailerNumber = null;
					if($productNameJsonDecode[$rowCount] == 'ULSD'){
						$itemCode = 1;
						if(!empty($cardCountRows->unit_number)){
							$truckNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					if($productNameJsonDecode[$rowCount] == 'ULSR'){
						$itemCode = 2;
						if(!empty($cardCountRows->unit_number)){
							$trailerNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					if($productNameJsonDecode[$rowCount] == 'DEFD'){
						$itemCode = 3;
						if(!empty($cardCountRows->unit_number)){
							$truckNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					$Receipt_Number = "CL".$invoiceID;
					/* $csvdata .= '""""'.$Receipt_Number.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'",""""'.$cardCountRows->gas_station_state .'",'.$itemCode.",".$productQuantityJsonDecode[$rowCount].',""""G",'.$pridePriceJsonDecode[$rowCount].", ,".$total."\n"; */
					
					if(strpos(date("G:i a", strtotime($cardCountRows->transaction_date)), 'pm') !== false){
						$transtime = date("h:i", strtotime($cardCountRows->transaction_date))." p";
					}else{
						$transtime = date("h:i", strtotime($cardCountRows->transaction_date))." a";
					}
					$toalAfterFloor = floor($total*100)/100;
					$csvdata .= '""""'.$cardCountRows->transaction_id.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).",".$transtime.',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'",""""'.$cardCountRows->gas_station_name.'",""""'.$cardCountRows->gas_station_city.'",""""'.$cardCountRows->gas_station_state .'",'.$truckNumber.','.$trailerNumber.','.$productNameJsonDecode[$rowCount].','.$productQuantityJsonDecode[$rowCount].',""""G",'.$pridePriceJsonDecode[$rowCount]." ,".$toalAfterFloor."\n";
					/*Dat file fields*/
					$datContent .= '"'.$cardCountRows->transaction_id.'"';
					$datContent .= date('m/d/Y', strtotime($cardCountRows->transaction_date));
					$datContent .= '"Credit"';
					$datContent .= 'PRIDE DIESEL INC.';
					$datContent .= '"'.$cardCountRows->card_number .'"';
					$datContent .= '"'.$cardCountRows->gas_station_state .'"';
					$datContent .= $productNameJsonDecode[$rowCount];
					$datContent .= $productQuantityJsonDecode[$rowCount];
					$datContent .= '"G"';
					$datContent .= $pridePriceJsonDecode[$rowCount];
					//$datContent .= '';
					$datContent .= $total."\n";
					/*fields Ended*/
					//unset($truckNumber,$trailerNumber);
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
							<td>$ '.floor($grandTotal*100)/100 .'</td>
							<td>USD</td>
						</tr>
					</tbody>
					</table>';
			/*Dat File create*/
			/* $file = fopen(FCPATH ."assets/modules/invoices/PRIDEDIESEL_110_".$invoiceID."_USD_".date('Ymd').".dat","w");
			//fwrite($file,"$a\n$b$c\n$e$d$f$k$g$l$h$m\n$n$o$p$q$r$s$t\n$u$v");
			fwrite($file,"$datContent");
			fclose($file); */			
			/*CSV for TransPlus Software*/
			//header('Content-Type: text/csv');
			//header('filename="sample.csv"');
			$fd = fopen (FCPATH."assets/modules/invoices/".$filename, "w");
			/* $csvheader ="Receipt_Number,Transaction_Date,Method,Vendor,Card_Number,Province_State,Item_Type,Quantity,Measure,Price,Tax,Total\n"; */
			$csvheader ="Receipt_Number,Transaction_Date,Transaction_Time,Method,Vendor,Card_Number,Supplier,City,Province_State,Truck_Number,Trailer_Number,Item_Type,Quantity,Measure,Price,Total\n";
			//header('Content-Type: application/csv');
			//header('Content-Disposition: attachment; filename="'.$filename.'"');
			
			$fileContent = $csvheader.$csvdata;
			fputs($fd, $fileContent);
			//$fileContent = $csvheader;
			//fputcsv($fd, $csvheader);
			/* fputs($fd, $csvheader);
			//fwrite($fd, $fileContent);
			//fputs($fd, $fileContent);
			foreach ($csvdata as $line) {
				// though CSV stands for "comma separated value"
				// in many countries (including France) separator is ";"
				//fputcsv($fd, $line);
				fputs($fd, $line);
			} */
			fclose($fd);
			//echo $csvheader.$csvdata; 
			//exit();
			//Insert invoice data in transaction invoice table			
			$data['invoice_id'] = "CL".$invoiceID;
			$data['company_id'] = $cid;
			$data['invoice_date'] = date('Y-m-d');
			$data['billingOn'] = 'EFS';
			$data['billingCurrency'] = 'USD';
			$data['invoice_data'] = $invoice_data;
			$data['trans_data'] = $transactionDetails;		  
			$data['status'] = 0;	  
			$data['grand_total'] = $grandTotal;
			$data['date_created'] = date('Y-m-d H:i:s');
			$data['date_modified'] = date('Y-m-d H:i:s');
			$this->db->insert('transaction_invoice', $data);
			//pre($content);die;
			$obj_pdf->writeHTML($content);
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
			$csvFilePath = FCPATH . 'assets/modules/invoices/company_trans_plus_USD_'.$cid."_".date('Ymd').'.csv';
			//$this->email->clear();
			$usdresult = $this->email
						->from('info@pridediesel.com', 'From Pride Diesel')
						//->to('jagdishchander6373@gmail.com')
						->to($company_email_usd)
						->bcc('abhinavdua1435@gmail.com')
						//->cc('jagdishchander4667@gmail.com')
						->subject($usd_subject)
						->message($usd_body)
						->attach($pdfFilePath)
						->attach($csvFilePath)
						->send();
			$this->email->clear($pdfFilePath);
			$this->email->clear($csvFilePath);

			//if($usdresult) {
				//echo "Send";
				//unlink($pdfFilePath); //for delete generated pdf file. 
			//}
	}		
	
	public function generateCanadianInvoice($cid,$daterange){
	/* CAD Transaction Invoice Generate Code */
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
		//$obj_pdf->SetFont('helvetica', '', 9);
		$obj_pdf->SetFont('helvetica', '', 12);
		
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
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0, 'transactions.transactionAt'=> 'EFS'));
		if(!empty($daterange)){
			$expDateRange = explode(' - ', $daterange);
			$startDate = $expDateRange[0];
			$endDate = $expDateRange[1];			
			$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
		}			
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
			/*HH Code Started*/		  
			$datContent .= "Receipt_NumberTransaction_DateMethodVendorCard_NumberProvince_StateItem_TypeQuantityMeasurePriceTaxTotal\n"; 
			/*HH Code Ended*/			
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
							<td>';
							if($companyDetails->company_location == 'ca'){
								$content .= '<h4>Pride Diesel Inc.</h4><br />
								Corporate office: 10862 Steeles Ave East,<br/>Milton,ON L9T 2X8<br />';
							}else{
								$content .= '<h4>Pride Diesel USA Inc.</h4><br />
								Corporate office: 1100 Brickell Bay DR Unit 310747,<br/>Miami, FL 33231<br />';
							}
							$content .= 'Fax: (877)867-8922<br />
							Ph. 1888-909-7117<br />
							www.pridediesel.com<br />
							Email: info@pridediesel.com<br />';
							if($companyDetails->company_location == 'ca'){
								$content .= 'GST/HST # 808170526RT0001';
							}
							$content .= '</td>
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
							Period beginning<br />
							Period ending<br />
							</td>
							<td>
							'.$companyDetails->id .'<br />
							<strong>'.$companyDetails->company_name .'</strong><br />
							'.$companyDetails->address .'<br />
							
							CL'.$invoiceID.'<br />
							'.date('d/m/Y').'<br />
							'.date('d/m/Y', $periodBeg).'<br />
							'.date('d/m/Y', $periodEnd).'<br />
							Due on Receipt
							</td>
						</tr>
					</table>
			   </td>
		       <td class="pride">Page-'.$obj_pdf->getPage().'<br />Confidential information</td>
		  </tr>
         
		</tbody>
		</table><div class="header-section-bot"></div><br />';
			$company_email_ca = $companyDetails->company_email;
		
		$content .='<table class="custom-css" style="border: 1px solid #D9D9D9 ; clear: both; border-spacing: 0; border-collapse: collapse;" cellpadding="3">
		<tbody>		
           <tr>
		      <th style="width:12%;">Card #</th>
		      <th style="width:7%;">Driver name</th>
			  <th style="width:7%;">Transaction #</th>
			  <th style="width:7%;">Site name</th>
			  <th style="width:4%;">Site #</th>
			  <th style="width:3%;">City</th>
			  <th style="width:3%;">Province</th>
			  <th style="width:6%;">Date</th>
			  <th style="width:5%;">Time</th>
			  <th style="width:4%;">Unit #</th>
			  <th style="width:5%;">Product</th>
			  <th style="width:3%;">UOM</th>
			  <th style="width:7%;">Price per unit</th>
			  <th style="width:7%;">Total quantity</th>
			  <th style="width:6%;">HST/GST</th>
			  <th style="width:4%;">QST</th>
			  <th style="width:5%;">Total</th>
			  <th style="width:5%;">Currency</th>
           </tr>';
			$this->db->select('users.*, transactions.*, cards.driver_id');
			$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
			$this->db->join('users', 'users.id = cards.company_id');
			//$this->db->join('drivers', 'drivers.company_id = cards.company_id', 'LEFT');
			//$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0, 'transactions.transactionAt'=> 'EFS'));
			if(!empty($daterange)){
				$expDateRange = explode(' - ', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}			
			//$this->db->group_by('transactions.card_number');
			//$CAcardCount = $this->db->get('cards')->result();
			$CAcardCount = $this->db->get('transactions')->result();
			//pre($CAcardCount);die;
			$totalQuantity = 0; $grandTotal = 0; $grandTax=0;
			$jsonInc = 0; $grandQSTTaxAmt = $grandTaxAmt = 0;
			
			$transactionCount = 0;
			$csvdata = "";
			foreach($CAcardCount as $cardCountRows){
				$productNameJsonDecode = json_decode($cardCountRows->category);
				$pridePriceJsonDecode = json_decode($cardCountRows->pride_price);
				$EFSpriceJsonDecode = json_decode($cardCountRows->unit_price);
				$productQuantityJsonDecode = json_decode($cardCountRows->quantity);
				$explodeDateTime = explode(' ', $cardCountRows->transaction_date);	
				$transactionDate = $explodeDateTime[0];		
				$transactionTime = $explodeDateTime[1];
				$driverid = $cardCountRows->driver_id;//pre($cardCountRows->driver_id);die;
				$driverName = "-";
				if(!empty($driverid)){
					$getDriverName = $this->db->select('name')->where('id', $driverid)->get('drivers')->row();
					$driverName = $getDriverName->name;
				}				
				
				$rowCount=0; $finalGST=0; $finalPST=0; $finalQST=0; $finalTaxAmt=0; 
				foreach($productNameJsonDecode as $productCountJsonDecodeRows){
					$calcProduct = $productQuantityJsonDecode[$rowCount] * $pridePriceJsonDecode[$rowCount];
					$total = floor($calcProduct*100)/100;
					$transactionCount++;
					/* Product Taxes Start */
				/* GST/PST/QST Start */						
				$taxapplicable = $this->db->where('product_name', $productNameJsonDecode[$rowCount])->get('products')->row();

				if(!empty($taxapplicable->tax) ){
				$taxArray = json_decode($taxapplicable->tax);
					foreach($taxArray as $key=>$taxArrayRow){	
					$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>$cardCountRows->gas_station_state))->get('tax')->row();

					//GST/HST/FNT || PST/QST
					if(!empty($taxOutput['gst']->tax_rate)){$gst = str_replace('%', '', $taxOutput['gst']->tax_rate);}else{$gst = 0;}
					if(!empty($taxOutput['pst']->tax_rate)){$pst = str_replace('%', '', $taxOutput['pst']->tax_rate);}else{$pst = 0;}
					if(!empty($taxOutput['qst']->tax_rate)){$qst = str_replace('%', '', $taxOutput['qst']->tax_rate);}else{$qst = 0;}			
					}
					
				}
				
				if(empty($gst)){$gst = 0;}
				if(empty($pst)){$pst = 0;}
				if(empty($qst)){$qst = 0;}
				//$totalTax = $gst + $pst + $qst;
				$totalTax = $gst + $pst;
				########## GST/HST Calculations
				if(strlen($totalTax) < 2){
					$revTotalTaxAmt = '1.0'.$totalTax;
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;					
				}elseif(strpos($totalTax, '.')!==false){
					$revTotalTaxAmt = '1.'.str_replace(".","",$totalTax);
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;
				}else{
					$revTotalTaxAmt = '1.'.$totalTax;
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;
				}
				########## QST Calculations
				$totalTaxQST = $qst;
				
				if(strlen($totalTaxQST) < 2){
					$revTotalQSTAmt = '1.0'.$totalTaxQST;
					$amtAfterReversalQST = floatval($total) / floatval($revTotalQSTAmt);
					$minuspriceandQSTtax = $total - $amtAfterReversalQST;
					$finalQSTTaxAmt =  floor($minuspriceandQSTtax*100)/100;					
				}elseif(strpos($totalTaxQST, '.')!==false){
					$revTotalQSTAmt = '1.0'.str_replace(".","",$totalTaxQST);
					$amtAfterReversalQST = floatval($total) / floatval($revTotalQSTAmt);
					$minuspriceandQSTtax = $total - $amtAfterReversalQST;
					$finalQSTTaxAmt =  floor($minuspriceandQSTtax*100)/100;
				}else{
					$revTotalQSTAmt = '1.'.$totalTaxQST;
					$amtAfterReversalQST = floatval($total) / floatval($revTotalQSTAmt);
					$minuspriceandQSTtax = $total - $amtAfterReversalQST;
					$finalQSTTaxAmt =  floor($minuspriceandQSTtax*100)/100;
				}				
	   
					$content .='<tr>
						<td style="width:12%;">'.$cardCountRows->card_number .'</td>
						<td style="width:7%;">'.$driverName.'</td>
						<td style="width:7%;">'.$cardCountRows->transaction_id .'</td>
						<td style="width:7%;">'.$cardCountRows->gas_station_name .'</td>
						<td style="width:4%;">'.$cardCountRows->gas_station_id .'</td>
						<td style="width:3%;">'.$cardCountRows->gas_station_city .'</td>
						<td style="width:3%;">'.$cardCountRows->gas_station_state .'</td>
						<td style="width:6%;">'.$transactionDate.'</td>
						<td style="width:5%;">'.$transactionTime.'</td>
						<td style="width:4%;">'.$cardCountRows->unit_number .'</td>
						<td style="width:5%;">'.$productNameJsonDecode[$rowCount].'</td>
						<td style="width:3%;">L</td>
						<td style="width:7%;">$ '.$pridePriceJsonDecode[$rowCount].'</td>
						<td style="width:7%;">'.$productQuantityJsonDecode[$rowCount].'</td>
						<td style="width:6%;">$ '.$finalTaxAmt.'</td>
						<td style="width:4%;">$ '.$finalQSTTaxAmt.'</td>
						<td style="width:5%;">$ '.$total.'</td>
						<td style="width:5%;">'.$cardCountRows->billing_currency .'</td>
					</tr>';
					$totalQuantity += $productQuantityJsonDecode[$rowCount];
					
					$grandTaxAmt += $finalTaxAmt;
					$grandQSTTaxAmt += $finalQSTTaxAmt;
					$grandTotal += floor($total*100)/100;
					/*trans_data field*/
					$CAtransJsonArrayObject = (array($cardCountRows->card_number => array('product_name'=>$productNameJsonDecode[$rowCount], 'quantity'=>$productQuantityJsonDecode[$rowCount], 'unit_price'=>$pridePriceJsonDecode[$rowCount], 'taxamount'=>$finalTaxAmt, 'qstTax'=>$grandQSTTaxAmt, 'amountwithouttax' => $total)));
					$transationDetails[$jsonInc] = $CAtransJsonArrayObject;
					/*invoice_data field*/
					$CAjsonArrayObject = (array('card_number' =>$cardCountRows->card_number,'transaction_date' => $cardCountRows->transaction_date, 'transaction_id' => $cardCountRows->transaction_id));
					$arr[$jsonInc] = $CAjsonArrayObject;
					$total_tax_amount = $finalTaxAmt + $finalQSTTaxAmt;
					/* CSV for TransPlus Software*/
					$filename = 'company_trans_plus_CAD_'.$cid."_".date('Ymd').'.csv';
					$truckNumber = null; $trailerNumber = null;
					if($productNameJsonDecode[$rowCount] == 'ULSD'){
						$itemCode = 1;
						if(!empty($cardCountRows->unit_number)){
							$truckNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					if($productNameJsonDecode[$rowCount] == 'ULSR'){
						$itemCode = 2;
						if(!empty($cardCountRows->unit_number)){
							$trailerNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					if($productNameJsonDecode[$rowCount] == 'DEFD'){
						$itemCode = 3;
						if(!empty($cardCountRows->unit_number)){
							$truckNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					$Receipt_Number = "CL".$invoiceID;
					/* $csvdata .= '""""'.$Receipt_Number.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'",""""'.$cardCountRows->gas_station_state .'",'.$itemCode.",".$productQuantityJsonDecode[$rowCount].',""""L",'.$pridePriceJsonDecode[$rowCount].",".$totalTaxAmount." ,".$total."\n"; */
					/* $csvdata .= '""""'.$Receipt_Number.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'","'.$driverName.'","'.$cardCountRows->transaction_id.'","'.$cardCountRows->gas_station_name.'","'.$cardCountRows->gas_station_id.'",""""'.$cardCountRows->gas_station_state .'","'.$transactionDate.'","'.$transactionTime.'","'.$cardCountRows->unit_number.'",'.$productNameJsonDecode[$rowCount].",".$productQuantityJsonDecode[$rowCount].',""""L",'.$pridePriceJsonDecode[$rowCount].",".$finalTaxAmt." ,".$total.",CAD\n"; */
					if(strpos(date("G:i a", strtotime($cardCountRows->transaction_date)), 'pm') !== false){
						$transtime = date("h:i", strtotime($cardCountRows->transaction_date))." p";
					}else{
						$transtime = date("h:i", strtotime($cardCountRows->transaction_date))." a";
					}					
					$toalAfterFloor = floor($total*100)/100;
					/* $csvdata[] = array('"'.$cardCountRows->transaction_id.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).",".$transtime.',"Credit", Pride Diesel Inc.'.',"'.$cardCountRows->card_number .'","'.$cardCountRows->gas_station_name.'","'.$cardCountRows->gas_station_city.'","'.$cardCountRows->gas_station_state .'",'.$itemCode.",".$productQuantityJsonDecode[$rowCount].',"L",'.$pridePriceJsonDecode[$rowCount]." ,".$finalTaxAmt." ,".$toalAfterFloor."\n"); */
					$csvdata .= '"""'.$cardCountRows->transaction_id.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).",".$transtime.',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'",""""'.$cardCountRows->gas_station_name.'",""""'.$cardCountRows->gas_station_city.'",""""'.$cardCountRows->gas_station_state .'",'.$truckNumber.','.$trailerNumber.','.$productNameJsonDecode[$rowCount].','.$productQuantityJsonDecode[$rowCount].',""""L",'.$pridePriceJsonDecode[$rowCount]." ,".$total_tax_amount.",".$toalAfterFloor."\n";					
					/*Dat file fields*/
					$datContent .= '"'.$Receipt_Number.'"';
					$datContent .= date('m/d/Y', strtotime($cardCountRows->transaction_date));
					$datContent .= '"Credit"';
					$datContent .= 'PRIDE DIESEL INC.';
					$datContent .= '"'.$cardCountRows->card_number .'"';
					$datContent .= '"'.$cardCountRows->gas_station_state .'"';
					$datContent .= $itemCode;
					$datContent .= $productQuantityJsonDecode[$rowCount];
					$datContent .= '"L"';
					$datContent .= $pridePriceJsonDecode[$rowCount];
					$datContent .= $total_tax_amount;
					$datContent .= $total."\n";
					/*fields Ended*/					
		
					$rowCount++;$jsonInc++;
				}
				/* Set Invoice_status as 1 */
				$this->db->where('transaction_id', $cardCountRows->transaction_id);
				$this->db->set('invoice_status', 1);
				$this->db->update('transactions'); 				
				$transactionDetails = json_encode($transationDetails);
				$invoice_data = json_encode($arr);
			}
			//$grandFinalTotal = $grandTaxAmt + $grandQSTTaxAmt;
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
			<td>$ '.$grandTaxAmt.'</td>
			<td>$ '.$grandQSTTaxAmt.'</td>
			<td>$ '.$grandTotal.'</td>
			<td>CAD</td>
		</tr>
	</tbody>
	</table>';
			/*Dat File create*/
			/* $file = fopen(FCPATH ."assets/modules/invoices/PRIDEDIESEL_110_".$invoiceID."_CAD_".date('Ymd').".dat","w");
			//fwrite($file,"$a\n$b$c\n$e$d$f$k$g$l$h$m\n$n$o$p$q$r$s$t\n$u$v");
			fwrite($file,"$datContent");
			fclose($file); */	
			/*CSV for TransPlus Software*/
			$fd = fopen (FCPATH."assets/modules/invoices/".$filename, "w");
			/* $csvheader ="Receipt_Number,Transaction_Date,Method,Vendor,Card_Number,Province_State,Item_Type,Quantity,Measure,Price,Tax,Total\n"; */
			$csvheader ="Receipt_Number,Transaction_Date,Transaction_Time,Method,Vendor,Card_Number,Supplier,City,Province_State,Truck_Number,Trailer_Number,Item_Type,Quantity,Measure,Price,Tax,Total\n";
			//header('Content-Type: application/csv');
			//header('Content-Disposition: attachment; filename="'.$filename.'"');
			$fileContent = $csvheader.$csvdata;
			fputs($fd, $fileContent);
			/* fputcsv($fd, $csvheader);
			foreach ($csvdata as $line) {
				// though CSV stands for "comma separated value"
				// in many countries (including France) separator is ";"
				fputcsv($fd, $line, ',');
			} */			
			fclose($fd);
			//echo $csvheader.$csvdata; 
			//exit();

		//Insert invoice data in transaction invoice table			
		$data['invoice_id'] = "CL".$invoiceID;
		$data['company_id'] = $cid;
		$data['invoice_date'] = date('Y-m-d');
		$data['billingOn'] = 'EFS';
		$data['billingCurrency'] = 'CAD';
		$data['invoice_data'] = $invoice_data;
		$data['trans_data'] = $transactionDetails;
		$data['status'] = 0;	
		$data['grand_total'] = $grandTotal;
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_modified'] = date('Y-m-d H:i:s');
		$this->db->insert('transaction_invoice', $data);
		//pre($content);die;
		$obj_pdf->writeHTML($content);	

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
		$csvFilePath = FCPATH . 'assets/modules/invoices/company_trans_plus_CAD_'.$cid."_".date('Ymd').'.csv';
		//$this->email->clear();
		$result = $this->email
					->from('info@pridediesel.com', 'From Pride Diesel')
					//->to('jagdishchander6373@gmail.com')
					->to($company_email_ca)
					->bcc('abhinavdua1435@gmail.com')
					//->cc('jagdishchander4667@gmail.com')
					->subject($ca_subject)
					->message($ca_body)
					->attach($pdfFilePath)
					->attach($csvFilePath)
					->send();
		$this->email->clear($pdfFilePath);
		$this->email->clear($csvFilePath);

		//if($result) {
			//echo "Send";
			//unlink($pdfFilePath); //for delete generated pdf file. 
		//}	
	}

	public function generateCanadianInvoiceHusky($cid,$daterange){
		/* CAD Transaction Invoice Generate Code Husky */
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
		$obj_pdf->SetFont('helvetica', '', 12);
		
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
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0, 'transactions.transactionAt'=> 'HUSKY'));
		if(!empty($daterange)){
			$expDateRange = explode(' - ', $daterange);
			$startDate = $expDateRange[0];
			$endDate = $expDateRange[1];			
			$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
		}			
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
							
							Invoice #<br />
							Invoice Date<br />
							Period beginning<br />
							Period ending<br />
							</td>
							<td>
							'.$companyDetails->id .'<br />
							<strong>'.$companyDetails->company_name .'</strong><br />
							'.$companyDetails->address .'<br />
							
							CL'.$invoiceID.'<br />
							'.date('d/m/Y').'<br />
							'.date('d/m/Y', $periodBeg).'<br />
							'.date('d/m/Y', $periodEnd).'<br />
							Due on Receipt
							</td>
						</tr>
					</table>
			   </td>
		       <td class="pride">Page-'.$obj_pdf->getPage().'<br />Confidential information</td>
		  </tr>
         
		</tbody>
		</table><div class="header-section-bot"></div><br />';
			$company_email_ca = $companyDetails->company_email;
		
		$content .='<table class="custom-css" style="border: 1px solid #D9D9D9 ; clear: both; border-spacing: 0; border-collapse: collapse;" cellpadding="3">
		<tbody>		
           <tr>
		      <th style="width:12%;">Card #</th>
		      <th style="width:7%;">Driver name</th>
			  <th style="width:7%;">Transaction #</th>
			  <th style="width:7%;">Site name</th>
			  <th style="width:4%;">Site #</th>
			  <th style="width:3%;">City</th>
			  <th style="width:3%;">Province</th>
			  <th style="width:6%;">Date</th>
			  <th style="width:5%;">Time</th>
			  <th style="width:4%;">Unit #</th>
			  <th style="width:5%;">Product</th>
			  <th style="width:3%;">UOM</th>
			  <th style="width:7%;">Price per unit</th>
			  <th style="width:7%;">Total quantity</th>
			  <th style="width:6%;">HST/GST</th>
			  <th style="width:4%;">QST</th>
			  <th style="width:5%;">Total</th>
			  <th style="width:5%;">Currency</th>
           </tr>';
			$this->db->select('users.*, transactions.*, cards.driver_id, transactions.id as transactionid');
			$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
			$this->db->join('users', 'users.id = cards.company_id');
			//$this->db->join('drivers', 'drivers.company_id = cards.company_id', 'LEFT');
			//$this->db->join('transactions', 'transactions.card_number = cards.card_number');
			$this->db->where(array('cards.company_id'=> $cid, 'transactions.billing_currency'=> 'CAD', 'transactions.invoice_status'=>0, 'transactions.transactionAt'=> 'HUSKY'));
			if(!empty($daterange)){
				$expDateRange = explode(' - ', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}			
			//$this->db->group_by('transactions.card_number');
			//$CAcardCount = $this->db->get('cards')->result();
			$CAcardCount = $this->db->get('transactions')->result();
			//pre($CAcardCount);die;
			$totalQuantity = 0; $grandTotal = 0; $grandTax=0;
			$jsonInc = 0; $grandQSTTaxAmt = $grandTaxAmt = 0;
			
			$transactionCount = 0;
			$csvdata = "";
			foreach($CAcardCount as $cardCountRows){
				$productNameJsonDecode = json_decode($cardCountRows->category);
				$pridePriceJsonDecode = json_decode($cardCountRows->pride_price);
				$EFSpriceJsonDecode = json_decode($cardCountRows->unit_price);
				$productQuantityJsonDecode = json_decode($cardCountRows->quantity);
				$explodeDateTime = explode(' ', $cardCountRows->transaction_date);	
				$transactionDate = $explodeDateTime[0];		
				$transactionTime = $explodeDateTime[1];
				$driverid = $cardCountRows->driver_id;//pre($cardCountRows->driver_id);die;
				$driverName = "-";
				if(!empty($driverid)){
					$getDriverName = $this->db->select('name')->where('id', $driverid)->get('drivers')->row();
					$driverName = $getDriverName->name;
				}				
				
				$rowCount=0; $finalGST=0; $finalPST=0; $finalQST=0; $finalTaxAmt=0; 
				foreach($productNameJsonDecode as $productCountJsonDecodeRows){
					$calcProduct = $productQuantityJsonDecode[$rowCount] * $pridePriceJsonDecode[$rowCount];
					$total = floor($calcProduct*100)/100;
					$transactionCount++;
					/* Product Taxes Start */
				/* GST/PST/QST Start */						
				$taxapplicable = $this->db->where('product_name', $productNameJsonDecode[$rowCount])->get('products')->row();

				if(!empty($taxapplicable->tax) ){
				$taxArray = json_decode($taxapplicable->tax);
					foreach($taxArray as $key=>$taxArrayRow){	
					$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>$cardCountRows->gas_station_state))->get('tax')->row();

					//GST/HST/FNT || PST/QST
					if(!empty($taxOutput['gst']->tax_rate)){$gst = str_replace('%', '', $taxOutput['gst']->tax_rate);}else{$gst = 0;}
					if(!empty($taxOutput['pst']->tax_rate)){$pst = str_replace('%', '', $taxOutput['pst']->tax_rate);}else{$pst = 0;}
					if(!empty($taxOutput['qst']->tax_rate)){$qst = str_replace('%', '', $taxOutput['qst']->tax_rate);}else{$qst = 0;}			
					}
					
				}
				
				if(empty($gst)){$gst = 0;}
				if(empty($pst)){$pst = 0;}
				if(empty($qst)){$qst = 0;}
				//$totalTax = $gst + $pst + $qst;
				$totalTax = $gst + $pst;
				if(strlen($totalTax) < 2){
					$revTotalTaxAmt = '1.0'.$totalTax;
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;					
				}elseif(strpos($totalTax, '.')!==false){
					$revTotalTaxAmt = '1.'.str_replace(".","",$totalTax);
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;
				}else{
					$revTotalTaxAmt = '1.'.$totalTax;
					$amtAfterReversal = floatval($total) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $total - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;
				}
				########## QST Calculations
				$totalTaxQST = $qst;
				
				if(strlen($totalTaxQST) < 2){
					$revTotalQSTAmt = '1.0'.$totalTaxQST;
					$amtAfterReversalQST = floatval($total) / floatval($revTotalQSTAmt);
					$minuspriceandQSTtax = $total - $amtAfterReversalQST;
					$finalQSTTaxAmt =  floor($minuspriceandQSTtax*100)/100;					
				}elseif(strpos($totalTaxQST, '.')!==false){
					$revTotalQSTAmt = '1.0'.str_replace(".","",$totalTaxQST);
					$amtAfterReversalQST = floatval($total) / floatval($revTotalQSTAmt);
					$minuspriceandQSTtax = $total - $amtAfterReversalQST;
					$finalQSTTaxAmt =  floor($minuspriceandQSTtax*100)/100;
				}else{
					$revTotalQSTAmt = '1.'.$totalTaxQST;
					$amtAfterReversalQST = floatval($total) / floatval($revTotalQSTAmt);
					$minuspriceandQSTtax = $total - $amtAfterReversalQST;
					$finalQSTTaxAmt =  floor($minuspriceandQSTtax*100)/100;
				}				
	   
					$content .='<tr>
						<td style="width:12%;">'.$cardCountRows->card_number .'</td>
						<td style="width:7%;">'.$driverName.'</td>
						<td style="width:7%;">'.$cardCountRows->transaction_id .'</td>
						<td style="width:7%;">'.$cardCountRows->gas_station_name .'</td>
						<td style="width:4%;">'.$cardCountRows->gas_station_id .'</td>
						<td style="width:3%;">'.$cardCountRows->gas_station_city .'</td>
						<td style="width:3%;">'.$cardCountRows->gas_station_state .'</td>
						<td style="width:6%;">'.$transactionDate.'</td>
						<td style="width:5%;">'.$transactionTime.'</td>
						<td style="width:4%;">'.$cardCountRows->unit_number .'</td>
						<td style="width:5%;">'.$productNameJsonDecode[$rowCount].'</td>
						<td style="width:3%;">L</td>
						<td style="width:7%;">$ '.$pridePriceJsonDecode[$rowCount].'</td>
						<td style="width:7%;">'.$productQuantityJsonDecode[$rowCount].'</td>
						<td style="width:6%;">$ '.$finalTaxAmt.'</td>
						<td style="width:4%;">$ '.$finalQSTTaxAmt.'</td>
						<td style="width:5%;">$ '.$total.'</td>
						<td style="width:5%;">'.$cardCountRows->billing_currency .'</td>
					</tr>';
					$totalQuantity += $productQuantityJsonDecode[$rowCount];
					
					$grandTaxAmt += $finalTaxAmt;
					$grandQSTTaxAmt += $finalQSTTaxAmt;
					$grandTotal += floor($total*100)/100;
					/*trans_data field*/
					$CAtransJsonArrayObject = (array($cardCountRows->card_number => array('product_name'=>$productNameJsonDecode[$rowCount], 'quantity'=>$productQuantityJsonDecode[$rowCount], 'unit_price'=>$pridePriceJsonDecode[$rowCount], 'taxamount'=>$finalTaxAmt, 'qstTax'=>$grandQSTTaxAmt, 'amountwithouttax' => $total)));
					$transationDetails[$jsonInc] = $CAtransJsonArrayObject;
					/*invoice_data field*/
					$CAjsonArrayObject = (array('card_number' =>$cardCountRows->card_number,'transaction_date' => $cardCountRows->transaction_date, 'transaction_id' => $cardCountRows->transaction_id));
					$arr[$jsonInc] = $CAjsonArrayObject;
					$total_tax_amount = $finalTaxAmt + $finalQSTTaxAmt;
					
					/* CSV for TransPlus Software*/
					$filename = 'company_trans_husky_CAD_'.$cid."_".date('Ymd').'.csv';
					$truckNumber = null; $trailerNumber = null;
					if($productNameJsonDecode[$rowCount] == 'ULSD'){
						$itemCode = 1;
						if(!empty($cardCountRows->unit_number)){
							$truckNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					if($productNameJsonDecode[$rowCount] == 'ULSR'){
						$itemCode = 2;
						if(!empty($cardCountRows->unit_number)){
							$trailerNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					if($productNameJsonDecode[$rowCount] == 'DEFD'){
						$itemCode = 3;
						if(!empty($cardCountRows->unit_number)){
							$truckNumber = '""""'.$cardCountRows->unit_number.'"';
						}
					}
					$Receipt_Number = "CL".$invoiceID;
					/* $csvdata .= '""""'.$Receipt_Number.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'",""""'.$cardCountRows->gas_station_state .'",'.$itemCode.",".$productQuantityJsonDecode[$rowCount].',""""L",'.$pridePriceJsonDecode[$rowCount].",".$totalTaxAmount." ,".$total."\n"; */
					/* $csvdata .= '""""'.$Receipt_Number.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'","'.$driverName.'","'.$cardCountRows->transaction_id.'","'.$cardCountRows->gas_station_name.'","'.$cardCountRows->gas_station_id.'",""""'.$cardCountRows->gas_station_state .'","'.$transactionDate.'","'.$transactionTime.'","'.$cardCountRows->unit_number.'",'.$productNameJsonDecode[$rowCount].",".$productQuantityJsonDecode[$rowCount].',""""L",'.$pridePriceJsonDecode[$rowCount].",".$finalTaxAmt." ,".$total.",CAD\n"; */
					if(strpos(date("G:i a", strtotime($cardCountRows->transaction_date)), 'pm') !== false){
						$transtime = date("h:i", strtotime($cardCountRows->transaction_date))." p";
					}else{
						$transtime = date("h:i", strtotime($cardCountRows->transaction_date))." a";
					}					
					$toalAfterFloor = floor($total*100)/100;
					/* $csvdata[] = array('"'.$cardCountRows->transaction_id.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).",".$transtime.',"Credit", Pride Diesel Inc.'.',"'.$cardCountRows->card_number .'","'.$cardCountRows->gas_station_name.'","'.$cardCountRows->gas_station_city.'","'.$cardCountRows->gas_station_state .'",'.$itemCode.",".$productQuantityJsonDecode[$rowCount].',"L",'.$pridePriceJsonDecode[$rowCount]." ,".$finalTaxAmt." ,".$toalAfterFloor."\n"); */
					$csvdata .= '"""'.$cardCountRows->transaction_id.'"'.','.date('m/d/Y', strtotime($cardCountRows->transaction_date)).",".$transtime.',""""Credit", Pride Diesel Inc.'.',""""'.$cardCountRows->card_number .'",""""'.$cardCountRows->gas_station_name.'",""""'.$cardCountRows->gas_station_city.'",""""'.$cardCountRows->gas_station_state .'",'.$truckNumber.','.$trailerNumber.','.$productNameJsonDecode[$rowCount].','.$productQuantityJsonDecode[$rowCount].',""""L",'.$pridePriceJsonDecode[$rowCount]." ,".$total_tax_amount.",".$toalAfterFloor."\n";					
		
					$rowCount++;$jsonInc++;
				}
				/* Set Invoice_status as 1 */
				$this->db->where(['id' => $cardCountRows->transactionid, 'transaction_id' => $cardCountRows->transaction_id]);
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
			<td>$ '.$grandTaxAmt.'</td>
			<td>$ '.$grandQSTTaxAmt.'</td>
			<td>$ '.$grandTotal.'</td>
			<td>CAD</td>
		</tr>
	</tbody>
	</table>';
			/*Dat File create*/
			/* $file = fopen(FCPATH ."assets/modules/invoices/PRIDEDIESEL_110_".$invoiceID."_CAD_".date('Ymd').".dat","w");
			//fwrite($file,"$a\n$b$c\n$e$d$f$k$g$l$h$m\n$n$o$p$q$r$s$t\n$u$v");
			fwrite($file,"$datContent");
			fclose($file); */	
			/*CSV for TransPlus Software*/
			$fd = fopen (FCPATH."assets/modules/invoices/".$filename, "w");
			/* $csvheader ="Receipt_Number,Transaction_Date,Method,Vendor,Card_Number,Province_State,Item_Type,Quantity,Measure,Price,Tax,Total\n"; */
			$csvheader ="Receipt_Number,Transaction_Date,Transaction_Time,Method,Vendor,Card_Number,Supplier,City,Province_State,Truck_Number,Trailer_Number,Item_Type,Quantity,Measure,Price,Tax,Total\n";
			//header('Content-Type: application/csv');
			//header('Content-Disposition: attachment; filename="'.$filename.'"');
			$fileContent = $csvheader.$csvdata;
			fputs($fd, $fileContent);
			/* fputcsv($fd, $csvheader);
			foreach ($csvdata as $line) {
				// though CSV stands for "comma separated value"
				// in many countries (including France) separator is ";"
				fputcsv($fd, $line, ',');
			} */			
			fclose($fd);
			//echo $csvheader.$csvdata; 
			//exit();

		//Insert invoice data in transaction invoice table			
		$data['invoice_id'] = "CL".$invoiceID;
		$data['company_id'] = $cid;
		$data['invoice_date'] = date('Y-m-d');
		$data['billingOn'] = 'HUSKY';
		$data['billingCurrency'] = 'CAD';
		$data['invoice_data'] = $invoice_data;
		$data['trans_data'] = $transactionDetails;
		$data['status'] = 0;	
		$data['grand_total'] = $grandTotal;
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_modified'] = date('Y-m-d H:i:s');
		$this->db->insert('transaction_invoice', $data);
		//pre($content);die;
		$obj_pdf->writeHTML($content);	

		ob_end_clean();
		//if (ob_get_contents()) ob_end_clean();	

		//$obj_pdf->Output('sample.pdf', 'I'); die;		
		//$obj_pdf->Output(APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
		$obj_pdf->Output(FCPATH . 'assets/modules/invoices/trans_invoice_CAD_husky_'.$data['invoice_date']."_".$data['company_id'].'.pdf', 'F');
		
		//Send generated invoice to company and then delete pdf
		$this->load->library('email');
		$ca_subject = 'CAD Transactions Invoice Husky';
		$ca_body = 'Currency CAD';
		//$pdfFilePath = APPPATH . 'modules/cronjob/invoice_pdf/trans_invoice_CAD_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
		$pdfFilePath = FCPATH . 'assets/modules/invoices/trans_invoice_CAD_husky_'.$data['invoice_date']."_".$data['company_id'].'.pdf';
		$csvFilePath = FCPATH . 'assets/modules/invoices/company_trans_husky_CAD_'.$cid."_".date('Ymd').'.csv';
		//$this->email->clear();
		$result = $this->email
					->from('info@pridediesel.com', 'From Pride Diesel')
					//->to('jagdishchander6373@gmail.com')
					->to($company_email_ca)
					->bcc('abhinavdua1435@gmail.com')
					//->cc('jagdishchander4667@gmail.com')
					->subject($ca_subject)
					->message($ca_body)
					->attach($pdfFilePath)
					->attach($csvFilePath)
					->send();
		$this->email->clear($pdfFilePath);
		$this->email->clear($csvFilePath);

		//if($result) {
			//echo "Send";
			//unlink($pdfFilePath); //for delete generated pdf file. 
		//}	
	}

/*********************CAD Rebate Calc ******************/
	public function cad_rebate_per_invoice(){
		$this->load->library('pagination');
		//$this->data['can_edit'] = edit_permissions();
		//$this->data['can_delete'] = delete_permissions();
		//$this->data['can_add'] = add_permissions();
		$this->breadcrumb->add('CAD Rebate Cost and Profit', base_url() . 'CAD Rebate Cost and Profit');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		$this->settings['pageTitle'] = 'CAD Rebate Cost and Profit';
			
			$where = '';
			$where2 = '';
			if(!empty($_GET['card_search'])){
				$where = $_GET['card_search'];
			}
			if(!empty($_GET['date_range'])){
				$explodeDateRange = explode(' - ', $_GET['date_range']);			
				$where2 = $explodeDateRange;
			}		

			$this->data['cad_rebate'] = $this->account_model->get_rebate_transactions($where, $where2);
			
			$config['base_url'] = site_url('account/cad_rebate_per_invoice');
			$config['uri_segment'] = 3;
			$config['total_rows'] = count($this->data['cad_rebate']);
		   // $config['total_rows'] = 10;
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

			$this->data['cad_rebate'] = $this->account_model->get_cad_rebate_transactions($config['per_page'], $page, $where, $where2);		
			
			//$this->_render_template('cadRebate_calc/Cad_rebateCalcs', $this->data);
		}

	public function cad_rebate_calc(){
		/* $this->data['can_edit'] = edit_permissions();
		$this->data['can_delete'] = delete_permissions();
		$this->data['can_add'] = add_permissions(); */
		$this->settings['title'] = 'Invoices';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Invoices', base_url() . 'account/cad_rebate_calc');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->load->library('pagination');

		$where = '';
		$where2 = '';
		if(!empty($_GET['card_search'])){
			$where = $_GET['card_search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}		

		$this->data['allInvoices'] = $this->account_model->get_trans_invoices_rebate($where, $where2);
		
		
		
        /* Pagination */
        $config['base_url'] = site_url('account/cad_rebate_calc');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_pagination_trans_invoice_rebate($config['per_page'], $page, $where, $where2);		
        $this->data['allInvoices_rebate_calc'] = $this->account_model->get_pagination_trans_invoice_rebate_calculation($config['per_page'], $page,$where, $where2);		
		
		$this->_render_template('cadRebate_calc/index', $this->data);		
	}
	
	
	
	
	public function view_invoice(){
		$this->settings['title'] = 'Invoice Detail';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Invoices', base_url() . 'account/cad_rebate_calc');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->load->library('pagination');

		$where = '';
		$where2 = '';
		if(!empty($_GET['card_search'])){
			$where = $_GET['card_search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}

		$invoice_id = $this->uri->segment(3); 	

		$this->data['allInvoices'] = $this->account_model->get_trans_invoices_rebatePer($where, $where2,$invoice_id);
		
		
		
        /* Pagination */
        $config['base_url'] = site_url('account/cad_rebate_calc');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_pagination_trans_invoice_rebatePer($config['per_page'], $page, $where, $where2,$invoice_id);		
        $this->data['allInvoices_rebate_calc'] = $this->account_model->get_pagination_trans_invoice_rebate_calculationPer($where, $where2,$invoice_id);		
		
		$this->_render_template('cadRebate_calc/view', $this->data);
		
	}
	
/*********************CAD Rebate Calc ******************/
/*********************USA Rebate Calc ******************/
public function usa_rebate_calc(){
		$this->settings['title'] = 'Invoices';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Invoices', base_url() . 'account/usa_rebate_calc');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->load->library('pagination');

		$where = '';
		$where2 = '';
		if(!empty($_GET['card_search'])){
			$where = $_GET['card_search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}		
		$invoice_id = 0;
		$this->data['allInvoices'] = $this->account_model->get_trans_invoices_rebate_USA($where, $where2,$invoice_id);
		
		
		
        /* Pagination */
        $config['base_url'] = site_url('account/usa_rebate_calc');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_pagination_trans_invoice_rebate_USA($config['per_page'], $page, $where, $where2,$invoice_id);		
        $this->data['allInvoices_rebate_calc'] = $this->account_model->get_pagination_trans_invoice_rebate_calculation_USA($config['per_page'], $page,$where, $where2,$invoice_id);		
		
		$this->_render_template('cadRebate_calc/index_usa', $this->data);		
	}
	
	
	public function view_invoices(){
		$this->settings['title'] = 'USA Invoice Detail';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Invoices', base_url() . 'account/usa_rebate_calc');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->load->library('pagination');

		$where = '';
		$where2 = '';
		if(!empty($_GET['card_search'])){
			$where = $_GET['card_search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}

		$invoice_id = $this->uri->segment(3); 	

		$this->data['allInvoices'] = $this->account_model->get_trans_invoices_rebate_USA($where, $where2,$invoice_id);
		
		
		
        /* Pagination */
        $config['base_url'] = site_url('account/usa_rebate_calc');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_pagination_trans_invoice_rebate_USA($config['per_page'], $page, $where, $where2,$invoice_id);		
        $this->data['allInvoices_rebate_calc'] = $this->account_model->get_pagination_trans_invoice_rebate_calculation_USA($config['per_page'], $page,$where, $where2,$invoice_id);		
		
		$this->_render_template('cadRebate_calc/view_usa', $this->data);
		
	}

/*********************USA Rebate Calc ******************/
/*********************Husky Rebate Calc ******************/


public function husky_rebate(){
		$this->settings['title'] = 'HUSKY Invoices';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Invoices', base_url() . 'account/husky_rebate');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->load->library('pagination');

		$where = '';
		$where2 = '';
		if(!empty($_GET['card_search'])){
			$where = $_GET['card_search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}		
		$invoice_id = 0;
		$this->data['allInvoices'] = $this->account_model->get_trans_invoices_rebate_husky($where, $where2,$invoice_id);
		
		
		
        /* Pagination */
        $config['base_url'] = site_url('account/husky_rebate');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_pagination_trans_invoice_rebate_husky($config['per_page'], $page, $where, $where2,$invoice_id);		
        $this->data['allInvoices_rebate_calc'] = $this->account_model->get_pagination_trans_invoice_rebate_calculation_husky($config['per_page'], $page,$where, $where2,$invoice_id);		
		
		$this->_render_template('cadRebate_calc/index_cad_husky', $this->data);		
	}
	
	public function husky_rebate_per_invoice(){
		$this->settings['title'] = 'CAD HUSKY Invoices';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Invoices', base_url() . 'account/husky_rebate');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->load->library('pagination');

		$where = '';
		$where2 = '';
		if(!empty($_GET['card_search'])){
			$where = $_GET['card_search'];
		}
		if(!empty($_GET['date_range'])){
			$explodeDateRange = explode(' - ', $_GET['date_range']);			
			$where2 = $explodeDateRange;
		}		
		$invoice_id = $this->uri->segment(3);
		$this->data['allInvoices'] = $this->account_model->get_trans_invoices_rebate_husky($where, $where2,$invoice_id);
		
		/* Pagination */
        $config['base_url'] = site_url('account/husky_rebate');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allInvoices']);
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

        $this->data['allInvoices'] = $this->account_model->get_pagination_trans_invoice_rebate_husky($config['per_page'], $page, $where, $where2,$invoice_id);		
        $this->data['allInvoices_rebate_calc'] = $this->account_model->get_pagination_trans_invoice_rebate_calculation_husky($config['per_page'], $page,$where, $where2,$invoice_id);		
		
		$this->_render_template('cadRebate_calc/view_cad_husky', $this->data);		
	}
/*********************Husky Rebate Calc ******************/	
}