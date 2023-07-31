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

	$q = new Doctrine_Query();
	$q->from('Property n');
	$q->leftJoin('n.Area a');
	$q->andWhere('id = ?', $id);
	$q->andWhere('company_id = ?', Auth::get()->getActiveCompany()->id);
	$Model = $q->fetchOne();

	$Query = new Doctrine_Query();
	$Query->from('Contract c');
	$Query->leftJoin('c.Property pro');
	$Query->leftJoin('c.Primary p');
	$Query->leftJoin('c.Secondary s');
	$Query->andWhere('pro.id = ?', $Model->id);
	$Query->orderBy('c.created_at DESC');
	$Contract = $Query->fetchOne();

	//Validate that the model exists
	if( empty( $Model ) ){
	    $Flash->error( t('Could not find %0' , $MODEL_NAME ) , $REDIRECT_URL );
	}

	$conditionsByType = array();

	foreach($Property->Conditions as $Condition){
		$conditionsByType[$Condition->type][] = $Condition->name;
	}

	$q = new Doctrine_Query();
	$q->from('PropertyPriceLog PropertyPriceLog');
	$q->andWhere('PropertyPriceLog.property_id = ?', $Model->id);
	$q->orderBy('PropertyPriceLog.created_at DESC');
	$PriceLogs = $q->execute();

	Action::set(compact('breadcrumb','Model','Contract','conditionsByType', 'PriceLogs'));
