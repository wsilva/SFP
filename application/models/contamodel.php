<?php

class ContaModel extends CI_Model
{

    var $id;
    var $titular = '';
    var $tipo = 'c';
    var $banco_id = 0;
    var $num_agencia = '';
    var $ag_digito = '';
    var $num_conta = '';
    var $conta_digito = '';
    var $saldo = 0.0;
    var $limite = 0.0;
    var $dt_cadastro = '0000-00-00 00:00:00';
    var $dt_alteracao = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('contas');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->titular = $result[0]->titular;
                $this->tipo = $result[0]->tipo;
                $this->banco_id = $result[0]->banco_id;
                $this->num_agencia = $result[0]->num_agencia;
                $this->ag_digito = $result[0]->ag_digito;
                $this->num_conta = $result[0]->num_conta;
                $this->conta_digito = $result[0]->conta_digito;
                $this->saldo = $result[0]->saldo;
                $this->limite = $result[0]->limite;
                $this->dt_cadastro = $result[0]->dt_cadastro;
                $this->dt_alteracao = $result[0]->dt_alteracao;
            }

            //ñ existe
            else
            {
                die("ooops: Conta id does not exists! <br/> What are you doing?");
            }
        }

        //sem id criamos novo
        else
        {
            $this->id = null;
        }

        return $this;
    }

    function buscartodas()
    {
        $this->db->order_by("titular", "asc");
        $query = $this->db->get('contas');
        return $query->result();
    }

    function buscarporqtde($limit, $offset)
    {
        $this->db->order_by("titular", "asc");
        $query = $this->db->get('contas', $limit, $offset);
        return $query->result();
    }

    function saque($valor)
    {
        $updateData = array(
            'saldo' => $this->saldo - $valor,
            'dt_alteracao' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $this->id);
        return (boolean) $this->db->update('contas', $updateData);
    }

    function grava()
    {
        // inserindo nova conta
        if ($this->id == null)
        {
            $insertData = array(
                'titular' => $this->titular,
                'tipo' => $this->tipo,
                'banco_id' => $this->banco_id,
                'num_agencia' => $this->num_agencia,
                'ag_digito' => $this->ag_digito,
                'num_conta' => $this->num_conta,
                'conta_digito' => $this->conta_digito,
                'saldo' => $this->saldo,
                'limite' => $this->limite,
                'dt_cadastro' => date('Y-m-d H:i:s'),
                'dt_alteracao' => date('Y-m-d H:i:s')
            );

            $this->db->insert('contas', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //atualizando conta existente
        else
        {
            $updateData = array(
                'titular' => $this->titular,
                'tipo' => $this->tipo,
                'banco_id' => $this->banco_id,
                'num_agencia' => $this->num_agencia,
                'ag_digito' => $this->ag_digito,
                'num_conta' => $this->num_conta,
                'conta_digito' => $this->conta_digito,
                'saldo' => $this->saldo,
                'limite' => $this->limite,
                'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $this->id);
            $this->db->update('contas', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting
        $this->db->where('id', $this->id);
        $this->db->delete('contas');

        return TRUE;
    }

}

/* End of file contamodel.php */
/* Location: ./application/models/contamodel.php */
