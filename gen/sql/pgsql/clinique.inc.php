<?php
//$Id$ 
//gen openMairie le 27/04/2026 11:49

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("clinique");
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
$table = DB_PREFIXE."clinique
    LEFT JOIN ".DB_PREFIXE."rue 
        ON clinique.rue=rue.rue 
    LEFT JOIN ".DB_PREFIXE."ville 
        ON clinique.ville=ville.ville ";
// SELECT 
$champAffiche = array(
    'clinique.clinique as "'.__("clinique").'"',
    'clinique.nom as "'.__("nom").'"',
    'ville.nom as "'.__("ville").'"',
    'clinique.telephone as "'.__("telephone").'"',
    'clinique.num_rue as "'.__("num_rue").'"',
    'rue.nom as "'.__("rue").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'clinique.clinique as "'.__("clinique").'"',
    'clinique.nom as "'.__("nom").'"',
    'ville.nom as "'.__("ville").'"',
    'clinique.telephone as "'.__("telephone").'"',
    'clinique.num_rue as "'.__("num_rue").'"',
    'rue.nom as "'.__("rue").'"',
    );
$tri="ORDER BY clinique.nom ASC NULLS LAST";
$edition="clinique";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "rue" => array("rue", ),
    "ville" => array("ville", ),
);
// Filtre listing sous formulaire - rue
if (in_array($retourformulaire, $foreign_keys_extended["rue"])) {
    $selection = " WHERE (clinique.rue = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - ville
if (in_array($retourformulaire, $foreign_keys_extended["ville"])) {
    $selection = " WHERE (clinique.ville = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'soin',
    'veterinaire',
);

