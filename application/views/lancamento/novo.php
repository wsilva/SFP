<div id="content-container">

    <div id="content">

        <h1>Novo Lançamento</h1>

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
            echo form_open('/lancamento/grava_novo');

            echo form_fieldset('Dados');

            echo "<p>";
            echo form_label('Nome', 'nome');
            echo "<br/>";
            echo form_input(array('name' => 'nome', 'id' => 'nome', 'maxlength' => '100', 'value' => set_value('nome')));
            echo "</p>";

            echo "<p>";
            echo form_label('Descrição', 'descricao');
            echo "<br/>";
            echo form_input(array('name' => 'descricao', 'id' => 'descricao', 'maxlength' => '255', 'value' => set_value('descricao')));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Meio', 'meio');
            echo "<br/>";
            $opts = array(
                  'conta'  => 'Conta Corrente / Poupança',
                  'credito'    => 'Cartão de Crédito',
                  'beneficio'    => 'Cartão de Benefício'
                );
            echo form_dropdown('meio', $opts, 'conta', set_value('meio'));
            echo "</p>";
            
            echo "<p>";
            echo form_label('Categoria', 'categoria');
            echo "<br/>";
            $opts = array(
                  ''  => 'Selecione'
                );
            foreach($categorias as $categoria)
                $opts[$categoria->id] = $categoria->titulo;
            echo form_dropdown('categoria_id', $opts, '', set_value('categoria_id'));
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
            echo form_label('Valor (R$)', 'valor');
            echo "<br/>";
            echo form_input(array('name' => 'valor', 'id' => 'valor', 'class' => 'reais', 'maxlength' => '15', 'size' => '15', 'value' => set_value('valor')));
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
        <?php $this->load->view('lancamento/lateral'); ?>
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
    });
    
</script>
