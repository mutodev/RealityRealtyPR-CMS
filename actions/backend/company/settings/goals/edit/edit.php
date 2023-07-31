<?php

$Form  = helper('Form');
$Flash = helper('Flash');

$id   = get('id');
$edit = !empty($id);
$accountId = get('account_id', null);
$productionUnitId = get('production_unit_id', null);

//Define constants
$MODEL_CLASS     = "Goal";
$MODEL_NAME      = t("Goal");
$MODEL_TEMPLATE  = "%{year}";
$REDIRECT_URL    = $accountId ? url("..?account_id={$accountId}") : url("..?production_unit_id={$productionUnitId}");

//Get Model
if ($edit){
	$q = new Doctrine_query();
	$q->from('Goal m');
	$q->andWhere('m.id = ?', $id);
    $Model = $q->fetchOne();
}
else {
	$Model = new Goal();
}

//Validate that the model exists
if (empty($Model)){
    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
}

//Load the values to the form
if( $edit && !$Form->isSubmitted() ){

    $values = $Model->toArray();

    $Form->setValues( $values );

}

if (!$edit && $Form->isSubmitted()) {
    $Query = new Doctrine_Query();
    $Query->from('Goal g');
    $Query->andWhere('g.year = ?', $Form->get('year')->getValue());

    if ($accountId) {
        $Query->andWhere('g.account_id = ?', $accountId);
    }

    if ($productionUnitId) {
        $Query->andWhere('g.production_unit_id = ?', $productionUnitId);
    }

    if ($Query->count()) {
        $Form->get('year')->setError('Year goal already defined');
    }
}

//Check if the form is valid
if ($Form->isValid()){

	$values = $Form->getValues();
    $values['account_id'] = $accountId ?: null;
    $values['production_unit_id'] = $productionUnitId ?: null;
    $Model->syncAndSave($values);

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
