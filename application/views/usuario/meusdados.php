<div id="content-container">

    <div id="content">

        <h1>Meus dados</h1>

        <div id="flash">
            <?php if (isset($mensagens) && is_array($mensagens)): ?>
                <?php foreach ($mensagens as $tipo => $mensagem): ?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if( validation_errors() ): ?>
        <div id="form_errors">
            <div class="flash error">
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php endif; ?>

        <div id="body">
            
            <?php
            echo form_open('/usuario/grava_meusdados');

            echo form_fieldset('Dados');
            
            echo form_hidden('usuario_id', $usuario->id);

            echo "<p>";
            echo form_label('Nome', 'nome');
            echo "<br/>";
            $nome_input = array(
                'name' => 'nome', 
                'id' => 'nome', 
                'maxlength' => '100', 
                'value' => ( set_value('nome') ? set_value('nome') : $usuario->nome )
            );
            echo form_input($nome_input);
            echo "</p>";

            echo "<p>";
            echo form_label('E-mail', 'email');
            echo "<br/>";
            $email_input = array(
                'name' => 'email', 
                'id' => 'email', 
                'maxlength' => '100', 
                'value' => ( set_value('email') ? set_value('email') : $usuario->email )
            );
            echo form_input($email_input);
            echo "</p>";

            echo "<p>";
            echo form_submit('gravar', 'Gravar');
            echo "</p>";

            echo form_fieldset_close();
            ?>
        </div>

    </div>

    <div id="aside">
        <?php $this->load->view('usuario/meusdadoslateral'); ?>   
    </div>

</div>

<script type="text/javascript">
    function removeConfirmation(usuario_id)
    {
        var resp = confirm("Deseja realmente remover?");
        if(resp)
        {
            window.location = '/usuario/remover/' + usuario_id;
        }
    }
</script>
