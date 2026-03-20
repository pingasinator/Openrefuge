<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("om_profil");
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
$table = DB_PREFIXE."om_profil";
// SELECT 
$champAffiche = array(
    'om_profil.om_profil as "'.__("om_profil").'"',
    'om_profil.libelle as "'.__("libelle").'"',
    'om_profil.hierarchie as "'.__("hierarchie").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_profil.om_profil as "'.__("om_profil").'"',
    'om_profil.libelle as "'.__("libelle").'"',
    'om_profil.hierarchie as "'.__("hierarchie").'"',
    );
$tri="ORDER BY om_profil.libelle ASC NULLS LAST";
$edition="om_profil";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_dashboard',
    'om_droit',
    'om_utilisateur',
);

