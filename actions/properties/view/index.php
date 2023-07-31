<?php

$id = get('id');
$f  = get('force', false);
$landing  = get('landing', false);
$username   = get('username');

//Landing
if ($landing) {
    Action::setLayout($landing);
}

//Property
$Property = PropertyTable::retrieveById($id);

if( !$Property && !$force ){
    header('Location: /');
}

$conditionsByType = array();

foreach($Property->Conditions as $Condition){
    $conditionsByType[$Condition->type][] = $Condition->name;
}

$Contact = $Property->Agent;
$SecondaryAgent = $Property->SecondaryAgent;
$broker = false;

if ($username) {
    $Query = new Doctrine_Query();
    $Query->from('Account');
    $Query->andWhere('username = ?', $username);
    $Query->andWhere('is_top_agent = ?', true);
    $broker = $Query->fetchOne();
    $Contact = $broker;
}

//Similar
$SimilarProperties = PropertyTable::similarProperties($Property, 2, $Property->id);

$views = Session::read('property_view', []);

if (!in_array($Property->id, $views)) {
    $Stat = new PropertyStat();
    $Stat->property_id = $Property->id;
    $Stat->save();

    Session::push('property_view', $Property->id);
}

//Set values
Action::set('broker', $broker);
//Action::set('documents', $documents);
Action::set( 'Contact' , $Contact );
Action::set('SimilarProperties', $SimilarProperties);
Action::set( 'landing' , $landing );
Action::set( 'username' , $username );
Action::set( 'Property', $Property );
Action::set( 'id'      , $id );
Action::set('SecondaryAgent', $SecondaryAgent);
Action::set( 'conditionsByType', $conditionsByType );
Action::set( 'page_title', t('translation117') );
Action::set( 'sub_menu', array(
    array(
        'href' => '/propiedades?search%5Bsale_or_rent%5D=sale&search%5Bproperty_type%5D=&search%5Bproperty_type%5D=residential&search%5Barea%5D=&search%5Bkeywords%5D=&search%5Bproperty_number%5D=&search%5Bprice_from%5D=&search%5Bprice_to%5D=&search%5Bis_foreclosured%5D=0',
        'label' => t('translation148'),
        'active' => (in_array($Property->category_id, array(2,1)))
    ),
    array(
        'href' => '/propiedades?search%5Bsale_or_rent%5D=sale&search%5Bproperty_type%5D=&search%5Bproperty_type%5D=commercial&search%5Barea%5D=&search%5Bkeywords%5D=&search%5Bproperty_number%5D=&search%5Bprice_from%5D=&search%5Bprice_to%5D=&search%5Bis_foreclosured%5D=0',
        'label' => t('translation149'),
        'active' => ($Property->Category->type == 'Commercial')
    ),
    array(
        'href' => '/proyectos-nuevos',
        'label' => t('translation150')
    ),
));
