<?php
//$Id$ 
//gen openMairie le 18/02/2026 14:38

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("sejour_tarif");
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
$table = DB_PREFIXE."sejour_tarif";
// SELECT 
$champAffiche = array(
    'sejour_tarif.sejour_tarif as "'.__("sejour_tarif").'"',
    'sejour_tarif.libelle as "'.__("libelle").'"',
    'sejour_tarif.prix as "'.__("prix").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'sejour_tarif.sejour_tarif as "'.__("sejour_tarif").'"',
    'sejour_tarif.libelle as "'.__("libelle").'"',
    'sejour_tarif.prix as "'.__("prix").'"',
    );
$tri="ORDER BY sejour_tarif.libelle ASC NULLS LAST";
$edition="sejour_tarif";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

