<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id   = get('id');
	$edit = !empty($id);

	//Define constants
	$MODEL_CLASS     = "Property";
	$MODEL_NAME      = t("Property");
	$MODEL_TEMPLATE  = "%{title}";
	$REDIRECT_URL    = url("..");

	if( $edit ){
		$q = new Doctrine_Query();
		$q->from('Property n');
		$q->andWhere('id = ?', $id);
		$Model = $q->fetchOne();
	}else{
		$Model = new Property();
	}

	//Validate that the model exists
	if( empty( $Model ) ){
	    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
	}

	if( !$Form->isSubmitted() ){

		$values = $Model->toArray();

		$Form->setValues( $values );
	}

	if( $Form->isValid() ){

		$values = $Form->getValues();

		$values['company_id'] = Auth::get()->getActiveCompany()->id;

		$Model->syncAndSave( $values );

	    //Redirect
	    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
	        "action" => $edit ? t("updated") : t("created") ,
	        "model"  => $MODEL_NAME   ,
	        "url"    => url("..view?id=" . $Model->id )  ,
	        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
	    ));

	    $REDIRECT_URL = url('..view?id='.$Model->id);

	    $Flash->success( $flash_msg , $REDIRECT_URL );
	}

	Action::set(compact('breadcrumb'));
