<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Recebe o tipo de ação a ser executada pelo url */
    $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
    /** Verifica se existe uma ação a ser executada */
    if ($action) {
        /** Armazena o id do imóvel solicitada */
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $update = new Update();
        /** Case de verificação */
        switch ($action) {
            case 'active':
                /** Recebe o id e seta como ativo */
                $update->ExeUpdate('ws_properties', array('realty_status' => 1), "WHERE realty_id = :id", "id={$id}");
                Alert::set_flashdata('msg', $lang['active_realty']);
                header("location: painel.php?exe=properties/index");
                exit();
                break;
            case 'inative':
                $update->ExeUpdate('ws_properties', array('realty_status' => 0), "WHERE realty_id = :id", "id={$id}");
                Alert::set_flashdata('msg', $lang['inactive_realty'], E_USER_WARNING);
                header("location: painel.php?exe=properties/index");
                exit();
                break;
            default :
                /** Se o tipo de ação solicitado não existir no casem dispara um alerta */
                Alert::set_flashdata('msg', $lang['action_notfound'], E_USER_ERROR);
                header("location: painel.php?exe=properties/index");
                exit();
        }
    }
    /** Cria a paginação */
    $get_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $pager = new Pagination('painel.php?exe=properties/index&page=');
    $pager->ExePager($get_page, 10);
    /** Faz a consulta no banco de dados */
    $read = new Read;
    $read->ExeRead("ws_properties", "ORDER BY realty_status ASC, realty_date DESC LIMIT :limit OFFSET :offset", "limit={$pager->getLimit()}&offset={$pager->getOffset()}");
    $realty = $read->getResult();

    if (!$realty) {
        $pager->ReturnPage();
        Alert::set_flashdata('msg', $lang['properties_notfound'], E_USER_NOTICE);
    }
?>

<div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Imóveis</li>
    </ol>
    <div class="col">
        <div class="row">
            <div class="col">
                <i class="fa fa-home"></i> Imóveis cadastrados
            </div>
            <div class="col text-right">
                <a class="btn btn-success btn-sm" href="painel.php?exe=properties/create">
                    <i class="fa fa-plus fa-fw"></i> Cadastrar imóvel
                </a>
            </div>
        </div>
        <hr class="mt-2">
        <?php Alert::flashdata('msg'); ?>

        <div class="card-deck mt-3 mb-3">
            <?php
                if ($realty) {
                    foreach ($realty as $value) {
                        extract($value);
                        ?>
                        <div class="card mb-4 box-shadow property-content item-<?= $realty_id; ?>"
                             style="max-width: 237.25px;">
                            <a class="external" href="../imovel/<?= $realty_name; ?>" title="Ver Imóvel">
                                <?= Check::Image($realty_cover, $realty_title, 'class="img-fluid"', 237, 237); ?>

                            </a>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1">
                                    <a class="external" href="../imovel/<?= $realty_name; ?>" title="Ver Imóvel">
                                        <?= Check::characters($realty_title, 25); ?>

                                    </a>
                                </h6>
                                <p class="card-text text-bold text-success">
                                    R$ <?= number_format($realty_price, 2, ',', '.'); ?></p>
                                <p class="card-text">
                                    <i class="fa fa-map-marker fa-fw"></i> <?= Check::characters($realty_city . ' - ' . $realty_district, 25); ?>
                                </p>
                                <div class="facilities-list">
                                        <span title="Área">
                                            <i class="fa fa-square fa-fw"></i> <?= $realty_builtarea; ?> m²
                                        </span>
                                    <span title="Total">
                                            <i class="fa fa-square-o fa-fw"></i> <?= $realty_totalarea; ?> m²
                                        </span>
                                    <span>
                                            <i class="fa fa-bed fa-fw"></i> <?= $realty_bedrooms; ?> quarto
                                        </span>
                                    <span>
                                            <i class="fa fa-bath fa-fw"></i> <?= $realty_bathrooms; ?> banheiro
                                        </span>
                                    <span>
                                            <i class="fa fa-bathtub fa-fw"></i> <?= $realty_suites; ?> suíte
                                        </span>
                                    <span>
                                            <i class="fa fa-car fa-fw"></i> <?= $realty_parkings; ?> vagas
                                        </span>
                                </div>
                            </div>
                            <div class="card-footer" style="text-align: center;">
                                <?php
                                    if (!$realty_status) { ?>

                                        <a class="mt-auto btn btn-sm btn-outline-warning"
                                           href="painel.php?exe=properties/index&id=<?= $realty_id; ?>&action=active"
                                           role="button" title="Ativar">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    <?php } else { ?>

                                        <a class="mt-auto btn btn-sm btn-outline-success"
                                           href="painel.php?exe=properties/index&id=<?= $realty_id; ?>&action=inative"
                                           role="button" title="Inativar">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    <?php } ?>

                                <a class="mt-auto btn btn-sm btn-primary"
                                   href="painel.php?exe=properties/create&id=<?= $realty_id; ?>" title="Editar"
                                   role="button">
                                    <i class="fa fa-pencil"></i> Editar
                                </a>
                                <button class="mt-auto btn btn-sm btn-danger delete" data-url="properties"
                                        data-realty_title="<?= $realty_title; ?>" data-id="<?= $realty_id; ?>"
                                        data-toggle="modal" data-target="#modal-delete-properties">
                                    <i class="fa fa-trash"></i> Deletar
                                </button>
                            </div>
                        </div>
                    <?php }
                } ?>
        </div>
    </div>
    <?php
        $pager->ExePaginator("ws_properties");
        echo $pager->getPaginator();

    ?>

</div>
<!-- Modal de deletar imóvel -->
<div class="modal fade" id="modal-delete-properties" tabindex="-1" role="dialog" aria-labelledby="realty-delete"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="realty-delete">Atenção !</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Deseja mesmo remover o imóvel: <br> " <b class="realty_title"></b> " ?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger delete-confirm btn-load"> Deletar</button>
                <div class="icon-load"></div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal de deletar imóvel -->

<script>
    $('.delete').on('click', function () {
        var realty_title = $(this).data('realty_title');
        $('b.realty_title').text(realty_title);
    });
</script>
