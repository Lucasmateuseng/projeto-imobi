<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :('); ?>
<!-- Header -->
<header id="header-section" class="header header1 container-fluid p_z">
    <div class="content">
        <!-- Topo Header -->
        <div class="top-header">
            <p class="col-md-10 col-sm-12">
                <span>CRECI <?= SITE_CRECI; ?></span>
                <span><i class="tel_top fa fa-phone fa-fw"></i> <?= SITE_PHONE1; ?> </span>
                <span><a title="mail-to" href="mailto:<?= SITE_EMAIL; ?>">
                        <i class="fa fa-envelope-o"></i> <?= SITE_EMAIL; ?>
                    </a>
                </span>
            </p>
            <div class="icons-rede">
                <ul class="property-social p_l_z m_b_z">
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
            </div>
        </div><!-- /Topo Header -->
    </div>

    <!-- Bloco de navegação -->
    <div class="navigation-block">
        <!-- Logo Block -->
        <div class="col-md-2 logo-block no-padding">
            <a title="<?= SITE_NAME; ?>" href="<?= SITE_URL; ?>">
                <img src="<?= INCLUDE_PATH; ?>/images/logo-centenario-web.png" alt="<?= SITE_NAME; ?>"/>
            </a>
        </div><!-- /Logo Block  -->

        <!-- Menu Top -->
        <div class="col-md-10 menu-block">
            <!-- Menu Nav -->
            <nav class="navbar navbar-default navbar-top">
                <div class="navbar-collapse collapse" id="navbar">
                    <ul class="nav navbar-nav navbar-left">
                        <li>
                            <a href="<?= SITE_URL; ?>">INÍCIO</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL; ?>/imoveis/comprar">COMPRAR</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL; ?>/imoveis/alugar">ALUGAR</a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL; ?>/contato">CONTATE-NOS</a>
                        </li>
                    </ul>
                </div>
            </nav><!-- /Menu Nav -->
        </div><!-- /Menu Block -->
    </div><!-- /Bloco de navegação -->

</header><!-- /Header -->
