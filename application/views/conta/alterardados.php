<div id="content-container">

    <div id="content">

        <h1>Editar Conta</h1>

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
            echo form_open('/conta/grava_dados');

            echo form_fieldset('Dados');

            echo form_hidden('conta_id', $conta->id);
            
            echo "<p>";
            echo form_label('Titular', 'titular');
            echo "<br/>";
            echo form_input(array(
                'name' => 'titular', 
                'id' => 
                'titular', 
                'maxlength' => '255', 
                'value' => (set_value('titular') ? set_value('titular') : $conta->titular )
                ));
            echo "</p>";

            echo "<p>";
            echo form_label('Tipo', 'tipo');
            echo "<br/>";
            $tipo = array(
                'c' => 'Corrente',
                'p' => 'Poupança',
                'cp' => 'Corrente/Poupança'
            );
            echo form_dropdown(
                    'tipo', $tipo, (set_value('tipo') ? set_value('tipo') : $conta->tipo )
            );
            echo "</p>";

            echo "<p>";
            echo form_label('Banco', 'banco_id');
            echo "<br/>";
            echo form_dropdown(
                    'banco_id', $bancos, (set_value('banco_id') ? set_value('banco_id') : $conta->banco_id )
            );
            echo "</p>";
            
             echo "<p>";
            echo form_label('Agência', 'num_agencia');
            echo "<br/>";
            echo form_input(array('name' => 'num_agencia', 'id' => 'num_agencia', 'size' => '5', 'maxlength' => '5', 'value' => (set_value('num_agencia') ? set_value('num_agencia') : $conta->num_agencia )));
            echo form_input(array('name' => 'ag_digito', 'id' => 'ag_digito', 'size' => '2', 'maxlength' => '2', 'value' => (set_value('ag_digito') ? set_value('ag_digito') : $conta->ag_digito )));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Conta', 'num_conta');
            echo "<br/>";
            echo form_input(array('name' => 'num_conta', 'id' => 'num_conta', 'size' => '8', 'maxlength' => '8', 'value' => (set_value('num_conta') ? set_value('num_conta') : $conta->num_conta )));
            echo form_input(array('name' => 'conta_digito', 'id' => 'conta_digito', 'size' => '2', 'maxlength' => '2', 'value' => (set_value('conta_digito') ? set_value('conta_digito') : $conta->conta_digito )));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Saldo', 'saldo');
            echo "<br/>";
            echo form_input(array('name' => 'saldo', 'id' => 'saldo', 'size' => '13', 'maxlength' => '13', 'class'=>'reais', 'value' => (set_value('saldo') ? set_value('saldo') : $conta->saldo )));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Limite', 'limite');
            echo "<br/>";
            echo form_input(array('name' => 'limite', 'id' => 'limite', 'size' => '13', 'maxlength' => '13', 'class'=>'reais', 'value' => (set_value('limite') ? set_value('limite') : $conta->limite )));
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
        <?php $this->load->view('conta/lateral'); ?>
    </div>

</div>

<script type="text/javascript">
    function removeConfirmation(conta_id)
    {
        var resp = confirm("Deseja realmente remover?");
        if(resp)
        {
            window.location = '/conta/remover/' + conta_id;
        }
    }
    
    $(document).ready(function(){
        $(".reais").priceFormat({
            prefix: 'R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            clearPrefix: true
        });
    });
</script>
