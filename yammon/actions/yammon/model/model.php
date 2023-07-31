<?php

    //Set 3 minutes as the time limit
    set_time_limit( 60*20 );
    ini_set( 'memory_limit' , '1000M' );

    $Form    = helper("Form");
    $Flash   = helper("Flash");

    $mode = post('mode' , 0 );

    //If there are no models start from scratch
    $files = glob( DOCTRINE_MODELS_PATH . "*.php" );
    if( !count( $files ) ){
        $mode = 1;
    }

    //Make sure that we have a connection the database
    $connection = Doctrine_Manager::connection();
    if( empty( $connection ) ){
        $Flash->error( t("You must create a connection first") , ".." );
    }

    //Generate the models
    try{

        if( !empty( $_POST ) ){

                //Generate Models From Yaml
                if( $mode == 4 ){

                    Doctrine::generateModelsFromYaml( DOCTRINE_SCHEMA_PATH , DOCTRINE_MODELS_PATH , array(
                      'baseClassName'        => DOCTRINE_RECORD_CLASS ,
                      'generateBaseClasses'  => true ,
                      'generateTableClasses' => false  ,
                    ));

                }

                //If we are doing them from scratch
                else if( $mode != 0  ){

                  //Dump data
                  if( $mode == 3 ){
                      $dump_path = Yammon::getTemporaryPath("dump".DS.mktime().DS);
                      @mkdir( $dump_path , 0777 , true );
                      @chmod( $dump_path , 0777 );
                      Doctrine::dumpData( $dump_path  );
                  }

                  //Drop Database
                  try{
                     Doctrine::dropDatabases();
                  }catch( Doctrine_Exception $ex ){}

                  //Create Database
                  Doctrine::createDatabases();

                  //Generate Models From Yaml
                  $doctrine      = Doctrine_Manager::getInstance();
                  $baseClassName = $doctrine->getAttribute( Doctrine::ATTR_TABLE_CLASS );

                  Doctrine::generateModelsFromYaml( DOCTRINE_SCHEMA_PATH , DOCTRINE_MODELS_PATH , array(
                   'baseClassName'        => DOCTRINE_RECORD_CLASS ,
                   'generateBaseClasses'  => true   ,
                   'generateTableClasses' => false  ,
                  ));

                  //Create Tables
                  Doctrine::createTablesFromModels( DOCTRINE_MODELS_PATH );

                  //Generate sql
                  Doctrine::generateSqlFromModels( DOCTRINE_SQL_PATH );

                  //Load Data
                  if( $mode == 3 ){
                      $files = glob( $dump_path . "*.yml" );
                      if( is_array( $files ) ){
                        foreach( $files as $filename ){
                            Doctrine::loadData( $filename, true );
                            unlink( $filename );
                        }
                      }
                     rmdir( $dump_path );
                  }elseif( $mode == 2 ){
                    Doctrine::loadData( DOCTRINE_FIXTURES_PATH, true );
                  }

                }else{

                    //Disable autoloading
                    spl_autoload_unregister( 'models_autoload' );
                    $doctrine  = Doctrine_Manager::getInstance();
                    $doctrine->setAttribute( Doctrine::ATTR_MODEL_LOADING , Doctrine::MODEL_LOADING_AGGRESSIVE );

                    //Load the models manually
                    $bases = glob( DOCTRINE_MODELS_PATH."generated/*.php" );
                    foreach( $bases as $base ){
                        require_once( $base );
                    }

                    $models = glob( DOCTRINE_MODELS_PATH."*.php" );
                    foreach( $models as $model ){
                        require_once( $model );
                    }

                    //Generate random folder for migrations
                    $migration_path = Yammon::getTemporaryPath("migrations".DS.mktime().DS);

                    @mkdir( $migration_path , 0777 , true );
                    @chmod( $migration_path , 0777 );

                    //Get the databases current version
                    try{
                        $connection->execute( "DROP TABLE migration_version" );
                    }catch( Exception $ex ){}

                    //Generate Migration Diff
                    $migration = new Doctrine_Migration( $migration_path );
                    $diff = Doctrine::generateMigrationsFromDiff( $migration, DOCTRINE_MODELS_PATH , DOCTRINE_SCHEMA_PATH );

                    //Execute Migration
                    $connection->execute( "SET foreign_key_checks = 0;" );
                    $current_version = $migration->getCurrentVersion();
                    $last_version    = $migration->getLatestVersion();

                    if( $current_version != $last_version ){
                        $migration->migrate();
                    }

                    //Delete Migrations
                    $files = glob( $migration_path  . "*" );
                    foreach( $files as $file ){
                        unlink( $file );
                    }
                    rmdir( $migration_path );

                    //Restore Database Current Version
                    try{
                        $connection->execute( "DROP TABLE migration_version" );
                    }catch( Exception $ex ){}

                    //Generate Models From Yaml
                    Doctrine::generateModelsFromYaml( DOCTRINE_SCHEMA_PATH , DOCTRINE_MODELS_PATH , array(
                      'baseClassName'        => DOCTRINE_RECORD_CLASS ,
                      'generateBaseClasses'  => true ,
                      'generateTableClasses' => false  ,
                    ));

                    //Generate sql
                    Doctrine::generateSqlFromModels( DOCTRINE_SQL_PATH );

                }

                //Fix Permissions
                FS::chmod( DOCTRINE_MODELS_PATH , 0777 );

                $Flash->success( t("Your models were generated") );

        }

    }catch( Doctrine_Exception $ex ){
        Action::set( 'message' , t("There was an unexpected error creating your models please make sure the models directory exists and has write permissions: \n\n") );
        Action::set( 'ex' , $ex );
        $Flash->error( t("There was an unexpected error creating your models") );
    }

