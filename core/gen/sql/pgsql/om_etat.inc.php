<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("parametrage")." -> ".__("om_etat");
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
$table = DB_PREFIXE."om_etat
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_etat.om_collectivite=om_collectivite.om_collectivite 
    LEFT JOIN ".DB_PREFIXE."om_requete 
        ON om_etat.om_sql=om_requete.om_requete ";
// SELECT 
$champAffiche = array(
    'om_etat.om_etat as "'.__("om_etat").'"',
    'om_etat.id as "'.__("id").'"',
    'om_etat.libelle as "'.__("libelle").'"',
    "case om_etat.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    'om_etat.orientation as "'.__("orientation").'"',
    'om_etat.format as "'.__("format").'"',
    'om_etat.logo as "'.__("logo").'"',
    'om_etat.logoleft as "'.__("logoleft").'"',
    'om_etat.logotop as "'.__("logotop").'"',
    'om_etat.titreleft as "'.__("titreleft").'"',
    'om_etat.titretop as "'.__("titretop").'"',
    'om_etat.titrelargeur as "'.__("titrelargeur").'"',
    'om_etat.titrehauteur as "'.__("titrehauteur").'"',
    'om_etat.titrebordure as "'.__("titrebordure").'"',
    'om_requete.libelle as "'.__("om_sql").'"',
    'om_etat.se_font as "'.__("se_font").'"',
    'om_etat.se_couleurtexte as "'.__("se_couleurtexte").'"',
    'om_etat.margeleft as "'.__("margeleft").'"',
    'om_etat.margetop as "'.__("margetop").'"',
    'om_etat.margeright as "'.__("margeright").'"',
    'om_etat.margebottom as "'.__("margebottom").'"',
    'om_etat.header_offset as "'.__("header_offset").'"',
    'om_etat.footer_offset as "'.__("footer_offset").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_etat.om_collectivite as "'.__("om_collectivite").'"',
    'om_etat.titre_om_htmletat as "'.__("titre_om_htmletat").'"',
    'om_etat.corps_om_htmletatex as "'.__("corps_om_htmletatex").'"',
    'om_etat.header_om_htmletat as "'.__("header_om_htmletat").'"',
    'om_etat.footer_om_htmletat as "'.__("footer_om_htmletat").'"',
    );
//
$champRecherche = array(
    'om_etat.om_etat as "'.__("om_etat").'"',
    'om_etat.id as "'.__("id").'"',
    'om_etat.libelle as "'.__("libelle").'"',
    'om_etat.orientation as "'.__("orientation").'"',
    'om_etat.format as "'.__("format").'"',
    'om_etat.logo as "'.__("logo").'"',
    'om_etat.logoleft as "'.__("logoleft").'"',
    'om_etat.logotop as "'.__("logotop").'"',
    'om_etat.titreleft as "'.__("titreleft").'"',
    'om_etat.titretop as "'.__("titretop").'"',
    'om_etat.titrelargeur as "'.__("titrelargeur").'"',
    'om_etat.titrehauteur as "'.__("titrehauteur").'"',
    'om_etat.titrebordure as "'.__("titrebordure").'"',
    'om_requete.libelle as "'.__("om_sql").'"',
    'om_etat.se_font as "'.__("se_font").'"',
    'om_etat.se_couleurtexte as "'.__("se_couleurtexte").'"',
    'om_etat.margeleft as "'.__("margeleft").'"',
    'om_etat.margetop as "'.__("margetop").'"',
    'om_etat.margeright as "'.__("margeright").'"',
    'om_etat.margebottom as "'.__("margebottom").'"',
    'om_etat.header_offset as "'.__("header_offset").'"',
    'om_etat.footer_offset as "'.__("footer_offset").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_etat.libelle ASC NULLS LAST";
$edition="om_etat";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_etat.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
    "om_requete" => array("om_requete", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_etat.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_etat.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_etat.om_collectivite = ".intval($idxformulaire).") ";
    }
}
// Filtre listing sous formulaire - om_requete
if (in_array($retourformulaire, $foreign_keys_extended["om_requete"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_etat.om_sql = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_etat.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_etat.om_sql = ".intval($idxformulaire).") ";
    }
}

