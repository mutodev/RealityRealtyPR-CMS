<?php

    class NewDevelopments extends ListMax_AppCollection{
          
        protected $filters    = array( "category" , "city" , "status" , "pricefrom" , "priceto" );
        protected $orders     = array( "name"    => "name" , "price" => array("starting_price" , "maximum_price") );
        protected $tableName  = "ProyectosNuevosProjects";
        protected $primaryKey = "id";
        protected $itemClass  = "NewDevelopment";


		/* ------------------------------------------------------------------ */           
        protected function getSelect(){        
         
            $db    = ListMax::db();
         
            $select = $db->select();
            $select->from("ProyectosNuevosProjects" , array( "id"   ,
                                                             "name" ,
                                                             "starting_price" ,
                                                             "maximum_price" ,
                                                             "main_img" ,
                                                             "description" ,
                                                             "amenities" ,
                                                             "location"  ,
															 "contact_email1",
															 "contact_email2",
                                                             "sales" => "salesinfo" ,
                                                      ));
            $select->join("ProyectosNuevosCategories" , "ProyectosNuevosProjects.category_id = ProyectosNuevosCategories.id" , array("category"   => "name" ) );
            $select->join("ProyectosNuevosStatuses"   , "ProyectosNuevosProjects.status_id   = ProyectosNuevosStatuses.id"   , array("status" => "name" ) );
            $select->join("PueblosAreas"              , "ProyectosNuevosProjects.area_id     = PueblosAreas.id"              , array("city" => "pueblo" , "area" ));
            $select->where("published = 1 AND distribute_to_coa = 1");
//print_r($select);
//exit;                        
            return $select;
        }
		/* ------------------------------------------------------------------ */           
        protected function filter( &$select ){
            
            //Status
            if( $this->isFilteredBy( "status" ) ){
                $status = $this->getFilterBy("status");
                $select->where("status_id = ?" , $status );
            }
            
            //Category
            if( $this->isFilteredBy( "category" ) ){
                $category = $this->getFilterBy("category");
                $select->where("category_id = ?" , $category );
            }
            
            //Price
            $pricefrom = $this->getFilterBy("pricefrom");
            $priceto   = $this->getFilterBy("priceto");
            
            if( $pricefrom != null && $priceto != null ){
                $min       = min( $priceto , $pricefrom );
                $max       = max( $priceto , $pricefrom );
                $pricefrom = $min;
                $priceto   = $max;
            }
            
            if( $pricefrom !== null && $priceto !== null ){
	            $select->where("(IF( maximum_price <> 0  , ( maximum_price  >= ? AND starting_price <= ? ) OR ( starting_price <= ? AND maximum_price >= ? ) , starting_price >= ? AND starting_price <= ? ))" , $priceto , $priceto , $pricefrom , $pricefrom , $pricefrom , $priceto );
            } elseif(  $pricefrom !== null && $priceto == null ) {
                $select->where("starting_price >= ?" , $pricefrom );
            } elseif( $pricefrom == null && $priceto !== null ) {
                $select->where("(IF( maximum_price <> 0 , starting_price <= ? OR maximum_price <= ?  , starting_price <= ? ))" , $priceto , $priceto , $priceto );
            }
                        
            //City
            if( $this->isFilteredBy( "city" ) ){
                                                
                $city = $this->getFilterBy("city");

                $re     = "'".trim($city)."'";
                $search = array(); 
                  
                //Search in city 
                $search[] = "PueblosAreas.pueblo LIKE $re"; 
                  
                //Search in area 
                $search[] = "PueblosAreas.area LIKE $re"; 
                  
                //Search in region 
                $search[] = "PueblosAreas.region LIKE $re"; 
                 
                $search = implode( " OR " , $search ); 
                $select->where( $search ); 

            }
        
            
        }

    }


?>
