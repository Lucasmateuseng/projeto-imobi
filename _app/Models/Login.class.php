<?php

    /**
     * Login.class [ MODEL ]
     * Responável por autenticar, validar, e checar usuário do sistema de login!
     *
     * @copyright (c) 2014, Robson V. Leite UPINSIDE TECNOLOGIA
     * Modificado por Pedro Nunes =)
     */
    class Login
    {

        private $level;
        private $email;
        private $password;
        private $error;
        private $result;

        /**
         * <b>Informar level:</b> Informe o nível de acesso mínimo para a área a ser protegida.
         * @param INT $level = Nível mínimo para acesso
         */
        function __construct($level)
        {
            $this->level = (int)$level;
        }

        /**
         * <b>Efetuar Login:</b> Envelope um array atribuitivo com índices STRING user [email], STRING pass.
         * Ao passar este array na ExeLogin() os dados são verificados e o login é feito!
         * @param ARRAY $UserData = user [email], pass
         */
        public function ExeLogin(array $UserData)
        {
            $this->email = (string)strip_tags(trim($UserData['user']));
            $this->password = (string)strip_tags(trim($UserData['pass']));
            $this->setLogin();
        }

        /**
         * <b>Verificar Login:</b> Executando um getresult é possível verificar se foi ou não efetuado
         * o acesso com os dados.
         * @return BOOL $Var = true para login e false para erro
         */
        public function getresult()
        {
            return $this->result;
        }

        /**
         * <b>Obter Erro:</b> Retorna um array associativo com uma mensagem e um tipo de erro E_.
         * @return ARRAY $error = Array associatico com o erro
         */
        public function geterror()
        {
            return $this->error;
        }

        /**
         * <b>Checar Login:</b> Execute esse método para verificar a sessão USERLOGIN e revalidar o acesso
         * para proteger telas restritas.
         * @return BOOL $login = Retorna true ou mata a sessão e retorna false!
         */
        public function CheckLogin()
        {
            if (empty($_SESSION['userlogin']) || $_SESSION['userlogin']['user_level'] < $this->level) {
                unset($_SESSION['userlogin']);
                return FALSE;
            } else {
                return TRUE;
            }
        }

        /*
         * ***************************************
         * **********  PRIVATE METHODS  **********
         * ***************************************
         */

        //Valida os dados e armazena os erros caso existam. Executa o login!
        private function setLogin()
        {
            if (!$this->email || !$this->password || !Check::email($this->email)) {
                $this->error = array('<i class="fa fa-exclamation-triangle fa-fw"></i> <b>Atenção:</b> Informe seu E-mail e password para efetuar o login!', E_USER_WARNING);
                $this->result = FALSE;
            } elseif (!$this->getUser()) {
                $this->error = array('<i class="fa fa-exclamation-triangle fa-fw"></i> <b>Atenção:</b> Os dados informados não são compatíveis!', E_USER_WARNING);
                $this->result = FALSE;
            } elseif ($this->result['user_level'] < $this->level) {
                $this->error = array('<i class="fa fa-exclamation-triangle fa-fw"></i> <b>Erro:</b> Desculpe ' . $this->result['user_name'] . ', você não tem permissão para acessar esta área!', E_USER_ERROR);
                $this->result = FALSE;
            } else {
                $this->Execute();
            }
        }

        //Vetifica usuário e password no banco de dados!
        private function getUser()
        {
            $read = new Read;
            $read->ExeRead("ws_users", "WHERE user_email = :email AND user_status = :status", "email={$this->email}&status=1");
            $user = $read->getresult();
            if ($user) {
                $password_stored = password_verify($this->password, $user[0]['user_password']);
                if ($password_stored === TRUE) {
                    $this->result = array(
                        'user_name' => $user[0]['user_name'],
                        'user_lastname' => $user[0]['user_lastname'],
                        'user_email' => $user[0]['user_email'],
                        'user_id' => (int)$user[0]['user_id'],
                        'user_level' => (int)$user[0]['user_level'],
                        'logged' => TRUE
                    );
                    return TRUE;
                } else {
                    $this->result = FALSE;
                    return FALSE;
                }
            } else {
                $this->result = FALSE;
                return FALSE;
            }
        }

        //Executa o login armazenando a sessão!
        private function Execute()
        {
            if (!session_id()) {
                session_start();
            }
            $_SESSION['userlogin'] = $this->result;

            $this->error = array('<i class="fa fa-check fa-fw"></i> Olá ' . $this->result['user_name'] . ', seja bem vindo(a). Aguarde redirecionamento!');
            $this->result = TRUE;
        }

    }
