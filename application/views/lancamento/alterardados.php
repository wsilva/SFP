<div id="content-container">

    <div id="content">

        <h1>Editar Lançamento</h1>

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
            echo form_open('/lancamento/grava_dados');

            echo form_fieldset('Dados');

            echo form_hidden('lancamento_id', $lancamento->id);

            echo "<p>";
            echo form_label('Nome', 'nome');
            echo "<br/>";
            $lancamento_input = array(
                'name' => 'nome',
                'id' => 'nome',
                'maxlength' => '100',
                'value' => (set_value('nome') ? set_value('nome') : $lancamento->nome )
            );
            echo form_input($lancamento_input);
            echo "</p>";

            echo "<p>";
            echo form_label('Descrição', 'descricao');
            echo "<br/>";
            $lancamento_input = array(
                'name' => 'descricao',
                'id' => 'descricao',
                'maxlength' => '255',
                'value' => (set_value('descricao') ? set_value('descricao') : $lancamento->descricao )
            );
            echo form_input($lancamento_input);
            echo "</p>";

            echo "<p>";
            echo form_label('Meio', 'meio');
            echo "<br/>";
            $opts = array(
                'conta' => 'Conta Corrente / Poupança',
                'credito' => 'Cartão de Crédito',
                'beneficio' => 'Cartão de Benefício'
            );
            echo form_dropdown('meio', $opts, $lancamento->meio, (set_value('meio') ? set_value('meio') : $lancamento->meio));
            echo "</p>";

            echo "<p>";
            echo form_label('Categoria', 'categoria');
            echo "<br/>";
            $opts = array(
                '' => 'Selecione'
            );
            foreach ($categorias as $categoria)
                $opts[$categoria->id] = $categoria->titulo;
            echo form_dropdown('categoria_id', $opts, $lancamento->categoria_id, (set_value('categoria_id') ? set_value('categoria_id') : $lancamento->categoria_id));
            echo "</p>";

            echo "<p>";
            echo form_label('Tipo', 'tipo');
            echo "<br/>";
            $opts = array(
                'd' => 'Débito',
                'c' => 'Crédito',
            );
            echo form_dropdown('tipo', $opts, $lancamento->tipo, (set_value('tipo') ? set_value('tipo') : $lancamento->tipo));
            echo "</p>";

            echo "<p>";
            echo form_label('Valor (R$)', 'valor');
            echo "<br/>";
            echo form_input(array(
                'name' => 'valor', 
                'id' => 'valor', 
                'class' => 'reais', 
                'maxlength' => '15', 
                'size' => '15', 
                'value' => (set_value('valor') ? set_value('valor') : $lancamento->valor )
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
