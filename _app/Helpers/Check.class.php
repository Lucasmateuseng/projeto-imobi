<?php

    /**
     * Check.class [ HELPER ]
     * Classe responável por manipular e validade dados do sistema!
     *
     * @copyright (c) 2014, Robson V. Leite UPINSIDE TECNOLOGIA
     */
    class Check
    {

        private static $data;
        private static $format;

        /**
         * <b>Verifica E-mail:</b> Executa validação de formato de e-mail. Se for um email válido retorna true, ou retorna false.
         * @param STRING $email = Uma conta de e-mail
         * @return BOOL = True para um email válido, ou false
         */
        public static function Email($email)
        {
            self::$data = (string)$email;
            self::$format = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';

            if (preg_match(self::$format, self::$data)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        /**
         * <b>Tranforma URL:</b> Tranforma uma string no formato de URL amigável e retorna o a string convertida!
         * @param STRING $Name = Uma string qualquer
         * @return STRING = $data = Uma URL amigável válida
         */
        public static function Name($Name)
        {
            self::$format = array();
            self::$format['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
            self::$format['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

            self::$data = strtr(utf8_decode($Name), utf8_decode(self::$format['a']), self::$format['b']);
            self::$data = strip_tags(trim(self::$data));
            self::$data = str_replace(' ', '-', self::$data);
            self::$data = str_replace(array('-----', '----', '---', '--'), '-', self::$data);

            return strtolower(utf8_encode(self::$data));
        }

        /**
         * <b>Tranforma Data:</b> Transforma uma data no formato DD/MM/YY em uma data no formato TIMESTAMP!
         * @param STRING $Name = Data em (d/m/Y) ou (d/m/Y H:i:s)
         * @return STRING = $data = Data no formato timestamp!
         */
        public static function Data($data)
        {
            self::$format = explode(' ', $data);
            self::$data = explode('/', self::$format[0]);

            if (empty(self::$format[1])) {
                self::$format[1] = date('H:i:s');
            }

            self::$data = self::$data[2] . '-' . self::$data[1] . '-' . self::$data[0] . ' ' . self::$format[1];
            return self::$data;
        }

        /**
         * <b>Limita os Palavras:</b> Limita a quantidade de palavras a serem exibidas em uma string!
         * @param STRING $String = Uma string qualquer
         * @return INT = $Limite = String limitada pelo $Limite
         */
        public static function Words($String, $Limite, $Pointer = NULL)
        {
            self::$data = strip_tags(trim($String));
            self::$format = (int)$Limite;

            $ArrWords = explode(' ', self::$data);
            $NumWords = count($ArrWords);
            $NewWords = implode(' ', array_slice($ArrWords, 0, self::$format));

            $Pointer = (empty($Pointer) ? '...' : ' ' . $Pointer);
            $Result = (self::$format < $NumWords ? $NewWords . $Pointer : self::$data);
            return $Result;
        }

        /**
         * <b>Limita os Caracteres:</b> Limita a quantidade de caracteres a serem exibidas em uma string!
         * @param STRING $string = Uma string qualquer
         * @return INT = $limit = String limitada pelo $Limite
         */
        public static function Characters($string, $limit)
        {
            $string = strip_tags($string);
            return substr_replace($string, (strlen($string) > $limit ? '...' : ''), $limit);
        }

        /**
         * <b>Obter categoria:</b> Informe o name (url) de uma categoria para obter o ID da mesma.
         * @param STRING $category_name = URL da categoria
         * @return INT $category_id = id da categoria informada
         */
        public static function CatByName($CategoryName)
        {
            $read = new Read;
            $read->ExeRead('ws_categories', "WHERE category_name = :name", "name={$CategoryName}");
            if ($read->getRowCount()) {
                return $read->getResult()[0]['category_id'];
            } else {
                echo "A categoria {$CategoryName} não foi encontrada!";
                die();
            }
        }

        /**
         * <b>Usuários Online:</b> Ao executar este HELPER, ele automaticamente deleta os usuários expirados. Logo depois
         * executa um READ para obter quantos usuários estão realmente online no momento!
         * @return INT = Qtd de usuários online
         */
        public static function UserOnline()
        {
            $now = date('Y-m-d H:i:s');
            $deleteUserOnline = new Delete;
            $deleteUserOnline->ExeDelete('ws_siteviews_online', "WHERE online_endview < :now", "now={$now}");

            $readUserOnline = new Read;
            $readUserOnline->ExeRead('ws_siteviews_online');
            return $readUserOnline->getRowCount();
        }

        /**
         * <b>Imagem Upload:</b> Ao executar este HELPER, ele automaticamente verifica a existencia da imagem na pasta
         * uploads. Se existir retorna a imagem redimensionada!
         * @return STRING = imagem redimencionada!
         *
         * Modificação - Caso não exista a imagem, retorna um no_image.jpg
         */
        public static function Image($image, $title, $attributes = NULL, $width = NULL, $height = NULL)
        {
            $patch = SITE_URL;
            if (self::file_exists($image, '../uploads/')) {
                return '<img src="' . $patch . '/thumb.php?src=uploads/' . $image . '&w=' . $width . '&h=' . $height . '" alt="' . $title . '" title="' . $title . '" ' . $attributes . ' />';
            } else {
                return '<img src="' . $patch . '/thumb.php?src=uploads/no_image.jpg&w=' . $width . '&h=' . $height . '" ' . $attributes . ' />';
            }
        }

        public static function Image1($dir, $image, $title, $attributes = NULL, $width = NULL, $height = NULL)
        {
            $patch = SITE_URL;
            if (self::file_exists($image, $dir)) {
                return '<img src="' . $patch . '/thumb.php?src=uploads/' . $image . '&w=' . $width . '&h=' . $height . '" alt="' . $title . '" title="' . $title . '" ' . $attributes . ' />';
            } else {
                return '<img src="' . $patch . '/thumb.php?src=uploads/no_image.jpg&w=' . $width . '&h=' . $height . '" ' . $attributes . ' />';
            }
        }

        /**
         * <b>Files exists</b> Ao executar este HELPER, ele verifica se existe o
         * arquivo passado pelo parâmetro e se o mesmo não é uma pasta
         * @param $file_name STRING Recebe o nome do arquivo
         * @param $dir STRING Recebe o nome da pasta (opcional)
         * @return BOOL
         */
        public static function file_exists($file_name, $dir = '../../uploads/')
        {
            if (file_exists($dir . $file_name) && !is_dir($dir . $file_name)) {
                return TRUE;
            }
            return FALSE;

        }
    }
