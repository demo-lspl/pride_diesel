<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

     

class Api extends REST_Controller {

    

	  /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function __construct() {

       parent::__construct();

       $this->load->database();
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *');
    }
	
	public function index_get()
	{
		echo 'Hello, API here!';
        exit();
	}	

    /**

     * Get login from this method.

     *

     * @return Response

    */ 
    public function login_get(){
        //echo "test";die;
        $this->load->model('auth/auth_model', '', true);
        if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
            $result = $this->auth_model->login_check($_REQUEST['email'], md5($_REQUEST['password']));
            //print_r($result);die;
            if (!empty($result) && $result != 'Wrong user and password') {
 
                    $this->session->set_userdata('loggedInUser', $result);

                    $user_data   = array(
                        "user_id" => $result->id,
                        "company_name" => $result->company_name,
                        "company_type" => $result->company_type,
                        "company_email" => $result->company_email,
                        "pricing_type" => $result->pricing_type,
                        "role" => $result->role
                    );
                    //$data         = json_encode($user_data);
                    //echo '{"Status":"true","Data":' . $data . '}';
					$output = (array("Status"=> 'true', 'results'=>$user_data));
					$this->response($output, REST_Controller::HTTP_OK);
            } else {
                echo '{"Status":"false", "Data":[{"result":"The credentials you supplied are not correct"}]}';
            }
			
        }
    }	
    public function login_post(){
        //echo "test";die;
        $this->load->model('auth/auth_model', '', true);
        if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
            $result = $this->auth_model->login_check($_REQUEST['email'], md5($_REQUEST['password']));
            //print_r($result);die;
            if (!empty($result) && $result != 'Wrong user and password') {
 
                    $this->session->set_userdata('loggedInUser', $result);

                    $user_data   = array(
                        "user_id" => $result->id,
                        "company_name" => $result->company_name,
                        "company_type" => $result->company_type,
                        "company_email" => $result->company_email,
                        "pricing_type" => $result->pricing_type,
                        "role" => $result->role
                    );
                    //$data         = json_encode($user_data);
                    //echo '{"Status":"true","Data":' . $data . '}';
					$output = (array("Status"=> 'true', 'results'=>$user_data));
					$this->response($output, REST_Controller::HTTP_OK);
            } else {
                echo '{"Status":"false", "Data":[{"result":"The credentials you supplied are not correct"}]}';
            }
			
        }
    }
	
	public function getCards_get($card_id = 0){
		$this->load->model('card/card_model', '', true);
		
        if(!empty($card_id)){

            $data = $this->db->get_where("cards", ['id' => $card_id])->row_array();

        }else{

            $getCards = $this->db->order_by('id', 'DESC')->get("cards")->result();
			foreach($getCards as $getCardsItems){
				if($getCardsItems->company_id != 0){
					$getCompany = $this->db->select('company_name')->where('id', $getCardsItems->company_id)->get('users')->row();
					$data[] = array('id' => $getCardsItems->id,
									'card_number' => $getCardsItems->card_number,
									'card_status' => $getCardsItems->card_status,
									'policy_number' => $getCardsItems->policy_number,
									'card_pin' => $getCardsItems->card_pin,
									'driver_id' => $getCardsItems->driver_id,
									'unit_number' => $getCardsItems->unit_number,
									'odometer' => $getCardsItems->odometer,
									'company_assigned' => $getCompany->company_name,
									'company_id' => $getCardsItems->company_id,
									'date_created' => $getCardsItems->date_created,
									);
				}else{
					$data[] = array('id' => $getCardsItems->id,
									'card_number' => $getCardsItems->card_number,
									'card_status' => $getCardsItems->card_status,
									'policy_number' => $getCardsItems->policy_number,
									'card_pin' => $getCardsItems->card_pin,
									'driver_id' => $getCardsItems->driver_id,
									'unit_number' => $getCardsItems->unit_number,
									'odometer' => $getCardsItems->odometer,									
									'company_assigned' => null,
									'company_id' => 0,
									'date_created' => $getCardsItems->date_created,									
									);					
				}
			}
			//print_r($data);die;

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
     
        //echo json_encode($data);
       //exit();
        $this->response($output, REST_Controller::HTTP_OK);		
	}
	
	public function getCardsByCid_get($cid){
		$this->load->model('card/card_model', '', true);

        $data = $this->db->get_where("cards", ['company_id' => $cid])->result();

		$output = (array("Status"=> 'true', 'results'=>$data));
     
        //echo json_encode($data);
       //exit();
        $this->response($output, REST_Controller::HTTP_OK);		
	}	
	
    public function editCard_get(){

        $input = $this->put();
		$data = array(
			'card_number' =>$_REQUEST['card_number'],
			'card_limit' =>$_REQUEST['card_limit'],
			'policy_number' =>$_REQUEST['policy_number'],
			'card_status' =>$_REQUEST['card_status'],
			'card_pin' =>$_REQUEST['card_pin'],
			'company_id' =>$_REQUEST['company_id'],
			'driver_id' =>$_REQUEST['driver_id'],
			'unit_number' =>$_REQUEST['unit_number'],
			'odometer' =>$_REQUEST['odometer'],
			'date_modified' =>$_REQUEST['date_modified']
		);
		$this->db->set($data);
		$this->db->where(array('id'=>$_REQUEST['id']));
        //$this->db->update('drivers', $input, array('id'=>$id));
        $this->db->update('cards');

        $this->response(['Card updated successfully.'], REST_Controller::HTTP_OK);

    }	
	
	public function getUsers_get($card_id = 0){
		$this->load->model('user/user_model', '', true);
		
        if(!empty($card_id)){

            $data = $this->db->get_where("users", ['id' => $card_id])->row_array();

        }else{

            $data = $this->db->order_by('id', 'DESC')->get("users")->result();

        }

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}

	public function getTransactions_get($card_id =0,$daterange=null){
		/* $this->load->model('account/account_model', '', true);
			$data = $this->db->order_by('id', 'DESC')->get("transactions")->result();
			//pre($getTranss);die;
        if(!empty($card_id)){

            $data = $this->db->get_where("transactions", ['card_number' => $card_id])->row_array();

        }else if(!empty($daterange)){
			$where = null;
			$where2 = null;
			$explodeDateRange = explode('to', $daterange);			
			$where2 = $explodeDateRange;			
			$data = $this->account_model->get_transactions_api($where, $where2);
		}
		else{
			$getTrans = $this->db->order_by('id', 'DESC')->get("transactions")->result();
			pre($getTrans);die;
            //$data = $this->db->order_by('id', 'DESC')->get("transactions")->result();

        }//print_r($this->db->last_query());die; */
		$data = NULL;
		if(!empty($card_id)){
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			if(!empty($card_id)){
				$this->db->like('cards.card_number', $card_id);
			}
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
            //$data = $this->db->get_where("transactions", ['card_number' => $card_id])->row_array();
        }else if(!empty($daterange)){
			$where = null;
			$where2 = null;
			$explodeDateRange = explode('to', $daterange);			
			$where2 = $explodeDateRange;
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			if(!empty($where)){
				$this->db->like('cards.card_number', $where);
			}
			if(!empty($where2)){
				$start_date=$where2[0];
				$end_date=$where2[1];

				$this->db->where('transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
			}
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
			//$data = $this->account_model->get_transactions_api($where, $where2);
		}
		else{
			$getTrans = $this->db->order_by('id', 'DESC')->get("transactions")->result();
		}
		//print_r($getTrans);die;
			foreach($getTrans as $getTransItems){
				$categoryJsonDecode = json_decode($getTransItems->category);
				$pridePriceJsonDecode = json_decode($getTransItems->pride_price);
				$quantityJsonDecode = json_decode($getTransItems->quantity);
				$inc = 0;
				$transTotal = 0;
				//$amt = array();
				//print_r($categoryJsonDecode);
				//foreach($categoryJsonDecode as $categoryJsonDecodeItems){
				for($trans=0; $trans<count($categoryJsonDecode); $trans++){	
					
					$productName = $categoryJsonDecode[$inc];
					$pridePrice = $pridePriceJsonDecode[$inc];
					$productQuantity = $quantityJsonDecode[$inc];
					$totalAfterMultiplication = $productQuantity * floatval($pridePrice);
					//$amt[$inc] = $totalAfterMultiplication.",";
					$transTotal += floor($totalAfterMultiplication*100)/100;
					$inc++;
				}
				
				$data[] = array(
								'cardNumber' => $getTransItems->card_number, 
								'billingCurrency' => $getTransItems->billing_currency, 
								'gasStationName' => $getTransItems->gas_station_name, 
								'gasStationCity' => $getTransItems->gas_station_city, 
								'gasStationState' => $getTransItems->gas_station_state, 
								'productName' => $getTransItems->category, 
								'price' => $getTransItems->pride_price, 
								'quantity' => $getTransItems->quantity, 
								'transactionDate' => $getTransItems->transaction_date, 
								'transactionId' => $getTransItems->transaction_id, 
								'transactionTotal' => $transTotal,
								//'amt' => $amt
								);
				
			}
			//print_r($data);
			//die;
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK); 		
	}
	
	public function getTransactionsByCid_get($cid, $daterange=null){
		if(!empty($daterange)){
			$where = null;
			$where2 = null;
			$explodeDateRange = explode('to', $daterange);			
			$where2 = $explodeDateRange;
			//$this->db->select('cards.card_number, drivers.name, cards.card_status, transactions.transaction_date');
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			if(!empty($where)){
				$this->db->like('cards.card_number', $where);
			}
			$this->db->where('cards.company_id', $cid);
			if(!empty($where2)){
				$start_date=$where2[0];
				$end_date=$where2[1];

				$this->db->where('transaction_date BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
			}
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
		}
		else{
			$this->db->join('cards', 'cards.card_number = transactions.card_number');
			$this->db->where('cards.company_id', $cid);
			$getTrans = $this->db->order_by('transactions.id', 'DESC')->get("transactions")->result();
		}
			foreach($getTrans as $getTransItems){
				$categoryJsonDecode = json_decode($getTransItems->category);
				$pridePriceJsonDecode = json_decode($getTransItems->pride_price);
				$quantityJsonDecode = json_decode($getTransItems->quantity);
				$inc = 0;
				$transTotal = 0;
				foreach($categoryJsonDecode as $categoryJsonDecodeItems){
					$productName = $categoryJsonDecode[$inc];
					$pridePrice = $pridePriceJsonDecode[$inc];
					$productQuantity = $quantityJsonDecode[$inc];
					$totalAfterMultiplication = $productQuantity * floatval($pridePrice);
					$transTotal += floor($totalAfterMultiplication*100)/100;
				}
				$data[] = array(
								'cardNumber' => $getTransItems->card_number, 
								'billingCurrency' => $getTransItems->billing_currency, 
								'gasStationName' => $getTransItems->gas_station_name, 
								'gasStationCity' => $getTransItems->gas_station_city, 
								'gasStationState' => $getTransItems->gas_station_state, 
								'productName' => $getTransItems->category, 
								'price' => $getTransItems->pride_price, 
								'quantity' => $getTransItems->quantity, 
								'transactionDate' => $getTransItems->transaction_date, 
								'transactionId' => $getTransItems->transaction_id, 
								'transactionTotal' => $transTotal
								);
				
			}		
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK); 		
	}	

	public function getDrivers_get($name = null){
		$this->load->model('driver/driver_model', '', true);
		
        if(!empty($name)){

            $data = $this->db->get_where("drivers", ['name' => $name])->row_array();

        }else{

            $getDrivers = $this->db->order_by('id', 'DESC')->get("drivers")->result();
			foreach($getDrivers as $getDriversItems){
				if($getDriversItems->company_id != 0){
					$getCompany = $this->db->select('company_name')->where('id', $getDriversItems->company_id)->get('users')->row();
					$data[] = array('id' => $getDriversItems->id,
									'name' => $getDriversItems->name,
									'address' => $getDriversItems->address,
									'state' => $getDriversItems->state,
									'country' => $getDriversItems->country,
									'postal_code' => $getDriversItems->postal_code,
									'email' => $getDriversItems->email,
									'phone' => $getDriversItems->phone,
									'unit_number' => $getDriversItems->unit_number,
									'licence_number' => $getDriversItems->licence_number,
									'date_created' => $getDriversItems->date_created,
									'company_assigned' => $getCompany->company_name,
									'company_id' => $getDriversItems->company_id,
									);
				}
			}			

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}
	
	public function getDriversByCid_get($cid){
		$this->load->model('driver/driver_model', '', true);

        $data = $this->db->get_where("drivers", ['company_id' => $cid])->result();

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}	

	public function getGasStations_get($station_name = null){
		$this->load->model('gas_station/gas_station_model', '', true);
		
        if(!empty($station_name)){

            $data = $this->db->get_where("gas_stations", ['name' => $station_name])->row_array();

        }else{

            $data = $this->db->order_by('id', 'DESC')->get("gas_stations")->result();

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}

	public function getLedgers_get($where = null){
		$this->load->model('account/account_model', '', true);
		
        if(!empty($where)){

            $data = $this->account_model->get_ledgers($where);

        }else{

            $data = $this->account_model->get_ledgers($where);

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}
	
	public function createDriver_get(){
		$this->load->model('account/account_model', '', true);
		
        $input = $this->input->post();

		$data = array(
			'name' =>$_REQUEST['name'],
			'address' =>$_REQUEST['address'],
			'state' =>$_REQUEST['state'],
			'country' =>$_REQUEST['country'],
			'postal_code' =>$_REQUEST['postal_code'],
			'email' =>$_REQUEST['email'],
			'phone' =>$_REQUEST['phone'],
			'company_id' =>$_REQUEST['company_id'],
			'date_created' =>$_REQUEST['date_created'],
			'date_modified' =>$_REQUEST['date_modified'],
		);
        $this->db->insert('drivers',$data);
		$output = (array("Status"=> 'true', 'results'=>'Driver created successfully.'));
        $this->response($output, REST_Controller::HTTP_OK);		
	}	

	public function createDriver_post(){
		$this->load->model('account/account_model', '', true);
		
        $input = $this->input->post();

        $this->db->insert('drivers',$input);

        $this->response(['Driver created successfully.'], REST_Controller::HTTP_OK);		
	}
	
    public function editDriver_get(){

        $input = $this->put();
		$data = array(
			'name' =>$_REQUEST['name'],
			'address' =>$_REQUEST['address'],
			'state' =>$_REQUEST['state'],
			'country' =>$_REQUEST['country'],
			'postal_code' =>$_REQUEST['postal_code'],
			'email' =>$_REQUEST['email'],
			'phone' =>$_REQUEST['phone'],
			'company_id' =>$_REQUEST['company_id'],
			'date_modified' =>$_REQUEST['date_modified'],
		);
		$this->db->set($data);
		$this->db->where(array('id'=>$_REQUEST['id']));
        //$this->db->update('drivers', $input, array('id'=>$id));
        $this->db->update('drivers');

        $this->response(['Driver updated successfully.'], REST_Controller::HTTP_OK);

    }	

    public function editDriver_put(){

        $input = $this->put();

        $this->db->update('drivers', $input, array('id'=>$id));

        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);

    }

    public function downloadInvoice_get($where=null){

		$this->load->model('account/account_model', '', true);
		
        if(!empty($where)){

            $data = $this->account_model->get_card_trans_by_cid($where);

        }else{

            $data = $this->account_model->get_card_trans_by_cid($where);

        }
		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);

    }

	/* public function downloadInvoiceLink_get($cid=null){

		$output = (array("Status"=> 'true', 'results'=>array($pdfFilePath, $pdfFilePath2)));
        $this->response($output, REST_Controller::HTTP_OK);

    } */
    public function invoiced_trans_get($cid=null){
		$where = null;
		$this->load->model('account/account_model', '', true);
		if(!empty($cid)){
			$data = $this->db->where('company_id', $cid)->order_by('id', 'DESC')->get("transaction_invoice")->result();
		}else{
			$getInvoices = $this->db->order_by('id', 'DESC')->get("transaction_invoice")->result();
			foreach($getInvoices as $getInvoicesItems){
				if($getInvoicesItems->company_id != 0){
					$getCompany = $this->db->select('company_name')->where('id', $getInvoicesItems->company_id)->get('users')->row();
					$data[] = array('id' => $getInvoicesItems->id,
									'invoice_id' => $getInvoicesItems->invoice_id,
									'invoice_date' => $getInvoicesItems->invoice_date,
									'billingCurrency' => $getInvoicesItems->billingCurrency,
									'status' => $getInvoicesItems->status,
									'grand_total' => $getInvoicesItems->grand_total,
									'date_created' => $getInvoicesItems->date_created,
									'company_assigned' => $getCompany->company_name,
									'company_id' => $getInvoicesItems->company_id,
									);
				}
			}			
		}	
        

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);

    }	

    public function tax_rate_get(){
        $data = $this->db->order_by('id', 'DESC')->get("tax")->result();

		$output = (array("Status"=> 'true', 'results'=>$data));
        $this->response($output, REST_Controller::HTTP_OK);		
	}	

}
?>