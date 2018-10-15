<?php
    /** Inicia a sessão */
    session_start();
    require_once('../../_app/Config.inc.php');
    $validation = new Validation;
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < 5) {
        $json['alert'] = array('msg' => Alert::ajax_msg($lang['user_permission'], E_USER_ERROR));
        echo json_encode($json);
        die();
    }
    /** Só aceita o acesso caso o form seja enviado através do ajax */
    if (!$validation->is_ajax_required()) {
        die(header('LOCATION: ../index.php'));
    }
    /** Inicia a variável como null */
    $json = NULL;
    /** Recebe os dados do form e armazena na variável */
    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if ($data && $data['action'] && $data['key'] == 'dashboard') {
        /** Compara as ações */
        switch ($data['action']) {
            case 'siteviews':
                $read = new Read();
                $read->FullRead("SELECT count(online_id) AS total from ws_siteviews_online WHERE online_endview >= NOW()");
                $json['online'] = str_pad($read->getResult()[0]['total'], 4, 0, STR_PAD_LEFT);

                $read->ExeRead('ws_siteviews', "WHERE siteviews_date = date(NOW())");
                if ($read->getResult()) {
                    $views = $read->getResult()[0];
                    $stats = number_format($views['siteviews_pages'] / $views['siteviews_views'], 2, '.', '');
                    $json['users'] = str_pad($views['siteviews_users'], 4, 0, STR_PAD_LEFT);
                    $json['views'] = str_pad($views['siteviews_views'], 4, 0, STR_PAD_LEFT);
                    $json['pages'] = str_pad($views['siteviews_pages'], 4, 0, STR_PAD_LEFT);
                    $json['stats'] = $stats . ' Páginas por visita';
                } else {
                    $json['users'] = '0000';
                    $json['views'] = '0000';
                    $json['pages'] = '0000';
                    $json['stats'] = '0.00';
                }
                break;
            default;
                die();
        }
        echo json_encode($json);
    } else {
        die();
    }
