<?php

$Projects = ProjectTable::retrieveAll();

//Set values
Action::set( 'Projects', $Projects );
Action::set( 'page_title', 'PROPIEDADES' );
Action::set( 'top_id', 'properties-img' );
Action::set( 'sub_menu', array(
    array(
        'href' => '/propiedades?search%5Bsale_or_rent%5D=sale&search%5Bproperty_type%5D=&search%5Bproperty_type%5D=residential&search%5Barea%5D=&search%5Bkeywords%5D=&search%5Bproperty_number%5D=&search%5Bprice_from%5D=&search%5Bprice_to%5D=&search%5Bis_foreclosured%5D=0',
        'label' => 'RESIDENCIAL'
    ),
    array(
        'href' => '/propiedades?search%5Bsale_or_rent%5D=sale&search%5Bproperty_type%5D=&search%5Bproperty_type%5D=commercial&search%5Barea%5D=&search%5Bkeywords%5D=&search%5Bproperty_number%5D=&search%5Bprice_from%5D=&search%5Bprice_to%5D=&search%5Bis_foreclosured%5D=0',
        'label' => 'COMERCIAL'
    ),
    array(
        'href' => '/proyectos-nuevos',
        'label' => 'PROYECTOS NUEVOS',
        'active' => true,
    ),
));
