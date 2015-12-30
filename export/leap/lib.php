<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist-export-leap
 * @author     Jean FRUITET - jean.fruitet@univ-nantes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

class LeapExportElementChecklist extends LeapExportElement {
 	// nouvelles proprietes par rapport a la classe artefact standart
	protected $motivation ='';
    protected $public ='';

    public function get_leap_type() {
        return 'checklist';
    }

    public function get_template_path() {
        return 'export:leap/checklist:content.tpl';
    }

    public function assign_smarty_vars() {
        parent::assign_smarty_vars();
        if ($chk = get_record('artefact_checklist_checklist', 'artefact', $this->artefact->get('id'))) {
            $this->smarty->assign('motivation', $chk->motivation);
            $this->smarty->assign('public', $chk->public);
		}
    }

}

class LeapExportElementItem extends LeapExportElementChecklist {
	protected $code ='';
    protected $scale ='';
    protected $valueindex ='';

    public function get_leap_type() {
        return 'item';
    }

    public function get_template_path() {
        return 'export:leap/checklist:item.tpl';
    }
    public function assign_smarty_vars() {
        parent::assign_smarty_vars();

        if ($itm = get_record('artefact_checklist_item', 'artefact', $this->artefact->get('parent'))) {
            $this->smarty->assign('code', $itm->code);
        	$this->smarty->assign('scale', $itm->scale);
			$this->smarty->assign('valueindex', $itm->valueindex);
		}
    }

}
