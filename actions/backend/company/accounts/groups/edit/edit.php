<?php

$Form  = helper('Form');
$Flash = helper('Flash');

$id   = get('id');
$edit = !empty($id);

//Define constants
$MODEL_CLASS     = "AccountGroup";
$MODEL_NAME      = t("Group");
$MODEL_TEMPLATE  = "%{name}";
$REDIRECT_URL    = url("..");

//Get Model
if ($edit){
	$q = new Doctrine_query();
	$q->from('AccountGroup m');
    $q->leftJoin('m.AccountGroupRelation');
	$q->andWhere('m.id = ?', $id);
    $q->andWhere('m.company_id = ?', Auth::get()->getActiveCompany()->id);
	$Model = $q->fetchOne();
}
else {
	$Model = new AccountGroup();
}

//Validate that the model exists
if (empty($Model)){
    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
}

//Load the values to the form
if( $edit && !$Form->isSubmitted() ){

    $values = $Model->toArray();

    $agents = @$values['AccountGroupRelation'];
    $values['Agents'] = array();

    foreach( (array)$agents as $agent ){
        $values['Agents'][] = $agent['account_id'];
    }

    $Form->setValues( $values );

}

//Check if the form is valid
if ($Form->isValid()){

	$values = $Form->getValues();

	$values['company_id'] = Auth::get()->getActiveCompany()->id;

    $agents = $values['Agents'];
    unset( $values['Agents'] );

	$Model->syncAndSave( $values );

    Doctrine_Query::create()->delete('AccountGroupRelation')->where('group_id = ?', $Model->id)->execute();

    foreach( $agents as $agent ){
        $CampaingAgent = new AccountGroupRelation();
        $CampaingAgent->account_id  = $agent;
        $CampaingAgent->group_id = $Model->id;
        $CampaingAgent->save();
    }

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
