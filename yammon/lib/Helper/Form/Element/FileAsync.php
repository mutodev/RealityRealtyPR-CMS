<?php

    class Helper_Form_Element_FileAsync extends Helper_Form_Element_Valued{

        public function setupOptions(){
            parent::setupOptions();

            $this->addOption("class_file_save" , 'Helper_Form_Element_File_Value');
            $this->addOption("max_file_size"   , '10mb'  );
            $this->addOption("file_filters"    , array() );
            $this->addOption("chunk_size"      , '1mb'   );
            $this->addOption("disable_delete"  , false );
            $this->addOption("path_encode_key" , Configure::read("security.token", 'DEFAULT-KEY' ) );
            $this->addOption("filename_length" , 255 );
            $this->addOption("text"            , t('Browse') );
            $this->addOption("icon"            , '/yammon/public/form/img/actions/file.png' );
        }

        public function construct(){
            parent::construct();
            Event::connect( "form.handle" , array( $this , "handle" ) );

            //Load Css
            $Css = helper('css');
            $Css->add( "/yammon/public/form/css/fileasync.css" );

            //Load Javascript
            $Javascript = helper('Javascript');
            $Javascript->add( "http://bp.yahooapis.com/2.4.21/browserplus-min.js" );
            $Javascript->add( "/yammon/public/plupload/plupload.full.js" );
            $Javascript->add( "/yammon/public/widget/widget.js" );
            $Javascript->add( "/yammon/public/widget/widget-fileasync.js" );

        }

        public function handle( ){

           //Upload File
           if( post('X-YAMMON-REQUEST', null) == 'HELPER_FORM_ELEMENT_FILEASYNC' ){

             $element_id = post('ELEMENT');

             //Check that its my request
             if( $element_id != $this->getStaticDomId() ){
                return;
             }

             $fileInfo = $this->upload();
             $template = $this->renderTemplateEdit( $fileInfo );
             $template = str_replace( array('"', "\n") , array("'", ''), $template);

             // Return JSON-RPC response
             die( json_encode( array(
                 'jsonrpc'  => '2.0' ,
                 'id'       => 'id' ,
                 'filename' => $this->encodeFile( $fileInfo ),
                 'edit_tpl' => $template,
                 'result'   => null
             )));

           }

           //Download File
           if ( get('download') == 'FileAsync' && get('element_id') == $this->getStaticDomId() ) {

                $FileSystem = Configure::read("filesystem");

                $fileInfo      = $this->decodeFile( get('file') );
                $path          = $fileInfo['path'];
                $original_name = $fileInfo['properties']['original_name'];

                header("Pragma: private");
                header("Cache-Control: no-cache");
                header('Expires: 0');
                header('Content-Type: ' . $FileSystem->getMimetype($path) );
                header('Content-Length: ' . $FileSystem->getSize($path) );
                header("Content-Disposition: attachment; filename=\"$original_name\";");

                echo $FileSystem->read($path);
                exit;
           }
        }

        //Hack: Need the dom id for the ajax call without the numbers
        //of the dynamic fields of the repeat element
        public function getStaticDomId() {

            $names   = array();
            $parents = $this->getParents();
            foreach( $parents as $parent ){
                if( !($parent->getParent() instanceof Helper_Form_Element_Repeat || $parent->getParent() instanceof Helper_Form_Element_I18n) )
                    $names[] = $parent->getName();
            }

            $names[]  = $this->getName();
            $names    = implode( "_" , $names );
            $names    = strtolower( $names );

            return $names;
        }

        public function setValue($value) {

            $FileSystem    = Configure::read("filesystem");
            $classFileSave = $this->getOption("class_file_save");

            if ($value) {

                if (is_array($value) && isset($value['path']) && $FileSystem->has($value['path'])) {

                    $File = new $classFileSave($value['path']);
                    $File->setProperties($value['properties']);
                    $File->setProperty('is_new', false);

                    $value = $File;
                }
                else if (!is_object($value) && $FileSystem->has($value)) {

                    $File = new $classFileSave($value);
                    $File->setProperties(array('original_name' => basename($value)));
                    $File->setProperty('is_new', false);

                    $value = $File;
                }
                else if (is_object($value)) {
                    $value = $value;
                }
                else {
                    $value = '';
                }
            }

            return parent::setValue($value);
        }

        public function getValue() {

            $class_file_save = $this->getOption("class_file_save");

            //Get the parent value
            $value = parent::getValue();

            if ( strpos( $value, '[E]' ) === false )
                return $value;

            else if ( !$value )
                return null;

            //Decode
            $fileInfo   = $this->decodeFile($value);
            $path       = $fileInfo['path'];
            $properties = $fileInfo['properties'];

            if ( !$path )
                return null;

            //File Class
            $File = new $class_file_save( $path );
            $File->setProperties( $properties );


            return $File;
        }

        public function isPresent( $File ){

            $fileInfo = $this->fileArray( $File );

            return ( $fileInfo['path'] );
        }

        public function getUploadKey(){
            $domname = $this->getDomName();
            return md5($domname);
        }

        public function render(  ){

            //Get the options of the form element
            $File            = $this->getValue();
            $fileInfo        = $this->fileArray( $File );
            $encrypted_file  = $this->encodeFile( $fileInfo );
            $domid           = $this->getDomId( );
            $domname         = $this->getDomName();
            $style           = $this->getOption( "style" );
            $max_file_size   = $this->getOption( "max_file_size" );
            $file_filters    = $this->getOption( "file_filters" );
            $chunk_size      = $this->getOption( "chunk_size" );
            $max_width       = $this->getOption( "max_width" );
            $max_height      = $this->getOption( "max_height" );

            //Prepare the classes that this element will have
//            $this->addClass("form_element_plupload");
//            $this->addContainerClass("form_element_box_plupload");

            //File Filters
            $json_filters = array();
            foreach($file_filters as $title => $extensions) {
                $json_filter['title']      = $title;
                $json_filter['extensions'] = $extensions;
                $json_filters[] = $json_filter;
            }

            //Prepare hidden input
            $attributes             = array();
            $attributes["id"]       = $domid;
            $attributes["name"]     = $domname;
            $attributes["class"][]  = "form_element_plupload";
            $attributes["value"]    = $encrypted_file;
            $attributes["type"]     = "hidden";
            $attributes["key"]      = $this->getUploadKey();

            //Attributes DIV
            $this->addClass('form_element_plupload_filebox' );
            $attributes_div = $this->getAttributes( true );
            $attributes_div['widget']                         = 'FileAsync';
            $attributes_div['widget-fileasync-element_id']    = $this->getStaticDomId();
            $attributes_div['widget-fileasync-max_file_size'] = $max_file_size;
            $attributes_div['widget-fileasync-file_filters']  = json_encode($json_filters);
            $attributes_div['widget-fileasync-chunk_size']    = $chunk_size;
            $attributes_div['widget-fileasync-max_width']     = $max_width;
            $attributes_div['widget-fileasync-max_height']    = $max_height;

            //Set the content options for rendering
            $Html = new Html();
            $Html->open( "input" , $attributes );
            $Html->open( 'div'   , $attributes_div );
                $Html->text( $this->renderTemplateNew($fileInfo) );
                $Html->text( $this->renderTemplateUploading($fileInfo) );
                $Html->text( $this->renderTemplateEdit($fileInfo) );
            $Html->close( 'div'   , $attributes_div );

            //Do the Actual Rendering
            return $Html->get();
        }

        protected function renderTemplateNew( $fileInfo ) {

            //Get the options of the form element
            $content        = array();

            $text      = t($this->getOption('text'));
            $icon      = $this->getOption('icon');

            $content[] = '  <div class="plupload_state plupload_new" style="'.($fileInfo['path'] ? 'display: none;' : '').'">';
            $content[] = '    <a class="plupload_button_edit button" href="javascript:void">';
            if( $icon ){
                $content[] = '<img src="'.$icon.'" />';
            }
            $content[] = $text;
            $content[] = '</a>';
            $content[] = '  </div>';

            return implode( "\n" , $content );
        }

        protected function renderTemplateUploading( $fileInfo ) {

            //Get the options of the form element
            $content        = array();

            $content[] = '  <div class="plupload_state plupload_uploading" style="display: none;">';
            $content[] = '    <span class="plupload_filename"></span>';
            $content[] = '    <span class="plupload_filesize"></span>';
            $content[] = '    <div class="plupload_loadingbar"> <div></div> </div>';
            $content[] = '    <div class="plupload_buttons">';
            $content[] = '      <a class="plupload_button_cancel" href="javascript:void(0)">'.t("cancel").'</a>';
            $content[] = '    </div>';
            $content[] = '  </div>';

            return implode( "\n" , $content );
        }

        protected function renderTemplateEdit( $fileInfo ) {

            //Get the options of the form element
            $content         = array();
            $disable_delete  = $this->getOption('disable_delete');
            $filename_length = $this->getOption('filename_length');

            //Filename
            $filename = $fileInfo['properties']['original_name'];

            //Short filename
            if (strlen($filename) > $filename_length) {
                $filename = substr($filename, 0, floor(($filename_length - 3) / 2)) . '...' . substr($filename, ceil(($filename_length - 3) / 2) * -1);
            }

            $content[] = '  <div class="plupload_state plupload_edit" style="'.(!$fileInfo['path'] ? 'display: none;' : '').'">';
            $content[] = '    <a class="plupload_filename plupload_button_download" title="'.$fileInfo['properties']['original_name'].'" href="'.$this->getDownloadUrl($fileInfo).'">'.$filename.'</a>';
            //$content[] = '    <span class="plupload_filesize">- '.$this->readablizeBytes( filesize( $fileInfo['path'] )  ).'</span>';
            $content[] = '    <div class="plupload_buttons">';

            if (!$disable_delete)
                $content[] = '      <a class="plupload_button_delete" href="javascript:void(0)">'.t("delete").'</a>';

            $content[] = '    </div>';
            $content[] = '  </div>';

            return implode( "\n" , $content );
        }

        protected function getDownloadUrl( $path ) {
            return url('.') . qs( array('download' => 'FileAsync' , 'element_id' => $this->getStaticDomId() , 'file' => $this->encodeFile( $path ) ) );
        }

        protected function getTempPath() {
            return "writable/tmp/uploads";
        }

        protected function readablizeBytes($bytes) {

            if ( !is_numeric($bytes) OR $bytes <= 0 )
              return '';

            $s = array('bytes', 'kb', 'MB', 'GB', 'TB', 'PB');
            $e = floor( log($bytes) / log(1024) );
            return round( $bytes / pow(1024, floor($e) ) , 2) . ' ' . $s[$e];
        }

        protected function encodeFile( $value ) {

            $value = serialize($value);

            $key    = $this->getOption("path_encode_key");
            $key    = sha1( $key );
            $strLen = strlen($value);
            $keyLen = strlen($key);
            $j      = 0;
            $hash   = '';
            for ($i = 0; $i < $strLen; $i++) {
                $ordStr = ord(substr($value,$i,1));
                if ($j == $keyLen) { $j = 0; }
                $ordKey = ord(substr($key,$j,1));
                $j++;
                $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
            }

            return '[E]' . $hash;
        }

        protected function decodeFile( $value ) {

            $value  = substr( $value, 3 );
            $key    = $this->getOption("path_encode_key");
            $key    = sha1( $key );
            $strLen = strlen($value);
            $keyLen = strlen($key);
            $j      = 0;
            $hash   = '';
            for ($i = 0; $i < $strLen; $i+=2) {
                $ordStr = hexdec(base_convert(strrev(substr($value,$i,2)),36,16));
                if ($j == $keyLen) { $j = 0; }
                $ordKey = ord(substr($key,$j,1));
                $j++;
                $hash .= chr($ordStr - $ordKey);
            }

            $hash = unserialize($hash);

            return $hash;
        }

        protected function fileArray( $file, $properties = null ) {

            $fileInfo               = array();
            $fileInfo['path']       = is_object($file) ? $file->getPath() : $file;
            $fileInfo['properties'] = is_object($file) ? $file->getProperties() : $properties;

            return $fileInfo;
        }

        protected function upload( ) {

            $FileSystem = Configure::read("filesystem");
            $sysTempDir = sys_get_temp_dir();
            $targetDir  = $this->getTempPath();
            $maxFileAge = 60*60;
            $maxDirAge  = 60*60*24;

            // 5 minutes execution time
            @set_time_limit(5 * 60);

            // HTTP headers for no cache etc
            //header('Content-type: text/plain; charset=UTF-8');
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            // Uncomment this one to fake upload time
            //sleep( 9 );

            // Get parameters
            $chunk    = isset($_REQUEST["chunk"])   ? intval($_REQUEST["chunk"]) : 0;
            $chunks   = isset($_REQUEST["chunks"])  ? intval($_REQUEST["chunks"]) : 0;
            $fileName = isset($_REQUEST["name"])    ? $_REQUEST["name"] : '';
            $fileID   = isset($_REQUEST["FILE_ID"]) ? $_REQUEST["FILE_ID"] : '';

            // Clean the fileName for security reasons
            $fileName        = preg_replace('/[^\w\._]+/', '', $fileName);
            $fileName_upload = $fileID . '_' . preg_replace('/[^\w\._]+/', '', $fileName);

            //Path
            $sysTempPath = $sysTempDir . DS . $fileName_upload;
            $targetPath  = $targetDir . DS . $fileName_upload;

            //Remove Chunk Files
            foreach($FileSystem->listWith(['timestamp'], $targetDir) as $file) {

                // Remove temp files if they are older than the max age
                if (preg_match('/\\.tmp$/', $file['basename']) && ($file['timestamp'] < time() - $maxFileAge)) {
                    $FileSystem->delete($file['path']);
                }
            }

            // Look for the content type header
            if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
                $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
            elseif (isset($_SERVER["CONTENT_TYPE"]))
                $contentType = $_SERVER["CONTENT_TYPE"];
            else
                $contentType = "";

            //Check the type

            // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
            if (strpos($contentType, "multipart") !== false) {

                if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                    // Open temp file
                    $out = fopen($sysTempPath, $chunk == 0 ? "wb" : "ab");
                    if ($out) {

                        // Read binary input stream and append it to temp file
                        $in = fopen($_FILES['file']['tmp_name'], "rb");

                        if ($in) {
                            while ($buff = fread($in, 4096)){
                                fwrite($out, $buff);
                            }
                        } else
                            die( json_encode( array(
                                'jsonrpc' => '2.0' ,
                                'id'      =>  'id' ,
                                'error'   => array(
                                    'code'   => 101 ,
                                    'message' => 'Failed to open input stream.' ,
                                ),
                            )));

                        fclose($out);
                        @unlink($_FILES['file']['tmp_name']);
                    } else
                        die( json_encode( array(
                            'jsonrpc' => '2.0' ,
                            'id'      =>  'id' ,
                            'error'   => array(
                                'code'   => 102 ,
                                'message' => 'Failed to open output stream.' ,
                            ),
                        )));
                } else
                    die( json_encode( array(
                        'jsonrpc' => '2.0' ,
                        'id'      =>  'id' ,
                        'error'   => array(
                            'code'   => 103 ,
                            'message' => 'Failed to move uploaded file.' ,
                        ),
                    )));

            } else {

                // Open temp file
                $out = fopen($sysTempPath, $chunk == 0 ? "wb" : "ab");

                if ($out) {

                    // Read binary input stream and append it to temp file
                    $in = fopen("php://input", "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die( json_encode( array(
                            'jsonrpc' => '2.0' ,
                            'id'      =>  'id' ,
                            'error'   => array(
                                'code'   => 101 ,
                                'message' => 'Failed to open input stream.' ,
                            ),
                        )));

                    fclose($out);
                } else
                    die( json_encode( array(
                        'jsonrpc' => '2.0' ,
                        'id'      =>  'id' ,
                        'error'   => array(
                            'code'   => 102 ,
                            'message' => 'Failed to open output stream.' ,
                        ),
                    )));
            }

            //Done
            if ($chunks === 0 || $chunk+1 === $chunks) {

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $sysTempPath);
                $fileConfig = array('visibility' => 'public', 'mimetype' => $mime);

                $stream = fopen($sysTempPath, 'r+');
                $FileSystem->putStream($targetPath, $stream, $fileConfig);

                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            //Tramsform upload
            return $this->fileArray($targetPath, array('original_name' => $fileName));
        }

        public function getTranslationStrings()
        {

            $strings = parent::getTranslationStrings();
            $string  = $this->getOption('text');
            if( $string ) $strings[] = $string;

            return $strings;

        }

    }
