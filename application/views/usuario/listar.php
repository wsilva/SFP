<div id="content-container">
    
    <div id="content">
        
        <h1>Lista de Usuários</h1>
        
        <div id="flash">
            <?php if( isset($mensagens) && is_array($mensagens) ): ?>
                <?php foreach ($mensagens as $tipo => $mensagem):?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php echo $tabela_usuarios; ?>
        <?php echo $pagination; ?>
        
    </div>
    
    <div id="aside">
        <div id="links" class="box">
            <h3>Usuários</h3>
            <p><a href="/usuario/novo">novo usuário</a></p>
        </div>      
    </div>
    
</div>

<script type="text/javascript">
    function removeConfirmation(usuario_id)
    {
        var resp = confirm("Deseja realmente remover?");
        if(resp)
        {
            window.location = '/usuario/remover/' + usuario_id;
        }
    }
</script>
