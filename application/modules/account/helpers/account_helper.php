<?php
function export_csv_excel($data3 = array()){
	 if(!empty($_POST['ExportType'])){
		 ob_end_clean();
		switch($_POST["ExportType"])
		{
			case "export-to-excel" :
				// Submission from
				$filename = $_POST["ExportType"] . ".xls"; 
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");	
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				ExportFile($data3);
				//$_POST["ExportType"] = '';
				exit();
			case "export-to-csv" :
				// Submission from
				$filename = $_POST["ExportType"] . ".csv";            
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-type: text/csv");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				header("Expires: 0");
				ExportCSVFile($data3);
				//$_POST["ExportType"] = '';
				exit();
			case "export-to-blank-excel" :
				// Submission from
				$filename = $_POST["ExportType"] . ".xlsx";       
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				ExportFile_blank($data3);
				//$_POST["ExportType"] = '';
            exit();			
			
			default :
				die("Unknown action : ".$_POST["action"]);
				break;
		}
	 }
}

function export_csv_excel_blank($data_blank3 = array()){
	 if(!empty($_POST['ExportType_blank'])){
		// pre($_POST);
		 ob_end_clean();
		switch($_POST["ExportType_blank"])
		{
			case "export-to-blank-excel" :
				// Submission from
				$filename = $_POST["ExportType_blank"] . ".xlsx";       
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				ExportFile_blank($data_blank3);
				//$_POST["ExportType"] = '';
            exit();			
			
			default :
				die("Unknown action : ".$_POST["action"]);
				break;
		}
	 }
}
 
function ExportCSVFile($records) {
    // create a file pointer connected to the output stream
    $fh = fopen( 'php://output', 'w' );
    $heading = false;
        if(!empty($records))
			ob_end_clean();
          foreach($records as $row) {
            if(!$heading) {
              // output the column headings
              fputcsv($fh, array_keys($row));
              $heading = true;
            }
            // loop over the rows, outputting them
             fputcsv($fh, array_values($row));
              
          }
          fclose($fh);
}
 
function ExportFile($records) {
	$heading = false;
    if(!empty($records))
		//ob_end_clean();
      foreach($records as $row) {
        if(!$heading) {
          // display field/column names as a first row
          echo implode("\t", array_keys($row)) . "\n";
          $heading = true;
        }
        echo implode("\t", array_values($row)) . "\n";
      }
    exit;
}
function ExportFile_blank($records) {
	
    $heading = false;
    if(!empty($records))
		//ob_end_clean();
      foreach($records as $row) {
        if(!$heading) {
          // display field/column names as a first row
          echo implode("\t", array_keys($row)) . "\n";
          $heading = true;
        }
        echo implode("\t", array_values($row)) . "\n";
      }
    exit;
}



function checkValue($table='',$name='',$field=''){
			$query="select * from $table where $field='$name'";
			$CI =& get_instance();
			//$dynamicdb = $CI->load->database('dynamicdb', TRUE);
			$qryy = $CI->db->query($query);
			if($qryy->num_rows() > 0){
				  return true; 
				   return $result;
			   }else{
				   return false;
			   }
		}
		
	function get_purchase_bill_count($table='',$id='',$field=''){
			$qry= "SELECT id FROM ".$table." where ".$field." = ".$id." ORDER BY id DESC LIMIT 1";
			$CI =& get_instance();
			//$dynamicdb = $CI->load->database('dynamicdb', TRUE);
			$qryy = $CI->db->query($qry);	
			$result = $qryy->row();
			return $result;	
		}	
	
		
		
		/****** NEW CODE FOR ACCOUNT AND PERMISSSON *************************************/
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
		
	function getNameById_bywith_ledgers($table='',$id='',$field=''){
			$qry="select * from $table where $field='$id' AND account_group_id = 7 ";
			$CI =& get_instance();            
			$qryy = $CI->db->query($qry);	
			$result = $qryy->row();	
			return $result;	
		}
		
	/****** NEW CODE FOR ACCOUNT AND PERMISSSON *************************************/	
		function getLastTableId($table=''){	
		$CI =& get_instance();		
		$qry="SELECT * FROM $table ORDER BY id DESC LIMIT 1";		
		$CI =& get_instance();           
		$qryy = $CI->db->query($qry);				
		$result = $qryy->row();			
		if(!empty($result))				
		return $result->id;		
		else return false;  
	}	
		
		