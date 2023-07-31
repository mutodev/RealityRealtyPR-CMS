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
		$q->leftJoin('n.Tags');
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

	if( !$Form->isSubmitted() ){

		$values = $Model->toArray();

		if ($edit) {

		    $values['Tags'] = $Model->Tags->getPrimaryKeys();

		    $values['category_type'] = $Model->Category->type;

		    if ($values['is_repossessed']) {
                $values['repo_container']['source_type'] = empty($values['repossessed_investor_id']) ? 'BANK' : 'INVESTOR';
            }
        }

		$Form->setValues( $values );
	}

	if( $Form->isValid() ){

		$values = $Form->getValues();

		$values['company_id'] = Auth::get()->getActiveCompany()->id;

		if (!$values['title_en']) {
			$values['title_en'] = $values['title_es'];
		}

		$Model->syncAndSave( $values );

	    //Redirect
	    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
	        "action" => $edit ? t("updated") : t("created") ,
	        "model"  => $MODEL_NAME   ,
	        "url"    => url("..view?id=" . $Model->id )  ,
	        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
	    ));

	    if ($edit) {
            $REDIRECT_URL = url('..view?id='.$Model->id);
        } else {
            $REDIRECT_URL = url('..info?id='.$Model->id);
        }

	    $Flash->success( $flash_msg , $REDIRECT_URL );
	}

	Action::set(compact('breadcrumb'));
