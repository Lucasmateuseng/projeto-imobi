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
    /** Verifica se realmente veio do form de usuário */
    if ($data && $data['action'] && $data['key'] == 'users') {
        switch ($data['action']) {
            case 'create':
                $read = new Read();
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Nome', $data['user_name'])->is_required()
                    ->set('Sobrenome', $data['user_lastname'])->is_required()
                    ->set('E-mail', $data['user_email'])->is_required()->is_email()
                    ->set('Senha', $data['user_password'])->is_required()
                    ->set('Confirmar senha', $data['user_passconfirm'])->is_required()
                    ->set('Nível', $data['user_level'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    echo json_encode($json);
                    return;
                }
                /** Faz a consulta para não deixar conta com email duplicado*/
                $read->FullRead("SELECT user_email FROM ws_users WHERE user_email = :email", "email={$data['user_email']}");
                /** Verifica se obteve resultado */
                if ($read->getResult()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['email_found'], E_USER_WARNING));
                } else {
                    /** Verifica se o campo senha tem menos que 5 caracteres */
                    if (strlen($data['user_password']) < 5) {
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['pass_characters'], E_USER_WARNING));
                        echo json_encode($json);
                        /** Verifica se os campos senha e confirmar senha são identicos */
                    } else if ($data['user_password'] !== $data['user_passconfirm']) {
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['pass_confirm'], E_USER_WARNING));
                        echo json_encode($json);
                        return;
                        /** Se estiver tudo ok em cima, criptografa a senha */
                    } else {
                        $data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);
                    }
                    /** Destroi as variáveis para evitar conflito com banco de dados */
                    unset($data['user_passconfirm'], $data['action'], $data['key'], $data['id']);
                    /** Seta o nome com a primeira letra maiúscula */
                    $data['user_name'] = ucfirst($data['user_name']);
                    /** Seta o sobrenome com a primeira letra maiúscula */
                    $data['user_lastname'] = ucfirst($data['user_lastname']);
                    /** Seta o status, evita que o usuário admin seja inativado */
                    $data['user_status'] = (!empty($data['user_status']) ? 1 : 0);
                    /** Cadastra o usuário */
                    $create = new Create();
                    $create->ExeCreate('ws_users', $data);
                    if ($create->getResult()) {
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['create_user']));
                        $json['redirect'] = array('url' => 'painel.php?exe=users/index', 'timer' => 3500);
                    }
                }
                break;
            case'update':
                $read = new Read();
                $user_id = (int)$data['id'];
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Nome', $data['user_name'])->is_required()
                    ->set('Sobrenome', $data['user_lastname'])->is_required()
                    ->set('E-mail', $data['user_email'])->is_required()->is_email()
                    ->set('Nível', $data['user_level'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    echo json_encode($json);
                    return;
                }
                /** Faz a consulta para não deixar conta com email duplicado*/
                $read->FullRead("SELECT user_id FROM ws_users WHERE user_email = :email AND user_id != :id", "email={$data['user_email']}&id={$user_id}");
                /** Verifica se obteve resultado */
                if ($read->getResult()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['email_found'], E_USER_WARNING));
                } else {
                    /** Se o campo user_password não vir vazio, é para atualizar */
                    if (!empty($data['user_password'])) {
                        /** Verifica se o campo senha tem mais que 5 caracteres */
                        if (strlen($data['user_password']) < 5) {
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['pass_characters'], E_USER_WARNING));
                            echo json_encode($json);
                            /** Verifica se os campos senha e confirmar senha são identicos */
                        } else if ($data['user_password'] !== $data['user_passconfirm']) {
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['pass_confirm'], E_USER_WARNING));
                            echo json_encode($json);
                            return;
                            /** Se estiver tudo ok em cima, criptografa a senha */
                        } else {
                            $data['user_password'] = password_hash($data['user_password'], PASSWORD_DEFAULT);
                        }
                    } else {
                        /** Se vir vazio não é para atualizar a senha, então destroi a variável */
                        unset($data['user_password']);
                    }
                    /** Destroi as variáveis para evitar conflito com banco de dados */
                    unset($data['user_passconfirm'], $data['action'], $data['key'], $data['id']);
                    /** Seta o nome com a primeira letra maiúscula */
                    $data['user_name'] = ucfirst($data['user_name']);
                    /** Seta o sobrenome com a primeira letra maiúscula */
                    $data['user_lastname'] = ucfirst($data['user_lastname']);
                    /** Seta o nível, evita que o usuário admin altere seu nível para menor */
                    $data['user_level'] = ($_SESSION['userlogin']['user_level'] != $data['user_level'] && $_SESSION['userlogin']['user_id'] == $user_id ? $_SESSION['userlogin']['user_level'] : $data['user_level']);
                    /** Seta o status, evita que o usuário admin seja inativado */
                    $data['user_status'] = ($_SESSION['userlogin']['user_id'] == $user_id ? 1 : (!empty($data['user_status']) ? 1 : 0));

                    /** Atualiza os dados */
                    $update = new Update();
                    $update->ExeUpdate('ws_users', $data, "WHERE user_id = :id", "id={$user_id}");
                    if ($update->getResult()) {
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['update_user']));
                        $json['redirect'] = array('url' => 'painel.php?exe=users/index','timer' => 3500);
                    }
                }
                break;
            case 'delete':
                $read = new Read();
                $user_id = (int)$data['id'];
                /** Primeiro faz uma consulta para verificar se o usuário existe */
                $read->ExeRead('ws_users', "WHERE user_id = :id", "id={$user_id}");
                /** Verifica se o resultado não é nulo, caso seja nulo retorna uma mensagem */
                if (!$read->getResult()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['delete_notfound'], E_USER_NOTICE));
                } else {
                    /** Caso o resultado não seja nulo, verifica se não é o usuário logado */
                    $user = $read->getResult()[0];
                    if ($user['user_id'] == $_SESSION['userlogin']['user_id']) {
                        /** Caso seja o usuário logado, retorna uma mensagem */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['delete_account'], E_USER_ERROR));
                    } else {
                        $delete = new Delete();
                        /** Se tudo ok acima, deleta o usuário */
                        $delete->ExeDelete('ws_users', "WHERE user_id = :id", "id={$user['user_id']}");
                        if ($delete->getResult()) {
                            $json['success'] = TRUE;
                        }
                    }
                }
                break;
            default;
                $json['alert'] = array('msg' => Alert::ajax_msg($lang['action_notfound'], E_USER_ERROR));
                $json['redirect'] = array('url' => 'painel.php?exe=users/index', 'timer' => 3000);
        }
        echo json_encode($json);
    } else {
        die();
    }