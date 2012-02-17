<div id="content-container">

    <div id="content">

        <h1>Definir Nova Senha</h1>

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
            
            <p>
                <b><?php echo $usuario->nome; ?>, informe uma nova senha</b>
            </p>
            
            <?php
            echo form_open('/usuario/grava_minhasenha');

            echo form_fieldset('Dados');
            
            echo form_hidden('usuario_id', $usuario->id);

            echo "<p>";
            echo form_label('Senha', 'senha');
            echo "<br/>";
            echo form_password(array('name' => 'senha', 'id' => 'senha', 'maxlength' => '100'));
            echo "</p>";

            echo "<p>";
            echo form_label('Confirmação', 'confirmacao');
            echo "<br/>";
            echo form_password(array('name' => 'confirmacao', 'id' => 'confirmacao', 'maxlength' => '100'));
            echo "</p>";

            echo "<p>";
            echo form_submit('gravar', 'Gravar');
            echo "</p>";

            echo form_fieldset_close();

            echo form_close();
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
