<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings_model extends MY_Model {
	protected $table = 'efs_husky_crendentials';
	
	
	public function __construct(){
		parent::__construct();
	}
	
	public $rules_efs = array( 
					'efs_username' => array('field'=>'efs_username', 'label'=>'User Name', 'rules'=>'required|trim'), 
					'efs_password' => array('field'=>'efs_password', 'label'=>'Password', 'rules'=>'required|trim'),
					);
					
	public $rules_husky = array( 
					'husky_username' => array('field'=>'husky_username', 'label'=>'User Name', 'rules'=>'required|trim'), 
					'husky_password' => array('field'=>'husky_password', 'label'=>'Password', 'rules'=>'required|trim'),
					);					
	
	public function get_new_efs(){
		$efs = new stdClass();
		$efs->efs_username 		= '';
		$efs->efs_password 		= '';
		
		return $efs;
	}
	
	public function get_new_husky(){
		$husky = new stdClass();
		$husky->husky_username 		= '';
		$husky->husky_password 		= '';
		
		return $husky;
	}	
	
	public function get($where = null){
		if($where){
			$this->db->where('company', $where);
		}
		$getUsers = $this->db->get($this->table);
		return $getUsers->result();
	}
		
	public function set($data, $id=null){
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
}