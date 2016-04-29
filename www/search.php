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

// en GET : 'mot'
$mot='';
if(isset($_GET['mot'])) {$mot=$_GET['mot'];}
$mot=str_replace('%','',$mot);
$mot=str_replace('_','',$mot);
define('SEARCH_WORD',$mot);

$title = '<i class="fa fa-search"></i> '._('Search');
define('PAGE_NAME',''); // pour class active
$content = '';
$script='';

// Toutes les taches
// dont la date <= aujourdhui
$query=SELECT_ALL_TASKS.'WHERE 
libelle_tache LIKE ? OR note LIKE ? 
ORDER BY
libelle_tag,
taches.priorite DESC,
date_orderby
';

if(strlen($mot)<3) {
    $content .= divError(_('Please type at least 3 characters'));
    }
else {
    $db = Database::getInstance();
    $rows = $db->select($query,array('%'.SEARCH_WORD.'%', '%'.SEARCH_WORD.'%'));
    $content.=lister_taches($rows,'tag'); // records SQLite, titre des blocs (tag,date), afficher tag, afficher date
    if(count($rows)>0) {$title .= ' <span class="badge">'.count($rows).'</span>';}
    }

render(
    'template.php',
    $title,
    $content,
    $script
    );
