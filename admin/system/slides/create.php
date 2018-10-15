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
        <li class="breadcrumb-item"><a href="painel.php?exe=slides/index">Slides</a></li>
        <li class="breadcrumb-item active">Criar Slide</li>
    </ol>

    <?php Alert::flashdata('msg'); ?>

    <form class="form" action="slides/create" enctype="multipart/form-data" method="post" accept-charset="utf-8"
          autocomplete="off">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong>Criar Slide</strong>
                    </div>
                    <div class="card-body card-block">
                        <div class="form-group">
                            <div class="form-group load-image-src slide_image"
                                 style="max-width: <?= SLIDE_W; ?>; max-height: <?= SLIDE_H; ?>">
                            </div>
                            <label for="slide_image"><b>* Imagem:</b></label>
                            <input type="file" class="form-control load-image" name="slide_image" id="slide_image"
                                   required="">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="slide_title"><b>* Título:</b></label>
                                <input type="text" class="form-control" name="slide_title" id="slide_title"
                                       placeholder="Título" value="" required="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="slide_price"><b>Valor: (opcional)</b></label>
                                <input type="text" class="form-control" name="slide_price" id="slide_price"
                                       placeholder="Valor" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="slide_link"><b>* Link:</b></label>
                            <input type="text" class="form-control" name="slide_link" id="slide_link" placeholder="Link"
                                   value="" required="">
                        </div>
                        <div class="form-group">
                            <label for="slide_description"><b>* Descrição:</b></label>
                            <textarea class="form-control" name="slide_description" id="slide_description"
                                      placeholder="Descrição" rows="7"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="slide_start"><b>* Data inicial:</b></label>
                                <input type="text" class="form-control date-time" name="slide_start" id="slide_start"
                                       value="<?= date('d/m/Y H:i:s'); ?>" required="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="slide_end"><b>Data final:</b></label>
                                <input type="text" class="form-control date-time" name="slide_end" id="slide_end"
                                       value="">
                            </div>
                        </div>
                        <button class="btn btn-success btn-load" style="float: right;">
                            <i class="fa fa-save fa-fw"></i> Salvar
                        </button>
                        <div class="icon-load"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
