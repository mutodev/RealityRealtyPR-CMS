<?php

    class ListMax_Module_NewDevelopments_Collection extends ListMax_Core_Collection{
          
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
            $min_price = $this->getFilterBy("pricefrom");
            $max_price  = $this->getFilterBy("priceto");
                        
            if( $min_price != null && $max_price != null ){
                $min       = min( $min_price , $max_price );
                $max       = max( $min_price , $max_price );
                $min_price = (int)$min;
                $max_price = (int)$max;
            }

    	    //This codes asumes there will allways be a starting_price and that
    	    //maximum_price = 0 means there is no maximum_price
        	if( !empty($min_price) && empty($max_price))
	        	$select->where( "(IF( maximum_price <> 0  ,  $min_price <= maximum_price   , $min_price <= starting_price ))" );
        	elseif( empty($min_price) && !empty($max_price) ) 
	        	$select->where( "(IF( maximum_price <> 0  ,  $max_price >= starting_price  , $max_price >= starting_price ))" );	        	
    	    elseif( !empty($min_price) && !empty($max_price) )    	        	    
	        	$select->where( "(IF( maximum_price <> 0  ,  starting_price <=  $max_price  AND maximum_price >= $min_price , starting_price >= $min_price AND starting_price <= $max_price ))" );	        	
                        
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
