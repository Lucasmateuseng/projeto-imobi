<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :(');
    
    if (PARTNER_ENABLE) {
        $read = new Read();
        $read->ExeRead('ws_partners', 'WHERE partner_status = 1 ORDER BY partner_update = NOW()');
        if ($read->getResult()) {
            ?>
            <!-- Partner Section -->
            <div id="partner-section" class="partner-section ">
                <div class="container">
                    <div id="business-partner" class="business-partner">
                        <?php foreach ($read->getResult() as $value) {
                            extract($value); ?>

                            <div class="item">
                                <?php if (!empty($partner_link)) {
                                    echo '<a title="' . $partner_title . '" href="' . $partner_link . '"  target="_blank">';
                                    echo Check::Image1('uploads/', $partner_logo, $partner_title, '', PARTNER_LOGO_W, PARTNER_LOGO_H);
                                    echo '</a>';
                                } else {
                                    echo Check::Image1('uploads/', $partner_logo, $partner_title, '', PARTNER_LOGO_W, PARTNER_LOGO_H);
                                } ?>

                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div><!-- Partner Section /- -->
        <?php }
        unset($read, $value);
    } ?>