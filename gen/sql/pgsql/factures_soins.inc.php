<?php
//$Id$ 
//gen openMairie le 20/02/2026 10:21

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("factures_soins");
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
$table = DB_PREFIXE."factures_soins
    LEFT JOIN ".DB_PREFIXE."animale 
        ON factures_soins.animale=animale.animale 
    LEFT JOIN ".DB_PREFIXE."clinique 
        ON factures_soins.clinique=clinique.clinique 
    LEFT JOIN ".DB_PREFIXE."factures 
        ON factures_soins.factures=factures.factures 
    LEFT JOIN ".DB_PREFIXE."soins 
        ON factures_soins.soins=soins.soins 
    LEFT JOIN ".DB_PREFIXE."veterinaire 
        ON factures_soins.veterinaire=veterinaire.veterinaire ";
// SELECT 
$champAffiche = array(
    'factures_soins.factures_soins as "'.__("factures_soins").'"',
    'factures.personne as "'.__("factures").'"',
    'animale.nom as "'.__("animale").'"',
    'clinique.ville as "'.__("clinique").'"',
    'veterinaire.nom as "'.__("veterinaire").'"',
    'to_char(factures_soins.date_soin ,\'DD/MM/YYYY\') as "'.__("date_soin").'"',
    'factures_soins.tarifs as "'.__("tarifs").'"',
    'soins.posologie as "'.__("soins").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'factures_soins.factures_soins as "'.__("factures_soins").'"',
    'factures.personne as "'.__("factures").'"',
    'animale.nom as "'.__("animale").'"',
    'clinique.ville as "'.__("clinique").'"',
    'veterinaire.nom as "'.__("veterinaire").'"',
    'factures_soins.tarifs as "'.__("tarifs").'"',
    'soins.posologie as "'.__("soins").'"',
    );
$tri="ORDER BY factures.personne ASC NULLS LAST";
$edition="factures_soins";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animale" => array("animale", ),
    "clinique" => array("clinique", ),
    "factures" => array("factures", ),
    "soins" => array("soins", ),
    "veterinaire" => array("veterinaire", ),
);
// Filtre listing sous formulaire - animale
if (in_array($retourformulaire, $foreign_keys_extended["animale"])) {
    $selection = " WHERE (factures_soins.animale = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - clinique
if (in_array($retourformulaire, $foreign_keys_extended["clinique"])) {
    $selection = " WHERE (factures_soins.clinique = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - factures
if (in_array($retourformulaire, $foreign_keys_extended["factures"])) {
    $selection = " WHERE (factures_soins.factures = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - soins
if (in_array($retourformulaire, $foreign_keys_extended["soins"])) {
    $selection = " WHERE (factures_soins.soins = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - veterinaire
if (in_array($retourformulaire, $foreign_keys_extended["veterinaire"])) {
    $selection = " WHERE (factures_soins.veterinaire = ".intval($idxformulaire).") ";
}

