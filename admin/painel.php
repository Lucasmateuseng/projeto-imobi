<?php
    ob_start();
    session_start();
    require('../_app/Config.inc.php');

    define('BASEPATH', dirname(__FILE__));

    $login = new Login(5);
    $logoff = filter_input(INPUT_GET, 'logoff', FILTER_VALIDATE_BOOLEAN);
    $get_exe = filter_input(INPUT_GET, 'exe', FILTER_DEFAULT);

    /** Verifica se existe usuário logado */
    if (!$login->CheckLogin()) {
        unset($_SESSION['userlogin']);
        Alert::set_flashdata('msg', $lang['user_permission'], E_USER_ERROR);
        header('Location: index.php');
        exit();
    } else {
        $userlogin = $_SESSION['userlogin'];
    }

    /** Faz o logoff */
    if ($logoff) {
        unset($_SESSION['userlogin']);
        Alert::set_flashdata('msg', '<i class="fa fa-check fa-fw"></i> <b>Tudo certo.</b> Você deslogou com sucesso do sistema');
        header('Location: index.php');
        exit();
    }

    /** Seta menu como ativo */
    if (isset($get_exe)) {
        $linkto = explode('/', $get_exe);
    } else {
        $linkto = array();
    }
?>
    <!DOCTYPE html>
    <html lang="pt">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Painel de controle - <?= SITE_NAME; ?></title>
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
        <link href="css/sb-admin.css" rel="stylesheet">
        <link href="vendor/bootstrap/css/pricing.css" rel="stylesheet">

        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/jquery/jquery.form.js"></script>
        <script src="vendor/jquery/jquery.mask.min.js"></script>

    </head>

    <body class="sticky-footer bg-dark" id="page-top">
    <!-- Navegação-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top" id="mainNav">
        <a class="navbar-brand" href="painel.php"><?= SITE_NAME; ?></a>
        <!-- Menu mobile -->
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- /Menu mobile -->
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav">
                <li class="nav-item<?php if (empty($linkto)) echo ' active'; ?>" data-toggle="tooltip"
                    data-placement="right" title="Dashboard">
                    <a class="nav-link" href="painel.php">
                        <i class="fa fa-fw fa-dashboard"></i> <span class="nav-link-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item<?php if (in_array('pages', $linkto)) echo ' active'; ?>" data-toggle="tooltip"
                    data-placement="right" title="Páginas">
                    <a class="nav-link" href="painel.php?exe=pages/index">
                        <i class="fa fa-fw fa-file-text"></i> <span class="nav-link-text">Páginas</span>
                    </a>
                </li>
                <li class="nav-item<?php if (in_array('slides', $linkto)) echo ' active'; ?>" data-toggle="tooltip"
                    data-placement="right" title="Slides">
                    <a class="nav-link" href="painel.php?exe=slides/index">
                        <i class="fa fa-fw fa-picture-o"></i> <span class="nav-link-text">Slides</span>
                    </a>
                </li>
                <li class="nav-item<?php if (in_array('properties', $linkto)) echo ' active'; ?>" data-toggle="tooltip"
                    title="Imóveis">
                    <a class="nav-link" href="painel.php?exe=properties/index">
                        <i class="fa fa-fw fa-home"></i> <span class="nav-link-text">Imóveis</span>
                    </a>
                </li>
                <li class="nav-item<?php if (in_array('partners', $linkto)) echo ' active'; ?>" data-toggle="tooltip"
                    data-placement="right" title="Patrocinadores">
                    <a class="nav-link" href="painel.php?exe=partners/index">
                        <i class="fa fa-fw fa-handshake-o"></i> <span class="nav-link-text">Patrocinadores</span>
                    </a>
                </li>
                <?php if ($_SESSION['userlogin']['user_level'] >= 6) { ?>
                    <li class="nav-item<?php if (in_array('users', $linkto)) echo ' active'; ?>" data-toggle="tooltip"
                        title="Usuários">
                        <a class="nav-link" href="painel.php?exe=users/index">
                            <i class="fa fa-fw fa-user"></i> <span class="nav-link-text">Usuários</span>
                        </a>
                    </li>
                    <li class="nav-item<?php if (in_array('settings', $linkto)) echo ' active'; ?>"
                        data-toggle="tooltip"
                        data-placement="right" title="Configurações">
                        <a class="nav-link" href="painel.php?exe=settings/index">
                            <i class="fa fa-fw fa-gear"></i> <span class="nav-link-text">Configurações</span>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Ver site">
                    <a class="nav-link external" href="../">
                        <i class="fa fa-fw fa-desktop"></i> <span class="nav-link-text">Ver site</span>
                    </a>
                </li>
            </ul>
            <!-- Recolher menu -->
            <ul class="navbar-nav sidenav-toggler">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                        <i class="fa fa-fw fa-angle-left"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0 mr-lg-2" action="painel.php?exe=properties/search"
                          method="post" autocomplete="off">
                        <div class="input-group">
                            <input type="text" class="form-control" name="ref" placeholder="Código do imóvel..."
                                   minlength="3" required>
                            <span class="input-group-append">
                                <button class="btn btn-primary">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="painel.php?logoff=true"><i class="fa fa-fw fa-sign-out"></i> Sair</a>
                </li>
            </ul>

        </div>
    </nav>

    <div class="content-wrapper">
        <?php
            if (!empty($get_exe)) {
                $include_patch = __DIR__ . DS . 'system' . DS . strip_tags(trim($get_exe) . '.php');
            } else {
                $include_patch = __DIR__ . DS . 'system' . DS . 'home.php';
            }
            if (file_exists($include_patch)) {
                require_once($include_patch);
            } else {
                echo Alert::alert_msg('<i class="fa fa-exclamation fa-fw"></i><b>Erro ao incluir tela:</b> Erro ao incluir o controller /' . $get_exe . '.php!', E_USER_ERROR);
            }
        ?>

    </div>

    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center">
                <small>Copyright © Your Website <?= date('Y'); ?></small>
            </div>
        </div>
    </footer>
    <!-- Botão subir ao topo -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fa fa-angle-up"></i>
    </a>
    <!-- Bootstrap -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Datatables -->
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <!-- Custom template -->
    <script src="js/sb-admin.js"></script>
    <script src="js/sb-admin-datatables.min.js"></script>
    <script src="js/tinymce/tinymce.min.js"></script>
    </body>

    </html>
<?php
    ob_end_flush();
