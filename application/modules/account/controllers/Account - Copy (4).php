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
		
		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		
		$this->data['allLedger'] = $this->account_model->get_ledgers($where);
        // pagination
        $config['base_url'] = site_url('account/ledgers');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allLedger']);
        $config['per_page'] = 3;
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
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		

		$this->data['transactionData'] = $this->account_model->get_transactions($where);
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

        $this->data['transactionData'] = $this->account_model->get_pagination_transactions($config['per_page'], $page, $where);		
		
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

	public function generate_trans_invoice($cid){
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
		
/* 		foreach($pricelistDecoded as $key=>$pricelistDecodedRow){
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
//pre();die;
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
			/* $this->db->where('id', $usdCardRows->transid);
			$this->db->set('invoice_status', 1);
			$this->db->update('transactions'); */   		

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
		
/* 		foreach($pricelistDecoded as $key=>$pricelistDecodedRow){
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
//pre();die;
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
		  //$this->db->insert('transaction_invoice', $data);
			$obj_pdf->writeHTML($content);
	ob_end_clean();

    $obj_pdf->Output('sample.pdf', 'I'); die;		
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
	echo"<script>alert('Invoice generated and sent.')</script>";	
		redirect(base_url().'account/ledgers', 'refresh');
		/* $this->load->library('Pdf');
		$dataPdf = $this->user_model->get_data_byId('users','id',$id);
		print_r($dataPdf);die;
		create_pdf($dataPdf,'modules/user/views/view_user_pdf.php');
		$this->load->view('sale_orders/view_saleOrder_pdf'); */
	}
	
}