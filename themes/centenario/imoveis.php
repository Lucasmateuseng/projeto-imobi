<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :(');

    $link = ($Link->getLink() == 'comprar' ? 2 : ($Link->getLink() == 'alugar' ? 1 : ($Link->getLink() == 'temporada' ? 3 : 0)));
    if (!$link) {
        require(REQUIRE_PATH . '/404.php');
    } else {
        /** Cria a paginação */
        $get_page = (!empty($Link->getLocal()[2]) ? $Link->getLocal()[2] : 1);
        $pager = new Pagination(SITE_URL . "/imoveis/{$Link->getLink()}/", '<<', '>>', 5);
        $pager->ExePager($get_page, 6);
        /** Faz a consulta */
        $read = new Read();
        $read->ExeRead('ws_properties', 'WHERE realty_status = 1 AND realty_date <= NOW() AND realty_transaction = :tran ORDER BY realty_date DESC LIMIT :limit OFFSET :offset', "tran={$link}&limit={$pager->getLimit()}&offset={$pager->getOffset()}");
        ?>

        <div class="page-content">
            <!-- Property Listing Section -->
            <div id="property-listing" class="property-listing">
                <div class="container">
                    <div class="property-left col-md-9 col-sm-6 p_l_z content-area">
                        <div class="section-header p_l_z">
                            <div class="col-md-10 col-sm-10 p_l_z">
                                <p>
                                <h3>Imóveis para <?= ucwords($Link->getLink()); ?> </h3></p>
                            </div>
                        </div>

                        <div class="property-listing-row row">
                            <?php
                                if (!$read->getResult()) {
                                    $pager->ReturnPage();
                                    echo Alert::alert_msg('<i class="fa fa-exclamation fa-fw"></i> Desculpe, ainda não existe imóveis cadastrado para esta categoria', E_USER_NOTICE);
                                } else {
                                    foreach ($read->getResult() as $value) {
                                        extract($value);
                                        ?>
                                        <!-- Col-md-4 -->
                                        <div class="col-md-4 col-sm-12 mb-3 <?= ($realty_transaction == 1 ? 'rent' : 'sale'); ?>-block">
                                            <?php include(REQUIRE_PATH . '/inc/realty.inc.php'); ?>
                                        </div><!-- /Col-md-4 -->
                                    <?php }
                                }
                                unset($value); ?>
                        </div>
                        <!-- Paginação -->
                        <div class="listing-pagination">
                            <?php
                                $pager->ExePaginator('ws_properties', 'WHERE realty_status = 1 AND realty_date <= NOW() AND realty_transaction = :tran', "tran={$link}");
                                echo $pager->getPaginator();
                            ?>
                        </div><!-- Paginação /- -->
                    </div>
                    <div class="col-md-3 col-sm-6 p_r_z property-sidebar widget-area">
                        <aside class="widget widget-search">
                            <h2 class="widget-title">Filtrar<span>Imóveis</span></h2>
                            <form class="filter-form" name="filter" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="send_form" value="<?= md5(mt_rand()); ?>">
                                <select name="transaction">
                                    <option value="">O que deseja ?</option>
                                    <?php foreach (realty_transaction() as $key => $value) { ?>
                                        <option value="<?= $key; ?>"><?= $value; ?></option>
                                    <?php }
                                        unset($key, $value); ?>
                                </select>
                                <select name="type">
                                    <option value="">Tipo</option>
                                </select>
                                <select name="finality">
                                    <option value="">Finalidade</option>
                                </select>
                                <select name="district">
                                    <option value="">Bairro</option>
                                </select>
                                <select name="bedrooms">
                                    <option value="">Dormitórios</option>
                                </select>
                                <div class="col-md-6 col-sm-12 p_l_z">
                                    <select name="min_price">
                                        <option value="">Valor mínimo</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-sm-12 p_r_z">
                                    <select name="max_price">
                                        <option value="">Valor máximo</option>
                                    </select>
                                </div>
                                <a href="<?= SITE_URL; ?>/filtro" class="btn">FILTRAR</a>
                            </form>
                        </aside>
                    </div>
                </div>
            </div><!-- Property Listing Section /- -->
            <?php
                unset($read, $pager, $get_page, $link);
                include(REQUIRE_PATH . '/inc/partner.inc.php'); ?>

        </div><!-- Page Content -->
        <?php
    }
