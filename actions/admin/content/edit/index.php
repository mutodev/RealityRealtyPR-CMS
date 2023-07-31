<?php

$Form    = helper("Form");
$Flash   = helper("Flash");
$Request = helper("Request");

//Get the id
$id                      = get('id');
$edit                    = !empty( $id );
$class                   = "Content";
$redirect_url            = "/admin/content/";
$error_flash_template    = t("Couldn't find the site content for editing" );
$update_flash_template   = t("Successfully updated content: <a href='/admin/content/edit/?id=%0'> %1 </a>" , "%{id}" , "%{title}" );

$Obj = $edit ? Doctrine_Core::getTable( $class )->find( $id ) : new $class;

//Redirect if we don't find the obj
if( empty( $Obj ) )
  $Flash->error( $error_flash_template , $redirect_url );

if( !$Request->isPost() ){

    //Load Data to edit into the form
    $Form->setValues( $Obj );

}

if( $Form->isValid() ){

    //Get the form data
    $values  = $Form->getValues();

    //Save
    $Obj->sync( $values );
    $Obj->save();

    //Redirect
    $template = new Template( $update_flash_template );
    $Flash->success( $template->apply( $Obj ) , $redirect_url );

}

//Set values
Action::set( 'obj' , $Obj );
Action::set( 'id' , $id );
Action::set( 'edit' , $edit );
