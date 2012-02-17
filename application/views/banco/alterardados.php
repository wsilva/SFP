<div id="content-container">

    <div id="content">

        <h1>Editar Banco</h1>

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
            echo form_open('/banco/grava_dados');

            echo form_fieldset('Dados');
            
            echo form_hidden('id', $banco->id);

            echo "<p>";
            echo form_label('N°', 'id');
//            echo "<br/>";
            echo " ".$banco->id;
            echo "</p>";

            echo "<p>";
            echo form_label('Nome', 'nome');
            echo "<br/>";
            echo form_input(array(
                'name' => 'nome', 
                'id' => 'nome', 
                'maxlength' => '100', 
                'value' => (set_value('nome') ? set_value('nome') : $banco->nome )
                ));
            echo "</p>";

            echo "<p>";
            echo form_label('Descrição', 'descricao');
            echo "<br/>";
            echo form_input(array(
                'name' => 'descricao', 
                'id' => 'descricao', 
                'maxlength' => '255', 
                'value' => (set_value('descricao') ? set_value('descricao') : $banco->descricao )
                ));
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
        <?php $this->load->view('banco/lateral'); ?>
    </div>

</div>

<script type="text/javascript">
    function removeConfirmation(banco_id)
    {
        var resp = confirm("Deseja realmente remover?");
        if(resp)
        {
            window.location = '/banco/remover/' + banco_id;
        }
    }
</script>
