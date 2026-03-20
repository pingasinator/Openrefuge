<?php
//$Id$ 
//gen openMairie le 14/05/2018 22:44

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("SIG")." -> ".__("carte");
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
$table = DB_PREFIXE."om_sig_map
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_sig_map.om_collectivite=om_collectivite.om_collectivite 
    LEFT JOIN ".DB_PREFIXE."om_sig_extent 
        ON om_sig_map.om_sig_extent=om_sig_extent.om_sig_extent 
    LEFT JOIN ".DB_PREFIXE."om_sig_map 
        ON om_sig_map.source_flux=om_sig_map.om_sig_map ";
// SELECT 
$champAffiche = array(
    'om_sig_map.om_sig_map as "'.__("om_sig_map").'"',
    'om_sig_map.id as "'.__("id").'"',
    'om_sig_map.libelle as "'.__("libelle").'"',
    "case om_sig_map.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    'om_sig_map.zoom as "'.__("zoom").'"',
    "case om_sig_map.fond_osm when 't' then 'Oui' else 'Non' end as \"".__("fond_osm")."\"",
    "case om_sig_map.fond_bing when 't' then 'Oui' else 'Non' end as \"".__("fond_bing")."\"",
    "case om_sig_map.fond_sat when 't' then 'Oui' else 'Non' end as \"".__("fond_sat")."\"",
    "case om_sig_map.layer_info when 't' then 'Oui' else 'Non' end as \"".__("layer_info")."\"",
    'om_sig_map.projection_externe as "'.__("projection_externe").'"',
    'om_sig_map.retour as "'.__("retour").'"',
    "case om_sig_map.util_idx when 't' then 'Oui' else 'Non' end as \"".__("util_idx")."\"",
    "case om_sig_map.util_reqmo when 't' then 'Oui' else 'Non' end as \"".__("util_reqmo")."\"",
    "case om_sig_map.util_recherche when 't' then 'Oui' else 'Non' end as \"".__("util_recherche")."\"",
    'om_sig_map.libelle as "'.__("source_flux").'"',
    'om_sig_map.fond_default as "'.__("fond_default").'"',
    'om_sig_extent.nom as "'.__("om_sig_extent").'"',
    "case om_sig_map.restrict_extent when 't' then 'Oui' else 'Non' end as \"".__("restrict_extent")."\"",
    'om_sig_map.sld_marqueur as "'.__("sld_marqueur").'"',
    'om_sig_map.sld_data as "'.__("sld_data").'"',
    'om_sig_map.point_centrage as "'.__("point_centrage").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_sig_map.om_collectivite as "'.__("om_collectivite").'"',
    'om_sig_map.url as "'.__("url").'"',
    'om_sig_map.om_sql as "'.__("om_sql").'"',
    );
//
$champRecherche = array(
    'om_sig_map.om_sig_map as "'.__("om_sig_map").'"',
    'om_sig_map.id as "'.__("id").'"',
    'om_sig_map.libelle as "'.__("libelle").'"',
    'om_sig_map.zoom as "'.__("zoom").'"',
    'om_sig_map.projection_externe as "'.__("projection_externe").'"',
    'om_sig_map.retour as "'.__("retour").'"',
    'om_sig_map.libelle as "'.__("source_flux").'"',
    'om_sig_map.fond_default as "'.__("fond_default").'"',
    'om_sig_extent.nom as "'.__("om_sig_extent").'"',
    'om_sig_map.sld_marqueur as "'.__("sld_marqueur").'"',
    'om_sig_map.sld_data as "'.__("sld_data").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_sig_map.libelle ASC NULLS LAST";
$edition="om_sig_map";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_sig_map.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
    "om_sig_extent" => array("om_sig_extent", ),
    "om_sig_map" => array("om_sig_map", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sig_map.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sig_map.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sig_map.om_collectivite = ".intval($idxformulaire).") ";
    }
}
// Filtre listing sous formulaire - om_sig_extent
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_extent"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sig_map.om_sig_extent = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sig_map.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sig_map.om_sig_extent = ".intval($idxformulaire).") ";
    }
}
// Filtre listing sous formulaire - om_sig_map
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_map"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sig_map.source_flux = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sig_map.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sig_map.source_flux = ".intval($idxformulaire).") ";
    }
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_sig_map',
    'om_sig_map_comp',
    'om_sig_map_flux',
);

