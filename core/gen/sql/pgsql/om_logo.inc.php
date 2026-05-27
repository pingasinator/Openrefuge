<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("parametrage")." -> ".__("om_logo");
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
$table = DB_PREFIXE."om_logo
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_logo.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_logo.om_logo as "'.__("om_logo").'"',
    'om_logo.id as "'.__("id").'"',
    'om_logo.libelle as "'.__("libelle").'"',
    'om_logo.description as "'.__("description").'"',
    'om_logo.fichier as "'.__("fichier").'"',
    'om_logo.resolution as "'.__("resolution").'"',
    "case om_logo.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_logo.om_collectivite as "'.__("om_collectivite").'"',
    );
//
$champRecherche = array(
    'om_logo.om_logo as "'.__("om_logo").'"',
    'om_logo.id as "'.__("id").'"',
    'om_logo.libelle as "'.__("libelle").'"',
    'om_logo.description as "'.__("description").'"',
    'om_logo.fichier as "'.__("fichier").'"',
    'om_logo.resolution as "'.__("resolution").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_logo.libelle ASC NULLS LAST";
$edition="om_logo";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_logo.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_logo.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_logo.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_logo.om_collectivite = ".intval($idxformulaire).") ";
    }
}

