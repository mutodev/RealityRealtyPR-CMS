<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id   = get('id');
	$edit = !empty($id);

	//Define constants
	$MODEL_CLASS     = "Document";
	$MODEL_NAME      = t("Document");
	$MODEL_TEMPLATE  = "%{name}";
	$REDIRECT_URL    = url("..");

	if( $edit ){
		$q = new Doctrine_Query();
		$q->from('Document n');
		$q->leftJoin('n.Category c');
		$q->andWhere('n.id = ?', $id);
		$q->andWhere('c.company_id = ?', Auth::get()->getActiveCompany()->id);
		$Model = $q->fetchOne();
	}else{
		$Model = new Document();
	}

	//Get category options
	$Query = new Doctrine_Query();
	$Query->from('DocumentCategory dc');
	$Query->leftJoin('dc.Parent');
	$Categories = $Query->execute();

	$categories = array();
	foreach($Categories as $Category){
		$categories[$Category->Parent->name][$Category->id] = $Category->name;
	}

	$Form->get('category_id')->setOption('options', $categories);

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

		if($values['path']){
			$values['path']->save('documents');
		}

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
