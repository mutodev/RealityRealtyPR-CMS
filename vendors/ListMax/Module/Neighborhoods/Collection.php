<?php

    class ListMax_Module_Neighborhoods_Collection extends ListMax_Core_Collection{

        protected $tableName  = "Comunidades";
        protected $primaryKey = "id";
        protected $filters = array(
            "name",
			'city',
			'withData'
        );

        protected $orders = array(
            "name" => "name"
        );

        /* --------------------------------------------- */
        function getSelect(){
            
            $db    = ListMax::db();
            $select = $db->select();
            $select->from("Comunidades" , array(
                    "id"       => "Comunidades.id" ,
                    "name"     => "Comunidades.comunidad" ,
                    "lat"      => "Comunidades.clati" ,
                    "lng"      => "Comunidades.clong" ,
                    "type"     => "ComunidadesTipos.name" , 
                    "city_id"  => "P1.id" ,
                    "city2_id" => "P2.id" ,
                    "city3_id" => "P3.id" ,
                    "city"     => "P1.pueblo" ,
                    "city2"    => "P2.pueblo" ,
                    "city3"    => "P3.pueblo"
            ));
            
            $select->joinLeft("ComunidadesTipos" , "Comunidades.type_id   = ComunidadesTipos.id"    , array() );
			$select->joinLeft('ComunidadesInfo'  , 'Comunidades.id = ComunidadesInfo.id_comunidad'  , array() );
			$select->joinLeft('ComunidadesFotos' , 'Comunidades.id = ComunidadesFotos.id_comunidad' , array() );
			
            $select->joinLeft( array("P1" => "PueblosAreas" ) , "Comunidades.id_pueblo  = P1.id"  , array() );
            $select->joinLeft( array("P2" => "PueblosAreas" ) , "Comunidades.id_pueblo2 = P2.id"  , array() );
            $select->joinLeft( array("P3" => "PueblosAreas" ) , "Comunidades.id_pueblo3 = P3.id"  , array() );            
            $select->where('pending = 0');
            $select->where('ComunidadesTipos.isDefault OR ComunidadesTipos.id IS NULL');
            $select->where('Comunidades.clati IS NOT NULL AND Comunidades.clong IS NOT NULL');
            $select->where('Comunidades.clati <> 0 AND Comunidades.clong <> 0');
            $select->where('Comunidades.clati <> "" AND Comunidades.clong <> ""');
            $select->group("Comunidades.id");            
			
						
            return $select;
            
        }
        /* --------------------------------------------- */
        function filter( &$select ){

            global $ListMax;
            $db  = ListMax::db();            
         
            //Name ---------------------------------
            if( $this->isFilteredBy("name") ){
			
                $keywords = $this->getFilterBy("name");
                                                                
                //Remove special characters
                $keywords = preg_replace("/[^A-Z0-9\s-]/i", ' ' , $keywords );
                
                //Remove extra space
                $keywords = preg_replace('/\s\s+/'       , ' ', $keywords);                

                //Check if keywords its an ud
                if( is_numeric( $keywords ) ){
                    $select->where('Comunidades.id = ?' , $keywords );
                }else{

                    //Split keywords
                    $keywords = explode( " ", $keywords );
                    
                    //Remove keywords of length < 3
                    foreach( $keywords as $i => $keyword ){
                        if( strlen( $keywords ) <= 3 )
                            unset( $keywords[ $i ] );
                    }
                    
                    //Search keywords                
                    $fields = array('Propiedades.nombre' , 'Comunidades.comunidad'  );                
                    $keywords = '%'.implode('%' , $keywords ).'%';
                    $select->where( 'Comunidades.comunidad like ?' , $keywords );

                }			
             
             }
             
            //City ----------------------------------
            if( $this->isFilteredBy("city") ){
                $city = $this->getFilterBy("city");
                                
                //Find the city                                
                if( is_numeric( $city ) ){
        			$select->where("P1.id = ? OR P2.id = ? OR P3.id = ?" , $city , $city , $city );
        		}else{

                    $city_row = $ListMax->query("SELECT * FROM PueblosAreas WHERE PueblosAreas.pueblo LIKE '".addslashes($city)."' OR PueblosAreas.area LIKE '".addslashes($city)."' ");
                    
                    if( empty( $city_row ) ){
                        $select->where( 0 );
                    }else{
                        $city_row = current( $city_row );
                        $id = !empty( $city_row['id_p'] ) ? $city_row['id_p'] : $city_row['id'];
            			$select->where("P1.id = ? OR P2.id = ? OR P3.id = ?" , $id , $id , $id );
                    }
        		        			
                }
            }
			
			//With Data ----------------------------------
            if( $this->isFilteredBy("withData") ){
                $withData = $this->getFilterBy("withData");
				if ($withData == 1) {
					$exprEdited = new Zend_DB_Expr("EXISTS( SELECT id FROM ComunidadesFotos WHERE ComunidadesFotos.id_comunidad = Comunidades.id) 
					OR EXISTS( SELECT id FROM ComunidadesInfo WHERE ComunidadesInfo.id_comunidad = Comunidades.id)");
					$select->where( $exprEdited );
				}
            }
                  
        }
        
    }
    
