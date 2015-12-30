<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist
 * @author     JF
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('JSON', 1);

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'checklist');

$checklist = param_integer('id');
$limit = param_integer('limit', 10);
$offset = param_integer('offset', 0);

if (!$USER->can_edit_artefact(new ArtefactTypeChecklist($checklist))) {
    json_reply(true, get_string('accessdenied', 'error'));
}

$items = ArtefactTypeItem:get_items($checklist, $offset, $limit);
ArtefactTypeItem::build_items_list_html($items);

json_reply(false, (object) array('message' => false, 'data' => $items));
