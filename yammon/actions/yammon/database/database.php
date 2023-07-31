<?php

    $Form    = helper("Form");
    $Flash   = helper("Flash");
    $Header  = helper("Header");
    
    //Define some Exceptions
    class Database_Configuration_Exception extends Exception {};
                    
    if( !$Form->isSubmitted() ){
        $Form['host']     = Configure::read("database.host");
        $Form['port']     = Configure::read("database.port");
        $Form['name']     = Configure::read("database.database");
        $Form['username'] = Configure::read("database.username");
        $Form['password'] = Configure::read("database.password");
    }            

    if( $Form->isValid() ){

        try{
        
            //Get the variables out of the request
            $database_host     = $Form['host'];
            $database_port     = $Form['port'];
            $database_database = $Form['name'];
            $database_username = $Form['username'];
            $database_password = $Form['password'];
            
            if( empty( $database_port  ) )
                $database_port = 3306;
            
            //Attempt to connect to the database
            $dsn    = "mysql://$database_username:$database_password@$database_host:$database_port/$database_database";
            $conn   = Doctrine_Manager::connection( $dsn );
                            
            //Write the configuration
            $code    = array();
            $code[] = "<?php";
            $code[] = "";
            $code[] = "\tConfigure::write('database.host'     , '$database_host' );";
            $code[] = "\tConfigure::write('database.port'     , '$database_port' );";
            $code[] = "\tConfigure::write('database.database' , '$database_database' );";
            $code[] = "\tConfigure::write('database.username' , '$database_username' );";
            $code[] = "\tConfigure::write('database.password' , '$database_password' );";
            $code   = implode( "\n" , $code );
                
            //Send it to the file
            $config_paths = Yammon::getConfigPaths();
            $config_path  = array_shift( $config_paths );
            $database_config_file = $config_path."database.php";
            @$result = file_put_contents( $database_config_file , $code );
            if( $result === false ) throw new Database_Configuration_Exception();
            @chmod( $database_config_file, 0666 );
          
            //Show Success
            $Flash->success( t("Sucessfully created your configuration") , '..' );
                                        
        }catch( Doctrine_Connection_Exception $ex ){
        
            $Flash->error( t("Can't connect to the database:\n<br />" . $ex->getMessage() ) );
            
        }catch( Database_Configuration_Exception $ex ){
        
            $Flash->error( t("Can't write database file please set the appropiate permissions on the config folder"));
                            
        }
        
  }
             
