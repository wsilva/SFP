<?php

class MetodoModel extends CI_Model
{

    var $id;
    var $classe = '';
    var $metodo = '';
    var $apelido = '';
    var $privado = 1;

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('metodos');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = (int) $id;
                $this->classe = (string) $result[0]->classe;
                $this->metodo = (string)$result[0]->metodo;
                $this->apelido = (string)$result[0]->apelido;
                $this->privado = (boolean) $result[0]->privado;
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
  

    function grava()
    {
        //inserindo novo metodo
        if($this->id == null)
        {
            $insertData = array(
               'id' => $this->id ,
               'classe' => $this->classe ,
               'metodo' => $this->metodo,
               'apelido' => $this->apelido,
               'privado' => $this->privado
            );

            $this->db->insert('metodos', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //alterando metodo
        else
        {
            $updateData = array(
               'classe' => $this->classe ,
               'metodo' => $this->metodo,
               'apelido' => $this->apelido,
               'privado' => $this->privado
            );
            $this->db->where('id',  $this->id);
            $this->db->update('metodos', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting
        $this->db->where('id', $this->id);
        $this->db->delete('metodos');

        return TRUE;
    }
    
    function buscartodos()
    {
        $this->db->order_by("apelido", "asc");
        $query = $this->db->get('metodos');
        return $query->result();
    }
    

}

/* End of file metodomodel.php */
/* Location: ./application/models/metodomodel.php */
