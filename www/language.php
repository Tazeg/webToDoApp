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

$user_lang = '';
if(isset($_POST['inputLng'])) {
    setcookie('lng',$_POST['inputLng'],time()+(3600*24*31*100));
    $user_lang = $_POST['inputLng'];
    $_COOKIE['lng'] = $user_lang;
    }
if(isset($_COOKIE['lng'])) {
    $user_lang = $_COOKIE['lng'];
    }

require_once 'common.php';

define('PAGE_NAME','settings'); // pour class active
$content = '';
$script='';
$divMsg='';
$title = '<i class="fa fa-globe"></i> '._('Language');

$content.='  
      <form action="language.php" method="POST">
      '.$divMsg.'
            <select class="selectpicker" name="inputLng">
                <option value="fr_FR"';
if(preg_match('/^fr/',$user_lang)) {$content .= ' selected';}
$content .= '>'._('French').'</option>
                <option value="en_US"';
if(preg_match('/^en/',$user_lang)) {$content .= ' selected';}
$content .= '>'._('English').'</option>
            </select>
            <button class="btn btn-primary" type="submit">'._('Save').'</button>
      </form>';

render(
    'template.php',
    $title,
    $content,
    $script
    );
