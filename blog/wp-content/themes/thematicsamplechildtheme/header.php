<?php

    // Creating the doctype
    thematic_create_doctype();
    echo " ";
    language_attributes();
    echo ">\n";

    // Creating the head profile
    thematic_head_profile();

    // Creating the doc title
    thematic_doctitle();

    // Creating the content type
    thematic_create_contenttype();

    // Creating the description
    thematic_show_description();

    // Creating the robots tags
    thematic_show_robots();

    // Creating the canonical URL
    thematic_canonical_url();

    // Loading the stylesheet
    //thematic_create_stylesheet();

    // Creating the internal RSS links
    thematic_show_rss();

    // Creating the comments RSS links
    thematic_show_commentsrss();

    // Creating the pingback adress
    thematic_show_pingback();

    // Enables comment threading
    thematic_show_commentreply();

    // Calling WordPress' header action hook
    //wp_head();

?>

<meta name="description" content="Bienes Raices Puerto Rico busqueda de propiedades en venta o alquiler, Casas, Apartamentos, Oficinas, Terrenos , Locales Comerciales y Edificios.">

<meta name="keywords" content="bienes raices en puerto rico, real estate puerto rico, hogares, apartamentos, casas, fincas, terrenos, propiedades en puerto rico, venta de casas, venta de residencias, alquiler de apartamentos, venta de propiedades puerto rico">


<link href="https://fonts.googleapis.com/css?family=Roboto&amp;text=0123456789" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Raleway:3100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">



<link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">
<link type="text/css" rel="stylesheet" href="/css/cover.css?v7">
<link type="text/css" rel="stylesheet" href="/css/cover-sass.css">
<link type="text/css" rel="stylesheet" href="/css/slider.css">
<link type="text/css" rel="stylesheet" href="/css/colorbox/colorbox.css">
<link type="text/css" rel="stylesheet" href="/css/jAlerts/jquery.alerts.css">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<script type="text/javascript" charset="UTF-8" src="https://maps.googleapis.com/maps-api-v3/api/js/41/1/common.js"></script><script type="text/javascript" charset="UTF-8" src="https://maps.googleapis.com/maps-api-v3/api/js/41/1/util.js"></script>

<link rel="stylesheet" type="text/css" href="/blog/wp-content/themes/thematicsamplechildtheme/style.css" />

</head>

<?php

if (apply_filters('thematic_show_bodyclass',TRUE)) {
    // Creating the body class
    ?>

<body class="<?php thematic_body_class() ?>">

<?php }

// action hook for placing content before opening #wrapper
thematic_before();

if (apply_filters('thematic_open_wrapper', true)) {
	echo '<div id="wrapper" class="hfeed">';
}

    // action hook for placing content above the theme header
    thematic_aboveheader();

    ?>

    <div class="pageTop general">
        <div class="pageTopHeader">
            <nav class="navbar navbar-default menu-header">
                <div class="container">
                    <div class="row header-top">
                        <div class="hidden-xs navbar-left navbar-socials">
                            <a target="_blank" class="btn" href="https://www.facebook.com/realityrealtypr/">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a target="_blank" class="btn" href="https://twitter.com/realityrealtyp">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a target="_blank" class="btn" href="https://www.instagram.com/prrealityrealty/">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                        <div class="hidden-xs navbar-right">
                            <div class="tell-us inline-imp vl"><p class="navbar-text navbar-right ">Dinos qué necesitas</p>
                            </div>
                            <div class="inline-imp">
                                <div class="tell-us-icon dropdown-tell-us inline-imp dropdown clearfix">
                                    <a class="dropdown-toggle" type="button" id="dropdownMenu4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="/new/call_icon-01.svg">
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu4">
                                        <li>
                                            <a href="tel:7877458777">
                                                San Juan (787) 745-8777
                                            </a>
                                            <a href="tel:7877458792">
                                                Caguas (787) 745-8792
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tell-us-icon inline-imp"><a href="https://realityrealtypr.com/contactenos/">
                                        <img src="/new/mail_icon-01.svg">
                                    </a></div>
                            </div>

                            <ul class="nav navbar-nav inline-imp ">
                                <li class=""><span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span><a class="inline-imp" href="https://realityrealtypr.com/properties/list/">Mi selección</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row header-bottom" data-children-count="1">
                        <div class="vcenter">
                            <label for="navbar-toggle-cbox" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar" data-children-count="0">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </label>
                            <a class="navbar-brand" href="/" data-children-count="0"></a>
                        </div>
                        <input type="checkbox" id="navbar-toggle-cbox">
                        <div id="navbar" class="navbar-collapse collapse">
                            <ul class="nav navbar-nav navbar-right">
                                <li>
                                    <a href="/nosotros">NOSOTROS<span class="grey hidden-xs">|</span></a></li>
                                <li><a href="/servicios">SERVICIOS<span class="grey hidden-xs">|</span></a></li>
                                <li><a href="/agents?type=Broker">Agentes<span class="grey hidden-xs">|</span></a>
                                </li>
                                <li>
                                    <a href="/propiedades">PROPIEDADES<span class="grey hidden-xs">|</span></a>
                                </li>
                                <!--                        <li><a href="/blog/">--><!--<span class="grey hidden-xs">|</span></a></li>-->
                                <li>
                                    <a style="white-space: pre;" href="/instant-offer">Instant Offer<span class="grey hidden-xs">|</span></a>
                                </li>
                                <li><a style="border-right:none;" href="/contactenos">CONTÁCTENOS</a></li>
                                <li class="visible-xs"><a href="/backend">ENTRAR</a></li>
                            </ul>
                        </div><!--/.nav-collapse -->

                    </div>
                </div>
            </nav>

        </div>

    </div>

    <!--
    <div id="home-img" class="header-image hidden-xs"></div>
    -->

    <?php

    // action hook for placing content below the theme header
    thematic_belowheader();

    ?>

    <?php if(single_cat_title('', FALSE) == 'Reality Way'): ?>

        <div id="know-us-img" class="header-image hidden-xs"></div>

        <div class="page-title text-center">
            NOSOTROS
        </div>

        <div class="sub-menu text-center ">
            <div class="container">
                <ul class="nav navbar-nav navbar-center">
                    <li>
                        <a href="/corredores">
                        NUESTRO EQUIPO
                        </a>
                    </li>
                    <li>
                        <a href="#">
                        REALITY+ PROGRAM
                        </a>
                    </li>
                    <li class="active">
                        <a href="/blog/category/realityway/">
                        “REALITY WAY”
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="page-title text-center">
            BLOG
        </div>
    <?php endif ?>

    <div id="main-content" class="container">
        <div class="row">
            <div class="col-xs-12">

