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
    if ($data && $data['action'] && $data['key'] == 'pages') {
        switch ($data['action']) {
            case 'create':
                $read = new Read();
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Título', $data['page_title'])->is_required()
                    ->set('Descrição', $data['page_subtitle'])->is_required()
                    ->set('Conteúdo', $data['page_content'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    /** Se passar na validação, continua */
                } else {
                    /** Verifica e seta o link da página */
                    $data['page_name'] = Check::Name($data['page_title']);
                    /** Faz uma consulta para verificar se já existe uma página com o mesmo url */
                    $read->FullRead("SELECT page_name FROM ws_pages WHERE page_name = :page_name", "page_name={$data['page_name']}");
                    /** Armazena a consulta na variável */
                    $page_name = $read->getResult();
                    /** Verifica se obteve resultado */
                    if ($page_name) {
                        /** Caso sim, seta um novo page_name usando um id rand no final para diferenciar */
                        $data['page_name'] = $data['page_name'] . '-' . mt_rand(0, 999);
                        /** Destroi a variável */
                        unset($page_name);
                    }
                    /** Verifica se o input cover não veio vazio, necessário se for enviar uma imagens destacada */
                    if (!empty($_FILES['page_cover'])) {
                        /** Armazena os dados do arquivo na variável */
                        $file = $_FILES['page_cover'];
                        /** Inicia o processo de upload */
                        $upload = new Upload('../../uploads/');
                        /** Faz o upload e renomeia a imagem, assim evita conflito com nomes */
                        $upload->Image($file, $data['page_name'] . '-' . time(), IMAGE_W, 'pages');
                        if ($upload->getResult()) {
                            /** Armazena o novo nome com caminho na variável para salvar no banco de dados */
                            $data['page_cover'] = $upload->getResult();
                        } else {
                            /** Caso exista um erro no processo, exibe uma mensagem */
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['select_image'], E_USER_WARNING));
                            echo json_encode($json);
                            return;
                        }
                    }
                    /** faz uma varredura no array antes de enviar pro banco de dados */
                    $data = array_filter($data);
                    /** Verifica e seta o status da pagina */
                    $data['page_status'] = (!empty($data['page_status']) ? 1 : 0);
                    /** Seta a data */
                    $data['page_date'] = (!empty($data['page_date']) ? Check::Data($data['page_date']) : date('Y-m-d H:i:s'));
                    /** Remove os campos para evitar conflitos com banco de dados */
                    unset($data['key'], $data['action']);
                    /** Atualiza */
                    $create = new Create();
                    $create->ExeCreate('ws_pages', $data);
                    /** Faz uma verificação par saber se atualizou */
                    if ($create->getResult()) {
                        /** Caso atualize com sucesso, exibe uma mensagem de sucesso */
                        $json['alert'] = array('msg' => Alert::ajax_msg('<i class="fa fa-check fa-fw"></i> <b>Tudo certo.</b> A página <b>' . $data['page_title'] . '</b> foi criada com sucesso!'));
                        $json['redirect'] = array('url' => 'painel.php?exe=pages/index', 'timer' => 3500);
                    } else {
                        /** Caso não, exibe uma mensagem de erro */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                        $json['redirect'] = array('url' => 'painel.php?exe=pages/index', 'timer' => 3000);
                    }
                }
                break;
            case'update':
                $read = new Read();
                $page_id = (int)$data['id'];
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Título', $data['page_title'])->is_required()
                    ->set('Descrição', $data['page_subtitle'])->is_required()
                    ->set('Conteúdo', $data['page_content'])->is_required();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    /** Se passar na validação, continua */
                } else {
                    /** Verifica e seta o link da pagina */
                    $data['page_name'] = (!empty($data['page_name']) ? Check::Name($data['page_name']) : Check::Name($data['page_title']));
                    /** Faz uma consulta para verificar se já existe uma página com o mesmo url */
                    $read->FullRead("SELECT page_name FROM ws_pages WHERE page_name = :page_name AND page_id != :id", "page_name={$data['page_name']}&id={$page_id}");
                    /** Armazena a consulta na variável */
                    $page_name = $read->getResult();
                    /** Verifica se obteve resultado */
                    if ($page_name) {
                        /** Caso sim, seta um novo page_name usando o id no final para diferenciar */
                        $data['page_name'] = $data['page_name'] . '-' . $page_id;
                        /** Destroi a variável */
                        unset($page_name);
                    }
                    /** Verifica se o input cover não veio vazio, necessario se for atualizar o cover */
                    if (!empty($_FILES['page_cover'])) {
                        /** Armazena os dados do arquivo na variável */
                        $file = $_FILES['page_cover'];
                        /** Faz a consulta no banco de dados para verificar se existe cover cadastrada */
                        $read->FullRead('SELECT page_cover FROM ws_pages WHERE page_id = :id', "id={$page_id}");
                        /** Armazena o resultado na variavel */
                        $page_cover = $read->getResult();
                        /** Verifica se o resultado não é nulo, caso exista resultado, faz uma pesquisa da imagem no servidor */
                        if ($page_cover[0]['page_cover'] && Check::file_exists($page_cover[0]['page_cover'])) {
                            /** Se existir a imagem, deleta para fazer um novo upload */
                            unlink('../../uploads/' . $page_cover[0]['page_cover']);
                            unset($page_cover);
                        }
                        /** Inicia o processo de upload */
                        $upload = new Upload('../../uploads/');
                        /** Faz o upload e renomeia a imagem, assim evita conflito com nomes */
                        $upload->Image($file, $data['page_name'] . '-' . time(), IMAGE_W, 'pages');
                        if ($upload->getResult()) {
                            $data['page_cover'] = $upload->getResult();
                        } else {
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['select_image'], E_USER_WARNING));
                            echo json_encode($json);
                            return;
                        }
                    }
                    /** faz uma varredura no array antes de enviar pro banco de dados */
                    $data = array_filter($data);
                    /** Verifica e seta o status da pagina */
                    $data['page_status'] = (!empty($data['page_status']) ? 1 : 0);
                    /** Seta a data */
                    $data['page_date'] = (!empty($data['page_date']) ? Check::Data($data['page_date']) : date('Y-m-d H:i:s'));
                    /** Remove os campos para evitar conflitos com banco de dados */
                    unset($data['key'], $data['action'], $data['id']);
                    /** Atualiza */
                    $update = new Update();
                    $update->ExeUpdate('ws_pages', $data, "WHERE page_id = :id", "id={$page_id}");
                    /** Faz uma verificação par saber se atualizou */
                    if ($update->getResult()) {
                        /** Caso atualize com sucesso, exibe uma mensagem de sucesso */
                        $json['alert'] = array('msg' => Alert::ajax_msg('<i class="fa fa-check fa-fw"></i> <b>Tudo certo.</b> A página <b>' . $data['page_title'] . '</b> foi atualizado com sucesso!'));
                        $json['redirect'] = array('url' => 'painel.php?exe=pages/index', 'timer' => 3500);
                    } else {
                        /** Caso não, exibe uma mensagem de erro */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                        $json['redirect'] = array('url' => 'painel.php?exe=pages/index', 'timer' => 3000);
                    }
                }
                break;
            case
            'delete':
                $read = new Read();
                /** Recebe o id */
                $page_id = (int)$data['id'];
                /** Primeiro faz a consulta para verificar se existe uma imagem destacada cadastrada */
                $read->FullRead("SELECT page_cover FROM ws_pages WHERE page_id = :id", "id={$page_id}");
                /** Armazena o resultado na variável */
                $cover = $read->getResult();
                /** Verifica se o resultado não é nulo, caso exista resultado, faz uma pesquisa da imagem no servidor */
                if ($cover && Check::file_exists($cover[0]['page_cover'])) {
                    /** Se encontrar a imagem no servidor, deleta */
                    unlink('../../uploads/' . $cover[0]['page_cover']);
                    /** Por garantia, estou destuindo a variavel */
                    unset($cover);
                }
                /** Agora verifica se existe imagens vinculadas ao id desta pagina */
                $read->FullRead("SELECT image FROM ws_pages_images WHERE page_id = :id", "id={$page_id}");
                /** Armazena o resultado na variavel */
                $image = $read->getResult();
                /** Verifica se o resultado não é nulo */
                if ($image) {
                    /** Faz uma pesquisa com o resultado no servidor */
                    foreach ($image as $page_image) {
                        /** Verifica se existe a imagem no servidor */
                        if (Check::file_exists($page_image['image'])) {
                            /** Caso sim, deleta */
                            unlink('../../uploads/' . $page_image['image']);
                            unset($image);
                        }
                    }
                }
                /** Deleta do banco os dados vinculados ao id da página */
                $delete = new Delete;
                $delete->ExeDelete("ws_pages_images", "WHERE page_id = :id", "id={$page_id}");
                $delete->ExeDelete('ws_pages', "WHERE page_id = :id", "id={$page_id}");
                $json['alert'] = array('success');
                break;
            default;
                $json['alert'] = array('msg' => Alert::ajax_msg($lang['action_notfound'], E_USER_ERROR));
                $json['redirect'] = array('url' => 'painel.php?exe=pages/index', 'timer' => 3000);
        }
        echo json_encode($json);
    } else {
        die();
    }