<?php

    class Helper_Table_Column_Actions extends Helper_Table_Column{

       /**
        * Render the header for this column
        */
        public function header( ){
            return $this->getLabel();
        }

       /**
        * Render the content for this column
        */
        public function text( $record ){

            $html    = '';
            $actions = $this->getOption("actions" , array() );
            $items   = array();

            foreach( $actions as $action_name => $action ){
                $result = $this->createAction( $action_name , $action , $record );

                if ($result) {
                    $items[] = $result;
                }
            }

            if (empty($items)) {
                return '';
            }

            $html .= '<div class="btn-group">';
            $html .= '  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
            $html .= '    <span class="hidden-xs-down">Action</span><i class="fa fa-gear hidden-sm-up"></i> <span class="caret"></span>';
            $html .= '  </button>';
            $html .= '  <div class="dropdown-menu dropdown-menu-right" role="menu">';

            //Get the actions
            foreach( $items as $item ){
                //$html .= '<li class="dropdown-item">';
                $html .= $item;
                //$html .= '</li>';
            }

            $html .= '  </div>';
            $html .= '</div>';

            return $html;

        }

        protected function createAction( $action_name , $action , $record ){

                $Html       = new Html();
                $attributes = input( $action , 'attributes' , array() );
                $icon       = input( $action , 'icon'       , "" );
                $link       = input( $action , 'link'       , "" );
                $onclick    = input( $action , 'onclick'    , "" );
                $label      = input( $action , 'label'      , Inflector::humanize($action_name) );
                $confirm    = input( $action , 'confirm'    , false );
                $permission = input( $action , 'permission' , null  );
                $condition  = input( $action , 'condition'  , null  );

                //Check Permission
                if ( $permission && !Auth::hasPermission( $permission ) )
                    return false;

                //Condition
                if ($condition) {

                    if (function_exists($condition) && call_user_func($condition, $record) === false) {
                        return false;
                    }


                    $value = Template::create($condition)->apply($record, null, true);
                    if (eval("return (bool)($value);") === false) {
                        return false;
                    }
                }

                //Get the link
                if( $link )
                    $link = url( $this->getValue( $record , $link ) );
                else
                    $link = "javascript:void(0)";

                //Get the label
                $label = $this->getValue( $record , t($label) );

                //Get the onclick
                if( $confirm && !$onclick ){

                    if( is_string( $confirm ) && $confirm != '1' ){
                        $onclick_msg = $confirm;
                    }else{
                        $onclick_msg = t('Are you sure?');
                    }

                    $onclick_msg = addslashes( $onclick_msg );
                    $onclick     = "return (function(link) { bootbox.confirm(\"$onclick_msg\", function(result) { if (result) window.location.href = link; }); return false; })(this.href)";

                }elseif( $onclick ){
                    $onclick = $this->getValue( $record , $onclick );
                }

                $attributes['href']    = $link;
                $attributes['onclick'] = $onclick;
                $attributes['class']   .= ' dropdown-item';

                $Html->open( "a" , $attributes );
                    if( $icon ) {
                        $Html->text('<i class="fa fa-'.$icon.'"></i>');
                    }
                    $Html->text( $label );
                $Html->close("a");

                return (string)$Html;

        }

        public function getTranslationStrings(){

            $strings   = parent::getTranslationStrings();

            $actions   = $this->getOption('actions');
            foreach( $actions as $action_name => $action ){
                $strings[] = input( $action , 'label', Inflector::humanize($action_name) );
            }

            return $strings;

        }

    }
