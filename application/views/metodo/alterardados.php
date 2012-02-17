<div id="content-container">

    <div id="content">

        <h1>Editar Usuário</h1>

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
            echo form_open('/metodo/grava_dados');

            echo form_fieldset('Dados');
            
            echo form_hidden('metodo_id', $metodo->id);

            echo "<p>";
            echo form_label('Nome da permissão', 'apelido');
            echo "<br/>";
            $apelido_input = array(
                'name' => 'apelido', 
                'id' => 'apelido', 
                'maxlength' => '255', 
                'value' => (set_value('apelido') ? set_value('apelido') : $metodo->apelido )
            );
            echo form_input($apelido_input);
            echo "</p>";

            echo "<p>";
            echo form_label('Primeiro Parâmetro', 'classe');
            echo "<br/>";
            $classe_input = array(
                'name' => 'classe', 
                'id' => 'classe', 
                'maxlength' => '50', 
                'value' => ( set_value('classe') ? set_value('classe') : $metodo->classe )
            );
            echo form_input($classe_input);
            echo "</p>";

            echo "<p>";
            echo form_label('Segundo Parâmetro', 'metodo');
            echo "<br/>";
            $metodo_input = array(
                'name' => 'metodo', 
                'id' => 'metodo', 
                'maxlength' => '50', 
                'value' => ( set_value('metodo') ? set_value('metodo') : $metodo->metodo )
            );
            echo form_input($metodo_input);
            echo "</p>";

            echo "<p>";
            echo form_label('Tipo', 'privado');
            echo "<br/>";
            $privado0_input = array(
                'name' => 'privado', 
                'id' => 'privado0', 
                'checked' => ! (boolean) $metodo->privado,
                'value' => 0
            );
            echo form_radio($privado0_input);
            echo form_label('publico', 'privado0');
            
            $privado1_input = array(
                'name' => 'privado', 
                'id' => 'privado1', 
                'checked' => (boolean) $metodo->privado,
                'value' => 1
            );
            echo form_radio($privado1_input);
            echo form_label('privado', 'privado1');
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
        <div id="links" class="box">
            <h3>Usuários</h3>
            <p><a href="/usuario">voltar</a></p>
        </div>      
    </div>

</div>

