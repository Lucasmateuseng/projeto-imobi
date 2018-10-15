<?php
    /** Evita o acesso direto ao arquivo */
    defined('BASEPATH') OR exit();
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($login) OR $_SESSION['userlogin']['user_level'] < 10) {
        die(Alert::alert_msg($lang['user_permission'], E_USER_ERROR));
    }
    /** Faz a consulta no banco de dados */
    $read = new Read;
    $read->FullRead('SELECT set_type FROM ws_settings GROUP BY set_type');
    /** Armazena o resultado na variável */
    $settings = $read->getResult();
    /** Verifica se obteve resultado */
    if (!$settings) {
        Alert::set_flashdata('msg', $lang['settings_notfound'], E_USER_NOTICE);
    }
?>

<div class="container-fluid">

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Configurações</li>
    </ol>

    <div class="col">
        <div class="row">
            <div class="col">
                <i class="fa fa-gears"></i> Lista de configurações
            </div>
        </div>
        <hr class="mt-2">
        <?php
            echo Alert::flashdata('msg');
        ?>

        <div class="row mt-3 mb-3">
            <div class="col tab-content" id="v-pills-tabContent">
                <?php
                    $active_class = 0;
                    if ($settings) {
                        foreach ($settings as $value) {
                            $current_content = '';
                            if (!$active_class) {
                                $active_class = 1;
                                $current_content = ' show active';
                            }
                            extract($value);
                            $read->ExeRead('ws_settings', 'WHERE set_type = :set_type', "set_type={$set_type}");
                            if ($read->getResult()) {
                                ?>

                                <div class="tab-pane fade <?= $current_content; ?>" id="<?= $set_type; ?>"
                                     role="tabpanel" aria-labelledby="<?= $set_type; ?>">
                                    <form class="form" method="post" action="settings/update">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4><?= $set_type; ?></h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-row">
                                                    <?php
                                                        foreach ($read->getResult() as $value) {
                                                            extract($value);
                                                            unset($value);
                                                            ?>

                                                            <div class="form-group col-md-6">
                                                                <label><?= $set_key; ?></label>
                                                                <input name="set_value[<?= $set_id; ?>]" type="text"
                                                                       class="mr-3 form-control"
                                                                       value="<?= $set_value ? htmlspecialchars($set_value, ENT_QUOTES) : 0; ?>">
                                                            </div>
                                                        <?php } ?>

                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-success btn-load" style="float: right;">
                                                        <i class="fa fa-refresh fa-fw"></i> Atualizar
                                                    </button>
                                                    <div class="icon-load"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <?php
                            }
                        }
                    }
                ?>

            </div>
            <?php if ($settings) { ?>
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-body nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                             aria-orientation="vertical">
                            <?php
                                $active_class = 0;
                                foreach ($settings as $value) {
                                    $current_tab = "";
                                    if (!$active_class) {
                                        $active_class = 1;
                                        $current_tab = ' active';
                                    }
                                    extract($value);
                                    ?>
                                    <a class="nav-link <?= $current_tab; ?>" id="<?= $set_type; ?>"
                                       data-toggle="pill" href="#<?= $set_type; ?>" role="tab"
                                       aria-controls="<?= $set_type; ?>" aria-selected="true"
                                       style="margin-top:0.8rem;">
                                        <i class="fa fa-cog fa-fw"></i> <?= $set_type; ?>
                                    </a>
                                <?php } ?>

                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
