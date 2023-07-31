<?php

Router::connect("feeds/leadingpr_xml.php" , "feeds.leadingpr_xml");

Router::connect("top/%{username}"   , "agents.top?username=%{username}");

Router::connect("propiedades" , "properties");
Router::connect("propiedades/%{type}" , "properties?type=%{type}");
//Router::connect("properties/%{type}" , "properties?type=%{type}");
Router::connect("l/%{view}" , "properties?view=%{view}");

Router::connect("compra-venta/*/%{id}"   , "properties.view?id=%{id}" , array( "id" => "::numeric::" ));
Router::connect("alquiler-renta/*/%{id}" , "properties.view?id=%{id}" , array( "id" => "::numeric::" ));

Router::connect("p/%{id}"   , "properties.view?id=%{id}" , array( "id" => "::numeric::" ));
Router::connect("e/%{id}"   , "properties.e?id=%{id}" , array( "id" => "::numeric::" ));

Router::connect("calculadora" , "calculator");

Router::connect("contactenos" , "contact");

Router::connect("proyectos-nuevos" , "projects");
Router::connect("proyectos-nuevos/*/%{id}" , "projects.view?id=%{id}" , array( "id" => "::numeric::" ));

//Sites
//Router::connect("/info/%{identifier}.html" , "site?identifier=%{identifier}");

Router::connect('info/testimonios.html', 'testimonios');
Router::connect('info/unete-al-equipo.html', 'unete');
Router::connect('info/conocenos.html', 'brokers');
Router::connect('info/reire.html', 'reire');
Router::connect('info/relocation.html', 'servicios.relocalizacion');
Router::connect('info/consultoria_*', 'servicios.consultoria');
//Router::connect("nosotros" , "agents");
