<?php
    ob_start();
    session_start();
    require('../_app/Config.inc.php');
?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Painel de controle - <?= SITE_NAME; ?></title>
        <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
        <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="css/sb-admin.css" rel="stylesheet">
    </head>
    <body class="bg-dark">
    <div class="container">
        <div class="card card-login mx-auto mt-5 p-3">
            <?php
                $login = new Login(3);
                if ($login->CheckLogin()) {
                    header('Location: painel.php');
                    exit();
                }
                $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                if (!empty($data['login'])) {
                    $login->ExeLogin($data);
                    if (!$login->getResult()) {
                        Alert::set_flashdata('msg', $login->getError()[0], $login->getError()[1]);
                        header('Location: index.php');
                        exit();
                    } else {
                        header('Location: painel.php');
                        exit();
                    }
                }
                echo Alert::flashdata('msg');
            ?>

            <div class="card-body p-0">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input class="form-control" id="email" name="user" type="email" aria-describedby="emailHelp"
                               placeholder="E-mail" required="">
                    </div>
                    <div class="form-group">
                        <label for="password">Senha</label>
                        <input class="form-control" id="password" name="pass" type="password" placeholder="Senha"
                               required="">
                    </div>
                    <button class="btn btn-primary btn-block" name="login" value="logar">Entrar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    </body>

    </html>
<?php
    ob_end_flush();

