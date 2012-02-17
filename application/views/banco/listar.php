<div id="content-container">
    
    <div id="content">
        
        <h1>Bancos</h1>
        
        <div id="flash">
            <?php if( isset($mensagens) && is_array($mensagens) ): ?>
                <?php foreach ($mensagens as $tipo => $mensagem):?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php echo $tabela_bancos; ?>
        <?php echo $pagination; ?>
        
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
