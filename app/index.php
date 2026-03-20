<?php
/**
 * Ce script permet d'interfacer l'application.
 *
 * @package framework_openmairie
 * @version SVN : $Id: index.php 4348 2018-07-20 16:49:26Z softime $
 */

require_once "../app/framework_openmairie.class.php";
$flag = filter_input(INPUT_GET, 'module');
if (in_array($flag, array("login", "logout", )) === false) {
    $flag = "nohtml";
}
$f = new framework_openmairie($flag);
$f->view_main();
