<?php
//$Id$ 
//gen openMairie le 19/02/2026 09:14

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("factures_sejours");
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
$table = DB_PREFIXE."factures_sejours
    LEFT JOIN ".DB_PREFIXE."animale 
        ON factures_sejours.animale=animale.animale 
    LEFT JOIN ".DB_PREFIXE."factures 
        ON factures_sejours.factures=factures.factures 
    LEFT JOIN ".DB_PREFIXE."hebergement 
        ON factures_sejours.hebergement=hebergement.hebergement 
    LEFT JOIN ".DB_PREFIXE."provenance 
        ON factures_sejours.provenance=provenance.provenance 
    LEFT JOIN ".DB_PREFIXE."sejours 
        ON factures_sejours.sejours=sejours.sejours ";
// SELECT 
$champAffiche = array(
    'factures_sejours.factures_sejours as "'.__("factures_sejours").'"',
    'factures.personne as "'.__("factures").'"',
    'sejours.date_d_entree as "'.__("sejours").'"',
    'to_char(factures_sejours.date_d_entree ,\'DD/MM/YYYY\') as "'.__("date_d_entree").'"',
    'to_char(factures_sejours.date_de_sortie ,\'DD/MM/YYYY\') as "'.__("date_de_sortie").'"',
    'factures_sejours.payee as "'.__("payee").'"',
    'animale.nom as "'.__("animale").'"',
    'provenance.provenance as "'.__("provenance").'"',
    'hebergement.adresse as "'.__("hebergement").'"',
    'factures_sejours.tarif as "'.__("tarif").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'factures_sejours.factures_sejours as "'.__("factures_sejours").'"',
    'factures.personne as "'.__("factures").'"',
    'sejours.date_d_entree as "'.__("sejours").'"',
    'factures_sejours.payee as "'.__("payee").'"',
    'animale.nom as "'.__("animale").'"',
    'provenance.provenance as "'.__("provenance").'"',
    'hebergement.adresse as "'.__("hebergement").'"',
    'factures_sejours.tarif as "'.__("tarif").'"',
    );
$tri="ORDER BY factures.personne ASC NULLS LAST";
$edition="factures_sejours";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animale" => array("animale", ),
    "factures" => array("factures", ),
    "hebergement" => array("hebergement", ),
    "provenance" => array("provenance", ),
    "sejours" => array("sejours", ),
);
// Filtre listing sous formulaire - animale
if (in_array($retourformulaire, $foreign_keys_extended["animale"])) {
    $selection = " WHERE (factures_sejours.animale = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - factures
if (in_array($retourformulaire, $foreign_keys_extended["factures"])) {
    $selection = " WHERE (factures_sejours.factures = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - hebergement
if (in_array($retourformulaire, $foreign_keys_extended["hebergement"])) {
    $selection = " WHERE (factures_sejours.hebergement = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - provenance
if (in_array($retourformulaire, $foreign_keys_extended["provenance"])) {
    $selection = " WHERE (factures_sejours.provenance = '".$f->db->escapeSimple($idxformulaire)."') ";
}
// Filtre listing sous formulaire - sejours
if (in_array($retourformulaire, $foreign_keys_extended["sejours"])) {
    $selection = " WHERE (factures_sejours.sejours = ".intval($idxformulaire).") ";
}

