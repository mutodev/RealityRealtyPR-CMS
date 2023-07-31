<?php

$id = get('id');

$q = new Doctrine_Query();
$q->from('Property p');
$q->select('Photo.*, p.*, a.*, 1 as type_id, title as address, bathrooms, rooms as bedrooms, sale_price as price, description_en, description_es, a.name_es as city, a.region_es, a.region_en, p.catastro, (CASE WHEN cat.type = "Residential" THEN 1 WHEN cat.type = "Commercial" THEN 1 ELSE 3
END) as type_id, cuerdas');
$q->leftJoin('p.Category cat');
$q->leftJoin('p.Area a');
$q->leftJoin('p.Contract c');
$q->leftJoin('p.Photos Photo WITH Photo.is_approved = 1');
$q->andWhere('p.id = ?', $id);
$q->orWhere('p.internal_number = ?', $id);

$Property = $q->fetchOne();

die(json_encode($Property->toArray()));