<?php

$summary = $Goal ? $Goal->getStats($month) : [];

Action::set(compact('breadcrumb', 'accountId', 'Goal', 'Goals', 'data', 'summary', 'year', 'month'));
