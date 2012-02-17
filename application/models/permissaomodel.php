<?php

class PermissaoModel extends CI_Model
{

    var $id;
    var $usuario_id = '';
    var $metodo_id = '';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('permissoes');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->usuario_id = $result[0]->usuario_id;
                $this->metodo_id = $result[0]->metodo_id;
            }

            //ñ existe
            else
            {
                die("ooops: id does not exists! <br/> What are you doing?");
            }
        }

        //without user id we create a new one
        else
        {
            $this->id = null;
        }
        
        return $this;
    }

    
    function buscarporusuario($usuario_id)
    {
        $this->db->where('usuario_id',  $usuario_id);
        $query = $this->db->get('permissoes');
        return $query->result();
    }
    
    function buscarpormetodo($metodo_id)
    {
        $this->db->where('metodo_id',  $metodo_id);
        $query = $this->db->get('permissoes');
        return $query->result();
    }    

    function grava()
    {
        //inserindo nova permissao
        if($this->id == null)
        {
            $insertData = array(
               'id' => $this->id ,
               'usuario_id' => $this->usuario_id ,
               'metodo_id' => $this->metodo_id
            );

            $this->db->insert('permissoes', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //alterando permissao
        else
        {
            $updateData = array(
               'usuario_id' => $this->usuario_id ,
               'metodo_id' => $this->metodo_id
            );
            $this->db->where('id',  $this->id);
            $this->db->update('permissoes', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting
        $this->db->where('id', $this->id);
        $this->db->delete('permissoes');

        return TRUE;
    }
    
    function removeporusuario($usuario_id)
    {

        //deleting
        $this->db->where('usuario_id', $usuario_id);
        $this->db->delete('permissoes');

        return TRUE;
    }
    
    function removepormetodo($metodo_id)
    {

        //deleting 
        $this->db->where('metodo_id', $metodo_id);
        $this->db->delete('permissoes');

        return TRUE;
    }

}

/* End of file permissaomodel.php */
/* Location: ./application/models/permissaomodel.php */
