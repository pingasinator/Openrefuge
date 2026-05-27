<?php
/**
 * Ce script contient la définition de la classe 'logger'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_logger.class.php 4348 2018-07-20 16:49:26Z softime $
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
require_once PATH_OPENMAIRIE."om_locales.inc.php";
require_once PATH_OPENMAIRIE."om_debug.inc.php";
if (defined("DEBUG") !== true) {
    /**
     * @ignore
     */
    define("DEBUG", PRODUCTION_MODE);
}

/**
 * Définition de la classe 'logger'.
 */
class logger {

    /**
     * Instance statique de la classe logger.
     * @var resource
     */
    private static $_instance;

    /**
     * Constructeur.
     */
    private function __construct() {
        //
        $this->path = getcwd();
    }

    /**
     * Accesseur pour la propriété '_instance'.
     *
     * @return resource
     */
    static function instance() {
        //
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        //
        return self::$_instance;
    }

    /**
     * Interdit de cloner l'objet logger.
     *
     * @return void
     */
    public function __clone() {
        throw new Exception('Cannot clone the logger object.');
    }

    /**
     * Liste des niveaux de log.
     * @var array
     */
    var $types_to_show = array(
        DEBUG_MODE => "DEBUG",
        VERBOSE_MODE => "VERBOSE",
        EXTRA_VERBOSE_MODE => "EXTRA_VERBOSE",
    );

    /**
     * Ajoute un message de log dans la pile.
     *
     * @param string $message Message à logger.
     * @param integer $type Niveau de log.
     *
     * @return void
     */
    public function log($message = "", $type = DEBUG_MODE) {
        //
        array_push($this->storage,
            array(
                "message" => $message,
                "type" => $type,
                "date" => date("\nY-m-d H:i:s"),
            )
        );
    }

    /**
     * Pile des messages de log.
     * @var array
     */
    var $storage = array();

    /**
     * Marqueur pour indiquer si l'affichage à l'écran est souhaité ou non.
     * @var boolean
     */
    var $display_log = true;

    /**
     * Affichage des logs à l'écran.
     *
     * @return void
     */
    function displayLog() {
        //
        if ($this->display_log == true
            && DEBUG > PRODUCTION_MODE
            && count($this->storage) > 0) {
            //
            echo "\n<div class=\"log-box\">\n";
            //
            echo "<fieldset class=\"cadre ui-widget-content ui-corner-all\">\n";
            //
            echo "<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
            echo __("Logger");
            echo "</legend>\n";
            //
            echo "<div class=\"even\"><span class=\"url\">".htmlentities($_SERVER['REQUEST_URI'])."</span></div>\n";
            foreach ($this->storage as $key => $log) {
                //
                if (DEBUG >= $log["type"] && in_array($log["type"], array_keys($this->types_to_show))) {
                    //
                    echo "<div class=\"".($key % 2 == 0 ? "odd" : "even")."\">";
                    echo "<span class=\"".strtolower($this->types_to_show[$log["type"]])."\">";
                    echo "<span class=\"message\">".$log["message"]."</span>";
                    echo "&nbsp;";
                    echo "<span class=\"type\">".$this->types_to_show[$log["type"]]."</span>";
                    echo "</span>";
                    echo "</div>\n";
                }
            }
            //
            echo "</fieldset>\n";
            //
            echo "</div>\n";
        }
    }

    /**
     * Cette méthode est dépréciée et ne doit plus être utilisée.
     * @deprecated
     */
    function writeLogToFile() {
    }

    /**
     * Cette méthode permet d'écrire tous les messages de log de type
     * DEBUG_MODE dans le fichier ../var/log/error.log peu importe
     * le niveau de log configuré dans le fichier ../dyn/debug.inc.php.
     * Attention si le fichier ../var/log/error.log ne peut pas être écrit
     * aucune erreur n'est levée.
     *
     * @return void
     */
    function writeErrorLogToFile() {
        //
        $to_write = "";
        //
        foreach ($this->storage as $key => $log) {
            //
            if ($log["type"] == DEBUG_MODE) {
                //
                $to_write .= $log["date"]." ".$log["message"]." [".$this->types_to_show[$log["type"]]."]";
            }
        }
        if ($to_write != "") {
            //
            $logfile = "error.log";
            // Si le répertoire dans lequel le fichier de log doit être écrit
            // n'existe pas alors on tente de le créer de manière transparente
            // (si la création échoue on ne lève pas d'erreur)
            if (!$this->is_targetfolder_exists()) {
                $this->create_targetfolder();
            }
            // Si le fichier de log n'est pas accessible en écriture alors on
            // sort de la méthode et aucun log ne sera écrit
            if ($this->is_logfile_writable($logfile) !== true) {
                return;
            }
            //
            @$fp = fopen($this->get_logfile_path($logfile), "a");
            //
            if ($fp != false) {
                //
                $uri = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "UNKNOWN_URI");
                //
                fwrite($fp, date("\nY-m-d H:i:s")." ERROR [".(isset($_SESSION["login"]) ? $_SESSION["login"] : ".")."] ".$uri."");
                fwrite($fp, $to_write);
                fwrite($fp, "\n\n");
                //
                fclose($fp);
            }
        }
    }

    /**
     * Vide le contenu de l'attribut storage
     *
     * @return void
     */
    function cleanLog() {
        unset($this->storage);
        $this->storage = array();
    }

    /**
     * Écrit le message dans le fichier de log passé en paramètre.
     *
     * @param string $logfile Nom du fichier de log dans lequel on veut écrire.
     * @param string $message Chaine de caractères à logger.
     *
     * @return void
     */
    public function log_to_file($logfile, $message = "") {
        // Si le répertoire dans lequel le fichier de log doit être écrit
        // n'existe pas alors on tente de le créer de manière transparente
        // (si la création échoue on ne lève pas d'erreur)
        if (!$this->is_targetfolder_exists()) {
            $this->create_targetfolder();
        }
        // Si le fichier de log n'est pas accessible en écriture alors on
        // sort de la méthode et aucun log ne sera écrit
        if ($this->is_logfile_writable($logfile) !== true) {
            return;
        }
        // On écrit dans le fichier de log
        @file_put_contents(
            $this->get_logfile_path($logfile),
            date("Y-m-d H:i:s")." - ".str_replace("    ", " ", str_replace("\n", "", $message))."\n",
            FILE_APPEND
        );
    }

    /**
     * Indique si le fichier de log est accessible en écriture.
     *
     * On vérifie :
     * - le répertoire dans lequel le fichier doit être écrit existe
     * - si le fichier n'existe pas qu'il peut être créé
     * - si le fichier existe qu'il peut être écrit
     *
     * @param string $logfile Nom du fichier de log dans lequel on veut écrire.
     *
     * @return boolean
     */
    private function is_logfile_writable($logfile) {
        // On récupère le path complet du fichier
        $logfile_path = $this->get_logfile_path($logfile);
        // Si le répertoire dans lequel le fichier doit être écrit n'existe pas
        // alors on retourne la valeur false. Cette méthode n'a pas vocation à
        // créer ce répertoire s'il n'existe pas.
        if (!$this->is_targetfolder_exists()) {
            return false;
        }
        // Si le fichier à écrire n'existe pas et que le répertoire dans lequel
        // le fichier doit être écrit n'est pas accessible en écriture alors on
        // retourne false.
        if (!file_exists($logfile_path)
            && !is_writable($this->get_targetfolder_path())) {
            return false;
        }
        // Si le fichier à écrire existe mais n'est pas accessible en écriture
        // alors on retourne false.
        if (file_exists($logfile_path)
            && !is_writable($logfile_path)) {
            return false;
        }
        // OK le fichier est accessible en écriture.
        return true;
    }

    /**
     * Indique si le répertoire cible des fichiers de log existe.
     *
     * @return boolean
     */
    private function is_targetfolder_exists() {
        //
        if (is_dir($this->get_targetfolder_path())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Crée le répertoire cible des fichiers de log.
     *
     * @return void
     */
    private function create_targetfolder() {
        //
        @mkdir($this->get_targetfolder_path(), 0755, true);
    }

    /**
     * Retourne le path vers le répertoire cible des fichiers de log.
     *
     * @return string
     */
    private function get_targetfolder_path() {
        //
        return $this->path."/../var/log/";
    }

    /**
     * Retourne le path vers le fichier de log.
     *
     * @param string $logfile Nom du fichier de log dans lequel on veut écrire.
     *
     * @return string
     */
    private function get_logfile_path($logfile) {
        //
        return $this->get_targetfolder_path().$logfile;
    }

}
