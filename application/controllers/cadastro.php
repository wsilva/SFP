<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cadastro extends CI_Controller {

	
	public function index()
	{
            $this->auth->check_logged($this->router->class , $this->router->method);
            
            $data = array();

            $this->load->view('tmpl/header', $data);
            $this->load->view('cadastro/index');
            $this->load->view('tmpl/footer');

	}
	
	public function cadastro()
	{
            $this->auth->check_logged($this->router->class , $this->router->method);
            
            $data = array();

            $this->load->view('tmpl/header', $data);
            $this->load->view('cadastro');
            $this->load->view('tmpl/footer');

	}
	
        
        
        public function __construct()
        {
            parent::__construct();
        }
}

/* End of file cadastro.php */
/* Location: ./application/controllers/cadastro.php */