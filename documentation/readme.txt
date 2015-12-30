Artefact CheckList - Mahara

Jean FRUITET - Universit� de Nantes

jean.fruitet@univ-nantes.fr


L'artefact Mahara CheckList 
https://github.com/jfruitet/moodle_checkskill
est un d�calque (avec des fonctions moins ambitieuses) des plugins CheckList / CheckSkill de Moodle dans leur usage 
pour l'�valuation de comp�tences...
https://github.com/jfruitet/moodle_checkskill

Comme Mahara est centr� sur l'�tudiants (utilisateur) il n'y pas dans la version Mahara la couche d'�valuation par l'enseignant.

En substance  :
Un utilisateur qui souhaite faire valoir des comp�tences (ou lister des items et les �valuer dans Mahara) commence par cr�er une nouvelle Liste, lui associe des Items, et pour ces items il d�finit un bar�me.

S'il souhaite partager sa liste avec d'autres utilisateurs il peut la rendre publique.
Une fois recopi�e dans l'espace personnel une liste peut �tre adapt�e, ceci n'ayant pas d'impact sur la liste dont elle serait issues, chaque contexte Mahara restant propre � l'utilisateur...

Bien s�r il est aussi possible d'importer des listes en utilisant diff�rents formats de fichiers
- un format d'import /export XML propre � l'artefact Checklist de Mahara
- deux formats CSV export�s de Moodle :
    - format CSV de CheckList / CheckSkill de Moodle
    - format CSV des Outcomes de Moodle

Concernant les items d'une liste l'utilisateur peut les modifier et les r�organiser sans que cela n'ait d'impact sur les listes des autres utilisateurs.
Chaque Item a un code, un descriptif et un type (qui appara�t dans le fichier CSV CheckList-Moodle), et d�termine si l'Item est un intitul� (header), un item �valuable ou doit �tre masqu� � l'affichage.
Regarde dans les fichiers exemples du document ci-joint.

Enfin et c'est � cela que tout conduit, l'utilisateur peut noter pour chaque item son appr�ciation personnelle en utilisant le bar�me associ� � chaque item.
En g�n�ral, comme on est plut�t dans le contexte de l'�valuation des comp�tences, les items sot des comp�tences et le bar�me de type LOMER :
Non pertinent, Non acquis, En cours d'acquisition, Presque acquis, Acquis, Excellent
ou
Non Pertinent, Quelques notions, Pratique �l�mentaire, Ma�trise, Expertise...
Bref tu vois le genre.

Bine s�r on peut d�tourner Checklist pour implanter une  liste de course  :
En stock, A renouveler, Manquant, Abandonn�...

Et pour terminer les listes peuvent �tre affich�es dans les Portfolio (vues) de Mahara.
