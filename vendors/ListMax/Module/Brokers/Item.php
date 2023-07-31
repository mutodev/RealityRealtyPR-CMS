<?php

    class ListMax_Module_Brokers_Item extends ListMax_Core_Collection_Item{

        /* ------------------------------------- */
        function field_name(){    
            return $this->first_name." ".$this->last_name;
        }
        /* ------------------------------------- */
        function field_telephones(){

            $phones = array();

            if( !empty( $this->tel1 ) )
                $phones[] = ListMax_Core_Filter_Telephone::apply($this->tel1);
            
            if( !empty( $this->tel2 ) )
                $phones[] = ListMax_Core_Filter_Telephone::apply($this->tel2);

            if( !empty( $this->tel3 ) )
                $phones[] = ListMax_Core_Filter_Telephone::apply($this->tel3);

            if( !empty( $this->business_tel ) )
                $phones[] = ListMax_Core_Filter_Telephone::apply($this->business_tel);
   
            return array_unique( $phones );
            
        }        
        /* ------------------------------------- */
        function field_faxes(){

            $phones = array();

            if( !empty( $this->fax ) )
                $phones[] = ListMax_Core_Filter_Telephone::apply($this->fax);
            
            if( !empty( $this->business_fax ) )
                $phones[] = ListMax_Core_Filter_Telephone::apply($this->business_fax);
   
            return array_unique( $phones );
            
        }                
        /* ------------------------------------- */
        function field_logo(){    
        
            $url = "http://www.compraoalquila.com.pr";
        
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
        
            $url = "http://www.compraoalquila.com.pr";
        
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
