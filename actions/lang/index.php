<?php

Session::write('lang', get('lang', 'es'));
redirect($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : '/');
