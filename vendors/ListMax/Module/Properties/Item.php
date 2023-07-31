<?php

    require_once( dirname( __FILE__ ). DIRECTORY_SEPARATOR . 'constants.php' );

    class ListMax_Module_Properties_Item extends ListMax_Core_Collection_Item{    

        protected $specs;

		/* ------------------------------------------------ */
		function field_buisness_logo_url(){
		    $buisness_logo = @$this->data['buisness_logo'];
		    if( $buisness_logo ){
    		    return "http://compraoalquila.com.pr/".$buisness_logo;
            }else{
                return "";
            }
		}

		/* ------------------------------------------------ */
        function field_thumbnail(){
          
            if( empty( $this->img1 ) ){
                return "";
            }else{
                return new ListMax_Core_Collection_Item_Image( "propiedades" , $this->id , 1 , 100 , 100 );
            }
                        
        }
		/* ------------------------------------------------ */
        function field_mainimg(){
                
            if( empty( $this->img1 ) ){
                return "";
            }else{
                return new ListMax_Core_Collection_Item_Image( "propiedades" , $this->id , 1 , null , null );
            }
            
        }		
		/* ------------------------------------------------ */    
        function field_photos(){

            $photos = array();
            for( $i = 1 ; $i <= 25 ; $i++ ){
            
                if( !empty( $this["img$i"] ) ){
                    $photos[] = new ListMax_Core_Collection_Item( array( 
                        "thumbnail" => new ListMax_Core_Collection_Item_Image( "propiedades" , $this->id , $i , 100  , 100  ) ,
                        "image"     => new ListMax_Core_Collection_Item_Image( "propiedades" , $this->id , $i , null , null )
                    ));
                }
                
            }
                                                               
            return $photos;
        }            
		/* ------------------------------------------------ */
        function field_category(){        
            global $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP;
                        
            $category     = @$this->data["category"];
            $sub_category = @$this->data["subcategory"];
            return $category;
            
        }
		/* ------------------------------------------------ */
        function field_category_path(){        
            global $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP;
                        
            $path         = array();
            $category     = @$this->data["category"];
            $sub_category = @$this->data["subcategory"];



            if( !empty( $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $category ] )){
                $path[] = $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $category ];
            }

            if( !empty( $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $category.":".$sub_category ] )){
                $path[] = $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $category.":".$sub_category ];
            }
            
            return $category;
        }		
		/* ------------------------------------------------ */
		function field_vista(){

            $vista = array();

            if( $this->data["vista_sea"]	){
                $vista[] = Listmax::t('al mar');
            }
            
            if( $this->data["vista_lagoon"]	){
                $vista[] = Listmax::t('a laguna');            
            }            

            if( $this->data["vista_panoramic"]	){
                $vista[] = Listmax::t('Panorámica');
            }

            return implode( " , " , $vista );

		}
		/* ------------------------------------------------ */		
        function field_location_path(){
        
            $components   = array();
            
            if( !empty($this->neighborhood) && trim(strtolower($this->neighborhood)) != trim(strtolower($this->name)) )

                $components[] = $this->neighborhood;


            if( !empty($this->area))

                $components[] = $this->area;


            if( !empty($this->city))			

                $components[] = $this->city;


            if( !empty($this->country))

                $components[] = $this->country; 

            else

                $components[] = Listmax::t("Puerto Rico"); 
            		                        
            return $components;
                        
        }
		/* ------------------------------------------------ */
		public function field_conditions(){

		    $specs = $this->getSpecs();
		
			$fields = array();
			$fields["con_nueva"]				= array(	'caption' 	=> ListMax::t('Nueva'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
			$fields["con_vieja"]				= array(	'caption' 	=> ListMax::t('Propiedad vieja'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
			$fields["con_exelente"]			= array(	'caption' 	=> ListMax::t('Excelentes condiciones'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
			$fields["con_buena"]				= array(	'caption' 	=> ListMax::t('Buenas Condiciones'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
			/*$fields["con_muybuena"]			= array(	'caption' 	=> ListMax::t('Muy Buenas Condiciones'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );*/
			$fields["con_pobre"]				= array(	'caption' 	=> ListMax::t('Condiciones pobres'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
			$fields["con_promedio"]			= array(	'caption' 	=> ListMax::t('Condiciones promedio'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
			/*$fields["con_limpio"]			= array(	'caption' 	=> ListMax::t('Limpio'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );*/
			$fields["con_remodelado"]		= array(	'caption' 	=> ListMax::t('Remodelado'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
			$fields["con_nec_reparacion"]	= array(	'caption' 	=> ListMax::t('Necesita remodelaci&oacute;n'), 
																			'sell' 		=> array(1,2,3,4,5) , 
																			'rent' 		=> array(1,2,3,4,5) );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v['caption']; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_locations(){
		
		    $specs = $this->getSpecs();
		
		    $fields = array();
		    $fields["loc_urbana"]				= array(	'caption' 	=> ListMax::t('Urbana'), 
																'sell' 		=> array(1,2,3,4,5,6) , 
																'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_rural"]				= array(	'caption' 	=> ListMax::t('Rural'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_vista_panoramica"]= array(	'caption' 	=> ListMax::t('Vista panoramica'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_vista_laguna"]		= array(	'caption' 	=> ListMax::t('Vista a laguna'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );	
			$fields["loc_vista_mar"]			= array(	'caption' 	=> ListMax::t('Vista al mar'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_frente_mar"]		= array(	'caption' 	=> ListMax::t('Frente al mar'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );	
			$fields["loc_cerca_mall"]		= array(	'caption' 	=> ListMax::t('Cerca de mall'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_cerca_school"]		= array(	'caption' 	=> ListMax::t('Cerca de Escuela'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_cerca_hospital"]		= array(	'caption' 	=> ListMax::t('Cerca de Hospital'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_cerca_mall"]		= array(	'caption' 	=> ListMax::t('Cerca de mall'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_lote_esquina"]		= array(	'caption' 	=> ListMax::t('Lote de esquina'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );	
			$fields["loc_culdsac"]			= array(	'caption' 	=> ListMax::t('Cul-d-sac'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_vacational"]			= array(	'caption' 	=> ListMax::t('Vacacional'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_beach"]			= array(	'caption' 	=> ListMax::t('De Playa'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_camp"]			= array(	'caption' 	=> ListMax::t('De Campo'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_culdsac"]			= array(	'caption' 	=> ListMax::t('Cul-d-sac'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_avenida"]			= array(	'caption' 	=> ListMax::t('Avenida'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_acce_viasprincipales"]			= array(	'caption' 	=> ListMax::t('Accesible a v&iacute;as principales'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
			$fields["loc_cerca_parque"]		= array(	'caption' 	=> ListMax::t('Cerca de parque'), 
																			'sell' 		=> array(1,2,3,4,5,6) , 
																			'rent' 		=> array(1,2,3,4,5,6) );
		
		    $return = array();


            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v['caption']; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_roomareas(){
		
		    $specs = $this->getSpecs();
		
            $fields  = array();

            $fields["alfombra"]                 = array('caption' => ListMax::t('Alfombra')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["almacen"]                  = array('caption' => ListMax::t('Almacen')                        , 'sell' => array(4,5)       , 'rent' => array(4,5)       );
			$fields["area_carga"]               = array('caption' => ListMax::t('Area de Carga')                  , 'sell' => array(4,5)       , 'rent' => array(4,5)       );
			$fields["balcon"]                   = array('caption' => ListMax::t('Balc&oacute;n')                         , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["biblioteca"]               = array('caption' => ListMax::t('Biblioteca')                     , 'sell' => array(1,2,3)     , 'rent' => array(1,2,3)     );
			$fields["cava"]                     = array('caption' => ListMax::t('Cava de Vinos')                  , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["ceramica"]                 = array('caption' => ListMax::t('Pisos de Cer&aacute;mica')       , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["cocina_comedor"]           = array('caption' => ListMax::t('Cocina-Comedor')                 , 'sell' => array(1,2,3,5)   , 'rent' => array(1,2,3,5)   );
			$fields["cocina"]           = array('caption' => ListMax::t('Cocina')                 , 'sell' => array(1,2,3,5)   , 'rent' => array(1,2,3,5)   );
			$fields["comedor"]                  = array('caption' => ListMax::t('Comedor')                        , 'sell' => array(1,2,3,5)   , 'rent' => array(1,2,3,5)   );
			$fields["cortinas"]                 = array('caption' => ListMax::t('Cortinas')                       , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["gazebo"]                  = array('caption' => ListMax::t('Gazebo')                         , 'sell' => array(1,2,3,5)     , 'rent' => array(1,2,3,5)     );
			$fields["cuarto_huespedes"]         = array('caption' => ListMax::t('Cuarto de Hu&eacute;spedes')            , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["cuarto_juego"]             = array('caption' => ListMax::t('Cuarto de Juegos')               , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["cuarto_servicio"]          = array('caption' => ListMax::t('Cuarto de Servicio')             , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["deck_madera"]              = array('caption' => ListMax::t('Deck de Madera')                 , 'sell' => array(2)         , 'rent' => array(2)         );
			$fields["desagues"]                 = array('caption' => ListMax::t('Desagues')                       , 'sell' => array(4,5)       , 'rent' => array(4,5)       );
			$fields["family"]                   = array('caption' => ListMax::t('Family')                         , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["gym"]                      = array('caption' => ListMax::t('Gimnasio')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["laundry"]                  = array('caption' => ListMax::t('Cuarto de Laundry')              , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["marquesina"]               = array('caption' => ListMax::t('Marquesina Sencilla')                     , 'sell' => array(1,2,3,5)   , 'rent' => array(1,2,3,5)   );
			$fields["marque_doble"]             = array('caption' => ListMax::t('Marquesina doble')               , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["marquesina_extended"]             = array('caption' => ListMax::t('Marquesina Extendida')               , 'sell' => array(1,2,3,5)       , 'rent' => array(1,2,3,5)       );
			$fields["piscina"]             = array('caption' => ListMax::t('Piscina')               , 'sell' => array(1,2,3,5)       , 'rent' => array(1,2,3,5)       );
			$fields["terraza_abierta"]             = array('caption' => ListMax::t('Terraza Abierta')               , 'sell' => array(1,2,3,5)       , 'rent' => array(1,2,3,5)       );
			$fields["kitchenette"]             = array('caption' => ListMax::t('Kitchenette')               , 'sell' => array(1,2,3,5)       , 'rent' => array(1,2,3,5)       );
			$fields["cuarto_de_oficina"]             = array('caption' => ListMax::t('Cuarto de Oficina')               , 'sell' => array(1,2,3,5)       , 'rent' => array(1,2,3,5)       );
			$fields["media_cancha"]             = array('caption' => ListMax::t('Media Cancha de Baloncesto')               , 'sell' => array(1,2,3,5)       , 'rent' => array(1,2,3,5)       );

			$fields["pisos_marmol"]             = array('caption' => ListMax::t('Pisos en M&aacute;rmol')                , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["prkg"]                     = array('caption' => ListMax::t('Estacionamiento/s')              , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["prkg_bajotecho"]           = array('caption' => ListMax::t('Estacionamiento Bajo Techo')     , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["puertas_garage"]           = array('caption' => ListMax::t('Puertas de Garaje')              , 'sell' => array(2,3,5)     , 'rent' => array(2,3,5)     );
			$fields["recepcion"]                = array('caption' => ListMax::t('Recepci&oacute;n')                      , 'sell' => array(1,3,4,5)   , 'rent' => array(1,3,4,5)   );
			$fields["recibidor"]                = array('caption' => ListMax::t('Recibidor')                      , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["sala"]                     = array('caption' => ListMax::t('Sala')                           , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["sala_comedor"]             = array('caption' => ListMax::t('Sala-Comedor')                   , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["salon_conferencia"]        = array('caption' => ListMax::t('Sal&oacute;n de Conferencias')          , 'sell' => array(3,5)       , 'rent' => array(3,5)       );
			$fields["sauna"]                    = array('caption' => ListMax::t('Sauna')                          , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["tejas"]                    = array('caption' => ListMax::t('Tejas')                          , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["terraza_concreto"]         = array('caption' => ListMax::t('Terraza de Concreto')            , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["terraza_concreto_madera"]  = array('caption' => ListMax::t('Terraza de Concreto y Madera')   , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["terraza_madera"]           = array('caption' => ListMax::t('Terraza de Madera')              , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v['caption']; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_security(){
		
		    $specs = $this->getSpecs();
		
            $fields = array();

            $fields["acceso_controlado"]       = array('caption' => ListMax::t('Acceso Controlado')              , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["rejas"]                   = array('caption' => ListMax::t('Rejas')                          , 'sell' => array(1,2,3,5)   , 'rent' => array(1,2,3,5)   );
			$fields["seguridad"]               = array('caption' => ListMax::t('Guardia de Seguridad')                      , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["sistema_alarma"]          = array('caption' => ListMax::t('Sistema de Alarma')              , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["tormenteras"]             = array('caption' => ListMax::t('Tormenteras')                    , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );

			$fields["tele_entry"]             = array('caption' => ListMax::t('Tele-Entry')                    , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );

			$fields["verjada"]             = array('caption' => ListMax::t('Verjada')                    , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );

			$fields["verjada_en_cemento"]             = array('caption' => ListMax::t('Verjada en Cemento')                    , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
		
		
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v['caption']; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_ammenities(){

		    $specs = $this->getSpecs();
		
			$fields = array();
            $fields["area_juegos_ninos"]       = array('caption' => ListMax::t('Area de Juegos para Ni&ntilde;os')      , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["baloncesto"]              = array('caption' => ListMax::t('Cancha de baloncesto')           , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["casa_club"]               = array('caption' => ListMax::t('Casa Club')                      , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["establos"]                = array('caption' => ListMax::t('Establos')                       , 'sell' => array(2,6)       , 'rent' => array(2,6)       );
			$fields["gazebo_comunal"]                  = array('caption' => ListMax::t('Gazebo')                         , 'sell' => array(1,2,6)     , 'rent' => array(1,2,6)     );
			$fields["jacuzzi"]                 = array('caption' => ListMax::t('Jacuzzi')                        , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["landscaping"]             = array('caption' => ListMax::t('Landscaping')                    , 'sell' => array(2,3,4)     , 'rent' => array(2,3,4)     );
			$fields["piscina"]                 = array('caption' => ListMax::t('Piscina')                        , 'sell' => array(2)         , 'rent' => array(2)         );
			$fields["piscina_comunal"]         = array('caption' => ListMax::t('Piscina Comunal')                , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["taller"]                  = array('caption' => ListMax::t('Taller')                         , 'sell' => array(2,3,4,5)   , 'rent' => array(2,3,4,5)   );
			$fields["tennis"]                  = array('caption' => ListMax::t('Cancha de tennis')               , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["volleyball"]              = array('caption' => ListMax::t('Cancha de volleyball')           , 'sell' => array(1,2)       , 'rent' => array(1,2)       );

			$fields["media_cancha_comunal"]              = array('caption' => ListMax::t('Media Cancha de Baloncesto')           , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v['caption']; 
                }   
            }

            return $return;

		}		
		/* ------------------------------------------------ */
		public function field_equipments(){
		
		    $specs = $this->getSpecs();
		
			$fields = array();
            $fields["nevera"]                   = array('caption' => ListMax::t('Nevera')                         , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["estufa_electrica"]         = array('caption' => ListMax::t('Estufa El&eacute;ctrica')               , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["estufa_gas"]               = array('caption' => ListMax::t('Estufa de Gas')                  , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["horno"]                    = array('caption' => ListMax::t('Horno')                          , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["microhonda"]               = array('caption' => ListMax::t('Microonda')                     , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["lava_platos"]              = array('caption' => ListMax::t('Lavadora de Platos')             , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["filtro_agua"]             = array('caption' => ListMax::t('Filtro de Agua')                 , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["triturador"]               = array('caption' => ListMax::t('Triturador')                     , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["lavadora"]                = array('caption' => ListMax::t('Lavadora')                       , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["secadora"]                = array('caption' => ListMax::t('Secadora')                       , 'sell' => array(1,2,3,4,5)       , 'rent' => array(1,2,3,4,5)       );
			$fields["combo_sec_lav"]                = array('caption' => ListMax::t('Combo de lavadora/secadora')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["sisterna"]                = array('caption' => ListMax::t('Cisterna con Motor')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["tanque_de_agua"]                = array('caption' => ListMax::t('Tanque de Agua')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );

			$fields["abanicos"]                = array('caption' => ListMax::t('Abanicos')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["aire_central"]            = array('caption' => ListMax::t('Aire Central')                   , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["aire_consola"]            = array('caption' => ListMax::t('Aire de Consola')                , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["aire_ventana"]            = array('caption' => ListMax::t('Aire de Ventana')                , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["antena_tv"]               = array('caption' => ListMax::t('Antena de TV')                   , 'sell' => array(1,2)       , 'rent' => array(1,2)       );
			$fields["calentador"]              = array('caption' => ListMax::t('Calentador')                     , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["calentador_solar"]        = array('caption' => ListMax::t('Calentador Solar')               , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["compactador_basura"]      = array('caption' => ListMax::t('Compactador de Basura')          , 'sell' => array(4,5,6)     , 'rent' => array(4,5,6)     );
			$fields["elevador"]                = array('caption' => ListMax::t('Elevador')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["intercom"]                = array('caption' => ListMax::t('Intercom')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );
			$fields["parabolica_dish"]         = array('caption' => ListMax::t('Antena Parab&oacute;lica')              , 'sell' => array(2)         , 'rent' => array(2)         );
			$fields["planta_electrica"]        = array('caption' => ListMax::t('Planta El&eacute;ctrica')               , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );

			$fields["alarma"]                = array('caption' => ListMax::t('Alarma')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );

			$fields["transfer_switch"]                = array('caption' => ListMax::t('Transfer Switch')                       , 'sell' => array(1,2,3,4,5) , 'rent' => array(1,2,3,4,5) );

			$fields["sub_estacion_electrica"]  = array('caption' => ListMax::t('Sub-Estaci&oacute;n Eléctrica')         , 'sell' => array(4,5)       , 'rent' => array(4,5)       );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v['caption']; 
                }   
            }

            return $return;

		}		
		/* ------------------------------------------------ */
		public function field_services(){
		
		    $specs = $this->getSpecs();
		
			$fields = array();
            $fields["amueblado"]               = array('caption' => ListMax::t('Mobiliario')                      , 'sell' => array()          , 'rent' => array(1,2)       );
			$fields["facilidades_dehotel"]     = array('caption' => ListMax::t('Facilidades de Hotel')           , 'sell' => array(1)         , 'rent' => array(1)         );
			$fields["inc_cabletv"]             = array('caption' => ListMax::t('CableTV')                , 'sell' => array()          , 'rent' => array(1,2)       );
			$fields["inc_electricidad"]        = array('caption' => ListMax::t('Electricidad')           , 'sell' => array()          , 'rent' => array(1,2,3,4,5) );
			$fields["internet"]                = array('caption' => ListMax::t('Internet')                       , 'sell' => array()     , 'rent' => array(1,2,3)     );
			$fields["agua"]       = array('caption' => ListMax::t('Agua')          , 'sell' => array()          , 'rent' => array(1,2,3,4,5) );
			$fields["mantenimiento_jardin"]       = array('caption' => ListMax::t('Mantenimiento Jard&iacute;n')          , 'sell' => array()          , 'rent' => array(1,2,3,4,5) );
			$fields["servicio_de_alarma"]       = array('caption' => ListMax::t('Servicio de Alarma')          , 'sell' => array()          , 'rent' => array(1,2,3,4,5) );
			$fields["mantenimiento_del_complejo"]       = array('caption' => ListMax::t('Mantenimiento del Complejo')          , 'sell' => array()          , 'rent' => array(1,2,3,4,5) );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v['caption']; 
                }   
            }

            return $return;

		}		
		/* ------------------------------------------------ */
	    function getBroker(){		
	        global $ListMax;
		    $Brokers = $ListMax->getBrokers();
		    return $Brokers->fetchById( $this->broker_id );
		}
		/* ------------------------------------------------ */
		private function getSpecs(){
		    
            $db    = ListMax::db();
            
            if( !empty( $this->specs ) ){
                return $this->specs;
            }
            		       
            $select = $db->select();
            $select->from("FeaturesPropiedades");
            $select->where("id_propiedad = {$this->id}" );
            $features = $db->fetchRow( $select ); 

            $select = $db->select();
            $select->from("LocalizacionYCondiciones");
            $select->where("id_propiedad = {$this->id}" );
            $locationAndConditions = $db->fetchRow( $select ) OR array();  

            $this->specs = array_merge( empty($features) ? array() : $features , empty($locationAndConditions) ? array() : $locationAndConditions );
            unset( $this->specs['id_propiedad'] );
            
            return $this->specs;

		}
		/* ------------------------------------------------ */
		protected function siteurl(){
            return strtolower( ( empty($_SERVER['HTTPS'] )? "http://" : "https://" ) . $_SERVER['HTTP_HOST'] );
		}
		/* ------------------------------------------------ */
		protected function siteid(){
		
			// lookups the site's url in WEBSITES table and get the id
	        $site    = $this->siteurl();
			$site_id = ListMax::query("SELECT id FROM websites WHERE url = '$site'", 'log_view', 'counters');
						
			if( empty( $site_id ) ){
			    ListMax::db()->insert("websites" , array( "url" => $site , "name" => $site ) );
			    $site_id = ListMax::db()->lastInsertId();
			}else{
			    $site_id = $site_id[0]['id'];
			}
			
			if( empty( $site_id ) )
			    return false;
            else
                return $site_id;
				
		}
		/* ------------------------------------------------ */		
        public function contact( $contact_information  ){

            global $ListMax;
        
            //Get the site
	        $site    = $this->siteurl();
	        $site_id = $this->siteid();

            //Get the Database
            $db = ListMax::db();
            
            //Get the Property id
            $Property    = $this;
            $property_id = $Property->id;
        
            
            //Get The Information out of the post
            $contact_email   = isset( $contact_information ["email"] )   ? $contact_information ["email"]   : "";
            $contact_name    = isset( $contact_information ["name"] )    ? $contact_information ["name"]    : "";
            $contact_phone   = isset( $contact_information ["phone"] )   ? $contact_information ["phone"]   : "";           
            $contact_message = isset( $contact_information ["message"] ) ? $contact_information ["message"] : "";
            $contact_url     = isset( $contact_information ["url"] )     ? $contact_information ["url"]     : "";
            
            //Validate the data
            $errors = array();
            if ( empty( $contact_name ) ) {
	            $errors[] = t("Por favor, provea su nombre.");
            }
            

            if ( empty( $contact_email ) ) {
	            $errors[] = t("Por favor, provea su email.");
            }elseif ( !filter_var( $contact_email , FILTER_VALIDATE_EMAIL ) ) {
	            $errors[] = t("Por favor, provea un email v&aacute;lido.");
            }


            $contact_phone = preg_replace( "/[^0-9]/" , "" , $contact_phone );
    
            if ( empty( $contact_phone ) ) {
	            $errors[] = t("Por favor, provea un n&uacute;mero de contacto.");
            }elseif( strlen( $contact_phone) != 10 ){
	            $errors[] = t("Por favor, provea un n&uacute;mero de contacto v&aacute;lido.");            
            }

            $contact_message = trim( $contact_message );
            if ( empty( $contact_message) ) {
	            $errors[] = t("Por favor, provea un mensaje.");
            }
            
            //Return Errors
            if( !empty( $errors ) ){
                return $errors;
            }
            
            //Get The Information out of the config
            //$mail_from        = "referido@".$_SERVER['HTTP_HOST'];
            $mail_from        = "referido@listmax.com";
            $mail_bcc         = "forms@compraoalquila.com";
            $mail_return_path = "forms@compraoalquila.com";
            
            //Get the information about the id of the lister
            $lister_id     = $Property->broker_id;
            $lister_name   = $Property->broker_name . " " . $Property->broker_last_name;
            $lister_email  = $Property->broker_email;
            $lister_email2 = !empty( $Property->broker_email2 ) ? $Property->broker_email2 : null;
            
            //Add the lead to the database
        	$lead_id = $db->fetchOne("SELECT id FROM lm_clients WHERE email = ? AND user_id = ?" , array($contact_email , $lister_id) );

	        if( empty( $lead_id ) ){

		        $contact_split_name     = preg_split("/[\s,]+/", $contact_name , -1 , PREG_SPLIT_NO_EMPTY );
		        $contact_first_name     = array_shift( $contact_split_name );
		        $contact_last_name      = implode( " " , $contact_split_name );

                $values                 = array();		        
		        $values['app_name']   	= 'leads';
		        $values['type']			= 'Buyer';
		        $values['user_id']		= $lister_id;
		        $values['first_name']	= $contact_first_name;
		        $values['last_name'] 	= $contact_last_name;
		        $values['telephone']	= $contact_phone;
		        $values['email']		= $contact_email;
		        $values['date_entered'] = date('Y-m-d');
		        $values['notes']		= "Client from $site property #".$property_id."\n Form: ".$contact_message;

		        $lead_id = $db->insert( "lm_clients" , $values );
		        		
	        }
	        
	        	        
            //Add notes to the database
	        $values              = array();
	        $values['client_id'] = $lead_id;
	        $values['log_date']	 = date('Y-m-d H:i:s');
	        $values['notes']	 = "Lleno la forma desde el listado <a href='$contact_url' target='_blank'>#$property_id</a><br />Notas: $contact_message";

	        $db->insert("lm_client_log" , $values );

            //Log the contact form
	        $values                 = array();
	        $values["id_propiedad"] = $property_id;
            $values["clientID"]		= $lead_id;
	        $values["nombre"]		= $contact_name;
	        $values["tel"]			= $contact_phone;
	        $values["email"]		= $contact_email;
            $values["pregunta"]		= $contact_message;
	        $values["id_site"]		= $site_id;

	        $db->insert( "FormasContacto" , $values );

            //Create Subject
            $mail_subject = "Forma de Contacto de ListMax.com anuncio #$property_id";

            //Set up the mailer instance    
            $mailer = new ListMax_Core_Mailer();
            $mailer->addTo( $lister_email );
            
            if( $lister_email2 ){
                $mailer->addTo( $lister_email2 );
            }
            
            $mailer->setSubject( $mail_subject );


            if( !empty( $mail_from ) ){
               $mailer->setFrom( $mail_from );
               $mailer->setReturnPath( $mail_from );
            }

            if( !empty( $contact_email ) ){
                 $mailer->setReplyTo( $contact_email );
            }
            
            if( !empty( $mail_bcc ) ){
                $mailer->addBCC( $mail_bcc );	
            }
               
            //Do not send html messages to the following domains
            if( preg_match( "/\@cendant.leadrouter.com$/i" , $lister_email ) )
                $mailer->setMode('text');

            if( preg_match( "/\@cendant.leadrouter.com$/i" , $lister_email2 ) ){
                $mailer->setMode('text');            
            }


            $mailer->set( array( 'Property'     => $Property , 
                                 'url'          => $contact_url ,
                                 'name'         => $contact_name , 
                                 'email'        => $contact_email , 
                                 'phone'        => $contact_phone , 
                                 'msg'          => $contact_message ,
                                 'lister'       => $lister_name ,
                                 'listerEmail'  => $lister_email
            ));

            $mailer->send('properties.contact');
        
            return true;
        
        }
		/* ------------------------------------------------ */
		public function logView( ){
						
			$db = ListMax::db();
			$values = array();
			
			//Get the property id
			if( empty( $property_id ) )
			    $property_id = $this->data['id'];
			
            if( empty( $property_id ) )
                return false;
			
			//Get the site id
	        $site_id = $this->siteid();

			if( empty( $site_id ) )
			    return false;
			
			// decides what table to log according to the site id: 1 is for CoA and so uses viewPropiedades; everything else uses viewPropiedades2
			if( $site_id == 1) {
				$tableToLog = 'ViewsPropiedad';
			} else {
				$tableToLog = 'ViewsPropiedades2';
				$values['wsite'] = $site_id; // since we also need the site id in this table
			}
			
			// inserts values
			try{
    	        $values['id_propiedad'] = $property_id;
	            $db->insert($tableToLog , $values );
            }catch( Exception $ex ){
                return false;
            }
            
            return true;
		
		}
		/* ------------------------------------------------ */
		
    }
