<?php

    require '../../properties/print/vendor/autoload.php';

    define('DOMPDF_ENABLE_REMOTE', true);

    $id = get('id');
    $output = get('output', 'pdf');

    $View = new View();

    //Property
    $Project = ProjectTable::retrieveById($id);

    if( !$Project ){
        die('Property not found');
    }

    $brokerId = get('broker_id');
    $force = $brokerId;

    if(!$brokerId && $Project->user_id){
        $brokerId = $Project->user_id;
    }

    $Broker = AccountTable::retrieveById($brokerId);
    $Broker = $Broker ? current($Broker): array();

    $content = $View->partial('project-flyer', array('Project' => $Project,'Broker'=>$Broker,'force'=>$force));
    //echo $content;exit();

    // reference the Dompdf namespace
    use Dompdf\Dompdf;

    // instantiate and use the dompdf class
    $dompdf = new Dompdf(array('is_remote_enabled' => true));
    $dompdf->loadHtml($content);

    // (Optional) Setup the paper size and orientation
    //$dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    if($output=='image'){
        $file_to_save = dirname(__FILE__).'/'.$Project->id.'.pdf';
        //save the pdf file on the server
        file_put_contents($file_to_save, $dompdf->output());

        $im = new imagick($file_to_save.'[0]');
        $im->setImageFormat('jpg');
        header('Content-Type: image/jpeg');
        echo $im;
    }else{
        // Output the generated PDF to Browser
        $dompdf->stream($Project->id);
    }
