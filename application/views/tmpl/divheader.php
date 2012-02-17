<div id="header">
    <div id="nav_user">
        <ul>
            <?php if ($this->session->userdata('logged_in')): ?>
                <li><a href="/usuario/meusdados">Meus dados</a></li>
                <li><a href="/home/logout">Sair</a></li> 
            <?php else: ?>
                <li><a href="/home/login">Autenticar</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <h1>
        <a href="/" >Sistema Financeiro Pessoal</a>
    </h1>
</div>
