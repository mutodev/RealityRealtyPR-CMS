<?php

$Pagination = helper('Pagination');

$group_id   = 90;
$search     = get('search');
$page       = get('page'     , 0 );
$pagesize   = get('pagesize' , 24 );
$order      = get('order'    , 0);

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
$q->leftJoin('a.Properties p');
$q->andWhere('a.show_in_list = 1');
$q->andWhere('a.s3_photo IS NOT NULL');
$q->andWhere('a.active = 1');


if ( @$search['firstname'] ) {
    $q->andWhere('a.first_name LIKE ? OR a.last_name LIKE ?', ['%'.$search['firstname'].'%', '%'.$search['lastname'].'%']);
}

if( @$search['service'] && in_array($search['service'], array_keys($services)) ){
	$q->andWhere('a.'.$search['service'].' = 1');
}

if( @$search['rbiz'] ){
    $q->andWhere('a.branch_id = ?', $search['rbiz']);
}

//Search Order
switch( $order ){
    case '1':
        $q->orderBy('a.first_name ASC');
        break;
    default:
        $seed = @$_SESSION['seed'];

        if(!$seed){
            $seed = rand(1, 100);
            $_SESSION['seed'] = $seed;
        }

        $q->orderBy("RAND($seed)");
        break;
}

//Execute Count and Results queries

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
