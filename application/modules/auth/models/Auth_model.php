<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	
	public function login_check($username, $password){
		$this->db->where(array('company_email' => $username, 'company_password' => $password));
		$fetchData = $this->db->get('users');
		$getUserLogin = $fetchData->row();

		if(!empty($getUserLogin)){
			return $getUserLogin;
		}else{
			return 'Wrong user and password';
		}
	}
	
	public function validate_email($email){
		$this->db->where(array('company_email' => $email));
		$fetchData = $this->db->get('users');
		return $fetchData->result();		
	}
	
	public function update_user_pass($userid, $pass){
		$this->db->set('company_password', $pass);
		$this->db->where('id', $userid);
		$this->db->update('users');
		return true;
	}
}