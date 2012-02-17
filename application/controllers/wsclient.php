<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class WSClient extends CI_Controller
{

    function index()
    {
        phpinfo();
    }

    public function __construct()
    {
        parent::__construct();

        $this->load->library("Nusoap_lib");
    }
    
    
    function client1()
    {
        $this->nusoap_client = new nusoap_client('http://sfp.inet/wsserver/server1/wsdl');
//        $this->nusoap_client = new nusoap_client('http://sfp.inet/wsserver/server1');
        $this->nusoap_client->soap_defencoding = 'utf-8'; 
        $this->nusoap_client->decode_utf8 = false; 
        $this->nusoap_client->xml_encoding = 'utf-8';
//        var_dump($this->nusoap_client);
//        die('testando');

        $err = $this->nusoap_client->getError();
        if ($err)
        {
            echo '<p> <b> Constructor error:' . $err . '</b> </p>';
        }
        $result = $this->nusoap_client->call(
                'Hello', 
                array('name' => ' Mundão'), 
                'Uri:soap/server',
                'Uri:soap/server/hello'
                );

        if ($this->nusoap_client->fault)
        {
            echo '<p> <b> Fault:';
            print_r($result);
            echo '</b> </p>';
        }
        else
        {
            print_r($result);
        }

        echo "<br />";
        echo '<p> Request: <br />';
        echo '<pre>', htmlspecialchars($this->nusoap_client->request, ENT_QUOTES), '</pre> ';
        echo '<br /> Response: <br />';
        echo '<pre>', htmlspecialchars($this->nusoap_client->response, ENT_QUOTES), '</pre> </p> ';
        echo "<br />";

        //Display the debug messages 
        echo '<h2> Debug </h2>';
        echo '<pre>' . htmlspecialchars($this->nusoap_client->debug_str, ENT_QUOTES) . '</pre>';
    }

//    function primteste()
//    {
//
////        $this->nusoap_client = new nusoap_client("http://www.scottnichol.com/samples/hellowsdl2.php?wsdl&debug=1", 'wsdl');
////        $person = array('firstname' => 'Tom', 'age' => -12, 'gender' => 'male');
////        $result = $this->nusoap_client->call('hello', array('person' => $person), '', '', false, true);
//
//        $this->nusoap_client = new nusoap_client('http://www.scottnichol.com/samples/helloutf8.php?wsdl', 'wsdl');
//
//
//        $this->nusoap_client->soap_defencoding = 'UTF-8';
//        $client->decode_utf8 = false;
//        $utf8string = array('stuff' => "\xc2\xa9\xc2\xae\xc2\xbc\xc2\xbd\xc2\xbe");
//        $result = $this->nusoap_client->call('echoback', $utf8string);
//
//        if ($this->nusoap_client->fault)
//        {
//            echo '<h2>Fault</h2><pre>';
//            print_r($result);
//            echo '</pre>';
//        }
//        else
//        {
//            $err = $this->nusoap_client->getError();
//            if ($err)
//            {
//                echo '<h2>Error</h2><pre>' . $err . '</pre>';
//            }
//            else
//            {
//                echo '<h2>Result</h2><pre>';
//                print_r($result);
//                echo '</pre>';
//            }
//        }
//    }
//
//    public function segteste()
//    {
//
//        $this->nusoap_client = new nusoap_client('http://sfp.inet/wsserver/primserver?wsdl', true);
//        // Create the client instance
////        $this->nusoap_client = new soapclient('http://sfp.inet/wsserver/index?wsdl', true);
//        // Check for an error
//        $err = $this->nusoap_client->getError();
//        if ($err)
//        {
//            // Display the error
//            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
//            // At this point, you know the call that follows will fail
//        }
//        // Call the SOAP method
//        $result = $this->nusoap_client->call('hello', array('name' => 'Scott'));
//        // Check for a fault
//        if ($this->nusoap_client->fault)
//        {
//            echo '<h2>Fault</h2><pre>';
//            print_r($result);
//            echo '</pre>';
//        }
//        else
//        {
//            // Check for errors
//            $err = $this->nusoap_client->getError();
//            if ($err)
//            {
//                // Display the error
//                echo '<h2>Error</h2><pre>' . $err . '</pre>';
//            }
//            else
//            {
//                // Display the result
//                echo '<h2>Result</h2><pre>';
//                print_r($result);
//                echo '</pre>';
//            }
//        }
//        // Display the request and response
//        echo '<h2>Request</h2>';
//        echo '<pre>' . htmlspecialchars($this->nusoap_client->request, ENT_QUOTES) . '</pre>';
//        echo '<h2>Response</h2>';
//        echo '<pre>' . htmlspecialchars($this->nusoap_client->response, ENT_QUOTES) . '</pre>';
//        // Display the debug messages
//        echo '<h2>Debug</h2>';
//        echo '<pre>' . htmlspecialchars($this->nusoap_client->debug_str, ENT_QUOTES) . '</pre>';
//    }
//
//    public function primclient()
//    {
//        $client = new soapclient('http://sfp.inet/wsserver/primserver');
//        $result = $client->call('hello', array('Tom'));
//        print_r($result);
//    }
//
//    public function segclient()
//    {
//        $wsdl = "http://sfp.inet/wsserver/segserver?wsdl";
//        $client = new nusoap_client($wsdl, 'wsdl');
//
//        $err = $client->getError();
//        if ($err)
//        {
//            echo "Erro no construtor<pre>{$err}</pre>";
//        }
//
//        $result = $client->call('hello', array('Tom'));
//
//        if ($client->fault)
//        {
//            echo "Falha<pre>" . print_r($result) . "</pre>";
//        }
//        else
//        {
//            $err = $client->getError();
//            if ($err)
//            {
//                echo "Erro <pre>{$err}</pre>";
//            }
//            else
//            {
//                print_r($result);
//            }
//        }
//    }
//
//    public function terclient()
//    {
//        $api_url = "http://app.x.com/webservicefordiscuz/Service.asmx";
//        $this->nusoap_client = new nusoap_client($api_url);
//        $this->nusoap_client->soap_defencoding = 'utf-8';
//        $this->nusoap_client->decode_utf8 = false;
//        $this->nusoap_client->xml_encoding = 'utf-8';
//        
//    }

}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */