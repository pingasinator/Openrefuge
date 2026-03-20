<?php
//$Id$ 
//gen openMairie le 16/03/2026 10:09

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("hebergement");
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
$table = DB_PREFIXE."hebergement
    LEFT JOIN ".DB_PREFIXE."hebergement_type 
        ON hebergement.hebergement_type=hebergement_type.hebergement_type 
    LEFT JOIN ".DB_PREFIXE."ville 
        ON hebergement.ville=ville.ville ";
// SELECT 
$champAffiche = array(
    'hebergement.hebergement as "'.__("hebergement").'"',
    'hebergement.nom as "'.__("nom").'"',
    'hebergement.adresse as "'.__("adresse").'"',
    'ville.nom as "'.__("ville").'"',
    'hebergement.telephone as "'.__("telephone").'"',
    'hebergement_type.libelle as "'.__("hebergement_type").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'hebergement.hebergement as "'.__("hebergement").'"',
    'hebergement.nom as "'.__("nom").'"',
    'hebergement.adresse as "'.__("adresse").'"',
    'ville.nom as "'.__("ville").'"',
    'hebergement.telephone as "'.__("telephone").'"',
    'hebergement_type.libelle as "'.__("hebergement_type").'"',
    );
$tri="ORDER BY hebergement.nom ASC NULLS LAST";
$edition="hebergement";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "hebergement_type" => array("hebergement_type", ),
    "ville" => array("ville", ),
);
// Filtre listing sous formulaire - hebergement_type
if (in_array($retourformulaire, $foreign_keys_extended["hebergement_type"])) {
    $selection = " WHERE (hebergement.hebergement_type = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - ville
if (in_array($retourformulaire, $foreign_keys_extended["ville"])) {
    $selection = " WHERE (hebergement.ville = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'facture_sejour',
    'sejour',
);

