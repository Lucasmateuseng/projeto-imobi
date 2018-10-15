<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :('); ?>
<div class="page-content">
    <!-- contact-detail -->
    <div id="contact-detail" class="contact-detail" style="margin-bottom: 10px;">
        <div class="container">
            <!-- contato da empresa -->
            <div class="content">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="contact-logo-box">
                        <img src="<?= INCLUDE_PATH; ?>/images/logo-centenario-web.png" alt="<?= SITE_NAME; ?>"
                             title="<?= SITE_NAME; ?>">
                    </div>
                    <div class="contact-address">
                        <p>
                            <i class="fa fa-map-marker"></i>
                            <span> <?= SITE_ADDR . ', ' . SITE_DISTRICT . ' - ' . SITE_CITY . ' - ' . SITE_UF; ?> </span>
                        </p>
                        <p>
                            <i class="fa fa-phone"></i>
                            <span> <?= SITE_PHONE1; ?></span>
                        </p>
                        <p>
                            <i class="fa fa-whatsapp"></i>
                            <span> <?= SITE_PHONE2; ?></span>
                        </p>
                        <p>
                            <i class="fa fa-envelope-o"></i>
                            <a title="mailto" href="mailto:<?= SITE_EMAIL; ?>"><?= SITE_EMAIL; ?></a>
                        </p>
                    </div>
                    <!-- Redes sociais -->
                    <ul class="contact-social-icon">
                        <?php
                            if (SITE_SOCIAL_FB) { ?>
                                <li>
                                    <a href="https://facebook.com/<?= SITE_SOCIAL_FB_PAGE; ?>" target="_blank"><i
                                                class="fa fa-facebook"></i></a>
                                </li>
                            <?php }
                            if (SITE_SOCIAL_GOOGLE) { ?>
                                <li><a href="https://plus.google.com/<?= SITE_SOCIAL_GOOGLE_PAGE; ?>"
                                       target="_blank"><i class="fa fa-google-plus"></i></a></li>
                            <?php }
                            if (SITE_SOCIAL_TWITTER) { ?>
                                <li><a href="https://twitter.com/<?= SITE_SOCIAL_TWITTER; ?>"><i
                                                class="fa fa-twitter"></i></a></li>
                            <?php } ?>
                    </ul><!-- /Redes sociais -->
                </div>

                <!-- FORMULARIO -->
                <div class="col-md-8 col-sm-6 col-xs-12">
                    <div class="contact-feedback-form">
                        <h3>ENVIE-NOS UMA MENSAGEM</h3>
                        <form action="" method="post" autocomplete="off">
                            <div class="col-md-12 col-sm-12">
                                <div id="alert-msg" class="alert-msg"></div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <input type="text" id="input_name" name="name" placeholder="SEU NOME" required=""/>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <input type="email" id="input_email" name="email" placeholder="E-MAIL"
                                       required=""/>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <input type="text" id="input_subject" name="subject" placeholder="Assunto"
                                       required=""/>
                            </div>
                            <div class="col-md-12 col-xs-12">
                        <textarea rows="3" id="textarea_message" name="message"
                                  placeholder="ESCREVA SUA MENSAGEM"></textarea>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <button type="submit" id="btn_smt" class="btn">ENVIAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- container /- -->
        </div><!-- contact-detail /- -->
        <!-- contact-address-group-section/- -->
    </div>
    <?php include(REQUIRE_PATH . '/inc/partner.inc.php'); ?>

</div><!-- Page Content -->