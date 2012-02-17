<?php

class LancamentoModel extends CI_Model
{

    var $id;
    var $nome = '';
    var $descricao = '';
    var $categoria_id = '';
    var $imagem = '';
    var $meio = '';
    var $tipo = '';
    var $valor = 0.0;
    var $dt_cadastro = '0000-00-00 00:00:00';
    var $dt_alteracao = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('lancamentos');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->nome = $result[0]->nome;
                $this->descricao = $result[0]->descricao;
                $this->categoria_id = $result[0]->categoria_id;
                $this->imagem = $result[0]->imagem;
                $this->meio = $result[0]->meio;
                $this->tipo = $result[0]->tipo;
                $this->valor = $result[0]->valor;
                $this->dt_cadastro = $result[0]->dt_cadastro;
                $this->dt_alteracao = $result[0]->dt_alteracao;
            }

            //ñ existe
            else
            {
                die("ooops: Lancamento id does not exists! <br/> What are you doing?");
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
        $query = $this->db->get('lancamentos');
        return $query->result();
    }

    function buscarporqtde($limit, $offset)
    {
        $this->db->order_by("nome", "asc");
        $query = $this->db->get('lancamentos', $limit, $offset);
        return $query->result();
    }
    
    function grava()
    {
        //inserting new user
        if($this->id == null)
        {
            $insertData = array(
               'id' => $this->id ,
               'nome' => $this->nome,
               'descricao' => $this->descricao,
               'categoria_id' => $this->categoria_id,
               'imagem' => $this->imagem,
               'meio' => $this->meio,
               'tipo' => $this->tipo,
               'valor' => $this->valor,
               'dt_cadastro' => date('Y-m-d H:i:s'),
               'dt_alteracao' => date('Y-m-d H:i:s')
            );

            $this->db->insert('lancamentos', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //updating existing user
        else
        {
            $updateData = array(
               'nome' => $this->nome ,
               'descricao' => $this->descricao ,
               'categoria_id' => $this->categoria_id ,
               'imagem' => $this->imagem ,
               'meio' => $this->meio,
               'tipo' => $this->tipo,
               'valor' => $this->valor,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            $this->db->update('lancamentos', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting user
        $this->db->where('id', $this->id);
        $this->db->delete('lancamentos');

        return TRUE;
    }

}

/* End of file lancamentomodel.php */
/* Location: ./application/models/lancamentomodel.php */
