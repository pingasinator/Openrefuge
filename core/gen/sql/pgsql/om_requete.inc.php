<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("parametrage")." -> ".__("om_requete");
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
$table = DB_PREFIXE."om_requete";
// SELECT 
$champAffiche = array(
    'om_requete.om_requete as "'.__("om_requete").'"',
    'om_requete.code as "'.__("code").'"',
    'om_requete.libelle as "'.__("libelle").'"',
    'om_requete.description as "'.__("description").'"',
    'om_requete.type as "'.__("type").'"',
    'om_requete.classe as "'.__("classe").'"',
    'om_requete.methode as "'.__("methode").'"',
    );
//
$champNonAffiche = array(
    'om_requete.requete as "'.__("requete").'"',
    'om_requete.merge_fields as "'.__("merge_fields").'"',
    );
//
$champRecherche = array(
    'om_requete.om_requete as "'.__("om_requete").'"',
    'om_requete.code as "'.__("code").'"',
    'om_requete.libelle as "'.__("libelle").'"',
    'om_requete.description as "'.__("description").'"',
    'om_requete.type as "'.__("type").'"',
    'om_requete.classe as "'.__("classe").'"',
    'om_requete.methode as "'.__("methode").'"',
    );
$tri="ORDER BY om_requete.libelle ASC NULLS LAST";
$edition="om_requete";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_etat',
    'om_lettretype',
);

