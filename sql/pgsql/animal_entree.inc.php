<?php
//$Id$ 
//gen openMairie le 28/04/2026 14:53

include "../gen/sql/pgsql/animal_entree.inc.php";

$champAffiche = array(
    'animal_entree.animal_entree as "'.__("id").'"',
    'animal.nom as "'.__("animal").'"',
    'animal.num_identification as "'.__("num identification").'"',
    'to_char(animal_entree.date_entree ,\'DD/MM/YYYY\') as "'.__("date d'entrée").'"'
);

$champs = array(
    'animal_entree' => array(
		'table' => 'animal_entree',
		'colonne' => 'animal_entree',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('id')
	),
	'animal' => array(
		'table' => 'animal',
		'colonne' => 'animal',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('animal')
	),
    'num_identification' => array(
		'table' => 'animal',
		'colonne' => 'num_identification',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('num d\'identification')
	),
    'date_entree' => array(
		'table' => 'animal_entree',
		'colonne' => 'date_entree',
		'libelle' => _('date d\'entrée'),
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
	'absolute_object' => 'animal_entree',
	'export' => array("csv")
);