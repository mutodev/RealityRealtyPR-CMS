<?php

require_once 'S3.php';

$propertyId = get('id');

$Property = Doctrine::getTable('Property')->find($propertyId);

if($Property){

    $breadcrumb[] = array(
        'label' => '#'.$Property->id .' - '.$Property->title,
        'url'   => url('backend.company.properties.view?id='.$Property->id),
        'icon'  => 'home'
    );
}

$breadcrumb[] = array(
    'label' => 'Photos',
    'url'   => url('backend.company.properties'),
    'icon'  => 'camera'
);

function uploadPhotoToS3( $file, $folder, $position )
{
    $aws_access_key = Configure::read('aws.access_key');
    $aws_secret_key = Configure::read('aws.secret_key');
    $s3_bucket      = Configure::read('s3.bucket');

	//Initalize S3
	$s3 = new S3( $aws_access_key , $aws_secret_key );
    $urls = array();

    $dimensions = array(
        'original' => array( 'w' => 1024 , 'h' => 1024   , 'sharpen' => false  ),
        'large'    => array( 'w' => 640  , 'h' => 640    , 'sharpen' => false  ),
        'medium'   => array( 'w' => 320  , 'h' => 320    , 'sharpen' => false  ),
        'small'    => array( 'w' => 160  , 'h' => 160    , 'sharpen' => true  ),
    );

    //Resize the image
    $oImage = new Image( $file );
    foreach( $dimensions as $k => $v ){

        $basename  = $position.( $k ? "_".$k : null ).'.jpg';
        $directory = Yammon::getWritablePath()."uploads".DIRECTORY_SEPARATOR."listings".DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR;
        $dest_file = $directory.$basename;
        $s3_file   = $folder . '/' . $basename;

        //Make directory
        FS::makeDirectory($directory);

        if( $oImage->getType() !== IMAGETYPE_JPEG || $oImage->getWidth() > $v['w'] || $oImage->getHeight() > $v['h'] ){
            $oImage->resizeToBox( $v['w'] , $v['h'] , false , $v['sharpen'] );
            $oImage->save( $dest_file , 'jpg' );
        }else{
            copy( $file , $dest_file );
        }

        $s3->putObjectFile( $dest_file , $s3_bucket, $s3_file , S3::ACL_PUBLIC_READ );
        $urls[ $k ] = Configure::read('s3.base_url').$s3_bucket.'/'.$s3_file;
        @unlink( $dest_file );
    }

    return $urls;
}
