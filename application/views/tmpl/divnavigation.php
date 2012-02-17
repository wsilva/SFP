<div id="navigation">
    <ul>
        <li><a href="/">Home</a></li>
        <?php if( $this->session->userdata('logged_in') ):?>
            <?php if( $this->auth->check_menu('cadastro', 'index') ): ?>
                <li><a href="/cadastro">Cadastros</a></li>            
            <?php endif; ?>
            <?php if( $this->auth->check_menu('relatorio', 'index') ): ?>
                <li><a href="/relatorio">Relatorios</a></li>            
            <?php endif; ?>
            <?php if( $this->auth->check_menu('usuario', 'index') ): ?>
                <li><a href="/usuario">Usuários</a></li>            
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>