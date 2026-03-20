<?php
/**
 * Ce fichier permet de declarer la classe PDF.
 *
 * @package framework_openmairie
 * @version SVN : $Id: db_fpdf.php 4348 2018-07-20 16:49:26Z softime $
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
 * Inclusion de la classe FPDF qui permet de generer des fichiers PDF.
 */
require_once "fpdf.php";

/**
 * Cette methode surcharge la classe standard fpdf pour permettre la gestion
 * d'editions de resultats de requetes dans la base de donnnees.
 */
class PDF extends FPDF {

    /**
     * Instance de la classe 'application'.
     * @var null|application
     */
    var $f = null;

    /**
     *
     */
    var $edition_params = array();

    /**
     * Méthode d'affichage de l'entête de page.
     *
     * @return void
     */
    function Header() {
        //// Numéro de page
        //
        $this->SetFont(
            $this->get_edition_param("police"),
            '',
            '9'
        );
        //
        $this->Cell(0, 2, 'Page '.$this->PageNo().' / {nb}', 0, 1, 'R');
        //// Titre
        //
        $this->SetFont(
            $this->get_edition_param("police"),
            $this->get_edition_param("grastitre"),
            $this->get_edition_param("sizetitre")
        );
        //
        $this->SetTextColor(
            $this->get_edition_param("C1titre"),
            $this->get_edition_param("C2titre"),
            $this->get_edition_param("C3titre")
        );
        //
        $this->SetFillColor(
            $this->get_edition_param("C1titrefond"),
            $this->get_edition_param("C2titrefond"),
            $this->get_edition_param("C3titrefond")
        );
        //
        if ($this->get_edition_param("flagsessionliste") == 1) {
            //
            $text = sprintf(
                "%s %s",
                $this->get_edition_param("libtitre"),
                $this->get_edition_param("nolibliste")
            );
        } else {
            //
            $text = $this->get_edition_param("libtitre");
        }
        //
        $this->Cell(
            $this->get_edition_param("widthtableau"),
            $this->get_edition_param("heightitre"),
            $text,
            $this->get_edition_param("bordertitre"),
            1,
            $this->get_edition_param("aligntitre"),
            $this->get_edition_param("fondtitre")
        );
        //// Bordure de haut de tableau
        //
        $this->Cell(
            $this->get_edition_param("widthtableau"),
            0,
            ' ',
            $this->get_edition_param("bt"),
            1,
            'L',
            0
        );
        //
        $this->ln(0);
    }

    /**
     * Méthode d'affichage du pied de page.
     *
     * @return void
     */
    function Footer() {
        //// Bordure de bas du tableau
        //
        $this->ln(0);
        //
        $this->Cell(
            $this->get_edition_param("widthtableau"),
            0,
            ' ',
            $this->get_edition_param("bt"),
            1,
            'L',
            0
        );
    }

    /**
     *
     */
    function Table($sql, $db, $height, $border, $align, $fond, $police,
        $size, $multiplicateur, $flag_entete) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();

        /**
         *
         */
        //
        $edition_params = array(
            "height" => $height,
            "border" => $border,
            "align" => $align,
            "fond" => $fond,
            "police" => $police,
            "size" => $size,
            "multiplicateur" => $multiplicateur,
            "flag_entete" => $flag_entete,
        );
        //
        $this->set_edition_params($edition_params);

        /**
         *
         */
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sql."\");",
            VERBOSE_MODE
        );
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        $info = $res->tableInfo();
        //
        $nbchamp = count($info);
        //
        $nbrow = $res->numrows();

        /**
         *
         */
        //
        $this->SetFont(
            $this->get_edition_param("police"),
            '',
            $this->get_edition_param("size")
        );
        //
        $this->ln();
        //
        if ($flag_entete == 1) {
            //
            $this->SetFillColor(
                $this->get_edition_param("C1fondentete"),
                $this->get_edition_param("C2fondentete"),
                $this->get_edition_param("C3fondentete")
            );
            $this->SetTextColor(
                $this->get_edition_param("C1entetetxt"),
                $this->get_edition_param("C2entetetxt"),
                $this->get_edition_param("C3entetetxt")
            );
            //
            for ($k = 0; $k < $nbchamp; $k++) {
                //
                $this->Cell(
                    $this->get_edition_param("l".$k),
                    $this->get_edition_param("heightentete"),
                    strtoupper($info[$k]['name']),
                    $this->get_edition_param("be".$k),
                    0,
                    $this->get_edition_param("ae".$k),
                    $this->get_edition_param("fondentete")
                );
            }
            //
            $this->ln();
        }
        //
        $this->SetTextColor(
            $this->get_edition_param("C1"),
            $this->get_edition_param("C2"),
            $this->get_edition_param("C3")
        );
        //
        $total = array();
        //
        $flagtot = 0;
        $flagmoy = 0;
        //
        $couleur = 1;
        //
        $cptenr = 0;
        //
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            //
            $this->SetFillColor(
                $this->get_edition_param("C1fond".$couleur),
                $this->get_edition_param("C2fond".$couleur),
                $this->get_edition_param("C3fond".$couleur)
            );
            $couleur = ($couleur == 1 ? 2 : 1);
            //
            for ($k = 0; $k < $nbchamp; $k++) {
                //
                if ($this->get_edition_param("chnd".$k) != null
                    && trim($this->get_edition_param("chnd".$k)) != ""
                    && $this->get_edition_param("chnd".$k) != 999) {
                    //
                    $text = number_format(
                        $row[$info[$k]['name']],
                        $this->get_edition_param("chnd".$k),
                        ',',
                        ' '
                    );
                    //calcul totaux
                    if ($this->get_edition_param("chtot".$k) == 1
                        || $this->get_edition_param("chmoy".$k) == 1) {
                        //
                        if (!isset($total[$k])) {
                            $total[$k] = 0;
                        }
                        //
                        $total[$k] += $row[$info[$k]['name']];
                        //
                        if ($this->get_edition_param("chtot".$k) == 1
                            && $flagtot == 0) {
                            //
                            $flagtot = 1;
                        }
                        //
                        if ($this->get_edition_param("chmoy".$k) == 1
                            && $flagmoy == 0) {
                            //
                            $flagmoy = 1;
                        }
                    }
                } else {
                    //
                    if (defined("DBCHARSET") && (DBCHARSET == 'UTF8')) {
                        //
                        $text = utf8_decode($row[$info[$k]['name']]);
                    } else {
                        //
                        $text = $row[$info[$k]['name']];
                    }
                }
                //
                $this->Cell(
                    $this->get_edition_param("l".$k),
                    $this->get_edition_param("height"),
                    $text,
                    $this->get_edition_param("b".$k),
                    0,
                    $this->get_edition_param("a".$k),
                    $this->get_edition_param("fond")
                );
            }
            //
            $cptenr += 1;
            //
            $this->ln();
        }

        //affichage totaux----------------------------------------------------
        if ($flagtot == 1) {
            //
            for ($k = 0; $k < $nbchamp; $k++) {
                //
                if ($this->get_edition_param("chtot".$k) == 1
                    || $this->get_edition_param("chtot".$k) == 2) {
                    $text = number_format(
                        $total[$k],
                        $this->get_edition_param("chnd".$k),
                        ',',
                        ' '
                    );
                }elseif ($k == 0) {
                    $text = "TOTAL";
                } else {
                    $text = "";
                }
                //
                $this->Cell(
                    $this->get_edition_param("l".$k),
                    $this->get_edition_param("height"),
                    $text,
                    $this->get_edition_param("border"),
                    0,
                    $this->get_edition_param("a".$k),
                    $this->get_edition_param("fond")
                );
            }
            //
            $this->ln();
        }

        //affichage moyenne---------------------------------------------------
        if ($flagmoy == 1) {
            //
            for ($k = 0; $k < $nbchamp; $k++) {
                //
                if ($this->get_edition_param("chmoy".$k) == 1) {
                    $text = number_format(
                        ($total[$k]/$cptenr),
                        $this->get_edition_param("chnd".$k),
                        ',',
                        ' '
                    );
                } elseif ($k == 0) {
                    $text = "MOYENNE";
                } else {
                    $text = "";
                }
                //
                $this->Cell(
                    $this->get_edition_param("l".$k),
                    $this->get_edition_param("height"),
                    $text,
                    $this->get_edition_param("border"),
                    0,
                    $this->get_edition_param("a".$k),
                    $this->get_edition_param("fond")
                );
            }
        }
    }

    /**
     *
     */
    function get_edition_param($param) {
        //
        if (isset($this->edition_params[$param])) {
            return $this->edition_params[$param];
        }
        //
        return null;
    }

    /**
     *
     */
    function get_edition_params() {
        //
        return $this->edition_params;
    }

    /**
     *
     */
    function set_edition_param($param, $value) {
        //
        $this->edition_params[$param] = $value;
    }

    /**
     *
     */
    function set_edition_params($edition_params) {
        //
        $this->edition_params = array_merge(
            $this->edition_params,
            $edition_params
        );
    }

    /**
     *
     */
    function reset_edition_params() {
        //
        $this->edition_params = array();
    }

    // {{{ BEGIN - UTILS, LOGGER, ERROR

    /**
     * Initialisation de la classe 'application'.
     *
     * Cette méthode permet de vérifier que l'attribut f de la classe contient
     * bien la ressource utils du framework et si ce n'est pas le cas de la
     * récupérer.
     *
     * @return boolean
     */
    function init_om_application() {
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
    function addToLog($message, $type = DEBUG_MODE) {
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

    /**
     * Cette méthode ne doit plus être appelée, c'est
     * '$this->f->isDatabaseError($res)' qui s'occupe d'afficher le message
     * d'erreur et de faire le 'die()'.
     *
     * @param null $debuginfo Deprecated.
     * @param null $messageDB Deprecated.
     * @param null $table     Deprecated.
     *
     * @return void
     * @deprecated
     */
    function erreur_db($debuginfo, $messageDB, $table) {
        die(__("Erreur de base de donnees. Contactez votre administrateur."));
    }

    // }}} END - UTILS, LOGGER, ERROR

}
