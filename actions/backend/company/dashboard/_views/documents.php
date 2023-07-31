<?php

//$Query = new Doctrine_Query();
//$Query->from('Document');
//$Query->innerJoin('Document.Category Category');
//$Query->andWhereIn('Category.id', [30, 31]);
//$Documents = $Query->execute();

$Query = new Doctrine_Query();
$Query->from('DocumentCategory');
$Query->innerJoin('DocumentCategory.Childs Childs');
$Query->innerJoin('Childs.Documents Documents');
$Query->andWhere('DocumentCategory.is_active = 1');
$Query->andWhere('DocumentCategory.id = 6');
$Query->orderBy('Documents.name ASC');
$DocumentCategories = $Query->execute();
