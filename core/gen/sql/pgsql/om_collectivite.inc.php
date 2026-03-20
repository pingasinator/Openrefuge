<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("om_collectivite");
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
$table = DB_PREFIXE."om_collectivite";
// SELECT 
$champAffiche = array(
    'om_collectivite.om_collectivite as "'.__("om_collectivite").'"',
    'om_collectivite.libelle as "'.__("libelle").'"',
    'om_collectivite.niveau as "'.__("niveau").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_collectivite.om_collectivite as "'.__("om_collectivite").'"',
    'om_collectivite.libelle as "'.__("libelle").'"',
    'om_collectivite.niveau as "'.__("niveau").'"',
    );
$tri="ORDER BY om_collectivite.libelle ASC NULLS LAST";
$edition="om_collectivite";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_collectivite.om_collectivite = '".$_SESSION["collectivite"]."') ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_etat',
    'om_lettretype',
    'om_logo',
    'om_parametre',
    'om_sig_flux',
    'om_sig_map',
    'om_sousetat',
    'om_utilisateur',
);

