<?php

$Form  = helper('Form');
$Flash = helper('Flash');

$id   = get('id');
$edit = !empty($id);

//Define constants
$MODEL_CLASS     = "Account";
$MODEL_NAME      = t("Account");
$MODEL_TEMPLATE  = "%{first_name} %{last_name}";
$REDIRECT_URL    = url("..");

//Get Model
if ($edit){
	$q = new Doctrine_query();
	$q->from('Account a');
	$q->leftJoin('a.Roles');
    $q->leftJoin('a.AccountGroupRelation');
    $q->leftJoin('a.WorkingHours');
	$q->andWhere('a.id = ?', $id);
    $q->andWhere('a.organization_id = ?', Auth::get()->organization_id);

	$Model = $q->fetchOne();
}
else {
	$Model = new Account();
}

//Validate that the model exists
if (empty($Model)){
    $Flash->error( t('Could not find %0 for editing' , $MODEL_NAME ) , $REDIRECT_URL );
}

//Validate that only the administrator can edit himself
if (Auth::hasRole('organization.admin', $Model) && !Auth::hasRole('organization.admin')){
    $Flash->error( t('This %0 can only be updated by the administrator' , $MODEL_NAME ) , $REDIRECT_URL );
}

//Validate that role can not be edited by himself
if (Auth::hasRole('organization.admin', $Model) || $Model->id == Auth::get()->id){
    $Form->get('role_id')->setOption('options', array($Model->Roles[0]->id=>$Model->Roles[0]->name));
    $Form->get('role_id')->setValue($Model->Roles[0]->id);
    $Form->get('role_id')->setOption('disabled', true);
}

//Load the values to the form
if ($edit && !$Form->isSubmitted()){

	$values = $Model->toArray();

	$values['role_id'] = $Model->Roles[0]->id;

	$Form->setValues($values);
}

//Validate subscription
if (!Auth::get()->Organization->is_active) {
    $Form->get('receive_sms_notifications')->setValue(0);
    $Form->get('receive_sms_notifications')->setOption('disabled', true);
    $Form->get('receive_sms_notifications')->setOption('example', t('For this feature you need a paid subscription'));
}

//Check if the form is valid
if ($Form->isValid()){

	$values = $Form->getValues();

    $values['organization_id'] = Auth::get()->organization_id;

	if( $values['password'] ){
		$Model->setPassword( $values['password'] );
		unset( $values['password'] );
	}

    $Model->syncAndSave( $values );

	//Role
    $q = new Doctrine_Query();
    $q->delete('AccountRole');
    $q->andWhere('account_id = ?', $Model->id);
    $q->execute();

	Auth::addAccountToRole($Model, $values['role_id']);

    //Redirect
    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
        "action" => $edit ? t("updated") : t("created") ,
        "model"  => $MODEL_NAME,
        "url"    => url(".?id=" . $Model->id)  ,
        "name"   => Template::create($MODEL_TEMPLATE)->apply($Model) ,
    ));

    $Flash->success($flash_msg , $REDIRECT_URL);
}

Action::set(compact('breadcrumb'));
