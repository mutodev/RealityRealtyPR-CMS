<?php

    class Image{

        protected $image                 = null;
        protected $image_file            = null;
        protected $image_width           = null;
        protected $image_height          = null;
        protected $image_type            = null;
        protected $image_quality         = 100;
        protected $image_mime            = null;
        protected $resize_width          = null;
        protected $resize_height         = null;
        protected $resize_allow_blowup   = false;
        protected $resize_preserve_ratio = true;
        protected $rotate_degrees        = null;
        protected $sharpen               = false;

        function __construct( $file = null )
        {
            if( $file )$this->load( $file );
            ini_set('gd.jpeg_ignore_warning', 1);
            ini_set('memory_limit' , -1 );
        }

        function __destruct( )
        {
            if( $this->image )
                imagedestroy( $this->image );
        }

        function load( $file ){
            $this->image_file = $file;
            $info = getimagesize( $file , $extra );

            if( empty( $info ) ){
                throw new Exception('Invalid Image');
            }

            $this->image_width  = $info[0];
            $this->image_height = $info[1];
            $this->image_type   = $info[2];
            $this->image_mime   = $info['mime'];
            $this->image        = null;
        }

        function getFile(){
            return $this->image_file;
        }

        function getHeight(){
            return $this->image_height;
        }

        function getMime(){

          $mime = "application/octet-stream";
          switch( $this->image_type ){
                case IMAGETYPE_JPEG:
                   $mime = "image/jpeg";
                   break;
                case IMAGETYPE_GIF:
                   $mime = "image/gif";
                   break;
                case IMAGETYPE_PNG:
                   $mime = "image/png";
                   break;
          }

          return $mime;

        }

        function getExtension()
        {

          $extension = null;
          switch( $this->image_type ){
                case IMAGETYPE_JPEG:
                   $extension = ".jpg";
                   break;
                case IMAGETYPE_GIF:
                   $extension = ".gif";
                   break;
                case IMAGETYPE_PNG:
                   $extension = ".png";
                   break;
          }

          return $extension;

        }

        function getQuality(){
            return $this->image_quality;
        }

        function getResizeDimmensions(){

             $allow_blowup   = $this->resize_allow_blowup;
             $preserve_ratio = $this->resize_preserve_ratio;
             $image_width    = $this->getWidth( );
             $image_height   = $this->getHeight( );
             $resize_width   = $this->resize_width;
             $resize_height  = $this->resize_height;

             //Don't allow blowup
             if( !$allow_blowup ){
                 if( $resize_height > $image_height || $resize_width > $image_width ){

                    if( $resize_width > $resize_height ){
                        $resize_width  = $image_width;
                        $resize_height = null;
                    }else{
                        $resize_width  = null;
                        $resize_height = $image_height;
                    }
                 }
             }

             //Get Dimmensions
             if( empty( $resize_width ) && empty( $resize_height ) ){

                return array( $image_width , $image_height );

             }elseif( !empty( $resize_width ) && empty( $resize_height ) ){

                $ratio      = $resize_width / $image_width;
                $new_width  = $resize_width;
                $new_height = $image_height * $ratio;
                return array( $new_width , $new_height );

             }elseif( empty( $resize_width ) && !empty( $resize_height ) ){

                $ratio      = $resize_height / $image_height;
                $new_width  = $image_width * $ratio;
                $new_height = $resize_height;
                return array( $new_width , $new_height );

             }elseif( !$preserve_ratio ){

                return array( $resize_width , $resize_height );

             }elseif( $image_width > $image_height ){

                $ratio      = $resize_width / $image_width;
                $new_width  = $resize_width;
                $new_height = $image_height * $ratio;
                return array( $new_width , $new_height );

             }else{

                $ratio      = $resize_height / $image_height;
                $new_width  = $image_width * $ratio;
                $new_height = $resize_height;
                return array( $new_width , $new_height );

             }

        }

        function getResizeHeight(){
            $dim = $this->getResizeDimmensions();
            return $dim[1];
        }

        function getResizeWidth(){
            $dim = $this->getResizeDimmensions();
            return $dim[0];
        }

        function getType(){
            return $this->image_type;
        }

        function getWidth(){
            return $this->image_width;
        }

        function reset(){
            $this->image_quality         = 100;
            $this->resize_width          = null;
            $this->resize_height         = null;
            $this->resize_allow_blowup   = false;
            $this->resize_preserve_ratio = true;
            $this->image                 = null;
        }

        function resize( $width , $height , $preserve_ratio = true , $allow_blowup = false , $sharpen = true ){
            $this->resize_width          = (int)$width  > 0 ? (int)$width : null;
            $this->resize_height         = (int)$height > 0 ? (int)$height : null;
            $this->resize_preserve_ratio = (bool)$preserve_ratio;
            $this->resize_allow_blowup   = (bool)$allow_blowup;
            $this->sharpen               = (bool)$sharpen;
            $this->image                 = null;
        }

        function resizeToWidth( $width , $blowup = false , $sharpen = true ){
            $this->resize( $width , null , true , $blowup , $sharpen );
        }

        function resizeToHeight( $height , $blowup = false , $sharpen = true ){
            $this->resize( null , $height , true , $blowup , $sharpen );
        }

        function resizeToBox( $width , $height , $blowup = false , $sharpen = true ){
            $this->resize( $width , $height , true , $blowup , $sharpen );
        }

        function rotate( $degrees ){
            $this->rotate_degrees = $degrees;
            $this->image          = null;
        }

        function setQuality( $quality = 100 ){
            $quality = (int) $quality;
            if( $quality <= 0 || $quality >= 100 )
                $quality = 100;
            $this->image_quality = $quality;
            $this->image         = null;
        }


        function getImage( ){

            //Check the cache
            if( $this->image )
                return $this->image;

            //Get Data
            $input   = $this->getFile();
            $width   = $this->getWidth();
            $height  = $this->getHeight();
            $quality = $this->getQuality();

            //Open Image
            $image = null;
            switch( $this->image_type ){
                case IMAGETYPE_JPEG:
                   @$image = imagecreatefromjpeg( $input );
                   break;
                case IMAGETYPE_GIF:
                   @$image = imagecreatefromgif( $input );
                   break;
                case IMAGETYPE_PNG:
                   @$image = imagecreatefrompng( $input );
                   break;
                default:
                    return false;
            }

            //Make sure we opened the image
            if( !$image )
                return false;

            //Remove Interlace bit
            imageinterlace( $image , 0 );

            //Get Resize Dimmensions
            list( $new_width , $new_height ) = $this->getResizeDimmensions();

            //Create new image
            $new_image = imagecreatetruecolor( $new_width , $new_height );

            //Set Tranparency
            if( in_array( $this->image_type , array(IMAGETYPE_GIF,IMAGETYPE_PNG))){
                imagealphablending($new_image, false );
                imagesavealpha($new_image, true );
            }

            //Resize Image
            imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width , $height );

            //Sharpen Image
            if( $this->sharpen ){

                $sharpen_matrix = array
                (
                    array(-1.2, -1, -1.2),
                    array(-1, 20, -1),
                    array(-1.2, -1, -1.2)
                );

                //Calculate the Sharpen Divisor
                $sharpen_divisor = array_sum(array_map('array_sum', $sharpen_matrix ));

                //Apply the convolution
                imageconvolution( $new_image , $sharpen_matrix, $sharpen_divisor, 0);

            }

            //Rotate Image
            if( $this->rotate_degrees ){
                $new_image = imagerotate( $new_image , $this->rotate_degrees , 0);
            }

            return $this->image = $new_image;

        }


        function save( $output = null , $type = null ){

            //Save to default file
            if( !$output )
                $output = $this->getFile();

            //Get the image
            $image    = $this->getImage();
            $quality  = $this->getQuality();
            $type     = $type ? $type : $this->image_type;

            switch( $type ){
                case IMAGETYPE_JPEG: $type = 'jpg';
                                     break;
                case IMAGETYPE_GIF:  $type = 'gif';
                                     break;
                case IMAGETYPE_PNG:  $type = 'png';
                                     break;
            }

            //Save the file back to disk
            switch( $type ){
                case 'jpg':
                   imagejpeg( $image , $output , $quality );
                   break;
                case 'gif':
                   imagegif( $image , $output );
                   break;
                case 'png':
                   imagepng( $image , $output , floor( ($quality / 100) * 9 ) );
                   break;
                default:
                    return false;
            }

            return true;

        }

        function inline(){

			$boundary   = "_".md5( rand() );
			$mime       = $this->getMime();
			$base64     = $this->base64();
			$identifier = md5($base64);
			$uri        = 'data:' . $mime . ';base64,' . $base64;

			$uriie   = "mhtml:";
			$uriie  .= !empty($_SERVER['HTTPS']) ?  "https://" : "http://";
			$uriie  .= @$_SERVER['HTTP_HOST'];
			$uriie  .= empty($_SERVER['HTTP_PORT']) || $_SERVER['HTTP_PORT'] == "80" ? "" : ":".$_SERVER['HTTP_PORT'];
			$uriie  .= @$_SERVER['REQUEST_URI'];
			//$uriie  .= "?r=".rand();
			$uriie  .= "!".$identifier;


			$doc = <<<EOF
<style type="text/css">
/*
Content-Type: multipart/related; boundary="$boundary"

--$boundary
Content-Location:$identifier
Content-Transfer-Encoding:base64

$base64

--$boundary--
*/
</style>
<!--[if IE]>
	<img src='$uriie' />
<![endif]-->

<!--[if !IE]><!-->
<h1>You are NOT using Internet Explorer</h1>
	<img src='$uri' />
<!--<![endif]-->

EOF;

			return $doc;

        }

        function uri(){

          //Get Mime
          $mime = $this->getMime();

		      //Get Base64
          $base64   = $this->base64();

          //Create Uri
          $uri      = 'data:' . $mime . ';base64,' . $base64;

          //Unlink tmp file
          @unlink( $file );
          return $uri;
        }

        function base64(){

          //Save File to temp folder
          $file = tempnam( sys_get_temp_dir() , "img" );
          $this->save( $file );

          //Get Base64
          $contents = file_get_contents($file);
          $base64   = base64_encode($contents);

          //Unlink tmp file
          @unlink( $file );

          return $base64;
        }

        function send( $download = null ){

          //Save File to temp folder
          $file = tempnam( sys_get_temp_dir() , "img" );
          $this->save( $file );

          //Get Mime
          $mime = $this->getMime();

          //Send File
          FS::send( $file , $download, $mime , false , false );

          //Unlink tmp file
          @unlink( $file );
          exit();

        }

    }

