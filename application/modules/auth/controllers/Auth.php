<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH.'libraries/sendgrid-php.php';
require_once APPPATH.('libraries/sendgrid-php/lib/SendGrid.php');
require_once APPPATH.('libraries/sendgrid-php/lib/BaseSendGridClientInterface.php');
require_once APPPATH.('libraries/sendgrid-php/lib/TwilioEmail.php');
use SendGrid\Mail\Mail;
class Auth extends MY_Controller {
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Toronto');
		$this->load->model('auth_model');
		$this->load->helper('cookie');
		//$this->load->library('session');
		//ini_set("SMTP","ssl://smtp.gmail.com");
		//ini_set("SMTP","tls://smtp.gmail.com");
		//ini_set("SMTP","smtp.gmail.com");
		//ini_set("smtp_port","465");
		//ini_set("smtp_port","25");
		//ini_set("sendmail_from","<jagdish@lastingerp.com>");
	}
	
	public function index(){
		if(isset($_SESSION['loggedin'])){
			redirect(base_url().'dashboard/', 'refresh');
		}		
		$this->load->view('auth/login');
	}
	
	public function login(){
		if(isset($_SESSION['loggedin'])){
			redirect(base_url().'dashboard/', 'refresh');
		}	

		$this->data = '';
        $this->load->view('auth', $this->data);		
	}
	
	public function user_authenticate(){
		
		//print_r("Hello");die;
		//$decodepass = base64_decode($this->input->post('user_pass'));
		//$finalDecryptPass = str_replace("_@#!@", "", $decodepass);
		$userEmail = $this->input->post('company_email');
		$password = md5($this->input->post('company_password'));
		//$password = $this->input->post('user_pass');
		
		$loginStatus = $this->auth_model->login_check($userEmail, $password);
		
		if($loginStatus == 'Wrong user and password'){
			$this->session->set_flashdata('login_failed', 'User or Password is wrong.');
			redirect(base_url('auth'), 'refresh');
		}else{
			$this->db->set('last_activity', date('Y-m-d h:i:s'));
			$this->db->where('id', $loginStatus->id);
			$this->db->update('users');
			
			if($this->input->post('remember') == 'on'){
				set_cookie('company_email', $this->input->post('company_email'), time()+60*60*24*365);
				set_cookie('company_password', $this->input->post('company_password'), time()+60*60*24*365);
				set_cookie('rememberme', 'checked', time()+60*60*24*365);
				//print_r($this->input->post('remember'));die;
			}else{
				delete_cookie('company_email');
				delete_cookie('company_password');
				delete_cookie('rememberme');
			}
			//$localIP = getHostByName(getHostName());
			$localIP = $this->input->ip_address();
			$getIPaddr = $this->db->select('ip_address')->where('id', $loginStatus->id)->get('users')->row();
			//echo $localIP ."!=". $getIPaddr->ip_address;
			if(empty($getIPaddr) || $localIP !== $getIPaddr->ip_address){
				//echo $localIP;
				//echo $_SERVER['SERVER_ADDR'];
				//die;
				$uemail = $this->input->post('company_email');
				$vercode = mt_rand(100000, 999999);
				$this->db->set(['verification_code'=> $vercode, 'ver_code_sent' => date('Y-m-d H:i:s')])->where('id', $loginStatus->id)->update('users');
				/* $sessdata = array(
					'userid' => $loginStatus->id,
					'user_type' => $loginStatus->role,
					'userdata' => $loginStatus,
					'loggedin' => true
				); */
				$userRole = $loginStatus->role;				
				$this->send_varification_email($uemail, $vercode, $userRole);
				$this->data['userId'] = $loginStatus->id;

				/*$this->data['userId'] = $loginStatus->id;
				$this->data['userId'] = $loginStatus->id; */
				$this->data['userEmail'] = $this->input->post('company_email');
				$this->data['userIP'] = $localIP;
				//$this->data['session_data'] = $this->session->set_userdata($sessdata);
				//$this->load->view('authentication', $this->data);
			}else{
				$sessdata = array(
					'userid' => $loginStatus->id,
					'user_type' => $loginStatus->role,
					'userdata' => $loginStatus,
					'loggedin' => true
				);
				$this->data['session_data'] = $this->session->set_userdata($sessdata);
				redirect(base_url().'dashboard/', 'refresh');
				exit;	
			}
			//redirect(base_url().'dashboard/', 'refresh');			
			$this->load->view('authentication', $this->data);			
		}
	}
	
	public function send_varification_email($uemail, $vercode, $userRole){
		//$this->load->library('email');
		/* $config = Array(        
		'protocol' => 'sendmail',
		'smtp_host' => 'ssl://smtp.googlemail.com',
		'smtp_port' => 465,
		'smtp_user' => 'jagdish@lastingerp.com',
		'smtp_pass' => 'dKhWX=3GJOSHI',
		'smtp_timeout' => '4',
		'mailtype'  => 'html', 
		'charset'   => 'utf-8',
		'wordwrap' => TRUE
		); */
		##Outlook
		/* $config = [        
			'protocol' => 'smtp',
			'smtp_host' => 'smtp.office365.com',
			//'smtp_user' => 'YOUR_EMAIL',
			//'smtp_pass' => 'YOUR_PASSWORD',
			'smtp_crypto' => 'tls',    
			'newline' => "\r\n", //REQUIRED! Notice the double quotes!
			'smtp_port' => 587,
			'mailtype' => 'html'    
		]; jagdish@lastingerp.com */
		//require APPPATH .'third_party/sendgrid-php/sendgrid-php.php';
		//require APPPATH .'third_party/sendgrid-php/lib/SendGrid.php';
		/* require_once (APPPATH.'third_party/sendgrid-php/lib/BaseSendGridClientInterface.php');
		require_once (APPPATH.'third_party/sendgrid-php/lib/SendGrid.php');
		
		//SendGrid::register_autoloader();
		$to = "jagdish@lastingerp.com";
		$from = "info@pridediesel.com";
		$subject = "Sending with SendGrid is Fun";
		$html = "<strong>and easy to do anywhere, even with PHP</strong>";
    $sendgrid = new SendGrid('dev@lastingerp.com', 'LastingSoftware@123');
    $email    = new SendGrid\Email();
    $email->addTo( $to)->
           setFrom($from)->
           setSubject($subject)->
           setText('Test')->
           setHtml($html);

    $x = $sendgrid->send($email); */		
		/* require FCPATH .'vendor/sendgrid-php/sendgrid-php.php';

		$email = new \SendGrid\Mail\Mail();
		$email->setFrom("info@pridediesel.com", "Example User");
		$email->setSubject("Sending with SendGrid is Fun");
		$email->addTo("jagdish@lastingerp.com", "Example User");
		$email->addContent(
			"text/plain", "and easy to do anywhere, even with PHP"
		);
		$email->addContent(
			"text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
		);
		//$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
		$sendgrid = new \SendGrid(getenv('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'));
		try {
			$response = $sendgrid->send($email);
			print $response->statusCode() . "\n";
			print_r($response->headers());
			print $response->body() . "\n";
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		} */
		//die;
		/* $this->email->initialize(array(
		  'protocol' => 'smtp',
		  'smtp_host' => 'smtp.sendgrid.net',
		  //'smtp_user' => 'apikey',
		  'smtp_user' => 'Adua@pridediesel.com',
		  'smtp_pass' => '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',
		  //'smtp_timeout' => '30',
		  'smtp_port' => 587,
		  //'smtp_port' => 587,
		  'crlf' => "\r\n",
		  'newline' => "\r\n"
		)); */
$config = Array(
		  'protocol' => 'sendmail',
		  'smtp_host' => 'ssl://smtp.sendgrid.net',
		  //'smtp_user' => 'apikey',
		  'smtp_user' => 'Adua@pridediesel.com',
		  'smtp_pass' => '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',
		  //'smtp_timeout' => '30',
		  'smtp_port' => 587,
		  //'smtp_port' => 587,
		  'crlf' => "\r\n",
		  'newline' => "\r\n"
);		
		//$this->load->library('email');
		$this->load->library('email', $config);
// Uncomment next line if you're not using a dependency loader (such as Composer)


/* $email = new Mail();
$email->setFrom("info@pridediesel.com", "Example User");
$tos = [
    "test+jagdish@lastingerp.com" => "Example User1",
    "test+test2@example.com" => "Example User2",
    "test+test3@example.com" => "Example User3"
];
$email->addTos($tos);
$email->setSubject("Sending with Twilio SendGrid is Fun");
$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
$email->addContent(
    "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
);
$sendgrid = new \SendGrid(getenv('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'));
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
} catch (Exception $e) {
    echo 'Caught exception: '.  $e->getMessage(). "\n";
} */		
		//$this->load->library('email', $config);
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		//$this->email->from('jagdish@lastingerp.com');				
		$this->email->from('info@pridediesel.com');				
		$this->email->to($uemail);
		if($userRole == 'admin'){
			//$this->email->cc('H.lakhiann@gmail.com,Guribani8171@gmail.com');
		}
		$this->email->subject('Pride Diesel login verification code');
		
		$message = '<p>Your 2 factor verification code is below:-</p>';
		$message .= "Verification Code: ".$vercode;
		$message .= "<p>Note: Code will expire within 10 minutes</p>";
		$this->email->message($message);
		
		$this->email->send();
		//$this->email->print_debugger();		
	}
	
	public function resend_verification_email(){
		$this->load->library('email');
		$uid = $_POST['uid'];
		$uemail = $_POST['uemail'];
		//$getResults = $this->db->where('id', $uid)->get('users')->row();
		
		$vercode = mt_rand(100000, 999999);
		$this->db->set(['verification_code'=> $vercode, 'ver_code_sent' => date('Y-m-d H:i:s')])->where('id', $uid)->update('users');
		//$this->data['userId'] = $getResults->id;
		//$this->data['userEmail'] = $getResults->company_email;
		
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('info@pridediesel.com');				
		$this->email->to($uemail);
		$this->email->subject('Pride Diesel login verification code');
		
		$message = '<p>Your 2 factor verification code is below:-</p>';
		$message .= "Verification Code: ".$vercode;
		$message .= "<p>Note: Code will expire within 10 minutes</p>";
		$this->email->message($message);
		
		$this->email->send();
		echo "newtokensent";
		//$this->session->set_flashdata('success', 'Verification code sent');
		//redirect('auth/user_authenticate', 'redirect');
		//$this->load->view('authentication', $this->data);	
	}	

	public function validate_ver_code(){
		$uid = $_POST['uid'];
		$uip = $_POST['uip'];
		//$uemail = $this->input->post('uemail');
		$verification_code = $_POST['verification_code'];
		$getResults = $this->db->select('verification_code, ver_code_sent')->where(['id' => $uid])->get('users')->row();
		//pre($this->db->last_query());
			//$this->data['userId'] = $uid;
			//$this->data['userEmail'] = $uemail;
			//$this->data['verificationCode'] = $verification_code;
			//$this->data['userEmail'] = $this->input->post('company_email');		
		if(is_object($getResults)){
			$minutes = round(abs(strtotime($getResults->ver_code_sent) - time()) / 60);
			//$to_time = strtotime(date('Y-m-d H:i:s'));
			//$from_time = strtotime($getResults->ver_code_sent);
			//echo abs($to_time - $from_time) / 60 . " minute";
			//pre($minutes);
			//die;
			//echo json_encode($getResults);
			if($minutes > 9){
				echo "timeexpire";
				//$this->session->set_flashdata('error', 'Verification code expired, kindly resend');
				//redirect(base_url().'auth/validate_ver_code', 'refresh');
				//$this->load->view('authentication', $this->data);
			}else{
				if($verification_code === $getResults->verification_code){
					$this->db->set('ip_address', $uip)->where('id', $uid)->update('users');
					$loginStatus = $this->db->where('id', $uid)->get('users')->row();	
					$sessdata = array(
						'userid' => $uid,
						'user_type' => $loginStatus->role,
						'userdata' => $loginStatus,
						'loggedin' => true
					);
					$this->session->set_userdata($sessdata);					
					//redirect(base_url().'dashboard', 'refresh');
					echo "loggedin";
				}else{
					echo "dontmatch";
					//unset($this->session->set_flashdata('success'));
					//$this->session->set_flashdata('error', 'Verification code does not match.');
					//redirect(base_url().'auth/authentication', 'refresh');
				}
			}
		}
		//$this->load->view('authentication', $this->data);
	}
	
	public function forgot_password(){
		$this->load->view('forgot-password', $this->data);	
	}
	
	public function recover_password($id = null){
		$getUserID = substr($this->uri->segment(3), 7);
		/* if($id){
		
		$getUserID = substr($this->uri->segment(3), 7);
		
		$this->form_validation->set_rules(array(array('field'=>'password', 'label'=>'Password', 'rules'=>'required'), array('field'=>'confirm_password', 'label'=>'Confirm Password', 'rules'=>'required|matches[password]')));
		//$this->form_validation->set_rules('password', 'Password', 'required');
		
		if($this->form_validation->run() == true){
			$updateStatus = $this->auth_model->update_user_pass($getUserID, $newpass);
			
			if($updateStatus == true){
				$this->session->set_flashdata('success_msg', 'Password changed successfully, now you can login.');
				redirect(base_url('auth/recover_password'), 'refresh');
			}
		}
	} */
		$getForgotPassStatus = $this->db->select('forgot_password')->where('id', $getUserID)->get('users')->row();

		if(is_object($getForgotPassStatus) && $getForgotPassStatus->forgot_password == 0){	
			redirect(base_url('auth/'), 'refresh');
		}else{	
			$this->load->view('recover-password', $this->data);
		}
	}	
	
	public function send_email(){
		$this->load->library('email');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		
		if($this->form_validation->run() == true){
			$email_exists = $this->auth_model->validate_email($this->input->post('email'));
			
			if(!empty($email_exists)){
				$randomKey = substr(md5(mt_rand()), 0, 7);
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from('info@pridediesel.com');		
				$this->email->to($this->input->post('email'));
				$this->email->subject('Forgot Password Email');
				
				$message = '<p>If you want to change/recover the password so please click and follow the link below:-</p>';
				$message .= "<a href='".base_url('auth/recover_password/').$randomKey.$email_exists[0]->id."'>Click here to recover password</a>";
				$this->email->message($message);
				
				if($this->email->send()){
					$this->db->set('forgot_password', 1)->where('id', $email_exists[0]->id)->update('users');
					$this->session->set_flashdata('success_msg', 'Password recovery link is send, check your email.');
					redirect(base_url('auth/forgot_password'), 'refresh');
				}
			}else{
				$this->session->set_flashdata('error_msg', 'Email id does not exist');
				redirect(base_url('auth/forgot_password'), 'refresh');				
			}
			
		}else{
			$this->session->set_flashdata('error_msg', 'Please Enter email id');
			redirect(base_url('auth/forgot_password'), 'refresh');
		}		
	}	
	
	public function update_password($id = null){
		if($id){
		
		$getUserID = substr($this->uri->segment(3), 7);

		$this->form_validation->set_rules(array(array('field'=>'password', 'label'=>'Password', 'rules'=>'required'), array('field'=>'confirm_password', 'label'=>'Confirm Password', 'rules'=>'required|matches[password]')));
		
		if($this->form_validation->run() == true){
			$updateStatus = $this->auth_model->update_user_pass($getUserID, md5($this->input->post('password')));
			$this->db->set('forgot_password', 0)->where('id', $getUserID)->update('users');
			if($updateStatus == true){
				$this->session->set_flashdata('success_msg', 'Password changed successfully, now you can login.');
				redirect(base_url('auth'), 'refresh');
				//redirect(base_url('auth/recover_password'), 'refresh');
				//header( "refresh:3;url=base_url(auth)" );
			}
		}else{
			$this->session->set_flashdata('error_msg', validation_errors());
			redirect(base_url('auth/recover_password/'.$id), 'refresh');
		}
	}
	//$this->load->view('recover-password', $this->data);

	}
	
	public function logout(){
		$this->session->sess_destroy();
		redirect( base_url('auth/'), 'refresh');
	}
}