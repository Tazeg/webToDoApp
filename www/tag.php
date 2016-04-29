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

$db = Database::getInstance();

//----------------------------------------------------------------------
// AFFICHAGE DES TACHES D'UN TAG DONNE
//----------------------------------------------------------------------

$title='';

$id=0;
if(isset($_GET['id'])) {$id = intval($_GET['id']);}
if($id<0) {$id=0;}

// id en base ?
$rows = $db->select('SELECT libelle FROM tags WHERE id_tag = ?',array($id));
if(count($rows)!=0) {$title = $rows[0]['libelle'];}
else {$id=0;}
        
if($id==0) {
    // Taches sans tag (et non marquées done)
    $rows = $db->select(SELECT_ALL_TASKS.'
    WHERE
        taches.done = 0 AND
        libelle_tag IS NULL
    ORDER BY
    taches.priorite DESC,
    date_orderby,
    libelle_tache');
    $title=_('No tag');
    }
else {
    // Taches du tag donné (et non marquées done)
    $rows = $db->select(SELECT_ALL_TASKS.'
    WHERE
        taches.done = 0 AND 
        taches_tags.id_tag = ?        
    ORDER BY
    taches.priorite DESC,
    date_orderby,
    libelle_tache',array($id));
    if(count($rows)>0) {$title=$rows[0]['libelle_tag'];}
    }

define('PAGE_NAME','tags'); // pour class active
$title = '<i class="fa fa-tag"></i> '.$title;
if(count($rows)>0) {$title .= ' <span class="badge">'.count($rows).'</span>';}
$content = '';
$script='';

$content.=lister_taches($rows,'',false,true); // records SQLite, titre des blocs (tag,date), afficher tag, afficher date

render(
    'template.php',
    $title,
    $content,
    $script
    );
