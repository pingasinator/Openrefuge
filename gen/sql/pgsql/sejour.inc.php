<?php
//$Id$ 
//gen openMairie le 16/03/2026 16:50

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("sejour");
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
$table = DB_PREFIXE."sejour
    LEFT JOIN ".DB_PREFIXE."animal 
        ON sejour.animal=animal.animal 
    LEFT JOIN ".DB_PREFIXE."hebergement 
        ON sejour.hebergement=hebergement.hebergement 
    LEFT JOIN ".DB_PREFIXE."provenance 
        ON sejour.provenance=provenance.provenance 
    LEFT JOIN ".DB_PREFIXE."sejour_tarif 
        ON sejour.sejour_tarif=sejour_tarif.sejour_tarif ";
// SELECT 
$champAffiche = array(
    'sejour.sejour as "'.__("sejour").'"',
    'to_char(sejour.date_entree ,\'DD/MM/YYYY\') as "'.__("date_entree").'"',
    'to_char(sejour.date_sortie ,\'DD/MM/YYYY\') as "'.__("date_sortie").'"',
    "case sejour.paye when 't' then 'Oui' else 'Non' end as \"".__("paye")."\"",
    'animal.nom as "'.__("animal").'"',
    'provenance.libelle as "'.__("provenance").'"',
    'hebergement.nom as "'.__("hebergement").'"',
    'sejour_tarif.libelle as "'.__("sejour_tarif").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'sejour.sejour as "'.__("sejour").'"',
    'animal.nom as "'.__("animal").'"',
    'provenance.libelle as "'.__("provenance").'"',
    'hebergement.nom as "'.__("hebergement").'"',
    'sejour_tarif.libelle as "'.__("sejour_tarif").'"',
    );
$tri="ORDER BY sejour.date_entree ASC NULLS LAST";
$edition="sejour";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "animal" => array("animal", ),
    "hebergement" => array("hebergement", ),
    "provenance" => array("provenance", ),
    "sejour_tarif" => array("sejour_tarif", ),
);
// Filtre listing sous formulaire - animal
if (in_array($retourformulaire, $foreign_keys_extended["animal"])) {
    $selection = " WHERE (sejour.animal = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - hebergement
if (in_array($retourformulaire, $foreign_keys_extended["hebergement"])) {
    $selection = " WHERE (sejour.hebergement = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - provenance
if (in_array($retourformulaire, $foreign_keys_extended["provenance"])) {
    $selection = " WHERE (sejour.provenance = ".intval($idxformulaire).") ";
}
// Filtre listing sous formulaire - sejour_tarif
if (in_array($retourformulaire, $foreign_keys_extended["sejour_tarif"])) {
    $selection = " WHERE (sejour.sejour_tarif = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'facture_sejour',
);

