<?php
/**
 * Ce script permet de déclarer des surcharges niveau 'CUSTOM'.
 *
 * Objet de la surcharge : pour vérifier la bonne application des contraintes
 * sur les widget de formulaire fichier, on ajoute un onglet logo sur l'objet
 * collectivité pour pouvoir tester en sousformulaire.
 *
 * @package framework_openmairie
 * @version SVN : $Id$
 */

include PATH_OPENMAIRIE."sql/pgsql/om_utilisateur.inc.php";

//
$champs = array(
    "identifiant_utilisateur" => array(
        'colonne' => 'om_utilisateur',
        'table' => 'om_utilisateur',
        'type' => 'text',
        'libelle' => _('Identifiant'),
        'taille' => 10,
        'max' => 8,
    ),
);
$options = array();
$options[] = array(
    'type' => 'search',
    'display' => true,
    'advanced' => $champs,
    'default_form' => 'advanced',
    'export' => array("csv", ),
    'absolute_object' => 'om_utilisateur',
);
