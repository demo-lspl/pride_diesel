<?php
class Agents extends MY_Controller {
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
		$this->load->model('agents_model');
		$this->load->library('form_validation');
	}
	
	public function index($pagination_offset=0){
		$this->settings['title'] = 'All Agents';
		$this->breadcrumb->mainctrl("agents");
		$this->breadcrumb->add('All Agents', base_url() . 'agents/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->load->library('pagination');
		$this->data['getuserdata'] = $this->agents_model->get_users();
		//Get all data of company
		$where = '';
		if(!empty($_GET['company_name'])){
			$where = $_GET['company_name'];
		}
		$this->data['allUserData'] = $this->agents_model->get_users($where);
        // pagination
        $config['base_url'] = site_url('agents/index');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allUserData']);
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

        $this->data['allUserData'] = $this->agents_model->get_pagination($config['per_page'], $page, $where);		
		$this->_render_template('index', $this->data);
	}	
	
	public function edit($id = null){			
		if($id){
			$this->settings['title'] = 'Agent Edit';
			$this->breadcrumb->mainctrl("agents");
			$this->breadcrumb->add('Agent Edit', base_url() . 'agents/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
			$this->data['company'] = $this->agents_model->get_by_id($id);		
		}else{
			$this->settings['title'] = 'Add Agent';
			$this->breadcrumb->mainctrl("agents");
			$this->breadcrumb->add('Add Agent', base_url() . 'agents/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();

			$this->data['company'] = $this->agents_model->get_new();
						
		}
		$id == NULL || $this->data['company'] = $this->agents_model->get_by_id($id);
		
		$rules = $this->agents_model->rules;

		$id || $rules['company_password']['rules'] .= '|required';	
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			
            $data = $this->agents_model->array_from_post(array('company_name', 'address', 'city', 'province', 'postal_code','company_email', 'company_password', 'role'));
			
			if($data['company_password'] == ''){
				unset($data['company_password']);
			}else
            {
                $data['company_password'] = md5($data['company_password']);
            }	
			
			if($id == NULL){
				$data['status'] = 0;
				$data['date_created'] = date('Y-m-d H:i:s');
				$data['date_modified'] = date('Y-m-d H:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d H:i:s');
			}
			//pre($data);die;
			$cid = $this->agents_model->create_user($data, $id);
			
			if($cid){

				$this->session->set_flashdata('success', 'Changes Saved.');
				
				redirect(base_url('agents/edit/'.$cid), 'refresh');				
			}
			
		}		
		
		$this->_render_template('edit', $this->data);
	}	
	
	public function delete($id=null){
		$userDeleted = $this->agents_model->delete($id);
		
		if($userDeleted == true){
			$this->session->set_flashdata('success', 'User deleted');
			redirect(base_url('agents/index'), 'refresh');
		}
	}
		
}