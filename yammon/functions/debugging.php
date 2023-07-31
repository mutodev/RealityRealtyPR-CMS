<?php

    $PR_BACKTRACE_SKIP = 0;
    function pr( ){

        global $PR_BACKTRACE_SKIP;
        
        $args      = func_get_args();
        $argc      = count( $args );
        $trace     = debug_backtrace( false ); 
        for( $i = 0 ; $i < $PR_BACKTRACE_SKIP ; $i++ ){
            array_shift( $trace );
        }
        $back      = array_shift( $trace );
        
        $file      = $back['file'];
        $line      = $back['line'];
        $class     = !empty($back['class'])    ? $back['class']    : '';
        $function  = !empty($back['function']) ? $back['function'] : '';
        $type      = !empty($back['type'])     ? $back['type']     : '';
        
        $file = DIRECTORY_SEPARATOR . substr( $file , strlen( DOCUMENT_ROOT ) );
    
        echo "<div style='background:#EFEFEF;color:#1F1F1F;border:1px solid gray;padding:0;margin:5px 0;'>\n";

                echo "<div style='background:#DFDFDF;'>";
                    echo "<table style='margin:0;width:100%;border-bottom:1px solid black;'>";
                        echo "<tbody>";
                        echo "<tr>";
                            echo "<td width='50%'>";
                                echo "<a href='javascript:void(0)' title='$file' onclick='var next = this.parentNode.parentNode.parentNode.parentNode.parentNode.nextSibling; next.style.display = next.style.display == \"none\" ? \"\" : \"none\";' style='font-weight:bold;color:#cc0000'>";
                                    echo basename( $file ) . " : " . $line;                        
                                echo "</a>";
                            echo "</td>";
                            echo "<td width='50%'>";
                                if( !empty( $function ) ){
                                    echo "<small>";
                                        echo $class.$type.$function."()";
                                    echo "</small>";        
                                }
                            echo "</td>";
                        echo "</tr>";
                        echo "</tbody>";
                    echo "</table>";
                echo "</div>";
                
                echo "<div style='display:none;background:#DFDFDF;'>";
                    echo "<table style='margin:0;width:100%'>";
                                        
                            foreach( $trace as $back ){
                            
                                $file      = isset($back['file'])      ? $back['file'] : '';
                                $line      = isset($back['line'])      ? $back['line'] : '';
                                $class     = !empty($back['class'])    ? $back['class']    : '';
                                $function  = !empty($back['function']) ? $back['function'] : '';
                                $type      = !empty($back['type'])     ? $back['type']     : '';
                                $file      = DIRECTORY_SEPARATOR . substr( $file , strlen( DOCUMENT_ROOT ) );
                                
                                echo "<tr style='border-bottom:1px dotted gray'>";
                                    echo "<td width='50%'>";
                                        echo "<span title='$file'>";
                                            echo basename( $file ) . " : " . $line;                        
                                        echo "</span>";
                                    echo "</td>";
                                    echo "<td width='50%'>";
                                        if( !empty( $function ) ){
                                            echo "<small>";
                                                echo $class.$type.$function."()";
                                            echo "</small>";        
                                        }
                                    echo "</td>";
                                echo "</tr>";
                                
                            }
                    echo "</table>";
                echo "</div>";
                    
                echo "\n\t<pre style='padding:5px'>\n";

                    for( $i = 0 ; $i < $argc ; $i++ ){
                        if( function_exists("xdebug_var_dump") )
                			xdebug_var_dump( $args[ $i ] );
                        else
                			print_r( $args[ $i ] );
                			
                        if( $i != $argc - 1 )
                            echo "<hr />";
                            
                    }
        			
                echo "\n\t</pre>\n"; 
    
        echo "</div>";
        flush();
        
    }

    function prd( )
    {

        global $PR_BACKTRACE_SKIP;

        $PR_BACKTRACE_SKIP = 2;
        $args = func_get_args();
        call_user_func_array( "pr" , $args );
        $PR_BACKTRACE_SKIP = 1;
        die();
        
    }

    function prcli( ){
    
        global $PR_CLI_BACKTRACE_SKIP;    
    
        $args      = func_get_args();  
        $trace     = debug_backtrace( false ); 
        for( $i = 0 ; $i < $PR_CLI_BACKTRACE_SKIP ; $i++ ){
            array_shift( $trace );
        }        
        $back      = array_shift( $trace ); 
        $file      = $back['file'];
        $line      = $back['line'];
        $xdebug    = function_exists("xdebug_var_dump");
        
        echo "\n";        
        echo basename( $file ) . " : " . $line;         
        echo "\n";    
        foreach( $args as $arg ){
         
            if( $xdebug )
                xdebug_var_dump( $arg );
            else
                print_r( $arg );
            echo "\n";                     
        }

    }

    function prdcli( ){
    
        global $PR_CLI_BACKTRACE_SKIP;

        $PR_CLI_BACKTRACE_SKIP = 2;
        $args = func_get_args();
        call_user_func_array( "prcli" , $args );
        $PR_CLI_BACKTRACE_SKIP = 1;
        die();    
    
    }

    function queries(){
    
        global $profiler;
        
        if( !$profiler )
            return;
        
        echo "<table border='1' style='border-collapse:collapse;empty-cells:show'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th> #          </th>";                
                    echo "<th> Event      </th>";                
                    echo "<th> Query      </th>";
                    echo "<th> Seconds    </th>";                    
                    echo "<th> Parameters </th>";                                        
                echo "</tr>";                
            echo "</thead>";                
            echo "<tbody>";           
                $i = 0; 
                foreach( $profiler as $event ){
                    echo "<tr>";
                        echo "<td>";
                            echo ++$i;
                        echo "</td>";  
                        echo "<td>";
                            echo $event->getName();
                        echo "</td>";                    
                        echo "<td>";
                            echo $event->getQuery();
                        echo "</td>";                
                        echo "<td>";
                            echo $event->getElapsedSecs();                    
                        echo "</td>";                
                        echo "<td>";
                            $params = $event->getParams();
                            if( !empty( $params ) ){
                                var_dump( $params );                       
                            }
                        echo "</td>";                                
                    echo "</tr>";            
                }
            echo "</tbody>";
        echo "</table>";
    
    }

