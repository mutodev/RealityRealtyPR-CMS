<?php

class Route{

      protected $matches;
      protected $pattern;
      protected $action;
      protected $requirements;
      protected $methods;
      protected $parameters;
      protected $regex;

      public function __construct( $pattern , $action , $requirements = array()  , $methods = array() ){
         $this->pattern      = $pattern;
         $this->action       = $action;
         $this->requirements = $requirements;
         $this->methods      = $methods;
      }

  	  public function getPattern(){
  		  return $this->pattern;
  	  }

      public function getParameters(){

         if( $this->parameters !== null )
            return $this->parameters;

          $template = new Template( $this->pattern );
          $parameters = $template->getParameters();

          return $this->parameters = $parameters;

      }

      public function getRegEx(){

        //If we already created the regex return it
        if( $this->regex !== null )
            return $this->regex;

        //Get the pattern and quote special characters
        $pattern = $this->pattern;

        if( defined('APPLICATION_PREFIX') )
          $pattern = APPLICATION_PREFIX.$pattern;

        $pattern = strtr( $pattern , array(

            //Substiture asterisk with a regular expression
            "*"  => "(.*?)"  ,

            //Escape all other special characters
            "."  => "\\."  ,
            "\\" => "\\\\" ,
            "/"  => "\\/"  ,
            "+"  => "\\+"  ,
            "?"  => "\\?"  ,
            "["  => "\\["  ,
            "^"  => "\\^"  ,
            "]"  => "\\]"  ,
            "$"  => "\\$"  ,
            "("  => "\\("  ,
            ")"  => "\\)"  ,
            "{"  => "\\{"  ,
            "}"  => "\\}"  ,
            "="  => "\\="  ,
            "!"  => "\\!"  ,
            "<"  => "\\<"  ,
            ">"  => "\\>"  ,
            "|"  => "\\|"  ,
            ":"  => "\\:"  ,
            "-"  => "\\-"  ,
        ));

        //Make trailing slash optional
        if( substr( $pattern , -1 , 1 ) != "/" ){
            $pattern .= "(\\/)?";
        }else{
            $pattern .= "?";
        }

        //Get the parameters for the pattern
        $pattern_parameters = $this->getParameters();

        //Crate Subsitution Array
        $pattern_regexs = array();
        foreach( $pattern_parameters as $k ){

            //Get the sub pattern
            $sub_pattern = "";
            if( !empty( $this->requirements[ $k ] ) ){
                $sub_pattern = $this->requirements[ $k ];
            }

            //Expand the sub pattern
            if( $sub_pattern == "::numeric::" ){
                $sub_pattern = "[0-9]+?";
            }

            if( $sub_pattern == "::alpha::" ){
                $sub_pattern = "[A-Za-z]+?";
            }

            if( $sub_pattern == "::alphanumeric::" ){
                $sub_pattern = "[A-Za-z0-9]+?";
            }

            if( empty( $sub_pattern ) ){
                $sub_pattern = ".+?";
            }

            $pattern_regexs[ $k ] = "(?<$k>$sub_pattern)\n";
        }

    		//Substitute patterns
    		foreach( $pattern_regexs as $param => $sub_pattern ){
    			$pattern = str_replace( "%\\{".$param."\\}" , $sub_pattern , $pattern );
    		}
    		$pattern = "/^".$pattern."$/xi";

        return $this->regex = $pattern;
      }

      public function matchesUrl( $url ){

         //If we already matched the url
         //use cached version
         if( isset( $this->matches[ $url ] ) ){
            return $this->matches[ $url ];
         }

         //Start by assuming it doesn't match
         $this->matches[ $url ] = false;

         //Check that the method is met
         if( !empty( $this->methods ) ){
            $method = strtoupper($_SERVER['REQUEST_METHOD']);
            if( !in_array( $method , $this->methods ) ){
                return false;
            }
         }

         //Get the regular expression
         $regex = $this->getRegEx();

         //Check if the pattern matches the url
         if (!preg_match( $regex , $url , $matches ) ){
           return false;
         }

         //Apply the parameters to the action
         $template = new Template( $this->action );
         $action = $template->apply( $matches );

         //Save the match into the cache
         $this->matches[ $url ] = true;

         //Return the action
         return $action;

      }

      public function matchesAction( $action , $arguments = array() ){

           //Make sure the actions are the same
           list( $action ) = Router::parseAction( $action );
           if( $this->action != $action ){
              return false;
           }

           //If the pattern has a star return false
           //since we can't construct it
           if( strpos( $this->pattern , "*" ) !== false ){
               return false;
           }

           //Make sure the parameters are the same
           $parameters = $this->getParameters();
           $keys       = array_keys( $arguments );

           if( $parameters != $keys ){
              return false;
           }

           //Return the url
           $template = new Template( $this->pattern );
           $path     = $template->apply( $arguments );
           $scheme   = !empty($_SERVER['HTTPS']) ?  "https://" : "http://";
           $host     = @$_SERVER['HTTP_HOST'];
           $port     = @$_SERVER['SERVER_PORT'];

           $url  = $scheme;
           $url .= $host;

           if( !empty($port) ){
                if( ($scheme == "http://" && $port != 80) || ($scheme == "https://" && $port != 443 ) ){
                    $url .= ':'.$port;
                }
           }

           if( !empty( $path ) ){
                $url .= $path;
           }

           if( substr( $url , -1 , 1 ) !== "/" )
                $url .= "/";

           return $url;

      }

}
