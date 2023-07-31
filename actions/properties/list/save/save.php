<?php

$propertyId = get('property');

$savedProperties = Session::read('properties', []);

if (in_array($propertyId, $savedProperties)) {
    unset($savedProperties[array_search($propertyId, $savedProperties)]);
}
else {
    $savedProperties[] = $propertyId;
}

Session::write('properties', $savedProperties);

die('Ok');
