<?php
//$Id$ 
//gen openMairie le 28/04/2026 10:26

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("test");
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
$table = DB_PREFIXE."test";
// SELECT 
$champAffiche = array(
    'test.test as "'.__("test").'"',
    'test.mon_int as "'.__("mon_int").'"',
    'test.mon_varchar as "'.__("mon_varchar").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'test.test as "'.__("test").'"',
    'test.mon_int as "'.__("mon_int").'"',
    'test.mon_varchar as "'.__("mon_varchar").'"',
    );
$tri="ORDER BY test.mon_int ASC NULLS LAST";
$edition="test";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

