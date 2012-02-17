<div id="content-container">
    
    <div id="content">
        
        <h1>Contas</h1>
        
        <div id="flash">
            <?php if( isset($mensagens) && is_array($mensagens) ): ?>
                <?php foreach ($mensagens as $tipo => $mensagem):?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php echo $tabela_contas; ?>
        <?php echo $pagination; ?>
        
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
</script>
