<?php
/**
 * Ce script contient la définition de la classe 'database'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_database.class.php 4348 2018-07-20 16:49:26Z softime $
 */

/**
 *
 */
if (defined("PATH_OPENMAIRIE") !== true) {
    /**
     * @ignore
     */
    define("PATH_OPENMAIRIE", "");
}
require_once PATH_OPENMAIRIE."om_debug.inc.php";
if (defined("DEBUG") !== true) {
    /**
     * @ignore
     */
    define("DEBUG", PRODUCTION_MODE);
}
require_once PATH_OPENMAIRIE."om_logger.class.php";

/**
 *
 */
require_once "DB.php";

/**
 * Définition de la classe 'database'.
 */
class database extends DB {

    /**
     * Vérifie si une erreur de base de données s'est produite.
     *
     * Cette méthode permet de vérifier si une erreur de base de données est
     * survenue sur la ressource passée en paramètre. Si c'est le cas :
     * - soit on retourne true (si le marqueur de retour est passée),
     * - soit on affiche un message à l'utilisateur et on arrête le script.
     * Dans les deux cas on ajoute l'erreur dans les logs.
     * Si il n'y a pas d'erreur de base de données la méthode retourne false.
     *
     * @param null|resource $resource Ressource de base de données sur laquelle vérifier l'erreur.
     * @param boolean $forcereturn Marqueur indiquant un retour booléen ou non.
     *
     * @return void|boolean
     */
    static function isError($resource = null, $forcereturn = false) {
        //
        if (!DB::isError($resource)) {
            return false;
        }

        // Logger
        $temp = explode('[', $resource->getDebugInfo());
        if (trim($temp[0]) != "") {
            logger::instance()->log(__METHOD__."(): QUERY => ".$temp[0], DEBUG_MODE);
        }
        logger::instance()->log(__METHOD__."(): SGBD ERROR => ".substr($temp[1], 0, strlen($temp[1])-1), DEBUG_MODE);
        logger::instance()->log(__METHOD__."(): PEAR ERROR => ".$resource->getMessage(), DEBUG_MODE);

        //
        if ($forcereturn == true) {
            return true;
        }

        //
        $class = "error";
        $message = __("Erreur de base de donnees. Contactez votre administrateur.");
        //
        echo "\n<div class=\"message ui-widget ui-corner-all ui-state-highlight ui-state-".$class."\">\n";
        echo "<p>\n";
        echo "\t<span class=\"ui-icon ui-icon-info\"><!-- --></span> \n\t";
        echo "<span class=\"text\">";
        echo $message;
        echo "</span>";
        echo "\n</p>\n";
        echo "</div>\n";
        //
        echo "</div>";
        die();
    }
}
