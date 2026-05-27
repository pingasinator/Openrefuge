<?php
/**
 * Ce fichier permet de faire une redirection vers la page de login de
 * l'application.
 *
 * @package framework_openmairie
 * @version SVN : $Id: index.php 4348 2018-07-20 16:49:26Z softime $
 */

//
$came_from = "";
if (isset($_GET['came_from'])) {
    $came_from = $_GET['came_from'];
}

//
header("Location: app/index.php?module=login&came_from=".urlencode($came_from));

