Checklist artefact for Mahara
=============================

This artefact allows to create lists of items to check with a scale.

Cet artefact permet la création de liste d'items à évaluer avec un barème.


Author : <jean.fruitet@free.fr>

Version for Mahara 15.10

**Attention** :

La branche *master* est toujours la plus à jour pour la version de Mahara la plus récente.
Il y a une branche pour chaque version majeure antérieure.

The *master* branch is up-to-date for the latest Mahara version.
There is a GitHub branch for each old major version of Mahara.


- Mahara 15.10 : https://github.com/jfruitet/mahara_artefact-checklist/tree/master

- Mahara 1.9, 10.x, 15.04 :  https://github.com/jfruitet/mahara_artefact-checklist/tree/mahara102


####User can create his lists

Then user can chek the list against a multivaluated scale.

####User can import

 * Mahara XML Checklist files (https://github.com/jfruitet/mahara_artefact-checklist)
 * Moodle CSV Checklist (https://moodle.org/plugins/view.php?plugin=mod_checklist)
 * Outcomes CVS files from Moodle (https://docs.moodle.org/27/en/Outcomes#Importing_outcomes) .

####User can share lists and export them.

Blocktype Checklist can be displayed as any other artefact in Mahara views (portfolio).
  
###Installation :

 * Download ZIP from the mahara1510 branch  https://github.com/jfruitet/mahara_artefact-checklist/tree/mahara1510
 * Unzip archive in ./artefact/ then rename it "checklist"
 * Move  ./artefact/theme-checklist to ./mahara/theme/raw/plugintype/artefact/ then rename it "checklist"
 * Copy ./artefact/checklist/lang/fr.utf8 to ./maharadata/langpacks/fr.utf8/artefact/checklist/lang
 * Copy ./artefact/checklist/blocktype/(listofblocktypes)/lang to ./maharadata/langpacks/fr.utf8/artefact/checklist/blocktype/(listofblocktypes)/lang  

 * Log as admin
 * Go to Administration / plugins
 * Install checklist artefact
 * Install checklist blocktype
 * Print ./artefact/checklist/documentation/ as a user tutorial.

###Documentation

Read the readme.md in the ./documentation folder(in French)

###Files to import

Look at the ./documentation/FileToImport folder
