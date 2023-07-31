<?php

    $zend_path = VENDORS_PATH . "Zend" . DS;
    set_include_path( $zend_path . PATH_SEPARATOR . DOCUMENT_ROOT . PATH_SEPARATOR . get_include_path()  );

	require_once('ListMax.php');
    require_once('seo.php');

    define( "IMG_URL"              , "http://www.compraoalquila.com.pr" );
	define( "DATABASE_HOST"        , "realityrealty-prod.cielwkgr0tvv.us-east-1.rds.amazonaws.com" );
	define( "DATABASE_USER"        , "realityrealty" );
	define( "DATABASE_PWD"         , "admindb123" );
	define( "DATABASE_NAME"        , "website" );

	define( "CACHE"                , true );
    define( "CACHE_DIR"            , CACHE_PATH."API" );
    define( "CACHE_TIME"           , 5*60 );
    define( "CACHE_COUNTERS_TIME"  , 23*60*60 );

    define( "SMTP"                 , "mail.listmax.com" );
    define( "CONTACTUS_TO"         , "mon@listmax.com");
    define( "CONTACTUS_TO_NAME"    , "Reality Realty");
    define( "CONTACTUS_SUBJECT"    , "Reality Realty Contact Form" );

    //Configure Listmax
	ListMax::config( "database_driver"         , "mysqli"      );
	ListMax::config( "database_host"           , DATABASE_HOST );
	ListMax::config( "database_name"           , DATABASE_NAME );
	ListMax::config( "database_username"       , DATABASE_USER );
	ListMax::config( "database_password"       , DATABASE_PWD  );
	ListMax::config( "cache_on"                , CACHE         );
	ListMax::config( "cache_lifetime"          , CACHE_TIME    );
	ListMax::config( "cache_counters_lifetime" , CACHE_COUNTERS_TIME );
	ListMax::config( "cache_path"              , CACHE_DIR     );
    ListMax::config( "translation_on"          , true );
    ListMax::config( "translation_lang"        , "es" );
    ListMax::config( "translation_country"     , "PR" );
    ListMax::config( "image_url"               , IMG_URL );
    ListMax::config( "mail_from"               , "forms@realityrealtypr.com" );
    ListMax::config( "mail_from_name"          , "Reality Realty" );
//    ListMax::config( "mail_bcc"                , "forms@compraoalquila.com" );
//    ListMax::config( "mail_return_path"        , "forms@compraoalquila.com" );

   
