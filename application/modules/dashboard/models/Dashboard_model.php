<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	
	public function get_card_count($id=null){
		if($id){
			$this->db->where('company_id', $id);
		}
		return $this->db->get('cards')->num_rows();
	}
	
	public function get_user_count(){
		return $this->db->get('users')->num_rows();
	}

	public function get_gas_station_count(){
		return $this->db->get('gas_stations')->num_rows();
	}	
	
	public function get_invoice_count(){
		$userSessDetails = $this->session->userdata('userdata');
		if($userSessDetails->role == 'admin'){
			return $this->db->get('transaction_invoice')->num_rows();
		}else{
			return $this->db->where('company_id', $userSessDetails->id)->get('transaction_invoice')->num_rows();
		}
		
	}

	public function get_driver_count($id=null){
		if($id){
			$this->db->where('company_id', $id);
		}		
		return $this->db->get('drivers')->num_rows();
	}	
}