<?php

$Pagination = helper("Pagination");

$type = get('type');
$page = get('page', 0);
$pagesize = get('pagesize', 15);
$order = get('order', 'expira');
$asc = get('asc', 1);
$landing = get('landing', false);
$username = get('username');
$section = get('section');

//Landing
if ($landing) {
    Action::setLayout($landing);
}

if ($type) {
    switch ($type) {
        case 'venta':
            PropertyTable::$defaultSearchFilters['sale_or_rent'] = 'sale';
            //PropertyTable::$defaultSearchFilters['type'] = 'residential';
            //PropertyTable::$defaultSearchFilters['property_type'] = 'residential:';
            $type = null;
            break;
        case 'alquiler':
            PropertyTable::$defaultSearchFilters['sale_or_rent'] = 'rent';
//            PropertyTable::$defaultSearchFilters['type'] = 'residential';
//            PropertyTable::$defaultSearchFilters['property_type'] = 'residential:';
                $type = null;
            break;
        case 'comercial':
            PropertyTable::$defaultSearchFilters['type'] = 'commercial';
            PropertyTable::$defaultSearchFilters['property_type'] = 'commercial:';
            break;
        case 'commercial':
            PropertyTable::$defaultSearchFilters['type'] = 'commercial';
            PropertyTable::$defaultSearchFilters['property_type'] = 'commercial:';
            break;
        case 'luxe':
            PropertyTable::$defaultSearchFilters['tags'][] = 'luxury';
            PropertyTable::$defaultSearchFilters['property_type'] = 'residential:';
            break;
        case 'luxury':
            PropertyTable::$defaultSearchFilters['tags'][] = 'luxury';
            PropertyTable::$defaultSearchFilters['property_type'] = 'residential:';
            break;
        case 'new-developments':
            PropertyTable::$defaultSearchFilters['tags'][] = 'new-developments';
            PropertyTable::$defaultSearchFilters['property_type'] = 'residential:';
            break;
        case 'reposeidas':
            PropertyTable::$defaultSearchFilters['tags'][] = 'foreclosure';
            break;
            break;
        case 'solares-fincas':
            PropertyTable::$defaultSearchFilters['property_type'] = 'residential:8';
            break;
        case 'shortsale':
            PropertyTable::$defaultSearchFilters['short_sale'] = 1;
            break;

        default:
            header("HTTP/1.0 404 Not Found");
            exit;
    }
}

 if ($section) {
     switch ($section) {
         case 'commercial':
             PropertyTable::$defaultSearchFilters['type'] = 'commercial';
             PropertyTable::$defaultSearchFilters['property_type'] = 'commercial:';
             break;
         case 'luxe':
             PropertyTable::$defaultSearchFilters['luxe'] = true;
             break;
     }
 }

$searchFilter = isset($_GET['search']) ? get('search') : array();
$searchFilter = array_merge(PropertyTable::$defaultSearchFilters, $searchFilter);

$q = PropertyTable::retrieveBySearch($searchFilter);

$Pagination->setSize($pagesize);
$q = $Pagination->paginate($q);
$Properties = $q->execute();

@list($typec, $category) = explode(':', $searchFilter['property_type']);

$typec = $typec ? $typec : null;

if ($searchFilter['user_id']) {
    $broker = Doctrine::getTable('Account')->find($searchFilter['user_id']);
}

$broker = false;

if ($username) {
    $Query = new Doctrine_Query();
    $Query->from('Account');
    $Query->andWhere('username = ?', $username);
    $Query->andWhere('is_top_agent = ?', true);
    $broker = $Query->fetchOne();
}

$FeaturedProperties = PropertyTable::getFeaturedProperties();

//Set values
Action::set('FeaturedProperties', $FeaturedProperties);
Action::set('type', $type);
Action::set('category', $category);
Action::set('propertyType', $type);
Action::set('Properties', $Properties);
Action::set('broker', $broker);
Action::set('searchFilter', $searchFilter);
Action::set('username', $username);
Action::set('landing', $landing);
Action::set('properties', Session::read('properties', []));
Action::set('propertyTypes', PropertyTable::getCategories());
Action::set('Pagination', $Pagination);
Action::set('order', $order);
Action::set('asc', $asc);
Action::set('propertyAreas', array(
    'Metro' => 'Area Metro',
    'Norte' => 'Area Norte',
    'Sur' => 'Area Sur',
    'Este' => 'Area Este',
    'Oeste' => 'Area Oeste',
    'Centro' => 'Area Centro',
    'Adjuntas' => 'Adjuntas',
    'Aguada' => 'Aguada',
    'Aguadilla' => 'Aguadilla',
    'Aguas Buenas' => 'Aguas Buenas',
    'Aibonito' => 'Aibonito',
    'Anasco' => 'Anasco',
    'Arecibo' => 'Arecibo',
    'Arroyo' => 'Arroyo',
    'Barceloneta' => 'Barceloneta',
    'Barranquitas' => 'Barranquitas',
    'Bayamon' => 'Bayamon',
    'Cabo Rojo' => 'Cabo Rojo',
    'Caguas' => 'Caguas',
    'Camuy' => 'Camuy',
    'Canovanas' => 'Canovanas',
    'Carolina' => 'Carolina',
    'Isla Verde' => 'Carolina &gt; Isla Verde',
    'Catano' => 'Catano',
    'Cayey' => 'Cayey',
    'Ceiba' => 'Ceiba',
    'Ciales' => 'Ciales',
    'Cidra' => 'Cidra',
    'Coamo' => 'Coamo',
    'Comerio' => 'Comerio',
    'Corozal' => 'Corozal',
    'Culebra' => 'Culebra',
    'Dorado' => 'Dorado',
    'Fajardo' => 'Fajardo',
    'Florida' => 'Florida',
    'Guanica' => 'Guanica',
    'Guayama' => 'Guayama',
    'Guayanilla' => 'Guayanilla',
    'Guaynabo' => 'Guaynabo',
    'Gurabo' => 'Gurabo',
    'Hatillo' => 'Hatillo',
    'Hormigueros' => 'Hormigueros',
    'Humacao' => 'Humacao',
    'Palmas del Mar' => 'Humacao &gt; Palmas del Mar',
    'Isabela' => 'Isabela',
    'Jayuya' => 'Jayuya',
    'Juana Diaz' => 'Juana Diaz',
    'Juncos' => 'Juncos',
    'Lajas' => 'Lajas',
    'Lares' => 'Lares',
    'Las Marias' => 'Las Marias',
    'Las Piedras' => 'Las Piedras',
    'Loiza' => 'Loiza',
    'Luquillo' => 'Luquillo',
    'Manati' => 'Manati',
    'Maricao' => 'Maricao',
    'Maunabo' => 'Maunabo',
    'Mayaguez' => 'Mayaguez',
    'Moca' => 'Moca',
    'Morovis' => 'Morovis',
    'Naguabo' => 'Naguabo',
    'Naranjito' => 'Naranjito',
    'Orocovis' => 'Orocovis',
    'Patillas' => 'Patillas',
    'Penuelas' => 'Penuelas',
    'Ponce' => 'Ponce',
    'Quebradillas' => 'Quebradillas',
    'Rincon' => 'Rincon',
    'Rio Grande' => 'Rio Grande',
    'Sabana Grande' => 'Sabana Grande',
    'Salinas' => 'Salinas',
    'San German' => 'San German',
    'San Juan' => 'San Juan',
    'Caimito' => 'San Juan &gt; Caimito',
    'Condado' => 'San Juan &gt; Condado',
    'Cupey' => 'San Juan &gt; Cupey',
    'Hato Rey' => 'San Juan &gt; Hato Rey',
    'Miramar' => 'San Juan &gt; Miramar',
    'Rio Piedras' => 'San Juan &gt; Rio Piedras',
    'Santurce' => 'San Juan &gt; Santurce',
    'Viejo San Juan' => 'San Juan &gt; Viejo San Juan',
    'San Lorenzo' => 'San Lorenzo',
    'San Sebastian' => 'San Sebastian',
    'Santa Isabel' => 'Santa Isabel',
    'Toa Alta' => 'Toa Alta',
    'Toa Baja' => 'Toa Baja',
    'Levittown' => 'Toa baja &gt; Levittown',
    'Trujillo Alto' => 'Trujillo Alto',
    'Utuado' => 'Utuado',
    'Vega Alta' => 'Vega Alta',
    'Vega Baja' => 'Vega Baja',
    'Vieques' => 'Vieques',
    'Villalba' => 'Villalba',
    'Yabucoa' => 'Yabucoa',
    'Yauco' => 'Yauco')
);
Action::set('section', $section);
