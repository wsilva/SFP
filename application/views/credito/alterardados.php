<div id="content-container">

    <div id="content">

        <h1>Editar Cartão Crédito</h1>

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
            echo form_open('/credito/grava_dados');

            echo form_fieldset('Dados');

            echo form_hidden('credito_id', $credito->id);
            
            echo "<p>";
            echo form_label('Titular', 'titular');
            echo "<br/>";
            echo form_input(array(
                'name' => 'titular', 
                'id' => 
                'titular', 
                'maxlength' => '255', 
                'value' => (set_value('titular') ? set_value('titular') : $credito->titular )
                ));
            echo "</p>";

            echo "<p>";
            echo form_label('Bandeira', 'bandeira');
            echo "<br/>";
            echo form_input(array(
                'name' => 'bandeira', 
                'id' => 'bandeira', 
                'maxlength' => '50', 
                'value' => (set_value('bandeira') ? set_value('bandeira') : $credito->bandeira )
                ));
            echo "</p>";

            echo "<p>";
            echo form_label('Número', 'numero');
            echo "<br/>";
            
            echo form_input(array(
                'name' => 'numero_a', 
                'id' => 'numero_a', 
                'size' => '4', 
                'maxlength' => '4', 
                'value' => (set_value('numero_a') ? set_value('numero_a') : substr($credito->numero, 0, 4) )
                ));
            
            echo form_input(array(
                'name' => 'numero_b', 
                'id' => 'numero_b', 
                'size' => '4', 
                'maxlength' => '4', 
                'value' => (set_value('numero_b') ? set_value('numero_b') : substr($credito->numero, 3, 4) )
                ));
            
            echo form_input(array(
                'name' => 'numero_c', 
                'id' => 'numero_c', 
                'size' => '4', 
                'maxlength' => '4', 
                'value' => (set_value('numero_c') ? set_value('numero_c') : substr($credito->numero, 7, 4) )
                ));
            
            echo form_input(array(
                'name' => 'numero_d', 
                'id' => 'numero_d', 
                'size' => '4', 
                'maxlength' => '4', 
                'value' => (set_value('numero_d') ? set_value('numero_d') : substr($credito->numero, 11, 4) )
                ));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Validade', 'validade');
            echo "<br/>";
            $mes = array(
                '01' => 'Jan',
                '02' => 'Fev',
                '03' => 'Mar',
                '04' => 'Abr',
                '05' => 'Mai',
                '06' => 'Jun',
                '07' => 'Jul',
                '08' => 'Ago',
                '09' => 'Set',
                '10' => 'Out',
                '11' => 'Nov',
                '12' => 'Dez'
            );
            $this->load->helper('date');
            $validade_mes = mdate('%m', mysql_to_unix($credito->validade));
            $validade_ano = mdate('%Y', mysql_to_unix($credito->validade));
            echo form_dropdown(
                    'validade_a', 
                    $mes,
                    (set_value('validade_a') ? set_value('validade_a') : $validade_mes )
                    );
            echo "/";
            echo form_input(array(
                'name' => 'validade_b', 
                'id' => 'validade_b', 
                'size' => '4', 
                'maxlength' => '4', 
                'value' => set_value('validade_b') ? set_value('validade_b') : $validade_ano
                ));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Vencimento', 'vencimento');
            echo "<br/>";
            echo form_input(array(
                'name' => 'vencimento', 
                'id' => 'vencimento', 
                'maxlength' => '2', 
                'size' => '2', 
                'value' => (set_value('vencimento') ? set_value('vencimento') : $credito->vencimento )
                ));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Limite em reais (R$)', 'limite_reais');
            echo "<br/>";
            echo form_input(array(
                'name' => 'limite_reais', 
                'id' => 'limite_reais', 
                'class' => 'reais', 
                'maxlength' => '15', 
                'size' => '15', 
                'value' => (set_value('limite_reais') ? set_value('limite_reais') : $credito->limite_reais )
                ));
            echo "</p>";

            echo "<p>";
            echo form_label('Limite em dolares (US$)', 'limite_dolar');
            echo "<br/>";
            echo form_input(array(
                'name' => 'limite_dolar', 
                'id' => 'limite_dolar', 
                'class' => 'dolares', 
                'maxlength' => '15', 
                'size' => '15', 
                'value' => (set_value('limite_dolar') ? set_value('limite_dolar') : $credito->limite_dolar )
                ));
            echo "</p>";

            echo "<p>";
            echo form_label('Limite de saques em reais (R$)', 'limite_saque_reais');
            echo "<br/>";
            echo form_input(array(
                'name' => 'limite_saque_reais', 
                'id' => 'limite_saque_reais', 
                'class' => 'reais', 
                'maxlength' => '15', 
                'size' => '15', 
                'value' => (set_value('limite_saque_reais') ? set_value('limite_saque_reais') : $credito->limite_saque_reais )
                ));
            echo "</p>";

            echo "<p>";
            echo form_label('Limite de saques em dolares (US$)', 'limite_saque_dolar');
            echo "<br/>";
            echo form_input(array(
                'name' => 'limite_saque_dolar', 
                'id' => 'limite_saque_dolar', 
                'class' => 'dolares', 
                'maxlength' => '15', 
                'size' => '15', 
                'value' => (set_value('limite_saque_dolar') ? set_value('limite_saque_dolar') : $credito->limite_saque_dolar )
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
        <?php $this->load->view('credito/lateral'); ?>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".reais").priceFormat({
            prefix: 'R$ ',
            centsSeparator: ',',
            thousandsSeparator: '.',
            clearPrefix: true
        });
            
        $(".dolares").priceFormat({
            clearPrefix: true
        });
    });
</script>
