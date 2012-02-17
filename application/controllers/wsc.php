<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class WSC extends CI_Controller
{

    public function index()
    {
        phpinfo();
    }

    public function primclient()
    {
        $client = new SoapClient(null, array(
                    'location' => 'http://sfp.inet/wsc/primserver',
                    'uri' => "http://sfp.inet/wss",
                    'trace' => 1
                ));
        
        $result = $client->ola('Tom');
        
        var_dump($result);
        die('testando');
        
        if(is_soap_fault($result))
        {
            trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, 
            faultstring: {$result->faultstring})", E_ERROR);
        }
        else
        {
            echo "Resultado: <br/><br/>";
            print_r($result);
        }
    }

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */