<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_lettretype.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_lettretype.inc.php")) {
    include "../gen/sql/pgsql/om_lettretype.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_lettretype.inc.php";
}

//
$champAffiche = array(
    'om_lettretype.om_lettretype as "'.__("om_lettretype").'"',
    'om_lettretype.id as "'.__("id").'"',
    'om_lettretype.libelle as "'.__("libelle").'"',
    "case om_lettretype.actif when 't' then 'Oui' else 'Non' end as \"".__("actif")."\"",
    'om_collectivite.niveau as "'.__("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
$champRecherche = array(
    'om_lettretype.om_lettretype as "'.__("om_lettretype").'"',
    'om_lettretype.id as "'.__("id").'"',
    'om_lettretype.libelle as "'.__("libelle").'"',
    'om_collectivite.niveau as "'.__("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \"".__("collectivite")."\"");
}
//
if ($_SESSION['niveau'] == '2') {
    $selection = "";
    if ($retourformulaire== 'om_requete') {
        $selection .= " WHERE (".$obj.".om_sql = ".intval($idxformulaire).")";
    }
} else {
    $selection = " WHERE (".$obj.".om_collectivite='".$_SESSION['collectivite']."' or niveau='2')";
    if ($retourformulaire== 'om_requete') {
        $selection .= " AND (".$obj.".om_sql = ".intval($idxformulaire).")";
    }
}
