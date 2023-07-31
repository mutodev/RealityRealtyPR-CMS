<?php

    $Flash = helper('Flash');

    //Get the id
    $id                      = get('id');
    $undo                    = get('undo' , 0 );
    $class                   = "Slideshow";
    $redirect_url            = "/admin/slideshow/";
    $delete_flash_template   = t("Deleted Slideshow Image %0"   , "%{title}" );

    //Get the record
    $Obj   = Doctrine_Query::create()
             ->from( $class )
             ->addWhere('id = ?', $id)
             ->addWhere('is_deletable = ?', true)
             ->fetchOne();

    //Delete
    if( $Obj )
        $Obj->delete();


    $template = new Template( $delete_flash_template );

    //Redirect and notify
    $Flash->success( $template->apply( $Obj )  , $redirect_url );

    exit();
