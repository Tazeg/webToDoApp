<?php
//----------------------------------------------------------------------
// JeffProd - Web ToDo app
//----------------------------------------------------------------------
// AUTHOR	: Jean-Francois GAZET
// WEB 		: http://www.jeffprod.com
// TWITTER	: @JeffProd
// MAIL		: jeffgazet@gmail.com
// LICENCE	: GNU GENERAL PUBLIC LICENSE Version 3, June 2007
//----------------------------------------------------------------------

// translation are in locale/xx_XX/LC_MESSAGES/traductions.mo

session_start();

if(isset($_COOKIE['lng'])) {
    $language = $_COOKIE['lng'];
    }
else {
    $language = str_replace('-','_',substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5));
    setcookie('lng', $language, time()+(3600*24*31*100));
    }

$locale=$language.'.utf8';
putenv('LC_ALL='.$locale);
setlocale(LC_ALL, $locale);

$domaine = 'traductions';
bindtextdomain($domaine, __DIR__.'/locale');
textdomain($domaine);
