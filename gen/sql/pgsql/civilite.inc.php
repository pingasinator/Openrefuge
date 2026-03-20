<?php
//$Id$ 
//gen openMairie le 20/02/2026 10:17

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("civilite");
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
$table = DB_PREFIXE."civilite";
// SELECT 
$champAffiche = array(
    'civilite.civilite as "'.__("civilite").'"',
    'civilite.libelle as "'.__("libelle").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'civilite.civilite as "'.__("civilite").'"',
    'civilite.libelle as "'.__("libelle").'"',
    );
$tri="ORDER BY civilite.libelle ASC NULLS LAST";
$edition="civilite";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'personne',
    'veterinaire',
);

