<?php

Action::setLayout( false );
Auth::logout( );

//Redirect
redirect('/');

exit();
