<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id   = get('id');
	$edit = !empty($id);
	$propertyId = get('property_id');
	$parentId = get('parent_id');

	//Define constants
	$MODEL_CLASS     = "PropertyOffer";
	$MODEL_NAME      = t("Offer");
	$MODEL_TEMPLATE  = "%{id}";
	$REDIRECT_URL    = url("..?property_id=".$propertyId);

	if( $edit ){
		$q = new Doctrine_Query();
		$q->from('PropertyOffer n');
		$q->leftJoin('n.Property p');
		$q->andWhere('id = ?', $id);
		$q->andWhere('p.company_id = ?', Auth::get()->getActiveCompany()->id);
		$Model = $q->fetchOne();
	}else{
		$Model = new PropertyOffer();
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

		$values['property_id'] = $propertyId;

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
