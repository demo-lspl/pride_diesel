<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
 
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		//$this->load->library(array('auth/ion_auth'));
		
		#$user_id = $this->session->userdata('user_id');
		$this->current_controller = $this->router->fetch_class();
		
		/*$year = $this->session->userdata('year');
		$month = $this->session->userdata('month');
		
		/* BB Core things */
		/*$this->year = (isset($year) && $year!='')?$year:date('Y');
		$this->month = (isset($month) && $month!='')?$month:date('m');
		
		$this->user_id = $this->session->userdata('user_id');
		
		$this->user_group = $this->get_user_group();*/
		
	}
	
	public function array_from_post($fields){
		$data = array();
		foreach ($fields as $field) {
			$data[$field] = $this->input->post($field);
		}
		return $data;
	}	
	

}