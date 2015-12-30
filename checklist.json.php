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

$limit = param_integer('limit', 10);
$offset = param_integer('offset', 0);

$listes = ArtefactTypeChecklist::get_checklists(0, $offset, $limit);
ArtefactTypeChecklist::build_checklist_list_html($listes);

json_reply(false, (object) array('message' => false, 'data' => $listes));
