<?php
/**
 * Ce fichier permet de declarer la classe PDF.
 *
 * @package framework_openmairie
 * @version SVN : $Id: fpdf_etat.php 4348 2018-07-20 16:49:26Z softime $
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
 * Inclusion de la classe TCPDF qui permet de generer des fichiers PDF.
 */
require_once "tcpdf.php";

/**
 * Cette classe surcharge la classe standard TCPDF pour permettre la gestion
 * d'états et de sous-états de résultats de requêtes dans la base de donnnées.
 */
class PDF extends TCPDF {

    /**
     * Instance de la classe 'application'.
     * @var null|application
     */
    var $f = null;

    /**
     * Variables utilisées pour la génération des code barres.
     */
    // Tableau des codes 128
    var $T128;
    // Jeu de caractères éligibles au code 128
    var $ABCset="";
    // Set A du jeu de caractères éligibles
    var $Aset="";
    // Set B du jeu de caractères éligibles
    var $Bset="";
    // Set C du jeu de caractères éligibles
    var $Cset="";
    //Convertisseur source des jeux vers le tableau
    var $SetFrom;
    // Convertisseur destination des jeux vers le tableau
    var $SetTo;
    // Caractères de sélection de jeu au début du code 128
    var $JStart = array("A"=>103, "B"=>104, "C"=>105);
    // Caractères de changement de jeu
    var $JSwap = array("A"=>101, "B"=>100, "C"=>99);

    /**
     * Filigrane actif ou non
     */
    var $watermark;

    /**
     *
     */
    var $header = null;

    /**
     *
     */
    var $footer = null;

    /**
     * This is the class constructor from TCPDF library
     *
     * It is extended only to set DefaultdiplayMode to 'real' as othet parts of openMairie code do when using FPDF library
     *
     * It allows to set up the page format, the orientation and the measure unit used in all the methods (except for the font sizes).
     *
     * IMPORTANT: Please note that this method sets the mb_internal_encoding to ASCII, so if you are using the mbstring module functions with TCPDF you need to correctly set/unset the mb_internal_encoding when needed.
     *
     * @param $orientation (string) page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li><li>'' (empty string) for automatic orientation</li></ul>
     * @param $unit (string) User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
     * @param $format (mixed) The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
     * @param $unicode (boolean) TRUE means that the input text is unicode (default = true)
     * @param $encoding (string) Charset encoding (used only when converting back html entities); default is UTF-8.
     * @param $diskcache (boolean) DEPRECATED FEATURE
     * @param $pdfa (boolean) If TRUE set the document to PDF/A mode.
     * @public
     * @see getPageSizeFromFormat(), setPageFormat()
     */
    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
        // call parent
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        // full width display mode
        $this->SetDisplayMode('real');
    }
    /**
     * Returns value of $ZoomMode which is a protected variable, to allow PHP UNITary test
     *
     * @param none
     *
     * @returns string $ZoomMode protected variable value
     */
    public function getZoomMode() {
        return $this->ZoomMode;
    }
    
    /**
     *
     */
    function set_footer($params = array()) {
        //
        $this->footer = true;
        //
        $this->footer_params = $params;
    }

    /**
     *
     */
    function set_header($params = array()) {
        //
        $this->header = true;
        //
        $this->header_params = $params;
    }

    /**
     * Méthode d'affichage de l'entête de page.
     *
     * @return void
     */
    function Header() {

        /**
         *
         */
        // Si le paramétrage du filigrane est actif
        if ($this->getWatermark() == true) {
            // On l'ajoute sur la page en cours
            $this->displayWatermark();
        }

        /**
         *
         */
        //
        if ($this->header !== true) {
            //
            return;
        }
        //
        $params = $this->header_params;
        //
        $this->writeHTMLCell(
            // (float) Cell width. If 0, the cell extends up to the right margin.
            0,
            // (float) Cell minimum height. The cell extends automatically if needed.
            0,
            // (float) upper-left corner X coordinate
            '',
            // (float) upper-left corner Y coordinate
            $params["offset"],
            // (string) html text to print. Default value: empty string.
            $params["html"],
            // (mixed) Indicates if borders must be drawn around the cell.
            0,
            // (int) Indicates where the current position should go after the call.
            0,
            // (boolean) Indicates if the cell background must be painted (true) or transparent (false).
            false,
            // (boolean) if true reset the last cell height (default true).
            true,
            // (string) Allows to center or align the text.
            "L",
            // (boolean) if true, uses internal padding and automatically adjust it to account for line width.
            false
        );
    }

    /**
     * Méthode d'affichage du pied de page.
     *
     * @return void
     */
    function Footer() {

        /**
         *
         */
        //
        if ($this->footer !== true) {
            //
            return;
        }
        //
        $params = $this->footer_params;

        /**
         * Positionnement du footer.
         *
         * Le positionnement se fait par un offset en mm depuis le bord inférieur
         * du document.
         */
        //
        if (isset($params["offset"])
            && $params["offset"] != ""
            && intval($params["offset"]) > 0 ) {
            //
            $params["offset"] = intval($params["offset"]);
        } else {
            $params["offset"] = 1;
        }
        //
        $this->SetY((-1)*($params["offset"]));
        //
        $this->writeHTMLCell(
            // (float) Cell width. If 0, the cell extends up to the right margin.
            0,
            // (float) Cell minimum height. The cell extends automatically if needed.
            0,
            // (float) upper-left corner X coordinate
            '',
            // (float) upper-left corner Y coordinate
            '',
            // (string) html text to print. Default value: empty string.
            $params["html"],
            // (mixed) Indicates if borders must be drawn around the cell.
            0,
            // (int) Indicates where the current position should go after the call.
            2,
            // (boolean) Indicates if the cell background must be painted (true) or transparent (false).
            false,
            // (boolean) if true reset the last cell height (default true).
            true,
            // (string) Allows to center or align the text.
            "L",
            // (boolean) if true, uses internal padding and automatically adjust it to account for line width.
            false
        );
    }

    /**
     * Paramètre l'activation du filigrane.
     *
     * @param boolean $state True si actif.
     *
     * @return void
     */
    function setWatermark($state = true) {
        $this->watermark = $state;
    }

    /**
     * Retourne l'état d'activation du filigrane.
     *
     * @return boolean True si actif.
     */
    function getWatermark() {
        return $this->watermark;
    }

    /**
     * Affiche un filigrane "DOCUMENT DE TRAVAIL" en fond si appelé depuis
     * le header. Il est composé de deux lignes obliques qui barrent la page.
     *
     * @return void
     */
    function displayWatermark() {
        // Police (police, style, taille)
        $this->SetFont('courier', 'B', 40);
        // Couleur (niveau de gris)
        $this->SetTextColor(200);
        // Position, texte et angle de rotation
        $text1 = "IL - DOCUMENT DE TRAVAIL - DOCUMENT";
        $text2 = "DE TRAVAIL - DOCUMENT DE TRAVAIL - DOCUMENT DE";
        $this->TextWithRotation(0, 180, $text1, 45);
        $this->TextWithRotation(0, 340, $text2, 45);
    }

    /**
     * Méthode de rotation de texte.
     *
     * @param [type]  $xr         [description]
     * @param [type]  $yr         [description]
     * @param [type]  $txtr       [description]
     * @param [type]  $txtr_angle [description]
     * @param integer $font_angle [description]
     *
     * @return void
     */
    function TextWithRotation($xr, $yr, $txtr, $txtr_angle, $font_angle = 0) {
        //nouvelle fonction : ROTATION texte 90 45 .....
        $txtr=str_replace(
            ')',
            '\\)',
            str_replace(
                '(',
                '\\(',
                str_replace(
                    '\\',
                    '\\\\',
                    $txtr
                )
            )
        );
        //
        $font_angle+=90+$txtr_angle;
        $txtr_angle*=M_PI/180;
        $font_angle*=M_PI/180;
        //
        $txtr_dx=cos($txtr_angle);
        $txtr_dy=sin($txtr_angle);
        $font_dx=cos($font_angle);
        $font_dy=sin($font_angle);
        //
        $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',
            $txtr_dx,
            $txtr_dy,
            $font_dx,
            $font_dy,
            $xr*$this->k,
            ($this->h-$yr)*$this->k,
            $txtr
        );
        if ($this->ColorFlag) {
            $s='q '.$this->TextColor.' '.$s.' Q';
        }
        $this->_out($s);
    }

    /**
     * Initialisation des sous-états : ajout d'une balise tcpdf.
     *
     * @param string  $etat   identifiant de l'état
     * @param string  $corps  corps de l'état
     * @param integer $om_collectivite_idx Identifiant de la collectivité
     *
     * @return string Corps de l'état avec initialisation des sous-états.
     */
    function initSousEtats($etat, $corps, $om_collectivite_idx = null) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();
        // Si le html fourni n'est pas valide, loadHTML lève des erreurs PHP
        // par défaut. On désactive l'affichage de ces erreurs pour ne pas
        // bloquer la génération des fichiers PDF.
        libxml_use_internal_errors(true);
        // On initialise le html passé en paramètre et on le charge dans un DOM
        $dom = new DOMDocument;
        $dom->loadHTML($corps);
        // On vide les erreurs
        libxml_clear_errors();
        // On supprime le doctype du dom, l'objectif est d'avoir uniquement
        // les balises nécessaires pour éviter des problèmes d'interlignage
        $dom->removeChild($dom->doctype);
        // On transforme le DOM en XPATH pour pouvoir le requêter plus
        // facilement.
        $xPath = new DOMXPath($dom);
        // Gestion de la fonctionnalité sousetat
        // Pour chaque marqueur TinyMCE (class='mce_sousetat'), on insère un
        // marqueur TCPDF pour qu'il interprète le sousetat correctement
        foreach ($xPath->query("//*[contains(@class, 'mce_sousetat')]") as $node) {

            // On récupère l'attribut id de ce marqueur SOUSETAT pour récupérer
            // l'identifiant du sous-état.
            $nomsousetat = $node->getAttribute("id");

            // On récupère les paramètres du sous-état selon une logique
            // particulière :
            //  - 1 - si le sous-état existe sur la collectivité
            //        "$_SESSION['collectivite']"" et est actif alors on le
            //        récupère
            //  - 2 - si le sous-état existe sur la collectivité de niveau
            //        supérieur ou la collectivité fournie en paramètre
            //        et est actif alors on le récupère
            //  - 3 - si le sous-état existe sur la collectivité de niveau
            //        supérieur ou la collectivité fournie en paramètre
            //        et n'est pas actif alors on le récupère
            // On récupère l'enregistrement 'om_sousetat' de la collectivité en
            // cours dans l'état 'actif'
            $sql = " select * from ".DB_PREFIXE."om_sousetat ";
            $sql .= " where id='".trim($nomsousetat)."' ";
            $sql .= " and actif IS TRUE ";
            $sql .= " and om_collectivite='".$om_collectivite_idx."' ";
            // Exécution de la requête
            $res2 = $this->f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res2);
            // Si on obtient aucun résultat
            if ($res2->numrows() == 0) {
                // On libère le résultat de la requête précédente
                $res2->free();
                // On récupère l'identifiant de la collectivité de niveau 2
                $sql = "select om_collectivite from ".DB_PREFIXE."om_collectivite ";
                $sql .= " where niveau='2' ";
                // Exécution de la requête
                $niveau = $this->f->db->getone($sql);
                // Logger
                $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $this->f->isDatabaseError($niveau);
                // On récupère l'enregistrement 'om_sousetat' de la collectivité
                // de niveau 2 dans l'état 'actif'
                $sql = " select * from ".DB_PREFIXE."om_sousetat ";
                $sql .= " where id='".trim($nomsousetat)."'";
                $sql .= " and actif IS TRUE ";
                $sql .= " and om_collectivite='".($niveau == "" ? -1 : $niveau)."' ";
                // Exécution de la requête
                $res2 = $this->f->db->query($sql);
                // Logger
                $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $this->f->isDatabaseError($res2);
                // Si on obtient aucun résultat
                if ($res2->numrows() == 0) {
                    // On libère le résultat de la requête précédente
                    $res2->free();
                    // On récupère l'enregistrement 'om_sousetat' de la collectivité de
                    // niveau 2 dans n'importe quel état
                    $sql = " select * from ".DB_PREFIXE."om_sousetat ";
                    $sql .= " where id='".trim($nomsousetat)."' ";
                    $sql .= " and om_collectivite='".($niveau == "" ? -1 : $niveau)."' ";
                    // Exécution de la requête
                    $res2 = $this->f->db->query($sql);
                    // Logger
                    $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                    // Vérification d'une éventuelle erreur de base de données
                    $this->f->isDatabaseError($res2);
                    // Si on obtient aucun résultat
                    if ($res2->numrows() == 0) {
                        // On libère le résultat de la requête précédente
                        $res2->free();
                        // On positionne la valeur null dans res2 pour
                        // l'utiliser comme marqueur de l'impossibilité de
                        // trouver un sous-état correpsondant à l'id
                        // fourni.
                        $res2 = null;
                    }
                }
            }
            // Définition de la valeur $collectivité pour inclure le fichier de
            // substitution varetatpdf.inc
            $collectivite = $this->f->getCollectivite($om_collectivite_idx);

            // Si aucun sous-état n'a été trouvé
            if ($res2 == null) {
                // On affiche un message d'erreur dans l'édition
                $message = sprintf(
                    __("Erreur de parametrage. Le sous-etat '%s' n'existe pas."),
                    trim($nomsousetat)
                );
                //
                $node->nodeValue = '';
                $fragment = $dom->createDocumentFragment();
                $fragment->appendXML(sprintf('<span class="error">%s</span>', $message));
                $node->appendChild($fragment);
                // On passe à l'itération suivante
                continue;
            }

            //
            while ($sousetat =& $res2->fetchRow(DB_FETCHMODE_ASSOC)) {
                //
                $sql = '';
                $titre = '';
                // Variables statiques contenant des paramètres à remplacer
                $sql = $sousetat['om_sql'];
                $titre = $sousetat['titre'];
                // Remplacement des paramètres dans le fichier ../dyn/varsousetatpdf.inc
                // ou ../dyn/varetatpdf.inc
                if (file_exists("../dyn/varsousetatpdf.inc.php")) {
                    include "../dyn/varsousetatpdf.inc.php";
                } elseif (file_exists("../dyn/varetatpdf.inc")) {
                    include "../dyn/varetatpdf.inc";
                }
                // remplacement d'idx par sa valeur
                $sql = str_replace('&idx', $_GET["idx"], $sql);
                // définition du schéma
                $sql = str_replace('&DB_PREFIXE', DB_PREFIXE, $sql);
                //
                $sousetat['om_sql'] = $sql;
                $sousetat['titre'] = $titre;

                $params = $this->serializeTCPDFtagParameters(
                array(
                    null,
                    $etat,
                    $sousetat)
                );
                // On vide le noeud courant sur lequel on a trouvé le marqueur
                // TinyMCE
                $node->nodeValue = '';
                // On compose le marqueur TCPDF
                $fragment = $dom->createDocumentFragment();
                $fragment->appendXML('<tcpdf method="sousetatdb" params="'.$params.'" />');
                // On insère le marqueur TCPDF dans le noeud courant
                $node->appendChild($fragment);
            }
        }
        // On retransforme le DOM en chaîne de caractères.
        $data = $dom->saveHTML();
        // On supprime les balises html, head, body du dom, l'objectif est
        // d'avoir uniquement les balises nécessaires pour éviter des problèmes
        // d'interlignage
        $data = str_replace("<html><head>", "", $data);
        $data = str_replace("</head><body>", "", $data);
        $data = str_replace("</body></html>", "", $data);
        // On retourne le html préparé.
        return $data;
    }

    /**
     * Affiche la ligne d'entête du tableau d'un sous-état.
     *
     * @param array $sousetat Tableau de paramétrage du sous-état.
     * @param array $info     Liste des colonnes du sous-état.
     *
     * @return void
     */
    function entete_sous_etat($sousetat, $info) {
        // Si le marqueur permettant de paramétrer l'affichage ou non de
        // la ligne d'entête du tableau n'est pas activé alors on sort de
        // la méthode.
        if ($sousetat['entete_flag'] != 1) {
            return;
        }
        // Le nombre de champs correspond au nombre d'éléments dans la
        // liste des colonnes du sous-état.
        $nbchamp = count($info);
        // On définit la couleur de remplissage de la ligne d'entête du
        // tableau.
        $this->SetFillColor(
            $sousetat['entete_fondcouleur'][0],
            $sousetat['entete_fondcouleur'][1],
            $sousetat['entete_fondcouleur'][2]
        );
        // On définit la couleur du texte de la ligne d'entête du tableau.
        $this->SetTextColor(
            $sousetat['entete_textecouleur'][0],
            $sousetat['entete_textecouleur'][1],
            $sousetat['entete_textecouleur'][2]
        );
        // On boucle sur chaque colonne.
        for ($k = 0; $k < $nbchamp; $k++) {
            // Valeur à afficher
            $value = mb_strtoupper(__($info[$k]['name']), "UTF-8");
            // En fonction des paramètres d'orientation, on utilise des
            // méthodes d'affichage de cellule différentes.
            if (!isset($sousetat['entete_orientation'])
                || !isset($sousetat['entete_orientation'][$k])
                || $sousetat['entete_orientation'][$k] == 0) {
                // Soit l'orientation de la cellule n'est pas définie
                // ou est paramétrée à 0, alors on affiche le texte de manière
                // standard (horizontal) dans la cellule.
                // Affichage de la cellule.
                $this->Cell(
                    $sousetat['cellule_largeur'][$k],
                    $sousetat['entete_hauteur'],
                    $value,
                    $sousetat['entetecolone_bordure'][$k],
                    0,
                    $sousetat['entetecolone_align'][$k],
                    $sousetat['entete_fond'],
                    '',
                    1
                );
            } else {
                // Soit l'orientation de la cellule est définie avec un
                // paramètre et on affiche la cellule puis le texte avec la
                // rotation demandée.
                // Affichage de la cellule.
                $this->Cell(
                    $sousetat['cellule_largeur'][$k],
                    $sousetat['entete_hauteur'],
                    '',
                    $sousetat['entetecolone_bordure'][$k],
                    0,
                    $sousetat['entetecolone_align'][$k],
                    $sousetat['entete_fond']
                );
                //
                if ($sousetat['entete_orientation'][$k] > 0) {
                    // Calcul des paramètres de la rotation.
                    $xd=$this->Getx();
                    $yd=$this->Gety();
                    $xd=$xd-(floor($sousetat['cellule_largeur'][$k]/2));
                    if ($sousetat['entete_orientation'][$k] < 91) {
                        $yd=($yd+$sousetat['entete_hauteur'])-1;
                    } else {
                        $yd=($yd+$sousetat['entete_hauteur'])-5;
                    }
                } elseif ($sousetat['entete_orientation'][$k] < 0) {
                    // Calcul des paramètres de la rotation.
                    $xd=$this->Getx();
                    $yd=$this->Gety();
                    $xd = $xd -
                        floor((($sousetat['cellule_largeur'][$k]/2))) -
                        floor(strlen (__($info[$k]['name'])));
                    $yd=($yd+$sousetat['entete_hauteur'])-3;
                }
                // Affichage du texte rotaté.
                $this->TextWithRotation(
                    $xd,
                    $yd,
                    $value,
                    $sousetat['entete_orientation'][$k],
                    0
                );
            }
        }
        // On positionne le curseur en début de ligne suivante.
        $this->ln();
    }

    /**
     * Méthode d'affichage des sous états depuis la version 4.0.0.
     *
     * @param database $db       handler database
     * @param string   $etat     identifiant de l'état
     * @param array    $sousetat paramétrage du sous-état à afficher
     *
     * @return void
     */
    function sousetatdb($db = null, $etat, $sousetat) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();

        // Exécution de la requête
        $res = $this->f->db->query($sousetat['om_sql']);
        // Logger
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sousetat['om_sql']."\");",
            VERBOSE_MODE
        );
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        $info = $res->tableInfo();
        //
        $nbchamp = count($info);

        // Les paramètres de sous-états arrivent pour la plupart sous forme
        // de chaine de caractères '123-123-242' pour les couleurs ou '0|90|45'
        // pour l'orientation ou 'TBL|TBL|TBLR' pour les bordures. Il est
        // nécessaire de reformater ces paramètres en tableau pour en
        // faciliter leur utilisation.
        $replacements = array(
            //
            array(
                "separator" => "-",
                "keys" => array(
                    "titrefondcouleur",
                    "titretextecouleur",
                    "entete_fondcouleur",
                    "entete_textecouleur",
                    "bordure_couleur",
                    "se_fond1",
                    "se_fond2",
                    "cellule_fondcouleur_total",
                    "cellule_fondcouleur_moyenne",
                    "cellule_fondcouleur_nbr",
                ),
            ),
            //
            array(
                "separator" => "|",
                "keys" => array(
                    "entete_orientation",
                    "entetecolone_bordure",
                    "entetecolone_align",
                    "cellule_largeur",
                    "cellule_bordure_un",
                    "cellule_bordure",
                    "cellule_align",
                    "cellule_bordure_total",
                    "cellule_align_total",
                    "cellule_bordure_moyenne",
                    "cellule_align_moyenne",
                    "cellule_bordure_nbr",
                    "cellule_align_nbr",
                    "cellule_numerique",
                    "cellule_total",
                    "cellule_moyenne",
                    "cellule_compteur",
                ),
            ),
        );
        // On remplace toutes les chaines de caractères par des tableaux
        foreach ($replacements as $replacement) {
            foreach ($replacement["keys"] as $key) {
                $sousetat[$key] = explode(
                    $replacement["separator"],
                    $sousetat[$key]
                );
            }
        }

        // Gestion de l'espace vide avant le sous-état.
        $this->ln(intval($sousetat['intervalle_debut']));

        // On définit la couleur des bordures
        $this->SetDrawColor(
            $sousetat['bordure_couleur'][0],
            $sousetat['bordure_couleur'][1],
            $sousetat['bordure_couleur'][2]
        );

        // Gestion de l'affichage du titre du sous-état.
        // On définit la couleur de remplissage du bloc de titre.
        $this->SetFillColor(
            $sousetat['titrefondcouleur'][0],
            $sousetat['titrefondcouleur'][1],
            $sousetat['titrefondcouleur'][2]
        );
        // On définit la couleur du texte du titre
        $this->SetTextColor(
            $sousetat['titretextecouleur'][0],
            $sousetat['titretextecouleur'][1],
            $sousetat['titretextecouleur'][2]
        );
        // On définit les paramètre de la police du titre
        $this->SetFont(
            $sousetat["titrefont"],
            $sousetat["titreattribut"],
            $sousetat["titretaille"]
        );
        // Affichage de la cellule.
        $this->MultiCell(
            $sousetat['tableau_largeur'],
            $sousetat["titrehauteur"],
            $sousetat["titre"],
            $sousetat["titrebordure"],
            $sousetat["titrealign"],
            $sousetat["titrefond"]
        );

        // On définit la police du texte du tableau. Attention, la police de
        // caractère est définie dans l'édition et non dans le sous-état.
        $this->SetFont($etat['se_font'], '', $sousetat['tableau_fontaille']);

        // ENTETE
        $this->entete_sous_etat($sousetat, $info);
        // On positionne la couleur de police pour l'affichage des celulles
        // de données.
        $this->SetTextColor(
            $etat['se_couleurtexte'][0],
            $etat['se_couleurtexte'][1],
            $etat['se_couleurtexte'][2]
        );

        // On initialise les marqueurs permettant de vérifier si une opération
        // est configurée sur au moins une des colonnes à 0. Ces marqueurs
        // seront passés à 1 si une des colonnes a une opération paramétrée.
        $flagtotal = 0;
        $flagmoyenne = 0;
        $flagcompteur = 0;
        // On initialise un tableau permettant de stocker les valeurs pour
        // réaliser les éventuelles opération
        $total = array();
        for ($j = 0; $j < $nbchamp; $j++) {
            $total[$j] = 0;
        }
        // On initialise le compteur d'enregistrements.
        $cptenr = 0;
        // On initialise la couleur de remplissage de la première ligne. Cette
        // valeur est liée au nom des clés se_fond1 et se_fond2.
        $couleur = 1;

        // On boucle sur chaque enregistrement renvoyé par la requête.
        while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)) {

            //preparer multiligne
            $max_ln=1;
            $multi_height=$sousetat['cellule_hauteur'];
            //Etablir nb lignes necessaires et preparer chaines avec \n
            for ($j=0; $j<$nbchamp; $j++) {
                // A ajouter eventuellement dans .sousetat.inc
                // //a 1 texte organise en multiligne, avec autre valeur texte compresse
                // $sousetat['cellule_multiligne']=
                //      array("0","0","1","1","1","0","0","1","1","0");
                // //pourcentage de hauteur utilisee pour 1 ligne d'une cellule multiligne
                // $sousetat['cellule_hautmulti']=1/2;
                if (isset($sousetat['cellule_multiligne'])) {
                    //si variable definie, valeur a 1 => multiligne
                    if ($sousetat['cellule_multiligne'][$j] == 1) {
                        $txt = mb_convert_encoding($row[$info[$j]['name']], "ASCII", "UTF-8");
                        $t_ln=$this->PrepareMultiCell(
                            $sousetat['cellule_largeur'][$j],
                            $txt
                        );
                        if ($t_ln>$max_ln) {
                            $max_ln=$t_ln;
                        }
                    }
                    // sinon compression
                } else {
                    $txt = mb_convert_encoding($row[$info[$j]['name']], "ASCII", "UTF-8");
                    //si variable non definie, multiligne par defaut
                    $t_ln=$this->PrepareMultiCell(
                        $sousetat['cellule_largeur'][$j],
                        $txt
                    );
                    if ($t_ln > $max_ln) {
                        $max_ln = $t_ln;
                    }
                }
            }
            //fixation de la nouvelle hauteur si plus d'1 ligne selon quota
            //hauteur/nblignesmulti ou pas
            if ($max_ln > 1) {
                if (isset($sousetat['cellule_hautmulti'])) {
                    //si valeur cellule_hautmulti existe
                    $multi_height=
                        $max_ln*
                        $sousetat['cellule_hauteur']*
                        $sousetat['cellule_hautmulti'];
                } else { //sinon valeur par defaut 1/2
                    $multi_height=$max_ln*$sousetat['cellule_hauteur']*1/2;
                }
            }

            // Saut de page si pagebreak atteint
            if ($this->checkPageBreak($multi_height)) {
                // ENTETE
                $this->entete_sous_etat($sousetat, $info);
                // On positionne la couleur de police pour l'affichage des
                // cellules de données.
                $this->SetTextColor(
                    $etat['se_couleurtexte'][0],
                    $etat['se_couleurtexte'][1],
                    $etat['se_couleurtexte'][2]
                );
            }

            // On définit la couleur de remplissage des cellules pour cette
            // ligne d'enregistrement.
            $this->SetFillColor(
                $sousetat['se_fond'.$couleur][0],
                $sousetat['se_fond'.$couleur][1],
                $sousetat['se_fond'.$couleur][2]
            );
            $couleur = ($couleur == 2 ? 1 : 2);

            // On boucle sur chaque colonne.
            for ($j = 0; $j < $nbchamp; $j++) {
                //
                $value = $row[$info[$j]['name']];
                //
                if ($cptenr == 0) {
                    $cellule_bordure_key = "cellule_bordure_un";
                } else {
                    $cellule_bordure_key = "cellule_bordure";
                }
                // Si le marqueur n'a pas déjà été activé et que l'opération
                // est paramétrée sur la colonne en question, alors on active
                // le marqueur de l'opération.
                if ($flagcompteur != 1
                    && $sousetat['cellule_compteur'][$j] == 1) {
                    // On active le marqueur de l'opération COMPTEUR.
                    $flagcompteur = 1;
                }
                // champs non numerique = 999 , numerique
                if (isset($sousetat['cellule_numerique'][$j])
                    && is_numeric(trim($sousetat['cellule_numerique'][$j]))
                    && $sousetat['cellule_numerique'][$j] != 999) {
                    // numerique
                    $text = number_format(
                        $value,
                        $sousetat['cellule_numerique'][$j],
                        ',',
                        ' '
                    );
                    // Si l'opération TOTAL est paramétrée sur la colonne,
                    // alors on active le marqueur de l'opération et on fait le
                    // calcul.
                    if ($sousetat['cellule_total'][$j] == 1) {
                        // On active le marqueur de l'opération TOTAL.
                        $flagtotal = 1;
                        // On fait le calcul.
                        $total[$j] += $value;
                    }
                    // Si l'opération MOYENNE est paramétrée sur la colonne,
                    // alors on active le marqueur de l'opération et on fait le
                    // calcul.
                    if ($sousetat['cellule_moyenne'][$j] == 1) {
                        // On active le marqueur de l'opération MOYENNE.
                        $flagmoyenne = 1;
                        // Si le marqueur de l'opération TOTAL n'est pas activé
                        // alors on fait le calcul, sinon on ne fait rien
                        // puisqu'il a déjà été fait.
                        if ($flagtotal == 0) {
                            $total[$j] += $value;
                        }

                    }
                } else {
                    // non numérique
                    $text = $value;
                }
                // Affichage de la cellule.
                $this->MultiCell(
                    $sousetat['cellule_largeur'][$j],
                    $multi_height,
                    $text,
                    $sousetat[$cellule_bordure_key][$j],
                    $sousetat['cellule_align'][$j],
                    $sousetat['cellule_fond'],
                    0
                );
            }
            // On incrémente le compteur d'enregistrements.
            $cptenr += 1;
            // On positionne le curseur en début de ligne suivante.
            $this->ln();
        }

        // Gestion de la dernière bordure des cellules de données du tableau.
        if ($sousetat['tableau_bordure'] == "1") {
            $this->Cell($sousetat['tableau_largeur'], 0, '', "T", 1, 'L', 0);
        }

        // Gestion des opérations.
        // Définition des différents types d'opérations disponibles.
        $operations = array(
            "total" => array(
                "flag" => $flagtotal,
                "label" => "TOTAL",
                "key_suffix" => "total",
                "key_flag" => "cellule_total",
            ),
            "moyenne" => array(
                "flag" => $flagmoyenne,
                "label" => "MOYENNE",
                "key_suffix" => "moyenne",
                "key_flag" => "cellule_moyenne",
            ),
            "compteur" => array(
                "flag" => $flagcompteur,
                "label" => "NOMBRE",
                "key_suffix" => "nbr",
                "key_flag" => "cellule_compteur",
            ),
        );
        // On boucle sur chaque opération pour afficher ou non la ligne.
        // résultat de l'opération.
        foreach ($operations as $key => $operation) {
            // Si le flage de l'opération n'est pas activé, alors on passe
            // à l'itération suivante
            if ($operation["flag"] != 1) {
                continue;
            }
            //
            $this->SetFont(
                $etat['se_font'],
                '',
                $sousetat['cellule_fontaille_'.$operation["key_suffix"]]
            );
            //
            $this->SetFillColor(
                $sousetat['cellule_fondcouleur_'.$operation["key_suffix"]][0],
                $sousetat['cellule_fondcouleur_'.$operation["key_suffix"]][1],
                $sousetat['cellule_fondcouleur_'.$operation["key_suffix"]][2]
            );
            // On boucle sur chaque colonne.
            for ($k = 0; $k < $nbchamp; $k++) {
                // Plusieurs cas sont possibles pour la valeur affichée dans la
                // celulle.
                if ($sousetat[$operation["key_flag"]][$k] == 1) {
                    // Si la colonne est paramétrée pour afficher le résultat
                    // de cette opération alors on affiche le résultat de
                    // l'opération.
                    if ($key == "total") {
                        $text = number_format(
                            $total[$k],
                            $sousetat['cellule_numerique'][$k],
                            ',',
                            ' '
                        );
                    } elseif ($key == "moyenne") {
                        $text = number_format(
                            $total[$k]/$cptenr,
                            $sousetat['cellule_numerique'][$k],
                            ',',
                            ' '
                        );
                    } elseif ($key == "compteur") {
                        $text = number_format($cptenr, 0, ',', ' ');
                    } else {
                        $text = "";
                    }
                } elseif ($k == 0) {
                    // Si c'est la première colonne et qu'elle n'est pas
                    // paramétrée pour afficher le résultat de cette opération
                    // alors on affiche le titre de l'opération.
                    $text = $operation["label"];
                } else {
                    // Par défaut la valeur de la cellule est vide.
                    $text = "";
                }
                // Affichage de la cellule.
                $this->Cell(
                    $sousetat['cellule_largeur'][$k],
                    $sousetat['cellule_hauteur_'.$operation["key_suffix"]],
                    $text,
                    $sousetat['cellule_bordure_'.$operation["key_suffix"]][$k],
                    0,
                    $sousetat['cellule_align_'.$operation["key_suffix"]][$k],
                    $sousetat['cellule_fond_'.$operation["key_suffix"]]
                );
            }
            // On positionne le curseur en début de ligne suivante.
            $this->ln();
        }

        // Gestion de l'espace vide après le sous-état.
        // XXX Pourquoi si il n'y a aucun enregistrement on ne fait pas
        //     tout de même l'espace ?
        if ($cptenr > 0) {
            $this->ln(intval($sousetat['intervalle_fin']));
        }
    }

    /**
     *
     */
    function PrepareMultiCell($w, &$txt) {

        //prepare un texte passe par reference (en le modifiant) avec ajout \n
        //pour traitement par Cell modifie
        //et retourne nb ligne necessaire
        //base sur code MultiCell mais pas d'affichage

        $cw=&$this->CurrentFont['cw']; //largeur caractere
        //
        if ($w == 0) { //si largeur=0, largeur=largeurcourante-margegauche-positionx
            $w = $this->w - $this->rMargin - $this->x;
        }
        //
        $cellPaddings = $this->getCellPaddings();
        //
        $wmax = ($w - 2 * $cellPaddings['L']) * 1000 / $this->FontSize;
        //
        $s = str_replace("\r", '', $txt);
        //
        $nb = strlen($s); //longueur texte sans retour chariot
        //
        if ($nb > 0 && $s[$nb-1] == "\n") {
            $nb--;      //supp. dernier retour ligne si existe
        }
        //
        $sep=-1;    //espace
        $i=0;       //index boucle
        $j=0;
        $l=0;
        $ns=0;
        $nl=1;
        $nbrc=0; //nb retourcharriot
        //
        while ($i < $nb) {  //boucle sur texte
            //Get next character
            $c=$s{$i};  //caractere courant
            //
            if ($c=="\n") {  //retour ligne
                //Explicit line break
                $i++;     //
                $sep=-1;   //raz espace
                $j=$i;     //debut de ligne
                $l=0;
                $ns=0;
                $nl++;   //nb ligne +1
                continue; // prochain caractere
            }
            if ($c==' ') {  //si espace
                $sep=$i; //position espace
                $ls=$l;
                $ns++;
            }
            $l+=$cw[ord($c)];
            if ($l>$wmax) { //si ligne depasse largeur
                //Automatic line break
                if($sep==-1) { //si aucun espace detecte
                    if($i==$j) {
                        $i++;
                    }
                } else { //espace detecte
                    $i=$sep+1;   //prochain car = car suivant dernier espace
                }  //insertion retour charriot dans texte
                $txt=substr($txt, 0, $i+$nbrc)."\n".substr($txt, $i+$nbrc);
                $nbrc++;
                $sep=-1;
                $j=$i;
                $l=0;
                $ns=0;
                $nl++;   //nb ligne +1
            } else {
                //ligne < largeur colonne
                $i++;
            }
        }  //fin de texte
        //
        return $nl;
    }






    /**
     * Initialisation des variables pour la création d'un code barre de type code 128.
     *
     * @return void
     */
    function init_Code128(){
        // Composition des caractères
        $this->T128[] = array(2, 1, 2, 2, 2, 2);    //0 : [ ]
        $this->T128[] = array(2, 2, 2, 1, 2, 2);    //1 : [!]
        $this->T128[] = array(2, 2, 2, 2, 2, 1);    //2 : ["]
        $this->T128[] = array(1, 2, 1, 2, 2, 3);    //3 : [#]
        $this->T128[] = array(1, 2, 1, 3, 2, 2);    //4 : [$]
        $this->T128[] = array(1, 3, 1, 2, 2, 2);    //5 : [%]
        $this->T128[] = array(1, 2, 2, 2, 1, 3);    //6 : [&]
        $this->T128[] = array(1, 2, 2, 3, 1, 2);    //7 : [']
        $this->T128[] = array(1, 3, 2, 2, 1, 2);    //8 : [(]
        $this->T128[] = array(2, 2, 1, 2, 1, 3);    //9 : [)]
        $this->T128[] = array(2, 2, 1, 3, 1, 2);    //10 : [*]
        $this->T128[] = array(2, 3, 1, 2, 1, 2);    //11 : [+]
        $this->T128[] = array(1, 1, 2, 2, 3, 2);    //12 : [,]
        $this->T128[] = array(1, 2, 2, 1, 3, 2);    //13 : [-]
        $this->T128[] = array(1, 2, 2, 2, 3, 1);    //14 : [.]
        $this->T128[] = array(1, 1, 3, 2, 2, 2);    //15 : [/]
        $this->T128[] = array(1, 2, 3, 1, 2, 2);    //16 : [0]
        $this->T128[] = array(1, 2, 3, 2, 2, 1);    //17 : [1]
        $this->T128[] = array(2, 2, 3, 2, 1, 1);    //18 : [2]
        $this->T128[] = array(2, 2, 1, 1, 3, 2);    //19 : [3]
        $this->T128[] = array(2, 2, 1, 2, 3, 1);    //20 : [4]
        $this->T128[] = array(2, 1, 3, 2, 1, 2);    //21 : [5]
        $this->T128[] = array(2, 2, 3, 1, 1, 2);    //22 : [6]
        $this->T128[] = array(3, 1, 2, 1, 3, 1);    //23 : [7]
        $this->T128[] = array(3, 1, 1, 2, 2, 2);    //24 : [8]
        $this->T128[] = array(3, 2, 1, 1, 2, 2);    //25 : [9]
        $this->T128[] = array(3, 2, 1, 2, 2, 1);    //26 : [:]
        $this->T128[] = array(3, 1, 2, 2, 1, 2);    //27 : [;]
        $this->T128[] = array(3, 2, 2, 1, 1, 2);    //28 : [<]
        $this->T128[] = array(3, 2, 2, 2, 1, 1);    //29 : [=]
        $this->T128[] = array(2, 1, 2, 1, 2, 3);    //30 : [>]
        $this->T128[] = array(2, 1, 2, 3, 2, 1);    //31 : [?]
        $this->T128[] = array(2, 3, 2, 1, 2, 1);    //32 : [@]
        $this->T128[] = array(1, 1, 1, 3, 2, 3);    //33 : [A]
        $this->T128[] = array(1, 3, 1, 1, 2, 3);    //34 : [B]
        $this->T128[] = array(1, 3, 1, 3, 2, 1);    //35 : [C]
        $this->T128[] = array(1, 1, 2, 3, 1, 3);    //36 : [D]
        $this->T128[] = array(1, 3, 2, 1, 1, 3);    //37 : [E]
        $this->T128[] = array(1, 3, 2, 3, 1, 1);    //38 : [F]
        $this->T128[] = array(2, 1, 1, 3, 1, 3);    //39 : [G]
        $this->T128[] = array(2, 3, 1, 1, 1, 3);    //40 : [H]
        $this->T128[] = array(2, 3, 1, 3, 1, 1);    //41 : [I]
        $this->T128[] = array(1, 1, 2, 1, 3, 3);    //42 : [J]
        $this->T128[] = array(1, 1, 2, 3, 3, 1);    //43 : [K]
        $this->T128[] = array(1, 3, 2, 1, 3, 1);    //44 : [L]
        $this->T128[] = array(1, 1, 3, 1, 2, 3);    //45 : [M]
        $this->T128[] = array(1, 1, 3, 3, 2, 1);    //46 : [N]
        $this->T128[] = array(1, 3, 3, 1, 2, 1);    //47 : [O]
        $this->T128[] = array(3, 1, 3, 1, 2, 1);    //48 : [P]
        $this->T128[] = array(2, 1, 1, 3, 3, 1);    //49 : [Q]
        $this->T128[] = array(2, 3, 1, 1, 3, 1);    //50 : [R]
        $this->T128[] = array(2, 1, 3, 1, 1, 3);    //51 : [S]
        $this->T128[] = array(2, 1, 3, 3, 1, 1);    //52 : [T]
        $this->T128[] = array(2, 1, 3, 1, 3, 1);    //53 : [U]
        $this->T128[] = array(3, 1, 1, 1, 2, 3);    //54 : [V]
        $this->T128[] = array(3, 1, 1, 3, 2, 1);    //55 : [W]
        $this->T128[] = array(3, 3, 1, 1, 2, 1);    //56 : [X]
        $this->T128[] = array(3, 1, 2, 1, 1, 3);    //57 : [Y]
        $this->T128[] = array(3, 1, 2, 3, 1, 1);    //58 : [Z]
        $this->T128[] = array(3, 3, 2, 1, 1, 1);    //59 : [[]
        $this->T128[] = array(3, 1, 4, 1, 1, 1);    //60 : [\]
        $this->T128[] = array(2, 2, 1, 4, 1, 1);    //61 : []]
        $this->T128[] = array(4, 3, 1, 1, 1, 1);    //62 : [^]
        $this->T128[] = array(1, 1, 1, 2, 2, 4);    //63 : [_]
        $this->T128[] = array(1, 1, 1, 4, 2, 2);    //64 : [`]
        $this->T128[] = array(1, 2, 1, 1, 2, 4);    //65 : [a]
        $this->T128[] = array(1, 2, 1, 4, 2, 1);    //66 : [b]
        $this->T128[] = array(1, 4, 1, 1, 2, 2);    //67 : [c]
        $this->T128[] = array(1, 4, 1, 2, 2, 1);    //68 : [d]
        $this->T128[] = array(1, 1, 2, 2, 1, 4);    //69 : [e]
        $this->T128[] = array(1, 1, 2, 4, 1, 2);    //70 : [f]
        $this->T128[] = array(1, 2, 2, 1, 1, 4);    //71 : [g]
        $this->T128[] = array(1, 2, 2, 4, 1, 1);    //72 : [h]
        $this->T128[] = array(1, 4, 2, 1, 1, 2);    //73 : [i]
        $this->T128[] = array(1, 4, 2, 2, 1, 1);    //74 : [j]
        $this->T128[] = array(2, 4, 1, 2, 1, 1);    //75 : [k]
        $this->T128[] = array(2, 2, 1, 1, 1, 4);    //76 : [l]
        $this->T128[] = array(4, 1, 3, 1, 1, 1);    //77 : [m]
        $this->T128[] = array(2, 4, 1, 1, 1, 2);    //78 : [n]
        $this->T128[] = array(1, 3, 4, 1, 1, 1);    //79 : [o]
        $this->T128[] = array(1, 1, 1, 2, 4, 2);    //80 : [p]
        $this->T128[] = array(1, 2, 1, 1, 4, 2);    //81 : [q]
        $this->T128[] = array(1, 2, 1, 2, 4, 1);    //82 : [r]
        $this->T128[] = array(1, 1, 4, 2, 1, 2);    //83 : [s]
        $this->T128[] = array(1, 2, 4, 1, 1, 2);    //84 : [t]
        $this->T128[] = array(1, 2, 4, 2, 1, 1);    //85 : [u]
        $this->T128[] = array(4, 1, 1, 2, 1, 2);    //86 : [v]
        $this->T128[] = array(4, 2, 1, 1, 1, 2);    //87 : [w]
        $this->T128[] = array(4, 2, 1, 2, 1, 1);    //88 : [x]
        $this->T128[] = array(2, 1, 2, 1, 4, 1);    //89 : [y]
        $this->T128[] = array(2, 1, 4, 1, 2, 1);    //90 : [z]
        $this->T128[] = array(4, 1, 2, 1, 2, 1);    //91 : [{]
        $this->T128[] = array(1, 1, 1, 1, 4, 3);    //92 : [|]
        $this->T128[] = array(1, 1, 1, 3, 4, 1);    //93 : [}]
        $this->T128[] = array(1, 3, 1, 1, 4, 1);    //94 : [~]
        $this->T128[] = array(1, 1, 4, 1, 1, 3);    //95 : [DEL]
        $this->T128[] = array(1, 1, 4, 3, 1, 1);    //96 : [FNC3]
        $this->T128[] = array(4, 1, 1, 1, 1, 3);    //97 : [FNC2]
        $this->T128[] = array(4, 1, 1, 3, 1, 1);    //98 : [SHIFT]
        $this->T128[] = array(1, 1, 3, 1, 4, 1);    //99 : [Cswap]
        $this->T128[] = array(1, 1, 4, 1, 3, 1);    //100 : [Bswap]
        $this->T128[] = array(3, 1, 1, 1, 4, 1);    //101 : [Aswap]
        $this->T128[] = array(4, 1, 1, 1, 3, 1);    //102 : [FNC1]
        $this->T128[] = array(2, 1, 1, 4, 1, 2);    //103 : [Astart]
        $this->T128[] = array(2, 1, 1, 2, 1, 4);    //104 : [Bstart]
        $this->T128[] = array(2, 1, 1, 2, 3, 2);    //105 : [Cstart]
        $this->T128[] = array(2, 3, 3, 1, 1, 1);    //106 : [STOP]
        $this->T128[] = array(2, 1);                //107 : [END BAR]

        //J eux de caractères
        for ($i = 32; $i <= 95; $i++) {
            $this->ABCset .= chr($i);
        }
        $this->Aset = $this->ABCset;
        $this->Bset = $this->ABCset;
        for ($i = 0; $i <= 31; $i++) {
            $this->ABCset .= chr($i);
            $this->Aset .= chr($i);
        }
        for ($i = 96; $i <= 126; $i++) {
            $this->ABCset .= chr($i);
            $this->Bset .= chr($i);
        }
        $this->Cset="0123456789";

        //Convertisseurs des jeux A & B
        for ($i=0; $i<96; $i++) {
            @$this->SetFrom["A"] .= chr($i);
            @$this->SetFrom["B"] .= chr($i + 32);
            @$this->SetTo["A"] .= chr(($i < 32) ? $i+64 : $i-32);
            @$this->SetTo["B"] .= chr($i);
        }
    }

    /**
     * Génération d'un code barre de type code 128.
     *
     * Script original : http://www.fpdf.org/fr/script/script88.php
     *
     * @param $x    Position X supérieur du code.
     * @param $y    Position Y supérieur du code.
     * @param $code Le code à créer.
     * @param $w    Largeur du code.
     * @param $h    Hauteur du code.
     */
    function Code128($x, $y, $code, $w, $h) {

        // Initialisation des données
        if( $this->T128 == "" || $this->ABCset == "" || $this->Aset == "" ||
            $this->Bset == "" || $this->Cset == "" || $this->SetFrom == "" ||
            $this->SetTo == "" ){
            $this->init_Code128();
        }

        // Affiche le numéro sous le code barres
        $this->Text($x, $y+$h+4, $code);

        // Création des guides de choix ABC
        $Aguid = "";
        $Bguid = "";
        $Cguid = "";
        for ($i=0; $i < strlen($code); $i++) {
            $needle = substr($code, $i, 1);
            $Aguid .= ((strpos($this->Aset, $needle)===false) ? "N" : "O");
            $Bguid .= ((strpos($this->Bset, $needle)===false) ? "N" : "O");
            $Cguid .= ((strpos($this->Cset, $needle)===false) ? "N" : "O");
        }

        $SminiC = "OOOO";
        $IminiC = 4;

        $crypt = "";
        while ($code > "") {

            $i = strpos($Cguid, $SminiC);
            // Force le jeu C, si possible
            if ($i!==false) {
                $Aguid [$i] = "N";
                $Bguid [$i] = "N";
            }

            // Jeu C
            if (substr($Cguid, 0, $IminiC) == $SminiC) {
                // Début Cstart, sinon Cswap
                $crypt .= chr(($crypt > "") ? $this->JSwap["C"] : $this->JStart["C"]);
                // Étendu du set C
                $made = strpos($Cguid, "N");
                if ($made === false) {
                    $made = strlen($Cguid);
                }
                // Seulement un nombre pair
                if (fmod($made, 2)==1) {
                    $made--;
                }
                for ($i=0; $i < $made; $i += 2) {
                    // Conversion 2 par 2
                    $crypt .= chr(strval(substr($code, $i, 2)));
                }
                $jeu = "C";
            } else {
                // Étendu du set A
                $madeA = strpos($Aguid, "N");
                if ($madeA === false) {
                    $madeA = strlen($Aguid);
                }
                // Étendu du set B
                $madeB = strpos($Bguid, "N");
                if ($madeB === false) {
                    $madeB = strlen($Bguid);
                }
                // Étendu traité
                $made = (($madeA < $madeB) ? $madeB : $madeA );
                // Jeu en cours
                $jeu = (($madeA < $madeB) ? "B" : "A" );

                //  Début start, sinon swap
                $crypt .= chr(($crypt > "") ? $this->JSwap[$jeu] : $this->JStart[$jeu]);

                // Conversion selon jeu
                $crypt .= strtr(
                    substr($code, 0, $made),
                    $this->SetFrom[$jeu],
                    $this->SetTo[$jeu]
                );

            }
            // Raccourcir légende et guides de la zone traitée
            $code = substr($code, $made);
            $Aguid = substr($Aguid, $made);
            $Bguid = substr($Bguid, $made);
            $Cguid = substr($Cguid, $made);
        }

        // Calcul de la somme de contrôle
        $check = ord($crypt[0]);
        for ($i=0; $i<strlen($crypt); $i++) {
            $check += (ord($crypt[$i]) * $i);
        }
        $check %= 103;

        // Chaine cryptée complète
        $crypt .= chr($check) . chr(106) . chr(107);

        // Calcul de la largeur du module
        $i = (strlen($crypt) * 11) - 8;
        $modul = $w/$i;

        for ($i=0; $i<strlen($crypt); $i++) {
            $c = $this->T128[ord($crypt[$i])];
            for ($j=0; $j<count($c); $j++) {
                $this->Rect($x, $y, $c[$j]*$modul, $h, "F");
                $x += ($c[$j++]+$c[$j])*$modul;
            }
        }
    }

    /**
     * Prépare le code html pour qu'il puisse être interprété par TCPDF.
     *
     * @param string $data Le texte à traiter.
     *
     * @todo Il doit être possible de corriger les erreur de HTML valides
     *       connues. Par exemple, l'insertion d'un champ de fusion html dans
     *       du html est un cas fréquent qui conduit à avoir deux ouvertures
     *       de balises <p><p> successives.
     *
     * @return string
     */
    function prepare_html_for_tcpdf($data) {
        // Si le html fourni n'est pas valide, loadHTML lève des erreurs PHP
        // par défaut. On désactive l'affichage de ces erreurs pour ne pas
        // bloquer la génération des fichiers PDF.
        libxml_use_internal_errors(true);
        // On initialise le html passé en paramètre et on le charge dans un DOM
        $dom = new DOMDocument;
        $dom->loadHTML($data);
        // On vide les erreurs
        libxml_clear_errors();
        // On supprime le doctype du dom, l'objectif est d'avoir uniquement
        // les balises nécessaires pour éviter des problèmes d'interlignage
        $dom->removeChild($dom->doctype);
        // On transforme le DOM en XPATH pour pouvoir le requêter plus
        // facilement.
        $xPath = new DOMXPath($dom);
        // Gestion de la fonctionnalité codebarre
        // Pour chaque marqueur TinyMCE (class='mce_code_barre'), on insère un
        // marqueur TCPDF pour qu'il interprète le codebarre correctement.
        foreach ($xPath->query("//*[contains(@class, 'mce_codebarre')]") as $node) {
            // On sérialise les paramètres du codebarre
            $params = $this->serializeTCPDFtagParameters(
                array(
                    $node->textContent,
                    'C128',
                    '',
                    '',
                    50,
                    10,
                    0.4,
                    array(
                        'position'=>'S',
                        'border'=>false,
                        'padding'=>0,
                        'fgcolor'=>array(0,0,0),
                        'bgcolor'=>array(255,255,255),
                        'text'=>true,
                        'font'=>$this->getFontFamily(),
                        'fontsize'=>8,
                        'stretchtext'=>4,
                    ),
                    'N',
                )
            );
            // On vide le noeud courant sur lequel on a trouvé le marqueur
            // TinyMCE
            $node->nodeValue = '';
            // On compose le marqueur TCPDF
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML(
                '<tcpdf method="write1DBarcode" params="'.$params.'" />'
            );
            // On insère le marqueur TCPDF dans le noeud courant
            $node->appendChild($fragment);
        }
        // On retransforme le DOM en chaîne de caractères.
        $data = $dom->saveHTML();
        // On supprime les balises html, head, body du dom, l'objectif est
        // d'avoir uniquement les balises nécessaires pour éviter des problèmes
        // d'interlignage
        $data = str_replace("<html><head>", "", $data);
        $data = str_replace("</head><body>", "", $data);
        $data = str_replace("</body></html>", "", $data);
        // On retourne le html préparé.
        return $data;
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
