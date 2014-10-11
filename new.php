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

define('INTERNAL', 1);
define('MENUITEM', 'content/checklist');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'checklist');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'checklist');
if (!PluginArtefactChecklist::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('checklist','artefact.checklist')));
}

$id = param_integer('id',0);
$positionafter = param_integer('positionafter', -1); // item position before new one

if ($id) {
    $checklist = new ArtefactTypeChecklist($id);
    if (!$USER->can_edit_artefact($checklist)) {
        throw new AccessDeniedException(get_string('accessdenied', 'error'));
    }
    define('TITLE', get_string('newitem','artefact.checklist'));
    $form = ArtefactTypeItem::get_form($id, null, $positionafter); // new item to insert
}
else {
    define('TITLE', get_string('newchecklist','artefact.checklist'));
    $form = ArtefactTypeChecklist::get_form();
}

$smarty =& smarty();
$smarty->assign_by_ref('form', $form);
$smarty->assign_by_ref('PAGEHEADING', hsc(TITLE));
$smarty->display('artefact:checklist:new.tpl');
