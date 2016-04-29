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

require_once 'common.php';

define('PAGE_NAME','scheduled'); // pour class active
$content = '';
$script='';

// Toutes les taches
// dont la date > j+2 car 
// j+0=aujourdhui (fait), j+1=demain (fait)
$query=SELECT_ALL_TASKS.'WHERE
taches.done = 0 AND (
    (date_start!="" AND date_start NOTNULL AND date_start >= date("now","+2 day","localtime")) OR 
    (date_deadline!="" AND date_deadline NOTNULL AND date_deadline >= date("now","+2 day","localtime")) 
    )
ORDER BY 
date_orderby,
taches.priorite DESC,
date_orderby,
libelle_tag
';

$db = Database::getInstance();
$rows = $db->select($query);
$content.=lister_taches($rows,'date',true); // records SQLite, titre des blocs (tag,date), afficher tag, afficher date

$title = '<i class="fa fa-calendar"></i> '._('Scheduled');
if(count($rows)>0) {$title .= ' <span class="badge">'.count($rows).'</span>';}

render(
    'template.php',
    $title,
    $content,
    $script
    );
