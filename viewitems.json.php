<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-plans
 * @author     Jean FRUITET - jean.fruitet@univ-nantes.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('JSON', 1);

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'checklist');
require_once(get_config('docroot') . 'blocktype/lib.php');
require_once(get_config('docroot') . 'artefact/checklist/blocktype/checklist/lib.php');

$offset = param_integer('offset', 0);
$limit = param_integer('limit', 10);

if ($blockid = param_integer('block', null)) {
    $bi = new BlockInstance($blockid);
    if (!can_view_view($bi->get('view'))) {
        json_reply(true, get_string('accessdenied', 'error'));
    }
    $options = $configdata = $bi->get('configdata');

    $items = ArtefactTypeItem::get_items($configdata['artefactid'], $offset, $limit);

    $template = 'artefact:checklist:itemrows.tpl';
    $baseurl = $bi->get_view()->get_url();
    $baseurl .= ((false === strpos($baseurl, '?')) ? '?' : '&') . 'block=' . $blockid;
    $pagination = array(
        'baseurl'   => $baseurl,
        'id'        => 'block' . $blockid . '_pagination',
        'datatable' => 'itemtable_' . $blockid,
        'jsonscript' => 'artefact/checklist/viewitems.json.php',
    );
}
else {
    $checklistid = param_integer('artefact');
    $viewid = param_integer('view');
    if (!can_view_view($viewid)) {
        json_reply(true, get_string('accessdenied', 'error'));
    }
    $options = array('viewid' => $viewid);
    $items = ArtefactTypeItem::get_items($checklistid, $offset, $limit);

    $template = 'artefact:checklist:itemrows.tpl';
    $baseurl = get_config('wwwroot') . 'view/artefact.php?artefact=' . $checklistid . '&view=' . $options['viewid'];
    $pagination = array(
        'baseurl' => $baseurl,
        'id' => 'item_pagination',
        'datatable' => 'itemtable',
        'jsonscript' => 'artefact/checklist/viewitems.json.php',
    );

}
ArtefactTypeItem::render_items($items, $template, $options, $pagination);

json_reply(false, (object) array('message' => false, 'data' => $items));
