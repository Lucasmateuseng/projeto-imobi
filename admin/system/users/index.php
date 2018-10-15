<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 10) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o tipo de ação a ser executada */
    $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
    /** Verifica se existe uma ação */
    if ($action) {
        switch ($action) {
            case 'delete':
                /** recebe e armazena o id na variável */
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                $read = new Read();
                /** Primeiro faz uma consulta para verificar se o usuário existe */
                $read->ExeRead('ws_users', "WHERE user_id = :id", "id={$id}");
                /** Verifica se o resultado não é nulo, caso seja nulo retorna uma mensagem */
                if (!$read->getResult()) {
                    Alert::set_flashdata('msg', $lang['delete_notfound'], E_USER_NOTICE);
                    header('LOCATION: painel.php?exe=users/index');
                    exit();
                } else {
                    /** Caso o resultado não seja nulo, verifica se não é o usuário logado */
                    $user = $read->getResult()[0];
                    if ($user['user_id'] == $_SESSION['userlogin']['user_id']) {
                        /** Caso seja o usuário logado, retorna uma mensagem */
                        Alert::set_flashdata('msg', $lang['nodelete_user'], E_USER_ERROR);
                        header('LOCATION: painel.php?exe=users/index');
                        exit();
                    } else {
                        $delete = new Delete();
                        /** Se tudo ok acima, deleta o usuário */
                        $delete->ExeDelete('ws_users', "WHERE user_id = :id", "id={$user['user_id']}");
                        /** Verifica se deletou e exibe uma mensagem */
                        if ($delete->getResult()) {
                            Alert::set_flashdata('msg', $lang['update_success']);
                            header('LOCATION: painel.php?exe=users/index');
                            exit();
                        }
                    }
                }
                break;
            default;
                Alert::set_flashdata('msg', $lang['action_notfound'], E_USER_ERROR);
                header('LOCATION: painel.php?exe=users/index');
                exit();
        }
    }

    $read = new Read;
    /** Faz a consulta e armazena na variável */
    $read->ExeRead("ws_users");
    $user = $read->getResult();
    /** Verifica se a variável obteve resultado
     * Caso a tabela esteja vazia no banco de dados, o resultado é false
     * Caso exista resultado, o valor armazenado vale como true
     */

?>

<div class="container-fluid">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="painel.php">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Usuários</li>
    </ol>
    <?php
        echo Alert::flashdata('msg');
    ?>

    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <i class="fa fa-users"></i> Lista de usuários
                </div>
                <div class="col text-right">
                    <a class="btn btn-success btn-sm" href="painel.php?exe=users/create">
                        <i class="fa fa-plus fa-fw"></i> Cadastrar usuário
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Nível</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Nível</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php
                        if ($user) {
                            foreach ($user as $value) {
                                extract($value);
                                ?>
                                <tr class="item-<?= $user_id; ?>">
                                    <td><?= $user_name . ' ' . $user_lastname; ?></td>
                                    <td><?= $user_email; ?></td>
                                    <td><?= user_level((int)$user_level); ?></td>
                                    <td><?= ((int)$user_status === 1 ? 'Ativo' : 'Inativo'); ?></td>
                                    <td class="text-right" style="width:20%;">
                                        <a href="painel.php?exe=users/update&userid=<?= $user_id; ?>"
                                           class="btn btn-primary btn-sm">
                                            <i class="fa fa-pencil fa-fw"></i> Editar
                                        </a>
                                        <button class="delete btn btn-danger btn-sm" data-user_name="<?= $user_name; ?>"
                                                data-user_lastname="<?= $user_lastname; ?>" data-id="<?= $user_id; ?>"
                                                data-toggle="modal" data-target="#modal-delete-users">
                                            <i class="fa fa-trash"></i> Deletar
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de deletar usuario -->
<div class="modal fade" id="modal-delete-users" tabindex="-1" role="dialog" aria-labelledby="user-delete"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="user-delete">Atenção !</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Deseja mesmo remover o usuário <b class="user_name"></b> do sistema ?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger delete-confirm"> Deletar</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal de deletar slide -->
<script>
    $('.delete').on('click', function () {
        var user_name = $(this).data('user_name');
        var user_lastname = $(this).data('user_lastname');
        var user_id = $(this).data('id');
        $('b.user_name').text(user_name + ' ' + user_lastname);
        $('.delete-confirm').on('click', function () {
            window.location.href = 'painel.php?exe=users/index&action=delete&id=' + user_id;
        })
    });
</script>

