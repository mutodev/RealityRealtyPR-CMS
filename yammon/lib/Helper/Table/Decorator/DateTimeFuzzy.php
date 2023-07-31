<?php

    class Helper_Table_Decorator_DateTimeFuzzy extends Helper_Table_Decorator{

        public function apply( $value ){
                
            $now     = mktime();
            $then    = is_numeric( $value ) ? $value : strtotime( $value );
            $past    = $then <= $now;            
            $seconds = abs($now - $then);
            $minutes = floor($seconds / 60);
            $hours   = floor($minutes / 60);
            $days    = floor($hours   / 24);
            $months  = $days < 30 ?  0 : abs(date('n' , $now ) - date('n' , $then ));
            $years   = abs(date('Y' , $now ) - date('Y' , $then ));


            $msg     = "";

            if( $past ){

                if( $years ){
                    $msg = t("%{years} years(s) ago");
                }elseif( $months ){
                    $msg = t("%{months} months(s) ago");            
                }elseif( $days ){
                    $msg = t("%{days} day(s) ago");
                }elseif( $hours ){
                    $msg = t("%{hours} hours(s) ago");                
                }elseif( $minutes ){
                    $msg = t("%{minutes} minutes(s) ago");
                }else{
                    $msg = t("seconds ago");
                }
            
            }else{

                if( $years ){
                    $msg = t("in %{years} years(s)");
                }elseif( $months ){
                    $msg = t("in %{months} months(s)");            
                }elseif( $days ){
                    $msg = t("in %{days} day(s)");
                }elseif( $hours ){
                    $msg = t("in %{hours} hours(s)");                
                }elseif( $minutes ){
                    $msg = t("in %{minutes} minutes(s)");
                }else{
                    $msg = t("in seconds");
                }

            }
            
            $template = new Template( $msg );
            $msg = $template->apply( array(
                "seconds" => $seconds ,
                "minutes" => $minutes ,
                "hours"   => $hours   ,
                "days"    => $days    ,
                "months"  => $months  ,
                "years"   => $years   ,
            ));
            
            return $msg;
            
        }
    
    }
