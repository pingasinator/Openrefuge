<?php
//$Id$ 
//gen openMairie le 14/05/2018 22:44

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("SIG")." -> ".__("étendue");
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
$table = DB_PREFIXE."om_sig_extent";
// SELECT 
$champAffiche = array(
    'om_sig_extent.om_sig_extent as "'.__("om_sig_extent").'"',
    'om_sig_extent.nom as "'.__("nom").'"',
    'om_sig_extent.extent as "'.__("extent").'"',
    "case om_sig_extent.valide when 't' then 'Oui' else 'Non' end as \"".__("valide")."\"",
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_sig_extent.om_sig_extent as "'.__("om_sig_extent").'"',
    'om_sig_extent.nom as "'.__("nom").'"',
    'om_sig_extent.extent as "'.__("extent").'"',
    );
$tri="ORDER BY om_sig_extent.nom ASC NULLS LAST";
$edition="om_sig_extent";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_sig_map',
);

