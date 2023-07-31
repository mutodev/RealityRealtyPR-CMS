<?php

$Table = helper('Table');

$Query = $Table->getSource();

$Table->setOption('source', $Query);

Action::set(compact('breadcrumb'));
