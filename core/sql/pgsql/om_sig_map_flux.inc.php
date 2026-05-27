<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sig_map_flux.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_sig_map_flux.inc.php")) {
    include "../gen/sql/pgsql/om_sig_map_flux.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_sig_map_flux.inc.php";
}

// Onglets
$tab_title = __("flux appliqué à une carte");

//
$champAffiche = array(
    'om_sig_map_flux.om_sig_map_flux as "'.__("n°").'"',
    'om_sig_map.libelle as "'.__("carte").'"',
    'om_sig_flux.libelle||\' (\'||CASE WHEN cache_type IS NULL OR cache_type = \'\' THEN \'WMS\' ELSE cache_type END||\')\'  as "'.__("flux").'"',
    'om_sig_map_flux.ol_map as "'.__("nom OL").'"',
    "case om_sig_map_flux.baselayer when 't' then 'Oui' else 'Non' end as \"".__("Base")."\"",
    'om_sig_map_flux.ordre as "'.__("ordre").'"',
    "case om_sig_map_flux.visibility when 't' then 'Oui' else 'Non' end as \"".__("Vis.")."\"",
    "case om_sig_map_flux.panier when 't' then 'Oui' else 'Non' end as \"".__("panier")."\"",
);
//
$champRecherche = array(
    'om_sig_map_flux.om_sig_map_flux as "'.__("n°").'"',
    'om_sig_map.libelle as "'.__("carte").'"',
    'om_sig_flux.libelle||\' (\'||CASE WHEN cache_type IS NULL OR cache_type = \'\' THEN \'WMS\' ELSE cache_type END||\')\'  as "'.__("flux").'"',
    'om_sig_map_flux.ol_map as "'.__("nom OL").'"',
    'om_sig_map_flux.baselayer as "'.__("base").'"',
    'om_sig_map_flux.ordre as "'.__("ordre").'"',
    'om_sig_map_flux.visibility as "'.__("vis.").'"',
    'om_sig_map_flux.panier as "'.__("panier").'"',
    'om_sig_map_flux.pa_nom as "'.__("pa_nom").'"',
    'om_sig_map_flux.pa_layer as "'.__("pa_layer").'"',
    'om_sig_map_flux.pa_sql as "'.__("pa_sql").'"',
    'om_sig_map_flux.pa_attribut as "'.__("pa_attribut").'"',
    'om_sig_map_flux.pa_encaps as "'.__("pa_encaps").'"',
    'om_sig_map_flux.pa_type_geometrie as "'.__("pa_type_geometrie").'"',
    'om_sig_map_flux.sql_filter as "'.__("sql_filter").'"',
    "case om_sig_map_flux.singletile when 't' then 'Oui' else 'Non' end as \"".__("singletile")."\"",
    'om_sig_map_flux.maxzoomlevel as "'.__("maxzoomlevel").'"',
);

//
$tri = "ORDER BY om_sig_map_flux.baselayer,om_sig_map_flux.ordre, om_sig_flux.libelle ";
