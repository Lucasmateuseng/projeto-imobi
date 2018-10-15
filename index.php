<?php
    ob_start();
    require('./_app/Config.inc.php');
    define('BASEPATH', __DIR__);
    $Session = new Session;
    $Link = new Link;
    $Seo = new Seo($Link->getFile(), $Link->getLink());
    $getUrl = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
?>
    <!DOCTYPE html>
    <html lang="pt-br" itemscope itemtype="https://schema.org/<?= $Seo->getSchema(); ?>">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="mit" content="2017-05-31T08:21:59-03:00+14607">

        <title><?= $Seo->getTitle(); ?></title>
        <meta name="robots" content="index, follow"/>
        <?php
            if (SITE_SOCIAL_GOOGLE) {
                echo '<link rel="author" href="https://plus.google.com/' . SITE_SOCIAL_GOOGLE_AUTHOR . '/posts"/>' . "\r\n";
                echo '        <link rel="publisher" href="https://plus.google.com/' . SITE_SOCIAL_GOOGLE_PAGE . '"/>' . "\r\n";
            }
        ?>
        <link rel="canonical" href="<?= SITE_URL . '/' . $getUrl; ?>"/>
        <base href="<?= SITE_URL; ?>">

        <meta itemprop="name" content="<?= $Seo->getTitle(); ?>"/>
        <meta itemprop="description" content="<?= $Seo->getDescription(); ?>"/>
        <meta itemprop="image" content="<?= $Seo->getImage(); ?>"/>
        <meta itemprop="url" content="<?= SITE_URL . '/' . $getUrl; ?>"/>

        <meta property="og:url" content="<?= SITE_URL . '/' . $getUrl; ?>"/>
        <meta property="og:type" content="<?= $Seo->getSchema(); ?>"/>
        <meta property="og:title" content="<?= $Seo->getTitle(); ?>"/>
        <meta property="og:description" content="<?= $Seo->getDescription(); ?>"/>
        <meta property="og:image" content="<?= $Seo->getImage(); ?>"/>

        <meta property="og:site_name" content="<?= SITE_NAME; ?>"/>
        <meta property="og:locale" content="pt_BR"/>
        <?php
            if (SITE_SOCIAL_FB) {
                if (SITE_SOCIAL_FB_APP) {
                    echo '<meta property="og:app_id" content="' . SITE_SOCIAL_FB_APP . '" />' . "\r\n";
                }
                echo '<meta property="article:author" content="https://www.facebook.com/' . SITE_SOCIAL_FB_AUTHOR . '" />' . "\r\n";
                echo '        <meta property="article:publisher" content="https://www.facebook.com/' . SITE_SOCIAL_FB_PAGE . '" />' . "\r\n";
            }
            if (SITE_SOCIAL_TWITTER) {
                echo '        <meta property="twitter:card" content="summary_large_image" />' . "\r\n";
                echo '        <meta property="twitter:site" content="@" />' . "\r\n";
                echo '        <meta property="twitter:domain" content="' . SITE_URL . '" />' . "\r\n";
                echo '        <meta property="twitter:title" content="' . $Seo->getTitle() . '" />' . "\r\n";
                echo '        <meta property="twitter:description" content="' . $Seo->getDescription() . '" />' . "\r\n";
                echo '        <meta property="twitter:image:src" content="' . $Seo->getImage() . '" />' . "\r\n";
                echo '        <meta property="twitter:url" content="' . SITE_URL . '/' . $getUrl . '" />' . "\r\n";
            } ?>


        <!--[if lt IE 9]>
        <script src="<?= INCLUDE_PATH; ?>/themes/imobi/js/html5shiv.js"></script>
        <![endif]-->
        <!-- FONTES -->
        <link href='https://fonts.googleapis.com/css?family=Anton|Passion+One|PT+Sans+Caption' rel='stylesheet'
              type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Lato:100,300,400,700' rel='stylesheet' type='text/css'>
        <!-- STYLE -->
        <link rel="shortcut icon" href="<?= INCLUDE_PATH; ?>/images/logo-centenario-web.png"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/libraries/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/libraries/owl-carousel/owl.carousel.css"/>
        <!-- Core Owl Carousel CSS File  *  v1.3.3 -->
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/libraries/owl-carousel/owl.theme.css"/>

        <!-- Core Owl Carousel CSS Theme  File  *   v1.3.3 -->
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/libraries/fonts/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/libraries/animate/animate.min.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/libraries/checkbox/minimal.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/libraries/drag-drop/drag-drop.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/components.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/style.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/css/media.css"/>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-122753919-1"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'UA-122753919-1');
        </script>

    </head>

    <body data-offset="200" data-spy="scroll" data-target=".primary-navigation">

    <a id="top"></a>
    <?php
        require(REQUIRE_PATH . '/inc/header.inc.php');

        if (!require($Link->getPatch())) {
            Alert::alert_msg('<i calss="fa fa-exclamation fa-fw"></i>Erro ao incluir o arquivo de navegação!', E_USER_ERROR, TRUE);
        }

        require(REQUIRE_PATH . '/inc/footer.inc.php');
    ?>
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v3.0';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <!-- jQuery Include -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/jquery.min.js"></script>
    <script src="<?= INCLUDE_PATH; ?>/libraries/jquery.easing.min.js"></script>
    <!-- Easing Animation Effect -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/bootstrap/js/bootstrap.min.js"></script>
    <!-- Core Bootstrap v3.2.0 -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/modernizr/modernizr.custom.37829.js"></script>
    <!-- Modernizer -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/jquery.appear.js"></script>
    <!-- It Loads jQuery when element is appears -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/owl-carousel/owl.carousel.min.js"></script>
    <!-- Core Owl Carousel CSS File  *  v1.3.3 -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/checkbox/icheck.min.js"></script>
    <!-- Check box -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/drag-drop/jquery.tmpl.min.js"></script>
    <!-- Drag Drop file -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/drag-drop/drag-drop.js"></script>
    <!-- Drag Drop File -->
    <script src="<?= INCLUDE_PATH; ?>/libraries/drag-drop/modernizr.custom.js"></script>
    <!-- Customized Scripts -->
    <script src="<?= INCLUDE_PATH; ?>/js/functions.js"></script>
    <!--script id="imageTemplate" type="text/x-jquery-tmpl">
            <div class="col-md-3 col-sm-3 col-xs-6">
                <div class="imageholder">
                    <figure>
                        <img src="${filePath}" alt="${fileName}"/>
                    </figure>
                </div>
            </div>
            </script-->
    </body>
    </html>
<?php
    ob_end_flush();
