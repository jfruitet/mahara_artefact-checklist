<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist-export-html
 * @author     Jean FRUITET - jean.fruitet@univ-nantes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

class HtmlExportChecklist extends HtmlExportArtefactPlugin {

    public function pagination_data($artefact) {
        if ($artefact instanceof ArtefactTypeChecklist) {
            return array(
                'perpage'    => 10,
                'childcount' => $artefact->count_children(),
                'plural'     => get_string('checklist', 'artefact.checklist'),
            );
        }
    }

    public function dump_export_data() {
        foreach ($this->exporter->get('artefacts') as $artefact) {
            if ($artefact instanceof ArtefactTypeChecklist) {
                $this->paginate($artefact);
            }
        }
    }

    public function get_summary() {
        $smarty = $this->exporter->get_smarty();
        $checklists = array();
        foreach ($this->exporter->get('artefacts') as $artefact) {
            if ($artefact instanceof ArtefactTypeChecklist) {
                $checklists[] = array(
                    'link' => 'files/checklist/' . PluginExportHtml::text_to_URLpath(PluginExportHtml::text_to_filename($artefact->get('title'))) . '/index.html',
                    'title' => $artefact->get('title'),
                );
            }
        }
        $smarty->assign('checklist', $checklist);

        return array(
            'title' => get_string('checklist', 'artefact.checklist'),
            'description' => $smarty->fetch('export:html/checklist:summary.tpl'),
        );
    }

    public function get_summary_weight() {
        return 40;
    }
}
