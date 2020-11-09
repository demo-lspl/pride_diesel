<?php
class Inventory extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		if (!is_loggedin()) {
            redirect( base_url().'auth/login', 'refresh');
        }		
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.css';
		$this->settings['css'][] = 'assets/plugins/select2/css/select2.min.css';
		
		$this->scripts['js'][] = 'assets/modules/inventory/js/script.js';

		$this->load->model('inventory_model');
		$this->load->library('form_validation');
	}
	
	public function index(){
		$this->settings['title'] = 'All Card';
		$this->breadcrumb->mainctrl("card");
		$this->breadcrumb->add('View Card', base_url() . 'card/index');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->data['inventoryData'] = $this->card_model->get_cards();
		$this->_render_template('index', $this->data);
	}

	public function products(){
		$this->settings['title'] = 'All Product';
		$this->breadcrumb->mainctrl("inventory");
		$this->breadcrumb->add('All Product', base_url() . 'inventory/products');
		$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
		$this->load->library('pagination');

		//Get all data of company
		$where = '';
		if(!empty($_GET['search'])){
			$where = $_GET['search'];
		}		
		$this->data['productData'] = $this->inventory_model->get_products($where);
        // pagination
        $config['base_url'] = site_url('inventory/products');
        $config['uri_segment'] = 3;
        $config['total_rows'] = count($this->data['productData']);
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

        $this->data['productData'] = $this->inventory_model->get_pagination($config['per_page'], $page, $where);		
			
		$this->_render_template('product/index', $this->data);
	}
	
	public function product_by_id($id = null){
		$data = $this->inventory_model->get_product_by_id($id);
		echo json_encode($data);
	}
	
	public function edit_product($id = NULL){
		if($id){
			$this->settings['title'] = 'Edit Product';
			$this->breadcrumb->mainctrl("inventory");
			$this->breadcrumb->add('Edit Product', base_url() . 'inventory/index');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();
		
			$this->data['product'] = $this->inventory_model->get_product_by_id($id);
		}else{
			$this->settings['title'] = 'Add Product';
			$this->breadcrumb->mainctrl("inventory");
			$this->breadcrumb->add('Add Product', base_url() . 'inventory/index');
			$this->settings['breadcrumbs'] = $this->breadcrumb->output();			
			
			$this->data['product'] = $this->inventory_model->get_product_new();
		}
		
		$id == NULL || $this->data['product'] = $this->inventory_model->get_product_by_id($id);
		
		$rules = $this->inventory_model->product_rules;
		
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run() == true){
			$data = $this->inventory_model->array_from_post(array('product_name', 'price'));
			
			/* $product_taxTypeLength = count($_POST['tax_type']);
				if($product_taxTypeLength >0){
					$arr = [];
					$i = 0;
					while($i < $product_taxTypeLength) {	
						$jsonArrayObject = (array('tax_type' =>$_POST['tax_type'][$i],'tax_amount' => $_POST['tax_amount'][$i]));
						$arr[$i] = $jsonArrayObject;
						$i++;				
					}
					$descr_of_taxtype_array = json_encode($arr);
				}else{
					$descr_of_taxtype_array = '';
				} */			
			//JSON encoded data of description of products rows
			//print_r(json_encode($this->input->post('taxes')));die;
			$data['tax'] = json_encode($this->input->post('taxes'));			
			
			if($id == NULL){
				$data['date_modified'] = date('Y-m-d h:i:s');
				$data['date_created'] = date('Y-m-d h:i:s');
			}else{
				$data['date_modified'] = date('Y-m-d h:i:s');
			}
			
			$id = $this->inventory_model->save_product($data, $id);
			
			$this->session->set_flashdata('success', 'Changes Saved');
			redirect(base_url('inventory/edit_product/').$id, 'refresh');
		}
		
		$this->_render_template('product/edit', $this->data);
	}

	public function delete_product($id){
		$deleted = $this->inventory_model->delete_product($id);
		if($deleted == true){
			$this->session->set_flashdata('success', 'Record Deleted');
			redirect(base_url('inventory/products'), 'redirect');
		}
	}	
		
}