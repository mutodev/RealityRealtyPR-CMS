<?php

     global $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP;
	 $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP = array( 
        "1"    => ListMax::t('Apartamentos')             ,
        "1:1"  => ListMax::t('Walkups')                  ,  
        "1:2"  => ListMax::t('Studios')                  ,  
        "1:3"  => ListMax::t('Condominios')              ,  
        "1:4"  => ListMax::t('Town Houses')              ,  
        "1:5"  => ListMax::t('Apartamentos-Otros')       ,
        "2"    => ListMax::t('Casas')                    , 
        "2:1"  => ListMax::t('Casas Multi Familiares')   , 
        "3"    => ListMax::t('Oficinas')                 , 
        "4"    => ListMax::t('Edificios')                , 
        "5"    => ListMax::t('Locales')                  , 
        "5:6"  => ListMax::t('Almacenes')                , 
        "5:7"  => ListMax::t('Comerciales')              , 
        "5:8"  => ListMax::t('Industriales')             , 
        "5:9"  => ListMax::t('Negocios en marcha/Otro')  , 
        "6"    => ListMax::t('Terrenos-Fincas')          ,
        "6:10" => ListMax::t('Solares')                  ,
        "6:11" => ListMax::t('Fincas')
    );

    global $LISTMAX_PROPERTY_CATEGORY_TO_ID_MAP;
    $LISTMAX_PROPERTY_CATEGORY_TO_ID_MAP = array(

        ListMax::t('apartamento')             => "1" ,
        ListMax::t('apartamentos')            => "1" ,

        ListMax::t('walkup')                  => "1:1" ,
        ListMax::t('walkups')                 => "1:1" ,                
        ListMax::t('walk-up')                 => "1:1" ,
        ListMax::t('walk-ups')                => "1:1" ,

        ListMax::t('studio')                  => "1:2" ,
        ListMax::t('studios')                 => "1:2" ,
        
        ListMax::t('condominio')              => "1:3" ,
        ListMax::t('condominios')             => "1:3" ,
        
        ListMax::t('town-house')              => "1:4" ,
        ListMax::t('town-houses')             => "1:4" ,
        
        ListMax::t('apartamento-otro')        => "1:5" ,
        ListMax::t('apartamentos-otros')      => "1:5" ,
        
        ListMax::t('casa')                    => "2" ,
        ListMax::t('casas')                   => "2" ,
        
        ListMax::t('casa-multifamiliar')      => "2:1" ,
        ListMax::t('casas-multifamiliares')   => "2:1" ,
        
        ListMax::t('oficina')                 => "3" ,
        ListMax::t('oficinas')                => "3" ,
        
        ListMax::t('edificio')                => "4" ,
        ListMax::t('edificios')               => "4" ,
        
        ListMax::t('local')                   => "5" ,
        ListMax::t('locales')                 => "5" ,
            
        ListMax::t('almacen')                 => "5:6" ,
        ListMax::t('almacenes')               => "5:6" ,

        ListMax::t('comercial')               => "5:7" ,                
        ListMax::t('comerciales')             => "5:7" ,
        ListMax::t('local-comercial')         => "5:7" ,
        ListMax::t('locales-comerciales')     => "5:7" ,
        
        ListMax::t('industrial')              => "5:8" ,                
        ListMax::t('industriales')            => "5:8" ,
        
        ListMax::t('negocios-en-marcha')      => "5:9" ,
        ListMax::t('negocios-en-marcha')      => "5:9" ,
                                                        
        ListMax::t('terreno')                 => "6" ,
        ListMax::t('terrenos')                => "6" ,

        ListMax::t('terreno-finca')           => "6" ,
        ListMax::t('terrenos-fincas')         => "6" ,
        
        ListMax::t('solar')                   => "6:10" ,
        ListMax::t('solares')                 => "6:10" ,
        
        ListMax::t('finca')                   => "6:11" ,
        ListMax::t('fincas')                  => "6:11"
    );