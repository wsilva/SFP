<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Relatorio extends CI_Controller
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
        $this->load->model('RelatorioModel');
        
        $relatorios = $this->RelatorioModel->buscartodos();
        $relatorios_pag = $this->RelatorioModel->buscarporqtde($this->limit, $offset);
        
        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('relatorio/index');
        $config['total_rows'] = sizeof($relatorios);
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        
        //para montar tabelas html
        $this->load->library('table');
        
        //para trabalhar com datas
        $this->load->helper('date');

        //table header
        $tablearr[] = array('Nome', 'Usuário', 'E-mail', 'Criação', 'Última alteração', '');

        //varrendo relatorios
        foreach($relatorios_pag as $relatorio){
            

            //ações
            $actions = "<a href='/relatorio/alterar/{$relatorio->id}' >editar</a>";
            $actions .= " | <a href='/relatorio/novasenha/{$relatorio->id}' >nova senha</a>";
            $actions .= " | <a href='/relatorio/permissoes/{$relatorio->id}' >permissoes</a>";
            
            //evitando de remover a si mesmo - wired behavior
            if( $this->session->userdata('relatorio_id') != $relatorio->id )
                $actions .= " | <a href=\"javascript:removeConfirmation({$relatorio->id})\" >remover</a>";

            //create update
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($relatorio->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($relatorio->dt_alteracao));

            //populando html table
            $tablearr[] = array($relatorio->nome, $relatorio->relatorio, $relatorio->email, $created, $updated, $actions);
        }
        

        //definindo abertura da tag table
        $table_tmpl = array ('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //dumping to data variable
        $data['tabela_relatorios'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('relatorio/list');
        $this->load->view('tmpl/footer');
    }
    
    public function meusdados()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('relatorio/meusdados');
        $this->load->view('tmpl/footer');
    }


    public function novo()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('relatorio/novo');
        $this->load->view('tmpl/footer');
    }
    
    public function alterar()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do relatorio
        $relatorio_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('RelatorioModel');

        # criando o objeto usuário
        $relatorio = new RelatorioModel($relatorio_id);
        $data['relatorio'] = $relatorio;
        
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('relatorio/alterardados');
        $this->load->view('tmpl/footer');
    }
    
    public function novasenha()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do relatorio
        $relatorio_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('RelatorioModel');

        # criando o objeto usuário
        $relatorio = new RelatorioModel($relatorio_id);
        $data['relatorio'] = $relatorio;
        
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('relatorio/alterarsenha');
        $this->load->view('tmpl/footer');
    }
    
    public function grava_novo()
    {
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'relatorio',
                'label' => 'Usuário',
                'rules' => 'trim|required|alpha_dash|min_length[5]|max_length[20]|xss_clean'
            ),
            array(
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'trim|required|min_length[5]|max_length[200]|xss_clean'
            ),
            array(
                'field' => 'email',
                'label' => 'E-mail',
                'rules' => 'trim|required|max_length[150]|valid_email'
            ),
            array(
                'field' => 'senha',
                'label' => 'Senha',
                'rules' => 'trim|required|min_length[5]|max_length[200]|alpha_numeric|matches[confirmacao]|md5'
            ),
            array(
                'field' => 'confirmacao',
                'label' => 'Confirmação de Senha',
                'rules' => 'trim|required'
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
            $this->load->model('RelatorioModel');
            
            # criando o objeto usuário
            $relatorio = new RelatorioModel();
            
            # populando obj usuário
            $relatorio->relatorio = $this->input->post('relatorio');
            $relatorio->nome = $this->input->post('nome');
            $relatorio->email = $this->input->post('email');
            $relatorio->senha = $this->input->post('senha');
            $relatorio->dt_cadastro = date('Y-m-d H:i:s');
            $relatorio->dt_alteracao = $relatorio->dt_cadastro;
            
            # gravando dados no banco
            if( $relatorio->grava() )
            {
                $mensagens = array('notice'=>'Usuário criado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao criar usuário.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'relatorio', 'refresh');
            exit();
            
        }
    }
    
    public function grava_dados()
    {
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'relatorio',
                'label' => 'Usuário',
                'rules' => 'trim|required|alpha_dash|min_length[5]|max_length[20]|xss_clean'
            ),
            array(
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'trim|required|min_length[5]|max_length[200]|xss_clean'
            ),
            array(
                'field' => 'email',
                'label' => 'E-mail',
                'rules' => 'trim|required|max_length[150]|valid_email'
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
            $this->load->model('RelatorioModel');
            
            # criando o objeto usuário
            $relatorio = new RelatorioModel($this->input->post('relatorio_id'));
            
            # populando obj usuário
            $relatorio->relatorio = $this->input->post('relatorio');
            $relatorio->nome = $this->input->post('nome');
            $relatorio->email = $this->input->post('email');
            $relatorio->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $relatorio->grava() )
            {
                $mensagens = array('notice'=>'Usuário gravado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao gravar usuário.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'relatorio', 'refresh');
            exit();
            
        }
    }
    
    public function grava_novasenha()
    {
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'senha',
                'label' => 'Senha',
                'rules' => 'trim|required|min_length[5]|max_length[200]|alpha_numeric|matches[confirmacao]|md5'
            ),
            array(
                'field' => 'confirmacao',
                'label' => 'Confirmação de Senha',
                'rules' => 'trim|required'
            )
        );
        $this->form_validation->set_rules($validacoes);
        
        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('alpha_numeric', 'O campo <strong>%s</strong> deve ter apenas letras e/ou números');
        $this->form_validation->set_message('matches', 'Os campos <strong>%s</strong> e <strong>%s</strong> não conferem.');
        
        # definindo delimitadores
        $this->form_validation->set_error_delimiters('<li class="submiterror">', '</li>');
        
        # não passou na validação
        if ($this->form_validation->run() == FALSE)
        {
            $this->novasenha();
        }
        
        #passou na validação
        else
        {
            
            # carregando model
            $this->load->model('RelatorioModel');
            
            # criando o objeto usuário
            $relatorio = new RelatorioModel($this->input->post('relatorio_id'));
            
            # populando obj usuário
            $relatorio->senha = $this->input->post('senha');
            $relatorio->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $relatorio->grava() )
            {
                $mensagens = array('notice'=>'Senha gravada com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao gravar senha.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'relatorio', 'refresh');
            exit();
            
        }
    }
    
    public function remover()
    {
        $this->load->library('form_validation');
        
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do relatorio
        $relatorio_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('RelatorioModel');

        # criando o objeto usuário
        $relatorio = new RelatorioModel($relatorio_id);

        # removendo no banco
        if( $relatorio->remove() )
        {
            $mensagens = array('notice'=>'Usuário removido com sucesso.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # erro ao remover dados
        else
        {
            $mensagens = array('error'=>'Erro ao remover usuário.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # redirecionando
        redirect(base_url() . 'relatorio', 'refresh');
        exit();
            
    }
    
    public function permissoes()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do relatorio
        $relatorio_id = $this->uri->segment(3);
        
        //carregando model
        $this->load->model('RelatorioModel');
        $this->load->model('PermissaoModel');
        $this->load->model('MetodoModel');
        
        $relatorio = new RelatorioModel($relatorio_id);
        $permissoes = $this->PermissaoModel->buscarporrelatorio($relatorio_id);
        $metodos = $this->MetodoModel->buscartodos();
        
        //marcando se tem ou não permissão
        foreach ($metodos as $metodo)
        {
            $metodo->tem_permissao = FALSE;
            
            foreach ($permissoes as $permissao)
            {
                if($permissao->metodo_id == $metodo->id)
                {
                    $metodo->tem_permissao = TRUE;
                    break;
                }
            }
        }
        
        $data['relatorio'] = $relatorio;
        $data['permissoes'] = $permissoes;
        $data['metodos'] = $metodos;
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('relatorio/permissoes');
        $this->load->view('tmpl/footer');
        
    }
    
    public function grava_permissoes()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        # pergando post
        $metodos = $this->input->post('metodos');
        $relatorio_id = $this->input->post('relatorio_id');
        
        # carregando model
        $this->load->model('PermissaoModel');

        # criando o objeto permissao
        $permissaoModel = new PermissaoModel();
        
        # remove antigas permissões
        $permissaoModel->removeporrelatorio($relatorio_id);
        
        foreach($metodos as $metodo_id)
        {
            $premissao = new PermissaoModel();
            $premissao->relatorio_id = $relatorio_id;
            $premissao->metodo_id = $metodo_id;
            $premissao->grava();
        }
        
        $mensagens = array('notice'=>'Permissões gravadas.');
        $this->session->set_flashdata('mensagens', $mensagens);

        # redirecionando
        redirect(base_url() . "relatorio/permissoes/{$relatorio_id}", 'refresh');
        exit();
            
    }

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file relatorio.php */
/* Location: ./application/controllers/relatorio.php */