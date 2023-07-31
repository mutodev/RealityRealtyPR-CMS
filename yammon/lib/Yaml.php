<?php

vendor("sfYaml");

class Yaml extends sfYaml{

   public static function load($input){
                
        //Check if it is an input file
        if (false && strpos($input, "\n") === false && FS::isFile($input) ){
        
            //Check the cache
            $path       = Yammon::getTemporaryPath('yaml');
            $cache_file = $path.md5( $input ).".yml";
            $exits      = FS::isFile( $cache_file );
            $modified   = $exists ? FS::mtime( $input ) > FS::mtime( $cache_file ) : false;
        
            if( $exists && !$modified ){
                $output = include( $cache_file );
            }else{
                      
                //Add it to the cache
                $output   = parent::load( $input );
                $contents = "<?php return ".var_export( $output , true ).";";
                file_put_contents( $cache_file, $contents );
                
            }
        
        }else{
            $output = parent::load( $input );
        }
        
        return $output;
      
   }

}
