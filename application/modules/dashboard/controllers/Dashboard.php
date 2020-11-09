<?php
class Dashboard extends MY_Controller {
	public function __construct(){
		parent::__construct();
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }		
		$this->load->model('dashboard_model');
	}
	
	public function index(){
		$userSessDetails = $this->session->userdata('userdata');
		$this->settings['title'] = 'Dashboard';
		$this->breadcrumb->mainctrl("dashboard");
		$this->breadcrumb->add('Dashboard', base_url() . 'dashboard/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		if($userSessDetails->role == 'admin'){	
			$this->data['cardCount'] = $this->dashboard_model->get_card_count();
			$this->data['userCount'] = $this->dashboard_model->get_user_count();
			$this->data['gasStations'] = $this->dashboard_model->get_gas_station_count();
			$this->data['invoiceCount'] = $this->dashboard_model->get_invoice_count();
		}else{
			$this->data['cardCount'] = $this->dashboard_model->get_card_count($userSessDetails->id);
			$this->data['driverCount'] = $this->dashboard_model->get_driver_count($userSessDetails->id);
			$this->data['invoiceCount'] = $this->dashboard_model->get_invoice_count();	
		}
		$this->_render_template('dashboard', $this->data);
	}
	

}