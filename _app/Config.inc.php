<?php
    // Exibe os erros
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // CONFIGRAÇÕES DO BANCO ####################
    define('HOST', 'localhost');
    define('USER', 'root');
    define('PASS', '123');
    define('DBSA', 'imobi-base');

    define('DS', DIRECTORY_SEPARATOR);

    /** Autoload */
    function AutoLoad($Class)
    {
        $cDir = array('Conn', 'Helpers', 'Library', 'Models');
        $iDir = NULL;
        foreach ($cDir as $dirName) {
            if (!$iDir && file_exists(__DIR__ . DS . $dirName . DS . $Class . '.class.php') && !is_dir(__DIR__ . DS . $dirName . DS . $Class . '.class.php')) {
                require_once(__DIR__ . DS . $dirName . DS . $Class . '.class.php');
                $iDir = TRUE;
            }
        }
    }

    spl_autoload_register("AutoLoad");

    // PHPErro :: personaliza o gatilho do PHP
    function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine)
    {
        $CssClass = ($ErrNo == E_USER_NOTICE ? 'alert-info' : ($ErrNo == E_USER_WARNING ? 'alert-warning' : ($ErrNo == E_USER_ERROR ? 'alert-danger' : $ErrNo)));
        echo "<p class=\"trigger {$CssClass}\">";
        echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
        echo "<small>{$ErrFile}</small>";
        echo "<span class=\"ajax_close\"></span></p>";

        if ($ErrNo == E_USER_ERROR) {
            die();
        }
    }

    set_error_handler('PHPErro');

    /** Carrega as configurações cadastradas no banco de dados e transforma em constantes */
    $custom_define = NULL;
    $read = new Read;
    $read->FullRead("SELECT set_key, set_value FROM ws_settings");
    if ($read->getResult()) {
        foreach ($read->getResult() as $value) {
            define("{$value['set_key']}", "{$value['set_value']}");
        }
        $custom_define = TRUE;
    }
    unset($read, $custom_define, $value);

    define('SITE_IMAGE_DEFAULT', 'site.png?3006');
    define('INCLUDE_PATH', SITE_URL . '/themes/' . SITE_THEME);
    define('REQUIRE_PATH', 'themes' . DS . SITE_THEME);

    /** Carrega as funções */

    /** Carrega as configurações do template site/admin */
    require_once('Config/Config.imobi.php');

    /** Carrega as mensagens de alerta em todo o sistema */
    require_once('Config/Lang.imobi.php');

