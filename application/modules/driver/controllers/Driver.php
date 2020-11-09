<?php
class Driver extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }

		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';

		$this->scripts['js'][] = 'assets/modules/driver/js/script.js';

		$this->load->model('driver_model');
		$this->load->library('form_validation');
	}
	
	public function index(){
		$userSessDetails = $this->session->userdata('userdata');
		$this->settings['title'] = 'All Driver';
		$this->breadcrumb->mainctrl("driver");
		$this->breadcrumb->add('View Driver', base_url() . 'driver/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		$this->load->model('user/user_model');
		$companyID = null;
		if(!empty($userSessDetails->id) && $userSessDetails->role != 'admin'){
			$companyID = $userSessDetails->id;
			$this->data['getuserdata'] = $this->driver_model->get_drivers($companyID);
			
		}else{
			$this->data['getuserdata'] = $this->user_model->get_users($companyID);
		}
		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}		
		if($userSessDetails->role == 'admin'){
			$this->data['allDriver'] = $this->driver_model->get_drivers($companyID,$where);
			
		}else{
			$companyID = $userSessDetails->id;
			$this->data['allDriver'] = $this->driver_model->get_drivers($userSessDetails->id, $where);
//pre($this->db->last_query());die;			
		}

        // pagination
        $config['base_url'] = site_url('driver/index');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allDriver']);
        $config['per_page'] = 10;
        $config['full_tag_open'] = '<ul class="pagination custom-pagination">';
        $config['full_tag_close'] = '</ul>';
		$config['first_link']= '&laquo; First';
		$config['first_tag_open'] =  '<li class="prev page">';
		$config['first_tag_close']= '</li>'; 
		$config['last_link']= 'Last &raquo;';
		$config['last_tag_open']= '<li class="next page">';
		$config['last_tag_close']= '</li>';
		$config['next_link']= 'Next &rarr;';
		$config['next_tag_open']= '<li class="next page">';
		$config['next_tag_close']= '</li>';
		$config['prev_link']= '&larr; Previous';
		$config['prev_tag_open']= '<li class="prev page">';
		$config['prev_tag_close']= '</li>';		
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>';
        $config['link_suffix'] = '#content';
		$config['reuse_query_string'] = true;
		$config["use_page_numbers"] = TRUE;

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->data['allDriver'] = $this->driver_model->get_pagination($companyID, $config['per_page'], $page, $where);		
		$this->_render_template('index', $this->data);
	}	
	
	public function edit($id = null){
		if($id){
			$this->settings['title'] = 'Edit Driver';
			$this->breadcrumb->mainctrl("driver");
			$this->breadcrumb->add('Edit Driver', base_url() . 'driver/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();

			$this->data['driver'] = $this->driver_model->get_by_id($id);	
			
		}else{
			$this->settings['title'] = 'Add Driver';
			$this->breadcrumb->mainctrl("driver");
			$this->breadcrumb->add('Add Driver', base_url() . 'driver/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
			
			$this->data['driver'] = $this->driver_model->get_new();
		}
		
		$id == NULL || $this->data['driver'] = $this->driver_model->get_by_id($id);
		
		$this->data['companies'] = $this->driver_model->get_companies();
		
		$rules = $this->driver_model->driver_rules;
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			$data = $this->driver_model->array_from_post(array('name', 'address', 'state', 'country', 'postal_code', 'email', 'phone', 'unit_number', 'licence_number'));
			
			$userSessDetails = $this->session->userdata('userdata');
			//if($userSessDetails->role == 'admin'){
				$data['company_id'] = $this->input->post('company_id');
			//}			
			
			if($id == NULL){
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['date_modified'] = date('Y-m-d H:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d H:i:s');
			}
			
			/*	Driver Add/Edit  */
			$id = $this->driver_model->save($data, $id);
			
			if($id){

				$this->session->set_flashdata('success_msg', 'Changes Saved.');
				
				redirect(base_url('driver/edit/'.$id), 'refresh');				
			}			
		}
		
		$this->_render_template('edit', $this->data);
	}
	
	public function all_drivers($id = NULL){
		$data = $this->driver_model->get_all_drivers($id);
		echo json_encode($data);
	}
	
	public function delete($id=null){
		$rowDeleted = $this->driver_model->delete($id);
		
		if($rowDeleted == true){
			$this->session->set_flashdata('success_msg', 'Driver Deleted');
			redirect(base_url('driver/index'), 'refresh');
		}
	}	
		
}