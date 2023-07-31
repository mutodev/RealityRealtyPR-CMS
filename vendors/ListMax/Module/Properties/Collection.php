<?php

    require_once( dirname( __FILE__ ). DIRECTORY_SEPARATOR . 'constants.php' );

    class ListMax_Module_Properties_Collection extends ListMax_Core_Collection{
        
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
		private $opt_exp;
        
        protected $tableName     = "Propiedades";
        protected $primaryKey    = "id";
        protected $itemClass     = "Property";
                        
        protected $filters = array(
            "id"           ,
            "bathfrom"     ,
            "bathto"       ,
            "category"     ,
            "commercial"   ,
            "short_sale"   ,
            "residential"  ,
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
			"keywords"     ,
			"featured"	   ,
			"updated"      ,
			"auction"      ,
      "type"      ,
        );        
        
        protected $orders = array(
            "name"   => "name",
            "rooms"   => "rooms",
            "baths"   => "baths",
            "newly"   => "newly",
            "updated" => "updated" ,
	        "nextopen" => "OpenHouse.fecha" ,
            "rand"    => "rand"
        );
       
        public function order_price(){
			
			$return = array();
			if( $this->getFilterBy('forsale') == '0' ){
				$return = "rent_sort";
			}elseif( $this->getFilterBy('forsale') == '1' ){	
				$return =  "price_sort";
			}else{
				$return =  array( 'rent_sort' , 'price_sort' );
			}
			
			return $return;

		}
		
        protected function orderByRand( &$select ){
            $select->order( 'random' );        
        }		

        /* -------------------------------------------------------- */
        private function setExpressions () {

            $this->vid_exp        = new Zend_Db_Expr("YouTubeEmbedCodes.id IS NOT NULL");
            $this->repo_exp       = new Zend_Db_Expr("Repo.id IS NOT NULL");
            $this->openh_exp      = new Zend_Db_Expr("DATEDIFF( CURRENT_DATE , STR_TO_DATE( OpenHouse.fecha , GET_FORMAT(DATE,'INTERNAL')) ) BETWEEN 0 AND 14");
            $this->bname_exp      = new Zend_Db_Expr("IF( RBiz.id IS NOT NULL , RBiz.rbiz_name , RealtorsInfo.negocio )");
            $this->blogo_exp      = new Zend_Db_Expr("IF( RBiz.id IS NOT NULL , RBiz.logo      , CONCAT( '/fotos/logos_realtors/' , RealtorsInfo.logo_path ) )");
            $this->burl_exp       = new Zend_Db_Expr("IF( RBiz.id IS NOT NULL , RBiz.url       , RealtorsInfo.url )");
            $this->has_photo      = new Zend_Db_Expr("FotosPropiedades.img1 != '0'");
            $this->active_exp     = new Zend_Db_Expr("expira >= ".date('Ymd'));
			      $this->forrent_exp    = new Zend_DB_Expr("Propiedades.sealquila OR ( NOT Propiedades.sealquila AND NOT Propiedades.sevende )");
            $this->rent_exp       = new Zend_DB_Expr("IF( Propiedades.sealquila , Propiedades.precio_renta , IF( Propiedades.sevende ,  0 , Propiedades.precio ))" ); 
            $this->opt_exp        = new Zend_DB_Expr("PropiedadStatus.status = 1" ); 
            $this->beach_exp      = new Zend_DB_Expr("Propiedades.de_playa OR LocalizacionYCondiciones.loc_frente_mar" );                                  
            
            $this->desc_exp = new Zend_Db_Expr("Info.nota");
                                        
            $this->rooms_exp = new Zend_Db_Expr("Info.cuartos");
                                         
            $this->baths_exp = new Zend_Db_Expr("Info.banos");

            $this->prkg_exp = new Zend_Db_Expr("Info.notaprkg");
                                       
            $this->sqfeet_exp = new Zend_Db_Expr("Info.piesc");
                                        
            $this->sqmt_exp = new Zend_Db_Expr("Info.metrosc");

            $this->comm_exp = new Zend_Db_Expr("CASE Categories.type 
                                          WHEN 'residential' THEN 0
                                          WHEN 'commercial' THEN 1
                                          ELSE '0' 
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
        }

        /* -------------------------------------------------------- */        
        public function fetchById( $id , $cache = "" , $active = true ){
                        
            global $ListMax;
                        
            if( $active ){                      
                $select = $this->getSelect(  );
    			$pk     = empty($this->tableName) ? $this->primaryKey : $this->tableName.".".$this->primaryKey;
            }else{
                $select = $this->getSelect2( false );
    			$pk     = 'Propiedades'.".".$this->primaryKey;
            }
            
            $select->where("$pk = ?" , $id );
            
            $result = $ListMax->query( $select , $this->tableName , $cache );
                                                                   
            if( empty( $result ) )
                return null;
            else
    			return $this->getItem( current($result) );
                        
        }        
        /* -------------------------------------------------------- */
        protected function getSelect2(  ){
                   
               
            static $create_view = true;
            static $i = 0;
            /*
            if( !Listmax::config('master' ) ){
                $create_view = false;                
            }                       
            */            
            //Get the Materialized view name
            $mv_name     = "Propiedades_mv";
            $mv_name_log = "Propiedades_mv_log";
            $mv_time     = 60*15;                        
   
            //Get the current time
            $now = mktime();

            //Get the normal select        
            $select = $this->getSelect2();
            
            //Get the Column Names
            $columns = $select->getPart('columns');
            $column_names = array();
            foreach( $columns as $v ){    
                $column_name    = !empty( $v[2] ) ? $v[2] : $v[1];
                $column_names[] = $column_name;
            }
           
            
            if( $create_view ){
                                        
                //Create Materialized View
                $sql   = array();
                $sql[] = "CREATE TABLE IF NOT EXISTS `$mv_name` (";
                foreach( $column_names as $v ){
                
                  //Get the type
                  if( in_array( $v , array('id','expires'))){              
                     $type = 'int(11) unsigned NOT NULL';
                  }elseif( in_array( $v , array('bath','rooms' , 'sqfeet' , 'sqmt' , 'parkings' ))){              
                     $type = 'int(11) unsigned';
                  }elseif( in_array( $v , array('for_sale','for_rent'))){              
                     $type = 'tinyint(1) NOT NULL';                  
                  }elseif( in_array( $v , array('price' , 'price_sort' , 'rent' , 'rent_sort' ))){
                    $type  = 'decimal(10,2) DEFAULT NULL';
                  }elseif( in_array( $v , array('description' , 'video_code' ))){
                     $type = 'TEXT';
                  }elseif( in_array( $v , array('random'))){
                     $type = 'FLOAT';                  
                  }else{
                     $type = 'varchar(255)';              
                  }
                
                  $sql[] = "   `$v` $type ,";
                }

                $sql[]=  "   KEY `expires` (`expires`),";
                $sql[]=  "   KEY `forsale` (`expires`,`forsale`,`price`),";
                $sql[]=  "   KEY `forrent` (`expires`,`forrent`,`rent`),";                
                $sql[]=  "   KEY `price_sort` (`expires`,`price_sort`),";
                $sql[]=  "   KEY `rent_sort` (`expires`,`rent_sort`),";
                $sql[]=  "   KEY `newly` ( `expires`, `newly`),"; 
                $sql[]=  "   KEY `random` (`expires`,`random`),";                 
                $sql[] = "   PRIMARY KEY (`id`)";
                $sql[] = ") ENGINE=MyISAM DEFAULT CHARSET=latin1;";
                $sql   = implode( "\n" , $sql );
    
                ListMax::query( $sql , null , null );
                        
                //Create Materialized View Log
                $sql   = array();
                $sql[] = "CREATE TABLE IF NOT EXISTS `".$mv_name_log."` (";
                    $sql[] = "  `date` Timestamp ";
                $sql[] = ");";
                $sql   = implode( "\n" , $sql );
                ListMax::query( $sql , null , null );
                
                //Get the last log entry
                $sql          = "SELECT date 
                                 FROM $mv_name_log 
                                 ORDER BY date DESC LIMIT 1";
                $last_refresh = ListMax::query( $sql , null , null );
                $last_refresh = isset( $last_refresh[0]['date'] ) ?  strtotime($last_refresh[0]['date']) : 0;

                //Update the values
                if( $now - $last_refresh > $mv_time ){
    
                    //Save to the log
                    $sql   = "INSERT INTO `$mv_name_log`( `date` ) 
                              VALUES( '".date('Y-m-d H:i:s' , $now )."' )";
                    ListMax::query( $sql , null , null );
    
                    //Get changed rows
                    $sql = "SELECT id
                            FROM Propiedades 
                            WHERE  expira > 100
                            AND    ( last_update >= '".date('Y-m-d H:i:s' , $last_refresh - 5*24*60*60 )."'
                            OR record_created >= '".date('Y-m-d H:i:s' , $last_refresh - 5*24*60*60 )."')";
    
                    $changed = ListMax::query( $sql , null , null );   
                    foreach( $changed as $k => $v ){
                        $changed[$k] = $v['id'];
                    }
                    
                    $changed[] = 0;                
                    $changed   = implode(",",$changed);
          
                    //Delete old rows
                    $sql = "DELETE FROM `$mv_name`
                            WHERE ( 
                              (Propiedades_mv.expires ) < ".date('Ymd' , $now )."
                              OR Propiedades_mv.id IN ( $changed )
                            )"; 
                                   
                   ListMax::query( $sql , null , null );                
                    
                    //Add new rows
                    $select->where("Propiedades.id IN( $changed )" );   
                    $select->group("Propiedades.id");      
                    
                    $sql   = array();
                    $sql[] = "INSERT INTO `$mv_name` ";
                    $sql[] = (string)$select;
                    $sql   = implode( "\n" , $sql );

    //                try{
                        ListMax::query( $sql , null , null );                
    //                }catch( Exception $ex ){
    //                    return $select;
    //                }
                    
                    
                    //This is a hack
                    $fields = "key=fixEMFCUKytrue4";
                    $url = "https://cms.realityrealtypr.com/cjobs/fix-exp-hack.php";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_exec($ch);
                    curl_close ($ch);                       
                    
                    //Update random number
                    $sql = "UPDATE Propiedades_mv SET random = RAND()";
                    ListMax::query( $sql , null , null );                                          
                    
                }
            
                $create_view = false;
            
         
            
            
            }
            
            $db        = ListMax::db();
            $newselect = $db->select(); 
            $newselect->from($mv_name , $column_names );            
            
            return $newselect;
           
        }
        /* -------------------------------------------------------- */        
        public function getSelect( $active = true ){   

            $db     = ListMax::db();
            $select = $db->select();
            			            									            
            $this->setExpressions();
                        
            $select->from("Propiedades", array( "id"                           , 
                                                "active"           => $this->active_exp ,
										        "name"             => "nombre"         , 
								                "category"         => "Categories.name"         , 
                                                "category_type"    => "Categories.type"         , 
								                "category_id"      => "category_id"         , 
									            "subcategory"      => "id_subcat"      , 
									            "subcategory_id"   => "id_subcat"      , 
									            "forsale"          => "sevende"        , 
                                                "notas"            => "Info.nota",
									            "forrent"          => $this->forrent_exp , 
									            "price"            => "precio"         ,
                                                "price_sort"       => new Zend_Db_Expr("IF( precio = 0 , 999999999 , precio )") ,
									            "price_type"       => "tipo_precio"    ,
									            "rent"             => $this->rent_exp  , 
                                                "rent_sort"        => new Zend_Db_Expr("IF( ".$this->rent_exp." = 0 , 999999999 , ".$this->rent_exp." )") ,									            
									            "rent_type"        => "tipo_precio_renta",
									            "rent_long_term"   => $this->longt_exp ,
                                                "rent_short_term"  => $this->shortt_exp ,
                                                "rent_time_share"  => $this->tshare_exp ,
									            "expires"          => "expira"         , 
                                                "newly"            => "record_created" , 
                                                "updated"          => "last_update"    , 
                                                "catastro"         => "catastro",
                                                "short_sale"         => "short_sale",
                                                "num_cpr"         => "num_cpr",
                                                "num_co"         => "num_co",
                                                "num_mls"         => "num_mls",
									            "rooms"            => 'Info.cuartos' ,
									            "baths"            => 'Info.banos',
                              "category_name" => 'Categories.name',
                              "category_id" => 'Propiedades.category_id',
                                                "parkings"         => 'Info.prkg'  ,
                                                "buisness_name"    => $this->bname_exp ,
                                                "buisness_logo"    => $this->blogo_exp ,
                                                "buisness_url"     => $this->burl_exp  ,
                                                "has_photo"        => $this->has_photo ,
                                                "description"      => 'Info.nota' ,
                                                "sqfeet"           => 'Info.piesc' ,
                                                "sqmt"             => 'Info.metrosc' ,
                                                "commercial"       => $this->comm_exp ,
                                                "openhouse"        => $this->openh_exp ,
                                                "reposessed"       => $this->repo_exp ,
                                                "has_video"        => $this->vid_exp ,
                                                "video_code"       => "YouTubeEmbedCodes.code" ,
                                                "neighborhood_lat" => "Comunidades.clati" ,
                                                "neighborhood_lng" => "Comunidades.clong" ,
                                                "longterm"         => $this->longt_exp ,
                                                "shortterm"        => $this->shortt_exp ,
                                                "timeshare"        => $this->tshare_exp ,
                                                "opcionada"        => $this->opt_exp ,
                                                "location_country" => "de_campo" ,
                                                "location_beach"   => $this->beach_exp ,
                                                "vacational"       => "vacational",
                                                "plati"            => "plati",
                                                "plong"            => "plong", 
                                                "auction"          => "subasta",
                                                "internal_num"     => "num_interno",
                                                "random"           => new Zend_Db_Expr("NULL")                                                
									           ));
                                    
			$select->join(    "Usuarios"                   , "Propiedades.id_usuario   = Usuarios.id" , 
			array( "is_realtor"       => "realtor" , 
			       "broker_id"        => "id" , 
			       "broker_is_agent"  => new Zend_Db_Expr("RealtorsInfo.lic != '0' AND RealtorsInfo.lic != '' AND Usuarios.realtor = 1") ,
			       "broker_name"      => "nombre" , 
			       "broker_last_name" => "apellido" , 
			       "broker_email"     => "email"  , 
			       "broker_email2"    => "email2" , 			       
			       "broker_tel1"      => "tel1" , 
			       "broker_tel2"      => "tel2" , 
			       "broker_tel3"      => "tel3" , 
			       "broker_fax"       => "fax"  
            ));
            
			$select->join(    "PueblosAreas"               , "Propiedades.id_pueblo    = PueblosAreas.id"                , array( "area_id" => "id" , "city" => "pueblo" , "area" , "region" ));
			$select->joinLeft("RealtorsInfo"               , "Usuarios.id              = RealtorsInfo.id_usuario"        , array( "broker_license"=> "lic" ) );
			$select->joinLeft("RBiz"                       , "Usuarios.rbiz_id         = RBiz.id"                        , array( "buisness_id" => "id", "buisness_license" => "blic" , "buisness_tel" => "tel" , "buisness_fax" =>  "fax", "buisness_location" => "office_loc" ) );
			$select->joinLeft("FotosPropiedades"           , "Propiedades.id           = FotosPropiedades.id_propiedad"  , array( "img1" , "img2" , "img3" , "img4" , "img5" , "img6" , "img7" , "img8" , "img9" , "img10" , "img11" , "img12" , "img13" , "img14" , "img15" , "img16" , "img17" , "img18" , "img19" , "img20" , "img21" , "img22" , "img23" , "img24" , "img25" ) );
			$select->joinLeft("Comunidades"                , "Propiedades.id_comunidad = Comunidades.id AND Comunidades.clati <> 0 AND Comunidades.clong <> 0 AND Comunidades.clati <> '' AND Comunidades.clong <> ''" , array( "neighborhood_id" => "id" , "neighborhood" => "Comunidades.comunidad" , "neighborhood_city1" => "Comunidades.id_pueblo" , "neighborhood_city2" => "Comunidades.id_pueblo2" , "neighborhood_city3" => "Comunidades.id_pueblo3" ));
            $select->joinLeft("Repo"                       , "Propiedades.id           = Repo.id"                        , array() );
            $select->joinLeft("YouTubeEmbedCodes"          , "Propiedades.id           = YouTubeEmbedCodes.id_propiedad" , array() );
            $select->joinLeft("OpenHouse"                  , "Propiedades.id           = OpenHouse.id_propiedad"         , array() );
			$select->joinLeft("PropiedadStatus"            , "Propiedades.id           = PropiedadStatus.id_propiedad"   , array( 'status' ) );
			$select->joinLeft("LocalizacionYCondiciones"   , "Propiedades.id           = LocalizacionYCondiciones.id_propiedad" , array() );
            $select->joinLeft("Info"        , "Propiedades.id           = Info.id_propiedad"        , array("multifamily" => "mf", "house_type" => "casa_tipo","floor_level" => "piso" ) );
            $select->joinLeft("Categories"        , "Propiedades.category_id           = Categories.id"        , array() );
            $select->joinLeft("TiposAlquilerPropiedades" , "Propiedades.id   = TiposAlquilerPropiedades.id_propiedad" , array() );

            $select->joinLeft("EnglishDescription"   , "Propiedades.id  = EnglishDescription.id_propiedad"    , array( "eng_description"=> "description" ) );
            $select->joinLeft("PropiedadesDir"   , "Propiedades.id  = PropiedadesDir.id_propiedad"    , array( "street" => "calle",'notas_cortas'=>'notas_cortas', 
                                                                                                                "address"=> "dir",
                                                                                                                "ste" => "suite",
                                                                                                                "zip_code" => "zip" ) );
            $select->joinLeft("FeaturesPropiedades"   , "Propiedades.id  = FeaturesPropiedades.id_propiedad"    , 
                                                                    array(  "estufa_gas"    => "estufa_gas", 
                                                                            "estufa_electrica" => "estufa_electrica", 
                                                                            "microhonda" => "microhonda",
                                                                            "lava_platos" => "lava_platos", 
                                                                            "nevera" => "nevera", 
                                                                            "horno" => "horno", 
                                                                            "triturador" => "triturador",
                                                                            "lavadora" => "lavadora", 
                                                                            "secadora" => "secadora",
                                                                            "calentador" => "calentador", 
                                                                            "calentador_solar" => "calentador_solar",
                                                                            "abanicos" => "abanicos", 
                                                                            "aire_central" => "aire_central", 
                                                                            "aire_ventana" => "aire_ventana", 
                                                                             "aire_consola" => "aire_consola",
                                                                             "piscina" => "piscina",
                                                                             "terraza_concreto" => "terraza_concreto",
                                                                             "terraza_concreto_madera" => "terraza_concreto_madera",
                                                                             "terraza_madera" => "terraza_madera",
                                                                             "balcon" => "balcon",
                                                                             "pisos_marmol" => "pisos_marmol",
                                                                             "ceramica" => "ceramica",
                                                                             "amueblado" => "amueblado",
                                                                             "sistema_alarma" => "sistema_alarma",
                                                                             "planta_electrica" => "planta_electrica",
                                                                             "landscaping" => "landscaping",
                                                                             "tormenteras" => "tormenteras",
                                                                             "sisterna" => "sisterna",
                                                                             "area_juegos_ninos" => "area_juegos_ninos",
                                                                             ) );
                                                                                                                                             
            if( $active ){
                $select->where(" ( expira >= '".date('Ymd')."' OR expira = '".date('ymd')."' OR expira = '".date('ymd', mktime(0,0,0,date('m'),date('d')-1,date('Y')) )."')");
            }
            
            return $select;
        }
        /* -------------------------------------------------------- */
        protected function filter ( &$select ) {

            $db    = ListMax::db();
                        
			// Expires
			if ( $this->isFilteredBy( "active" ) ) {
			    $active = $this->isFilteredBy( "active" );
			    if( $active ){
    			    $select->where( "expira >= ?" , date('Ymd') );
                }else{
    			    $select->where( "expira < ?" , date('Ymd') );                
                }
			}
										
            //Buy or Rent
            if( $this->isFilteredBy( "forsale" ) ){

                $forsale = $this->getFilterBy("forsale");
                
			    if( $forsale == 1 )
			         $select->where( "sevende = 1");
			    elseif( $forsale == 0 )
			         $select->where( "sealquila = 1");

            }

			//Featured
            if( $this->isFilteredBy( "featured" ) ){

                $featured = $this->getFilterBy("featured");

                if( !empty($featured) )
                    $select->where("broker_id IN ( SELECT group_user.id_user FROM group_user WHERE id_group IN( 5 , 7 ) )" );
                    //Falta comprar las fechas que se halla creado en mas de 3 meses
                    //$select->where("record_created ");
                else
                    $select->where("broker_id NOT IN ( SELECT group_user.id_user FROM group_user WHERE id_group IN( 5 , 7 ) )" );

            }
			
            //Id
            if( $this->isFilteredBy("id") ){
                $id = $this->getFilterBy("id");
			    $select->where("(Propiedades.id = ? OR Propiedades.num_mls = ? OR Propiedades.num_co = ? OR Propiedades.num_cpr = ? OR Propiedades.num_interno = ?)" , $id );
			}
			
			//Commercial
            if( $this->isFilteredBy("commercial") ){
                $commercial = $this->getFilterBy("commercial");
                $select->where( "commercial = ?" , $commercial ? "1" : "0" );
			}

      //Short Sale
            if( $this->isFilteredBy("short_sale") ){
                $short_sale = $this->getFilterBy("short_sale");
                if($short_sale)
                    $select->where( "rr_id_departamento = ?" , 3 );
                else
                    $select->where( "rr_id_departamento != ?" , 3 );
      }
			
			//Residential
            if( $this->isFilteredBy("residential") ){
                $residential = $this->getFilterBy("residential");
                $select->where( "category IN (1,2)" );
			}
			
            //Price From
            if( $this->isFilteredBy( "pricefrom" ) ){

                $forsale    = $this->getFilterBy("forsale");
                $lowerValue = $this->getFilterBy("pricefrom");
                                    
                if( $forsale == "1" ){
                    $select->where("Propiedades.precio >= ?"                  , $lowerValue );
                }elseif( $forsale == "0" ){
                    $select->where("Propiedades.precio_renta  >= ?"                  , $lowerValue );                
                }else{
                    $select->where("Propiedades.precio >= ? OR Propiedades.precio_renta >= ?" , $lowerValue );
                }
                
            }
            
            //Price To
            if( $this->isFilteredBy( "priceto" ) ){
                
                $forsale    = $this->getFilterBy("forsale");
                $upperValue = $this->getFilterBy("priceto");
                                    
                if( $forsale == "1" ){
                    $select->where("Propiedades.precio <= ?"                 , $upperValue );
                }elseif( $forsale == "0" ){
                    $select->where("Propiedades.precio_renta <= ?"                  , $upperValue );                
                }else{
                    $select->where("Propiedades.precio <= ? OR Propiedades.precio_renta <= ?" , $upperValue );
                }
                
            }

            //Category
            if( $this->isFilteredBy( "category" ) ){            

                $category = $this->getFilterBy("category");

                @list($type, $category) = explode(':', $category);

                if($category){
                    $select->where("category_id = ?" , $category );
                }elseif($type){
                    $select->where("Categories.type = ?" , $type );
                }
            }

            //Category
            if( $this->isFilteredBy( "type" ) ){            

                $type = $this->getFilterBy("type");
                $select->where("Categories.type = ?" , $type );
                    
            }

            //Baths From
            if( $this->isFilteredBy( "bathfrom" ) ){
                $bath_from = $this->getFilterBy("bathfrom");
                if( $bath_from == 1 )
    			    $select->where( "baths = 1" );
                else
    			    $select->where( "baths >= ?" , $bath_from );
			}

            //Baths To
            if( $this->isFilteredBy( "bathto" ) ){
                $bath_to = $this->getFilterBy("bathto");
			    $select->where( "baths <= ?" , $bath_to );
			}
            
            //Rooms From
            if( $this->isFilteredBy( "roomsfrom" ) ){
                $rooms_from = $this->getFilterBy("roomsfrom");
                if( $rooms_from == 1 )
    			    $select->where( "rooms = 1" );
                else
    			    $select->where( "rooms >= ?" , $rooms_from );
			}

            //Rooms To
            if( $this->isFilteredBy( "roomsto" ) ){
                $rooms_to = $this->getFilterBy("roomsto");
			    $select->where( "rooms <= ?" , $rooms_to );
			}
            
            //Sq Feet From
            if( $this->isFilteredBy( "sqftfrom" ) ){
                $sqft_from = $this->getFilterBy("sqftfrom");
                $select->where("sqfeet >= ?" , $sqft_from );
            }
            
            //Sq Meters
            if( $this->isFilteredBy( "sqmtfrom" ) ){
                $sqmt_from = $this->getFilterBy("sqmtfrom");
                $select->where("sqmt >= ?" , $sqmt_from );
            }
            
            //Broker
            if( $this->isFilteredBy( "broker" ) ){
                $broker = $this->getFilterBy("broker");
                $select->where( "Propiedades.id_usuario = ?" , $broker );
            }
			
            //Brokers
            if( $this->isFilteredBy( "brokers" ) ){
                $brokers = $this->getFilterBy("brokers");
                $select->where( "broker_is_agent = ?" , $brokers ? "1" : "0" );
            }
            
            //Office
            if( $this->isFilteredBy( "office" ) ){
                $office = $this->getFilterBy("office");
                $select->where( "buisness_id = ?" , $office );
            }
			
			// Business
			if( $this->isFilteredBy( "business" ) ){
			    $lic = $this->getFilterBy("business");
                $select->where( "RBiz.blic = ?" , $lic );
            }
            
            //Keywords
            if( $this->isFilteredBy( "keywords" ) ){

                $keywords = $this->getFilterBy("keywords");
                if( is_numeric( $keywords ) )
                    $select->where('id = ?' , $keywords );       
            }
            
            if( $this->isFilteredBy( "neighborhood" ) ){

                $keywords = $this->getFilterBy("neighborhood");
                if( is_numeric( $keywords ) )
                    $select->where('Comunidades.comunidad = ?' , $keywords );       
            }            
            
            //Neighboorhood
            if( $this->isFilteredBy( "keywords" ) )
                $keywords = $this->getFilterBy("keywords");
            elseif( $this->isFilteredBy( "neighborhood" ) )                 
                $keywords = $this->getFilterBy("neighborhood");                 
            else
                $keywords = null;

            if( $keywords && !is_numeric( $keywords) ){
                          
                //Remove special characters
                $keywords = preg_replace("/[^A-Z0-9\s-]/i", ' ' , $keywords );
                
                //Remove extra space
                $keywords = preg_replace('/\s\s+/'       , ' ', $keywords);                

                //Split keywords
                $keywords = explode( " ", $keywords );
                
                //Remove keywords of length < 3
                foreach( $keywords as $i => $keyword ){
                    if( strlen( $keyword ) <= 3 )
                        unset( $keywords[ $i ] );
                }
                
                //Search keywords                
                $fields = array('Propiedades.nombre' , 'Comunidades.comunidad'  );                
                $keywords = $db->quote( '%'.implode('%' , $keywords ).'%' );
                $where    = array();
                foreach( $fields as $field ){
                    $where[] = "$field LIKE $keywords";
                }   
                $where = "( ".implode(" OR " , $where )." )";
                                    
                $select->where( $where );
                    
            }            
            
            //City
            if( $this->isFilteredBy( "city" ) ){

                $city       = $this->getFilterBy("city");
                $city_ids   = $this->getAllCityIds( $city );
                $city_ids   = implode("," , $city_ids );
                
                $search     = "Propiedades.id_pueblo             IN( $city_ids ) OR Propiedades.id_area             IN( $city_ids )";
                $select->where( $search );
                                             
            }

            // Open House
            if( $this->isFilteredBy( "openhouse" ) ){
                $openhouse = $this->getFilterBy("openhouse");
                $select->where( "openhouse = ?" , $openhouse ? "1" : "0" );
            }
            
            // Reposessed
            if( $this->isFilteredBy( "repo" ) ){
                $repo = $this->getFilterBy("repo");
                if($repo)
                    $select->where( "Propiedades.rr_id_departamento = 1");
                else
                    $select->where( "Propiedades.rr_id_departamento != 1");
            }
            
            // Has Photo            
            if( $this->isFilteredBy( "has_photo" ) ){            
                $has_photo = $this->getFilterBy("has_photo");
                if( $has_photo )
                    $select->where("(img1 <> '0' AND img1 <> '')");
                else                    
                    $select->where("NOT (img1 <> '0' AND img1 <> '')");                
            }
            
            // Has Video
            if( $this->isFilteredBy( "video" ) ){
                $video = $this->getFilterBy("video");
                if( $video )
                    $select->where("( video_code <> '' AND video_code IS NOT NULL )");
                else                    
                    $select->where("NOT ( video_code <> '' AND video_code IS NOT NULL )");                
            }
            
            // Parkings
            if( $this->isFilteredBy( "parkings" ) ){
                $parkings = $this->getFilterBy("parkings");
			    $select->where( "parkings >= ?" , $parkings );
			}
            
            // Long Term
            if( $this->isFilteredBy( "longterm" ) ){
                $longterm = $this->getFilterBy("longterm");
			    $select->where( "rent_long_term = ?" , $longterm ? "1" : "0"  );
			}
            
            // Short Term
            if( $this->isFilteredBy( "shortterm" ) ){
                $shortterm = $this->getFilterBy("shortterm");
			    $select->where( "rent_short_term = ?" , $shortterm ? "1" : "0" );
			}
            
            // Time Share
            if( $this->isFilteredBy( "timeshare" ) ){
                $timeshare = $this->getFilterBy("timeshare");
			    $select->where( "rent_time_share = ?" , $timeshare ? "1" : "0" );
			}
			
			//Location    
            if( $this->isFilteredBy( "location" ) ){
/*
                $location = $this->getFilterBy("location");
                
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
*/              
            }

/*
                    case "beach":       $select->where("Propiedades.de_playa OR LocalizacionYCondiciones.loc_frente_mar");
                                        break;
                    case "urban":       $select->where("LocalizacionYCondiciones.loc_urbana"); 
                                        break;
                    case "rural":       $select->where("LocalizacionYCondiciones.loc_rural"); 
                                        break;
                    case "countryside": $select->where("Propiedades.de_campo");
                                        break;

                    case "sea":         $select->where("LocalizacionYCondiciones.loc_vista_mar");
                                        break;
                    case "lagoon":      $select->where("LocalizacionYCondiciones.loc_vista_laguna"); 
                                        break;
                    case "panoramic":   $select->where("LocalizacionYCondiciones.loc_vista_panoramica"); 
                                        break;
*/
            			
			//View
            if( $this->isFilteredBy( "vista" ) ){
/*          
                $view = $this->getFilterBy("vista");
                
                switch( $view ){
                    case "sea":         $select->where("LocalizacionYCondiciones.loc_vista_mar");
                                        break;
                    case "lagoon":      $select->where("LocalizacionYCondiciones.loc_vista_laguna"); 
                                        break;
                    case "panoramic":   $select->where("LocalizacionYCondiciones.loc_vista_panoramica"); 
                                        break;
                }
*/              
            } 
            
            if( $this->isFilteredBy( "updated" ) ){

                $updated = $this->getFilterBy("updated");
                $select->where("newly >= ? OR updated = ?", $updated );

            } 
                                  
        }
        /* -------------------------------------------------------- */
        protected function getAllCityIds( $city ){
        
            $db  = ListMax::db();
            $ids = array();
                        
            if( !is_numeric( $city) ){
                                                               
                $select = $db->select();
                $select->from(  "PueblosAreas" , "id" );                                           
                $select->where( "pueblo LIKE ? OR area LIKE ? OR region LIKE ?"  ,  $city , $city , $city );
                                
                $result = ListMax::query( $select );
                foreach( $result as $k => $v ){
                    $ids[] = $v['id'];
                }

            }else{
                $ids = array( $city );                
            }
                                            
            //Get the children for the city
            if( count( $ids ) ){
                $select = $db->select();
                $select->from(  "PueblosAreas" , "id" );                                           
                $select->where( "id_p IN( ".implode( " , " , $ids )." )" ); 
        
                $result = ListMax::query( $select );
                foreach( $result as $k => $v ){
                    $ids[] = $v['id'];
                }
        
            }
                
            return $ids;
        
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
        /* -------------------------------------------------------- */
        public function getCities(){
        
            global $ListMax;

            //Get the current filters        
            $filters    = $this->getFilters();
  
            //Create a new Property Object          
            $Properties = $ListMax->get('Properties');
            $Properties->filterBy( $filters );

            //Transform the select
            $select     = $Properties->getSelect();
            $select->group( "area_id");
            $select->order( array("city" , "area" ) );
            $Properties->filter( $select );

            //Get the data      
            $data = $ListMax->query( $select , $this->tableName , '' );
    
            $cities = array();
            foreach( $data as $row ){

                if( !empty($row["area_parent_id"]) ){
                    $cities[ $row["area_parent_id"] ] = $row["city"];
                }
            
                if( !empty( $row["area"] ) ){
                    $cities[ $row["area_id"] ] = "- " . $row["area"];
                }else{
                    $cities[ $row["area_id"] ] = $row["city"];                
                }
            
            }
    
            return $cities;        
        }  
        /* -------------------------------------------------------- */
        public function getCategories(){

            global $ListMax;
            global $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP;

            //Get the current filters        
            $filters    = $this->getFilters();
  
            //Create a new Property Object          
            $Properties = $ListMax->get('Properties');
            $Properties->filterBy( $filters );

            //Transform the select
            $select     = $Properties->getSelect();
            $select->group( array("category" , "subcategory" ) );
            $Properties->filter( $select );
  
            //Get the data      
            $data = $ListMax->query( $select , $this->tableName , '' );
    
            $categories = array();
            foreach( $data as $row ){

                $category_id    = $row["category"];
                $subcategory_id = "";
                if( !empty($row["subcategory"]) ){
                    $subcategory_id = $row["category"].":".$row["subcategory"];
                }
                

                $category                   = $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $category_id ];
                $subcategory                = $LISTMAX_PROPERTY_ID_TO_CATEGORY_MAP[ $subcategory_id ];

                $categories[ $category_id ] = $category;
                if( $subcategory ){
                    $categories[ $subcategory_id ] = "$category &gt; $subcategory";                
                }
                            
            }
        
            return $categories;        
        
        }        
    
    }

