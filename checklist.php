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
$itemid = param_integer('itemid', 0); // item id to move
$direction = param_integer('direction', 0); // 0 : move up or 1 : move down

// offset and limit for pagination
$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 10);
$order = param_alpha('order', 'ASC');

$artefact = new ArtefactTypeChecklist($id);

if (!$USER->can_edit_artefact($artefact)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

define('TITLE', get_string('Items', 'artefact.checklist', $artefact->get('title') ));

// move item up or down
if (!empty($itemid)){
	if ($order=='DESC'){
        $direction = $direction ? 0 : 1;
	}
    ArtefactTypeItem::invert_item($artefact->get('id'), $itemid, $direction);
}

$items = ArtefactTypeItem::get_items($artefact->get('id'), $offset, $limit, $order);
//print_object($items);

ArtefactTypeItem::build_items_list_html($items);

$js = <<< EOF
addLoadEvent(function () {
    {$items['pagination_js']}
});
EOF;


$smarty = smarty(array('paginator'));
$smarty->assign_by_ref('items', $items);
$smarty->assign_by_ref('checklist', $id);
$smarty->assign_by_ref('tags', $artefact->get('tags'));
$smarty->assign_by_ref('owner', $artefact->get('owner'));

$smarty->assign('artefacttitle', $artefact->get('title'));
$smarty->assign('artefactdescription', $artefact->get('description'));
$smarty->assign('artefactmotivation', $artefact->get('motivation'));


if ($limit<$items['count']){
	$smarty->assign('urlallitems', '<a href="' . get_config('wwwroot') . 'artefact/checklist/checklist.php?id='.$artefact->get('id').'&amp;offset=0&amp;limit='.$items['count'].'">'.get_string('allitems','artefact.checklist',$items['count']).'</a>');
}
else{
	$smarty->assign('urlallitems', '<a href="' . get_config('wwwroot') . 'artefact/checklist/checklist.php?id='.$artefact->get('id').'&amp;offset=0&amp;limit=10">'.get_string('paginationitems','artefact.checklist',10).'</a>');
}
if ($order=='ASC'){
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/checklist/checklist.php?id='.$artefact->get('id').'&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=DESC">'.get_string('inverselist','artefact.checklist',10).'</a>');
}
else{
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/checklist/checklist.php?id='.$artefact->get('id').'&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=ASC">'.get_string('inverselist','artefact.checklist',10).'</a>');
}

$smarty->assign('iconcheckpath', ArtefactTypeChecklist::get_icon_checkpath());
$smarty->assign('iconedit', ArtefactTypeChecklist::get_icon_edit());
$smarty->assign('icondelete', ArtefactTypeChecklist::get_icon_delete());
$smarty->assign('iconmovedown', ArtefactTypeChecklist::get_icon_movedown());
$smarty->assign('iconmoveup', ArtefactTypeChecklist::get_icon_moveup());
$smarty->assign('iconeadd', ArtefactTypeChecklist::get_icon_add());


$smarty->assign('strorder', $order);

$smarty->assign('strnoitemsaddone',
    get_string('noitemaddone', 'artefact.checklist',
    '<a href="' . get_config('wwwroot') . 'artefact/checklist/new.php?id='.$artefact->get('id').'">', '</a>'));
$smarty->assign('checklistitemsdescription', get_string('checklistitemsdescription', 'artefact.checklist', get_string('newitem', 'artefact.checklist')));
$smarty->assign('PAGEHEADING', get_string("Items", "artefact.checklist", $artefact->get('title')));
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:itemsedit.tpl');