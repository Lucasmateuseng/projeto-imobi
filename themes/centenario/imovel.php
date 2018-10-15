<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :(');
    
    $read = new Read();
    $realty_name = strip_tags(trim($Link->getLink()));
    $read->ExeRead('ws_properties', "WHERE realty_status = 1 AND realty_date <= NOW() AND realty_name = :realty_name ", "realty_name={$realty_name}");
    if (!$read->getResult() || empty($realty_name)) {
        require REQUIRE_PATH . '/404.php';
    } else {
        extract($read->getResult()[0]);
        if (!isset($_SESSION['userlogin'])) {
            $update = new Update;
            $update->ExeUpdate('ws_properties', array('realty_views' => $realty_views + 1), "WHERE realty_id = :id", "id={$realty_id}");
        }
        ?>
        <!-- Page Content -->
        <div class="page-content">
            <!-- Property Detail Page -->
            <div class="property-main-details">
                <!-- container -->
                <div class="container">
                    <div class="property-header">
                        <h3><i class="fa fa-map-marker fa-fw"></i> <?= $realty_district . ' - ' . $realty_city; ?>
                            <span><?= realty_transaction($realty_transaction); ?></span></h3>
                        <ul>
                            <li><?= ($realty_price ? "R$ " . number_format($realty_price, '2', ',', '.') : 'Combinar'); ?></li>
                            <li>Referência: <?= $realty_ref; ?></li>
                            <li><i class="fa fa-expand"></i><?= $realty_totalarea; ?></li>
                            <li><i class="fa fa-bed"></i><?= $realty_bedrooms; ?></li>
                            <li><i class="fa fa-bath"></i><?= $realty_bathrooms; ?></li>
                            <li><i class="fa fa-car"></i><?= $realty_parkings; ?></li>
                        </ul>
                    </div>
                    <div class="property-details-content container-fluid p_z">
                        <!-- col-md-9 -->
                        <div class="col">
                            <!-- Slider Section -->
                            <div id="property-detail1-slider" class="carousel slide property-detail1-slider"
                                 data-ride="carousel">
                                <!-- Wrapper for slides -->
                                <div class="carousel-inner" role="listbox">
                                    <div class="item active">
                                        <?= Check::Image1('uploads/', $realty_cover, $realty_title, '', IMAGE_W, IMAGE_H); ?>
                                    </div>
                                    <?php
                                        $read->ExeRead('ws_properties_image', "WHERE realty_id = :id", "id={$realty_id}");
                                        if ($read->getResult()) {
                                            foreach ($read->getResult() as $value) { ?>
                                                <div class="item">
                                                    <?= Check::Image1('uploads/', $value['image'], $realty_title, '', IMAGE_W, IMAGE_H); ?>
                                                </div>
                                                <?php
                                            }
                                            unset($value);
                                        }
                                    ?>
                                </div>

                                <!-- Controls -->
                                <a class="left carousel-control" href="#property-detail1-slider" role="button"
                                   data-slide="prev">
                                    <span class="fa fa-long-arrow-left" aria-hidden="true"></span>
                                    <span class="sr-only">Anterior</span>
                                </a>
                                <a class="right carousel-control" href="#property-detail1-slider" role="button"
                                   data-slide="next">
                                    <span class="fa fa-long-arrow-right" aria-hidden="true"></span>
                                    <span class="sr-only">Proximo</span>
                                </a>
                            </div><!-- Slider Section /- -->
                            <div class="single-property-details">
                                <h3>Descrição</h3>
                                <p><?= $realty_description; ?></p>
                            </div>
                            <div class="property-direction pull-left">
                                <div class="property-map">
                                    <h3>Compartilhar este imóvel:</h3>
                                    <ul>
                                        <?php
                                            if (SITE_SOCIAL_FB) { ?>

                                                <li>
                                                    <a href="//facebook.com/sharer.php?u=<?= urlencode(SITE_URL . '/imovel/' . $realty_name); ?>"
                                                       title="facebook" target="_blank">
                                                        <i class="fa fa-facebook"></i>
                                                    </a>
                                                </li>
                                            <?php }
                                            if (SITE_SOCIAL_GOOGLE) { ?>

                                                <li>
                                                    <a href="//plus.google.com/share?url=<?= SITE_URL . '/imovel/' . $realty_name; ?>"
                                                       target="_blank"><i class="fa fa-google-plus"></i></a>
                                                </li>
                                            <?php }
                                            if (SITE_SOCIAL_TWITTER) { ?>

                                                <li>
                                                    <a href="https://twitter.com/<?= SITE_SOCIAL_TWITTER; ?>"
                                                       target="_blank"><i class="fa fa-twitter"></i></a>
                                                </li>
                                            <?php } ?>

                                    </ul>
                                </div>
                            </div>
                        </div><!-- col-md-9 /- -->
                    </div>
                    <!-- container -->
                    <div class="contact">
                        <aside class="widget widget-search">
                            <h2 class="widget-title">MANDA UMA MENSAGEM PARA NOSSOS<span>CORRETORES</span></h2>
                            <form method="POST" autocomplete="off">
                                <div id="alert-msg" class="alert-msg"></div>
                                <input type="hidden" name="subject" value="Imóvel REF: <?= $realty_ref; ?>"/>
                                <div class="form-group">
                                    <label>* Nome:</label>
                                    <input type="text" name="name" id="input_name" placeholder="SEU NOME" required=""/>
                                </div>
                                <div class="form-group">
                                    <label>* E-mail:</label>
                                    <input type="text" name="email" id="input_email" placeholder="SEU E-MAIL"
                                           required=""/>
                                </div>
                                <div class="form-group">
                                    <label>* Mensagem:</label>
                                    <textarea name="message" id="textarea_message" placeholder="SUA MENSAGEM" rows="4"
                                              required=""
                                              style="color: #000;"></textarea>
                                </div>
                                <input type="submit" class="btns" value="ENVIAR" id="btn_smt" class="btn"/>
                            </form>
                        </aside><!-- /Fim do contato-->
                    </div>

                </div><!-- container /- -->
            </div><!-- Property Detail Page /- -->
            <?php
                unset($read, $update);
                include(REQUIRE_PATH . '/inc/partner.inc.php'); ?>

        </div><!-- Page Content -->
        <?php
    }
