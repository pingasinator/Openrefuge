<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_parametre.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_parametre.inc.php")) {
    include "../gen/sql/pgsql/om_parametre.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_parametre.inc.php";
}

// SELECT
$champAffiche = array(
    'om_parametre.om_parametre as "'.__("om_parametre").'"',
    'om_parametre.libelle as "'.__("libelle").'"',
    'om_parametre.valeur as "'.__("valeur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champRecherche = array(
    'om_parametre.om_parametre as "'.__("om_parametre").'"',
    'om_parametre.libelle as "'.__("libelle").'"',
    'om_parametre.valeur as "'.__("valeur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
