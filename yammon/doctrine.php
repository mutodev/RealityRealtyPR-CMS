<?php

    define( 'DOCTRINE_PATH'             , YAMMON_PATH      . 'vendors'. DS . 'Doctrine'    . DS );
    define( 'DOCTRINE_FIXTURES_PATH'    , APPLICATION_PATH . 'models' . DS . 'fixtures'   . DS );
    define( 'DOCTRINE_MODELS_PATH'      , APPLICATION_PATH . 'models' . DS . 'classes'    . DS );
    define( 'DOCTRINE_MIGRATIONS_PATH'  , APPLICATION_PATH . 'models' . DS . 'migrations' . DS );
    define( 'DOCTRINE_SQL_PATH'         , APPLICATION_PATH . 'models' . DS . 'sql'        . DS );
    define( 'DOCTRINE_SCHEMA_PATH'      , APPLICATION_PATH . 'models' . DS . 'schema'     . DS );

    //Load Doctrine
    require_once( DOCTRINE_PATH. 'Doctrine.php' );

    function models_autoload( $className ){

        $class = DOCTRINE_MODELS_PATH . DS . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $base  = DOCTRINE_MODELS_PATH . DS . 'generated' . DS . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        if (file_exists($class)) {
            require $class;
            return true;
        }

        if (file_exists($base)) {
            require $base;
            return true;
        }

        return false;

    }

    //Register Doctrine Autoloads
    spl_autoload_register(array('Doctrine', 'autoload'));
//    spl_autoload_register(array('Doctrine', 'modelsAutoload'));
    spl_autoload_register( 'models_autoload' );
    spl_autoload_register(array('Doctrine', 'extensionsAutoload'));

    //Set Doctrine Attributes
    $doctrine  = Doctrine_Manager::getInstance();
    $doctrine->setAttribute( Doctrine::ATTR_MODEL_LOADING           , Doctrine::MODEL_LOADING_CONSERVATIVE);
    $doctrine->setAttribute( Doctrine::ATTR_VALIDATE                , true );
    $doctrine->setAttribute( Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE  , true);
    $doctrine->setAttribute( Doctrine::ATTR_QUOTE_IDENTIFIER        , true);
    $doctrine->setAttribute( Doctrine::ATTR_USE_DQL_CALLBACKS       , true);
    $doctrine->setAttribute( Doctrine::ATTR_AUTO_FREE_QUERY_OBJECTS , true);
    $doctrine->setAttribute( Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES  , true);

    //Set Doctrine Custom Classes
    if( class_exists('Doctrine_Collection_App' ) )
        $doctrine->setAttribute( Doctrine::ATTR_COLLECTION_CLASS , 'Doctrine_Collection_App' );
    else
        $doctrine->setAttribute( Doctrine::ATTR_COLLECTION_CLASS , 'Doctrine_Collection_Yammon' );

    if( class_exists('Doctrine_Query_App' ) )
        $doctrine->setAttribute( Doctrine::ATTR_QUERY_CLASS , 'Doctrine_Query_App' );
    else
        $doctrine->setAttribute( Doctrine::ATTR_QUERY_CLASS , 'Doctrine_Query_Yammon' );

    if( class_exists('Doctrine_Record_App' ) )
        define( 'DOCTRINE_RECORD_CLASS' , 'Doctrine_Record_App' );
    else
        define( 'DOCTRINE_RECORD_CLASS' , 'Doctrine_Record_Yammon' );

    if( class_exists('Doctrine_Table_App' ) )
        $doctrine->setAttribute( Doctrine::ATTR_TABLE_CLASS , 'Doctrine_Table_App' );
    else
        $doctrine->setAttribute( Doctrine::ATTR_TABLE_CLASS , 'Doctrine_Table_Yammon' );

    //Set Doctrine Query Cache
    if( Configure::read('doctrine.cache.query' , false ) ){

        //Get the Driver
        $driver_name    = Configure::read('doctrine.cache.query.driver' , 'Apc' );
        $driver_options = Configure::read('doctrine.cache.query.driver.options' , array() );
        $driver_class   = 'Doctrine_Cache_'.$driver_name;
        $driver         = new $driver_class( $driver_options );

        //Set the Driver
        $doctrine->setAttribute( Doctrine::ATTR_QUERY_CACHE  , $driver );
    }

    //Set Doctrine Result Cache
    if( Configure::read('doctrine.cache.result' , false ) ){

        //Get the Driver
        $driver_name    = Configure::read('doctrine.cache.result.driver' , 'Apc' );
        $driver_options = Configure::read('doctrine.cache.result.driver.options' , array() );
        $driver_class   = 'Doctrine_Cache_'.$driver_name;
        $driver         = new $driver_class( $driver_options );

        //Set the Driver
        $doctrine->setAttribute( Doctrine::ATTR_RESULT_CACHE  , $driver );
    }

    //Connect to the database
    $database_driver   = Configure::read('database.driver' , 'mysql' );
    $database_host     = Configure::read('database.host', 'localhost');
    $database_port     = Configure::read('database.port', 3306);
    $database_database = Configure::read('database.database');
    $database_username = Configure::read('database.username');
    $database_password = Configure::read('database.password');
    $database_options  = Configure::read('database.options', array());
    $database_connect  = $database_database && ($database_host || $database_username || $data_password);

    if( $database_connect ){

        $doctrine = Doctrine_Manager::getInstance();
        $doctrine->setCharset('utf8');
        $doctrine->setCollate('utf8_general_ci');

        if (empty($database_options)) {
            $connectionInfo = "$database_driver://$database_username:$database_password@$database_host/$database_database";
        }
        else {
            $connectionInfo = new PDO("$database_driver:host=$database_host;port=$database_port;dbname=$database_database", $database_username, $database_password, $database_options);
        }

        $connection = Doctrine_Manager::connection( $connectionInfo , 'yammon' );
        $connection->setCharset('utf8');
        $connection->setCollate('utf8_general_ci');
        $connection->execute( 'SET CHARACTER SET utf8' );  //TODO: CHECK IF NEEDED
        $connection->execute( 'SET time_zone="+00:00"' );
        mb_internal_encoding('UTF-8');
    }

    //Set Doctrine Profiler
    global $profiler;
    if( isset( $connection ) && Configure::read('debug') ){
        $profiler = new Doctrine_Connection_Profiler();
        $connection->setListener($profiler);
    }

    //Set Models Directory
    Doctrine::setModelsDirectory( DOCTRINE_MODELS_PATH );

    //Profile test code
    /*
    global $profiler;
    $query_count = 0;
    $time = 0;
    echo "<table width='100%' border='1'>";
    foreach ( $profiler as $event ) {
        if ($event->getName() != 'execute') {
            continue;
        }
        $query_count++;
        echo "<tr>";
        $time += $event->getElapsedSecs() ;
        echo "<td>" . $event->getName() . "</td><td>" . sprintf ( "%f" , $event->getElapsedSecs() ) . "</td>";
        echo "<td>" . $event->getQuery() . "</td>" ;
        $params = $event->getParams() ;
        if ( ! empty ( $params ) ) {
              echo "<td>";
              echo join(', ', $params);
              echo "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "Total time: " . $time . ", query count: $query_count <br>\n ";
    exit;
    */
