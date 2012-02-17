<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Conta extends CI_Controller
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
        $this->load->model('ContaModel');

        $contas = $this->ContaModel->buscartodas();
        $contas_pag = $this->ContaModel->buscarporqtde($this->limit, $offset);

        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('conta/index');
        $config['total_rows'] = sizeof($contas);
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = $uri_segment;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        //para montar tabelas html
        $this->load->library('table');

        //para trabalhar com datas
        $this->load->helper('date');

        //table header
        $tablearr[] = array(
            'Titular',
            'Tipo',
            'Banco',
            'Agência',
            'Conta',
            'Saldo (R$)',
            'Limite (R$)',
            'Criação',
            'Última alteração',
            ''
        );

        //varrendo contas
        foreach ($contas_pag as $conta)
        {

            //ações
            $actions = "<a href='/conta/alterar/{$conta->id}' >editar</a>";
            $actions .= " | <a href=\"javascript:removeConfirmation({$conta->id})\" >remover</a>";

            //create update
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($conta->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($conta->dt_alteracao));

            // ajustes de apresentação
            $saldo = number_format($conta->saldo, 2, ',', '.');
            $limite = number_format($conta->limite, 2, ',', '.');
            $tipo = ($conta->tipo=="c" ? "Corrente" : ($conta->tipo=="p" ? "Poupança" : "Corrente e Poupança" ) );

            //populando html table
            $tablearr[] = array(
                $conta->titular,
                $tipo,
                $conta->banco_id,
                $conta->num_agencia . '-' .$conta->ag_digito,
                $conta->num_conta . '-' .$conta->conta_digito,
                $saldo,
                $limite,
                $created,
                $updated,
                $actions
            );
        }


        //definindo abertura da tag table
        $table_tmpl = array('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //guardando no array da view
        $data['tabela_contas'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('conta/listar');
        $this->load->view('tmpl/footer');
    }

    public function nova()
    {
        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');
        
        //carregando model
        $this->load->model('BancoModel');
        
        $bancosObj = $this->BancoModel->buscartodos();
        foreach($bancosObj as $banco)
        {
            $bancosArray[$banco->id] = $banco->nome;
        }
        
        $data['bancos'] = $bancosArray;

        $data['jsadicionais'] = '<script type="text/javascript" src="/assets/js/jquery.price_format.1.6.min.js"></script>';

        $this->load->view('tmpl/header', $data);
        $this->load->view('conta/nova');
        $this->load->view('tmpl/footer');
    }

    public function grava_nova()
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
                'rules' => 'trim|required|min_length[1]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'banco_id',
                'label' => 'Banco',
                'rules' => 'trim|required|numeric|min_length[2]|max_length[5]|xss_clean'
            ),
            array(
                'field' => 'num_agencia',
                'label' => 'Agência (num.)',
                'rules' => 'trim|required|numeric|min_length[3]|max_length[5]|xss_clean'
            ),
            array(
                'field' => 'ag_digito',
                'label' => 'Agência (dig.)',
                'rules' => 'trim|required|numeric|min_length[1]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'num_conta',
                'label' => 'Conta(num.)',
                'rules' => 'trim|required|numeric|min_length[5]|max_length[8]|xss_clean'
            ),
            array(
                'field' => 'conta_digito',
                'label' => 'Conta (dig.)',
                'rules' => 'trim|required|numeric|min_length[1]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'saldo',
                'label' => 'Saldo',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite',
                'label' => 'Limite',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
            )
        );
        $this->form_validation->set_rules($validacoes);

        # mensagens de erro
        $this->form_validation->set_message('required', 'O campo <strong>%s</strong> é obrigatório');
        $this->form_validation->set_message('min_length', 'O campo <strong>%s</strong> deve ter no mínimo %s caracteres');
        $this->form_validation->set_message('max_length', 'O campo <strong>%s</strong> deve ter no máximo %s caracteres');
        $this->form_validation->set_message('greater_than', 'O campo <strong>%s</strong> deve ser maior que %s ');
        $this->form_validation->set_message('less_than', 'O campo <strong>%s</strong> deve ser menor que %s ');
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
            $this->nova();
        }

        #passou na validação
        else
        {

            # carregando model
            $this->load->model('ContaModel');

            # criando o objeto Conta
            $conta = new ContaModel();

            # populando obj Conta
            $conta->titular = $this->input->post('titular');
            $conta->tipo = $this->input->post('tipo');
            $conta->banco_id = $this->input->post('banco_id');
            $conta->num_agencia = $this->input->post('num_agencia');
            $conta->ag_digito = $this->input->post('ag_digito');
            $conta->num_conta = $this->input->post('num_conta');
            $conta->conta_digito = $this->input->post('conta_digito');
            $conta->saldo = preg_replace('#\.#', '', $this->input->post('saldo'));
            $conta->saldo = preg_replace('#,#', '.', $conta->saldo);
            $conta->limite = preg_replace('#\.#', '', $this->input->post('limite'));
            $conta->limite = preg_replace('#,#', '.', $conta->limite);
            $conta->dt_cadastro = date('Y-m-d H:i:s');
            $conta->dt_alteracao = $conta->dt_cadastro;

            # gravando dados no banco
            if ($conta->grava())
            {
                $mensagens = array('notice' => 'Conta criada com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao criar Conta.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'conta', 'refresh');
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

    function validade()
    {

        $this->load->helper('date');
        $validade = $this->input->post('validade_b') . '-' . $this->input->post('validade_a') . '-01 00:00:00';
        $hoje = date('Y-m-d H:i:s');
        $diff = mysql_to_unix($validade) - mysql_to_unix($hoje);
        if ($diff <= 0)
        {
            $this->form_validation->set_message('validade', 'A data de validade informada <strong>' . $this->input->post('validade_a') . '/' . $this->input->post('validade_b') . '</strong> já passou ou não existe.');
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

        # js adicionais
        $data['jsadicionais'] = '<script type="text/javascript" src="/assets/js/jquery.price_format.1.6.min.js"></script>';

        //pegando id
        $conta_id = $this->uri->segment(3);

        # carregando model
        $this->load->model('ContaModel');
        $this->load->model('BancoModel');
        
        $bancosObj = $this->BancoModel->buscartodos();
        foreach($bancosObj as $banco)
        {
            $bancosArray[$banco->id] = $banco->nome;
        }
        
        $data['bancos'] = $bancosArray;

        # criando o objeto
        $conta = new ContaModel($conta_id);
        $data['conta'] = $conta;


        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('conta/alterardados');
        $this->load->view('tmpl/footer');
    }

    public function remover()
    {
        $this->load->library('form_validation');

        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        //pegando id
        $conta_id = $this->uri->segment(3);

        # carregando model
        $this->load->model('ContaModel');

        # criando o objeto conta
        $conta = new ContaModel($conta_id);

        # removendo no banco
        if ($conta->remove())
        {
            $mensagens = array('notice' => 'Benefício removido com sucesso.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # erro ao remover dados
        else
        {
            $mensagens = array('error' => 'Erro ao remover benefício.');
            $this->session->set_flashdata('mensagens', $mensagens);
        }

        # redirecionando
        redirect(base_url() . 'conta', 'refresh');
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
                'rules' => 'trim|required|min_length[1]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'banco_id',
                'label' => 'Banco',
                'rules' => 'trim|required|numeric|min_length[2]|max_length[5]|xss_clean'
            ),
            array(
                'field' => 'num_agencia',
                'label' => 'Agência (num.)',
                'rules' => 'trim|required|numeric|min_length[3]|max_length[5]|xss_clean'
            ),
            array(
                'field' => 'ag_digito',
                'label' => 'Agência (dig.)',
                'rules' => 'trim|required|numeric|min_length[1]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'num_conta',
                'label' => 'Conta(num.)',
                'rules' => 'trim|required|numeric|min_length[5]|max_length[8]|xss_clean'
            ),
            array(
                'field' => 'conta_digito',
                'label' => 'Conta (dig.)',
                'rules' => 'trim|required|numeric|min_length[1]|max_length[2]|xss_clean'
            ),
            array(
                'field' => 'saldo',
                'label' => 'Saldo',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite',
                'label' => 'Limite',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
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
            $this->load->model('ContaModel');

            # criando o objeto
            $conta = new ContaModel($this->input->post('conta_id'));

            # populando obj Conta
            $conta->titular = $this->input->post('titular');
            $conta->tipo = $this->input->post('tipo');
            $conta->banco_id = $this->input->post('banco_id');
            $conta->num_agencia = $this->input->post('num_agencia');
            $conta->ag_digito = $this->input->post('ag_digito');
            $conta->num_conta = $this->input->post('num_conta');
            $conta->conta_digito = $this->input->post('conta_digito');
            $conta->saldo = preg_replace('#\.#', '', $this->input->post('saldo'));
            $conta->saldo = preg_replace('#,#', '.', $conta->saldo);
            $conta->limite = preg_replace('#\.#', '', $this->input->post('limite'));
            $conta->limite = preg_replace('#,#', '.', $conta->limite);
            $conta->dt_alteracao = date('Y-m-d H:i:s');

            # gravando dados no banco
            if ($conta->grava())
            {
                $mensagens = array('notice' => 'Conta gravada com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao gravar Conta.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'conta', 'refresh');
            exit();
        }
    }

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file conta.php */
/* Location: ./application/controllers/conta.php */