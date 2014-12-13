<?php
/**
 *
 * @package    mahara
 * @subpackage blocktype-checklist
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

defined('INTERNAL') || die();

class PluginBlocktypeChecklist extends PluginBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.checklist/checklist');
    }

    public static function get_description() {
        return get_string('description1', 'blocktype.checklist/checklist');
    }

    public static function get_categories() {
        return array('general');
    }

     /**
     * Optional method. If exists, allows this class to decide the title for
     * all blockinstances of this type
     */
    public static function get_instance_title(BlockInstance $bi) {
        $configdata = $bi->get('configdata');

        if (!empty($configdata['artefactid'])) {
            return $bi->get_artefact_instance($configdata['artefactid'])->get('title');
        }
        return '';
    }



    public static function get_instance_javascript(BlockInstance $bi) {
        $blockid = $bi->get('id');
        return array(
            array(
                'file'   => 'js/checklistblock.js',
                'initjs' => "initNewChecklistBlock($blockid);",
            )
        );
    }



    public static function render_instance(BlockInstance $instance, $editing=false) {
        global $exporter;

        require_once(get_config('docroot') . 'artefact/lib.php');
        safe_require('artefact','checklist');

        $configdata = $instance->get('configdata');

		$threshold = (!empty($configdata['threshold'])) ? $configdata['threshold'] : 0;

		$smarty = smarty_core();

        if (!empty($configdata['artefactid'])) {
            if ($checklist = artefact_instance_from_id($configdata['artefactid'])){
				// print_object($checklist);

                $items = ArtefactTypeItem::get_items_threshold($configdata['artefactid'], $threshold); // only if scale valueindex > =1
            	// $template = 'artefact:checklist:itemrows.tpl';
                // $template = '';
            	$blockid = $instance->get('id');
            	if ($exporter) {
                	$pagination = false;
            	}
            	else {
                	$baseurl = $instance->get_view()->get_url();
 	                $baseurl .= ((false === strpos($baseurl, '?')) ? '?' : '&') . 'block=' . $blockid;
     	           	$pagination = array(
        	            'baseurl'   => $baseurl,
            	        'id'        => 'block' . $blockid . '_pagination',
                	    'datatable' => 'itemtable_' . $blockid,
                    	'jsonscript' => 'artefact/checklist/viewitems.json.php',
	                );
    	        }
        	    ArtefactTypeItem::render_items($items, null, $configdata, $pagination, false);

            	if ($exporter && $items['count'] > $items['limit']) {
                	$artefacturl = get_config('wwwroot') . 'view/artefact.php?artefact=' . $configdata['artefactid']
                    	. '&view=' . $instance->get('view');
	                $items['pagination'] = '<a href="' . $artefacturl . '">' . get_string('allitems', 'artefact.checklist') . '</a>';
    	        }
                $smarty->assign('chkdescription', $checklist->get('description'));
                $smarty->assign('chkmotivation', $checklist->get('motivation'));
				$smarty->assign('owner', $checklist->get('owner'));
            	// $smarty->assign('tags', $checklist->get('tags'));
	            $smarty->assign('items', $items);
    	    }
        	else {
            	$smarty->assign('nochecklist','blocktype.checklist/checklist');
			}
		}
        else {
            $smarty->assign('nochecklist','blocktype.checklist/checklist');
		}
        $smarty->assign('blockid', $instance->get('id'));
        return $smarty->fetch('blocktype:checklist:content.tpl');
    }

    // My Checklist blocktype only has 'title' option so next two functions return as normal
    public static function has_instance_config() {
        return true;
    }

    public static function instance_config_form($instance) {
        $instance->set('artefactplugin', 'checklist');
        $configdata = $instance->get('configdata');

        $form = array();

        // Which resume field does the user want
        $form['artefactid'] = self::artefactchooser_element((isset($configdata['artefactid'])) ? $configdata['artefactid'] : null);
        $form['threshold'] = array(
                'type'  => 'checkbox',
                'title' => get_string('threshold', 'blocktype.checklist/checklist'),
				'description' => get_string('thresholddesc', 'blocktype.checklist/checklist'),
                'defaultvalue' => 1,
            );

        return $form;
    }

    public static function artefactchooser_element($default=null) {
        safe_require('artefact', 'checklist');
        return array(
            'name'  => 'artefactid',
            'type'  => 'artefactchooser',
            'title' => get_string('checklisttoshow', 'blocktype.checklist/checklist'),
            'defaultvalue' => $default,
            'blocktype' => 'checklist',
            'selectone' => true,
            'search'    => false,
            'artefacttypes' => array('checklist'),
            'template'  => 'artefact:checklist:artefactchooser-element.tpl',
        );
    }

    public static function allowed_in_view(View $view) {
        return $view->get('owner') != null;
    }
}
