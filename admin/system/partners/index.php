<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o numero da página atual (Para páginação) */
    $get_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $pager = new Pagination('painel.php?exe=partners/index&page=', '<<', '>>');
    /** Passa o númedo da página atual e seta a quantidade de itens por página */
    $pager->ExePager($get_page, 8);
    /** Faz a consulta para listar as páginas cadastradas no sistema */
    $read = new Read;
    $read->ExeRead("ws_partners", "ORDER BY partner_status ASC, partner_update DESC LIMIT :limit OFFSET :offset", "limit={$pager->getLimit()}&offset={$pager->getOffset()}");
    $partners = $read->getResult();

    if (!$partners) {
        $pager->ReturnPage();
        Alert::set_flashdata('msg', $lang['partners_notfound'], E_USER_NOTICE);
    }
?>

<div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Patrocinadores</li>
    </ol>

    <div class="col">
        <div class="row">
            <div class="col">
                <i class="fa fa-handshake-o"></i> Patrocinadores cadastrados
            </div>
            <div class="col text-right">
                <a class="btn btn-success btn-sm" href="painel.php?exe=partners/create">
                    <i class="fa fa-plus fa-fw"></i> Cadastrar Patrocinador
                </a>
            </div>
        </div>
        <hr class="mt-2">
        <?php Alert::flashdata('msg'); ?>

        <div class="row mt-3">
            <?php
                if ($partners) {
                    foreach ($partners as $partner) {
                        extract($partner);
                        ?>
                        <div class="col-md-4 col-lg-4 col-xl-3 item-<?= $partner_id; ?>">
                            <div class="card mb-3">
                                <a class="external" href="<?= $partner_link; ?>" title="Acessar link">
                                    <?= Check::Image($partner_logo, $partner_title, 'class="img-fluid"', 365, 170); ?>
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title mb-1">
                                        <a class="external" href="<?= $partner_link; ?>"
                                           title="Acessar link"><?= Check::Words($partner_title, 6); ?></a>
                                    </h6>
                                </div>
                                <div class="card-footer" style="text-align: center;">
                                    <a class="btn btn-primary btn-sm"
                                       href="painel.php?exe=partners/update&id=<?= $partner_id; ?>"
                                       title="Editar"
                                       role="button">
                                        <i class="fa fa-pencil"></i> Editar
                                    </a>
                                    <button class="delete btn btn-danger btn-sm" data-url="partners"
                                            data-id="<?= $partner_id; ?>" data-toggle="modal"
                                            data-target="#modal-delete-partners">
                                        <i class="fa fa-trash fa-fw"></i> Deletar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
        </div>
    </div>
    <?php
        $pager->ExePaginator("ws_partners");
        echo $pager->getPaginator();
    ?>

</div>

<!-- Modal de delete partner -->
<div class="modal fade" id="modal-delete-partners" tabindex="-1" role="dialog" aria-labelledby="partner-delete"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partner-delete">Atenção !</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Tem certeza que deseja remover este patrocinador ?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger delete-confirm btn-load"> Deletar</button>
                <div class="icon-load"></div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal de delete partner -->
