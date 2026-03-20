<?php
//$Id$ 
//gen openMairie le 14/05/2018 22:44

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("tableaux de bord")." -> ".__("om_dashboard");
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
$table = DB_PREFIXE."om_dashboard
    LEFT JOIN ".DB_PREFIXE."om_profil 
        ON om_dashboard.om_profil=om_profil.om_profil 
    LEFT JOIN ".DB_PREFIXE."om_widget 
        ON om_dashboard.om_widget=om_widget.om_widget ";
// SELECT 
$champAffiche = array(
    'om_dashboard.om_dashboard as "'.__("om_dashboard").'"',
    'om_profil.libelle as "'.__("om_profil").'"',
    'om_dashboard.bloc as "'.__("bloc").'"',
    'om_dashboard.position as "'.__("position").'"',
    'om_widget.libelle as "'.__("om_widget").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_dashboard.om_dashboard as "'.__("om_dashboard").'"',
    'om_profil.libelle as "'.__("om_profil").'"',
    'om_dashboard.bloc as "'.__("bloc").'"',
    'om_dashboard.position as "'.__("position").'"',
    'om_widget.libelle as "'.__("om_widget").'"',
    );
$tri="ORDER BY om_profil.libelle ASC NULLS LAST";
$edition="om_dashboard";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_profil" => array("om_profil", ),
    "om_widget" => array("om_widget", ),
);
// Filtre listing sous formulaire - om_profil
if (in_array($retourformulaire, $foreign_keys_extended["om_profil"])) {
    $selection = " WHERE (om_dashboard.om_profil = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - om_widget
if (in_array($retourformulaire, $foreign_keys_extended["om_widget"])) {
    $selection = " WHERE (om_dashboard.om_widget = ".intval($idxformulaire).") ";
}

