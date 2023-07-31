<?php

    $Form  = helper('Form');
    $Flash = helper('Flash');
    
    //TODO: We need to move this logic to the
    //router
    function find_files_recursive( $paths , $pattern = "/.*/" ){
    
        $paths = (array) $paths;
        $files = array();
        
        foreach( $paths as $path ){
            $dirs      = array();
            $pathfiles = glob( $path . "*" , GLOB_MARK );
            
            foreach( $pathfiles as $file ){
            
                //If its a directory save it 
                if( is_dir( $file ) ){
                    $dirs[] = $file;
                }
                
                //If its a file check if it matches the pattern
                if( is_file( $file ) ){
                    $basename = basename( $file );
                    if( preg_match( $pattern , $basename ) ){
                        $files[] = $file;
                    }
                }
                
            }
            
            //Find in children directories
            if( !empty( $dirs ) ){
                $subfiles = find_files_recursive( $dirs , $pattern );
                $files  = array_merge( $files , $subfiles );
            }
            
        }
        
        return $files;
        
    }
    

    
    if( !empty( $_POST ) ){
    
        $paths     = Router::getPaths();
        $files     = FS::findFiles( "auth.yml" , FS::RECURSIVE , $paths );

        if( !empty($files) ){
            Auth::load( $files );
        }

        $Flash->success( t('Sucesfully created auth configuration') );

    }
    
    //Load the settings
    $q = new Doctrine_Query();
    $q->from('Setting');
    $q->orderBy( 'Setting.root_id , Setting.category , Setting.lft' );
    $Settings = $q->execute();
    
    Action::set( 'Settings' ,  $Settings );
   