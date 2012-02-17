<?php


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Metodo extends CI_Controller
{
    
    public function alterar()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do metodo
        $metodo_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('MetodoModel');

        # criando o objeto metodo
        $metodo = new MetodoModel($metodo_id);
        $data['metodo'] = $metodo;
        
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('metodo/alterardados');
        $this->load->view('tmpl/footer');
    }
    
    public function grava_dados()
    {
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'classe',
                'label' => 'Primeiro Parâmetro',
                'rules' => 'trim|required|alpha_dash|min_length[1]|max_length[50]|xss_clean'
            ),
            array(
                'field' => 'metodo',
                'label' => 'Segundo Parâmetro',
                'rules' => 'trim|required|alpha_dash|min_length[1]|max_length[50]|xss_clean'
            ),
            array(
                'field' => 'apelido',
                'label' => 'Nome da Permissão',
                'rules' => 'trim|required|min_length[5]|max_length[255]|xss_clean'
            )
        );
        $this->form_validation->set_rules($validacoes);
        
        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('alpha_dash', 'O campo <strong>%s</strong> deve ter apenas letras e/ou números e/ou os caracteres sublinhado (_) e traço (-).');
        $this->form_validation->set_message('valid_email', 'O campo <strong>%s</strong> deve ter um endereço de e-mail válido');
        
        # definindo delimitadores
        $this->form_validation->set_error_delimiters('<li class="submiterror">', '</li>');
        
        # não passou na validação
        if ($this->form_validation->run() == FALSE)
        {
            $this->alterar();
        }
        
        #passou na validação
        else
        {
            
            # carregando model
            $this->load->model('MetodoModel');
            
            # criando o objeto metodo
            $metodo = new MetodoModel($this->input->post('metodo_id'));
            
            # populando obj metodo
            $metodo->classe = $this->input->post('classe');
            $metodo->metodo = $this->input->post('metodo');
            $metodo->apelido = $this->input->post('apelido');
            $metodo->privado = $this->input->post('privado');
            
            # gravando dados no banco
            if( $metodo->grava() )
            {
                $mensagens = array('notice'=>'Metodo gravado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao gravar metodo.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'usuario', 'refresh');
            exit();
            
        }
    }
    
    public function __construct()
    {
        parent::__construct();
    }
}
