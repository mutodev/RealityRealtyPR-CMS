<?php

Auth::requirePermission('company.access');

Yammon::addViewsPath(__DIR__ . DS . '_views');

$Menu = helper('Menu');
$Menu->add( "category"  , array(
    "label"      => t('Navigation') ,
   	"class"      => 'site-menu-category',
));
$Menu->add( "dashboard"  , array(
    "label"      => t('Dashboard') ,
    "link"       => "backend.company.dashboard" ,
   	"class"      => 'site-menu-item',
   	"icon"       => "fa fa-tachometer",
));
$Menu->add( "properties"  , array(
    "label"      => t('Properties') ,
    "link"       => "backend.company.properties" ,
   	"class"      => 'site-menu-item',
   	"icon"       => "fa fa-home",
    'permission' => 'property.access'
));
$Menu->add( "properties.active"  , array(
    "label"      => t('Activas') ,
    "link"       => "backend.company.properties?status=active" ,
    "class"      => 'site-menu-item',
    'permission' => 'property.access'
));
$Menu->add( "properties.inactive"  , array(
    "label"      => t('Inactivo') ,
    "link"       => "backend.company.properties?status=inactive" ,
    "class"      => 'site-menu-item',
    'permission' => 'property.access'
));
$Menu->add( "leads"  , array(
    "label"      => t('Leads') ,
    "link"       => "backend.company.leads" ,
    'permission' => 'property.access',
    "class"      => 'site-menu-item',
    "icon"       => "fa fa-exchange",
));
$Menu->add( "documents"  , array(
    "label"      => t('Documents') ,
    "link"       => "backend.company.documents" ,
    'permission' => 'document.access',
    "class"      => 'site-menu-item',
    "icon"       => "fa fa-file-text-o",
));

function companyAgents(){
    $Query = new Doctrine_Query();
    $Query->from('Account a');
    $Query->andWhere('a.company_id = ?', Auth::get()->getActiveCompany()->id);
    $Query->andWhere('a.active = ?', true);
    $Query->orderBy('a.first_name, a.last_name');

    $return = array();
    foreach($Query->execute() as $Agent){
        $return[$Agent->id] = $Agent->getFullName();
    }

    return $return;
}
