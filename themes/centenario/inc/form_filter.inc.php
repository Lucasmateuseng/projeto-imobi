<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit('Acesso negado :('); ?>
<!-- Filtro -->
<div id="search-section" class="search-section container-fluid p_z">
    <!-- Container -->
    <div class="container">
        <form class="filter-form" name="filter" method="post" enctype="multipart/form-data">
            <input type="hidden" name="send_form" value="<?= md5(mt_rand()); ?>">
            <!-- col-md-10 -->
            <div class="col-md-10 col-sm-9 p_l_z">
                <select name="transaction" required="">
                    <option>O QUE DESEJA</option>
                    <?php foreach (realty_transaction() as $key => $value) { ?>
                        <option value="<?= $key; ?>"><?= $value; ?></option>
                    <?php }
                        unset($key, $value); ?>
                </select>

                <select name="type">
                    <option>TIPO DE IMÓVEL</option>
                </select>

                <select name="finality">
                    <option>FINALIDADE</option>
                </select>

                <select name="district">
                    <option>BAIRRO</option>
                </select>

                <select name="bedrooms">
                    <option>DORMITÓRIOS</option>
                </select>

                <select name="min_price">
                    <option>VALOR MÍNIMO</option>
                </select>

                <select name="max_price">
                    <option>VALOR MÁXIMO</option>
                </select>
            </div><!-- col-md-10 /- -->
            <!-- col-md-2 -->
            <div class="col-md-2 col-sm-3">
                <div class="section-header">
                    <h3><span>filtre a</span></span>Propiedade</h3>
                    <a href="<?= SITE_URL; ?>/filtro" title="Filtrar" class="btn">FILTRAR</a>
                </div>
            </div><!-- col-md-2 /- -->
        </form>
    </div><!-- Container /- -->
</div><!-- FIM FILTRO /- -->