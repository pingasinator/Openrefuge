<?php
//$Id$ 
//gen openMairie le 14/05/2018 22:44

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("SIG")." -> ".__("flux");
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
$table = DB_PREFIXE."om_sig_flux
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_sig_flux.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_sig_flux.om_sig_flux as "'.__("om_sig_flux").'"',
    'om_sig_flux.libelle as "'.__("libelle").'"',
    'om_sig_flux.id as "'.__("id").'"',
    'om_sig_flux.attribution as "'.__("attribution").'"',
    'om_sig_flux.chemin as "'.__("chemin").'"',
    'om_sig_flux.couches as "'.__("couches").'"',
    'om_sig_flux.cache_type as "'.__("cache_type").'"',
    'om_sig_flux.cache_gfi_chemin as "'.__("cache_gfi_chemin").'"',
    'om_sig_flux.cache_gfi_couches as "'.__("cache_gfi_couches").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_sig_flux.om_collectivite as "'.__("om_collectivite").'"',
    );
//
$champRecherche = array(
    'om_sig_flux.om_sig_flux as "'.__("om_sig_flux").'"',
    'om_sig_flux.libelle as "'.__("libelle").'"',
    'om_sig_flux.id as "'.__("id").'"',
    'om_sig_flux.attribution as "'.__("attribution").'"',
    'om_sig_flux.chemin as "'.__("chemin").'"',
    'om_sig_flux.couches as "'.__("couches").'"',
    'om_sig_flux.cache_type as "'.__("cache_type").'"',
    'om_sig_flux.cache_gfi_chemin as "'.__("cache_gfi_chemin").'"',
    'om_sig_flux.cache_gfi_couches as "'.__("cache_gfi_couches").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_sig_flux.libelle ASC NULLS LAST";
$edition="om_sig_flux";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_sig_flux.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sig_flux.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sig_flux.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sig_flux.om_collectivite = ".intval($idxformulaire).") ";
    }
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_sig_map_flux',
);

