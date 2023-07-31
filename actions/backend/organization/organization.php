<?php

//Get Model
$Organization = Auth::get()->Organization;

Action::set(compact('breadcrumb', 'Organization'));
