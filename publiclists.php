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
define('SECTION_PAGE', 'index');
define('CHECKLIST_SUBPAGE', 'publiclists');

defined('INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))) . '/init.php');
// require_once('pieforms/pieform.php');
// require_once('license.php');
safe_require('artefact', 'checklist');
// safe_require('artefact', 'file');

define('TITLE', get_string('Publiclists','artefact.checklist'));

if (!PluginArtefactChecklist::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('checklist','artefact.checklist')));
}

// offset and limit for pagination
$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 10);
$order = param_alpha('order', 'ASC');

$checklist = ArtefactTypeChecklist::get_checklists(1, $offset, $limit, $order);  // public lists only
ArtefactTypeChecklist::build_publiclists_list_html($checklist);

$js = <<< EOF
addLoadEvent(function () {
    {$checklist['pagination_js']}
});
EOF;

$smarty = smarty(array('paginator'));


$smarty->assign_by_ref('checklist', $checklist);
if ($limit<$checklist['count']){
	$smarty->assign('urlalllists', '<a href="' . get_config('wwwroot') . 'artefact/checklist/publiclists.php?public=1&amp;offset=0&amp;limit='.$checklist['count'].'&amp;order='.$order.'">'.get_string('alllists','artefact.checklist',$checklist['count']).'</a>');
}
else{
	$smarty->assign('urlalllists', '<a href="' . get_config('wwwroot') . 'artefact/checklist/publiclists.php?public=1&amp;offset=0&amp;limit=10&amp;order='.$order.'">'.get_string('paginationlists','artefact.checklist',10).'</a>');
}
if ($order=='ASC'){
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/checklist/publiclists.php?public=1&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=DESC">'.get_string('inverselist','artefact.checklist').'</a>');
}
else{
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/checklist/publiclists.php?public=1&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=ASC">'.get_string('inverselist','artefact.checklist').'</a>');
}
$smarty->assign('iconconfigure', ArtefactTypeChecklist::get_icon_configure());

$smarty->assign('strnochecklistaddone',
    get_string('nopubliclistaddone', 'artefact.checklist',
    '<a href="' . get_config('wwwroot') . 'artefact/checklist/new.php">', '</a>'));
$smarty->assign('PAGEHEADING', hsc(get_string("Publiclists", "artefact.checklist")));
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:publiclists.tpl');
