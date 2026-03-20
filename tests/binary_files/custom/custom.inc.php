<?php
/**
 * Ce script permet de déclarer des surcharges niveau 'CUSTOM'.
 *
 * @package framework_openmairie
 * @version SVN : $Id$
 */

//
$custom = array(
    "tab" => array(
        "om_collectivite" => '../custom/sql/pgsql/om_collectivite.inc.php',
        "om_utilisateur" => '../custom/sql/pgsql/om_utilisateur.inc.php',
    ),
    "obj" => array(
        "om_logo" => '../custom/obj/om_logo.class.php',
    ),
);
