<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o id da página a ser atualizada */
    $pageid = filter_input(INPUT_GET, 'pageid', FILTER_VALIDATE_INT);
    /**  Faz a consulta para listar os dados da página de acordo com o id */
    $read = new Read;
    $read->ExeRead("ws_pages", "WHERE page_id = :id", "id={$pageid}");
    /** Verifica se o resultado não é nulo, se sim exibe uma mensagem */
    if (!$read->getResult()) {
        Alert::set_flashdata('msg', $lang['update_notfound'], E_USER_NOTICE);
        header("location: painel.php?exe=pages/index");
        exit();
    } else {
        /** Caso o resultado não seja nulo, armazena na variável */
        $data = $read->getResult()[0];
        /** Converte a dara para dia/mes/ano hora:segundo:minuto */
        $data['page_date'] = date('d/m/Y H:i:s', strtotime($data['page_date']));
    }

?>

<div class="container-fluid mb-3">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="painel.php?exe=pages/index">Páginas</a></li>
        <li class="breadcrumb-item active">Editar página</li>
    </ol>

    <?php Alert::flashdata('msg'); ?>

    <form class="form" action="pages/update/<?= $pageid; ?>" enctype="multipart/form-data" method="post"
          accept-charset="utf-8">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <strong>Editar página</strong>
                    </div>
                    <div class="card-body card-block">
                        <div class="form-group">
                            <label for="page_title"><b>* Título:</b></label>
                            <input type="text" class="form-control" name="page_title" id="page_title"
                                   placeholder="Título da página"
                                   value="<?= $data['page_title']; ?>" required="">
                        </div>
                        <div class="form-group">
                            <label for="page_name"><b>Link: (opcional)</b></label>
                            <input type="text" class="form-control" name="page_name" id="page_name"
                                   placeholder="Link alternativo"
                                   value="<?= $data['page_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="page_subtitle"><b>* Descrição:</b></label>
                            <textarea class="form-control" name="page_subtitle" id="page_subtitle"
                                      placeholder="Breve descrição da página" rows="7"
                                      required=""><?= $data['page_subtitle']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="page_content"><b>* Conteúdo:</b></label>
                            <textarea class="form-control tiny-editor" name="page_content" id="page_content"
                                      placeholder="Conteúdo da página" rows="7"><?= $data['page_content']; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <strong>Imagem destacada</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <input type="file" class="form-control load-image" name="page_cover">
                        </div>
                        <div class="form-group load-image-src page_cover">

                            <?php
                                if (Check::file_exists($data['page_cover'], '../uploads/')) {
                                    echo Check::Image($data['page_cover'], 'Imagem destacada', 'class="img-fluid"', 294, 159);
                                }
                            ?>

                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">
                        <strong>Publicar</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="page_date"><i class="fa fa-calendar"></i> Data</label>
                            <input type="text" class="form-control date-time" name="page_date" id="page_date"
                                   value="<?= date('d/m/Y H:i', strtotime($data['page_update'])); ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-check">
                                <div class="col">
                                    <input type="checkbox" class="form-check-input" name="page_status" id="page_status"
                                           value="1" <?= ($data['page_status'] == 1 ? 'checked' : ''); ?>>
                                    <label for="page_status">
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
