<?php
    
    class Brokers extends AppCollection{
          
        protected $itemClass  = "Broker";
        protected $tableName  = "Usuarios";
        protected $primaryKey = "id";
        protected $filters    = array( "name" , 
									   "company" ,
                                       "city" , 
                                       "ranking" , 
                                       "photo",
									   "business" ,
									   "paying" ,
									   "hasActiveProperties"
									  );
                                      
        protected $orders    = array( 
                                      "name"    => array( "trim(nombre)" , "trim(apellido)" ) ,
                                      "ranking" => "ranking"
                                    );

        /* -------------------------------------------------------------- */
        protected function getSelect(){        
         
            
            $db     = ListMax::db();
            $select = $db->select();
            
            $select->from("Usuarios" , array( "id" ,
                                              "first_name" => "nombre"   ,
                                              "last_name"  => "apellido" ,
                                              "email",
                                              "tel1",
                                              "tel2",
                                              "tel3",
                                              "fax",
                                              "business_name" => new Zend_Db_Expr("IF( RBiz.id IS NOT NULL  , RBiz.rbiz_name , RealtorsInfo.negocio )") ,
                                              "logo_raw"      => new Zend_Db_Expr("IF( RBiz.id IS NOT NULL AND RBiz.logo != '', 
                                                                                       RBiz.logo , 
                                                                                   IF( RealtorsInfo.id_usuario AND RealtorsInfo.logo_path != '' , 
                                                                                       CONCAT( '/fotos/logos_realtors/' , RealtorsInfo.logo_path ) , 
                                                                                       ''
                                                                                   ))") ,
                                              "photo_raw"     => new Zend_Db_Expr("IF( Usuarios.foto = '0'  , ''             , CONCAT( '/fotos/fotos_agentes/'  , Usuarios.foto ) )") , 
                                              "url_raw"       => new Zend_Db_Expr("IF( RBiz.id IS NOT NULL , RBiz.url        , RealtorsInfo.url )") ,
                                              "ranking"       
            ));

			$select->join( "RealtorsInfo" , "Usuarios.id = RealtorsInfo.id_usuario" , array( "license"  => "lic" , 
			                                                                                 "address"  => "direccion"
			                                                                                ));                                                                                 
            $select->joinLeft("RBiz" , "Usuarios.rbiz_id = RBiz.id" , array( "business_license" => "blic" , 
                                                                             "business_tel"     => "tel"  , 
                                                                             "business_fax"     => "fax" ));
		
			            					
			$select->where("nombre <> '' and nombre <> '0' and Usuarios.realtor = 1 ");
			$select->where("RealtorsInfo.lic <> '0' and RealtorsInfo.lic <> ''");			
			
			         			            						            			            
            return $select;
        }
        /* -------------------------------------------------------------- */
        protected function filter( &$select ){        

            //Name
            if( $this->isFilteredBy( "name" ) ){
                $name = $this->getFilterBy("name");                
                $select->where(" CONCAT( Usuarios.nombre , ' ' , Usuarios.apellido ) LIKE ? OR CONCAT( Usuarios.apellido , ' ' , Usuarios.nombre ) LIKE ? " , '%'.$name.'%' , '%'.$name.'%' );
                $select->orWhere( "RBiz.id              LIKE ? " , '%'.$name.'%' );
                $select->orWhere( "RealtorsInfo.negocio LIKE ? " , '%'.$name.'%' );
                                
            }

            //Company
            if( $this->isFilteredBy( "company" ) ){
                $company = $this->getFilterBy("company");                
                $select->where("IF( RBiz.id IS NOT NULL  , RBiz.rbiz_name , RealtorsInfo.negocio ) LIKE ? " , '%'.$company.'%' );                
            }

            //Paying
            if( $this->isFilteredBy( "paying" ) ){

                $active = $this->getFilterBy("paying"); 

                if( !empty($active) )
                    $select->where("Usuarios.id IN     ( SELECT group_user.id_user FROM group_user WHERE id_group IN( 5 , 7 , 9 , 41 , 46 , 54 , 66 , 122 ) )" );
                else
                    $select->where("Usuarios.id NOT IN ( SELECT group_user.id_user FROM group_user WHERE id_group IN( 5 , 7 , 9 , 41 , 46 , 54 , 66 , 122 ) )" );

            }
            
            //Has Active Properties
            if( $this->isFilteredBy( "hasActiveProperties" ) ){

                $active = $this->getFilterBy("hasActiveProperties"); 
                                
                if( !empty($active) )
                    $select->where("EXISTS(     SELECT Propiedades.id FROM Propiedades WHERE Propiedades.id_usuario = Usuarios.id AND expira >= ".date('Ymd').")" );
                else
                    $select->where("NOT EXISTS( SELECT Propiedades.id FROM Propiedades WHERE Propiedades.id_usuario = Usuarios.id AND expira >= ".date('Ymd').")" );
                                                 
            }

            //City
            if( $this->isFilteredBy( "city" ) ){
                                                
                $city = $this->getFilterBy("city");
                $re   = "'".trim($city)."'";
                
                $select->where("EXISTS( 
                                SELECT     Propiedades.id
                                FROM       Propiedades 
                                INNER JOIN PueblosAreas ON Propiedades.id_pueblo = PueblosAreas.id
                                WHERE 
                                Propiedades.id_usuario = Usuarios.id AND
                                expira >= ".date('Ymd')."            AND
                                ( PueblosAreas.pueblo LIKE $re OR PueblosAreas.area LIKE $re OR PueblosAreas.region LIKE $re ))"
                );

            }
            
            //Ranking
            if( $this->isFilteredBy( "ranking" ) ){
                $ranking = $this->getFilterBy("ranking");
                $select->where("ranking > ?" , $ranking );
            }

            //Photo
            if( $this->isFilteredBy( "photo" ) ){
                $photo = $this->getFilterBy("photo");
                if( !empty( $photo ) )
                    $select->where("Usuarios.foto <> '0'" );
            }
			
			//Business
            if( $this->isFilteredBy( "business" ) ){
                $businessId = $this->getFilterBy("business");
				$select->where("RBiz.blic = ?", $businessId);
            }
                                
        }

    }


?>
