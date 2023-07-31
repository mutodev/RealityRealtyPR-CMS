<?php

$Form  = helper('Form');
$Flash = helper('Flash');

$id   = get('id');
$edit = !empty($id);

//Define constants
$MODEL_CLASS     = "Agency";
$MODEL_NAME      = t("Agency");
$MODEL_TEMPLATE  = "%{name}";
$REDIRECT_URL    = url("..");

//Get Model
if ($edit){
	$q = new Doctrine_query();
	$q->from('Agency c');
	$q->andWhere('c.id = ?', $id);
	$Model = $q->fetchOne();
}
else {
	$Model = new Agency();
}

//Validate that the model exists
if (empty($Model)){
    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
}

//Load the values to the form
if( $edit && !$Form->isSubmitted() ){

	$values = $Model->toArray();
	$Form->setValues( $values );
}

//Check if the form is valid
if ($Form->isValid()){

	$values = $Form->getValues();
    $Model->syncAndSave( $values );

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
