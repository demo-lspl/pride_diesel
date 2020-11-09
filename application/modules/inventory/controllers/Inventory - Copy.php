<?php
class Inventory extends MY_Controller {
	public function __construct(){
		parent::__construct();
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
		
		$this->data['productData'] = $this->inventory_model->get_products();
			
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
			
			$product_taxTypeLength = count($_POST['tax_type']);
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
				}			
			//JSON encoded data of description of products rows
			$data['tax'] = $descr_of_taxtype_array;			
			
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