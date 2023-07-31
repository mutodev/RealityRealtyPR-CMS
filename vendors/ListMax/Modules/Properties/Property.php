<?php

    class Property extends ListMax_AppCollectionItem{

        protected $specs;

		/* ------------------------------------------------ */
        function field_thumbnail(){
          
            if( empty( $this->img1 ) ){
                return "";
            }else{
                return new ListMaxImage( "propiedades" , $this->id , 1 , 100 , 100 );
            }
                        
        }
		/* ------------------------------------------------ */
        function field_mainimg(){
                
            if( empty( $this->img1 ) ){
                return "";
            }else{
                return new ListMaxImage( "propiedades" , $this->id , 1 , null , null );
            }
            
        }		
		/* ------------------------------------------------ */    
        function field_photos(){

            $photos = array();
            for( $i = 1 ; $i <= 25 ; $i++ ){
            
                if( !empty( $this["img$i"] ) ){
                    $photos[] = new ListMax_CollectionItem( array(
                        "thumbnail" => new ListMaxImage( "propiedades" , $this->id , $i , 100  , 100  ) ,
                        "image"     => new ListMaxImage( "propiedades" , $this->id , $i , null , null )
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
            return $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $category ];
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
            
            return $path;
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
		
            $fields = array(
                "con_nueva"          => ListMax::t("Nueva") , 
                "con_vieja"          => ListMax::t("Vieja") , 
                "con_remodelado"     => ListMax::t("Remodelado/a") , 
                "con_exelente"       => ListMax::t("Excelentes condiciones") , 
                "con_buena"          => ListMax::t("Buenas condiciones") , 
                "con_muybuena"       => ListMax::t("Muy buenas condiciones") , 
                "con_pobre"          => ListMax::t("Condiciones pobres") , 
                "con_promedio"       => ListMax::t("Condiciones promedio") , 
                "con_limpio"         => ListMax::t("Limpio/a") , 
                "con_sucio"          => ListMax::t("Sucio/a") , 
                "con_nec_reparacion" => ListMax::t("Necesita Repraci&oacute;n")
            );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_locations(){
		
		    $specs = $this->getSpecs();
		
            $fields = array(
                "loc_urbana"               => ListMax::t("Urbana") , 
                "loc_rural"                => ListMax::t("Rural") , 
                "loc_vista_panoramica"     => ListMax::t("Vista panor&aacute;mica") , 
                "loc_vista_laguna"         => ListMax::t("Vista a laguna") , 
                "loc_vista_mar"            => ListMax::t("Vista al mar") , 
                "loc_frente_mar"           => ListMax::t("Frente al mar") , 
                "loc_cerca_mall"           => ListMax::t("Cerca de centro comercial") , 
                "loc_lote_esquina"         => ListMax::t("Lote de esquina") , 
                "loc_culdsac"              => ListMax::t("Cul-d-sac") , 
                "loc_avenida"              => ListMax::t("En avenida") , 
                "loc_acce_viasprincipales" => ListMax::t("Acceso a v&iacute;as principales") ,
                "loc_cerca_parque"         => ListMax::t("Cerca de parque")
            );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_roomareas(){
		
		    $specs = $this->getSpecs();
		
            $fields  = array(
	            "recibidor"               => ListMax::t("Recibidor"),
	            "recepcion"               => ListMax::t("Recepci&oacute;n"),			
	            "cocina_comedor"          => ListMax::t("Cocina-Comedor"),
	            "comedor"                 => ListMax::t("Comedor"),
	            "family"                  => ListMax::t("Family"),
	            "sala"                    => ListMax::t("Sala"),
	            "laundry"                 => ListMax::t("Laundry"),		
	            "cava"                    => ListMax::t("Cava de vinos"),
	            "cuarto_huespedes"        => ListMax::t("Cuarto de hu&eacute;spedes"),
	            "cuarto_juego"            => ListMax::t("Cuarto de juegos"),
	            "cuarto_servicio"         => ListMax::t("Cuarto de Servicio"),
	            "biblioteca"              => ListMax::t("Biblioteca"),		
	            "taller"                  => ListMax::t("Taller"),		
	            "prkg"                    => ListMax::t("Estacionamiento"),
	            "prkg_bajotecho"          => ListMax::t("Estacionamiento bajo techo"),		
	            "marquesina"              => ListMax::t("Marquesina"),		
	            "area_carga"              => ListMax::t("Area de Carga"),
	            "almacen"                 => ListMax::t("Almac&eacute;n"),
	            "salon_conferencia"       => ListMax::t("Sal&oacute;n de Conferencias"),
	            "balcon"                  => ListMax::t("Balc&oacute;n"),
	            "terraza_concreto"        => ListMax::t("Terraza de Concreto"),
	            "terraza_madera"          => ListMax::t("Terraza de Madera"),
	            "terraza_concreto_madera" => ListMax::t("Terraza de Concreto y Madera"),
	            "gazebo"                  => ListMax::t("Gazebo")
	        );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_security(){
		
		    $specs = $this->getSpecs();
		
            $fields  = array(
	            "acceso_controlado" => ListMax::t("Acceso Controlado"),
	            "sistema_alarma"    => ListMax::t("Sistema de alarma"),
	            "planta_electrica"  => ListMax::t("Planta el&eacute;ctrica"),
	            "sisterna"          => ListMax::t("Cisterna"),
	            "filtro_agua"       => ListMax::t("Filtro de Agua"),
	            "tormenteras"       => ListMax::t("Tormenteras"),		
	            "rejas"             => ListMax::t("Rejas"),
	            "intercom"          => ListMax::t("Intercom"),
	            "seguridad"         => ListMax::t("Seguridad")
	        );
		
		
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v; 
                }   
            }

            return $return;

		}
		/* ------------------------------------------------ */
		public function field_ammenities(){
		
		    $specs = $this->getSpecs();
		
            $fields  = array(
	            "casa_club"         => ListMax::t("Casa club"),
	            "piscina_comunal"   => ListMax::t("Piscina comunal"),
	            "area_juegos_ninos" => ListMax::t("Area de Juegos para ni&ntilde;os"),		
	            "piscina"           => ListMax::t("Piscina"),
	            "jacuzzi"           => ListMax::t("Jacuzzi"),		
	            "sauna"             => ListMax::t("Sauna"),
	            "establos"          => ListMax::t("Establos"),
	            "deck_madera"       => ListMax::t("Deck de Madera"),
	            "gym"               => ListMax::t("Gym"),
                "landscaping"       => ListMax::t("Landscaping")
            );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v; 
                }   
            }

            return $return;

		}		
		/* ------------------------------------------------ */
		public function field_equipments(){
		
		    $specs = $this->getSpecs();
		
            $fields  = array(
	            "estufa_gas"             => ListMax::t("Estufa de Gas"),		
	            "estufa_electrica"       => ListMax::t("Estufa El&eacute;ctrica"),
	            "microhonda"             => ListMax::t("Microhonda"),
	            "lava_platos"            => ListMax::t("Lava platos"),
	            "triturador"             => ListMax::t("Triturador"),
	            "nevera"                 => ListMax::t("Nevera"),
	            "secadora"               => ListMax::t("Secadora"),
	            "lavadora"               => ListMax::t("Lavadora"),
	            "calentador"             => ListMax::t("Calentador"),
	            "calentador_solar"       => ListMax::t("Calentador Solar"),
	            "sub_estacion_electrica" => ListMax::t("Sub-Estaci&oacute;n El&eacute;ctrica"),
	            "elevador"               => ListMax::t("Elevador"),
	            "desagues"               => ListMax::t("Desague"),
	            "tanque_septico"         => ListMax::t("Tanque S&eacute;ptico"),
	            "compactador_basura"     => ListMax::t("Compactador de Basura"),
	            "parabolica_dish"        => ListMax::t("Antena Parab&oacute;lica/Dish"),		
	            "antena_tv"              => ListMax::t("Antena de TV"),				
	            "aire_central"           => ListMax::t("Aire Central"),
	            "aire_ventana"           => ListMax::t("Aire de ventana"),				
	            "aire_consola"           => ListMax::t("Aire de consola"),
	            "abanicos"               => ListMax::t("Abanicos de techo"),		
	            "alfombra"               => ListMax::t("Alfombras"),
	            "pisos_marmol"           => ListMax::t("Pisos M&aacute;rmol"),
	            "tejas"                  => ListMax::t("Tejas"),
                "puertas_garage"         => ListMax::t("Puertas de Garaje")
            );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v; 
                }   
            }

            return $return;

		}		
		/* ------------------------------------------------ */
		public function field_services(){
		
		    $specs = $this->getSpecs();
		
            $fields  = array(
	            "amueblado"           => ListMax::t("Amueblado"),		
	            "inc_mantenimiento"   => ListMax::t("Incluye Mantenimiento"),
	            "inc_cabletv"         => ListMax::t("Incluye Cable TV"),		
	            "inc_electricidad"    => ListMax::t("Incluye El&eacute;ctricidad"),
	            "inc_agua"            => ListMax::t("Incluye Agua"),
	            "facilidades_dehotel" => ListMax::t("Facilidades de Hotel"),
	            "sepermiten_animales" => ListMax::t("Se permiten animales"),
	            "internet"            => ListMax::t("Internet")
	        );
		
		    $return = array();
            foreach( $fields as $f => $v ){
                if( !empty( $specs[$f] ) ){
                   $return[] = $v; 
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
        public function contact( $contact_information  ){

            global $ListMax;

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
            $mail_from        = ListMax::config("mail_from");
            $mail_from_name   = ListMax::config("mail_from_name");
            $mail_bcc         = ListMax::config("mail_bcc");
            $mail_return_path = ListMax::config("mail_return_path");
            
            //Get the information about the id of the lister
            $lister_id     = $Property->broker_id;
            $lister_name   = $Property->broker_name . " " . $Property->broker_last_name;
            $lister_email  = $Property->broker_email;
            $lister_email2 = $Property->broker_email2;
            
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
		        $values['notes']		= "Client from www.compraoalquila.com property #".$property_id." Form: ".$contact_message;

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
	        $values["id_site"]		= '8';
	        $db->insert( "FormasContacto" , $values );

            //Create Subject
            $site         = $_SERVER['HTTP_HOST'];
            $mail_subject = "Pregunta acerca de la Propiedad #$property_id en $site";

            //Set up the mailer instance    
            $mailer = new Mailer();

//            $mailer->addTo('mon@listmax.com');
            $mailer->addTo( $lister_email );
            
            if( $lister_email2 ){
                $mailer->addTo( $lister_email2 );
            }
            
            $mailer->setSubject( $mail_subject );


            if( !empty( $mail_from ) ){
               $mailer->setFrom( $mail_from , $mail_from_name );
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

            $mailer->set( array( 'Property'     => $Property , 
                                 'url'          => $contact_url ,
                                 'name'         => $contact_name , 
                                 'email'        => $contact_email , 
                                 'phone'        => $contact_phone , 
                                 'msg'          => $contact_message ,
                                 'lister'       => $lister_name ,
                                 'listerEmail'  => $lister_email
            ));

            $mailer->send('contact');
        
            return true;
        
        }
		/* ------------------------------------------------ */
        public function logView( ){
        
            $values = array();
        
        }
		/* ------------------------------------------------ */
		
    }

?>
