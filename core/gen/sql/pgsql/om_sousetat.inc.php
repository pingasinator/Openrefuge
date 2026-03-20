<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("parametrage")." -> ".__("om_sousetat");
if(!isset($premier)) $premier='';
if(!isset($tricolsf)) $tricolsf='';
if(!isset($premiersf)) $premiersf='';
if(!isset($selection)) $selection='';
if(!isset($retourformulaire)) $retourformulaire='';
if (!isset($idxformulaire)) {
    $idxformulaire = '';
}
if (!isset($tricol)) {
    $tricol = '';
}
if (!isset($valide)) {
    $valide = '';
}
// FROM 
$table = DB_PREFIXE."om_sousetat
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_sousetat.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_sousetat.om_sousetat as "'.__("om_sousetat").'"',
    'om_sousetat.id as "'.__("id").'"',
    'om_sousetat.libelle as "'.__("libelle").'"',
    "case om_sousetat.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    'om_sousetat.titrehauteur as "'.__("titrehauteur").'"',
    'om_sousetat.titrefont as "'.__("titrefont").'"',
    'om_sousetat.titreattribut as "'.__("titreattribut").'"',
    'om_sousetat.titretaille as "'.__("titretaille").'"',
    'om_sousetat.titrebordure as "'.__("titrebordure").'"',
    'om_sousetat.titrealign as "'.__("titrealign").'"',
    'om_sousetat.titrefond as "'.__("titrefond").'"',
    'om_sousetat.titrefondcouleur as "'.__("titrefondcouleur").'"',
    'om_sousetat.titretextecouleur as "'.__("titretextecouleur").'"',
    'om_sousetat.intervalle_debut as "'.__("intervalle_debut").'"',
    'om_sousetat.intervalle_fin as "'.__("intervalle_fin").'"',
    'om_sousetat.entete_flag as "'.__("entete_flag").'"',
    'om_sousetat.entete_fond as "'.__("entete_fond").'"',
    'om_sousetat.entete_orientation as "'.__("entete_orientation").'"',
    'om_sousetat.entete_hauteur as "'.__("entete_hauteur").'"',
    'om_sousetat.entetecolone_bordure as "'.__("entetecolone_bordure").'"',
    'om_sousetat.entetecolone_align as "'.__("entetecolone_align").'"',
    'om_sousetat.entete_fondcouleur as "'.__("entete_fondcouleur").'"',
    'om_sousetat.entete_textecouleur as "'.__("entete_textecouleur").'"',
    'om_sousetat.tableau_largeur as "'.__("tableau_largeur").'"',
    'om_sousetat.tableau_bordure as "'.__("tableau_bordure").'"',
    'om_sousetat.tableau_fontaille as "'.__("tableau_fontaille").'"',
    'om_sousetat.bordure_couleur as "'.__("bordure_couleur").'"',
    'om_sousetat.se_fond1 as "'.__("se_fond1").'"',
    'om_sousetat.se_fond2 as "'.__("se_fond2").'"',
    'om_sousetat.cellule_fond as "'.__("cellule_fond").'"',
    'om_sousetat.cellule_hauteur as "'.__("cellule_hauteur").'"',
    'om_sousetat.cellule_largeur as "'.__("cellule_largeur").'"',
    'om_sousetat.cellule_bordure_un as "'.__("cellule_bordure_un").'"',
    'om_sousetat.cellule_bordure as "'.__("cellule_bordure").'"',
    'om_sousetat.cellule_align as "'.__("cellule_align").'"',
    'om_sousetat.cellule_fond_total as "'.__("cellule_fond_total").'"',
    'om_sousetat.cellule_fontaille_total as "'.__("cellule_fontaille_total").'"',
    'om_sousetat.cellule_hauteur_total as "'.__("cellule_hauteur_total").'"',
    'om_sousetat.cellule_fondcouleur_total as "'.__("cellule_fondcouleur_total").'"',
    'om_sousetat.cellule_bordure_total as "'.__("cellule_bordure_total").'"',
    'om_sousetat.cellule_align_total as "'.__("cellule_align_total").'"',
    'om_sousetat.cellule_fond_moyenne as "'.__("cellule_fond_moyenne").'"',
    'om_sousetat.cellule_fontaille_moyenne as "'.__("cellule_fontaille_moyenne").'"',
    'om_sousetat.cellule_hauteur_moyenne as "'.__("cellule_hauteur_moyenne").'"',
    'om_sousetat.cellule_fondcouleur_moyenne as "'.__("cellule_fondcouleur_moyenne").'"',
    'om_sousetat.cellule_bordure_moyenne as "'.__("cellule_bordure_moyenne").'"',
    'om_sousetat.cellule_align_moyenne as "'.__("cellule_align_moyenne").'"',
    'om_sousetat.cellule_fond_nbr as "'.__("cellule_fond_nbr").'"',
    'om_sousetat.cellule_fontaille_nbr as "'.__("cellule_fontaille_nbr").'"',
    'om_sousetat.cellule_hauteur_nbr as "'.__("cellule_hauteur_nbr").'"',
    'om_sousetat.cellule_fondcouleur_nbr as "'.__("cellule_fondcouleur_nbr").'"',
    'om_sousetat.cellule_bordure_nbr as "'.__("cellule_bordure_nbr").'"',
    'om_sousetat.cellule_align_nbr as "'.__("cellule_align_nbr").'"',
    'om_sousetat.cellule_numerique as "'.__("cellule_numerique").'"',
    'om_sousetat.cellule_total as "'.__("cellule_total").'"',
    'om_sousetat.cellule_moyenne as "'.__("cellule_moyenne").'"',
    'om_sousetat.cellule_compteur as "'.__("cellule_compteur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_sousetat.om_collectivite as "'.__("om_collectivite").'"',
    'om_sousetat.titre as "'.__("titre").'"',
    'om_sousetat.om_sql as "'.__("om_sql").'"',
    );
//
$champRecherche = array(
    'om_sousetat.om_sousetat as "'.__("om_sousetat").'"',
    'om_sousetat.id as "'.__("id").'"',
    'om_sousetat.libelle as "'.__("libelle").'"',
    'om_sousetat.titrehauteur as "'.__("titrehauteur").'"',
    'om_sousetat.titrefont as "'.__("titrefont").'"',
    'om_sousetat.titreattribut as "'.__("titreattribut").'"',
    'om_sousetat.titretaille as "'.__("titretaille").'"',
    'om_sousetat.titrebordure as "'.__("titrebordure").'"',
    'om_sousetat.titrealign as "'.__("titrealign").'"',
    'om_sousetat.titrefond as "'.__("titrefond").'"',
    'om_sousetat.titrefondcouleur as "'.__("titrefondcouleur").'"',
    'om_sousetat.titretextecouleur as "'.__("titretextecouleur").'"',
    'om_sousetat.intervalle_debut as "'.__("intervalle_debut").'"',
    'om_sousetat.intervalle_fin as "'.__("intervalle_fin").'"',
    'om_sousetat.entete_flag as "'.__("entete_flag").'"',
    'om_sousetat.entete_fond as "'.__("entete_fond").'"',
    'om_sousetat.entete_orientation as "'.__("entete_orientation").'"',
    'om_sousetat.entete_hauteur as "'.__("entete_hauteur").'"',
    'om_sousetat.entetecolone_bordure as "'.__("entetecolone_bordure").'"',
    'om_sousetat.entetecolone_align as "'.__("entetecolone_align").'"',
    'om_sousetat.entete_fondcouleur as "'.__("entete_fondcouleur").'"',
    'om_sousetat.entete_textecouleur as "'.__("entete_textecouleur").'"',
    'om_sousetat.tableau_largeur as "'.__("tableau_largeur").'"',
    'om_sousetat.tableau_bordure as "'.__("tableau_bordure").'"',
    'om_sousetat.tableau_fontaille as "'.__("tableau_fontaille").'"',
    'om_sousetat.bordure_couleur as "'.__("bordure_couleur").'"',
    'om_sousetat.se_fond1 as "'.__("se_fond1").'"',
    'om_sousetat.se_fond2 as "'.__("se_fond2").'"',
    'om_sousetat.cellule_fond as "'.__("cellule_fond").'"',
    'om_sousetat.cellule_hauteur as "'.__("cellule_hauteur").'"',
    'om_sousetat.cellule_largeur as "'.__("cellule_largeur").'"',
    'om_sousetat.cellule_bordure_un as "'.__("cellule_bordure_un").'"',
    'om_sousetat.cellule_bordure as "'.__("cellule_bordure").'"',
    'om_sousetat.cellule_align as "'.__("cellule_align").'"',
    'om_sousetat.cellule_fond_total as "'.__("cellule_fond_total").'"',
    'om_sousetat.cellule_fontaille_total as "'.__("cellule_fontaille_total").'"',
    'om_sousetat.cellule_hauteur_total as "'.__("cellule_hauteur_total").'"',
    'om_sousetat.cellule_fondcouleur_total as "'.__("cellule_fondcouleur_total").'"',
    'om_sousetat.cellule_bordure_total as "'.__("cellule_bordure_total").'"',
    'om_sousetat.cellule_align_total as "'.__("cellule_align_total").'"',
    'om_sousetat.cellule_fond_moyenne as "'.__("cellule_fond_moyenne").'"',
    'om_sousetat.cellule_fontaille_moyenne as "'.__("cellule_fontaille_moyenne").'"',
    'om_sousetat.cellule_hauteur_moyenne as "'.__("cellule_hauteur_moyenne").'"',
    'om_sousetat.cellule_fondcouleur_moyenne as "'.__("cellule_fondcouleur_moyenne").'"',
    'om_sousetat.cellule_bordure_moyenne as "'.__("cellule_bordure_moyenne").'"',
    'om_sousetat.cellule_align_moyenne as "'.__("cellule_align_moyenne").'"',
    'om_sousetat.cellule_fond_nbr as "'.__("cellule_fond_nbr").'"',
    'om_sousetat.cellule_fontaille_nbr as "'.__("cellule_fontaille_nbr").'"',
    'om_sousetat.cellule_hauteur_nbr as "'.__("cellule_hauteur_nbr").'"',
    'om_sousetat.cellule_fondcouleur_nbr as "'.__("cellule_fondcouleur_nbr").'"',
    'om_sousetat.cellule_bordure_nbr as "'.__("cellule_bordure_nbr").'"',
    'om_sousetat.cellule_align_nbr as "'.__("cellule_align_nbr").'"',
    'om_sousetat.cellule_numerique as "'.__("cellule_numerique").'"',
    'om_sousetat.cellule_total as "'.__("cellule_total").'"',
    'om_sousetat.cellule_moyenne as "'.__("cellule_moyenne").'"',
    'om_sousetat.cellule_compteur as "'.__("cellule_compteur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_sousetat.libelle ASC NULLS LAST";
$edition="om_sousetat";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_sousetat.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sousetat.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sousetat.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sousetat.om_collectivite = ".intval($idxformulaire).") ";
    }
}

