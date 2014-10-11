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
define('MENUITEM', 'content/checklist');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'checklist');
define('SECTION_PAGE', 'index');
define('CHECKLIST_SUBPAGE', 'import');

defined('INTERNAL') || die();
require_once(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform.php');
safe_require('artefact', 'checklist');

class ArtefactTypeImportChecklistXml extends ArtefactTypeChecklist {
    public static function is_singular() { return true;  }
    public static function get_form() {
        $importformxml = pieform(
		array(
            'name'        => 'importformxml',
            'plugintype'  => 'artefact',
            'successcallback' => 'import_submit_xml',
            'pluginname'  => 'checklist',
            'method'      => 'post',
        	'elements' => array(
				'optionnal' => array(
	            	'type' => 'fieldset',
	    	        'name' => 'xmlfield',
					'title' => 'importxml',
        		    'collapsible' => true,
            		'collapsed' => true,
	            	'legend' => get_string('descimportxml','artefact.checklist'),
                	'elements' => array(
        		        'filename' => array(
                	    	'type' => 'file',
                    		'title' => get_string('filename', 'artefact.checklist'),
	                    	'rules' => array('required' => true),
    	               	 'maxfilesize'  => get_max_upload_size(false),
        	        	),
            	    	'save' => array(
                	    	'type' => 'submit',
                    		'value' => get_string('import', 'artefact.checklist'),
						),
              	  	),
				),
           	 ),
        ));
        return $importformxml;
    }
}

/**
 * checklist + item load
 * @input doc  DOMDocument
 * @input owner  USER id
 * @return none
 **/
function create_checklist_xml ($doc, $owner) {
    $checklists = $doc->getElementsByTagName('checklist');
    $checklist = $checklists->item(0);

    $title = $checklist->getAttribute('title');
    $public = $checklist->getAttribute('public');

    $descrip = $doc->getElementsByTagName('description');
	foreach ($descrip as $desc) {
        if ($desc) {
			// echo $desc->nodeValue, PHP_EOL;
        	$cdatadescription = $desc->nodeValue;
			break;
		}
	}

    $motiv = $doc->getElementsByTagName('motivation');
	foreach ($motiv as $moti) {
        if ($moti) {
			//echo $moti->nodeValue, PHP_EOL;
        	$cdatamotivation = $moti->nodeValue;
			break;
		}
	}

	// Save artefact
	$data = new stdClass();
    $data->owner = $owner;
    $data->title = $title. ' '. get_string('imported', 'artefact.checklist', date("Y-m-d H:i:s"));
    $data->public = $public;
	$data->description = (string) $cdatamotivation;
    $data->motivation = (string) $cdatamotivation;

    $classname = 'ArtefactTypeChecklist';
    $ac = new $classname(0, $data);
    $ac->commit();

	// Items
	$items = $doc->getElementsByTagName('item');
	foreach ($items as $item) {
        if ($item) {
 			if ($item->hasAttributes()){
				$datai= array();
        		foreach ($item->attributes as $attr){
            		$datai[$attr->nodeName] = $attr->nodeValue;
        		}
                $datai['artefact'] = $ac->get('id');
                $datai['parent'] = $ac->get('id');
                $datai['owner'] = $owner;
   				// DEBUG
				// print_object($datai);
    			$classname = 'ArtefactTypeItem';
    			$ai = new $classname(0, $datai);
    			$ai->commit();
			}
		}
	}
}

function import_submit_xml (Pieform $form, $values) {
    global $USER, $SESSION;
    $filename = $values['filename']['tmp_name'];
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $x = libxml_disable_entity_loader(false);
    if ($doc->load($filename)) {
        create_checklist_xml($doc, $USER->get('id'));
    }
    else {
        $SESSION->add_error_msg(get_string('loadxmlfailed', 'artefact.checklist'));
    }
    $goto = get_config('wwwroot') . '/artefact/checklist/index.php';
    redirect($goto);
}


/**
 * Import a Moodle Outcome csv file format
 *
 */

class ArtefactTypeImportOutcomesMoodle extends ArtefactTypeChecklist {
    public static function is_singular() { return true;  }
    public static function get_form() {
        $importformcsv = pieform(
		array(
            'name'        => 'importformcsv',
            'plugintype'  => 'artefact',
            'successcallback' => 'import_submit_csv',
            'pluginname'  => 'checklist',
            'method'      => 'post',
            'elements'    => array(
				'optionnal' => array(
	            	'type' => 'fieldset',
	    	        'name' => 'csvfield',
					'title' => 'importcsv',
        		    'collapsible' => true,
            		'collapsed' => true,
	            	'legend' => get_string('descimportcsv','artefact.checklist'),
                	'elements' => array(
						'help'  => array(
                        	'title' => get_string('documentation','artefact.checklist'),
                        	'type' => 'html',
							'value' =>  get_string('urlimportcsv','artefact.checklist'),
						),
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
	        		        'type'  => 'text',
                            'defaultvalue' => null,
	       			        'size' => 80,
		    	            'title' => get_string('description', 'artefact.checklist'),
    		    	    ),
	        		    'motivation' => array(
	        		        'type'  => 'text',
                            'defaultvalue' => null,
	       			        'size' => 80,
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
                				'required' => true,
            				),
	            			'separator' => ' &nbsp; ',
    	            		'title' => get_string('publiclist', 'artefact.checklist'),
        	        		'description' => get_string('publiclistdesc','artefact.checklist'),
						),
    	            	'filename' => array(
        	            	'type' => 'file',
	        	            'title' => get_string('filename', 'artefact.checklist'),
    	        	        'rules' => array('required' => true),
        	        	    'maxfilesize'  => get_max_upload_size(false),
	            	    ),
    	            	'save' => array(
	    	                'type' => 'submit',
    	    	            'value' => get_string('import', 'artefact.checklist'),
        	    	    ),
/*
	            	    'save' => array(
    	            	    'type' => 'submitcancel',
        	            	'value' => get_string('import', 'artefact.checklist'),
							'goto' => get_config('wwwroot') . 'artefact/checklist/index.php',
                		),
*/
					),
            	),
        	),
		)
		);
        return $importformcsv;
    }
}


function import_submit_csv(Pieform $form, $values) {
    global $USER, $SESSION;

	$title = !empty($values['title'])?$values['title']:'';
    $description = !empty($values['description'])?$values['description']:'';
    $motivation = !empty($values['motivation'])?$values['motivation']:'';
    $public = !empty($values['public'])?$values['public']:0;
	if (!empty($values['filename']   )){
		// DEBUG
		//echo "<br /> import.php :: 241 :: FILENAME <br />\n";
		//print_object( $values['filename']  );
		//exit;
	 	$name =  $values['filename']['name'];
		$type = $values['filename']['type'];
        $filename = $values['filename']['tmp_name'];
		// test correct type of file
		$ok = ($type == 'text/csv') || (strripos($name, ".csv")) ? 1 : 0;
	    // $values['filename']['type']=='text/csv') works with admin role, but not with user role ! Why ?
        if (file_exists($filename) && $ok) {
		    create_checklist_csv($title, $description, $motivation, $public, $name, $filename, $USER->get('id'));
    	}
    	else {
        	$SESSION->add_error_msg(get_string('loadcsvfailedfilenonexist', 'artefact.checklist', $name));
	    }
	}
   	redirect(get_config('wwwroot') . '/artefact/checklist/index.php');
}


/**
 * checklist + item load
 * @input handle file
 * @input owner  USER id
 * @return none
 **/
function create_checklist_csv($title, $description, $motivation, $public, $name, $filename, $owner) {
// Borowed from moodle/grade/edit/outcome/import.php
// JF 2014
if ($handle = fopen($filename, "r")){
    $line = 0; // will keep track of current line, to give better error messages.
    $counteritem=0; // how many items ?
    $motivation = $motivation.' '. get_string('outcomes', 'artefact.checklist', $name);
	$newtitle='';

	$file_headers = '';

    // $csv_data needs to have at least these columns, the value is the default position in the data file.
    $headers = array('outcome_name' => 0, 'outcome_shortname' => 1, 'scale_name' => 3, 'scale_items' => 4);
    $optional_headers = array('outcome_description'=>2, 'scale_description' => 5);
    $imported_headers = array(); // will later be initialized with the values found in the file

    $fatal_error = false;

	// Save artefact
	$data = new stdClass();
	$data->owner = $owner;
    $data->title = $title;
	if (empty($description)){
		$data->description = get_string('outcomesdesc', 'artefact.checklist');
	}
	else{
        $data->description = $description;
	}
    $data->description .= ' '. get_string('imported', 'artefact.checklist', date("Y-m-d H:i:s"));
    $data->motivation = $motivation;
	$data->public = $public;
	// Debug
	// print_object($data      );

	$classname = 'ArtefactTypeChecklist';
	$ac = new $classname(0, $data);
	$ac->commit();

    // data should be separated by a ';'.  *NOT* by a comma!  TODO: version 2.0
    // or whenever we can depend on PHP5, set the second parameter (8192) to 0 (unlimited line length) : the database can store over 128k per line.
    while ( $csv_data = fgetcsv($handle, 8192, ';', '"')) { // if the line is over 8k, it won't work...
        $line++;

		//print_object($csv_data      );

        // be tolerant on input, as fgetcsv returns "an array comprising a single null field" on blank lines
        if ($csv_data == array(null)) {
            continue;
        }

        // on first run, grab and analyse the header
        if ($file_headers == '') {
            $file_headers = array_flip($csv_data); // save the header line ... TODO: use the header line to let import work with columns in arbitrary order
			// outcome_name;outcome_shortname;outcome_description;scale_name;scale_items;scale_description
			// C2i2e-2011 A.1-1 :: Identifier les personnes ressources Tic et leurs rôles (...);A.1-1;Identifier les personnes ressources Tic et leurs rôles respectifs au niveau local, régional et national.;Item référentiel;Non pertinent,Non validé,Validé;Ce barème est destiné à évaluer l'acquisition des compétences du module référentiel.

            $error = false;
            foreach($headers as $key => $value) {
                // sanity check #1: make sure the file contains all the mandatory headers
                if (!array_key_exists($key, $file_headers)) {
                    $error = true;
                    break;
                }
            }
            if ($error) {
                $fatal_error = true;
                break;
            }

            foreach(array_merge($headers, $optional_headers) as $header => $position) {
                // match given columns to expected columns *into* $headers
                $imported_headers[$header] = $file_headers[$header];
            }

            continue; // we don't import headers
        }

        // sanity check #2: every line must have the same number of columns as there are
        // headers.  If not, processing stops.
        if ( count($csv_data) != count($file_headers) ) {
           $fatal_error = true;
            break;
        }

        // sanity check #3: all required fields must be present on the current line.
        foreach ($headers as $header => $position) {
            if ($csv_data[$imported_headers[$header]] == '') {
                $fatal_error = true;
                break;
            }
        }

        // MDL-17273 errors in csv are not preventing import from happening. We break from the while loop here
        if ($fatal_error) {
            break;
        }
		// Items
		// outcome_name;outcome_shortname;outcome_description;scale_name;scale_items;scale_description
		// "B2i_Collège Col1.1 :: 1.1) Je sais m'identifier sur un réseau ou un site et (...)";Col1.1;"1.1) Je sais m'identifier sur un réseau ou un site et mettre fin à cette identification.";Competences;"NA, ECA, PA, A";"NA : Non acquisECA : En cours d'acquisitionPA : Presque (...)"

        $outcome_data = array(
			'fullname' => $csv_data[$imported_headers['outcome_name']],
			'shortname' => $csv_data[$imported_headers['outcome_shortname']],
            'description' => $csv_data[$imported_headers['outcome_description']],
			'scalename' => $csv_data[$imported_headers['scale_name']],
            'scale' => $csv_data[$imported_headers['scale_items']],
            'scale_description' => $csv_data[$imported_headers['scale_description']]);
		//print_object($outcome_data);
		//exit;

		if (empty($newtitle)){
		   $newtitle=substr($outcome_data['fullname'], 0, strpos($outcome_data['fullname'], " "));
		}
		$datai= array();
       	$datai['artefact'] = $ac->get('id');
        $datai['parent'] = $ac->get('id');
   	    $datai['owner'] = $owner;
      	$datai['title'] = $outcome_data['description'];
        $datai['code'] = $outcome_data['shortname'];
   	    $datai['scale'] = $outcome_data['scale'];
      	$datai['valueindex'] = 0;
        $datai['optionitem'] = 0;
        $datai['displayorder'] = $counteritem;
        // DEBUG
		// print_object($datai);
		// exit;
    	$classname = 'ArtefactTypeItem';
   		$ai = new $classname(0, $datai);
   		$ai->commit();
        $counteritem++;
	}
    if (!empty($newtitle)){
		$description = $ac->get('description');
		$ac->set('description', $description . "\n<br /><b>$newtitle</b>\n");
		$ac->commit();
	}
}
}



/**
 * Import a Moodle CheckList csv file format
 *
 */

class ArtefactTypeImportChecklistMoodle extends ArtefactTypeChecklist {
    public static function is_singular() { return true;  }
    public static function get_form() {
        $importformmoodlecsv = pieform(
		array(
            'name'        => 'importformmoodlecsv',
            'plugintype'  => 'artefact',
            'successcallback' => 'import_submit_moodlecsv',
            'pluginname'  => 'checklist',
            'method'      => 'post',
            'elements'    => array(
				'optionnal' => array(
	            	'type' => 'fieldset',
	    	        'name' => 'csvfield',
					'title' => 'importmoodlecsv',
        		    'collapsible' => true,
            		'collapsed' => true,
	            	'legend' => get_string('descimportmoodlecsv','artefact.checklist'),
                	'elements' => array(
						'help'  => array(
                        	'title' => get_string('documentation','artefact.checklist'),
                        	'type' => 'html',
							'value' =>  get_string('urlimportmoodlecsv','artefact.checklist'),
						),
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
	        		        'type'  => 'text',
                            'defaultvalue' => null,
	       			        'size' => 80,
		    	            'title' => get_string('description', 'artefact.checklist'),
    		    	    ),
	        		    'motivation' => array(
	        		        'type'  => 'text',
                            'defaultvalue' => null,
	       			        'size' => 80,
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
                				'required' => true,
            				),
	            			'separator' => ' &nbsp; ',
    	            		'title' => get_string('publiclist', 'artefact.checklist'),
        	        		'description' => get_string('publiclistdesc','artefact.checklist'),
						),
                        'codeprefix' => array(
	        		        'type'  => 'text',
                            'defaultvalue' => null,
	       			        'size' => 20,
	    	            	'title' => get_string('codeprefix', 'artefact.checklist'),
                            'description' => get_string('codeprefixdesc', 'artefact.checklist'),
		    	        ),

                        'scale' => array(
	        		        'type'  => 'text',
                            'defaultvalue' => null,
	       			        'size' => 40,
	    	            	'title' => get_string('scale', 'artefact.checklist'),
                            'description' => get_string('scaledesc', 'artefact.checklist'),
		    	        ),
    	            	'filename' => array(
        	            	'type' => 'file',
	        	            'title' => get_string('filename', 'artefact.checklist'),
    	        	        'rules' => array('required' => true),
        	        	    'maxfilesize'  => get_max_upload_size(false),
	            	    ),
    	            	'save' => array(
	    	                'type' => 'submit',
    	    	            'value' => get_string('import', 'artefact.checklist'),
        	    	    ),
/*
	            	    'save' => array(
    	            	    'type' => 'submitcancel',
        	            	'value' => get_string('import', 'artefact.checklist'),
							'goto' => get_config('wwwroot') . 'artefact/checklist/index.php',
                		),
*/
					),
            	),
        	),
		)
		);
        return $importformmoodlecsv;
    }
}


function import_submit_moodlecsv(Pieform $form, $values) {
    global $USER, $SESSION;

	$title = !empty($values['title'])?$values['title']:'';
    $description = !empty($values['description'])?$values['description']:'';
    $motivation = !empty($values['motivation'])?$values['motivation']:'';
    $public = !empty($values['public'])?$values['public']:0;
    $codeprefix = !empty($values['codeprefix'])?$values['codeprefix']:'Item';
    $scale = !empty($values['scale'])?$values['scale']:'NA,ECA,PA,A';
	if (!empty($values['filename']   )){
		// DEBUG
		//echo "<br /> import.php :: 241 :: FILENAME <br />\n";
		//print_object( $values['filename']  );
		//exit;
	 	$name =  $values['filename']['name'];
		$type = $values['filename']['type'];
        $filename = $values['filename']['tmp_name'];
		// test correct type of file
		$ok = ($type == 'text/csv') || (strripos($name, ".csv")) ? 1 : 0;
	    // $values['filename']['type']=='text/csv') works with admin role, but not with user role ! Why ?
        if (file_exists($filename) && $ok) {
		    create_checklist_moodlecsv($title, $description, $motivation, $public, $name, $filename, $USER->get('id') ,$scale, $codeprefix);
    	}
    	else {
        	$SESSION->add_error_msg(get_string('loadcsvfailedfilenonexist', 'artefact.checklist', $name));
	    }
	}
   	redirect(get_config('wwwroot') . '/artefact/checklist/index.php');
}


/**
 * checklist + item load from moodle/mod/checklist plugin
 * @input handle file
 * @input owner  USER id
 * @return none
 **/
function create_checklist_moodlecsv($title, $description, $motivation, $public, $name, $filename, $owner, $scale='NA,ECA,PA,A', $codeprefix='Item') {
// from moodle/mod/checklist plugin import / export format
// JF 2014
$separator = ',';

if ($handle = fopen($filename, "r")){
    $line = 0; // will keep track of current line, to give better error messages.
    $counteritem=0; // how many items ?
    $motivation = $motivation.' '. get_string('outcomes', 'artefact.checklist', $name);

	$file_headers = '';

   // $csv_data needs to have at least these columns, the value is the default position in the data file.
    $headers = array('Item text' => 0, 'Indent' => 1, 'Type (0 - normal; 1 - optional; 2 - heading)' => 2, 'Due Time (timestamp)' => 3, 'Colour (red; orange; green; purple; black)' => 4);

    $optional_headers = array();
    $imported_headers = array(); // will later be initialized with the values found in the file

    $fatal_error = false;

	// Save artefact
	$data = new stdClass();
	$data->owner = $owner;
    $data->title = $title;
	if (empty($description)){
		$data->description = get_string('outcomesdesc', 'artefact.checklist');
	}
	else{
        $data->description = $description;
	}
    $data->description .= ' '. get_string('imported', 'artefact.checklist', date("Y-m-d H:i:s"));
    $data->motivation = $motivation;
	$data->public = $public;
	// Debug
	// print_object($data      );

	$classname = 'ArtefactTypeChecklist';
	$ac = new $classname(0, $data);
	$ac->commit();

    // data should be separated by a ';'.  *NOT* by a comma!  TODO: version 2.0
    // or whenever we can depend on PHP5, set the second parameter (8192) to 0 (unlimited line length) : the database can store over 128k per line.
    while ( $csv_data = fgetcsv($handle, 8192, $separator, '"')) { // if the line is over 8k, it won't work...
        $line++;

		//print_object($csv_data      );

        // be tolerant on input, as fgetcsv returns "an array comprising a single null field" on blank lines
        if ($csv_data == array(null)) {
            continue;
        }

        // on first run, grab and analyse the header
        if ($file_headers == '') {
            $file_headers = array_flip($csv_data); // save the header line ... TODO: use the header line to let import work with columns in arbitrary order
// Item text,Indent,Type (0 - normal; 1 - optional; 2 - heading),Due Time (timestamp),Colour (red; orange; green; purple; black)
            $error = false;
            foreach($headers as $key => $value) {
                // sanity check #1: make sure the file contains all the mandatory headers
                if (!array_key_exists($key, $file_headers)) {
                    $error = true;
                    break;
                }
            }
            if ($error) {
                $fatal_error = true;
                break;
            }

            foreach(array_merge($headers, $optional_headers) as $header => $position) {
                // match given columns to expected columns *into* $headers
                $imported_headers[$header] = $file_headers[$header];
            }

            continue; // we don't import headers
        }

        // sanity check #2: every line must have the same number of columns as there are
        // headers.  If not, processing stops.
        if ( count($csv_data) != count($file_headers) ) {
           $fatal_error = true;
            break;
        }

        // sanity check #3: all required fields must be present on the current line.
        foreach ($headers as $header => $position) {
            if ($csv_data[$imported_headers[$header]] == '') {
                $fatal_error = true;
                break;
            }
        }

        // MDL-17273 errors in csv are not preventing import from happening. We break from the while loop here
        if ($fatal_error) {
            break;
        }
		// Items
// Item text,Indent,Type (0 - normal; 1 - optional; 2 - heading),Due Time (timestamp),Colour (red; orange; green; purple; black)

// Domaine 1,0,2,0,green
// C2i1 D1.1.1 :: Organiser un espace de travail complexe,1,1,0,0

		if (preg_match("/ :: /", $csv_data[$imported_headers['Item text']])){
			$pos = strpos($csv_data[$imported_headers['Item text']], " :: ");
			$code=substr($csv_data[$imported_headers['Item text']], 0, $pos);
           	$title=substr($csv_data[$imported_headers['Item text']], $pos+4);
		}
		else{
            $code=$codeprefix.' '.$counteritem;
			$title=$csv_data[$imported_headers['Item text']];
		}

        $outcome_data = array(
			'title' => $title,
            'description' => '',
			'code' => $code,
            'scale' => $scale,
            'optionitem' => $csv_data[$imported_headers['Type (0 - normal; 1 - optional; 2 - heading)']],
			);

		$datai= array();
       	$datai['artefact'] = $ac->get('id');
        $datai['parent'] = $ac->get('id');
   	    $datai['owner'] = $owner;
      	$datai['title'] = $outcome_data['title'];
        $datai['code'] = $outcome_data['code'];
   	    $datai['scale'] = $outcome_data['scale'];
      	$datai['valueindex'] = 0;
        $datai['optionitem'] = $outcome_data['optionitem'];
        $datai['displayorder'] = $counteritem;
        // DEBUG
		//print_object($datai);
		//exit;
    	$classname = 'ArtefactTypeItem';
   		$ai = new $classname(0, $datai);
   		$ai->commit();
        $counteritem++;
	}
    if (!empty($newtitle)){
		$description = $ac->get('description');
		$ac->set('description', $description . "\n<br /><b>$newtitle</b>\n");
		$ac->commit();
	}
}
}


// Xml file
$importformxml = ArtefactTypeImportChecklistXml::get_form();

// Csv file
$importformcsv = ArtefactTypeImportOutcomesMoodle::get_form();

// Csv Moodle checklist form file
$importformmoodlecsv = ArtefactTypeImportChecklistMoodle::get_form();

$smarty = smarty(array('tablerenderer','jquery'));
$smarty->assign('PAGEHEADING', hsc(get_string('import', 'artefact.checklist')));
$smarty->assign('importformxml', $importformxml);
$smarty->assign('importformcsv', $importformcsv);
$smarty->assign('importformmoodlecsv', $importformmoodlecsv);
$smarty->assign('SUBPAGENAV', PluginArtefactChecklist::submenu_items());
$smarty->display('artefact:checklist:import.tpl');

