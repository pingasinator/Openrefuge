<?php
//$Id$ 
//gen openMairie le 30/04/2026 14:27

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("animal_entree");
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
$table = DB_PREFIXE."animal_entree
    LEFT JOIN ".DB_PREFIXE."animal 
        ON animal_entree.animal=animal.animal ";
// SELECT 
$champAffiche = array(
    'animal_entree.animal_entree as "'.__("animal_entree").'"',
    'to_char(animal_entree.date_entree ,\'DD/MM/YYYY\') as "'.__("date_entree").'"',
    'animal.nom as "'.__("animal").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'animal_entree.animal_entree as "'.__("animal_entree").'"',
    'animal.nom as "'.__("animal").'"',
    );
$tri="ORDER BY animal_entree.date_entree ASC NULLS LAST";
$edition="animal_entree";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal" => array("animal", ),
);
// Filtre listing sous formulaire - animal
if (in_array($retourformulaire, $foreign_keys_extended["animal"])) {
    $selection = " WHERE (animal_entree.animal = ".intval($idxformulaire).") ";
}

