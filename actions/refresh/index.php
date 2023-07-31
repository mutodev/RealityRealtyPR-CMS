<?php
    
    vendor("ListMax");
    global $ListMax;

    $Properties = $ListMax->getProperties();

    $select = $Properties->getSelect2();

    //Get the Column Names
    $columns = $select->getPart('columns');
    $column_names = array();
    foreach( $columns as $v ){    
        $column_name    = !empty( $v[2] ) ? $v[2] : $v[1];
        $column_names[] = $column_name;
    }

    //Get the Materialized view name
    $mv_name     = "Propiedades_mv";
    $mv_name_log = "Propiedades_mv_log";                    

    //Get the current time
    $now = mktime();
    
    $sql = "UPDATE Propiedades SET last_update = now() WHERE last_update = '0000-00-00 00:00:00'";
    ListMax::query( $sql , null , null );

    //Get the last log entry
    $sql          = "SELECT date 
                     FROM $mv_name_log 
                     ORDER BY date DESC LIMIT 1";
    $last_refresh = ListMax::query( $sql , null , null );
    $last_refresh = isset( $last_refresh[0]['date'] ) ?  strtotime($last_refresh[0]['date']) : 0;

    //Save to the log
    $sql   = "INSERT INTO `$mv_name_log`( `date` ) 
              VALUES( '".date('Y-m-d H:i:s' , $now )."' )";
    ListMax::query( $sql , null , null );

    //Get changed rows
    $sql = "SELECT id
            FROM Propiedades 
            WHERE  expira > 100
            AND    ( last_update >= '".date('Y-m-d H:i:s' , $last_refresh - 5*24*60*60 )."'
            OR record_created >= '".date('Y-m-d H:i:s' , $last_refresh - 5*24*60*60 )."')";

    $changed = ListMax::query( $sql , null , null );   
    foreach( $changed as $k => $v ){
        $changed[$k] = $v['id'];
    }
    
    $changed[] = 0;                
    $changed   = implode(",",$changed);

    //Delete old rows
    $sql = "DELETE FROM `$mv_name`
            WHERE ( 
              (Propiedades_mv.expires ) < ".date('Ymd' , $now )."
              OR Propiedades_mv.id IN ( $changed )
            )"; 
                   
    ListMax::query( $sql , null , null );                
    
    //Add new rows
    $select->where("Propiedades.id IN( $changed )" );   
    $select->group("Propiedades.id");      
    
    $sql   = array();
    $sql[] = "INSERT INTO `$mv_name` ";
    $sql[] = (string)$select;
    $sql   = implode( "\n" , $sql );


    ListMax::query( $sql , null , null );            

    //This is a hack
    $fields = "key=fixEMFCUKytrue4";
    $url = "https://rpm.realityrealtypr.com/cjobs/fix-exp-hack.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_exec($ch);
    curl_close ($ch);                       
    
    //Update random number
    $sql = "UPDATE Propiedades_mv SET random = RAND()";
    ListMax::query( $sql , null , null );

    echo 'Changed: '.$changed;
    exit();