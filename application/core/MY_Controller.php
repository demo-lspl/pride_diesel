<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {	
	var $breadcrumb;
	public function __construct(){
		parent::__construct();
		//is_loggedin();
		$this->load->database();		
		//$this->CI =& get_instance();		
		#$this->load->model('erp_model');		
		#$this->load->library(array('form_validation','breadcrumb','twilio'));
		$this->load->library(array('form_validation', 'breadcrumb'));
		//$this->load->helper(array('url','layout_helper','function_helper'));	
		$this->load->helper(array('url', 'function_helper', 'functions_helper'));
		
		$this->styles = array();
		$this->scripts = array();		
		$this->settings['css'] = array(	'assets/plugins/fontawesome-free/css/all.min.css',   
										'assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css',
										'assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css',
										'assets/dist/css/adminlte.min.css',
										'assets/css/style.css'
										);			
		$this->scripts['js'] = array(  /* 'assets/js/jquery.min.js', */
									   'assets/plugins/jquery/jquery.min.js',
									   'assets/plugins/select2/js/select2.js',
									   'assets/plugins/select2/js/select2.min.js',
									   'assets/plugins/bootstrap/js/bootstrap.bundle.min.js',   
									   'assets/plugins/datatables/jquery.dataTables.min.js',  
									   'assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js', 
									   'assets/plugins/datatables-responsive/js/dataTables.responsive.min.js',   
									   'assets/plugins/jquery-validation/jquery.validate.min.js', 
									   'assets/plugins/jquery-validation/additional-methods.min.js', 
									   'assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js', 
									   'assets/dist/js/adminlte.min.js', 
									   'assets/dist/js/demo.js'
									);							
		$this->data['pageTitle'] = '';	
		
		$this->data['permissions'] = array();
		
 	}
	
	/*public function get_user($id){		
		$user = $this->ion_auth->user($id)->row();
		return $user;		
	}
	*/
	/* Render Template with Header and Footer*/
	public function _render_template($view, $data=null, $returnhtml=false, $header = true, $menu = true, $footer = true){
		//pre($_POST); 
	//	$result = $this->app_menus_listing(113);
		
		
		$this->viewdata = (empty($data)) ? $this->data: $data;
		//$this->data['css'] = $this->settings['css'];
		//$this->data['menus'] = $result;
		
		if($header){
			$this->load->view('template/header', $this->settings );
		//	$this->load->view('template/header', $this->data );
		}		
		$view_html = $this->load->view($view, $this->viewdata, $returnhtml);		
		if($footer){
			$this->load->view('template/footer', $this->scripts);
		}		
		/* This will return html on 3rd argument being true */
		if ($returnhtml) return $view_html;		
	}
	
	
	
	
	# Let's validate if all required fields are in the ajax request
	function validate_fields($fields = array(), $required = array()){		
		$output = array();		
		foreach($required as $val){			
			if(isset($fields[$val]) && $fields[$val] == ''){
				$output[$val] = '<p>' . ucwords(str_replace('_', ' ', $val)) . ' is an required field! </p>';
			}			
			if(!isset($fields[$val])){
				$output[$val] = '<p>' . ucwords(str_replace('_', ' ', $val)) . ' is missing from your request! </p>';
			}			
		}		
		return $output;
	}
	
	

	
}