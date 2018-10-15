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
    if ($data && $data['action'] && $data['key'] == 'slides') {
        switch ($data['action']) {
            case 'create':
                /** Recebe os dados da imagem e armazena na variável */
                $slide_image = (!empty($_FILES['slide_image']) ? $_FILES['slide_image'] : NULL);
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Título', $data['slide_title'])->is_required()
                    ->set('Descrição', $data['slide_description'])->is_required()
                    ->set('Data inicial', $data['slide_start'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    /** Verifica se existe imagem */
                } else if (empty($slide_image)) {
                    /** Caso não exista, exibi uma mensagem de alerta */
                    $json['alert'] = array('msg' => Alert::ajax_msg('<i class="fa fa-exclamation fa-fw"></i> <b>Atenção:</b> Por favor, envie uma imagem de ' . SLIDE_W . ' por ' . SLIDE_H . 'px JPG ou PNG!', E_USER_WARNING));
                    /** Se passar na validação, continua */
                } else {
                    /** Seta a data da criação */
                    $data['slide_date'] = date('Y-m-d H:i:s');
                    /** Verifica e seta o valor */
                    $data['slide_price'] = ($data['slide_price'] ? str_replace(array('.', ','), array('', '.'), $data['slide_price']) : NULL);
                    /** Converte a data de inicio */
                    $data['slide_start'] = Check::Data($data['slide_start']);
                    /** Verifica e converte a data final */
                    $data['slide_end'] = (!empty($data['slide_end']) ? Check::Data($slide_end) : NULL);
                    /** Seta o status como ativo */
                    $data['slide_status'] = 1;
                    /** Inicia o processo de upload */
                    $upload = new Upload('../../uploads/');
                    /** Faz o upload e renomeia a imagem, assim evita conflito com nomes */
                    $upload->Image($slide_image, Check::Name($data['slide_title'] . time()), SLIDE_W, 'slides');
                    if ($upload->getResult()) {
                        /** Armazena o novo nome com caminho na variável para salvar no banco de dados */
                        $data['slide_image'] = $upload->getResult();
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
                    $create->ExeCreate('ws_slides', $data);
                    /** Faz uma verificação par saber se atualizou */
                    if ($create->getResult()) {
                        /** Caso atualize com sucesso, exibe uma mensagem de sucesso */
                        $json['alert'] = array('msg' => Alert::ajax_msg('<i class="fa fa-check fa-fw"></i> <b>Tudo certo.</b> O slide foi criado com sucesso. E sera exibido nas datas cadastradas!'));
                        $json['redirect'] = array('url' => 'painel.php?exe=slides/index');
                    } else {
                        /** Caso não, exibe uma mensagem de erro */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                        $json['redirect'] = array('url' => 'painel.php?exe=slides/index', 'timer' => 3000);
                    }
                }
                break;
            case 'update':
                $read = new Read();
                $slide_id = (int)$data['id'];
                /** Remove os campos para evitar conflitos com banco de dados */
                unset($data['key'], $data['action'], $data['id'], $data['slide_image']);
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Título', $data['slide_title'])->is_required()
                    ->set('Descrição', $data['slide_description'])->is_required()
                    ->set('Data inicial', $data['slide_start'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    /** Se passar na validação, continua */
                } else {
                    /** Recebe os dados da imagem e armazena na variável */
                    $slide_image = (!empty($_FILES['slide_image']) ? $_FILES['slide_image'] : NULL);
                    /** Faz uma consulta para verificar se existe imagem cadastrada */
                    $read->FullRead("SELECT slide_image FROM ws_slides WHERE slide_id = :id", "id={$slide_id}");
                    /** Verifica se o campo imagem veio nulo, se existe imagem cadastrada no banco com o id do slide */
                    if (empty($slide_image) && (!$read->getResult() || !$read->getResult()[0]['slide_image'])) {
                        /** Se o resultado for false, exibe uma mensagem de alerta */
                        $json['alert'] = array('msg' => Alert::ajax_msg('<i class="fa fa-exclamation fa-fw"></i> <b>Atenção: </b> Por favor, envie uma imagem de ' . SLIDE_W . 'px por ' . SLIDE_H . 'px! JPG ou PNG', E_USER_WARNING));
                        /** Se não, continua */
                    } else {
                        /** Seta a data de atualização */
                        $data['slide_date'] = date('Y-m-d H:i:s');
                        /** Verifica e seta o valor */
                        $data['slide_price'] = ($data['slide_price'] ? str_replace(array('.', ','), array('', '.'), $data['slide_price']) : NULL);
                        /** Seta a data de inicio do slide */
                        $data['slide_start'] = Check::Data($data['slide_start']);
                        /** Seta a data final do slide */
                        $data['slide_end'] = (!empty($data['slide_end']) ? Check::Data($data['slide_end']) : NULL);
                        /** Se o campo de imagem não veio nulo, é para atualizar */
                        if (!empty($slide_image)) {
                            /** Se for atualizar a imagem, verifica se existe uma cadastrada e deleta do servidor */
                            if ($read->getResult() && !empty($read->getResult()[0]['slide_image']) && Check::file_exists($read->getResult()[0]['slide_image'])) {
                                unlink('../../uploads/' . $read->getResult()[0]['slide_image']);
                            }
                            /** Inicia o processo de upload */
                            $upload = new Upload('../../uploads/');
                            /** Faz o upload e renomeia a imagem, assim evita conflito com nomes */
                            $upload->Image($slide_image, Check::Name($data['slide_title'] . time()), SLIDE_W, 'slides');
                            if ($upload->getResult()) {
                                /** Armazena o novo nome com caminho na variável para salvar no banco de dados */
                                $data['slide_image'] = $upload->getResult();
                            } else {
                                /** Caso exista um erro no processo, exibe uma mensagem */
                                $json['alert'] = array('msg' => Alert::ajax_msg($lang['select_image'], E_USER_WARNING));
                                echo json_encode($json);
                                return;
                            }
                        }
                        /** Atualiza */
                        $update = new Update();
                        $update->ExeUpdate('ws_slides', $data, "WHERE slide_id = :id", "id={$slide_id}");
                        /** Faz uma verificação par saber se atualizou */
                        if ($update->getResult()) {
                            /** Caso atualize com sucesso, exibe uma mensagem de sucesso */
                            $json['alert'] = array('msg' => Alert::ajax_msg('<i class="fa fa-check fa-fw"></i> <b>Tudo certo.</b> O slide foi atualizado com sucesso. E sera exibido nas datas cadastradas!'));
                            $json['redirect'] = array('url' => 'painel.php?exe=properties/index');
                        } else {
                            /** Caso não, exibe uma mensagem de erro */
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                            $json['redirect'] = array('url' => 'painel.php?exe=slides/index', 'timer' => 3000);
                        }
                    }
                }
                break;
            case
            'delete':
                $read = new Read();
                /** Recebe o id */
                $slide_id = (int)$data['id'];
                /** Primeiro faz a consulta para verificar se existe uma imagem cadastrada */
                $read->FullRead("SELECT slide_image FROM ws_slides WHERE slide_id = :id", "id={$slide_id}");
                /** Armazena o resultado na variável */
                $image = $read->getResult();
                /** Verifica se o resultado não é nulo, caso exista resultado, faz uma pesquisa da imagem no servidor */
                if ($image && Check::file_exists($image[0]['slide_image'])) {
                    /** Se encontrar a imagem no servidor, deleta */
                    unlink('../../uploads/' . $image[0]['slide_image']);
                    /** Por garantia, estou destuindo a variavel */
                    unset($image);
                }
                /** Deleta do banco os dados vinculados ao id do slide*/
                $delete = new Delete;
                $delete->ExeDelete('ws_slides', "WHERE slide_id = :id", "id={$slide_id}");
                $json['alert'] = array('success');
                break;
            default;
                $json['alert'] = array('msg' => Alert::ajax_msg($lang['action_notfound'], E_USER_ERROR));
                $json['redirect'] = array('url' => 'painel.php?exe=slides/index', 'timer' => 3000);
        }
        echo json_encode($json);
    } else {
        die();
    }