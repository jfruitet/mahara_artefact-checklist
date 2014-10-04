<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist
 * @author     Jean FRUITET - jean.fruitet@univ-nan
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

define('TITLE', get_string('selectexport','artefact.checklist'));

$id = param_integer('id');

$artefact = new ArtefactTypeChecklist($id);
$items = ArtefactTypeItem::get_all_items_raw($artefact->get('id'));

//print_object($items);


// is owner
if (!$USER->can_edit_artefact($artefact)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$editform = ArtefactTypeChecklist::get_form_export($artefact, $items);

$smarty = smarty();
$smarty->assign('editform', $editform);
$smarty->assign('PAGEHEADING', hsc(get_string("exportchecklist", "artefact.checklist")));
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:select_index.tpl');
