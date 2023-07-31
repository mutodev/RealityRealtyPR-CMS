<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/**
 * Yammon entry file
 *
 * This is the entry point the yammon application
 * all requests pass thru this file to be routed
 *
 * @package    yammon
 * @subpackage core
 * @author     Jose R Villalon <mon@listmax.com>
 * @copyright  Check the LICENSE file for copyright information
 * @license    Check the LICENSE file for license information
 * @version    SVN: $Id$
 */

require 'vendor/autoload.php';

if (class_exists('Dotenv')) {
    Dotenv::load(__DIR__);
}

//Define the application path
define( "APPLICATION_PATH" ,  dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

//Boostrap Yammon
require_once "yammon/yammon.php";

//Route the Request
Router::route( );

//Prevent stuck in "Content Download" when there is not content-length header
exit;
