<?php

    class Session_Redis extends Session_Php{

        public function __construct(){

            parent::__construct();

            $host  = Configure::read('session.redis.host' , "localhost");
            $port  = Configure::read('session.redis.port' , 6379       );
            $auth  = Configure::read('session.redis.auth' , null       );

            $session_save_path = "tcp://$host:$port?persistent=1&weight=2&timeout=2&retry_interval=10";

            if ($auth) {
                $session_save_path .= "&auth=$auth";
            }

            ini_set('session.save_handler' , 'redis');
            ini_set('session.save_path'    , $session_save_path);
        }

    }
