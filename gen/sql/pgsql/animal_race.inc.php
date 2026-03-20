<?php
//$Id$ 
//gen openMairie le 12/03/2026 15:55

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("animal_race");
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
$table = DB_PREFIXE."animal_race
    LEFT JOIN ".DB_PREFIXE."animal_espece 
        ON animal_race.animal_espece=animal_espece.animal_espece ";
// SELECT 
$champAffiche = array(
    'animal_race.animal_race as "'.__("animal_race").'"',
    'animal_race.nom as "'.__("nom").'"',
    'animal_espece.nom as "'.__("animal_espece").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'animal_race.animal_race as "'.__("animal_race").'"',
    'animal_race.nom as "'.__("nom").'"',
    'animal_espece.nom as "'.__("animal_espece").'"',
    );
$tri="ORDER BY animal_race.nom ASC NULLS LAST";
$edition="animal_race";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal_espece" => array("animal_espece", ),
);
// Filtre listing sous formulaire - animal_espece
if (in_array($retourformulaire, $foreign_keys_extended["animal_espece"])) {
    $selection = " WHERE (animal_race.animal_espece = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'animal',
);

