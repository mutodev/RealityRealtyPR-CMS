<?php

	//Auth::requirePermission('property.manage');

	$Form  = helper('Form');
	$Flash = helper('Flash');

	$id   = get('id');

	//Define constants
	$MODEL_CLASS     = "DocumentCategory";
	$MODEL_NAME      = t("Category");
	$MODEL_TEMPLATE  = "%{name}";
	$REDIRECT_URL    = url("..");

    $q = new Doctrine_Query();
    $q->from($MODEL_CLASS);
    $q->andWhere('id = ?', $id);
    $Model = $q->fetchOne();

    try {
        $Model->delete();

        //Redirect
        $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
            "action" => t("deleted") ,
            "model"  => $MODEL_NAME   ,
            "url"    => url(".?id=" . $Model->id )  ,
            "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
        ));

        $Flash->success( $flash_msg , $REDIRECT_URL );
    } catch (Exception $e) {
        //Redirect
        $flash_msg = t("Category has documents can not be deleted.");

        $Flash->error( $flash_msg , $REDIRECT_URL );
    }
