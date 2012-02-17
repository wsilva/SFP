<?php

class BancoModel extends CI_Model
{

    var $id;
    var $nome = '';
    var $descricao = '';
    var $dt_cadastro = '0000-00-00 00:00:00';
    var $dt_alteracao = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('bancos');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->nome = $result[0]->nome;
                $this->descricao = $result[0]->descricao;
                $this->dt_cadastro = $result[0]->dt_cadastro;
                $this->dt_alteracao = $result[0]->dt_alteracao;
            }

            //ñ existe
            else
            {
                return FALSE;
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
        $this->db->order_by("nome", "asc");
        $query = $this->db->get('bancos');
        return $query->result();
    }

    function buscarporqtde($limit, $offset)
    {
        $this->db->order_by("nome", "asc");
        $query = $this->db->get('bancos', $limit, $offset);
        return $query->result();
    }
    
    function grava_novo()
    {

        $insertData = array(
           'id' => $this->id ,
           'nome' => $this->nome,
           'descricao' => $this->descricao,
           'dt_cadastro' => date('Y-m-d H:i:s'),
           'dt_alteracao' => date('Y-m-d H:i:s')
        );
        $this->db->insert('bancos', $insertData);
        $this->id = $this->db->insert_id(); //last inserted id

        return TRUE;
        
    }
    
    function grava()
    {
        $updateData = array(
           'nome' => $this->nome ,
           'descricao' => $this->descricao,
           'dt_alteracao' => date('Y-m-d H:i:s')
        );
        $this->db->where('id',  $this->id);
        $this->db->update('bancos', $updateData);

        return TRUE;
        
    }
    

    function remove()
    {

        //deleting user
        $this->db->where('id', $this->id);
        $this->db->delete('bancos');

        return TRUE;
    }

}

/* End of file bancomodel.php */
/* Location: ./application/models/bancomodel.php */
