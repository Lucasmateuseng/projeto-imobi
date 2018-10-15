<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }

?>

<div class="container-fluid mb-3">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="painel.php?exe=partners/index">Patrocinadores</a></li>
        <li class="breadcrumb-item active">Adicionar patrocinador</li>
    </ol>

    <form class="form" action="partners/create" enctype="multipart/form-data" method="post"
          accept-charset="utf-8">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong>Adicionar patrocinador</strong>
                    </div>
                    <div class="card-body card-block">
                        <div class="form-group">
                            <label for="partner_title"><b>* Título:</b></label>
                            <input type="text" class="form-control" name="partner_title" id="partner_title"
                                   placeholder="Título" value="" required="">
                        </div>
                        <div class="form-group">
                            <label for="partner_link"><b>Link:</b></label>
                            <input type="text" class="form-control" name="partner_link" id="partner_link"
                                   placeholder="https://patrocinador.com.br" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <strong>* Logo</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <input type="file" class="form-control load-image" name="partner_logo" required="">
                        </div>
                        <div class="form-group load-image-src partner_logo">

                        </div>
                        <div class="form-row">
                            <div class="form-check">
                                <div class="col">
                                    <input type="checkbox" class="form-check-input" name="partner_status"
                                           id="partner_status"
                                           value="1" checked="">
                                    <label for="partner_status">
                                        Publicar!
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-success btn-load" style="float: right;">
                                    <i class="fa fa-save"></i> Salvar
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

