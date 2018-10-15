<?php
    /** Inicia a sessão */
    session_start();
    require_once('../../_app/Config.inc.php');
    /** Seta o nível de usuário */
    $level = 10;
    $validation = new Validation;
    /** Evita o acesso caso não esteja logado ou não tenha permissão */
    if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < $level) {
        $json['alert'] = array('msg' => Alert::ajax_msg($lang['user_permission'], E_USER_ERROR));
        echo json_encode($json);
        die();
    }
    /** Só aceita o acesso caso o form seja enviado através do ajax */
    if (!$validation->is_ajax_required()) {
        die(header('LOCATION: ../index.php'));
    }
    /** Pausa na execução do script */
    usleep(50000);
    /** Inicia a variável como null */
    $json = NULL;
    /** Recebe os dados do form e armazena na variável */
    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    /** Verifica se realmente veio do form de configurações */
    if ($data && $data['action'] && $data['key'] == 'settings') {
        switch ($data['action']) {
            case 'update':
                foreach ($data['set_value'] as $id => $value) {
                    $update = new Update();
                    $update->ExeUpdate('ws_settings', array('set_value' => $value), "WHERE set_id = :id", "id={$id}");
                    if ($update->getResult()) {
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['update_settings']));
                        $json['redirect'] = array('url' => 'painel.php?exe=settings/index');
                    } else {
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                    }
                }
                break;
            default;
                $json['alert'] = array('msg' => Alert::ajax_msg($lang['action_notfound'], E_USER_ERROR));
                $json['redirect'] = ['url' => 'painel.php'];
        }
        echo json_encode($json);
    } else {
        die();
    }