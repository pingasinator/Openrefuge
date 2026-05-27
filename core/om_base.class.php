<?php
/**
 * Ce script contient la définition de la classe 'om_base'.
 *
 * @package framework_openmairie
 * @version SVN : $Id$
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
 * Définition de la classe 'om_base'.
 */
class om_base {

    /**
     * Instance de la classe 'application'.
     * @var null|application
     */
    protected $f = null;

    /**
     * Destructeur.
     */
    public function __destruct() {
        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);
    }

    /**
     * Initialisation de la classe 'application'.
     *
     * Cette méthode permet de vérifier que l'attribut f de la classe contient
     * bien la ressource utils du framework et si ce n'est pas le cas de la
     * récupérer.
     *
     * @return boolean
     */
    protected function init_om_application() {
        //
        if (isset($this->f) && $this->f != null) {
            return true;
        }
        //
        if (isset($GLOBALS["f"])) {
            $this->f = $GLOBALS["f"];
            return true;
        }
        //
        return false;
    }

    /**
     * Ajout d'un message au système de logs.
     *
     * Cette méthode permet de logger un message.
     *
     * @param string  $message Message à logger.
     * @param integer $type    Niveau de log du message.
     *
     * @return void
     */
    protected function addToLog($message, $type = DEBUG_MODE) {
        //
        if (isset($this->f) && method_exists($this->f, "elapsedtime")) {
            logger::instance()->log(
                $this->f->elapsedtime()." : class ".get_class($this)." - ".$message,
                $type
            );
        } else {
            logger::instance()->log(
                "X.XXX : class ".get_class($this)." - ".$message,
                $type
            );
        }
    }
}
