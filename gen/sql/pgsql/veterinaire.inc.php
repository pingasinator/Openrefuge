<?php
//$Id$ 
//gen openMairie le 16/03/2026 10:29

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("veterinaire");
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
$table = DB_PREFIXE."veterinaire
    LEFT JOIN ".DB_PREFIXE."civilite 
        ON veterinaire.civilite=civilite.civilite 
    LEFT JOIN ".DB_PREFIXE."clinique 
        ON veterinaire.clinique=clinique.clinique ";
// SELECT 
$champAffiche = array(
    'veterinaire.veterinaire as "'.__("veterinaire").'"',
    'veterinaire.nom as "'.__("nom").'"',
    'veterinaire.prenom as "'.__("prenom").'"',
    'veterinaire.telephone as "'.__("telephone").'"',
    'clinique.nom as "'.__("clinique").'"',
    'civilite.libelle as "'.__("civilite").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'veterinaire.veterinaire as "'.__("veterinaire").'"',
    'veterinaire.nom as "'.__("nom").'"',
    'veterinaire.prenom as "'.__("prenom").'"',
    'veterinaire.telephone as "'.__("telephone").'"',
    'clinique.nom as "'.__("clinique").'"',
    'civilite.libelle as "'.__("civilite").'"',
    );
$tri="ORDER BY veterinaire.nom ASC NULLS LAST";
$edition="veterinaire";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "civilite" => array("civilite", ),
    "clinique" => array("clinique", ),
);
// Filtre listing sous formulaire - civilite
if (in_array($retourformulaire, $foreign_keys_extended["civilite"])) {
    $selection = " WHERE (veterinaire.civilite = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - clinique
if (in_array($retourformulaire, $foreign_keys_extended["clinique"])) {
    $selection = " WHERE (veterinaire.clinique = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'facture_soin',
    'soin',
);

