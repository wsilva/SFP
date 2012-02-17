<div id="content-container">

    <div id="content">

        <h1>Lista de Permissões</h1>

        <div id="flash">
            <?php if (isset($mensagens) && is_array($mensagens)): ?>
                <?php foreach ($mensagens as $tipo => $mensagem): ?>
                    <div class="flash <?php echo $tipo; ?>"><?php echo $mensagem ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php echo form_open('/usuario/grava_permissoes'); ?>
        
        <?php echo form_hidden('usuario_id', $usuario->id); ?>

        <p>
            <b>Usuário:</b> <?php echo $usuario->nome; ?>
        </p>

        <table class="tabledetail">
            <thead>
                <tr>
                    <th>Nome da Permissão</th>
                    <th>Primeiro Parâmetro</th>
                    <th>Segundo Parâmetro</th>
                    <th>Tipo</th>
                    <th>Permitir</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metodos as $metodo): ?>
                    <tr>
                        <td><a href="/metodo/alterar/<?php echo $metodo->id; ?>"><?php echo $metodo->apelido; ?></a></td>
                        <td><?php echo $metodo->classe; ?></td>
                        <td><?php echo $metodo->metodo; ?></td>
                        <td><?php echo ($metodo->privado ? "privado" : "publico"); ?></td>
                        <td>
                            <?php if ($metodo->tem_permissao): ?>
                                <input type="checkbox" id="metodos" name="metodos[]" value="<?php echo $metodo->id; ?>" checked="checked" />
                            <?php else: ?>
                                <input type="checkbox" id="metodos" name="metodos[]" value="<?php echo $metodo->id; ?>" />
                            <?php endif; ?>
                        </td>
                    </tr>            
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php echo form_submit('gravar', 'Gravar'); ?>
        
        <?php echo form_close(); ?>
    </div>



    <div id="aside">
        <div id="links" class="box">
            <h3>Usuários</h3>
            <p><a href="/usuario">voltar</a></p>
        </div>      
    </div>

</div>

