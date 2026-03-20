<?php
/**
 * Ce fichier permet de declarer la classe PDF.
 *
 * @package framework_openmairie
 * @version SVN : $Id: fpdf_etiquette.php 4348 2018-07-20 16:49:26Z softime $
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
     *
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
 * d'etiquettes de resultats de requetes dans la base de donnnees.
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
     *
     */
    function Get_Height_Chars($pt) {

        // Give the height for a char size given.
        //
        // Tableau de concordance entre la hauteur des caracteres et de l'espacement entre les lignes
        $_Table_Hauteur_Chars = array(6=>2, 7=>2.5, 8=>3, 9=>4, 10=>5, 11=>6, 12=>7, 13=>8, 14=>9, 15=>10);
        if (in_array($pt, array_keys($_Table_Hauteur_Chars))) {
            return $_Table_Hauteur_Chars[$pt];
        } else {
            return 100; // There is a prob..
        }

    }

    /**
     *
     */
    function Set_Font_Size($pt, $Char_Size, $Line_Height) {

        if ($pt > 3) {
            $this->$Char_Size = $pt;
            $this->$Line_Height = $this->Get_Height_Chars($pt);
            $this->SetFontSize($this->$Char_Size);
        }

    }

    /**
     *
     */
    function Table_position($sql, $db, $param, $champs, $texte, $champs_compteur, $img) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();

        /**
         *
         */
        //
        $edition_params = array(
            "_margin_left" => $param[0],
            "_margin_top" => $param[1],
            "_x_space" => $param[2],
            "_y_space" => $param[3],
            "_x_number" => $param[4],
            "_y_number" => $param[5],
            "_width" => $param[6],
            "_height" => $param[7],
            "_char_size" => $param[8],
            "_line_height" => $param[9],
            0,
            0,
            "size" => $param[12],
            "cadrechamps" => $param[13],
            "cadre" => $param[14],
            "champs" => $champs,
            "texte" => $texte,
            "champs_compteur" => $champs_compteur,
            "img" => $img,
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
        $this->isDatabaseError($res);
        //
        $info = $res->tableInfo();
        //
        $nbchamp = count($info);
        //
        $nbrow = $res->numrows();

        /**
         *
         */
        if ($nbrow == 0) {
            //
            $this->Set_Font_Size(
                $this->get_edition_param("size"),
                $this->get_edition_param("_char_size"),
                $this->get_edition_param("_line_height")
            );
            //
            $this->SetTextColor(245, 34, 108);
            //
            $_PosX = $this->get_edition_param("_margin_left");
            $_PosY = $this->get_edition_param("_margin_top");
            //
            $this->SetXY($_PosX, $_PosY);
            // Affichage de la cellule.
            $this->MultiCell(
                $this->get_edition_param("_width"),
                $this->get_edition_param("_height"),
                __("Aucun enregistrement."),
                1,
                "C"
            );
            //
            $res->free();
            //
            return;
        }

        /**
         *
         */
        function display_elem_in_bloc($pdf, $_PosX, $_PosY, $elem_offset_x,
            $elem_offset_y, $elem_width, $elem_override_bold,
            $elem_override_size, $elem_content) {
            //
            $champ_bold = '';
            if ($elem_override_bold == 1) {
                $champ_bold = 'B';
            }
            //
            $champ_size = $pdf->get_edition_param("size");
            if ($elem_override_size > 0) {
                $champ_size = $elem_override_size;
            }
            //
            $pdf->SetFont(
                $pdf->get_edition_param("police"),
                $champ_bold,
                $champ_size
            );
            //
            $pdf->Set_Font_Size(
                $champ_size,
                $pdf->get_edition_param("_char_size"),
                $pdf->get_edition_param("_line_height")
            );
            //
            $pdf->SetXY(
                $_PosX + $elem_offset_x,
                $_PosY + $elem_offset_y
            );
            //
            if ($elem_width == 0
                || $elem_width > $pdf->get_edition_param("_width")) {
                $elem_width = $pdf->get_edition_param("_width");
            }
            // Affichage de la cellule.
            $pdf->MultiCell(
                $elem_width,
                $pdf->get_edition_param("_line_height"),
                $elem_content,
                $pdf->get_edition_param("cadrechamps")
            );
            //
            $pdf->SetXY($_PosX, $_PosY);
        }


        //
        $_cptx = 0;
        $_cpty = 0;
        // On initialise le compteur de blocs à 0.
        $compteur = 0;
        //
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            // On incrémente le compteur de blocs affichés.
            $compteur++;
            // Définition de la position (coordonées X et Y) du bloc en
            // fonction des marges de la page, de la largeur d'un bloc, de la
            // hauteur d'un bloc,
            $_PosX = $this->get_edition_param("_margin_left") + ($_cptx * ($this->get_edition_param("_width") + $this->get_edition_param("_x_space")));
            $_PosY = $this->get_edition_param("_margin_top") + ($_cpty * ($this->get_edition_param("_height") + $this->get_edition_param("_y_space"))) + $_cpty;
            //
            $this->SetXY($_PosX, $_PosY);
            // Affichage de la cellule.
            $this->MultiCell(
                $this->get_edition_param("_width"),
                $this->get_edition_param("_height"),
                "",
                $this->get_edition_param("cadre")
            );
            ////
            //
            if (isset($champs_compteur[0]) && $champs_compteur[0] == 1) {
                //
                $elem_content = $compteur;
                //
                $elem_offset_x = (isset($champs_compteur[1]) ? $champs_compteur[1] : 0);
                $elem_offset_y = (isset($champs_compteur[2]) ? $champs_compteur[2] : 0);
                $elem_width = (isset($champs_compteur[3]) ? $champs_compteur[3] : 0);
                //
                $elem_override_bold = (isset($champs_compteur[4]) ? $champs_compteur[4] : "");
                $elem_override_size = (isset($champs_compteur[5]) ? $champs_compteur[5] : "");
                //
                display_elem_in_bloc(
                    $this,
                    $_PosX,
                    $_PosY,
                    $elem_offset_x,
                    $elem_offset_y,
                    $elem_width,
                    $elem_override_bold,
                    $elem_override_size,
                    $elem_content
                );
            }
            //
            for ($j = 0; $j < $nbchamp; $j++) {
                //
                if (!isset($champs[$info[$j]['name']])) {
                    continue;
                }
                //
                $field_params = $champs[$info[$j]['name']];
                //
                if (isset($field_params[3]) && $field_params[3] == 1) {
                    $text = number_format($row[$info[$j]['name']], 0);
                } else {
                    $text = $row[$info[$j]['name']];
                }
                //
                $elem_content = sprintf(
                    "%s%s%s",
                    (isset($field_params[0]) ? $field_params[0] : ""),
                    $text,
                    (isset($field_params[1]) ? $field_params[1] : "")
                );
                //
                $elem_offset_x = (isset($field_params[2][0]) ? $field_params[2][0] : 0);
                $elem_offset_y = (isset($field_params[2][1]) ? $field_params[2][1] : 0);
                $elem_width = (isset($field_params[2][2]) ? $field_params[2][2] : 0);
                //
                $elem_override_bold = (isset($field_params[2][3]) ? $field_params[2][3] : "");
                $elem_override_size = (isset($field_params[2][4]) ? $field_params[2][4] : "");
                //
                display_elem_in_bloc(
                    $this,
                    $_PosX,
                    $_PosY,
                    $elem_offset_x,
                    $elem_offset_y,
                    $elem_width,
                    $elem_override_bold,
                    $elem_override_size,
                    $elem_content
                );
            }
            // Gestion des images
            foreach ($img as $elem) {
                //
                $this->SetXY($_PosX + $elem[1], $_PosY + $elem[2]);
                //
                $this->Image(
                    $elem[0],
                    $_PosX + $elem[1],
                    $_PosY + $elem[2],
                    $elem[3],
                    $elem[4],
                    $elem[5]
                );
                //
                $this->SetXY($_PosX, $_PosY);
            }
            // ////
            //
            foreach ($texte as $elem) {
                //
                if (!isset($elem[0])) {
                    continue;
                }
                $elem_content = $elem[0];
                //
                $elem_offset_x = (isset($elem[1]) ? $elem[1] : 0);
                $elem_offset_y = (isset($elem[2]) ? $elem[2] : 0);
                $elem_width = (isset($elem[3]) ? $elem[3] : 0);
                //
                $elem_override_bold = (isset($elem[4]) ? $elem[4] : 0);
                $elem_override_size = (isset($elem[5]) ? $elem[5] : 0);
                //
                display_elem_in_bloc(
                    $this,
                    $_PosX,
                    $_PosY,
                    $elem_offset_x,
                    $elem_offset_y,
                    $elem_width,
                    $elem_override_bold,
                    $elem_override_size,
                    $elem_content
                );
            }
            //// Gestion du positionnement des blocs dans la page et des
            //// sauts de page.
            // On incrémente le compteur de colonnes.
            $_cptx++;
            // Si le compteur de colonnes est le nombre maximum de colonnes
            // par page.
            if ($_cptx == $this->get_edition_param("_x_number")) {
                // On initialise le compteur de colonnes à 0.
                $_cptx = 0;
                // On incrémente le compteur de lignes.
                $_cpty++;
                // Si le compteur de lignes est le nombre maximum de lignes
                // par page.
                if ($_cpty == $this->get_edition_param("_y_number")) {
                    // On initialise le compteur de lignes à 0.
                    $_cpty = 0;
                    // On initialise une nouvelle page seulement si on ne se
                    // trouve pas sur le dernier bloc à afficher (si le
                    // compteur de blocs déjà affichés est bien inférieur
                    // au nombre de résultats de la requête).
                    if ($compteur < $nbrow) {
                        // Nouvelle page.
                        $this->AddPage();
                    }
                }
            }
        }
        //
        $res->free();
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
