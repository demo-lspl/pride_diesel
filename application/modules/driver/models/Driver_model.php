<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Driver_model extends MY_Model {
	protected $table = 'drivers';
	
	public function __construct(){
		parent::__construct();
	}
	
	public $driver_rules = array(
							'name' => array('field'=>'name', 'label'=>'Driver Name', 'rules'=>'required|trim'),
							'address' => array('field'=>'address', 'label'=> 'Address', 'rules'=> 'required|trim' ), 
							'state' => array('field'=>'state', 'label'=> 'State', 'rules'=> 'required|trim' ), 
							'country' => array('field'=>'country', 'label'=> 'Country', 'rules'=> 'required|trim' ), 
							'postal_code' => array('field'=>'postal_code', 'label'=> 'Postal/Zip Code', 'rules'=> 'required|trim' ),
							'email' => array('field'=>'email', 'label'=> 'Email', 'rules'=> 'required|trim|valid_email' ),
							'phone' => array('field'=>'phone', 'label'=> 'Phone', 'rules'=> 'required|trim' ),
	);
	
	public function get_new(){
		$driver = new stdClass();
		$driver->name 			= '';
		$driver->address 		= '';
		$driver->state 			= '';
		$driver->country 		= '';
		$driver->postal_code 	= '';
		$driver->licence_number = '';
		$driver->email 			= '';
		$driver->unit_number 	= '';
		$driver->odometer 		= '';
		$driver->phone 			= '';
		$driver->company_id 	= '';
		
		return $driver;
	}
	
	public function get_drivers($id=null, $where = null){
		$userSessDetails = $this->session->userdata('userdata');
		if($userSessDetails->role == 'admin'){
			$this->db->select('drivers.*, users.company_name');
			if($id){
				$this->db->where('drivers.company_id', $id);
			}
			$this->db->join('users', 'users.id = drivers.company_id', 'LEFT');
			if(!empty($where)){
				$this->db->where('drivers.company_id', $where);
			}
		}else{
			$this->db->select('drivers.*');
			if($id){
				$this->db->where('drivers.company_id', $id);
			}
			//$this->db->join('users', 'users.id = drivers.company_id', 'LEFT');
			if(!empty($where)){
				$this->db->where('drivers.id', $where);
			}			
		}		
		$getdrivers = $this->db->get($this->table);
		return $getdrivers->result();
	}
	
    public function get_pagination($id=null, $limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;
				$userSessDetails = $this->session->userdata('userdata');
		if($userSessDetails->role == 'admin'){
			$this->db->select('drivers.*, users.company_name');
			if($id){
				$this->db->where('drivers.company_id', $id);
			}
			$this->db->join('users', 'users.id = drivers.company_id', 'LEFT');
			if(!empty($where)){
				$this->db->where('drivers.company_id', $where);
			}
		}else{
			$this->db->select('drivers.*');
			if($id){
				$this->db->where('drivers.company_id', $id);
			}
			//$this->db->join('users', 'users.id = drivers.company_id', 'LEFT');
			if(!empty($where)){
				$this->db->where('drivers.id', $where);
			}			
		}		
        $this->db->limit($limit, $offset);
		$this->db->order_by('drivers.id','DESC');
        $query = $this->db->get($this->table);
        
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
	
	public function get_all_drivers($id){
		$this->db->select('id as id, name as text');
		$this->db->where('company_id', $id);
		$getdrivers = $this->db->get($this->table);
		return $getdrivers->result();		
	}
	
	public function get_by_id($id = null){
		$this->db->where('id', $id);
		$getDriver = $this->db->get($this->table);
		
		foreach($getDriver->result() as $driverDetails){
				$values = $driverDetails;
			}
		return $values;		
	}
	
	public function save($data, $id = null){
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
	
	public function get_companies(){
		$this->db->select('id, company_name');
		$this->db->where('role !=', 'admin');
		$getCompanies = $this->db->get('users');
		return $getCompanies->result();		
	}
	
	public function get_driver_by_cid($cid=NULL){
		$this->db->where('company_id', $cid);
		return $this->db->get($this->table)->result();
	}

	public function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
		return true;
	}
	
}