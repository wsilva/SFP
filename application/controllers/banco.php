<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Banco extends CI_Controller
{

    //registros por página
    private $limit = 5;

    public function index()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        //pegando parametro da paginação
        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        //carregando model
        $this->load->model('BancoModel');

        $bancos = $this->BancoModel->buscartodos();
        $bancos_pag = $this->BancoModel->buscarporqtde($this->limit, $offset);

        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('banco/index');
        $config['total_rows'] = sizeof($bancos);
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        //para montar tabelas html
        $this->load->library('table');

        //para trabalhar com datas
        $this->load->helper('date');

        //table header
        $tablearr[] = array('N°', 'Nome', 'Criação', 'Última alteração', '');

        //varrendo bancos
        foreach ($bancos_pag as $banco)
        {


            //ações
            $actions = "<a href='/banco/alterar/{$banco->id}' >editar</a>";
            $actions .= " | <a href=\"javascript:removeConfirmation({$banco->id})\" >remover</a>";

            //create update
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($banco->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($banco->dt_alteracao));

            //populando html table
            $tablearr[] = array($banco->id, $banco->nome, $created, $updated, $actions);
        }


        //definindo abertura da tag table
        $table_tmpl = array('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //guardando no array da view
        $data['tabela_bancos'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('banco/listar');
        $this->load->view('tmpl/footer');
    }

    public function novo()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('banco/novo');
        $this->load->view('tmpl/footer');
    }

    public function grava_novo()
    {

        $this->auth->check_logged($this->router->class, $this->router->method);

        $this->load->library('form_validation');

        # validações
        $validacoes = array(
            array(
                'field' => 'id',
                'label' => 'Número',
                'rules' => 'trim|required|callback_exists|min_length[1]|max_length[3]|xss_clean'
            ),
            array(
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'trim|required|min_length[5]|max_length[100]|xss_clean'
            ),
            array(
                'field' => 'descricao',
                'label' => 'Descrição',
                'rules' => 'trim|required|min_length[1]|max_length[255]|xss_clean'
            )
        );
        $this->form_validation->set_rules($validacoes);

        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('numeric', 'O campo <strong>%s</strong> deve conter apenas números');
        $this->form_validation->set_message('alpha_numeric', 'O campo <strong>%s</strong> deve ter apenas letras e/ou números');
        $this->form_validation->set_message('alpha_dash', 'O campo <strong>%s</strong> deve ter apenas letras, números, ou os caracteres sublinhado (_) e traço (-).');
        $this->form_validation->set_message('valid_email', 'O campo <strong>%s</strong> deve ter um endereço de e-mail válido');
        $this->form_validation->set_message('matches', 'Os campos <strong>%s</strong> e <strong>%s</strong> não conferem.');

        # definindo delimitadores
        $this->form_validation->set_error_delimiters('<li class="submiterror">', '</li>');

        # não passou na validação
        if ($this->form_validation->run() == FALSE)
        {
            $this->novo();
        }

        #passou na validação
        else
        {

            # carregando model
            $this->load->model('BancoModel');

            # criando o objeto Banco
            $banco = new BancoModel();

            # populando obj Banco
            $banco->id = $this->input->post('id');
            $banco->nome = $this->input->post('nome');
            $banco->descricao = $this->input->post('descricao');
            $banco->dt_cadastro = date('Y-m-d H:i:s');
            $banco->dt_alteracao = $banco->dt_cadastro;

            # gravando dados no banco
            if ($banco->grava_novo())
            {
                $mensagens = array('notice' => 'Banco criado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao criar banco.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'banco', 'refresh');
            exit();
        }
    }

    function exists($banco_id)
    {
        
        $this->load->model('BancoModel');
        $banco = new BancoModel($banco_id);
        $exists = (boolean) $banco->id;

        if ($exists)
        {
            $this->form_validation->set_message('exists', 'O n° <strong>'.$banco_id.'</strong> já existe.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function alterar()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        //pegando id
        $banco_id = $this->uri->segment(3);

        # carregando model
        $this->load->model('BancoModel');

        # criando o objeto
        $banco = new BancoModel($banco_id);
        $data['banco'] = $banco;


        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('banco/alterardados');
        $this->load->view('tmpl/footer');
    }

    public function remover()
    {
        $this->load->library('form_validation');

        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        //pegando id
        $banco_id = $this->uri->segment(3);

        # carregando model
        $this->load->model('BancoModel');

        # criando o objeto banco
        $banco = new BancoModel($banco_id);

        # removendo no banco
        if ($banco->remove())
        {
            $mensagens = array('notice' => 'Banco removido com sucesso.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # erro ao remover dados
        else
        {
            $mensagens = array('error' => 'Erro ao remover banco.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # redirecionando
        redirect(base_url() . 'banco', 'refresh');
        exit();
    }

    public function grava_dados()
    {
        $this->load->library('form_validation');

        # validações
        $validacoes = array(
            array(
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'trim|required|min_length[5]|max_length[100]|xss_clean'
            ),
            array(
                'field' => 'descricao',
                'label' => 'Descrição',
                'rules' => 'trim|required|min_length[1]|max_length[255]|xss_clean'
            )
        );
        $this->form_validation->set_rules($validacoes);

        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('numeric', 'O campo <strong>%s</strong> deve conter apenas números');
        $this->form_validation->set_message('alpha_dash', 'O campo <strong>%s</strong> deve ter apenas letras e/ou números');
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
            $this->load->model('BancoModel');

            # criando o objeto
            $banco = new BancoModel($this->input->post('id'));

            # populando obj usuário
            $banco->id = $this->input->post('id');
            $banco->nome = $this->input->post('nome');
            $banco->descricao = $this->input->post('descricao');
            $banco->dt_alteracao = date('Y-m-d H:i:s');

            # gravando dados no banco
            if ($banco->grava())
            {
                $mensagens = array('notice' => 'Banco gravado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao gravar Banco.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'banco', 'refresh');
            exit();
        }
    }

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file banco.php */
/* Location: ./application/controllers/banco.php */