<?php

    abstract class Helper_Form_Renderer_Label extends Helper_Form_Renderer{

        static public function factory( $type ){
            $type = $type ? $type : 'Inline';
            return Helper_Form_Renderer::factory( 'Label' , $type );        
        }

    }
