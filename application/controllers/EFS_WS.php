<?php
set_time_limit(0);
class EFS_WS extends CI_controller {
    function __construct() {
        parent::__construct();

        $this->load->library("NuSoap_lib");
        //$this->load->model("Member");

		//Configure EFS
        $this->nusoap_server = new soap_server();
        //$this->nusoap_server->configureWSDL("Bills_WSDL", "urn:Bills_WSDL");
        /* $this->nusoap_server->configureWSDL("CardManagementWS", "urn:CardManagementWS", 'https://test.efsllc.com/axis2/services/CardManagementWS/'); */
		/*Live URL endpoint*/
        $this->nusoap_server->configureWSDL("CardManagementWS", "urn:CardManagementWS", 'https://ws.efsllc.com/axis2/services/CardManagementWS/');		
		
		//Login
		$this->nusoap_server->register('login',                // method name
            array('user' => 'xsd:string','password' => 'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#login',                // soapaction
            'rpc',                                // style
            'encoded',                            // use
            'Says hello to the caller'            // documentation
        );
		//Get Single Card Data
		$this->nusoap_server->register('getCard',                // method name
            array('clientId' => 'xsd:string','cardNumber' => 'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#getCard',                // soapaction
            'rpc',                                // style
            'encoded'                            // use
        );
		//Get Card Summaries
		$this->nusoap_server->register('getCardSummaries',                // method name
            array('clientId' => 'xsd:string','request' => 'ns2:WSCardSummaryReq'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#getCardSummaries',                // soapaction
            'rpc',                                // style
            'encoded'                            // use
        );

		//Get Transaction Summaries
		$this->nusoap_server->register('getMCTransExtLocV2',                // method name
            array('clientId' => 'xsd:string','begDate' => 'xsd:dateTime', 'endDate'=>'xsd:dateTime'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#getMCTransExtLocV2',                // soapaction
            'rpc',                                // style
            'encoded'                            // use
        );
		//Set Card PIN
		$this->nusoap_server->register('setCardPin',                // method name
            array('clientId' => 'xsd:string','cardNum' => 'xsd:string', 'newPin'=>'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#setCardPin',                // soapaction
            'rpc',                                // style
            'encoded'                            // use
        );
		//Set Card PIN
		$this->nusoap_server->register('setCard',                // method name
            array('clientId' => 'xsd:string','card' => 'ns2:WSCard'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#setCard',                // soapaction
            'rpc',                                // style
            'literal'                            // use(encoded|literal)
        );
		//Get Single Card Data
		$this->nusoap_server->register('getProducts',                // method name
            array('clientId' => 'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#getProducts',                // soapaction
            'rpc',                                // style
            'encoded'                            // use
        );

		//Create money code
		$this->nusoap_server->register('issueMoneyCode',                // method name
            array('clientId' => 'xsd:string', 'contractId' => 'xsd:int', 'masterContractId' => 'xsd:int', 'amount' => 'xsd:decimal', 'feeType' => 'xsd:boolean', 'issuedTo' => 'xsd:string', 'notes' => 'xsd:string', 'currency' => 'xsd:string'),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#getProducts',                // soapaction
            'rpc',                                // style
            'encoded'                            // use
        );		
/* 		$this->nusoap_server->register('setCard',                // method name
            array('clientId' => 'xsd:string','card'=>array('cardNumber' => 'xsd:string', 'header'=>array('companyXRef'=>'xsd:string', 'handEnter'=>'xsd:string', 'infoSource'=>'xsd:string', 'limitSource'=>'xsd:string', 'locationOverride'=>'xsd:string', 'locationSource'=>'xsd:string', 'overrideAllLocations'=>'xsd:string', 'originalStatus'=>'xsd:string', 'payrollStatus'=>'xsd:string', 'override'=>'xsd:string', 'policyNumber'=>'xsd:string', 'status'=>'xsd:string', 'timeSource'=>'xsd:string', 'lastUsedDate'=>'xsd:string', 'lastTransaction'=>'xsd:string', 'payrollUse'=>'xsd:string', 'payrollAtm'=>'xsd:string', 'payrollChk'=>'xsd:string', 'payrollAch'=>'xsd:string', 'payrollWire'=>'xsd:string', 'payrollDebit'=>'xsd:string'), 'locationGroups'=>'xsd:string')),        // input parameters
            array('return' => 'xsd:string'),      // output parameters
            'urn:CardManagementWS',                      // namespace
            'urn:CardManagementWS#setCard',                // soapaction
            'rpc',                                // style
            'encoded'                            // use
        ); */		
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