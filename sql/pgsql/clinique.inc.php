<?php
//$Id$ 
//gen openMairie le 02/12/2025 16:17

include "../gen/sql/pgsql/clinique.inc.php";

$ent = __("application")." -> ".__("Cliniques");
$tab_title = _("Cliniques");

$sousformulaire = array(
    'veterinaire',
);

$champAffiche = array(
    'clinique.clinique as "'.__("ID").'"',
    'clinique.nom as "'.__("nom").'"',
    'clinique.adresse as "'.__("adresse").'"',
    'ville.nom as "'.__("ville").'"',
    'ville.code_postal as "'.__("code postal").'"',
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

$champs['nom'] = array(
	'table' => 'clinique',
	'colonne' => 'nom',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('nom')
);

$champs['adresse'] = array(
	'table' => 'clinique',
	'colonne' => 'adresse',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('adresse')
);

$champs['ville'] = array(
	'table' => 'clinique',
	'colonne' => 'ville',
	'type' => 'select',
	'taille' => 10,
	'libelle' => _('ville')
);

$champs['code_postal'] = array(
	'table' => 'clinique',
	'colonne' => 'code_postal',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('code postal')
);

$options[] = array(
	'type' => 'search',
	'display' => true,
    'advanced'  => $champs,
    'default_form'  => 'advanced',
	'absolute_object' => 'clinique',
	'export' => array("csv")
);