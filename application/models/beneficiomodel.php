<?php

class BeneficioModel extends CI_Model
{

    var $id;
    var $titular = '';
    var $tipo = '';
    var $bandeira = '';
    var $numero = '';
    var $validade = '0000-00-00 00:00:00';
    var $saldo = 0.0;
    var $dt_cadastro = '0000-00-00 00:00:00';
    var $dt_alteracao = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('beneficios');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->titular = $result[0]->titular;
                $this->tipo = $result[0]->tipo;
                $this->bandeira = $result[0]->bandeira;
                $this->numero = $result[0]->numero;
                $this->validade = $result[0]->validade;
                $this->saldo = $result[0]->saldo;
                $this->dt_cadastro = $result[0]->dt_cadastro;
                $this->dt_alteracao = $result[0]->dt_alteracao;
            }

            //ñ existe
            else
            {
                die("ooops: Beneficio id does not exists! <br/> What are you doing?");
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
        $query = $this->db->get('beneficios');
        return $query->result();
    }

    function buscarporqtde($limit, $offset)
    {
        $this->db->order_by("titular", "asc");
        $query = $this->db->get('beneficios', $limit, $offset);
        return $query->result();
    }
    
    function debitar($valor)
    {
        $updateData = array(
           'saldo' => $this->saldo - $valor,
           'dt_alteracao' => date('Y-m-d H:i:s')
        );
        $this->db->where('id',  $this->id);
        return (boolean) $this->db->update('beneficios', $updateData);
    }
    
    function creditar($valor)
    {
        $updateData = array(
           'saldo' => $this->saldo + $valor,
           'dt_alteracao' => date('Y-m-d H:i:s')
        );
        $this->db->where('id',  $this->id);
        return (boolean) $this->db->update('beneficios', $updateData);
    }
    
    function grava()
    {
        //inserting new user
        if($this->id == null)
        {
            $insertData = array(
               'id' => $this->id ,
               'titular' => $this->titular,
               'tipo' => $this->tipo,
               'bandeira' => $this->bandeira,
               'numero' => $this->numero,
               'validade' => $this->validade,
               'saldo' => $this->saldo,
               'dt_cadastro' => date('Y-m-d H:i:s'),
               'dt_alteracao' => date('Y-m-d H:i:s')
            );

            $this->db->insert('beneficios', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //updating existing user
        else
        {
            $updateData = array(
               'titular' => $this->titular ,
               'tipo' => $this->tipo,
               'bandeira' => $this->bandeira,
               'numero' => $this->numero,
               'validade' => $this->validade,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            $this->db->update('beneficios', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting user
        $this->db->where('id', $this->id);
        $this->db->delete('beneficios');

        return TRUE;
    }

}

/* End of file beneficiomodel.php */
/* Location: ./application/models/beneficiomodel.php */
