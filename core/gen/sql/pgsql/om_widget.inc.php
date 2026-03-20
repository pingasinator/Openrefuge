<?php
//$Id$ 
//gen openMairie le 14/05/2018 22:44

$DEBUG=0;
$serie=15;
$ent = __("administration")." -> ".__("tableaux de bord")." -> ".__("om_widget");
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
$table = DB_PREFIXE."om_widget";
// SELECT 
$champAffiche = array(
    'om_widget.om_widget as "'.__("om_widget").'"',
    'om_widget.libelle as "'.__("libelle").'"',
    'om_widget.type as "'.__("type").'"',
    );
//
$champNonAffiche = array(
    'om_widget.lien as "'.__("lien").'"',
    'om_widget.texte as "'.__("texte").'"',
    'om_widget.script as "'.__("script").'"',
    'om_widget.arguments as "'.__("arguments").'"',
    );
//
$champRecherche = array(
    'om_widget.om_widget as "'.__("om_widget").'"',
    'om_widget.libelle as "'.__("libelle").'"',
    'om_widget.type as "'.__("type").'"',
    );
$tri="ORDER BY om_widget.libelle ASC NULLS LAST";
$edition="om_widget";
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
);

