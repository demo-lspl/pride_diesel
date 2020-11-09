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
	
	public function invoice_view($id){
		$this->settings['title'] = 'View Invoice';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('Edit Invoice', base_url() . 'account/invoice_view');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['fetchInvoice'] = $this->account_model->get_trans_invoice_by_id($id);
		
		
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

	public function company_transactions($cid=NULL){
		$this->settings['title'] = 'All Transaction';
		$this->breadcrumb->mainctrl("account");
		$this->breadcrumb->add('All Transaction', base_url() . 'account/company_transactions');
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
		//define ('PDF_PAGE_FORMAT', 'A4');
		$width = '350.00';
		$height = '250.00';
		if(!empty($usdTrans)){
		$custom_layout = array('350.00', '250.00');
		$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, $custom_layout, true, 'UTF-8', false);
			
		//$obj_pdf->getPageOrientedSize( $width, $height, $orientation = '' );	
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
			
		$subTotal = 0; $gstCount = 0; $pstCount = 0; $productprice = 0; $jsonArray = 0; $total_sub = 0; $usdi = 0;
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
		$productName = $decodeCategory[$catcount];
		$productQuantity = $decodeQuantity[$catcount];
		$productPriceSaved = $decodeUnitPrice[$catcount];
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
		
		$taxapplicable = $this->db->where('product_name', $productName)->get('products')->row();
		
		/* Exclude Company Type Price */
		$getGasStation = $this->db->where('name', $cardDetails->gas_station_name)->get('gas_stations')->row();
		if(!empty($getGasStation->exclude_pack_price) && $getGasStation->exclude_pack_price == 1){
			$companyTypePrice = 0;
		}	
			
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
			//$tax3withprice = number_format($decodeUnitPrice[$catcount]+$companyTypePrice, 4) + number_format($ppfTot, 4);
			$tax3withprice = number_format($finalProductPrice + $companyTypePrice, 4) + number_format($ppfTot, 4);
			$amtAfterTax = number_format($tax3withprice, 4) * $productQuantity;

			//$priceByCompany = number_format($decodeUnitPrice[$catcount]+$companyTypePrice, 4);
			$priceByCompany = number_format($finalProductPrice+$companyTypePrice, 4);
			if(empty($gst)){$gst = 0;}
			if(empty($pst)){$pst = 0;}
			
			$includeGST = $amtAfterTax * $gst / 100;
			$gstCount += $includeGST;
			
			$includePST = $amtAfterTax * $pst / 100;
			$pstCount += $includePST;
			
			//$amoutWithoutTax = number_format($decodeUnitPrice[$catcount]+$companyTypePrice, 4) * $decodeQuantity[$catcount];
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
			//$cardDetail[$count]['unit_price_'.$count] = $decodeUnitPrice[$catcount];
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
		for($upd =0; $upd <count($allResults); $upd++){
			/* $this->db->where('id', $allResults[$upd]->transid);
			$this->db->set('invoice_status', 1);
			$this->db->update('transactions'); */
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
		  $data['date_modified'] = date('Y-m-d H:i:s');
		  
		  //$this->db->insert('transaction_invoice', $data);
		  
		$divide = $count/10;

		$after_divide =  ceil($divide);

		/* if($after_divide <=  1){
			$after_divide = 1;
		} */
	
	if ( $count >= 0 ){  //If there are more than 0 transations

	$k =0;
		for ($j = 0; $j < $after_divide; $j++){
			//echo $j;
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

			  $content .= '<table class="table hrrule" border="0" style="border: 1px solid #1e1e1e;" cellspacing="0" cellpadding="3" nobr="true">  
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
			
			for($i = 0;$i<10;$i++){
				if($k < count($cardDetail) && !empty($cardDetail)){
				//if($cardDetail[$k]['quantity_'.$k] != ''){
					$content .= "<tr>
					<td>CARD: ".$cardDetail[$k]['card_number_'.$k]."</td>
					<td></td>
					<td></td>
					<td></td>"
					."</tr><tr><td>UNIT: ".$cardDetail[$k]['unit_price_'.$k]."</td></tr>
					<tr><td style='border-bottom: 1px solid #000 !important;'>PRODUCT: ".$cardDetail[$k]['category_'.$k]." </td><td>".$cardDetail[$k]['gas_station_name_'.$k]."</td><td>".$cardDetail[$k]['transaction_date_'.$k]."</td><td>".number_format($cardDetail[$k]['fet_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pft_'.$k], 4)."</td><td>".number_format($cardDetail[$k]['pct_'.$k], 4)."</td><td>".$cardDetail[$k]['quantity_'.$k]."</td><td>".number_format($cardDetail[$k]['ppftotal_'.$k], 4)."</td><td>".$cardDetail[$k]['unit_price_'.$k]."</td><td>".number_format($cardDetail[$k]['pst_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['gst_'.$k], 2)."</td><td style='border-bottom: 1px solid #000 !important;'>".number_format($cardDetail[$k]['amountwithouttax_'.$k], 2)."</td><td>".number_format($cardDetail[$k]['grandamount_'.$k], 2)."</td></tr>
					";
					  $total_sub += $cardDetail[$k]['amount_'.$k];
					//$content = "<hr />";
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

    $obj_pdf->Output('sample.pdf', 'I');	die;	
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
		$productQuantity = $decodeQuantity[$catcount];		
		$productPriceSaved = $decodeUnitPrice[$catcount];		
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
			//$cardDetail[$cadcount]['unit_price_'.$cadcount] = $decodeUnitPrice[$catcount];
			$cardDetail[$cadcount]['unit_price_'.$cadcount] = $priceByCompany;
			$cardDetail[$cadcount]['gas_station_name_'.$cadcount] = $cardDetails->gas_station_name;
			
			/*trans_data field*/
			$transJsonArrayObject = (array($cardDetails->card_number => array('product_name'=>$productName, 'quantity'=>$productQuantity, 'unit_price'=>number_format($priceByCompany, 4), 'fet' => $fet, 'pct'=>$pct, 'pft'=>$pft, 'gst'=>$gst, 'pst'=>$pst, 'amountwithouttax' => number_format($amoutWithoutTax, 2) , 'amountwithtax' => number_format($GrandTotalwithTax, 2))));
			$transationDetails[$jsonInc] = $transJsonArrayObject;
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

		$after_divide =  ceil($divide);

		if($after_divide <=  1){
			$after_divide = 1;
		}
	if ( $cadcount >= 0 ){  //If there are more than 0 transations

	$k_cad =0;
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
				//if($cardDetail[$k_cad]['quantity_'.$k_cad] != ''){
				if($k < count($cardDetail) && !empty($cardDetail)){		
					$content .= "<tr>
					<td>CARD: ".$cardDetail[$k_cad]['card_number_'.$k_cad]."</td>
					<td></td>
					<td></td>
					<td></td>"
					."</tr><tr><td>UNIT: ".$cardDetail[$k_cad]['unit_price_'.$k_cad]."</td></tr>
					<tr><td>PRODUCT: ".$cardDetail[$k_cad]['category_'.$k_cad]." </td><td>".$cardDetail[$k_cad]['gas_station_name_'.$k_cad]."</td><td>".$cardDetail[$k_cad]['transaction_date_'.$k_cad]."</td><td>".number_format($cardDetail[$k_cad]['fet_'.$k_cad], 4)."</td><td>".number_format($cardDetail[$k_cad]['pft_'.$k_cad], 4)."</td><td>".number_format($cardDetail[$k_cad]['pct_'.$k_cad], 4)."</td><td>".$cardDetail[$k_cad]['quantity_'.$k_cad]."</td><td>".number_format($cardDetail[$k_cad]['ppftotal_'.$k_cad], 4)."</td><td>".$cardDetail[$k_cad]['unit_price_'.$k_cad]."</td><td>".number_format($cardDetail[$k_cad]['pst_'.$k_cad], 2)."</td><td>".number_format($cardDetail[$k_cad]['gst_'.$k_cad], 2)."</td><td>".number_format($cardDetail[$k_cad]['amountwithouttax_'.$k_cad], 2)."</td><td>".number_format($cardDetail[$k_cad]['grandamount_'.$k_cad], 2)."</td></tr>
					";
					  $total_sub += $cardDetail[$k_cad]['amount_'.$k_cad];
				  $k_cad++;
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
	echo"<script>alert('Invoice generated and sent.')</script>";	
		redirect(base_url().'account/ledgers', 'refresh');
		/* $this->load->library('Pdf');
		$dataPdf = $this->user_model->get_data_byId('users','id',$id);
		print_r($dataPdf);die;
		create_pdf($dataPdf,'modules/user/views/view_user_pdf.php');
		$this->load->view('sale_orders/view_saleOrder_pdf'); */
	}
	
}