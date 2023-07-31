<?php

	//Auth::requirePermission('property.manage');

    $status = get('status');
	$Form  = helper('Form', 'form', Yaml::load("{$status}.yml"));
	$Flash = helper('Flash');

    $Form->handle();

	$id   = get('id');
	$edit = !empty($id);
	$propertyId = get('property_id');

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
        $q->leftJoin('n.OptionClient oc');
        $q->leftJoin('n.RentClient rc');
        $q->leftJoin('n.SaleClient sc');
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

        $values = $Model->toArray();
        $values['Client'] = $values[ucfirst($status).'Client'];


        if (isset($values["{$status}_start_at"])) {
            $start = strtotime($values["{$status}_start_at"]);
            $end = strtotime($values["{$status}_end_at"]);

            $values['term_days'] = ceil(abs($end - $start) / 86400);
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

		$values["{$status}_client_id"] = $Client->id;

		unset($values['Client']);

        if (isset($values["{$status}_start_at"])) {
            $date = new DateTime($values["{$status}_start_at"]);
            $date->modify("+{$values['term_days']} days");
            $values["{$status}_end_at"] = $date->format('Y-m-d');
        }

		$Model->syncAndSave( $values );

		$Property = Doctrine::getTable('Property')->find($propertyId);
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
