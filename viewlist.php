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
define('CHECKLIST_SUBPAGE', 'publiclists');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'checklist');
if (!PluginArtefactChecklist::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('checklist','artefact.checklist')));
}

$id = param_integer('id');

// offset and limit for pagination
$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 10);
$order = param_alpha('order', 'ASC');

$artefact = new ArtefactTypeChecklist($id);
if (!$artefact->get('public')) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

define('TITLE', get_string('Items', 'artefact.checklist', $artefact->get('title') ));

$items = ArtefactTypeItem::get_items($artefact->get('id'), $offset, $limit, $order);
//print_object($items);

ArtefactTypeItem::build_items_flatlist_html($items);

$js = <<< EOF
addLoadEvent(function () {
    {$items['pagination_js']}
});
EOF;


$smarty = smarty(array('paginator'));
$smarty->assign_by_ref('artefacttitle', $artefact->get('title'));
$smarty->assign_by_ref('items', $items);
$smarty->assign_by_ref('checklist', $id);
$smarty->assign_by_ref('tags', $artefact->get('tags'));
$smarty->assign_by_ref('owner', $artefact->get('owner'));


if ($limit<$items['count']){
	$smarty->assign('urlallitems', '<a href="' . get_config('wwwroot') . 'artefact/checklist/viewlist.php?id='.$artefact->get('id').'&amp;offset=0&amp;limit='.$items['count'].'">'.get_string('allitems','artefact.checklist',$items['count']).'</a>');
}
else{
	$smarty->assign('urlallitems', '<a href="' . get_config('wwwroot') . 'artefact/checklist/viewlist.php?id='.$artefact->get('id').'&amp;offset=0&amp;limit=10">'.get_string('paginationitems','artefact.checklist',10).'</a>');
}
if ($order=='ASC'){
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/checklist/viewlist.php?id='.$artefact->get('id').'&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=DESC">'.get_string('inverselist','artefact.checklist',10).'</a>');
}
else{
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/checklist/viewlist.php?id='.$artefact->get('id').'&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=ASC">'.get_string('inverselist','artefact.checklist',10).'</a>');
}

$smarty->assign('iconcheckpath', ArtefactTypeChecklist::get_icon_checkpath());
$smarty->assign('strorder', $order);

$smarty->assign('PAGEHEADING', get_string("Items", "artefact.checklist", $artefact->get('title')));
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:itemsflatlist.tpl');