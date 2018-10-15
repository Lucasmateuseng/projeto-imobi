<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 10) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o id do usuário a ser atualizado */
    $userid = filter_input(INPUT_GET, 'userid', FILTER_VALIDATE_INT);
    /** Faz a consulta */
    $read = new Read;
    $read->ExeRead("ws_users", "WHERE user_id = :id", "id={$userid}");
    /** Verifica se o resultado não é nulo */
    if (!$read->getResult()) {
        /** Caso não, seta uma mensagem na sessão e trava a execução do codigo */
        Alert::set_flashdata('msg', $lang['update_notfound'], E_USER_NOTICE, 1);
        header("location: painel.php?exe=users/index");
        exit();
    } else {
        /** Caso o resultado não seja nulo, armazena na variável */
        $data = $read->getResult()[0];
    }

?>

<div class="container-fluid mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="painel.php?exe=users/index">Usuários</a></li>
        <li class="breadcrumb-item active">Editar Usuário</li>
    </ol>

    <form class="form" action="users/update/<?= $userid; ?>" enctype="multipart/form-data" method="post"
          accept-charset="utf-8">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong>Editar usuário</strong>
                    </div>
                    <div class="card-body card-block">
                        <div class="form-group">
                            <label for="user_name"><b>* Nome:</b></label>
                            <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Nome"
                                   value="<?= $data['user_name']; ?>" required="">
                        </div>
                        <div class="form-group">
                            <label for="user_lastname"><b>* Sobrenome:</b></label>
                            <input type="text" class="form-control" name="user_lastname" id="user_lastname"
                                   placeholder="Sobrenome" value="<?= $data['user_lastname']; ?>"
                                   required="">
                        </div>
                        <div class="form-group">
                            <label for="user_email"><b>* E-mail:</b></label>
                            <input type="email" class="form-control" name="user_email" id="user_email"
                                   placeholder="E-mail" value="<?= $data['user_email']; ?>"
                                   required="">
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="user_password"><b>Senha:</b></label>
                                    <input type="password" class="form-control" name="user_password" id="user_password"
                                           placeholder="Senha" value="">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="user_passconfir"><b>Confirmar senha:</b></label>
                                    <input type="password" class="form-control" name="user_passconfirm"
                                           id="user_passconfir" placeholder="Confirmar senha" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="user_level"><b>* Nível:</b></label>
                            <select name="user_level" id="user_level" class="form-control" required="">
                                <option value="">Selecione um nível</option>
                                <?php foreach (user_level() as $key => $value) { ?>
                                    <option
                                        <?= ($data['user_level'] == $key ? 'selected=""' : NULL); ?>value="<?= $key; ?>"><?= $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-check">
                                <div class="col">
                                    <input type="checkbox" class="form-check-input" name="user_status" id="status"
                                           value="1" <?= ($data['user_status'] == 1 ? 'checked' : ''); ?>>
                                    <label for="user_status">
                                        Ativo!
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-success float-right">
                                    <i class="fa fa-refresh"></i>&nbsp;Atualizar
                                </button>
                                <div class="icon-load"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
