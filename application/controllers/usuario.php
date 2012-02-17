<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Usuario extends CI_Controller
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
        $this->load->model('UsuarioModel');
        
        $usuarios = $this->UsuarioModel->buscartodos();
        $usuarios_pag = $this->UsuarioModel->buscarporqtde($this->limit, $offset);
        
        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('usuario/index');
        $config['total_rows'] = sizeof($usuarios);
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

        //varrendo usuarios
        foreach($usuarios_pag as $usuario){
            

            //ações
            $actions = "<a href='/usuario/alterar/{$usuario->id}' >editar</a>";
            $actions .= " | <a href='/usuario/novasenha/{$usuario->id}' >nova senha</a>";
            $actions .= " | <a href='/usuario/permissoes/{$usuario->id}' >permissoes</a>";
            
            //evitando de remover a si mesmo - wired behavior
            if( $this->session->userdata('usuario_id') != $usuario->id )
                $actions .= " | <a href=\"javascript:removeConfirmation({$usuario->id})\" >remover</a>";

            //create update
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($usuario->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($usuario->dt_alteracao));

            //populando html table
            $tablearr[] = array($usuario->nome, $usuario->usuario, $usuario->email, $created, $updated, $actions);
        }
        

        //definindo abertura da tag table
        $table_tmpl = array ('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //dumping to data variable
        $data['tabela_usuarios'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('usuario/listar');
        $this->load->view('tmpl/footer');
    }
    
    public function minhasenha()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do usuario
        $usuario_id = $this->session->userdata('usuario_id');
        
        # carregando model
        $this->load->model('UsuarioModel');

        # criando o objeto usuário
        $usuario = new UsuarioModel($usuario_id);
        $data['usuario'] = $usuario;
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('usuario/minhasenha');
        $this->load->view('tmpl/footer');
    }
    
    public function grava_minhasenha()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
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
            $this->minhasenha();
        }
        
        #passou na validação
        else
        {
            
            # carregando model
            $this->load->model('UsuarioModel');
            
            //pegando id do usuario
            $usuario_id = $this->session->userdata('usuario_id');
            
            # criando o objeto usuário
            $usuario = new UsuarioModel($usuario_id);
            
            # populando obj usuário
            $usuario->senha = $this->input->post('senha');
            $usuario->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $usuario->grava() )
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
            redirect(base_url() . 'usuario/minhasenha', 'refresh');
            exit();
            
        }
    }
    
    public function meusdados()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do usuario
        $usuario_id = $this->session->userdata('usuario_id');
        
        # carregando model
        $this->load->model('UsuarioModel');

        # criando o objeto usuário
        $usuario = new UsuarioModel($usuario_id);
        $data['usuario'] = $usuario;
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('usuario/meusdados');
        $this->load->view('tmpl/footer');
    }
    
    public function grava_meusdados()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
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
            $this->load->model('UsuarioModel');
            
            # criando o objeto usuário
            $usuario = new UsuarioModel($this->input->post('usuario_id'));
            
            # populando obj usuário
            $usuario->nome = $this->input->post('nome');
            $usuario->email = $this->input->post('email');
            $usuario->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $usuario->grava() )
            {
                $mensagens = array('notice'=>'Dados gravados com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao gravar dados.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'usuario/meusdados', 'refresh');
            exit();
            
        }
    }


    public function novo()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('usuario/novo');
        $this->load->view('tmpl/footer');
    }
    
    public function alterar()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do usuario
        $usuario_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('UsuarioModel');

        # criando o objeto usuário
        $usuario = new UsuarioModel($usuario_id);
        $data['usuario'] = $usuario;
        
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('usuario/alterardados');
        $this->load->view('tmpl/footer');
    }
    
    public function novasenha()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do usuario
        $usuario_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('UsuarioModel');

        # criando o objeto usuário
        $usuario = new UsuarioModel($usuario_id);
        $data['usuario'] = $usuario;
        
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('usuario/alterarsenha');
        $this->load->view('tmpl/footer');
    }
    
    public function grava_novo()
    {        
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'usuario',
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
            $this->load->model('UsuarioModel');
            
            # criando o objeto usuário
            $usuario = new UsuarioModel();
            
            # populando obj usuário
            $usuario->usuario = $this->input->post('usuario');
            $usuario->nome = $this->input->post('nome');
            $usuario->email = $this->input->post('email');
            $usuario->senha = $this->input->post('senha');
            $usuario->dt_cadastro = date('Y-m-d H:i:s');
            $usuario->dt_alteracao = $usuario->dt_cadastro;
            
            # gravando dados no banco
            if( $usuario->grava() )
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
            redirect(base_url() . 'usuario', 'refresh');
            exit();
            
        }
    }
    
    public function grava_dados()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'usuario',
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
            $this->load->model('UsuarioModel');
            
            # criando o objeto usuário
            $usuario = new UsuarioModel($this->input->post('usuario_id'));
            
            # populando obj usuário
            $usuario->usuario = $this->input->post('usuario');
            $usuario->nome = $this->input->post('nome');
            $usuario->email = $this->input->post('email');
            $usuario->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $usuario->grava() )
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
            redirect(base_url() . 'usuario', 'refresh');
            exit();
            
        }
    }
    
    public function grava_novasenha()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
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
            $this->load->model('UsuarioModel');
            
            # criando o objeto usuário
            $usuario = new UsuarioModel($this->input->post('usuario_id'));
            
            # populando obj usuário
            $usuario->senha = $this->input->post('senha');
            $usuario->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $usuario->grava() )
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
            redirect(base_url() . 'usuario', 'refresh');
            exit();
            
        }
    }
    
    
    
    public function remover()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $this->load->library('form_validation');
        
        $data = array();
        
        //pegando id do usuario
        $usuario_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('UsuarioModel');

        # criando o objeto usuário
        $usuario = new UsuarioModel($usuario_id);

        # removendo no banco
        if( $usuario->remove() )
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
        redirect(base_url() . 'usuario', 'refresh');
        exit();
            
    }
    
    public function permissoes()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id do usuario
        $usuario_id = $this->uri->segment(3);
        
        //carregando model
        $this->load->model('UsuarioModel');
        $this->load->model('PermissaoModel');
        $this->load->model('MetodoModel');
        
        $usuario = new UsuarioModel($usuario_id);
        $permissoes = $this->PermissaoModel->buscarporusuario($usuario_id);
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
        
        $data['usuario'] = $usuario;
        $data['permissoes'] = $permissoes;
        $data['metodos'] = $metodos;
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('usuario/permissoes');
        $this->load->view('tmpl/footer');
        
    }
    
    public function grava_permissoes()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        # pergando post
        $metodos = $this->input->post('metodos');
        $usuario_id = $this->input->post('usuario_id');
        
        # carregando model
        $this->load->model('PermissaoModel');

        # criando o objeto permissao
        $permissaoModel = new PermissaoModel();
        
        # remove antigas permissões
        $permissaoModel->removeporusuario($usuario_id);
        
        foreach($metodos as $metodo_id)
        {
            $premissao = new PermissaoModel();
            $premissao->usuario_id = $usuario_id;
            $premissao->metodo_id = $metodo_id;
            $premissao->grava();
        }
        
        $mensagens = array('notice'=>'Permissões gravadas.');
        $this->session->set_flashdata('mensagens', $mensagens);

        # redirecionando
        redirect(base_url() . "usuario/permissoes/{$usuario_id}", 'refresh');
        exit();
            
    }

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file usuario.php */
/* Location: ./application/controllers/usuario.php */