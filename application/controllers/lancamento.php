<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lancamento extends CI_Controller
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

        //carregando models
        $this->load->model('LancamentoModel');
        $this->load->model('CategoriaModel');

        $lancamentos = $this->LancamentoModel->buscartodos();
        $lancamentos_pag = $this->LancamentoModel->buscarporqtde($this->limit, $offset);

        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('lancamento/index');
        $config['total_rows'] = sizeof($lancamentos);
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        //para montar tabelas html
        $this->load->library('table');

        //para trabalhar com datas
        $this->load->helper('date');

        //table header
        $tablearr[] = array('Nome', 'Categoria', 'Meio', 'Tipo', 'Valor (R$)', 'Criação', 'Última alteração', '');

        //varrendo lancamentos
        foreach ($lancamentos_pag as $lancamento)
        {


            //ações
            $actions = "<a href='/lancamento/alterar/{$lancamento->id}' >editar</a>";
            $actions .= " | <a href=\"javascript:removeConfirmation({$lancamento->id})\" >remover</a>";

            //create update
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($lancamento->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($lancamento->dt_alteracao));
            
            $valor = number_format($lancamento->valor, 2, ',', '.');

            //tipo
            $tipo = ($lancamento->tipo == 'd' ? 'Débito' : 'Crédito');

            //meio
            $meio = ($lancamento->meio == 'credito' ? 'Cartão de Crédito' : ($lancamento->meio == 'conta' ? 'Conta Corrente / Poupança' : 'Cartão de Benefício' ) );
            
            //categoria
            $categoria = new CategoriaModel($lancamento->categoria_id);

            //populando html table
            $tablearr[] = array($lancamento->nome, $categoria->titulo, $meio, $tipo, $valor, $created, $updated, $actions);
        }


        //definindo abertura da tag table
        $table_tmpl = array('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //guardando no array da view
        $data['tabela_lancamentos'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('lancamento/listar');
        $this->load->view('tmpl/footer');
    }

    public function novo()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        # js adicionais
        $data['jsadicionais'] = '<script type="text/javascript" src="/assets/js/jquery.price_format.1.6.min.js"></script>';
        
        # categorias
        $this->load->model('CategoriaModel');
        $categorias = $this->CategoriaModel->buscartodas();
        $data['categorias'] = $categorias;

        $this->load->view('tmpl/header', $data);
        $this->load->view('lancamento/novo');
        $this->load->view('tmpl/footer');
    }

    public function grava_novo()
    {

        $this->auth->check_logged($this->router->class, $this->router->method);

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
                'rules' => 'trim|max_length[255]|xss_clean'
            ),
            array(
                'field' => 'meio',
                'label' => 'Meio',
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'categoria_id',
                'label' => 'Categoria',
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'tipo',
                'label' => 'Tipo',
                'rules' => 'trim|required|min_length[1]|max_length[1]|xss_clean'
            ),
            array(
                'field' => 'valor',
                'label' => 'Valor',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
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
            $this->novo();
        }

        #passou na validação
        else
        {

            # carregando model
            $this->load->model('LancamentoModel');

            # criando o objeto Lancamento
            $lancamento = new LancamentoModel();

            # populando obj Lancamento
            $lancamento->nome = $this->input->post('nome');
            $lancamento->descricao = $this->input->post('descricao');
            $lancamento->meio = $this->input->post('meio');
            $lancamento->categoria_id = $this->input->post('categoria_id');
            $lancamento->tipo = $this->input->post('tipo');
            $lancamento->valor = preg_replace('#\.#', '', $this->input->post('valor'));
            $lancamento->valor = preg_replace('#,#', '.', $lancamento->valor);
            $lancamento->dt_cadastro = date('Y-m-d H:i:s');
            $lancamento->dt_alteracao = $lancamento->dt_cadastro;

            # gravando dados no banco
            if ($lancamento->grava())
            {
                $mensagens = array('notice' => 'Lançamento criado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao criar lançamento.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'lancamento', 'refresh');
            exit();
        }
    }
    
    function is_reais($valor)
    {
        $return = (bool) preg_match('/^([0-9]{1,3})(\.[0-9]{3})*(,[0-9]{2})$/', $valor);
        if ($return == FALSE)
        {
            $this->form_validation->set_message('is_reais', 'O valor <strong>' . $valor . '</strong> não é reais.');
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
        $lancamento_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('LancamentoModel');

        # criando o objeto
        $lancamento = new LancamentoModel($lancamento_id);
        $data['lancamento'] = $lancamento;
        
        # js adicionais
        $data['jsadicionais'] = '<script type="text/javascript" src="/assets/js/jquery.price_format.1.6.min.js"></script>';
        
        # categorias
        $this->load->model('CategoriaModel');
        $categorias = $this->CategoriaModel->buscartodas();
        $data['categorias'] = $categorias;
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('lancamento/alterardados');
        $this->load->view('tmpl/footer');
    }
    
    public function remover()
    {
        $this->load->library('form_validation');
        
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id
        $lancamento_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('LancamentoModel');

        # criando o objeto lancamento
        $lancamento = new LancamentoModel($lancamento_id);

        # removendo no banco
        if( $lancamento->remove() )
        {
            $mensagens = array('notice'=>'Lançamento removido com sucesso.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # erro ao remover dados
        else
        {
            $mensagens = array('error'=>'Erro ao remover lançamento.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # redirecionando
        redirect(base_url() . 'lancamento', 'refresh');
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
                'rules' => 'trim|max_length[255]|xss_clean'
            ),
            array(
                'field' => 'meio',
                'label' => 'Meio',
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'categoria_id',
                'label' => 'Categoria',
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'tipo',
                'label' => 'Tipo',
                'rules' => 'trim|required|min_length[1]|max_length[1]|xss_clean'
            ),
            array(
                'field' => 'valor',
                'label' => 'Valor',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
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
            $this->load->model('LancamentoModel');
            
            # criando o objeto
            $lancamento = new LancamentoModel($this->input->post('lancamento_id'));
            
            # populando obj usuário
            $lancamento->nome = $this->input->post('nome');
            $lancamento->descricao = $this->input->post('descricao');
            $lancamento->meio = $this->input->post('meio');
            $lancamento->categoria_id = $this->input->post('categoria_id');
            $lancamento->tipo = $this->input->post('tipo');
            $lancamento->valor = preg_replace('#\.#', '', $this->input->post('valor'));
            $lancamento->valor = preg_replace('#,#', '.', $lancamento->valor);
            $lancamento->valor = preg_replace('#,#', '.', $lancamento->valor);
            $lancamento->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $lancamento->grava() )
            {
                $mensagens = array('notice'=>'Lançamento gravado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao gravar Lançamento.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'lancamento', 'refresh');
            exit();
            
        }
    }
    

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file lancamento.php */
/* Location: ./application/controllers/lancamento.php */