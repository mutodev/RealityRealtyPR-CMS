<?php

    class Predesigns extends ListMax_AppCollection{

        protected $tableName  = "Predisenadas";
        protected $primaryKey = "id";
        protected $itemClass  = "Predesigned";
        protected $filters = array(
            "name"         ,
            "bathfrom"     ,
            "bathto"       ,        
            "roomsfrom"    ,
            "roomsto"      ,
            "sqftfrom"     ,
            "sqmtfrom"     
        );

        protected $orders = array(
            "name" => "nombre_modelo" ,
            "rooms" => "hab" , 
            "baths" => "ban" ,
            "sqft"  => "sqr_ft_aprox"
        );


        /* --------------------------------------------- */
        function getSelect(){
            
            $db    = ListMax::db();
            $select = $db->select();
            $select->from("Predisenadas" , array(
                'name' => 'nombre_modelo' ,
                'id' ,
                'id_usuario' ,
                'numero_interno' ,
                'nombre_modelo' ,
                'tipo' ,
                'construccion' ,
                'hab' ,
                'max_hab' ,
                'ban' ,
                'medios_ban' ,
                'sqr_ft_aprox' ,
                'recibidor' ,
                'sala' ,
                'comedor' ,
                'cocina' ,
                'cocina_comedor' ,
                'family' ,
                'laundry' ,
                'walkincloset' ,
                'marquesina' ,
                'marquesina_doble' ,
                'marquesina_sencilla' ,
                'terraza' ,
                'dimensiones_x' ,
                'dimensiones_y' ,
                'balcon' ,
                'publicar' ,
                'precio' ,
                'tour_url' ,
                'notas' ,
                'notes' ,
                'foto1' ,
                'foto2' ,
                'foto3' ,
                'foto4' ,
                'foto5' ,
                'foto6' ,
                'foto7' ,
                'plano1' ,
                'plano2' ,
                'plano3' ,
                'plano4' ,
                'pdf_file1' ,
                'pdf_file2' ,
                'id_pcat' ,
                'v'
            ));
            $select->join("Predisenadas_Cats" , "Predisenadas.id_pcat = Predisenadas_Cats.id"       , array("category" => "predcat" ) );						
            $select->where("publicar = 'si'");
            return $select;
            
        }
        /* --------------------------------------------- */
        function filter( &$select ){
         
            //Name
            if( $this->isFilteredBy("name") ){
                $name = $this->getFilterBy("name");
                if( is_numeric( $name ) )
    			    $select->where("Predisenadas.id like ?" , $name );
        		elseif( !empty($name) )
                    $select->where("Predisenadas.nombre_modelo like ?" , '%'.$name.'%' );                
			}
                            
            //Baths From
            if( $this->isFilteredBy( "bathfrom" ) ){
                $bath_from = $this->getFilterBy("bathfrom");
			    $select->where( "ban >= ?" , $bath_from );
			}

            //Baths To
            if( $this->isFilteredBy( "bathto" ) ){
                $bath_to = $this->getFilterBy("bathto");
			    $select->where( "ban <= ?" , $bath_to );
			}
            
            //Rooms From
            if( $this->isFilteredBy( "roomsfrom" ) ){
                $rooms_from = $this->getFilterBy("roomsfrom");
			    $select->where( "hab >= ?" , $rooms_from );
			}

            //Rooms To
            if( $this->isFilteredBy( "roomsto" ) ){
                $rooms_to = $this->getFilterBy("roomsto");
			    $select->where( "hab <= ?" , $rooms_to );
			}
            
            //Sq Feet From
            if( $this->isFilteredBy( "sqftfrom" ) ){
                $sqft_from = $this->getFilterBy("sqftfrom");
                $select->where("sqr_ft_aprox >= ?" , $sqft_from );
            }
            
            //Sq Meters
            if( $this->isFilteredBy( "sqmtfrom" ) ){
                $sqmt_from = $this->getFilterBy("sqmtfrom");
                $select->where("sqr_ft_aprox >= ?" , $sqmt_from );
            }
                                                                                                                                                           
        }
        /* --------------------------------------------- */
        
    }
  

    
?>
