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
    /** Verifica se realmente veio do form de imóveis */
    if ($data && $data['action'] && $data['key'] == 'properties') {
        switch ($data['action']) {
            case 'update':
                $read = new Read();
                $create = new Create();
                $upload = new Upload('../../uploads/');
                $realty_id = (int)$data['id'];
                unset($data['id'], $data['realty_cover'], $data['images'], $data['key'], $data['action']);
                /** Valida os campos obrigatórios */
                $validation
                    ->set('Título', $data['realty_title'])->is_required()->is_string()
                    ->set('Referência', $data['realty_ref'])->is_required()->is_string()
                    ->set('Descrição', $data['realty_description'])->is_required()->is_string()
                    ->set('Transação', $data['realty_transaction'])->is_required()->is_num()
                    ->set('Tipo de imóvel', $data['realty_type'])->is_required()->is_num()
                    ->set('Finalidade', $data['realty_finality'])->is_required()->is_num()
                    ->set('Cidade', $data['realty_city'])->is_required()->is_string()
                    ->set('Bairro', $data['realty_district'])->is_required()->is_string();
                /** Se não passar na validação, exibe uma mensagem de alerta e trava a execução seguinte */
                if (!$validation->validate()) {
                    $json['alert'] = array('msg' => Alert::ajax_msg($lang['empty_input'], E_USER_WARNING));
                    /** Se passar na validação, continua */
                } else {
                    /** Thumb é obrigatório, então faz uma consulta para verificar se já existe um cadastrado */
                    $read->FullRead("SELECT realty_cover FROM ws_properties WHERE realty_id = :id", "id={$realty_id}");
                    /** Armazena os dados na variável */
                    $cover = $read->getResult();
                    /** Verifica se o campo veio vazio e se o resultado da consulta é false */
                    if (empty($_FILES['realty_cover']) && (!$cover || !$cover[0]['realty_cover'])) {
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['error_image'], E_USER_WARNING));
                    } else {
                        /** Seta o link da pagina */
                        $data['realty_name'] = Check::Name($data['realty_title']);
                        /** Verifica e seta o valor */
                        $data['realty_price'] = ($data['realty_price'] ? str_replace(array('.', ','), array('', '.'), $data['realty_price']) : NULL);
                        /** Seta o nome da cidade e passa a primeira letra para maiúscula */
                        $data['realty_city'] = ucwords(mb_strtolower($data['realty_city']));
                        /** Seta o nome do bairro e passa a primeira letra para maiúscula */
                        $data['realty_district'] = ucwords(mb_strtolower($data['realty_district']));
                        /** Seta o status */
                        $data['realty_status'] = (!empty($data['realty_status']) ? 1 : 0);
                        /** Seta o destaque */
                        $data['realty_featured'] = (!empty($data['realty_featured']) ? 1 : 0);
                        /** Seta a data */
                        $data['realty_date'] = (!empty($data['realty_date']) ? Check::Data($data['realty_date']) : date('Y-m-d H:i:s'));
                        /** Faz a consulta para verificar se ja existe um imóvel com o mesmo name */
                        $read->FullRead("SELECT realty_name FROM ws_properties WHERE realty_name = :realty_name AND realty_id != :id", "realty_name={$data['realty_name']}&id={$realty_id}");
                        /** Se o resultado for true, adiciona o id para não permitir dois items com mesmo name */
                        if ($read->getResult()) {
                            $data['realty_name'] = $data['realty_name'] . '-' . $realty_id;
                        }
                        /** Verifica se o campo thumb veio com imagem */
                        if (!empty($_FILES['realty_cover'])) {
                            /** Caso sim, vamos atualizar e primeiro removemos a imagem antiga so servidor */
                            if ($cover && !empty($cover[0]['realty_cover']) && Check::file_exists($cover[0]['realty_cover'])) {
                                unlink('../../uploads/' . $cover[0]['realty_cover']);
                            }
                            /** Faz upload do novo thumb e gera um novo nome */
                            $upload->Image($_FILES['realty_cover'], Check::Name($data['realty_title'] . '-' . time()), IMAGE_W, 'properties');
                            /** Armazena o nome do thumb na variável */
                            $data['realty_cover'] = $upload->getResult();
                            /** Destroi as variáveis */
                            unset($_FILES['realty_cover'], $cover);
                        }
                        /** Verifica se existe imagens adicionais */
                        if (!empty($_FILES['images'])) {
                            $file_array = array();
                            $file_count = count($_FILES['images']['name']);
                            $file_key = array_keys($_FILES['images']);
                            for ($i = 0; $i < $file_count; $i++) {
                                foreach ($file_key as $value) {
                                    $file_array[$i][$value] = $_FILES['images'][$value][$i];
                                }
                            }
                            $json['images'] = NULL;
                            foreach ($file_array as $value) {
                                $upload->Image($value, $data['realty_name'] . $realty_id . '-' . mt_rand() . '-' . base64_encode(time()), IMAGE_W, 'properties');
                                if ($upload->getResult()) {
                                    $create->ExeCreate('ws_properties_image', array('realty_id' => $realty_id, "image" => $upload->getResult()));
                                    $json['images'] .= '<img id="' . $create->getResult() . '" src="../uploads/' . $upload->getResult() . ' " width="96" height="96" />';
                                }
                            }
                        }
                        $update = new Update();
                        /** Faz o update no banco de dados */
                        $update->ExeUpdate('ws_properties', $data, "WHERE realty_id = :id", "id={$realty_id}");
                        if ($update->getResult()) {
                            /** Caso atualize com sucesso, exibe uma mensagem de sucesso */
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['update_success']));
                            $json['redirect'] = array('url' => 'painel.php?exe=properties/index', 'timer' => 3000);
                        } else {
                            /** Caso não, exibe uma mensagem de erro */
                            $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                            $json['redirect'] = array('url' => 'painel.php?exe=properties/index', 'timer' => 3000);
                        }
                    }
                }
                break;
            case 'delete':
                $realty_id = $data['id'];
                $read = new Read();
                /** Faz a consulta para verificar se existe e o caminho do thumb */
                $read->FullRead("SELECT realty_cover FROM ws_properties WHERE realty_id = :id", "id={$realty_id}");
                /** Armazena o resultado na variável */
                $cover = $read->getResult();
                /** Verifica se o resultado não é nulo, se não for nulo faz uma busca para ver se existe o arquivo dentro do servidor */
                if ($cover && !empty($cover[0]['realty_cover']) && Check::file_exists($cover[0]['realty_cover'])) {
                    /** Se existir o arquivo, delete */
                    unlink('../../uploads/' . $read->getResult()[0]['realty_cover']);
                }
                /** Faz a consulta para verificar se existe imagens vinculadas ao id do imóvel */
                $read->ExeRead('ws_properties_image', "WHERE realty_id = :id", "id={$realty_id}");
                /** Verifica se o resultado não é nulo */
                if ($read->getResult()) {
                    /** caso não, faz uma varredura testando e deletando os arquivos encontrados */
                    foreach ($read->getResult() as $value) {
                        if (Check::file_exists($value['image'])) {
                            unlink('../../uploads/' . $value['image']);
                        }
                    }
                }
                $delete = new Delete();
                /** Deleta do banco de dados o imóvel */
                $delete->ExeDelete('ws_properties', "WHERE realty_id = :id", "id={$realty_id}");
                /** Deleta do banco de dados as imagens vinculadas ao id do imóvel */
                $delete->ExeDelete('ws_properties_image', "WHERE realty_id = :id", "id={$realty_id}");
                $json['alert'] = array('success');
                break;
            case 'delete-image':
                $read = new Read();
                /** Faz uma consulta e recebe o caminho e o nome do arquivo para deletar */
                $read->FullRead("SELECT image FROM ws_properties_image WHERE id = :id", "id={$data['id']}");
                /** Verifica se o resultado não é nulo */
                if ($read->getResult()) {
                    /** Se não for nulo, faz uma pesquisa para ver se existe o arquivo no banco de dados */
                    if (Check::file_exists($read->getResult()[0]['image'])) {
                        /** Se existir, deleta */
                        unlink('../../uploads/' . $read->getResult()[0]['image']);
                    }
                    $delete = new Delete();
                    /** Executa o comando para deletar */
                    $delete->ExeDelete('ws_properties_image', "WHERE id = :id", "id={$data['id']}");
                    /** Verifica se deletou com sucesso */
                    if ($delete->getResult()) {
                        /** Caso sim, retorna sucesso e remove da tela */
                        $json['alert'] = array('success');
                    } else {
                        /** Caso não, retorna uma mensagem */
                        $json['alert'] = array('msg' => Alert::ajax_msg($lang['execution_fail'], E_USER_ERROR));
                    }
                }
                break;
            default:
                $json['alert'] = array('msg' => Alert::ajax_msg($lang['action_notfound'], E_USER_ERROR));
                $json['redirect'] = array('url' => 'painel.php?exe=properties/index', 'timer' => 3000);
                break;
        }
        echo json_encode($json);
        exit();
    } else {
        die();
    }