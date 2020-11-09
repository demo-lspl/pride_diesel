<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_person_model extends MY_Model {
	protected $table = 'sales_person';
	
	
	public function __construct(){
		parent::__construct();
	}
	
	public $rules = array(
					'name' => array('field'=>'name', 'label'=>'Name', 'rules'=>'required'),
					);					
	
	public function get_new(){
		$exceutive = new stdClass();
		$exceutive->name 		= '';
		
		return $exceutive;
	}	
	
	public function get_sales_executives($where = null){
		if(!empty($where)){
			$this->db->like('name', $where);
		}
		$this->db->order_by('id', 'DESC');
		$getUsers = $this->db->get($this->table);
		return $getUsers->result();
	}
	
	public function get_users_where($where = null){
		//$this->db->where('com !=', 'admin');
		$this->db->like('name', $where);
		$this->db->order_by('id', 'DESC');
		$getUsers = $this->db->get($this->table);
		//print_r($this->db->last_query());
		return $getUsers->result();		
	}
	
	public function get_by_id($id = null){
		$this->db->where('id', $id);
		//$getUser = $this->db->get($this->table);
		$values = $this->db->get($this->table)->row();
		return $values;
		/* foreach($getUser->result() as $key => $userdatas){
			$values = $userdatas;
			}
		return $values; */		
	}
	
    public function get_count($where) {
		$this->db->like('company_name', $where);
        return $this->db->count_all($this->table);
    }	
	
    public function get_pagination($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('name', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get($this->table);
        
        if(!is_object($query))
        {
            echo $this->db->last_query();
            exit();
        }
		//pre($this->db->last_query());
        if ($query->num_rows() > 0)
			//print_r($query->result());
            return $query->result();
            
        return array();
    }

    public function get_pagination_company_type($limit, $offset, $where = null)
    {
		$offset = ($offset-1) * $limit;	
		if(!empty($where)){
			$this->db->like('company_type', $where);
		}
        $this->db->limit($limit, $offset);
		$this->db->order_by('id','DESC');
        $query = $this->db->get('company_types');
        
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
	
	public function create_user($data, $id=null){
		//print_r($data);die;
		if($id){
			$this->db->set($data);
			$this->db->where('id', $id);
			$this->db->update($this->table);		
		}else{
			$this->db->insert($this->table, $data);
			$id = $this->db->insert_id();			
		}
		return $id;
	}
	
	public function update_user($data = array(), $id){
		//print_r($data);die;
		/* if(!empty($data['user_pass'])){
			$this->db->set(array('first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'user_email' => $data['user_email'], 'user_pass' => $data['user_pass']));
		}else{
			$this->db->set(array('first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'user_email' => $data['user_email']));
		} */
		$this->db->set($data);
		$this->db->where('id', $id);
		$this->db->update($this->table);
		return $id;
	}	

	public function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table);
		return true;
	}
	
	public function exportusers(){
		$this->db->from('users');
        $query = $this->db->get();
        return $query->result_array();
	}
	
	public function exportuserspdf(){
		$this->db->from('users');
        $query = $this->db->get();

        return $query->result();
	}	
	
	public function importUsers($data) {
			$data['date_created'] = date('Y-m-d h:i:s');
			$data['date_modified'] = date('Y-m-d h:i:s');
			//$res = $this->db->insert_batch('users1',$data);
			$res = $this->db->insert('users1',$data);
			   
			 //if($_SESSION['loggedInUser']->role != 3){
				//$dynamicdb = $this->load->database('dynamicdb', TRUE);
				//$res = $dynamicdb->insert_batch('leads',$data);
			//}    
			 if($res){
			 return TRUE;
			 }else{
			 return FALSE;
			 }

	}
	
	public function importTApriceList($data) {
			//$data['date_created'] = date('Y-m-d h:i:s');
			//$data['date_modified'] = date('Y-m-d h:i:s');
			$res = $this->db->insert_batch('retail_pricing',$data);
			//$res = $this->db->insert('users1',$data);
			   
			 //if($_SESSION['loggedInUser']->role != 3){
				//$dynamicdb = $this->load->database('dynamicdb', TRUE);
				//$res = $dynamicdb->insert_batch('leads',$data);
			//}    
			 if($res){
			 return TRUE;
			 }else{
			 return FALSE;
			 }

	}	

	public function get_data_byId($table ,$field, $id) {
		/* if(!empty($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']->role == 3){ */
			$this->db->select('*');  
			$this->db->from($table);
			$this->db->where($table.'.'.$field, $id);
			$qry = $this->db->get();
		/* }else{
			$dynamicdb = $this->load->database('dynamicdb', TRUE);
			$dynamicdb->select('*');  
			$dynamicdb->from($table);
			$dynamicdb->where($table.'.'.$field, $id);
			$qry = $dynamicdb->get();
		} */
		$result = $qry->row();	
		return $result;
	}

	/*	Company		*/
	public function get_c_type($where=null){
		if(!empty($where)){
			$this->db->like('company_type', $where);
		}	
		return $this->db->get('company_types')->result();
	}
	
	public function get_c_type_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('company_types')->row();
	}	
	
	public function save_c_type($data, $id=NULL){
		if($id){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('company_types');
		}else{
			$this->db->insert('company_types', $data);
			$id = $this->db->insert_id();
		}
		
		
		return $id;
	}
	
	public function delete_c_type($id){
		$this->db->where('id', $id);
		$this->db->delete('company_types');
		return true;
	}
	
	/** Price List	**********/
	public function get_prodcuts(){
		return $this->db->get('products')->result();
	}

	public function get_pricelist(){
		return $this->db->get('pricelist_us')->result();
	}

	public function get_dailypricelist(){

		return $this->db->get('retail_pricing')->result();
	}
	
	public function get_dailypricelist_by_product(){
		//$this->db->where('product !=', '');
		return $this->db->get('retail_pricing')->result();
	}	

	public function get_dailypricelist_pro(){
		$this->db->where('product !=', '');
		$this->db->group_by('product');
		return $this->db->get('retail_pricing')->result();
	}	
	
	public function get_pricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist_us')->row();
	}
	
	public function get_ca_pricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist')->row();
	}	
	
	public function get_com_pricelist_by_id($id){
		$this->db->where('id', $id);
		return $this->db->get('pricelist_edit_us')->row();
	}	
	
	public function save_pricelist($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_us');
		}else{
			$this->db->insert('pricelist_us', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}
	
	public function save_ca_pricelist($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist');
		}else{
			$this->db->insert('pricelist', $data);
			$id = $this->db->insert_id();
		}
		return $id;
	}	
	
	public function save_pricelist_edit($data, $id=null){
		if($id == 1){
			$this->db->where('id', $id);
			$this->db->set($data);
			$this->db->update('pricelist_edit_us');
		}else{
			$this->db->insert('pricelist_edit_us', $data);
			$id = $this->db->insert_id();
		}
		//return $id;
	}	
	
	public function importPricelist($data) {
			//$data['date_created'] = date('Y-m-d h:i:s');
			$data['date_modified'] = date('Y-m-d h:i:s');
			//$res = $this->db->insert_batch('users1',$data);
			$this->db->set('efs_price', '');
			$this->db->update('pricelist');
			
			$this->db->set($data);
			$this->db->where('id', 1);
			//$res = $this->db->insert('pricelist',$data);
			$res = $this->db->update('pricelist');
			   
			 //if($_SESSION['loggedInUser']->role != 3){
				//$dynamicdb = $this->load->database('dynamicdb', TRUE);
				//$res = $dynamicdb->insert_batch('leads',$data);
			//}    
			 if($res){
			 return TRUE;
			 }else{
			 return FALSE;
			 }

	}

	/* Product */
	public function get_products(){
		return $this->db->get('products')->result();
	}			
	
}