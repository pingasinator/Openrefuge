<?php
//$Id$ 
//gen openMairie le 18/02/2026 10:49

include "../gen/sql/pgsql/animal_race.inc.php";

$ent = __("application")." -> ".__("Races");
$tab_title = _("Races");

$champAffiche = array(
    'animal_race.animal_race as "'.__("ID").'"',
    'animal_race.nom as "'.__("nom").'"',
    'animal_espece.nom as "'.__("espèce").'"'
);

$champs = array(
    'animal_race' => array(
		'table' => 'animal_race',
		'colonne' => 'animal_race',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('id')
	),
    'animal_espece' => array(
        'table' => 'animal_race',
        'colonne' => 'animal_espece',
        'type' => 'select',
        'taille' => 10,
        'libelle' => _('espèce')
    ),
    'nom' => array(
        'table' => 'animal_race',
        'colonne' => 'nom',
        'type' => 'text',
        'taille' => 10,
        'libelle' => _('nom')
    )
);

$options[] = array(
    'type' => 'search',
    'display' => true,
    'advanced' => $champs,
    'default_form' => 'advanced',
    'absolute_object' => 'animal_race',
    'export' => array('csv')
);