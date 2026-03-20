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

include PATH_OPENMAIRIE."sql/pgsql/om_collectivite.inc.php";

$sousformulaire[] = "om_logo";
