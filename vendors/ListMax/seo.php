<?php

    function url_slug($string,$space="-") {
        $string = trim($string);
        $string = preg_replace("/[^a-zA-Z0-9 -]/", "", $string);  
        $string = preg_replace("/\s+/", $space , $string);
        $string = strtolower($string);  
        $string = urlencode( $string );
        return $string;  
    }  

    function url_property( $Property ){
        
        $url = array();
        
        if( $Property->forsale ){
            $url[] = t("compra-venta");
        }else{
            $url[] = t("alquiler-renta");        
        }
        
        $url[] = urlencode(strtolower($Property->category));
        $url[] = "puerto-rico";
        $url[] = urlencode(strtolower($Property->city));

        if( $Property->neighborhood ){
          $url[] = urlencode(strtolower($Property->neighborhood));        
        }

        if( $Property->name ){
          $url[] = url_slug( $Property->buisness_name . "-" . $Property->name );                
        }
        
        $url[] = $Property->id;
            
        return "/".implode( "/" , $url  )."/";
            
    }
    
    function title_property( $Property ){
    
        $title = array();
        
        if( $Property->name ){
          $title[] = $Property->name;
        }
        
        
        if( $Property->forsale && !$Property->forrent){
            $title[] = "Compra y Venta de";
        }elseif( !$Property->forsale && $Property->forrent){
            $title[] = "Alquiler y Renta de";
        }else{
            $title[] = "Compra y Venta o Alquiler y Renta de";
        }
        
        $title[] = $Property->category;
        $title[] = "en " . $Property->city .", Puerto Rico";
                
        if( $Property->buisness_name ){
            $title[] = $Property->buisness_name;
        }

        $title[] = "| Bienes Raices Puerto Rico";

        return implode( " " , $title );
    
    }
    
    function url_search( $Properties ){
        
    }
    
    function title_search( $Properties ){

        global $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP;
    
        $title = array();
     
           
        if( $Properties->getFilterBy("forsale") == "1" ){
            $title[] = "Compra y Venta";
        }elseif( $Properties->getFilterBy("forsale") == "0" ){
            $title[] = "Alquiler y Renta";
        }else{
            $title[] = "Compra y Venta o Alquiler y Renta";
        }

        if( $Properties->getFilterBy("commercial") ){
            $title[] = "Comercial";
        }

        if( $Properties->getFilterBy("repo") ){
            $title[] = "Reposeidas";
        }

        if( $Properties->getFilterBy("cat") ){
            $title[] = "de " . $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $Properties->getFilterBy("cat") ];
        }
        
        if( $Properties->getFilterBy("city") ){
            $title[] = "en ".$Properties->getFilterBy("city")." Puerto Rico";
        }
                
        $title[] = "| Bienes Raices en Puerto Rico";

        if( $Properties->getFilterBy("city")){
            $title[] = ". MLS ".$Properties->getFilterBy("city")." Puerto Rico";
        }

        return implode( " " , $title );
    
    }
