<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :(');

    $read = new Read();

    $filter = (isset($_SESSION['filter-form']) ? $_SESSION['filter-form'] : array());
    unset($filter['min_price'], $filter['max_price'], $filter['realty_bedrooms']);

    $value = NULL;
    $parse = http_build_query($filter);
    foreach ($filter as $key => $value_) {
        $value .= " AND {$key} = :{$key}";
    }
    unset($filter, $key);
    // Prepara a consulta
    $bedrooms = (!empty($_SESSION['filter-form']['realty_bedrooms']) ? "AND realty_bedrooms >= '{$_SESSION['filter-form']['realty_bedrooms']}'" : '');
    $min_price = (!empty($_SESSION['filter-form']['min_price']) ? "AND realty_price >= '{$_SESSION['filter-form']['min_price']}'" : '');
    $max_price = (!empty($_SESSION['filter-form']['max_price']) ? "AND realty_price <= '{$_SESSION['filter-form']['max_price']}'" : '');

    /** Cria a paginação */
    $get_page = (!empty($Link->getLocal()[1]) ? $Link->getLocal()[1] : 1);
    $pager = new Pagination(SITE_URL . "/filtro/", '<<', '>>', 5);
    $pager->ExePager($get_page, 9);
    /** Faz a consulta */
    $read->ExeRead('ws_properties', "WHERE realty_status = 1 {$value} {$bedrooms} {$min_price} {$max_price} LIMIT :limit OFFSET :offset", "{$parse}&limit={$pager->getLimit()}&offset={$pager->getOffset()}");
    unset($_SESSION['filter-form'], $get_page);
?>
<!-- Page Content -->
<div class="page-content">
    <!-- Property Listing Section -->
    <div id="property-listing" class="property-listing">
        <div class="container">
            <div class="property-left col-md-9 col-sm-6 p_l_z content-area">
                <div class="section-header p_l_z">
                    <div class="col-md-10 col-sm-10 p_l_z">
                        <h3>Resultado</h3>
                    </div>
                </div>
                <?php if ($read->getResult()) { ?>
                    <div class="property-listing-row row">
                        <?php
                            foreach ($read->getResult() as $v) {
                                extract($v);
                                ?>
                                <!-- Col-md-4 -->
                                <div class="col-md-4 col-sm-12 mb-3 <?= ($realty_transaction == 1 ? 'rent' : 'sale'); ?>-block">
                                    <?php include(REQUIRE_PATH . '/inc/realty.inc.php'); ?>
                                </div><!-- /Col-md-4 -->
                            <?php }
                            unset($v); ?>
                    </div>

                    <!-- Paginação -->
                    <div class="listing-pagination">
                        <?php
                            $pager->ExePaginator('ws_properties', "WHERE realty_status = 1 {$value} {$bedrooms} {$min_price} {$max_price} ", "{$parse}");
                            echo $pager->getPaginator();
                        ?>
                    </div><!-- Paginação /- -->
                    <?php
                } else {
                    $pager->ReturnPage();
                    echo Alert::alert_msg('<i class="fa fa-exclamation"></i> Desculpe, não encontramos imóveis cadastrados nos termos desta consulta!', E_USER_NOTICE);
                }
                    unset($pager, $value, $bedrooms, $min_price, $max_price, $parse);
                ?>

            </div>
            <div class="col-md-3 col-sm-6 p_r_z property-sidebar widget-area">
                <aside class="widget widget-search">
                    <h2 class="widget-title">Filtrar<span>Imóveis</span></h2>
                    <form class="filter-form" name="filter" method="get" enctype="multipart/form-data">
                        <input type="hidden" name="send_form" value="<?= md5(mt_rand()); ?>">
                        <select name="transaction" required="">
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
                        <a class=" btn" href="<?= SITE_URL; ?>/filtro" title="FILTRAR">FILTRAR</a>
                    </form>
                </aside>
                <?php
                    $read->ExeRead('ws_properties', 'WHERE realty_status = 1 AND realty_featured = 1 ORDER BY rand() LIMIT 4');
                    if ($read->getResult()) {
                        ?>
                        <!-- Imóveis em destaque -->
                        <aside class="widget widget-property-featured">
                            <h2 class="widget-title"><span>Imóveis</span> em destaque</h2>
                            <?php foreach ($read->getResult() as $value) { ?>

                                <div class="property-featured-inner">
                                    <div class="col-md-4 col-sm-3 col-xs-2 p_z">
                                        <a title="<?= $value['realty_title']; ?>"
                                           href="<?= SITE_URL . '/imovel/' . $value['realty_name']; ?>">
                                            <?= Check::Image1('uploads/', $value['realty_cover'], $value['realty_title'], 'class="realty-cover-owl"', 85, 64); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-8 col-sm-9 col-xs-10 featured-content">
                                        <a title="<?= $value['realty_title']; ?>"
                                           href="<?= SITE_URL . '/imovel/' . $value['realty_name']; ?>">
                                            <?= $value['realty_title']; ?>
                                        </a>
                                        <h3><?= ($value['realty_price'] ? number_format($value['realty_price'], '2', ',', '.') : 'Combinar'); ?></h3>
                                    </div>
                                </div>
                            <?php }
                                unset($value); ?>

                        </aside>
                    <?php }
                    unset($read);
                ?>

            </div><!-- /Imóveis em destaque -->
        </div>
    </div><!-- /Property Listing Section -->
</div>