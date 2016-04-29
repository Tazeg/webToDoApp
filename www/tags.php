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

define('PAGE_NAME','settings'); // pour class active
$title = '<i class="fa fa-tags"></i> '._('Tags');
$content='';
$script="$('#confirm-delete').on('show.bs.modal', function(e) {
$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
$('.debug-tag').html('"._('Tag to delete')." : <strong>' + $(e.relatedTarget).data('tagname') + '</strong>');
});
";

// INSERTION / MOD D'UN TAG
if(isset($_POST['frm_action'])) {
    
    $txt=traite_form_value($_POST['frm_tag'],MAX_LEN_TAG);
            
    switch($_POST['frm_action'])
        {
        case 'add':
        if($txt=='') {$content.=divError('<strong>'._('Error').'!</strong> '._('empty tag'));}
        else {
            $bool = @$db->execute('INSERT INTO tags (libelle) VALUES (?)',array($txt));
            if(!$bool) {
                if(preg_match('/UNIQUE constraint/',$db->getLastError())) {$content.=divError('<strong>'._('Error').'!</strong> '._('Tag must be unique'));}
                else {$content.=divError('<strong>'._('Error').'!</strong> '.$db->getLastError());}
                }
            else {$content.=divSuccess(_('Tag').' <strong>'.$_POST['frm_tag'].'</strong> '._('added'));}
            }
        break;
        
        case 'mod':     
        if($txt=='') {$content.=divError('<strong>'._('Error').'!</strong> '._('empty tag'));}
        else {
            $bool = @$db->execute('UPDATE tags SET libelle = ? WHERE id_tag = ?',array($txt,$_POST['id_tag']));
            if(!$bool) {$content.=divError('<strong>'._('Error').'!</strong> '.$db->getLastError());}
            else {$content.=divSuccess(_('Tag updated to').' <strong>'.$txt.'</strong>');}
            }        
        break;
        }
    }
    
// SUPPRESSION
if(isset($_GET['id'])) {
    $bool = @$db->execute('DELETE FROM taches_tags WHERE id_tag = ?',array($_GET['id']));
    if(!$bool) {$content.=divError('<strong>'._('Error').'!</strong> '._('tag not deleted').' : '.$db->getLastError());}
    $bool = @$db->execute('DELETE FROM tags WHERE id_tag = ?',array($_GET['id']));
    if(!$bool) {$content.=divError('<strong>'._('Error').'!</strong> '._('tag not deleted').' : '.$db->getLastError());}
    }

// FORMULAIRE AJOUT D'UN TAG
$content.='<form method="POST" action="tags.php">
<input type="hidden" name="frm_action" value="add">
<div class="input-group">
  <input type="text" name="frm_tag" class="form-control" maxlength="'.MAX_LEN_TAG.'" placeholder="'._('Add tag').'...">
  <span class="input-group-btn">
    <button class="btn btn-default" type="submit">'._('Add').'</button>
  </span>
</div>
</form>'.PHP_EOL;

// LISTE DES TAGS + FORMULAIRES MOD/SUP
$content.='<ul class="list-group">'.PHP_EOL;
$tags = getTags();
while (list($id, $tag) = each($tags)) {
    $content .= '
      <div class="input-group">
        <form action="tags.php" method="POST">
          <input type="text" id="'.$id.'" name="frm_tag" class="form-control" maxlength="'.MAX_LEN_TAG.'" value="'.$tag.'">
          <input type="hidden" name="frm_action" value="mod">
          <input type="hidden" name="id_tag" value="'.$id.'">
        </form>
        <span class="input-group-btn">
          <a href="#" class="btn btn-default" data-tagname="'.$tag.'" data-href="tags.php?id='.$id.'" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i></a>
        </span>
      </div>'.PHP_EOL;    
    }
$content.='</ul>'.PHP_EOL;

// FENETRE CONFIRMATION SUPPR
$content.='    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">'._('Confirm Delete').'</h4>
                </div>
            
                <div class="modal-body">
                    <p>'._('You are about to delete one tag, this procedure is irreversible. Of course, it won\'t delete related tasks.').'</p>
                    <p>'._('Do you want to proceed').' ?</p>
                    <p class="debug-tag"></p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">'._('Cancel').'</button>
                    <a class="btn btn-danger btn-ok">'._('Delete').'</a>
                </div>
            </div>
        </div>
    </div>';

render(
    'template.php',
    $title,
    $content,
    $script
    );
