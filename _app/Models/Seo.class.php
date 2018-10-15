<?php

    /**
     * Seo [ MODEL ]
     * Classe de apoio para o modelo LINK. Pode ser utilizada para gerar SSEO para as páginas do sistema!
     *
     * @copyright (c) 2014, Robson V. Leite UPINSIDE TECNOLOGIA
     */
    class Seo
    {

        private $File;
        private $Link;

        private $Schema;
        private $Title;
        private $Description;
        private $Image;

        function __construct($File, $Link)
        {
            $this->File = strip_tags(trim($File));
            $this->Link = strip_tags(trim($Link));
            $this->getSeo();
        }

        public function getSchema()
        {
            return $this->Schema;
        }

        public function getTitle()
        {
            return $this->Title;
        }

        public function getDescription()
        {
            return $this->Description;
        }

        public function getImage()
        {
            return $this->Image;
        }

        /*
         * ***************************************
         * **********  PRIVATE METHODS  **********
         * ***************************************
         */

        //Identifica o arquivo e monta o SEO de acordo
        private function getSeo()
        {
            $read = new Read();
            switch ($this->File) {
                //SEO:: IMÓVEIS
                case 'imovel':
                    $read->ExeRead('ws_properties', "WHERE realty_name = :realty_name", "realty_name={$this->Link}");
                    if ($read->getResult()) {
                        $realty = $read->getResult()[0];
                        $this->Schema = 'WebSite';
                        $this->Title = $realty['realty_title'] . ' - ' . SITE_NAME;
                        $this->Description = Check::Characters($realty['realty_description'], 156);
                        $this->Image = SITE_URL . '/uploads/' . $realty['realty_cover'];
                    } else {
                        $this->Schema = 'WebSite';
                        $this->Title = "Desculpe, não encontrado o conteúdo relacionado - " . SITE_NAME;
                        $this->Description = SITE_DESC;
                        $this->Image = INCLUDE_PATH . '/images/' . SITE_IMAGE_DEFAULT;
                    }
                    break;
                case 'filtro':
                    $this->Schema = 'WebSite';
                    $this->Title = 'Filtrar Imóveis - ' . SITE_NAME;
                    $this->Description = SITE_DESC;
                    $this->Image = INCLUDE_PATH . '/images/' . SITE_IMAGE_DEFAULT;
                    break;
                //SEO:: INDEX
                case 'index':
                    $this->Schema = 'WebSite';
                    $this->Title = SITE_NAME;
                    $this->Description = SITE_DESC;
                    $this->Image = INCLUDE_PATH . '/images/' . SITE_IMAGE_DEFAULT;
                    break;
                //SEO:: IMÓVEIS
                case 'imoveis':
                    $link = ($this->Link == 'comprar' ? 2 : ($this->Link == 'alugar' ? 1 : ($this->Link == 'temporada' ? 3 : 0)));
                    $read->ExeRead('ws_properties', "WHERE realty_transaction = :tran", "tran={$link}");
                    if ($read->getResult()) {
                        $realty = $read->getResult()[0];
                        $this->Schema = 'WebSite';
                        $this->Title = ucfirst($this->Link) . ' - ' . SITE_NAME;
                        $this->Description = Check::Characters($realty['realty_description'], 156);
                        $this->Image = SITE_URL . '/uploads/' . $realty['realty_cover'];
                    } else {
                        $this->Schema = 'WebSite';
                        $this->Title = ucfirst($this->Link) . ' - ' . SITE_NAME;
                        $this->Description = SITE_DESC;
                        $this->Image = INCLUDE_PATH . '/images/' . SITE_IMAGE_DEFAULT;
                    }
                    break;
                //SEO:: CONTATO
                case 'contato':
                    $this->Schema = 'WebSite';
                    $this->Title = 'Fale conosco - ' . SITE_NAME;
                    $this->Description = SITE_DESC;
                    $this->Image = INCLUDE_PATH . '/images/' . SITE_IMAGE_DEFAULT;
                    break;
                //SEO:: 404
                default :
                    $this->Schema = 'WebSite';
                    $this->Title = "Desculpe, não encontrado o conteúdo relacionado - " . SITE_NAME;
                    $this->Description = SITE_DESC;
                    $this->Image = INCLUDE_PATH . '/images/' . SITE_IMAGE_DEFAULT;
                    break;
            }
        }
    }
