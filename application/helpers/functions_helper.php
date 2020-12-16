<?php
/* Custom Helper for extra common functions */
		
#Count Rows		
	function pre($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}

	/*Accounts*/
	function getNameById($table='',$id='',$field=''){
		$qry="select * from $table where $field='$id'";
		$CI =& get_instance();
		$qryy = $CI->db->query($qry);	
		$result = $qryy->row();	
		return $result;	
	}
	function getNameById_new($table='',$id='',$field=''){
		$qry="select * from $table where $field='$id'";
		$CI =& get_instance();
		$qryy = $CI->db->query($qry);	
		$result = $qryy->row_array();	
		return $result;	
	}
	
	function getNameById_state($table='',$id='',$field=''){
		$qry="select * from $table where $field='$id'";
		$CI =& get_instance();
		$qryy = $CI->db->query($qry);	
		$result = $qryy->result();	
		return $result;	
	}		