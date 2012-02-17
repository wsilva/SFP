<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Beneficio extends CI_Controller
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
        $this->load->model('BeneficioModel');

        $beneficios = $this->BeneficioModel->buscartodas();
        $beneficios_pag = $this->BeneficioModel->buscarporqtde($this->limit, $offset);

        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('beneficio/index');
        $config['total_rows'] = sizeof($beneficios);
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        //para montar tabelas html
        $this->load->library('table');

        //para trabalhar com datas
        $this->load->helper('date');

        //table header
        $tablearr[] = array('Titular', 'Tipo', 'Bandeira', 'N°', 'Validade', 'Saldo', 'Criação', 'Última alteração', '');

        //varrendo beneficios
        foreach ($beneficios_pag as $beneficio)
        {

            //ações
            $actions = "<a href='/beneficio/alterar/{$beneficio->id}' >editar</a>";
            $actions .= " | <a href=\"javascript:removeConfirmation({$beneficio->id})\" >remover</a>";

            //create update
            $validade = mdate('%m/%Y', mysql_to_unix($beneficio->validade));
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($beneficio->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($beneficio->dt_alteracao));
            
            $saldo = number_format($beneficio->saldo, 2, ',', '.');
            
            $numero = substr($beneficio->numero,0,4)." ".substr($beneficio->numero,4,4)." ".substr($beneficio->numero,8,4)." ".substr($beneficio->numero,12,4);

            //populando html table
            $tablearr[] = array($beneficio->titular, $beneficio->tipo, $beneficio->bandeira, $numero, $validade, $saldo, $created, $updated, $actions);
        }


        //definindo abertura da tag table
        $table_tmpl = array('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //guardando no array da view
        $data['tabela_beneficios'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('beneficio/listar');
        $this->load->view('tmpl/footer');
    }

    public function novo()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('beneficio/novo');
        $this->load->view('tmpl/footer');
    }

    public function grava_novo()
    {

        $this->auth->check_logged($this->router->class, $this->router->method);

        $this->load->library('form_validation');

        # validações
        $validacoes = array(
            array(
                'field' => 'titular',
                'label' => 'Titular',
                'rules' => 'trim|required|min_length[5]|max_length[255]|xss_clean'
            ),
            array(
                'field' => 'tipo',
                'label' => 'Tipo',
                'rules' => 'trim|required|min_length[5]|max_length[255]|xss_clean'
            ),
            array(
                'field' => 'bandeira',
                'label' => 'Bandeira',
                'rules' => 'trim|required|min_length[1]|max_length[50]|xss_clean'
            ),
            array(
                'field' => 'numero_a',
                'label' => 'Número (primeiro grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'numero_b',
                'label' => 'Número (segundo grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'numero_c',
                'label' => 'Número (terceiro grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'numero_d',
                'label' => 'Número (quarto grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'validade_a',
                'label' => 'Validade (mês)',
                'rules' => 'trim|required|numeric|min_length[2]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'validade_b',
                'label' => 'Validade (ano)',
                'rules' => 'trim|required|numeric|callback_validade|min_length[4]|max_length[4]|xss_clean'
            )
        );
        $this->form_validation->set_rules($validacoes);
        

        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('numeric', 'O campo <strong>%s</strong> deve ter apenas números');
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
            $this->load->model('BeneficioModel');

            # criando o objeto Beneficio
            $beneficio = new BeneficioModel();

            # populando obj Beneficio
            $beneficio->titular = $this->input->post('titular');
            $beneficio->tipo = $this->input->post('tipo');
            $beneficio->bandeira = $this->input->post('bandeira');
            $beneficio->numero = $this->input->post('numero_a').$this->input->post('numero_b').$this->input->post('numero_c').$this->input->post('numero_d');
            $beneficio->validade = $this->input->post('validade_b').'-'.$this->input->post('validade_a').'-01 00:00:00';
            $beneficio->dt_cadastro = date('Y-m-d H:i:s');
            $beneficio->dt_alteracao = $beneficio->dt_cadastro;

            # gravando dados no banco
            if ($beneficio->grava())
            {
                $mensagens = array('notice' => 'Benefício criado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao criar benefício.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'beneficio', 'refresh');
            exit();
        }
    }
    
    function validade()
    {
        
        $this->load->helper('date');
        $validade = $this->input->post('validade_b').'-'.$this->input->post('validade_a').'-01 00:00:00';
        $hoje = date('Y-m-d H:i:s');
        $diff = mysql_to_unix($validade) - mysql_to_unix($hoje);
        if($diff <= 0)
        {
            $this->form_validation->set_message('validade', 'A data de validade informada <strong>'.$this->input->post('validade_a').'/'.$this->input->post('validade_b').'</strong> já passou ou não existe.');
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
        $beneficio_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('BeneficioModel');

        # criando o objeto
        $beneficio = new BeneficioModel($beneficio_id);
        $data['beneficio'] = $beneficio;
        
        
        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('beneficio/alterardados');
        $this->load->view('tmpl/footer');
    }
    
    public function remover()
    {
        $this->load->library('form_validation');
        
        $this->auth->check_logged($this->router->class, $this->router->method);
        
        $data = array();
        
        //pegando id
        $beneficio_id = $this->uri->segment(3);
        
        # carregando model
        $this->load->model('BeneficioModel');

        # criando o objeto beneficio
        $beneficio = new BeneficioModel($beneficio_id);

        # removendo no banco
        if( $beneficio->remove() )
        {
            $mensagens = array('notice'=>'Benefício removido com sucesso.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # erro ao remover dados
        else
        {
            $mensagens = array('error'=>'Erro ao remover benefício.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # redirecionando
        redirect(base_url() . 'beneficio', 'refresh');
        exit();
            
    }
    
    public function grava_dados()
    {
        $this->load->library('form_validation');
        
        # validações
        $validacoes = array(
            array(
                'field' => 'titular',
                'label' => 'Titular',
                'rules' => 'trim|required|min_length[5]|max_length[255]|xss_clean'
            ),
            array(
                'field' => 'tipo',
                'label' => 'Tipo',
                'rules' => 'trim|required|min_length[5]|max_length[255]|xss_clean'
            ),
            array(
                'field' => 'bandeira',
                'label' => 'Bandeira',
                'rules' => 'trim|required|min_length[1]|max_length[50]|xss_clean'
            ),
            array(
                'field' => 'numero_a',
                'label' => 'Número (primeiro grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'numero_b',
                'label' => 'Número (segundo grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'numero_c',
                'label' => 'Número (terceiro grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'numero_d',
                'label' => 'Número (quarto grupo)',
                'rules' => 'trim|required|numeric|min_length[4]|max_length[4]|xss_clean'
            ),
            array(
                'field' => 'validade_a',
                'label' => 'Validade (mês)',
                'rules' => 'trim|required|numeric|min_length[2]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'validade_b',
                'label' => 'Validade (ano)',
                'rules' => 'trim|required|numeric|callback_validade|min_length[4]|max_length[4]|xss_clean'
            )
        );
        $this->form_validation->set_rules($validacoes);
        
        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('numeric', 'O campo <strong>%s</strong> deve ter apenas números');
        $this->form_validation->set_message('alpha_numeric', 'O campo <strong>%s</strong> deve ter apenas letras e/ou números');
        $this->form_validation->set_message('alpha_dash', 'O campo <strong>%s</strong> deve ter apenas letras, números, ou os caracteres sublinhado (_) e traço (-).');
        $this->form_validation->set_message('valid_email', 'O campo <strong>%s</strong> deve ter um endereço de e-mail válido');
        $this->form_validation->set_message('matches', 'Os campos <strong>%s</strong> e <strong>%s</strong> não conferem.');
        
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
            $this->load->model('BeneficioModel');
            
            # criando o objeto
            $beneficio = new BeneficioModel($this->input->post('beneficio_id'));
            
            # populando obj beneficio
            $beneficio->titular = $this->input->post('titular');
            $beneficio->tipo = $this->input->post('tipo');
            $beneficio->bandeira = $this->input->post('bandeira');
            $beneficio->numero = $this->input->post('numero_a').$this->input->post('numero_b').$this->input->post('numero_c').$this->input->post('numero_d');
            $beneficio->validade = $this->input->post('validade_b').'-'.$this->input->post('validade_a').'-01 00:00:00';
            $beneficio->dt_alteracao = date('Y-m-d H:i:s');
            
            # gravando dados no banco
            if( $beneficio->grava() )
            {
                $mensagens = array('notice'=>'Benefício gravado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            #erro ao gravar dados
            else
            {
                $mensagens = array('error'=>'Erro ao gravar Benefício.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }
            
            # redirecionando
            redirect(base_url() . 'beneficio', 'refresh');
            exit();
            
        }
    }
    

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file beneficio.php */
/* Location: ./application/controllers/beneficio.php */