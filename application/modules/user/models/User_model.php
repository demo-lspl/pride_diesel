<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends MY_Model {
	protected $table = 'users';
	
	
	public function __construct(){
		parent::__construct();
	}
	
	public $rules = array(
					//'company_type' => array('field'=>'company_type', 'label'=>'US Account Type', 'rules'=>'required'), 
					//'company_type_ca' => array('field'=>'company_type_ca', 'label'=>'CA Account Type', 'rules'=>'required'), 
					'company_name' => array('field'=>'company_name', 'label'=>'Company Name', 'rules'=>'required|trim'), 
					'address' => array('field'=>'address', 'label'=>'Address', 'rules'=>'required|trim'),
					'company_email' => array('field'=>'company_email', 'label'=>'Email Address', 'rules'=>'required|trim|valid_email'),
					'company_password' => array('field'=>'company_password', 'label'=>'Password', 'rules'=>'trim|matches[confirm_company_password]'),
					'confirm_company_password' => array('field'=>'confirm_company_password', 'label'=>'Confirm Password', 'rules'=>'trim'));
					
	public $rules_new_company = array(
					'company_name' => array('field'=>'company_name', 'label'=>'Company Name', 'rules'=>'required'), 
					'address' => array('field'=>'address', 'label'=>'Address', 'rules'=>'required|trim'), 
					'company_email' => array('field'=>'company_email', 'label'=>'Company Email', 'rules'=>'required|trim|valid_email'),
					'company_password' => array('field'=>'company_password', 'label'=>'Password', 'rules'=>'trim|matches[confirm_company_password]'),
					'confirm_company_password' => array('field'=>'confirm_company_password', 'label'=>'Confirm Password', 'rules'=>'trim'));					
					
	public $c_type_rules = array(
					'company_type' => array('field'=>'company_type', 'label'=>'Company Type', 'rules'=>'required|trim'),					
					);

	public $pricelist_rules = array(
					'product_name' => array('field'=>'product_name[]', 'label'=>'Product Name', 'rules'=>'trim'), 
					);

	public $moneyCodeIssueRules = array(
									'amount' => array('field'=>'amount', 'label'=>'Amount', 'rules'=>'required'),
									);				
	
	public function get_new(){
		$company = new stdClass();
		$company->company_type 		= '';
		$company->company_type_ca 	= '';
		$company->sales_person 		= 0;
		$company->company_name 		= '';
		$company->customer_id 		= '';
		$company->efs_policy_id 	= '';
		$company->address 			= '';
		$company->city 				= '';
		$company->province 			= '';
		$company->postal_code 		= '';
		$company->company_email 	= '';
		$company->moreEmails 		= '';
		$company->fix_price 		= '';
		$company->fix_cost_data 	= '';
		$company->invoice_schedule 	= '';
		$company->usa_pricing 		= 'retail_price';
		$company->cad_pricing 		= 'add_on_efs';
		$company->sms_notification 	= 0;
		$company->allowMoneyCode 	= 0;
		$company->role 				= 'company';
		$company->last_activity 	= '';
		$company->status 			= 0;
		$company->company_password 	= '';
		
		return $company;
	}
	
	public function get_c_type_new(){
		$companyType = new stdClass();
		$companyType->company_type 	= '';
		
		return $companyType;
	}

	public function get_pricelist_new(){
		$priceList = new stdClass();
		$priceList->price_descr = '';
		
		return $priceList;
	}

	public function get_moneyCode_new(){
		$moneyCode = new stdClass();
		$moneyCode->cardNumber = '';
		$moneyCode->contractId = '';
		$moneyCode->masterContractId = '';
		$moneyCode->amount = '';
		$moneyCode->feeType = '';
		$moneyCode->issuedTo = '';
		$moneyCode->notes = '';
		$moneyCode->currency = '';
		return $moneyCode;
	}
	
	public function get_users($where = null){
		$this->db->where('role !=', 'admin');
		if(!empty($where)){
			$this->db->like('company_name', $where);
		}
		$this->db->order_by('id', 'DESC');
		$getUsers = $this->db->get($this->table);
		return $getUsers->result();
	}
	
	public function get_users_where($where = null){
		//$this->db->where('com !=', 'admin');
		$this->db->like('company_name', $where);
		$this->db->order_by('id', 'DESC');
		$getUsers = $this->db->get($this->table);
		//print_r($this->db->last_query());
		return $getUsers->result();		
	}
	
	public function get_by_id($id = null){
		$this->db->where('id', $id);
		//$getUser = $this->db->get($this->table);
		$values = $this->db->get($this->table)->row();
		return $values;
		/* foreach($getUser->result() as $key => $userdatas){
			$values = $userdatas;
			}
		return $values; */		
	}
	
    public function get_count($where) {
		$this->db->like('company_name', $where);
        return $this->db->count_all($this->table);
    }	
	
    public function get_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('company_name', $where);
		}
		$this->db->where('role !=', 'admin');
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get($this->table);
        
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

    public function get_pagination_company_type($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('company_type', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get('company_types');
        
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
	
	public function create_user($data, $id=null){
		//print_r($data);die;
		if($id){
			$this->db->set($data);
			$this->db->where('id', $id);
			$this->db->update($this->table);		
		}else{
			$this->db->insert($this->table, $data);
			$id = $this->db->insert_id();			
		}
		return $id;
	}
	
	public function update_user($data = array(), $id){
		//print_r($data);die;
		/* if(!empty($data['user_pass'])){
			$this->db->set(array('first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'user_email' => $data['user_email'], 'user_pass' => $data['user_pass']));
		}else{
			$this->db->set(array('first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'user_email' => $data['user_email']));
		} */
		$this->db->set($data);
		$this->db->where('id', $id);
		$this->db->update($this->table);
		return $id;
	}	

	public function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
		return true;
	}
	
	public function exportusers(){
		$this->db->from('users');
        $query = $this->db->get();
        return $query->result_array();
	}
	
	public function exportuserspdf(){
		$this->db->from('users');
        $query = $this->db->get();

        return $query->result();
	}	
	
	public function importUsers($data) {
			$data['date_created'] = date('Y-m-d h:i:s');
			$data['date_modified'] = date('Y-m-d h:i:s');
			//$res = $this->db->insert_batch('users1',$data);
			$res = $this->db->insert('users1',$data);
			   
			 //if($_SESSION['loggedInUser']->role != 3){
				//$dynamicdb = $this->load->database('dynamicdb', TRUE);
				//$res = $dynamicdb->insert_batch('leads',$data);
			//}    
			 if($res){
			 return TRUE;
			 }else{
			 return FALSE;
			 }

	}
	
	public function importTApriceList($data) {
		$res = $this->db->insert_batch('retail_pricing',$data);

		 if($res){
		 return TRUE;
		 }else{
		 return FALSE;
		 }
	}

	public function importCApriceList($data) {;
		$res = $this->db->insert_batch('retail_pricing_ca',$data);

		 if($res){
		 return TRUE;
		 }else{
		 return FALSE;
		 }
	}

	public function importHuskyCApriceList($data) {;
		$res = $this->db->insert_batch('retail_pricing_husky_ca',$data);
		 if($res){
		 return TRUE;
		 }else{
		 return FALSE;
		 }
	}	

	public function get_data_byId($table ,$field, $id) {
		/* if(!empty($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->role == 3){ */
			$this->db->select('*');  
			$this->db->from($table);
			$this->db->where($table.'.'.$field, $id);
			$qry = $this->db->get();
		/* }else{
			$dynamicdb = $this->load->database('dynamicdb', TRUE);
			$dynamicdb->select('*');  
			$dynamicdb->from($table);
			$dynamicdb->where($table.'.'.$field, $id);
			$qry = $dynamicdb->get();
		} */
		$result = $qry->row();	
		return $result;
	}

	/*	Company		*/
	public function get_c_type($where=null){
		if(!empty($where)){
			$this->db->like('company_type', $where);
		}	
		return $this->db->get('company_types')->result();
	}
	
	public function get_c_type_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('company_types')->row();
	}	
	
	public function save_c_type($data, $id=NULL){
		if($id){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('company_types');
		}else{
			$this->db->insert('company_types', $data);
			$id = $this->db->insert_id();
		}
		
		
		return $id;
	}
	
	public function delete_c_type($id){
		$this->db->where('id', $id);
		$this->db->delete('company_types');
		return true;
	}
	
	/** Price List	**********/
	public function get_prodcuts(){
		return $this->db->get('products')->result();
	}

	public function get_pricelist(){
		return $this->db->get('pricelist_us')->result();
	}

	public function get_dailypricelist(){

		return $this->db->get('retail_pricing')->result();
	}
	
	public function get_dailypricelist_ca(){

		return $this->db->get('retail_pricing_ca')->result();
	}
	
	public function get_husky_dailypricelist_ca(){

		return $this->db->get('retail_pricing_husky_ca')->result();
	}	

	public function get_dailyEditpricelist_CA(){

		return $this->db->get('pricelist_edit_ca')->result();
	}	
	
	public function get_dailypricelist_by_product(){
		//$this->db->where('product !=', '');
		return $this->db->get('retail_pricing')->result();
	}

	public function get_dailyCApricelist_by_product(){
		return $this->db->get('retail_pricing_ca')->result();
	}

	public function get_husky_dailyCApricelist_by_product(){
		return $this->db->get('retail_pricing_husky_ca')->result();
	}	

	public function get_dailypricelist_pro(){
		$this->db->where('product !=', '');
		$this->db->group_by('product');
		return $this->db->get('retail_pricing')->result();
	}

	public function get_dailyEditpricelist_US(){

		return $this->db->get('pricelist_edit_us')->result();
	}	
	
	public function get_pricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist_us')->row();
	}
	
	public function get_CApricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist_ca')->row();
	}

	public function get_husky_CApricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist_ca_husky')->row();
	}	
	
	public function get_ca_pricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist')->row();
	}	
	
	public function get_com_pricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist_edit_us')->row();
	}

	public function get_CAcom_pricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist_edit_ca')->row();
	}	
	
	public function save_pricelist($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_us');
		}else{
			$this->db->insert('pricelist_us', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}
	
	public function save_ca_pricelist($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_ca');
		}else{
			$this->db->insert('pricelist_ca', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}	
	
	public function save_pricelist_edit($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_edit_us');
		}else{
			$this->db->insert('pricelist_edit_us', $data);
			$id = $this->db->insert_id();
		}
		//return $id;
	}

	public function save_CApricelist_edit($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_edit_ca');
		}else{
			$this->db->insert('pricelist_edit_ca', $data);
			$id = $this->db->insert_id();
		}
		//return $id;
	}

	public function save_ca_pricelist_husky($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_ca_husky');
		}else{
			$this->db->insert('pricelist_ca_husky', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}

	public function save_CApricelist_edit_husky($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_edit_ca_husky');
		}else{
			$this->db->insert('pricelist_edit_ca_husky', $data);
			$id = $this->db->insert_id();
		}
		//return $id;
	}	
	
	public function importPricelist($data) {
			//$data['date_created'] = date('Y-m-d h:i:s');
			$data['date_modified'] = date('Y-m-d h:i:s');
			//$res = $this->db->insert_batch('users1',$data);
			$this->db->set('efs_price', '');
			$this->db->update('pricelist');
			
			$this->db->set($data);
			$this->db->where('id', 1);
			//$res = $this->db->insert('pricelist',$data);
			$res = $this->db->update('pricelist');
			   
			 //if($_SESSION['loggedInUser']->role != 3){
				//$dynamicdb = $this->load->database('dynamicdb', TRUE);
				//$res = $dynamicdb->insert_batch('leads',$data);
			//}    
			 if($res){
			 return TRUE;
			 }else{
			 return FALSE;
			 }

	}

	/* Product */
	public function get_products(){
		return $this->db->get('products')->result();
	}

	/* Money Code */
	public function issue_money_code($data, $id=null){
		if($id){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('money_codes');
		}else{
			$this->db->insert('money_codes', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}
	
	public function issueMoneyCode($data, $id){

		$this->db->insert('money_codes', $data);
		$id = $this->db->insert_id();
		return $id;
	}	

	public function get_moneyCode_by_id($id = null){
		$this->db->where('id', $id);
		$values = $this->db->get('money_codes')->row();
		return $values;	
	}

	/* Money Code */
	public function get_money_codes($where=null){
		if(!empty($where)){
			$this->db->like('issuedTo', $where);
		}
		$this->db->order_by('id', 'DESC');
		$getUsers = $this->db->get('money_codes');
		return $getUsers->result();		
	}
	
	public function get_moneyCode_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('issuedTo', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get('money_codes');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }

        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }

	public function getMoneyCodes($id,$where=null){
		$this->db->where('companyId', $id);
		if(!empty($where)){
			$this->db->like('issuedTo', $where);
		}
		$this->db->order_by('id', 'DESC');
		$getUsers = $this->db->get('money_codes');
		return $getUsers->result();		
	}
	
	public function getMoneyCode_pagination($id,$limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;
		$this->db->where('companyId', $id);	
		if(!empty($where)){
			$this->db->like('issuedTo', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get('money_codes');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }

        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }

	public function getNonInvoicedMoneyCodes($where = null){
		$this->db->join('users', 'users.id=money_codes.companyId');
		if(!empty($where)){
			$this->db->like('users.company_name', $where);
		}
		$getPendingInvoices = $this->db->where('money_codes.invoice_status', 0)->group_by('money_codes.companyId')->get('money_codes')->result();
		return $getPendingInvoices;
	}

	public function getNonInvoicedMoneyCodes_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		$this->db->join('users', 'users.id=money_codes.companyId');
		if(!empty($where)){
			$this->db->like('users.company_name', $where);
		}
		$this->db->where('money_codes.invoice_status', 0);
        $this->db->limit($limit, $offset);
		$this->db->group_by('money_codes.companyId');
		$this->db->order_by('money_codes.id','DESC');
        $query = $this->db->get('money_codes');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }

        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }
	
	public function getInvoicedMoneyCodes($where = null){
		$userSessDetails = $this->session->userdata('userdata');
		$this->db->select('money_codes_invoices.*, users.*');
		$this->db->join('users', 'users.id=money_codes_invoices.companyId', 'LEFT');
		if(!empty($where)){
			$this->db->like('users.company_name', $where);
		}
		/* $this->db->where('money_codes.invoice_status', 1);
		if($userSessDetails->role != 'admin'){
			$this->db->group_by('money_codes.companyId');
		}*/
		$this->db->order_by('money_codes_invoices.id','DESC'); 
		$getPendingInvoices = $this->db->get('money_codes_invoices')->result();
		//pre($this->db->last_query());die;
		return $getPendingInvoices;
	}

	public function getInvoicedMoneyCodes_pagination($limit, $offset, $where = null)
    {
		$userSessDetails = $this->session->userdata('userdata');
		$offset = ($offset-1) * $limit;	
		$this->db->select('users.*, money_codes_invoices.*');
		$this->db->join('users', 'users.id = money_codes_invoices.companyId', 'LEFT');
		if(!empty($where)){
			$this->db->like('users.company_name', $where);
		}
		//$this->db->where('money_codes.invoice_status', 1);
        $this->db->limit($limit, $offset);
		/* if($userSessDetails->role != 'admin'){
			$this->db->group_by('money_codes.companyId');
		} */
		$this->db->order_by('money_codes_invoices.id','DESC');
        $query = $this->db->get('money_codes_invoices');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }

        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }

	public function deleteMoneyCodeTrans($id){
		$this->db->where(array('id'=> $id));
		$this->db->delete('money_codes');
		return true;		
	}

	public function get_companyMoneyCodeTrans($cid){
		$this->db->where(array('companyId'=> $cid, 'invoice_status'=>0));
		$getMoneyCodeTrans = $this->db->get('money_codes')->result();
		return $getMoneyCodeTrans;
	}

	public function get_companyMoneyCodeTransInvoiced($invid){
		$this->db->select('money_codes.*, users.*, money_codes.date_modified as invoicedate');
		$this->db->where(array('money_codes.id'=> $invid));
		$this->db->join('users', 'users.id = money_codes.companyId');
		$getMoneyCodeTrans = $this->db->get('money_codes')->result();
		return $getMoneyCodeTrans;
	}
	
	public function updateMoneyCodeInvoiceStatus($mid){
		$this->db->set('invoice_status', 1);
		$this->db->where(array('id'=> $mid));
		$this->db->update('money_codes');
		
	}
	
}