<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o id do patrocinador a ser atualizado */
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    /**  Faz a consulta para listar os dados do patrocinador de acordo com o id */
    $read = new Read;
    $read->ExeRead("ws_partners", "WHERE partner_id = :id", "id={$id}");
    /** Verifica se o resultado não é nulo, se sim exibe uma mensagem */
    if (!$read->getResult()) {
        Alert::set_flashdata('msg', $lang['update_notfound'], E_USER_NOTICE);
        header("location: painel.php?exe=partners/index");
        exit();
    } else {
        /** Caso o resultado não seja nulo, armazena na variável */
        $data = $read->getResult()[0];
    }

?>

<div class="container-fluid mb-3">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="painel.php?exe=partners/index">Patrocinadores</a></li>
        <li class="breadcrumb-item active">Editar patrocinador</li>
    </ol>

    <form class="form" action="partners/update/<?= $id; ?>" enctype="multipart/form-data" method="post"
          accept-charset="utf-8">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong>Editar patrocinador</strong>
                    </div>
                    <div class="card-body card-block">
                        <div class="form-group">
                            <label for="partner_title"><b>* Título:</b></label>
                            <input type="text" class="form-control" name="partner_title" id="partner_title"
                                   placeholder="Título" value="<?= $data['partner_title']; ?>" required="">
                        </div>
                        <div class="form-group">
                            <label for="partner_link"><b>Link:</b></label>
                            <input type="text" class="form-control" name="partner_link" id="partner_link"
                                   placeholder="https://patrocinador.com.br" value="<?= $data['partner_link']; ?>">
                        </div>
                        <div class="row text-center">
                            <div class="col-6 form-group">
                                <label for="partner_insert"><b>Adicionado:</b></label>
                                <?= date('d/m/Y H:i', strtotime($data['partner_insert'])); ?>
                            </div>
                            <div class="col-6 form-group">
                                <label for="partner_update"><b>Atualizado:</b></label>
                                <?= date('d/m/Y H:i', strtotime($data['partner_update'])); ?>
                            </div>
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
                            <input type="file" class="form-control load-image" name="partner_logo">
                        </div>
                        <div class="form-group load-image-src partner_logo">

                            <?php
                                if (Check::file_exists($data['partner_logo'], '../uploads/')) {
                                    echo Check::Image($data['partner_logo'], 'Logo', 'class="img-fluid"', PARTNER_LOGO_W, PARTNER_LOGO_H);
                                }
                            ?>

                        </div>
                        <div class="form-row">
                            <div class="form-check">
                                <div class="col">
                                    <input type="checkbox" class="form-check-input" name="partner_status"
                                           id="partner_status"
                                           value="1" <?= ($data['partner_status'] == 1 ? 'checked' : ''); ?>>
                                    <label for="partner_status">
                                        Publicar!
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-success btn-load" style="float: right;">
                                    <i class="fa fa-refresh"></i> Atualizar
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
