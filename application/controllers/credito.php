<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Credito extends CI_Controller
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
        $this->load->model('CreditoModel');

        $creditos = $this->CreditoModel->buscartodos();
        $creditos_pag = $this->CreditoModel->buscarporqtde($this->limit, $offset);

        //paginação
        $this->load->library('pagination');
        $config['base_url'] = site_url('credito/index');
        $config['total_rows'] = sizeof($creditos);
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
            'Bandeira',
            'N°',
            'Validade',
            'Vencimento',
            'Limite (R$)',
            'Limite (US$)',
            'Saque (R$)',
            'Saque (US$)',
            'Criação',
            'Última alteração',
            ''
        );

        //varrendo creditos
        foreach ($creditos_pag as $credito)
        {

            //ações
            $actions = "<a href='/credito/alterar/{$credito->id}' >editar</a>";
            $actions .= " | <a href=\"javascript:removeConfirmation({$credito->id})\" >remover</a>";

            //create update
            $validade = mdate('%m/%Y', mysql_to_unix($credito->validade));
            $created = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($credito->dt_cadastro));
            $updated = mdate('%d/%m/%Y %Hh%i', mysql_to_unix($credito->dt_alteracao));

            $limite_reais = number_format($credito->limite_reais, 2, ',', '.');
            $limite_dolar = number_format($credito->limite_dolar, 2, '.', ',');
            $limite_saque_reais = number_format($credito->limite_saque_reais, 2, ',', '.');
            $limite_saque_dolar = number_format($credito->limite_saque_dolar, 2, '.', ',');

            $numero = substr($credito->numero, 0, 4) . " " . substr($credito->numero, 4, 4) . " " . substr($credito->numero, 8, 4) . " " . substr($credito->numero, 12, 4);

            //populando html table
            $tablearr[] = array(
                $credito->titular,
                $credito->bandeira,
                $numero,
                $validade,
                $credito->vencimento,
                $limite_reais,
                $limite_dolar,
                $limite_saque_reais,
                $limite_saque_dolar,
                $created,
                $updated,
                $actions
            );
        }


        //definindo abertura da tag table
        $table_tmpl = array('table_open' => ' <table class="tabledetail">');
        $this->table->set_template($table_tmpl);

        //guardando no array da view
        $data['tabela_creditos'] = $this->table->generate($tablearr);

        //limpando table helper para ser reutilizado
        $this->table->clear();

        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('credito/listar');
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

        $this->load->view('tmpl/header', $data);
        $this->load->view('credito/novo');
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
            ),
            array(
                'field' => 'vencimento',
                'label' => 'Vencimento',
                'rules' => 'trim|required|numeric|min_length[1]|max_length[2]|greater_than[0]|less_than[29]|xss_clean'
            ),
            array(
                'field' => 'limite_reais',
                'label' => 'Limite em reais',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite_dolar',
                'label' => 'Limite em dolar',
                'rules' => 'trim|required|callback_is_dolar|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite_saque_reais',
                'label' => 'Limite de saque em reais',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite_saque_dolar',
                'label' => 'Limite de saque em dolar',
                'rules' => 'trim|required|callback_is_dolar|min_length[3]|max_length[14]'
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
            $this->novo();
        }

        #passou na validação
        else
        {

            # carregando model
            $this->load->model('CreditoModel');

            # criando o objeto Credito
            $credito = new CreditoModel();

            # populando obj Credito
            $credito->titular = $this->input->post('titular');
            $credito->bandeira = $this->input->post('bandeira');
            $credito->numero = $this->input->post('numero_a') . $this->input->post('numero_b') . $this->input->post('numero_c') . $this->input->post('numero_d');
            $credito->validade = $this->input->post('validade_b') . '-' . $this->input->post('validade_a') . '-01 00:00:00';
            $credito->vencimento = $this->input->post('vencimento');
            $credito->limite_reais = preg_replace('#\.#', '', $this->input->post('limite_reais'));
            $credito->limite_reais = preg_replace('#,#', '.', $credito->limite_reais);
            $credito->limite_saque_reais = preg_replace('#\.#', '', $this->input->post('limite_saque_reais'));
            $credito->limite_saque_reais = preg_replace('#,#', '.', $credito->limite_saque_reais);
            $credito->limite_dolar = preg_replace('#,#', '', $this->input->post('limite_dolar'));
            $credito->limite_saque_dolar = preg_replace('#,#', '', $this->input->post('limite_saque_dolar'));
            $credito->dt_cadastro = date('Y-m-d H:i:s');
            $credito->dt_alteracao = $credito->dt_cadastro;

            # gravando dados no banco
            if ($credito->grava())
            {
                $mensagens = array('notice' => 'Cartão de crédito criado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao criar Cartão de crédito.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'credito', 'refresh');
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

    function is_dolar($valor)
    {
        $return = (bool) preg_match('/^([0-9]{1,3})(\,[0-9]{3})*(.[0-9]{2})?$/', $valor);
        if ($return == FALSE)
        {
            $this->form_validation->set_message('is_dolar', 'O valor <strong>' . $valor . '</strong> não é em dolar.');
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
        $credito_id = $this->uri->segment(3);

        # carregando model
        $this->load->model('CreditoModel');

        # criando o objeto
        $credito = new CreditoModel($credito_id);
        $data['credito'] = $credito;


        # pegando mensagens da sessão flash
        $data['mensagens'] = $this->session->flashdata('mensagens');

        $this->load->view('tmpl/header', $data);
        $this->load->view('credito/alterardados');
        $this->load->view('tmpl/footer');
    }

    public function remover()
    {
        $this->load->library('form_validation');

        $this->auth->check_logged($this->router->class, $this->router->method);

        $data = array();

        //pegando id
        $credito_id = $this->uri->segment(3);

        # carregando model
        $this->load->model('CreditoModel');

        # criando o objeto credito
        $credito = new CreditoModel($credito_id);

        # removendo no banco
        if ($credito->remove())
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
        redirect(base_url() . 'credito', 'refresh');
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
            ),
            array(
                'field' => 'vencimento',
                'label' => 'Vencimento',
                'rules' => 'trim|required|numeric|min_length[1]|max_length[2]|greater_than[0]|less_than[29]|xss_clean'
            ),
            array(
                'field' => 'limite_reais',
                'label' => 'Limite em reais',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite_dolar',
                'label' => 'Limite em dolar',
                'rules' => 'trim|required|callback_is_dolar|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite_saque_reais',
                'label' => 'Limite de saque em reais',
                'rules' => 'trim|required|callback_is_reais|min_length[3]|max_length[14]'
            ),
            array(
                'field' => 'limite_saque_dolar',
                'label' => 'Limite de saque em dolar',
                'rules' => 'trim|required|callback_is_dolar|min_length[3]|max_length[14]'
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
            $this->load->model('CreditoModel');

            # criando o objeto
            $credito = new CreditoModel($this->input->post('credito_id'));

            # populando obj Credito
            $credito->titular = $this->input->post('titular');
            $credito->bandeira = $this->input->post('bandeira');
            $credito->numero = $this->input->post('numero_a') . $this->input->post('numero_b') . $this->input->post('numero_c') . $this->input->post('numero_d');
            $credito->validade = $this->input->post('validade_b') . '-' . $this->input->post('validade_a') . '-01 00:00:00';
            $credito->vencimento = $this->input->post('vencimento');
            $credito->limite_reais = preg_replace('#\.#', '', $this->input->post('limite_reais'));
            $credito->limite_reais = preg_replace('#,#', '.', $credito->limite_reais);
            $credito->limite_saque_reais = preg_replace('#\.#', '', $this->input->post('limite_saque_reais'));
            $credito->limite_saque_reais = preg_replace('#,#', '.', $credito->limite_saque_reais);
            $credito->limite_dolar = preg_replace('#,#', '', $this->input->post('limite_dolar'));
            $credito->limite_saque_dolar = preg_replace('#,#', '', $this->input->post('limite_saque_dolar'));
            $credito->dt_alteracao = date('Y-m-d H:i:s');

            # gravando dados no banco
            if ($credito->grava())
            {
                $mensagens = array('notice' => 'Cartão de crédito gravado com sucesso.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            #erro ao gravar dados
            else
            {
                $mensagens = array('error' => 'Erro ao gravar Cartão de crédito.');
                $this->session->set_flashdata('mensagens', $mensagens);
            }

            # redirecionando
            redirect(base_url() . 'credito', 'refresh');
            exit();
        }
    }

    public function __construct()
    {
        parent::__construct();
    }

}

/* End of file credito.php */
/* Location: ./application/controllers/credito.php */