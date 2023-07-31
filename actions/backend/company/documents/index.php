<?php

$Query = new Doctrine_Query();
$Query->from('DocumentCategory');
$Query->innerJoin('DocumentCategory.Childs Childs');
$Query->innerJoin('Childs.Documents Documents');
$Query->andWhere('DocumentCategory.is_active = 1');
$Query->orderBy('Documents.name ASC');
$DocumentCategories = $Query->execute();

Action::set(compact('breadcrumb', 'DocumentCategories'));
