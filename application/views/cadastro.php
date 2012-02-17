<div id="content-container">
    <div id="content">
        <h1>Cadastro de ação</h1>

        <div id="body">
            <p>Preencher os dados abaixo:</p>

            <?php
            echo form_open('/cadastro/novo');

            $optDefeitos = array(
                'SIM' => 'Sim',
                'NAO' => 'Não',
            );
            $optId = 'id="defeito"';

            echo form_fieldset('Dados da ação');

            echo "<p>";
            echo form_label('Defeito?', 'defeito');
            echo "<br/>";
            echo form_dropdown('defeito', $optDefeitos, 'NAO', $optId);
            echo "</p>";
            
            echo "<p id='defeito_select'></p>";

            echo "<p>";
            echo form_label('Código de Barras', 'cod_barra');
            echo "<br/>";
            echo form_input(array('name' => 'cod_barra', 'id' => 'cod_barra', 'maxlength' => '100'));
            echo "</p>";

            echo "<p>";
            echo form_submit('finalizar', 'Finalizar');
            echo "</p>";

            echo form_hidden('dt_entrada', date('Y-m-d H:i:s'));
            echo form_fieldset_close();

            echo form_close();
            ?>
        </div>

    </div>
</div>  
<script type="text/javascript">
    $(document).ready(function () {
        
        $("#cod_barra").focus();
        $("#defeito_select").hide();
        
        $("#defeito").change( function(){
            
        if($("#defeito").val()=='NAO')
        {
            $("#defeito_select").fadeOut('fast');
        }
        else
        {
            $("#defeito_select").fadeIn('fast');
            $("#defeito_select").html('...Carregando...');
            $.ajax({
                type: "POST",
                url: "/cadastro/listadefeitos",
//                data: dataString,
                cache: false,
                success: function(resp)
                {
                    $("#defeito_select").html(resp);
                } 
            });
        }
            
        })
    });
    
</script>
