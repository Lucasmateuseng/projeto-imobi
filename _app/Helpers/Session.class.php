<?php

    defined('BASEPATH') OR exit();

    class Session
    {

        private $_cache;
        private $_session;
        private $_user_ip;

        public function __construct($cache = NULL)
        {
            $this->_cache = ($cache ? $cache : 20);

            if (preg_match('/google|yahoo|bing|bot|robots|facebook/i', $_SERVER['HTTP_USER_AGENT']) == FALSE) {
                session_start();
                $this->_set_session();
            }
            $this->_session_clear();
        }

        private function _set_session()
        {
            $this->_session = (!empty($_SESSION['user_online']) ? $_SESSION['user_online'] : NULL);
            if (!isset($_SESSION['userlogin'])) {
                if (!$this->_session) {
                    $this->_session_start();
                } else {
                    $this->_session_update();
                }
                $this->_views_start();
            }
        }

        private function _session_start()
        {
            $this->_session = array();
            $this->_session['online_startview'] = date('Y-m-d H:i:s');
            $this->_session['online_endview'] = date('Y-m-d H:i:s', strtotime('+' . $this->_cache . 'minutes'));
            $this->_session['online_ip'] = $_SERVER['REMOTE_ADDR'];
            $this->_session['online_url'] = trim(strip_tags(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
            $this->_session['online_agent'] = $_SERVER['HTTP_USER_AGENT'];

            $create = new Create;
            $create->ExeCreate('ws_siteviews_online', $this->_session);
            $_SESSION['user_online'] = $create->getResult();
        }

        private function _session_update()
        {
            $read = new Read;
            $read->ExeRead('ws_siteviews_online', "WHERE online_id = :online_id AND online_ip = :online_ip", "online_id={$_SESSION['user_online']}&online_ip={$_SERVER['REMOTE_ADDR']}");
            if (!$read->getResult()) {
                $this->_session_start();
            } else {
                $this->_session = $read->getResult()[0];
                $this->_session['online_url'] = trim(strip_tags(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
                $this->_session['online_endview'] = date('Y-m-d H:i:s', strtotime('+' . $this->_cache . 'minutes'));
                $Update = new Update;
                $Update->ExeUpdate('ws_siteviews_online', $this->_session, "WHERE online_id = :online_id", "online_id={$this->_session['online_id']}");
            }
        }

        private function _session_clear()
        {
            $delete = new Delete;
            $delete->ExeDelete('ws_siteviews_online', "WHERE (online_endview < NOW() OR online_startview IS NULL) AND online_id >= :online_id", "online_id=1");
        }

        private function _views_start()
        {
            $read = new read;
            $read->Exeread('ws_siteviews', "WHERE siteviews_date = date(NOW())");
            if ($read->getResult()) {
                $udpate_view = [
                    'siteviews_users' => (!$this->_get_cookie() ? $read->getResult()[0]['siteviews_users'] + 1 : $read->getResult()[0]['siteviews_users']),
                    'siteviews_views' => (empty($this->_session['online_id']) ? $read->getResult()[0]['siteviews_views'] + 1 : $read->getResult()[0]['siteviews_views']),
                    'siteviews_pages' => $read->getResult()[0]['siteviews_pages'] + 1
                ];
                $update = new Update;
                $update->ExeUpdate('ws_siteviews', $udpate_view, "WHERE siteviews_date = :siteviews_date", "siteviews_date=" . date('Y-m-d'));

            } else {
                $create = new Create;
                $create->ExeCreate(
                    'ws_siteviews',
                    [
                        'siteviews_date' => date('Y-m-d'),
                        'siteviews_users' => 1,
                        'siteviews_views' => 1,
                        'siteviews_pages' => 1
                    ]
                );
            }
        }

        private function _get_cookie()
        {
            $cookie = filter_input(INPUT_COOKIE, 'user_view', FILTER_DEFAULT);
            setcookie("user_view", base64_encode("upinside"), time() + 86400);
            if (!$cookie) {
                return FALSE;
            } else {
                return TRUE;
            }
        }

    }
