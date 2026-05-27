<?php
/**
 * Ce script permet de paramétrer le générateur.
 *
 * @package framework_openmairie
 * @version SVN : $Id$
 */

/**
 * Ce tableau permet de lister les tables qui ne doivent pas être prises en
 * compte dans le générateur. Elles n'apparaîtront donc pas dans l'interface
 * et ne seront pas automatiquement générées par le 'genfull'.
 */
$core_tables_to_avoid = array(
    "om_version",
    "om_password_reset",
);

/**
 * Ce tableau de configuration permet de donner des informations de surcharges
 * sur certains objets pour qu'elles soient prises en compte par le générateur.
 * $core_tables_to_overload = array(
 *    "<table>" => array(
 *        // définition de la liste des classes qui surchargent la classe
 *        // <table> pour que le générateur puisse générer ces surcharges
 *        // et les inclure dans les tests de sous formulaire
 *        "extended_class" => array("<classe_surcharge_1_de_table>", ),
 *        // définition de la liste des champs à afficher dans l'affichage du
 *        // tableau champAffiche dans <table>.inc.php
 *        "displayed_fields_in_tableinc" => array("<champ_1>", ),
 *    ),
 * );
 */
$core_tables_to_overload = array(
    //
    "om_dashboard" => array(
        "breadcrumb_in_page_title" => array("administration", "tableaux de bord", ),
    ),
    //
    "om_sig_extent" => array(
        "tablename_in_page_title" => "étendue",
        "breadcrumb_in_page_title" => array("administration", "SIG", ),
    ),
    //
    "om_sig_flux" => array(
        "tablename_in_page_title" => "flux",
        "breadcrumb_in_page_title" => array("administration", "SIG", ),
    ),
    //
    "om_sig_map" => array(
        "tablename_in_page_title" => "carte",
        "breadcrumb_in_page_title" => array("administration", "SIG", ),
    ),
    //
    "om_sig_map_comp" => array(
        "tablename_in_page_title" => "géométrie",
        "breadcrumb_in_page_title" => array("administration", "SIG", ),
    ),
    //
    "om_sig_map_flux" => array(
        "tablename_in_page_title" => "flux appliqué à une carte",
        "breadcrumb_in_page_title" => array("administration", "SIG", ),
    ),
    //
    "om_utilisateur" => array(
        "displayed_fields_in_tableinc" => array(
            "nom", "email", "login", "om_profil",
        ),
    ),
    //
    "om_widget" => array(
        "breadcrumb_in_page_title" => array("administration", "tableaux de bord", ),
        "displayed_fields_in_tableinc" => array(
            "libelle", "om_profil", "type",
        ),
    ),
);
