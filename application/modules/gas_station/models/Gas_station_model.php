<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gas_station_model extends MY_Model {
	protected $table = 'gas_stations';
	
	public function __construct(){
		parent::__construct();
	}
	
	public $rules = array('name'=>array('field'=>'name', 'label'=> 'Gas Station Name', 'rules'=> 'required|trim' ), 
					'address'=>array('field'=>'address', 'label'=> 'Address', 'rules'=> 'required|trim' ),
					'city'=> array('field'=>'city', 'label'=> 'City', 'rules'=> 'required|trim' ),
					'state'=> array('field'=>'state', 'label'=> 'State', 'rules'=> 'required|trim' ),
					'exclude_pack_price'=> array('field'=>'exclude_pack_price', 'label'=> 'Exclude Pack Price', 'rules'=> 'required' ),
					);
	
	public function get_new(){
		$gas_station = new stdClass();
		$gas_station->name = '';
		$gas_station->address = '';
		$gas_station->city = '';
		$gas_station->state = '';
		$gas_station->latitude = '';
		$gas_station->longitude = '';
		$gas_station->services = '';
		$gas_station->exclude_pack_price = '';
		$gas_station->contact_number = '';
		
		return $gas_station;
	}
	
	public function get_gas_stations($where = null){
		if(!empty($where)){
			$this->db->like('name', $where);
		}		
		$getGasStation = $this->db->get($this->table);
		return $getGasStation->result();
	}
	
    public function get_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('name', $where);
		}
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
            return $query->result();
            
        return array();
    }	
	
	public function get_by_id($id = null){
		$this->db->where('id', $id);
		$getGasStation = $this->db->get($this->table);
		return $getGasStation->row();		
	}
	
	public function save($data, $id){
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
	
	/* public function update($data = array(), $id){
		//print_r($data);die;

		$this->db->set(array('name' => $data['name'], 'address' => $data['address'], 'city' => $data['city'], 'state' => $data['state'], 'latlong' => $data['latlong'], 'services' => $data['services'], 'contact_number' => $data['contact_number']));

		$this->db->where('id', $id);
		$this->db->update($this->table);
		return true;
	} */

	public function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
		return true;
	}
	
}