<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sig_map.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_sig_map.inc.php")) {
    include "../gen/sql/pgsql/om_sig_map.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_sig_map.inc.php";
}

// Onglets
$tab_title = __("carte");
$sousformulaire_parameters = array(
    "om_sig_map_comp" => array(
        "title" => __("géométrie"),
    ),
    "om_sig_map_flux" => array(
        "title" => __("flux appliqué à une carte"),
    ),
);
// On supprime l'onglet sur le même objet car il n'a pas de sens
$index_elemn_to_remove = array_search("om_sig_map", $sousformulaire);
unset($sousformulaire[$index_elemn_to_remove]);

// FROM
$table = DB_PREFIXE."om_sig_map
    LEFT JOIN ".DB_PREFIXE."om_collectivite
        ON om_sig_map.om_collectivite=om_collectivite.om_collectivite
    LEFT JOIN ".DB_PREFIXE."om_sig_extent
        ON om_sig_map.om_sig_extent=om_sig_extent.om_sig_extent
    LEFT JOIN ".DB_PREFIXE."om_sig_map s
        ON om_sig_map.source_flux=s.om_sig_map ";
// SELECT
$champAffiche = array(
    'om_sig_map.om_sig_map as "'.__("n°").'"',
    'om_sig_map.id as "'.__("id").'"',
    'om_sig_map.libelle as "'.__("Libellé").'"',
    "case om_sig_map.actif when 't' then 'Oui' else 'Non' end as \"".__("Act")."\"",
    "case om_sig_map.util_idx when 't' then 'Oui' else 'Non' end as \"".__("Idx")."\"",
    "case om_sig_map.util_reqmo when 't' then 'Oui' else 'Non' end as \"".__("ReqMo")."\"",
    "case om_sig_map.util_recherche when 't' then 'Oui' else 'Non' end as \"".__("Rec.")."\"",
    's.libelle as "'.__("Src.Flux").'"',
    'om_sig_map.fond_default as "'.__("Fond").'"',
    'om_sig_extent.nom as "'.__("Etendue").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champRecherche = array(
    'om_sig_map.om_sig_map as "'.__("n°").'"',
    'om_sig_map.id as "'.__("id").'"',
    'om_sig_map.libelle as "'.__("libelle").'"',
    'om_sig_map.libelle as "'.__("source_flux").'"',
    'om_sig_map.zoom as "'.__("zoom").'"',
    'om_sig_map.fond_osm as "'.__("fond_osm").'"',
    'om_sig_map.fond_bing as "'.__("fond_bing").'"',
    'om_sig_map.fond_sat as "'.__("fond_sat").'"',
    'om_sig_map.layer_info as "'.__("layer_info").'"',
    'om_sig_map.fond_default as "'.__("fond_default").'"',
    'om_sig_map.etendue as "'.__("etendue").'"',
    'om_sig_map.projection_externe as "'.__("projection_externe").'"',
    'om_sig_map.retour as "'.__("retour").'"',
    );
$tri="ORDER BY om_sig_map.libelle ASC NULLS LAST";
