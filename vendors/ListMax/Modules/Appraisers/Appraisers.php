<?php
    
    class Appraisers extends AppCollection{
          
        protected $itemClass  = "Appraiser";
        protected $tableName  = "Usuarios";
        protected $primaryKey = "id";
        protected $filters    = array( "name"     ,
                                       "lastname" ,  
                                       "city"
									  );
                                      
        protected $orders    = array( 
                                      "name"    => array( "trim(nombre)" , "trim(apellido)" )
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
                                              "fax" ,
                                              "photo_raw"     => new Zend_Db_Expr("IF( Usuarios.foto = '0'  , '' , CONCAT( '/fotos/fotos_agentes/'  , Usuarios.foto ) )") 
            ));

			$select->where("Usuarios.id IN     ( SELECT group_user.id_user FROM group_user where group_user.id_group = 62  )");
			$select->where("Usuarios.id NOT IN ( SELECT group_user.id_user FROM group_user where group_user.id_group = 106 )");
            return $select;
            
        }
        /* -------------------------------------------------------------- */
        protected function filter( &$select ){        

            //Name
            if( $this->isFilteredBy( "name" ) ){
                $name = $this->getFilterBy("name");                
                $select->where(" CONCAT( Usuarios.nombre , ' ' , Usuarios.apellido ) LIKE ? OR CONCAT( Usuarios.apellido , ' ' , Usuarios.nombre ) LIKE ? " , '%'.$name.'%' , '%'.$name.'%' );                
            }
             
            //LastName
            if( $this->isFilteredBy( "lastname" ) ){
                $lastname = $this->getFilterBy("lastname");                
                $select->where(" Usuarios.apellido  LIKE ? " , $lastname.'%' );                
            }
                                
        }

    }


?>
