<?php
//$Id$ 
//gen openMairie le 16/03/2026 15:55

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
    LEFT JOIN ".DB_PREFIXE."ville 
        ON clinique.ville=ville.ville ";
// SELECT 
$champAffiche = array(
    'clinique.clinique as "'.__("clinique").'"',
    'clinique.nom as "'.__("nom").'"',
    'clinique.adresse as "'.__("adresse").'"',
    'ville.nom as "'.__("ville").'"',
    'clinique.telephone as "'.__("telephone").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'clinique.clinique as "'.__("clinique").'"',
    'clinique.nom as "'.__("nom").'"',
    'clinique.adresse as "'.__("adresse").'"',
    'ville.nom as "'.__("ville").'"',
    'clinique.telephone as "'.__("telephone").'"',
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
    "ville" => array("ville", ),
);
// Filtre listing sous formulaire - ville
if (in_array($retourformulaire, $foreign_keys_extended["ville"])) {
    $selection = " WHERE (clinique.ville = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'facture_soin',
    'soin',
    'veterinaire',
);

