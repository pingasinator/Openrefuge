<?php
//$Id$ 
//gen openMairie le 16/03/2026 15:55

$DEBUG=0;
$serie=15;
$ent = __("application")." -> ".__("facture");
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
$table = DB_PREFIXE."facture
    LEFT JOIN ".DB_PREFIXE."personne 
        ON facture.personne=personne.personne ";
// SELECT 
$champAffiche = array(
    'facture.facture as "'.__("facture").'"',
    'personne.nom as "'.__("personne").'"',
    'to_char(facture.date_creation ,\'DD/MM/YYYY\') as "'.__("date_creation").'"',
    'facture.numero_facture as "'.__("numero_facture").'"',
    'facture.etat as "'.__("etat").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'facture.facture as "'.__("facture").'"',
    'personne.nom as "'.__("personne").'"',
    'facture.numero_facture as "'.__("numero_facture").'"',
    'facture.etat as "'.__("etat").'"',
    );
$tri="ORDER BY personne.nom ASC NULLS LAST";
$edition="facture";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "personne" => array("personne", ),
);
// Filtre listing sous formulaire - personne
if (in_array($retourformulaire, $foreign_keys_extended["personne"])) {
    $selection = " WHERE (facture.personne = ".intval($idxformulaire).") ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'facture_sejour',
    'facture_soin',
);

