<?php

$Form  = helper('Form');
$Flash = helper('Flash');

$id = get('id');

//Define constants
$MODEL_CLASS     = "Organization";
$MODEL_NAME      = t("Phone numbers for");
$MODEL_TEMPLATE  = "%{name}";
$REDIRECT_URL    = url("..");

//Get Model
$Query = new Doctrine_Query();
$Query->from("$MODEL_CLASS m");
$Query->where('m.id = ?', $id);
$Model = $Query->fetchOne();

$q = new Doctrine_Query();
$q->from('Company');
$q->andWhere('organization_id = ?', $id);

$Form->get('company_id')->setOption('source', $q);

if ($Form->isSubmitted()){

    $values = $Form->getValues();

    //Validate numbers
    $invalidNumbers = array();
    $numbers = explode("\n", $values['numbers']);

    foreach ($numbers as $key => $number) {

        $number = preg_replace('/[^0-9]/i', '', $number);

        if (strlen($number) < 10) {
            $invalidNumbers[] = $numbers[$key];
        }
        else if (strlen($number) == 10) {
            $number = '1' . $number;
        }

        $number = '+' . $number;

        $numbers[$key] = $number;
    }

    if (!empty($invalidNumbers)) {
        $Form->get('numbers')->setError("Invalid numbers: " . implode(', ', $invalidNumbers));
    }
}

//Check if the form is valid
if ($Form->isValid()){

	$values = $Form->getValues();

    $mediumsResult = Doctrine_Query::create()->from('Medium')->whereIn('slug', $values['mediums'])->fetchArray();
    $mediums = array();
    foreach ($mediumsResult as $mediumsRow) {
        $mediums[ $mediumsRow['slug'] ] = $mediumsRow['id'];
    }

    foreach ($numbers as $key => $number) {

        $Resource = new Resource;
        $Resource->company_id = $values['company_id'];
        $Resource->address = $number;
        $Resource->provider = $values['provider'];
        $Resource->is_active = false;
        $Resource->save();

        foreach ($mediums as $medium) {

            $MediumResource = new MediumResource;
            $MediumResource->medium_id = $medium;
            $MediumResource->resource_id = $Resource->id;
            $MediumResource->save();
        }
    }

    $Company = Doctrine::getTable('Company')->find($values['company_id']);

    //Redirect
    $flash_msg = t("Sucesfully %{action} %{model} &quot;<i><a href='%{url}'>%{name}</a></i>&quot;" , array(
        "action" => t("updated"),
        "model"  => $MODEL_NAME   ,
        "url"    => url(".?id=" . $id )  ,
        "name"   => Template::create( $MODEL_TEMPLATE )->apply( $Company ) ,
    ));

    $Flash->success( $flash_msg , $REDIRECT_URL );
}

Action::set(compact('Model', 'breadcrumb'));
