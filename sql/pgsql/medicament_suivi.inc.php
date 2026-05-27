<?php
//$Id$ 
//gen openMairie le 05/05/2026 16:31

include "../gen/sql/pgsql/medicament_suivi.inc.php";

$champAffiche = array(
    'medicament_suivi.medicament_suivi as "'.__("id").'"',
    'medicament.medicament as "'.__("id du medicament").'"',
    'medicament.nom as "'.__("medicament").'"',
    'animal.animal as "'.__("id de l'animal").'"',
    'animal.nom as "'.__("animal").'"',
    'to_char(medicament_suivi.date ,\'DD/MM/YYYY\') as "'.__("date").'"',
    'medicament_suivi.heure as "'.__("heure").'"',
    );

    $champs = array(
	'clinique' => array(
		'table' => 'clinique',
		'colonne' => 'clinique',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('id')
	),
	'medicament' => array(
		'table' => 'medicament',
		'colonne' => 'medicament',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('medicament')
	),
	'animal' => array(
		'table' => 'animal',
		'colonne' => 'animal',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('animal')
	),
	'date' => array(
		'table' => 'medicament_suivi',
		'colonne' => 'date',
		'libelle' => _('date'),
    	'lib1' => _('Du'),
    	'lib2' => _('Au'),
    	'type' => 'date',
    	'taille' => 10,
    	'max' => 8,
    	'where' => 'intervaldate'
	),
	'heure' => array(
		'table' => 'medicament_suivi',
		'colonne' => 'heure',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('heure')
	)
);

$options[] = array(
	'type' => 'search',
	'display' => true,
    'advanced'  => $champs,
    'default_form'  => 'advanced',
	'absolute_object' => 'medicament_suivi',
	'export' => array("csv")
);