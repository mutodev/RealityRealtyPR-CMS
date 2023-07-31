<?php
    
    class Auth_Authenticator_Error{
    
        protected $code;
        protected $message;
        
        const AMBIGUOUS         = 0;
        const USERNAME          = 1;
        const PASSWORD          = 2;
        const TO_MANY_TRIES     = 3;
        const INACTIVE          = 4;
        
        public function __construct( $code , $message = null ){
            $this->code    = $code;
            $this->message = $message;
        }
        
        public function getCode(){
            return $this->code;        
        }
        
        public function getMessage(){
        
            if( !empty( $this->message ) )
                return $this->message;
                
            switch( $this->code ){            
                case self::USERNAME:
                    return t('Invalid username/password');
                    break;
                case self::PASSWORD:
                    return t('Invalid username/password');
                    break;
                case self::TO_MANY_TRIES:
                    return t('You have to many unsucessfull login attempts');
                    break;                    
                case self::INACTIVE:
                    return t('Your account has been disabled');
                    break;
            }

            return t('There was an unexpected error');
        
        }

        public function __toString(){
            return $this->getMessage();
        }
        
    }