<?php
//$Id$ 
//gen openMairie le 11/12/2025 11:25

include "../gen/sql/pgsql/personne.inc.php";

$ent = __("application")." -> ".__("Personnes");
$tab_title = _("Personnes");

$champAffiche = array(
    'personne.personne as "'.__("ID").'"',
    'personne.nom as "'.__("nom").'"',
    'personne.prenom as "'.__("prenom").'"',
    'civilite.libelle as "'.__("civilité").'"',
	'concat(personne.num_rue,\' \',rue.nom) as "'.__("adresse").'"',
    'ville.nom as "'.__("Ville").'"', 
    'ville.code_postal as "'.__("code postal").'"',
    'personne.telephone as "'.__("telephone").'"',
    'personne.telephone_sec as "'.__("telephone sec").'"',
    'personne.mail as "'.__("mail").'"',
);

$tab_actions['left']['lettretype'] = array(
'lien' => OM_ROUTE_FORM."&obj=".$obj."&action=102&idx=",
'id' => '',
'target' => "_blank",
'lib' => '<span class="om-icon om-icon-16 om-icon-fix pdf-16" title="'.__('télécharger le récapitulatif au format pdf').'">'.__('récapitulatif').'</span>',
'ordre' => 110,
);

$sousformulaire = array(
    'animal',
    'facture',
);

$champRecherche = array(
    'personne.personne as "'.__("ID").'"',
    'personne.nom as "'.__("nom").'"',
    'personne.prenom as "'.__("prenom").'"'
);

// Recherche avancée

$champs = array(
	'personne' => array(
		'table' => 'personne',
		'colonne' => 'personne',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('id')
	),
	'nom' => array(
		'table' => 'personne',
		'colonne' => 'nom',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('nom')
	),
	'prenom' => array(
		'table' => 'personne',
		'colonne' => 'prenom',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('prénom')
	),
	'civilite' => array(
		'table' => 'personne',
		'colonne' => 'civilite',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('civilité')
	),
	'ville' =>  array(
		'table' => 'personne',
		'colonne' => 'ville',
		'type' => 'select',
		'taille' => 10,
		'libelle' => _('ville')
	),
	'code_postal' => array(
		'table' => 'ville',
		'colonne' => 'code_postal',
		'type' => 'text',
		'taille' => 10,
		'libelle' => _('code postal')
	)
);

$options[] = array(
	'type' => 'search',
	'display' => true,
	'advanced'  => $champs,
	'default_form'  => 'advanced',
	'absolute_object' => 'personne',
	'export' => array("csv")
);