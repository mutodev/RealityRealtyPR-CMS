<?php

    class NewDevelopment extends ListMax_AppCollectionItem{

        var $photos_cache = null;
        
		/* ------------------------------------------------ */    
        function field_photos(){

            $db    = ListMax::db();

            if( empty($this->id ) )
                return array();

            if( $this->photos_cache !== null )
                return $this->photos_cache;
                                
            $photos               = $db->fetchAll( "SELECT path FROM ProyectosNuevosImages WHERE project_id = ?" , $this->id );            
            $this->photos_cache   = array();

            foreach( $photos as $row ){            
                $photo = $row['path'];
                $this->photos_cache[] = new ListMax_CollectionItem( array(
                    "thumbnail" => "http://images.listmax.com/q:90/w:120/aoe:1/".$photo ,
                    "image"     => "http://images.listmax.com/q:90/w:800/".$photo      
                ));
            }
                    
                                                       
            return $this->photos_cache;
        }        
		/* ------------------------------------------------ */    
        function field_thumbnail(){
                                                        
            if( empty( $this->main_img ) ){
                return "";
            }else{
                return "http://images.listmax.com/q:90/w:120/aoe:1/".$this->main_img;
            }
            
        }
		/* ------------------------------------------------ */
        function field_bigThumbnail(){
                
            if( empty( $this->main_img ) ){
                return "";
            }else{
                return "http://images.listmax.com/q:90/w:400/aoe:1/".$this->main_img;
            }
            
        }
		/* ------------------------------------------------ */
        function field_location_path(){
        
            $components   = array();
            
            if( !empty($this->comunidad) && trim(strtolower($this->comunidad)) != trim(strtolower($this->name)) )
                $components[] = $this->comunidad;

            if( !empty($this->area))
                $components[] = $this->area;

            if( !empty($this->city))			
                $components[] = $this->city;

            if( !empty($this->country))
                $components[] = $this->country; 
            else
                $components[] = ListMax::t("Puerto Rico"); 
            		                        
            return $components;
                        
        }
      
    }

?>
