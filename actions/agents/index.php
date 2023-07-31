<?php

$Pagination = helper('Pagination', 'pagination', ['sizes' => ['16' => 16]]);

$group_id   = 90;
$search     = get('search');
$page       = get('page'     , 0 );
$pagesize   = get('pagesize' , 16 );
$order      = get('order'    , 0);
$type       = get('type');

//Search Filters
$filtro = '';

$services = array(
    'resell' => 'Reventa',
    'commercial' => 'Comercial',
    'foreclosure' => 'Reposeídas',
    'short_sales' => 'Short Sales',
    'relocation' => 'Relocation',
    'auction' => 'Subastas',
    'new_developments' => 'Proyectos Nuevos',
);

$q = new Doctrine_Query();
$q->from('Account a');
$q->leftJoin('a.Branch b');
//$q->leftJoin('a.Properties p');
$q->andWhere('a.show_in_list = 1');
//$q->andWhere('a.s3_photo IS NOT NULL');
$q->andWhere('a.active = 1');
$q->andWhereIn('a.type', ['Broker', 'Manager']);

//if ($type) {
//    $q->andWhere('a.type = ?', $type);
//}

if ( @$search['firstname'] ) {
    $q->andWhere('(a.first_name LIKE ? OR a.last_name LIKE ?)', ['%'.$search['firstname'].'%', '%'.$search['firstname'].'%']);
}

if( @$search['service'] && in_array($search['service'], array_keys($services)) ){
	$q->andWhere('a.'.$search['service'].' = 1');
}

$q->orderBy("a.first_name, a.last_name");
$q->groupBy('a.id');

$Pagination->setSize($pagesize);
$q = $Pagination->paginate($q);

$Brokers = $q->execute();

$q = new Doctrine_Query();
$q->from('Branch b');
$RBizs = $q->execute();

//Set Controller Variables
Action::set('Brokers'      , $Brokers );
Action::set('services', $services);
Action::set('RBizs'        , $RBizs );
Action::set('searchFilter' , $search  );
Action::set('order'        , $order   );
Action::set('Pagination', $Pagination);
Action::set( 'page_title', t('translation116') );
Action::set('type', $type);
