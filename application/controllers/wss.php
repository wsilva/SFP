<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class WSS extends CI_Controller
{

    public function index()
    {
        phpinfo();
    }

    public function primserver()
    {
        $server = new SoapServer(null, array('uri' => "http://sfp.inet/wss"));

        function ola($texto)
        {
            return 'Olá ' . $texto;
        }

        $server->addFunction('ola');

        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $server->handle();
        }
        else
        {
            $functions = $server->getFunctions();

            foreach ($functions as $function)
            {
                print $function . "<br/>";
            }
        }
    }

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */