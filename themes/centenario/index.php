<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :(');
    $read = new Read();
?>
<!-- Page Content -->
<div class="page-content">
    <!-- Slider block -->
    <div class="slider-block container-fluid p_z">
        <!-- Slider Section -->
        <div id="property-slider" class="carousel slide slider-section" data-ride="carousel">

            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php
                    $i = 0;
                    $active = 'class="active"';
                    $read->ExeRead('ws_slides', 'WHERE slide_status = 1 AND slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL) ORDER BY slide_date DESC');
                    foreach ($read->getResult() as $value) {
                        extract($value);
                        $active = (!$i ? 'class="active"' : '');
                        ?>
                        <li data-target="#property-slider" data-slide-to="<?= $i; ?>" <?= $active; ?>>
                            <?= Check::Image1('uploads/', $slide_image, $slide_title, '', 43, 45); ?>
                        </li>
                        <?php $i++;
                    }
                    unset($i, $active, $value); ?>
            </ol>

            <!-- Embalagem para slides -->
            <div class="carousel-inner" role="listbox">
                <?php
                    $s = 0;
                    $active_s = 'class="active"';
                    foreach ($read->getResult() as $value) {
                        extract($value);
                        $active_s = (!$s ? 'active' : '');
                        ?>
                        <div class="item <?= $active_s; ?>">
                            <?= Check::Image1('uploads/', $slide_image, $slide_title, '', SLIDE_W, SLIDE_H); ?>
                            <div class="carousel-caption">
                                <div class="slider-content">
                                    <h4><?= ($slide_price ? 'R$ ' . number_format($slide_price, '2', ',', '.') : 'Combinar'); ?> </h4>
                                    <h3><?= $slide_title; ?></h3>
                                    <p><?= $slide_description; ?></p>
                                    <a href="<?= $slide_link; ?>" title="Ver Link" class="caption-arrow">
                                        <i class="fa fa-long-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php $s++;
                    }
                    unset($s, $active_s, $value); ?>

            </div>
            <!-- Controls -->
            <a class="left carousel-control" href="#property-slider" role="button" data-slide="prev">
                <span class="fa fa-long-arrow-left" aria-hidden="true"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="right carousel-control" href="#property-slider" role="button" data-slide="next">
                <span class="fa fa-long-arrow-right" aria-hidden="true"></span>
                <span class="sr-only">Próximo</span>
            </a>
        </div><!-- Slider Section /- -->
    </div><!-- Slider Block /- -->
    <?php include_once(REQUIRE_PATH . '/inc/form_filter.inc.php');
        $read->ExeRead('ws_properties', 'WHERE realty_status = 1 AND realty_date <= NOW() AND realty_featured = 1 ORDER BY RAND() LIMIT 10');
        if ($read->getResult()) {
            ?>
            <!-- propriedades em destaque -->
            <div id="featured-section" class="featured-section container-fluid p_z">
                <div class="container">
                    <div class="section-header">
                        <p>TENDÊNCIAS</p>
                        <h3>PROPRIEDADES EM DESTAQUE</h3>
                    </div>
                    <div id="featured-property" class="featured-property row">
                        <?php
                            foreach ($read->getResult() as $value) {
                                extract($value); ?>
                                <!-- Item -->
                                <div class="item">
                                    <!-- col-md-12 -->
                                    <div class="col-md-12 <?= ($realty_transaction == 1 ? 'rent' : 'sale'); ?>-block">
                                        <?php include(REQUIRE_PATH . '/inc/realty.inc.php'); ?>
                                    </div><!-- /col-md-12 -->
                                </div><!-- /Item -->
                            <?php }
                            unset($value); ?>

                    </div>
                </div>
            </div><!-- /propriedades em destaque -->
        <?php } ?>

    <!-- propriedades para alugar e vender -->
    <div id="rent-and-sale-section" class="rent-and-sale-section">
        <!-- container -->
        <div class="container">
            <?php
                $read->ExeRead('ws_properties', 'WHERE realty_status = 1 AND realty_date <= NOW() AND realty_transaction = :tran ORDER BY realty_date DESC LIMIT :limit', "tran=1&limit=5");
                if ($read->getResult()) {
                    ?>

                    <!-- propriedades para alugar -->
                    <div class="rent-property">
                        <div class="col-md-3">
                            <div class="section-header">
                                <h3><span>PROPRIEDADES</span>PARA ALUGAR</h3>
                                <p>Nossas últimas propriedades listadas e confira as instalações em eles.</p>
                            </div>
                        </div>
                        <div class="col-md-9 p_r_z">
                            <div id="rent-property-block" class="rent-property-block">
                                <?php
                                    foreach ($read->getResult() as $value) {
                                        extract($value);
                                        ?>

                                        <!-- Item -->
                                        <div class="item">
                                            <!-- col-md-12 -->
                                            <div class="col-md-12 rent-block">
                                                <!-- Property Main Box -->
                                                <div class="property-main-box">
                                                    <div class="property-images-box">
                                                        <a title="<?= $realty_title; ?>"
                                                           href="<?= SITE_URL; ?>/imovel/<?= $realty_name; ?>">
                                                            <?= Check::Image1('uploads/', $realty_cover, $realty_title, 'class="realty-cover-owl"', THUMB_W, THUMB_H); ?>
                                                        </a>
                                                        <h4><?= ($realty_price ? 'R$ ' . number_format($realty_price, '2', ',', '.') : 'Combinar'); ?></h4>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="property-details">
                                                        <a title="<?= $realty_title; ?>"
                                                           href="<?= SITE_URL; ?>/imovel/<?= $realty_name; ?>"><?= $realty_title; ?></a>
                                                        <ul>
                                                            <li><i class="fa fa-expand"
                                                                   title="Área total"></i><?= $realty_totalarea; ?>
                                                            </li>
                                                            <li>
                                                                <i class="fa fa-bed"></i><?= $realty_bedrooms; ?>
                                                            </li>
                                                            <li>
                                                                <i class="fa fa-bath"></i><?= $realty_bathrooms; ?>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div><!-- Property Main Box -->
                                            </div>
                                        </div><!-- /Item -->
                                    <?php }
                                    unset($value); ?>

                            </div>
                        </div>
                    </div><!-- /propriedades para alugar -->
                    <div class="clearfix"></div>
                <?php }
                $read->setPlaces("tran=2&limit=5");
                if ($read->getResult()) {
                    ?>
                    <!-- propriedades para vender -->
                    <div class="sale-property">
                        <div class="col-md-3">
                            <div class="section-header">
                                <h3><span>PROPRIEDADES</span>Á VENDA</h3>
                                <p>Nossas últimas propriedades listadas e confira as instalações em eles.</p>
                            </div>
                        </div>
                        <div class="col-md-9 p_r_z">
                            <div id="sale-property-block" class="sale-property-block">
                                <?php
                                    foreach ($read->getResult() as $value) {
                                        extract($value);
                                        ?>

                                        <!-- Item -->
                                        <div class="item">
                                            <!-- col-md-12 -->
                                            <div class="col-md-12 sale-block">
                                                <!-- Property Main Box -->
                                                <div class="property-main-box">
                                                    <div class="property-images-box">
                                                        <a title="<?= $realty_title; ?>"
                                                           href="<?= SITE_URL; ?>/imovel/<?= $realty_name; ?>">
                                                            <?= Check::Image1('uploads/', $realty_cover, $realty_title, 'class="realty-cover-owl"', THUMB_W, THUMB_H); ?>
                                                        </a>
                                                        <h4><?= ($realty_price ? 'R$ ' . number_format($realty_price, '2', ',', '.') : 'Combinar'); ?></h4>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="property-details">
                                                        <a title="<?= $realty_title; ?>"
                                                           href="<?= SITE_URL; ?>/imovel/<?= $realty_name; ?>"><?= $realty_title; ?></a>
                                                        <ul>
                                                            <li><i class="fa fa-expand"
                                                                   title="Área total"></i><?= $realty_totalarea; ?>
                                                            </li>
                                                            <li>
                                                                <i class="fa fa-bed"></i><?= $realty_bedrooms; ?>
                                                            </li>
                                                            <li>
                                                                <i class="fa fa-bath"></i><?= $realty_bathrooms; ?>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div><!-- Property Main Box -->
                                            </div>
                                        </div><!-- /Item -->
                                    <?php }
                                    unset($value); ?>

                            </div>
                        </div>
                    </div><!-- /propriedades para vender -->
                <?php } ?>
        </div><!-- container /- -->
    </div><!-- Property Rent And Sale Section /- -->
    <?php
        include(REQUIRE_PATH . '/inc/partner.inc.php'); ?>

</div><!-- Page Content -->
