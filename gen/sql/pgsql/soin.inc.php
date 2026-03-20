<?php
//$Id$ 
//gen openMairie le 17/03/2026 09:06

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("soin");
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
$table = DB_PREFIXE."soin
    LEFT JOIN ".DB_PREFIXE."animal 
        ON soin.animal=animal.animal 
    LEFT JOIN ".DB_PREFIXE."clinique 
        ON soin.clinique=clinique.clinique 
    LEFT JOIN ".DB_PREFIXE."soin_type 
        ON soin.soin_type=soin_type.soin_type 
    LEFT JOIN ".DB_PREFIXE."veterinaire 
        ON soin.veterinaire=veterinaire.veterinaire ";
// SELECT 
$champAffiche = array(
    'soin.soin as "'.__("soin").'"',
    'to_char(soin.date_soin ,\'DD/MM/YYYY\') as "'.__("date_soin").'"',
    'soin.posologie as "'.__("posologie").'"',
    'veterinaire.nom as "'.__("veterinaire").'"',
    'animal.nom as "'.__("animal").'"',
    'clinique.nom as "'.__("clinique").'"',
    'soin_type.libelle as "'.__("soin_type").'"',
    'soin.tarif as "'.__("tarif").'"',
    );
//
$champNonAffiche = array(
    'soin.description as "'.__("description").'"',
    );
//
$champRecherche = array(
    'soin.soin as "'.__("soin").'"',
    'soin.posologie as "'.__("posologie").'"',
    'veterinaire.nom as "'.__("veterinaire").'"',
    'animal.nom as "'.__("animal").'"',
    'clinique.nom as "'.__("clinique").'"',
    'soin_type.libelle as "'.__("soin_type").'"',
    'soin.tarif as "'.__("tarif").'"',
    );
$tri="ORDER BY soin.date_soin ASC NULLS LAST";
$edition="soin";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal" => array("animal", ),
    "clinique" => array("clinique", ),
    "soin_type" => array("soin_type", ),
    "veterinaire" => array("veterinaire", ),
);
// Filtre listing sous formulaire - animal
if (in_array($retourformulaire, $foreign_keys_extended["animal"])) {
    $selection = " WHERE (soin.animal = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - clinique
if (in_array($retourformulaire, $foreign_keys_extended["clinique"])) {
    $selection = " WHERE (soin.clinique = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - soin_type
if (in_array($retourformulaire, $foreign_keys_extended["soin_type"])) {
    $selection = " WHERE (soin.soin_type = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - veterinaire
if (in_array($retourformulaire, $foreign_keys_extended["veterinaire"])) {
    $selection = " WHERE (soin.veterinaire = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'facture_soin',
    'medicament',
);

