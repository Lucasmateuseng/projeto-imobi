<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :('); ?>
<!-- Property Main Box -->
<div class="property-main-box">
    <div class="property-images-box">
        <span><?= ($realty_transaction == 1 ? 'A' : 'V'); ?></span>
        <a title="<?= $realty_title; ?>" href="<?= SITE_URL; ?>/imovel/<?= $realty_name; ?>">
            <?= Check::Image1('uploads/', $realty_cover, $realty_title, 'class="realty-cover-owl"',  THUMB_W, THUMB_H); ?>
        </a>
        <h4><?= ($realty_price ? 'R$ ' . number_format($realty_price, '2', ',', '.') : 'Combinar'); ?></h4>
    </div>
    <div class="clearfix"></div>
    <div class="property-details">
        <a title="<?= $realty_title; ?>" href="<?= SITE_URL; ?>/imovel/<?= $realty_name; ?>">
            <?= $realty_title; ?>
        </a>
        <ul>
            <li><i class="fa fa-expand"
                   title="Área total"></i><?= $realty_totalarea; ?> m²
            </li>
            <li><i class="fa fa-bed"></i><?= $realty_bedrooms; ?></li>
            <li><i class="fa fa-bath"></i><?= $realty_bathrooms; ?></li>
        </ul>
    </div>
</div><!-- /Property Main Box -->