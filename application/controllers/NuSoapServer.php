<?php
class NuSoapServer extends CI_Controller {
	//var $server;
    //var $endPoint;	
	function __construct() {
		parent::__construct();
		$this->load->library("nuSoap_lib");
		$this->nusoap_server = new soap_server();
		//$this->nusoap_server->configureWSDL("cartWSDL", "urn:cartWSDL");
		$this->nusoap_server->configureWSDL("MySoapServer", "urn:MySoapServer");

        /* $this->nusoap_server->register(
                "echoTest", array("tmp" => "xsd:string"), array("return" => "xsd:string"), "urn:MySoapServer", "urn:MySoapServer#echoTest", "rpc", "encoded", "Echo test"
        );
		
        function echoTest($tmp) {
            if (!$tmp) {
                return new soap_fault('-1', 'Server', 'Parameters missing for echoTest().', 'Please refer documentation.');
            } else {
                return "from MySoapServer() : $tmp";
            }
        } */		
        //$this->server = 'https://the-other-company.com/services/PksiryacwebMgrTarjetasSubsi?wsdl';
        //$this->endPoint = 'http://172.20.8.152/services/PksiryacwebMgrTarjetasSubsi';			
        //$this->server = 'https://test.efsllc.com/richapp/Wsdl.action?wsdl=/axis2/services/CardManagementWS';	
        //$this->endPoint = 'https://test.efsllc.com/axis2/services/CardManagementWS/';		
		/* $this->nusoap_server->wsdl->addComplexType(
			"Member",
			"complexType",
			"array",
			"",
			"SOAP-ENC:Array",
			array(
				"id"=>array("name"=>"id", "type"=>"xsd:int"),
				"first_name"=>array("name"=>"first_name", "type"=>"xsd:string"),
				"surname"=>array("name"=>"surname", "type"=>"xsd:string")
				)
		); 
		$this->nusoap_server->register(
			"getMember",
			array(
			"id" => "xsd:int",
		),
		array("return"=>"tns:Member"),
			"urn:cartWSDL",
			"urn:cartWSDL#getMember",
			"rpc",
			"encoded",
			"Returns the information of a certain member"
		); */
		
		/* $this->nusoap_server->register(
			"getCard",
			array(
			"id" => "xsd:int",
		),
		array("return"=>"tns:card"),
			"urn:CardManagementWSDL",
			"urn:CardManagementWSDL#getCard",
			"rpc",
			"encoded",
			"Returns the information of a certain member"
		); */		
	}
	
    /* function client_process($data){

        $cliente = new nusoap_client($this->server,'wsdl','','','','');
        $cliente->setEndpoint($this->endPoint);

        $err = $cliente->getError();
        if ($err) { echo 'Error en Constructor' . $err ; }

        $response = $cliente->call('getCard',$data,'','', false,true);  //OK
		print_r($response);
        //return $response;
    } */	
	function index() {
		if($this->uri->segment(3) == "wsdl") {
			$_SERVER['QUERY_STRING'] = "wsdl";
		} else {
			$_SERVER['QUERY_STRING'] = "";
		}
		//$this->nusoap_server->service(file_get_contents("php://input"));
		$this->nusoap_server->service(file_get_contents("php://input"));
	 }
	 
	 
}
?>