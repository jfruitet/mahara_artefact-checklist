<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist
 * @author     Jean FRUITET - jean.fruitet@univ-nantes.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', true);
define('MENUITEM', 'content/checklist');

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php');
require_once('pieforms/pieform.php');
safe_require('artefact','checklist');

define('TITLE', get_string('deleteitem','artefact.checklist'));

$id = param_integer('id');
$todelete = new ArtefactTypeItem($id);
if (!$USER->can_edit_artefact($todelete)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$deleteform = array(
    'name' => 'deleteitemform',
    'plugintype' => 'artefact',
    'pluginname' => 'checklist',
    'renderer' => 'div',
    'elements' => array(
        'submit' => array(
            'type' => 'submitcancel',
            'value' => array(get_string('deleteitem','artefact.checklist'), get_string('cancel')),
            'goto' => get_config('wwwroot') . '/artefact/checklist/checklist.php?id='.$todelete->get('parent'),
        ),
    )
);
$form = pieform($deleteform);

$smarty = smarty();
$smarty->assign('form', $form);
$smarty->assign('PAGEHEADING', $todelete->get('title'));
$smarty->assign('subheading', get_string('deletethisitem','artefact.checklist',$todelete->get('title')));
$smarty->assign('message', get_string('deleteitemconfirm','artefact.checklist'));
$smarty->display('artefact:checklist:delete_index.tpl');

// calls this function first so that we can get the artefact and call delete on it
function deleteitemform_submit(Pieform $form, $values) {
    global $SESSION, $todelete;

    $todelete->delete();
    $SESSION->add_ok_msg(get_string('itemdeletedsuccessfully', 'artefact.checklist'));

    redirect('/artefact/checklist/checklist.php?id='.$todelete->get('parent'));
}
