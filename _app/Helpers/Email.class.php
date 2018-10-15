<?php

    require(__DIR__ . '/../Library/PHPMailer/class.phpmailer.php');

    /**
     * Email [ MODEL ]
     * Modelo responável por configurar a PHPMailer, validar os dados e disparar e-mails do sistema!
     *
     * @copyright (c) year, Robson V. Leite UPINSIDE TECNOLOGIA
     */
    class Email
    {

        /** @var PHPMailer */
        private $Mail = stdClass::class;

        /** EMAIL DATA */
        private $Data;

        /** CORPO DO E-MAIL */
        private $Assunto;
        private $Mensagem;

        /** REMETENTE */
        private $RemetenteNome;
        private $RemetenteEmail;

        /** DESTINO */
        private $DestinoNome;
        private $DestinoEmail;

        /** CONSTROLE */
        private $Error;
        private $Result;

        function __construct()
        {
            $this->Mail = new PHPMailer();
            $this->Mail->Host = MAIL_HOST;
            $this->Mail->Port = MAIL_PORT;
            $this->Mail->Username = MAIL_USER;
            $this->Mail->Password = MAIL_PASS;
            $this->Mail->SMTPSecure = MAIL_SECURE;
            $this->Mail->CharSet = 'UTF-8';
            $this->Mail->SMTPDebug = 0;
        }

        /**
         * <b>Enviar E-mail SMTP:</b> Envelope os dados do e-mail em um array atribuitivo para povoar o método.
         * Com isso execute este para ter toda a validação de envio do e-mail feita automaticamente.
         *
         * <b>REQUER DADOS ESPECÍFICOS:</b> Para enviar o e-mail você deve montar um array atribuitivo com os
         * seguintes índices corretamente povoados:<br><br>
         * <i>
         * &raquo; Assunto<br>
         * &raquo; Mensagem<br>
         * &raquo; RemetenteNome<br>
         * &raquo; RemetenteEmail<br>
         * &raquo; DestinoNome<br>
         * &raquo; DestinoEmail
         * </i>
         */
        public function Enviar(array $Data)
        {
            $this->Data = $Data;
            $this->Clear();

            if (in_array('', $this->Data)) {
                $this->Error = array(
                    "type" => "error",
                    "msg" => Alert::alert_msg('<i class="fa fa-exclamation fa-fw"></i> <b>Atenção:</b> Para enviar sua mensagem, preencha todos os campos', E_USER_NOTICE)
                );
                $this->Result = FALSE;
            } elseif (!Check::Email($this->Data['email'])) {
                $this->Error = array(
                    "type" => "error",
                    "msg" => Alert::alert_msg('<i class="fa fa-exclamation fa-fw"></i> <b>Atenção:</b> E-mail informado não tem um formato valido, informe seu e-mail', E_USER_NOTICE)
                );
                $this->Result = FALSE;
            } else {
                $this->setMail();
                $this->Config();
                $this->sendMail();
            };
        }

        /**
         * <b>Verificar Envio:</b> Executando um getResult é possível verificar se foi ou não efetuado
         * o envio do e-mail. Para mensagens execute o getError();
         * @return BOOL $Result = TRUE or FALSE
         */
        public function getResult()
        {
            return $this->Result;
        }

        /**
         * <b>Obter Erro:</b> Retorna um array associativo com o erro e o tipo de erro.
         * @return ARRAY $Error = Array associatico com o erro
         */
        public function getError()
        {
            return $this->Error;
        }

        /*
         * ***************************************
         * **********  PRIVATE METHODS  **********
         * ***************************************
         */

        //Limpa código e espaços!
        private function Clear()
        {
            array_map('strip_tags', $this->Data);
            array_map('trim', $this->Data);
        }

        //Recupera e separa os atributos pelo Array Data.
        private function setMail()
        {
            $this->Assunto = $this->Data['subject'];
            $this->Mensagem = $this->Data['message'];
            $this->RemetenteNome = $this->Data['name'];
            $this->RemetenteEmail = $this->Data['email'];
            $this->DestinoNome = $this->Data['recipient_name'];
            $this->DestinoEmail = $this->Data['recipient_email'];

            $this->Data = NULL;
            $this->setMsg();
        }

        //Formatar ou Personalizar a Mensagem!
        private function setMsg()
        {
            $this->Mensagem = "{$this->Mensagem}<hr><small>Recebida em: " . date('d/m/Y H:i') . "</small>";
        }

        //Configura o PHPMailer e valida o e-mail!
        private function Config()
        {
            //SMTP AUTH
            $this->Mail->IsSMTP();
            $this->Mail->SMTPAuth = TRUE;
            $this->Mail->IsHTML(TRUE);

            //REMETENTE E RETORNO
            $this->Mail->From = $this->RemetenteEmail;
            $this->Mail->FromName = $this->RemetenteNome;
            $this->Mail->AddReplyTo($this->RemetenteEmail, $this->RemetenteNome);

            //ASSUNTO, MENSAGEM E DESTINO
            $this->Mail->Subject = $this->Assunto;
            $this->Mail->Body = $this->Mensagem;
            $this->Mail->AddAddress($this->DestinoEmail, $this->DestinoNome);
        }

        //Envia o e-mail!
        private function sendMail()
        {

            if ($this->Mail->Send()) {
                $this->Error = array(
                    "type" => "success",
                    "msg" => Alert::alert_msg('<i class="fa fa-check fa-fw"></i> Obrigado por entrar em contato: Recebemos sua mensagem e estaremos respondendo em breve!')
                );
                $this->Result = TRUE;
            } else {
                $this->Error = array(
                    "type" => "error",
                    "msg" => Alert::alert_msg('<i class="fa fa-exclamation fa-fw"></i> Erro ao enviar: Entre em contato com o administrador. ( ' . $this->Mail->ErrorInfo . ' )', E_USER_ERROR)
                );
                $this->Result = FALSE;
            }
        }

    }
