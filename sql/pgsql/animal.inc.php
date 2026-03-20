<?php
//$Id$ 
//gen openMairie le 02/12/2025 14:23

include "../gen/sql/pgsql/animal.inc.php";

$ent = __("application")." -> ".__("Animaux");
$tab_title = _("Animaux");

$champAffiche = array(
    'animal.animal as "'.__("ID").'"',
    'animal.nom as "'.__("nom").'"',
    'animal_espece.nom as "'.__("espèce").'"',
    'animal_race.nom as "'.__("race").'"',
    'to_char(animal.date_naissance ,\'DD/MM/YYYY\') as "'.__("date de naissance").'"',
	'animal_sexe.libelle as "'.__("sexe").'"',
    'personne.prenom as "'.__("personne").'"',
);



// EDITION
$tab_actions['left']['état'] = array(
'lien' => OM_ROUTE_FORM."&obj=".$obj."&action=102&idx=",
'id' => '',
'target' => "_blank",
'lib' => '<span class="om-icon om-icon-16 om-icon-fix pdf-16" title="'.__('télécharger le récapitulatif au format pdf').'">'.__('récapitulatif').'</span>',
'ordre' => 110,
);

$sousformulaire = array(
    'soin',
    'sejour',
    'medicament'
);

$sousformulaire_parameters = array(
    "sejours" => array(
        "title" => _("séjours")
        )
    
);

// Recherche avancée

$champs = array(
	'nom' => array(
		'table' => 'animal',
		'colonne' => 'nom',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('nom')
	),
	'personne' =>  array(
		'table' => 'animal',
		'colonne' => 'personne',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('personne')
	),
	'animal_espece' => array(
	'table' => 'animal',
	'colonne' => 'animal_espece',
	'type' => 'select',
	'taille' => 10,
	'libelle' => _('espèce')
	),
	'animal_race' => array(
		'table' => 'animal',
		'colonne' => 'animal_race',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('race')
	),
	'date_de_naissance' => array(
		'table' => 'animal',
		'colonne' => 'date_de_naissance',
		'libelle' => _('date de naissance'),
    	'lib1' => _('Du'),
    	'lib2' => _('Au'),
    	'type' => 'date',
    	'taille' => 10,
    	'max' => 8,
    	'where' => 'intervaldate'
	)
);

$options[] = array(
	'type' => 'search',
	'display' => true,
    'advanced'  => $champs,
    'default_form'  => 'advanced',
	'absolute_object' => 'animal',
	'export' => array("csv")
);