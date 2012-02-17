<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categoria extends CI_Controller
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
        $this->load->model('CategoriaModel');

        $categorias = $this->CategoriaModel->buscartodas();
        $categorias_pag = $this->CategoriaModel->buscarporqtde($this->limit, $offset);

        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('categoria/index');
        $config['total_rows'] = sizeof($categorias);
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        //para montar tabelas html
        $this->load->library('table');

        //para trabalhar com datas
        $this->load->helper('date');

        //table header
        $tablearr[] = array('Título', 'Criação', 'Última alteração', '');

        //varrendo categorias
        foreach ($categorias_pag as $categoria)
        {


            //ações
            $actions = "<a href='/categoria/alterar/{$categoria->id}' >editar</a>";
            $actions .= " | <a href=\"javascript:removeConfirmation({$categoria->id})\" >remover</a>";

            //create update
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($categoria->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($categoria->dt_alteracao));

            //populando html table
            $tablearr[] = array($categoria->titulo, $created, $updated, $actions);
        }


        //definindo abertura da tag table
        $table_tmpl = array('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //guardando no array da view
        $data['tabela_categorias'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('categoria/listar');
        $this->load->view('tmpl/footer');
    }

    public function nova()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('categoria/nova');
        $this->load->view('tmpl/footer');
    }

    public function grava_nova()
    {

        $this->auth->check_logged($this->router->class, $this->router->method);

        $this->load->library('form_validation');

        # validações
        $validacoes = array(
            array(
                'field' => 'titulo',
                'label' => 'Título',
                'rules' => 'trim|required|min_length[5]|max_length[255]|xss_clean'
            )
        );
        $this->form_validation->set_rules($validacoes);

        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('alpha_numeric', 'O campo <strong>%s</strong> deve ter apenas letras e/ou números');
        $this->form_validation->set_message('alpha_dash', 'O campo <strong>%s</strong> deve ter apenas letras, números, ou os caracteres sublinhado (_) e traço (-).');
        $this->form_validation->set_message('valid_email', 'O campo <strong>%s</strong> deve ter um endereço de e-mail válido');
        $this->form_validation->set_message('matches', 'Os campos <strong>%s</strong> e <strong>%s</strong> não conferem.');

        # definindo delimitadores
        $this->form_validation->set_error_delimiters('<li class="submiterror">', '</li>');

        # não passou na validação
        if ($this->form_validation->run() == FALSE)
        {
            $this->nova();
        }

        #passou na validação
        else
        {

            # carregando model
            $this->load->model('CategoriaModel');

            # criando o objeto Categoria
            $categoria = new CategoriaModel();

            # populando obj Categoria
            $categoria->titulo = $this->input->post('titulo');
            $categoria->dt_cadastro = date('Y-m-d H:i:s');
            $categoria->dt_alteracao = $categoria->dt_cadastro;

            # gravando dados no banco
            if ($categoria->grava())
            {
                $mensagens = array('notice' => 'Categoria criada com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao criar categoria.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'categoria', 'refresh');
            exit();
        }
    }
    
    
    public function alterar()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id
        $categoria_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('CategoriaModel');

        # criando o objeto
        $categoria = new CategoriaModel($categoria_id);
        $data['categoria'] = $categoria;
        
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('categoria/alterardados');
        $this->load->view('tmpl/footer');
    }
    
    public function remover()
    {
        $this->load->library('form_validation');
        
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id
        $categoria_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('CategoriaModel');

        # criando o objeto categoria
        $categoria = new CategoriaModel($categoria_id);

        # removendo no banco
        if( $categoria->remove() )
        {
            $mensagens = array('notice'=>'Categoria removido com sucesso.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # erro ao remover dados
        else
        {
            $mensagens = array('error'=>'Erro ao remover categoria.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # redirecionando
        redirect(base_url() . 'categoria', 'refresh');
        exit();
            
    }
    
    public function grava_dados()
    {
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'titulo',
                'label' => 'Título',
                'rules' => 'trim|required|min_length[5]|max_length[255]|xss_clean'
            )
        );
        $this->form_validation->set_rules($validacoes);
        
        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
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
            $this->load->model('CategoriaModel');
            
            # criando o objeto
            $categoria = new CategoriaModel($this->input->post('categoria_id'));
            
            # populando obj usuário
            $categoria->titulo = $this->input->post('titulo');
            $categoria->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $categoria->grava() )
            {
                $mensagens = array('notice'=>'Categoria gravado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao gravar Categoria.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'categoria', 'refresh');
            exit();
            
        }
    }
    

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file categoria.php */
/* Location: ./application/controllers/categoria.php */