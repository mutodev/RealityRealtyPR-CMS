<?php

    class Helper_Form_Element_File_VideoValue extends Helper_Form_Element_File_Value {

        protected function moveFile( $location ) {

            $saved = parent::moveFile( $location );

            if ($saved)
                $this->setProperty('status', 'WAITING_COMPRESSION');

            return $saved;
        }

    }