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

define('PAGE_NAME','next'); // pour class active
$content = '';
$script='';

// Toutes les taches sans dates
$query=SELECT_ALL_TASKS.'
WHERE
    taches.done = 0 AND 
    (date_start="" OR date_start IS NULL) AND 
    (date_deadline="" OR date_deadline IS NULL) 
ORDER BY
libelle_tag,
taches.priorite DESC,
date_orderby
';

$db = Database::getInstance();
$rows = $db->select($query);
$content.=lister_taches($rows,'tag'); // records SQLite, titre des blocs (tag,date), afficher tag, afficher date

$title = '<i class="fa fa-server"></i> '._('Tasks without due date');
if(count($rows)>0) {$title .= ' <span class="badge">'.count($rows).'</span>';}

render(
    'template.php',
    $title,
    $content,
    $script
    );
