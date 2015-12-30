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
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'checklist');
define('SECTION_PAGE', 'checklist');
define('CHECKLIST_SUBPAGE', 'index');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'checklist');
if (!PluginArtefactChecklist::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('checklist','artefact.checklist')));
}



$id = param_integer('id');

// offset and limit for pagination
$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 10);

$checklist = new ArtefactTypeChecklist($id);
if (!$USER->can_edit_artefact($checklist)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

define('TITLE', get_string('Items', 'artefact.checklist', $checklist->get('title') ));

$items = ArtefactTypeItem::get_items($checklist->get('id'), $offset, $limit);
ArtefactTypeItem::build_items_scalevalueindex_html($items);

$js = <<< EOF
addLoadEvent(function () {
    {$items['pagination_js']}
});
EOF;



$smarty = smarty(array('paginator'));

//  icons
$smarty->assign('iconcheckpath', ArtefactTypeChecklist::get_icon_checkpath());
$smarty->assign('iconedit', ArtefactTypeChecklist::get_icon_edit());
$smarty->assign('icondelete', ArtefactTypeChecklist::get_icon_delete());
$smarty->assign('iconmovedown', ArtefactTypeChecklist::get_icon_movedown());
$smarty->assign('iconmoveup', ArtefactTypeChecklist::get_icon_moveup());
$smarty->assign('iconadd', ArtefactTypeChecklist::get_icon_add());

$smarty->assign_by_ref('items', $items);
$smarty->assign_by_ref('checklist', $id);
$smarty->assign_by_ref('tags', $checklist->get('tags'));
$smarty->assign_by_ref('owner', $checklist->get('owner'));
$smarty->assign('strnoitemsaddone',
    get_string('noitemaddone', 'artefact.checklist',
    '<a href="' . get_config('wwwroot') . 'artefact/checklist/new.php?id='.$checklist->get('id').'">', '</a>'));
$smarty->assign('checklistitemsdescription', get_string('checklistitemsdescription', 'artefact.checklist', get_string('newitem', 'artefact.checklist')));
$smarty->assign('PAGEHEADING', get_string("Items", "artefact.checklist", $checklist->get('title')));
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:itemsedit.tpl');
