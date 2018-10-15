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

    $email = new Email();
    $json = NULL;

    /** Recebe os inputs */
    $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    /** Se existir $data e não vir vazio, executa */
    if (empty($data)) {
        die();
    } else {
        $data['recipient_email'] = SITE_EMAIL;
        $data['recipient_name'] = SITE_NAME;
        $data['subject'] .= ' (Formulário de contato)';
        $data['message'] = (isset($data['message']) ? "<p>" . $data['message'] . "</p>" : '');
        $data['message'] .= "<p><b>Mensagem enviada por: <font color=red>" . $data['name'] . "</font></b></p>";
        $data['message'] .= "<p><b>E-mail: <font color=red>" . $data['email'] . "</font></b></p>";
        $data['email'] = strtolower($data['email']);
        $email->Enviar($data);
        if ($email->getResult()) {
            $json = $email->getError();
        } else {
            $json = $email->getError();
        }
    }

    /** Se existir mensagem, exibe */
    if (!empty($json)) {
        echo json_encode($json);
    }