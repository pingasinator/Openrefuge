<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sig_extent.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_sig_extent.inc.php")) {
    include "../gen/sql/pgsql/om_sig_extent.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_sig_extent.inc.php";
}

// Onglets
$tab_title = __("étendue");
$sousformulaire_parameters = array(
    "om_sig_map" => array(
        "title" => __("carte"),
    ),
);

// SELECT 
$champAffiche = array(
    'om_sig_extent.om_sig_extent as "'.__("n°").'"',
    'om_sig_extent.nom as "'.__("nom").'"',
    'om_sig_extent.extent as "'.__("étendue").'"',
    );
//
$champRecherche = array(
    'om_sig_extent.om_sig_extent as "'.__("n°").'"',
    'om_sig_extent.nom as "'.__("nom").'"',
    'om_sig_extent.extent as "'.__("étendue").'"',
);
