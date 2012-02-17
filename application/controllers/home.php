<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
//        $this->load->helper('logs');
        $this->load->helper('cookie');
    }

    public function index()
    {
        $data = array();
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');
        
        if( $this->session->userdata('logged_in') )
        {

            $this->load->view('tmpl/header',$data);
            $this->load->view('logado');
            $this->load->view('tmpl/footer');        
        }
        else
        {
            $this->load->view('tmpl/header', $data);
            $this->load->view('bemvindo');
            $this->load->view('tmpl/footer');
        }
    }


    public function sempermissao()
    {

        $mensagens = array('error'=>'Você não tem permissão para acessar esta funcionalidade.');
        $this->session->set_flashdata('mensagens', $mensagens);
        redirect(base_url() . 'home', 'refresh');
        exit();
    }

    public function login()
    {
        $data = array();
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');
        
        $this->load->view('tmpl/header', $data);
        $this->load->view('login');
        $this->load->view('tmpl/footer');
    }

    public function dologin()
    {
        $usuario = $this->input->post('usuario');
        $senha = md5($this->input->post('senha'));
        if ($usuario == ""  || $this->input->post('senha') == "")
        {
            $mensagens = array('error'=>'Usuário e senha devem ser informados.');
            $this->session->set_flashdata('mensagens', $mensagens);
            redirect(base_url() . 'home/login', 'refresh');
            exit();
        }
        if (isset($_POST['lembrar']))
        {
            setcookie("usuario", $usuario);
            setcookie("lembrar", "checked");
        }
        $sql = "SELECT 
                    id,usuario,nome,email
                FROM usuarios
                WHERE usuario ='" . $usuario . "'
                    AND senha ='" . $senha . "'";
        
        $query = $this->db->query($sql);
        $result = $query->result();
        
        if (count($result) < 1)
        {
            $mensagens = array('error'=>'Usuário e ou senha incorretos.');
            $this->session->set_flashdata('mensagens', $mensagens);
            redirect(base_url() . 'home/login', 'refresh');
            exit();
        }
        
        else
        {
            $login = array(
                'usuario_id' => $result[0]->id,
                'usuario' => $result[0]->usuario,
                'nome' => $result[0]->nome,
                'email' => $result[0]->email,
                'logged_in' => TRUE,
                'data' => date("d/m/Y h:i:s")
            );
            $data['ip'] = getenv("REMOTE_ADDR");
            $data['usuario_id'] = $result[0]->id;
            $data['dt'] = date('Y-m-d H:i:s');
            $this->db->insert('acessos', $data);
            $this->session->set_userdata($login);
            redirect(base_url() . 'home', 'refresh');
        }
    }

    public function logout()
    {
        unset($this->session->userdata);
        $this->session->sess_destroy();
        $mensagens = array('notice'=>'Logout realizado com sucesso.');
        $this->session->set_flashdata('mensagens', $mensagens);
//        $this->login();
        redirect(base_url() . 'home/login', 'refresh');
        exit();
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */