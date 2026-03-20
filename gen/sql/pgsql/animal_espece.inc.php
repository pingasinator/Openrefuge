<?php
//$Id$ 
//gen openMairie le 12/03/2026 15:34

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("animal_espece");
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
$table = DB_PREFIXE."animal_espece";
// SELECT 
$champAffiche = array(
    'animal_espece.animal_espece as "'.__("animal_espece").'"',
    'animal_espece.nom as "'.__("nom").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'animal_espece.animal_espece as "'.__("animal_espece").'"',
    'animal_espece.nom as "'.__("nom").'"',
    );
$tri="ORDER BY animal_espece.nom ASC NULLS LAST";
$edition="animal_espece";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'animal',
    'animal_race',
);

