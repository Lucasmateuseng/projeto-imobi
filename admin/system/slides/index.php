<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o tipo de ação a ser executado, ativar ou desativar o slide */
    $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
    /** Verifica se existe uma ação a ser executada */
    if ($action) {
        /** Armazena o id da página solicitada */
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $update = new Update();
        /** Case de verificação */
        switch ($action) {
            case 'active':
                /** Recebe o id e seta como ativo */
                $update->ExeUpdate('ws_slides', array('slide_status' => 1), "WHERE slide_id = :id", "id={$id}");
                Alert::set_flashdata('msg', $lang['active_slide']);
                header("location: painel.php?exe=slides/index");
                exit();
                break;
            case 'inative':
                $update->ExeUpdate('ws_slides', array('slide_status' => 0), "WHERE slide_id = :id", "id={$id}");
                Alert::set_flashdata('msg', $lang['inactive_slide'], E_USER_WARNING);
                header("location: painel.php?exe=slides/index");
                exit();
                break;
            default :
                /** Se o tipo de ação solicitado não existir no casem dispara um alerta */
                Alert::set_flashdata('msg', $lang['action_notfound'], E_USER_ERROR);
                header("location: painel.php?exe=slides/index");
                exit();
        }
    }
    /** Cria a paginação */
    $get_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $pager = new Pagination('painel.php?exe=slides/index&page=');
    $pager->ExePager($get_page, 5);
    /** Faz a consulta no banco de dados */
    $read = new Read;
    $read->ExeRead("ws_slides", "ORDER BY slide_status ASC, slide_date DESC LIMIT :limit OFFSET :offset", "limit={$pager->getLimit()}&offset={$pager->getOffset()}");
    $slide = $read->getResult();

    if (!$slide) {
        $pager->ReturnPage();
        Alert::set_flashdata('msg', $lang['slide_notfound'], E_USER_NOTICE);
    }
?>

<div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Slides</li>
    </ol>

    <div class="col">
        <div class="row">
            <div class="col">
                <i class="fa fa-picture-o"></i> Slides cadastrados
            </div>
            <div class="col text-right">
                <a class="btn btn-success btn-sm" href="painel.php?exe=slides/create">
                    <i class="fa fa-plus fa-fw"></i> Cadastrar slide</a>
            </div>
        </div>
        <hr class="mt-2">
        <?php Alert::flashdata('msg'); ?>

        <div class="mt-3">
            <?php
                if ($slide) {
                    foreach ($slide as $value) {
                        extract($value);
                        ?>
                        <div class="row item-<?= $slide_id; ?>">

                            <div class="col">
                                <div class="card mb-3 slide">
                                    <a class="external" href="<?= $slide_link; ?>" title="Ver link">
                                        <?= Check::Image($slide_image, $slide_title, 'class="img-fluid"', SLIDE_W, SLIDE_H); ?>

                                    </a>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $slide_title; ?></h5>
                                        <p class="card-text"><?= Check::Words($slide_description, 20); ?></p>
                                    </div>
                                    <div class="card-footer" style="text-align: left;">
                                        <?php if (!$slide_status) { ?>

                                            <a class="btn btn-outline-warning btn-sm"
                                               href="painel.php?exe=slides/index&id=<?= $slide_id; ?>&action=active"
                                               role="button" title="Ativar">
                                                <i class="fa fa-exclamation"></i>
                                            </a>
                                        <?php } else { ?>

                                            <a class="btn btn-outline-success btn-sm"
                                               href="painel.php?exe=slides/index&id=<?= $slide_id; ?>&action=inative"
                                               role="button" title="Inativar">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        <?php } ?>

                                        <a class="btn btn-primary btn-sm"
                                           href="painel.php?exe=slides/update&slideid=<?= $slide_id; ?>" title="Editar"
                                           role="button">
                                            <i class="fa fa-pencil"></i> Editar
                                        </a>
                                        <button class="delete btn btn-danger btn-sm"
                                                data-url="slides" data-id="<?= $slide_id; ?>"
                                                data-toggle="modal" data-target="#modal-delete-slides">
                                            <i class="fa fa-trash"></i> Deletar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            ?>
        </div>
    </div>

    <?php
        $pager->ExePaginator("ws_slides");
        echo $pager->getPaginator();
    ?>
</div>
<!-- Modal de deletar slide -->
<div class="modal fade" id="modal-delete-slides" tabindex="-1" role="dialog" aria-labelledby="slide-delete"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slide-delete">Atenção !</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Deseja mesmo remover este slide ?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger delete-confirm btn-load"> Deletar</button>
                <div class="icon-load"></div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal de deletar slide -->
