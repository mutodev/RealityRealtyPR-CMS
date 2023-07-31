<?php

$Form  = helper('Form');
$Flash = helper('Flash');

$id   = get('id');
$edit = true;

//Define constants
$MODEL_CLASS     = "BillingSubscription";
$MODEL_NAME      = t("Plan Subscription for");
$MODEL_TEMPLATE  = "%{Organization.name}";
$REDIRECT_URL    = url("..");

ChargeBee_Environment::configure($_ENV['CHARGEBEE_SITE'], $_ENV['CHARGEBEE_KEY']);

//Get Model
$Query = new Doctrine_Query();
$Query->from("$MODEL_CLASS m");
$Query->leftJoin('m.Organization');
$Query->leftJoin('m.Plan');
$Query->where('m.organization_id = ?', $id);
$Model = $Query->fetchOne();

//New Custom Subscription
if (empty($Model) ){

    $edit = false;

    $Model = new $MODEL_CLASS;
    $Model->organization_id = $id;
    $Model->custom_price = 0.00;
}

//Get Customer:
try {
    $result     = ChargeBee_Customer::retrieve($id);
    $cbCustomer = $result->customer();
} catch (Exception $e) {
    $cbCustomer = null;
}

//Load the values to the form
if (!$Form->isSubmitted()){

	$Form->setValues($Model);

    //Customer Info
    if ($cbCustomer) {

        $Form->get('auto_collection')->setValue($cbCustomer->autoCollection);
        $Form->get('invoice_recipient')->setValue( intval(strtolower(substr($cbCustomer->email, -13)) == '@rpm.realityrealtypr.com') );
    }

    if ($edit) {
        $Form->get('plan')->setValue($Model->Plan->chargebee_plan_id);
    }
}


//Check if the form is valid
if ($Form->isValid()){

	$values = $Form->getValues();

    //Update Customer
    $Organization      = Doctrine::getTable('Organization')->findOneById($id);
    $OrganizationAdmin = $Organization->getAdmin();

    $customer = array(
        "id" => $id,
        "firstName" => $OrganizationAdmin->first_name,
        "lastName" => $OrganizationAdmin->last_name,
        "company" => $Organization->name,
        "phone" => $OrganizationAdmin->phone,
        "email" => $values['invoice_recipient'] ? "alberto+{$id}@rpm.realityrealtypr.com" : $OrganizationAdmin->email,
        "autoCollection" => $values['auto_collection']
    );

    $result = $cbCustomer ? ChargeBee_Customer::update($id, $customer) : ChargeBee_Customer::create($customer);


    //Update Subscription
    $Plan = Doctrine::getTable('BillingPlan')->findOneByChargebeePlanId($values['plan']);

    $subscription = array(
        "planId"  => $values['plan'],
        "prorate" => false
    );

    $result = $Model->chargebee_subscription_id ? ChargeBee_Subscription::update($Model->chargebee_subscription_id, $subscription) : ChargeBee_Subscription::createForCustomer($customer['id'], $subscription);
    $cbSubscription = $result->subscription();

    //New custom subscription (Make the first charge now)
    if (!$Model->chargebee_subscription_id && $values['plan'] == 'custom') {

        ChargeBee_Subscription::changeTermEnd($cbSubscription->id, array(
            "termEndsAt" => time() + 60
        ));
    }

    //Update Plan and Subscription
    $values['plan_id'] = $Plan->id;
    $values['chargebee_subscription_id'] = $cbSubscription->id;
    $values['current_term_start'] = date("Y-m-d H:i:s", $cbSubscription->currentTermStart);
    $values['current_term_end']   = date("Y-m-d H:i:s", $cbSubscription->currentTermEnd);
    $values['status'] = $cbSubscription->status;


    $Model->syncAndSave($values);

    //Redirect
    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
        "action" => t("updated"),
        "model"  => $MODEL_NAME   ,
        "url"    => url(".?id=" . $Model->Organization->id )  ,
        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Model ) ,
    ));

    $Flash->success( $flash_msg , $REDIRECT_URL );
}

Action::set(compact('Model', 'breadcrumb'));
