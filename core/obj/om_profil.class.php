<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_profil.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_profil.class.php")) {
    require_once "../gen/obj/om_profil.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_profil.class.php";
}

/**
 *
 */
class om_profil_core extends om_profil_gen {

    /**
     * On active les nouvelles actions sur cette classe.
     */
    var $activate_class_action = true;

}
