<?php

    class Session_Memcached extends Session_Php{

        public function __construct(){

            parent::__construct();

            $host  = Configure::read('session.memcache.host' , "localhost");
            $port  = Configure::read('session.memcache.port' , 11211      );

            $session_save_path = "tcp://$host:$port?persistent=1&weight=2&timeout=2&retry_interval=10,  ,tcp://$host:$port  ";
            ini_set('session.save_handler' , 'memcached');
            ini_set('session.save_path'    , $session_save_path);

        }

    }
