<?php

    $Flash = helper('Flash');

    //Get the id
    $id                      = get('id');
    $table                   = "Slideshow";
    $redirect_url            = "/admin/slideshow/";
    $delete_flash_template   = t("Featured Image %0", "%{title}" );

    //Get the record
    $Table  = Doctrine_Core::getTable( $table );
    $Obj    = $Table->find( $id );

    if( $Obj ) {

        // Ya estaba resaltada, la quitamos
        if ( $Obj->is_sticky ) {
            $Obj->is_sticky = false;
        }

        // La anadimos como resaltada
        else {
            $oldStickyImg = Doctrine_Query::create()
                 ->from('Slideshow')
                 ->where('is_sticky = ?', true)
                 ->fetchOne();

            if ( $oldStickyImg ) {
                $oldStickyImg->is_sticky = false;
                $oldStickyImg->save();
            }

            $Obj->is_sticky = true;
            
        }

        $Obj->save();

    }

    $template = new Template( $delete_flash_template );

    //Redirect and notify
    $Flash->success( $template->apply( $Obj )  , $redirect_url );

    exit();
