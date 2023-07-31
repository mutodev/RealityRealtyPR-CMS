<?php

$identifier = get('identifier');
$class      = 'Content';

$content = Doctrine_Query::create()
           ->from( $class )
           ->where( 'identifier = ?', $identifier )
           ->fetchOne();

if (!$content) {
    header('Location: /');
}

//Set values
Action::set( 'content' , $content );
