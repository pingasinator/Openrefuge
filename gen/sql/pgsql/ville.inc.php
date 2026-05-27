<?php
//$Id$ 
//gen openMairie le 09/03/2026 15:39

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("ville");
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
$table = DB_PREFIXE."ville";
// SELECT 
$champAffiche = array(
    'ville.ville as "'.__("ville").'"',
    'ville.nom as "'.__("nom").'"',
    'ville.code_postal as "'.__("code_postal").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'ville.ville as "'.__("ville").'"',
    'ville.nom as "'.__("nom").'"',
    'ville.code_postal as "'.__("code_postal").'"',
    );
$tri="ORDER BY ville.nom ASC NULLS LAST";
$edition="ville";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

