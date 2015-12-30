<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist-import-leap
 * @author     Jean FRUITET - jean.fruitet@univ-nantes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

/**
 * Implements LEAP2A import of checklist/item entries into Mahara
 *
 * Mahara currently only has two levels of checklist, but the exporting
 * system may have more, so the strategy will be to find all the checklists
 * that are not part of another checklist, use those for the top level, with
 * everything else crammed in at the second level.
 */

class LeapImportChecklist extends LeapImportArtefactPlugin {

    const STRATEGY_IMPORT_AS_CHECKLIST = 1;

    // Keep track of checklist ancestors which will become item parents
    private static $ancestors = array();
    private static $parents = array();

    public static function get_import_strategies_for_entry(SimpleXMLElement $entry, PluginImportLeap $importer) {
        $strategies = array();

        // Mahara can't handle html checklist yet, so don't claim to be able to import them.
        if (PluginImportLeap::is_rdf_type($entry, $importer, 'checklist')
            && (empty($entry->content['type']) || (string)$entry->content['type'] == 'text')) {
            $strategies[] = array(
                'strategy' => self::STRATEGY_IMPORT_AS_CHECKLIST,
                'score'    => 90,
                'other_required_entries' => array(),
            );
        }

        return $strategies;
    }

    public static function add_import_entry_request_using_strategy(SimpleXMLElement $entry, PluginImportLeap $importer, $strategy, array $otherentries) {
        if ($strategy != self::STRATEGY_IMPORT_AS_CHECKLIST) {
            throw new ImportException($importer, 'TODO: get_string: unknown strategy chosen for importing entry');
        }
        self::add_import_entry_request_checklist($entry, $importer);
    }

/**
 * Import from entry requests for Mahara checklists and their items
 *
 * @param PluginImportLeap $importer
 * @return updated DB
 * @throw    ImportException
 */
    public static function import_from_requests(PluginImportLeap $importer) {
        $importid = $importer->get('importertransport')->get('importid');
        if ($entry_requests = get_records_select_array('import_entry_requests', 'importid = ? AND plugin = ? AND entrytype = ?', array($importid, 'checklists', 'checklist'))) {
            foreach ($entry_requests as $entry_request) {
                if ($checklistid = self::create_artefact_from_request($importer, $entry_request)) {
                    if ($checklistitem_requests = get_records_select_array('import_entry_requests', 'importid = ? AND entryparent = ? AND entrytype = ?', array($importid, $entry_request->entryid, 'item'))) {
                        foreach ($checklistitem_requests as $checklistitem_request) {
                            self::create_artefact_from_request($importer, $checklistitem_request, $$checklistid);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param SimpleXMLElement $entry
     * @param PluginImportLeap $importer
     * @param unknown_type $strategy
     * @param array $otherentries
     * @throws ImportException
     */
    public static function import_using_strategy(SimpleXMLElement $entry, PluginImportLeap $importer, $strategy, array $otherentries) {

        if ($strategy != self::STRATEGY_IMPORT_AS_CHECKLIST) {
            throw new ImportException($importer, 'TODO: get_string: unknown strategy chosen for importing entry');
        }

        $artefactmapping = array();
        $artefactmapping[(string)$entry->id] = self::create_checklist($entry, $importer);
        return $artefactmapping;
    }

    /**
     * Get the id of the plan entry which ultimately contains this entry
     */
    public static function get_ancestor_entryid(SimpleXMLElement $entry, PluginImportLeap $importer) {
        $entryid = (string)$entry->id;

        if (!isset(self::$ancestors[$entryid])) {
            self::$ancestors[$entryid] = null;
            $child = $entry;

            while ($child) {
                $childid = (string)$child->id;

                if (!isset(self::$parents[$childid])) {
                    self::$parents[$childid] = null;

                    foreach ($child->link as $link) {
                        $href = (string)$link['href'];
                        if ($href != $entryid
                            && $importer->curie_equals($link['rel'], PluginImportLeap::NS_LEAP, 'is_part_of')
                            && $importer->entry_has_strategy($href, self::STRATEGY_IMPORT_AS_CHECKLIST, 'checklist')) {
                            self::$parents[$childid] = $href;
                            break;
                        }
                    }
                }

                if (!self::$parents[$childid]) {
                    break;
                }
                if ($child = $importer->get_entry_by_id(self::$parents[$childid])) {
                    self::$ancestors[$entryid] = self::$parents[$childid];
                }
            }
        }

        return self::$ancestors[$entryid];
    }


    /**
     * Add import entry request for a checklist or an item from the given entry
     * TODO: Refactor this to combine it with create_checklist()
     *
     * @param SimpleXMLElement $entry    The entry for the checklist or item
     * @param PluginImportLeap $importer The importer
     */
    private static function add_import_entry_request_checklist(SimpleXMLElement $entry, PluginImportLeap $importer) {

        // First decide if it's going to be a checklist or an item depending
        // on whether it has any ancestral checklists.

        if ($ancestorid = self::get_ancestor_entryid($entry, $importer)) {
            $type = 'item';
        }
        else {
            $type = 'checklist';
        }

        if (isset($entry->author->name) && strlen($entry->author->name)) {
            $authorname = $entry->author->name;
        }
        else {
            $author = $importer->get('usr');
        }

        // Set completiondate and completed status if we can find them
        if ($type === 'item') {

            $namespaces = $importer->get_namespaces();
            $ns = $importer->get_leap2a_namespace();

            $dates = PluginImportLeap::get_leap_dates($entry, $namespaces, $ns);
            if (!empty($dates['target']['value'])) {
                $completiondate = strtotime($dates['target']['value']);
            }
            $completiondate = empty($completiondate) ? $updated : $completiondate;

            $completed = 0;
            if ($entry->xpath($namespaces[$ns] . ':status[@' . $namespaces[$ns] . ':stage="completed"]')) {
                $completed = 1;
            }
        }

        PluginImportLeap::add_import_entry_request($importer->get('importertransport')->get('importid'), (string)$entry->id, self::STRATEGY_IMPORT_AS_CHECKLIST, 'checklist', array(
            'owner'   => $importer->get('usr'),
            'type'    => $type,
            'parent'  => $ancestorid,
            'content' => array(
                'title'       => (string)$entry->title,
                'description' => PluginImportLeap::get_entry_content($entry, $importer),
                'authorname'  => isset($authorname) ? $authorname : null,
                'author'      => isset($author) ? $author : null,
                'ctime'       => (string)$entry->published,
                'mtime'       => (string)$entry->updated,
                'motivation' => ($type === 'checklist') ? $motivation : null,
                'code'   => ($type === 'item') ? $code : null,
                'scale'   => ($type === 'item') ? $scale : null,
                'tags'        => PluginImportLeap::get_entry_tags($entry),
            ),
        ));
    }

    /**
     * Creates a checklist or item from the given entry
     * TODO: Refactor this to combine it with add_import_entry_request_checklist()
     *
     * @param SimpleXMLElement $entry    The entry to create the  checklist or item from
     * @param PluginImportLeap $importer The importer
     * @return array A list of artefact IDs created, to be used with the artefact mapping.
     */
    private static function create_checklist(SimpleXMLElement $entry, PluginImportLeap $importer) {

        // First decide if it's going to be a checklist or a item depending
        // on whether it has any ancestral checklist.

        if (self::get_ancestor_entryid($entry, $importer)) {
            $artefact = new ArtefactTypeItem();
        }
        else {
            $artefact = new ArtefactTypeChecklist();
        }

        $artefact->set('title', (string)$entry->title);
        $artefact->set('description', PluginImportLeap::get_entry_content($entry, $importer));
        $artefact->set('owner', $importer->get('usr'));
        if (isset($entry->author->name) && strlen($entry->author->name)) {
            $artefact->set('authorname', $entry->author->name);
        }
        else {
            $artefact->set('author', $importer->get('usr'));
        }
        if ($published = strtotime((string)$entry->published)) {
            $artefact->set('ctime', $published);
        }
        if ($updated = strtotime((string)$entry->updated)) {
            $artefact->set('mtime', $updated);
        }

        $artefact->set('tags', PluginImportLeap::get_entry_tags($entry));

        // Set motivation if we can find them
        if ($artefact instanceof ArtefactTypeChecklist) {
	        if (isset($entry->motivation) && strlen($entry->motivation)) {
            	$artefact->set('motivation', $entry->motivation);
        	}
        	else {
            	$artefact->set('motivation', '');
        	}
		}

        // Set code and scale  if we can find them
        if ($artefact instanceof ArtefactTypeItem) {
	        if (isset($entry->code) && strlen($entry->code)) {
            	$artefact->set('code', $entry->code);
        	}
        	else {
            	$artefact->set('code', $entry->title);
        	}
        	if (isset($entry->scale) && strlen($entry->scale)) {
            	$artefact->set('scale', $entry->scale);
        	}
        	else {
            	$artefact->set('scale', '');
        	}
		}

        $artefact->commit();

        return array($artefact->get('id'));
    }

    /**
     * Set item parents
     */
    public static function setup_relationships(SimpleXMLElement $entry, PluginImportLeap $importer) {
        if ($ancestorid = self::get_ancestor_entryid($entry, $importer)) {
            $ancestorids = $importer->get_artefactids_imported_by_entryid($ancestorid);
            $artefactids = $importer->get_artefactids_imported_by_entryid((string)$entry->id);
            if (empty($artefactids[0])) {
                throw new ImportException($importer, 'Task artefact not found: ' . (string)$entry->id);
            }
            if (empty($ancestorids[0])) {
                throw new ImportException($importer, 'Plan artefact not found: ' . $ancestorid);
            }
            $artefact = new ArtefactTypeItem($artefactids[0]);
            $artefact->set('parent', $ancestorids[0]);
            $artefact->commit();
        }
    }

    /**
     * Render import entry requests for Mahara checklists and their itemss
     * @param PluginImportLeap $importer
     * @return HTML code for displaying plans and choosing how to import them
     */
    public static function render_import_entry_requests(PluginImportLeap $importer) {
        $importid = $importer->get('importertransport')->get('importid');
        // Get import entry requests for Mahara checklists
        $entrychecklists = array();
        if ($ierchecklists = get_records_select_array('import_entry_requests', 'importid = ? AND entrytype = ?', array($importid, 'checklist'))) {
            foreach ($ierchecklists as $ierchecklist) {
                $checklist = unserialize($ierchecklist->entrycontent);
                $checklist['id'] = $ierchecklist->id;
                $checklist['decision'] = $ierchecklist->decision;
                if (is_string($ierchecklist->duplicateditemids)) {
                    $ierchecklist->duplicateditemids = unserialize($ierchecklist->duplicateditemids);
                }
                if (is_string($ierchecklist->existingitemids)) {
                    $ierchecklist->existingitemids = unserialize($ierchecklist->existingitemids);
                }
                $checklist['disabled'][PluginImport::DECISION_IGNORE] = false;
                $checklist['disabled'][PluginImport::DECISION_ADDNEW] = false;
                $checklist['disabled'][PluginImport::DECISION_APPEND] = true;
                $checklist['disabled'][PluginImport::DECISION_REPLACE] = true;
                if (!empty($ierchecklist->duplicateditemids)) {
                    $duplicated_item = artefact_instance_from_id($ierchecklist->duplicateditemids[0]);
                    $checklist['duplicateditem']['id'] = $duplicated_item->get('id');
                    $checklist['duplicateditem']['title'] = $duplicated_item->get('title');
                    $res = $duplicated_item->render_self(array());
                    $checklist['duplicateditem']['html'] = $res['html'];
                }
                else if (!empty($ierchecklist->existingitemids)) {
                    foreach ($ierchecklist->existingitemids as $id) {
                        $existing_item = artefact_instance_from_id($id);
                        $res = $existing_item->render_self(array());
                        $checklist['existingitems'][] = array(
                            'id'    => $existing_item->get('id'),
                            'title' => $existing_item->get('title'),
                            'html'  => $res['html'],
                        );
                    }
                }
                // Get import entry requests of items in the checklist
                $entrytasks = array();
                if ($ieritems = get_records_select_array('import_entry_requests', 'importid = ? AND entrytype = ? AND entryparent = ?',
                        array($importid, 'item', $ierchecklist->entryid))) {
                    foreach ($ieritems as $ieritem) {
                        $item = unserialize($ieritem->entrycontent);
                        $item['id'] = $ieritem->id;
                        $item['decision'] = $ieritem->decision;
                        $item['completiondate'] = format_date($item['completiondate'], 'strftimedate');
                        $item['disabled'][PluginImport::DECISION_IGNORE] = false;
                        $item['disabled'][PluginImport::DECISION_ADDNEW] = false;
                        $item['disabled'][PluginImport::DECISION_APPEND] = true;
                        $item['disabled'][PluginImport::DECISION_REPLACE] = true;
                        $entryitems[] = $item;
                    }
                }
                $checklist['entryitems'] = $entryitems;
                $entrychecklists[] = $checklist;
            }
        }
        $smarty = smarty_core();
        $smarty->assign_by_ref('displaydecisions', $importer->get('displaydecisions'));
        $smarty->assign_by_ref('entrychecklists', $entrychecklists);
        return $smarty->fetch('artefact:checklist:import/checklists.tpl');
    }
}
