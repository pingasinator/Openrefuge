<?php
/**
 * Ce script contient la définition de la classe 'reqmo'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_reqmo.class.php 4348 2018-07-20 16:49:26Z softime $
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
 * Définition de la classe 'reqmo'.
 *
 * Cette classe gère le module 'Reqmo' du framework openMairie. Ce module
 * permet d'exporter des données de l'application sous différents formats :
 * écran, CSV, carte, pdf, ... Chaque reqmo disponible est paramétré dans un
 * fichier de configuration qui peut être composé manuellement ou généré depuis
 * le générateur.
 */
class reqmo extends om_base {

    /**
     * Constructeur.
     */
    public function __construct() {
        // Initialisation de la classe 'application'.
        $this->init_om_application();
    }

    /**
     * VIEW - view_reqmo.
     *
     * @return void
     */
    public function view_reqmo() {
        //
        $this->f->isAuthorized("reqmo");

        /**
         * Fonction permetant de retourner le contenu d'un fichier csv.
         *
         * @param array $data      tableau des lignes et colonnes du csv.
         * @param string $delimiter séparateur
         * @param string $enclosure enclosure
         *
         * @return string csv
         * @ignore
         */
        function generateCsv($data, $delimiter = ';', $enclosure = '"') {
            $contents="";
            $handle = tmpfile();
            foreach ($data as $line) {
                fputcsv($handle, $line, $delimiter, $enclosure);
            }
            rewind($handle);
            while (!feof($handle)) {
                $contents .= fread($handle, 8192);
            }
            fclose($handle);
            return $contents;
        }

        /**
         * Fonction permettant de lister les reqmos disponibles dans un répertoire.
         *
         * @param string $folder_path Path vers le répertoire.
         * @param array  $reqmo_list    Liste des reqmos (optionnelle).
         *
         * @return array Liste des reqmos disponibles.
         * @ignore
         */
        function get_reqmo_list_in_folder($folder_path = "", $reqmo_list = array()) {
            // On teste si le répertoire existe
            if (is_dir($folder_path)) {
                // Si le répertoire existe alors l'ouvre
                $folder = opendir($folder_path);
                // On liste le contenu du répertoire
                while ($file = readdir($folder)) {
                    // Si le nom du fichier contient la bonne extension
                    if (strpos($file, ".reqmo.inc.php")) {
                        // On récupère la première partie du nom du fichier
                        // c'est à dire sans l'extension complète
                        $elem = substr($file, 0, strlen($file) - 14);
                        // On l'ajoute à la liste des reqmos disponibles
                        // avec le path complet vers le script et le titre
                        $reqmo_list[$elem] = array(
                            "title" => __($elem),
                            "path" => $folder_path.$file,
                        );
                    }
                }
                // On ferme le répertoire
                closedir($folder);
            }
            // On retourne la liste des reqmos disponibles
            return $reqmo_list;
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
         * Récupération de la liste des reqmos disponibles.
         *
         * Ces reqmos correspondent aux requêtes mémorisées paramétrées dans des
         * scripts <reqmo>.reqmo.inc.php. Ces scripts sont généralement présents dans
         * le répertoire sql/<db_type>/ de l'application mais peuvent également être
         * présents dans le répertoire CUSTOM prévu à cet effet.
         */
        // On définit le répertoire STANDARD où se trouvent les scripts des reqmos
        $dir = getcwd();
        $dir = substr($dir, 0, strlen($dir) - 4)."/sql/".OM_DB_PHPTYPE."/";
        // On récupère la liste des reqmos disponibles dans ce répertoire STANDARD
        $reqmo_list = get_reqmo_list_in_folder($dir);
        //
        if ($this->f->get_custom("path", ".reqmo.inc.php") != null) {
            // On définit le répertoire CUSTOM où se trouvent les scripts des reqmos
            $dir = $this->f->get_custom("path", ".reqmo.inc.php");
            // On récupère la liste des reqmos disponibles dans ce répertoire CUSTOM
            $reqmo_list = get_reqmo_list_in_folder($dir, $reqmo_list);
        }
        // On tri la liste des reqmos disponibles par ordre alphabétique
        uasort($reqmo_list, "sort_by_lower_title");

        /**
         *
         */
        // Nom de l'objet metier
        // Initialisation des paramètres
        $params = array(
            // Nom de l'objet metier
            "obj" => array(
                "default_value" => "",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        // Vérification de l'existence de l'objet
        // XXX Vérifier une permission spécifique ?
        if ($obj != "" && !array_key_exists($obj, $reqmo_list)) {
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
        $this->f->setTitle(__("export")." -> ".__("requetes memorisees").($obj != "" ? " -> ".__($obj) : ""));
        $this->f->setFlag(null);
        $this->f->display();
        //
        $description = __(
            "Le module 'requetes memorisees' permet d'exporter des donnees de ".
            "l'application en fonction de criteres parametrables."
        );
        $this->f->displayDescription($description);

        /**
         *
         */
        //
        if ($obj == "") {
            //
            $usecase = "reqmo-list";
        } elseif (isset($_POST["reqmo-form-valid"])
            || $this->f->get_submitted_get_value('step') == 1) {
            //
            $usecase = "reqmo-out";
        } else {
            //
            $usecase = "reqmo-form";
        }

        /**
         *
         */
        //
        if ($usecase == "reqmo-list") {

            /**
             * Affichage de la liste des reqmos disponibles.
             */
            //
            echo "\n<div id=\"reqmo-list\">\n";
            // Composition de la liste de liens vers les reqmos disponibles.
            // En partant de la liste des reqmos disponibles, on compose une liste
            // d'éléments composés d'une URL, d'un libellé, et de tous les paramètres
            // permettant l'affichage de l'élément comme un élément de liste.
            $list = array();
            foreach ($reqmo_list as $key => $value) {
                //
                $list[] = array(
                    "href" => OM_ROUTE_MODULE_REQMO."&obj=".$key,
                    "title" => $value["title"],
                    "class" => "om-prev-icon reqmo-16",
                    "id" => "action-reqmo-".$key."-exporter",
                );
            }
            //
            $this->f->layout->display_list(
                array(
                    "title" => __("choix de la requete memorisee"),
                    "list" => $list,
                )
            );
            //
            echo "</div>\n";

        } elseif ($usecase == "reqmo-form") {

            /**
             * Affichage du formulaire de requête mémorisée
             */
            //
            echo "\n<div id=\"reqmo-form\">\n";
            //
            include $reqmo_list[$obj]["path"];

            //
            $fields_list = array(
                "SELECT" => array(
                    "checkbox" => array(
                    ),
                ),
                "WHERE" => array(
                    "text" => array(
                    ),
                    "select" => array(
                    ),
                ),
                "ORDER" => array(
                    "select" => array(
                    ),
                ),
            );

            //
            // Chaque élément entre crochets dans la requête représente un élément
            // de sélection, de filtre ou de tri. On peut appeler ces éléments des
            // sélecteurs. On extrait donc chacun de ces sélecteurs de la requête
            // puis on boucle sur ces sélecteurs.
            $elements = explode("[", $reqmo["sql"]);
            foreach ($elements as $key => $element) {
                // On saute le premier élément (select)
                if ($key == 0) {
                    continue;
                }
                // On extrait le sélecteur en récupérant tout ce qui se trouve
                // avant le crochet fermant
                $selecteur_complet = explode("]", $element);
                $selecteur_complet = $selecteur_complet[0];
                //
                $temp4 = explode (" as ", $selecteur_complet);
                if (isset($temp4[1])) {
                    $selecteur = $temp4[1];
                } else {
                    $selecteur = $selecteur_complet;
                }
                //
                $mode = "";
                if (isset($reqmo[$selecteur])) {
                    $mode = $reqmo[$selecteur];
                }
                //
                if ($mode == "checked") {
                    // MODE - ELEMENT DE LA CLAUSE "SELECT"
                    //
                    $fields_list["SELECT"]["checkbox"][] = $selecteur;
                } elseif (is_array($mode)) {
                    // MODE - ELEMENT DE LA CLAUSE "ORDER BY"
                    //
                    $fields_list["ORDER"]["select"][$selecteur] = array();
                    foreach ($mode as $elem) {
                        $fields_list["ORDER"]["select"][$selecteur][] = array(
                            "value" => $elem,
                            "libelle" => __($elem),
                        );
                    }
                } elseif (strtolower(substr($mode, 0, 6)) == "select") {
                    // MODE - ELEMENT DE LA CLAUSE "WHERE"
                    // Filtre en fonction d'une liste de valeur
                    //
                    $fields_list["WHERE"]["select"][$selecteur] = array();
                    // Exécution de la requête
                    $res1 = $this->f->db->query($mode);
                    // Logger
                    $this->f->addToLog(__METHOD__."(): db->query(\"".$mode."\");", VERBOSE_MODE);
                    // Vérification d'une éventuelle erreur de base de données
                    $this->f->isDatabaseError($res1);
                    while ($row1 =& $res1->fetchRow()) {
                        $fields_list["WHERE"]["select"][$selecteur][] = array(
                            "value" => $row1[0],
                            "libelle" => $row1[1],
                        );
                    }
                } else {
                    // MODE - ELEMENT DE LA CLAUSE "WHERE"
                    // Filtre en fonction d'une saisie utilisateur
                    //
                    if (in_array($selecteur, $fields_list["WHERE"]["text"]) === false) {
                        $fields_list["WHERE"]["text"][] = $selecteur;
                    }
                }
            }

            // Ouverture de la balise formulaire
            $this->f->layout->display__form_container__begin(array(
                "action" => OM_ROUTE_MODULE_REQMO."&obj=".$obj,
                "name" => "f1",
            ));
            //
            $validation = 0;
            $maj = 0;
            $champs = array();
            //
            foreach ($fields_list["SELECT"]["checkbox"] as $field) {
                $champs[] = $field;
            }
            //
            foreach ($fields_list["WHERE"]["text"] as $field) {
                $champs[] = $field;
            }
            //
            foreach ($fields_list["WHERE"]["select"] as $field => $select) {
                $champs[] = $field;
            }
            //
            foreach ($fields_list["ORDER"]["select"] as $field => $select) {
                $champs[] = $field;
            }
            //
            $champs[] = "sortie";
            $champs[] = "separateur";
            $champs[] = "limite";
            //
            $form = $this->f->get_inst__om_formulaire(array(
                "validation" => $validation,
                "maj" => $maj,
                "champs" => $champs,
            ));
            //
            foreach ($fields_list["SELECT"]["checkbox"] as $key => $field) {
                if ($key == 0) {
                    $form->setBloc($field, "D", __("Champs a afficher"));
                    $form->setBloc($field, "D", "", "group");
                }
                $form->setType($field, "checkbox");
                $form->setLib($field, __($field));
                $form->setTaille($field, 30);
                $form->setMax($field, 30);
                $form->setVal($field, "t");
                if ($key == count($fields_list["SELECT"]["checkbox"])-1) {
                    $form->setBloc($field, "F");
                    $form->setBloc($field, "F");
                }
            }
            //
            foreach ($fields_list["WHERE"]["text"] as $field) {
                $form->setType($field, "text");
                $form->setLib($field, $field);
                $form->setTaille($field, 30);
                $form->setMax($field, 30);
            }
            //
            foreach ($fields_list["WHERE"]["select"] as $field => $select) {
                $form->setType($field, "select");
                $contenu = array(0 => array(), 1 => array());
                foreach ($select as $key => $value) {
                    $contenu[0][] = $value["value"];
                    $contenu[1][] = $value["libelle"];
                }
                $form->setSelect($field, $contenu);
                $form->setLib($field, $field);
            }
            //
            foreach ($fields_list["ORDER"]["select"] as $field => $select) {
                $form->setType($field, "select");
                $contenu = array(0 => array(), 1 => array());
                foreach ($select as $key => $value) {
                    $contenu[0][] = $value["value"];
                    $contenu[1][] = $value["libelle"];
                }
                $form->setSelect($field, $contenu);
                $form->setLib($field, $field);
            }
            //
            $form->setBloc("sortie", "D", __("Options de sortie"));
            //
            $form->setLib("sortie", __("Format de sortie"));
            $form->setType("sortie", "select");
            $contenu = array(
                0 => array("tableau", "csv", ),
                1 => array(__("Tableau - Affichage a l'ecran"), __("CSV - Export vers logiciel tableur"), ),
            );
            $form->setSelect("sortie", $contenu);
            //
            $form->setLib("separateur", __("Separateur de champs (pour le format CSV)"));
            $form->setType("separateur", "select");
            $contenu = array(
                0 => array(";", "|", ",", ),
                1 => array("; ".__("(point-virgule)"), "| ".__("(pipe)"), ", ".__("(virgule)"), ),
            );
            $form->setSelect("separateur", $contenu);
            //
            $form->setLib("limite", __("Nombre limite d'enregistrements a afficher (pour le format Tableau)"));
            $form->setType("limite", "text");
            $form->setTaille("limite", 10);
            $form->setMax("limite", 5);
            $form->setVal("limite", 100);
            //
            $form->setBloc("limite", "F");
            //
            $form->entete();
            $form->afficher($champs, $validation, false, false);
            $form->enpied();

            // Affichage des actions de controles du formulaire
            $this->f->layout->display__form_controls_container__begin(array(
                "controls" => "bottom",
            ));
            // Bouton de validation du formulaire
            $this->f->layout->display__form_input_submit(array(
                "name" => "reqmo-form-valid",
                "value" => __("Executer la requete sur :")." '".__($obj),
            ));
            // Lien retour
            $this->f->layout->display_lien_retour(array(
                "href" => OM_ROUTE_MODULE_REQMO,
            ));
            // Fermeture du conteneur des actions de controles du formulaire
            $this->f->layout->display__form_controls_container__end();
            // Fermeture de la balise formulaire
            $this->f->layout->display__form_container__end();
            // Fermeture du container global
            echo "</div>\n";

        } elseif ($usecase == "reqmo-out") {

            /**
             *
             */
            //
            echo "\n<div id=\"reqmo-out\">\n";
            //
            include $reqmo_list[$obj]["path"];

            // STEP 1 - Composition de la requête

            //
            $temp = explode ("[", $reqmo["sql"]);
            //
            for ($i = 1; $i < sizeof ($temp); $i++) {
                //
                $temp1 = explode ("]", $temp [$i]);
                //
                $temp4 = explode (" as ", $temp1 [0]);
                //
                if (isset($temp4[1])) {
                    $temp5 = $temp4[1]; // uniquement as
                } else {
                    $temp5 = $temp1[0]; // en entier
                }
                //
                if (isset($_POST[$temp5])) {
                    $temp2 = $_POST[$temp5];
                } else {
                    $temp2 = "";
                }
                //
                if (isset($reqmo[$temp5])) {
                    //
                    if ($reqmo[$temp5] == "checked") {
                        //
                        if ($temp2 == 'Oui') {
                            $reqmo ['sql'] = str_replace ("[".$temp1[0]."]", $temp1[0], $reqmo['sql']);
                        } else {
                            $reqmo['sql'] = str_replace("[".$temp1[0]."],", '', $reqmo['sql']);
                            $reqmo['sql'] = str_replace(",[".$temp1[0]."]", '', $reqmo['sql']);
                            $reqmo['sql'] = str_replace(", [".$temp1[0]."]", '', $reqmo['sql']);
                            $reqmo['sql'] = str_replace("[".$temp1[0]."]", '', $reqmo['sql']);
                        }
                    } else {
                        $reqmo['sql'] = str_replace("[".$temp1[0]."]", $temp2, $reqmo['sql']);
                    }
                } else {
                    $reqmo['sql'] = str_replace("[".$temp1[0]."]", $temp2, $reqmo['sql']);
                }
                $temp1[0] = "";
            }
            //
            $blanc = 0;
            $temp = "";
            for ($i = 0; $i < strlen($reqmo['sql']); $i++) {
                //
                if (substr($reqmo['sql'], $i, 1) == chr(13)
                    or substr($reqmo['sql'], $i, 1) == chr(10)
                    or substr($reqmo['sql'], $i, 1) == chr(32)) {
                    //
                    if ($blanc == 0) {
                        $temp .= chr(32);
                    }
                    $blanc=1;
                } else {
                    $temp .= substr($reqmo['sql'], $i, 1);
                    $blanc=0;
                }
            }
            $reqmo['sql'] = $temp;
            $reqmo['sql'] = str_replace(',,', ',', $reqmo['sql']);
            $reqmo['sql'] = str_replace(', ,', ',', $reqmo['sql']);
            $reqmo['sql'] = str_replace(', from', ' from', $reqmo['sql']);
            $reqmo['sql'] = str_replace('select ,', 'select ', $reqmo['sql']);
            // post limite
            if (isset($_POST['limite'])) {
                $limite = $_POST['limite'];
            } else {
                $limite = 100;
            }
            // post  sortie
            if (isset($_POST['sortie'])) {
                $sortie = $_POST['sortie'];
            } else {
                $sortie ='tableau';
            }
            //
            if (isset($_POST['separateur'])) {
                $separateur = $_POST['separateur'];
            } else {
                $separateur = ';';
            }
            // limite uniquement pour tableau
            if ($sortie == 'tableau') {
                $reqmo['sql'] .= " limit ".intval($limite);
            }

            // STEP 2 - Exécution de la requête

            // Exécution de la requête
            $res = $this->f->db->query($reqmo['sql']);
            // Logger
            $this->f->addToLog(__METHOD__."(): db->query(\"".$reqmo['sql']."\");", VERBOSE_MODE);
            // Gestion d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            //
            $info = $res->tableInfo();

            // STEP 3 - Construction et affichage de la sortie
            if ($sortie == 'tableau') {
                // OUT => tableau
                //
                echo "&nbsp;";
                $param['class']="tab";
                $param['idcolumntoggle']="requeteur";
                $this->f->layout->display_table_start($param);
                //echo "<table class=\"tab-tab\">\n";
                //
                echo "<thead><tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">";
                $key=0;
                foreach ($info as $elem) {
                    $param = array(
                        "key" => $key,
                        "info" => $info
                    );
                    $this->f->layout->display_table_cellule_entete_colonnes($param);
                    echo "<center>".__($elem['name'])."</center></th>";
                    $key = $key + 1;
                }
                echo "</tr></thead>\n";
                //
                $cptenr = 0;
                while ($row=& $res->fetchRow()) {
                    //
                    echo "<tr class=\"tab-data ".($cptenr % 2 == 0 ? "odd" : "even")."\">\n";
                    //
                    $cptenr = $cptenr + 1;
                    $i = 0;
                    foreach ($row as $elem) {
                        if (is_numeric($elem)) {
                            echo "<td   class='resultrequete' align='right'>";
                        } else {
                            echo "<td  class='resultrequete'>";
                        }
                        $tmp = "";
                        $tmp = str_replace(chr(13).chr(10), '<br>', $elem);
                        echo $tmp."</td>";
                        $i++;
                    }
                    echo "</tr>\n";
                }
                //
                echo "</tbody></table>\n";
                if ($cptenr == 0) {
                    echo "<br>".__('aucun')."&nbsp;".__('enregistrement')."<br>";
                }
            } elseif ($sortie == 'csv') {
                // Tableau de données à mettre en CSV
                $data = array();
                // Composition de la première ligne avec les noms de colonnes de la requête
                $head = array();
                foreach ($info as $elem) {
                    $head[] = $elem['name'];
                }
                $data[] = $head;

                while ($row=& $res->fetchRow()) {
                    $data[]=$row;
                }
                $content=generateCsv($data, $separateur);

                // Écriture du fichier sur le disque
                $nom_fichier = "export_".$obj.".csv";
                $metadata = array(
                    "filename" => $nom_fichier,
                    "size" => strlen($content),
                    "mimetype" => "application/vnd.ms-excel",
                );
                $uid = $this->f->storage->create_temporary($content, $metadata);
                //
                // Affichage du lien de téléchargement vers le fichier
                echo  __("Le fichier a ete exporte, vous pouvez l'ouvrir immediatement en cliquant sur : ");
                $this->f->layout->display_link(array(
                    "href" => OM_ROUTE_FORM."&snippet=file&uid=".$uid."&amp;mode=temporary",
                    "title" => __("Télécharger le fichier CSV")." [".$nom_fichier."]",
                    "class" => "om-prev-icon reqmo-16",
                    "target" => "_blank",
                    "id" => "reqmo-out-link",
                ));
            }

            // STEP 4 - Lien retour vers le formulaire précédent
            // Affichage des actions de controles du formulaire
            $this->f->layout->display__form_controls_container__begin(array(
                "controls" => "bottom",
            ));
            // Lien retour
            $this->f->layout->display_lien_retour(array(
                "href" => OM_ROUTE_MODULE_REQMO."&obj=".$obj,
            ));
            // Fermeture du conteneur des actions de controles du formulaire
            $this->f->layout->display__form_controls_container__end();

            //
            echo "</div>\n";
        }
    }
}
