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

//print_r($_POST);
// [tache_libelle] => titre
// [note] => note
// [id_tag] => Array
//   (
//   [0] => 13
//   [1] => 11
//   )
// [date_start] => 2015-09-09
// [date_deadline] => 2015-09-12
// [priorite] => 0

$db = Database::getInstance();

$ICONE_TITRE = '<i class="fa fa-check-square-o"></i> ';
$title = $ICONE_TITRE._('Add task');
$button_label = _('Add');
$content = '';

// init variables
$get_id_tache = '';
$get_libelle = '';
$get_note = '';
$get_date_start = '';
$get_date_deadline = '';
$get_priorite = '';

// marquer comme done (0 = non fait, 1 = done, 2 = supprimé)
if(isset($_GET['action']) && isset($_GET['id'])) {
    
    $id=intval($_GET['id']);
    
    if($_GET['action']=='done') {
        if(!mark_task_as_done($id)) {$content.=divError('<strong>('._('Error').'!</strong> '._('could not mark task as done').' : '.$db->getLastError());}
        else {header('Location:'.$_SESSION['previous']);}
        }
        
    else if($_GET['action']=='undo') {        
        if(!undo_task($id)) {$content.=divError('<strong>'._('Error').'!</strong> '._('could not undo task').' : '.$db->getLastError());}
        else {header('Location:'.$_SESSION['previous']);}
        }
        
    else if($_GET['action']=='delete') {
        if(!delete_task($id)) {$content.=divError('<strong>'._('Error').'!</strong> '._('could not delete task').' : '.$db->getLastError());}
        else {header('Location:'.$_SESSION['previous']);}
        }
        
    else if($_GET['action']=='tomorrow') {
        if(!doit_tomorrow($id)) {$content.=divError('<strong>'._('Error').'!</strong> '._('could not set task to tomorrow').' : '.$db->getLastError());}
        else {header('Location:'.$_SESSION['previous']);}
        }

    }

// MODIF D'UNE TACHE ?
if(isset($_GET['id'])) {
   
    // récup des infos de la tache
    $id=intval($_GET['id']);
    $rows = $db->select('SELECT * FROM taches WHERE id_tache = ?',array($id));
    if(count($rows)!=0) {
        $get_id_tache = $rows[0]['id_tache'];
        $get_libelle = traite_sqlite_value($rows[0]['libelle']);
        $get_note = traite_sqlite_value($rows[0]['note']);
        $get_date_start = $rows[0]['date_start'];
        $get_date_deadline = $rows[0]['date_deadline'];
        $get_priorite = $rows[0]['priorite'];
        
        $title = $ICONE_TITRE._('Edit task');
        $button_label = _('Update');        
        }
    }

define('PAGE_NAME','task'); // pour class active
$script = '$("#select_tag").click(function(){
    var selText = $(this).text();
    $(this).parents(\'.btn-group\').find(\'.dropdown-toggle\').html(selText+\' <span class="caret"></span>\');
    });
$(\'#datepicker_due\').datepicker({
    orientation: "top auto",
    clearBtn: true,
    todayBtn: true,
    language: "'.substr($_COOKIE['lng'],0,2).'",
    calendarWeeks: true,
    format: \'yyyy-mm-dd\',
    todayHighlight: true,
    autoclose: true
    });
$(\'#datepicker_deadline\').datepicker({
    orientation: "top auto",
    clearBtn: true,
    todayBtn: true,
    language: "'.substr($_COOKIE['lng'],0,2).'",
    calendarWeeks: true,
    format: \'yyyy-mm-dd\',
    todayHighlight: true,
    autoclose: true
    });
$("textarea").height( $("textarea")[0].scrollHeight );
    ';

// AJOUT/MOD TACHE
if(isset($_POST['tache_libelle'])) {
    
    // VARIABLES PAR DEFAULT SI 'quick add'
    if(!isset($_POST['note'])) {$_POST['note']=null;}
    if(!isset($_POST['date_start'])) {$_POST['date_start']=null;}
    if(!isset($_POST['date_deadline'])) {$_POST['date_deadline']=null;}
    if(!isset($_POST['priorite'])) {$_POST['priorite']=0;}
    if(!isset($_POST['id_tache'])) {$_POST['id_tache']=null;}
    if(!isset($_POST['id_tag'])) {$_POST['id_tag']=array();}

    $titre=traite_form_value($_POST['tache_libelle'],MAX_LEN_TITRE_TACHE);
    $note=traite_form_value($_POST['note'],MAX_LEN_NOTE_TACHE);
    if($titre=='') {$content.=divError('<strong>'._('Error').'!</strong> '._('empty title'));}
    else {
        if($_POST['id_tache']!='') {
            // UPDATE
            $query = 'UPDATE taches SET libelle=?, note=?, date_start=?, date_deadline=?, priorite=? WHERE id_tache=?';
            $bool = @$db->execute($query,array($titre,$note,$_POST['date_start'],$_POST['date_deadline'],$_POST['priorite'],$_POST['id_tache']));
            if(!$bool) {$content.=divError('<strong>Error!</strong> task not updated : '.$db->getLastError());}
            else {$content.=divSuccess(_('Task updated'));}
            
            $bool = @$db->execute('DELETE FROM taches_tags WHERE id_tache = ?',array($_POST['id_tache']));
            if(!$bool) {$content.=divError('<strong>'._('Error').'!</strong> '._('update tags failed').' : '.$db->getLastError());}
            
            $lastIdTache = $_POST['id_tache'];
            }
        else {
            // INSERT table "taches"
            $query = 'INSERT INTO taches (libelle,note,date_start,date_deadline,priorite,date_creation) VALUES (?,?,?,?,?,strftime("%Y-%m-%d %H:%M:%S","now","localtime"))';
            
            // quick add ?
            $idTagReferer=0;
            if(preg_match('/index\.php/',$_SERVER['HTTP_REFERER'])) {$_POST['date_start']=date('Y-m-d');} // depuis "Today" => à faire aujourd'hui
            else if(preg_match('/tomorrow\.php/',$_SERVER['HTTP_REFERER'])) { // "Tomorrow" => à faire demain
                $date_start = new DateTime('tomorrow');
                $_POST['date_start']=$date_start->format('Y-m-d');
                }
            else if(preg_match('/scheduled\.php/',$_SERVER['HTTP_REFERER'])) { // "Scheduled" => à faire après-demain, pour voir la tache apparaitre après quick add
                $date_start = new DateTime('tomorrow + 1day');
                $_POST['date_start']=$date_start->format('Y-m-d');
                }                
            else if(preg_match('/tag\.php\?id=([0-9]+)/',$_SERVER['HTTP_REFERER'],$matches)) { // ajout id_tag à la tache
                $idTagReferer = $matches[1];
                }                
            
            $bool = @$db->execute($query,array($titre,$note,$_POST['date_start'],$_POST['date_deadline'],$_POST['priorite']));
            $lastIdTache = $db->lastInsertId();
            if(!$bool) {$content.=divError('<strong>'._('Error').'!</strong> '._('task not added').' : '.$db->getLastError());}
            else {
                $content.=divSuccess(_('Task added'));
                // ajout du tag quick add
                if($idTagReferer>0) {
                    $bool = @$db->execute('INSERT INTO taches_tags VALUES (?,?)',array($lastIdTache,$idTagReferer));
                    if(!$bool) {$content.=divError('<strong>'._('Error').'!</strong> '._('related tag not added').' : '.$db->getLastError());} 
                    }                
                if($bool && !preg_match('/task\.php/',$_SERVER['HTTP_REFERER'])) {header('Location:'.$_SERVER['HTTP_REFERER']);}
                }                

            }

        // INSERTS table "taches_tags"
        foreach($_POST['id_tag'] as $id_tag) {
            $bool = @$db->execute('INSERT INTO taches_tags VALUES (?,?)',array($lastIdTache,$id_tag));
            if(!$bool) {$content.=divError('<strong>'._('Error').'!</strong> '._('related tag not added').' : '.$db->getLastError());}            
            }
        }
    }

$content .= '<form method="POST" action="task.php">

    <input type="hidden" name="id_tache" value="'.$get_id_tache.'">

    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-font"></i></span>
      <input type="text" name="tache_libelle" maxlength="'.MAX_LEN_TITRE_TACHE.'" class="form-control" aria-label="'._('Add task').'" placeholder="'._('Title').'" value="'.$get_libelle.'" autofocus>
    </div>
    
    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
      <textarea name="note" class="form-control" aria-label="Note" placeholder="'._('Note').'" rows="4">'.$get_note.'</textarea>
    </div>    
  
    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-tags"></i></span>
    <select name="id_tag[]" class="selectpicker" multiple data-width="100%">'.PHP_EOL;

// cas : mise à jour des tags, on sélect les actuels
$oldIdTags = array();
if($get_id_tache!='') {
    $rows = $db->select('SELECT id_tag FROM taches_tags WHERE id_tache = ?',array($get_id_tache));
    foreach($rows as $row) {
        $oldIdTags[] = $row['id_tag'];
        }
    }

$tags = getTags();
while (list($id, $tag) = each($tags)) {
    $content .= '<option value="'.$id.'"';
    if(in_array($id,$oldIdTags)) {$content.=' selected';}
    $content.='>'.$tag.'</option>'.PHP_EOL;
    }
$content .='</select>
    </div>

    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      <input id="datepicker_due" name="date_start" placeholder="'._('Start date').'" type="text" class="form-control" value="'.$get_date_start.'">
    </div>
    
    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
      <input id="datepicker_deadline" name="date_deadline" placeholder="'._('Deadline').'" type="text" class="form-control" value="'.$get_date_deadline.'">
    </div>
    
    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-flag"></i></span>
      <select name="priorite" class="selectpicker" data-width="100%">
        <option value="0">'._('Priority').'...</option>
        <option value="1"';
if($get_priorite==1) {$content.=' selected';}
        $content.='>'._('Low').'</option>
        <option value="2"';
if($get_priorite==2) {$content.=' selected';}
        $content.='>'._('Medium').'</option>
        <option value="3"';
if($get_priorite==3) {$content.=' selected';}
        $content.='>'._('High').'</option>
      </select>
    </div>    

    <br>
    <button class="btn btn-success" type="submit">'.$button_label.'</button>';
if($get_id_tache!='') {
    $doneTmp=get_done($get_id_tache);
    if($doneTmp!=0) {$content.=' <a href="task.php?action=undo&id='.$get_id_tache.'" role="button" class="btn btn-warning">'._('Undo').'</a>';}
    else {$content.=' <a href="task.php?action=done&id='.$get_id_tache.'" role="button" class="btn btn-warning"><i class="fa fa-check"></i> '._('Done').'</a>';}
    if($doneTmp!=2) {$content.=' <a href="task.php?action=delete&id='.$get_id_tache.'" role="button" class="btn btn-danger"><i class="fa fa-trash"></i> '._('Delete').'</a>';}
    $content.=' <a href="task.php?action=tomorrow&id='.$get_id_tache.'" role="button" class="btn btn-info">'._('Do it tomorrow').'</a>';
    }
$content.='</form> ';

render(
    'template.php',
    $title,
    $content,
    $script
    );
