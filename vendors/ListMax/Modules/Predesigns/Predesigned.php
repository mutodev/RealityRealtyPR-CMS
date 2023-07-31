<?php

    class Predesigned extends ListMax_AppCollectionItem{
    

		/* ------------------------------------------------ */
        function field_photos(){

            $photos = array();
            
            if( empty($this->id ) )
                return array();

            for( $i = 1 ; $i <= 7 ; $i++ ){
            
                $photo = $this["foto$i"];
                
                if( empty( $photo ) )
                    continue;
            
                $photos[] = new ListMax_CollectionItem( array(
                    "thumbnail" => "http://images.listmax.com/w:90/aoe:1/".$photo ,
                    "image"     => "http://images.listmax.com/w:800/".$photo      
                ));
                
            }

            return $photos;
        }        
		/* ------------------------------------------------ */
        function field_blueprints(){

            $photos = array();
            
            if( empty($this->id ) )
                return array();

            for( $i = 1 ; $i <= 4 ; $i++ ){
            
                $photo = $this["plano$i"];
                
                if( empty( $photo ) )
                    continue;
            
                $photos[] = new ListMax_CollectionItem( array(
                    "thumbnail" => "http://images.listmax.com/w:90/aoe:1/".$photo ,
                    "image"     => "http://images.listmax.com/w:800/".$photo      
                ));
                
            }

            return $photos;
            
        }        
		/* ------------------------------------------------ */
        function field_thumbnail(){
                                                        
            if( empty( $this->foto1 ) ){
                return "";
            }else{
                return "http://images.listmax.com/w:90/aoe:1/".$this->foto1;
            }
            
        }
		/* ------------------------------------------------ */
        function field_bigThumbnail(){
                
            if( empty( $this->foto1 ) ){
                return "";
            }else{
                return "http://images.listmax.com/w:400/aoe:1/".$this->foto1;
            }
            
        }
		/* ------------------------------------------------ */
        function field_mainimg(){
                
            if( empty( $this->foto1 ) ){
                return "";
            }else{
                return "http://images.listmax.com/w:400/aoe:1/".$this->foto1;
            }          
        }
		/* ------------------------------------------------ */
		    
    }

?>
