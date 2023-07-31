<?php

Error::stop();
ini_set('memory_limit' , -1 );
set_time_limit(0);

//Get the Files
$excludes   = Yammon::getVendorsPaths();
$excludes[] = Yammon::getWritablePath();
$excludes[] = YAMMON_PATH;
foreach( $excludes as $k => $v ){
    $excludes[ $k ] = realpath( $v ).DS;
}

$include   = array();
$include[] = APPLICATION_PATH;
$include[] = YAMMON_PATH;

$files = FS::findFiles( "*.{php,phtml,yml}" , FS::RECURSIVE , $include );
$files = array_unique( $files );

$other = array();
foreach( $files as $k => $v ){
    foreach( $excludes as $exclude ){
        if( strpos( $v , $exclude ) === 0 ){
            $other[] = $v;
            unset( $files[$k] );
        }
    }
}

//Extract Strings
$t = new Translate();
$result = $t->extract( $files , Yammon::getWritablePath('locale')."strings.php" );
pr( $result );
exit;

