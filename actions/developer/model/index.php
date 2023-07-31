<?php
    
    //Set 3 minutes as the time limit
    set_time_limit( 60*20 );
    ini_set( 'memory_limit' , '1000M' );
    
    $Form    = helper("Form");
    $Flash   = helper("Flash");
    
    $mode = post('mode' , 0 );
        
    //If there are no models start from scratch
    $files = glob( MODEL_CLS_PATH . "*.php" );
    if( !count( $files ) ){
        $mode = 1;
    }
    
    //Make sure that we have a connection the database
    $connection = Doctrine_Manager::connection();
    if( empty( $connection ) ){
        $Flash->error( t("You must create a connection first") , "developer" );
    }
    
    //Generate the models
    try{
              
        if( !empty( $_POST ) ){

                //If we are doing them from scratch
                if( $mode != 0  ){

                  //Dump data
                  if( $mode == 3 ){
                      $dump_path = TMP_PATH."dump".DS.mktime().DS;
                      mkdir( $dump_path , 0777 , true );
                      chmod( $dump_path , 0777 );
                      Doctrine::dumpData( $dump_path  );
                  }

                  //Drop Database
                  try{
                     Doctrine::dropDatabases();
                  }catch( Doctrine_Exception $ex ){}
                
                  //Create Database
                  Doctrine::createDatabases();
            
                  //Generate Models From Yaml
                  Doctrine::generateModelsFromYaml( SCHEMA_PATH , MODEL_CLS_PATH , array(
                   'baseClassName'        => 'Doctrine_Record_App',
                   'baseClassesDirectory' => 'base' ,
                   'generateBaseClasses'  => true ,
                   'generateTableClasses' => true
                  ) );
            
                  //Create Tables
                  Doctrine::createTablesFromModels( MODEL_CLS_PATH );

                  //Generate sql
                  Doctrine::generateSqlFromModels( SQL_PATH );

                  //Load Data
                  if( $mode == 3 ){
                      $files = glob( $dump_path . "*.yml" );
                      if( is_array( $files ) ){
                        foreach( $files as $filename ){
                            Doctrine::loadData( $filename );
                            unlink( $filename );
                        }
                      }
                     rmdir( $dump_path );
                  }elseif( $mode == 2 ){
                    $files = glob( FIXTURES_PATH . "*.yml" );
                    if( is_array( $files ) ){
                        foreach( $files as $filename ){
                            Doctrine::loadData( $filename );
                        }
                    }
                  } 

                }else{
                
                    //Generate random folder for migrations
                    $migration_path = TMP_PATH."migrations".DS.mktime().DS;
                    mkdir( $migration_path , 0777 , true );
                    chmod( $migration_path , 0777 );
                    
                    //Get the databases current version
                    $current_version = null;
                    try{
                        $current_version = $connection->fetchOne("SELECT version FROM migration_version");
                        $connection->execute( "UPDATE migration_version SET version = ?" , array(0) );
                    }catch( Exception $ex ){}
                    
                    //Generate Migration Diff
                    $migration = new Doctrine_Migration( $migration_path );
                    $diff = Doctrine::generateMigrationsFromDiff( $migration , MODEL_CLS_PATH , SCHEMA_PATH  );                    
                                        
                    //Execute Migration
                    $current_version = $migration->getCurrentVersion();
                    $latest_version  = $migration->getLatestVersion();
                    if( $current_version != $latest_version ){
                        $migration->migrate();
                    }

                    //Delete Migrations
                    $files = glob( $migration_path  . "*" );
                    foreach( $files as $file ){
                        unlink( $file );
                    }
                    rmdir( $migration_path );

                    //Restore Database Current Version
                    if( $current_version !== null ){
                        $connection->execute( "UPDATE migration_version SET version = ?" , array($current_version) );
                    }
                    
                    //Generate Models From Yaml
                    Doctrine::generateModelsFromYaml( SCHEMA_PATH , MODEL_CLS_PATH , array(
                     'baseClassName'        => 'Doctrine_Record_App',
                     'baseClassesDirectory' => 'base' ,
                     'generateBaseClasses'  => true ,
                     'generateTableClasses' => true
                    ) );

                    //Generate sql
                    Doctrine::generateSqlFromModels( SQL_PATH );
                                
                }
      
                //Fix Permissions
                chmodr(  MODELS_PATH   , 0777 );
                
                $Flash->success("Your models were generated" );
            
        }
    
    }catch( Doctrine_Exception $ex ){
        Action::set( 'message' , "There was an unexpected error creating your models please make sure the models directory exists and has write permissions: \n\n" . $ex->getMessage() . "\n\n" . $ex->getFile() . ":" . $ex->getLine() . "\n\n" . $ex->getTraceAsString() ."\n" );
        $Flash->error( "There was an unexpected error creating your models" );
    }

