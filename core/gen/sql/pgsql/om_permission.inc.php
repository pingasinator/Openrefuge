<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("om_permission");
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
$table = DB_PREFIXE."om_permission";
// SELECT 
$champAffiche = array(
    'om_permission.om_permission as "'.__("om_permission").'"',
    'om_permission.libelle as "'.__("libelle").'"',
    'om_permission.type as "'.__("type").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_permission.om_permission as "'.__("om_permission").'"',
    'om_permission.libelle as "'.__("libelle").'"',
    'om_permission.type as "'.__("type").'"',
    );
$tri="ORDER BY om_permission.libelle ASC NULLS LAST";
$edition="om_permission";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

