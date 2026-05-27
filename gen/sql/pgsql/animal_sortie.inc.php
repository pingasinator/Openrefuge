<?php
//$Id$ 
//gen openMairie le 23/04/2026 10:41

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("animal_sortie");
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
$table = DB_PREFIXE."animal_sortie
    LEFT JOIN ".DB_PREFIXE."animal 
        ON animal_sortie.animal=animal.animal 
    LEFT JOIN ".DB_PREFIXE."cause_mort 
        ON animal_sortie.cause_mort=cause_mort.cause_mort ";
// SELECT 
$champAffiche = array(
    'animal_sortie.animal_sortie as "'.__("animal_sortie").'"',
    'to_char(animal_sortie.date_sortie ,\'DD/MM/YYYY\') as "'.__("date_sortie").'"',
    'animal.nom as "'.__("animal").'"',
    'cause_mort.libelle as "'.__("cause_mort").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'animal_sortie.animal_sortie as "'.__("animal_sortie").'"',
    'animal.nom as "'.__("animal").'"',
    'cause_mort.libelle as "'.__("cause_mort").'"',
    );
$tri="ORDER BY animal_sortie.date_sortie ASC NULLS LAST";
$edition="animal_sortie";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal" => array("animal", ),
    "cause_mort" => array("cause_mort", ),
);
// Filtre listing sous formulaire - animal
if (in_array($retourformulaire, $foreign_keys_extended["animal"])) {
    $selection = " WHERE (animal_sortie.animal = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - cause_mort
if (in_array($retourformulaire, $foreign_keys_extended["cause_mort"])) {
    $selection = " WHERE (animal_sortie.cause_mort = ".intval($idxformulaire).") ";
}

