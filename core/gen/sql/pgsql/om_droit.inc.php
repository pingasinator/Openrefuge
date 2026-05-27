<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("om_droit");
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
$table = DB_PREFIXE."om_droit
    LEFT JOIN ".DB_PREFIXE."om_profil 
        ON om_droit.om_profil=om_profil.om_profil ";
// SELECT 
$champAffiche = array(
    'om_droit.om_droit as "'.__("om_droit").'"',
    'om_droit.libelle as "'.__("libelle").'"',
    'om_profil.libelle as "'.__("om_profil").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_droit.om_droit as "'.__("om_droit").'"',
    'om_droit.libelle as "'.__("libelle").'"',
    'om_profil.libelle as "'.__("om_profil").'"',
    );
$tri="ORDER BY om_droit.libelle ASC NULLS LAST";
$edition="om_droit";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_profil" => array("om_profil", ),
);
// Filtre listing sous formulaire - om_profil
if (in_array($retourformulaire, $foreign_keys_extended["om_profil"])) {
    $selection = " WHERE (om_droit.om_profil = ".intval($idxformulaire).") ";
}

