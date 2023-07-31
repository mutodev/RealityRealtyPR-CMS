<?php

    //Get the requested file    
    $id = get('id');
    
    //Find the requested object
    $Upload = Doctrine::getTable('Upload')->find( $id );
    
    //Redirect to 404 if not found
    if( !$Upload ){
        Router::fowardError(404);
    }
    
    //If we found it send it to the brower
    $file_size = filesize( $Upload->path );
    header('Content-type: '.$Upload->mime );
    header('Content-Length: '.$file_size );
    readfile( $Upload->path );
    exit();
