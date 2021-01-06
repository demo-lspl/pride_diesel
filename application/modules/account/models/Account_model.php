<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}
	//Form Validation rules for Ledger Form
	public $ledger_rules = array(
							'name'=>array('field'=> 'name', 'label'=> 'name', 'rules'=> 'required'),
							'phone'=>array('field'=> 'phone', 'label'=> 'Phone', 'rules'=> 'required'),
							'opening_balance'=>array('field'=> 'opening_balance', 'label'=> 'Opening Balance', 'rules'=> 'required'),
							//'registration_type'=>array('field'=> 'registration_type', 'label'=> 'Registration Type', 'rules'=> 'required'),
							//'account_group_id'=>array('field'=> 'account_group_id', 'label'=> 'Account Group', 'rules'=> 'required'),
							'email'=>array('field'=> 'email', 'label'=> 'Email', 'rules'=> 'required'),
							);
	//Form Validation rules for Invoice Form	
	public $invoice_rules = array(
			'party_name' => array('field'=> 'party_name', 'label'=> 'Party Name', 'rules'=> 'required|trim'),
			'party_address'=> array('field'=> 'party_address', 'label'=> 'Party Address', 'rules'=> 'required|trim'),
			'invoice_number' => array('field'=> 'invoice_number', 'label'=> 'Invoice Number', 'rules'=> 'required|trim'),
		);
	//Form Validation rules for Tax Form
	public $tax_rules = array(
			'state' => array('field'=> 'state', 'label'=> 'State', 'rules'=> ''),
			'tax_type'=> array('field'=> 'tax_type', 'label'=> 'Tax Type', 'rules'=> ''),
			'is_per_is_val'=> array('field'=> 'is_per_is_val', 'label'=> 'Percentage/Value', 'rules'=> 'required'),
			'tax_rate'=> array('field'=> 'tax_rate', 'label'=> 'Tax Rate', 'rules'=> 'trim|required'),
		);

	public function get_new_ledger_fields(){
		$ledgerFields = new stdClass();
		$ledgerFields->name = '';
		$ledgerFields->account_group_id = '';
		$ledgerFields->email = '';
		$ledgerFields->phone = '';
		$ledgerFields->opening_balance = '';
		$ledgerFields->registration_type = '';
				
		return $ledgerFields;
	}		
	
	public function get_new_invoice_fields(){
		$invoiceFields = new stdClass();
		$invoiceFields->party_name = '';
		$invoiceFields->party_address = '';
		$invoiceFields->driver_id = '';
		$invoiceFields->product_id = '';
		$invoiceFields->unit = '';
		$invoiceFields->invoice_number = '';
		$invoiceFields->invoice_date = date('Y-m-d');
		$invoiceFields->descr_of_products = '';
		$invoiceFields->card_number = '';
		$invoiceFields->quantity = '';
		$invoiceFields->price_unit = '';
		$invoiceFields->fuel_taxes = '';
				
		return $invoiceFields;
	}
	
	public function get_new_tax(){
		$tax = new stdClass();
		$tax->isfederaltax = '';
		$tax->state = '';
		$tax->tax_type = '';
		$tax->is_per_is_val = '';
		$tax->tax_rate = '';
	
		return $tax;
	}	
	
	public function get_ledgers($where = null){
		$this->db->select('users.*, transactions.invoice_status');
		$this->db->join('cards', 'cards.company_id = users.id', 'INNER');		
		$this->db->join('transactions', 'transactions.card_number = cards.card_number', 'INNER');
		$this->db->where('users.role', 'company');
		if(!empty($where)){
			$this->db->like($where);
		}
		$this->db->order_by('users.id', 'ASC');	
		//$this->db->join('users', 'users.id=ledger.name');
		$this->db->group_by('cards.company_id');
		//$this->db->order_by('users.id','ASC');
		return $this->db->get('users')->result();
	}
	
    public function get_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;
		$this->db->select('users.*, transactions.invoice_status, transactions.id as transid');
		$this->db->join('cards', 'cards.company_id = users.id', 'INNER');		
		$this->db->join('transactions', 'transactions.card_number = cards.card_number', 'INNER');
		$this->db->where('users.role', 'company');	
		if(!empty($where)){
			$this->db->like($where);
		}
		//$this->db->join('users', 'users.id=ledger.name');
        $this->db->limit($limit, $offset);
		$this->db->group_by('cards.company_id');
		$this->db->order_by('users.id ASC, transactions.id DESC');
        $query = $this->db->get('users');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	public function get_account_group(){
		$this->db->select('name, id');
		return $this->db->get('account_group')->result();
	}
	
	public function get_companies(){
		$this->db->select('company_name, id');
		$this->db->where('role !=', 'admin');
		return $this->db->get('users')->result();
	}	
	
	public function get_ledger_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('ledger')->row();
	}
	
	public function create_ledger($data){
		$this->db->insert('ledger', $data);
		return $this->db->insert_id();
	}

	public function edit_ledger($data, $id){
		if($id){
			$this->db->set($data);
			$this->db->where('id', $id);
			$this->db->update('ledger');
		}else{
			$this->db->insert('ledger', $data);
			$id = $this->db->insert_id();
		}

		return $id;
	}
	
	public function ledger_delete($id){
		$this->db->where('id', $id);
		$this->db->delete('ledger');
		return true;
	}	
	/*******************************	Invoice Code	*********************/
	public function get_invoices($where = null){
		$this->db->select('invoices.*, users.company_name');
		$this->db->from('invoices', 'users', 'drivers');
		$this->db->join('users', 'users.id=invoices.party_name');
		if(!empty($where)){
			$this->db->like('invoice_number', $where);
		}		
		//$this->db->join('drivers', 'drivers.id=invoices.driver_id');
		return $this->db->get()->result();
	}	
	
    public function get_pagination_invoice($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		$this->db->select('invoices.*, users.company_name');
		$this->db->from('invoices', 'users', 'drivers');
		$this->db->join('users', 'users.id=invoices.party_name');		
		if(!empty($where)){
			$this->db->like('invoice_number', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	

	public function get_trans_invoices($where = array(), $dateRange=null){
		$this->db->select('transaction_invoice.*, users.company_name');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);
		if(!empty($where)){
			$this->db->like($where);
		}
		if($dateRange){
			$expDateRange = explode(' - ', $dateRange);
			$startDate = $expDateRange[0];
			$endDate = $expDateRange[1];			
			$this->db->where('transaction_invoice.date_created >= "'. date('Y-m-d', strtotime($startDate)). '" and transaction_invoice.date_created <= "'. date('Y-m-d', strtotime($endDate)).'"');			
		}	

		return $this->db->get()->result();
	}
	
    public function get_pagination_trans_invoice($limit, $offset, $where = null, $dateRange=null)
    {
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where)){
			//$this->db->like('invoice_id', $where);
			$this->db->like($where);
		}
		if($dateRange){
			$expDateRange = explode(' - ', $dateRange);
			$startDate = $expDateRange[0];
			$endDate = $expDateRange[1];			
			$this->db->where('date(transaction_invoice.date_created) >= "'. date('Y-m-d', strtotime($startDate)). '" and date(transaction_invoice.date_created) <= "'. date('Y-m-d', strtotime($endDate)).'"');			
		}		
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	public function get_invoiced_transactions($id, $where = null){
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');		
		/* if(!empty($where)){
			$this->db->like('invoice_id', $where);
		} */
		$this->db->where('transaction_invoice.company_id', $id);	

		return $this->db->get()->result();
	}	
	
	public function get_company_invoices($cid, $where=null){
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');		
		$this->db->where('company_id', $cid);
		if(!empty($where)){
			$this->db->like('invoice_id', $where);
		}		

		return $this->db->get()->result();
	}	
	
    public function get_company_invoice_pagination($limit, $offset, $cid, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');		
		if(!empty($where)){
			$this->db->like('invoice_id', $where);
		}
		$this->db->where('company_id', $cid);
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
			//print_r($query->result());
            return $query->result();
            
        return array();
    }	

    public function get_pagination_invoiced_trans($limit, $offset, $id, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');		
		if(!empty($where)){
			$this->db->like('invoice_number', $where);
		}
		$this->db->where('transaction_invoice.company_id', $id);
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }		

	public function save_invoice($data, $id = null){
		/* $data['driver_id'] = json_encode($data['driver_id']);
		$data['product_id'] = json_encode($data['product_id']);
		$data['unit'] = json_encode($data['unit']);
		$data['quantity'] = json_encode($data['quantity']);
		$data['sub_total'] = json_encode($data['sub_total']);
		$data['price_unit'] = json_encode($data['price_unit']);
		$data['fuel_taxes'] = json_encode($data['fuel_taxes']); */
		//echo "<pre>";print_r($data);die;
		if($id){
			$this->db->set($data);
			$this->db->where('id', $id);
			$this->db->update('invoices');			
		}else{
			
			$this->db->insert('invoices', $data);
			$id = $this->db->insert_id();
		}
		
		return $id;
	}
	
	public function set_invoice_paid_status($invid){
		$this->db->set('status', 1);
		$this->db->where('id', $invid);
		$this->db->update('transaction_invoice');
	}

	public function get_invoice_by_id($id){
		$this->db->select('invoices.*, users.company_name, users.company_email');
		$this->db->join('users', 'users.id = invoices.party_name');		
		//$this->db->join('drivers', 'drivers.id = invoices.driver_id');		
		$this->db->where('invoices.id', $id);
		$query = $this->db->get('invoices');
		//print_r($this->db->last_query());die;
		foreach($query->result() as $invresults){
			$invresult = $invresults;
		}
		return $invresult;
	}
	
	public function get_trans_invoice_by_id($id){
		$this->db->select('transaction_invoice.*, users.company_name, users.company_email');
		$this->db->join('users', 'users.id = transaction_invoice.company_id');				
		$this->db->where('transaction_invoice.id', $id);
		$query = $this->db->get('transaction_invoice');
		foreach($query->result() as $invresults){
			$invresult = $invresults;
		}
		return $invresult;
	}	
	
	public function get_max_id(){
		$this->db->select_max('id');
		return $this->db->get('invoices')->row();
	}
	
	public function get_products(){
		return $this->db->get('products')->result();
	}
	
	public function get_product_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('products')->row();
	}

	public function export_invoice_pdf($id){
		$this->db->select('invoices.*, users.company_name');
		$this->db->join('users', 'users.id = invoices.party_name');
		$this->db->where('invoices.id', $id);
		$this->db->from('invoices');
        $query = $this->db->get();

        return $query->row();
	}
	/*******************************	End Invoice Code	*********************/
	public function get_users(){
		$this->db->where('role', 'company');
		$this->db->order_by('company_name', 'ASC');
		$results = $this->db->get('users')->result();
		return $results;
	}

	public function get_comp_addr($id){
		//echo $id;
		$this->db->select('users.address, drivers.id, drivers.name');
		$this->db->from('users', 'drivers');
		$this->db->join('drivers', 'drivers.company_id=users.id');
		$this->db->where('users.id', $id);
		$query = $this->db->get()->result();
		/* foreach($query->result() as $row){
			$comp_details = $row;
		} */
		//print_r($query);die;
		return $query;
		//echo "hello";
		//echo array('did'=>$comp_details->address,'did'=>$comp_details->id,'dname'=>$comp_details->name);		
	}

	/*******************************	Start Tax Code	*********************/
	public function get_taxes($where = null){
		if(!empty($where)){
			$this->db->like('state', $where);
		}		
		return $this->db->get('tax')->result();
	}
	
    public function get_pagination_tax($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('state', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get('tax');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	public function save_tax($data, $id){
		if($id){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('tax');
		}else{
			$this->db->insert('tax', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}

	public function get_tax_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('tax')->row();
	}
	
	public function delete_tax($id){
		$this->db->where('id', $id);
		$this->db->delete('tax');
		return true;
	}
	/*******************************	End Tax Code	*********************/	
	/*******************************	Start Transaction Code	*********************/
	public function get_transactions($where = null, $where2=null){
		$this->db->select('transactions.*, cards.card_status, cards.driver_id, cards.company_id');
		//$this->db->join('drivers', 'drivers.id = cards.driver_id', 'left');
		$this->db->join('cards', 'cards.card_number = transactions.card_number');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$start_date=$where2[0];
			$end_date=$where2[1];

			$this->db->where('transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
		}		
		//$this->db->where('cards.company_id', $cid);
		$this->db->group_by('transactions.card_number');	
		$this->db->order_by('transactions.id', 'DESC');	
		return $this->db->get('transactions')->result();
		/* if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$start_date=$where2[0];
			$end_date=$where2[1];

			$this->db->where('transaction_date >= "'. date('Y-m-d', strtotime($start_date)). '" and transaction_date <= "'. date('Y-m-d', strtotime($end_date)).'"');
		}		
		return $this->db->count_all_results('transactions'); */
	}
	
    public function get_pagination_transactions($limit, $offset, $where = null, $where2)
    {
		$offset = ($offset-1) * $limit;	
		$this->db->select('transactions.*, cards.card_status, cards.driver_id, cards.company_id');
		//$this->db->join('drivers', 'drivers.id = cards.driver_id', 'left');	
		$this->db->join('cards', 'cards.card_number = transactions.card_number');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$start_date=$where2[0];
			$end_date=$where2[1];
			/* $this->db->where('transactions.transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"'); */
			$this->db->where('transaction_date >= "'. date('Y-m-d', strtotime($start_date)). '" and transaction_date <= "'. date('Y-m-d', strtotime($end_date)).'"');
		}		
		//$this->db->where('cards.company_id', $cid);
		//$this->db->group_by('transactions.card_number');
        $this->db->limit($limit, $offset);
		$this->db->order_by('transactions.id','DESC');
        $query = $this->db->get('transactions');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        //return array();
    }

	public function getLastTransaction(){
		$this->db->select('transaction_date, transaction_id');
		//$this->db->limit(1);
		//$this->db->order_by('id', 'DESC');
		$getTransResults = $this->db->get('transactions')->result();
		//pre($this->db->last_query());die;
		return $getTransResults;
	}	
	
	public function get_transactions_api($where = null, $where2=null, $limit, $offset){
		if(empty($limit)){
			$limit = 10;
		}
		$offset = ($offset-1) * $limit;
		$this->db->select('transactions.amount, cards.card_number, drivers.name, cards.card_status, transactions.transaction_date, transactions.transaction_id');
		$this->db->join('drivers', 'drivers.id = cards.driver_id', 'left');
		$this->db->join('transactions', 'transactions.card_number = cards.card_number');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$start_date=$where2[0];
			$end_date=$where2[1];

			$this->db->where('transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
		}		
		//$this->db->where('cards.company_id', $cid);
		$this->db->limit($limit, $offset);
		$this->db->group_by('transactions.card_number', 'DESC');
		//$this->db->order_by('transactions.transaction_date', 'DESC');	
		return $this->db->get('cards')->result();
	}		
	
	public function get_comp_transactions($where = null, $where2=null, $cid){
		$this->db->select('cards.card_number, drivers.name, cards.card_status, transactions.transaction_date, transactions.id as transactionid');
		$this->db->join('drivers', 'drivers.id = cards.driver_id', 'left');
		$this->db->join('transactions', 'transactions.card_number = cards.card_number');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$start_date=$where2[0];
			$end_date=$where2[1];

			$this->db->where('transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
		}		
		$this->db->where('cards.company_id', $cid);
		//$this->db->group_by('transactions.card_number');	
		return $this->db->get('cards')->result();
	}	
	
    public function get_comp_pagination_transactions($limit, $offset, $where = null, $where2=null, $cid)
    {
		$offset = ($offset-1) * $limit;
		$this->db->select('cards.card_number, drivers.name, cards.card_status, transactions.transaction_date, transactions.id as transactionid');
		$this->db->join('drivers', 'drivers.id = cards.driver_id', 'left');	
		$this->db->join('transactions', 'transactions.card_number = cards.card_number');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$start_date=$where2[0];
			$end_date=$where2[1];
			$this->db->where('transactions.transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
		}		
		$this->db->where('cards.company_id', $cid);
		//$this->db->group_by('transactions.card_number');
        $this->db->limit($limit, $offset);
		$this->db->order_by('transactions.id','DESC');
        $query = $this->db->get('cards');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
	
	public function importHuskyTransactions($data) {
		//pre($data);die;
		$res = $this->db->insert_batch('transactions', $data);
		if($res){
			return TRUE;
		}else{
			return FALSE;
		}
	}	

	public function exportTransByComp($cid){
		$startDate = date('Y-m-d h:i:s');
		$endDate = date('Y-m-d h:i:s', strtotime('-30 days'));
		$this->db->from('transactions');
		//$this->db->from('cards');
		//$this->db->join('transactions', 'cards.card_number = transactions.card_number', 'LEFT');
		$this->db->join('cards', 'cards.card_number = transactions.card_number');
		//$this->db->where('transactions.transaction_date BETWEEN "'. $startDate. '" and "'. $endDate.'"');
		$this->db->where('cards.company_id', $cid);
        $query = $this->db->get();
        return $query->result_array();
	}	
	
	public function get_card_transactions($transid, $daterange=null, $card=null){
		$this->db->join('users', 'users.id = cards.company_id', 'LEFT');
		$this->db->join('transactions', 'cards.card_number = transactions.card_number', 'LEFT');
		$this->db->from('cards');
		//$oldDate = date('Y-m-d H:i:s', strtotime('-30 days'));
		
		if(!empty($daterange)){
			$expDateRange = explode(' - ', $daterange);
			$startDate = $expDateRange[0];
			$endDate = $expDateRange[1];			
			$this->db->where('transactions.transaction_date >= "'. date('Y-m-d', strtotime($startDate)). '" and transactions.transaction_date <= "'. date('Y-m-d', strtotime($endDate)).'"');
			$this->db->where('transactions.card_number', $card);
		}else{		
			$this->db->where('transactions.id', $transid);
		}
		return $this->db->get();result();
	}
	
	public function get_card_trans_by_cid($cid, $daterange=null, $companyName=null){
		//$this->db->select('transactions.*, transactions_ca.*, users.*, cards.*');
		$this->db->select('transactions.*, users.*, cards.* , transactions.id as transactionId');
		//$this->db->from('cards');
		$this->db->join('cards', 'cards.card_number = transactions.card_number', 'LEFT');
		$this->db->join('users', 'users.id = cards.company_id', 'LEFT');
		//$this->db->join('drivers', 'drivers.id = cards.driver_id', 'LEFT');
		//$this->db->join('transactions', 'transactions.card_number = cards.card_number', 'LEFT');
		//$this->db->join('transactions_ca', 'transactions_ca.card_number = cards.card_number', 'LEFT');
		$this->db->where(array('cards.company_id'=> $cid, 'transactions.invoice_status'=>0));
		if(!empty($companyName)){
			$this->db->where(array('transactions.transactionAt'=>$companyName));
		}
		if(!empty($daterange)){
			$expDateRange = explode(' - ', $daterange);
			$startDate = $expDateRange[0];
			$endDate = $expDateRange[1];			
			$this->db->where('DATE(transactions.transaction_date) BETWEEN "'. date('Y-m-d', strtotime($startDate)). '" and "'. date('Y-m-d', strtotime($endDate)).'"');
		}//$this->db->get()->result();
		//$this->db->group_by('cards.card_number');	
		return $this->db->get('transactions')->result();
		//pre($this->db->last_query());die;
	}
	
	public function get_invoiced_trans_by_cid($cid){
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		//$this->db->select('transactions.*, users.*, cards.*, transaction_invoice.invoice_id, transaction_invoice.invoice_date');
		$this->db->from('transaction_invoice');
		//$this->db->join('cards', 'cards.card_number = transactions.card_number');
		$this->db->join('users', 'users.id = transaction_invoice.company_id');
		//$this->db->join('transactions', 'transactions.card_number = cards.card_number', 'LEFT');
		//$this->db->join('transaction_invoice', 'transaction_invoice.company_id = cards.company_id', 'LEFT');
		$this->db->where(array('transaction_invoice.company_id'=> $cid));
		//$this->db->group_by('transactions.id');
		return $this->db->get()->result();
		
	}	
	
	public function get_cad_card_trans_by_cid($cid){
		$this->db->select('transactions_ca.*, users.*, cards.*');
		$this->db->from('cards');

		$this->db->join('users', 'users.id = cards.company_id');
		$this->db->join('transactions_ca', 'transactions_ca.card_number = cards.card_number', 'LEFT');

		$this->db->where(array('cards.company_id'=> $cid, 'transactions_ca.invoice_status'=>0));
		
		return $this->db->get()->result();
		
	}	
	
	public function get_card_driver($cardNumber){
		$this->db->select('driver_id');
		$this->db->where('card_number', $cardNumber);
		return $this->db->get('cards')->row();
	}

	public function export_invoice($cardNum){
		//$this->db->select('invoices.*, users.company_name');
		//$this->db->join('users', 'users.id = invoices.party_name');
		$this->db->where('id', $cardNum);
		$this->db->from('transactions');
        $query = $this->db->get();

        return $query->result();
	}

	public function get_max_trans_inv_id(){
		return $this->db->select_max('id')->get('transaction_invoice')->row();
	}
	
	/*******************************	End Transaction Code	*********************/

	
	/*************************** Get CAD Transaction Details ****************************/
	public function get_cad_rebate_transactions($limit, $offset, $where = null, $where2){
		$offset = ($offset-1) * $limit;	
		$this->db->select('transactions.id as tid,transactions.transaction_id ,transactions.amount ,transactions.gas_station_state,transactions.gas_station_city,transactions.gas_station_name,transactions.unit_price,transactions.quantity,transactions.category,cards.id, cards.card_number, drivers.name, cards.card_status, MAX(transactions.transaction_date) as transdate');
		$this->db->join('drivers', 'drivers.id = cards.driver_id', 'left');	
		$this->db->join('transactions', 'transactions.card_number = cards.card_number');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			// $start_date = $where2[0];
			// $end_date = $where2[1];
			
			
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transactions.transaction_date >='" . $start_date . "' AND  transactions.transaction_date <='" . $end_date. "'");
			
		}		
		//$this->db->where('cards.company_id', $cid);
		$this->db->where('transactions.billing_currency', 'CAD');
		$this->db->group_by('transactions.card_number');
        $this->db->limit($limit, $offset);
        //$this->db->limit(10);
		$this->db->order_by('transactions.id','DESC');
        $query = $this->db->get('cards');
        
        if(!is_object($query))
        {
            //echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
	public function get_rebate_transactions($where = null, $where2=null){
		$this->db->select('transactions.id as tid, cards.card_number, drivers.name, cards.card_status, MAX(transactions.transaction_date) as transdate, transactions.transaction_id');
		$this->db->join('drivers', 'drivers.id = cards.driver_id', 'left');
		$this->db->join('transactions', 'transactions.card_number = cards.card_number');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transactions.transaction_date >='" . $start_date . "' AND  transactions.transaction_date <='" . $end_date. "'");
		}		
		//$this->db->where('cards.company_id', $cid);
		$this->db->group_by('transactions.card_number');	
		$this->db->order_by('transactions.id', 'DESC');	
		//$this->db->limit(10);
		return $this->db->get('cards')->result();
	}
	public function get_trans_invoices_rebate($where = null, $where2){
		$this->db->select('transaction_invoice.*, users.company_name');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'EFS');
		//$this->db->limit(10);
		if(!empty($where2)){
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
		}		

		return $this->db->get()->result();
	}
	
	public function get_pagination_trans_invoice_rebate($limit, $offset, $where = null, $where2){
		
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'EFS');
       $this->db->limit($limit, $offset);
	   // $this->db->limit(10);
		$this->db->order_by('id','DESC');
		
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	
	public function get_pagination_trans_invoice_rebate_calculation($limit, $offset,$where = null, $where2){
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'EFS');
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
		//$this->db->limit(10);
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	/*************************** Get CAD Transaction Details ****************************/
	/*************************** Get CAD Per Transaction Details ****************************/
	public function get_trans_invoices_rebatePer($where = null, $where2,$invoice_id){
		$this->db->select('transaction_invoice.*, users.company_name');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'EFS');
		$this->db->where('transaction_invoice.id', $invoice_id);
		//$this->db->limit(10);
		if(!empty($where2)){
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
		}		

		return $this->db->get()->result();
	}
	
	public function get_pagination_trans_invoice_rebatePer($limit, $offset, $where = null, $where2,$invoice_id){
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'EFS');
		$this->db->where('transaction_invoice.id', $invoice_id);
        //$this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
		//$this->db->limit(10);
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	
	public function get_pagination_trans_invoice_rebate_calculationPer($where = null, $where2,$invoice_id){
		
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'EFS');
		$this->db->where('transaction_invoice.id', $invoice_id);
        //$this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
		//$this->db->limit(10);
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
	
	
	/*************************** Get CAD Per Transaction Details ****************************/
	/*************************** Get USA Transaction Details ****************************/
	public function get_trans_invoices_rebate_USA($where = null, $where2,$invoice_id){
		$this->db->select('transaction_invoice.*, users.company_name');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		$this->db->where('transaction_invoice.billingCurrency', 'USD');
		 //$this->db->where('transaction_invoice.date_created BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
		// $this->db->limit(10);
		 if($invoice_id != 0){
		 $this->db->where('transaction_invoice.id', $invoice_id);
		}
		if(!empty($where2)){
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
		}		

		return $this->db->get()->result();
	}
	
	public function get_pagination_trans_invoice_rebate_USA($limit, $offset, $where = null, $where2,$invoice_id){
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'USD');
		// $this->db->where('transaction_invoice.date_created BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
		//$this->db->limit(10);
		 if($invoice_id != 0){
			$this->db->where('transaction_invoice.id', $invoice_id);
		}else{
			$this->db->limit($limit, $offset);
			$this->db->order_by('id','DESC');
		}	
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	
	public function get_pagination_trans_invoice_rebate_calculation_USA($limit, $offset, $where = null, $where2,$invoice_id){
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'USD');
		 //$this->db->where('transaction_invoice.date_created BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
        //$this->db->limit($limit, $offset);
		//$this->db->limit(10);
		if($invoice_id != 0){
		 $this->db->where('transaction_invoice.id', $invoice_id);
		}else{
			$this->db->limit($limit, $offset); 
			$this->db->order_by('id','DESC');
		}
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
	
	/*************************** Get USA Transaction Details ****************************/
	
	
	
	
	
	
	
	
	
	/*************************** Get CAD HUSKY Transaction Details ****************************/
	public function get_trans_invoices_rebate_husky($where = null, $where2,$invoice_id){
		
		$this->db->select('transaction_invoice.*, users.company_name');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'HUSKY');
		if($invoice_id != 0){
		 $this->db->where('transaction_invoice.id', $invoice_id);
		}
		// $this->db->where('transaction_invoice.date_created BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
		//$this->db->limit(10);
		if(!empty($where2)){
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
		}		

		return $this->db->get()->result();
	}
	
	public function get_pagination_trans_invoice_rebate_husky($limit, $offset, $where = null, $where2,$invoice_id){
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'HUSKY');
		if($invoice_id != 0){
		 $this->db->where('transaction_invoice.id', $invoice_id);
		}else{
		// $this->db->where('transaction_invoice.date_created BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
			$this->db->limit($limit, $offset);
			// $this->db->limit(10);
			$this->db->order_by('id','DESC');
		}
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	
	public function get_pagination_trans_invoice_rebate_calculation_husky($limit, $offset,$where = null, $where2,$invoice_id){
		$offset = ($offset-1) * $limit;	
		$this->db->select('transaction_invoice.*, users.company_name, users.address');
		$this->db->from('transaction_invoice', 'users', 'drivers');
		$this->db->join('users', 'users.id=transaction_invoice.company_id');
		//$this->db->where('transaction_invoice.status', 0);	
		if(!empty($where2)){
			
			$start_date = $where2[0]. ' 00:00:00';
			$end_date = $where2[1]. ' 23:59:59';
			$this->db->where("transaction_invoice.date_created >='" . $start_date . "' AND  transaction_invoice.date_created <='" . $end_date. "'");
			
		}		
		$this->db->where('transaction_invoice.billingCurrency', 'CAD');
		$this->db->where('transaction_invoice.billingOn', 'HUSKY');
		if($invoice_id != 0){
		 $this->db->where('transaction_invoice.id', $invoice_id);
		}else{
		// $this->db->where('transaction_invoice.date_created BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()');
			$this->db->limit($limit, $offset);
		}
		// $this->db->limit(10);
		$this->db->order_by('id','DESC');
        $query = $this->db->get();
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//echo $this->db->last_query();
		
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
	/*************************** Get CAD HUSKY Transaction Details ****************************/
	/***************************Company commission  Details ****************************/
	public function num_rows($table, $where = array(),$where2){
		$this->db->select('*');  
		$this->db->from($table);
		$this->db->where('sales_person',$_SESSION['userdata']->id);
	 if($where2!=''){
		 $this->db->where($where2);
		 }
		$qry = $this->db->get();
		//echo $this->db->last_query();
		$result = $qry->num_rows();		
		return $result; 
	}

	public function get_company_dtl($limit, $offset,$where2){
		$offset = ($offset-1) * $limit;	
		$this->db->select('*');
		$this->db->from('users');
		$this->db->where('sales_person',$_SESSION['userdata']->id);
		// if(!empty($where2)){
			// $start_date = $where2[0]. ' 00:00:00';
			// $end_date = $where2[1]. ' 23:59:59';
			// $this->db->where("users.date_created >='" . $start_date . "' AND  users.date_created <='" . $end_date. "'");
		 // }		
			$this->db->limit($limit, $offset);
			$this->db->order_by('users.id','DESC');
			$query = $this->db->get();
        
        if(!is_object($query)){
            exit();
        }
		//echo $this->db->last_query();
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
    public function get_data_byId($table ,$field, $id) {
		$this->db->select('*');  
		$this->db->from($table);
		$this->db->where($table.'.'.$field, $id);
		$qry = $this->db->get();
		$result = $qry->row();	
		return $result;
	
	}

    public function get_data($limit, $offset , $where = array(),$where2) {
		$offset = ($offset-1) * $limit;	
		$this->db->select('cards.id,cards.card_number,cards.policy_number,cards.card_status,cards.cardToken,cards.company_id,cards.cardCompany');
		$this->db->from('cards');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		$this->db->where('users.sales_person', $_SESSION['userdata']->id);
		$this->db->join('users', 'cards.company_id = users.id', 'LEFT');
		$this->db->limit($limit, $offset);
		$qry = $this->db->get();
		echo $this->db->last_query();
		$resultw = $qry->result();	
		return $resultw;
	
	}	
	public function get_data_count($where = array(),$where2) {
		//$offset = ($offset-1) * $limit;	
		$this->db->select('cards.id,cards.card_number,cards.policy_number,cards.card_status,cards.cardToken,cards.company_id');
		$this->db->from('cards');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		$this->db->where('users.sales_person', $_SESSION['userdata']->id);
		$this->db->join('users', 'cards.company_id = users.id', 'LEFT');
		//$this->db->limit($limit, $offset);
		$qry = $this->db->get();
		//echo $this->db->last_query();
		$resultw = $qry->result();	
		return $resultw;
	
	}
	
	Public function getTRANS_details($limit, $offset ,$card_no,$where2){
		$offset = ($offset-1) * $limit;	
		$this->db->select('*');
		$this->db->from('transactions');
		//$oldDate = date('Y-m-d H:i:s', strtotime('-30 days'));
		
		 if(!empty($where2)){
			$startDate = $where2[0]. ' 00:00:00';
			$endDate = $where2[1]. ' 23:59:59';	
			$this->db->where('transactions.transaction_date >= "'.$startDate.'" and transactions.transaction_date <= "'. $endDate.'"');
			
		}
		//else{		
			$this->db->where('transactions.card_number', $card_no);
			$this->db->limit($limit, $offset);
			$qry = $this->db->get();
		//echo $this->db->last_query();
		$resultw = $qry->result();	
		return $resultw;
		//}
	}
	Public function getTRANS_details_count($card_no,$where2){
		$this->db->select('*');
		$this->db->from('transactions');
		if(!empty($where2)){
			
			$startDate = $where2[0]. ' 00:00:00';
			$endDate = $where2[1]. ' 23:59:59';
			
			$this->db->where('transactions.transaction_date >= "'. $startDate. '" and transactions.transaction_date <= "'.$endDate.'"');
			
		}
		$this->db->where('transactions.card_number', $card_no);
		$qry = $this->db->get();
		$resultw = $qry->result();	
		return $resultw;
	}
	/***************************Company commission  Details ****************************/
	/***************************Sales commission  Details ****************************/
	public function get_sales_person_data($limit, $offset , $where = array(),$where2) {
		$offset = ($offset-1) * $limit;	
		$this->db->select('*');
		$this->db->from('users');
		if(!empty($where)){
			$this->db->like('users.company_name', $where);
		}
		$this->db->where('users.role', 'sales');
		$this->db->limit($limit, $offset);
		$qry = $this->db->get();
		//echo $this->db->last_query();
		$resultw = $qry->result();	
		return $resultw;
	
	}	
	
	public function get_sales_person_company_data($limit, $offset , $where = array(),$userid) {
		$offset = ($offset-1) * $limit;	
		$this->db->select('cards.id,cards.card_number,cards.policy_number,cards.card_status,cards.cardToken,cards.company_id,cards.cardCompany');
		$this->db->from('cards');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		$this->db->where('users.sales_person', $userid);
		$this->db->join('users', 'cards.company_id = users.id', 'LEFT');
		$this->db->limit($limit, $offset);
		$qry = $this->db->get();
		//echo $this->db->last_query();
		$resultw = $qry->result();	
		//pre($resultw);
		return $resultw;
	
	}	
	public function get_sales_person_company_data_count($where = array(),$where2,$userid) {
		$this->db->select('cards.id,cards.card_number,cards.policy_number,cards.card_status,cards.cardToken,cards.company_id,cards.cardCompany');
		$this->db->from('cards');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		$this->db->where('users.sales_person', $userid);
		$this->db->join('users', 'cards.company_id = users.id', 'LEFT');
		$qry = $this->db->get();
		//echo $this->db->last_query();
		$resultw = $qry->result();	
		return $resultw;
	}	
	/***************************Sales commission  Details ****************************/
	
	
	
	
	
	
	
	
	
	
	
	
}