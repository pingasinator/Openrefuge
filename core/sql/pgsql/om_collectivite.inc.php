<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_collectivite.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_collectivite.inc.php")) {
    include "../gen/sql/pgsql/om_collectivite.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_collectivite.inc.php";
}

// Pas d'action ajouter en niveau 1
if ($_SESSION['niveau'] == "1") {
    $tab_actions['corner']['ajouter'] = array('lien' => '#');
}

//
$sousformulaire = array(
    //
    'om_utilisateur',
    //
    'om_parametre',
    //
    'om_etat',
    'om_lettretype',
    'om_sousetat',
);

//
if (isset($f) && $f->getParameter("option_localisation") == "sig_interne") {
    $sousformulaire[] = "om_sig_map";
    $sousformulaire[] = "om_sig_flux";
}
