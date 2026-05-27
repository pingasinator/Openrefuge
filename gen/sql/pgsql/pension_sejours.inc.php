<?php
//$Id$ 
//gen openMairie le 03/12/2025 15:43

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("pension_sejours");
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
$table = DB_PREFIXE."pension_sejours
    LEFT JOIN ".DB_PREFIXE."animale 
        ON pension_sejours.animale=animale.animale 
    LEFT JOIN ".DB_PREFIXE."pension 
        ON pension_sejours.pension=pension.pension ";
// SELECT 
$champAffiche = array(
    'pension_sejours.pension_sejours as "'.__("pension_sejours").'"',
    'to_char(pension_sejours.date_entrée ,\'DD/MM/YYYY\') as "'.__("date_entrée").'"',
    'to_char(pension_sejours.date_sortie ,\'DD/MM/YYYY\') as "'.__("date_sortie").'"',
    'pension_sejours.nb_jours as "'.__("nb_jours").'"',
    'pension_sejours.somme as "'.__("somme").'"',
    "case pension_sejours.payée when 't' then 'Oui' else 'Non' end as \"".__("payée")."\"",
    'animale.race as "'.__("animale").'"',
    'pension.nom as "'.__("pension").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'pension_sejours.pension_sejours as "'.__("pension_sejours").'"',
    'pension_sejours.nb_jours as "'.__("nb_jours").'"',
    'pension_sejours.somme as "'.__("somme").'"',
    'animale.race as "'.__("animale").'"',
    'pension.nom as "'.__("pension").'"',
    );
$tri="ORDER BY pension_sejours.date_entrée ASC NULLS LAST";
$edition="pension_sejours";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animale" => array("animale", ),
    "pension" => array("pension", ),
);
// Filtre listing sous formulaire - animale
if (in_array($retourformulaire, $foreign_keys_extended["animale"])) {
    $selection = " WHERE (pension_sejours.animale = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - pension
if (in_array($retourformulaire, $foreign_keys_extended["pension"])) {
    $selection = " WHERE (pension_sejours.pension = ".intval($idxformulaire).") ";
}

