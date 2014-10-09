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
define('MENUITEM', 'content/chechklistt');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'checklist');
define('SECTION_PAGE', 'index');
defined('INTERNAL') || die();
require_once(dirname(dirname(dirname(__FILE__))) . '/init.php');
//require_once('pieforms/pieform.php');
safe_require('artefact', 'checklist');

define('TITLE', get_string('Checklists','artefact.checklist'));

if (!PluginArtefactChecklist::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('checklist','artefact.checklist')));
}

$id = param_integer('id', null);
$title = urldecode(param_variable('title', null));
$description = urldecode(param_variable('description', null));
$motivation = urldecode(param_variable('motivation', null));
$public = param_integer('public', null);
$itemsids = param_variable('itemsids', null);
$resetitems = param_integer('resetitems', 1);

if (get_config('licensemetadata')) {
    $license = urldecode(param_variable('license', null));
    $licensor = urldecode(param_variable('licensor', null));
    $licensorurl = urldecode(param_variable('licensorurl', null));
}

if (empty($id)){
	redirect(get_config('wwwroot').'/artefact/checklist/index.php');
}

// Items selected for export
$artefactitems = array();

// Debug
//echo "<br />DEBUG :: exportxml.php :: 48<br />\n";
//echo $itemsids."<br />\n";


if (!empty($itemsids)){
	if (preg_match("/,/", $itemsids)){
   		if ($tabids = explode(',', $itemsids)){
			foreach ($tabids as $itemid){
				if (!empty($itemid)){
					// Item artefact
					//echo "<br /> $itemid\n";
					$aitem = ArtefactTypeItem::get_item($itemid);
					//print_object($aitem);
					// exit;
					$artefactitems[] = $aitem;
				}
			}
		}
	}
}

// Some data of artefact checklist exported
	$exportclass = new stdClass();
    $exportclass->id = $id;       // inutile en realite
    $exportclass->title = $title;
    $exportclass->description = strip_tags($description);
    $exportclass->motivation = strip_tags($motivation);
    $exportclass->public = $public;
    $exportclass->license = $license;
	$exportclass->licensor = $licensor;
    $exportclass->licensorurl = $licensorurl;

// Items exported
    $exportclass->items = array();

if (!empty($artefactitems)){
		foreach ($artefactitems as $aitem){
			$item = new stdClass();
            $item->owner = $USER->get('id');
            $item->id = $aitem->id;
            $item->parent = $id;
            $item->title = $aitem->title;
        	$item->description = strip_tags($aitem->description);
        	$item->code = $aitem->code;
        	$item->scale = $aitem->scale;
        	$item->valueindex = ($resetitems) ? 0 : $aitem->valueindex;
            $item->optionitem = $aitem->optionitem;
            $item->displayorder = $aitem->displayorder;

            if (get_config('licensemetadata')) {
            	$item->license = $aitem->license;
            	$item->licensor = $aitem->licensor;
            	$item->licensorurl = $aitem->licensorurl;
        	}

            $exportclass->items[]=$item;
		}
}

// XML export here

$name = "exported_checklist_" . implode('_', explode(' ',trim($exportclass->title . date("_Y_m_d"))));
$xml = set_xml($exportclass);
//print_object($xml);
//exit;

header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=$name.xml");

print($xml);

die();