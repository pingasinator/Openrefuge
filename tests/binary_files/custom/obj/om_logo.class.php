<?php
/**
 * Ce script permet de déclarer des surcharges niveau 'CUSTOM'.
 *
 * Objet de la surcharge : pour vérifier la bonne application des contraintes
 * sur les widget de formulaire fichier, on ajoute une contrainte sur l'extension
 * sur le formulaire du logo.
 *
 * @package framework_openmairie
 * @version SVN : $Id$
 */

require_once PATH_OPENMAIRIE."obj/om_logo.class.php";

class om_logo_custom extends om_logo_core {

    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj, $dnu1, $dnu2);
        $params = array(
            "constraint" => array(
                "extension" => ".png",
            ),
        );
        $form->setSelect("fichier", $params);
    }

}
