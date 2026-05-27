<?php
//$Id$ 
//gen openMairie le 09/12/2025 16:33

include "../gen/sql/pgsql/facture.inc.php";

$ent = __("application")." -> ".__("Factures");
$tab_title = _("Factures");

// EDITION
$tab_actions['left']['état'] = array(
'lien' => OM_ROUTE_FORM."&obj=".$obj."&action=102&idx=",
'id' => '',
'target' => "_blank",
'lib' => '<span class="om-icon om-icon-16 om-icon-fix pdf-16" title="'.__('télécharger le récapitulatif au format pdf').'">'.__('récapitulatif').'</span>',
'ordre' => 110,
);


$champAffiche = array(
    'facture.facture as "'.__("ID").'"',
    'facture.numero_facture as "'.__("numero de facture").'"',
    'to_char(facture.date_creation ,\'DD/MM/YYYY\') as "'.__("date de creation").'"',
    'personne.prenom as "'.__("personne").'"'
);

$sousformulaire = array(
    'facture_sejours',
    'facture_soins'
);

$champs = array(
	'numero_facture' => array(
		'table' => 'facture',
		'colonne' => 'numero_facture',
		'type' => 'text',
		'taille' => 15,
		'libelle' => _('numero de facture')
	),
	'personne' => array(
		'table' => 'facture',
		'colonne' => 'personne',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('personne')
	),
	'date_creation' => array(
		'table' => 'facture',
		'colonne' => 'date_creation',
		'libelle' => _('date de création'),
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
	'absolute_object' => 'facture',
	'export' => array("csv")
);