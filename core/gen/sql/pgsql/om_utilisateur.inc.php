<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("om_utilisateur");
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
$table = DB_PREFIXE."om_utilisateur
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_utilisateur.om_collectivite=om_collectivite.om_collectivite 
    LEFT JOIN ".DB_PREFIXE."om_profil 
        ON om_utilisateur.om_profil=om_profil.om_profil ";
// SELECT 
$champAffiche = array(
    'om_utilisateur.om_utilisateur as "'.__("om_utilisateur").'"',
    'om_utilisateur.nom as "'.__("nom").'"',
    'om_utilisateur.email as "'.__("email").'"',
    'om_utilisateur.login as "'.__("login").'"',
    'om_profil.libelle as "'.__("om_profil").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_utilisateur.pwd as "'.__("pwd").'"',
    'om_utilisateur.om_collectivite as "'.__("om_collectivite").'"',
    'om_utilisateur.om_type as "'.__("om_type").'"',
    );
//
$champRecherche = array(
    'om_utilisateur.om_utilisateur as "'.__("om_utilisateur").'"',
    'om_utilisateur.nom as "'.__("nom").'"',
    'om_utilisateur.email as "'.__("email").'"',
    'om_utilisateur.login as "'.__("login").'"',
    'om_profil.libelle as "'.__("om_profil").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_utilisateur.nom ASC NULLS LAST";
$edition="om_utilisateur";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_utilisateur.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
    "om_profil" => array("om_profil", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_utilisateur.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_utilisateur.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_utilisateur.om_collectivite = ".intval($idxformulaire).") ";
    }
}
// Filtre listing sous formulaire - om_profil
if (in_array($retourformulaire, $foreign_keys_extended["om_profil"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_utilisateur.om_profil = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_utilisateur.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_utilisateur.om_profil = ".intval($idxformulaire).") ";
    }
}

