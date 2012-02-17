<?php

class CreditoModel extends CI_Model
{

    var $id;
    var $titular = '';
    var $bandeira = '';
    var $conta_id = 0;
    var $numero = '';
    var $validade = '0000-00-00 00:00:00';
    var $vencimento = '1';
    var $limite_reais = 0.0;
    var $limite_dolar = 0.0;
    var $limite_saque_reais = 0.0;
    var $limite_saque_dolar = 0.0;
    var $dt_cadastro = '0000-00-00 00:00:00';
    var $dt_alteracao = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('creditos');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->titular = $result[0]->titular;
                $this->bandeira = $result[0]->bandeira;
                $this->conta_id = $result[0]->conta_id;
                $this->numero = $result[0]->numero;
                $this->validade = $result[0]->validade;
                $this->limite_reais = $result[0]->limite_reais;
                $this->limite_dolar = $result[0]->limite_dolar;
                $this->limite_saque_reais = $result[0]->limite_saque_reais;
                $this->limite_saque_dolar = $result[0]->limite_saque_dolar;
                $this->dt_cadastro = $result[0]->dt_cadastro;
                $this->dt_alteracao = $result[0]->dt_alteracao;
            }

            //ñ existe
            else
            {
                die("ooops: Credito id does not exists! <br/> What are you doing?");
            }
        }

        //sem id criamos novo
        else
        {
            $this->id = null;
        }
        
        return $this;
    }

    function buscartodos()
    {
        $this->db->order_by("titular", "asc");
        $query = $this->db->get('creditos');
        return $query->result();
    }

    function buscarporqtde($limit, $offset)
    {
        $this->db->order_by("titular", "asc");
        $query = $this->db->get('creditos', $limit, $offset);
        return $query->result();
    }
    
    function lancar($valor, $tipo = 'r')
    {
        if($tipo=='r')
        {
            $updateData = array(
               'limite_reais' => $this->limite_reais - $valor,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            return (boolean) $this->db->update('creditos', $updateData);
        }
        else
        {
            $updateData = array(
               'limite_dolar' => $this->limite_dolar - $valor,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            return (boolean) $this->db->update('creditos', $updateData);
        }
                
    }
    
    function saque($valor, $tipo = 'r')
    {
        if($tipo=='r')
        {
            $updateData = array(
               'limite_saque_reais' => $this->limite_saque_reais - $valor,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            return (boolean) $this->db->update('creditos', $updateData);
        }
        else
        {
            $updateData = array(
               'limite_saque_dolar' => $this->limite_saque_dolar - $valor,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            return (boolean) $this->db->update('creditos', $updateData);
        }
    }
    
    
    /**
     * @todo pagar via débito em conta, converter valores em dólar e suporte a compras parceladas
     * @param type $valor
     * @return type 
     */
    function pagar($valor)
    {
        $updateData = array(
           'limite_reais' => $this->limite_reais + $valor,
           'dt_alteracao' => date('Y-m-d H:i:s')
        );
        $this->db->where('id',  $this->id);
        return (boolean) $this->db->update('creditos', $updateData);
    }
    
    function grava()
    {
        // inserindo novo cartão de crédito
        if($this->id == null)
        {
            $insertData = array(
               'titular' => $this->titular,
               'conta_id' => $this->conta_id,
               'bandeira' => $this->bandeira,
               'numero' => $this->numero,
               'validade' => $this->validade,
               'vencimento' => $this->vencimento,
               'limite_reais' => $this->limite_reais,
               'limite_dolar' => $this->limite_dolar,
               'limite_saque_reais' => $this->limite_saque_reais,
               'limite_saque_dolar' => $this->limite_saque_dolar,
               'dt_cadastro' => date('Y-m-d H:i:s'),
               'dt_alteracao' => date('Y-m-d H:i:s')
            );

            $this->db->insert('creditos', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //atualizando cartão de crédito existente
        else
        {
            $updateData = array(
               'titular' => $this->titular,
               'conta_id' => $this->conta_id,
               'bandeira' => $this->bandeira,
               'numero' => $this->numero,
               'validade' => $this->validade,
               'vencimento' => $this->vencimento,
               'limite_reais' => $this->limite_reais,
               'limite_dolar' => $this->limite_dolar,
               'limite_saque_reais' => $this->limite_saque_reais,
               'limite_saque_dolar' => $this->limite_saque_dolar,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            $this->db->update('creditos', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting user
        $this->db->where('id', $this->id);
        $this->db->delete('creditos');

        return TRUE;
    }

}

/* End of file creditomodel.php */
/* Location: ./application/models/creditomodel.php */
