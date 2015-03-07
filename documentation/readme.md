Artefact CheckList - Mahara
===========================

Jean FRUITET - Université de Nantes

jean.fruitet@univ-nantes.fr


L'artefact Mahara CheckList https://github.com/jfruitet/moodle_checkskill est un décalque (avec des fonctions moins ambitieuses) des plugins CheckList / CheckSkill (https://github.com/jfruitet/moodle_checkskill) de Moodle dans leur usage 
pour l'évaluation de compétences...



Comme Mahara est centré sur l'étudiants (utilisateur) il n'y pas dans la version Mahara la couche d'évaluation par l'enseignant.

## Fonctionnalités
Un utilisateur qui souhaite faire valoir des compétences (ou lister des items et les évaluer dans Mahara) commence par créer une nouvelle Liste, lui associe des Items, et pour ces items il définit un barème.

S'il souhaite partager sa liste avec d'autres utilisateurs il peut la rendre publique.

Une fois recopiée dans l'espace personnel une liste peut être adaptée, ceci n'ayant pas d'impact sur la liste dont elle serait issues, chaque contexte Mahara restant propre à l'utilisateur...

Bien sûr il est aussi possible d'importer des listes en utilisant différents formats de fichiers
* un format d'import /export XML propre à l'artefact Checklist de Mahara
* deux formats CSV exportés de Moodle :
    - format CSV de CheckList / CheckSkill de Moodle
    - format CSV des Outcomes de Moodle

Concernant les items d'une liste l'utilisateur peut les modifier et les réorganiser sans que cela n'ait d'impact sur les listes des autres utilisateurs.

Chaque Item a un code, un descriptif et un type (qui apparaît dans le fichier CSV CheckList-Moodle), et détermine si l'Item est un intitulé (header), un item évaluable ou doit être masqué à l'affichage.
Consulter les fichiers à importer dans ***FilesToImportCheckList***.

Enfin et c'est à cela que tout conduit, l'utilisateur peut noter pour chaque item son appréciation personnelle en utilisant le barème associé à chaque item.

En général, comme on est plutôt dans le contexte de l'évaluation des compétences, les items sont des compétences et le barème de type LOMER :

**Non pertinent, Non acquis, En cours d'acquisition, Presque acquis, Acquis, Excellent**

ou

**Non Pertinent, Quelques notions, Pratique élémentaire, Maîtrise, Expertise**


Bien sûr on peut détourner Checklist pour implanter une  liste de course  :

**En stock, A renouveler, Manquant, Abandonné**

Et pour terminer les listes peuvent être affichées dans les Portfolio (vues) de Mahara.
