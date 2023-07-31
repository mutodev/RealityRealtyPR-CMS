<?php

    class Helper_Form_Element_DateTime extends Helper_Form_Element_Valued{

        public function setupOptions(){
            parent::setupOptions();
            $this->addOption('format'     , '%M/%d/%Y - %H:%i' );
            $this->addOption('output'     , null);
            $this->addOption('timezone_input',  'UTC');
            $this->addOption('timezone_output', 'UTC');
            $this->addOption('split'      , true );
            $this->addOption('year_start' , '-10' );
            $this->addOption('year_end'   , '+10' );
        }

        public function render(){

            $Javascript = helper('Javascript');
            $Javascript->add("/yammon/public/widget/widget.js");
            $Javascript->add("/yammon/public/widget/datepicker/datepicker.js");

            $Css = helper('Css');
            $Css->add("/yammon/public/widget/datepicker/datepicker.css");

            if( $this->getOption('split') )
                return $this->renderSplitted();
            else
                return $this->renderSingle();
        }

        protected function renderSingle(){

            $Html     = new Html();
            $spec     = $this->spec();
            $format   = $this->getOption('format');
            $timezone = $this->getOption('timezone_input');
            $value    = $this->getUnfilteredValue();

            //Check if it contains a picker
            $has_picker = preg_match( '/%p/'   , $format );
            $format     = preg_replace( '/%p/' , '' , $format );

            //Get the value
            $formatted_value = '';
            if( $value ){

                $DateTime = date_create("@$value")->setTimezone(new DateTimeZone($timezone));

                $split           = str_split( $format , 1 );
                for( $i = 0 , $c = count( $split ) ; $i < $c ; $i++ ){

                    $char = $split[$i];
                    $next = isset( $split[$i+1] ) ? $split[$i+1] : null;

                    if( $char == '%' && isset( $spec[ $next] ) ){
                        $part_value       = $value ? $DateTime->format($next) : '';
                        $formatted_value .= $part_value;
                        $i++;
                    }else{
                        $formatted_value .= $char;
                    }
                }
            }
            $formatted_value = trim( $formatted_value );

            $attributes = $this->getAttributes( true );
            $attributes['id']      = $this->getDomId();
            $attributes['name']    = $this->getDomName();
            $attributes['value']   = $formatted_value;
            $attributes['class'][] = 'ym-form-text form-control';
            $attributes['placeholder'] = 'yyyy-mm-dd';

            $HtmlWrap = new Html();
            $HtmlWrap->open('div', array('class' => 'input-group'));

            $Html->open('input' , $attributes , null , true );
            $Html->text('<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>');

            if( $has_picker )
                $Html->text( $this->renderPicker() );


            $HtmlWrap->text( $Html->get() );
            $HtmlWrap->close();
            return $HtmlWrap->get();
        }

        public function renderSplitted(){

            $Html     = new Html();
            $spec     = $this->spec();
            $format   = $this->getOption('format');
            $timezone = $this->getOption('timezone_input');
            $value    = $this->getUnfilteredValue();
            $domid    = $this->getDomId();

            $DateTime = date_create($value ? "@$value" : "now")->setTimezone(new DateTimeZone($timezone));

            $split = str_split( $format , 1 );
            for( $i = 0 , $c = count( $split ) ; $i < $c ; $i++ ){
                $char  = $split[$i];
                $next  = isset( $split[$i+1] ) ? $split[$i+1] : null;

                //If we found a delimeter show the input
                if( $char == '%' && isset( $spec[ $next] ) ){

                    //Get the value for the part
                    $part_value = $value ? $DateTime->format($spec[$next]['value']) : '';

                    //Create a select box
                    if( is_array( $spec[$next]['options'] ) ){
                        $Html->open( 'select' , $spec[$next]['atts'] , null , false );
                        foreach( $spec[$next]['options'] as $k => $v ){

                            $atts            = array();
                            $atts['value']   = $k;
                            if( $part_value != '' && $k == $part_value ){
                                $atts['selected'] = 'selected';
                            }

                            $Html->open( 'option' , $atts );
                            $Html->text( $v );
                            $Html->close( 'option' );

                        }
                        $Html->close( 'select' );
                    }

                    //Create a textbox
                    if( !is_array( $spec[$next]['options'] ) ){
                        $atts          = $spec[$next]['atts'];
                        $atts['value'] = $part_value;
                        $Html->open( 'input' , $atts  , null , true );
                    }

                    //Skip to next character
                    $i++;
                    continue;
                }

                //Show the picker
                if( $char == '%' && $next == 'p' ){

                    //Show the picker
                    $Html->text( $this->renderPicker() );

                    //Skip to next character
                    $i++;
                    continue;

                }

                //Just show the character
                $Html->text( $char );


            }

            //Do the Actual Rendering
            return $Html->get();

        }

        protected function renderPicker(){

            $domid  = $this->getDomId( );
            $format = $this->getOption('format');

            $attributes          = array();
            $attributes["id"]    = $domid."_p";
            $attributes["src"]   = "/yammon/public/widget/datepicker/calendar.gif";
            $attributes["style"] = $this->getOption('split') ? "vertical-align:middle;cursor:pointer;" : 'cursor:pointer;margin-left:-18px;margin-top:5px;position:absolute;';

            $attributes["widget"]                   = 'DatePicker';
            $attributes["widget-datepicker-render"] = $this->getOption('split') ? 'splitted' : 'single';
            $attributes["widget-datepicker-format"] = trim(strtr( $format , array(
                    '%M' => '%b' ,
                    '%F' => '%B' ,
                    '%m' => '%m' ,
                    '%n' => '%m' ,
                    '%d' => '%d' ,
                    '%j' => '%d' ,
                    '%y' => '%Y' ,
                    '%Y' => '%Y' ,
                    '%H' => '%H' ,
                    '%G' => '%H' ,
                    '%h' => '%I' ,
                    '%g' => '%I' ,
                    '%i' => '%M' ,
                    '%I' => '%M' ,
                    '%s' => '%S' ,
                    '%S' => '%S' ,
                    '%A' => '%p' ,
                    '%a' => '%p' ,
                    '%p' => '' ,
            )));

            $Html = new Html();
            $Html->open( 'img' , $attributes , null , true );
            return $Html->get();

        }

        protected function spec(){

            $spec     = array();
            $domid    = $this->getDomId( );
            $domname  = $this->getDomName();
            $timezone = $this->getOption('timezone_input');

            //-- Month Select Short -------------------------------
            $spec['M']['value']               = 'm';
            $spec['M']['atts']['id']          = $domid."_m";
            $spec['M']['atts']['name']        = $domname."[m]";
            $spec['M']['atts']['class']       = "ym-form-select";
            $spec['M']['options']             = array(
                ''   => t('MM')  ,
                '1'  => t('Jan') ,
                '2'  => t('Feb') ,
                '3'  => t('Mar') ,
                '4'  => t('Apr') ,
                '5'  => t('May') ,
                '6'  => t('Jun') ,
                '7'  => t('Jul') ,
                '8'  => t('Aug') ,
                '9'  => t('Sep') ,
                '10' => t('Oct') ,
                '11' => t('Nov') ,
                '12' => t('Dec') ,
            );

            //-- Month Select Long -------------------------------
            $spec['F']['value']               = 'm';
            $spec['F']['atts']['id']          = $domid."_m";
            $spec['F']['atts']['name']        = $domname."[m]";
            $spec['F']['atts']['class']       = "ym-form-select";
            $spec['F']['options']             = array(
                ''   => t('MM')  ,
                '1'  => t('January') ,
                '2'  => t('February') ,
                '3'  => t('March') ,
                '4'  => t('April') ,
                '5'  => t('May') ,
                '6'  => t('June') ,
                '7'  => t('July') ,
                '8'  => t('August') ,
                '9'  => t('September') ,
                '10' => t('October') ,
                '11' => t('November') ,
                '12' => t('December') ,
            );

            //-- Month Select Numeric ----------------------------
            $spec['m']['value']               = 'm';
            $spec['m']['atts']['id']          = $domid."_m";
            $spec['m']['atts']['name']        = $domname."[m]";
            $spec['m']['atts']['class']       = "ym-form-select";
            $spec['m']['options']             = array('' => t('MM') ) +
            array_combine(
                range( 1 , 12 ) ,
                range( 1 , 12 )
            );

            //-- Month Textbox ------------------------------------
            $spec['n']['value']               = 'm';
            $spec['n']['atts']['id']          = $domid."_m";
            $spec['n']['atts']['name']        = $domname."[m]";
            $spec['n']['atts']['class']       = "ym-form-text";
            $spec['n']['atts']['size']        = 2;
            $spec['n']['atts']['maxlength']   = 2;
            $spec['n']['options']             = null;

            //-- Day Select  --------------------------------
            $spec['d']['value']               = 'd';
            $spec['d']['atts']['id']          = $domid."_d";
            $spec['d']['atts']['name']        = $domname."[d]";
            $spec['d']['atts']['class']       = "ym-form-select";
            $spec['d']['options']             = array('' => t('DD') ) +
            array_combine(
                range( 1 , 31 ) ,
                range( 1 , 31 )
            );

            //-- Day Textbox ------------------------------------
            $spec['j']['value']               = 'd';
            $spec['j']['atts']['id']          = $domid."_d";
            $spec['j']['atts']['name']        = $domname."[d]";
            $spec['j']['atts']['class']       = "ym-form-text";
            $spec['j']['atts']['size']        = 2;
            $spec['j']['atts']['maxlength']   = 2;
            $spec['j']['atts']['value']       = "";
            $spec['j']['options']             = null;

            //-- Year Select --------------------------------------
            $year_start = $this->getOption('year_start');
            $year_end   = $this->getOption('year_end');
            $sign    = substr( $year_start , 0 , 1 );
            $amount  = substr( $year_start , 1 );
            if( $sign == '+' || $sign == '-' )
                $year_start = date_create('now', new DateTimeZone($timezone))->format('Y') + ($sign == '+' ? $amount : $amount*-1);

            $sign    = substr( $year_end , 0 , 1 );
            $amount  = substr( $year_end , 1 );
            if( $sign == '+' || $sign == '-' )
                $year_end = date_create('now', new DateTimeZone($timezone))->format('Y') + ($sign == '+' ? $amount : $amount*-1);

            $spec['y']['value']               = 'Y';
            $spec['y']['atts']['id']          = $domid."_y";
            $spec['y']['atts']['name']        = $domname."[y]";
            $spec['y']['atts']['class']       = "ym-form-select";
            $spec['y']['options']             = array( '' => t('YYYY') ) +
            array_combine(
                range( $year_start , $year_end ) ,
                range( $year_start , $year_end )
            );

            //-- Year Textbox ----------------------------------------
            $spec['Y']['value']               = 'Y';
            $spec['Y']['atts']['id']          = $domid."_y";
            $spec['Y']['atts']['name']        = $domname."[y]";
            $spec['Y']['atts']['class']       = "ym-form-text";
            $spec['Y']['atts']['size']        = 4;
            $spec['Y']['atts']['maxlength']   = 4;
            $spec['Y']['atts']['value']       = "";
            $spec['Y']['options']             = null;

            //-- Hour Select 24  ----------------------------------------
            $spec['H']['value']               = 'H';
            $spec['H']['atts']['id']          = $domid."_g";
            $spec['H']['atts']['name']        = $domname."[g]";
            $spec['H']['atts']['class']       = "ym-form-select";
            $spec['H']['options']             = array('' => t('hh') ) +
            array_combine(
                range( 0 , 23 ) ,
                range( 0 , 23 )
            );

            //-- Hour Textbox 24 ----------------------------------------
            $spec['G']['value']               = 'H';
            $spec['G']['atts']['id']          = $domid."_g";
            $spec['G']['atts']['name']        = $domname."[g]";
            $spec['G']['atts']['class']       = "ym-form-text";
            $spec['G']['atts']['size']        = 2;
            $spec['G']['atts']['maxlength']   = 2;
            $spec['G']['atts']['value']       = "";
            $spec['G']['options']             = null;


            //-- Hour Select 12  ----------------------------------------
            $spec['x']['value']               = 'H';
            $spec['x']['atts']['id']          = $domid."_x";
            $spec['x']['atts']['name']        = $domname."[x]";
            $spec['x']['atts']['class']       = "ym-form-select";
            $spec['x']['options']             = array('' => t('hh') ) +
            array_combine(
                range( 0 , 23 ) ,
                array('12 AM', '1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM',
                      '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM'
                )
            );

            //-- Hour Select 12  ----------------------------------------
            $spec['h']['value']               = 'g';
            $spec['h']['atts']['id']          = $domid."_h";
            $spec['h']['atts']['name']        = $domname."[h]";
            $spec['h']['atts']['class']       = "ym-form-select";
            $spec['h']['options']             = array('' => t('hh') ) +
            array_combine(
                range( 1 , 12 ) ,
                range( 1 , 12 )
            );

            //-- Hour Textbox 12 ----------------------------------------
            $spec['g']['value']               = 'g';
            $spec['g']['atts']['id']          = $domid."_h";
            $spec['g']['atts']['name']        = $domname."[h]";
            $spec['g']['atts']['class']       = "ym-form-text";
            $spec['g']['atts']['size']        = 2;
            $spec['g']['atts']['maxlength']   = 2;
            $spec['g']['atts']['value']       = "";
            $spec['g']['options']             = null;

            //-- Minute Select  ----------------------------------------
            $spec['i']['value']               = 'i';
            $spec['i']['atts']['id']          = $domid."_i";
            $spec['i']['atts']['name']        = $domname."[i]";
            $spec['i']['atts']['class']       = "ym-form-select";
            $spec['i']['options']             =  array('' => t('ii') ) +
            array_combine(
                range( 0 , 59 ) ,
                range( 0 , 59 )
            );

            //-- Minute Texbox  ----------------------------------------
            $spec['I']['value']               = 'i';
            $spec['I']['atts']['id']          = $domid."_i";
            $spec['I']['atts']['name']        = $domname."[i]";
            $spec['I']['atts']['class']       = "ym-form-text";
            $spec['I']['atts']['size']        = 2;
            $spec['I']['atts']['maxlength']   = 2;
            $spec['I']['atts']['value']       = "";
            $spec['I']['options']             = null;

            //-- Seconds Select ----------------------------------------
            $spec['s']['value']               = 's';
            $spec['s']['atts']['id']          = $domid."_s";
            $spec['s']['atts']['name']        = $domname."[s]";
            $spec['s']['atts']['class']       = "ym-form-select";
            $spec['s']['options']             = array('' => t('ss') ) +
            array_combine(
                range( 0 , 59 ) ,
                range( 0 , 59 )
            );

            //-- Seconds Texbox  ----------------------------------------
            $spec['S']['value']               = 's';
            $spec['S']['atts']['id']          = $domid."_s";
            $spec['S']['atts']['name']        = $domname."[s]";
            $spec['S']['atts']['class']       = "ym-form-text";
            $spec['S']['atts']['size']        = 2;
            $spec['S']['atts']['maxlength']   = 2;
            $spec['S']['atts']['value']       = "";
            $spec['S']['options']             = null;

            //-- AM selectbox  ----------------------------------------
            $spec['A']['value']               = 'a';
            $spec['A']['atts']['id']          = $domid."_a";
            $spec['A']['atts']['name']        = $domname."[a]";
            $spec['A']['atts']['class']       = "ym-form-select";
            $spec['A']['options'] = array(
                'am' => t('AM') ,
                'pm' => t('PM') ,
            );

            //-- am selectbox  ----------------------------------------
            $spec['a']['value']               = 'a';
            $spec['a']['atts']['id']          = $domid."_a";
            $spec['a']['atts']['name']        = $domname."[a]";
            $spec['a']['atts']['class']       = "ym-form-select";
            $spec['a']['options']             = array(
                'am' => t('am') ,
                'pm' => t('pm') ,
            );

            //Pad the numeric options
            foreach( $spec as $format => $options ){
                if( !is_array( $options['options'] ) ) continue;
                foreach( $options['options'] as $k => $v ){
                    if( !is_numeric( $v ) ) continue;
                    $spec[ $format ]['options'][$k] = str_pad($v, 2 , "0" , STR_PAD_LEFT );
                }
            }

            return $spec;

        }

        public function getValue()
        {
            $output   = $this->getOption('output');
            $timezone = $this->getOption('timezone_output');
            $value    = parent::getValue();

            if ($output && $value) {
                return date_create("@$value")->setTimezone(new DateTimeZone($timezone))->format($output);
            }

            return $value;
        }

        public function setValue($value)
        {
            if ( !is_int($value) ) {
                $value = strtotime($value);
            }

            return parent::setValue($value);
        }

        protected function normalizeValue( $value ){

            $timezone = $this->getOption('timezone_input');

            if( is_null( $value ) ){
                return null;
            }elseif( $value === '' ){
                return null;
            }elseif( $value == '0' ){
                return null;
            }elseif( is_numeric( $value ) ){
                return (int) $value;
            }elseif( is_array( $value ) ){

                $meridian = isset($value['a']) && $value['a'] == 'am' ? $value['a'] : 'pm';
                unset( $value['a'] );

                //Return null if values are empty
                foreach( $value as $k => $v ){
                    if( trim($v) == '' )
                        unset( $value[$k] );
                }
                if( empty( $value ) ){
                    return null;
                }

                $month    = isset($value['m']) && is_numeric($value['m']) ? $value['m'] : 1;
                $day      = isset($value['d']) && is_numeric($value['d']) ? $value['d'] : 1;
                $year     = isset($value['y']) && is_numeric($value['y']) ? $value['y'] : date_create('now', new DateTimeZone($timezone))->format('Y');
                $minute   = isset($value['i']) && is_numeric($value['i']) ? $value['i'] : 0;
                $second   = isset($value['s']) && is_numeric($value['s']) ? $value['s'] : 0;

                if( isset($value['g']) && is_numeric( $value['g']) ) {
                    $hour  = $value['g'];
                }
                elseif( isset($value['h']) && is_numeric( $value['h'])  ) {

                    $hour = $value['h'];

                    if ($hour == 12 && $meridian == 'am') {
                        $hour -= 12;
                    }
                    else if ($hour != 12 && $meridian == 'pm') {
                        $hour += 12;
                    }
                }
                else {
                    $hour  = 0;
                }

                $value = mktime( $hour, $minute, $second, $month, $day , $year );

            }else{

                $value = strtotime( (string)$value );

                if( $value === false ) {
                    return null;
                }
            }

            //Normalize date to UTC
            $date = date('Y-m-d H:i:s', $value);
            return date_create($date, new DateTimeZone($timezone))->setTimezone(new DateTimeZone('UTC'))->getTimestamp();
        }


    }

