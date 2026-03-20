<?php
//$Id$ 
//gen openMairie le 16/03/2026 10:16

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("medicament");
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
$table = DB_PREFIXE."medicament
    LEFT JOIN ".DB_PREFIXE."animal 
        ON medicament.animal=animal.animal 
    LEFT JOIN ".DB_PREFIXE."soin 
        ON medicament.soin=soin.soin 
    LEFT JOIN ".DB_PREFIXE."unite_mesure 
        ON medicament.unite_mesure=unite_mesure.unite_mesure ";
// SELECT 
$champAffiche = array(
    'medicament.medicament as "'.__("medicament").'"',
    'medicament.nom as "'.__("nom").'"',
    'to_char(medicament.date_debut ,\'DD/MM/YYYY\') as "'.__("date_debut").'"',
    'to_char(medicament.date_fin ,\'DD/MM/YYYY\') as "'.__("date_fin").'"',
    'medicament.dose as "'.__("dose").'"',
    'medicament.frequence as "'.__("frequence").'"',
    'unite_mesure.libelle as "'.__("unite_mesure").'"',
    'soin.date_soin as "'.__("soin").'"',
    'animal.nom as "'.__("animal").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'medicament.medicament as "'.__("medicament").'"',
    'medicament.nom as "'.__("nom").'"',
    'medicament.dose as "'.__("dose").'"',
    'medicament.frequence as "'.__("frequence").'"',
    'unite_mesure.libelle as "'.__("unite_mesure").'"',
    'soin.date_soin as "'.__("soin").'"',
    'animal.nom as "'.__("animal").'"',
    );
$tri="ORDER BY medicament.nom ASC NULLS LAST";
$edition="medicament";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal" => array("animal", ),
    "soin" => array("soin", ),
    "unite_mesure" => array("unite_mesure", ),
);
// Filtre listing sous formulaire - animal
if (in_array($retourformulaire, $foreign_keys_extended["animal"])) {
    $selection = " WHERE (medicament.animal = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - soin
if (in_array($retourformulaire, $foreign_keys_extended["soin"])) {
    $selection = " WHERE (medicament.soin = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - unite_mesure
if (in_array($retourformulaire, $foreign_keys_extended["unite_mesure"])) {
    $selection = " WHERE (medicament.unite_mesure = ".intval($idxformulaire).") ";
}

