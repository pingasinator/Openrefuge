<?php
//$Id$ 
//gen openMairie le 23/04/2026 10:00

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("rue");
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
$table = DB_PREFIXE."rue";
// SELECT 
$champAffiche = array(
    'rue.rue as "'.__("rue").'"',
    'rue.nom as "'.__("nom").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'rue.rue as "'.__("rue").'"',
    'rue.nom as "'.__("nom").'"',
    );
$tri="ORDER BY rue.nom ASC NULLS LAST";
$edition="rue";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'provenance',
);

