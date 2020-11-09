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
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
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
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
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
	
	public function update_invoice_status($invoiceid){
		$this->account_model->set_invoice_paid_status($invoiceid);
		redirect(base_url('account/invoice'), 'refresh');
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

		//Get all data of company
		$where = '';
		$where2 = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
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

	public function exportTransactionByCompany($cid){
		$this->load->model('user/user_model');
		//File Name
		$fileName = 'user-data-'.time().'.csv'; //format should be .xlsx , .csv

		$this->load->library('excel');
		$empInfo = $this->account_model->exportTransByComp($cid);
		//pre($this->db->last_query());die;
		$dailyPriceList = $this->user_model->get_dailypricelist();
		
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Billing Currency');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Card Number');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Invoice');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Amount');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Category');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Unit Price');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'GST/PST/QST');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Gas Station Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Gas Station City');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Gas Station State');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Transaction Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Date Created');


        // set Row
        $rowCount = 2;
        foreach ($empInfo as $element) {
			
			/* $this->db->join('company_types', 'company_types.id=users.company_type');
			$this->db->where('users.id', $cid);
			$get_user_type = $this->db->get('users')->row();
			$companyType = strtolower($get_user_type->company_type);
			$pricingTypeUS = $get_user_type->usa_pricing;
			$pricingTypeCA = $get_user_type->cad_pricing; */
			//$decCategory = json_decode($element['category']);
			$decUnit_price = json_decode($element['unit_price']);
			$decPride_price = json_decode($element['pride_price']);
			//$decQuantity = json_decode($element['quantity']);
			$productNameJsonDecode = json_decode($element['category']);
			
			$productQuantityJsonDecode = json_decode($element['quantity']);
			//$productName = $decCategory[0];			
			
			
			$totalTaxAmount = 0;
			for($cnt=0; $cnt<count($productNameJsonDecode); $cnt++){
			//pre($productNameJsonDecode[$cnt]);
			$amoutQtyTotal = $decPride_price[$cnt] * $productQuantityJsonDecode[$cnt];
			$grandTotal = floor($amoutQtyTotal*100)/100;				
			if($element['billing_currency'] == 'CAD'){
				$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $element['gas_station_state'])->get('tax')->result();
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
	
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element['billing_currency']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount, $element['card_number'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element['invoice']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $grandTotal);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $productNameJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $decPride_price[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $productQuantityJsonDecode[$cnt]);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $totalTaxAmount);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $element['gas_station_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $element['gas_station_city']);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $element['gas_station_state']);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $element['transaction_date']);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $element['date_created']);

            $rowCount++;
			}
        }
//die;
       $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 

			$object_writer = new PHPExcel_Writer_Excel2007($objPHPExcel);//pre($object_writer);die;
			        header('Content-Type: application/vnd.ms-excel');
			       header("Content-Disposition: attachment;filename=Transactions_".date('Ymd').".xlsx");
			         ob_end_clean();
			        $object_writer->save('php://output');		
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
	
	public function transaction_view_by_cid($cid=NULL, $daterange=null){
		$this->settings['title'] = 'View Transaction';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transaction', base_url() . 'account/transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		if(!empty($_GET['date_range'])){
			$daterange = $_GET['date_range'];
		}
		$this->data['cardsTransData'] = $this->account_model->get_card_trans_by_cid($cid, $daterange);
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

	public function card_transactions($cardNumber=NULL, $daterange=null){
		$this->settings['title'] = 'View Transactions';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transactions', base_url() . 'account/card_transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		//$this->data['cardDetails'] = $this->account_model->get_card_transactions($cardNumber, $daterange);
		$this->data['cardsTransData'] = $this->account_model->get_card_transactions($cardNumber, $daterange);
		//pre($this->db->last_query());
		$this->data['driverDetails'] = $this->account_model->get_card_driver($cardNumber);
		//die;
		$this->_render_template('transaction/card_trans_view', $this->data);		
	}
	
	public function comp_card_transactions($cardNumber=NULL, $daterange=null){
		$this->load->model('user/user_model');
		$this->settings['title'] = 'View Transactions';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('View Transactions', base_url() . 'account/card_transactions');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();

		$this->data['cardDetails'] = $this->account_model->get_card_transactions($cardNumber, $daterange);
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
		if(!empty($this->input->post('daterange'))){
			$daterange = $this->input->post('daterange');
		}else{
			$daterange = null;
		}		
		$count=0;
		$vv = 0;
		$this->data['cardsTransData'] = $this->account_model->get_card_trans_by_cid($cid,$daterange);
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
			$this->generateUSInvoice($cid,$daterange);
		}
		if(!empty($cadTrans)){
			//$this->generateCanadianInvoice($cid,$daterange);
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
			if(!empty($daterange)){
				$expDateRange = explode(' - ', $daterange);
				$startDate = $expDateRange[0];
				$endDate = $expDateRange[1];			
				$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
			}			
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
			$data['date_created'] = date('Y-m-d H:i:s');
			$data['date_modified'] = date('Y-m-d H:i:s');
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
						->cc('info@pridediesel.com')
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
	
	public function generateCanadianInvoice($cid,$daterange){
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
		if(!empty($daterange)){
			$expDateRange = explode(' - ', $daterange);
			$startDate = $expDateRange[0];
			$endDate = $expDateRange[1];			
			$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d H:i:s', strtotime($startDate)). '" and "'. date('Y-m-d H:i:s', strtotime($endDate)).'"');
		}			
			//$this->db->group_by('transactions.card_number');
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
							$mergeOneGst = '1.'.$gstRate;
							$reverseGst = $total / $mergeOneGst;
							//$finalGST = $total * $gstRate / 100;
							$finalGST = $total - $reverseGst;
						}
						if($taxTypeRows->tax_type == 'pst'){
							$pstRate = str_replace('%', '', $taxTypeRows->tax_rate);
							$mergeOnePst = '1.'.$pstRate;
							$reversePst = $total / $mergeOnePst;
							//$finalPST = $total * $pstRate / 100;
							$finalPST = $total - $reversePst;
						}
						if($taxTypeRows->tax_type == 'qst'){
							$qstRate = str_replace('%', '', $taxTypeRows->tax_rate);
							$mergeOneQst = '1.'.$qstRate;
							$reverseQst = $total / $mergeOneQst;
							//$finalQST = $total * $qstRate / 100;
							$finalQST = $total - $reverseQst;
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
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['date_modified'] = date('Y-m-d H:i:s');
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
					->cc('info@pridediesel.com')
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