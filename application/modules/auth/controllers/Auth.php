<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		$this->load->model('auth_model');
		$this->load->helper('cookie');
		//$this->load->library('session');
	}
	
	public function index(){
		if(isset($_SESSION['loggedin'])){
			redirect(base_url().'dashboard/', 'refresh');
		}		
		$this->load->view('auth/login');
	}
	
	public function login(){
		if(isset($_SESSION['loggedin'])){
			redirect(base_url().'dashboard/', 'refresh');
		}	

		$this->data = '';
        $this->load->view('auth', $this->data);		
	}
	
	public function user_authenticate(){
		//print_r("Hello");die;
		//$decodepass = base64_decode($this->input->post('user_pass'));
		//$finalDecryptPass = str_replace("_@#!@", "", $decodepass);
		$userEmail = $this->input->post('company_email');
		$password = md5($this->input->post('company_password'));
		//$password = $this->input->post('user_pass');
		
		$loginStatus = $this->auth_model->login_check($userEmail, $password);
		
		if($loginStatus == 'Wrong user and password'){
			$this->session->set_flashdata('login_failed', 'User or Password is wrong.');
			redirect(base_url('auth'), 'refresh');
		}else{
			$this->db->set('last_activity', date('Y-m-d h:i:s'));
			$this->db->where('id', $loginStatus->id);
			$this->db->update('users');
			
			if($this->input->post('remember') == 'on'){
				
				set_cookie('company_email', $this->input->post('company_email'), time()+60*60*24*365);
				set_cookie('company_password', $this->input->post('company_password'), time()+60*60*24*365);
				set_cookie('rememberme', 'checked', time()+60*60*24*365);
				//print_r($this->input->post('remember'));die;
			}else{
				delete_cookie('company_email');
				delete_cookie('company_password');
				delete_cookie('rememberme');
			}
			
			$sessdata = array(
				'userid' => $loginStatus->id,
				'user_type' => $loginStatus->role,
				'userdata' => $loginStatus,
				'loggedin' => true
			);
			$this->session->set_userdata($sessdata);
			
			redirect(base_url().'dashboard/', 'refresh');			
		}
	}
	
	public function forgot_password(){
		$this->load->view('forgot-password', $this->data);	
	}
	
	public function recover_password($id = null){
		/* if($id){
		
		$getUserID = substr($this->uri->segment(3), 7);
		
		$this->form_validation->set_rules(array(array('field'=>'password', 'label'=>'Password', 'rules'=>'required'), array('field'=>'confirm_password', 'label'=>'Confirm Password', 'rules'=>'required|matches[password]')));
		//$this->form_validation->set_rules('password', 'Password', 'required');
		
		if($this->form_validation->run() == true){
			$updateStatus = $this->auth_model->update_user_pass($getUserID, $newpass);
			
			if($updateStatus == true){
				$this->session->set_flashdata('success_msg', 'Password changed successfully, now you can login.');
				redirect(base_url('auth/recover_password'), 'refresh');
			}
		}
	} */
		$this->load->view('recover-password', $this->data);
	}	
	
	public function send_email(){
		//print_r("hello");die;
		$this->load->library('email');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		
		if($this->form_validation->run() == true){
			$email_exists = $this->auth_model->validate_email($this->input->post('email'));
			
			if(!empty($email_exists)){
				$randomKey = substr(md5(mt_rand()), 0, 7);
				//print_r($email_exists[0]->id);die;
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from('info@pridediesel.com');				
				$this->email->to($this->input->post('email'));
				$this->email->subject('Forgot Password Email');
				
				$message = '<p>If you want to change/recover the password so please click and follow the link below:-</p>';
				$message .= "<a href='".base_url('auth/recover_password/').$randomKey.$email_exists[0]->id."'>Click here to recover password</a>";
				
				$this->email->message($message);
				
				if($this->email->send()){
					$this->session->set_flashdata('success_msg', 'Password recovery link is send, check your email.');
					redirect(base_url('auth/forgot_password'), 'refresh');
				}
			}else{
				$this->session->set_flashdata('error_msg', 'Email id does not exist');
				redirect(base_url('auth/forgot_password'), 'refresh');				
			}
			
		}else{
			$this->session->set_flashdata('error_msg', 'Please Enter email id');
			redirect(base_url('auth/forgot_password'), 'refresh');
		}		
	}
	
	public function update_password($id = null){
		if($id){
		
		$getUserID = substr($this->uri->segment(3), 7);
		
		$this->form_validation->set_rules(array(array('field'=>'password', 'label'=>'Password', 'rules'=>'required'), array('field'=>'confirm_password', 'label'=>'Confirm Password', 'rules'=>'required|matches[password]')));
		//$this->form_validation->set_rules('password', 'Password', 'required');
		
		if($this->form_validation->run() == true){
			$updateStatus = $this->auth_model->update_user_pass($getUserID, $this->input->post('password'));
			
			if($updateStatus == true){
				$this->session->set_flashdata('success_msg', 'Password changed successfully, now you can login.');
				redirect(base_url('auth/recover_password'), 'refresh');
			}
		}else{
			$this->session->set_flashdata('error_msg', validation_errors());
			redirect(base_url('auth/recover_password/'.$id), 'refresh');
		}
	}
	//$this->load->view('recover-password', $this->data);

	}
	
	public function logout(){
		$this->session->sess_destroy();
		redirect( base_url('auth/'), 'refresh');
	}
}