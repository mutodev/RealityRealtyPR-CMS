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
	$REDIRECT_URL    = url("..view?id=".$id);

	if( $edit ){
		$q = new Doctrine_Query();
		$q->from('Property n');
		$q->leftJoin('n.Conditions c');
		$q->andWhere('id = ?', $id);
		$q->andWhere('company_id = ?', Auth::get()->getActiveCompany()->id);
		$Model = $q->fetchOne();
	}else{
		$Model = new Property();
	}

	//Validate that the model exists
	if( empty( $Model ) ){
	    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
	}

	$Query = new Doctrine_Query();
	$Query->from('PropertyCondition c');
	$Query->andWhere('c.is_active = ?', true);
	$Conditions = $Query->execute();

	$groupByType = array();
	foreach($Conditions as $Condition){
		$groupByType[$Condition->type][$Condition->id] = $Condition->name;
	}

	foreach($groupByType as $type => $Conditions){
		$Form->get('conditions')->add(array(
			'type'    => 'SelectCheckbox',
			'label'   => $type,
			'tags'    => array('condition'),
			'options' => $Conditions,
			'name'    => strtolower($type).'_conditions'
		));
	}

	if( !$Form->isSubmitted() ){

		$values = $Model->toArray();

		$conditions = $Model->Conditions->getPrimaryKeys();

		foreach($Form->get('conditions')->getElements() as $el){

			if(!$el->hasTag('condition')){
				continue;
			}

			$el->setValue($conditions);
		}

		$Form->setValues( $values );
	}

	if( $Form->isValid() ){

		$values = $Form->getValues();

		$values['company_id'] = Auth::get()->getActiveCompany()->id;

		$conditions = array();
		foreach($values['conditions'] as $group){
			$conditions = array_merge($conditions, $group);
		}
		unset($values['conditions']);

		$Model->syncAndSave( $values );

		Doctrine_Query::create()->delete('PropertyConditionRelation c')->where('c.property_id = ?', $Model->id)->execute();

		foreach($conditions as $condition){
			$PropertyConditionRelation = new PropertyConditionRelation();
			$PropertyConditionRelation->condition_id  = $condition;
			$PropertyConditionRelation->property_id = $Model->id;
			$PropertyConditionRelation->save();
		}

	    //Redirect
	    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
	        "action" => $edit ? t("updated") : t("created") ,
	        "model"  => $MODEL_NAME   ,
	        "url"    => url("..view?id=" . $Model->id )  ,
	        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
	    ));

        $REDIRECT_URL = url("..contract.edit?id=".$Model->contract_id.'&property_id='.$Model->id);

	    $Flash->success( $flash_msg , $REDIRECT_URL );
	}

	Action::set(compact('breadcrumb'));
