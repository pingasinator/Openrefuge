<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

$DEBUG=0;
$serie=15;
$ent = __("parametrage")." -> ".__("om_lettretype");
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
$table = DB_PREFIXE."om_lettretype
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_lettretype.om_collectivite=om_collectivite.om_collectivite 
    LEFT JOIN ".DB_PREFIXE."om_requete 
        ON om_lettretype.om_sql=om_requete.om_requete ";
// SELECT 
$champAffiche = array(
    'om_lettretype.om_lettretype as "'.__("om_lettretype").'"',
    'om_lettretype.id as "'.__("id").'"',
    'om_lettretype.libelle as "'.__("libelle").'"',
    "case om_lettretype.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    'om_lettretype.orientation as "'.__("orientation").'"',
    'om_lettretype.format as "'.__("format").'"',
    'om_lettretype.logo as "'.__("logo").'"',
    'om_lettretype.logoleft as "'.__("logoleft").'"',
    'om_lettretype.logotop as "'.__("logotop").'"',
    'om_lettretype.titreleft as "'.__("titreleft").'"',
    'om_lettretype.titretop as "'.__("titretop").'"',
    'om_lettretype.titrelargeur as "'.__("titrelargeur").'"',
    'om_lettretype.titrehauteur as "'.__("titrehauteur").'"',
    'om_lettretype.titrebordure as "'.__("titrebordure").'"',
    'om_requete.libelle as "'.__("om_sql").'"',
    'om_lettretype.margeleft as "'.__("margeleft").'"',
    'om_lettretype.margetop as "'.__("margetop").'"',
    'om_lettretype.margeright as "'.__("margeright").'"',
    'om_lettretype.margebottom as "'.__("margebottom").'"',
    'om_lettretype.se_font as "'.__("se_font").'"',
    'om_lettretype.se_couleurtexte as "'.__("se_couleurtexte").'"',
    'om_lettretype.header_offset as "'.__("header_offset").'"',
    'om_lettretype.footer_offset as "'.__("footer_offset").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_lettretype.om_collectivite as "'.__("om_collectivite").'"',
    'om_lettretype.titre_om_htmletat as "'.__("titre_om_htmletat").'"',
    'om_lettretype.corps_om_htmletatex as "'.__("corps_om_htmletatex").'"',
    'om_lettretype.header_om_htmletat as "'.__("header_om_htmletat").'"',
    'om_lettretype.footer_om_htmletat as "'.__("footer_om_htmletat").'"',
    );
//
$champRecherche = array(
    'om_lettretype.om_lettretype as "'.__("om_lettretype").'"',
    'om_lettretype.id as "'.__("id").'"',
    'om_lettretype.libelle as "'.__("libelle").'"',
    'om_lettretype.orientation as "'.__("orientation").'"',
    'om_lettretype.format as "'.__("format").'"',
    'om_lettretype.logo as "'.__("logo").'"',
    'om_lettretype.logoleft as "'.__("logoleft").'"',
    'om_lettretype.logotop as "'.__("logotop").'"',
    'om_lettretype.titreleft as "'.__("titreleft").'"',
    'om_lettretype.titretop as "'.__("titretop").'"',
    'om_lettretype.titrelargeur as "'.__("titrelargeur").'"',
    'om_lettretype.titrehauteur as "'.__("titrehauteur").'"',
    'om_lettretype.titrebordure as "'.__("titrebordure").'"',
    'om_requete.libelle as "'.__("om_sql").'"',
    'om_lettretype.margeleft as "'.__("margeleft").'"',
    'om_lettretype.margetop as "'.__("margetop").'"',
    'om_lettretype.margeright as "'.__("margeright").'"',
    'om_lettretype.margebottom as "'.__("margebottom").'"',
    'om_lettretype.se_font as "'.__("se_font").'"',
    'om_lettretype.se_couleurtexte as "'.__("se_couleurtexte").'"',
    'om_lettretype.header_offset as "'.__("header_offset").'"',
    'om_lettretype.footer_offset as "'.__("footer_offset").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
$tri="ORDER BY om_lettretype.libelle ASC NULLS LAST";
$edition="om_lettretype";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_lettretype.om_collectivite = '".$_SESSION["collectivite"]."') ";
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
        $selection = " WHERE (om_lettretype.om_collectivite = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_lettretype.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_lettretype.om_collectivite = ".intval($idxformulaire).") ";
    }
}
// Filtre listing sous formulaire - om_requete
if (in_array($retourformulaire, $foreign_keys_extended["om_requete"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_lettretype.om_sql = ".intval($idxformulaire).") ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_lettretype.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_lettretype.om_sql = ".intval($idxformulaire).") ";
    }
}

