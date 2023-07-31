<?php

    class Broker extends AppCollectionItem{    

        /* ------------------------------------- */
        function field_name(){    
            return $this->first_name." ".$this->last_name;
        }
        /* ------------------------------------- */
        function field_telephones(){

            $phones = array();

            if( !empty( $this->tel1 ) )
                $phones[] = listmax_telephone($this->tel1);
            
            if( !empty( $this->tel2 ) )
                $phones[] = listmax_telephone($this->tel2);

            if( !empty( $this->tel3 ) )
                $phones[] = listmax_telephone($this->tel3);

            if( !empty( $this->business_tel ) )
                $phones[] = listmax_telephone($this->business_tel);
   
            return array_unique( $phones );
            
        }        
        /* ------------------------------------- */
        function field_faxes(){

            $phones = array();

            if( !empty( $this->fax ) )
                $phones[] = listmax_telephone($this->fax);
            
            if( !empty( $this->business_fax ) )
                $phones[] = listmax_telephone($this->business_fax);
   
            return array_unique( $phones );
            
        }                
        /* ------------------------------------- */
        function field_logo(){    
        
            $url = ListMax::config("image_url");
        
            if( $this->logo_raw{0} != "/" )
                $url .= "/";
        
            if( !empty( $this->logo_raw ) ){
                return $url.$this->logo_raw;
            }else{
                return "";
            }
        }
        /* ------------------------------------- */
        function field_photo(){    
        
            $url = ListMax::config("image_url");
        
            if( !empty( $this->photo_raw ) ){
                return $url.$this->photo_raw;            
            }else{
                return "";                
            }
        }
        /* ------------------------------------- */
        function field_website(){            
        
            if( empty( $this->url_raw ) ){
                return "";
            }elseif( strtolower(substr( trim($this->url_raw) , 0 , 4 )) != "http" ){
                return "http://".$this->url_raw;
            }else{
                return $this->url_raw;
            }
                   
        }
        /* ------------------------------------- */
            
    }

?>
