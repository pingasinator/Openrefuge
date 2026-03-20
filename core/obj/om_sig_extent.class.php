<?php
//$Id: om_sig_extent.class.php 4348 2018-07-20 16:49:26Z softime $
//gen openMairie le 10/04/2015 12:03

if (file_exists("../gen/obj/om_sig_extent.class.php")) {
    require_once "../gen/obj/om_sig_extent.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_sig_extent.class.php";
}

class om_sig_extent_core extends om_sig_extent_gen {

    /**
     * On active les nouvelles actions sur cette classe.
     */
    var $activate_class_action = true;

}
