<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id   = get('unit_id');
	$edit = !empty($id);
	$propertyId = get('id');

	//Define constants
	$MODEL_CLASS     = "PropertyUnit";
	$MODEL_NAME      = t("Unit");
	$MODEL_TEMPLATE  = "%{id}";
	$REDIRECT_URL    = url("..");

	if( $edit ){
		$q = new Doctrine_Query();
		$q->from('PropertyUnit n');
		$q->leftJoin('n.Property p');
		$q->andWhere('id = ?', $id);
		$q->andWhere('p.company_id = ?', Auth::get()->getActiveCompany()->id);
		$Model = $q->fetchOne();
	}else{
		$Model = new PropertyUnit();
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
		$values['property_id'] = $propertyId;

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
