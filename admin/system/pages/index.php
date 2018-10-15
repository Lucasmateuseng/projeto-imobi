<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o tipo de ação a ser executado, ativar ou desativar a página */
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
                $update->ExeUpdate('ws_pages', array('page_status' => 1), "WHERE page_id = :id", "id={$id}");
                Alert::set_flashdata('msg', $lang['active_page']);
                header("location: painel.php?exe=pages/index");
                exit();
                break;
            case 'inative':
                $update->ExeUpdate('ws_pages', array('page_status' => 0), "WHERE page_id = :id", "id={$id}");
                Alert::set_flashdata('msg', $lang['inactive_page'], E_USER_WARNING);
                header("location: painel.php?exe=pages/index");
                exit();
                break;
            default :
                /** Se o tipo de ação solicitado não existir no casem dispara um alerta */
                Alert::set_flashdata('msg', $lang['action_notfound'], E_USER_ERROR);
                header("location: painel.php?exe=pages/index");
                exit();
        }
    }
    /** Recebe o numero da página atual (Para páginação) */
    $get_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $pager = new Pagination('painel.php?exe=pages/index&page=', '<<', '>>');
    /** Passa o númedo da página atual e seta a quantidade de itens por página */
    $pager->ExePager($get_page, 12);
    /** Faz a consulta para listar as páginas cadastradas no sistema */
    $read = new Read;
    $read->ExeRead("ws_pages", "ORDER BY page_status ASC, page_date ASC LIMIT :limit OFFSET :offset", "limit={$pager->getLimit()}&offset={$pager->getOffset()}");
    $pages = $read->getResult();

    if (!$pages) {
        $pager->ReturnPage();
        Alert::alert_msg('<i class="fa fa-exclamation fa-fw"></i> Deslculpe, ainda não existe páginas cadastradas!');
    }
?>

<div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Páginas</li>
    </ol>

    <div class="col">
        <div class="row">
            <div class="col">
                <i class="fa fa-file-text"></i> Páginas cadastradas
            </div>
            <div class="col text-right">
                <a class="btn btn-success btn-sm" href="painel.php?exe=pages/create">
                    <i class="fa fa-plus fa-fw"></i> Cadastrar Página
                </a>
            </div>
        </div>
        <hr class="mt-2">
        <?php Alert::flashdata('msg'); ?>

        <div class="row mt-3">
            <?php
                if ($pages) {
                    foreach ($pages as $page) {
                        extract($page);
                        ?>
                        <div class="col-md-4 col-lg-4 col-xl-3 item-<?= $page_id; ?>">
                            <div class="card mb-3">
                                <a class="external" href="../<?= $page_name; ?>" title="Ver Página">
                                    <?= Check::Image($page_cover, $page_title, 'class="img-fluid"', 365, 324); ?>
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title mb-1">
                                        <a class="external" href="../<?= $page_name; ?>"
                                           title="Ver Página"><?= Check::Words($page_title, 6); ?></a>
                                    </h6>
                                </div>
                                <div class="card-footer" style="text-align: center;">
                                    <?php if (!$page_status) { ?>
                                        <a class="btn btn-outline-warning"
                                           href="painel.php?exe=pages/index&id=<?= $page_id; ?>&action=active"
                                           role="button" title="Ativar">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    <?php } else { ?>
                                        <a class="btn btn-outline-success btn-sm"
                                           href="painel.php?exe=pages/index&id=<?= $page_id; ?>&action=inative"
                                           role="button" title="Inativar">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    <?php } ?>
                                    <a class="btn btn-primary btn-sm"
                                       href="painel.php?exe=pages/update&pageid=<?= $page_id; ?>" title="Editar"
                                       role="button">
                                        <i class="fa fa-pencil"></i> Editar
                                    </a>
                                    <button class="delete btn btn-danger btn-sm" data-url="pages"
                                            data-id="<?= $page_id; ?>" data-toggle="modal"
                                            data-target="#modal-delete-pages">
                                        <i class="fa fa-trash fa-fw"></i> Deletar
                                    </button>
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
        $pager->ExePaginator("ws_pages");
        echo $pager->getPaginator();
    ?>

</div>

<!-- Modal de delete page -->
<div class="modal fade" id="modal-delete-pages" tabindex="-1" role="dialog" aria-labelledby="page-delete"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="page-delete">Atenção !</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Tem certeza que deseja remover esta página ?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger delete-confirm btn-load"> Deletar</button>
                <div class="icon-load"></div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal de delete page -->
