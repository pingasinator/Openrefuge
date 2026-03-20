<?php
//$Id$ 
//gen openMairie le 04/12/2025 17:01

include "../gen/sql/pgsql/sejour.inc.php";

$ent = __("application")." -> ".__("Séjours");
$tab_title = _("Séjours");

// SELECT 
$champAffiche = array(
    'sejour.sejour as "'.__("id").'"',
    'animal.nom as "'.__("animal").'"',
    'hebergement.adresse as "'.__("hebergement").'"',
    'provenance.provenance as "'.__("provenance").'"',
    'to_char(sejour.date_entree ,\'DD/MM/YYYY\') as "'.__("date d'entée").'"',
    'to_char(sejour.date_sortie ,\'DD/MM/YYYY\') as "'.__("date de sortie").'"',
    'sejour.date_sortie - sejour.date_entree as "'.__("nb de jours").'"',
    'round(coalesce(sejour_tarif.prix,\'0\')::numeric,2) as "'.__("tarif").'"',
    'round(coalesce(cast((sejour.date_sortie - sejour.date_entree) as int) * sejour_tarif.prix,\'0\')::numeric,2) as  "'.__("somme").'"',
    "case sejour.paye when 't' then 'Oui' else 'Non' end as \"".__("payé")."\"",
    );

// EDITION

/*
$tab_actions['left']['état'] = array(
'lien' => OM_ROUTE_FORM."&obj=".$obj."&action=102&idx=",
'id' => '',
'target' => "_blank",
'lib' => '<span class="om-icon om-icon-16 om-icon-fix pdf-16" title="'.__('télécharger le récapitulatif au format pdf').'">'.__('récapitulatif').'</span>',
'ordre' => 110,
);
*/

// Recherche avancée
$champs = array(
    'animal' => array(
        'table' => 'sejour',
	    'colonne' => 'animal',
	    'type' => 'select',
	    'taille' => 10,
	    'libelle' => _('animal')
    ),
    'hebergement' => array(
        'table' => 'sejour',
	    'colonne' => 'hebergement',
	    'type' => 'select',
	    'taille' => 10,
	    'libelle' => _('hebergement')
    ),
    'date_d_entree' => array(
        'table' => 'sejour',
	    'colonne' => 'date_entree',
	    'libelle' => _('date d\'entrée'),
        'lib1' => _('Du'),
        'lib2' => _('Au'),
        'type' => 'date',
        'taille' => 10,
        'max' => 8,
        'where' => 'intervaldate'
    ),
    'date_de_sortie' => array(
	'table' => 'sejour',
	'colonne' => 'date_sortie',
	'libelle' => _('date de sortie'),
    'lib1' => _('Du'),
    'lib2' => _('Au'),
    'type' => 'date',
    'taille' => 10,
    'max' => 8,
    'where' => 'intervaldate'
    ),
);

$options[] = array(
	'type' => 'search',
	'display' => true,
	'advanced'  => $champs,
	'default_form'  => 'advanced',
	'absolute_object' => 'sejour',
	'export' => array("csv")
);