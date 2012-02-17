<div id="content-container">

    <div id="content">

        <h1>Editar Categoria</h1>

        <div id="flash">
            <?php if (isset($mensagens) && is_array($mensagens)): ?>
                <?php foreach ($mensagens as $tipo => $mensagem): ?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (validation_errors()): ?>
            <div id="form_errors">
                <div class="flash error">
                    <?php echo validation_errors(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div id="body">

            <?php
            echo form_open('/categoria/grava_dados');

            echo form_fieldset('Dados');

            echo form_hidden('categoria_id', $categoria->id);

            echo "<p>";
            echo form_label('Título', 'titulo');
            echo "<br/>";
            $categoria_input = array(
                'name' => 'titulo',
                'id' => 'titulo',
                'maxlength' => '255',
                'value' => (set_value('titulo') ? set_value('titulo') : $categoria->titulo )
            );
            echo form_input($categoria_input);
            echo "</p>";

            echo "<p>";
            echo form_label('Tipo', 'tipo');
            echo "<br/>";
            $opts = array(
                'd' => 'Débito',
                'c' => 'Crédito',
            );
            echo form_dropdown('tipo', $opts, $categoria->tipo);
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
