<?php

$Form  = helper('Form');
$Flash = helper('Flash');

//Define constants
$MODEL_CLASS     = "Account";
$MODEL_NAME      = t("Account");
$MODEL_TEMPLATE  = "%{first_name} %{last_name}";
$REDIRECT_URL    = url("..");

//Get Model
$Model = $Account;

//Validate that the model exists
if( empty( $Model ) ){
    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
}

//Load the values to the form
if (!$Form->isSubmitted() ){
    $Form->setValues( $Model );
}

//Check if the form is valid
if( $Form->isValid() ){

	$values = $Form->getValues();

    $values['username'] = $values['email'];

	$Model->syncAndSave( $values );

    //Redirect
    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
        "action" => t("updated"),
        "model"  => $MODEL_NAME   ,
        "url"    => url(".?id=" . $Model->id )  ,
        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
    ));

    $Flash->success( $flash_msg , $REDIRECT_URL );
}

Action::set(compact('breadcrumb'));
