<?php

    class Helper_Table_Decorator_Seconds extends Helper_Table_Decorator{

        public function apply( $seconds ){    

            $result = array();
                
            if( empty( $seconds ) )
                return t("%0 seconds" , 0 );

            if( !is_numeric( $seconds ) )
                return t("%0 seconds" , $seconds );

            $days    = floor( $seconds / 86400 );
            $seconds = $seconds % (86400);
            
            $hours   = floor( $seconds / 3600 );
            $seconds = $seconds % (3600);
    
            $minutes = floor( $seconds / 60 );
            $seconds = $seconds % 60;

            $show_minutes = true;
            $show_seconds = true;
             
            if( $days ){
                if( $days == 1 )
                    $result[] = t("%0 day" , number_format( $days ) );
                else                    
                    $result[] = t("%0 days" , number_format( $days ) );

                $show_minutes = false;
                $show_seconds = true;                
            }
        
            if( $hours ){
                if( $hours == 1 )            
                    $result[] = t("%0 hour" , number_format( $hours ) );
                else                    
                    $result[] = t("%0 hours" , number_format( $hours ) );

                $show_seconds = false;                                
            }
        
            if( $show_minutes && $minutes ){
                if( $minutes == 1 )                        
                    $result[] = t("%0 minute" , number_format( $minutes ) );
                else                    
                    $result[] = t("%0 minutes" , number_format( $minutes ) );                
            }
    
            if( $show_seconds && $seconds ){
                if( $seconds == 1 )
                    $result[] = t("%0 second" , number_format( $seconds ) );
                else                    
                    $result[] = t("%0 seconds" , number_format( $seconds ) );                
            }
            
            return implode( ' ' , $result );
            
        }
    
    }
