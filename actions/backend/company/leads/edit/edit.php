<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id          = get('id');
	$edit        = !empty($id);
	$property_id = get('property_id');

	//Define constants
	$MODEL_CLASS     = "Lead";
	$MODEL_NAME      = t("Lead");
	$MODEL_TEMPLATE  = "%{first_name}";
	$REDIRECT_URL    = url("..");

	if( $edit ){
		$q = new Doctrine_Query();
		$q->from('Lead n');
		$q->leftJoin('n.Searches');
		$q->andWhere('n.id = ?', $id);
		$Model = $q->fetchOne();
	}else{
		$Model = new Lead();
	}

	//Validate that the model exists
	if( empty( $Model ) ){
	    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
	}

    if (!Auth::hasPermission('property.manage')) {
        $Form->removeElement('account_id');
    }

	if( !$Form->isSubmitted() ){

		$values = $Model->toArray();

		if (!$edit) {
			$values['property_id'] = $property_id;
		}

		$Form->setValues( $values );
	}

	if( $Form->isValid() ){

		$values = $Form->getValues();

        if (!Auth::hasPermission('property.manage')) {
            $values['account_id'] = Auth::get()->id;
        }

		$Model->syncAndSave( $values );

	    //Redirect
	    $flash_msg = t("Sucesfully %{action} #%{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
	        "action" => $edit ? t("updated") : t("created") ,
	        "model"  => $MODEL_NAME   ,
	        "url"    => url(".?id=" . $Model->id )  ,
	        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
	    ));

	    $Flash->success( $flash_msg , $REDIRECT_URL );
	}

	Action::set(compact('breadcrumb'));
