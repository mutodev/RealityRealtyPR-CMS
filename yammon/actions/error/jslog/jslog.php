<?php

//Variables
$error   = array();
$msg     = get('msg');
$source  = get('source');
$line    = get('line');
$url     = get('url');
$plugins = get('plugins');
$id      = $msg.$source.$line;

$error['type']      = 'JavaScript';
$error['level']     = 0;
$error['message']   = $msg;
$error['name']      = 'JavaScript';
$error['file']      = $source;
$error['line']      = $line;
$error['url']       = $url;
$error['supressed'] = false;
$error['data']      = array('plugins' => explode(',', $plugins) );

Error::logErrors( array($id => $error) );

//Send a BEACON image back to the user's browser
header('Content-type: image/gif');

//Transparent Image (1x1)
echo chr(71).chr(73).chr(70).chr(56).chr(57).chr(97).
  chr(1).chr(0).chr(1).chr(0).chr(128).chr(0).
  chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).
  chr(33).chr(249).chr(4).chr(1).chr(0).chr(0).
  chr(0).chr(0).chr(44).chr(0).chr(0).chr(0).chr(0).
  chr(1).chr(0).chr(1).chr(0).chr(0).chr(2).chr(2).
  chr(68).chr(1).chr(0).chr(59);

exit;