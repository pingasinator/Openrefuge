<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sig_flux.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_sig_flux.inc.php")) {
    include "../gen/sql/pgsql/om_sig_flux.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_sig_flux.inc.php";
}

// Onglets
$tab_title = __("flux");
$sousformulaire_parameters = array(
    "om_sig_map_flux" => array(
        "title" => __("flux appliqué à une carte"),
    ),
);

//
$champAffiche = array(
    'om_sig_flux.om_sig_flux as "'.__("n°").'"',
    'om_sig_flux.id as "'.__("id").'"',
    'om_sig_flux.libelle as "'.__("libelle").'"',
    " CASE WHEN cache_type = 'IMP' THEN 'Impression' WHEN cache_type = 'TCF' THEN 'flux tilecache' WHEN cache_type = 'SMT' THEN 'Slippy Map Tiles' ELSE 'WMS' END as type",
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champRecherche = array(
    'om_sig_flux.om_sig_flux as "'.__("n°").'"',
    'om_sig_flux.libelle as "'.__("libelle").'"',
    'om_sig_flux.id as "'.__("id").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}

//
$tri = ' order by om_sig_flux.id';
