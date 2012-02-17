<div id="content-container">
    
    <div id="content">
        
        <h1>Categorias de Gastos</h1>
        
        <div id="flash">
            <?php if( isset($mensagens) && is_array($mensagens) ): ?>
                <?php foreach ($mensagens as $tipo => $mensagem):?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php echo $tabela_categorias; ?>
        <?php echo $pagination; ?>
        
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
