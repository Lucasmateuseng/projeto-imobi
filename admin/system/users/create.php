<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 10) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
?>

<div class="container-fluid mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="painel.php?exe=users/index">Usuários</a></li>
        <li class="breadcrumb-item active">Cadastrar usuário</li>
    </ol>

    <form class="form" action="users/create" enctype="multipart/form-data"
          method="post" accept-charset="utf-8">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong>Cadastrar usuário</strong>
                    </div>
                    <div class="card-body card-block">
                        <div class="form-group">
                            <label for="user_name"><b>* Nome:</b></label>
                            <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Nome"
                                   value="" required="">
                        </div>
                        <div class="form-group">
                            <label for="user_lastname"><b>* Sobrenome:</b></label>
                            <input type="text" class="form-control" name="user_lastname" id="user_lastname"
                                   placeholder="Sobrenome" value="" required="">
                        </div>
                        <div class="form-group">
                            <label for="user_email"><b>* E-mail:</b></label>
                            <input type="email" class="form-control" name="user_email" id="user_email"
                                   placeholder="E-mail" value="" required="">
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="user_password"><b>* Senha:</b></label>
                                    <input type="password" class="form-control" name="user_password" id="user_password"
                                           minlength="5" placeholder="Senha" value="" required="">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="user_passconfir"><b>* Confirmar senha:</b></label>
                                    <input type="password" class="form-control" name="user_passconfirm"
                                           data-equalto="#user_password" id="user_passconfir"
                                           placeholder="Confirmar senha" value="" required="">
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
                                    <option value="<?= $key; ?>"><?= $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-check">
                                <div class="col">
                                    <input type="checkbox" class="form-check-input" name="user_status" id="user_status"
                                           value="1">
                                    <label for="user_status">
                                        Ativo!
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-success float-right btn-load">
                                    <i class="fa fa-save"></i>&nbsp;Salvar
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
