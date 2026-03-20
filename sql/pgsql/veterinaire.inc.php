<?php
//$Id$ 
//gen openMairie le 02/12/2025 15:12

include "../gen/sql/pgsql/veterinaire.inc.php";

$ent = __("application")." -> ".__("Vétérinaires");
$tab_title = _("Vétérinaires");

// SELECT 
$champAffiche = array(
    'veterinaire.veterinaire as "'.__("id").'"',
    'veterinaire.nom as "'.__("nom").'"',
    'veterinaire.prenom as "'.__("prenom").'"',
    'civilite.libelle as "'.__("civilité").'"',
    'veterinaire.telephone as "'.__("telephone").'"',
    'clinique.nom as "'.__("clinique").'"',
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
	'table' => 'veterinaire',
	'colonne' => 'nom',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('nom')
);

$champs['prenom'] = array(
	'table' => 'veterinaire',
	'colonne' => 'nom',
	'type' => 'text',
	'taille' => 10,
	'libelle' => _('prénom')
);

$champs['civilite'] = array(
	'table' => 'veterinaire',
	'colonne' => 'civilite',
	'type' => 'select',
	'taille' => 10,
	'libelle' => _('civilité')
);

$champs['clinique'] = array(
	'table' => 'veterinaire',
	'colonne' => 'clinique',
	'type' => 'select',
	'taille' => 10,
	'libelle' => _('clinique')
);

$options[] = array(
	'type' => 'search',
	'display' => true,
    'advanced'  => $champs,
    'default_form'  => 'advanced',
	'absolute_object' => 'veterinaire',
	'export' => array("csv")
);