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
define('CHECKLIST_SUBPAGE', 'publiclists');

defined('INTERNAL') || die();

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php');

safe_require('artefact', 'checklist');

if (!PluginArtefactChecklist::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('checklist','artefact.checklist')));
}
define('TITLE', get_string('selectlist','artefact.checklist'));

$id = param_integer('id');

$artefact = new ArtefactTypeChecklist($id);
$items = ArtefactTypeItem::get_all_items_raw($artefact->get('id'));

// Provoque un bug bizarre sur le serveur  Mahara-Dev su PostGres
/*
if (empty($artefact->get('public'))){
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}
*/

$editform = ArtefactTypeChecklist::get_form_select($artefact, $items);

$smarty = smarty();
$smarty->assign('editform', $editform);
$smarty->assign('PAGEHEADING', hsc(get_string("selectinglist", "artefact.checklist")));
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:select_index.tpl');
