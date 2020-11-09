<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agents_model extends MY_Model {
	protected $table = 'users';
	
	
	public function __construct(){
		parent::__construct();
	}
	
	public $rules = array( 
					'company_name' => array('field'=>'company_name', 'label'=>'Name', 'rules'=>'required|trim'), 
					'address' => array('field'=>'address', 'label'=>'Address', 'rules'=>'required|trim'),
					'company_email' => array('field'=>'company_email', 'label'=>'Email Address', 'rules'=>'required|trim|valid_email'),
					'company_password' => array('field'=>'company_password', 'label'=>'Password', 'rules'=>'trim|matches[confirm_company_password]'),
					'confirm_company_password' => array('field'=>'confirm_company_password', 'label'=>'Confirm Password', 'rules'=>'trim'));			
	
	public function get_new(){
		$company = new stdClass();
		$company->company_name 		= '';
		$company->address 			= '';
		$company->city 				= '';
		$company->province 			= '';
		$company->postal_code 		= '';
		$company->company_email 	= '';
		$company->role 				= 'admin';
		$company->last_activity 	= '';
		$company->status 			= 0;
		$company->company_password 	= '';
		
		return $company;
	}
	
	public function get_users($where = null){
		$this->db->where('role =', 'admin');
		$this->db->where('company_email !=', 'admin@gmail.com');
		if(!empty($where)){
			$this->db->like('company_name', $where);
		}
		$this->db->order_by('id', 'DESC');
		$getUsers = $this->db->get($this->table);
		return $getUsers->result();
	}
	
    public function get_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('company_name', $where);
		}
		$this->db->where('role =', 'admin');
		$this->db->where('company_email !=', 'admin@gmail.com');
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
	
	public function create_user($data, $id=null){
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
	
	public function get_by_id($id = null){
		$this->db->where('id', $id);
		$values = $this->db->get($this->table)->row();
		return $values;	
	}	

	public function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
		return true;
	}	
}