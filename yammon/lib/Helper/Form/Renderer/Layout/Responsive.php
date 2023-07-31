<?php

    class Helper_Form_Renderer_Layout_Responsive extends Helper_Form_Renderer_Layout{

        public function render( $element , $options ){
                                                                
            $elements       = $element->getElements();
            $classes        = $this->getClasses( $element , $options  );
            $style          = $this->getStyle( $element , $options  );            
            $output         = array();
            $columns        = isset( $options['columns']) ? $options['columns'] : 2;
            $rows           = isset( $options['rows'])    ? $options['rows']    : null;
            $align          = isset( $options['align'])   ? $options['align']   : null;
            $valign         = isset( $options['valign'])  ? $options['valign']  : null;
            $dcolstyle      = isset( $options['colstyle'])   ? $options['colstyle']  : null;
                        
            //Remove hidden elements form the elements array
            $hidden_elements = array();
            foreach( $elements as $k => $element ){
                if( $element instanceOf Helper_Form_Element_Hidden ){
                    $hidden_elements[] = $element;
                    unset( $elements[$k] );
                }
            }
                                         
            //Transpose elements if rows are specified
            if( $rows !== null ){
                                                
                //Create a transposed two dimensional array
                $grid_elements = array();
                $i = 0; $j = 0;
                foreach( $elements as $element ){
                                
                    if( $j >= $rows ){
                      $j = 0; 
                      $i++;
                    }

                    @$grid_elements[ $j ][ $i ] = $element;
                    $j++;
                }
                                    
                //Flattern elements back            
                $new_elements = array();
                for( $i = 0 ; $i < count( $grid_elements ) ; $i++ )
                    for( $j = 0 ; $j < count( $grid_elements[ $i ] ) ; $j++ )
                        $new_elements[] = $grid_elements[ $i ][ $j ];

                $elements = $new_elements;
                $columns  = count($grid_elements[0]);
                                
            }

            //Render Hidden Elements
            foreach( $hidden_elements as $element ){
                $output[] = $element->renderBox();   
            }
                             
            $col_size = ceil( 12 / $columns );

            //Render Elements
            $i      = 0;
            $rowodd = false;
            $rowspanCounters = array();
            $count  = 0;
            $element_count = count( $elements );
            $output[] = "\t<div class='$classes container-fluid' style='$style'>";
                foreach( $elements as $k => $element ){

                    $count++;

                    if( $i == 0 )  {      
                       $rowodd        = !$rowodd;
                       $row_classes   = array();
                       $row_classes[] = "row ym-form-layout-grid-row";
                       $row_classes[] = $rowodd ? "ym-form-layout-grid-row-odd" : "ym-form-layout-grid-row-even";
                       $row_classes   = implode( " " , $row_classes );
                       $output[] = "\t\t<div class='$row_classes'>";
                    }
                    
                    $colspan        = min( $element->getOption( "colspan"   , 1 ) , $columns );
                    $colclass       = $element->getOption( "colclass"  );
                    $colwidth       = $element->getOption( "colwidth"  );
                    $colheight      = $element->getOption( "colheight" );
                    $colalign       = $element->getOption( "colalign"  , $align );
                    $colvalign      = $element->getOption( "colvalign" , $valign );
                    $colstyle       = $element->getOption( "colstyle"  , $dcolstyle );
                    $rowspan        = $element->getOption( "rowspan" , 1 );
                                        
                    if( $rowspan > 1 )
                        $rowspanCounters[] = $rowspan - 1;

                    $colclass .= ' col-xs-12 col-sm-'.$col_size;

                    //Get the style for the column
                    $colstyle = (array) $colstyle;
                    if( $colwidth )
                        $colstyle[] = "width: $colwidth";

                    if( $colheight )
                        $colstyle[] = "height: $colheight";

                    if( $colalign )
                        $colstyle[] = "text-align: $colalign";

                    if( $colvalign )
                        $colstyle[] = "vertical-align: $colvalign";

                    $colstyle = implode( ';' , $colstyle );

                    //Get the attributes for the column
                    $attributes = array();
                    
                    if( $colstyle )
                        $attributes[] = "style='".$colstyle."'";

                    if( $colclass )
                        $attributes[] = "class='".$colclass."'";

                    if( $colspan != 1 )
                        $attributes[] = "colspan='".$colspan."'";

                    if( $rowspan != 1 )
                        $attributes[] = "rowspan='".$rowspan."'";
                       
                    $attributes = implode(' ' , $attributes );                    
     
                    $output[] = "\t\t\t<div $attributes>";
                        $output[] = $element->renderBox();
                    $output[] = "\t\t\t</div>";

                    $i = ($i + $colspan) % $columns;

                    if( $i == 0 || $count == $element_count ){
                        $i = count($rowspanCounters);

                        foreach( $rowspanCounters as $j => $rowspanCounter) {
                            $rowspanCounter--;
                            if ( $rowspanCounter < 1 )
                                unset( $rowspanCounters[$j] );
                        }

                        $output[] = "\t\t</div>";
                    }

                                       
                }
                                        
            $output[] = "\t</div>";        
            $output = implode( "\n" , $output );
            return $output;
        }
        
    }
