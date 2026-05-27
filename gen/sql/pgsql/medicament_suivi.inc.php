<?php
//$Id$ 
//gen openMairie le 05/05/2026 16:17

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("medicament_suivi");
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
$table = DB_PREFIXE."medicament_suivi
    LEFT JOIN ".DB_PREFIXE."animal 
        ON medicament_suivi.animal=animal.animal 
    LEFT JOIN ".DB_PREFIXE."medicament 
        ON medicament_suivi.medicament=medicament.medicament ";
// SELECT 
$champAffiche = array(
    'medicament_suivi.medicament_suivi as "'.__("medicament_suivi").'"',
    'medicament.nom as "'.__("medicament").'"',
    'animal.nom as "'.__("animal").'"',
    'to_char(medicament_suivi.date ,\'DD/MM/YYYY\') as "'.__("date").'"',
    'medicament_suivi.heure as "'.__("heure").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'medicament_suivi.medicament_suivi as "'.__("medicament_suivi").'"',
    'medicament.nom as "'.__("medicament").'"',
    'animal.nom as "'.__("animal").'"',
    );
$tri="ORDER BY medicament.nom ASC NULLS LAST";
$edition="medicament_suivi";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal" => array("animal", ),
    "medicament" => array("medicament", ),
);
// Filtre listing sous formulaire - animal
if (in_array($retourformulaire, $foreign_keys_extended["animal"])) {
    $selection = " WHERE (medicament_suivi.animal = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - medicament
if (in_array($retourformulaire, $foreign_keys_extended["medicament"])) {
    $selection = " WHERE (medicament_suivi.medicament = ".intval($idxformulaire).") ";
}

