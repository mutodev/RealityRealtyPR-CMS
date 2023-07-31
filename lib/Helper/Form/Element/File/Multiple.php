<?php

    class Helper_Form_Element_File_Multiple extends Helper_Form_Element_Valued
    {

        public function setupOptions(){
            parent::setupOptions();


            $this->addOption("max_file_size"   , '6mb' );
            $this->addOption("chunk_size"      , '256kb' );
            $this->addOption("max_width"       , 1024    );
            $this->addOption("max_height"      , 1024    );
            $this->addOption("preview_width"   , 100     );
            $this->addOption("preview_height"  , 100     );
            $this->addOption("resize_quality"  , 90      );
            $this->addOption("extensions"      , array(t("Image files") => "jpg,jpeg,gif,png") );
            $this->addOption("text"            , t('Add images') );
            $this->addOption("icon"            , '/yammon/public/form/img/actions/image.png' );
            $this->addOption("empty"           , '<img src="/yammon/public/form/img/actions/image.png">'.t('') );
            $this->addOption("target_dir"      , null );
            $this->addOption("target_relative" , true );
            $this->addOption("session_key"     , "uploads" );
            $this->addOption("filename_prefix" , null );
            $this->setOption('box_renderer'    , '1Column' );
            $this->addOption("cleanup_time"    , 60*60*3 );

        }

        public function construct(){

            parent::construct();
            Event::connect( "form.handle" , array( $this , "handle" ) );

            //Load Css
            $Css = helper('css');
            $Css->add( "/yammon/public/widget/filemultiple/filemultiple.css" );

            //Load Javascript
            $Javascript = helper('Javascript');
            $Javascript->add( "/2.4.21/browserplus-min.js" );
            $Javascript->add( "/yammon/public/plupload/plupload.full.js?v=2" );
            $Javascript->add( "/yammon/public/widget/widget.js" );
            $Javascript->add( "/yammon/public/widget/filemultiple/filemultiple.js?v=4" );

        }

        public function handle()
        {

            $name = $this->getName();

            $request  = request('X-YAMMON-REQUEST'     , null);
            $instance = request('X-YAMMON-REQUEST-ID'  , null);
            $data     = request('X-YAMMON-REQUEST-DATA', null);

           //if( $instance != $name )
           //     return;

           if( $request == 'HELPER_FORM_ELEMENT_FILE_MULTIPLE:UPLOAD' ){
               try {
                   $this->handleUpload();
               } catch (Exception $e) {
                   prd($e->getMessage());
               }
           }

           if( $request == 'HELPER_FORM_ELEMENT_FILE_MULTIPLE:PREVIEW' ){
                $this->handlePreview( $data );
           }

           if( $request == 'HELPER_FORM_ELEMENT_FILE_MULTIPLE:DOWNLOAD' ){
                $this->handleDownload( $data );
           }

        }

        private function handlePreview( $id )
        {

            //Get the file for the id
            $file = $this->getFileForId( $id );

            if( !$file ){
                header('x', TRUE, 404);
                echo "404 NOT FOUND";
                exit();
            }

            //Send the image
            $im = new Image( $file );
            $im->resizeToBox( $this->getPreviewWidth() , $this->getPreviewHeight() );
            $im->send();
            exit;

        }

        private function handleDownload( $id )
        {

            //Get the file for the id
            $file = $this->getFileForId( $id );

            if( !$file ){
                header('x', TRUE, 404);
                echo "404 NOT FOUND";
                exit();
            }

            //Send the image
            FS::send( $path . DS . $file , true );
            exit;

        }

        private function handleUpload()
        {

            // HTTP headers for no cache etc
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            // Settings
            $TARGET_DIR = $this->getTargetDir();
            $maxFileAge = $this->getOption('cleanup_time');

            // 5 minutes execution time
            @set_time_limit(5 * 60);

            // Uncomment this one to fake upload time
            //usleep(5000);

            // Get parameters
            $chunk    = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
            $chunks   = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
            $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

            // Clean the fileName for security reasons
            $fileName = preg_replace('/[^\w\._]+/', '', $fileName);
            $fileName = $this->getFilenamePrefix().'-'.$fileName;

            $ext = strtolower( pathinfo($fileName, PATHINFO_EXTENSION) );

            //Only images
            if( !in_array($ext, array('jpg','jpeg','png')) ){
                exit();
            }

            // Make sure the fileName is unique but only if chunking is disabled
            if ($chunks < 2 && file_exists($TARGET_DIR . DIRECTORY_SEPARATOR . $fileName)) {
                $ext = strrpos($fileName, '.');
                $fileName_a = substr($fileName, 0, $ext);
                $fileName_b = substr($fileName, $ext);

                $count = 1;
                while (file_exists( $TARGET_DIR . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                    $count++;

                $fileName = $fileName_a . '_' . $count . $fileName_b;
            }


            // Remove old temp files
            if ($maxFileAge && is_dir($TARGET_DIR) && ($dir = opendir($TARGET_DIR))) {
                while (($file = readdir($dir)) !== false) {
                    $filePath = $TARGET_DIR . DIRECTORY_SEPARATOR . $file;

                    // Remove temp files if they are older than the max age
                    if( (filemtime($filePath) < time() - $maxFileAge))
                        @unlink($filePath);
                }

                closedir($dir);
            } else{
                echo('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
                exit;
            }

            // Look for the content type header
            if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
                $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

            if (isset($_SERVER["CONTENT_TYPE"]))
                $contentType = $_SERVER["CONTENT_TYPE"];

            // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
            if (strpos($contentType, "multipart") !== false) {
                if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                    // Open temp file
                    $out = fopen($TARGET_DIR . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
                    if ($out) {
                        // Read binary input stream and append it to temp file
                        $in = fopen($_FILES['file']['tmp_name'], "rb");

                        if ($in) {
                            while ($buff = fread($in, 4096))
                                fwrite($out, $buff);
                        } else{
                            echo('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                            exit;
                        }
                        fclose($in);
                        fclose($out);
                        @unlink($_FILES['file']['tmp_name']);
                    } else{
                        echo('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
                        exit;
                    }
                } else{
                    echo('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
                    exit;
                }
            } else {

                // Open temp file
                $out = fopen($TARGET_DIR . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen("php://input", "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else{
                        echo('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                        exit;
                    }
                    fclose($in);
                    fclose($out);
                } else{
                    echo('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
                    exit;
                }
            }

            //Check if we finished uploading the file
            if( $chunk == $chunks - 1 || ($chunks == 0 && $chunk == 0)) {

                //Resize the image
                try{
                    $im = new Image( $TARGET_DIR . DIRECTORY_SEPARATOR . $fileName );
                    $im->resizeToBox( $this->getMaxWidth(), $this->getMaxHeight() );
                    $im->save();
                }catch( Exception $ex ){
                    echo('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Invalid Image"}, "id" : "id"}');
                    exit;
                }

                //Save the image to the Session
                $id = $this->getIdForFile( $TARGET_DIR . $fileName );

                // Return JSON-RPC response
                echo( json_encode( array(
                    "jsonrpc" => "2.0" ,
                    "result"  => array(
                        'id'       => $id ,
                        'preview'  => $this->getPreviewUrl( $id )   ,
                        'download' => $this->getDownloadUrl( $id )
                    ) ,
                    "id"      => "id"
                )));
                exit;

            }

            echo('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
            exit;

        }

        public function getSessionKey()
        {
            return $this->getOption("session_key");
        }

        public function getFilenamePrefix()
        {
            return md5( Auth::getId() );
        }

        public function getTargetDir()
        {
            $target_dir = $this->getOption('target_dir');
            if( empty( $target_dir ))
                $target_dir = Yammon::getTemporaryPath('uploads');
            else
                FS::makeDirectory( $target_dir );

            return $target_dir;
        }

        public function getMaxWidth()
        {
            return $this->getOption('max_width');
        }

        public function getMaxHeight()
        {
            return $this->getOption('max_height');
        }

        public function getPreviewWidth()
        {
            return $this->getOption('preview_width');
        }

        public function getPreviewHeight(){
            return $this->getOption('preview_height');
        }

        public function cleanup()
        {

        }

        private function getIdForFile( $file )
        {
            $key     = $this->getSessionKey();
            $id      = uniqid();
            $uploads = Session::read( $key , array() );
            $uploads[ $id ] = $file;
            Session::write( $key , $uploads );
            return $id;
        }

        private function getFileForId( $id )
        {
            $key     = $this->getSessionKey();
            $uploads = Session::read( $key , array() );

            if( isset( $uploads[ $id ] ))
                return $uploads[ $id ];
            else
                return null;

        }

        private function getPreviewUrl( $id )
        {

            return url('.').qs( array(
                'X-YAMMON-REQUEST'      => 'HELPER_FORM_ELEMENT_FILE_MULTIPLE:PREVIEW' ,
                'X-YAMMON-REQUEST-ID'   => $this->getFullName() ,
                'X-YAMMON-REQUEST-DATA' => $id ,
            ));

        }

        private function getDownloadUrl( $id )
        {

            return url('.').qs( array(
                'X-YAMMON-REQUEST'      => 'HELPER_FORM_ELEMENT_FILE_MULTIPLE:DOWNLOAD' ,
                'X-YAMMON-REQUEST-ID'   => $this->getFullName() ,
                'X-YAMMON-REQUEST-DATA' => $id ,
            ));

        }

        public function render()
        {

            $id = $this->getDomId();

            $this->addClass('ym-form-file-multiple' );
            $attributes               = $this->getAttributes( true );
            $attributes               = array();
            $attributes["id"]         = $this->getDomId();
            $attributes['widget']     = 'FileMultiple';
            $attributes['widget-filemultiple-max_file_size']  =  $this->getOption('max_file_size');
            $attributes['widget-filemultiple-chunk_size']     =  $this->getOption('chunk_size');
            $attributes['widget-filemultiple-max_width']      =  $this->getOption('max_width');
            $attributes['widget-filemultiple-max_height']     =  $this->getOption('max_height');
            $attributes['widget-filemultiple-resize_quality'] =  $this->getOption('resize_quality');
            $attributes['widget-filemultiple-resize_quality'] =  $this->getOption('resize_quality');
            $attributes['widget-filemultiple-extensions']     = json_encode( $this->getOption('extensions') );

            $html = new Html();
            $html->open( 'div' , $attributes );
                $html->text( $this->renderHeader()     );
                $html->text( $this->renderEmptyQueue() );
                $html->text( $this->renderQueue()      );
            $html->close();
            return (string)$html;

        }

        public function renderHeader()
        {

            $icon = $this->getOption('icon');
            $text = $this->getOption('text');

            $html = new Html();
            $html->open( 'div' , array( 'class' => 'ym-form-file-multiple-header'));

                $html->open( 'table' );
                    $html->open( 'tr' );

                        $html->open( 'td' , array('class' => 'ym-form-file-multiple-header-button') );
                            $html->open( 'button' , array( 'class' => array('ym-form-file-multiple-start' , 'button' )));
                                $html->open('img' , array('src' => $icon) , null , true );
                                $html->text( $text );
                            $html->close('button');
                        $html->close( 'td' );

                        $html->open( 'td' , array('class' => 'ym-form-file-multiple-header-progress') );
                            $html->open( 'div' , array( 'class' => 'ym-form-file-multiple-progress-bar' ));
                                $html->open( 'div' , array( 'class' => 'ym-form-file-multiple-progress-bg' , 'style' => array('width' => '0%') ));
                                    $html->open( 'div' , array( 'class' => 'ym-form-file-multiple-progress-value'));
                                        $html->text('0%');
                                    $html->close('div');
                                $html->close('div');
                            $html->close('div');
                        $html->close( 'td' );

                    $html->close( 'tr' );
                $html->close( 'table' );


            $html->close('div');
            return (string)$html;
        }

        public function renderQueue()
        {

            $value = (array)$this->getValue();

            $html = new Html();
            $html->open('table' , array( 'class' => 'ym-form-file-multiple-queue') );
            $html->text( $this->renderItem( array() , true ) );

            foreach( $value as $k => $v ){
                $html->text( $this->renderItem( $v ) );
            }

            $html->close('table');
            return (string)$html;

        }

        public function renderEmptyQueue()
        {
            $empty = $this->getOption( 'empty' );

            $html = new Html();
            $html->open('div' , array( 'class' => 'ym-form-file-multiple-empty') );
                $html->text( $empty );
            $html->close('div');
            return (string)$html;

        }

        public function renderItem( $value = array() , $template = false )
        {

            //Get the image url
            $file           = isset( $value['file'] ) ? $value['file'] : null;
            $id             = $file ? $this->getIdForFile( $file ): null;
            $dom_name       = $this->getDomName();
            $id_dom_name    = $dom_name."[__id__][]";
            $download_url   = $id ? $this->getDownloadUrl( $id ) : null;
            $image_url      = $id ? $this->getPreviewUrl( $id )  : null;

            $classes   = array();

            if( $template )
                $classes[] = 'ym-form-file-multiple-queue-template';
            else
                $classes[] = 'ym-form-file-multiple-queue-item';

            $html = new Html();
            $html->open( 'tr' , array( 'class' => $classes ));

                $html->open('td' , array( 'class' => 'ym-form-file-multiple-queue-item-preview-column') );

                    $html->open('a' , array('href' => $download_url , 'class' => 'ym-form-file-multiple-queue-item-preview' ) );
                        $html->open('img' , array('src' => $image_url ) );
                    $html->close('a');

                    $html->open('input' , array( 'type' => 'hidden' , 'name' => $id_dom_name , 'value' => $id , 'class' => 'ym-form-file-multiple-queue-item-id'), null , true );

                $html->close('td');

                $html->open('td' , array('class' => 'ym-form-file-multiple-queue-item-details-column') );

                    $html->text( $this->renderContent( $value ) );

                    $html->open('div' , array('class' => 'ym-form-file-multiple-queue-item-actions') );

                        $html->open('a' , array('class' => 'ym-form-file-multiple-queue-item-delete') );
                            $html->open('i' , array( 'class' => 'fa fa-trash', 'aria-hidden' => 'true' ) );
                            $html->close('i');
                            $html->text(t('Delete'));
                        $html->close('a');

                        $html->open('a' , array('class' => 'ym-form-file-multiple-queue-item-moveup') );
                            $html->open('i' , array( 'class' => 'fa fa-arrow-up', 'aria-hidden' => 'true' ) );
                            $html->close('i');
                            $html->text(t('Move Up'));
                        $html->close('a');

                        $html->open('a' , array('class' => 'ym-form-file-multiple-queue-item-movedown') );
                            $html->open('i' , array( 'class' => 'fa fa-arrow-down', 'aria-hidden' => 'true' ));
                            $html->close('i');
                            $html->text(t('Move Down'));
                        $html->close('a');

                    $html->close('div');

                $html->close('td');

            $html->close('tr');

            return (string) $html;

        }

        public function renderContent( $value = array() )
        {

            $dom_name       = $this->getDomName();
            $desc_dom_name  = $dom_name."[description][]";
            $id_dom_name    = $dom_name."[id][]";

            $html = new Html();
            $html->open('div');

                $html->open('input' , array( 'type' => 'hidden' , 'name' => $id_dom_name , 'value' => @$value['id'] ), null , true );

                $html->open('label' , array('class' => 'ym-form-label' )  );
                    $html->text(t('Image Description'));
                $html->close('label');

                $html->open('div' , array('class' => 'ym-form-description' )  );
                    $html->text( t('Please enter a short description of the image') );
                $html->close('div');

                $html->open('input' , array('name' => $desc_dom_name , 'value' => @$value['description'] , 'class' => 'ym-form-text' ) , null , true );

                $html->open('div' , array('class' => 'ym-form-example' )  );
                    $html->text( t('Front view, side view, close up, etc...') );
                $html->close('div');

            $html->close('div');
            return (string) $html;
        }

        public function setValue( $value )
        {

            $newValue = array();

            //Convert Files to ids
            $value = (array) $value;
            foreach( $value as $k => $v ){

                if( !isset( $v['file'] )){
                    continue;
                }

                $item       = array();
                $item       = $v;
                $item['__id__'] = $this->getIdForFile( $v['file'] );
                unset( $item['file'] );
                $newValue[] = $item;
            }

            return parent::setValue( $newValue );
        }

        public function getSubmissionValue(){

            $value = parent::getSubmissionValue();
            if( $value === null )
                return null;

            //Transpose values
            $return = array();
            foreach( $value['__id__'] as $k => $id ){
                $item = array();

                if( empty($id ) )
                    continue;

                foreach( $value as $k2 => $v2 ){
                    $item[ $k2 ] = $v2[$k];
                }

                $return[] = $item;
            }

            return $return;

        }

        public function getValue(){

            $value = parent::getValue();

            //Return null
            if( $value === null )
                return null;

            //Convert ids to files
            foreach( $value as $k => &$v ){
                $file = $this->getFileForId( $v['__id__'] );

                if( !$file ){
                    unset( $value[$k] );
                    continue;
                }

                $v['file'] = $this->getFileForId( $v['__id__'] );
                unset( $v['__id__'] );

            }

            if( empty( $value ) )
                return null;

            return $value;

        }

    }
