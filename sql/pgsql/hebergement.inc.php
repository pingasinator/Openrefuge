<?php
//$Id$ 
//gen openMairie le 04/12/2025 10:31

include "../gen/sql/pgsql/hebergement.inc.php";

$ent = __("application")." -> ".__("Hébergements");
$tab_title = _("Hébergements");

$champAffiche = array(
    'hebergement.hebergement as "'.__("ID").'"',
    'hebergement.nom as "'.__("nom").'"',
    'hebergement_type.libelle as "'.__("type d'hébergement").'"',
    'hebergement.adresse as "'.__("adresse").'"',
    'ville.nom as "'.__("ville").'"',
    'ville.code_postal as "'.__("code postal").'"',
    'hebergement.telephone as "'.__("telephone").'"',

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
    'sejours'
);

// Recherche avancée

$champs['nom'] = array(
	'table' => 'hebergement',
	'colonne' => 'nom',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('nom')
);

$champs['hebergement_type'] = array(
	'table' => 'hebergement',
	'colonne' => 'hebergement_type',
	'type' => 'select',
	'taille' => 10,
	'libelle' => _('type d\'hébergement')
);

$champs['adresse'] = array(
	'table' => 'hebergement',
	'colonne' => 'adresse',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('adresse')
);

$champs['ville'] = array(
	'table' => 'hebergement',
	'colonne' => 'ville',
	'type' => 'select',
	'taille' => 10,
	'libelle' => _('ville')
);

$champs['code_postal'] = array(
	'table' => 'hebergement',
	'colonne' => 'code_postale',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('code postal')
);


$options[] = array(
	'type' => 'search',
	'display' => true,
    'advanced'  => $champs,
    'default_form'  => 'advanced',
	'absolute_object' => 'hebergement',
	'export' => array("csv")
);