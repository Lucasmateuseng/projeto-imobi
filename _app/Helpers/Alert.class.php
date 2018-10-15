<?php

    /**
     * Alert [HELPER]
     * Classe responsável por montar e exibir as mensagens de alerta no sistema!
     * Monta os Alertas em html no padrão bootstrap e exibe no front
     */
    class Alert
    {

        /**
         * <b>Recebe os parâmetros e seta a mensagem na sessão.</b>
         * @param STRING $key Chave para setar $_SESSION
         * @param STRING $msg Mensagem a ser exibida
         * @param STRING $error Tipo de erro a ser disparado
         * @return VOID
         */
        public static function set_flashdata($key, $msg, $error = NULL)
        {
            $_SESSION[$key] = self::alert_msg($msg, $error);
        }

        /**
         * <b>Recebe a key para identificar a variável e exibe a mensagem quando necessário</b>
         * @param STRING $key Chave do array $_SESSION da mensagem a ser exibida
         */
        public static function flashdata($key)
        {
            if (isset($_SESSION[$key])) {
                echo $_SESSION[$key];
                unset($_SESSION[$key]);
            }
        }

        /**
         * <b>Monta a mensagem em html no padrão bootstrap e exibe</b>
         * @param STRING $msg Mensagem a ser exibida
         * @param STRING $error Tipo de erro a ser disparado
         * @param INT/BOOL NULL $die Trava a execução do código após esta função
         * @return STRING
         */
        public static function alert_msg($msg, $error = NULL, $die = NULL)
        {

            $class = ($error == E_USER_NOTICE ? 'alert-info' : ($error == E_USER_WARNING ? 'alert-warning' : ($error == E_USER_ERROR ? 'alert-danger' : 'alert-success')));
            $return = '<div class="alert ' . $class . '"  role="alert">' . $msg . '<span class="ajax_close"></span>';
            $return .= '<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>';
            $return .= '</div>';
            if ($die) {
                echo $return;
                die();
            }
            return $return;
        }

        /**
         * <b>Monta a mensagem para ser exibida atraves do ajax</b>
         * @param STRING $msg Mensagem a ser exibida
         * @param NULL $error Tipo de erro a ser disparado
         * @return STRING
         */
        public static function ajax_msg($msg, $error = NULL)
        {
            $class = ($error == E_USER_NOTICE ? 'alert-info' : ($error == E_USER_WARNING ? 'alert-warning' : ($error == E_USER_ERROR ? 'alert-danger' : 'alert-success')));
            $return = '<div class="alert ' . $class . ' alert-dismissible fade show" style="left: 100%; opacity: 0;" role="alert">' . $msg;
            $return .= '<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button> ';
            $return .= '<span class="alert-notify-time"></span> ';
            $return .= '</div> ';
            return $return;
        }
    }