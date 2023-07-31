<?php

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
        "6"    => ListMax::t('Terrenos')                 ,
        "6:10" => ListMax::t('Solares')                  ,
        "6:11" => ListMax::t('Fincas')
    );

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
        
        ListMax::t('solar')                   => "6:10" ,
        ListMax::t('solares')                 => "6:10" ,
        
        ListMax::t('finca')                   => "6:11" ,
        ListMax::t('fincas')                  => "6:11"
    );

    class Properties extends ListMax_AppCollection{

        private $category;
        private $rooms_exp;
        private $baths_exp;
        private $prkg_exp;
        private $desc_exp;
        private $sqfeet_exp;
        private $sqmt_exp;
        private $comm_exp;
        private $openh_exp;
        private $longt_exp;
        private $shortt_exp;
        private $tshare_exp;
		private $active_exp;

        protected $tableName     = "Propiedades";
        protected $primaryKey    = "id";
        protected $itemClass     = "Property";
                        
        protected $filters = array(
            "id"           ,
            "bathfrom"     ,
            "bathto"       ,
            "cat"          ,
            "commercial"   ,
            "city"         ,
            "forsale"      ,
            "neighborhood" ,
            "pricefrom"    ,
            "priceto"      ,
            "roomsfrom"    ,
            "roomsto"      ,
            "sqftfrom"     ,
            "sqmtfrom"     ,
            "openhouse"    ,
            "repo"         ,
            "has_photo"    ,
            "video"        ,
            "parkings"     ,
            "longterm"     ,
            "shortterm"    ,
            "timeshare"    ,
            "office"       ,
			"business"     ,
            "broker"       ,
            "brokers"      ,
			"active"       ,
			"location"     ,
			"vista"        ,
			"brokers"      ,
			"keywords"     ,
        );        
        
        protected $orders = array(
            "name"   => "name",
            "price"   => "IF( price = 0 , 999999999 , price )",
            "rooms"   => "rooms",
            "baths"   => "baths",
            "newly"   => "newly",
            "updated" => "updated" ,
	        "nextopen" => "OpenHouse.fecha" ,
            "rand"    => "rand"
        );
       
        /* -------------------------------------------------------- */
        private function setExpressions () {

            $this->vid_exp     = new Zend_Db_Expr("YouTubeEmbedCodes.id IS NOT NULL");
            $this->repo_exp    = new Zend_Db_Expr("Repo.id IS NOT NULL");
            $this->openh_exp   = new Zend_Db_Expr("DATEDIFF( CURRENT_DATE , STR_TO_DATE( OpenHouse.fecha , GET_FORMAT(DATE,'INTERNAL')) ) BETWEEN 0 AND 14");
            $this->bname_exp   = new Zend_Db_Expr("IF( RBiz.id IS NOT NULL , RBiz.rbiz_name , RealtorsInfo.negocio )");
            $this->blogo_exp   = new Zend_Db_Expr("IF( RBiz.id IS NOT NULL , RBiz.logo      , CONCAT( '/fotos/logos_realtors/' , RealtorsInfo.logo_path ) )");
            $this->burl_exp    = new Zend_Db_Expr("IF( RBiz.id IS NOT NULL , RBiz.url       , RealtorsInfo.url )");
            $this->has_photo   = new Zend_Db_Expr("FotosPropiedades.img1 != '0'");
            $this->active_exp  = new Zend_Db_Expr("expira >= ".date('Ymd'));
            $this->forrent_exp = new Zend_DB_Expr("Propiedades.sealquila OR ( NOT Propiedades.sealquila AND NOT Propiedades.sevende )");
            $this->rent_exp    = new Zend_DB_Expr("IF( Propiedades.sealquila , Propiedades.precio_renta , IF( Propiedades.sevende ,  0 , Propiedades.precio ))" ); 
            
            $category_filter = $this->getFilterBy("cat");
            @list( $category , $sub_category ) = explode( ":" , $category_filter );

            switch( $category ){
				case "1": //APTS
                          $this->desc_exp   = new Zend_Db_Expr("InfoApartamentos.nota");
				          $this->rooms_exp  = new Zend_Db_Expr("InfoApartamentos.cuartos");
					      $this->baths_exp  = new Zend_Db_Expr("InfoApartamentos.banos");
                          $this->prkg_exp   = new Zend_Db_Expr("InfoApartamentos.prkg");
                          $this->sqfeet_exp = new Zend_Db_Expr("InfoApartamentos.piesc");
                          $this->sqmt_exp   = new Zend_Db_Expr("0");
                          $this->comm_exp   = new Zend_Db_Expr("0");
                          $this->longt_exp  = new Zend_Db_Expr("TiposAlquilerPropiedades.long_term");
                          $this->shortt_exp  = new Zend_Db_Expr("TiposAlquilerPropiedades.short_term");
                          $this->tshare_exp  = new Zend_Db_Expr("TiposAlquilerPropiedades.time_share");
					      break;
					      
				case "2": //HOUSES
                          $this->desc_exp   = new Zend_Db_Expr("InfoCasas.nota");
					      $this->rooms_exp  = new Zend_Db_Expr("InfoCasas.cuartos");
                          $this->baths_exp  = new Zend_Db_Expr("InfoCasas.banos");
                          $this->prkg_exp   = new Zend_Db_Expr("InfoCasas.prkg");
					      $this->sqfeet_exp = new Zend_Db_Expr("InfoCasas.piesc");
                          $this->sqmt_exp   = new Zend_Db_Expr("InfoCasas.metrosc");
                          $this->comm_exp   = new Zend_Db_Expr("0");
                          $this->longt_exp  = new Zend_Db_Expr("0");
                          $this->shortt_exp  = new Zend_Db_Expr("0");
                          $this->tshare_exp  = new Zend_Db_Expr("0");
					      break;
					      
				case "3": //OFFICE
                          $this->desc_exp   = new Zend_Db_Expr("InfoOficinas.nota");
					      $this->rooms_exp  = new Zend_Db_Expr("0");
                          $this->baths_exp  = new Zend_Db_Expr("InfoOficinas.banos");
                          $this->prkg_exp   = new Zend_Db_Expr("InfoOficinas.prkg");
					      $this->sqfeet_exp = new Zend_Db_Expr("InfoOficinas.piesc");
                          $this->sqmt_exp   = new Zend_Db_Expr("0");
                          $this->comm_exp   = new Zend_Db_Expr("1");
                          $this->longt_exp  = new Zend_Db_Expr("0");
                          $this->shortt_exp  = new Zend_Db_Expr("0");
                          $this->tshare_exp  = new Zend_Db_Expr("0");
					      break;
					      
				case "4": //EDIFICIO
                          $this->desc_exp   = new Zend_Db_Expr("InfoEdificios.nota");
					      $this->rooms_exp  = new Zend_Db_Expr("0");
                          $this->baths_exp  = new Zend_Db_Expr("InfoEdificios.banos");
                          $this->prkg_exp   = new Zend_Db_Expr("InfoEdificios.prkg");
					      $this->sqfeet_exp = new Zend_Db_Expr("InfoEdificios.piesc");
                          $this->sqmt_exp   = new Zend_Db_Expr("0");
                          $this->comm_exp   = new Zend_Db_Expr("1");
                          $this->longt_exp  = new Zend_Db_Expr("0");
                          $this->shortt_exp  = new Zend_Db_Expr("0");
                          $this->tshare_exp  = new Zend_Db_Expr("0");
					      break;
					      
				case "5": //LOCAL
				          $this->desc_exp   = new Zend_Db_Expr("InfoLocales.nota");
					      $this->rooms_exp  = new Zend_Db_Expr("0");
                          $this->baths_exp  = new Zend_Db_Expr("0");
                          $this->prkg_exp   = new Zend_Db_Expr("InfoLocales.prkg");
					      $this->sqfeet_exp = new Zend_Db_Expr("InfoLocales.piesc");
                          $this->sqmt_exp   = new Zend_Db_Expr("0");
                          $this->comm_exp   = new Zend_Db_Expr("1");
                          $this->longt_exp  = new Zend_Db_Expr("0");
                          $this->shortt_exp  = new Zend_Db_Expr("0");
                          $this->tshare_exp  = new Zend_Db_Expr("0");
					      break;
					      
				case "6": //TERRENO
					      $this->rooms_exp  = new Zend_Db_Expr("0");
                          $this->baths_exp  = new Zend_Db_Expr("0");
                          $this->prkg_exp   = new Zend_Db_Expr("0");
                          $this->desc_exp   = new Zend_Db_Expr("InfoTerrenos.nota");
					      $this->sqfeet_exp = new Zend_Db_Expr("0");
                          $this->sqmt_exp   = new Zend_Db_Expr("InfoTerrenos.metrosc");
                          $this->comm_exp   = new Zend_Db_Expr("InfoTerrenos.terr_comercial");
                          $this->longt_exp  = new Zend_Db_Expr("0");
                          $this->shortt_exp  = new Zend_Db_Expr("0");
                          $this->tshare_exp  = new Zend_Db_Expr("0");
					      break;
				default:    
				
                          $this->desc_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
				                                                WHEN 1 THEN InfoApartamentos.nota
				                                                WHEN 2 THEN InfoCasas.nota
				                                                WHEN 3 THEN InfoOficinas.nota
                                                                WHEN 4 THEN InfoEdificios.nota
				                                                WHEN 5 THEN InfoLocales.nota
				                                                WHEN 6 THEN InfoTerrenos.nota
				                                                ELSE '' 
				                                              END");
				                                              
					      $this->rooms_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
			                                                    WHEN 1 THEN InfoApartamentos.cuartos 
			                                                    WHEN 2 THEN InfoCasas.cuartos 
				                                                ELSE 0 
				                                               END");
				                                               
                          $this->baths_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
					                                            WHEN 1 THEN InfoApartamentos.banos   
					                                            WHEN 2 THEN InfoCasas.banos   
					                                            WHEN 3 THEN InfoOficinas.banos 
					                                            WHEN 4 THEN InfoEdificios.banos 
					                                            ELSE 0 
					                                           END");
                          
                          $this->prkg_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
					                                            WHEN 1 THEN InfoApartamentos.prkg   
					                                            WHEN 2 THEN InfoCasas.prkg   
					                                            WHEN 3 THEN InfoOficinas.prkg 
                                                                WHEN 4 THEN InfoEdificios.prkg 
					                                            WHEN 5 THEN InfoLocales.prkg 
					                                            ELSE 0 
					                                           END");
					                                           
					      $this->sqfeet_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
					                                                WHEN 1 THEN InfoApartamentos.piesc
					                                                WHEN 2 THEN InfoCasas.piesc
					                                                WHEN 3 THEN InfoOficinas.piesc
                                                                    WHEN 4 THEN InfoEdificios.piesc
					                                                WHEN 5 THEN InfoLocales.piesc
					                                                ELSE 0 
					                                            END");
					                                            
                          $this->sqmt_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
					                                             WHEN 2 THEN InfoCasas.metrosc
                                                                 WHEN 6 THEN InfoTerrenos.metrosc
					                                             ELSE 0 
					                                          END");

                          $this->comm_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
				                                                WHEN 1 THEN 0
				                                                WHEN 2 THEN 0
				                                                WHEN 3 THEN 1
				                                                WHEN 4 THEN 1
				                                                WHEN 5 THEN 1
				                                                WHEN 6 THEN InfoTerrenos.terr_comercial
				                                                ELSE '' 
				                                              END");
                          $this->longt_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
				                                                WHEN 1 THEN TiposAlquilerPropiedades.long_term
				                                                ELSE 0
				                                              END");
                          $this->shortt_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
				                                                WHEN 1 THEN TiposAlquilerPropiedades.short_term
				                                                ELSE 0
				                                              END");
                          $this->tshare_exp = new Zend_Db_Expr("CASE Propiedades.id_cat 
				                                                WHEN 1 THEN TiposAlquilerPropiedades.time_share
				                                                ELSE 0
				                                              END");
					     break;
			}
        }
        /* -------------------------------------------------------- */
        protected function getSelect(){        

	  // exit;

            $db     = ListMax::db();
            $select = $db->select();
            			            						
            $category = $this->getFilterBy("cat");			            
            $this->setExpressions();
            
            $select->from("Propiedades", array( "id"                           , 
                                                "active"           => $this->active_exp ,
                                                "internal_num"     => "num_interno"    ,
										        "name"             => "nombre"         , 
								                "category"         => "id_cat"         , 
									            "subcategory"      => "id_subcat"      , 
									            "forsale"          => "sevende"        , 
									            "forrent"          => $this->forrent_exp , 
									            "price"            => "precio"         ,
									            "price_type"       => "tipo_precio"    ,
									            "rent"             => $this->rent_exp  , 
									            "rent_type"        => "tipo_precio_renta",
									            "expires"          => "expira"         , 
                                                "newly"            => "record_created" , 
                                                "updated"          => "last_update"    , 
									            "rooms"            => $this->rooms_exp ,
									            "baths"            => $this->baths_exp ,
                                                "parkings"         => $this->prkg_exp  ,
                                                "buisness_name"    => $this->bname_exp ,
                                                "buisness_logo"    => $this->blogo_exp ,
                                                "buisness_url"     => $this->burl_exp  ,

                                                "office_location"  => 'RBiz.office_loc' ,
                                                "office_phone"     => 'RBiz.tel'   ,
                                                "office_fax"       => 'RBiz.fax'   ,
                                                "office_address"   => 'RBiz.dir'   ,
                                                "office_email"     => 'RBiz.email_adm' ,

                                                "has_photo"        => $this->has_photo ,
                                                "description"      => $this->desc_exp ,
                                                "sqfeet"           => $this->sqfeet_exp ,
                                                "sqmt"             => $this->sqmt_exp ,
                                                "commercial"       => $this->comm_exp ,
                                                "openhouse"        => $this->openh_exp ,
                                                "reposessed"       => $this->repo_exp ,
                                                "has_video"        => $this->vid_exp ,
                                                "video_code"       => "YouTubeEmbedCodes.code" ,
                                                "neighborhood_lat" => "Comunidades.clati" ,
                                                "neighborhood_lng" => "Comunidades.clong" ,
                                                "longterm"         => $this->longt_exp ,
                                                "shortterm"        => $this->shortt_exp ,
                                                "timeshare"        => $this->tshare_exp
									           ));
                                    
			$select->join(    "Usuarios"          , "Propiedades.id_usuario   = Usuarios.id"                    , array( "is_realtor" => "realtor" , "broker_id" => "id" , "broker_name" => "nombre" , "broker_last_name" => "apellido" , "broker_email" => "email" , "broker_email2" => "email2" , "broker_tel1" => "tel1" , "broker_tel2" => "tel2" , "broker_tel3" => "tel3" , "broker_fax" =>  "fax"  ));
			$select->join(    "PueblosAreas"      , "Propiedades.id_pueblo    = PueblosAreas.id"                , array( "city" => "pueblo" , "area" , "region" ));
			$select->joinLeft("RealtorsInfo"      , "Usuarios.id              = RealtorsInfo.id_usuario"        , array( "broker_license"=> "lic" ) );
			$select->joinLeft("RBiz"              , "Usuarios.rbiz_id         = RBiz.id"                        , array( "buisness_id" => "id", "buisness_license" => "blic" , "buisness_tel" => "tel" , "buisness_fax" =>  "fax", "buisness_office_location" => "office_loc", "buisness_address" =>  "dir", "buisness_email" =>  "email_adm" ) );

            $select->joinLeft("bl_cases"          , "bl_cases.property_id = Propiedades.id"    , array( "second_broker_id" => "second_broker_id", "third_broker_id" =>"third_broker_id" ) );
            $select->joinLeft("lm_clients"        , "lm_clients.user_id = Usuarios.id AND lm_clients.type = 'Seller' AND bl_cases.client_id = lm_clients.id"    , array( "client_id" => "id" ) );

            $select->joinLeft( array("Broker2" => "Usuarios") , "bl_cases.second_broker_id   = Broker2.id"   , array( "is2_realtor" => "realtor" , "broker2_id" => "id" , "broker2_name" => "nombre" , "broker2_last_name" => "apellido" , "broker2_email" => "email" , "broker2_tel1" => "tel1" , "broker2_tel2" => "tel2" , "broker2_tel3" => "tel3" , "broker2_fax" =>  "fax"  ));
            $select->joinLeft( array("Realtors2Info" => "RealtorsInfo")      , "Broker2.id   = Realtors2Info.id_usuario"        , array( "broker2_license"=> "lic" ) );
            $select->joinLeft( array("Broker3" => "Usuarios") , "bl_cases.second_broker_id   = Broker3.id"   , array( "is3_realtor" => "realtor" , "broker3_id" => "id" , "broker3_name" => "nombre" , "broker3_last_name" => "apellido" , "broker3_email" => "email" , "broker3_tel1" => "tel1" , "broker3_tel2" => "tel2" , "broker3_tel3" => "tel3" , "broker3_fax" =>  "fax"  ));
            $select->joinLeft( array("Realtors3Info" => "RealtorsInfo")      , "Broker3.id   = Realtors3Info.id_usuario"        , array( "broker3_license"=> "lic" ) );

            $select->joinLeft("FotosPropiedades"  , "Propiedades.id           = FotosPropiedades.id_propiedad"  , array( "img1" , "img2" , "img3" , "img4" , "img5" , "img6" , "img7" , "img8" , "img9" , "img10" , "img11" , "img12" , "img13" , "img14" , "img15" , "img16" , "img17" , "img18" , "img19" , "img20" , "img21" , "img22" , "img23" , "img24" , "img25" ) );
			$select->joinLeft("Comunidades"       , "Propiedades.id_comunidad = Comunidades.id AND Comunidades.clati <> 0 AND Comunidades.clong <> 0 AND Comunidades.clati <> '' AND Comunidades.clong <> ''" , array( "neighborhood_id" => "id" , "neighborhood" => "Comunidades.comunidad" ));
            $select->joinLeft("Repo"              , "Propiedades.id           = Repo.id"                        , array() );
            $select->joinLeft("YouTubeEmbedCodes" , "Propiedades.id           = YouTubeEmbedCodes.id_propiedad" , array() );
            $select->joinLeft("OpenHouse"         , "Propiedades.id           = OpenHouse.id_propiedad"                  , array() );
			$select->joinLeft("PropiedadStatus"   , "Propiedades.id           = PropiedadStatus.id_propiedad"            , array('opcionada' => 'status') );

            $select->group("Propiedades.id");

            $category_filter  = $this->getFilterBy("cat");
            @list( $category , $sub_category ) = explode( ":" , $category_filter );

            switch( $category ){
				case "1": //APTS
					      $select->where("Propiedades.id_cat = 1");
					      $select->join("InfoApartamentos" , "Propiedades.id  = InfoApartamentos.id_propiedad" , array() );
                          $select->joinLeft("TiposAlquilerPropiedades" , "Propiedades.id  = TiposAlquilerPropiedades.id_propiedad" , array() );
					      break;
					      
				case "2": //HOUSES
					      $select->where("Propiedades.id_cat = 2");
					      $select->join("InfoCasas" , "Propiedades.id = InfoCasas.id_propiedad" , array() );
					      break;
					      
				case "3": //OFFICE
					      $select->where("Propiedades.id_cat = 3");
					      $select->join("InfoOficinas"     , "Propiedades.id           = InfoOficinas.id_propiedad"     , array() );
					      break;
					      
				case "4": //LOCAL
					      $select->where("Propiedades.id_cat = 4");
					      $select->join("InfoEdificios"    , "Propiedades.id           = InfoEdificios.id_propiedad"    , array() );
					      break;
					      
				case "5": //EDIFICIO
					      $select->where("Propiedades.id_cat = 5");
					      $select->join("InfoLocales"      , "Propiedades.id           = InfoLocales.id_propiedad"      , array() );
					      break;
					      
				case "6": //TERRENO
					      $select->where("Propiedades.id_cat = 6");
					      $select->join("InfoTerrenos"     , "Propiedades.id           = InfoTerrenos.id_propiedad"     , array() );
					      break;
					      
				default:  //ALL
					      $select->joinLeft("InfoApartamentos" , "Propiedades.id           = InfoApartamentos.id_propiedad" , array() );
					      $select->joinLeft("InfoCasas"        , "Propiedades.id           = InfoCasas.id_propiedad"        , array() );
					      $select->joinLeft("InfoOficinas"     , "Propiedades.id           = InfoOficinas.id_propiedad"     , array() );
					      $select->joinLeft("InfoEdificios"    , "Propiedades.id           = InfoEdificios.id_propiedad"    , array() );
					      $select->joinLeft("InfoLocales"      , "Propiedades.id           = InfoLocales.id_propiedad"      , array() );
					      $select->joinLeft("InfoTerrenos"     , "Propiedades.id           = InfoTerrenos.id_propiedad"     , array() );
                          $select->joinLeft("TiposAlquilerPropiedades" , "Propiedades.id   = TiposAlquilerPropiedades.id_propiedad" , array() );
					      break;
			}

            return $select;
        }
        /* -------------------------------------------------------- */
        protected function filter ( &$select ) {

            $db    = ListMax::db();
            
			// Expires
			if ( $this->isFilteredBy( "active" ) ) {
                $active = $this->getFilterBy("active");
                if( $active ){
    				$select->where("$this->active_exp");
                }else{
    				$select->where("not $this->active_exp");
                }
			}
			
            //Buy or Rent
            if( $this->isFilteredBy( "forsale" ) ){

                $forsale = $this->getFilterBy("forsale");
                
			    if( $forsale == 1 )
			         $select->where( "sevende = 1");
			    elseif( $forsale == 0 )
			         $select->where( $this->forrent_exp );

            }
			
            //Id
            if( $this->isFilteredBy("id") ){
                $id = $this->getFilterBy("id");
			    $select->where("Propiedades.id = ?" , $id );
			}
			
			//Commercial
            if( $this->isFilteredBy("commercial") ){
                $commercial = $this->getFilterBy("commercial");
                if( $commercial == 0  )
    			    $select->where("$this->comm_exp = ?" , 0 );
                elseif( $commercial == 1 )
    			    $select->where("$this->comm_exp = ?" , 1 );
			}
			
            //Price From
            if( $this->isFilteredBy( "pricefrom" ) ){

                $forsale    = $this->getFilterBy("forsale");
                $lowerValue = $this->getFilterBy("pricefrom");
                                    
                if( $forsale == "1" ){
                    $select->where("precio >= ?"                        , $lowerValue );
                }elseif( $forsale == "0" ){
                    $select->where("$this->rent_exp >= ?"               , $lowerValue );                
                }else{
                    $select->where("precio >= ? OR $this->rent_exp >=?" , $lowerValue );
                }
                
            }
            
            //Price To
            if( $this->isFilteredBy( "priceto" ) ){
                
                $forsale    = $this->getFilterBy("forsale");
                $upperValue = $this->getFilterBy("priceto");
                                    
                if( $forsale == "1" ){
                    $select->where("precio <= ?"                        , $upperValue );
                }elseif( $forsale == "0" ){
                    $select->where("$this->rent_exp <= ?"               , $upperValue );                
                }else{
                    $select->where("precio <= ? OR $this->rent_exp <=?" , $upperValue );
                }
                
            }
			
            //Category
            if( $this->isFilteredBy( "cat" ) ){            

                $category_filter = $this->getFilterBy("cat");
                @list( $category , $sub_category ) = explode( ":" , $category_filter );
                $select->where("id_cat = ?" , $category );

                if( $category == 2 && $sub_category )
    			    $select->where("InfoCasas.mf =1" );
                elseif( $sub_category )
                    $select->where("id_subcat = ?" , $sub_category );
                    
            }

            //Baths From
            if( $this->isFilteredBy( "bathfrom" ) ){
                $bath_from = $this->getFilterBy("bathfrom");
                if( $bath_from == 1 )
    			    $select->where( "$this->baths_exp = 1" );
                else
    			    $select->where( "$this->baths_exp >= ?" , $bath_from );
			}

            //Baths To
            if( $this->isFilteredBy( "bathto" ) ){
                $bath_to = $this->getFilterBy("bathto");
			    $select->where( "$this->baths_exp <= ?" , $bath_to );
			}
            
            //Rooms From
            if( $this->isFilteredBy( "roomsfrom" ) ){
                $rooms_from = $this->getFilterBy("roomsfrom");
                if( $rooms_from == 1 )
    			    $select->where( "$this->rooms_exp = 1" );
                else
    			    $select->where( "$this->rooms_exp >= ?" , $rooms_from );
			}

            //Rooms To
            if( $this->isFilteredBy( "roomsto" ) ){
                $rooms_to = $this->getFilterBy("roomsto");
			    $select->where( "$this->rooms_exp <= ?" , $rooms_to );
			}
            
            //Sq Feet From
            if( $this->isFilteredBy( "sqftfrom" ) ){
                $sqft_from = $this->getFilterBy("sqftfrom");
                $select->where("$this->sqfeet_exp >= ?" , $sqft_from );
            }
            
            //Sq Meters
            if( $this->isFilteredBy( "sqmtfrom" ) ){
                $sqmt_from = $this->getFilterBy("sqmtfrom");
                $select->where("$this->sqmt_exp >= ?" , $sqmt_from );
            }
            
            //Broker
            if( $this->isFilteredBy( "broker" ) ){
                $broker = $this->getFilterBy("broker");
                $select->where( "Propiedades.id_usuario = ?" , $broker );
            }
			
            //Brokers
            if( $this->isFilteredBy( "brokers" ) ){
                $brokers = $this->getFilterBy("brokers");
                $select->where( "RealtorsInfo.lic != '0' AND RealtorsInfo.lic != '' AND Usuarios.realtor = 1" , !!$brokers );
            }
			
            //Office
            if( $this->isFilteredBy( "office" ) ){
                $office = $this->getFilterBy("office");
                $select->where( "RBiz.id = ?" , $office );
            }
			
			// Business
			if( $this->isFilteredBy( "business" ) ){
				$businessLic  = $this->getFilterBy("business");
				$select->where( "RBiz.blic = ?" , $businessLic );				
            }
            
            //City
            if( $this->isFilteredBy( "city" ) ){

                $city     = $this->getFilterBy("city");
                $city_ids = array();
                
                if( !is_numeric( $city) ){
                                        
                    $search = "PueblosAreas.pueblo LIKE ? OR
                               PueblosAreas.area   LIKE ? OR
                               PueblosAreas.region LIKE ?"; 
                               
                    $cityselect = $db->select();
                    $cityselect->from(  "PueblosAreas" , "id" );                                           
                    $cityselect->where( $search  ,  $city , $city , $city ); 
                    $city_ids = $db->fetchCol( $cityselect );

                }else{
                	$city_ids = array( $city );                
                }
                
                $city_ids = empty( $city_ids ) ? 0 : implode( " , " , $city_ids );
                
                $search   = "PueblosAreas.id        IN( $city_ids ) OR
						     Comunidades.id_pueblo  IN( $city_ids ) OR
						     Comunidades.id_pueblo2 IN( $city_ids ) OR
						     Comunidades.id_pueblo3 IN( $city_ids )";
                $select->where( $search ); 
                                                                            
            }
                        
            //Neighboorhood
            if( $this->isFilteredBy( "neighborhood" ) ){
                
                $neighborhood = $this->getFilterBy("neighborhood");
                
                if( is_numeric( $neighborhood ) ){
    				$select->where( "Comunidades.id = ?" , $neighborhood );
                }else{

                    $search = "Comunidades.comunidad LIKE ? OR
                               Propiedades.nombre    LIKE ?";
				
				    $select->where( $search ,  '%'.$neighborhood.'%' , '%'.$neighborhood.'%' );   
				    
                }
            }

            // Open House
            if( $this->isFilteredBy( "openhouse" ) ){
                $openhouse = $this->getFilterBy("openhouse");
                $select->where("$this->openh_exp = ?" , $openhouse );
            }
            
            // Reposessed
            if( $this->isFilteredBy( "repo" ) ){
                $repo = $this->getFilterBy("repo");
                $select->where("$this->repo_exp");
            }
            
            // Has Photo
            if( $this->isFilteredBy( "has_photo" ) ){
                $has_photo = $this->getFilterBy("has_photo");
                $select->where("$this->has_photo");
            }
            
            // Has Video
            if( $this->isFilteredBy( "video" ) ){
                $video = $this->getFilterBy("video");
                $select->where("$this->vid_exp");
            }
            
            // Parkings
            if( $this->isFilteredBy( "parkings" ) ){
                $parkings = $this->getFilterBy("parkings");
			    $select->where( "$this->prkg_exp >= ?" , $parkings );
			}
            
            // Long Term
            if( $this->isFilteredBy( "longterm" ) ){
                $longterm = $this->getFilterBy("longterm");
			    $select->where( "$this->longt_exp >= ?" , $longterm );
			}
            
            // Short Term
            if( $this->isFilteredBy( "shortterm" ) ){
                $shortterm = $this->getFilterBy("shortterm");
			    $select->where( "$this->shortt_exp >= ?" , $shortterm );
			}
            
            // Time Share
            if( $this->isFilteredBy( "timeshare" ) ){
                $timeshare = $this->getFilterBy("timeshare");
			    $select->where( "$this->tshare_exp >= ?" , $timeshare );
			}
			
			//Location    
            $LocalizacionYCondicionesAttached = false;
            if( $this->isFilteredBy( "location" ) ){

                $LocalizacionYCondicionesAttached = true;            
                $location = $this->getFilterBy("location");
                $select->joinLeft("LocalizacionYCondiciones" , "Propiedades.id = LocalizacionYCondiciones.id_propiedad" , array() );
                
                switch( $location ){
                    case "beach":       $select->where("Propiedades.de_playa OR LocalizacionYCondiciones.loc_frente_mar");
                                        break;
                    case "urban":       $select->where("LocalizacionYCondiciones.loc_urbana"); 
                                        break;
                    case "rural":       $select->where("LocalizacionYCondiciones.loc_rural"); 
                                        break;
                    case "countryside": $select->where("Propiedades.de_campo");
                                        break;
                }
                
            }
            			
			//View
            if( $this->isFilteredBy( "vista" ) ){
            
                $view = $this->getFilterBy("vista");
                if( !$LocalizacionYCondicionesAttached )
                    $select->joinLeft("LocalizacionYCondiciones" , "Propiedades.id = LocalizacionYCondiciones.id_propiedad" , array() );
                
                switch( $view ){
                    case "sea":         $select->where("LocalizacionYCondiciones.loc_vista_mar");
                                        break;
                    case "lagoon":      $select->where("LocalizacionYCondiciones.loc_vista_laguna"); 
                                        break;
                    case "panoramic":   $select->where("LocalizacionYCondiciones.loc_vista_panoramica"); 
                                        break;
                }
                
            }

            //Keywords
            if( $this->isFilteredBy( "keywords" ) ){
                $keywords = $this->getFilterBy("keywords");
                $select->where("CONVERT( $this->desc_exp USING utf8 ) LIKE ? OR Propiedades.nombre LIKE ? OR Comunidades.comunidad LIKE ?" , "%".$keywords."%" , "%".$keywords."%" , "%".$keywords."%" );
            }

        }
        /* -------------------------------------------------------- */
        public function normalize_cat( $value ){
                                  
            global $LISTMAX_PROPERTY_CATEGORY_TO_ID_MAP;
            global $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP;
                                  
            $value = str_replace( " " , "-" , trim( strtolower( $value ) ) );
                                                
            if( isset( $LISTMAX_PROPERTY_CATEGORY_TO_ID_MAP[ $value ] ) ){
                return $LISTMAX_PROPERTY_CATEGORY_TO_ID_MAP[ $value ];
            }elseif( isset( $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $value ] ) ){
                return $value;
            }else{
                return "";
            }
                
        }
        /* -------------------------------------------------------- */
        function normalize_city( $value ){
            return ucwords( strtolower(trim($value)) );
        }
        /* -------------------------------------------------------- */
        function normalize_forsale( $value ){
            if( in_array( $value , array( '1' , 'compra' , 'venta' , 'compra-venta' , 'venta-compra' , 'sell' , 'buy' , 'sell-buy' , 'buy-sell' ))){
                return "1";
            }elseif( in_array( $value , array( '0' , 'renta' , 'alquiler' , 'renta-alquiler' , 'alquiler-renta' , 'rent' , 'lease' , 'rent-lease' , 'lease-rent' ))){
                return "0";
            }else{
                return "";
            }
        }    
    }
    
?>
