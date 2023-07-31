<?php

    class Console_IO{
            
        protected static $codes     = array(
            'color' => array(
                    'black'  => 30,
                    'red'    => 31,
                    'green'  => 32,
                    'brown'  => 33,
                    'blue'   => 34,
                    'purple' => 35,
                    'cyan'   => 36,
                    'grey'   => 37,
                    'yellow' => 33
            ),
            'style' => array(
                    'normal'     => 0,
                    'bold'       => 1,
                    'light'      => 1,
                    'underscore' => 4,
                    'underline'  => 4,
                    'blink'      => 5,
                    'inverse'    => 6,
                    'hidden'     => 8,
                    'concealed'  => 8
            ),
            'background' => array(
                    'black'  => 40,
                    'red'    => 41,
                    'green'  => 42,
                    'brown'  => 43,
                    'yellow' => 43,
                    'blue'   => 44,
                    'purple' => 45,
                    'cyan'   => 46,
                    'grey'   => 47
        ));
    
        /**
         * Returns an ANSI-Controlcode
         * 
         * Takes 1 to 3 Arguments: either 1 to 3 strings containing the name of the
         * FG Color, style and BG color, or one array with the indices color, style
         * or background.
         *
         * @param mixed  $color      Optional.
         *                           Either a string with the name of the foreground
         *                           color, or an array with the indices 'color', 
         *                           'style', 'background' and corresponding names as
         *                           values.
         * @param string $style      Optional name of the style
         * @param string $background Optional name of the background color
         *
         * @access public
         * @return string
         */
        protected static function color($color = null, $style = null, $background = null)
        {
            $colors = self::$codes;
            if (is_array($color)) {
                $style      = @$color['style'];
                $background = @$color['background'];
                $color      = @$color['color'];
            }
    
            if ($color == 'reset') {
                return "\033[0m";
            }
    
            $code = array();
            if (isset($color)) {
                $code[] = $colors['color'][$color];
            }
    
            if (isset($style)) {
                $code[] = $colors['style'][$style];
            }
    
            if (isset($background)) {
                $code[] = $colors['background'][$background];
            }
    
            if (empty($code)) {
                $code[] = 0;
            }
    
            $code = implode(';', $code);
            return "\033[{$code}m";
        }
    
            
        /**
         * Converts colorcodes in the format %y (for yellow) into ansi-control
         * codes. The conversion table is: ('bold' meaning 'light' on some
         * terminals). It's almost the same conversion table irssi uses.
         * <pre> 
         *                  text      text            background
         *      ------------------------------------------------
         *      %k %K %0    black     dark grey       black
         *      %r %R %1    red       bold red        red
         *      %g %G %2    green     bold green      green
         *      %y %Y %3    yellow    bold yellow     yellow
         *      %b %B %4    blue      bold blue       blue
         *      %m %M %5    magenta   bold magenta    magenta
         *      %p %P       magenta (think: purple)
         *      %c %C %6    cyan      bold cyan       cyan
         *      %w %W %7    white     bold white      white
         *
         *      %F     Blinking, Flashing
         *      %U     Underline
         *      %8     Reverse
         *      %_,%9  Bold
         *
         *      %n     Resets the color
         *      %%     A single %
         * </pre>
         * First param is the string to convert, second is an optional flag if
         * colors should be used. It defaults to true, if set to false, the
         * colorcodes will just be removed (And %% will be transformed into %)
         *
         * @param string $string  String to convert
         * @param bool   $colored Should the string be colored?
         *
         * @access public
         * @return string
         */
        protected static function convert($string, $colored = true)
        {
            static $conversions = array ( // static so the array doesn't get built
                                          // everytime
                // %y - yellow, and so on... {{{
                '%y' => array('color' => 'yellow'),
                '%g' => array('color' => 'green' ),
                '%b' => array('color' => 'blue'  ),
                '%r' => array('color' => 'red'   ),
                '%p' => array('color' => 'purple'),
                '%m' => array('color' => 'purple'),
                '%c' => array('color' => 'cyan'  ),
                '%w' => array('color' => 'grey'  ),
                '%k' => array('color' => 'black' ),
                '%n' => array('color' => 'reset' ),
                '%Y' => array('color' => 'yellow',  'style' => 'light'),
                '%G' => array('color' => 'green',   'style' => 'light'),
                '%B' => array('color' => 'blue',    'style' => 'light'),
                '%R' => array('color' => 'red',     'style' => 'light'),
                '%P' => array('color' => 'purple',  'style' => 'light'),
                '%M' => array('color' => 'purple',  'style' => 'light'),
                '%C' => array('color' => 'cyan',    'style' => 'light'),
                '%W' => array('color' => 'grey',    'style' => 'light'),
                '%K' => array('color' => 'black',   'style' => 'light'),
                '%N' => array('color' => 'reset',   'style' => 'light'),
                '%3' => array('background' => 'yellow'),
                '%2' => array('background' => 'green' ),
                '%4' => array('background' => 'blue'  ),
                '%1' => array('background' => 'red'   ),
                '%5' => array('background' => 'purple'),
                '%6' => array('background' => 'cyan'  ),
                '%7' => array('background' => 'grey'  ),
                '%0' => array('background' => 'black' ),
                // Don't use this, I can't stand flashing text
                '%F' => array('style' => 'blink'),
                '%U' => array('style' => 'underline'),
                '%8' => array('style' => 'inverse'),
                '%9' => array('style' => 'bold'),
                '%_' => array('style' => 'bold')
                // }}}
            );
    
            if ($colored) {
                $string = str_replace('%%', '% ', $string);
                foreach ($conversions as $key => $value) {
                    $string = str_replace($key, self::color($value),
                              $string);
                }
                $string = str_replace('% ', '%', $string);
    
            } else {
                $string = preg_replace('/%((%)|.)/', '$2', $string);
            }
    
            return $string;
        }
                 
        public static function read( $prompt = null )
        {

            if( $prompt !== null )
                self::write( $prompt );

            return trim( fgets(STDIN) );

        }        
   
        public static function write( $string = '' , $endline = false , $colorize = true , $error = false )
        {

            //Check if we are in cli mode
            $cli = Console::cli();

           
            //Add End line
            if( $endline && substr( $string , -1 ) !== '\n' ){
                if( $cli )
                    $string = $string."\n";
                else{
                    $string = $string." <br />\n";
                }
            }
            
            //Colorize ( TODO FOR NOW ONLY FOR CLI )
            if( $cli && $colorize ){
                $string = self::convert( $string );
            }
            
            //Write
            if( $cli ){
                if( $error ){
                    fwrite( STDERR, $string  );            
                }else{
                    fwrite( STDOUT , $string );                  
                }
            }else{
                echo $string;
                flush();
            }
 
        }

        public static function writeLine( $string = '' , $colorize = true , $error = false )
        {
            return self::write( $string , true , $colorize , $error );
        }
  
        public static function writeError( $string , $endline = false , $colorize = true )
        {
            return self::write( $string , $endline , $colorize , true );
        }

        public static function writeErrorLine( $string = '', $colorize = true  )
        {
            return self::writeError( $string , true , $colorize );
        }
                
    }    
