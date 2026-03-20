<?php
//$Id$ 
//gen openMairie le 14/05/2018 22:44

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("SIG")." -> ".__("flux appliqué à une carte");
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
$table = DB_PREFIXE."om_sig_map_flux
    LEFT JOIN ".DB_PREFIXE."om_sig_flux 
        ON om_sig_map_flux.om_sig_flux=om_sig_flux.om_sig_flux 
    LEFT JOIN ".DB_PREFIXE."om_sig_map 
        ON om_sig_map_flux.om_sig_map=om_sig_map.om_sig_map ";
// SELECT 
$champAffiche = array(
    'om_sig_map_flux.om_sig_map_flux as "'.__("om_sig_map_flux").'"',
    'om_sig_flux.libelle as "'.__("om_sig_flux").'"',
    'om_sig_map.libelle as "'.__("om_sig_map").'"',
    'om_sig_map_flux.ol_map as "'.__("ol_map").'"',
    'om_sig_map_flux.ordre as "'.__("ordre").'"',
    "case om_sig_map_flux.visibility when 't' then 'Oui' else 'Non' end as \"".__("visibility")."\"",
    "case om_sig_map_flux.panier when 't' then 'Oui' else 'Non' end as \"".__("panier")."\"",
    'om_sig_map_flux.pa_nom as "'.__("pa_nom").'"',
    'om_sig_map_flux.pa_layer as "'.__("pa_layer").'"',
    'om_sig_map_flux.pa_attribut as "'.__("pa_attribut").'"',
    'om_sig_map_flux.pa_encaps as "'.__("pa_encaps").'"',
    'om_sig_map_flux.pa_type_geometrie as "'.__("pa_type_geometrie").'"',
    "case om_sig_map_flux.baselayer when 't' then 'Oui' else 'Non' end as \"".__("baselayer")."\"",
    "case om_sig_map_flux.singletile when 't' then 'Oui' else 'Non' end as \"".__("singletile")."\"",
    'om_sig_map_flux.maxzoomlevel as "'.__("maxzoomlevel").'"',
    );
//
$champNonAffiche = array(
    'om_sig_map_flux.pa_sql as "'.__("pa_sql").'"',
    'om_sig_map_flux.sql_filter as "'.__("sql_filter").'"',
    );
//
$champRecherche = array(
    'om_sig_map_flux.om_sig_map_flux as "'.__("om_sig_map_flux").'"',
    'om_sig_flux.libelle as "'.__("om_sig_flux").'"',
    'om_sig_map.libelle as "'.__("om_sig_map").'"',
    'om_sig_map_flux.ol_map as "'.__("ol_map").'"',
    'om_sig_map_flux.ordre as "'.__("ordre").'"',
    'om_sig_map_flux.pa_nom as "'.__("pa_nom").'"',
    'om_sig_map_flux.pa_layer as "'.__("pa_layer").'"',
    'om_sig_map_flux.pa_attribut as "'.__("pa_attribut").'"',
    'om_sig_map_flux.pa_encaps as "'.__("pa_encaps").'"',
    'om_sig_map_flux.pa_type_geometrie as "'.__("pa_type_geometrie").'"',
    'om_sig_map_flux.maxzoomlevel as "'.__("maxzoomlevel").'"',
    );
$tri="ORDER BY om_sig_flux.libelle ASC NULLS LAST";
$edition="om_sig_map_flux";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_sig_flux" => array("om_sig_flux", ),
    "om_sig_map" => array("om_sig_map", ),
);
// Filtre listing sous formulaire - om_sig_flux
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_flux"])) {
    $selection = " WHERE (om_sig_map_flux.om_sig_flux = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - om_sig_map
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_map"])) {
    $selection = " WHERE (om_sig_map_flux.om_sig_map = ".intval($idxformulaire).") ";
}

