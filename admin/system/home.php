<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) || $_SESSION['userlogin']['user_level'] < 5) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    $read = new Read();
?>

<div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <div class="dashboard-stats">
        <div class="card-deck mb-3">
            <div class="card text-white bg-success mb-3 box-shadow" style="max-width: 18rem;">
                <div class="card-header"><i class="fa fa-globe fa-fw"></i> Usuários online</div>
                <div class="card-body d-flex flex-column">
                    <div class="card-body-icon"><i class="fa fa-fw fa-users"></i></div>
                    <h2 class="card-text text-center siteviews_online">
                        <i class="fa fa-users"></i> <?= Check::UserOnline(); ?>
                    </h2>
                </div>
                <span class="card-footer text-white clearfix small z-1 text-center">Intervalo de 20 minutos</span>
            </div>
            <div class="card text-white bg-info mb-3 box-shadow" style="max-width: 18rem;">
                <div class="card-header"><i class="fa fa-fw fa-line-chart"></i> Visitas de hoje</div>
                <div class="card-body d-flex flex-column">
                    <div class="card-body-icon"><i class="fa fa-fw fa-line-chart"></i></div>
                    <div class="card-count-views count-views">
                        <p class="siteviews_users"><b>0000</b><span>Usuários</span></p>
                        <p class="siteviews_views"><b>0000</b><span>Visitas</span></p>
                        <p class="siteviews_pages"><b>0000</b><span>Páginas</span></p>
                    </div>
                </div>
                <span class="card-footer text-white clearfix small z-1 text-center siteviews_stats">0 Páginas por visita</span>
            </div>
            <div class="card text-white bg-warning mb-3 box-shadow" style="max-width: 18rem;">
                <div class="card-header"><i class="fa fa-fw fa-bar-chart"></i> Visitas do mês</div>
                <div class="card-body d-flex flex-column">
                    <div class="card-body-icon"><i class="fa fa-fw fa-bar-chart"></i></div>
                    <div class="card-count-views count-views">

                        <?php
                            $read->FullRead("SELECT sum(siteviews_users) AS users, sum(siteviews_views) AS views, sum(siteviews_pages) AS pages FROM ws_siteviews WHERE year(siteviews_date) = year(NOW()) AND month(siteviews_date) = month(NOW())");
                            if (!$read->getResult()) {
                                $stats = 00;
                                ?>

                                <p><b>0000</b><span>Usuários</span></p>
                                <p><b>0000</b><span>Visitas</span></p>
                                <p><b>0000</b><span>Páginas</span></p>

                            <?php } else {
                                $views = $read->getResult()[0];
                                $stats = (!empty($views['pages']) ? number_format($views['pages'] / $views['views'], 2, '.', '') : '0.00');
                                echo "<p><b>" . str_pad($views['users'], 4, 0, STR_PAD_LEFT) . "</b><span>Usuários</span></p>";
                                echo "<p><b>" . str_pad($views['views'], 4, 0, STR_PAD_LEFT) . "</b><span>Visitas</span></p>";
                                echo "<p><b>" . str_pad($views['pages'], 4, 0, STR_PAD_LEFT) . "</b><span>Páginas</span></p>";
                            }
                        ?>

                    </div>
                </div>
                <span class="card-footer text-white clearfix small z-1 text-center"><?= $stats; ?>
                    Páginas por visita</span>
            </div>
            <div class="card text-white bg-danger mb-3 box-shadow" style="max-width: 18rem;">
                <div class="card-header"><i class="fa fa-fw fa-area-chart"></i> Total de visitas</div>
                <div class="card-body d-flex flex-column">
                    <div class="card-body-icon"><i class="fa fa-fw fa-area-chart"></i></div>
                    <div class="card-count-views count-views">

                        <?php
                            unset($views, $stats);
                            $read->FullRead("SELECT sum(siteviews_users) AS users, sum(siteviews_views) AS views, sum(siteviews_pages) AS pages FROM ws_siteviews");
                            if (!$read->getResult()) {
                                $stats = 00; ?>
                                <p><b>0000</b><span>Usuários</span></p>
                                <p><b>0000</b><span>Visitas</span></p>
                                <p><b>0000</b><span>Páginas</span></p>
                            <?php } else {
                                $views = $read->getResult()[0];
                                $stats = (!empty($views['pages']) ? number_format($views['pages'] / $views['views'], 2, '.', '') : '0.00');
                                echo "<p><b>" . str_pad($views['users'], 4, 0, STR_PAD_LEFT) . "</b><span>Usuários</span></p>";
                                echo "<p><b>" . str_pad($views['views'], 4, 0, STR_PAD_LEFT) . "</b><span>Visitas</span></p>";
                                echo "<p><b>" . str_pad($views['pages'], 4, 0, STR_PAD_LEFT) . "</b><span>Páginas</span></p>";
                            }
                        ?>

                    </div>
                </div>
                <span class="card-footer text-white clearfix small z-1 text-center"><?= $stats; ?>
                    Páginas por visita</span>
            </div>
        </div>
    </div>

    <?php
        unset($views, $stats);
        $read->ExeRead('ws_properties', "WHERE realty_status = 1 AND realty_views > 0 ORDER BY realty_views DESC, realty_date DESC LIMIT 10");
        if (!$read->getResult()) {
            echo Alert::alert_msg('<i class="fa fa-exclamation fa-fw"></i> Ainda não existem imóveis cadastrados!', E_USER_NOTICE);
        } else {
            ?>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-home"></i> Imóveis mais vistos
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Código</th>
                                <th>Bairro</th>
                                <th>Cidade</th>
                                <th>Tipo do imóvel</th>
                                <th>Transação</th>
                                <th>Visitas</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Código</th>
                                <th>Bairro</th>
                                <th>Cidade</th>
                                <th>Tipo</th>
                                <th>Transação</th>
                                <th>Finalidade</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            <?php foreach ($read->getResult() as $value) { ?>
                                <tr>
                                    <td><?= $value['realty_ref']; ?></td>
                                    <td><?= $value['realty_district']; ?></td>
                                    <td><?= $value['realty_city']; ?></td>
                                    <td><?= realty_type($value['realty_type']); ?></td>
                                    <td><?= realty_transaction($value['realty_transaction']); ?></td>
                                    <td><?= $value['realty_views']; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php }
        unset($read); ?>
</div>
<script>
    setInterval(function () {
        dashboard();
    }, 10000);
</script>
