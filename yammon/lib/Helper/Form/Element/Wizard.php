<?php

    class Helper_Form_Element_Wizard extends Helper_Form_Element_Container{

        protected $stepped = false;
        protected $step;
        protected $identifier;
                
        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( "edit"            , false );
            $this->addOption( "identifier_name" , "wizard_identifier");
            $this->addOption( "step_name"       , "wizard_step");
            $this->addOption( "session_key"     , "wizard");   
            $this->setOption( "box_renderer"    , 'NoBox' );
        }
             
        protected function guessElementType( $name ){ 
        
            if( preg_match( '/^step/' , $name ) )
                return 'WizardStep';
            else
                return parent::guessElementType( $name );

        }                
             
        public function build(){
            parent::build();
                                    
            //Get the form
            $Form = $this->getForm();
            if( !$Form ) return;
            
            //Get the Options
            $indentifier_element = $this->getOption('identifier_name');
            $step_element        = $this->getOption('step_name');
                
            //Add the indetifier to the form
            $Form->add( array(
                'name' => $indentifier_element ,
                'type' => 'Hidden'
            ));
            
            //Add step tp the form
            $Form->add( array(
                'name' => $step_element ,
                'type' => 'Hidden'
            ));
                        
            if( !$Form->isSubmitted() ){
            
                //Start at step 0
                $this->step = 0;
                        
                //Create an identifier
                $this->identifier = md5( time() );
                
                //Save the identifier
                $Form->setValue( $indentifier_element , $this->identifier );
                $Form->setValue( $step_element        , $this->step       );
                                        
            }else{
            
                //Get the value of the identifier
                $this->identifier = $Form->getValue( $indentifier_element );
        
                //Get the value of the step
                $this->step       = $Form->getValue( $step_element );                
                            
            }

            //Load Values
            $this->loadValues();
                
        }

        public function setValue( $element_name = null , $value = null ){
           $ret = parent::setValue( $element_name , $value );
           $this->saveValues();
           return $ret;
        }
        

        public function setValues( $values ){
            $ret = parent::setValues( $values );
            $this->saveValues();
            return $ret;
        }

        public function loadValues(){
            $Form        = $this->getForm();    
            $session_key = $this->getOption('session_key');
            $count       = $this->getStepCount();
            $method      = $Form ? $Form->getOption('method') : 'POST';
            $input       = $method == 'POST' ? $_POST : $_GET;

            for( $step = 0 ; $step < $count ; $step++ ){
                $values    = Session::read( $session_key . "." . $this->identifier . '.'. $step , array() );
                $container = $this->getStepContainer( $step );
                if( !input( $input , $container->getFullName() ) )
                    $container->setValues( $values );
            }        
        }
        
        public function saveValues(){
            //Refresh the session
            $session_key = $this->getOption('session_key');            
            $count       = $this->getStepCount();            
            for( $step = 0 ; $step < $count ; $step++ ){
                $container = $this->getStepContainer( $step );
                $values    = $container->getValues( );
                Session::write( $session_key . "." . $this->identifier . '.'. $step , $values ); 
            }        
        }

        public function isEdit(){
            return $this->getOption("edit");
        }

        public function getIdentifier(){
            return $this->getValue("identifier");
        }


        public function goNext( ){
            $this->goStep( $this->step + 1 );
        }

        public function goPrevious( ){
            $this->goStep( $this->step - 1 );
        }
        
        public function goStep( $step ){

            $count = $this->getStepCount();
        
            if( $step < 0 )
                $step = 0;
            elseif( $step > $count - 1){
                $step = $count - 1;
            }
            
            $this->step = $step;
                   
            //Update the step
            $Form =               $this->getForm();
            if( !$Form )         return;
            $step_element        = $this->getOption('step_name');
            unset( $_POST[ $Form->getName() ][  $step_element ] );
            $Form->setValue( $step_element , $this->step );
                   
        }
    
        public function getStep(){
            $this->step();
            return $this->step;        
        }

        public function isStep( $step ){
            $this->step();        
            return $this->getStep() == $step;        
        }

        public function isLastStep( ){
            $this->step();        
            return $this->getStep() == $this->getStepCount() - 1;        
        }

        public function getStepCount(){
            $count = 0;
            foreach( $this->getElements() as $element )
                if( $element instanceof Helper_Form_Element_WizardStep )
                    $count++;
            return $count;
        }
    
        public function getStepContainer( $step = null ){
        
            if( $step === null ){
                $step = $this->getStep();
            }
        
            $i = 0;
            foreach( $this->getElements() as $element ){
                if( $element instanceof Helper_Form_Element_WizardStep ){
                    if( $step == $i ){
                        return $element;
                    }
                    $i++;
                }                
            }
        
            return null;
        
        }

        public function step(){
                
            //Check if we have already stepped
            if( $this->stepped  )
                return true;
        
            $this->stepped = true;        
                
            //Get the form
            $Form = $this->getForm();
            if( !$Form ){
                return false;
            }
            
            //Get the step container
            $step = $this->getStepContainer();            

            //If we are moving backwards
            if( $Form->isSubmitted("previous") ){
                $this->goPrevious();
                $this->saveValues();
                return false;
            }
        
            //If we are moving foward
            if( $Form->isSubmitted("next") ){
            
                //Save data for step
                $values = $step->getValues();

                $session_key = $this->getOption('session_key');
                Session::write( $session_key ."." . $this->identifier . "." . $this->step , $values );
            
                if( $step->isValid() ){
                    $this->goNext();
                    return true;
                }
                
            }

            return false;
        
        }
    
        
        public function isValid(){
                
            //Step
            $this->step();            
                
            //Get the form
            $Form = $this->getForm();
            if( !$Form ) return false;                
                
            //Get the step container
            $step = $this->getStepContainer();            
                                
            //Validate
            if( $Form->isSubmitted("save") ){

                //Save data for step
                $values = $step->getValues();
                $session_key = $this->getOption('session_key');
                Session::write( $session_key ."." . $this->identifier . "." . $this->step , $values );

                //Validate all steps
                $valid = true;
                $count = $this->getStepCount();
                for( $i = 0 ; $i < $count ; $i++ ){
                    $step = $this->getStepContainer( $i );
                    if( !$step->isValid() ){
                        $valid = false;
                    }
                }

                return $valid;
            
            }

            return false;
        
        }
    
        public function render(){
                            
            //Get the step container
            $step = $this->getStepContainer();
                        
            list( $type , $options ) = $this->getRendererOptions( 'Layout' , array() );
            return Helper_Form_Renderer_Layout::factory( $type )->render( $step , $options );
            
            return $content;
        }
    
    }
