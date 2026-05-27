<?php
//$Id$ 
//gen openMairie le 23/04/2026 09:56

include "../gen/sql/pgsql/animal_sortie.inc.php";

$champAffiche = array(
    'animal_sortie.animal_sortie as "'.__("id").'"',
    'animal.nom as "'.__("animal").'"',
    'animal.num_identification as "'.__("num identification").'"',
    'to_char(animal_sortie.date_sortie ,\'DD/MM/YYYY\') as "'.__("date de sortie").'"',
    'cause_mort.libelle as "'.__("cause de la mort").'"'
);

$champs = array(
    'animal_sortie' => array(
		'table' => 'animal_sortie',
		'colonne' => 'animal_sortie',
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
    'date_sortie' => array(
		'table' => 'animal_sortie',
		'colonne' => 'date_sortie',
		'libelle' => _('date de sortie'),
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