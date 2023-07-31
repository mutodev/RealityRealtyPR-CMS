<?php


    class Template{

        protected $message;
        protected $callback;
        protected $parameters = array();
        protected $preserve   = true;
        protected $expand     = true;

        public static function create( $message = "" , $preserve = true , $callback = null )
        {
            return new Template($message, $preserve, $callback);
        }

        public function __construct( $message = "" , $preserve = true , $callback = null ){
            $this->message  = $message;
            $this->preserve = $preserve;
            $this->callback = $callback;
        }

        public function setMessage( $message ){
            return $this->message = $message;
        }

        public function getMessage(  ){
            return $this->message;
        }

        public function setPreserveTokens( $preserve = true ){
            $this->preserve = !!$preserve;
        }

        public function getPreserveTokens( ){
            return $this->preserve;
        }

        public function getCallback( ){
            return $this->callback;
        }

        public function setCallback( $callback ){
            return $this->callback = $callback;
        }

        public function setExpand( $expand = true ){
            $this->expand = !!$expand;
        }

        public function getExpand( ){
            return $this->expand;
        }

		public function getParameters( ){

		    if( !empty( $this->parameters ) )
		        return $this->parameters;

		    //Get the current message
		    $message = $this->message;

		    //Apply
			$this->apply( );

			//Restore Message
			$this->setMessage( $message );

			//Return Parameters
			return $this->parameters;
		}

		public function getParameterCount(){
			$parameters = $this->getParameters();
			return count( $parameters );
		}

        public function apply( $data = array() , $message = null, $returnStringRepresentation = false ){

            //Get the message
            if( $message === null ){
                $message = $this->message;
            }

            //Save the data
            $this->data       = $data;

		    //Reset the parameters
		    $this->parameters = array();

            //Apply data to the template
            $pattern = "
            /
			(?<escape>\\\\)?
			(?<expression>
                %
                (?<number>[0-9]+)
                |
                (
                    %
                    (\\\\)?
                    \{
                       ((?<function>[0-9A-Z_.-]+):)?
                       (?<source>[^{}]+)
                    (\\\\)?
                    \}
                )
            )
            /xi";

	        //Get the result
            $fn     = $returnStringRepresentation ? 'applymatch_export' : 'applymatch';
            $result = preg_replace_callback( $pattern , array( $this , $fn) , $message , -1 );

            //Save the message
            $this->message = $result;

            return $result;

        }

        public function applymatch_export( $matches ){
            return var_export($this->applymatch($matches), true);
        }

        public function applymatch( $matches ){

            $escapek  = 'escape';
            $exprk    = 'expression';
            $numk     = 'number';
            $funck    = 'function';
            $sourcek  = 'source';

            //If its an escape return without the slash
            if( !empty($matches[ $escapek ]) ){
            	return substr( $matches[0] , strlen( $matches[ $escapek ] ) );
            }

            //Get the value
            if( !empty($matches[ $funck ]) ){ //If it is a function

                //Get the function
                $function = $matches[ $funck  ];

                //Get the arguments
                $arguments = array();
                $str       = $matches[ $sourcek ];
                $len       = strlen( $str );
                $buffer    = null;
                $until     = null;
                $wait      = false;

                for( $i = 0 ; $i < $len ; $i++ ){
                    $char = $str{$i};

                    if( $char !== ',' && $wait ){
                        continue;
                    }elseif( $char === ',' && !$until ){
                        if( $buffer !== null )
                            $arguments[] = array( trim($buffer) , 'value');
                        $buffer = null;
                        $wait   = false;
                    }elseif( $char === $until ){

                        $type = 'quoted';

                        if( $buffer !== null )
                            $arguments[] = array( $buffer , $type);
                        else
                            $arguments[] = array( $buffer , $type);

                        $buffer = null;
                        $until  = null;
                        $wait   = true;
                    }elseif( $char == "'" || $char === '"' ){
                        $until = $char;
                        $buffer = null;
                    }else{
                        $buffer .= $char;
                    }
                }
                if( $buffer !== null )
                    $arguments[] = array( trim($buffer) , 'value');


                //Transform arguments
                $function_arguments = array();
                foreach( $arguments as $arg ){

                    list( $value , $type ) = $arg;
                    if( $type == 'value' ){
                        if( strpos( $value , '%' ) !== false ){
                            $value = preg_replace('/(\b|\s)%(.+?)(\b|\s)/' , '\1%{\2}\3' , $value );
                            $t     = new Template( $value , false , $this->callback );
                            $value = $t->apply( $this->data );
                        }else{
                            $value = $this->extractValue( $value );
                            if( $value instanceof Template_NoValue ){
                                $value = null;
                            }
                        }

                    }

                    $function_arguments[] = $value;
                }

                //Call function
                if( function_exists( $function ) ){
                    $return = call_user_func_array($function, $function_arguments);
                }else{
                    $return = null;
                }

            }else{

                //Get the source
                if( @$matches[ $numk ] != '' )
                    $source = $matches[ $numk ];
                else
                    $source = $matches[ $sourcek ];

                $return = $this->extractValue( $source );

                //Save the parameter
                $this->parameters[] = $source;

            }

            //If there is no value return it as is
            if( $return instanceof Template_NoValue ){
                if( $this->preserve )
                    return $matches[ $exprk ];
                else
                    return '';
            }

            //If there is a callback apply it to the result
            if( $this->callback ){
                if( is_callable( $this->callback ) ){
                    $return = call_user_func( $this->callback , $return );
                }else{
                    throw new Exception('Can\'t call callback function');
                }
            }

            //Return result
            if( is_string( $return ) )
                return trim($return);
            else
                return $return;

        }

        private function extractValue( $key  ){

            $object = $this->data;

            if( $key === "*")
                return $object;

            if( $object instanceOf Doctrine_Record ){

                //Remove the prefix from the key for doctrine
                $prefix = get_class( $object ) . ".";
                if( substr( $key , 0 , strlen( $prefix ) ) == $prefix )
                    $key = substr( $key , strlen( $prefix ) );

            }

            if( $this->getExpand() )
                $parts = explode( "." , $key );
            else
                $parts = array( $key );

            //Find the value
            while( ($part = array_shift( $parts ) ) !== null ){

                if( is_array( $object ) ){

                    if( array_key_exists( $part , $object ) )
                        $object = $object[ $part ];
                    else
                        return new Template_NoValue();

                }elseif( is_object($object) ){

                    try{

                        $vars    = get_object_vars( $object );
                        $methods = get_class_methods( $object );

                        if( ($object instanceof Doctrine_Record) &&
                             $object->getTable()->hasTemplate('Translatable') &&
                             $object->isTranslatableField( $part ) ){

                            $object = $object->getTranslatableValue( $part );

                        }elseif( array_key_exists($part, $vars) ){
                            $object = $vars[ $part ];
                        }elseif( in_array( $part , $methods ) ){
                            $object = call_user_func( array( $object , $part ) );
                        }elseif( $object instanceof ArrayAccess ){

                            @$val = $object->offsetGet( $part );
                            $set  = $object->offsetExists( $part );

                            if( $set || $val === null )
                                $object = $val;
                            else
                                return new Template_NoValue();

                        }elseif( in_array( '__get' , $methods ) ){

                            $set  = isset( $object->{ $part } );
                            @$val = $object->__get( $part );

                            if( $set || $val === null )
                                $object = $val;
                            else
                                return new Template_NoValue();

                        }else{
                            return new Template_NoValue();
                        }

                    }catch( Exception $ex ){
                        return new Template_NoValue();
                    }

                }else{
                    return new Template_NoValue();
                }

            }

            return $object;

        }

    }

