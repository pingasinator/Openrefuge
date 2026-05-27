<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sousetat.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_sousetat.inc.php")) {
    include "../gen/sql/pgsql/om_sousetat.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_sousetat.inc.php";
}

//
$champAffiche = array(
    'om_sousetat.om_sousetat as "'.__("om_sousetat").'"',
    'om_sousetat.id as "'.__("id").'"',
    'om_sousetat.libelle as "'.__("libelle").'"',
    "case om_sousetat.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    'om_collectivite.niveau as "'.__("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champRecherche = array(
    'om_sousetat.om_sousetat as "'.__("om_sousetat").'"',
    'om_sousetat.id as "'.__("id").'"',
    'om_sousetat.libelle as "'.__("libelle").'"',
    'om_collectivite.niveau as "'.__("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
if ($_SESSION['niveau'] == '2') {
    $selection = "";
} else {
    $selection = " where (".$obj.".om_collectivite='".$_SESSION['collectivite']."' or niveau='2')";
}
