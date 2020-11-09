<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inventory_model extends MY_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
	public $product_rules = array(	
							'product_name' => array('field'=> 'product_name', 'label' => 'Product Name', 'rules' => 'required|trim'), 
							
							);
	
	public function get_product_new(){
		$product = new stdClass();
		$product->product_name = '';
		$product->price = '';
		$product->tax = '';
		
		return $product;
	}
	
	public function get_products($where = null){
		if(!empty($where)){
			$this->db->like('product_name', $where);
		}		
		return $this->db->get('products')->result();
	}
	
    public function get_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('product_name', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get('products');
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
            return $query->result();
            
        return array();
    }	

	public function get_product_by_id($id = NULL){
		$this->db->from('products');
		$this->db->where('id=', $id);
		return $this->db->get()->row();
	}
	
	public function save_product($data, $id = NULL){
		//print_r($id);die;
		if($id){
			$this->db->set($data);
			$this->db->where('id', $id);
			$this->db->update('products');
		}else{
			$this->db->insert('products', $data);
			$id = $this->db->insert_id();			
		}
		
		return $id;
	}

	public function delete_product($id){
		$this->db->where('id', $id);
		$this->db->delete('products');
		return true;
	}
		 
}