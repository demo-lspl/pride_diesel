<?php
class Settings extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		//$this->settings['css'][] = 'assets/css/tags.scss';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';		
		
		$this->scripts['js'][] = 'assets/modules/user/js/script.js';
		$this->scripts['js'][] = 'assets/js/tags.js';
		$this->scripts['js'][] = 'assets/modules/user/pricelist/js/script.js';
		$this->load->model('settings_model');
		$this->load->library('form_validation');
	}
	
	public function index(){
		$this->settings['title'] = 'Settings';
		$this->breadcrumb->mainctrl("settings");
		$this->breadcrumb->add('Settings', base_url() . 'settings/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$getData = $this->settings_model->get();
		if(!empty($getData)){
			//$this->data['efsNew'] = null;
			//$this->data['huskyNew'] = null;
			$this->data['efsNew'] = $this->settings_model->get_new_efs();
			$this->data['huskyNew'] = $this->settings_model->get_new_husky();			
			foreach($getData as $key=>$getDataItems){
				$this->data[$getDataItems->company] = array('id'=>$getDataItems->id,'username'=>$getDataItems->username, 'password'=>$getDataItems->password);
			}
		}else{
			$this->data['efsNew'] = $this->settings_model->get_new_efs();
			$this->data['huskyNew'] = $this->settings_model->get_new_husky();
		}
		$this->_render_template('index', $this->data);
	}	
	
	public function edit($id = null){			
		/* if($id){
			$this->data['company'] = $this->settings_model->get_by_id($id);		
		}else{
			$this->data['company'] = $this->settings_model->get_new();			
		}
		$id == NULL || $this->data['company'] = $this->settings_model->get_by_id($id); */
		
		if($this->input->post('company') == 'efs'){
			$rules = $this->settings_model->rules_efs;
			//$id == NULL || $this->data['company'] = $this->settings_model->get_by_id($id);
		}
		if($this->input->post('company') == 'husky'){
			$rules = $this->settings_model->rules_husky;
			//$id == NULL || $this->data['company'] = $this->settings_model->get_by_id($id);
		}		
		$this->form_validation->set_rules($rules);

		/* $id || $rules['company_password']['rules'] .= '|required';	
		$this->form_validation->set_rules($rules); */
		
		if($this->form_validation->run() == true){
			if($this->input->post('company') == 'efs'){
				$data = array('company'=>$this->input->post('company'), 'username'=>$this->input->post('efs_username'), 'password'=>$this->input->post('efs_password'));
			}
			if($this->input->post('company') == 'husky'){
				$data = array('company'=>$this->input->post('company'), 'username'=>$this->input->post('husky_username'), 'password'=>$this->input->post('husky_password'));
			}			
			
			/* if($data['company_password'] == ''){
				unset($data['company_password']);
			}else
            {
                $data['company_password'] = md5($data['company_password']);
            } */	
			
			if($id == NULL){
				//$data['status'] = 0;
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['date_modified'] = date('Y-m-d H:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d H:i:s');
			}
			//pre($data);die;
			$cid = $this->settings_model->set($data, $id);
			
			if($cid){

				$this->session->set_flashdata('success', 'Changes Saved.');
				
								
			}
			
		}
		
		redirect(base_url('settings'), 'refresh');
		//$this->_render_template('edit', $this->data);
	}	
	
	public function delete($id=null){
		$userDeleted = $this->settings_model->delete($id);
		
		if($userDeleted == true){
			$this->session->set_flashdata('success', 'User deleted');
			redirect(base_url('agents/index'), 'refresh');
		}
	}
		
}