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
            Esse sistema foi desenvolvido para uso pessoal. Maiores informações <a href="http://wfsilva.com.br" target="_blank">contate-me</a>.
        </p>
        <p>
            Informe <a href="/home/login">aqui</a> seus dados para acessar e utilize as opções dos menus para navegar.
        </p>
    </div>
</div>
