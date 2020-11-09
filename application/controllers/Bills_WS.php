<?php
class Bills_WS extends CI_controller {
		//var $cid = 'PvK2FjSAmXnODMmu0W0M1KZeyTJublRj';
		//var $cnum = '7083052035236900017';
    function __construct() {
        parent::__construct();

        $this->load->library("Nusoap_lib");
        $this->load->model("Member");

        $this->nusoap_server = new soap_server();
        //$this->nusoap_server->configureWSDL("Bills_WSDL", "urn:Bills_WSDL");
        $this->nusoap_server->configureWSDL("CardManagementWS", "urn:CardManagementWS", 'https://test.efsllc.com/axis2/services/CardManagementWS/');

		/* $this->nusoap_server->register(
                "echoTest", array("tmp" => "xsd:string"), array("return" => "xsd:string"), "urn:CardManagementWS", "urn:CardManagementWS#echoTest", "rpc", "encoded", "Echo test"
        ); */
		
        /* function echoTest($tmp) {
            if (!$tmp) {
                return new soap_fault('-1', 'Server', 'Parameters missing for echoTest().', 'Please refer documentation.');
            } else {
                return "from CardManagementWS() : $tmp";
            }
        } */
        /* $this->nusoap_server->register('hello',                // method name
            array('name' => 'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:Bills_WSDL',                      // namespace
            'urn:Bills_WSDL#hello',                // soapaction
            'rpc',                                // style
            'encoded',                            // use
            'Says hello to the caller'            // documentation
        ); */
		$this->nusoap_server->register('getCard',                // method name
            array('clientId' => 'xsd:string','cardNumber' => 'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#getCard',                // soapaction
            'rpc',                                // style
            'encoded',                            // use
            'Says hello to the caller'            // documentation
        );		
    }

    function index(){

        if($this->uri->rsegment(3) == "wsdl") {
            $_SERVER['QUERY_STRING'] = "wsdl";
        } else {
            $_SERVER['QUERY_STRING'] = "";
        }        

        function hello($name) {
                return 'Hello, ' . $name;
        }        
		function getCard($cid, $cnum) {
                return 'Hello, ' . $cid;
        }
        $this->nusoap_server->service(file_get_contents("php://input"));
    }

}