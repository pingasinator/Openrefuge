<?php
//$Id$ 
//gen openMairie le 05/12/2025 15:14

include "../gen/sql/pgsql/medicament.inc.php";

$ent = __("application")." -> ".__("Médicaments");
$tab_title = _("Médicaments");

// SELECT 
$champAffiche = array(
    'medicament.medicament as "'.__("ID").'"',
    'medicament.nom as "'.__("nom").'"',
    'to_char(medicament.date_debut ,\'DD/MM/YYYY\') as "'.__("date de debut").'"',
    'to_char(medicament.date_fin ,\'DD/MM/YYYY\') as "'.__("date de fin").'"',
    'medicament.dose as "'.__("dose").'"',
    'medicament.frequence as "'.__("fréquence").'"',
    'soin.soin as "'.__("soin").'"',
    'animal.nom as "'.__("animal").'"',
    );

// EDITION
$tab_actions['left']['état'] = array(
'lien' => OM_ROUTE_FORM."&obj=".$obj."&action=102&idx=",
'id' => '',
'target' => "_blank",
'lib' => '<span class="om-icon om-icon-16 om-icon-fix pdf-16" title="'.__('télécharger le récapitulatif au format pdf').'">'.__('récapitulatif').'</span>',
'ordre' => 110,
);

// Recherche avancée

$champs = array(
	'medicament' => array(
		'table' => 'medicament',
		'colonne' => 'medicament',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('id')
	),
	'nom' => array(
		'table' => 'medicament',
		'colonne' => 'nom',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('nom')
	),
	'soin' => array(
		'table' => 'medicament',
		'colonne' => 'soin',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('soin')
	),
	'date_debut' => array(
		'table' => 'medicament',
		'colonne' => 'date_debut',
		'libelle' => _('date de début'),
    	'lib1' => _('du'),
    	'lib2' => _('au'),
    	'type' => 'date',
    	'taille' => 10,
    	'max' => 8,
    	'where' => 'intervaldate'
	),
	'date_fin' => array(
		'table' => 'medicament',
		'colonne' => 'date_fin',
		'libelle' => _('date de fin'),
    	'lib1' => _('du'),
    	'lib2' => _('au'),
    	'type' => 'date',
    	'taille' => 10,
    	'max' => 8,
    	'where' => 'intervaldate'
	),
	'animal' => array(
		'table' => 'medicament',
		'colonne' => 'animal',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('animal')
	)
);

$options[] = array(
	'type' => 'search',
	'display' => true,
    'advanced'  => $champs,
    'default_form'  => 'advanced',
	'absolute_object' => 'medicament',
	'export' => array("csv")
);