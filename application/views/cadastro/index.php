<div id="content-container">
    
    <div id="content">
        
        <h1>Cadastros</h1>
        
        <div id="flash">
            <?php if( isset($mensagens) && is_array($mensagens) ): ?>
                <?php foreach ($mensagens as $tipo => $mensagem):?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        Selecione uma opção do menu lateral
        
    </div>
    
    <div id="aside">
        <?php $this->load->view('cadastro/lateral'); ?>
    </div>
    
</div>