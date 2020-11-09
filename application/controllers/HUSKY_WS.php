<?php
set_time_limit(0);
class HUSKY_WS extends CI_controller {
    function __construct() {
        parent::__construct();

        $this->load->library("NuSoap_lib");
        //$this->load->model("Member");

		//Configure HUSKY
        $this->nusoap_server = new soap_server();
        //$this->nusoap_server->configureWSDL("Bills_WSDL", "urn:Bills_WSDL");
        /* $this->nusoap_server->configureWSDL("CardManagementWS", "urn:CardManagementWS", 'https://test.efsllc.com/axis2/services/CardManagementWS/'); */
		/*Live URL endpoint*/
        $this->nusoap_server->configureWSDL("FleetCreditWS0200", "fc:FleetCreditWS0200", 'https://api.iconnectdata.com:443/FleetCreditWS/services/FleetCreditWS0200');		

		//Get Single Card Data
		$this->nusoap_server->register('cardListing',                // method name
            array('accountCode' => 'xsd:string', 'customerId' => 'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'fc:FleetCreditWS0200', // namespace
            'fc:FleetCreditWS0200#cardListing', // soapaction
            'rpc',                                // style rpc||document
            'literal'                            // use encoded||literal 
        );
		
    }

    function index(){

        if($this->uri->rsegment(3) == "wsdl") {
            $_SERVER['QUERY_STRING'] = "wsdl";
        } else {
            $_SERVER['QUERY_STRING'] = "";
        }        

        $this->nusoap_server->service(file_get_contents("php://input"));
    }

}