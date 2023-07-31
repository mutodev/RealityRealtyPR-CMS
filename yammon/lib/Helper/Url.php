<?php

    class Helper_Url extends Helper{

        public function url( $action = null ){
            $args = func_get_args();
            return call_user_func_array( array("Router" , "url" ) , $args );        
        }

        public function link( $text , $path , $options = array() ){

            $url = $this->url( $path , $options );
                        
            if( isset( $options['confirm'] ) ){
                $confirm_message = empty($options['confirm']) ? t('Are you sure?') : $options['confirm'];
                $options['onclick'] = "if( !confirm('".add_slashes($confirm_message)."')) return false;" . isset($options['onclick']) ? $options['onclick'] : '';
                unset( $options['confirm'] );
            }
            
            $options['href'] = $url;
            return helper('Html')->tag('a' , $options , $text );
        }
 
        public function mailto( $email , $options = array() ){
            $options['href'] = "mailto:$email";
            return helper('Html')->tag('a' , $options , $email );
        }

    }
