<?php
use Dompdf\Dompdf;
$Pagination = helper('Pagination');

$action = get('action');
$ids = get('ids', array());

if ($action == 'delete') {
    Session::write('properties', []);
}

if ($ids) {
    Session::write('properties', $ids);
}

$savedProperties = Session::read('properties', []);

$q = PropertyTable::retrieveByIds($savedProperties);

if ($action != 'pdf') {
    $Pagination->setSize(15);
    $q = $Pagination->paginate($q);
}

$Properties = $q->execute();

if ($action == 'pdf') {
    require '../print/vendor/autoload.php';
    define('DOMPDF_ENABLE_REMOTE', true);

    $content = (new View())->partial('list-flyer', compact('Properties'));
    //echo $content;exit();

    // instantiate and use the dompdf class
    $dompdf = new Dompdf(array('is_remote_enabled' => true));
    $dompdf->loadHtml($content);
    $dompdf->render();
    $dompdf->stream($Property->id);
    exit();
}

Action::set(compact('Properties', 'savedProperties'));
