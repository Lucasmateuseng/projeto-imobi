<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH');
    /** Faz a consulta */
    $read = new Read();
    $read->ExeRead('ws_pages', 'WHERE page_name = :page_name', "page_name=sobre-nos");
?>
<!-- Seção de rodapé -->
<div id="footer-section" class="footer-section">
    <!-- container -->
    <div class="container">
        <!-- col-md-3 -->
        <div class="col-md-3 col-sm-6">
            <!-- About Widget -->
            <aside class="widget widget_about">
                <h3 class="widget-title">Sobre Nós</h3>
                <p><?= ($read->getResult() && !empty($read->getResult()[0]['page_content']) ? $read->getResult()[0]['page_content'] : '');
                        unset($read); ?></p>
            </aside>
            <!-- /About Widget -->
        </div><!-- /col-md-3 -->

        <div class="col-md-3 col-sm-6">
            <!-- Address Widget -->
            <aside class="widget widget_address">
                <h3 class="widget-title">Endereço</h3>
                <p>
                    <i class="fa fa-map-marker fa-fw"></i> <?= SITE_ADDR . ', <br> ' . SITE_DISTRICT . ' - ' . SITE_CITY . ' - ' . SITE_UF; ?>
                </p>
                <span><i class="fa fa-phone fa-fw"></i> <?= SITE_PHONE1; ?></span>
                <span><i class="fa fa-whatsapp fa-fw"></i> <?= SITE_PHONE2; ?></span>
                <a title="EMAIL" href="mailto: "><i class="fa fa-envelope-o fa-fw"></i> <?= SITE_EMAIL; ?></a>
            </aside><!-- /Address Widget -->
        </div><!-- /col-md-3 -->

        <!-- col-md-3 -->
        <div class="col-md-3 col-sm-6">
            <!-- Address Widget -->
            <aside class="widget widget_newsletter">
                <h3 class="widget-title"></h3>
                <div class="maps">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3705.779383622678!2d-48.83723288548076!3d-21.750076985612054!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94bed62bd416bcef%3A0xe859efe063e3c76a!2sCentenario+Im%C3%B3veis!5e0!3m2!1spt-BR!2sbr!4v1519667381961"
                            width="600" height="400" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div><!-- /input-group -->
                <strong class="text-center">VENHA NÓS FAZER UMA VISITA</strong>
            </aside><!-- Address Widget /- -->
        </div><!-- col-md-3 -->
    </div><!-- container /- -->

    <!-- Footer Bottom -->
    <div id="footer-bottom" class="footer-bottom">
        <!-- container -->
        <div class="container">
            <p class="col-md-6 col-sm-6 col-xs-12">&copy; <?= date('Y'); ?> CENTENÁRIO IMÓVEIS - TODOS DIREITOS
                RESERVADOS - CNPJ <?= SITE_CNPJ; ?></p>
            <div class="col-md-4 col-sm-6 col-xs-12 pull-right social">
                <ul class="footer_social m_b_z">
                    <?php
                        if (SITE_SOCIAL_FB) { ?>

                            <li>
                                <a href="https://facebook.com/<?= SITE_SOCIAL_FB_PAGE; ?>" target="_blank"><i
                                            class="fa fa-facebook"></i></a>
                            </li>
                        <?php }
                        if (SITE_SOCIAL_GOOGLE) { ?>

                            <li>
                                <a href="https://plus.google.com/<?= SITE_SOCIAL_GOOGLE_PAGE; ?>" target="_blank"><i
                                            class="fa fa-google-plus"></i></a>
                            </li>
                        <?php }
                        if (SITE_SOCIAL_TWITTER) { ?>

                            <li>
                                <a href="https://twitter.com/<?= SITE_SOCIAL_TWITTER; ?>" target="_blank"><i
                                            class="fa fa-twitter"></i></a>
                            </li>
                        <?php } ?>

                </ul>
                <a href="#" title="Subir ao topo" id="back-to-top" class="back-to-top">
                    <i class="fa fa-long-arrow-up"></i> TOPO</a>
            </div>
        </div><!-- container /- -->
    </div><!-- Footer Bottom /- -->
</div><!-- Footer Section -->
