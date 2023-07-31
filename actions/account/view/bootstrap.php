<?php

    Auth::requireLogin();

    //Set Layout
    Action::setLayout('backend');

    //Set The Dashboard as Active
    $Menu = helper('Menu' , 'main' );
    $Menu->setActive('index');

    //Get the Lead
    $Account = Auth::get();

    $breadcrumb[] = array(
        'label' => Auth::get('first_name') . ' ' . Auth::get('last_name'),
        'url'   => url('account.view'),
        'icon'  => 'user'
    );

    $Menu = helper('Menu');
    $Menu->add("home"  , array(
        "label"      => '<i class="fa fa-arrow-left"></i>'.t('Back to Dashboard') ,
        "link"       => "/"
    ));

    //Companies for drop down menu
    $Query = new Doctrine_Query();
    $Query->from('Company co');
    $Query->andWhere('co.is_active = ?', true);
    $Query->andWhere('co.organization_id = ?', Auth::get()->organization_id);
    $Query->orderBy('co.name');
    $Companies = $Query->fetchArray();

    Action::set(compact('Companies'));