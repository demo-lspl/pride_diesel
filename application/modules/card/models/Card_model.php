<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Card_model extends MY_Model {
	protected $table = 'cards';
	
	public $rules = array('card_number' => array('field'=>'card_number', 'label'=> 'Card Number', 'rules'=>'required|trim'),
					/* 'card_limit' => array('field'=>'card_limit', 'label'=> 'Card Limit', 'rules'=>'required|trim'), */
					'policy_number' => array('field'=>'policy_number', 'label'=> 'Policy Number', 'rules'=>'required|trim'),
					'card_status' => array('field'=>'card_status', 'label'=> 'Card Status', 'rules'=>'required|trim'));
	
	public function __construct(){
		parent::__construct();
	}
	
	public function get_new(){
		$card = new stdClass();
		//(object) $company;
		$card->card_number = '';
		$card->cardToken = '';
		$card->card_limit = '';
		$card->policy_number = '';
		$card->card_status = '';
		$card->card_pin = '';
		
		return $card;
	}	
	
	public function get_cards($where = null, $where2=null){
		$this->db->select('cards.*, users.company_name');
		$this->db->join('users', 'users.id = cards.company_id', 'LEFT');
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$this->db->like('users.company_name', $where2);
		}		
		$getCards = $this->db->get($this->table);
		return $getCards->result();
	}
	
    public function get_pagination($limit, $offset, $where = null, $where2=null)
    {
		$offset = ($offset-1) * $limit;
		$this->db->select('cards.*, users.company_name');
		$this->db->join('users', 'users.id = cards.company_id', 'LEFT');	
		if(!empty($where)){
			$this->db->like('cards.card_number', $where);
		}
		if(!empty($where2)){
			$this->db->like('users.company_name', $where2);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('cards.id','DESC');
        $query = $this->db->get($this->table);
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());die;
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	
	
	public function get_by_id($id = null){
		//$this->db->select('cards.*, drivers.unit_number, drivers.odometer');
		//$this->db->join('drivers', 'drivers.id = cards.driver_id', 'LEFT');
		$this->db->where('id', $id);
		$getUser = $this->db->get($this->table);
		return $getUser->row();		
	}
	
	public function get_card_by_id($id, $where = null){
		if(!empty($where)){
			$this->db->like('card_number', $where);
		}		
		$this->db->where('company_id', $id);
		return $this->db->get('cards')->result();
	}
	
    public function get_pagination_company_cards($id, $limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('card_number', $where);
		}
		$this->db->where('company_id', $id);
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
	
	public function edit($data, $id){
			$this->db->set($data);
			$this->db->where('id', $id);
			$this->db->update($this->table);
				
		/* if($id){
			$this->db->set($data);
			$this->db->where('id', $id);
			$this->db->update($this->table);
		}else{
			$this->db->insert($this->table, $data);
			$id = $this->db->insert_id();
		} */
		//print_r($this->db->last_query());die;
		return $id;
	}
	
	public function update($data = array(), $id){
		//print_r($data);die;

		$this->db->set(array('card_number' => $data['card_number'], 'card_limit' => $data['card_limit'], 'card_assigned' => $data['card_assigned']));

		$this->db->where('id', $id);
		$this->db->update($this->table);
		return true;
	}

	public function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
		return true;
	}

	public function importCards($data) {
	  
			$res = $this->db->insert_batch('users1',$data);
			   
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

	public function exportcards(){
		$this->db->from($this->table);
        $query = $this->db->get();
        return $query->result_array();
	}	 
}