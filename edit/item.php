<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist
 * @author     Jean FRUITET - jean.fruitet@univ-nantes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', true);
define('MENUITEM', 'content/checklist');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'checklist');
define('SECTION_PAGE', 'index');
define('CHECKLIST_SUBPAGE', 'index');

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php');
require_once('pieforms/pieform.php');
require_once('pieforms/pieform/elements/calendar.php');
require_once(get_config('docroot') . 'artefact/lib.php');
safe_require('artefact','checklist');

if (!PluginArtefactChecklist::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('checklist','artefact.checklist')));
}

define('TITLE', get_string('edititem','artefact.checklist'));

$id = param_integer('id');
$item = new ArtefactTypeItem($id);
if (!$USER->can_edit_artefact($item)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$form = ArtefactTypeItem::get_form($item->get('parent'), $item);

$smarty = smarty();
$smarty->assign('editform', $form);
$smarty->assign('PAGEHEADING', hsc(get_string("editingitem", "artefact.checklist")));
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:edit_index.tpl');
