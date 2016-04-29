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

require_once 'translation.php';
require_once 'private/config.php';
require_once 'class/class.database.php';

// For months and days translations
$DAY_LNG=array(
    1 =>_('Monday'),
    2 =>_('Tuesday'),
    3 =>_('Wednesday'),
    4 =>_('Thirsday'),
    5 =>_('Friday'),
    6 =>_('Saturday'),
    0 =>_('Sunday')
    );
$MONTH_LNG=array(
    1 =>_('january'),
    2 =>_('february'),
    3 =>_('march'),
    4 =>_('april'),
    5 =>_('may'),
    6 =>_('june'),
    7 =>_('july'),
    8 =>_('august'),
    9 =>_('september'),
   10 =>_('october'),
   11 =>_('november'),
   12 =>_('december'),
   );

// date sorting method :
// date_start   date_deadline   date_creation    date_orderby
// not null                                     => date_start
// null         not null                        => date_deadline
// null         null                            => date_creation
define('SELECT_ALL_TASKS','SELECT
taches.id_tache,
taches.libelle AS libelle_tache,
taches_tags.id_tag,
tags.libelle AS libelle_tag,
taches.date_start,
taches.date_deadline,
taches.priorite,
taches.note,
taches.done,
CASE WHEN (taches.date_start="" OR taches.date_start IS NULL) THEN 
    CASE WHEN (taches.date_deadline="" OR taches.date_deadline IS NULL) THEN
        taches.date_creation
    ELSE
        taches.date_deadline
    END
ELSE 
    taches.date_start 
END as date_orderby
FROM
taches
LEFT JOIN taches_tags ON taches_tags.id_tache = taches.id_tache
LEFT JOIN tags ON taches_tags.id_tag = tags.id_tag ');

function traite_form_value($txt,$limit=1000) {
    // Traitement avant insertion BDD
    $txt=substr($txt,0,$limit);
    $txt=trim($txt);
    return $txt;
    }
    
function traite_sqlite_value($txt) {
    // donnée BDD pour affichage HTML
    $txt=htmlentities($txt);
    return $txt;
    }    
    
function divError($txt) {
    return '<div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$txt.'</div>';
    }

function divWarning($txt) {
    return '<div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$txt.'</div>';
    }
    
function divSuccess($txt) {
    return '<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$txt.'</div>';
    }    
    
function getTags() {
    // OUT : array (id=>tag) = Les tags par ordre alplabétique + leur id
    $out=array();
    $rows = Database::getInstance()->select('SELECT * FROM tags ORDER BY libelle');
    foreach($rows as $row) {
        $out[$row['id_tag']]=traite_sqlite_value($row['libelle']);
        }
    return $out;
    }
    
function get_done($id) {
    // IN : id_tache
    // OUT : done (0=not done, 1=done, 2=supprimée)
    $r=0;
    $rows = Database::getInstance()->select('SELECT done FROM taches WHERE id_tache = ?',array($id));
    if(count($rows>0)) {
        return $rows[0]['done'];
        }
    return $r;
    }
    
function dropDownTags() {
    // Retourne le code HTML pour le menu dropdown "Tags"
    $r='';
    
    $db = Database::getInstance();
    
    // Tous les tags + nb taches liées NON DONES
    $rows = $db->select('SELECT
    taches_tags.id_tag,
    tags.libelle,
    count(*) as nb 
    FROM
    taches_tags
    INNER JOIN taches ON taches_tags.id_tache = taches.id_tache
    INNER JOIN tags ON taches_tags.id_tag = tags.id_tag
    WHERE
    taches.done = 0
    GROUP BY
    tags.libelle
    ORDER BY tags.libelle');
    
    foreach($rows as $row) {
        $r.='<li><a href="tag.php?id='.$row['id_tag'].'"><i class="fa fa-tag"></i> '.traite_sqlite_value($row['libelle']).' <small>('.$row['nb'].')</small></a></li>'.PHP_EOL;    
        }
        
    // nb taches sans tag
    $rows = $db->select('SELECT
    count(*) as nb
    FROM
    taches
    LEFT JOIN taches_tags ON taches_tags.id_tache = taches.id_tache
    WHERE
    taches_tags.id_tache IS NULL
    AND taches.done = 0');
    $r.='<li><a href="tag.php?id=0"><i class="fa fa-tag"></i> '._('No tag').' <small>('.$rows[0]['nb'].')</small></a></li>'.PHP_EOL;    
        
    return $r;
    }

function mark_task_as_done($id) {
    // IN : id_tache à marque comme done
    // OUT : bool succès/erreur
    return @Database::getInstance()->execute('UPDATE taches SET done=1, date_done=strftime("%Y-%m-%d %H:%M:%S","now","localtime") WHERE id_tache=?',array($id));
    }

function doit_tomorrow($id) {
    // IN : id_tache à marquer pour le lendemain
    // OUT : bool succès/erreur
    $date_start = new DateTime('tomorrow');
    $tomorrow=$date_start->format('Y-m-d');    
    return @Database::getInstance()->execute('UPDATE taches SET done=0, date_done=null, date_start=? WHERE id_tache=?',array($tomorrow,$id));
    }

function undo_task($id) {
    // IN : id_tache à marquer comme non faite
    // OUT : bool succès/erreur
    return @Database::getInstance()->execute('UPDATE taches SET done=0, date_done=null WHERE id_tache=?',array($id));
    }
    
function delete_task($id) {
    // IN : id_tache à marquer comme supprimée
    // OUT : bool succès/erreur
    return @Database::getInstance()->execute('UPDATE taches SET done=2, date_done=strftime("%Y-%m-%d %H:%M:%S","now","localtime") WHERE id_tache=?',array($id));
    }
    
function delete_task_definitly($id) {
    // IN : id_tache à supprimer définitivement de la base
    // OUT : nb suppression
    $db = Database::getInstance();
    $db->execute('DELETE FROM taches_tags WHERE id_tache = ?', array($id)); // suppr des liaisons tags-tache
    return $db->execute('DELETE FROM taches WHERE id_tache = ?', array($id)); // suppr tache et retourne 1 si OK
    }

function delete_tasks_definitly($done) {
    // Supprime définitivement les taches où done = $done
    // rappel : 0 => non faite, 1 => faite, 2 => supprimée
    // OUT : string info nb suppressions
    $db = Database::getInstance();
    $content='';
    
    $rows = $db->select('SELECT
    taches.id_tache
    FROM taches
    WHERE 
    done = ?',array($done));

    $db->beginTransaction();
    $cpt=0;
    foreach($rows as $row) {
        $cpt+=delete_task_definitly($row['id_tache']);
        }
    $db->commit();

    if($cpt>0) {
        $content.=divSuccess('<strong>'.$cpt.'</strong> '._('task(s) definitly removed from database').'.');
        }

    return $content;
    }

function lister_taches($rows,$separator='',$afficher_tag=false,$afficher_date=false) {

    global $JOUR_LNG;

    // listing des taches, cf. SELECT_ALL_TASKS
    // $rows = tableau des lignes d'enregistrements de la base
    //      on aura : $row['x'] avec x = 
    //      id_tache
    //      libelle_tache
    //      id_tag
    //      libelle_tag
    //      date_start : date du jour quand exécuter la tache
    //      date_deadline : à faire avant le
    //      priorite : 0 => rien, 1 => low, 2 => medium, 3 => high
    //      note : texte
    //      done : 0 => non, 1 => oui, 2 => supprimée
    //      date_orderby : date_start ou date_deadline si nul, permet de trier la liste par date
    // $separator = tag|date pour mettre en titre de paragraphe l'élément demandé
    // $afficher_tag = true|false, afficher le tag en badge à droite ou pas
    // $barre = true|false, barrer le libellé de la tache (done)
    
    $content='';
    
    // on sauve l'url de la page actuelle (en session)
    // qui liste les taches pour :
    // - sur clic d'une tache + DONE|POUBELLE => go back page actuelle
    $_SESSION['previous'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    
    if(count($rows)==0) {return '<i class="fa fa-sun-o"></i> '._('Nothing here').'.';}
    if(count($rows)>MAX_TASKS_IN_LIST) {
        $content .= divWarning('<strong>'._('Too much things to display').' !</strong> '._('Only').' '.MAX_TASKS_IN_LIST.' '._('tasks are listed below'));
        $rows = array_slice($rows, 0, MAX_TASKS_IN_LIST);
        }
        
    $content .= '<div class="list-group">';
    
    $previousDate='';
    $previousTag='';
    foreach($rows as $row) {
        
        // titre paragraphe
        if($separator=='date') {
            $datetmp=$JOUR_LNG[date('w',strtotime($row['date_orderby']))].' '.date('d/m/Y',strtotime($row['date_orderby']));
            if($datetmp!=$previousDate) {$content.='</div><div class="list-group">
                <a href="#" class="list-group-item active"><h4 class="list-group-item-heading">'.$datetmp.'</h4></a>'.PHP_EOL;
                }
            $previousDate=$datetmp;
            }
        else if($separator=='tag') {
            if($row['libelle_tag']!=$previousTag) {$content.='</div><div class="list-group">
                <a href="#" class="list-group-item active"><h4 class="list-group-item-heading"><i class="fa fa-tag"></i> '.$row['libelle_tag'].'</h4></a>'.PHP_EOL;
                }
            $previousTag=$row['libelle_tag'];
            }
        
        switch($row['priorite']) {
            case 1:
            $priorite_css = ' list-group-item-info';
            break;
            case 2:
            $priorite_css = ' list-group-item-warning';
            break;
            case 3:
            $priorite_css = ' list-group-item-danger';
            break;
            default:
            $priorite_css='';
            break;
            }
        $content.='<a href="task.php?id='.$row['id_tache'].'" class="list-group-item'.$priorite_css.'">';

        // titre tache
        $titre=traite_sqlite_value($row['libelle_tache']);
        if(defined('SEARCH_WORD')) {$titre = str_ireplace(SEARCH_WORD,'<strong>'.SEARCH_WORD.'</strong>',$titre);}
        if($row['done']!=0) {$content.='<del>'.$titre.'</del>';}
        else {$content.=$titre;}   
        
        // note
        if($row['note']!='') {$content.=' <i class="fa fa-file-text-o"></i>';}
        
        // date petite, si pas en titre paragraphe
        $tmpBRdone=false;
        if($row['date_start']!='' && $afficher_date) {
            $datetmp = $JOUR_LNG[date('w',strtotime($row['date_start']))].' '.date('d/m/Y',strtotime($row['date_start']));
            $content .= '<br><small>'._('To do on').' '.$datetmp.'</small>';
            $tmpBRdone=true;
            }
        
        // échéance
        if($row['date_deadline']!='') {
            $datetmp = $JOUR_LNG[date('w',strtotime($row['date_deadline']))].' '.date('d/m/Y',strtotime($row['date_deadline']));
            if(!$tmpBRdone) {$content .= '<br>';} else {$content .= ' - ';}
            $content .= '<small>'._('To do before').' '.$datetmp.'</small>';
            }
            
        // badge-tag
        if($afficher_tag) {
            $content.=' <span class="badge">'.$row['libelle_tag'].'</span>';
            }
        
        $content.='</a>'.PHP_EOL;
        }
        
    $content .= '</div>'.PHP_EOL;
    
    return $content;
    }
    
function render($template_file, $title, $content, $script) {
    require_once 'class/class.template.php';
    $template = new Template($template_file);
    $template->assign('##TITLE##', $title);
    $template->assign('##DROPDOWN_TAGS##',dropDownTags());
    $template->assign('##CONTENT##',$content);
    $template->assign('##SCRIPT##',$script);
    $template->render(); // affichage    
    }
