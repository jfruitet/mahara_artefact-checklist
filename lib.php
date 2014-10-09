<?php
/**
 * Mahara: Electronic portfolio, weblog, checklist builder and social networking
 * Copyright (C) 2006-2008 Catalyst IT Ltd (http://www.catalyst.net.nz)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mahara
 * @subpackage artefact-checklist
 * @author     Jean FRUITET - jean.fruitet@univ-nantes.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2012 Technische Universitaet Darmstadt, Germany
 *
 */

defined('INTERNAL') || die();

class PluginArtefactChecklist extends PluginArtefact {

	/** 
     * This function returns a list of classnames 
     * of artefact types this plugin provides.
     * @abstract
     * @return array
     */
	public static function get_artefact_types() {
		return array(
			'item',
			'checklist',
		);
	}
	
    /**
    * This function returns a list of classnames
    * of block types this plugin provides
    * they must match directories inside artefact/$name/blocktype
    * @abstract
    * @return array
    */
    public static function get_block_types() {
        return array();
    }

	public static function get_plugin_name() {
		return 'checklist';
	}

    public static function is_active() {
        return get_field('artefact_installed', 'active', 'name', 'checklist');
    }

	
    /**
     * This function returns an array of menu items
     * to be displayed
     * Each item should be a stdClass object containing -
     * - name language pack key
     * - url relative to wwwroot
     * @return array
     */
	public static function menu_items() {
		return array(
   			'content/checklist' => array(
				'path' => 'content/checklist',
				'url' => 'artefact/checklist/index.php',
				'title' => get_string('checklist_short', 'artefact.checklist'),
				'weight' => 60,
			)
		);
	}
	
	/**
	This function return a list of submenues
	*/
    public static function submenu_items() {
        $tabs = array(
            'index' => array(
                'page'  => 'index',
                'url'   => 'artefact/checklist',
                'title' => get_string('mychecklists', 'artefact.checklist'),
            ),
            'publiclists' => array(
                'page'  => 'publiclists',
                'url'   => 'artefact/checklist/publiclists.php',
                'title' => get_string('publiclists', 'artefact.checklist'),
            ),
            'import' => array(
                'page'  => 'import',
                'url'   => 'artefact/checklist/import.php',
                'title' => get_string('import', 'artefact.checklist'),
            ),
        );
        if (defined('CHECKLIST_SUBPAGE') && isset($tabs[CHECKLIST_SUBPAGE])) {
            $tabs[CHECKLIST_SUBPAGE]['selected'] = true;
        }
        return $tabs;
    }


	 /**
     * When filtering searches, some artefact types are classified the same way
     * even when they come from different artefact plugins.  This function allows
     * artefact plugins to declare which search filter content type each of their
     * artefact types belong to.
     * @return array of artefacttype => array of filter content types
     */
    public static function get_artefact_type_content_types() {
        return array(
            'checklist' => array('text'),
			'item' =>  array('text'),
        );
    }

	 
    /**
     * Returns the relative URL path to the place in mahara that relates
     * to the artefact.
     * E.g. For plan artefact the link will be 'artefact/plans/index.php'
     * @param int The name of the artefact type (in case different ones need different links)
     * @return string Url path to artefact.
     */	 
    public static function progressbar_link($artefacttype) {
        return 'artefact/checklist/index.php';
    }

  	public static function get_activity_types() {
        return array();
    }

	public static function get_cron() {
		return array();
	}

	public static function postinst($prevversion) {
        if ($prevversion == 0) {
            $sort = (get_record_sql('SELECT MAX(sort) AS maxsort FROM {blocktype_category}')->maxsort) + 1;
            insert_record('blocktype_category', (object)array('name' => 'checklist', 'sort' => $sort));
            /* log_warn('installation de la categorie checklist'); */
        }
        else {
            /* log_warn('pas d installation necessaire de la categorie checklist'); */
        }
    }


}


class ArtefactTypeChecklist extends ArtefactType {

 	// nouvelles proprietes par rapport Ó la classe artefact standart
	protected $motivation = '';
    protected $public = 0;
	
    public function __construct($id = 0, $data = null) {

        if (empty($this->id)) {
            $this->container = 1;
        }

        parent::__construct($id, $data);

        if ($this->id) {
            if ($pdata = get_record('artefact_checklist_checklist', 'artefact', $this->id)) {
                foreach($pdata as $name => $value) {
                    if (property_exists($this, $name)) {
                        $this->{$name} = $value;
                    }
                }
            }
            else {
                // This should never happen unless the user is playing around with task IDs in the location bar or similar
                throw new ArtefactNotFoundException(get_string('checklistdoesnotexist', 'artefact.checklist'));
            }
        }

    }
	
	public static function get_links($id) {
        return array(
            '_default' => get_config('wwwroot') . 'artefact/checklist/checklist.php?id=' . $id,
        );

	}


    /**
     * Returns a URL for an icon for the appropriate artefact
     *
     * @param array $options Options for the artefact. The array MUST have the
     *                       'id' key, representing the ID of the artefact for
     *                       which the icon is being generated. Other keys
     *                       include 'size' for a [width]x[height] version of
     *                       the icon, as opposed to the default 20x20, and
     *                       'view' for the id of the view in which the icon is
     *                       being displayed.
     * @abstract
     * @return string URL for the icon
     */
	public static function get_icon($options=null) {
        global $THEME;
        return $THEME->get_url('images/checklist.png', false, 'artefact/checklist');
	}


    /**
     * Returns a URL for an icon for the appropriate artefact
     *
     * @return string URL for the icon
     */
	public static function get_icon_checkpath($options=null) {
        global $THEME;
        return $THEME->get_url('images/btn_check.png', false, 'artefact/checklist');
	}



    /**
     * This method extends ArtefactType::commit() by adding additional data
     * into the artefact_checklist_checklist table.
     * If artefact has extra information in other tables, you need modify
     * this method, and call parent::commit() in your own function.
     */
    public function commit() {
        if (empty($this->dirty)) {
            return;
        }

        // Return whether or not the commit worked
        $success = false;

        db_begin();
        $new = empty($this->id);

        parent::commit();

        $this->dirty = true;

        $data = (object)array(
   			'artefact'  	=> $this->get('id'),
            'motivation' 	=> $this->get('motivation'),
            'public' 		=> $this->get('public'),
        );
		// DEBUG
		// print_object($data);
		// exit;
        if ($new) {
            $success = insert_record('artefact_checklist_checklist', $data);
        }
        else {
            $success = update_record('artefact_checklist_checklist', $data, 'artefact');
        }

        db_commit();

        $this->dirty = $success ? false : true;

        return $success;
    }


    /** 
     * This function provides basic delete functionality.  It gets rid of the
     * artefact's row in the artefact table, and the tables that reference the
     * artefact table.  It also recursively deletes child artefacts.
     *
     * If your artefact has additional data in another table, you should
     * modify this function, but you MUST call parent::delete() after you
     * have done your own thing.
     */
    public function delete() {
        if (empty($this->id)) {
            return;
        }

        db_begin();
        delete_records('artefact_checklist_checklist', 'artefact', $this->id);

        parent::delete();
        db_commit();
    }

    public static function bulk_delete($artefactids) {
        if (empty($artefactids)) {
            return;
        }

        $idstr = join(',', array_map('intval', $artefactids));

        db_begin();
        delete_records_select('artefact_checklist_checklist', 'artefact IN (' . $idstr . ')');
        parent::bulk_delete($artefactids);
        db_commit();
    }


	public static function is_singular() {
		return false;
	}

   /**
     * This function returns a checklist of the given user's.
     *
     * @return array (count: integer, data: array)
     */
    public static function get_checklist($id) {
        global $USER;

        ($checklist = get_record_sql("
							SELECT a.*, ac.motivation, ac.public
							FROM {artefact} a
            				JOIN {artefact_checklist_checklist} ac ON ac.artefact = a.id
                            WHERE a.id = ? AND a.owner = ? AND a.artefacttype = 'checklist'
                            ORDER BY a.title ASC", array($id, $USER->get('id'))));

 		if ($checklist){
            $checklist->description = '<p>' . preg_replace('/\n\n/','</p><p>', $checklist->description) . '</p>';
            $checklist->motivation = '<p><i>' . preg_replace('/\n\n/','</p><p>', $checklist->motivation) . '</i></p>';
            if (!empty($checklist->public)){
				$checklist->public = '<p>' . get_string('publiclist', 'artefact.checklist').'</p>';
			}
		}

        return $checklist;
    }


   /**
     * This function returns a list of the given user's checklists.
     *
     * @param limit how many checklist to display per page
     * @param offset current page to display
     * @return array (count: integer, data: array)
     */

    public static function get_checklists($public=0, $offset=0, $limit=10, $order='ASC') {
        global $USER;
		if (!empty($public)){
			($checklists = get_records_sql_array("
							SELECT a.* , ac.motivation
							FROM {artefact} a
            				JOIN {artefact_checklist_checklist} ac ON ac.artefact = a.id
                            WHERE ac.public = ? AND a.artefacttype = 'checklist'
                            ORDER BY a.title ".$order, array(1), $offset, $limit))
                            || ($checklists = array());
			$count = count(get_records_sql_array("
							SELECT a.id
							FROM {artefact} a
            				JOIN {artefact_checklist_checklist} ac ON ac.artefact = a.id
                            WHERE ac.public = ? AND a.artefacttype = 'checklist'
                            ", array(1)));
		}
        else{
			($checklists = get_records_sql_array("
							SELECT a.* , ac.motivation
							FROM {artefact} a
            				JOIN {artefact_checklist_checklist} ac ON ac.artefact = a.id
                            WHERE a.owner = ? AND a.artefacttype = 'checklist'
                            ORDER BY a.title ".$order, array($USER->get('id')), $offset, $limit))
                            || ($checklists = array());
			$count = count_records('artefact', 'owner', $USER->get('id'), 'artefacttype', 'checklist');
		}

        foreach ($checklists as &$checklist) {
            $checklist->description = '<p>' . preg_replace('/\n\n/','</p><p>', $checklist->description) . '</p>';
            $checklist->motivation = '<p><i>' . preg_replace('/\n\n/','</i></p><p><i>', $checklist->motivation) . '</i></p>';
        }

		$result = array(
            'count'  =>  $count,
	        'data'   => $checklists,
            'offset' => $offset,
            'limit'  => $limit,
        );

        return $result;
    }

       /**
    * Gets the new/edit fields for the checklist pieform
    *
    */
    public static function get_checklistform_elements($checklist) {
        $elements = array(
            'title' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('title', 'artefact.checklist'),
                'size' => 30,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'description' => array(
                'type'  => 'wysiwyg',
                'rows' => 10,
                'cols' => 50,
                'resizable' => true,
                'defaultvalue' => null,
                'title' => get_string('description', 'artefact.checklist'),
            ),
            'motivation' => array(
                'type'  => 'wysiwyg',
                'rows' => 10,
                'cols' => 50,
                'resizable' => true,
                'defaultvalue' => null,
                'title' => get_string('motivation', 'artefact.checklist'),
            ),
            'public' => array(
                'type'  => 'radio',
            	'options' => array(
                	0 => get_string('no'),
                	1 => get_string('yes'),
            	),
            	'defaultvalue' => 0,
            	'rules' => array(
                	'required' => true
            	),
            	'separator' => ' &nbsp; ',
                'title' => get_string('publiclist', 'artefact.checklist'),
                'description' => get_string('publiclistdesc','artefact.checklist'),
            ),

        );

        if (!empty($checklist)) {
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $checklist->get($k);
            }
            $elements['checklist'] = array(
                'type' => 'hidden',
                'value' => $checklist->id,
            );
        }

        if (get_config('licensemetadata')) {
            $elements['license'] = license_form_el_basic($checklist);
            $elements['license_advanced'] = license_form_el_advanced($checklist);
        }

        return $elements;
    }



    /**
     * Builds the checklists list table
     *
     * @param checklists (reference)
     */
    public static function build_checklists_list_html(&$checklists) {
        $smarty = smarty_core();
        $smarty->assign_by_ref('checklists', $checklists);
		$smarty->assign('iconcheckpath', ArtefactTypeChecklist::get_icon_checkpath());

        $checklists['tablerows'] = $smarty->fetch('artefact:checklist:checklistlist.tpl');
        $pagination = build_pagination(array(
            'id' => 'checklistlist_pagination',
            'class' => 'center',
            'url' => get_config('wwwroot') . 'artefact/checklist/index.php',
            // 'jsonscript' => 'artefact/checklist/checklist.json.php',     // source d'erreur inconnue ??????????
            'datatable' => 'checklistlist',
            'count' => $checklists['count'],
            'limit' => $checklists['limit'],
            'offset' => $checklists['offset'],
            'firsttext' => '',
            'previoustext' => '',
            'nexttext' => '',
            'lasttext' => '',
            'numbersincludefirstlast' => false,
            'resultcounttextsingular' => get_string('checklist', 'artefact.checklist'),
            'resultcounttextplural' => get_string('checklists', 'artefact.checklist'),
        ));
        $checklists['pagination'] = $pagination['html'];
        $checklists['pagination_js'] = $pagination['javascript'];
    }


    public static function validate(Pieform $form, $values) {
        global $USER;
        if (!empty($values['checklist'])) {
            $id = (int) $values['checklist'];
            $artefact = new ArtefactTypeChecklist($id);
            if (!$USER->can_edit_artefact($artefact)) {
                $form->set_error('submit', get_string('canteditdontownchecklist', 'artefact.checklist'));
            }
        }
    }


    public static function submit(Pieform $form, $values) {
        global $USER, $SESSION;

        $new = false;

        if (!empty($values['checklist'])) {
            $id = (int) $values['checklist'];
            $artefact = new ArtefactTypeChecklist($id);
        }
        else {
            $artefact = new ArtefactTypeChecklist();
            $artefact->set('owner', $USER->get('id'));
            $new = true;
        }

        $artefact->set('title', $values['title']);
        $artefact->set('description', $values['description']);
        $artefact->set('motivation', $values['motivation']);
        $artefact->set('public', $values['public']);

        if (get_config('licensemetadata')) {
            $artefact->set('license', $values['license']);
            $artefact->set('licensor', $values['licensor']);
            $artefact->set('licensorurl', $values['licensorurl']);
        }


        $artefact->commit();

        $SESSION->add_ok_msg(get_string('checklistsavedsuccessfully', 'artefact.checklist'));

        if ($new) {
            redirect('/artefact/checklist/checklist.php?id='.$artefact->get('id'));
        }
        else {
            redirect('/artefact/checklist/index.php');
        }
    }

    /**
     * Builds the public checklists list table
     *
     * @param checklists (reference)
     */
    public static function build_publiclists_list_html(&$checklists) {
        $smarty = smarty_core();
        $smarty->assign_by_ref('checklists', $checklists);
        $checklists['tablerows'] = $smarty->fetch('artefact:checklist:publiclistlist.tpl');
        $pagination = build_pagination(array(
            'id' => 'checklistlist_pagination',
            'class' => 'center',
            'url' => get_config('wwwroot') . 'artefact/checklist/publiclists.php',
            //'jsonscript' => 'artefact/checklist/checklist.json.php',

            'datatable' => 'checklistlist',
            'count' => $checklists['count'],
            'limit' => $checklists['limit'],
            'offset' => $checklists['offset'],
            'firsttext' => '',
            'previoustext' => '',
            'nexttext' => '',
            'lasttext' => '',
            'numbersincludefirstlast' => false,
            'resultcounttextsingular' => get_string('plist', 'artefact.checklist'),
            'resultcounttextplural' => get_string('plists', 'artefact.checklist'),
        ));
        $checklists['pagination'] = $pagination['html'];
        $checklists['pagination_js'] = $pagination['javascript'];
    }



    /**
    * Gets the new/edit checklist pieform
    *
    */
    public static function get_form($checklist=null) {
        require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('checklist'), 'get_checklistform_elements', $checklist);
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('savechecklist','artefact.checklist'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/checklist/index.php',
        );
        $checklistform = array(
            'name' => empty($checklist) ? 'addchecklist' : 'editchecklist',
            'plugintype' => 'artefact',
            'pluginname' => 'item',
            'validatecallback' => array(generate_artefact_class_name('checklist'),'validate'),
            'successcallback' => array(generate_artefact_class_name('checklist'),'submit'),
            'elements' => $elements,
        );

        return pieform($checklistform);
    }

	/******************  Select lists ******************************************************************/
    /**
    * Gets the new/edit checklist pieform
    *
    */
    public static function get_form_select($checklist=null, $items=null) {
        require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('checklist'), 'get_publiclistform_elements', $checklist);
		//echo "ITEMS<br />\n";
        //print_object($elements);
		//exit;
		if (!empty($items['data'])){
			$i = 0;
			foreach ($items['data'] as $item){
                //echo "<br />ITEM<br />\n";
				//print_object($item);
                //$name="'select_".$item->id."'";
                $elements['select'.$i] = array(
                	'type' => 'checkbox',
                	'defaultvalue' => $item->id,
                	'title' => $item->code,
                	'description' => '',
           		);
                $elements['html'.$i] = array(
                	'type' => 'html',
                	'value' => $item->title.'<br /><i>'.$item->description.'</i>',
           		);

				$elements['code'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->code,
           		);
				$elements['title'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->title,
           		);
				$elements['description'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->description,
           		);
				$elements['scale'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->scale,
           		);
				$elements['valueindex'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->valueindex,
           		);
                $i++;
			}
            $elements['nbitems'] = array(
                	'type' => 'hidden',
                	'value' => $i,
           		);
		}

        //print_object($elements);
		//exit;
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('savechecklist','artefact.checklist'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/checklist/index.php',
        );
        $form = array(
            'name' => empty($checklist) ? 'addchecklist' : 'editchecklist',
            'plugintype' => 'artefact',
            'pluginname' => 'item',
            'validatecallback' => array(generate_artefact_class_name('checklist'),'validate_select'),
            'successcallback' => array(generate_artefact_class_name('checklist'),'submit_select'),
            'elements' => $elements,
        );
        //print_object($form);
		//exit;
        return pieform($form);
    }


    /**
    * Gets the new/edit fields for the checklist pieform
    *
    */
    public static function get_publiclistform_elements($checklist) {
        $elements = array(
            'title' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('title', 'artefact.checklist'),
                'size' => 80,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'description' => array(
                'type'  => 'wysiwyg',
                'rows' => 3,
                'cols' => 70,
                'resizable' => true,
                'defaultvalue' => null,
                'title' => get_string('description', 'artefact.checklist'),
            ),
            'motivation' => array(
                'type'  => 'wysiwyg',
                'rows' => 3,
                'cols' => 70,
                'resizable' => true,
                'defaultvalue' => null,
                'title' => get_string('motivation', 'artefact.checklist'),
            ),
			/*
            'public' => array(
                'type'  => 'radio',
            	'options' => array(
                	0 => get_string('no'),
                	1 => get_string('yes'),
            	),
            	'defaultvalue' => 0,
            	'rules' => array(
                	'required' => true
            	),
            	'separator' => ' &nbsp; ',
                'title' => get_string('publiclist', 'artefact.checklist'),
                'description' => get_string('publiclistdesc','artefact.checklist'),
            ),
			*/
            'public' => array(
                'type'  => 'hidden',
            	'value' => 0,
            ),


        );

        if (!empty($checklist)) {
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $checklist->get($k);
            }
            $elements['checklist'] = array(
                'type' => 'hidden',
                'value' => $checklist->id,
            );
        }

        if (get_config('licensemetadata')) {
            $elements['license'] = license_form_el_basic($checklist);
            $elements['license_advanced'] = license_form_el_advanced($checklist);
        }

        return $elements;
    }


    public static function validate_select(Pieform $form, $values) {
        global $USER;
        if (!empty($values['checklist'])) {
            $id = (int) $values['checklist'];
            $artefact = new ArtefactTypeChecklist($id);
			if (!$artefact->get('public')) {
                $form->set_error('submit', get_string('canteditdontownchecklist', 'artefact.checklist'));
            }
        }
    }

	/**
	 * vew artefact list
	 *
	 *
	 */
    public static function submit_select(Pieform $form, $values) {
        global $USER, $SESSION;

        $new = true;
		$artefactnew = new ArtefactTypeChecklist();
        $artefactnew->set('owner', $USER->get('id'));
    	$new = true;


        $artefactnew->set('title', $values['title']);
        $artefactnew->set('description', $values['description']);
        $artefactnew->set('motivation', $values['motivation']);
        $artefactnew->set('public', 0); // la copie est privée

        if (get_config('licensemetadata')) {
            $artefactnew->set('license', $values['license']);
            $artefactnew->set('licensor', $values['licensor']);
            $artefactnew->set('licensorurl', $values['licensorurl']);
        }
        $artefactnew->commit();

		// recopier les items
		if (!empty($values['nbitems'])){
			for ($i=0; $i<$values['nbitems']; $i++){
				if (!empty($values['select'.$i])){
					// new item
            		$item = new ArtefactTypeItem();
            		$item->set('owner', $USER->get('id'));
            		$item->set('parent', $artefactnew->get('id'));
                    $item->set('title', $values['title'.$i]);
        			$item->set('description', $values['description'.$i]);
        			$item->set('code', $values['code'.$i]);
        			$item->set('scale', $values['scale'.$i]);
        			$item->set('valueindex', $values['valueindex'.$i]);

        			if (get_config('licensemetadata')) {
            			$item->set('license', $values['license']);
            			$item->set('licensor', $values['licensor']);
            			$item->set('licensorurl', $values['licensorurl']);
        			}
        			$item->commit();
				}
			}
		}
        $SESSION->add_ok_msg(get_string('checklistsavedsuccessfully', 'artefact.checklist'));
        redirect('/artefact/checklist/index.php?id='.$artefactnew->get('id'));
    }



 	/******************  Export lists ******************************************************************/
    public static function get_form_export($checklist=null, $items=null) {
        require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('checklist'), 'get_exportform_elements', $checklist);

		$elementitems = array();
        $i = 0;
		if (!empty($items['data'])){
			foreach ($items['data'] as $item){
				// formatting scale display
				// valueindex formatting
                $scalestr=scale_display($item->scale, $item->valueindex);

                $elementitems['help'.$i] = array(
                	'type' => 'html',
                	'value' => '<b>'.strip_tags($item->title).'</b> :: '.$scalestr,
           		);

                $elementitems['select'.$i] = array(
                	'type' => 'checkbox',
                	'title' => $item->code,
                    'defaultvalue' => $item->id,
                	'description' => strip_tags($item->description),
           		);
				$elements['itemid'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->id,
           		);

                $i++;
			}
		}

		$elements['nbitems'] = array(
           	'type' => 'hidden',
           	'value' => $i,
        );

		$elements['resetitems'] = array(
                'type'  => 'radio',
	            'options' => array(
    	           	0 => get_string('no'),
        	       	1 => get_string('yes'),
            	),
            	'defaultvalue' => 1,
	            'separator' => ' &nbsp; ',
    	        'title' => get_string('resetlist', 'artefact.checklist'),
        	    'description' => get_string('resetlistdesc','artefact.checklist'),
		);

        $elements['optionnal'] = array(
	            'type' => 'fieldset',
    	        'name' => 'items',
				'title' => 'exportlist',
        	    'collapsible' => true,
            	'collapsed' => true,
	            'legend' => get_string('selectinglist','artefact.checklist'),
                'elements' => $elementitems,
  	    );
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('saveexportlist','artefact.checklist'), get_string('exportdonecancel','artefact.checklist')),
            'goto' => get_config('wwwroot') . 'artefact/checklist/index.php',
        );

		// DEBUG
        // print_object($elements);
		// exit;

        $form = array(
            'name' => 'export',
		    'method' => 'post',
            'plugintype' => 'artefact',
            'pluginname' => 'checklist',
            'action' => '',
            'validatecallback' => array(generate_artefact_class_name('checklist'),'validate'),
            'successcallback' => array(generate_artefact_class_name('checklist'),'submit_export'),
            'elements' => $elements,
        );

        //print_object($form);
		//exit;
        return pieform($form);
    }

    /**
    * Gets the new/edit fields for the checklist pieform
    *
    */
    public static function get_exportform_elements($checklist) {
        $elements = array(
            'id' => array(
                'type' => 'hidden',
                'value' => $checklist->id,
            ),

            'public' => array(
                'type'  => 'hidden',
            	'value' => 0,             // by default exported lists are not public at loading
            ),
            'title' => array(
                'type' => 'text',
                'defaultvalue' => $checklist->title,
                'title' => get_string('title', 'artefact.checklist'),
                'size' => 60,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'description' => array(
                'type'  => 'wysiwyg',
                'rows' => 2,
                'cols' => 50,
                'resizable' => true,
                'defaultvalue' => $checklist->description,
                'title' => get_string('description', 'artefact.checklist'),
            ),
            'motivation' => array(
                'type'  => 'wysiwyg',
                'rows' => 2,
                'cols' => 50,
                'resizable' => true,
                'defaultvalue' => $checklist->motivation,
                'title' => get_string('motivation', 'artefact.checklist'),
            ),
        );

        if (!empty($checklist)) {
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $checklist->get($k);
            }
            $elements['checklist'] = array(
                'type' => 'hidden',
                'value' => $checklist->id,
            );
        }

        if (get_config('licensemetadata')) {
            $elements['license'] = license_form_el_basic($checklist);
            $elements['license_advanced'] = license_form_el_advanced($checklist);
        }

        return $elements;
    }


	/**
	 * export artefact & items list
	 *
	 *
	 */
    public static function submit_export(Pieform $form, $values) {
    	global $USER, $SESSION;

	    if (empty($values['id'])){
    		redirect(get_config('wwwroot') . 'artefact/checklist/index.php');
		}

        $new = true;

        $exportid = $values['id'];
        $exporttitle = $values['title'];
        $exportdescription = $values['description'];
        $exportmotivation = $values['motivation'];
        $exportpublic = $values['public'];
        if (get_config('licensemetadata')) {
    		$license = $values['license'];
    		$licensor = $values['licensor'];
    		$licensorurl = $values['licensorurl'];
		}

        $exportlicense = '';
        $exportlicensor = '';
        $exportlicensorurl = '';
        if (get_config('licensemetadata')) {
            $exportlicense = $values['license'];
            $exportlicensor = $values['licensor'];
            $exportlicensorurl = $values['licensorurl'];
        }

		if (!empty($values['resetitems'])){
            $resetitems=1;
		}
		else{
            $resetitems=0;
		}

 		// checked items
		$exportitemsids='';
		if (!empty($values['nbitems'])){
			for ($i=0; $i<$values['nbitems']; $i++){
				if (!empty($values['select'.$i])){
                    $exportitemsids.=$values['itemid'.$i];
					if ($i<$values['nbitems']-1){
                        $exportitemsids.=',';
					}
				}
			}
		}
		//exit;
        $SESSION->add_ok_msg(get_string('checklistsavedsuccessfully', 'artefact.checklist'));
        redirect(get_config('wwwroot') . 'artefact/checklist/exportxml.php?id='.$exportid.'&title='.urlencode($exporttitle).'&description='.urlencode($exportdescription).'&motivation='.urlencode($exportmotivation).'&public='.$exportpublic.'&resetitems='.$resetitems.'&itemsids='.$exportitemsids.'&license='.urlencode($exportlicense).'&licensor='.urlencode($exportlicensor).'&licensorurl='.urlencode($exportlicensorurl));
    }

     /**
     * Builds the public checklists list table
     *
     * @param checklists (reference)
     */
    public static function build_items_scalevalueindex_html(&$items) {
        $smarty = smarty_core();
        $smarty->assign_by_ref('items', $items);
        $items['tablerows'] = $smarty->fetch('artefact:checklist:itemseditvalueindex.tpl');

        $pagination = build_pagination(array(
            'id' => 'itemlist_pagination',
            'class' => 'center',
            'url' => get_config('wwwroot') . 'artefact/checklist/checklist.php?id='.$items['id'],
            //'jsonscript' => 'artefact/checklist/items.json.php',
            'datatable' => 'itemslist',
            'count' => $items['count'],
            'limit' => $items['limit'],
            'offset' => $items['offset'],
            'firsttext' => '',
            'previoustext' => '',
            'nexttext' => '',
            'lasttext' => '',
            'numbersincludefirstlast' => false,
            'resultcounttextsingular' => get_string('item', 'artefact.checklist'),
            'resultcounttextplural' => get_string('items', 'artefact.checklist'),
        ));
        $items['pagination'] = $pagination['html'];
        $items['pagination_js'] = $pagination['javascript'];
    }

  	/******************  Set scales valueindex for list of Items ******************************************************************/



	/**
    * Gets the new/edit checklist pieform
    *
    */
    public static function get_form_valuesindex($checklist=null, $items=null) {
        require_once(get_config('libroot') . 'pieforms/pieform.php');
        // require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('checklist'), 'get_scaleform_elements', $checklist);
		//echo "ITEMS<br />\n";
        //print_object($elements);
		//exit;
		if (!empty($items['data'])){
			$i = 0;
			foreach ($items['data'] as $item){
                //echo "<br />ITEM<br />\n";
				//print_object($item);
                //$name="'select_".$item->id."'";

                $elements['htmltitle'.$i] = array(
					'title' => get_string('title', 'artefact.checklist'),
                	'type' => 'html',
                	'value' => $item->title,
           		);

                 $elements['htmldescription'.$i] = array(
					'title' => get_string('description', 'artefact.checklist'),
                	'type' => 'html',
                	'value' => $item->description,
           		);

               $elements['htmlcode'.$i] = array(
					'title' => get_string('code', 'artefact.checklist'),
                	'type' => 'html',
                	'value' => '<b>'.$item->code.'</b>',
           		);
 /*
                $elements['select'.$i] = array(
                	'type' => 'checkbox',
                	'defaultvalue' => $item->id,
                	'title' => get_string('selected', 'artefact.checklist'),
                	'description' => get_string('descselected', 'artefact.checklist'),
           		);
*/
                $elements['select'.$i] = array(
                	'type' => 'hidden',
                	'value' => 1,
           		);

				$elements['iditem'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->id,
           		);
				$elements['scale'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->scale,
           		);
				$elements['valueindex'.$i] = array(
                	'type' => 'hidden',
                	'value' => $item->valueindex,
           		);

				// Scale
				if (!empty($item->scale)){
					// DEBUG
					//echo "<br />lib.php :: 1155 :: Scale ".$item->scale."\n";
					if (preg_match("/,/", $item->scale)){

						$options = array();
	    	        	if ($tabscale = explode(',', $item->scale)){
							//print_object($tabscale);

							$max = count($tabscale);
                            //echo "<br />lib.php :: 1155 :: Max ".$max."\n";
							//exit;
							$index=0;
							$nbindex=0;
							while ($index<$max){
								if (!empty($tabscale[$index])){
									if ($index==$item->valueindex){
                                      	$options[$nbindex] = $tabscale[$index];
                                        $nbindex++;
									}
									else{
          								$options[$nbindex] = $tabscale[$index];
                                        $nbindex++;
									}
								}
    	   						$index++;
							}
				            $elements['scale'.$i] = array(
								'type' => 'radio',
								'title' => get_string('scale', 'artefact.checklist'),
								'name' => 'scaleselect'.$i,
								'id' => 'scaleselect'.$i,
								'defaultvalue' => $item->valueindex,
								'options' => $options,
							);

	        				$elements['nbindex'.$i] = array(
    	       					'type' => 'hidden',
               					'value' => $nbindex,
        					);

						}
					}
					else{
    	        	    $elements['scale0'] = array(
								'type' => 'radio',
								'title' => get_string('scale', 'artefact.checklist'),
								'name' => 'scaleselect0',
								'id' => 'scaleselect0',
								'defaultvalue' => $item->valueindex,
								'options' => array(
                                    $item->scale,
								),
						);
	        			$elements['nbindex'.$i] = array(
    	       				'type' => 'hidden',
               				'value' => 1,
        				);

					}
				}
    	        $i++;
			}


	        $elements['nbitems'] = array(
    	       	'type' => 'hidden',
               	'value' => $i,
        	);
		}

        //print_object($elements);
		//exit;
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('savechecklist','artefact.checklist'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/checklist/index.php',
        );

        $form = array(
            'name' => 'validationlist',
            'plugintype' => 'artefact',
            'pluginname' => 'item',
            'validatecallback' => array(generate_artefact_class_name('checklist'),'validate_selectscale'),
            'successcallback' => array(generate_artefact_class_name('checklist'),'submit_selectscale'),
            'elements' => $elements,
        );
        //print_object($form);
		//exit;
        return pieform($form);
    }


    /**
    * Gets the new/edit fields for the checklist pieform
    *
    */
    public static function get_scaleform_elements($checklist) {
        $elements = array(
            'title' => array(
				'title' => get_string('title','artefact.checklist'),
                'type' => 'html',
                'value' => $checklist->title,
            ),
            'description' => array(
				'title' => get_string('description','artefact.checklist'),
                'type' => 'html',
                'value' => $checklist->description,
            ),
            'motivation' => array(
                'title' => get_string('motivation','artefact.checklist'),
				'type' => 'html',
                'value' => $checklist->motivation,
            ),
            'public' => array(
                'type'  => 'hidden',
            	'value' => 0,
            ),


        );

        if (!empty($checklist)) {
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $checklist->get($k);
            }
            $elements['checklist'] = array(
                'type' => 'hidden',
                'value' => $checklist->id,
            );
        }

        if (get_config('licensemetadata')) {
            $elements['license'] = license_form_el_basic($checklist);
            $elements['license_advanced'] = license_form_el_advanced($checklist);
        }

        return $elements;
    }


    public static function validate_selectscale(Pieform $form, $values) {
        global $USER;
        if (!empty($values['checklist'])) {
            $id = (int) $values['checklist'];
            $artefact = new ArtefactTypeChecklist($id);
            if (!$USER->can_edit_artefact($artefact)) {
                $form->set_error('submit', get_string('canteditdontownchecklist', 'artefact.checklist'));
            }
        }
    }

	/**
	 * set artefact item valueindex
	 *
	 *
    */

    public static function submit_selectscale(Pieform $form, $values) {
        global $USER, $SESSION;

		//print_object($values);
		//exit;
    	if (!empty($values['checklist'])) {
			$checklist = (int) $values['checklist'];
            $artefact = new ArtefactTypeChecklist($checklist);

			 // recopier les items
			if (!empty($values['nbitems'])){
				for ($i=0; $i<$values['nbitems']; $i++){
					if (!empty($values['select'.$i])){
						// set new item valueindex
            			$item = new ArtefactTypeItem($values['iditem'.$i]);
						//echo "<br />DEBUG :: lib.php :: 1348<br />\n";
						//print_object($item);
						//exit;
                        $valueindex=$values['scaleselect'.$i];
						// artefact
      					$item->set('valueindex', $valueindex);
       					$item->commit();
					}
				}
                $i++;
			}
 		}

        $SESSION->add_ok_msg(get_string('checklistsavedsuccessfully', 'artefact.checklist'));
        redirect('/artefact/checklist/checklist.php?id='.$artefact->get('id'));
    }



    /**
     * Renders a checklist.
     *
     * @param  array  Options for rendering
     * @return array  A two key array, 'html' and 'javascript'.
     */

	public function render_self($options) {
        $this->add_to_render_path($options);

        $limit = !isset($options['limit']) ? 10 : (int) $options['limit'];
        $offset = isset($options['offset']) ? intval($options['offset']) : 0;

        $items = ArtefactTypeItem::get_items($this->id, $offset, $limit);

        $template = 'artefact:checklist:itemrows.tpl';

        $baseurl = get_config('wwwroot') . 'view/artefact.php?artefact=' . $this->id;
        if (!empty($options['viewid'])) {
            $baseurl .= '&view=' . $options['viewid'];
        }

        $pagination = array(
            'baseurl' => $baseurl,
            'id' => 'item_pagination',
            'datatable' => 'itemtable',
            'jsonscript' => 'artefact/checklist/viewitems.json.php',
        );

        ArtefactTypeItem::render_items($items, $template, $options, $pagination);

        $smarty = smarty_core();
        $smarty->assign_by_ref('items', $items);
        if (isset($options['viewid'])) {
            $smarty->assign('artefacttitle', '<a href="' . $baseurl . '">' . hsc($this->get('title')) . '</a>');
        }
        else {
            $smarty->assign('artefacttitle', hsc($this->get('title')));
        }

        $smarty->assign('checklist', $this);

        if (!empty($options['details']) and get_config('licensemetadata')) {
            $smarty->assign('license', render_license($this));
        }
        else {
            $smarty->assign('license', false);
        }
        $smarty->assign('owner', $this->get('owner'));

		$smarty->assign('tags', $this->get('tags'));

        return array('html' => $smarty->fetch('artefact:checklist:viewchecklist.tpl'), 'javascript' => '');
    }

    public static function is_countable_progressbar() {
        return true;
    }

}

/**
 * Class ArtefactTypeItem
 *  #############################################################################################################################
 *  Item is a Tile, Code, Description, Scale
 *
 *  #############################################################################################################################
 */

class ArtefactTypeItem extends ArtefactType {

 	// nouvelles proprietes par rapport a la classe artefact standart
    protected $code = 0;
    protected $scale = '';
    protected $valueindex = 0;
    protected $optionitem = 0;
    protected $displayorder = 0;

    /**
     * We override the constructor to fetch the extra data.
     *
     * @param integer
     * @param object
     */
    public function __construct($id = 0, $data = null) {
        parent::__construct($id, $data);

        if ($this->id) {
            if ($pdata = get_record('artefact_checklist_item', 'artefact', $this->id)) {
                foreach($pdata as $name => $value) {
                    if (property_exists($this, $name)) {
                        $this->{$name} = $value;
                    }
                }
            }
            else {
                // This should never happen unless the user is playing around with task IDs in the location bar or similar
                throw new ArtefactNotFoundException(get_string('itemdoesnotexist', 'artefact.checklist'));
            }
        }
    }


    public static function get_links($id) {
        return array(
            '_default' => get_config('wwwroot') . 'artefact/checklist/item.php?id=' . $id,
        );
    }

    public static function get_icon($options=null) {
        global $THEME;
        return $THEME->get_url('images/items.png', false, 'artefact/checklist');
    }

    public static function is_singular() {
        return false;
    }

	public function render_self($options) {
		return get_string('items', 'artefact.checklist');
	}


    /**
     * This method extends ArtefactType::commit() by adding additional data
     * into the artefact_checklist_item table.
     *
     */
    public function commit() {
       if (empty($this->dirty)) {
            return;
        }
         // Return whether or not the commit worked
        $success = false;

        db_begin();
        $new = empty($this->id);

        parent::commit();

        $this->dirty = true;

		$data = (object)array(
			'code'      	=> $this->get('code'),
			'artefact'  	=> $this->get('id'),
            'scale' 		=> $this->get('scale'),
            'valueindex' 	=> $this->get('valueindex'),
            'optionitem' 	=> $this->get('optionitem'),
            'displayorder'  => $this->get('displayorder'),
        );

        //echo "$data->scale<br />\n";
		// scale verification
        if (!empty($data->scale) && preg_match("/;/",$data->scale)){
			$data->scale = str_replace(';',',',$data->scale);
		}
		// valueindex verification
		$s='';
		if (!empty($data->scale) && preg_match("/,/",$data->scale)){
            //echo "$data->scale<br />\n";
            if ($tabscale = explode(',',$data->scale)){
				//print_r($tabscale);
				$max = count($tabscale);
				//echo $max.'<br />';
				$n=0;
				foreach($tabscale as $val){
                    if ($val){
						//echo $val . '<br />';
						$val=trim($val);
						$s.=$val.',';
						$n++;
					}
				}

                $data->scale=$s;

                //echo "$data->scale<br />\n";
				//exit;
				if ($data->valueindex >= $n){
                    $data->valueindex=$n-1;
				}
			}
            if ($data->valueindex < 0){
            	$data->valueindex = 0;
			}
		}
		else{
            $data->valueindex = 0;
		}


        if ($new) {
            $success = insert_record('artefact_checklist_item', $data);
        }
        else {
            $success = update_record('artefact_checklist_item', $data, 'artefact');
        }


        // We want to get all chekclist that contain this item. That is currently:
        // 1) All item blocktypes with this item in it
        // 2) All chechklist blocktypes with this item's  in it
        //
        // With these, we tell them to rebuild what artefacts they have in them,
        // since the item content could have changed and now have links to
        // different artefacts in it
        $blockinstanceids = (array)get_column_sql('SELECT block
            FROM {view_artefact}
            WHERE artefact = ?
            OR artefact = ?', array($this->get('id'), $this->get('parent')));
        if ($blockinstanceids) {
            require_once(get_config('docroot') . 'blocktype/lib.php');
            foreach ($blockinstanceids as $id) {
                $instance = new BlockInstance($id);
                $instance->rebuild_artefact_list();
            }
        }

        db_commit();

        $this->dirty = $success ? false : true;

        return $success;
    }


    /**
     * This function extends ArtefactType::delete() by also deleting anything
     * that's in item.
     */
    public function delete() {
        if (empty($this->id)) {
            return;
        }

        db_begin();
        delete_records('artefact_checklist_item', 'artefact', $this->id);

        parent::delete();
        db_commit();
    }

    public static function bulk_delete($artefactids) {
        if (empty($artefactids)) {
            return;
        }

        $idstr = join(',', array_map('intval', $artefactids));

        db_begin();
        delete_records_select('artefact_checklist_item', 'artefact IN (' . $idstr . ')');
        parent::bulk_delete($artefactids);
        db_commit();
    }

    /**
    * Gets the new/edit items pieform
    *
    */
    public static function get_form($parent, $item=null) {
        require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('item'), 'get_itemform_elements', $parent, $item);
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('saveitem','artefact.checklist'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/checklist/checklist.php?id=' . $parent,
        );
        $itemform = array(
            'name' => empty($item) ? 'additems' : 'edititem',
            'plugintype' => 'artefact',
            'pluginname' => 'item',
            'validatecallback' => array(generate_artefact_class_name('item'),'validate'),
            'successcallback' => array(generate_artefact_class_name('item'),'submit'),
            'elements' => $elements,
        );

        return pieform($itemform);
    }

    /**
    * Gets the new/edit fields for the items pieform
    *
    */
    public static function get_itemform_elements($parent, $item=null) {

		// position in the list
  		if (!empty($item)) {
			$position = $item->displayorder;
		}
		else{
            $position = -1;
		}

        $elements = array(
            'title' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('title', 'artefact.checklist'),
                'description' => get_string('titleitemdesc','artefact.checklist'),
                'size' => 80,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'code' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('code', 'artefact.checklist'),
                'description' => get_string('codeitemdesc','artefact.checklist'),
                'size' => 30,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'description' => array(
                'type'  => 'wysiwyg',
                'rows' => 5,
                'cols' => 70,
                'resizable' => false,
                'defaultvalue' => null,
                'title' => get_string('description', 'artefact.checklist'),
            ),
            'scale' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('scale', 'artefact.checklist'),
                'description' => get_string('scaledesc', 'artefact.checklist'),
                'size' => 80,
            ),
            'valueindex' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('valueindex', 'artefact.checklist'),
                'description' => get_string('valueindexdesc','artefact.checklist'),
                'size' => 10,
            ),
            'optionitem' => array(
                'type' => 'radio',
                'title' => get_string('optionitem', 'artefact.checklist'),
				'name' => 'optionitem',
				'id' => 'optionitem',
                'defaultvalue' => 0,
                'description' => get_string('optionitemdesc','artefact.checklist'),
                'rules' => array(
                    'required' => true,
                ),
				'options' => array(     // 0 - normal; 1 - optional; 2 - heading
					0,
					1,
					2,
				),
            ),
			/*
            'displayorder' => array(
                'type' => 'text',
                'defaultvalue' => 0,
                'title' => get_string('displayorder', 'artefact.checklist'),
                'description' => get_string('displayorderdesc','artefact.checklist'),
                'size' => 10,
            ),
			*/
            'displayorder' => array(
            	'type' => 'hidden',
            	'value' => $position,
        	),

        );

        if (!empty($item)) {
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $item->get($k);
            }
            $elements['item'] = array(
                'type' => 'hidden',
                'value' => $item->id,
            );
        }
        if (get_config('licensemetadata')) {
            $elements['license'] = license_form_el_basic($item);
            $elements['license_advanced'] = license_form_el_advanced($item);
        }

        $elements['parent'] = array(
            'type' => 'hidden',
            'value' => $parent,
        );

        return $elements;
    }

    public static function validate(Pieform $form, $values) {
        global $USER;
        if (!empty($values['item'])) {
            $id = (int) $values['item'];
            $artefact = new ArtefactTypeItem($id);
            if (!$USER->can_edit_artefact($artefact)) {
                $form->set_error('submit', get_string('canteditdontownitem', 'artefact.checklist'));
            }
        }
    }


    public static function submit(Pieform $form, $values) {
        global $USER, $SESSION;

        if (!empty($values['item'])) {
            $id = (int) $values['item'];
            $artefact = new ArtefactTypeItem($id);
        }
        else {
            $artefact = new ArtefactTypeItem();
            $artefact->set('owner', $USER->get('id'));
            $artefact->set('parent', $values['parent']);
        }

        $artefact->set('title', $values['title']);
        $artefact->set('description', $values['description']);
        $artefact->set('code', $values['code']);
        $artefact->set('scale', $values['scale']);
        $artefact->set('valueindex', $values['valueindex']);
        $artefact->set('optionitem', $values['optionitem']);

  		// set position of item in list
        if ($values['displayorder'] < 0){
            // how many items ?
			if ($rec = count_nb_items($values['parent'])){
				$artefact->set('displayorder', $rec['count']);
			}
			else{
                $artefact->set('displayorder', 0);
			}
		}
		else{
            $artefact->set('displayorder', $values['displayorder']);
		}

        if (get_config('licensemetadata')) {
            $artefact->set('license', $values['license']);
            $artefact->set('licensor', $values['licensor']);
            $artefact->set('licensorurl', $values['licensorurl']);
        }
        $artefact->commit();

        $SESSION->add_ok_msg(get_string('itemsavedsuccessfully', 'artefact.checklist'));

        redirect('/artefact/checklist/checklist.php?id='.$values['parent']);
    }


   /**
     * This function returns a list of the current checklist items.
     *
     * @param limit how many items to display per page
     * @param offset current page to display
     * @return array (count: integer, data: array)
     */
    public static function get_items($checklist, $offset=0, $limit=10, $order='ASC') {
        $datenow = time(); // time now to use for formatting items by completion

        ($results = get_records_sql_array("
            SELECT a.id, at.artefact AS item, at.code, at.scale, at.valueindex, at.optionitem, at.displayorder,
                a.title, a.description, a.parent, a.mtime
                FROM {artefact} a
            JOIN {artefact_checklist_item} at ON at.artefact = a.id
            WHERE a.artefacttype = 'item' AND a.parent = ?
            ORDER BY at.displayorder ".$order.", at.code ".$order, array($checklist), $offset, $limit))
            || ($results = array());

//            ORDER BY at.code ".$order, array($checklist), $offset, $limit))
//            || ($results = array());
//

        // format the data and setup completed for display
        if (!empty($results)) {
            foreach ($results as $result) {
                $result->description = '<p>' . preg_replace('/\n\n/','</p><p>', $result->description) . '</p>';
				// formatting scale display
				// valueindex formatting
				if (!empty($result->scale) && preg_match("/,/",$result->scale)){
            		if ($tabscale = explode(',',$result->scale)){
						$max = count($tabscale);
						$i=0;
						$s='';

						while ($i<$max){
							if (!empty($tabscale[$i])){
								if ($i==$result->valueindex){
                                	$s.='<span class="surligne"><b>'.trim($tabscale[$i]).'</b></span> ';
									//$s.='<span background-color:yellow; font-weight:bold; color:navy><b>'.trim($tabscale[$i]).'</b></span> ';
								}
								else{
                                	$s.='<i>'.trim($tabscale[$i]).'</i> ';
								}
							}
							$i++;
						}
                		$result->scale=$s;
					}
				}
				else{
                    $result->scale = '<span class="surligne"><b>'.trim($result->scale).'</b></span> ';
				}

            }
        }

        $result = array(
            'count'  => count_records('artefact', 'artefacttype', 'item', 'parent', $checklist),
            'data'   => $results,
            'offset' => $offset,
            'limit'  => $limit,
            'id'     => $checklist,
        );

        return $result;
    }

   /**
     * This function returns a raw list of items.
     *
     * @param chkid : artefact item id
     * @return array (count: integer, data: array)
     */
    public static function get_all_items_raw($chkid) {
        ($results = get_records_sql_array("
            SELECT a.id, at.artefact AS item, at.code, at.scale, at.valueindex, at.optionitem, at.displayorder,
                a.title, a.description, a.parent, a.mtime
                FROM {artefact} a
            JOIN {artefact_checklist_item} at ON at.artefact = a.id
            WHERE a.artefacttype = 'item' AND a.parent = ?
            ORDER BY at.displayorder ASC", array($chkid)))
            || ($results = array());

        $n=count_records('artefact', 'artefacttype', 'item', 'parent', $chkid);
        $result = array(
            'count'  => $n,
            'data'   => $results,
            'offset' => 0,
            'limit'  => $n,
            'id'     => $chkid,
        );

        return $result;
    }



   /**
     * This function returns an item artefact .
     * @input parent checklist id
     * @input item id
     *
     * @return Object
     */
    public static function get_item($itemid) {
        global $USER;

        ($item = get_record_sql("
							SELECT a.*, ai.*
							FROM {artefact} a
            				JOIN {artefact_checklist_item} ai ON ai.artefact = a.id
                            WHERE a.id = ? AND a.artefacttype = 'item'
							", array($itemid)));
        return $item;
    }

    /**
     * Builds the items list table for current checklist
     *
     * @param items (reference)
     */
    public function build_items_list_html(&$items) {
        $smarty = smarty_core();
        $smarty->assign_by_ref('items', $items);
        $items['tablerows'] = $smarty->fetch('artefact:checklist:itemslist.tpl');

        $pagination = build_pagination(array(
            'id' => 'itemlist_pagination',
            'class' => 'center',
            'url' => get_config('wwwroot') . 'artefact/checklist/checklist.php?id='.$items['id'],
            //'jsonscript' => 'artefact/checklist/items.json.php',
            'datatable' => 'itemslist',
            'count' => $items['count'],
            'limit' => $items['limit'],
            'offset' => $items['offset'],
            'firsttext' => '',
            'previoustext' => '',
            'nexttext' => '',
            'lasttext' => '',
            'numbersincludefirstlast' => false,
            'resultcounttextsingular' => get_string('item', 'artefact.checklist'),
            'resultcounttextplural' => get_string('items', 'artefact.checklist'),
        ));
        $items['pagination'] = $pagination['html'];
        $items['pagination_js'] = $pagination['javascript'];
    }


    /**
     * move items up or down
     * @param checklistid
     * @param itemid
     * @param direction
     */
	 public static function move_item($checklistid, $itemid, $direction=0){
		if (!empty($checklistid)){
			$nbitems = count_records('artefact', 'artefacttype', 'item', 'parent', $checklistid);

        	$item1 = get_record_sql("SELECT a.id, ai.displayorder
							FROM {artefact} a
            				JOIN {artefact_checklist_item} ai ON ai.artefact = a.id
                            WHERE a.parent = ? AND a.id = ? AND a.artefacttype = 'item'", array($checklistid, $itemid));
			if (!empty($item1)){
            	$pos1=$item1->displayorder;

				if (!empty($direction)){ // Down
                	if ($pos1 == $nbitems-1){
                        roll_items($checklistid, $nbitems, 1);
						return true;
					}
					else{
						$pos2=($item1->displayorder<$nbitems-1) ? $item1->displayorder+1 : $nbitems-1;
					}
				}
				else{  // Up
					if ($pos1 == 0){
						roll_items($checklistid, $nbitems, 0);
                        return true;
					}
					else{
                		$pos2=($item1->displayorder-1>0)? $item1->displayorder-1 : 0;
					}
				}

        		$item2 = get_record_sql("SELECT a.id
							FROM {artefact} a
            				JOIN {artefact_checklist_item} ai ON ai.artefact = a.id
                            WHERE a.parent = ? AND ai.displayorder = ? AND a.artefacttype = 'item'
							", array($checklistid, $pos2));
				if (!empty($item2)){
					set_field('artefact_checklist_item', 'displayorder', $pos1, 'artefact', $item2->id);
				}
                set_field('artefact_checklist_item', 'displayorder', $pos2, 'artefact', $item1->id);
			}
		}
	}

     // @TODO: make blocktype use this too
    public function render_items(&$items, $template, $options, $pagination) {
        $smarty = smarty_core();
 		$smarty->assign_by_ref('items', $items);
        $smarty->assign_by_ref('options', $options);

        $items['tablerows'] = $smarty->fetch($template);

        if ($items['limit'] && $pagination) {
            $pagination = build_pagination(array(
                'id' => $pagination['id'],
                'class' => 'center',
                'datatable' => $pagination['datatable'],
                'url' => $pagination['baseurl'],
                'jsonscript' => $pagination['jsonscript'],
                'count' => $items['count'],
                'limit' => $items['limit'],
                'offset' => $items['offset'],
                'numbersincludefirstlast' => false,
                'resultcounttextsingular' => get_string('item', 'artefact.checklist'),
                'resultcounttextplural' => get_string('items', 'artefact.checklist'),
            ));
            $items['pagination'] = $pagination['html'];
            $items['pagination_js'] = $pagination['javascript'];
        }
    }

}

	/**
     * This function returns the number of items from a parent checklist.
     *
     * @param checklist : parent artefact id
     * @return array  (count: integer, data: array)
     */
    function count_nb_items($checklist) {

        ($results = get_records_sql_array("
            SELECT COUNT(a.id) AS nbitems
			FROM {artefact} a
            	JOIN {artefact_checklist_item} at ON at.artefact = a.id
            WHERE a.artefacttype = 'item' AND a.parent = ?", array($checklist)))
            || ($results = array());

        $result = array(
            'count'   => $results[0]->nbitems,
            'id'     => $checklist,
        );

		//print_object($result);
		//exit;
        return $result;
    }


    /**
    * Format scale display
    *
    */
    function scale_display($scale, $valueindex=0) {
        $scalestr='';
		if (!empty($scale)){
			if (preg_match("/,/", $scale)){
            	if ($tabscale = explode(',', $scale)){
					$max = count($tabscale);
					$index=0;
					$s='';
					while ($index<$max){
						if (!empty($tabscale[$index])){
							if ($index==$valueindex){
                	        	$s.='<span class="surligne"><b>'.trim($tabscale[$index]).'</b></span> ';
							}
							else{
    	                		$s.='<i>'.trim($tabscale[$index]).'</i> ';
							}
                        }
    	   				$index++;
					}
            		$scalestr=$s;
				}
			}
			else{
                $scalestr = '<span class="surligne"><b>'.trim($scale).'</b></span> ';
                //$scalestr = '<span background-color:yellow; font-weight:bold; color:navy><b>'.trim($scale).'</b></span> ';
			}
		}
		return $scalestr;
	}

    /**
    * Export checklist + items
    *
    */
	function set_xml($data){
		$doc = new DOMDocument();
		$doc->version = '1.0';
		$doc->encoding = 'UTF-8';

        $docchk = $doc->createElement('checklist');
        $docchk->setAttribute('title', $data->title);
        $docchk->setAttribute('public', $data->public);

        $description = $doc->createCDATASection($data->description);
        $docdescription = $doc->createElement('description');
        $docdescription->appendChild($description);

		$docchk->appendChild($docdescription);

        $motivation = $doc->createCDATASection($data->motivation);
        $docmotivation = $doc->createElement('motivation');
        $docmotivation->appendChild($motivation);

		$docchk->appendChild($docmotivation);

        foreach ($data->items as $item) {
		    $docitem = $doc->createElement('item');
    		$docitem->setAttribute('title', $item->title);
        	$docitem->setAttribute('code', $item->code);
        	$docitem->setAttribute('scale', $item->scale);
        	$docitem->setAttribute('valueindex', $item->valueindex);
            $docitem->setAttribute('optionitem', $item->optionitem);
            $docitem->setAttribute('displayorder', $item->displayorder);
			$docchk->appendChild($docitem);
		}

		$doc->appendChild($docchk);
        //print_object($doc);
		//exit;

		$xml = $doc->saveXML();
		return($xml);
	}


    /**
     * This function sets the displayorder of items for a checklist.
     *
     * @param checklistid : parent artefact id
     * @param offset : position to move
	 * @param direction 0:up, 1:down
     * @return nothing
     */
     function roll_items($checklistid, $nitems, $direction=0){
        if (!empty($checklistid)){
			$items = get_records_sql_array("SELECT a.id
					FROM {artefact} a
            			JOIN {artefact_checklist_item} ai ON ai.artefact = a.id
                	WHERE a.parent = ? AND a.artefacttype = 'item'
					ORDER BY ai.displayorder ASC", array($checklistid) );

			if (!empty($items)){
				if (!empty($direction)){ // down
			    	$counter=0;
    	        	foreach ($items as $item) {
            	    	if ($counter<$nitems-1){
							$counter++;
		        		}
						else{
                        	$counter = 0;
						}
                    	set_field('artefact_checklist_item', 'displayorder', $counter, 'artefact', $item->id);
					}
				}
				else{
			    	$counter=0;
    	        	foreach ($items as $item) {
            	    	if ($counter==0){
							$pos=$nitems-1;
		        		}
						else{
                        	$pos=$counter-1;
						}
                        $counter++;
                    	set_field('artefact_checklist_item', 'displayorder', $pos, 'artefact', $item->id);
					}
				}
			}
		}
	}



   	/**
     * This function sets the displayorder of items for a checklist.
     *
     * @param checklistid : parent artefact id
     * @return nothing
     */
	function reset_list_displayorder($checklistid){
		$checklist = get_record_sql_array("SELECT id FROM {artefact}
			WHERE id=? AND artefacttype = 'checklist' ", array($checklistid));
		// Items
        if (!empty($checklist)){
			$items = get_records_sql_array("SELECT a.id
				FROM {artefact} a
            		JOIN {artefact_checklist_item} ai ON ai.artefact = a.id
                WHERE a.parent = ? AND a.artefacttype = 'item'
				ORDER BY ai.code ASC", array($checklist->id));
			if (!empty($items)){
	            $counter=0;
    	        foreach ($items as $item) {
        	    	set_field('artefact_checklist_item', 'displayorder', $counter, 'artefact', $item->id);
            	    $counter++;
		        }
			}
		}
	}

	/**
     * This function sets the displayorder of items for all checklists.
     *
     * @return nothing
     */
	function reset_alllists_displayorder(){
		$checklists = get_records_sql_array("SELECT id FROM {artefact} WHERE artefacttype = 'checklist' ", array());
		//print_object($checklists);
        if (!empty($checklists)) {
            foreach ($checklists as $checklist) {
				reset_list_displayorder($checklist->id);
			}
		}
	}


