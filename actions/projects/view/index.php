<?php

$id = get('id');

//$Project
$Project = ProjectTable::retrieveById($id);

if( !$Project ){
    header('Location: /');
}

//prd($Project);

$Contact = AccountTable::retrieveById($Project->user_id);

if($Contact){
    $Contact = $Contact[0];
}

$map = '';

if($Project->plat && $Project->plong){
    $map = "var map2 = new google.maps.Map(document.getElementById('map2'), {
        center: {lat: ".$Project->plat .", lng: ".$Project->plong ."},
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: false,
        draggable: false,
        panControl: true,
        zoomControl: true,
        mapTypeControl: true,
        scaleControl: true,
        streetViewControl: true,
        overviewMapControl: true,
        rotateControl: true
    });

    marker3 = new google.maps.Marker({
                        position: new google.maps.LatLng(".$Project->plat .", ".$Project->plong ."),
                        map: map2
                      });

    google.maps.event.addListener(marker3, 'click', (function(marker3, i) {
                        return function() {
                          infowindow.setContent('<a target=\"_blank\" href=\"https://www.google.com/maps/dir/Current+Location/".$Project->plat .",".$Project->plong ."\">Mapa <img src=\"/img/pin.png\" /></a>');
                          infowindow.open(map, marker3);
                        }
                      })(marker3, i));
    ";
}

//Set values
Action::set( 'Project', $Project );
Action::set( 'Contact', $Contact );
Action::set( 'map', $map );
Action::set( 'page_title', 'DEPARTAMENTOS' );
Action::set( 'top_id', 'np-bg' );
Action::set( 'sub_menu', array(
    array(
        'href' => '/propiedades?search%5Bsale_or_rent%5D=sale&search%5Bproperty_type%5D=&search%5Bproperty_type%5D=residential&search%5Barea%5D=&search%5Bkeywords%5D=&search%5Bproperty_number%5D=&search%5Bprice_from%5D=&search%5Bprice_to%5D=&search%5Bis_foreclosured%5D=0',
        'label' => 'RESIDENCIAL',
    ),
    array(
        'href' => '/propiedades?search%5Bsale_or_rent%5D=sale&search%5Bproperty_type%5D=&search%5Bproperty_type%5D=commercial&search%5Barea%5D=&search%5Bkeywords%5D=&search%5Bproperty_number%5D=&search%5Bprice_from%5D=&search%5Bprice_to%5D=&search%5Bis_foreclosured%5D=0',
        'label' => 'COMERCIAL',
    ),
    array(
        'href' => '/proyectos-nuevos',
        'label' => 'PROYECTOS NUEVOS',
        'active' => true
    ),
));