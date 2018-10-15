<?php
    /** Inicia a sessão */
    session_start();

    require '../../../_app/Config.inc.php';

    $validation = new Validation();

    /** Só aceita o acesso caso o form seja enviado através do ajax */
    if (!$validation->is_ajax_required()) {
        die(header('LOCATION: ../index.php'));
    }
    /** Pausa na execução do script */
    usleep(50000);

    /** Recebe os inputs */
    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    /** Se existir $data e não vir vazio, executa */
    if (empty($data) || empty($data['key'])) {
        die;
    }
    /** Se não existir $_SESSION['filter-form'] então seta como array vazio */
    if (empty($_SESSION['filter-form'])) {
        $_SESSION['filter-form'] = array();
    }

    /** mapea os dados limpando tags e espaços */
    $data = array_map('trim', array_map('strip_tags', $data));
    $read = new Read();
    $json = NULL;

    /** Testa a key */
    switch ($data['key']) {
        case 'transaction':
            $read->FullRead("SELECT realty_type FROM ws_properties WHERE realty_transaction = :tran GROUP BY realty_type ORDER BY realty_type ASC", "tran={$data['transaction']}");
            $_SESSION['filter-form']['realty_transaction'] = $data['transaction'];
            if ($read->getResult()) {
                $json['type'] = NULL;
                foreach ($read->getResult() as $type) {
                    $json['type'] .= '<option value="' . $type['realty_type'] . '">' . realty_type($type['realty_type']) . '</option>';
                }
            }
            break;
        case 'type':
            $read->FullRead("SELECT realty_finality FROM ws_properties WHERE realty_transaction = :tran AND realty_type = :type GROUP BY realty_finality ORDER BY realty_finality ASC", "tran={$data['transaction']}&type={$data['type']}");
            $_SESSION['filter-form']['realty_type'] = $data['type'];
            if ($read->getResult()) {
                $json['finality'] = NULL;
                foreach ($read->getResult() as $finality) {
                    $json['finality'] .= '<option value="' . $finality['realty_finality'] . '">' . realty_finality($finality['realty_finality']) . '</option>';
                }
            }
            break;
        case 'finality':
            $read->FullRead("SELECT realty_district FROM ws_properties WHERE realty_transaction = :tran AND realty_type = :type AND realty_finality = :finality GROUP BY realty_district ORDER BY realty_district ASC", "tran={$data['transaction']}&type={$data['type']}&finality={$data['finality']}");
            $_SESSION['filter-form']['realty_finality'] = $data['finality'];
            if ($read->getResult()) {
                $json['district'] = NULL;
                foreach ($read->getResult() as $district) {
                    $json['district'] .= '<option value="' . $district['realty_district'] . '">' . $district['realty_district'] . '</option>';
                }
            }
            break;
        case 'district':
            $read->FullRead("SELECT realty_bedrooms FROM ws_properties WHERE realty_transaction = :tran AND realty_type = :type AND realty_finality = :finality AND realty_district = :district GROUP BY realty_bedrooms ORDER BY realty_bedrooms ASC", "tran={$data['transaction']}&type={$data['type']}&finality={$data['finality']}&district={$data['district']}");
            $_SESSION['filter-form']['realty_district'] = $data['district'];
            if ($read->getResult()) {
                $json['bedrooms'] = NULL;
                foreach ($read->getResult() as $bedrooms) {
                    $json['bedrooms'] .= '<option value="' . $bedrooms['realty_bedrooms'] . '">' . $bedrooms['realty_bedrooms'] . '</option>';
                }
            }
            break;
        case 'bedrooms':
            $read->FullRead("SELECT realty_price FROM ws_properties WHERE realty_transaction = :tran AND realty_type = :type AND realty_finality = :finality AND realty_district = :district AND realty_bedrooms = :bedrooms ORDER BY realty_price DESC LIMIT 1", "tran={$data['transaction']}&type={$data['type']}&finality={$data['finality']}&district={$data['district']}&bedrooms={$data['bedrooms']}");
            $max_price = (!empty($read->getResult()[0]['realty_price']) ? $read->getResult()[0]['realty_price'] : $data['min_price']);
            $json['min_price'] = NULL;
            $_SESSION['filter-form']['realty_bedrooms'] = $data['bedrooms'];
            for ($min = 100; $min < $max_price; $min = $min * 10) {
                $json['min_price'] .= '<option value="' . $min . '">A partir de R$ ' . number_format($min, '2', ',', '.') . '</option>';
            }
            break;
        case 'min_price':
            $read->FullRead("SELECT realty_price FROM ws_properties WHERE realty_transaction = :tran AND realty_type = :type AND realty_finality = :finality AND realty_district = :district AND realty_bedrooms = :bedrooms AND realty_price >= :price ORDER BY realty_price ASC LIMIT 1", "tran={$data['transaction']}&type={$data['type']}&finality={$data['finality']}&district={$data['district']}&bedrooms={$data['bedrooms']}&price={$data['min_price']}");
            $min_price = (!empty($read->getResult()[0]['realty_price']) ? $read->getResult()[0]['realty_price'] : $data['min_price']);
            $json['max_price'] = NULL;
            $_SESSION['filter-form']['min_price'] = $data['min_price'];
            for ($min = 10000000; $min > $min_price; $min = $min / 10) {
                $json['max_price'] .= '<option value="' . $min . '">Até R$ ' . number_format($min, '2', ',', '.') . '</option>';
            }
            break;
        case 'max_price':
            $_SESSION['filter-form']['max_price'] = $data['max_price'];
            break;
        default:
            //$json['alert'] = '';
            //$json['redirect'] = './';
    }
    if (!empty($json)) {
        echo json_encode($json);
    }
