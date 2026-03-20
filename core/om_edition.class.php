<?php
/**
 * Ce script contient la définition de la classe 'edition'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_edition.class.php 4348 2018-07-20 16:49:26Z softime $
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
require_once PATH_OPENMAIRIE."om_base.class.php";

/**
 * Définition de la classe 'edition'.
 *
 * Cette classe gère le module 'Édition' du framework openMairie. Ce module
 * permet de gérer les différentes vues pour la génération des éditions PDF.
 */
class edition extends om_base {

    /**
     * Constructeur.
     */
    public function __construct() {
        // Initialisation de la classe 'application'.
        $this->init_om_application();
    }

    /**
     * VIEW - view_pdf.
     *
     * Cette vue remplace l'ancien script 'pdf/pdf.php'. Elle permet d'afficher
     * la liste des éditions disponibles 'sql/<OM_DB_PHPTYPE>/*.pdf.inc.php'
     * ainsi que de générer le fichier PDF d'une édition en particulier.
     *
     * @return void
     */
    public function view_pdf() {
        /**
         * Fonction permettant de lister les éditions disponibles dans un répertoire.
         *
         * @param string $folder_path Path vers le répertoire.
         * @param array  $pdf_list    Liste d'éditions (optionnelle).
         *
         * @return array Liste des éditions disponibles.
         * @ignore
         */
        function get_pdf_list_in_folder($folder_path = "", $pdf_list = array()) {
            // On teste si le répertoire existe
            if (is_dir($folder_path)) {
                // Si le répertoire existe alors l'ouvre
                $folder = opendir($folder_path);
                // On liste le contenu du répertoire
                while ($file = readdir($folder)) {
                    // Si le nom du fichier contient la bonne extension
                    if (strpos($file, ".pdf.inc.php")) {
                        // On récupère la première partie du nom du fichier
                        // c'est à dire sans l'extension complète
                        $elem = substr($file, 0, strlen($file) - 12);
                        // On l'ajoute à la liste des éditions disponibles
                        // avec le path complet vers le script et le titre
                        $pdf_list[$elem] = array(
                            "title" => __($elem),
                            "path" => $folder_path.$file,
                        );
                    }
                }
                // On ferme le répertoire
                closedir($folder);
            }
            // On retourne la liste des éditions disponibles
            return $pdf_list;
        }

        /**
         * Fonction permettant de comparer les valeurs de l'attribut title
         * des deux tableaux passés en paramètre.
         *
         * @param array $a
         * @param array $b
         *
         * @return bool
         * @ignore
         */
        function sort_by_lower_title($a, $b) {
            if (strtolower($a["title"]) == strtolower($b["title"])) {
                return 0;
            }
            return (strtolower($a["title"]) < strtolower($b["title"]) ? -1 : 1);
        }

        /**
         * Récupération de la liste des éditions disponibles.
         *
         * Ces éditions correspondent aux éditions génériques paramétrées dans des
         * scripts <edition>.pdf.inc.php. Ces scripts sont généralement présents dans
         * le répertoire sql/<db_type>/ de l'application mais peuvent également être
         * présents dans le répertoire CUSTOM prévu à cet effet.
         */
        // On définit le répertoire STANDARD où se trouvent les scripts des éditions
        $dir = getcwd();
        $dir = substr($dir, 0, strlen($dir) - 4)."/sql/".OM_DB_PHPTYPE."/";
        // On récupère la liste des éditions disponibles dans ce répertoire STANDARD
        $pdf_list = get_pdf_list_in_folder($dir);
        //
        if ($this->f->get_custom("path", ".pdf.inc.php") != null) {
            // On définit le répertoire CUSTOM où se trouvent les scripts des éditions
            $dir = $this->f->get_custom("path", ".pdf.inc.php");
            // On récupère la liste des éditions disponibles dans ce répertoire CUSTOM
            $pdf_list = get_pdf_list_in_folder($dir, $pdf_list);
        }
        // On tri la liste des éditions disponibles par ordre alphabétique
        uasort($pdf_list, "sort_by_lower_title");

        /**
         *
         */
        // Nom de l'objet metier
        (isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
        // Vérification de l'existence de l'objet
        // XXX Vérifier une permission spécifique ?
        if ($obj != "" && !array_key_exists($obj, $pdf_list)) {
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->f->addToMessage($class, $message);
            $this->f->setFlag(null);
            $this->f->display();
            die();
        }

        /**
         *
         */
        //
        if ($obj == "") {
            //
            $usecase = "pdf-list";
        } else {
            //
            $usecase = "pdf-output";
        }


        /**
         *
         */
        if ($usecase == "pdf-output") {
            /**
             * @ignore
             */
            function get_pdf_inc_vars($file) {
                //
                $multiplicateur = 1;
                //
                include $file;
                //
                return get_defined_vars();
            }

            /**
             * Génération du PDF
             */
            //
            $collectivite = $this->f->getCollectivite();
            //
            $edition = get_pdf_inc_vars($pdf_list[$obj]["path"]);
            //
            set_time_limit(180);
            //
            require_once PATH_OPENMAIRIE."db_fpdf.php";
            //
            $pdf = new PDF(
                $edition["orientation"],
                "mm",
                $edition["format"]
            );
            //
            $pdf->set_edition_params($edition);
            //
            $pdf->SetMargins(
                $edition["margeleft"],
                $edition["margetop"],
                $edition["margeright"]
            );
            //
            $pdf->AliasNbPages();
            //
            $pdf->SetDisplayMode('real', 'single');
            //
            $pdf->SetDrawColor(
                $edition["C1border"],
                $edition["C2border"],
                $edition["C3border"]
            );
            //
            $pdf->AddPage();
            //
            $pdf->Table(
                $edition["sql"],
                $this->f->db,
                $edition["height"],
                $edition["border"],
                $edition["align"],
                $edition["fond"],
                $edition["police"],
                $edition["size"],
                $edition["multiplicateur"],
                $edition["flag_entete"]
            );
            //
            $pdf->Output();
        } elseif ($usecase == "pdf-list") {
            /**
             * Affichage de la structure de la page
             */
            //
            $this->f->setTitle(__("export")." -> ".__("editions"));
            $this->f->isAuthorized("edition");
            $this->f->setFlag(null);
            //
            $this->f->display();
            //
            $description = __(
                "Le module 'editions' permet d'acceder aux listings PDF ".
                "de l'application."
            );
            $this->f->displayDescription($description);

            /**
             * Affichage de la liste des éditions disponibles.
             */
            //
            echo "\n<div id=\"edition\">\n";
            // Composition de la liste de liens vers les éditions disponibles.
            // En partant de la liste d'éditions disponibles, on compose une liste
            // d'éléments composés d'une URL, d'un libellé, et de tous les paramètres
            // permettant l'affichage de l'élément comme un élément de liste.
            $list = array();
            foreach ($pdf_list as $key => $value) {
                //
                $list[] = array(
                    "href" => OM_ROUTE_MODULE_EDITION."&obj=".$key,
                    "title" => $value["title"],
                    "class" => "om-prev-icon edition-16",
                    "target" => "blank",
                );
            }
            //
            $this->f->layout->display_list(
                array(
                    "title" => __("choix de l'edition"),
                    "list" => $list,
                )
            );
            //
            echo "</div>\n";
        }
    }

    /**
     * VIEW - view_pdfetiquette.
     *
     * Cette vue remplace l'ancien script 'pdf/pdfetiquette.php'. Elle permet
     * de générer le fichier PDF d'une édition d'étiquettes en particulier parmi
     * les éditions disponibles 'sql/<OM_DB_PHPTYPE>/*.pdfetiquette.inc.php'.
     *
     * @return void|array
     */
    public function view_pdfetiquette() {
        /**
         *
         */
        // Nom de l'objet metier
        (isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");

        /**
         * Verification des parametres
         */
        if (strpos($obj, "/") !== false
            or ! (file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".pdfetiquette.inc.php")
                  or file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".pdfetiquette.inc"))) {
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->f->addToMessage($class, $message);
            $this->f->setFlag(null);
            $this->f->display();
            die();
        }

        /**
         * @ignore
         */
        function get_pdfetiquette_inc_vars($file) {
            //
            $orientation = "P"; // string - ""
            $format = "A4"; // string - ""
            //
            $police = "arial"; // string - ""
            $gras = "B"; // string - "B" ou ""
            $size = 10; // int
            //
            $C1 = 0; // int - 0 < x < 255
            $C2 = 0; // int - 0 < x < 255
            $C3 = 0; // int - 0 < x < 255
            //
            $_margin_left = 10; // int - Marge de gauche de l'étiquette
            $_margin_top = 10; // int - Marge en haut de la page avant la première étiquette
            $_x_space = 0; // int - Espace horizontal entre 2 bandes d'étiquettes
            $_y_space = 0; // int - Espace vertical entre 2 bandes d'étiquettes
            $_x_number = 2; // int - Nombre d'étiquettes sur la largeur de la page
            $_y_number = 9; // int - Nombre d'étiquettes sur la hauteur de la page
            $_width = 95; // int - Largeur de chaque étiquette
            $_height = 30; // int - Hauteur de chaque étiquette
            $_char_size = 4; // int - Hauteur des caractères
            $_line_height = 5; // int - Hauteur par défaut  interligne
            //
            $cadrechamps = 1; // CADRE CHAMPS DATA,TXETE,COMPTEUR 1 OU 0
            $cadre = 1; // CADRE ZONE REPETEE avec tous les champs1 OU 0
            //
            $champs = array();
            //
            $texte = array();
            //
            $img = array();
            //
            $champs_compteur = array();
            //
            $sql = "select * from ".DB_PREFIXE."om_collectivite";
            //
            include $file;
            //
            return get_defined_vars();
        }

        /**
         *
         */
        //
        if (!isset($collectivite)) {
            $collectivite = $this->f->getCollectivite();
        }
        //
        $edition = get_pdfetiquette_inc_vars(
            "../sql/".OM_DB_PHPTYPE."/".$obj.".pdfetiquette.inc.php"
        );
        //
        set_time_limit(180);
        //
        require_once PATH_OPENMAIRIE."fpdf_etiquette.php";
        //
        $pdf = new PDF(
            $edition["orientation"],
            "mm",
            $edition["format"]
        );
        //
        $pdf->SetMargins(0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetDisplayMode('real', 'single');
        //
        $pdf->AddPage();
        //
        $pdf->SetFont(
            $edition["police"],
            $edition["gras"],
            $edition["size"]
        );
        //
        $pdf->SetTextColor(
            $edition["C1"],
            $edition["C2"],
            $edition["C3"]
        );
        //
        $param = array(
            $edition["_margin_left"],
            $edition["_margin_top"],
            $edition["_x_space"],
            $edition["_y_space"],
            $edition["_x_number"],
            $edition["_y_number"],
            $edition["_width"],
            $edition["_height"],
            $edition["_char_size"],
            $edition["_line_height"],
            0,
            0,
            $edition["size"],
            $edition["cadrechamps"],
            $edition["cadre"]
        );
        //
        $pdf->Table_position(
            $edition["sql"],
            $this->f->db,
            $param,
            $edition["champs"],
            $edition["texte"],
            $edition["champs_compteur"],
            $edition["img"]
        );

        // Construction du nom du fichier
        $filename = date("Ymd-His");
        $filename .= "-etiquette";
        $filename .= "-".$obj;
        $filename .= ".pdf";

        //
        $pdf_output = $this->handle_output($pdf, $filename);

        //
        return array(
            "pdf_output" => $pdf_output,
            "filename" => $filename,
        );
    }

    /**
     * Génération de l'édition PDF pour une édition "etat" ou "lettretype".
     *
     * @param string $edition_elem Élement sur lequel porte l'édition. Les
     *                             valeurs possibles sont "etat" ou "lettretype".
     * @param string $collectivite Identifiant de la collectivité spécifique
     *                             (dans certains cas d'utilisation liés au
     *                             multi-collectivité) sur laquelle porte
     *                             l'édition.
     *
     * @return array
     */
    function pdf_om_etat_om_lettretype($edition_elem, $collectivite) {

        // Initialisation des variables en fonction de l'élément <EDITION> passé
        // en paramètre.
        if ($edition_elem == "lettretype") {
            $table = "om_lettretype";
            $file_to_include = "../dyn/varlettretypepdf.inc";
        } elseif ($edition_elem == "etat") {
            $table = "om_etat";
            $file_to_include = "../dyn/varetatpdf.inc";
        } else {
            return array(
                "pdf_output" => "",
                "filename" => "",
            );
        }

        // Paramétrage du filigrane
        (isset($_GET['watermark']) && $_GET['watermark'] == 'true') ?
            $watermark = true : $watermark = false;

        // S'il s'agit d'une prévisualisation, on affecte la clé primaire de la lettre
        // type à la variable edition_direct_preview_id
        $edition_direct_preview_id = null;
        if (isset($_GET["specific"])
            && is_array($_GET["specific"])
            && isset($_GET["specific"]["mode"])
            && $_GET["specific"]["mode"] == "edition_direct_preview"
            && isset($_GET["specific"]["id"])) {
            //
            $edition_direct_preview_id = $_GET["specific"]["id"];
        }

        // Dans certains cas d'utilisation liés au multi collectivité, il est
        // nécessaire de récupérer l'édition de la collectivité de l'élément au
        // lieu de la collectivité de l'utilisateur. Donc on vérifie si le
        // tableau de paramètre de la collectivité a été passé en paramètre
        // sinon on définit celui de l'utilisateur connecté.
        if ($collectivite == null) {
            $collectivite = $this->f->getCollectivite();
        }
        //
        if (isset($_GET["obj"]) || isset($_GET['idx'])) {
            // Identifiant de l'édition à générer (champ id de la table om_<EDITION>)
            (isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
            // Identifiant de l'élément concerné par l'édition
            (isset($_GET['idx']) ? $idx = $_GET['idx'] : $idx = "");
        } elseif (isset($_POST["obj"]) || isset($_POST['idx'])) {
            //
            (isset($_POST['obj']) ? $obj = $_POST['obj'] : $obj = "");
            // Si c'est un tableau qui est fourni dans le POST alors on le concatène
            // avec des ; pour coller au format attendu
            if (is_array($obj) === true) {
                $obj_str = "";
                foreach ($obj as $value) {
                    $obj_str .= $value.";";
                }
                $obj = $obj_str;
            }
            //
            (isset($_POST['idx']) ? $idx = $_POST['idx'] : $idx = "");
            // Si c'est un tableau qui est fourni dans le POST alors on le concatène
            // avec des ; pour coller au format attendu
            if (is_array($idx) === true) {
                $idx_str = "";
                foreach ($obj as $value) {
                    $idx_str .= $value.";";
                }
                $idx = $idx_str;
            }
        } else {
            //
            $obj = "";
            $idx = "";
        }
        //
        $editions = array_filter(explode(";", $obj));
        $elements = array_filter(explode(";", $idx));

        // Si un seul élément est fourni alors qu'il y a plusieurs éditions alors on
        // suppose que c'est le même élément pour chacune des éditions
        if (count($editions) != count($elements) && count($editions) > 1 &&
            count($elements) == 1) {
            foreach ($editions as $edition) {
                $elements[] = $elements[0];
            }
        } elseif (count($editions) != count($elements) && count($editions) == 1 &&
            count($elements) > 1) {
            // Si une seule édition est fourni alors qu'il y a plusieurs
            // éléments alors on suppose que c'est la même édition pour chacun
            // des éléments
            $tmp_edition = $editions[0];
            $editions = array();
            foreach ($elements as $element) {
                $editions[] = $tmp_edition;
            }
            unset($tmp_edition);
        }

        /**
         * Ces paramètres sont ici pour une raison de rétro-compatibilité
         * @todo Vérifier qu'il n'est pas possible de les supprimer de ce fichier et de
         *       les gérer dans dyn/var<EDITION>pdf.inc ce qui est déjà en partie le
         *       cas
         */
        //
        $destinataire = "";
        //
        $datecourrier = date('d/m/Y');
        //
        $complement = "<-Ici le complement->";

        /**
         * Inclusion de la classe de génération des éditions
         */
        //
        set_time_limit(180);
        //
        require_once PATH_OPENMAIRIE."fpdf_etat.php";

        /**
         * Multi impression
         */
        // Définition du css interne au pdf
        $css = "<style>
        span.error {
            font-weight: bold;
            background-color: #cdcdcd;
            color: #ff0000;
        }
        .mce_maj {
            text-transform: uppercase;
        }
        .mce_min {
            text-transform: lowercase;
        }
        </style>";
        //
        $pdf = null;
        //
        foreach ($editions as $key => $value) {

            /**
             * Initialisation des variables :
             * - $obj :
             * - $idx :
             */
            //
            $obj = $value;
            //
            $idx = "-1";
            if (isset($elements[$key])) {
                if (is_integer($elements[$key])) {
                    $idx = $elements[$key];
                } else {
                    $idx = $this->f->db->escapeSimple($elements[$key]);
                }
            }
            // Compatibilité antérieure : dans le cas où le remplacement des variables
            // dans le fichier de remplacement dyn/var<EDITION>pdf.inc se base sur la
            // variable $_GET au lieu de la variable $idx
            $_GET['idx'] = $idx;

            /**
             * Récupération du paramétrage de l'édition.
             */
            //
            $edition = $this->get_edition_from_collectivite(
                $table,
                $obj,
                $collectivite['om_collectivite_idx'],
                $edition_direct_preview_id
            );
            // Si aucune édition ne correspond dans le paramétrage, on passe à
            // l'itération suivante de la boucle multi édition.
            if (is_null($edition)) {
                //
                continue;
            }

            // CHAMPS DE FUSION - Récupération des valeurs
            // Initialisation du tableau de champs de fusion
            $merge_fields_values = $this->get_merge_fields_values(
                $edition["om_sql"],
                $idx
            );

            // VARIABLES DE REMPLACEMENT - Récupération des valeurs
            //
            $substitution_vars_values = $this->get_substitution_vars_values(
                $collectivite['om_collectivite_idx']
            );

            /**
             * Initialisation du fichier PDF.
             */
            // Si on se trouve sur la première édition
            if (is_null($pdf)) {
                // Instanciation du document PDF avec les paramètres de la
                // première édition.
                $pdf = new PDF(
                    // $orientation (string) page orientation. Possible values are
                    // (case insensitive):
                    // - P or Portrait (default)
                    // - L or Landscape
                    // - '' (empty string) for automatic orientation
                    $edition["orientation"],
                    // $unit (string) User measure unit. Possible values are:
                    // - pt: point
                    // - mm: millimeter (default)
                    // - cm: centimeter
                    // - in: inch
                    "mm",
                    // $format (mixed) The format used for pages.
                    $edition["format"],
                    // $unicode (boolean) TRUE means that the input text is unicode
                    // (default = true)
                    true,
                    // $encoding (string) Charset encoding (used only when converting
                    // back html entities); default is UTF-8.
                    'HTML-ENTITIES'
                );
                // Si le filigrane "DOCUMENT DE TRAVAIL" est paramétré.
                if ($watermark == true) {
                    // On l'ajoute sur chaque page
                    $pdf->setWatermark();
                }
                // Start First Page Group
                // Lors d'une édition multi, la numérotation est globale.
                // XXX Il est possible de rendre la numérotation des pages
                //     spécifique à chaque édition en déplaçant l'instruction
                //     suivante en dehors de ce bloc pour qu'il soit appelé
                //     à chaque itération de la boucle multi.
                $pdf->startPageGroup();
            }

            /**
             * Initialisation des paramètres de marges de l'édition.
             */
            // Définit les marges du document
            if ($edition["margeleft"] == "") {
                $edition["margeleft"] = PDF_MARGIN_LEFT;
            }
            if ($edition["margetop"] == "") {
                $edition["margetop"] = PDF_MARGIN_TOP;
            }
            if ($edition["margeright"] == "") {
                $edition["margeright"] = PDF_MARGIN_RIGHT;
            }
            if ($edition["margebottom"] == "") {
                $edition["margebottom"] = PDF_MARGIN_BOTTOM;
            }
            // set margins
            $pdf->setMargins(
                $edition["margeleft"],
                $edition["margetop"],
                $edition["margeright"]
            );
            $pdf->SetHeaderMargin($edition["margetop"]);
            $pdf->SetFooterMargin($edition["margebottom"]);
            // set auto page breaks
            $pdf->SetAutoPageBreak(true, $edition["margebottom"]);
            // définition du padding haut et bas des balises p span et table
            $tagvs = array(
                'p' => array(
                    0 => array('h' => 0, 'n' => 0),
                    1 => array('h' => 0, 'n' => 0)
                ),
                'div' => array(
                    0 => array('h' => 0, 'n' => 0),
                    1 => array('h' => 0, 'n' => 0)
                ),
                'span' => array(
                    0 => array('h' => 0, 'n' => 0),
                    1 => array('h' => 0, 'n' => 0)
                ),
                'table' => array(
                    0 => array('h' => 0, 'n' => 0),
                    1 => array('h' => 0, 'n' => 0)
                ),
            );
            $pdf->setHtmlVSpace($tagvs);

            /**
             * HEADER - Paramétrage de l'entête
             */
            //
            if ($edition["header_om_htmletat"] != "") {
                $header = html_entity_decode($edition["header_om_htmletat"]);
                $header = preg_replace('#<\s*tcpdf[^>]+>#', '', $header);
                $header = $this->replace_all_elements(
                    $header,
                    $substitution_vars_values,
                    $merge_fields_values
                );
                $header = '<meta charset="UTF-8" />'.$header.'';
                $header = $pdf->prepare_html_for_tcpdf($header);
                $header = $css.$header;
                $header = str_replace("&numpage", $pdf->getPageNumGroupAlias(), $header);
                $header = str_replace("&nbpages", $pdf->getPageGroupAlias(), $header);
                $header = str_replace("&amp;numpage", $pdf->getPageNumGroupAlias(), $header);
                $header = str_replace("&amp;nbpages", $pdf->getPageGroupAlias(), $header);
                //
                $pdf->set_header(array(
                    "offset" => $edition["header_offset"],
                    "html" => $header,
                ));
            }

            /**
             * FOOTER - Paramétrage du pied de page
             */
            //
            if ($edition["footer_om_htmletat"] != "") {
                $footer = html_entity_decode($edition["footer_om_htmletat"]);
                $footer = preg_replace('#<\s*tcpdf[^>]+>#', '', $footer);
                $footer = $this->replace_all_elements(
                    $footer,
                    $substitution_vars_values,
                    $merge_fields_values
                );
                $footer = '<meta charset="UTF-8" />'.$footer.'';
                $footer = $pdf->prepare_html_for_tcpdf($footer);
                $footer = $css.$footer;
                $footer = str_replace("&numpage", $pdf->getPageNumGroupAlias(), $footer);
                $footer = str_replace("&nbpages", $pdf->getPageGroupAlias(), $footer);
                $footer = str_replace("&amp;numpage", $pdf->getPageNumGroupAlias(), $footer);
                $footer = str_replace("&amp;nbpages", $pdf->getPageGroupAlias(), $footer);
                //
                $pdf->set_footer(array(
                    "offset" => $edition["footer_offset"],
                    "html" => $footer,
                ));
            }

            /**
             *
             */
            // Ajoute une nouvelle page à l'édition
            $pdf->AddPage();

            /**
             * LOGO - Affichage du logo
             */
            //
            $logo = $this->get_logo_from_collectivite(
                $edition['logo'],
                $collectivite['om_collectivite_idx']
            );
            //
            if (!is_null($logo)) {
                // TCPDF::Image()
                $pdf->Image(
                    // $file (string) Name of the file containing the image or a '@' character followed by the image data string. To link an image without embedding it on the document, set an asterisk character before the URL (i.e.: '*http://www.example.com/image.jpg').
                    $logo["file"],
                    // $x (float) Abscissa of the upper-left corner (LTR) or upper-right corner (RTL).
                    $edition["logoleft"],
                    // $y (float) Ordinate of the upper-left corner (LTR) or upper-right corner (RTL).
                    $edition["logotop"],
                    // $w (float) Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
                    $logo["w"],
                    // $h (float) Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
                    $logo["h"],
                    // $type (string) Image format. Possible values are (case insensitive): JPEG and PNG (whitout GD library) and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;. If not specified, the type is inferred from the file extension.
                    $logo["type"]
                );
            }

            /**
             * TITRE ET CORPS
             */
            // Remise en forme du html pour être interprété par TCPDF
            $titre = html_entity_decode($edition["titre_om_htmletat"]);
            $corps = html_entity_decode($edition["corps_om_htmletatex"]);
            // Suppression des balises TCPDF pour éviter toutes intrusions
            $titre = preg_replace('#<\s*tcpdf[^>]+>#', '', $titre);
            $corps = preg_replace('#<\s*tcpdf[^>]+>#', '', $corps);
            // Éventuels champs de fusion spécifiques
            if (isset($_GET["specific"])
                && is_array($_GET["specific"])
                && isset($_GET["specific"]["titre"])
                && is_array($_GET["specific"]["titre"])
                && isset($_GET["specific"]["titre"]["mode"])
                && $_GET["specific"]["titre"]["mode"] == "set"
                && isset($_GET["specific"]["titre"]["value"])) {
                $titre = $_GET["specific"]["titre"]["value"];
            }
            if (isset($_GET["specific"])
                && is_array($_GET["specific"])
                && isset($_GET["specific"]["corps"])
                && is_array($_GET["specific"]["corps"])
                && isset($_GET["specific"]["corps"]["mode"])
                && $_GET["specific"]["corps"]["mode"] == "set"
                && isset($_GET["specific"]["corps"]["value"])) {
                $corps = $_GET["specific"]["corps"]["value"];
            }
            if (isset($_GET["specific"])
                && is_array($_GET["specific"])
                && isset($_GET["specific"]["merge_fields"])
                && is_array($_GET["specific"]["merge_fields"])) {
                foreach ($_GET["specific"]["merge_fields"] as $merge_field => $value) {
                    $titre = str_ireplace($merge_field, $value, $titre);
                    $corps = str_ireplace($merge_field, $value, $corps);
                }
            }
            //
            $titre = $this->replace_all_elements(
                $titre,
                $substitution_vars_values,
                $merge_fields_values
            );
            // Récupération du contenu du titre
            if (isset($_GET["specific"])
                && is_array($_GET["specific"])
                && isset($_GET["specific"]["titre"])
                && is_array($_GET["specific"]["titre"])
                && isset($_GET["specific"]["titre"]["mode"])
                && $_GET["specific"]["titre"]["mode"] == "get") {
                return array(
                    "pdf_output" => $titre
                );
            }
            //
            $corps = $this->replace_all_elements(
                $corps,
                $substitution_vars_values,
                $merge_fields_values
            );
            // Récupération du contenu du corps
            if (isset($_GET["specific"])
                && is_array($_GET["specific"])
                && isset($_GET["specific"]["corps"])
                && is_array($_GET["specific"]["corps"])
                && isset($_GET["specific"]["corps"]["mode"])
                && $_GET["specific"]["corps"]["mode"] == "get") {
                return array(
                    "pdf_output" => $corps
                );
            }

            // Remplacement des paramètres dans le fichier ../dyn/var<EDITION>pdf.inc
            // @deprecated
            if (file_exists($file_to_include)) {
                // Rétrocompatibilité - certaines variables doivent exister dans
                // le script inclus.
                $sql = "";
                // Traitement des & et &amp;
                $titre = str_ireplace("&amp;", "&", $titre);
                $corps = str_ireplace("&amp;", "&", $corps);
                // Inclusion du script
                include $file_to_include;
                // Suppression de la variable
                unset($sql);
            }

            /**
             * TITRE - Affichage du titre
             */
            //
            $titre = "<meta charset='UTF-8' />".$titre."";
            $titre = $pdf->prepare_html_for_tcpdf($titre);
            $titre = $css.$titre;
            $titre = str_replace("&amp;numpage", $pdf->getPageNumGroupAlias(), $titre);
            $titre = str_replace("&amp;nbpages", $pdf->getPageGroupAlias(), $titre);
            $titre = str_replace("&numpage", $pdf->getPageNumGroupAlias(), $titre);
            $titre = str_replace("&nbpages", $pdf->getPageGroupAlias(), $titre);
            // Affichage du titre si non vide
            if (trim($titre) != "") {
                // TCPDF::writeHTMLCell()
                $pdf->writeHTMLCell(
                    // $w (float) Cell width. If 0, the cell extends up to the right margin.
                    $edition["titrelargeur"],
                    // $h (float) Cell minimum height. The cell extends automatically if needed.
                    0,
                    // $x (float) upper-left corner X coordinate
                    $edition["titreleft"],
                    // $y (float) upper-left corner Y coordinate
                    $edition["titretop"],
                    // $html (string) html text to print. Default value: empty string.
                    $titre,
                    // $border (mixed) Indicates if borders must be drawn around the cell.
                    $edition["titrebordure"],
                    // $ln (int) Indicates where the current position should go after the call.
                    0,
                    // $fill (boolean) Indicates if the cell background must be painted (true) or transparent (false).
                    false,
                    // $reseth (boolean) if true reset the last cell height (default true).
                    true,
                    // $align (string) Allows to center or align the text.
                    '',
                    // $autopadding (boolean) if true, uses internal padding and automatically adjust it to account for line width.
                    true
                );
            }
            $pdf->ln();

            /**
             * CORPS - Affichage du corps
             */
            //
            $corps = "<meta charset='UTF-8' />".$corps."";
            $corps = $pdf->prepare_html_for_tcpdf($corps);
            $corps = $pdf->initSousEtats($edition, $corps, $collectivite['om_collectivite_idx']);
            $corps = $css.$corps;
            $corps = str_replace("&amp;numpage", $pdf->getPageNumGroupAlias(), $corps);
            $corps = str_replace("&amp;nbpages", $pdf->getPageGroupAlias(), $corps);
            $corps = str_replace("&numpage", $pdf->getPageNumGroupAlias(), $corps);
            $corps = str_replace("&nbpages", $pdf->getPageGroupAlias(), $corps);
            // Affichage du corps si non vide
            if (trim($corps) != "") {
                // TCPDF::writeHTML()
                $pdf->writeHTML(
                    // $html (string) text to display.
                    $corps,
                    // $ln (boolean) if true add a new line after text (default = true).
                    true,
                    // $fill (boolean) Indicates if the background must be painted (true) or transparent (false).
                    false,
                    // $reseth (boolean) if true reset the last cell height (default false).
                    true,
                    // $cell (boolean) if true add the current left (or right for RTL) padding to each Write (default false).
                    false
                );
            }
        }

        //
        if (is_null($pdf)) {
            return array(
                "pdf_output" => "",
                "filename" => "",
            );
        }

        // Construction du nom du fichier
        $filename = date("Ymd-His");
        $filename .= "-".$edition_elem;
        $filename .= "-".$obj;
        $filename .= ".pdf";

        //
        $pdf_output = $this->handle_output($pdf, $filename);

        //
        return array(
            "pdf_output" => $pdf_output,
            "filename" => $filename,
        );
    }

    /**
     * VIEW - view_pdfetat.
     *
     * Génération de l'édition PDF pour une édition "etat".
     *
     * @param string $collectivite Identifiant de la collectivité spécifique
     *                             (dans certains cas d'utilisation liés au
     *                             multi-collectivité) sur laquelle porte
     *                             l'édition.
     *
     * @return array
     */
    function view_pdfetat($collectivite = null) {
        //
        return $this->pdf_om_etat_om_lettretype("etat", $collectivite);
    }

    /**
     * VIEW - view_pdflettretype.
     *
     * Génération de l'édition PDF pour une édition "lettretype".
     *
     * @param string $collectivite Identifiant de la collectivité spécifique
     *                             (dans certains cas d'utilisation liés au
     *                             multi-collectivité) sur laquelle porte
     *                             l'édition.
     *
     * @return array
     */
    function view_pdflettretype($collectivite = null) {
        //
        return $this->pdf_om_etat_om_lettretype("lettretype", $collectivite);
    }

    /**
     * Gestion de la sélection des paramètres de l'édition à générer
     * en fonction du paramètre actif et/ou du niveau de la collectivité.
     *
     * Méthode spécifique à OM_ETAT et OM_LETTRETYPE.
     *
     * @param string $table                      Table.
     * @param string $id_edition                 Identifiant de l'édition.
     * @param string $id_collectivite            Identifiant de la colletcivité.
     * @param string $idx_edition_direct_preview Identifiant numérique de l'édition.
     *
     * @return array
     */
    function get_edition_from_collectivite($table, $id_edition, $id_collectivite, $idx_edition_direct_preview = null) {
        /**
         * Templates de la requête.
         */
        //
        $query_template_base = '
        SELECT
            *
        FROM
            %1$s%2$s
            LEFT JOIN %1$som_requete
                ON %2$s.om_sql=om_requete.om_requete
        ';
        //
        $query_template_direct_idx = $query_template_base.'
        WHERE
            %2$s.%2$s=%5$s
        ';
        //
        $query_template = $query_template_base.'
        WHERE
            %2$s.id=\'%3$s\'
            AND %2$s.om_collectivite=\'%4$s\'
        ';
        //
        $query_template_actif = $query_template.'
            AND %2$s.actif IS TRUE
        ';


        if (!is_null($idx_edition_direct_preview)) {
            /**
             * Cas n°5 : On récupère l'enregistrement 'om_<EDITION>' depuis son
             * identifiant numérique (clé primaire).
             */
            //
            $sql = sprintf(
                $query_template_direct_idx,
                DB_PREFIXE,
                $table,
                $this->f->db->escapeSimple($id_edition),
                $id_collectivite,
                $idx_edition_direct_preview
            );
            $res1 = $this->f->db->query($sql);
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->f->isDatabaseError($res1);
            //
            if ($res1->numrows() != 0) {
                $edition = $res1->fetchRow(DB_FETCHMODE_ASSOC);
                return $edition;
            }
            /**
             * Cas n°4 : Aucune édition ne correspond alors on retourne la valeur null
             */
            //
            return null;
        }

        /**
         * Cas n°1 : On récupère l'enregistrement 'om_<EDITION>' de la
         * collectivité en cours dans l'état 'actif'.
         */
        //
        $sql = sprintf(
            $query_template_actif,
            DB_PREFIXE,
            $table,
            $this->f->db->escapeSimple($id_edition),
            $id_collectivite
        );
        $res1 = $this->f->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->f->isDatabaseError($res1);
        //
        if ($res1->numrows() != 0) {
            $edition = $res1->fetchRow(DB_FETCHMODE_ASSOC);
            return $edition;
        }
        // Si on obtient aucun résultat au cas n°1
        // On libère le résultat de la requête précédente
        $res1->free();
        // On récupère l'identifiant de la collectivité de niveau 2
        $sql = " select om_collectivite from ".DB_PREFIXE."om_collectivite ";
        $sql .= " where niveau='2' ";
        $niveau = $this->f->db->getone($sql);
        $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
        $this->f->isDatabaseError($niveau);

        /**
         * Cas n°2 : On récupère l'enregistrement 'om_<EDITION>' de la collectivité
         * de niveau 2 dans l'état 'actif'
         */
        $sql = sprintf(
            $query_template_actif,
            DB_PREFIXE,
            $table,
            $this->f->db->escapeSimple($id_edition),
            ($niveau == "" ? -1 : $niveau)
        );
        $res1 = $this->f->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->f->isDatabaseError($res1);
        //
        if ($res1->numrows() != 0) {
            $edition = $res1->fetchRow(DB_FETCHMODE_ASSOC);
            return $edition;
        }
        // Si on obtient aucun résultat au cas n°2
        // On libère le résultat de la requête précédente
        $res1->free();

        /**
         * Cas n°3 : On récupère l'enregistrement 'om_<EDITION>' de la collectivité
         * de niveau 2 dans n'importe quel état
         */
        $sql = sprintf(
            $query_template,
            DB_PREFIXE,
            $table,
            $this->f->db->escapeSimple($id_edition),
            ($niveau == "" ? -1 : $niveau)
        );
        $res1 = $this->f->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->f->isDatabaseError($res1);
        //
        if ($res1->numrows() != 0) {
            $edition = $res1->fetchRow(DB_FETCHMODE_ASSOC);
            return $edition;
        }
        // Si on obtient aucun résultat au cas n°3
        // On libère le résultat de la requête précédente
        $res1->free();

        /**
         * Cas n°4 : Aucune édition ne correspond alors on retourne la valeur null
         */
        //
        return null;
    }

    /**
     * Récupère le chemin du logo en fonction de la collectivité :
     * si un logo actif existe pour la collectivité passée en paramètre on le
     * retourne sinon on retourne celui de la collectivité multi.
     *
     * Méthode spécifique à OM_ETAT et OM_LETTRETYPE.
     *
     * @param string  $id_logo         identifiant du logo
     * @param integer $id_collectivite identifiant de la collectivité
     *
     * @return mixed null si aucun logo ou array contenant les informations
     *               du logo.
     */
    function get_logo_from_collectivite($id_logo, $id_collectivite) {
        //
        $sql = "
        SELECT
            fichier,
            resolution
        FROM
            ".DB_PREFIXE."om_logo
            JOIN ".DB_PREFIXE."om_collectivite
                ON om_logo.om_collectivite=om_collectivite.om_collectivite
        WHERE
            (
                om_logo.om_collectivite=".$id_collectivite."
                OR om_collectivite.niveau='2'
            )
            AND om_logo.id='".$id_logo."'
            AND om_logo.actif IS TRUE
        ORDER BY
            niveau ASC
        LIMIT 1
        ";
        //
        $res = $this->f->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->f->isDatabaseError($res);
        // Si aucun logo ne correspond alors on retourne la valeur 'null'
        if ($res->numrows() == 0) {
            return null;
        }
        //
        $row = $res->fetchRow(DB_FETCHMODE_ASSOC);
        //
        $logo_path = $this->f->storage->getPath($row["fichier"]);
        // Si le fichier n'existe pas alors on retourne la valeur 'null'
        if (!file_exists($logo_path) || is_dir($logo_path)) {
            return null;
        }
        //
        $logo_type = str_ireplace(
            "image/",
            "",
            $this->f->storage->getMimetype($row['fichier'])
        );
        //
        $logo_resolution = $row["resolution"];
        if ($logo_resolution != "") {
            //
            $size = getimagesize($logo_path);
            //
            $logo_w = $size[0] / ($logo_resolution / 25.4);
            $logo_h = $size[1] / ($logo_resolution / 25.4);
        } else {
            $logo_w = 0;
            $logo_h = 0;
        }
        //
        $logo = array(
            "file" => $logo_path,
            "w" => $logo_w,
            "h" => $logo_h,
            "type" => $logo_type,
        );
        //
        return $logo;
    }

    /**
     * Récupération des valeurs des VARIABLES DE REMPLACEMENT.
     *
     * Méthode spécifique à OM_ETAT et OM_LETTRETYPE.
     *
     * Cette méthode permet de récupérer les valeurs pour les variables de
     * remplacement globales à toute l'application.
     *
     * ATTENTION le résultat est stocké à partir du premier appel
     * et renvoyé directement aux appels suivants.
     *
     * XXX Vérifier le fonctionnement du multi collectivité ici.
     *
     * @param null|mixed $om_collectivite_idx Identifiant de la collectivité.
     *
     * @return array
     */
    function get_substitution_vars_values($om_collectivite_idx = null) {
        // Définition d'une fonction de tri si elle n'existe pas déjà.
        if (!function_exists("cmp_key_length")) {
            /**
             * Fonction de comparaison de la longeur de deux chaînes de caractères.
             *
             * @param string $a Chaîne A.
             * @param string $b Chaîne B.
             *
             * @ignore
             */
            function cmp_key_length($a, $b) {
                //
                $a = strlen($a);
                $b = strlen($b);
                //
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? 1 : -1;
            }
        }
        // La classe 'om_requete' permet de récupérer ces valeurs.
        $om_requete = $this->f->get_inst__om_dbform(array(
            "obj" => "om_requete",
            "idx" => 0,
        ));
        $substitution_vars_values = $om_requete->get_substitution_vars('values', $om_collectivite_idx);
        // On tri le tableau par clé de la chaîne de caractères la plus longue
        // vers la chaîne de caractères la plus courte pour pallier à
        // l'éventuel problème de remplacement partiel (exemple : &aujourdhui et
        // &aujourd'hui_lettre, si c'est la première variable de remplacement
        // qui est remplacée en premier alors on va remplacer les occurences de
        // la seconde par une mauvaise valeur, le tri contre ce comportement).
        uksort($substitution_vars_values, 'cmp_key_length');
        // On retourne le tableau de valeurs.
        return $substitution_vars_values;
    }

    /**
     * Récupération des valeurs des CHAMPS DE FUSION.
     *
     * Méthode spécifique à OM_ETAT et OM_LETTRETYPE.
     *
     * @param string $id_om_requete Identifiant numérique de l'enregistrement
     *                              'om_requete'.
     * @param string $idx           Identifiant de l'élément.
     *
     * @return array
     */
    function get_merge_fields_values($id_om_requete, $idx) {
        // Instanciation de la requête
        $om_requete = $this->f->get_inst__om_dbform(array(
            "obj" => "om_requete",
            "idx" => $id_om_requete,
        ));
        // Récupération de son type
        $type_requete = $om_requete->getVal('type');

        // CHAMPS DE FUSION - Récupération des valeurs
        // Initialisation du tableau de champs de fusion
        $values = array();
        // Cas requête SQL
        if ($type_requete == 'sql') {
            // récupération de la requête SQL
            $sql = $om_requete->getVal('requete');
            // remplacement d'idx par sa valeur
            $sql = str_replace('&idx', $idx, $sql);
            // définition du schéma
            $sql = str_replace('&DB_PREFIXE', DB_PREFIXE, $sql);
            // exécution de la requête
            $res = $this->f->db->query($sql);
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->f->isDatabaseError($res);
            // création du tableau des champs de fusion
            $values = &$res->fetchRow(DB_FETCHMODE_ASSOC);
        }
        // Cas requête objet
        if ($type_requete == 'objet') {
            // récupération du(des) objet(s) et pour l'unique(premier)
            // son éventuelle méthode
            $classes = $om_requete->getVal('classe');
            $methode = $om_requete->getVal('methode');
            $classes = explode(';', $classes);
            $nb_classes = count($classes);
            $next_value = "";
            foreach ($classes as $key => $classe) {
                $classe = $classes[$key];
                // si unique(premier) objet
                if ($key == 0) {
                    $sql_object = $this->f->get_inst__om_dbform(array(
                        "obj" => $classe,
                        "idx" => $idx,
                    ));
                    // Si on récupère un paramètre spécifique de surcharge
                    // alors on le passe en paramètre à l'objet instancié
                    if (isset($_GET["specific"])) {
                        $sql_object->setParameter(
                            "edition_params_specific",
                            $_GET["specific"]
                        );
                    }
                    // Si l'objet instancié ne correspond à aucun enregistrement
                    // et qu'il n'y a aucun paramètre spécifique de surcharge
                    // on renvoi un tableau vide de valeurs pour afficher les
                    // libellés entre crochets
                    if ($sql_object->getVal($sql_object->clePrimaire) == null
                        && !isset($_GET["specific"])) {
                        $values = array();
                        continue;
                    }
                    // si une méthode custom existe on récupère ses valeurs
                    if ($methode != null && $methode != ''
                        && method_exists($sql_object, $methode)) {
                        $custom = $sql_object->$methode('values');
                        $values = array_merge($values, $custom);
                    }
                    // on récupère également les libellés par défaut
                    $default = $sql_object->get_merge_fields('values');
                    $values = array_merge($values, $default);
                } else {
                    // sinon traitement des éventuels objet supplémentaires
                    // si la valeur de la clé étrangère est valide
                    if ($next_value != null && $next_value != '') {
                        $sql_object = $this->f->get_inst__om_dbform(array(
                            "obj" => $classe,
                            "idx" => $next_value,
                        ));
                        // on ne récupère que les libellés par défaut
                        $default = $sql_object->get_merge_fields('values');
                        $values = array_merge($values, $default);
                    } else {
                        // sinon valeurs nulles pour supprimer l'appel
                        // aux champs de fusion dans l'édition
                        $sql_object = $this->f->get_inst__om_dbform(array(
                            "obj" => $classe,
                            "idx" => "]",
                        ));
                        $nuls = array();
                        $sql_object_table = $sql_object->table;
                        foreach ($sql_object->champs as $key => $champ) {
                            $nuls[$sql_object_table.".".$champ] ="";
                        }
                        $values = array_merge($values, $nuls);
                    }
                }
                // on récupère la valeur de liaison s'il y a encore un objet derrière
                if ($key < ($nb_classes - 1)) {
                    $j = $key + 1;
                    $next_objet = $classes[$j];
                    $next_objet = $this->f->get_inst__om_dbform(array(
                        "obj" => $next_objet,
                        "idx" => "]",
                    ));
                    // récupération de la clé primaire
                    $nextClePrimaire = $next_objet->clePrimaire;
                    // récupération de sa valeur
                    $next_value = $sql_object->getVal($nextClePrimaire);
                }
            }
        }
        //
        return $values;
    }

    /**
     * Remplace dans la chaîne passée en paramètre les variables de substitutions
     * et les champs de fusion par leurs valeurs.
     *
     * Méthode spécifique à OM_ETAT et OM_LETTRETYPE.
     * La boucle est réalisée 5 fois pour permettre de remplacer les champs de
     * dans les variables de remplacement et inversement.
     *
     * @param string $bloc                     Chaîne de caractères.
     * @param array  $substitution_vars_values Tableau de valeurs des variables de
     *                                         remplacement.
     * @param array  $merge_fields_values Tableau de valeurs des champs de fusion.
     *
     * @return string
     */
    function replace_all_elements($bloc, $substitution_vars_values, $merge_fields_values) {
        //
        for ($i = 0; $i < 5; $i++) {
            //
            $bloc = str_ireplace("&amp;", "&", $bloc);
            $bloc = $this->replace_substitution_vars($bloc, $substitution_vars_values);
            $bloc = $this->replace_merge_fields($bloc, $merge_fields_values);
        }
        //
        return $bloc;
    }

    /**
     * Remplace dans la chaîne passée en paramètres les variables de remplacement
     * par leurs valeurs.
     *
     * Méthode spécifique à OM_ETAT et OM_LETTRETYPE.
     *
     * @param string $bloc                     Chaîne de caractères.
     * @param array  $substitution_vars_values Tableau de valeurs des variables de
     *                                         remplacement.
     *
     * @return string
     */
    function replace_substitution_vars($bloc, $substitution_vars_values) {
        //
        foreach ($substitution_vars_values as $key => $value) {
            $bloc = str_ireplace("&".$key, $value, $bloc);
        }
        //
        return $bloc;
    }

    /**
     * Remplace dans la chaîne passée en paramètres les champs de fusion
     * par leurs valeurs.
     *
     * Méthode spécifique à OM_ETAT et OM_LETTRETYPE.
     *
     * @param string $bloc                Chaîne de caractères.
     * @param array  $merge_fields_values Tableau de valeurs des champs de fusion.
     *
     * @return string
     */
    function replace_merge_fields($bloc, $merge_fields_values) {
        // Explosion des champs à récupérer depuis la requête
        $temp = explode("[", $bloc);
        //
        for ($i = 1; $i < count($temp); $i++) {
            //
            $temp1 = explode("]", $temp[$i]);
            //
            if (isset($merge_fields_values[$temp1[0]])) {
                $bloc = str_ireplace(
                    "[".$temp1[0]."]",
                    $merge_fields_values[$temp1[0]],
                    $bloc
                );
            }
            //
            $temp1[0] = "";
        }
        //
        return $bloc;
    }

    /**
     * Gère la sortie PDF.
     *
     * La sortie est gréré en fonction du paramètre $_GET['output']. En fonction
     * de ce paramètre le PDF peut donc être envoyé en inline dans le navigateur,
     * en mode download, écrit sur le disque ou retourné sous forme de chaîne de
     * caractères.
     *
     * @param resource $pdf Instance d'une classe PDF.
     * @param string $filename Nom du fichier.
     *
     * @return void|string
     */
    private function handle_output($pdf, $filename) {
        //
        $pdf_output = "";
        //
        $output = "";
        if (isset($_GET["output"])) {
            $output = $_GET["output"];
        }
        if (!in_array($output, array("string", "file", "download", "inline", "no"))) {
            if ($this->f->getParameter("edition_output") == "download") {
                $output = "download";
            } else {
                $output = "inline"; // Valeur par defaut
            }
        }
        //
        if ($output == "string") {
            // S : renvoyer le document sous forme de chaine. name est ignore.
            $pdf_output = $pdf->Output("", "S");
        } elseif ($output == "file") {
            // F : sauver dans un fichier local, avec le nom indique dans name
            // (peut inclure un repertoire).
            $pdf->Output($this->f->getParameter("pdfdir").$filename, "F");
        } elseif ($output == "download") {
            // D : envoyer au navigateur en forcant le telechargement, avec le nom
            // indique dans name.
            $pdf->Output($filename, "D");
        } elseif ($output == "inline") {
            // I : envoyer en inline au navigateur. Le plug-in est utilise s'il est
            // installe. Le nom indique dans name est utilise lorsque l'on selectionne
            // "Enregistrer sous" sur le lien generant le PDF.
            $pdf->Output($filename, "I");
        } elseif ($output == "no") {
            //
        }
        //
        return $pdf_output;
    }

    /**
     * Expose le fichier PDF à l'utilisateur.
     *
     * @param string $pdf_output PDF sous forme de chaîne de carctères.
     * @param string $filename Nom du fichier.
     *
     * @return void
     */
    public function expose_pdf_output($pdf_output, $filename) {
        //
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le passé
        header("Content-Type: application/pdf");
        header("Accept-Ranges: bytes");
        //
        if ($this->f->getParameter("edition_output") == "download") {
            $dl = "attachment";
        } else {
            $dl = "inline";
        }
        //
        header("Content-Disposition: ".$dl."; filename=\"".$filename."\";" );
        //
        echo $pdf_output;
        die();
    }
}
