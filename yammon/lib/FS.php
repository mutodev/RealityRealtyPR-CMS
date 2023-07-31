<?php

	class FS{

        const ONCE                   = 1;
        const MANY                   = 0;

        const REQUIRED               = 1;
        const NON_REQUIRED           = 0;        

        const RECURSIVE              = 1;
        const NON_RECURSIVE          = 0;        

        const FIND_MODE_ALL          = 0;
        const FIND_MODE_FILES        = 1;
        const FIND_MODE_DIRECTORIES  = 2;

        private static $mimes = array(
            ""	      => "application/octet-stream" , 
            "323"     => "text/h323" , 
            "acx"     => "application/internet-property-stream" , 
            "ai"      => "application/postscript" , 
            "aif"     => "audio/x-aiff" , 
            "aifc"    => "audio/x-aiff" , 
            "aiff"    => "audio/x-aiff" , 
            "asf"     => "video/x-ms-asf" , 
            "asr"     => "video/x-ms-asf" , 
            "asx"     => "video/x-ms-asf" , 
            "au"      => "audio/basic" , 
            "avi"     => "video/x-msvideo" , 
            "axs"     => "application/olescript" , 
            "bas"     => "text/plain" , 
            "bcpio"   => "application/x-bcpio" , 
            "bin"     => "application/octet-stream" , 
            "bmp"     => "image/bmp" , 
            "c"       => "text/plain" , 
            "cat"     => "application/vnd.ms-pkiseccat" , 
            "cdf"     => "application/x-cdf" , 
            "cer"     => "application/x-x509-ca-cert" , 
            "class"   => "application/octet-stream" , 
            "clp"     => "application/x-msclip" , 
            "cmx"     => "image/x-cmx" , 
            "cod"     => "image/cis-cod" , 
            "cpio"    => "application/x-cpio" , 
            "crd"     => "application/x-mscardfile" , 
            "crl"     => "application/pkix-crl" , 
            "crt"     => "application/x-x509-ca-cert" , 
            "csh"     => "application/x-csh" , 
            "css"     => "text/css" , 
            "dcr"     => "application/x-director" , 
            "der"     => "application/x-x509-ca-cert" , 
            "dir"     => "application/x-director" , 
            "dll"     => "application/x-msdownload" , 
            "dms"     => "application/octet-stream" , 
            "doc"     => "application/msword" , 
            "dot"     => "application/msword" , 
            "dvi"     => "application/x-dvi" , 
            "dxr"     => "application/x-director" , 
            "eps"     => "application/postscript" , 
            "etx"     => "text/x-setext" , 
            "evy"     => "application/envoy" , 
            "exe"     => "application/octet-stream" , 
            "fif"     => "application/fractals" , 
            "flr"     => "x-world/x-vrml" , 
            "gif"     => "image/gif" , 
            "gtar"    => "application/x-gtar" , 
            "gz"      => "application/x-gzip" , 
            "h"       => "text/plain" , 
            "hdf"     => "application/x-hdf" , 
            "hlp"     => "application/winhlp" , 
            "hqx"     => "application/mac-binhex40" , 
            "hta"     => "application/hta" , 
            "htc"     => "text/x-component" , 
            "htm"     => "text/html" , 
            "html"    => "text/html" , 
            "htt"     => "text/webviewhtml" , 
            "ico"     => "image/x-icon" , 
            "ief"     => "image/ief" , 
            "iii"     => "application/x-iphone" , 
            "ins"     => "application/x-internet-signup" , 
            "isp"     => "application/x-internet-signup" , 
            "jfif"    => "image/pipeg" , 
            "jpe"     => "image/jpeg" , 
            "jpeg"    => "image/jpeg" , 
            "jpg"     => "image/jpeg" , 
            "js"      => "application/x-javascript" , 
            "latex"   => "application/x-latex" , 
            "lha"     => "application/octet-stream" , 
            "lsf"     => "video/x-la-asf" , 
            "lsx"     => "video/x-la-asf" , 
            "lzh"     => "application/octet-stream" , 
            "m13"     => "application/x-msmediaview" , 
            "m14"     => "application/x-msmediaview" , 
            "m3u"     => "audio/x-mpegurl" , 
            "man"     => "application/x-troff-man" , 
            "mdb"     => "application/x-msaccess" , 
            "me"      => "application/x-troff-me" , 
            "mht"     => "message/rfc822" , 
            "mhtml"   => "message/rfc822" , 
            "mid"     => "audio/mid" , 
            "mny"     => "application/x-msmoney" , 
            "mov"     => "video/quicktime" , 
            "movie"   => "video/x-sgi-movie" , 
            "mp2"     => "video/mpeg" , 
            "mp3"     => "audio/mpeg" , 
            "mpa"     => "video/mpeg" , 
            "mpe"     => "video/mpeg" , 
            "mpeg"    => "video/mpeg" , 
            "mpg"     => "video/mpeg" , 
            "mpp"     => "application/vnd.ms-project" , 
            "mpv2"    => "video/mpeg" , 
            "ms"      => "application/x-troff-ms" , 
            "mvb"     => "application/x-msmediaview" , 
            "nws"     => "message/rfc822" , 
            "oda"     => "application/oda" , 
            "p10"     => "application/pkcs10" , 
            "p12"     => "application/x-pkcs12" , 
            "p7b"     => "application/x-pkcs7-certificates" , 
            "p7c"     => "application/x-pkcs7-mime" , 
            "p7m"     => "application/x-pkcs7-mime" , 
            "p7r"     => "application/x-pkcs7-certreqresp" , 
            "p7s"     => "application/x-pkcs7-signature" , 
            "pbm"     => "image/x-portable-bitmap" , 
            "pdf"     => "application/pdf" , 
            "pfx"     => "application/x-pkcs12" , 
            "pgm"     => "image/x-portable-graymap" , 
            "pko"     => "application/ynd.ms-pkipko" , 
            "pma"     => "application/x-perfmon" , 
            "pmc"     => "application/x-perfmon" , 
            "pml"     => "application/x-perfmon" , 
            "pmr"     => "application/x-perfmon" , 
            "pmw"     => "application/x-perfmon" , 
            "png"     => "image/png" ,            
            "pnm"     => "image/x-portable-anymap" , 
            "pot"     => "application/vnd.ms-powerpoint" , 
            "ppm"     => "image/x-portable-pixmap" , 
            "pps"     => "application/vnd.ms-powerpoint" , 
            "ppt"     => "application/vnd.ms-powerpoint" , 
            "prf"     => "application/pics-rules" , 
            "ps"      => "application/postscript" , 
            "pub"     => "application/x-mspublisher" , 
            "qt"      => "video/quicktime" , 
            "ra"      => "audio/x-pn-realaudio" , 
            "ram"     => "audio/x-pn-realaudio" , 
            "ras"     => "image/x-cmu-raster" , 
            "rgb"     => "image/x-rgb" , 
            "rmi"     => "audio/mid" , 
            "roff"    => "application/x-troff" , 
            "rtf"     => "application/rtf" , 
            "rtx"     => "text/richtext" , 
            "scd"     => "application/x-msschedule" , 
            "sct"     => "text/scriptlet" , 
            "setpay"  => "application/set-payment-initiation" , 
            "setreg"  => "application/set-registration-initiation" , 
            "sh"      => "application/x-sh" , 
            "shar"    => "application/x-shar" , 
            "sit"     => "application/x-stuffit" , 
            "snd"     => "audio/basic" , 
            "spc"     => "application/x-pkcs7-certificates" , 
            "spl"     => "application/futuresplash" , 
            "src"     => "application/x-wais-source" , 
            "sst"     => "application/vnd.ms-pkicertstore" , 
            "stl"     => "application/vnd.ms-pkistl" , 
            "stm"     => "text/html" , 
            "svg"     => "image/svg+xml" , 
            "sv4cpio" => "application/x-sv4cpio" , 
            "sv4crc"  => "application/x-sv4crc" , 
            "swf"     => "application/x-shockwave-flash" , 
            "t"       => "application/x-troff" , 
            "tar"     => "application/x-tar" , 
            "tcl"     => "application/x-tcl" , 
            "tex"     => "application/x-tex" , 
            "texi"    => "application/x-texinfo" , 
            "texinfo" => "application/x-texinfo" , 
            "tgz"     => "application/x-compressed" , 
            "tif"     => "image/tiff" , 
            "tiff"    => "image/tiff" , 
            "tr"      => "application/x-troff" , 
            "trm"     => "application/x-msterminal" , 
            "tsv"     => "text/tab-separated-values" , 
            "txt"     => "text/plain" , 
            "uls"     => "text/iuls" , 
            "ustar"   => "application/x-ustar" , 
            "vcf"     => "text/x-vcard" , 
            "vrml"    => "x-world/x-vrml" , 
            "wav"     => "audio/x-wav" , 
            "wcm"     => "application/vnd.ms-works" , 
            "wdb"     => "application/vnd.ms-works" , 
            "wks"     => "application/vnd.ms-works" , 
            "wmf"     => "application/x-msmetafile" , 
            "wps"     => "application/vnd.ms-works" , 
            "wri"     => "application/x-mswrite" , 
            "wrl"     => "x-world/x-vrml" , 
            "wrz"     => "x-world/x-vrml" , 
            "xaf"     => "x-world/x-vrml" , 
            "xbm"     => "image/x-xbitmap" , 
            "xla"     => "application/vnd.ms-excel" , 
            "xlc"     => "application/vnd.ms-excel" , 
            "xlm"     => "application/vnd.ms-excel" , 
            "xls"     => "application/vnd.ms-excel" , 
            "xlt"     => "application/vnd.ms-excel" , 
            "xlw"     => "application/vnd.ms-excel" , 
            "xof"     => "x-world/x-vrml" , 
            "xpm"     => "image/x-xpixmap" , 
            "xwd"     => "image/x-xwindowdump" , 
            "z"       => "application/x-compress" , 
            "zip"     => "application/zip" , 
        );

       
        protected static function _find( $pattern = "*" , $recursive = self::RECURSIVE , $dirs = array() , $mode = FS::FIND_MODE_ALL , $first = false ){
                
            $return         = array();
            $dirs           = empty( $dirs )    ? array()    : (array)$dirs; 
            $pattern        = empty( $pattern ) ? "*" : $pattern;
            $basename       = basename( $pattern );
            $dirname        = dirname(  $pattern )."/";                    
            $pattern        = $basename;
            $pattern_dirs   = empty( $dirs ) ? array( $dirname ) : $dirs;
                                                      
            foreach( $pattern_dirs as $dir ){
   
                if( substr( $dir , -1 , 1  ) != "/" ){
                    $dir = $dir."/";
                }
                                  
                 
                $matches  = self::glob( $dir.$pattern , GLOB_MARK | GLOB_BRACE );
                
                $subdirs  = array();
                if( $recursive || $mode != FS::FIND_MODE_FILES ){
                    $subdirs = self::glob( $dir."*" , GLOB_MARK | GLOB_ONLYDIR | GLOB_BRACE );
                }
                
                foreach( $subdirs as $subdir ){
                
                    if( $mode != FS::FIND_MODE_FILES ){
                        if( in_array( $subdir , $matches ) ){
                            $return[] = $subdir;
                            if( $first ) return $return;                                
                        }                                
                    }

                    if( $recursive ){
                        $files2   = self::_find( $pattern , $recursive , $subdir , $mode , $first );
                        $return   = array_merge( $return , $files2 );
                        if( $first && count( $return ) ) return $return;
                    }                    
                
                }
                                
                foreach( $matches as $match ){                  
                    if( is_file( $match) ){           
                        if( $mode != FS::FIND_MODE_DIRECTORIES  ){
                            $return[] = $match;  
                            if( $first ) return $return;
                        }
                    }                        
                }
                                    
            }                
           
            return $return;                
        
        }
        
        public static function _include( $files , $recursive , $dir , $once , $require ){
            $return = false;
            $files  = (array)$files;
            
            foreach( $files as $file ){
                if( strpos( $file , "*" ) !== false ){
                    $files2 = self::findFiles( $pattern , $recursive );
                    $files  = array_merge( $files , $files2 );
                }
            }            
            
            foreach( $files as $file ){
                        
                if( !$once )
                    if( !$require )
                        $return = include( $file );
                    else                        
                        $return = require( $file );     
                else                    
                    if( !$require )
                        $return = include_once( $file );
                    else                        
                        $return = require_once( $file );

            }
            return $return;
        }        
                
        public static function chmod( $path , $file_mode = null , $directory_mode = null , $recursive = true ){
        
            if( $directory_mode == null ){
                $directory_mode = $file_mode;
            }
            
            if( $file_mode == null && $directory_mode == null ){
                return false;
            }
        
            if( self::isDirectory( $path ) ){
                        
                //Find subentries
                $scan = $recursive ? self::glob( rtrim( $path ,'/').'/*' , GLOB_MARK | GLOB_BRACE ) : array();
                
                foreach( $scan as $subpath ){
                    self::chmod( $subpath , $file_mode , $directory_mode , $recursive );
                }            
            
                //Change Directory Mode
                return @chmod( $path , $directory_mode );
            
            
            
            }elseif( self::isFile( $path ) ){
            
                //Change File mode
                return @chmod( $path , $file_mode );
                
            }
        
        }
        
        public static function chown( $path , $owner = null , $recursive = true ){
        
            if( $owner == null ){
                return false;
            }
                    
            if( self::isDirectory( $path ) ){
                        
                //Find subentries
                $scan = $recursive ? self::glob( rtrim( $path ,'/').'/*' , GLOB_MARK | GLOB_BRACE ) : array();
                
                foreach( $scan as $subpath ){
                    self::chown( $subpath , $file_mode , $directory_mode , $recursive );
                }            
            
                //Change Directory Owner
                return @chown( $path , $owner );
                        
            }elseif( self::isFile( $path ) ){
            
                //Change File Owner
                return @chown( $path , $owner );
                
            }
        
        }        
        
        public static function copy( $source , $destination , $file_mode = null , $directory_mode = null , $owner = null  , $recursive = true , $root = null ){
        
            $source           = realpath( $source );
            $source_dir       = dirname( $source )."/";
            $source_file      = basename( $source );
            
            if( self::isDirectory( $source ) ){

                $destination_dir  = $destination;
                $destination_file = "";
                
            }else{
            
                if( substr( $destination , -1 , 1 ) == "/" ){
                    $destination_dir   = $destination;
                    $destination_file  = $source_file;
                }else{
                    $destination_dir   = dirname($destination)."/";
                    $destination_file  = basename( $destination );                
                }
                
            }
                        
            if( $root == null ){
                $root = $destination_dir;
            }

            if( empty( $destination_file ) ){
                $destination_file = $source_file;
            }

            if( $directory_mode === null ){
                $directory_mode = $file_mode;
            }
        
            //Check if the source exists
            if( !self::isReadable( $source ) )
                throw new Exception("Can not read '$source'");        
                                          
            if( self::isDirectory( $source ) ){

                //Make directory
                $dest = $root.$source_file."/";                
                if( !$recursive ){
                    if( !self::isDirectory( $dest ) ){                
                        throw new Exception("Directory $dest doesn't exists");
                    }
                }else{
                    self::makeDirectory( $root , null , $directory_mode , $owner , true );                            
                }
                            
                //Find subentries
                $scan = $recursive ? self::glob( rtrim( $source ,'/').'/*' , GLOB_MARK | GLOB_BRACE ) : array();
                
                foreach( $scan as $subpath ){
                
                    //Find subpath
                    $subpath_dir    = dirname( $subpath )."/";
                    $subpath_file   = basename( $subpath );
                
                    //Copy Sub Entry
                    self::copy( $subpath , $subpath_file , $file_mode , $directory_mode , $owner , $recursive , $dest );
                    
                }
                
            }elseif( self::isFile( $source ) ){

                //Make directory
                if( !$recursive ){
                    if( !self::isDirectory( $root ) ){                
                        throw new Exception("Directory $root doesn't exists");
                    }
                }else{
                    self::makeDirectory( $root , null , $directory_mode , $owner , true );                            
                }
            
                $dest = $root.$destination_file;

                //Copy File              
                copy( $source , $destination );
                                
                //Change File Mode
                if( $file_mode !== null )
                    self::chmod( $dest , $file_mode );
                
                //Change the owner
                if( $owner !== null ){
                    self::chown( $dest , $owner );
                }
                
            }
        
            return true;
        
        }
        
        public static function delete( $path , $recursive = true ){
        
            //Check if its is deletable
            if( !self::isDeletable( $path ) ){
                throw new Exception("Can not delete '$path'");            
            }
                
            //Delete
            if( self::isDirectory( $path ) ){

                //Find subentries
                $scan = self::glob(rtrim( $path ,'/').'/*' , GLOB_MARK | GLOB_BRACE );

                //If its not recursive
                if( !$recursive && !empty( $scan ) ){
                    throw new Exception("Can not delete '$path' non-recursively because is not empty");                
                }

                //Delete subentries
                foreach( $scan as $subpath ){
                    self::delete( $subpath , $recursive );
                }
                
                //Delete Directory
                return rmdir( $path );
                
            }elseif( self::isFile( $path ) ){
                //Delete File
                return unlink( $path );
            }
                
        }
        
        public static function exists( $file ){
            return file_exists( $file );
        }        
        
        public static function directoryExists( $path ){
            return self::exists( $path ) && self::isDirectory( $path );
        }

        public static function fileExists( $file ){
            return self::exists( $file ) && self::isFile( $file );
        }
        
        public static function find( $pattern = "*" , $recursive = false , $dirs = array() ,  $mode = FS::FIND_MODE_ALL ){
            return self::_find( $pattern , $recursive , $dirs , $mode );
        }

        public static function findBackwards( $pattern  = "*" ,  $stop = array() , $mode = FS::FIND_MODE_ALL ){

            $return  = array();
            $pattern        = empty( $pattern ) ? "*" : $pattern;
            $basename       = basename( $pattern );
            $dirname        = dirname(  $pattern )."/";                    
            $pattern        = $basename;
            
            //Normalize the stop                
            $stop = empty( $stop ) ? array() : (array)$stop;
            foreach( $stop as $k => $v ){
                $v = dirname( $v );
                if( $v && is_dir( $v) ){ 
                    $stop[ $k ] = realpath( $v );
                }else{
                    unset( $stop[ $v ] );
                }
            }
                        
            //Go Upwards until we traverse to the root
            $path = explode( "/" , $dirname );
            foreach( $path as $k => $v )
                if( empty( $v ) )
                    unset( $path[ $k] );
                    
                                       
            do{
            
                //Get the current path
                $current_path = "/".implode( "/" , $path )."/";
                $real_path    = realpath( $current_path );
         
                //Check if we have to stop
                if( in_array( $real_path , $stop ) ){
                    break;
                }           
                  
                //Find the files
                $files   = self::_find( $basename , self::NON_RECURSIVE , $current_path , $mode );
                $return  = array_merge( $files , $return );
                        
                     
                //Remove a part from the path
                array_pop( $path );
            
            }while( !empty($path) );
            
            
            return $return;
            
        }

        public static function findDirectory( $pattern  = "*" , $recursive = false , $dirs = array() ){
            return self::findFirst( $pattern , $recursive , $dirs , FS::FIND_MODE_DIRECTORIES );
        }

        public static function findDirectories( $pattern  = "*" , $recursive = false , $dirs = array() ){
            return self::find( $pattern , $recursive , $dirs , FS::FIND_MODE_DIRECTORIES );
        }
        
        public static function findDirectoriesBackwards( $pattern  = "*" , $stop = array() , $dirs = array() ){
            return self::findBackwards( $pattern , $stop , $dirs , FS::FIND_MODE_DIRECTORIES );
        }

        public static function findFile( $pattern  = "*" , $recursive = false , $dirs = array() ){
            return self::findFirst( $pattern , $recursive , $dirs , FS::FIND_MODE_FILES );
        }
        
        public static function findFiles( $pattern  = "*" , $recursive = false , $dirs = array() ){
            return self::find( $pattern , $recursive , $dirs , FS::FIND_MODE_FILES );
        }
        
        public static function findFilesBackwards( $pattern  = "*" , $stop = array() , $dirs = array() ){
            return self::findBackwards( $pattern , $stop , $dirs , FS::FIND_MODE_FILES );
        }

        public static function findFirst( $pattern = "*" , $recursive = false , $dirs = array() ,  $mode = FS::FIND_MODE_ALL ){
            $files = self::_find( $pattern , $recursive , $dirs , $mode , true );
            return array_shift( $files );
        }

        protected static function glob( $pattern , $flags = 0 ){
            $return = glob( $pattern , $flags );
            if( $return === false ) 
                return array();
            else 
                return $return;
        }

        public static function includeFile( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::MANY , FS::NON_REQUIRED );    
        }

        public static function includeFileOnce( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::ONCE , FS::NON_REQUIRED );
        }

        public static function includeFiles( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::MANY , FS::NON_REQUIRED );
        }
            
        public static function includeFilesOnce( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::ONCE , FS::NON_REQUIRED );
        }             

        public static function isDirectory( $path ){
            return @is_dir( $path );
        }

        public static function isFile( $path ){
            return @is_file( $path );
        }

        public static function isDeletable( $path ){
            return @self::isWritable( $path );
        }

        public static function isReadable( $path ){
            return @is_readable( $path );
        }

        public static function isWritable( $path ){
            return @is_writable( $path );
        }
        
        public static function size( $source ){            
            return @filesize( $source );
        }
        
        public static function mtime( $source ){
            return @filemtime( $source );            
        }                

        public static function makeDirectory( $path , $mode = null , $owner = null , $recursive = true ){
                
            //Add trailing slash
            if( substr( $path , -1 , 1 ) != "/" ){
                $path = $path . "/";
            }
        
            //If the directory exists do nothing
            if( self::directoryExists( $path ) ){
            
                //Change Mode
                if( $mode ){
                    self::chmod( $path , null , $mode , $recursive );
                }                    
                
                //Change Owner
                if( $owner ){
                    self::chown( $path , $owner , $recursive );
                }                    
                
                return $path;
            }                
        
            //Set default Mode
            if( $mode == null ){
                $mode = 0777;
            }   
                
            //Create Directory  
            $success = @mkdir( $path , $mode , $recursive );

            //Check for success
            if( !self::isDirectory( $path ) ){
                throw new Exception("Coudn't create directory '$path'");
            }
            
            //Change Owner
            if( $owner ){
                self::chown( $path , $owner , $recursive );
            }
            
            //Return the path
            return $path;
            
        }

        public static function makeTempDirectory( $path , $mode = null , $owner = null , $recursive = true ){

            //Remove starting slash
            if( substr( $path , 0 ,1 ) == "/" ){
                $path = substr( $path , 1 );
            }
            
            //Create Directory
            return self::makeDirectory( Yammon::getTemporaryPath( $path ) , $mode , $owner , $recursive );

        }

        public static function mime( $source ){
            
            $pathinfo  = pathinfo( $source );
            $extension = !empty( $pathinfo[ 'extension' ] ) ? $pathinfo[ 'extension' ] : '';
                                                                   
            if( !isset( self::$mimes[ $extension ] ) ){
                $extension = '';
            }

            return self::$mimes[ $extension ];
            
        }

        public static function move( $source , $destination ){
            
            //Check if source and destination are the same
            if( $source == $destination ){
                return true;
            }            
            
            if( !self::isReadable( $source ) )
                throw new Exception( "'$source' is not readable " );

            if( !self::isDeletable( $destination ) )
                throw new Exception( "'$source' is not deletable " );
            
            self::copy( $source , $destination );
            self::delete( $source );
            
        }

        public static function requireFile( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::MANY , FS::REQUIRED );
        }

        public static function requireFileOnce( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::ONCE , FS::REQUIRED );
        }
             
        public static function requireFiles( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::MANY , FS::REQUIRED );
        }

        public static function requireFilesOnce( $files = array() , $recursive = false , $dirs = array() ){
            return self::_include( $files , $recursive , $dirs , FS::ONCE , FS::REQUIRED );
        }
                        
        public static function send( $source , $download = null , $mime = null ,  $cache = true ){

			//Clean the output buffering
			$level = ob_get_level();
			for( $i = 0 ; $i < $level ; $i++ ){
				ob_get_clean();
			}

            //Clean the source            
            $source = str_replace( ".." , "" , $source );
                
            //Check if is found
            if( !self::isFile( $source )  ){
                header('x', TRUE, 404);
                echo "404 NOT FOUND";
                exit();
            }

            if( !self::isReadable( $source ) ){
                header('x', TRUE, 403);            
                echo "403 FORBIDDEN";
                exit();
            }
   
            //Set the expires to 1year
            if( $cache === true ){
                $cache = 60*60*24*365;
            }
   
            //Auto find the download name
            if( $download === true ){
                $download = basename( $source );
            }

            //Dont cache downloads ( for ie bug )
            if( !empty( $download ) ){
                $cache = false;
            }

            //Get the stats of the file
            $stat   = @stat($source);
            $etag   = sprintf('%x-%x-%x', $stat['ino'], $stat['size'], $stat['mtime'] * 1000000);
            $mtime  = $stat['mtime'];
            $dmtime = date('r', $mtime );
            $size   = $stat['size'];
            $mime   = $mime !== null ? $mime : FS::mime( $source );
            
            //Check if the file was modified
            $not_modified = false;
            if( $cache ){
                if( isset($_SERVER['HTTP_IF_NONE_MATCH']) ) {
                    if( $_SERVER['HTTP_IF_NONE_MATCH'] == $etag ){
                        $not_modified = true;
                    }                    
                }else{
                    if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime ){
                        $not_modified = true;                
                    }
                }
            }
            
            //Send Headers                                                      
            if( $cache ){
                header("Pragma: public");
                header("Cache-Control: must-revalidate");
                header('Expires: ' . gmdate('D, d M Y H:i:s', time()+ $cache ) . ' GMT');
            }else{
                header("Pragma: private");
                header("Cache-Control: no-cache");                
                header('Expires: 0');
            }
                        
            header("Etag: $etag");            
            header('Last-Modified: ' . $dmtime );
            
            if( $not_modified ){
                header('x' , TRUE , 304 );
                exit();
            }

            header('Content-type: '.  $mime );
            header('Content-Length: '.$size );            

            if( $download ){
                header("Content-Disposition: attachment; filename=\"$download\";");
            }
                                                            
            //Send File                   
            $handle = fopen( $source , 'rb');
            while( !feof( $handle ) ){
                echo fread( $handle , 1024 );
            }
            fclose( $handle );
            
            //Exit
            exit();
            
        }
        
	}
