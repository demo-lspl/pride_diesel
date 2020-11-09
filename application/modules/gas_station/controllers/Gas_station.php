<?php
class Gas_station extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }		

		$this->scripts['js'][] = 'assets/modules/gas_station/js/script.js';
		$this->load->model('gas_station_model');
		$this->load->library('form_validation');
	}
	
	public function index(){
		$this->settings['title'] = 'All Gas Station';
		$this->breadcrumb->mainctrl("gas_station");
		$this->breadcrumb->add('View Gas Station', base_url() . 'gas_station/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		
		
		$this->data['allGasStation'] = $this->gas_station_model->get_gas_stations($where);
		
        // pagination
        $config['base_url'] = site_url('gas_station/index');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['allGasStation']);
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

        $this->data['allGasStation'] = $this->gas_station_model->get_pagination($config['per_page'], $page, $where);		
		$this->_render_template('index', $this->data);
	}	
	
	public function edit($id = null){
			$latlong = '';

		
		if($id){
			$this->settings['title'] = 'Edit Gas Station';
			$this->breadcrumb->mainctrl("gas_station");
			$this->breadcrumb->add('Edit Gas Station', base_url() . 'gas_station/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();			
			
			$this->data['gas_stations'] = $this->gas_station_model->get_by_id($id);
			
		}else{
			$this->settings['title'] = 'Add Gas Station';
			$this->breadcrumb->mainctrl("gas_station");
			$this->breadcrumb->add('Add Gas Station', base_url() . 'gas_station/edit');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();			
			
			$this->data['gas_stations'] = $this->gas_station_model->get_new();	
		}
		
		$id == NULL || $this->data['gas_stations'] = $this->gas_station_model->get_by_id($id);
		
		$rules = $this->gas_station_model->rules;
		$this->form_validation->set_rules($rules);
		if($this->form_validation->run() == true){
			$data = $this->gas_station_model->array_from_post(array('name', 'address', 'city', 'state', 'latitude', 'longitude', 'exclude_pack_price', 'contact_number'));
			/* if(!empty($this->input->post('latlong'))){
				$data['latlong'] = implode(',', $this->input->post('latlong'));
			} */
			if(!empty($this->input->post('services'))){
				$data['services'] = json_encode($this->input->post('services'));
			}
			if($id){	
				$data['date_modified'] = date('Y-m-d h:i:s');
			}else{
				$data['date_created'] = date('Y-m-d h:i:s');
				$data['date_modified'] = date('Y-m-d h:i:s');
			}
			//pre($data);die;			
			$id = $this->gas_station_model->save($data, $id);
			if($id){
				$this->session->set_flashdata('success', 'Changes Saved');
				redirect(base_url('gas_station/edit/'.$id), 'refresh');
			}			
		}
		
		$this->_render_template('edit', $this->data);
	}
	

	
	public function delete($id=null){
		$rowDeleted = $this->gas_station_model->delete($id);
		
		if($rowDeleted == true){
			$this->session->set_flashdata('success_msg', 'Gas Station Deleted');
			redirect(base_url('gas_station/index'), 'refresh');
		}
	}	
		
}