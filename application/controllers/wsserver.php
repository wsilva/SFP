<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class WSServer extends CI_Controller
{

    public function index()
    {
        phpinfo();
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Nusoap_lib');
    }

    function server1()
    {

        $this->nusoap_server = new soap_server();
//        $this->nusoap_server->configureWSDL('server.hello', 'urn:server.hello');
//        $this->nusoap_server->wsdl->schemaTargetNamespace = 'uri:server.hello';
//        $this->nusoap_server->register(
//                'hello', 
//                array('name' => 'xsd:string'), 
//                array('return' => 'xsd:string'), 
//                'uri:server/hello', 
//                'uri:server/hello#hello', 
//                'rpc', 
//                'encoded', 
//                'Retorna o nome'
//        );
        
        $this->nusoap_server->register(
                'Hello', 
                array('Name' => 'xsd: string'), 
                array('return' => 'xsd: string'), 
                'Uri: soap/server', 
                'Uri: soap/server/hello', 
                'Rpc', 
                'Encoded',
                'Retorna o nome'
        );

//        var_dump($this->uri->segment(3));
//        die('testando');
        if ($this->uri->segment(3) == "wsdl")
        {
            $_SERVER ['QUERY_STRING'] = "wsdl";
        }
        else
        {
            $_SERVER ['QUERY_STRING'] = "";
        }

        function hello($name)
        {
            return utf8_decode('E aí, ').$name.' !!!';
        }

        $this->nusoap_server->service(file_get_contents('php://input'));
        exit;
    }


//    public function primserver()
//    {
//        $server = new soap_server;
//
//        // Register the method to expose
//        $server->register('hello');
//
//        // Define the method as a PHP function
//        function hello($name)
//        {
//            return 'Hello, ' . $name;
//        }
//
//        // Use the request to (try to) invoke the service
//        $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
//
//        $server->service($HTTP_RAW_POST_DATA);
//    }
//
//    public function segserver()
//    {
//        $server = new soap_server;
//
//        // Register the method to expose
//        $server->configureWSDL('server.hello', 'urn:server.hello');
//        $server->wsdl->schemaTargetNamespace = 'urn:server.hello';
//
//        $server->register('hello', array('name' => 'xsd:string'), array('return' => 'xsd:string'), 'urn:server.hello', 'urn:server.hello#hello', 'rpc', 'encoded', 'Retorna o nome'
//        );
//
//        // Define the method as a PHP function
//        function hello($name)
//        {
//            return 'E ai, ' . $name;
//        }
//
//        // Use the request to (try to) invoke the service
//        $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
//
//        $server->service($HTTP_RAW_POST_DATA);
//    }
}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */