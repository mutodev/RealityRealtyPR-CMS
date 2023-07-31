<?php

$partial = array_pop(explode('.', Auth::get()->Roles[0]->id));

Action::set(compact('breadcrumb', 'partial'));
