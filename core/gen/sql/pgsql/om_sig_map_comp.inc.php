<?php
//$Id$ 
//gen openMairie le 14/05/2018 22:44

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("SIG")." -> ".__("géométrie");
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
$table = DB_PREFIXE."om_sig_map_comp
    LEFT JOIN ".DB_PREFIXE."om_sig_map 
        ON om_sig_map_comp.om_sig_map=om_sig_map.om_sig_map ";
// SELECT 
$champAffiche = array(
    'om_sig_map_comp.om_sig_map_comp as "'.__("om_sig_map_comp").'"',
    'om_sig_map.libelle as "'.__("om_sig_map").'"',
    'om_sig_map_comp.libelle as "'.__("libelle").'"',
    'om_sig_map_comp.ordre as "'.__("ordre").'"',
    "case om_sig_map_comp.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    "case om_sig_map_comp.comp_maj when 't' then 'Oui' else 'Non' end as \"".__("comp_maj")."\"",
    'om_sig_map_comp.type_geometrie as "'.__("type_geometrie").'"',
    'om_sig_map_comp.comp_table_update as "'.__("comp_table_update").'"',
    'om_sig_map_comp.comp_champ as "'.__("comp_champ").'"',
    'om_sig_map_comp.comp_champ_idx as "'.__("comp_champ_idx").'"',
    'om_sig_map_comp.obj_class as "'.__("obj_class").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_sig_map_comp.om_sig_map_comp as "'.__("om_sig_map_comp").'"',
    'om_sig_map.libelle as "'.__("om_sig_map").'"',
    'om_sig_map_comp.libelle as "'.__("libelle").'"',
    'om_sig_map_comp.ordre as "'.__("ordre").'"',
    'om_sig_map_comp.type_geometrie as "'.__("type_geometrie").'"',
    'om_sig_map_comp.comp_table_update as "'.__("comp_table_update").'"',
    'om_sig_map_comp.comp_champ as "'.__("comp_champ").'"',
    'om_sig_map_comp.comp_champ_idx as "'.__("comp_champ_idx").'"',
    'om_sig_map_comp.obj_class as "'.__("obj_class").'"',
    );
$tri="ORDER BY om_sig_map_comp.libelle ASC NULLS LAST";
$edition="om_sig_map_comp";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_sig_map" => array("om_sig_map", ),
);
// Filtre listing sous formulaire - om_sig_map
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_map"])) {
    $selection = " WHERE (om_sig_map_comp.om_sig_map = ".intval($idxformulaire).") ";
}

