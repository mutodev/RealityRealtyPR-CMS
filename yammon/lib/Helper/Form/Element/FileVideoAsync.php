<?php

    class Helper_Form_Element_FileVideoAsync extends Helper_Form_Element_FileAsync {

        public function setupOptions(){
            parent::setupOptions();

            $this->addOption("max_file_size"   , '100mb'  );
            $this->addOption("file_filters"    , array(t("Video files") => "WMV,3GP,3GPP,AVI,MOV,MP4,MPEG,MPEGPS,MPEG4,MPEG2,FLV,MKV") );
            $this->addOption("class_file_save" , 'Helper_Form_Element_File_VideoValue');
            $this->addClass('form_element_plupload_filebox_video' );    
            $this->setOption('icon' , '/yammon/public/form/img/actions/video.png' );

        }

    }