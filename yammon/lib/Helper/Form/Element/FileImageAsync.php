<?php

    class Helper_Form_Element_FileImageAsync extends Helper_Form_Element_FileAsync {

        public function setupOptions(){
            parent::setupOptions();

            $this->addOption("max_file_size"   , '2mb'  );
            $this->addOption("file_filters"    , array(t("Image files") => "jpg,gif,png") );
            $this->addOption("class_file_save" , 'Helper_Form_Element_File_ImageValue');
            $this->addOption("thumb_width"     , 100);
            $this->addOption("thumb_height"    , null);
            $this->addOption("max_width"       , null );
            $this->addOption("max_height"      , null  );
            $this->addOption("icon"            , '/yammon/public/form/img/actions/image.png' );
            $this->addClass('form_element_plupload_filebox_image' );

        }

        protected function renderTemplateUploading( $fileInfo ) {

            //Get the options of the form element
            $content        = array();

            $content[] = '  <div class="plupload_state plupload_uploading" style="display: none;">';
            $content[] = '    <span class="plupload_filename"></span>';
            $content[] = '    <div class="plupload_loadingbar"> <div></div> </div>';
            $content[] = '    <div class="plupload_buttons">';
            $content[] = '      <a class="plupload_button_cancel" href="#">'.t("cancel").'</a>';
            $content[] = '    </div>';
            $content[] = '  </div>';

            return implode( "\n" , $content );
        }

        protected function renderTemplateEdit( $fileInfo ) {

            //Get the options of the form element
            $content        = array();
            $thumbnaiStyle  = array();
            $disable_delete = $this->getOption('disable_delete');

            //Thumbnail Size
            if ( $this->getOption('thumb_width') ) {
                $thumbnaiStyle[] = 'max-width: ' . $this->getOption('thumb_width') . 'px;';
            }
            if ( $this->getOption('thumb_height') ) {
               $thumbnaiStyle[] = 'max-height: ' . $this->getOption('thumb_height') . 'px;';
            }

            $content[] = '  <div class="plupload_state plupload_edit" style="'.(!$fileInfo['path'] ? 'display: none;' : '').'">';
            $content[] = '    <a class="plupload_button_download" href="'.$this->getDownloadUrl($fileInfo).'"><img src="'.$this->getDownloadUrl($fileInfo).'" style="'.implode(' ', $thumbnaiStyle).'" /></a>';
            $content[] = '    <div class="plupload_buttons">';

            if (!$disable_delete)
                $content[] = '      <a class="plupload_button_delete" href="#">'.t("delete").'</a>';

            $content[] = '    </div>';
            $content[] = '  </div>';

            return implode( "\n" , $content );
        }
    }
