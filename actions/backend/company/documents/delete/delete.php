<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id   = get('id');

	//Define constants
	$MODEL_CLASS     = "Document";
	$MODEL_NAME      = t("Document");
	$MODEL_TEMPLATE  = "%{name}";
	$REDIRECT_URL    = url("..");

    $q = new Doctrine_Query();
    $q->from('Document n');
    $q->leftJoin('n.Category c');
    $q->andWhere('n.id = ?', $id);
    $Model = $q->fetchOne();

    $Model->delete();

    //Redirect
    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
        "action" => t("deleted") ,
        "model"  => $MODEL_NAME   ,
        "url"    => url(".?id=" . $Model->id )  ,
        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
    ));

    $Flash->success( $flash_msg , $REDIRECT_URL );
