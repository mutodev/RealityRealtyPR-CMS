<?php

    ini_set('memory_limit' , '200M' );

    require_once('Doctrine.php');
    spl_autoload_register(array('Doctrine', 'autoload'));
    Doctrine::compile('Doctrine.compiled.php', array('mysql'));
