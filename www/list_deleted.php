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

define('PAGE_NAME','settings'); // pour class active
$title = '<i class="fa fa-trash"></i> '._('List of deleted tasks');
$content='';
$content2 = '<p>'.
            '<a href="#" data-href="'.basename(__FILE__).'?a=delete" data-toggle="modal" data-target="#confirm-delete">'.
            _('Delete definitly now').'</a>.</p>';
$script="$('#confirm-delete').on('show.bs.modal', function(e) {
$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});
";

if(isset($_GET['a'])) {
    if($_GET['a']=='delete') {
        // on supprime définitivement toutes les taches où done = 1
        $content .= delete_tasks_definitly(2);
        }
    }

// FENETRE CONFIRMATION SUPPR
$content.='    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">'._('Confirm Delete').'</h4>
                </div>
            
                <div class="modal-body">
                    <p>'._('You are about to empty trash definitly.').'</p>
                    <p>'._('Do you want to proceed').' ?</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">'._('Cancel').'</button>
                    <a class="btn btn-danger btn-ok">'._('Delete').'</a>
                </div>
            </div>
        </div>
    </div>';

// Toutes les taches DONE
$query=SELECT_ALL_TASKS.'WHERE
taches.done = 2
ORDER BY
taches.date_done DESC
';

$db = Database::getInstance();
$rows = $db->select($query);
if(count($rows)>0) {$content .= $content2;}
$content.=lister_taches($rows,'date',true,true); // records SQLite, titre des blocs (tag,date), afficher tag, afficher date

render(
    'template.php',
    $title,
    $content,
    $script
    );
