<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id   = get('id');
	$edit = !empty($id);
	$propertyId = get('property_id');
	$parentId = get('parent_id');
	$status = get('status');

	//Define constants
	$MODEL_CLASS     = "Contract";
	$MODEL_NAME      = t("Contract");
	$MODEL_TEMPLATE  = "%{id}";
	$REDIRECT_URL    = url("...view?id=".$propertyId);

	if( $edit ){
		$q = new Doctrine_Query();
		$q->from('Contract n');
		$q->leftJoin('n.Property p');
		$q->leftJoin('n.Client c');
		$q->andWhere('id = ?', $id);
		$q->andWhere('p.company_id = ?', Auth::get()->getActiveCompany()->id);
		$Model = $q->fetchOne();
	}else{
		$Model = new Contract();
	}

	//Validate that the model exists
	if( empty( $Model ) ){
	    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
	}

	if( !$Form->isSubmitted() ){

		//Renewing
		if($parentId){
			$q = new Doctrine_Query();
			$q->from('Contract n');
			$q->leftJoin('n.Property p');
			$q->leftJoin('n.Client cls');
			$q->andWhere('id = ?', $parentId);
			$q->andWhere('p.company_id = ?', Auth::get()->getActiveCompany()->id);
			$Parent = $q->fetchOne();

			if($Parent){
				$values = $Parent->toArray();
			}
		}else{
			$values = $Model->toArray();
		}

		if ($edit) {
		    $values['publish'] = $Model->status === 'PUBLISHED';
        }

		$Form->setValues( $values );
	}

	if( $Form->isValid() ){

		$values = $Form->getValues();

		$values['parent_id']  = $parentId;
		$values['company_id'] = Auth::get()->getActiveCompany()->id;

		$Client = Doctrine::getTable('Client')->find($values['Client']['id']);

		if (!$Client) {
			$Client = new Client();
			$Client->syncAndSave($values['Client']);
		}

		$values['client_id'] = $Client->id;

		unset($values['Client']);

		$Model->syncAndSave( $values );

		$Property = Doctrine::getTable('Property')->find($propertyId);

		$Property->status = $values['publish'] ? 'PUBLISHED' : 'UNPUBLISHED';
        $Property->start_at = $Model->start_at;
        $Property->end_at = $Model->end_at;

        $Property->contract_id = $Model->id;
		$Property->save();

        $Model->account_id = $values['account_id'];
        $Model->save();

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
