<?php

    class Helper_Form_Element_Autocomplete extends Helper_Form_Element_Sourced{


        public function setupOptions(){
            parent::setupOptions();
            $this->addOption( 'checked'    , null );
            $this->addOption( 'readonly'   , null );
            $this->addOption( 'size'       , null );
            $this->addOption( 'minlength'  , 1 );
            $this->addOption( 'remote'     , true );
            $this->addOption( 'free'       , false );
        }

        public function construct(){

            parent::construct();

            $Javascript = helper('Javascript');
            $Javascript->add("/yammon/public/widget/widget.js");
            $Javascript->add("/yammon/public/widget/autocomplete/BGIframe.js");
            $Javascript->add("/yammon/public/widget/autocomplete/Meio.Autocomplete.js");
            $Javascript->add("/yammon/public/widget/autocomplete/autocomplete.js");

            $Css = helper('Css');
            $Css->add("/yammon/public/widget/autocomplete/meio.aucomplete.css");

            Event::connect( "form.handle" , array( $this , "handle" ) );
        }


        public function handle(){

           if( !isset( $_SERVER['HTTP_X_YAMMON_REQUEST']) )
                return;

           if( $_SERVER['HTTP_X_YAMMON_REQUEST'] != 'HELPER_FORM_ELEMENT_AUTOCOMPLETE' )
                return;

           if( !$this->matchRequestId( $_SERVER['HTTP_X_YAMMON_REQUEST_ID'] ) )
                return;

           $value     = get('__autocomplete');

           $values    = array();
           $possibles = $this->getPossibleValues( $value );
           foreach( $possibles as $k => $v ){
                $values[] = array( 'id' => $k , 'value' => $v );
           }
           echo json_encode( $values );
           exit();

        }

        public function matchRequestId( $id ) {

            $domId = strtolower($this->getDomId());
            $regex = '/^' . str_replace('__template__', '__[0-9]{1,}__', $domId) . '$/i';

            return preg_match($regex, $id);
        }

        public function getValue(){

            $free     = $this->getOption('free');
            $validate = $this->getOption('source_validate');

            if( $free ){
                return Helper_Form_Element_Valued::getValue();
            }else{
                return parent::getValue();
            }



        }

        public function renderStart( )
        {

        }

        public function renderBody( ){

            $remote = $this->getOption('remote');

            //Create autocomplete configuration
            $conf   = array();
            $conf['minLength'] = $this->getOption('minlength');

            if( !$remote ){
                $conf['values']    = $this->getPossibleValues();
            }

            //Render the element
            $id  = $this->getDomId() ;
            $hid = $id."_id";
            $name = $this->getDomName();
            $value = $this->getValue();
            $free = $this->getOption('free');

            $this->addAttribute( 'id'                  , $id );
            $this->addAttribute( 'type'                , $this->getOption('type') );
            $this->addAttribute( 'checked'             , $this->getOption('checked') );
            $this->addAttribute( 'disabled'            , $this->getOption('disabled') );
            $this->addAttribute( 'maxlength'           , $this->getOption('maxlength') );
            $this->addAttribute( 'readonly'            , $this->getOption('readonly') );
            $this->addAttribute( 'size'                , $this->getOption('size') );
            $this->addAttribute( 'src'                 , $this->getOption('src') );
            $this->addAttribute( 'widget'              , 'Autocomplete' );
            $this->addAttribute( 'widget-autocomplete' , json_encode( $conf ) );

            $attributes = $this->getAttributes( false );
            if( $free ){
                $content  = "<input $attributes name='$name' value='$value' />";
            }else{
                $content  = "<input $attributes name='$id' />";
                $content .= "<input type='hidden' id='$hid' name='$name' value='$value' />";
            }
            return $content;

        }

        public function renderEnd()
        {

        }

    }
