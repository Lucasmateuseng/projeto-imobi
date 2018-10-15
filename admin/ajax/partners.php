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
    /** Pausa na execução do script */
    usleep(50000);
    /** Inicia a variável como null */
    $json = NULL;
    /** Recebe os dados do form e armazena na variável */
    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    /** Verifica se realmente veio do form de páginas */
    if ($data && $data['action'] && $data['key'] == 'partners') {
        switch ($data['action']) {
            case 'create':
                /** Recebe os dados da imagem e armazena na variável */
                $partner_logo = (!empty($_FILES['partner_logo']) ? $_FILES['partner_logo'] : NULL);
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Título', $data['partner_title'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    /** Verifica se existe imagem */
                } else if (empty($partner_logo)) {
                    /** Caso não exista, exibi uma mensagem de alerta */
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['select_image'], E_USER_ERROR));
                    /** Se passar na validação, continua */
                } else {
                    /** Seta o status como ativo */
                    $data['partner_status'] = (!empty($data['partner_status']) ? 1 : 0);
                    /** Inicia o processo de upload */
                    $upload = new Upload('../../uploads/');
                    /** Faz o upload e renomeia a imagem, assim evita conflito com nomes */
                    $upload->Image($partner_logo, Check::Name($data['partner_title'] . time()), PARTNER_LOGO_W, 'partners');
                    if ($upload->getResult()) {
                        /** Armazena o novo nome com caminho na variável para salvar no banco de dados */
                        $data['partner_logo'] = $upload->getResult();
                    } else {
                        /** Caso exista um erro no processo, exibe uma mensagem */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['select_image'], E_USER_WARNING));
                        echo json_encode($json);
                        return;
                    }
                    /** Remove os campos para evitar conflitos com banco de dados */
                    unset($data['key'], $data['action'], $data['id']);
                    /** Atualiza */
                    $create = new Create();
                    $create->ExeCreate('ws_partners', $data);
                    /** Faz uma verificação par saber se atualizou */
                    if ($create->getResult()) {
                        /** Caso atualize com sucesso, exibe uma mensagem de sucesso */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['create_partner']));
                        $json['redirect'] = array('url' => 'painel.php?exe=partners/index', 'timer' => 3500);
                    } else {
                        /** Caso não, exibe uma mensagem de erro */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                        $json['redirect'] = array('url' => 'painel.php?exe=partners/index', 'timer' => 3000);
                    }
                }
                break;
            case 'update':
                $read = new Read();
                $partner_id = (int)$data['id'];
                /** Remove os campos para evitar conflitos com banco de dados */
                unset($data['key'], $data['action'], $data['id'], $data['partner_logo']);
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Título', $data['partner_title'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    /** Se passar na validação, continua */
                } else {
                    /** Recebe os dados da imagem e armazena na variável */
                    $partner_logo = (!empty($_FILES['partner_logo']) ? $_FILES['partner_logo'] : NULL);
                    /** Faz uma consulta para verificar se existe logo cadastrada */
                    $read->FullRead("SELECT partner_logo FROM ws_partners WHERE partner_id = :id", "id={$partner_id}");
                    /** Verifica se o campo logo veio nulo, se existe logo cadastrado no banco com o id do patrocinador */
                    if (empty($partner_logo) && (!$read->getResult() || !$read->getResult()[0]['partner_logo'])) {
                        /** Se o resultado for false, exibe uma mensagem de alerta */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['select_image'], E_USER_WARNING));
                        /** Se não, continua */
                    } else {
                        /** Seta o status */
                        $data['partner_status'] = (!empty($data['partner_status']) ? 1 : 0);
                        /** Se o campo logo não veio nulo, é para atualizar */
                        if (!empty($partner_logo)) {
                            /** Se for atualizar o logo verifica se existe um cadastrado e deleta do servidor */
                            if ($read->getResult() && !empty($read->getResult()[0]['partner_logo']) && Check::file_exists($read->getResult()[0]['partner_logo'])) {
                                unlink('../../uploads/' . $read->getResult()[0]['partner_logo']);
                            }
                            /** Inicia o processo de upload */
                            $upload = new Upload('../../uploads/');
                            /** Faz o upload e renomeia a imagem, assim evita conflito com nomes */
                            $upload->Image($partner_logo, Check::Name($data['partner_title'] . time()), PARTNER_LOGO_W, 'partners');
                            if ($upload->getResult()) {
                                /** Armazena o novo nome com caminho na variável para salvar no banco de dados */
                                $data['partner_logo'] = $upload->getResult();
                            } else {
                                /** Caso exista um erro no processo, exibe uma mensagem */
                                $json['alert'] = array('msg' => Alert::ajax_msg($lang['select_image'], E_USER_WARNING));
                                echo json_encode($json);
                                return;
                            }
                        }
                        /** Atualiza */
                        $update = new Update();
                        $update->ExeUpdate('ws_partners', $data, "WHERE partner_id = :id", "id={$partner_id}");
                        /** Faz uma verificação par saber se atualizou */
                        if ($update->getResult()) {
                            /** Caso atualize com sucesso, exibe uma mensagem de sucesso */
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['update_partner']));
                            $json['redirect'] = array('url' => 'painel.php?exe=partners/index', 'timer' => 3500);
                        } else {
                            /** Caso não, exibe uma mensagem de erro */
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                            $json['redirect'] = array('url' => 'painel.php?exe=partners/index', 'timer' => 3000);
                        }
                    }
                }
                break;
            case 'delete':
                $read = new Read();
                /** Recebe o id */
                $partner_id = (int)$data['id'];
                /** Primeiro faz a consulta para verificar se existe uma imagem cadastrada */
                $read->FullRead("SELECT partner_logo FROM ws_partners WHERE partner_id = :id", "id={$partner_id}");
                /** Armazena o resultado na variável */
                $image = $read->getResult();
                /** Verifica se o resultado não é nulo, caso exista resultado, faz uma pesquisa da imagem no servidor */
                if ($image && Check::file_exists($image[0]['partner_logo'])) {
                    /** Se encontrar a imagem no servidor, deleta */
                    unlink('../../uploads/' . $image[0]['partner_logo']);
                    /** Por garantia, estou destuindo a variavel */
                    unset($image);
                }
                /** Deleta do banco os dados vinculados ao id do patrocinador*/
                $delete = new Delete;
                $delete->ExeDelete('ws_partners', "WHERE partner_id = :id", "id={$partner_id}");
                $json['alert'] = array('success');
                break;
            default;
                $json['alert'] = array('msg' => Alert::ajax_msg($lang['action_notfound'], E_USER_ERROR));
                $json['redirect'] = array('url' => 'painel.php?exe=partners/index', 'timer' => 3000);
        }
        echo json_encode($json);
    } else {
        die();
    }