<?php

defined('INTERNAL') || die();

function xmldb_artefact_checklist_upgrade($oldversion=0) {
    $status = true;

    if ($oldversion < 2014091800) {
    /// Define field value to be added to artefact_checklist_item
        $table = new XMLDBTable('artefact_checklist_item');
        $field = new XMLDBField('valueindex');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'scale');
    /// Launch add field evaluation
        $status = $status && add_field($table, $field);
    }

	if ($oldversion < 2014092300) {
    /// Define field value to be added to artefact_checklist_checklist
        $table = new XMLDBTable('artefact_checklist_checklist');
        $field = new XMLDBField('public');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'motivation');
    /// Launch add field evaluation
        $status = $status && add_field($table, $field);
    }

    if ($oldversion < 2014100602) {
    /// Define field optionitem to be added to artefact_checklist_item
        $table = new XMLDBTable('artefact_checklist_item');
        $field = new XMLDBField('optionitem');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'valueindex');
    /// Launch add field evaluation
        $status = $status && add_field($table, $field);

    /// Define field displayorder to be added to artefact_checklist_item
        $table = new XMLDBTable('artefact_checklist_item');
        $field = new XMLDBField('displayorder');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'optionitem');

    /// Launch add field evaluation
        $status = $status && add_field($table, $field);

		// reset displayorder
		if ($status){
			$checklists = get_records_sql_array("SELECT id FROM {artefact} WHERE artefacttype = 'checklist' ", array());
	        if (!empty($checklists)) {
    	        foreach ($checklists as $checklist) {
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
			}
		}
	}

    return $status;
}

