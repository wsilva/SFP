<?php

class UsuarioModel extends CI_Model
{

    var $id;
    var $usuario = '';
    var $senha = '';
    var $nome = '';
    var $email = '';
    var $dt_cadastro = '0000-00-00 00:00:00';
    var $dt_alteracao = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        parent::__construct();

        if ($id)
        {
            $this->db->where('id', $id);
            $query = $this->db->get('usuarios');

            if ($query->num_rows == 1)
            {
                $result = $query->result();

                $this->id = $id;
                $this->nome = $result[0]->nome;
                $this->usuario = $result[0]->usuario;
                $this->senha = $result[0]->senha;
                $this->email = $result[0]->email;
                $this->dt_cadastro = $result[0]->dt_cadastro;
                $this->dt_alteracao = $result[0]->dt_alteracao;
            }

            //ñ existe
            else
            {
                die("ooops: User id does not exists! <br/> What are you doing?");
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
        $query = $this->db->get('usuarios');
        return $query->result();
    }

    function buscarporqtde($limit, $offset)
    {
        $this->db->order_by("nome", "asc");
        $query = $this->db->get('usuarios', $limit, $offset);
        return $query->result();
    }
    
    function alterasenha()
    {
        //no user id
        if($this->id == null)
        {
            return FALSE;
        }

        //updating password from user
        else
        {
            $updateData = array(
               'senha' => $this->senha,
               'updated' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            $this->db->update('usuarios', $updateData);

            return TRUE;
        }
    }

    function grava()
    {
        //inserting new user
        if($this->id == null)
        {
            $insertData = array(
               'id' => $this->id ,
               'nome' => $this->nome ,
               'usuario' => $this->usuario,
               'senha' => $this->senha,
               'email' => $this->email,
               'dt_cadastro' => date('Y-m-d H:i:s'),
               'dt_alteracao' => date('Y-m-d H:i:s')
            );

            $this->db->insert('usuarios', $insertData);
            $this->id = $this->db->insert_id(); //last inserted id
        }

        //updating existing user
        else
        {
            $updateData = array(
               'nome' => $this->nome ,
               'usuario' => $this->usuario,
               'email' => $this->email,
               'dt_alteracao' => date('Y-m-d H:i:s')
            );
            $this->db->where('id',  $this->id);
            $this->db->update('usuarios', $updateData);
        }

        return TRUE;
    }

    function remove()
    {

        //deleting user
        $this->db->where('id', $this->id);
        $this->db->delete('usuarios');

        return TRUE;
    }

}

/* End of file usuariomodel.php */
/* Location: ./application/models/usuariomodel.php */
