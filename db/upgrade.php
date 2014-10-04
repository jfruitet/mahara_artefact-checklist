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

    return $status;
}

