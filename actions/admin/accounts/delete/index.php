<?php

    $Flash = helper('Flash');

    //Get the id
    $id                      = get('id');
    $undo                    = get('undo' , 0 );
    $table                   = "Account";        
    $redirect_url            = "/admin/accounts/";
    $delete_url              = "/admin/accounts/delete/";
    $delete_flash_template   = t("Inactivated Account %0"   , "%{username}" );
    $undelete_flash_template = t("Restored Account %0"     , "%{username}" );
    
    //Get the record
    $Table    = Doctrine_Core::getTable( $table );
    $Obj      = $Table->find( $id );

    if( $undo ){
        $Obj->active = 1;
        $Obj->save();
        $template = new Template( $undelete_flash_template );
    }else{
        $Obj->active = 0;
        $Obj->save();
        $delete_flash_template .= " <a href='$delete_url?id=%{id}&undo=1'> ". t('undo') ." </a> ";
        $template = new Template( $delete_flash_template );
    }
    
    //Redirect and notify
    $Flash->success( $template->apply( $Obj )  , $redirect_url );
    
    exit();
