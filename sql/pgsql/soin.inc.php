<?php
//$Id$ 
//gen openMairie le 02/12/2025 16:57

include "../gen/sql/pgsql/soin.inc.php";

$ent = __("application")." -> ".__("Soins");
$tab_title = _("Soins");

$champAffiche = array(
    'soin.soin as "'.__("ID").'"',
	'animal.animal as "'.__("id de l'animal").'"',
	'animal.nom as "'.__("animal").'"',
    'soin_type.libelle as "'.__("type de soin").'"',
    'to_char(soin.date_soin ,\'DD/MM/YYYY\') as "'.__("date du soin").'"',
    'soin.posologie as "'.__("posologie").'"',
    'veterinaire.nom as "'.__("vétérinaire").'"',
    'clinique.nom as "'.__("clinique").'"'
);

$sousformulaire = array(
    'medicament',
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
	'soin' => array(
		'table' => 'soin',
		'colonne' => 'soin',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('id')
	),
	'animal' => array(
		'table' => 'soin',
		'colonne' => 'animal',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('animal')
	),
	'soin_type' => array(
		'table' => 'soin',
		'colonne' => 'soin_type',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('type de soin')
	),
	'veterinaire' => array(
		'table' => 'soin',
		'colonne' => 'veterinaire',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('veterinaire')
	),
	'clinique' => array(
		'table' => 'soin',
		'colonne' => 'clinique',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('clinique')
	),
	'date_soin' => array(
		'table' => 'sejours',
		'colonne' => 'date_soin',
		'libelle' => _('date du soin'),
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
	'absolute_object' => 'soin',
	'export' => array("csv")
);