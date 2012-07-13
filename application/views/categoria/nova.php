<div id="content-container">

    <div id="content">

        <h1>Nova Categoria</h1>

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
            echo form_open('/categoria/grava_nova');

            echo form_fieldset('Dados');

            echo "<p>";
            echo form_label('Titulo', 'titulo');
            echo "<br/>";
            echo form_input(array('name' => 'titulo', 'id' => 'titulo', 'maxlength' => '255', 'value' => set_value('categoria')));
            echo "</p>";

            echo "<p>";
            echo form_label('Tipo', 'tipo');
            echo "<br/>";
            $opts = array(
                  'd'  => 'Débito',
                  'c'    => 'Crédito',
                );
            echo form_dropdown('tipo', $opts, 'd', set_value('tipo'));
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
        <?php $this->load->view('cadastro/lateral'); ?>
        <?php $this->load->view('categoria/lateral'); ?>
    </div>

</div>

<script type="text/javascript">
    function removeConfirmation(categoria_id)
    {
        var resp = confirm("Deseja realmente remover?");
        if(resp)
        {
            window.location = '/categoria/remover/' + categoria_id;
        }
    }
</script>
