<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("om_parametre");
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
$table = DB_PREFIXE."om_parametre
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_parametre.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_parametre.om_parametre as "'.__("om_parametre").'"',
    'om_parametre.libelle as "'.__("libelle").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_parametre.valeur as "'.__("valeur").'"',
    'om_parametre.om_collectivite as "'.__("om_collectivite").'"',
    );
//
$champRecherche = array(
    'om_parametre.om_parametre as "'.__("om_parametre").'"',
    'om_parametre.libelle as "'.__("libelle").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_parametre.libelle ASC NULLS LAST";
$edition="om_parametre";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_parametre.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_parametre.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_parametre.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_parametre.om_collectivite = ".intval($idxformulaire).") ";
    }
}

