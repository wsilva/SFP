<div id="content-container">
    <div id="content">
        
        <div id="flash">
            <?php if( isset($mensagens) && is_array($mensagens) ): ?>
                <?php foreach ($mensagens as $tipo => $mensagem):?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <h2>
            Bem vindo
        </h2>
        <p>
            Utilize as opções de menu para navegar.
        </p>
    </div>
</div>
