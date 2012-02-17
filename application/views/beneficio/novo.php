<div id="content-container">

    <div id="content">

        <h1>Novo Cartão Benefício</h1>

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
            echo form_open('/beneficio/grava_novo');

            echo form_fieldset('Dados');

            echo "<p>";
            echo form_label('Titular', 'titular');
            echo "<br/>";
            echo form_input(array('name' => 'titular', 'id' => 'titular', 'maxlength' => '255', 'value' => set_value('titular')));
            echo "</p>";

            echo "<p>";
            echo form_label('Tipo', 'tipo');
            echo "<br/>";
            echo form_input(array('name' => 'tipo', 'id' => 'tipo', 'maxlength' => '255', 'value' => set_value('tipo')));
            echo "</p>";

            echo "<p>";
            echo form_label('Bandeira', 'bandeira');
            echo "<br/>";
            echo form_input(array('name' => 'bandeira', 'id' => 'bandeira', 'maxlength' => '50', 'value' => set_value('bandeira')));
            echo "</p>";

            echo "<p>";
            echo form_label('Número', 'numero');
            echo "<br/>";
            echo form_input(array('name' => 'numero_a', 'id' => 'numero_a', 'size' => '4', 'maxlength' => '4', 'value' => set_value('numero_a')));
            echo form_input(array('name' => 'numero_b', 'id' => 'numero_b', 'size' => '4', 'maxlength' => '4', 'value' => set_value('numero_b')));
            echo form_input(array('name' => 'numero_c', 'id' => 'numero_c', 'size' => '4', 'maxlength' => '4', 'value' => set_value('numero_c')));
            echo form_input(array('name' => 'numero_d', 'id' => 'numero_d', 'size' => '4', 'maxlength' => '4', 'value' => set_value('numero_d')));
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
            echo form_dropdown(
                    'validade_a', 
                    $mes,
                    set_value('validade_a')
                    );
            echo "/";
            echo form_input(array(
                'name' => 'validade_b', 
                'id' => 'validade_b', 
                'size' => '4', 
                'maxlength' => '4', 
                'value' => set_value('validade_b')
                ));

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
        <?php $this->load->view('beneficio/lateral'); ?>
    </div>

</div>

<script type="text/javascript">
    function removeConfirmation(beneficio_id)
    {
        var resp = confirm("Deseja realmente remover?");
        if(resp)
        {
            window.location = '/beneficio/remover/' + beneficio_id;
        }
    }
    
    $(document).ready(function(){
        $('#numero_a').keyup(function(){
            if($('#numero_a').val().length==4)
                $('#numero_b').focus();
        });
        $('#numero_b').keyup(function(){
            if($('#numero_b').val().length==4)
                $('#numero_c').focus();
        });
        $('#numero_c').keyup(function(){
            if($('#numero_c').val().length==4)
                $('#numero_d').focus();
        });
    });
    
</script>
