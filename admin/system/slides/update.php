<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o id do slide a ser atualizado */
    $slideid = filter_input(INPUT_GET, 'slideid', FILTER_VALIDATE_INT);
    /**  Faz a consulta para listar os dados do slide de acordo com o id */
    $read = new Read;
    $read->ExeRead("ws_slides", "WHERE slide_id = :id", "id={$slideid}");
    /** Verifica se o resultado não é nulo, se sim exibe uma mensagem */
    if (!$read->getResult()) {
        Alert::set_flashdata('msg', $lang['update_notfound'], E_USER_NOTICE);
        header("location: painel.php?exe=slides/index");
        exit();
    } else {
        /** Caso o resultado não seja nulo, armazena na variável */
        $data = array_map('htmlspecialchars', $read->getResult()[0]);
        /** Converte a dara para dia/mes/ano hora:segundo:minuto */
        $data['slide_start'] = (!empty($data['slide_start']) ? date('d/m/Y H:i:s', strtotime($data['slide_start'])) : date('d/m/Y H:i:s'));
        $data['slide_end'] = (!empty($data['slide_end']) ? date('d/m/Y H:i:s', strtotime($data['slide_end'])) : NULL);
    }

?>

<div class="container-fluid mb-3">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="painel.php?exe=slides/index">Slides</a></li>
        <li class="breadcrumb-item active">Editar Slide</li>
    </ol>

    <?php Alert::flashdata('msg'); ?>

    <form class="form" action="slides/update/<?= $slideid; ?>" enctype="multipart/form-data" method="post"
          accept-charset="utf-8" autocomplete="off">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong>Editar Slide</strong>
                    </div>
                    <div class="card-body card-block">
                        <div class="form-group">
                            <label for="slide_image"><b>* Imagem:</b></label>
                            <div class="form-group load-image-src slide_image">

                                <?php
                                    if (Check::file_exists($data['slide_image'], '../uploads/')) {
                                        echo Check::Image($data['slide_image'], $data['slide_title'], 'class="img-fluid"', SLIDE_W, SLIDE_H);;
                                    }
                                ?>

                            </div>
                            <div class="form-group">
                                <input type="file" class="form-control load-image" name="slide_image" id="slide_image">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="slide_title"><b>* Título:</b></label>
                                    <input type="text" class="form-control" name="slide_title" id="slide_title"
                                           placeholder="Título" value="<?= $data['slide_title']; ?>" required="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="slide_price"><b>Valor: (opcional)</b></label>
                                    <input type="text" class="form-control" name="slide_price" id="slide_price"
                                           placeholder="Valor"
                                           value="<?= ($data['slide_price'] ? number_format($data['slide_price'], 2, ',', '.') : ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="slide_link"><b>* Link:</b></label>
                                <input type="text" class="form-control" name="slide_link" id="slide_link"
                                       placeholder="Link" value="<?= $data['slide_link']; ?>" required="">
                            </div>
                            <div class="form-group">
                                <label for="slide_description"><b>* Descrição:</b></label>
                                <textarea class="form-control" name="slide_description" id="slide_description"
                                          placeholder="Descrição" rows="7"><?= $data['slide_description']; ?></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="slide_start"><b>* Data inicial:</b></label>
                                    <input type="text" class="form-control date-time" name="slide_start"
                                           id="slide_start" value="<?= $data['slide_start']; ?>" required="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="slide_end"><b>Data final:</b></label>
                                    <input type="text" class="form-control date-time" name="slide_end" id="slide_end"
                                           value="<?= $data['slide_end']; ?>">
                                </div>
                            </div>
                            <button class="btn btn-success btn-load" style="float: right;">
                                <i class="fa fa-refresh fa-fw"></i> Atualizar
                            </button>
                            <div class="icon-load"></div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
