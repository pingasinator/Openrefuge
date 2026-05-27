<?php
//$Id: om_permission.class.php 4348 2018-07-20 16:49:26Z softime $
//gen openMairie le 13/02/2015 16:09

if (file_exists("../gen/obj/om_permission.class.php")) {
    require_once "../gen/obj/om_permission.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_permission.class.php";
}

class om_permission_core extends om_permission_gen {

    /**
     * On active les nouvelles actions sur cette classe.
     */
    var $activate_class_action = true;

}
