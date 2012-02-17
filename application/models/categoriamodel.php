<?php

class CategoriaModel extends CI_Model
{

    var $id;
    var $titulo = '';
    var $dt_cadastro = '0000-00-00 00:00:00';
    var $dt_alteracao = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('categorias');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->titulo = $result[0]->titulo;
                $this->dt_cadastro = $result[0]->dt_cadastro;
                $this->dt_alteracao = $result[0]->dt_alteracao;
            }

            //ñ existe
            else
            {
                die("ooops: Categoria id does not exists! <br/> What are you doing?");
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
        $this->db->order_by("titulo", "asc");
        $query = $this->db->get('categorias');
        return $query->result();
    }

    function buscarporqtde($limit, $offset)
    {
        $this->db->order_by("titulo", "asc");
        $query = $this->db->get('categorias', $limit, $offset);
        return $query->result();
    }
    
    function grava()
    {
        //inserting new user
        if($this->id == null)
        {
            $insertData = array(
               'id' => $this->id ,
               'titulo' => $this->titulo,
               'dt_cadastro' => date('Y-m-d H:i:s'),
               'dt_alteracao' => date('Y-m-d H:i:s')
            );

            $this->db->insert('categorias', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //updating existing user
        else
        {
            $updateData = array(
               'titulo' => $this->titulo ,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            $this->db->update('categorias', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting user
        $this->db->where('id', $this->id);
        $this->db->delete('categorias');

        return TRUE;
    }

}

/* End of file categoriamodel.php */
/* Location: ./application/models/categoriamodel.php */
