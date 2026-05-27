<?php
//$Id$ 
//gen openMairie le 23/04/2026 11:39

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("personne");
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
$table = DB_PREFIXE."personne
    LEFT JOIN ".DB_PREFIXE."civilite 
        ON personne.civilite=civilite.civilite 
    LEFT JOIN ".DB_PREFIXE."rue 
        ON personne.rue=rue.rue 
    LEFT JOIN ".DB_PREFIXE."ville 
        ON personne.ville=ville.ville ";
// SELECT 
$champAffiche = array(
    'personne.personne as "'.__("personne").'"',
    'personne.nom as "'.__("nom").'"',
    'personne.prenom as "'.__("prenom").'"',
    'ville.nom as "'.__("ville").'"',
    'personne.telephone as "'.__("telephone").'"',
    'personne.telephone_sec as "'.__("telephone_sec").'"',
    'personne.mail as "'.__("mail").'"',
    'civilite.libelle as "'.__("civilite").'"',
    'personne.num_rue as "'.__("num_rue").'"',
    'rue.nom as "'.__("rue").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'personne.personne as "'.__("personne").'"',
    'personne.nom as "'.__("nom").'"',
    'personne.prenom as "'.__("prenom").'"',
    'ville.nom as "'.__("ville").'"',
    'personne.telephone as "'.__("telephone").'"',
    'personne.telephone_sec as "'.__("telephone_sec").'"',
    'personne.mail as "'.__("mail").'"',
    'civilite.libelle as "'.__("civilite").'"',
    'personne.num_rue as "'.__("num_rue").'"',
    'rue.nom as "'.__("rue").'"',
    );
$tri="ORDER BY personne.nom ASC NULLS LAST";
$edition="personne";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "civilite" => array("civilite", ),
    "rue" => array("rue", ),
    "ville" => array("ville", ),
);
// Filtre listing sous formulaire - civilite
if (in_array($retourformulaire, $foreign_keys_extended["civilite"])) {
    $selection = " WHERE (personne.civilite = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - rue
if (in_array($retourformulaire, $foreign_keys_extended["rue"])) {
    $selection = " WHERE (personne.rue = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - ville
if (in_array($retourformulaire, $foreign_keys_extended["ville"])) {
    $selection = " WHERE (personne.ville = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'animal',
    'facture',
);

