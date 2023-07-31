<?php

$Form  = helper('Form');
$Flash = helper('Flash');

$id   = get('id');
$edit = !empty($id);

//Define constants
$MODEL_CLASS     = "Account";
$MODEL_NAME      = t("Account");
$MODEL_TEMPLATE  = "%{first_name} %{last_name}";
$REDIRECT_URL    = url("..");

//Get Model
if ($edit) {
    $Model = Doctrine::getTable($MODEL_CLASS)->find($id);

    $Form->get('password')->setOption('required', false);
    $Form->get('password_confirmation')->setOption('required', false);
}
else {
    $Model = new $MODEL_CLASS();
}

//Validate that the model exists
if (empty( $Model) ){
    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
}

//Load the values to the form
if( $edit && !$Form->isSubmitted() ){
	$Form->setValues($Model);
}

//Check if the form is valid
if ($Form->isValid()){

	$values = $Form->getValues();

	$values['username']   = $values['email'];
	$values['company_id'] = null;

	if( $values['password'] ){
		$Model->setPassword( $values['password'] );
		unset( $values['password'] );
	}

	$Model->syncAndSave( $values );

	//Role
	Auth::addAccountToRole($Model, 'system.admin');

    //Redirect
    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
        "action" => $edit ? t("updated") : t("created") ,
        "model"  => $MODEL_NAME   ,
        "url"    => url(".?id=" . $Model->id )  ,
        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
    ));

    $Flash->success( $flash_msg , $REDIRECT_URL );
}

Action::set(compact('breadcrumb'));
