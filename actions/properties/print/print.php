<?php
    require './dompdf/autoload.inc.php';

    define('DOMPDF_ENABLE_REMOTE', true);

    $id = get('id');
    $output = get('output', 'pdf');

    $View = new View();

    //Property
    $Property = PropertyTable::retrieveById($id, true);

    if( !$Property ){
        die('Property not found');
    }

    $brokerId = get('broker_id');
    $force = $brokerId;

    if(!$brokerId){
        $brokerId = $Property->account_id;
    }

    $q = new Doctrine_Query();
        $q->from('Account a');
        $q->andWhere('a.id = ?', $brokerId);
	    $Broker = $q->execute();
	    $Broker = $Broker[0];

    $flyer = 'residential';

    if (round($Property->sale_price) >= 450000) {
        $flyer = 'luxe';
    }


    $content = $View->partial(strtolower($flyer).'-flyer', array('Property' => $Property, 'Broker'=>$Broker,'force'=>$force));
    // echo $content;exit();


    // reference the Dompdf namespace
    use Dompdf\Dompdf;

    // instantiate and use the dompdf class
    $dompdf = new Dompdf(array('is_remote_enabled' => true));
    $dompdf->set_option('isHtml5ParserEnabled', true);
    $dompdf->loadHtml($content);

    // (Optional) Setup the paper size and orientation
    //$dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    if($output=='image'){
        $file_to_save = dirname(__FILE__).'/'.$Property->id.'.pdf';
        //save the pdf file on the server
        file_put_contents($file_to_save, $dompdf->output());

        $im = new imagick($file_to_save.'[0]');
        $im->setImageFormat('jpg');
        header('Content-Type: image/jpeg');
        echo $im;
    }else{
        // Output the generated PDF to Browser
        $dompdf->stream($Property->id);
    }
