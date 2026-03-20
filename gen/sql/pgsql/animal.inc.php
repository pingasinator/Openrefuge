<?php
//$Id$ 
//gen openMairie le 16/03/2026 09:48

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("animal");
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
$table = DB_PREFIXE."animal
    LEFT JOIN ".DB_PREFIXE."animal_espece 
        ON animal.animal_espece=animal_espece.animal_espece 
    LEFT JOIN ".DB_PREFIXE."animal_race 
        ON animal.animal_race=animal_race.animal_race 
    LEFT JOIN ".DB_PREFIXE."animal_sexe 
        ON animal.animal_sexe=animal_sexe.animal_sexe 
    LEFT JOIN ".DB_PREFIXE."personne 
        ON animal.personne=personne.personne ";
// SELECT 
$champAffiche = array(
    'animal.animal as "'.__("animal").'"',
    'animal.nom as "'.__("nom").'"',
    'to_char(animal.date_naissance ,\'DD/MM/YYYY\') as "'.__("date_naissance").'"',
    'animal_espece.nom as "'.__("animal_espece").'"',
    'animal_race.nom as "'.__("animal_race").'"',
    'animal_sexe.libelle as "'.__("animal_sexe").'"',
    'personne.nom as "'.__("personne").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'animal.animal as "'.__("animal").'"',
    'animal.nom as "'.__("nom").'"',
    'animal_espece.nom as "'.__("animal_espece").'"',
    'animal_race.nom as "'.__("animal_race").'"',
    'animal_sexe.libelle as "'.__("animal_sexe").'"',
    'personne.nom as "'.__("personne").'"',
    );
$tri="ORDER BY animal.nom ASC NULLS LAST";
$edition="animal";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal_espece" => array("animal_espece", ),
    "animal_race" => array("animal_race", ),
    "animal_sexe" => array("animal_sexe", ),
    "personne" => array("personne", ),
);
// Filtre listing sous formulaire - animal_espece
if (in_array($retourformulaire, $foreign_keys_extended["animal_espece"])) {
    $selection = " WHERE (animal.animal_espece = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - animal_race
if (in_array($retourformulaire, $foreign_keys_extended["animal_race"])) {
    $selection = " WHERE (animal.animal_race = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - animal_sexe
if (in_array($retourformulaire, $foreign_keys_extended["animal_sexe"])) {
    $selection = " WHERE (animal.animal_sexe = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - personne
if (in_array($retourformulaire, $foreign_keys_extended["personne"])) {
    $selection = " WHERE (animal.personne = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'facture_sejour',
    'facture_soin',
    'medicament',
    'sejour',
    'soin',
);

