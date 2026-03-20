<?php
/**
 * Ce script contient la définition de la classe 'application'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_application.class.php 4365 2018-11-19 18:27:10Z fmichon $
 */

/**
 *
 */
if (defined("PATH_OPENMAIRIE") !== true) {
    /**
     *
     */
    define("PATH_OPENMAIRIE", "");
}
require_once PATH_OPENMAIRIE."om_locales.inc.php";
require_once PATH_OPENMAIRIE."om_debug.inc.php";
if (defined("DEBUG") !== true) {
    /**
     *
     */
    define("DEBUG", PRODUCTION_MODE);
}
require_once PATH_OPENMAIRIE."om_logger.class.php";
require_once PATH_OPENMAIRIE."om_filestorage.class.php";
require_once PATH_OPENMAIRIE."om_layout.class.php";

/**
 * Définition de la classe 'application'.
 *
 * Cette classe est la classe principale du framework openMairie.
 */
class application {

    // {{{ VARs

    // {{{ DATABASE

    /**
     * Cette variable est un marqueur permettant d'indiquer si nous sommes
     * en mode développement du framework ou non.
     * @var boolean
     */
    protected $_framework_development_mode = false;

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations de bases de donnees presentes dans le fichier
     * de configuration. Chaque connexion est representee par une cle de ce
     * tableau.
     * @var array
     */
    var $database = array();

    /**
     * Cette variable ...
     * @var array
     */
    var $database_config = array();

    /**
     * Instance de connexion à la base de données.
     * @var null|database
     */
    public $db = null;

    // }}}

    // {{{ DIRECTORY

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations d'annauires presentes dans le fichier de
     * configuration. Chaque connexion est representee par une cle de ce
     * tableau.
     * @var array
     */
    var $directory = array();

    /**
     * Cette variable ...
     * @var array
     */
    var $directory_config = array();

    /**
     * Cette variable est l'objet renvoye par la connexion a l'annuaire
     * @var resource
     */
    var $dt = null;

    /**
     * Contient le profil par defaut des utilisateurs ajoutes depuis l'annuaire
     * Cette variable peut etre surchargee par le parametrage du fichier
     * dyn/directory.inc.php
     *
     * @var integer
     */
    var $default_om_profil = 1;

    // }}}

    // {{{ ENVOI DE MAIL

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations de serveur de mail presentes dans le fichier
     * de configuration. Chaque serveur est represente par une cle de ce
     * tableau.
     * @var array
     */
    var $mail = array();

    /**
     * Cette variable ...
     * @var array
     */
    var $mail_config = array();

    // }}}

    // {{{ FILESTORAGE

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations de stockage des fichiers présentes dans le fichier
     * de configuration. Chaque configuration est représenté par une clé de ce
     * tableau.
     * @var array
     */
    var $filestorage = array();

    /**
     * Cette variable ...
     * @var array
     */
    var $filestorage_config = array();

    /**
     * Instance de l'abstracteur de stockage de fichiers.
     * @var null|filestorage
     */
    public $storage = null;

    // }}}

    // {{{ LAYOUT

    /**
     * Instance de l'abstracteur d'affichage.
     * @var null|layout
     */
    public $layout = null;

    // }}}

    // {{{ AUTHENTIFICATION ET GESTION DES ACCES AUX PAGES SPECIALES

    /**
     * Cette variable permet de définir la liste des marqueurs spéciaux.
     *
     * Ces marqueurs spéciaux correspondent à des actions spécifiques liées
     * à l'authentification des utilisateurs :
     * - login -> permet d'authentifier l'utilisateur
     * - logout -> permet de déconnecter l'utilisateur
     * - anonym -> permet d'accéder à du contenu sans être authentifié
     *
     * @var array
     */
    var $special_flags = array(
        "login",
        "logout",
        "anonym",
    );

    // }}}

    // {{{

    /**
     * Permet de spécifier le nom de la table à utiliser pour récupérer la
     * matrice des permissions.
     * @var string
     */
    var $table_om_droit = "om_droit";

    /**
     * Permet de spécifier le nom du champ 'identifiant' de la table à utiliser
     * pour récupérer la matrice des permissions.
     * @var string
     */
    var $table_om_droit_field_id = "om_droit";

    /**
     * Permet de spécifier le nom du champ 'libelle' de la table à utiliser
     * pour récupérer la matrice des permissions.
     * @var string
     */
    var $table_om_droit_field_libelle = "libelle";

    /**
     * Permet de spécifier le nom du champ 'om_profil' de la table à utiliser
     * pour récupérer la matrice des permissions.
     * @var string
     */
    var $table_om_droit_field_om_profil = "om_profil";

    /**
     * Permet de spécifier le nom de la table à utiliser pour récupérer les
     * utilisateurs.
     * @var string
     */
    var $table_om_utilisateur = "om_utilisateur";

    /**
     * Permet de spécifier le nom du champ 'identifiant' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_id = "om_utilisateur";

    /**
     * Permet de spécifier le nom du champ 'om_collectivite' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_om_collectivite = "om_collectivite";

    /**
     * Permet de spécifier le nom du champ 'om_profil' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_om_profil = "om_profil";

    /**
     * Permet de spécifier le nom du champ 'om_type' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_om_type = "om_type";

    /**
     * Permet de spécifier le nom du champ 'login' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_login = "login";

    /**
     * Permet de spécifier le nom du champ 'login' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_password = "pwd";

    /**
     * Permet de spécifier le nom du champ 'password' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_nom = "nom";

    /**
     * Permet de spécifier le nom du champ 'nom' de la table à utiliser
     * pour récupérer les utilisateurs.
     * @var string
     */
    var $table_om_utilisateur_field_email = "email";

    /**
     * Permet de spécifier le nom de la table à utiliser pour récupérer les
     * profils.
     * @var string
     */
    var $table_om_profil = "om_profil";

    /**
     * Permet de spécifier le nom du champ 'identifiant' de la table à utiliser
     * pour récupérer les profils.
     * @var string
     */
    var $table_om_profil_field_id = "om_profil";

    /**
     * Permet de spécifier le nom du champ 'libelle' de la table à utiliser
     * pour récupérer les profils.
     * @var string
     */
    var $table_om_profil_field_libelle = "libelle";

    /**
     * Permet de spécifier le nom du champ 'hierarachie' de la table à utiliser
     * pour récupérer les profils.
     * @var string
     */
    var $table_om_profil_field_hierarchie = "hierarchie";

    /**
     * Permet de spécifier le nom de la table à utiliser pour récupérer les
     * collectivités.
     * @var string
     */
    var $table_om_collectivite = "om_collectivite";

    /**
     * Permet de spécifier le nom du champ 'identifiant' de la table à utiliser
     * pour récupérer les collectivités.
     * @var string
     */
    var $table_om_collectivite_field_id = "om_collectivite";

    /**
     * Permet de spécifier le nom du champ 'niveau' de la table à utiliser
     * pour récupérer les collectivités.
     * @var string
     */
    var $table_om_collectivite_field_niveau = "niveau";

    /**
     * Permet de spécifier le nom de la table à utiliser pour gérer la
     * réinitialisation des mots de passe.
     * @var string
     */
    var $table_om_password_reset = "om_password_reset";

    // }}}

    /**
     * Chaine permettant de stocker un éventuel message d'erreur lors de
     * l'authentification.
     * @var string
     */
    var $authentication_message = "";

    /**
     * Type de base de données. Exemple : pgsql, mysql, ...
     * @var null|string
     * @deprecated
     * @see OM_DB_PHPTYPE
     */
    var $phptype = null;

    /**
     * Format de date de la base de données. Exemple : AAAA-MM-JJ, ...
     * @var null|string
     * @deprecated
     * @see OM_DB_FORMATDATE
     */
    var $formatdate = null;

    /**
     * Schéma de la base de données. Exemple : public, myschema, ...
     * @var null|string
     * @deprecated
     * @see OM_DB_SCHEMA
     */
    var $schema = null;

    // {{{

    /**
     * Marqueur de la page.
     *  - $flag = null; =>
     *  - $flag = "nodoctype"; =>
     *  - $flag = "nohtml"; =>
     *  - $flag = "htmlonly"; =>
     *  - $flag = "htmlonly_nodoctype"; =>
     *  - $flag = "login"; =>
     *  - $flag = "logout"; =>
     *  - $flag = "anonym"; =>
     * @var null|string
     */
    var $flag = null;

    /**
     * Titre de la page.
     * @var null|string
     */
    var $title = null;

    /**
     * Permissions nécessaires pour accéder à la page.
     * @var null|string|array
     */
    var $right = null;

    /**
     * Description de la page.
     * @var string
     */
    var $description = "";

    // }}}

    // {{{

    /**
     * Tableau de paramètres pour la configuration générale.
     * @var array
     */
    var $config = array();

    /**
     * Tableau de paramètres pour la configuration du custom.
     * @var array
     */
    var $custom = array();

    // }}}

    // {{{

    /**
     * Pile de messages à afficher.
     * @var array
     */
    var $message = array();

    // }}}

    // {{{

    /**
     * Cet attribut nous permet de stocker le nombre de rubriques dans le menu.
     * L'objectif est d'ajouter une classe css au contenu pour permettre un
     * affichage correct en pleine largeur de la page si il n'y a aucune
     * rubrique dans le menu (égale à 0).
     *
     * @var mixed
     * @deprecated Inutilisé depuis la version 4.4.
     */
    var $nomenu = null;

    // }}}

    // {{{

    /**
     * Route permettant d'interfacer le tableau de bord.
     * @var string
     */
    protected $route__dashboard = "../app/index.php?module=dashboard";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__login = "../app/index.php?module=login";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__logout = "../app/index.php?module=logout";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__password = "../app/index.php?module=password";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__password_reset = "../app/index.php?module=login&mode=password_reset";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__tab = "../app/index.php?module=tab";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__soustab = "../app/index.php?module=soustab";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__form = "../app/index.php?module=form";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__sousform = "../app/index.php?module=sousform";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__map = "../app/index.php?module=map";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__module_edition = "../app/index.php?module=edition";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__module_import = "../app/index.php?module=import";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__module_reqmo = "../app/index.php?module=reqmo";

    /**
     * Route permettant d'interfacer ...
     * @var string
     */
    protected $route__module_gen = "../app/index.php?module=gen";

    // }}}

    // {{{

    /**
     * Marqueur permettant d'indiquer si l'utilisateur est authentiifé ou non.
     * @var boolean
     */
    var $authenticated = false;

    /**
     * Tableau de paramètres de la collectivité.
     * @var array
     */
    var $collectivite;

    /**
     * Matrice des permissions pour vérifier plus rapidement les autorisations
     * des utilisateurs.
     * @var array
     */
    var $rights = array();

    // }}}

    /**
     * Timestamp UNIX avec les microsecondes à l'instanciation de la classe.
     * @var null|float
     */
    var $timestart = null;

    /**
     * Valeurs postées.
     * @var array
     */
    var $submitted_post_value = array();

    /**
     * Valeurs passées à l'url.
     * @var array
     */
    var $submitted_get_value = array();

    // }}}

    // {{{ construct & destruct

    /**
     * Constructeur.
     *
     * @param null|string $flag Marqueur de la page.
     * @param null|string|array $right Permissions nécessaires pour accéder à
     *                                 la page.
     * @param null|string $title Titre de la page.
     */
    function __construct($flag = null, $right = null, $title = null) {
        //
        $this->timestart = microtime(true);

        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);

        // XXX  Faire la gestion correcte du paramétrage du layout
        $this->layout = new layout("jqueryui");
        if (!is_null($this->layout->error)) {
            echo "error : ".$this->layout->error;
            die();
        }

        $this->init_routes();
        $this->setParamsFromFiles();
        $this->checkParams();

        $this->setFlag($flag);
        $this->setTitle($title);
        $this->setRight($right);

        // Pour les connexions anonymes
        if ($this->flag == "anonym") {
            //
            $this->authenticated = true;
        } else {
            // Vérification de l'authentification de l'utilisateur et stockage du
            // résultat en attribut de l'objet
            $this->authenticated = $this->isAuthenticated();
        }

        //
        $this->set_submitted_value();

        // XXX  Faire la gestion correcte du paramétrage du layout
        if ($this->get_submitted_get_value("layout") !== null) {
            $_SESSION["layout"] = $this->get_submitted_get_value("layout");
        } elseif (isset($_SESSION["layout"])) {
            $_SESSION["layout"] = $_SESSION["layout"];
        } else {
            $_SESSION["layout"] = "jqueryui";
        }
        $this->layout = new layout($_SESSION["layout"]);
        if (!is_null($this->layout->error)) {
            echo "Erreur (error : ".$this->layout->error."). Ce layout n'existe pas. Contactez votre administrateur.";
            die();
        }

        //
        $this->setDefaultValues();

        // Déconnexion de l'utilisateur
        if ($this->flag == "logout") {
            $this->logout();
        }

        // Connexion de l'utilisateur
        if ($this->flag == "login") {
            $this->login();
        }

        //
        if ($this->authenticated) {
            // Connexion à la base de données si l'utilisateur est authentifié
            $this->connectDatabase();
            // Pour les connexions anonymes
            if ($this->flag != "anonym") {
                // on verifie que l'utilisateur connecté est toujours valide
                $this->checkIfUserIsAlwaysValid();
            }
            // Instanciation du mode de stockage des fichiers
            // Il est important d'appeler cette méthode après la mise en place de
            // la session sinon la méthode ne peut pas trouver le path par défaut
            // et après la méthode connectDatabase sinon on ne trouve pas la
            // configuration de la base sélectionnée
            $this->setFilestorage();
        }

        //
        if (!in_array($this->flag, $this->special_flags)) {
            //
            $this->getAllRights();
            //
            $this->getCollectivite();
            //
            $this->isAuthorized();
        }

        //
        $this->setMoreParams();

        // Affichage HTML
        $this->display();
    }

    /**
     * Desctructeur de la classe, cette methode (appelee automatiquement)
     * permet d'afficher le footer de la page, le footer HTML, et de
     * deconnecter la base de donnees
     *
     * @return void
     */
    function __destruct() {
        // Footer
        $this->displayFooter();
        // Deconnexion SGBD
        $this->disconnectDatabase();
        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);
        // Affichage des logs à l'écran
        logger::instance()->displayLog();
        // Écriture des erreurs (log de type DEBUG) dans le fichier d'erreurs
        logger::instance()->writeErrorLogToFile();
        // Une fois que les logs sont écrits dans le fichier, il est nécessaire
        // de les vider pour ne pas les réécrire dans ce même fichier lors
        // d'un prochain usage du logger.
        logger::instance()->cleanLog();
        // Footer HTML
        $this->displayHTMLFooter();
    }

    // }}}

    // {{{

    /**
     * Est-ce que nous sommes en mode développement du framework ?
     *
     * @return boolean
     */
    function is_framework_development_mode() {
        return $this->_framework_development_mode;
    }

    /**
     * Retourne le temps écoulé depuis l'instanciation de la classe.
     *
     * L'objectif est d'afficher cette information dans les logs destinés au
     * développeur pour identifier facilement les traitements et les requêtes
     * avec un temps d'exécution excessif.
     *
     * @return string Temps en secondes écoulé depuis l'instanciation de la
     *                classe. Exemple: '0.123'.
     */
    function elapsedtime() {
        return number_format((microtime(true) - $this->timestart), 3);
    }

    /**
     * Définit les constantes pour chaque route.
     *
     * @return void
     */
    function init_routes() {
        //
        $routes_map = array(
            "dashboard" => array(
                "constant" => "OM_ROUTE_DASHBOARD",
                "view" => "view_dashboard",
            ),
            "login" => array(
                "constant" => "OM_ROUTE_LOGIN",
                "view" => "view_login",
            ),
            "logout" => array(
                "constant" => "OM_ROUTE_LOGOUT",
                "view" => "view_logout",
            ),
            "password" => array(
                "constant" => "OM_ROUTE_PASSWORD",
                "view" => "view_password",
            ),
            "password_reset" => array(
                "constant" => "OM_ROUTE_PASSWORD_RESET",
                "view" => "view_password_reset",
            ),
            "tab" => array(
                "constant" => "OM_ROUTE_TAB",
                "view" => "view_tab",
            ),
            "soustab" => array(
                "constant" => "OM_ROUTE_SOUSTAB",
                "view" => "view_soustab",
            ),
            "form" => array(
                "constant" => "OM_ROUTE_FORM",
                "view" => "view_form",
            ),
            "sousform" => array(
                "constant" => "OM_ROUTE_SOUSFORM",
                "view" => "view_sousform",
            ),
            "map" => array(
                "constant" => "OM_ROUTE_MAP",
                "view" => "view_map",
            ),
            "module_edition" => array(
                "constant" => "OM_ROUTE_MODULE_EDITION",
                "view" => "view_module_edition",
            ),
            "module_gen" => array(
                "constant" => "OM_ROUTE_MODULE_GEN",
                "view" => "view_module_gen",
            ),
            "module_reqmo" => array(
                "constant" => "OM_ROUTE_MODULE_REQMO",
                "view" => "view_module_reqmo",
            ),
            "module_import" => array(
                "constant" => "OM_ROUTE_MODULE_IMPORT",
                "view" => "view_module_import",
            ),
        );
        //
        foreach ($routes_map as $key => $value) {
            $constant = $value["constant"];
            if (defined($constant) !== true) {
                $property_name = "route__".$key;
                /**
                 * @ignore
                 */
                define($constant, $this->$property_name);
            }
        }
    }

    /**
     * Permet de récupérer les différents fichiers de configuration.
     *
     * Cette méthode inclut les différents fichiers de configuration présents
     * dans le répertoire dyn/ de l'application pour charger le contenu de
     * la configuration dans des attributs de la classe et pouvoir les utiliser
     * à tout moment dans les différentes méthodes de la classe.
     *
     * @return void
     */
    function setParamsFromFiles() {
        //
        if (file_exists("../dyn/custom.inc.php")) {
            include("../dyn/custom.inc.php");
        }
        if (isset($custom)) {
            $this->custom = $custom;
        }

        //
        if (file_exists("../dyn/config.inc.php")) {
            include("../dyn/config.inc.php");
        }
        if (isset($config)) {
            $this->config = $config;
        }

        //
        if (file_exists("../dyn/database.inc.php")) {
            include("../dyn/database.inc.php");
        }
        if (isset($conn)) {
            $this->conn = $conn;
            //
            foreach ($this->conn as $key => $conn) {
                $this->database[$key] = array(
                    'title' => $conn[0],
                    'phptype' => $conn[1],
                    'dbsyntax' => $conn[2],
                    'username' => $conn[3],
                    'password' => $conn[4],
                    'protocol' => $conn[5],
                    'hostspec' => $conn[6],
                    'port' => $conn[7],
                    'socket' => $conn[8],
                    'database' => $conn[9],
                    'formatdate' => $conn[10],
                    'schema' => $conn[11],
                    'prefixe' => (isset($conn[12]) ? $conn[12]: ""),
                    'directory' => (isset($conn[13]) ? $conn[13]: ""),
                    'mail' => (isset($conn[14]) ? $conn[14]: ""),
                    'filestorage' => (isset($conn[15]) ? $conn[15]: ""),
                    'extras' => (isset($conn['extras']) ? $conn['extras']: ""),
                );
            }
        }
        // Trie le tableau
        ksort($this->database);

        //
        if (file_exists("../dyn/directory.inc.php")) {
            include("../dyn/directory.inc.php");
        }
        if (isset($directory)) {
            $this->directory = $directory;
        }

        //
        if (file_exists("../dyn/mail.inc.php")) {
            include("../dyn/mail.inc.php");
        }
        if (isset($mail)) {
            $this->mail = $mail;
        }

        //
        if (file_exists("../dyn/filestorage.inc.php")) {
            include("../dyn/filestorage.inc.php");
        }
        if (isset($filestorage)) {
            $this->filestorage = $filestorage;
        }
    }

    /**
     * Cette méthode permet de paramétrer les valeurs par défaut pour les
     * scripts CSS et JS. Les valeurs par défaut pour ces registres sont gérées
     * par le layout.
     *
     * @return void
     */
    function setDefaultValues() {
    }

    /**
     * Cette methode permet d'affecter des parametres dans un attribut de
     * l'objet.
     *
     * @return void
     */
    function setMoreParams() {
    }

    /**
     * Point d'entrée appelé après le login d'un utilisateur.
     *
     * @param null|mixed $utilisateur
     *
     * @return void
     */
    function triggerAfterLogin($utilisateur = null) {
    }

    /**
     * Positionne des valeurs par défaut aux paramètres de configuration obligatoires.
     *
     * @return void
     */
    function checkParams() {
        // Nom de l'application.
        // Si le paramètre 'application' n'est pas défini dans la configuration de
        // l'instance alors on le positionne à null pour identifier ce cas de
        // figure.
        (isset($this->config['application']) ? "" : $this->config['application'] = null);
        // Titre HTML.
        // Si le paramètre 'title' n'est pas défini dans la configuration de
        // l'instance alors on le positionne à null pour identifier ce cas de
        // figure.
        (isset($this->config['title']) ? "" : $this->config['title'] = null);
        // Les extensions de fichiers autorisées.
        (isset($this->config['upload_extension']) ? "" : $this->config['upload_extension'] = ".gif;.jpg;.jpeg;.png;.txt;.pdf;.csv;");
        // La taille maximale de fichiers autorisée.
        (isset($this->config['upload_taille_max']) ? "" : $this->config['upload_taille_max'] = str_replace('M', '', ini_get('upload_max_filesize')) * 1024);
        // Mode démonstration de l'application
        (isset($this->config['demo']) ? "" : $this->config['demo'] = false);
        // La valeur par défaut lorsqu’une permission n’existe pas
        (isset($this->config['permission_if_right_does_not_exist']) ? "" : $this->config['permission_if_right_does_not_exist'] = false);
        // Gestion du nom de la session.
        // Si le paramètre 'session_name' n'est pas défini dans la
        // configuration de l'instance alors on le positionne à null pour
        // identifier ce cas de figure.
        (isset($this->config['session_name']) ? "" : $this->config['session_name'] = null);
        // Gestion du mode de gestion des permissions.
        // Si le paramètre 'permission_by_hierarchical_profile' n'est pas défini
        // dans la configuration de l'instance alors on le positionne à null
        // pour identifier ce cas de figure.
        (isset($this->config['permission_by_hierarchical_profile']) ? "" : $this->config['permission_by_hierarchical_profile'] = null);
        // Gestion du favicon de l'application.
        // Si le paramètre 'favicon' n'est pas défini dans la configuration de
        // l'instance alors on le positionne à null pour identifier ce cas de
        // figure.
        (isset($this->config['favicon']) ? "" : $this->config['favicon'] = null);
        // Gestion du nombre de colonnes du tableau de bord.
        // Si le paramètre 'dashboard_nb_column' n'est pas défini dans la
        // configuration de l'instance alors on le positionne à null pour
        // identifier ce cas de figure.
        (isset($this->config['dashboard_nb_column']) ? "" : $this->config['dashboard_nb_column'] = null);
    }

    /**
     * Retourne une configuration du 'custom'.
     *
     * @param null|string $type
     * @param null|string $elem
     *
     * @return mixed
     */
    function get_custom($type = null, $elem = null) {
        //
        if (is_array($this->custom) !== true) {
            return null;
        }
        //
        $root_path = null;
        if (array_key_exists("root", $this->custom) === true
            && is_string($this->custom["root"]) === true
            && file_exists($this->custom["root"]) === true) {
            $root_path = $this->custom["root"];
        }
        //
        if ($type == "path") {
            if (array_key_exists($elem."_dir", $this->custom) === true
                && is_string($this->custom[$elem."_dir"]) === true) {
                //
                if ($root_path !== null
                    && file_exists($root_path.$this->custom[$elem."_dir"]) === true) {
                    //
                    return $root_path.$this->custom[$elem."_dir"];
                }
            }
            return null;
        }
        //
        if (in_array($type, array("tab", "soustab", "form", "obj", )) === true) {
            if (array_key_exists($type, $this->custom) === true
                && is_array($this->custom[$type]) === true
                && array_key_exists($elem, $this->custom[$type]) === true
                && is_string($this->custom[$type][$elem]) === true) {
                //
                if ($root_path !== null
                    && file_exists($root_path.$this->custom[$type][$elem]) === true) {
                    return $root_path.$this->custom[$type][$elem];
                }
                if (file_exists($this->custom[$type][$elem]) === true) {
                    return $this->custom[$type][$elem];
                }
            }
            return null;
        }
        return null;
    }

    // }}}

    // {{{

    /**
     * Redirige l'utilisateur vers le tableau de bord de l'application.
     *
     * @return void
     */
    function goToDashboard() {
        header("location: ".OM_ROUTE_DASHBOARD."");
        exit();
    }

    // }}}

    // {{{ AUTHENTICATION

    /**
     * Cette méthode permet de vérifier si l'utilisateur est authentifié ou
     * non à l'application et permet d'agir en conséquence
     *
     * @return boolean
     */
    function isAuthenticated() {
        //
        session_name($this->get_session_name());
        @session_start();
        // Valeur par defaut de la cle du tableau de parametrage de la base de
        // donnees
        if (!isset($_SESSION['coll']) or
            (isset($_SESSION['coll']) and
             !isset($this->database[$_SESSION['coll']]))) {
            //
            $keys = array_keys($this->database);
            asort($keys);
            if (isset($keys[0])) {
                $_SESSION['coll'] = $keys[0];
            }
        }
        // L'utilisateur est authentifie
        if (isset($_SESSION['login']) and $_SESSION['login'] != "") {
            // L'utilisateur vient de s'identifier
            if (isset($_SESSION['justlogin']) && $_SESSION['justlogin'] == true) {
                //
                $class = "ok";
                $message = __("Votre session est maintenant ouverte.");
                $this->addToMessage($class, $message);
                //
                $_SESSION['justlogin'] = false;
            }
            //
            return true;
        }
        // Si l'utilisateur n'est pas authentifie alors on le redirige
        // vers la page de login
        $this->redirectToLoginForm();
        //
        return false;
    }

    /**
     * Cette méthode redirige vers le fichier index.php du dossier parent
     * si le fla de la page n'est pas special
     *
     * @return void
     */
    function redirectToLoginForm() {
        //
        if (!in_array($this->flag, $this->special_flags)) {
            //
            $came_from = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://":"http://").$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            //
            if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
                echo "<script type=\"text/javascript\">location = '".OM_ROUTE_LOGIN."';</script>";
                die();
            } else {
                header("location: ".OM_ROUTE_LOGIN."&came_from=".urlencode($came_from));
                die();
            }
        }
    }

    /**
     * Cette méthode permet de vérifier si l'utilisateur connecté est toujours
     * valide dans la base utilisateur, de lui mettre à jour son profil si c'est
     * le cas et de le déconnecter si il ne fait plus partie des utilisateurs
     * valides
     *
     * @return void
     */
    function checkIfUserIsAlwaysValid() {
        //
        $this->user_infos = $this->retrieveUserProfile($_SESSION["login"]);
        //
        if (empty($this->user_infos)) {
            //
            $this->logout();
            //
            $this->redirectToLoginForm();
        } else {
            //
            $_SESSION["profil"] = $this->user_infos[$this->table_om_profil_field_hierarchie];
        }
    }

    // }}}

    // {{{



    /**
     * Cette méthode permet de récupérer l'ensemble de la table om_droit pour
     * la stocker dans un attribut et faire les vérifications de sécurité plus
     * rapidement.
     *
     * @return void
     */
    function getAllRights() {
        //
        $sql = "select ";
        $sql .= "".$this->table_om_droit.".".$this->table_om_droit_field_id." as table_om_droit_field_id, ";
        $sql .= "".$this->table_om_profil.".".$this->table_om_profil_field_id." as table_om_profil_field_id, ";
        $sql .= "".$this->table_om_droit.".".$this->table_om_droit_field_libelle." as table_om_droit_field_libelle, ";
        $sql .= "".$this->table_om_profil.".".$this->table_om_profil_field_libelle." as table_om_profil_field_libelle, ";
        $sql .= "".$this->table_om_profil.".".$this->table_om_profil_field_hierarchie." as table_om_profil_field_hierarchie ";
        $sql .= " from ".DB_PREFIXE.$this->table_om_droit." ";
        $sql .= " left join ".DB_PREFIXE.$this->table_om_profil." ";
        $sql .= " on ".$this->table_om_droit.".".$this->table_om_droit_field_om_profil."=".$this->table_om_profil.".".$this->table_om_profil_field_id." ";
        //
        if ($this->get_config__permission_by_hierarchical_profile() === false) {
            //
            $sql .= " where ".$this->table_om_profil.".".$this->table_om_profil_field_id."=".$this->user_infos[$this->table_om_utilisateur_field_om_profil];
        }
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
        while ($row =& $res->fetchrow(DB_FETCHMODE_ASSOC)) {
            $this->rights[$row["table_om_droit_field_libelle"]] = $row["table_om_profil_field_hierarchie"];
        }
        $res->free();
        //
        $this->addToLog(__METHOD__."(): \$this->rights = ".print_r($this->rights, true)."", EXTRA_VERBOSE_MODE);
    }

    /**
     * Cette méthode permet de vérifier si l'utilisateur est autorisé ou non à
     * accéder à un élément et permet d'agir en conséquence
     *
     * @param null|string|array $obj Permissions à vérifier.
     * @param string $operator Si plusieurs permissisions sont données dans le
     *                         premier paramètre alors on indique si on veut
     *                         que l'utilisateur en possède au moins une 'OR'
     *                         ou les possède toutes 'AND' (défaut).
     *
     * @return void|boolean
     */
    function isAuthorized($obj = null, $operator = "AND") {
        //
        if ($obj == null) {
            $obj = $this->right;
        }
        //
        if ($obj == null) {
            return true;
        }
        // L'utilisateur n'est pas autorisé à accéder à l'élément
        if (!$this->isAccredited($obj, $operator)) {
            //
            $message_class = "error";
            $message = __("Droits insuffisants. Vous n'avez pas suffisamment de ".
                         "droits pour acceder a cette page.");
            $this->addToMessage($message_class, $message);
            //
            $this->setFlag(null);
            if (!defined('REST_REQUEST')) {
                $this->display();
            }
            // Arrêt du script
            die();
        }
        // L'utilisateur est autorisé à accéder à l'élément
        return true;
    }

    /**
     * Cette méthode permet de vérifier si l'utilisateur est autorisé ou non à
     * accéder à un élément
     *
     * @param null|string|array $obj Permissions à vérifier.
     * @param string $operator Si plusieurs permissisions sont données dans le
     *                         premier paramètre alors on indique si on veut
     *                         que l'utilisateur en possède au moins une 'OR'
     *                         ou les possède toutes 'AND' (défaut).
     *
     * @return boolean
     */
    function isAccredited($obj = null, $operator = "AND") {
        //
        $log = "isAccredited(): \$obj = ";
        //
        if (is_array($obj)) {
            //
            $log .= print_r($obj, true)." - \$operator = ".$operator;
            //
            if (count($obj) == 0) {
                $this->addToLog(__METHOD__."(): ".$log." => return ".($this->config['permission_if_right_does_not_exist'] == true ? "true" : "false"), EXTRA_VERBOSE_MODE);
                return $this->config['permission_if_right_does_not_exist'];
            }
            //
            $permission_temporary = null;
            foreach ($obj as $elem) {
                //
                if (!isset($this->rights[$elem])) {
                    $permission_to_apply = $this->config['permission_if_right_does_not_exist'];
                } else {
                    if ($this->rights[$elem] <= $_SESSION['profil']) {
                        $permission_to_apply = true;
                    } else {
                        $permission_to_apply = false;
                    }
                }
                //
                if ($permission_temporary == null) {
                    $permission_temporary = $permission_to_apply;
                } else {
                    if ($operator == "OR") {
                        //
                        $permission_temporary |= $permission_to_apply;
                        // Affecte une valeur booléenne au résultat
                        if ($permission_temporary === 1) {
                            $permission_temporary = true;
                        } else {
                            $permission_temporary = false;
                        }
                    } else {
                        //
                        $permission_temporary &= $permission_to_apply;
                        // Affecte une valeur booléenne au résultat
                        if ($permission_temporary === 1) {
                            $permission_temporary = true;
                        } else {
                            $permission_temporary = false;
                        }
                    }
                }
            }
            //
            $this->addToLog(__METHOD__."(): ".$log." => return ".($permission_temporary == true ? "true" : "false"), EXTRA_VERBOSE_MODE);
            return $permission_temporary;
        } else {
            //
            $log .= $obj." - \$operator = ".$operator;
            //
            if (!isset ($this->rights[$obj])) {
                $this->addToLog(__METHOD__."(): ".$log." => return ".($this->config['permission_if_right_does_not_exist'] == true ? "true" : "false"), EXTRA_VERBOSE_MODE);
                return $this->config['permission_if_right_does_not_exist'];
            }
            //
            if (isset ($this->rights[$obj])
                and $this->rights[$obj] <= $_SESSION['profil']) {
                //
                $this->addToLog(__METHOD__."(): ".$log." => return true", EXTRA_VERBOSE_MODE);
                return true;
            }
            //
            return false;
        }
    }

    /**
     * Retourne et/ou stocke les paramètres de la collectivité.
     *
     * Cette méthode permet de retourner la liste des paramètres
     * de la collectivité :
     * - cas n°1 : de l'utilisateur connecté si aucun paramètre
     *   n'est fourni. Dans ce cas le résultat est stocker dans un
     *   attribut de l'objet.
     * - cas n°2 : passée en paramètre
     *
     * @param integer|null $om_collectivite_idx Identifiant de la collectivité.
     *
     * @return array
     */
    function getCollectivite($om_collectivite_idx = null) {
        // On vérifie si une valeur a été passée en paramètre ou non.
        if ($om_collectivite_idx === null) {
            // Cas d'utilisation n°1 : nous sommes dans le cas où on
            // veut récupérer les informations de la collectivité de
            // l'utilisateur et on stocke l'info dans un flag.
            $is_get_collectivite_from_user = true;
            // On initialise l'identifiant de la collectivité
            // à partir de la variable de session de l'utilisateur.
            $om_collectivite_idx = $_SESSION['collectivite'];
        } else {
            // Cas d'utilisation n°2 : nous sommes dans le cas où on
            // veut récupérer les informations de la collectivité
            // passée en paramètre et on stocke l'info dans le flag.
            $is_get_collectivite_from_user = false;
            // Si le paramètre fourni n'est pas numérique
            if (intval($om_collectivite_idx) === 0) {
                return array();
            }
        }
        // Initialisation du tableau de paramètres
        $collectivite_parameters = array();
        // La clé 'om_collectivite_idx' a pour objectif de stocker
        // l'identifiant de l'enregistrement dans la table
        // 'om_collectivite'.
        $collectivite_parameters['om_collectivite_idx'] = $om_collectivite_idx;
        // On récupère tous les paramètres correspondant à la collectivité
        // dans la table 'om_parametre'. On récupère l'ensemble de paramètres
        // de la collectivité passée en paramètre selon le principe objet de
        // l'héritage, la collectivité de niveau 2 étant la classe parent.
        // Exemple :
        // Avec les données suivantes,
        // collectivité A | niveau 2 | paramètre orange
        // collectivité A | niveau 2 | paramètre pomme
        // collectivité B | niveau 1 | paramètre orange
        // collectivité B | niveau 1 | paramètre banane
        // On obtient les résultats,
        // sur la collectivité A
        // ->
        // collectivité A | niveau 2 | paramètre orange
        // collectivité A | niveau 2 | paramètre pomme
        // sur la collectivité B
        // ->
        // collectivité B | niveau 1 | paramètre orange
        // collectivité B | niveau 1 | paramètre banane
        // collectivité A | niveau 2 | paramètre pomme
        $sql = sprintf(
            'SELECT libelle, valeur FROM %1$som_parametre
                WHERE
                    om_collectivite=%2$s
            UNION
            SELECT libelle, valeur FROM %1$som_parametre
                WHERE
                    om_collectivite=(
                        SELECT om_collectivite FROM %1$som_collectivite
                            WHERE niveau=\'2\'
                    )
                    AND libelle NOT IN (
                        SELECT libelle FROM %1$som_parametre
                            WHERE om_collectivite=%2$s
                    )
            ',
            DB_PREFIXE,
            $om_collectivite_idx
        );
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $collectivite_parameters[$row['libelle']] = $row['valeur'];
        }
        $res->free();
        // Si on se trouve dans le cas d'utilisation n°1
        if ($is_get_collectivite_from_user === true) {
            // Alors on stocke dans l'attribut collectivite le tableau de
            // paramètres pour utilisation depuis la méthode 'getParameter'.
            $this->collectivite = $collectivite_parameters;
        }
        // On retourne le tableau de paramètres.
        return $collectivite_parameters;
    }

    /**
     * Vérifie les paramètres de la configuration 'mail', si les
     * vérifications sont correctes stocke cette configuration dans la
     * propriété 'mail_config' et retourne true. Si les vérifications ne
     * sont pas correctes, retourne false.
     *
     * @return boolean
     */
    function setMailConfig() {
        //
        if (!isset($this->database_config["mail"]) || !isset($this->mail[$this->database_config["mail"]])) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - ".__("Aucune entree dans le fichier de configuration"), DEBUG_MODE);
            //
            $this->mail_config = false;
            //
            return false;
        }
        //
        if (!isset($this->mail[$this->database_config["mail"]]["mail_host"])
            || empty($this->mail[$this->database_config["mail"]]["mail_host"]) ) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - ".__("Un nom d'hote est obligatoire"), DEBUG_MODE);
            //
            $this->mail_config = false;
            //
            return false;
        }
        //
        if (!isset($this->mail[$this->database_config["mail"]]["mail_from"])
            || empty($this->mail[$this->database_config["mail"]]["mail_from"]) ) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - ".__("Une adresse d'expediteur est obligatoire"), DEBUG_MODE);
            //
            $this->mail_config = false;
            //
            return false;
        }
        //
        $this->mail_config = $this->mail[$this->database_config["mail"]];
        //
        return true;
    }

    /**
     * Vérifie les paramètres de la configuration 'directory', si les
     * vérifications sont correctes stocke cette configuration dans la
     * propriété 'directory_config' et retourne true. Si les vérifications ne
     * sont pas correctes, retourne false.
     *
     * @return boolean
     */
    function setDirectoryConfig() {
        //
        if (!isset($this->database_config["directory"]) || !isset($this->directory[$this->database_config["directory"]])) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - ".__("Aucune entree dans le fichier de configuration"), DEBUG_MODE);
            //
            $this->directory_config = false;
            //
            return false;
        }
        //
        $this->directory_config = $this->directory[$this->database_config["directory"]];
        //
        return true;
    }

    // {{{ filestorage

    /**
     * Cette fonction permet de choisir une configuration de stockage des fichiers
     * spécifique.
     *
     * @return bool true si la configuration cherchée est trouvée, autrement false
     */
    function setFilestorageConfig() {
        // Si aucune configuration n'est définie pour le stockage
        if (!isset($this->database_config["filestorage"])
            || !isset($this->filestorage[$this->database_config["filestorage"]])) {
            // Logger
            $this->addToLog(__METHOD__."(): ".__("Aucune entree dans le fichier de configuration"), EXTRA_VERBOSE_MODE);
            // On définit alors la configuration dépréciée pour obtenir le même
            // fonctionnement que celui de l'ancien système de stockage
            $this->filestorage_config = array (
                "storage" => "filesystem",
                "path" => '../var/filestorage/',
                "temporary" => array(
                    "storage" => "filesystem", // l'attribut storage est obligatoire
                    "path" => "../var/tmp/", // le repertoire de stockage
                ),
            );
        } else {
            // On définit alors la configuration paramétrée
            $this->filestorage_config = $this->filestorage[$this->database_config["filestorage"]];

            // Vérification de la clé temporary
            if (!isset($this->filestorage_config["temporary"])) {
                // Ajout d'une clé par defaut
                $this->filestorage_config["temporary"] = array(
                   "storage" => "filesystem", // l'attribut storage est obligatoire
                    "path" => "../var/tmp/", // le repertoire de stockage
                );
            }
        }
        $this->addToLog(__METHOD__."(): this->filestorage_config = ".print_r($this->filestorage_config, true), EXTRA_VERBOSE_MODE);
        //
        return true;
    }

    /**
     * Cette fonction récupère le config de stockage des fichier s'il existe, et
     * s'il existe on crée une instance de la classe filestorage
     */
    function setFilestorage() {
        $this->storage = false;
        if ($this->setFilestorageConfig()) {
            $this->storage = new filestorage($this->filestorage_config);
        }
    }

    // }}}

    /**
     * Vérifie les paramètres de la configuration 'database', si les
     * vérifications sont correctes stocke cette configuration dans la
     * propriété 'database_config'. Si les vérifications ne sont pas correctes,
     * arrête le script.
     *
     * @return void
     */
    function setDatabaseConfig() {
        // On recupere la liste des cles du tableau associatif de configuration
        // de la connexion aux bases de donnees
        $database_keys = array_keys($this->database);
        // Si il y a plusieurs cles
        if (count($database_keys) != 0) {
            // On configure la premiere par defaut
            $coll = $database_keys[0];
        } else { // Si il n'y a aucune cle
            // Aucune base n'est configuree dans le fichier de configuration
            // donc on affiche un message d'erreur
            $class = "error";
            $message = __("Erreur de configuration. Contactez votre administrateur.");
            $this->addToMessage($class, $message);
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR: ".__("Aucune entree dans le fichier de configuration"), DEBUG_MODE);
            // On affiche la structure de la page
            $this->setFlag(null);
            $this->display();
            // On arrete le traitement en cours
            die();
        }
        // Si la variable coll (representant la cle de la base sur laquelle
        // nous travaillons) n'est pas en variable SESSION ou est en variable
        // SESSION mais n'existe pas dans les cles du tableau associatif de
        // configuration de la connexion aux bases de donnees
        if (!isset($_SESSION['coll']) or
            (isset($_SESSION['coll']) and
             !isset($this->database[$_SESSION['coll']]))) {
            // On configure la premiere par defaut
            $_SESSION['coll'] = $coll;
        } else {
            // On recupere la cle du tableau associatif de configuration de la
            // connexion aux bases de donnees correspondante a la base de
            // donnees sur laquelle nous travaillons
            $coll = $_SESSION['coll'];
        }
        // On renvoi le tableau de parametres pour la connexion a la base
        $this->database_config = $this->database[$coll];
    }

    // }}}

    // {{{

    /**
     * Mutateur pour la propriété 'flag'.
     *
     * @param null|string Marqueur de la page.
     * @return void
     */
    function setFlag($flag = null) {
        $this->flag = $flag;
    }

    /**
     * Mutateur pour la propriété 'title'.
     *
     * @param null|string Titre de la page.
     * @return void
     */
    function setTitle($title = null) {
        $this->title = $title;
    }

    /**
     * Mutateur pour la propriété 'right'.
     *
     * @param null|string|array Permissions nécessaires pour accéder à la page.
     * @return void
     */
    function setRight($right = null) {
        $this->right = $right;
    }

    /**
     * Mutateur pour la propriété 'description'.
     *
     * @param string Description de la page.
     * @return void
     */
    function setDescription($description = "") {
        $this->description = $description;
    }

    // }}}

    // {{{ database

    /**
     * Cette méthode permet de se connecter à la base de données
     * @return void
     */
    function connectDatabase() {
        // On inclus la classe d'abstraction de base de donnees
        require_once PATH_OPENMAIRIE."om_database.class.php";
        // On recupere le tableau de parametres pour la connexion a la base
        $this->setDatabaseConfig();
        // On fixe les options
        $options = array(
            'debug' => 2,
            'portability' => DB_PORTABILITY_ALL,
        );
        // Instanciation de l'objet connexion a la base de donnees
        $db = database::connect($this->database_config, $options);
        // Logger
        $this->addToLog(__METHOD__."(): ".__("Tentative de connexion au SGBD"), EXTRA_VERBOSE_MODE);
        // Traitement particulier de l'erreur en cas d'erreur de connexion a la
        // base de donnees
        if ($this->isDatabaseError($db, true)) {
            // Deconnexion de l'utilisateur
            $this->logout();
            // On affiche la page a l'ecran
            $this->setFlag(null);
            // On affiche un message d'erreur convivial pour l'utilisateur
            $class = "error";
            $message = __("Erreur de base de donnees. Contactez votre administrateur.");
            $this->addToMessage($class, $message);
            // On affiche la page
            if (!defined('REST_REQUEST')) {
                $this->display();
            }
            // On arrete le script
            die();
        } else {
            // On affecte la resource a l'attribut de la classe du meme nom
            $this->db = $db;
            // Logger
            $this->addToLog(__METHOD__."(): Connexion [".$this->database_config["phptype"]."] '".$this->database_config['database']."' OK", EXTRA_VERBOSE_MODE);

            // Compatibilite anterieure (deprecated)
            $this->phptype = $this->database_config["phptype"];
            $this->formatdate = $this->database_config["formatdate"];
            $this->schema = $this->database_config["schema"];

            // Definition des constantes pour l'acces aux informations de la base
            // donnees facilement.
            $temp = "";
            if ($this->database_config["schema"] != "") {
                $temp = $this->database_config["schema"].".";
            }
            $temp = $temp.$this->database_config["prefixe"];
            (defined("DB_PREFIXE") ? "" : define("DB_PREFIXE", $temp));
            if (defined("FORMATDATE") !== true) {
                /**
                 * @deprecated
                 * @see OM_DB_FORMATDATE
                 */
                define("FORMATDATE", $this->database_config["formatdate"]);
            }

            // Definition des constantes pour l'acces aux informations de la base
            // donnees facilement.
            (defined("OM_DB_FORMATDATE") ? "" : define("OM_DB_FORMATDATE", $this->database_config["formatdate"]));
            (defined("OM_DB_PHPTYPE") ? "" : define("OM_DB_PHPTYPE", $this->database_config["phptype"]));
            (defined("OM_DB_DATABASE") ? "" : define("OM_DB_DATABASE", $this->database_config["database"]));
            (defined("OM_DB_SCHEMA") ? "" : define("OM_DB_SCHEMA", $this->database_config["schema"]));
            (defined("OM_DB_TABLE_PREFIX") ? "" : define("OM_DB_TABLE_PREFIX", $this->database_config["prefixe"]));
        }
    }

    /**
     * Déconnecte la base de données.
     *
     * @return void
     */
    function disconnectDatabase() {
        //
        if ($this->db != null and !$this->isDatabaseError($this->db, true)) {
            $result = $this->db->disconnect();
            // Debug
            $this->addToLog(__METHOD__."(): ".__("Deconnexion")." ".($result == true ? __("OK") : __("ECHOUEE")), EXTRA_VERBOSE_MODE);
        } else {
            // Debug
            $this->addToLog(__METHOD__."(): ".__("Aucune base de donnees a deconnecter"), EXTRA_VERBOSE_MODE);
        }
    }

    /**
     * Vérifie si une erreur de base de données s'est produite.
     *
     * Cette méthode permet de vérifier si une erreur de base de données est
     * survenue sur la ressource passée en paramètre. Si c'est le cas :
     * - soit on retourne true (si le marqueur de retour est passée),
     * - soit on affiche un message à l'utilisateur et on arrête le script.
     * Si il n'y a pas d'erreur de base de données la méthode retourne false.
     *
     * @param null|resource $dbobj Ressource de base de données sur laquelle vérifier l'erreur.
     * @param boolean $return Marqueur indiquant un retour booléen ou non.
     *
     * @return void|boolean
     */
    function isDatabaseError($dbobj = null, $return = false) {
        //
        if (database::isError($dbobj, $return)) {
            if ($return == true) {
                //
                return true;
            }
            //
            $class = "error";
            $message = __("Erreur de base de donnees. Contactez votre administrateur.");
            $this->addToMessage($class, $message);
            // Logger
            $this->addToLog(__METHOD__."(): ".$dbobj->getDebugInfo(), DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ".$dbobj->getMessage(), DEBUG_MODE);
            //
            $this->setFlag(null);
            if (!defined('REST_REQUEST')) {
                $this->display();
                //
                die();
            }
        }
        //
        return false;
    }

    // }}}

    // {{{ login & logout

    /**
     * Déconnecte l'utilisateur authentifié.
     *
     * @return void
     */
    function logout() {
        //
        if ($this->authenticated == true) {
            //
            $coll = $_SESSION['coll'];
            session_unset();
            $_SESSION['coll'] = $coll;
            $this->authenticated = false;
            //
            $class = "ok";
            $message = __("Votre session est maintenant terminee.");
            $this->addToMessage($class, $message);
        }
    }

    /**
     * Modifie le message d'erreur affiche après un échec d'authentification.
     *
     * @param string $message Message à afficher
     * @return void
     * @access public
     */
    public function setAuthenticationMessage($message) {
        $this->authentication_message = $message;
    }

    /**
     * Initialisation de la connexion au serveur LDAP.
     *
     * Se connecte à l'annuaire et essaye de s'authentifier. Retourne true en
     * cas de succès ou false en cas d'erreur.
     *
     * @param string $login Identifiant.
     * @param string $password Mot de passe.
     *
     * @return boolean
     */
    public function connectDirectory($login = "", $password = "") {
        // Logger
        $this->addToLog(__METHOD__."(): start", EXTRA_VERBOSE_MODE);
        // On recupere le tableau de parametres pour la connexion a la base
        $this->setDirectoryConfig();
        // Instanciation de l'objet connexion a l'annuaire
        $this->dt = ldap_connect(
            $this->directory_config["ldap_server"],
            $this->directory_config["ldap_server_port"]
        );
        // Debug
        $this->addToLog(__METHOD__."(): ldap_connect(".$this->directory_config["ldap_server"].",".$this->directory_config["ldap_server_port"].")", EXTRA_VERBOSE_MODE);
        //
        ldap_set_option($this->dt, LDAP_OPT_PROTOCOL_VERSION, 3);
        //
        @$ldap_connect_user =& ldap_bind($this->dt, $login, $password);
        // Debug
        $this->addToLog(__METHOD__."(): ldap_bind(".$this->dt.",".$login.", ***)", EXTRA_VERBOSE_MODE);
        //
        if ($ldap_connect_user != true) {
            //
            $error = ldap_error($this->dt);
            //
            if ($error == "Invalid credentials") {
                $this->authentication_message = __("Votre identifiant ou votre mot de passe est incorrect.");
                $error_log_mode = VERBOSE_MODE;
            } else {
                $this->authentication_message = __("L'application n'est pas en mesure de vous identifier pour l'instant. Contactez votre administrateur.");
                $error_log_mode = DEBUG_MODE;
            }
            // Debug
            $this->addToLog(__METHOD__."(): ERR", $error_log_mode);
            $this->addToLog(__METHOD__."(): ERR: ".__("Erreur de l'annuaire")." - ".$error, $error_log_mode);
        }
        // Logger
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
        //
        return $ldap_connect_user;
    }

    /**
     * Deconnexion avec le serveur LDAP
     *
     * @return bool Etat du succes de la deconnexion
     * @access public
     */
    public function disconnectDirectory() {
        // Debug
        $this->addToLog(__METHOD__."()", EXTRA_VERBOSE_MODE);
        //
        return ldap_unbind($this->dt);
    }

    /**
     * Renvoie la liste des utilisateurs de l'annuaire LDAP à ajouter, et la
     * la liste des utilisateurs de la base de données à supprimer.
     *
     * @return array tabeau retourne un tableau associatif contenant les utilisateurs
     * à ajouter (clef 'userToAdd') et les utilisateurs à supprimer (clef 'userToDelete')
     * @access public
     */
    function initSynchronization() {
        // Logger
        $this->addToLog(__METHOD__."(): start", VERBOSE_MODE);
        // Si la configuration de l'annuaire n'est pas correcte alors on
        // retourne false
        if ($this->isDirectoryAvailable() != true) {
            //
            $class = "error";
            $message = __("Erreur de configuration. Contactez votre administrateur.");
            $this->displayMessage($class, $message);
            // On retourne false
            return false;
        }
        // Logger
        $this->addToLog(__METHOD__."(): \$this->isDirectoryAvailable() == true", EXTRA_VERBOSE_MODE);
        // Authentification de l'administrateur du LDAP
        $auth = false;
        $auth = $this->connectDirectory($this->directory_config["ldap_admin_login"],
                                        $this->directory_config["ldap_admin_passwd"]);
        //
        if ($auth == false) {
            //
            $class = "error";
            $message = __("Mauvais parametres : l'authentification a l'annuaire n'est pas possible.");
            $this->displayMessage($class, $message);
            $this->addToLog(__METHOD__."(): ".$message, DEBUG_MODE);
            //
            return null;
        }
        //
        if ($auth) {
            // Logger
            $this->addToLog(__METHOD__."(): Authentification OK (\$auth == ".($auth==true?"true":"false").")", EXTRA_VERBOSE_MODE);
            // Logger
            $this->addToLog(__METHOD__."(): start ldap_search()", VERBOSE_MODE);
            // recheche des utilisateurs de l'annuaire
            $ldapResults = null;
            $ldapResults = ldap_search($this->dt,
                                        $this->directory_config['ldap_base_users'],
                                        $this->directory_config['ldap_user_filter'],
                                        array("*"));
            // Logger
            $this->addToLog(__METHOD__."(): ldap_search(".$this->dt.",
                                        \"".$this->directory_config['ldap_base_users']."\",
                                        \"".$this->directory_config['ldap_user_filter']."\",
                                        array(\"*\"))", EXTRA_VERBOSE_MODE);
            // Logger
            $this->addToLog(__METHOD__."(): end ldap_search()", VERBOSE_MODE);
            //
            if (!$ldapResults) {
                //
                $class = "error";
                $message = __("Impossible de poursuivre la recherche des utilisateurs. ".
                             "La methode de recherche renvoie le message: ");
                $message .= ldap_error($this->dt);
                $message .= ".";
                $this->displayMessage($class, $message);
                //return false;
            }
            // récupération des utilisateurs de l'annuaire
            $ldapEntries = null;
            $ldapEntries = ldap_get_entries($this->dt, $ldapResults);
            // Logger
            $this->addToLog(__METHOD__."(): \$ldapEntries = ".print_r($ldapEntries, true).";", EXTRA_VERBOSE_MODE);
            // récupération des utilisateurs de la base de données
            $sql = "SELECT * FROM ".DB_PREFIXE.$this->table_om_utilisateur." WHERE UPPER(".$this->table_om_utilisateur_field_om_type.") = 'LDAP';";
            $sqlRes = $this->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            //
            $this->isDatabaseError($sqlRes);
            $databaseEntries = array();
            while ($row =& $sqlRes->fetchrow(DB_FETCHMODE_ASSOC)) {
                array_push($databaseEntries, $row);
            }
            // tableau des utilisateurs se trouvant dans l'annuaire et non en base
            $userToAdd = array();
            // tableau des utilisateurs se trouvant dans la base et non en annuaire
            $userToDelete = $databaseEntries;
            // tableau des utilisateurs se trouvant dans la base et l'annuaire
            $userToUpdate = array();

            $nbrDatabaseEntries = count($databaseEntries);
            $matched = false;
            // pour chaque utilisateur de l'annuaire on recherche s'il
            // existe dans la base un utilisateur ayant le même login
            for ($i=0; $i<$ldapEntries['count']; $i++) {
                for ($j=0; $j<$nbrDatabaseEntries; $j++) {
                    if ($ldapEntries[$i][$this->directory_config["ldap_login_attrib"]][0] == $databaseEntries[$j]['login']) {
                        unset($userToDelete[$j]);
                        $matched = true;
                    }
                }
                // si l'utilisateur de l'annuaire n'est pas dans la base, on
                // l'ajoute a la liste des utilisateurs a ajouter
                if ($matched == false) {
                    array_push($userToAdd, $ldapEntries[$i]);
                // si l'utilisateur de l'annuaire est dans la base, on l'ajoute
                // a la liste des utilisateurs a mettre a jour
                } else {
                    array_push($userToUpdate, $ldapEntries[$i]);
                }
                $matched = false;
            }
            // Logger
            $this->addToLog(__METHOD__."(): end", VERBOSE_MODE);
            return array(
                "userToAdd" => $userToAdd,
                "userToDelete" => $userToDelete,
                "userToUpdate" => $userToUpdate,
            );
        }
    }

    /**
     * Retourne les données de l'utilisateur à insérer en base de données.
     *
     * Les données sont composées à partir des informations de l'annuaire et
     * des données de l'application. Les données retournées sont formatées pour
     * une requête 'insert' en base de données dans la table des utilisateurs.
     *
     * @param array $user Informations de l'utilisateur.
     *
     * @return array
     */
    function getValFUserToAdd($user) {
        //
        $id = $this->db->nextId(DB_PREFIXE.$this->table_om_utilisateur);
        //
        $login = $user[$this->directory_config['ldap_login_attrib']][0];
        //
        if (isset($this->directory_config['default_om_profil'])) {
            $default_profile = $this->directory_config['default_om_profil'];
        } else {
            $default_profile = $this->default_om_profil;
        }
        //
        $valF = array(
            $this->table_om_utilisateur_field_id => $id,
            $this->table_om_utilisateur_field_login => $login,
            $this->table_om_utilisateur_field_password => md5($login),
            $this->table_om_utilisateur_field_om_profil => $default_profile,
            $this->table_om_utilisateur_field_om_collectivite => $_SESSION['collectivite'],
            $this->table_om_utilisateur_field_om_type => "ldap",
        );
        //
        if (isset($this->directory_config['ldap_more_attrib'])) {
            foreach ($this->directory_config['ldap_more_attrib'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $value1) {
                        if (isset($user[$value1][0])) {
                            $valF[$key] = $user[$value1][0];
                            break;
                        }
                    }
                } else {
                    if (isset($user[$value][0])) {
                        $valF[$key] = $user[$value][0];
                    }
                }
            }
        }
        //
        if (!isset($valF[$this->table_om_utilisateur_field_nom])) {
            $valF[$this->table_om_utilisateur_field_nom] = $login;
        }
        //
        if (!isset($valF[$this->table_om_utilisateur_field_email])) {
            $valF[$this->table_om_utilisateur_field_email] = "";
        }
        //
        return $valF;
    }

    /**
     * Retourne les données de l'utilisateur à insérer en base de données.
     *
     * Les données sont composées à partir des informations de l'annuaire et
     * des données de l'application. Les données retournées sont formatées pour
     * une requête 'update' en base de données dans la table des utilisateurs.
     *
     * @param array $user Informations de l'utilisateur.
     *
     * @return array
     */
    function getValFUserToUpdate($user) {
        //
        $valF = $this->retrieveUserInfos($user[$this->directory_config['ldap_login_attrib']][0]);

        /* Suppression des valeurs de la collectivite retournees par la
           methode retrieveUserProfile. Ces donnees n'appartiennent pas a
           la table utilisateur et declencheront une erreur de base de donnees
           lors de l'appel a autoExecute avec le mode DB_AUTOQUERY_UPDATE */

        if (isset($valF['libelle'])) {
            unset($valF['libelle']);
        }

        if (isset($valF['niveau'])) {
            unset($valF['niveau']);
        }

        if (isset($this->directory_config['ldap_more_attrib'])) {
            foreach ($this->directory_config['ldap_more_attrib'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $value1) {
                        if (isset($user[$value1][0])) {
                            $valF[$key] = $user[$value1][0];
                            break;
                        }
                    }
                } else {
                    if (isset($user[$value][0])) {
                        $valF[$key] = $user[$value][0];
                    }
                }
            }
        }
        //
        return $valF;
    }

    /**
     * Met à jour la table 'utilisateur' avec les opérations de synchronisation
     * calculées auparavant.
     *
     * @param array $users Liste des opérations de synchronisation à mener.
     *
     * @return boolean
     */
    public function synchronizeUsers($users) {
        // {{{ AJOUT DES UTILISATEURS
        //
        $attribError = false;
        //
        if (is_array($users) and array_key_exists('userToAdd', $users)) {
            //
            foreach ($users['userToAdd'] as $user) {
                if (!array_key_exists($this->directory_config['ldap_login_attrib'], $user)) {
                    $attribError = true;
                    continue;
                }
                $res = null;
                //
                $valF = $this->getValFUserToAdd($user);
                //
                $res = $this->db->autoExecute(DB_PREFIXE.$this->table_om_utilisateur, $valF, DB_AUTOQUERY_INSERT);
                // Logger
                $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table_om_utilisateur."\", ".print_r($valF, true).", DB_AUTOQUERY_INSERT)", VERBOSE_MODE);
                //
                if ($this->isDatabaseError($res, true)) {
                    //
                    $class = "error";
                    $message = __("Erreur de base de donnees. Contactez votre administrateur.");
                    $this->displayMessage($class, $message);
                    //
                    return false;
                }
            }
        }
        // }}}
        // {{{ SUPPRESSION DES UTILISATEURS
        //
        if (is_array($users) and array_key_exists('userToDelete', $users)) {
            //
            foreach ($users['userToDelete'] as $user) {
                // Instanciation de la classe om_utilisateur
                $om_utilisateur = $this->get_inst__om_dbform(array(
                    "obj" => "om_utilisateur",
                    "idx" => $user[$this->table_om_utilisateur_field_id],
                ));
                $value_om_utilisateur = array(
                        $this->table_om_utilisateur_field_id => $user[$this->table_om_utilisateur_field_id],
                    );
                // Supprime l'enregistrement
                $om_utilisateur->supprimer($value_om_utilisateur, $this->db, DEBUG);
            }
        }
        // }}}
        // {{{ MISE A JOUR DES UTILISATEURS
        //
        if (is_array($users) and array_key_exists('userToUpdate', $users)) {
            foreach ($users['userToUpdate'] as $user) {
                $user_datas = $this->getValFUserToUpdate($user);
                $user_login = $user_datas[$this->table_om_utilisateur_field_login];
                unset($user_datas[$this->table_om_utilisateur_field_id]);
                unset($user_datas[$this->table_om_utilisateur_field_login]);

                $res = $this->db->autoExecute(
                                    DB_PREFIXE.$this->table_om_utilisateur,
                                    $user_datas, DB_AUTOQUERY_UPDATE,
                                    $this->table_om_utilisateur_field_login."='".$user_login."'");
                // Logger
                $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table_om_utilisateur."\", ".print_r($user_datas, true).", DB_AUTOQUERY_UPDATE, \"".$this->table_om_utilisateur_field_login."='".$user_login."'\")", VERBOSE_MODE);
                //
                if ($this->isDatabaseError($res, true)) {
                    //
                    $class = "error";
                    $message = __("Erreur de base de donnees. Contactez votre administrateur.");
                    $this->displayMessage($class, $message);
                    //
                    return false;
                }
            }
        }
        // }}}

        if ($attribError) {
            $class = "error";
            $message = __("Certains enregistrements provenant du LDAP ".
                         "ne possedent pas l'attribut ".
                         $this->directory_config['ldap_login_attrib'].". ".
                         "Ils ne peuvent donc pas etre synchronises");
            $this->displayMessage($class, $message);
        }
        //
        $class = "ok";
        $message = __("La synchronisation des utilisateurs est terminee.");
        $this->displayMessage($class, $message);
        //
        return true;
    }

    /**
     * Cette methode permet verifier si la fonctionnalite annuaire est
     * disponible ou non. Si le support n'est pas active sur le serveur alors
     * les fonctions utilisees ne seront pas disponibles.
     *
     * @return boolean
     */
    function isDirectoryAvailable() {
        //
        if (!function_exists("ldap_connect")) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR: ".__("Les fonctions ldap ne sont pas disponibles sur cette installation."), DEBUG_MODE);
            //
            return false;
        }
        //
        if ($this->setDirectoryConfig() == false) {
            //
            return false;
        }
        //
        return true;
    }

    /**
     * Met à jour le mot de passe d'un utilisateur dans la table 'utilisateur'.
     *
     * @param string $login Identifiant.
     * @param string $password Mot de passe.
     *
     * @return void
     */
    function changeDatabaseUserPassword($login, $password) {
        //
        $valF[$this->table_om_utilisateur_field_password] = md5($password);
        $cle = $this->table_om_utilisateur_field_login."='".$login."'";
        // Exécution de la requête
        $res = $this->db->autoExecute(DB_PREFIXE.$this->table_om_utilisateur, $valF, DB_AUTOQUERY_UPDATE, $cle);
        // Logger
        $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table_om_utilisateur."\", ".print_r($valF, true).", DB_AUTOQUERY_UPDATE, \"".$cle."\")", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->isDatabaseError($res);
    }

    /**
     * Récupération des informations en base de données de l'utilisateur
     *
     * @param string $login Identifiant de l'utilisateur
     *
     * @return array Informations de l'utilisateur
     */
    public function retrieveUserProfile($login) {
        //
        $user_infos = array();
        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur;
        $sql .= " left join ".DB_PREFIXE.$this->table_om_collectivite;
        $sql .= " on ".$this->table_om_collectivite.".".$this->table_om_collectivite_field_id." = ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_om_collectivite;
        $sql .= " left join ".DB_PREFIXE.$this->table_om_profil;
        $sql .= " on ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_om_profil." = ".$this->table_om_profil.".".$this->table_om_profil_field_id;
        $sql .= " WHERE ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_login." = '".$login."';";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        $this->isDatabaseError($res);
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $user_infos = $row;
        }
        $res->free();
        return $user_infos;
    }

    /**
     * Récupération des informations en base de données de l'utilisateur
     * uniquement des données de la table om_utilisateur
     *
     * @param string $login Identifiant de l'utilisateur
     * @return array Informations de l'utilisateur
     * @access public
     */
    public function retrieveUserInfos($login) {
        //
        $user_infos = array();
        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur;
        $sql .= " WHERE ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_login." = '".$login."';";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        $this->isDatabaseError($res);
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $user_infos = $row;
        }
        $res->free();
        return $user_infos;
    }

    /**
     * Affiche le formulaire de login.
     *
     * @return void
     */
    function displayLoginForm() {
        // Initialisation des paramètres
        $params = array(
            "came_from" => array(
                "method" => array("post", "get", ),
                "default_value" => "",
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        // Cinq balises div uniquement pour permettre un style css particulier
        echo "\n<div id=\"loginform\" class=\"ui-widget\">";
        echo "<div id=\"loginform_t\">";
        echo "<div id=\"loginform_l\">";
        echo "<div id=\"loginform_r\">";
        echo "<div id=\"loginform_b\">\n";

        //
        echo "<div id=\"formulaire\">\n\n";
        //
        echo "<ul>\n";
        echo "\t<li><a href=\"#tabs-1\">".__("Identification")."</a></li>\n";
        echo "</ul>\n";
        //
        echo "\n<div id=\"tabs-1\">\n";

        //
        $this->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_LOGIN,
            "id" => "login_form",
        ));
        //
        $validation = 0;
        $maj = 0;
        $champs = array("came_from", "login", "password");
        if (count($this->database) > 1) {
            array_push($champs, "coll");
        }
        //
        $form = $this->get_inst__om_formulaire(array(
            "validation" => $validation,
            "maj" => $maj,
            "champs" => $champs,
        ));
        //
        $form->setType("came_from", "hidden");
        $form->setTaille("came_from", 20);
        $form->setMax("came_from", 20);
        $form->setVal("came_from", $came_from);
        //
        $form->setLib("login", __("Identifiant"));
        $form->setType("login", "text");
        $form->setTaille("login", 20);
        $form->setMax("login", 100);
        $form->setVal("login", ($this->config['demo']==true?"demo":""));
        //
        $form->setLib("password", __("Mot de passe"));
        $form->setType("password", "password");
        $form->setTaille("password", 20);
        $form->setMax("password", 100);
        $form->setVal("password", ($this->config['demo']==true?"demo":""));
        //
        if (count($this->database)>1) {
            $form->setLib("coll", __("Base de donnees"));
            $form->setType("coll", "select");
            $contenu = array(
                0 => array(),
                1 => array(),
            );
            foreach ($this->database as $key => $coll) {
                array_push($contenu[0], $key);
                array_push($contenu[1], $coll['title']);
            }
            $form->setSelect("coll", $contenu);
            if (isset($_SESSION['coll'])) {
                $form->setVal("coll", $_SESSION['coll']);
            }
        }
        //
        $form->entete();
        $form->afficher($champs, $validation, false, false);
        $form->enpied();

        //
        $this->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        $this->layout->display__form_input_submit(array(
            "name" => "login.action.connect",
            "value" => __("Se connecter"),
            "class" => "context boutonFormulaireLogin ui-button ui-state ui-corner-all",
        ));
        $this->layout->display__form_controls_container__end();
        $this->layout->display__form_container__end();

        // Ajout du lien de redefinition de mot de passe
        if (isset($this->config['password_reset']) and $this->config['password_reset'] == true) {
            echo "\t<p class=\"link-password-reset\">\n";
            echo "\t\t<a href=\"".OM_ROUTE_PASSWORD_RESET."\" title=\"".__("Redefinition du mot de passe")."\">";
            echo "<span class=\"om-icon ui-icon ui-icon-info\"><!-- --></span>";
            echo __("Mot de passe oublie ?");
            echo "</a>\n";
            echo "\t</p>\n";
        }

        //
        echo "</div>";
        echo "</div>";

        //
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>\n";
    }

    // }}}

    // {{{ AUTHENTIFICATION ET GESTION DES UTILISATEURS

    /**
     * Cette methode permet d'effectuer toutes les verifications et les
     * traitements necessaires pour la gestion de l'authentification des
     * utilisateurs a l'application.
     *
     * @return void
     */
    function login() {
        // Initialisation des paramètres
        $params = array(
            "came_from" => array(
                "method" => array("post", ),
                "default_value" => "",
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        // Debug
        $this->addToLog(__METHOD__."(): start", EXTRA_VERBOSE_MODE);
        $this->redirectAuthenticatedUsers();
        // Si l'utilisateur ne souhaite pas s'authentifier (le cas se presente
        // si nous sommes sur la page de login et que l'utilisateur n'a pas
        // valider le formulaire) alors on sort de la methode
        if ($this->wantToAuthenticate() != true) {
            // Logger
            $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
            // On retourne null
            return null;
        }
        // Si la valeur du champ coll dans le formulaire de login est definie
        if ($this->get_submitted_post_value("coll") !== null) {
            // On ajoute en variable de session la cle du tableau associatif de
            // configuration de base de donnees a laquelle l'utilisateur
            // souhaite se connecter
            $_SESSION['coll'] = $this->get_submitted_post_value("coll");
            // Debug
            $this->addToLog(__METHOD__."(): \$_SESSION['coll']=\"".$_SESSION['coll']."\"", EXTRA_VERBOSE_MODE);
        }
        // On se connecte a la base de donnees
        $this->connectDatabase();
        // On recupere le login et le mot de passe de l'utilisateur qui
        // demande l'authentification
        $login = $this->getUserLogin();
        $password = $this->getUserPassword();
        // Logger
        $this->addToLog(__METHOD__."(): credentials \"".$login."\"/\"***\"", EXTRA_VERBOSE_MODE);
        // On procede a l'authentification
        $authenticated = $this->processAuthentication($login, $password);
        //
        if ($authenticated) {
            $user_infos = $this->retrieveUserProfile($login);
        }
        //
        if (isset($user_infos[$this->table_om_utilisateur_field_om_profil])) {
            // Identification OK
            $_SESSION["profil"] = $user_infos[$this->table_om_profil_field_hierarchie];
            $_SESSION["login"] = $user_infos[$this->table_om_utilisateur_field_login];
            $_SESSION["collectivite"] = $user_infos[$this->table_om_utilisateur_field_om_collectivite];
            $_SESSION["niveau"] = $user_infos[$this->table_om_collectivite_field_niveau];
            $_SESSION["justlogin"] = true;
            //
            $this->triggerAfterLogin($user_infos);
            //
            $class = "ok";
            $message = __("Votre session est maintenant ouverte.");
            $this->addToMessage($class, $message);
            //
            $this->disconnectDatabase();
            // Redirection vers le came_from si existant
            if ($came_from != "") {
                //
                header("Location: ".urldecode($came_from));
                exit();
            } else {
                // Sinon on redirige vers le tableau de bord
                $this->goToDashboard ();
            }
        } else {
            //
            $class = "error";
            $this->addToMessage($class, $this->authentication_message);
            $this->came_from = $came_from;
        }
        // Logger
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
    }

    /**
     * Retourne l'etat de la demande d'authentification
     *
     * Cette methode pourra etre surchargee pour permettre d'utiliser un
     * systeme central d'authentification
     *
     * @return bool Etat de la demande d'authentification
     * @access public
     */
    public function wantToAuthenticate() {
        // Si l'utilisateur a valide le formulaire de login alors c'est qu'il
        // souhaite s'authentifier sinon l'authentification n'est pas
        // souhaitee
        return $this->get_submitted_post_value("login_action_connect") !== null;
    }

    /**
     * Retourne l'identifiant de l'utilisateur lors de la demande
     * d'authentification
     *
     * Cette methode pourra etre surchargee pour permettre d'utiliser un
     * systeme central d'authentification
     *
     * @return string Identifiant de l'utilisateur
     * @access public
     */
    public function getUserLogin() {
        return $this->get_submitted_post_value("login");
    }

    /**
     * Retourne le mot de passe de l'utilisateur lors de la demande
     * d'authentification
     *
     * Cette methode pourra etre surchargee pour permettre d'utiliser un
     * systeme central d'authentification
     *
     * @return string Mot de passe de l'utilisateur
     * @access public
     */
    public function getUserPassword() {
        // Si la valeur du champ mot de passe dans le formulaire de login est
        // definie
        if ($this->get_submitted_post_value("password") !== null) {
            // On retourne la valeur du champ mot de passe
            return $_POST['password'];
        }
        // Si la valeur du champ mot de passe dans le formulaire de login
        // n'est pas definie alors on retour null
        return null;
    }

    /**
     * Traitement de l'authentification
     *
     * @param string $login Indentifiant de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @access public
     * @return bool Etat de l'authentification de l'utilisateur
     */
    public function processAuthentication($login, $password) {
        // Initialisation de la valeur de retour a false
        $authenticated = false;
        // On recupere le mode d'authenfication de l'utilisateur
        $mode = $this->retrieveUserAuthenticationMode($login);
        // Debug
        $this->addToLog(__METHOD__."(): le mode d'authentification est \"".$mode."\"", EXTRA_VERBOSE_MODE);
        // Si mode base de donnees
        if (strtolower($mode) == "db") {
            // On procede a l'authentification depuis la base de donnees
            $authenticated = $this->processDatabaseAuthentication($login, $password);
        } elseif (strtolower($mode) == "ldap") { // Si mode annuaire
            //
            if ($password == "") {
                $authenticated = false;
                $this->authentication_message = __("Votre identifiant ou votre mot de passe est incorrect.");
            } else {
                // On procede a l'authentification depuis l'annuaire
                $authenticated = $this->processDirectoryAuthentication($login, $password);
            }
        }
        // On retourne la valeur
        return $authenticated;
    }

    /**
     * Recuperation du mode d'authentification de l'utilisateur
     *
     * @param string $login Identifiant de l'utilisateur
     * @return string Mode d'authentification de l'utilisateur
     * @access public
     */
    public function retrieveUserAuthenticationMode($login) {
        // Initialisation de la valeur de retour a db
        $mode = "db";
        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur." ";
        $sql .= " WHERE ".$this->table_om_utilisateur_field_login."='".$login."' ";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        if ($this->isDatabaseError($res, true) == true) {
            //
            $mode = false;
            $this->authentication_message = __("Erreur de base de donnees. Contactez votre administrateur.");
        } else {
            //
            if ($res->numRows() == 1) {
                //
                $row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
                //
                if (isset($row[$this->table_om_utilisateur_field_om_type]) && $row[$this->table_om_utilisateur_field_om_type] != "") {
                    $mode = $row[$this->table_om_utilisateur_field_om_type];
                }
            } elseif ($res->numRows() < 1) {
                $mode = false;
                $this->authentication_message = __("Votre identifiant ou votre mot de passe est incorrect.");
            }
            //
            $res->free();
        }
        // On retourne la valeur
        return $mode;
    }

    /**
     * Traitement de l'authentification pour un utilisateur en base de donnees
     *
     * @param string $login Identifiant de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return bool Etat de l'authentification de l'utilisateur
     * @access public
     */
    public function processDatabaseAuthentication($login, $password) {
        // Initialisation de la valeur de retour a false
        $authenticated = false;
        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur." ";
        $sql .= " WHERE ".$this->table_om_utilisateur_field_login."='".$login."' ";
        $sql .= " AND ".$this->table_om_utilisateur_field_password."='".md5($password)."' ";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        $this->isDatabaseError($res);
        //
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            //
            $authenticated = true;
        }
        //
        $res->free();
        //
        if ($authenticated == false) {
            $this->authentication_message = __("Votre identifiant ou votre mot de passe est incorrect.");
        }
        // On retourne la valeur
        return $authenticated;
    }

    /**
     * Traitement de l'authentification pour un utilisateur en annuaire
     *
     * @param string $login Identifiant de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return bool Etat de l'authentification de l'utilisateur
     * @access public
     */
    public function processDirectoryAuthentication($login, $password) {
        // Si la configuration de l'annuaire n'est pas correcte alors on
        // retourne false
        if ($this->isDirectoryAvailable() != true) {
            //
            $this->authentication_message = __("Erreur de configuration. Contactez votre administrateur.");
            // On retourne false
            return false;
        }
        if (isset($this->directory_config["ldap_login_dn"]) === true) {
            // Authentification de l'administrateur du LDAP
            $auth = false;
            $auth = $this->connectDirectory(
                $this->directory_config["ldap_admin_login"],
                $this->directory_config["ldap_admin_passwd"]
            );
            //
            if ($auth == false) {
                //
                $class = "error";
                $message = __("Mauvais parametres : l'authentification a l'annuaire n'est pas possible.");
                $this->addToLog(__METHOD__."(): ".$message, DEBUG_MODE);
                //
                $this->authentication_message = __("Erreur de configuration. Contactez votre administrateur.");
                return false;
            }
            //
            // Logger
            $this->addToLog(__METHOD__."(): Authentification OK (\$auth == ".($auth==true?"true":"false").")", EXTRA_VERBOSE_MODE);
            // Logger
            $this->addToLog(__METHOD__."(): start ldap_search()", VERBOSE_MODE);
            // recheche des utilisateurs de l'annuaire
            $ldapResults = false;
            $ldapResults = ldap_search(
                $this->dt,
                $this->directory_config['ldap_base_users'],
                '('.$this->directory_config["ldap_login_attrib"].'='.$login.')',
                array("*")
            );
            // Logger
            $this->addToLog(__METHOD__."(): ldap_search(".$this->dt.",
                                        \"".$this->directory_config['ldap_base_users']."\",
                                        \"(".$this->directory_config["ldap_login_attrib"]."=".$login.")\",
                                        array(\"*\"))", EXTRA_VERBOSE_MODE);
            // Logger
            $this->addToLog(__METHOD__."(): end ldap_search()", VERBOSE_MODE);
            //
            if ($ldapResults === false) {
                //
                $class = "error";
                $message = __("Impossible de poursuivre la recherche des utilisateurs. ".
                             "La methode de recherche renvoie le message: ");
                $message .= ldap_error($this->dt);
                $message .= ".";
                $this->addToLog(__METHOD__."(): ".$message, DEBUG_MODE);
                //
                $this->authentication_message = __("Erreur de configuration. Contactez votre administrateur.");
                return false;
            }
            // récupération des utilisateurs de l'annuaire
            $ldapEntries = null;
            $ldapEntries = ldap_get_entries($this->dt, $ldapResults);
            $this->disconnectDirectory();
            $login = $ldapEntries[0][$this->directory_config["ldap_login_dn"]];
        } else {
            $login = $this->directory_config["ldap_login_attrib"]."=".$login.",".$this->directory_config["ldap_base_users"];
        }
        // Tentative de connexion a l'annuaire
        $ldap_connect_user = $this->connectDirectory($login, $password);
        // Deconnexion de l'annuaire
        $this->disconnectDirectory();
        //
        return $ldap_connect_user;
    }

    /**
     * Redirige les utilisateurs authentifiés vers le tableau de bord.
     *
     * @param void
     * @return null
     * @access private
     */
    private function redirectAuthenticatedUsers() {
        // Si l'utilisateur est deja authentifie on le redirige sur le tableau
        // de bord de l'application et on sort de la methode
        if ($this->authenticated != false) {
            // Appel de la methode de redirection vers le tableau de bord
            $this->goToDashboard();
            // On retourne null
            return null;
        }
        return null;
    }

    // }}}

    /**
     * Compose la liste des liens à afficher dans la section 'actions'.
     *
     * Cette méthode retourne la liste des liens disponibles pour l'utilisateur
     * connecté dans le contexte actuel.
     *
     * @return array
     */
    function getActionsToDisplay() {
        return $this->handle_links_to_display("actions");
    }

    /**
     * Compose la liste des liens à afficher dans la section 'shortlinks'.
     *
     * Cette méthode retourne la liste des liens disponibles pour l'utilisateur
     * connecté dans le contexte actuel.
     *
     * @return array
     */
    function getShortlinksToDisplay() {
        return $this->handle_links_to_display("shortlinks");
    }

    /**
     * Compose la liste des liens à afficher dans la section 'footer'.
     *
     * Cette méthode retourne la liste des liens disponibles pour l'utilisateur
     * connecté dans le contexte actuel.
     *
     * @return array
     */
    function getFooterToDisplay() {
        return $this->handle_links_to_display("footer");
    }

    /**
     * Compose la liste des liens à afficher.
     *
     * Cette méthode retourne la liste des liens disponibles pour l'utilisateur
     * connecté dans le contexte actuel pour la section passée en paramètre.
     *
     * @param string $zone Section dont on souhaite récupérer les liens.
     *
     * @return array
     */
    function handle_links_to_display($zone) {
        $links_to_display = array();
        // Initialisation des trois sections autorisées
        if (in_array($zone, array("footer", "actions", "shortlinks", )) !== true) {
            return $links_to_display;
        }
        // Si l'utilisateur n'est pas authentifié, aucun lien n'est disponible
        if ($this->authenticated == false) {
            return $links_to_display;
        }
        // Vérification de l'existence de la méthode
        $method_name = sprintf("get_config__%s", $zone);
        if (method_exists($this, $method_name) !== true) {
            return $links_to_display;
        }
        // Boucle sur chaque lien pour vérifier sa disponibilité
        foreach ($this->$method_name() as $key => $value) {
            // Gestion des paramètres
            if (isset($value["parameters"])
                && is_array($value["parameters"])) {
                //
                $flag_parameter = false;
                foreach ($value["parameters"] as $parameter_key => $parameter_value) {
                    if ($this->getParameter($parameter_key) != $parameter_value) {
                        $flag_parameter = true;
                        break;
                    }
                }
                if ($flag_parameter == true) {
                    // On passe directement a l'iteration suivante de la boucle
                    continue;
                }
            }
            // Gestion des droits d'acces : si l'utilisateur n'a pas la
            // permission necessaire alors l'entree n'est pas affichee
            if (isset($value['right'])
                and !$this->isAccredited($value['right'], "OR")) {
                // On passe directement a l'iteration suivante de la boucle
                continue;
            }
            //
            $links_to_display[] = $value;
        }
        return $links_to_display;
    }

    /**
     * Cette variable permet de stocker le résultat de la méthode getMenuToDisplay
     * pour éviter d'effectuer le calcul plusieurs fois. Si la variable vaut
     * null alors le calcul n'a jamais été fait.
     * @var null|array
     */
    var $_menu_to_display = null;

    /**
     * Compose le menu à afficher.
     *
     * Cette méthode retourne la composition du menu, c'est-à-dire la liste des
     * rubriques et des entrées de menu disponibles pour l'utilisateur connecté
     * dans le contexte actuel.
     *
     * @return array
     */
    function getMenuToDisplay() {
        // Logger
        $this->addToLog(__METHOD__."(): start", EXTRA_VERBOSE_MODE);
        // Si le menu a déjà était composé
        if (!is_null($this->_menu_to_display)) {
            // On retourne directement le menu calculé précédemment
            $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
            return $this->_menu_to_display;
        }
        // On initialise le tableau avec un tableau vide
        $this->_menu_to_display = array();
        // Si l'utilisateur n'existe pas ou si le fichier de configuration
        // du menu n'existe pas
        $menu = $this->get_config__menu();
        if ($this->authenticated == false
            || count($menu) === 0) {
            // On retourne un menu vide
            $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
            return $this->_menu_to_display;
        }

        if (!function_exists('is_elem_selected')) {
            /**
             * Cette fonction permet d'indiquer si la chaine de caractère
             * passée en paramètre correspond aux critères de sélection du script
             * sur lequel on se trouve.
             * exemples :
             * - "tab.php|users" # tab.php?obj=users&*
             * - "|users" # *?obj=users&*
             * - "script.php|" # script.php?*
             * - "form.php|users[action=0]" # form.php?obj=users&action=0&*
             * - "form.php|users[action=3][idx=12]"# form.php?obj=users&action=3&idx=12&*
             *
             * @param string $elem Chaine de caractères représentant les critères
             *                     de sélection.
             *
             * @return boolean
             * @ignore Fonction dans une méthode, inutile de la présenter dans l'API.
             */
            function is_elem_selected($elem) {
                // separation du nom de fichier et du obj
                $scriptobjarray = explode("|", $elem);
                $cle_script=$scriptobjarray[0];
                $cle_obj=$scriptobjarray[1];
                //
                $scriptAppele = explode("/", $_SERVER["PHP_SELF"]);
                $scriptAppele = $scriptAppele[ count($scriptAppele) - 1 ];
                //
                $cle_script_ok = true;
                if ($cle_script != "" and $cle_script != $scriptAppele) {
                    $cle_script_ok = false;
                    return false;
                }

                //
                $params = explode("[", $cle_obj);
                //
                $paramstocheck = array();
                //
                foreach ($params as $key => $value) {
                    if ($key == 0) {
                        if ($value != "") {
                            $paramstocheck["obj"] = $value;
                        }
                    } else {
                        //
                        $param = str_replace("]", "", $value);
                        $param = explode("=", $param);
                        if (count($param) == 1) {
                            $paramstocheck[$param[0]] = "";
                        } else {
                            $paramstocheck[$param[0]] = $param[1];
                        }
                    }
                }
                //
                if (count($paramstocheck) == 0) {
                    $cle_obj_ok = true;
                } else {
                    //
                    $cle_obj_ok = true;
                    //
                    foreach ($paramstocheck as $key => $value) {
                        //
                        if (isset($_GET[$key]) && $_GET[$key] == $value) {
                            $cle_obj_ok = $cle_obj_ok && true;
                        } else {
                            $cle_obj_ok = false;
                        }
                    }
                }
                return $cle_obj_ok;
            }
        }

        //
        foreach ($menu as $m => $rubrik) {
            // Gestion des paramètres
            if (isset($rubrik["parameters"])
                && is_array($rubrik["parameters"])) {
                //
                $flag_parameter = false;
                //
                foreach ($rubrik["parameters"] as $parameter_key => $parameter_value) {
                    //
                    if ($this->getParameter($parameter_key) != $parameter_value) {
                        //
                        $flag_parameter = true;
                        break;
                    }
                }
                //
                if ($flag_parameter == true) {
                    // On passe directement a l'iteration suivante de la boucle
                    continue;
                }
            }
            // Gestion des droits d'acces : si l'utilisateur n'a pas la
            // permission necessaire alors la rubrique n'est pas affichee
            if (isset($rubrik['right'])
                and !$this->isAccredited($rubrik['right'])) {
                // On passe directement a l'iteration suivante de la boucle
                continue;
            }
            // Initialisation
            $rubrik_to_display = $rubrik;
            $elems_in_rubrik_to_display = array();
            $cpt_links = 0;


            // Test des criteres pour determiner si la rubrique est active
            if (isset($rubrik['open'])) {
                foreach ($rubrik['open'] as $scriptobj) {
                    $is_selected = is_elem_selected($scriptobj);
                    if ($is_selected === true) {
                        $rubrik_to_display["selected"] = "selected";
                        break;
                    }
                }
            }

            // Boucle sur les entrees de menu
            foreach ($rubrik['links'] as $link) {
                // Gestion des paramètres
                if (isset($link["parameters"])
                    && is_array($link["parameters"])) {
                    //
                    $flag_parameter = false;
                    //
                    foreach ($link["parameters"] as $parameter_key => $parameter_value) {
                        //
                        if ($this->getParameter($parameter_key) != $parameter_value) {
                            //
                            $flag_parameter = true;
                            break;
                        }
                    }
                    //
                    if ($flag_parameter == true) {
                        // On passe directement a l'iteration suivante de la boucle
                        continue;
                    }
                }
                // Gestion des droits d'acces : si l'utilisateur n'a pas la
                // permission necessaire alors l'entree n'est pas affichee
                if (isset($link['right'])
                    and !$this->isAccredited($link['right'], "OR")) {
                    // On passe directement a l'iteration suivante de la boucle
                    continue;
                }
                //
                $cpt_links++;

                // Entree de menu
                if (trim($link['title']) != "<hr />" and trim($link['title']) != "<hr/>"
                    and trim($link['title']) != "<hr>") {
                    // MENU OPEN
                    if (isset($link['open'])) {
                        if (gettype($link['open']) == "string") {
                                $link['open']=array($link['open'],);
                        }
                        foreach ($link['open'] as $scriptobj) {
                            $is_selected = is_elem_selected($scriptobj);
                            if ($is_selected === true) {
                                $rubrik_to_display["selected"] = "selected";
                                $link["selected"] = "selected";
                                break;
                            }
                        }
                    }
                }
                $elems_in_rubrik_to_display[] = $link;
            }

            //
            $rubrik_to_display["links"] = $elems_in_rubrik_to_display;
            // Si des liens ont ete affiches dans la rubrique alors on
            // affiche la rubrique
            if ($cpt_links != 0) {
                //
                $this->_menu_to_display[] = $rubrik_to_display;
            }
        }
        //
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
        return $this->_menu_to_display;
    }

    /**
     * Cette méthode permet de renvoyer la valeur d'un paramètre de
     * l'application, on utilise cette méthode car les paramètres peuvent
     * provenir de différentes sources :
     *   - le fichier dyn/var.inc
     *   - le fichier dyn/config.inc.php
     *   - la table om_parametre
     * En regroupant la récupération des paramètres dans une seule méthode :
     *  - on évite les erreurs
     *  - on peut se permettre de gérer des comportements
     * complexes comme : si le paramètre n'est pas disponible pour la
     * collectivité alors on va chercher dans la collectivité de niveau
     * supérieur.
     *  - on est indépendant du stockage de ces paramètres.
     *
     * Si on ne trouve pas de paramètre correspondant alors on retourne null
     *
     * @param null|string $param
     *
     * @return mixed
     */
    function getParameter($param = null) {
        //
        if ($param == null) {
            return null;
        }
        //
        if ($param == "isDirectoryOptionEnabled") {
            if ($this->is_option_directory_enabled() !== true) {
                //
                return false;
            } else {
                //
                return true;
            }
        }
        //
        if (isset($this->config[$param])) {
            //
            return $this->config[$param];
        }
        //
        if (isset($this->collectivite[$param])) {
            //
            return $this->collectivite[$param];
        }
        //
        return null;
    }

    /**
     * Cette méthode permet de renvoyer la valeur soumise par post.
     *
     * Si on ne trouve pas de paramètre correspondant alors on retourne chaîne vide
     *
     * @param string $param clé de la valeur dans le tableau
     *
     * @return null ou la valeur
     */
    function get_submitted_post_value($param = null) {
        //
        if ($param == null) {
            return $this->submitted_post_value;
        }
        //
        if (isset($this->submitted_post_value[$param])) {
            //
            return $this->submitted_post_value[$param];
        }
        //
        return null;
    }

    /**
     * Cette méthode permet de renvoyer la valeur soumise par get.
     *
     * Si on ne trouve pas de paramètre correspondant alors on retourne chaîne vide
     *
     * @param string $param clé de la valeur dans le tableau
     *
     * @return null ou la valeur
     */
    function get_submitted_get_value($param = null) {
        //
        if ($param == null) {
            return $this->submitted_get_value;
        }
        //
        if (isset($this->submitted_get_value[$param])) {
            //
            return $this->submitted_get_value[$param];
        }
        //
        return null;
    }

    /**
     * Méthode de prévention des failles de sécurités en nettoyant les variables
     * passées en paramètre.
     *
     * @param mixed $input valeurs à netoyer
     *
     * @return mixed valeurs nétoyées
     */
    function clean_break($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->clean_break($value);
            }
        } else {
            //remove whitespace...
            $input = trim($input);
            //disable magic quotes...
            if (get_magic_quotes_gpc()) {
                stripslashes($input);
            }
            //prevent sql injection...
            if (!is_numeric($input)) {
                if (isset($this->db)) {
                    $this->db->escapeSimple($input);
                }
            }
            //prevent xss...
            $input = filter_var($input, FILTER_SANITIZE_STRING);
        }
        return $input;
    }

    /**
     * Méthode permettant d'attribuer les valeurs de POST et GET.
     *
     * @return void
     */
    function set_submitted_value() {
        // S'il s'agit d'un GET
        if (isset($_GET) and !empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->submitted_get_value[$key]=$this->clean_break($value);
            }
        }
        // S'il s'agit d'un POST
        if (isset($_POST) and !empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->submitted_post_value[$key]=$this->clean_break($value);
            }
        }
    }

    /**
     * Permet d'empêcher l'accès aux scripts dédiés à la localisation.
     *
     * Cette méthode vérifie si la valeur de l'option de localisation est
     * différente de 'sig_interne' et si c'est le cas d'afficher un message
     * d'erreur puis d'arrêter l'exécution du script.
     * Exemple d'utilisation :
     * <?php
     * require_once "../obj/utils.class.php";
     * $f = new utils("nohtml");
     * $f->handle_if_no_localisation();
     */
    function handle_if_no_localisation() {
        //
        if ($this->is_option_localisation_enabled() !== true) {
            //
            $class = "error";
            $message = __("Cette option n'est pas activee. Contactez votre administrateur.");
            $this->addToMessage($class, $message);
            //
            $this->addToLog(__METHOD__."(): ERR: ".__("L'option de localisation 'sig_interne' n'est pas configuree."), DEBUG_MODE);
            //
            $this->setFlag(null);
            $this->display();
            //
            die();
        }
    }

    /**
     * Indique si l'option 'annuaire' est activée.
     *
     * @return boolean
     */
    function is_option_directory_enabled() {
        //
        if (!isset($this->database_config["directory"])
            || $this->database_config["directory"] == null) {
            //
            return false;
        }
        //
        return true;
    }

    /**
     * Indique si l'option 'localisation' est activée.
     *
     * @return boolean
     */
    function is_option_localisation_enabled() {
        //
        if ($this->getParameter("option_localisation") !== "sig_interne") {
            //
            return false;
        }
        //
        return true;
    }

    // }}}

    // {{{ message

    /**
     * Ajoute un message à la pile des messages à afficher.
     *
     * @param string $class Classe CSS du message.
     * @param string $message Texte du message.
     *
     * @return void
     */
    function addToMessage($class = "", $message = "") {
        array_push($this->message, array("class" => $class, "message" => $message));
    }



    // }}}

    // {{{

    /**
     * Numéro de version de l'application.
     * @var string
     */
    protected $version = null;

    /**
     * Gestion du numéro de version de l'application.
     *
     * L'objectif ici est d'avoir plusieurs niveaux de configuration pour cet élément.
     *
     * @return mixed
     */
    function get_application_version() {
        //
        if ($this->version !== null) {
            return $this->version;
        }
        //
        $version = "-";
        if (file_exists(PATH_OPENMAIRIE."om_version.inc.php")) {
            include PATH_OPENMAIRIE."om_version.inc.php";
        }
        if (file_exists("../VERSION.txt")) {
            $version = file_get_contents("../VERSION.txt");
        }
        if (file_exists("../dyn/version.inc.php")) {
            include "../dyn/version.inc.php";
        }
        $this->version = $version;
        return $this->version;
    }

    /**
     * Gestion du nom de l'application.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $_application_name = "openRefuge";

    /**
     * Gestion du nom de l'application.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_application_name() {
        // On récupère le paramètre depuis la configuration, si la valeur n'est
        // pas nulle cela signifie qu'une configuration instance a été spécifiée
        // soit par config.inc.php soit par om_parametre.
        if ($this->getParameter("application") !== null) {
            return $this->getParameter("application");
        }
        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        return $this->_application_name;
    }

    /**
     * Gestion du nom de la session.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $_session_name = "1bb484de79f96a7d0b00ff463c18fcbf";

    /**
     * Gestion du nom de la session.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_session_name() {
        // On récupère le paramètre depuis la configuration, si la valeur n'est
        // pas nulle cela signifie qu'une configuration instance a été spécifiée
        // soit par config.inc.php soit par om_parametre.
        if ($this->getParameter("session_name") !== null) {
            return $this->getParameter("session_name");
        }
        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        return $this->_session_name;
    }

    /**
     * Gestion du mode de gestion des permissions.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $config__permission_by_hierarchical_profile = true;

    /**
     * Gestion du mode de gestion des permissions.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__permission_by_hierarchical_profile() {
        // On récupère le paramètre depuis la configuration, si la valeur n'est
        // pas nulle cela signifie qu'une configuration instance a été spécifiée
        // soit par config.inc.php soit par om_parametre.
        if ($this->getParameter("permission_by_hierarchical_profile") !== null) {
            return $this->getParameter("permission_by_hierarchical_profile");
        }
        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        return $this->config__permission_by_hierarchical_profile;
    }

    /**
     * Gestion du nombre de colonnes du tableau de bord.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $config__dashboard_nb_column = 3;

    /**
     * Gestion du nombre de colonnes du tableau de bord.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__dashboard_nb_column() {
        // On récupère le paramètre depuis la configuration, si la valeur n'est
        // pas nulle cela signifie qu'une configuration instance a été spécifiée
        // soit par config.inc.php soit par om_parametre.
        if ($this->getParameter("dashboard_nb_column") !== null) {
            return $this->getParameter("dashboard_nb_column");
        }
        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        return $this->config__dashboard_nb_column;
    }

    /**
     * Gestion des liens du menu.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $config__menu = array();

    /**
     * Gestion des liens du menu.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__menu() {
        //
        if (file_exists("../dyn/menu.inc.php")) {
            include "../dyn/menu.inc.php";
            if (isset($menu)
                && is_array($menu) === true) {
                $this->config__menu = $menu;
                return $this->config__menu;
            }
            return array();
        }

        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        $this->set_config__menu();
        return $this->config__menu;
    }

    /**
     * Gestion des liens des actions.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $config__actions = null;

    /**
     * Gestion des liens des actions.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__actions() {
        //
        if (file_exists("../dyn/actions.inc.php")) {
            include "../dyn/actions.inc.php";
            if (isset($actions)
                && is_array($actions) === true) {
                $this->config__actions = $actions;
                return $this->config__actions;
            }
            return array();
        }

        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        $this->set_config__actions();
        return $this->config__actions;
    }

    /**
     * Gestion des liens shortlinks.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $config__shortlinks = null;

    /**
     * Gestion des liens shortlinks.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__shortlinks() {
        //
        if (file_exists("../dyn/shortlinks.inc.php")) {
            include "../dyn/shortlinks.inc.php";
            if (isset($shortlinks)
                && is_array($shortlinks) === true) {
                $this->config__shortlinks = $shortlinks;
                return $this->config__shortlinks;
            }
            return array();
        }

        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        $this->set_config__shortlinks();
        return $this->config__shortlinks;
    }

    /**
     * Gestion des liens du footer.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $config__footer = null;

    /**
     * Gestion des liens du footer.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__footer() {
        //
        if (file_exists("../dyn/footer.inc.php")) {
            include "../dyn/footer.inc.php";
            if (isset($footer)
                && is_array($footer) === true) {
                $this->config__footer = $footer;
                return $this->config__footer;
            }
            return array();
        }

        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        $this->set_config__footer();
        return $this->config__footer;
    }
    // }}}

    // {{{ BEGIN - Gestion des registres CSS & JS

    /**
     * @var array Registre des scripts JS
     *
     * Exemples :
     * $this->html_head_js = array(
     *     "add" => array(
     *         10 => array(
     *             "../app/js/specific.js",
     *         ),
     *     ),
     * );
     * $this->html_head_js = array(
     *     "set" => array(
     *         10 => array(
     *             "../app/js/specific1.js",
     *         ),
     *         20 => array(
     *             "../app/js/specific2.js",
     *         ),
     *     ),
     * );
     */
    var $html_head_js = array();

    /**
     * @var array Registre des scripts CSS
     *
     * Exemples :
     * $this->html_head_css = array(
     *     "add" => array(
     *         10 => array(
     *             "../app/css/specific.css",
     *         ),
     *     ),
     * );
     * $this->html_head_css = array(
     *     "set" => array(
     *         10 => array(
     *             "../app/css/specific1.css",
     *         ),
     *         20 => array(
     *             "../app/css/specific2.css",
     *         ),
     *     ),
     * );
     */
    var $html_head_css = array();

    /**
     * Permet d'ajouter un script JS au registre des scripts JS.
     *
     * Le layout est en charge du registre des scripts JS de base, cette
     * méthode permet d'ajouter l'appel à un script JS pour un script
     * PHP spécifique par exemple.
     * Utilisation :
     *  - $f->addHTMLHeadJs(array("../app/js/specific.js", ), 15);
     *  - $f->addHTMLHeadJs("../app/js/specific.js");
     *
     *
     * @param mixed $js Tableau (array) représentant une liste de chemin vers
     *                  les scripts JS à ajouter au registre ou chemin (string)
     *                  vers le script JS à ajouter au registre.
     * @param mixed $order Catégorie (integer) représentant l'odre dans le
     *                     registre dans lequel on souhaite ajouter le script.
     *                     Les anciennes catégories (string) : "begin", "middle"
     *                     et "end" peuvent être utilisées dans un souci de
     *                     rétro-compatibilité. Par défaut, si aucun ordre n'est
     *                     spécifié, il est ajouté dans l'ordre 20.
     *
     * @return void
     */
    function addHTMLHeadJs($js = array(), $order = null) {
        // Rétro-compatibilité et valeur par défaut pour le numéro d'ordre.
        if ($order == "begin") {
            $order = 10;
        } elseif ($order == "middle") {
            $order = 20;
        } elseif ($order == "end") {
            $order = 30;
        } elseif (!is_numeric($order) || is_null($order)) {
            $order = 20;
        }
        // Initialisation du stockage du registre : tous les scripts JS
        // supplémentaires sont stockés dans le tableau "<ORDER>" du tableau
        // "add" de l'attribut html_head_js.
        if (!isset($this->html_head_js["add"])) {
            $this->html_head_js["add"] = array();
        }
        if (!isset($this->html_head_js["add"][$order])) {
            $this->html_head_js["add"][$order] = array();
        }
        // Gestion du paramètre mixte $js (array ou string) et affectation au
        // registre.
        if (is_array($js)) {
            foreach ($js as $value) {
                $this->html_head_js["add"][$order][] = $value;
            }
        } else {
            $this->html_head_js["add"][$order][] = $js;
        }
    }

    /**
     * Permet de surcharger le registre des scripts JS.
     *
     * Le layout est en charge du registre des scripts JS de base, cette
     * méthode permet de remplacer l'appel aux scripts JS de base par les
     * scripts passés en paramètre pour un script PHP spécifique par exemple.
     * Utilisation :
     *  - $f->setHTMLHeadJs(
     *      array(
     *          10 => array(
     *              "../app/js/specific1.js",
     *          ),
     *          20 => array(
     *              "../app/js/specific2.js",
     *          ),
     *      ),
     *      true
     *  );
     *  - $f->setHTMLHeadJs("../app/js/specific.js");
     *
     * @param mixed $js Tableau (array) représentant une liste de chemin vers
     *                  les scripts JS à ajouter au registre ou chemin (string)
     *                  vers le script JS à ajouter au registre organisé ou non
     *                  en catégorie (voir second paramètre).
     * @param boolean $categories Le paramètre $js est organisée en catégories.
     *                            Par défaut on part du principe que ce n'est
     *                            pas le cas.
     *
     * @return void
     */
    function setHTMLHeadJs($js = array(), $categories = false) {
        // Initialisation du stockage du registre : tous les scripts JS
        // sont stockés dans le tableau "<ORDER>" du tableau "set" de
        // l'attribut html_head_js.
        if (!isset($this->html_head_js["set"])) {
            $this->html_head_js["set"] = array();
        }
        // Si le paramètre indique que la liste de scripts est organisée
        // en catégories.
        if ($categories == true) {
            // Alors on boucle sur chaque catégorie.
            foreach ($js as $key => $value) {
                // Rétro-compatibilité et valeur par défaut pour le numéro
                // d'ordre.
                if ($key == "begin") {
                    $key = 10;
                } elseif ($key == "middle") {
                    $key = 20;
                } elseif ($key == "end") {
                    $key = 30;
                } elseif (!is_numeric($key) || is_null($key)) {
                    $key = 20;
                }
                // Affectation au registre.
                $this->html_head_js["set"][$key] = $value;
            }
        } else {
            // Gestion du paramètre mixte $js (array ou string) et affectation
            // au registre.
            if (is_array($js)) {
                $this->html_head_js["set"][20] = $js;
            } else {
                $this->html_head_js["set"][20] = array($js, );
            }
        }
    }

    /**
     * Permet d'ajouter un script CSS au registre des scripts CSS.
     *
     * Le layout est en charge du registre des scripts CSS de base, cette
     * méthode permet d'ajouter l'appel à un script CSS pour un script
     * PHP spécifique par exemple.
     * Utilisation :
     *  - $f->addHTMLHeadCss(array("../app/css/specific.css", ), 15);
     *  - $f->addHTMLHeadCss("../app/css/specific.css");
     *
     *
     * @param mixed $css Tableau (array) représentant une liste de chemin vers
     *                   les scripts CSS à ajouter au registre ou chemin (string)
     *                   vers le script CSS à ajouter au registre.
     * @param mixed $order Catégorie (integer) représentant l'odre dans le
     *                     registre dans lequel on souhaite ajouter le script.
     *                     Les anciennes catégories (string) : "begin", "middle"
     *                     et "end" peuvent être utilisées dans un souci de
     *                     rétro-compatibilité. Par défaut, si aucun ordre n'est
     *                     spécifié, il est ajouté dans l'ordre 20.
     *
     * @return void
     */
    function addHTMLHeadCss($css = array(), $order = null) {
        // Rétro-compatibilité et valeur par défaut pour le numéro d'ordre.
        if ($order == "begin") {
            $order = 10;
        } elseif ($order == "middle") {
            $order = 20;
        } elseif ($order == "end") {
            $order = 30;
        } elseif (!is_numeric($order) || is_null($order)) {
            $order = 20;
        }
        // Initialisation du stockage du registre : tous les scripts CSS
        // supplémentaires sont stockés dans le tableau "<ORDER>" du tableau
        // "add" de l'attribut html_head_css.
        if (!isset($this->html_head_css["add"])) {
            $this->html_head_css["add"] = array();
        }
        if (!isset($this->html_head_css["add"][$order])) {
            $this->html_head_css["add"][$order] = array();
        }
        // Gestion du paramètre mixte $css (array ou string) et affectation au
        // registre.
        if (is_array($css)) {
            foreach ($css as $value) {
                $this->html_head_css["add"][$order][] = $value;
            }
        } else {
            $this->html_head_css["add"][$order][] = $css;
        }
    }

    /**
     * Permet de surcharger le registre des scripts CSS.
     *
     * Le layout est en charge du registre des scripts CSS de base, cette
     * méthode permet de remplacer l'appel aux scripts CSS de base par les
     * scripts passés en paramètre pour un script PHP spécifique par exemple.
     * Utilisation :
     *  - $f->setHTMLHeadCss(
     *      array(
     *          10 => array(
     *              "../app/css/specific1.css",
     *          ),
     *          20 => array(
     *              "../app/css/specific2.css",
     *          ),
     *      ),
     *      true
     *  );
     *  - $f->setHTMLHeadCss("../app/css/specific.css");
     *
     * @param mixed $css Tableau (array) représentant une liste de chemin vers
     *                   les scripts CSS à ajouter au registre ou chemin (string)
     *                   vers le script CSS à ajouter au registre organisé ou non
     *                   en catégorie (voir second paramètre).
     * @param boolean $categories Le paramètre $css est organisée en catégories.
     *                            Par défaut on part du principe que ce n'est
     *                            pas le cas.
     *
     * @return void
     */
    function setHTMLHeadCss($css = array(), $categories = false) {
        // Initialisation du stockage du registre : tous les scripts CSS
        // sont stockés dans le tableau "<ORDER>" du tableau "set" de
        // l'attribut html_head_css.
        if (!isset($this->html_head_css["set"])) {
            $this->html_head_css["set"] = array();
        }
        // Si le paramètre indique que la liste de scripts est organisée
        // en catégories.
        if ($categories == true) {
            // Alors on boucle sur chaque catégorie.
            foreach ($css as $key => $value) {
                // Rétro-compatibilité et valeur par défaut pour le numéro
                // d'ordre.
                if ($key == "begin") {
                    $key = 10;
                } elseif ($key == "middle") {
                    $key = 20;
                } elseif ($key == "end") {
                    $key = 30;
                } elseif (!is_numeric($key) || is_null($key)) {
                    $key = 20;
                }
                // Affectation au registre.
                $this->html_head_css["set"][$key] = $value;
            }
        } else {
            // Gestion du paramètre mixte $css (array ou string) et affectation
            // au registre.
            if (is_array($css)) {
                $this->html_head_css["set"][20] = $css;
            } else {
                $this->html_head_css["set"][20] = array($css, );
            }
        }
    }

    // }}} END - Gestion des registres CSS & JS

    // {{{ VRAC

    /**
     * Fonction tmp()
     *
     * @param string $fichier Nom du fichier.
     * @param string $msg Contenu du fichier.
     * @param boolean $entete Affichage ou non de l'entête.
     *
     * @deprecated
     */
    function tmp($fichier, $msg, $entete = false) {
        if (!$entete) {
            $ent = date("d/m/Y G:i:s")."\n";
            $ent .= "Collectivite : ".$_SESSION ['coll']." - ".$this->getParameter('ville')."\n";
            $ent .= "Utilisateur : ".$_SESSION ['login']."\n";
            $ent .= "==================================================\n";
            $msg = $ent."\n".$msg ;
        }
        @$enr = file_put_contents($fichier, $msg);
        if (!$enr) {
            $msg = __("Impossible d'ecrire le fichier de log :");
            $msg .= " ".$fichier.".";
            $msg .= " ".__("Le dossier n'est probablement pas accessible en ecriture.");
            $msg .= " ".__("Contactez votre administrateur.");
            $this->displayMessage ("error", $msg);
        }
        return $enr;
    }

    // }}}

    // {{{ Gestion des messages de debug

    /**
     * Ajoute un message à la pile des logs.
     *
     * @param string $message texte du message.
     * @param integer $type Niveau de log.
     *
     * @return void
     */
    function addToLog($message, $type = DEBUG_MODE) {
        //
        logger::instance()->log($this->elapsedtime()." : class ".get_class($this)." - ".$message, $type);
    }

    /**
     * Désactive l'affichage des logs pour cette page.
     *
     * @return void
     */
    function disableLog() {
        //
        logger::instance()->display_log = false;
    }

    // }}}

    // {{{ REDEFINITION DU MOT DE PASSE

    /**
     * Cree la table de redefinition du mot de passe.
     *
     * @return void
     */
    private function createPasswordResetTable() {

        //
        $sql = " CREATE TABLE ".DB_PREFIXE.$this->table_om_password_reset." (";
        $sql .= "id integer NOT NULL, ";
        $sql .= "login varchar(30) NOT NULL, ";
        $sql .= "reset_key varchar(50) NOT NULL, ";
        $sql .= "timeout float8 NOT NULL, ";
        $sql .= "PRIMARY KEY (id) );";

        //
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
    }

    /**
     * Recuperation du prochain id de la table de reinitialisation de mot de passe.
     * Si cette table n'existe pas, elle est cree et l'id renvoye est 1.
     *
     * @param int $id_column Nom de la colonne contenant l'identifiant de type int
     * @param string $table Nom de la table à interroger
     * @access private
     * @return int Valeur du prochain identifiant devant être insere
     */
    private function getNextPasswordResetId($id_column, $table) {
        //
        $id = null;
        $table_exists = true;
        //
        $sql = " SELECT MAX(".$id_column.") AS id";
        $sql .= " FROM ".DB_PREFIXE.$table;
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Si une erreur survient, la table est creee
        if ($this->isDatabaseError($res, true)) {
            $table_exists = false;
            $this->createPasswordResetTable();
        }
        // Si la table existait deja
        if ($table_exists == true) {
            while ($row =& $res->fetchrow(DB_FETCHMODE_ASSOC)) {
                $id = $row;
            }
            // On retourne l'id MAX
            return $id['id'] + 1;
        // Sinon on retourne 1
        } else {
            return 1;
        }
    }

    /**
     * Ajoute une nouvelle cle dans la table de redifinition de mot de
     * passe.
     *
     * @param $login Login de l'utilisateur reinitialisant son mot de passe
     * @param $key Cle valide necessaire au changement de mot de passe
     * @param $timeout Date de creation de la cle
     * @access public
     * @return void
     */
    public function addPasswordResetKey($login, $key, $timeout) {
        // Recuperation du prochain id
        $id = $this->getNextPasswordResetId("id", $this->table_om_password_reset);
        //
        $sql = "INSERT INTO ".DB_PREFIXE.$this->table_om_password_reset;
        $sql .= " VALUES (".$id.", '".$login."', '".$key."', ".$timeout.");";
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
    }

    /**
     * Supprime les cles expirees.
     *
     * @access public
     * @return void
     */
    public function deleteExpiredKey() {
        //
        if ($this->existsPasswordResetTable() === false) {
            //
            $this->createPasswordResetTable();
        } else {
            $timestamp = time();
            $now = date("YmdHis", $timestamp);
            $sql = "DELETE FROM ".DB_PREFIXE.$this->table_om_password_reset;
            $sql .= " WHERE timeout < ".$now;
            $res = $this->db->query($sql);
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->isDatabaseError($res);
        }
    }

    /**
     * Vérifie l'existence de la table permettant de stocker les clés de
     * réinitialisation de mot de passe.
     *
     * @access public
     * @return bool
     */
    public function existsPasswordResetTable() {
        if (in_array($this->table_om_password_reset, $this->db->getTables())) {
            return true;
        }
        return false;
    }

    /**
     * Teste l'existence d'une cle.
     *
     * @param string $key la cle à rechercher dans la base
     * @access public
     * @return string|bool Si la cle existe, le login de l'utilisateur associe est retourne
     * sinon la methode renvoie false.
     */
    public function passwordResetKeyExists($key) {
        //
        $sql = "SELECT * FROM ".DB_PREFIXE.$this->table_om_password_reset;
        $sql .= " WHERE reset_key = '".$key."';";
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Si une erreur survient
        if ($this->isDatabaseError($res, true)) {
            // La table de redefinition est cree
            $table_exists = false;
            $this->createPasswordResetTable();
            // On execute à nouveau la requete precedente
            $res = $this->db->query($sql);
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->isDatabaseError($res);
        }
        //
        $row = null;
        if ($res->numRows() == 1) {
            $row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
            return $row['login'];
        }
        // Si il existe plusieurs cles avec la meme signature,
        // une erreur est renvoyee stoppant ainsi le processus
        // de redefinition du mot de passe.
        // L'utilisateur doit alors re-generer une nouvelle
        // cle. Les doublons de ses cles seront supprimes apres
        // le succes de son prochain changement de mot de passe.
        if ($res->numRows() > 1) {
            $this->addToMessage("error", "Une erreur est survenue. Vous pouvez essayer ".
                                         "de redefinir votre mot de passe une nouvelle fois. ".
                                         "Si le probleme persiste, contactez votre administrateur.");
            return false;
        }
        $this->addToMessage("error", "La cle que vous avez valide n'existe pas ou a expiree.");
        return false;
    }

    /**
     * Supprime toutes les cles associes a un utilisateur.
     *
     * @param string $login Login de l'utilisateur
     * @access public
     * @return void
     */
    public function deletePasswordResetKeys($login) {
        //
        $sql = "DELETE FROM ".DB_PREFIXE.$this->table_om_password_reset;
        $sql .= " WHERE login = '".$login."';";
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
    }

    /**
     * Affichage du formulaire permettant de redefinir le mot de passe.
     *
     * @param int $coll Collectivite de l'utilisateur
     * @param string $login Login de l'utilisateur
     * @access public
     * @return void
     */
    public function displayPasswordResetLoginForm() {
        // Initialisation des paramètres
        $params = array(
            "came_from" => array(
                "method" => array("post", "get", ),
                "default_value" => "",
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        //
        echo "<div class=\"pageDescription\">";
        echo "\t <p>Pour des raisons de securite, nous gardons votre mot de passe chiffre,";
        echo "\t et nous ne pouvons pas vous l'envoyer. Si vous souhaitez re-initialiser";
        echo "\t votre mot de passe, remplissez le formulaire ci-dessous et nous vous enverrons";
        echo "\t un courrier electronique a l'adresse que vous avez donnee lors de l'enregistrement";
        echo "\t pour demarrer la phase de re-initialisation de votre mot de passe.";
        echo "\t </p>";
        echo "</div>";
        //
        $this->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_PASSWORD_RESET,
            "id" => "resetpw_form",
        ));
        //
        $validation = 0;
        $maj = 0;
        $champs = array("came_from", "login");

        if (count($this->database) > 1) {
            array_push($champs, "coll");
        }

        //
        $form = $this->get_inst__om_formulaire(array(
            "validation" => $validation,
            "maj" => $maj,
            "champs" => $champs,
        ));
        //
        $form->setType("came_from", "hidden");
        $form->setTaille("came_from", 20);
        $form->setMax("came_from", 20);
        $form->setVal("came_from", $came_from);
        //
        $form->setLib("login", __("Identifiant"));
        $form->setType("login", "text");
        $form->setTaille("login", 20);
        $form->setMax("login", 100);

        //
        if (count($this->database)>1) {
            $form->setLib("coll", __("Base de donnees"));
            $form->setType("coll", "select");
            $contenu = array(
                0 => array(),
                1 => array(),
            );
            foreach ($this->database as $key => $coll) {
                array_push($contenu[0], $key);
                array_push($contenu[1], $coll['title']);
            }
            $form->setSelect("coll", $contenu);
            if (isset($_SESSION['coll'])) {
                $form->setVal("coll", $_SESSION['coll']);
            }
        }
        //
        $form->entete();
        $form->afficher($champs, $validation, false, false);
        $form->enpied();

        //
        $this->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        $this->layout->display__form_input_submit(array(
            "class" => "context boutonFormulaireLogin ui-button ui-state ui-corner-all",
            "name" => "resetpwd_action_sendmail",
            "value" => __("Lancer la re-initialisation du mot de passe"),
        ));
        $this->layout->display__form_controls_container__end();
        $this->layout->display__form_container__end();
    }

    /**
     * Affichage du formulaire de saisi du nouveau mot de passe.
     *
     * @param int $coll Collectivite de l'utilisateur
     * @param string $login Login de l'utilisateur
     * @access public
     * @return void
     */
    public function displayPasswordResetPasswordForm($coll, $login) {
        // Initialisation des paramètres
        $params = array(
            "came_from" => array(
                "method" => array("post", "get", ),
                "default_value" => "",
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        //
        $this->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_PASSWORD_RESET,
            "id" => "resetpw_form",
        ));
        //
        $validation = 0;
        $maj = 0;
        $champs = array("came_from", "pwd_one", "pwd_two", "coll", "user_login");

        //
        $form = $this->get_inst__om_formulaire(array(
            "validation" => $validation,
            "maj" => $maj,
            "champs" => $champs,
        ));
        //
        $form->setType("came_from", "hidden");
        $form->setTaille("came_from", 20);
        $form->setMax("came_from", 20);
        $form->setVal("came_from", $came_from);
        //
        $form->setLib("pwd_one", __("Nouveau mot de passe"));
        $form->setType("pwd_one", "password");
        $form->setTaille("pwd_one", 20);
        $form->setMax("pwd_one", 100);
        //
        $form->setLib("pwd_two", __("Confirmation du mot de passe"));
        $form->setType("pwd_two", "password");
        $form->setTaille("pwd_two", 20);
        $form->setMax("pwd_two", 100);
        //
        $form->setLib("coll", "coll");
        $form->setType("coll", "hidden");
        $form->setVal("coll", $coll);
        //
        $form->setLib("user_login", "user_login");
        $form->setType("user_login", "hidden");
        $form->setVal("user_login", $login);
        //
        $form->entete();
        $form->afficher($champs, $validation, false, false);
        $form->enpied();

        //
        $this->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        $this->layout->display__form_input_submit(array(
            "class" => "context boutonFormulaireLogin ui-button ui-state ui-corner-all",
            "name" => "resetpwd_action_newpwd",
            "value" => __("Definir mon mot de passe"),
        ));
        $this->layout->display__form_controls_container__end();
        $this->layout->display__form_container__end();
    }

    /**
     * Envoie un mail.
     *
     * @param string $title Titre du mail
     * @param string $message Corps du mail
     * @param string $recipient Destinataire(s) du mail (séparés par une virgule)
     * @param array $file Liste de fichiers à envoyer en pièce jointe
     * @access public
     * @return bool True si le mail est correctement envoye, false sinon.
     */
    public function sendMail($title, $message, $recipient, $file = array()) {
        //
        @require_once "class.smtp.php";
        @require_once "class.phpmailer.php";

        if (!class_exists("PHPMailer")) {
            $this->addToLog(__METHOD__."(): !class_exists(\"PHPMailer\")", DEBUG_MODE);
            return false;
        }

        //
        $this->setMailConfig();

        //
        if ($this->mail_config == false) {
            $this->addToLog(__METHOD__."(): aucune configuration mail", DEBUG_MODE);
            return false;
        }

        //
        $mail = new PHPMailer(true);

        //
        $mail->IsSMTP();
        $mail->Username = $this->mail_config["mail_username"];
        $mail->Password = $this->mail_config["mail_pass"];
        if ($this->mail_config["mail_username"] == '') {
            $mail->SMTPAuth = false;
        } else {
            $mail->SMTPAuth = true;
        }
        // Possiilité de passer le paramètre de PHPMailer
        // $SMTPAutoTLS : boolean
        // @see https://github.com/PHPMailer/PHPMailer
        if (array_key_exists("smtp_auto_tls", $this->mail_config)) {
            $mail->SMTPAutoTLS = $this->mail_config["smtp_auto_tls"];
        }
        // Possiilité de passer le paramètre de PHPMailer
        // $AuthType : string
        // @see https://github.com/PHPMailer/PHPMailer
        if (array_key_exists("smtp_auth_type", $this->mail_config)) {
            $mail->AuthType = $this->mail_config["smtp_auth_type"];
        }
        // Possiilité de passer le paramètre de PHPMailer
        // $SMTPSecure : string
        // @see https://github.com/PHPMailer/PHPMailer
        if (array_key_exists("smtp_secure", $this->mail_config)) {
            $mail->SMTPSecure = $this->mail_config["smtp_secure"];
        }
        //
        $mail->Port = $this->mail_config["mail_port"];
        $mail->Host = $this->mail_config["mail_host"];
        $mail->AddReplyTo($this->mail_config["mail_from"], $this->mail_config["mail_from_name"]);
        $mail->From = $this->mail_config["mail_from"];
        $mail->FromName = $this->mail_config["mail_from_name"];
        // Gestion des destinataires du mail
        foreach (explode(",", $recipient) as $adresse) {
            if (!$this->checkValidEmailAddress($adresse)) {
                $this->addToLog(__METHOD__."(): courriel incorrect ".$adresse, DEBUG_MODE);
                return false;
            } else {
                $mail->AddAddress(trim($adresse));
            }
        }
        //
        $mail->IsHTML(true);

        // Corps du message
        $mail_body ="<html>";
        $mail_body .= "<head><title>".$title."</title></head>";
        $mail_body .= "<body>".$message."</body>";
        $mail_body .= "</html>";

        $mail->Subject  = $title;
        $mail->MsgHTML($mail_body);

        // Gestion des pièces jointes
        foreach ($file as $oneFile) {
            //
            if (isset($oneFile['stream'])) {
                $mail->AddStringAttachment($oneFile['content'], $oneFile['title'], $oneFile['encoding'] = 'base64', $oneFile['type'] = 'application/octet-stream');
            } else {
                $mail->AddAttachment($oneFile['url']);
            }
        }

        // Envoie de l'email
        try {
            $mail->Send();
            return true;
        } catch (phpmailerException $e) {
            $this->addToLog(__METHOD__."(): ".$e->errorMessage(), DEBUG_MODE);
        } catch (Exception $e) {
            $this->addToLog(__METHOD__."(): ".$e->getMessage(), DEBUG_MODE);
        }
        //
        return false;
    }

    /**
     * Genere une cle de 31 caracteres aplphanumerique minuscule
     * puis ajoute la date de cette maniere:
     *
     *  $hash .= date("YmdHis", time());
     *
     * @return string key
     * @access public
     * @return void
     */
    public function genPasswordResetKey() {
        $hash = "";
        $alphanumeric = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
            "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
            "u", "v", "w", "x", "y", "z", "0", "1", "2", "3",
            "4", "5", "6", "7", "8", "9");
        for ($i=0; $i<=30; $i++) {
            $rand = array_rand($alphanumeric);
            $hash .= $alphanumeric[$rand];
        }

        // ajout du temps pour eviter les collisions
        $hash .= date("YmdHis", time());
        return $hash;
    }

    // }}}

    // {{{ GESTION DU LAYOUT

    /**
     * Affiche la page.
     *
     * @return void
     */
    function display() {
        //
        $this->layout->set_parameter("actions_personnelles", $this->getActionsToDisplay());
        $this->layout->set_parameter("raccourcis", $this->getShortlinksToDisplay());
        $this->layout->set_parameter("actions_globales", $this->getFooterToDisplay());
        $this->layout->set_parameter("menu", $this->getMenuToDisplay());
        //
        $this->layout->set_parameter("page_title", $this->title);
        $this->layout->set_parameter("page_description", $this->description);
        //
        $this->layout->set_parameter("application", $this->get_application_name());
        $this->layout->set_parameter("version", $this->get_application_version());
        $this->layout->set_parameter("html_title", $this->get_config__html_head_title());
        $this->layout->set_parameter("url_dashboard", OM_ROUTE_DASHBOARD);
        $this->layout->set_parameter("favicon", $this->get_config__favicon());
        //
        $this->layout->set_parameter("style_header", $this->style_header);
        $this->layout->set_parameter("style_title", $this->style_title);
        //
        $this->layout->set_parameter("html_head_css", $this->html_head_css);
        $this->layout->set_parameter("html_head_js", $this->html_head_js);
        //
        $this->layout->set_parameter("collectivite", $this->collectivite);
        //
        $this->layout->set_parameter("messages", $this->message);
        //
        $this->layout->set_parameter("flag", $this->flag);
        //
        $this->layout->display();
    }

    /**
     * Titre HTML.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $html_head_title = ":: openMairie ::";

    /**
     * Titre HTML.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__html_head_title() {
        // On récupère le paramètre depuis la configuration, si la valeur n'est
        // pas nulle cela signifie qu'une configuration instance a été spécifiée
        // soit par config.inc.php soit par om_parametre.
        if ($this->getParameter("title") !== null) {
            return $this->getParameter("title");
        }
        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        return $this->html_head_title;
    }

    /**
     * Gestion du favicon de l'application.
     *
     * @var mixed Configuration niveau framework.
     */
    protected $html_head_favicon = null;

    /**
     * Gestion du favicon de l'application.
     *
     * L'objectif ici est d'avoir trois niveaux de configuration pour cet élément :
     * framework, application et instance. Voici l'ordre de préférence si les trois
     * niveaux sont configurés : instance > application > framework.
     *
     * @return mixed
     */
    function get_config__favicon() {
        // On récupère le paramètre depuis la configuration, si la valeur n'est
        // pas nulle cela signifie qu'une configuration instance a été spécifiée
        // soit par config.inc.php soit par om_parametre.
        if ($this->getParameter("favicon") !== null) {
            return $this->getParameter("favicon");
        }
        // On retourne ici la configuration framework ou application spécifiée
        // comme attribut de om_application (framework) ou de utils (application).
        return $this->html_head_favicon;
    }

    /**
     * Bloc HTML à ajouter dans le header HTML.
     * @var null|string
     */
    var $html_head_extras = null;

    /**
     * Mutateur pour la propriété 'html_head_extras'.
     *
     * @param string $html_head_extras Bloc HTML à ajouter dans le header HTML.
     *
     * @return void
     */
    function setHTMLHeadExtras($html_head_extras = "") {
        $this->html_head_extras = $html_head_extras;
        $this->layout->set_parameter("html_head_extras", $this->html_head_extras);
    }

    /**
     * Bloc HTML visant à remplacer la balise ouvrante <body>.
     * @var null|string
     */
    var $html_body = null;

    /**
     * Mutateur pour la propriété 'html_body'.
     *
     * @param string $html_body Bloc HTML visant à remplacer la balise ouvrante <body>.
     *
     * @return void
     */
    function setHTMLBody($html_body = "") {
        $this->html_body = $html_body;
        $this->layout->set_parameter("html_body", $this->html_body);
    }

    /**
     * Classe CSS du bloc entête.
     * @var string
     */
    var $style_header = "ui-widget-header";

    /**
     * Mutateur pour la propriété 'style_header'.
     *
     * Concaténation du paramètre avec la valeur actuelle de la propriété.
     *
     * @param string $style Classe CSS.
     *
     * @return void
     */
    function addStyleForHeader($style = "") {
        $this->style_header .= " ".$style;
    }

    /**
     * Mutateur pour la propriété 'style_header'.
     *
     * @param string $style Classe CSS.
     *
     * @return void
     */
    function setStyleForHeader($style = "") {
        $this->style_header = $style;
        $this->layout->set_parameter("style_header", $this->style_header);
    }

    /**
     * Accesseur pour la propriété 'style_header'.
     *
     * @return string
     */
    function getStyleForHeader() {
        return $this->style_header;
    }

    /**
     * Classe CSS du bloc titre.
     * @var string
     */
    var $style_title = "ui-state-active ui-corner-all";

    /**
     * Mutateur pour la propriété 'style_title'.
     *
     * Concaténation du paramètre avec la valeur actuelle de la propriété.
     *
     * @param string $style Classe CSS.
     *
     * @return void
     */
    function addStyleForTitle($style = "") {
        $this->style_title .= " ".$style;
    }

    /**
     * Mutateur pour la propriété 'style_title'.
     *
     * @param string $style Classe CSS.
     *
     * @return void
     */
    function setStyleForTitle($style = "") {
        $this->style_title = $style;
        $this->layout->set_parameter("style_title", $this->style_title);
    }

    /**
     * Accesseur pour la propriété 'style_title'.
     *
     * @return string
     */
    function getStyleForTitle() {
        return $this->style_title;
    }

    /**
     * Affiche l'entête.
     *
     * @return void
     */
    function displayHeader() {
        $this->layout->display_header();
    }

    /**
     * Affiche le pied de page.
     *
     * @return void
     */
    function displayFooter() {
        if (!is_null($this->layout)) {
            $this->layout->display_footer();
        }
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayStartContent() {
        $this->layout->display_content_start();
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayEndContent() {
        $this->layout->display_content_end();
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayHTMLHeader() {
        $this->layout->display_html_header();
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayHTMLFooter() {
        if (!is_null($this->layout)) {
            $this->layout->display_html_footer();
        }
    }

    /**
     * Affiche un titre de niveau 1 (titre de la page).
     *
     * Soit une valeur est passée en paramètre et c'est elle qui est affchée
     * sinon c'est la propriété 'page_title' qui l'est.
     *
     * @param string $page_title Titre à afficher.
     *
     * @return void
     */
    function displayTitle($page_title = "") {
        if ($page_title == "") {
            $page_title = $this->title;
        }
        $this->layout->display_page_title($page_title);
    }

    /**
     * Affiche le logo.
     *
     * @return void
     */
    function displayLogo() {
        $this->layout->display_logo();
    }

    /**
     * Affiche le menu.
     *
     * @return void
     */
    function displayMenu() {
        $this->layout->display_menu();
    }

    /**
     * Affiche une description.
     *
     * @param string $description Description à afficher.
     *
     * @return void
     */
    function displayDescription($description = "") {
        $this->layout->display_page_description($description);
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayActionLogin() {
        $this->layout->display_action_login();
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayActionCollectivite() {
        $this->layout->display_action_collectivite();
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayActionExtras() {
        $this->layout->display_action_extras();
    }

    /**
     * Affiche ...
     *
     * @return void
     */
    function displayActions() {
        $this->layout->display_actions();
    }

    /**
     * Affiche un titre de niveau 2.
     *
     * @param string $page_subtitle Titre de niveau 2 à afficher.
     *
     * @return void
     */
    function displaySubTitle($page_subtitle = null) {
        $this->layout->display_page_subtitle($page_subtitle);
    }

    /**
     * Affiche un lien 'Fermer'.
     *
     * A destination d'une popup pour fermer la popup. Le nom d'une fonction JS
     * peut être passé en paramètre pour que celle-ci soit appelée lors du clic
     * sur le lien fermer.
     *
     * @param string $js_function_close Nom de la fonction JS à appeler.
     *
     * @return void
     */
    function displayLinkJsCloseWindow($js_function_close = "") {
        $this->layout->display_link_js_close_window($js_function_close);
    }

    /**
     * Affiche un bloc message.
     *
     * @param string $class Classe CSS.
     * @param string $message Message à afficher.
     *
     * @return void
     */
    function displayMessage($class = "", $message = "") {
        if (!defined('REST_REQUEST')) {
            $this->layout->display_message($class, $message);
        }
    }

    /**
     * Affiche la liste des messages présents dans la propriété 'message'.
     *
     * @return void
     */
    function displayMessages() {
        $this->layout->set_parameter("messages", $this->message);
        $this->layout->display_messages();
    }

    /**
     * Affiche une balise link vers le script JS.
     *
     * @param string $js URL vers le script JS.
     *
     * @return void
     */
    function displayScriptJsCall($js = "") {
        $this->layout->display_script_js_call($js);
    }

    /**
     * Permet de récupérer un ou plusieurs paramètres optionnels du tableau 'extras'
     * de la configuration de database active.
     *
     * @param  string       $key     Clé de la valeur à récupérer, paramètre optionnel.
     * @return string/array $extras  Si on fournit une clé, on renvoie la valeur
     *                               correspondante sous forme de string. Sinon, on
     *                               renvoie le tableau 'extras' entier.
     */
    public function get_database_extra_parameters($key = null) {
        // Si la clé passée en paramètre existe dans la configuration
        if (array_key_exists($key, $this->database_config['extras'])) {
            // On retourne la valeur sous forme de string
            return $this->database_config['extras'][$key];
        }
        // On retourne le tableau 'extras' entier
        return $this->database_config['extras'];
    }

    // }}}

    // {{{ TOOLS - BEGIN

    /**
     * Gère l'initialisation des paramètres.
     *
     * @param array $parameters_to_initialize
     *
     * @return array
     */
    function get_initialized_parameters($parameters_to_initialize = array()) {
        $parameters_initialized = array();
        foreach ($parameters_to_initialize as $key => $param) {
            if (isset($param["var_name"]) === true) {
                $var_name = $param["var_name"];
            } else {
                $var_name = $key;
            }
            if (isset($param["method"]) === true) {
                $methods = $param["method"];
            } else {
                $methods = array("get", );
            }
            foreach ($methods as $method) {
                $method_name = "get_submitted_".$method."_value";
                $method_params = $this->$method_name();
                if (is_array($method_params) === true
                    && in_array($key, array_keys($method_params)) === true) {
                    $value = $this->$method_name($key);
                    if (isset($param["not_accepted_values"]) === true) {
                        if (in_array($value, $param["not_accepted_values"]) === false) {
                            $parameters_initialized[$var_name] = $value;
                            break;
                        } elseif (array_key_exists("default_value", $param) === true) {
                            $parameters_initialized[$var_name] = $param["default_value"];
                        }
                    } else {
                        $parameters_initialized[$var_name] = $value;
                        break;
                    }
                } elseif (array_key_exists("default_value", $param) === true) {
                    $parameters_initialized[$var_name] = $param["default_value"];
                }
            }
        }
        return $parameters_initialized;
    }

    /**
     * Vérifie la validité d'une date et la retourne dans le format souhaité.
     *
     * @param string $date
     * @param boolean $show
     *
     * @return false|string
     */
    function formatDate($date, $show = true) {

        $date_db = explode ('-', $date);
        $date_show = explode ('/', $date);

        if (count ($date_db) != 3 and count ($date_show) != 3) {
            return false;
        }

        if (count ($date_db) == 3) {
            if (!checkdate($date_db[1], $date_db[2], $date_db[0])) {
                return false;
            }
            if ($show == true) {
                return $date_db [2]."/".$date_db [1]."/".$date_db [0];
            } else {
                return $date;
            }
        }
        if (count ($date_show) == 3) {
            if (!checkdate($date_show[1], $date_show[0], $date_show[2])) {
                return false;
            }
            if ($show == true) {
                return $date;
            } else {
                return $date_show [2]."-".$date_show [1]."-".$date_show [0];
            }
        }
        return false;
    }

    /**
     * Vérifie la validité d'une adresse de courriel.
     *
     * @param string $address Adresse de courriel à valider.
     *
     * @return boolean
     */
    public function checkValidEmailAddress($address = "") {
        return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $address);
    }

    /**
     * Vérifie si la requête est de type XMLHttpRequest (Ajax).
     *
     * @return boolean
     */
    function isAjaxRequest() {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vérifie qu'une chaine commence par une chaine.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return boolean
     */
    public function starts_with($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * Vérifie q'une chaine se termine par une chaine.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return boolean
     */
    public function ends_with($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    /**
     * Retourne le type mime du fichier pour un chemin donné.
     * On peut préciser un type de secours au cas où cette
     * méthode ne parvient pas à le récupérer.
     *
     * @param   string  $path         chemin du fichier
     * @param   string  $backup_type  type de secours
     * @return  string                type mime du fichier
     */
    public function get_file_type($path, $backup_type = '') {
        // Le chemin est obligatoire
        if (empty($path) === true) {
            // si type de secours défini
            if (empty($backup_type) === false) {
                return $backup_type;
            }
            // sinon type mime inconnu on force le téléchargement
            return 'application/force-download';
        }
        // Instanciation de l'outil
        $finfo = new finfo(FILEINFO_MIME);
        // Extraction du mime type avec une regexp et l'outil
        preg_match('/(.*);/', $finfo->file($path), $type);
        // Cas géré
        if (is_array($type) === true
            && isset($type[1]) === true) {
            return $type[1];
        }
        // Cas non géré mais backup existant
        if ((is_array($type === false)
            || empty($type) === true)
            && empty($backup_type) === false) {
            return $backup_type;
        }
        // Cas non géré :
        // type mime inconnu on force le téléchargement
        return 'application/force-download';
    }

    // }}} TOOLS - END

    // {{{

    /**
     * Instanciation de la classe 'import'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return import
     */
    function get_inst__om_import($args = array()) {
        require_once PATH_OPENMAIRIE."om_import.class.php";
        return new import();
    }

    /**
     * Instanciation de la classe 'reqmo'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return reqmo
     */
    function get_inst__om_reqmo($args = array()) {
        require_once PATH_OPENMAIRIE."om_reqmo.class.php";
        return new reqmo();
    }

    /**
     * Instanciation de la classe 'gen'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return gen
     */
    function get_inst__om_gen($args = array()) {
        require_once PATH_OPENMAIRIE."om_gen.class.php";
        return new gen();
    }

    /**
     * Instanciation de la classe 'edition'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return edition
     */
    function get_inst__om_edition($args = array()) {
        require_once PATH_OPENMAIRIE."om_edition.class.php";
        return new edition();
    }

    /**
     * Instanciation de la classe 'table'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return table
     */
    function get_inst__om_table($args = array()) {
        //
        $method_args = array(
            "aff" => "",
            "table" => "",
            "serie" => 0,
            "champAffiche" => array(),
            "champRecherche" => array(),
            "tri" => "",
            "selection" => "",
            "edition" => "",
            "options" => array(),
            "advs_id" => null,
            "om_validite" => false,
        );
        //
        foreach ($method_args as $key => $value) {
            if (array_key_exists($key, $args) === true) {
                ${$key} = $args[$key];
            } else {
                ${$key} = $value;
            }
        }
        //
        if (file_exists("../obj/om_table.class.php")) {
            require_once "../obj/om_table.class.php";
            $class_name = "om_table";
        } else {
            require_once PATH_OPENMAIRIE."om_table.class.php";
            $class_name = "table";
        }
        return new $class_name(
            $aff,
            $table,
            $serie,
            $champAffiche,
            $champRecherche,
            $tri,
            $selection,
            $edition,
            $options,
            $advs_id,
            $om_validite
        );
    }

    /**
     * Instanciation de la classe 'dbform'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return dbform
     */
    function get_inst__om_dbform($args = array()) {
        //
        $method_args = array(
            "obj" => null,
            "idx" => 0,
        );
        //
        foreach ($method_args as $key => $value) {
            if (array_key_exists($key, $args) === true) {
                ${$key} = $args[$key];
            } else {
                ${$key} = $value;
            }
        }

        // Inclusion du script [obj/<OBJ>.class.php]
        $custom_script_path = $this->get_custom("obj", $obj);
        if ($custom_script_path !== null) {
            require_once $custom_script_path;
            $class_name = $obj.'_custom';
        } elseif (!file_exists("../obj/".$obj.".class.php")
            && file_exists(PATH_OPENMAIRIE."obj/".$obj.".class.php")) {
            require_once PATH_OPENMAIRIE."obj/".$obj.".class.php";
            $class_name = $obj.'_core';
        } elseif (!file_exists("../obj/".$obj.".class.php")
            && file_exists("../gen/obj/".$obj.".class.php")) {
            require_once "../gen/obj/".$obj.".class.php";
            $class_name = $obj.'_gen';
        } elseif (file_exists("../obj/".$obj.".class.php")) {
            require_once "../obj/".$obj.".class.php";
            $class_name = $obj;
        } else {
            return null;
        }
        return new $class_name(
            $idx,
            $this->db,
            0
        );
    }

    /**
     * Instanciation de la classe 'formulaire'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return formulaire
     */
    function get_inst__om_formulaire($args = array()) {
        //
        $method_args = array(
            "mode" => null,
            "validation" => 0,
            "maj" => 0,
            "champs" => array(),
            "val" => array(),
            "max" => array(),
        );
        //
        foreach ($method_args as $key => $value) {
            if (array_key_exists($key, $args) === true) {
                ${$key} = $args[$key];
            } else {
                ${$key} = $value;
            }
        }
        //
        if (file_exists("../obj/om_formulaire.class.php")) {
            require_once "../obj/om_formulaire.class.php";
            $class_name = "om_formulaire";
        } else {
            require_once PATH_OPENMAIRIE."om_formulaire.class.php";
            $class_name = "formulaire";
        }
        return new $class_name(
            $mode,
            $validation,
            $maj,
            $champs,
            $val,
            $max
        );
    }

    /**
     * Instanciation de la classe 'om_map'.
     *
     * @param array $args Arguments à passer au constructeur.
     * @return om_map
     */
    function get_inst__om_map($args = array()) {
        //
        $method_args = array(
            "obj" => null,
            "options" => array(),
        );
        //
        foreach ($method_args as $key => $value) {
            if (array_key_exists($key, $args) === true) {
                ${$key} = $args[$key];
            } else {
                ${$key} = $value;
            }
        }
        //
        if (file_exists("../obj/".$obj.".map.class.php")) {
            require_once "../obj/".$obj.".map.class.php";
            $class_name = "om_map_obj";
        } else {
            require_once PATH_OPENMAIRIE."om_map.class.php";
            $class_name = "om_map";
        }
        return new $class_name(
            $obj,
            $options
        );
    }

    // }}}

    // {{{

    /**
     * VIEW - view_main.
     *
     * <?php
     * require_once "../obj/utils.class.php";
     * $flag = filter_input(INPUT_GET, 'module');
     * if (in_array($flag, array("login", "logout", )) === false) {
     *     $flag = "nohtml";
     * }
     * $f = new utils($flag);
     * $f->view_main();
     *
     * @return void
     */
    function view_main() {
        //
        $module = $this->get_submitted_get_value("module");
        if ($module === null || $module === "") {
            $module = "dashboard";
        }
        //
        switch ($module) {
            case "login":
                $this->view_login();
                break;
            case "logout":
                $this->view_login();
                break;
            case "password":
                $this->view_password();
                break;
            case "dashboard":
                $this->view_dashboard();
                break;
            case "form":
                $this->view_form();
                break;
            case "sousform":
                $this->view_sousform();
                break;
            case "tab":
                $this->view_tab();
                break;
            case "soustab":
                $this->view_soustab();
                break;
            case "map":
                $this->view_map();
                break;
            case "edition":
                $this->view_module_edition();
                break;
            case "import":
                $this->view_module_import();
                break;
            case "gen":
                $this->view_module_gen();
                break;
            case "reqmo":
                $this->view_module_reqmo();
                break;
        }
    }

    /**
     * VIEW - view_login.
     *
     * @return void
     */
    function view_login() {
        //
        if ($this->get_submitted_get_value("mode") === "password_reset"
            && isset($this->config['password_reset']) === true
            && $this->config['password_reset'] === true) {
            //
            $this->view_reset_password();
            return;
        }

        //
        $this->setFlag(null);
        $this->setTitle(__("Veuillez vous connecter"));
        $this->display();

        //
        $this->displayLoginForm();
    }

    /**
     * VIEW - view_reset_password.
     *
     * @return void
     */
    function view_reset_password() {
        // Initialisation des paramètres
        $params = array(
            "key" => array(
                "default_value" => null,
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        //
        $this->setFlag(null);
        $this->setTitle(__("Redefinition du mot de passe"));
        $this->display();
        //
        $coll = null;
        $user_login = null;
        $next_action = "display_login_form";

        if (is_array($this->get_submitted_post_value())
            && count($this->get_submitted_post_value()) > 0) {
            // Si la valeur du champ coll dans le formulaire de login est definie
            if ($this->get_submitted_post_value("coll") !== null) {
                // On ajoute en variable de session la cle du tableau associatif de
                // configuration de base de donnees a laquelle l'utilisateur
                // souhaite se connecter
                $_SESSION['coll'] = $this->get_submitted_post_value("coll");
                // Debug
                $this->addToLog(__METHOD__."(): \$_SESSION['coll']=\"".$_SESSION['coll']."\"", EXTRA_VERBOSE_MODE);
            }
            $this->connectDatabase();
            $this->deleteExpiredKey();
        }

        // traitement de la demande de redefinition
        if ($this->get_submitted_post_value("resetpwd_action_sendmail") !== null && $key === null) {
            //
            $valid_post = true;
            $login = $this->get_submitted_post_value("login");
            // validation du login
            if ($login == "") {
                $valid_post = false;
                $this->addToMessage("error", __("Votre identifiant est incorrect, ou ne vous permet pas de redefinir votre mot de passe de cette maniere. Contactez votre administrateur."));
            }
            // traitement ...
            if ($valid_post == true) {
                $mode = $this->retrieveUserAuthenticationMode($login);

                // cas : login non trouve en base
                if ($mode == false) {
                    $this->addToMessage("error", __("Votre identifiant est incorrect, ou ne vous permet pas de redefinir votre mot de passe de cette maniere. Contactez votre administrateur."));
                // cas : login correct et mode == "db"
                } elseif (strtolower($mode) == "db") {
                    $sended = false;
                    $user_infos = $this->retrieveUserProfile($login);
                    if (isset($user_infos['email']) and !empty($user_infos['email'])) {
                        $hash = $this->genPasswordResetKey();
                        $key_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://":"http://").$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."&key=".$hash."&coll=".$this->get_submitted_post_value("coll");
                        // timeout 60 minutes
                        $timestamp = time() + 3600;
                        $timeout = date("YmdHis", $timestamp);
                        // compose mail
                        $mail_title  = __("Confirmation de reinitialisation du mot de passe ".$this->get_application_name());
                        $mail_recipient = $user_infos['email'];
                        $mail_content = __("Vous avez demande la reinitialisation de votre mot de passe ".$this->get_application_name()." pour l'identifiant")." : ".$login."<br>";
                        $mail_content .= __("Pour finaliser votre demande, veuillez cliquer sur ce lien")." : ";
                        $mail_content .= "<br><br><strong><a href=\"".$key_url."\" >".$key_url."</strong></a>";
                        $mail_content .= "<br><br>Pour des raisons de securite, le lien ci-dessus expire dans un delai de 1 heure.";
                        $sended = $this->sendMail($mail_title, $mail_content, $mail_recipient);
                    }
                    if ($sended) {
                        $this->addPasswordResetKey($login, $hash, $timeout);
                        $this->addToMessage("valid", __("Un message de demande de reinitialisation de mot de passe vous a ete envoye sur votre messagerie."));
                        $next_action = null;
                    } else {
                        $this->addToMessage("error", "Erreur lors de l'envoi par email. Veuillez contacter votre administrateur.");
                    }
                // cas : login correct et mode != "db"
                } else {
                    $this->addToMessage("error", __("Votre identifiant est incorrect, ou ne vous permet pas de redefinir votre mot de passe de cette maniere. Contactez votre administrateur."));
                }
            }
        } elseif ($this->get_submitted_post_value("resetpwd_action_newpwd") !== null && $key === null) {
            //
            $user_login = $this->get_submitted_post_value("user_login");
            if (empty($_POST['pwd_one']) or empty($_POST['pwd_two'])) {
                $this->addToMessage("error", "Veuillez remplir les deux champs mot de passe.");
                $coll = $this->get_submitted_post_value("coll");
                $next_action = "display_password_form";
            } elseif ($_POST['pwd_one'] == $_POST['pwd_two']) {
                $this->changeDatabaseUserPassword($user_login, $_POST['pwd_one']);
                $this->deletePasswordResetKeys($user_login);
                $this->addToMessage("valid", "Le nouveau mot de passe a bien ete enregistre. Vous pouvez desormais vous connecter avec ce mot de passe.");
                $next_action = null;
            } else {
                $this->addToMessage("error", "Les deux mots de passe ne sont pas identiques.");
                $coll = $this->get_submitted_post_value("coll");
                $next_action = "display_password_form";
            }
        } elseif ($key !== null and $this->get_submitted_get_value("coll") !== null) {
            //
            $_SESSION['coll'] = $this->get_submitted_get_value("coll");
            $this->connectDatabase();
            $login =  $this->passwordResetKeyExists($key);
            if ($login != false) {
                $coll = $_SESSION['coll'];
                $user_login = $login;
                $next_action = "display_password_form";
            } else {
                $next_action = "display_login_form";
            }
        }
        $this->displayMessages();

        if ($next_action == "display_login_form") {
            $this->displayPasswordResetLoginForm();
        } elseif ($next_action == "display_password_form") {
            $this->displayPasswordResetPasswordForm($coll, $user_login);
        }
    }

    /**
     * VIEW - view_password.
     *
     * Cette vue permet d'afficher un formulaire de changement de mot de passe
     * de l'utilisateur et de traiter les resultats en les validant dans la
     * base de données.
     *
     * @return void
     */
    function view_password() {
        //
        $this->isAuthorized("password");
        $this->setFlag(null);
        $this->setTitle(__("Mon compte")." -> ".__("Mot de passe"));
        $this->display();

        /**
         * Description de la page
         */
        $description = __("Cette page vous permet de changer votre mot de passe. Pour ".
                         "cela, il vous suffit de saisir votre mot de passe ".
                         "actuel puis votre nouveau mot de passe deux fois.");
        $this->displayDescription($description);

        /**
         * Affichage en onglet
         */
        //
        echo "<div id=\"formulaire\">\n\n";
        //
        $this->layout->display_start_navbar();
        echo "<ul>";
        echo "<li><a ";
        echo " href=\"#tabs-1\">".__("Mot de passe")."</a></li>";
        echo "</ul>\n";
        $this->layout->display_stop_navbar();
        /**
         * Onglet changement du mot de passe
         */
        //
        echo "<div id=\"tabs-1\">\n";
        // Traitement si validation du formulaire
        if ($this->get_submitted_post_value("submit-change-password") !== null) {
            // Recuperation des valeurs du formulaire
            $new_password = $_POST['new-password'];
            $new_password_confirmation = $_POST['new-password-confirmation'];
            // Verification du mot de passe actuel de l'utilisateur
            $authenticated = $this->processDatabaseAuthentication($_SESSION['login'], $_POST['current-password']);
            // Si la saisie n'est pas correcte on affiche un message d'erreur sinon
            // on change le mot de passe
            if ($authenticated == false) {
                // Affichage du message d'erreur
                $class = "error";
                $message = __("Mot de passe actuel incorrect");
                $this->displayMessage($class, $message);
            } elseif ($new_password != $new_password_confirmation or $new_password == "") {
                // Affichage du message d'erreur
                $class = "error";
                $message = __("Nouveau mot de passe incorrect");
                $this->displayMessage($class, $message);
            } else {
                // Changement du mot de passe
                $this->changeDatabaseUserPassword($_SESSION['login'], $new_password);
                // Affichage du message de validation
                $class = "ok";
                $message = __("Votre mot de passe a ete change correctement");
                $this->displayMessage($class, $message);
            }
        }

        /**
         *
         */
        // Affichage du formulaire de changement de mot de passe
        echo "\n<div id=\"form-change-password\" class=\"formulaire\">\n";
        //
        $this->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_PASSWORD,
        ));
        //
        $validation = 0;
        $maj = 0;
        $champs = array("current-password", "new-password", "new-password-confirmation");
        //
        $form = $this->get_inst__om_formulaire(array(
            "validation" => $validation,
            "maj" => $maj,
            "champs" => $champs,
        ));
        //
        $form->setLib("current-password", __("Mot de passe actuel"));
        $form->setType("current-password", "password");
        $form->setTaille("current-password", 20);
        $form->setMax("current-password", 20);
        //
        $form->setLib("new-password", __("Nouveau mot de passe"));
        $form->setType("new-password", "password");
        $form->setTaille("new-password", 20);
        $form->setMax("new-password", 20);
        //
        $form->setLib("new-password-confirmation", __("Confirmation du nouveau mot de passe"));
        $form->setType("new-password-confirmation", "password");
        $form->setTaille("new-password-confirmation", 20);
        $form->setMax("new-password-confirmation", 20);
        //
        $form->entete();
        
        $form->afficher($champs, $validation, false, false);
        $form->enpied();
        //
        $this->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        $this->layout->display__form_input_submit(array(
            "name" => "submit-change-password",
            "class" => "boutonFormulaire",
            "value" => __("Valider"),
        ));
        $this->layout->display__form_controls_container__end();
        //
        $this->layout->display__form_container__end();
        echo "</div>\n";
        //
        echo "</div>\n";

        /**
         * Fin de l'onglet changement du mot de passe
         */
        //
        echo "\n</div>\n";
    }

    /**
     * VIEW - view_dashboard.
     *
     * @return void
     */
    function view_dashboard() {
        //
        $this->setFlag(null);
        $this->setTitle(__("Tableau de bord"));
        $this->display();
        //
        $om_dashboard = $this->get_inst__om_dbform(array(
            "obj" => "om_dashboard",
            "idx" => 0,
        ));
        $om_dashboard->view_dashboard();
    }

    /**
     * VIEW - view_module_edition.
     *
     * @return void
     */
    function view_module_edition() {
        $om_edition = $this->get_inst__om_edition();
        $om_edition->view_pdf();
    }

    /**
     * VIEW - view_module_gen.
     *
     * @return void
     */
    function view_module_gen() {
        $om_gen = $this->get_inst__om_gen();
        $om_gen->view_gen();
    }

    /**
     * VIEW - view_module_import.
     *
     * @return void
     */
    function view_module_import() {
        $om_import = $this->get_inst__om_import();
        $om_import->view_import();
    }

    /**
     * VIEW - view_module_reqmo.
     *
     * @return void
     */
    function view_module_reqmo() {
        $om_reqmo = $this->get_inst__om_reqmo();
        $om_reqmo->view_reqmo();
    }

    /**
     * VIEW - view_form.
     *
     * @return void
     */
    function view_form() {
        // Gestion des snippets de formulaire
        if ($this->get_submitted_get_value("snippet") !== null) {
            $om_formulaire = $this->get_inst__om_formulaire(array(
                "mode" => "view_snippet",
            ));
            $om_formulaire->view_snippet();
            return;
        }

        // Rétrocompatibilité : il est possible que dans les scripts inclus
        // par cette méthode, la variable $f soit attendue et utilisée.
        // @deprecated Cette variable ne doit plus être utilisée.
        $f = $this;

        // Gestion de la fonction directlink
        if ($this->get_submitted_get_value("direct_link") === "true") {
            // Initialisation des paramètres
            $params = array(
                // objet de l'objet parent
                "obj" => array(
                    "default_value" => "",
                ),
                // action sur l'objet parent
                "action" => array(
                    "default_value" => "",
                ),
                // (optionnel) soit idx soit direct_field : identifiant de
                // l'objet contexte
                "idx" => array(
                    "default_value" => "",
                ),
                // (optionnel) soit idx soit direct_field : nom du champ contenant
                // l'identifiant de l'objet contexte
                "direct_field" => array(
                    "default_value" => "",
                ),
                // nom de l'objet du sous form a afficher
                "direct_form" => array(
                    "default_value" => "",
                ),
                // action a effectuer sur le sous-form
                "direct_action" => array(
                    "default_value" => "",
                ),
                // id de l'objet du sous-form a afficher
                "direct_idx" => array(
                    "default_value" => "",
                ),
            );
            foreach ($this->get_initialized_parameters($params) as $key => $value) {
                ${$key} = $value;
            }
            // Vérification des paramètres obligatoires
            if (empty($obj)
                || empty($action)
                || (empty($idx) && empty($direct_field))
                || empty($direct_form)
                || empty($direct_action)
                || empty($direct_idx)) {
                //
                $class = "error";
                $message = __("L'element n'est pas accessible.");
                $this->addToMessage($class, $message);
                $this->setFlag(null);
                $this->display();
                return;
            }
            // Inclusion du script [sql/<OM_DB_PHPTYPE>/<OBJ>.inc.php]
            // L'objectif est de récupéré la liste des onglets pour extraire
            // l'identifiant de l'onglet sélectionné
            // - Variable utilisée $sousformulaire
            $standard_script_path = "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
            $core_script_path = PATH_OPENMAIRIE."sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
            $gen_script_path = "../gen/sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
            $custom_script_path = $this->get_custom("tab", $obj);
            if ($custom_script_path !== null) {
                require_once $custom_script_path;
            } elseif (file_exists($standard_script_path) === false
                && file_exists($core_script_path) === true) {
                require_once $core_script_path;
            } elseif (file_exists($standard_script_path) === false
                && file_exists($gen_script_path) === true) {
                require_once $gen_script_path;
            } elseif (file_exists($standard_script_path) === true) {
                require_once $standard_script_path;
            }
            $tabs_id = 0;
            foreach ($sousformulaire as $sousform) {
                $droit = array();
                $droit[] = $sousform;
                $droit[] = $sousform."_tab";

                if ($this->isAccredited($droit, "OR")) {
                    $tabs_id++;
                    if ($sousform == $direct_form) {
                        break;
                    }
                }
            }
            // Pour récupérer l'identifiant de l'objet contexte, deux possibilités :
            // - soit le paramètre identifiant du contexte *idx* est fourni et on prend
            //   directement cet identifiant pour composer l'URL vers le form,
            // - soit le paramètre identifiant du contexte *idx* n'est pas fourni,
            //   alors on utilise le paramètre *direct_field* pour récupérer la valeur
            //   du champ correspondant dans l'instance de l'objet direct.
            $context_idx = null;
            if (empty($idx) === false) {
                $context_idx = $idx;
            } else {
                $object = $this->get_inst__om_dbform(array(
                    "obj" => $direct_form,
                    "idx" => $direct_idx,
                ));
                // Verification de la presence de la classe
                if ($object === null) {
                    $class = "error";
                    $message = __("L'objet est invalide.");
                    $this->addToMessage($class, $message);
                    $this->setFlag(null);
                    $this->display();
                    return;
                }
                $context_idx = $object->getVal($direct_field);
            }
            // Appel du sous-form avec l'id du formulaire parent recupere dans les valeur de l'objet instancie
            header(sprintf(
                'Location: %s&obj=%s&action=%s&idx=%s&direct_form=%s&direct_idx=%s&direct_action=%s#ui-tabs-%s',
                OM_ROUTE_FORM,
                $obj,
                $action,
                $context_idx,
                $direct_form,
                $direct_idx,
                $direct_action,
                $tabs_id
            ));
            return;
        }

        // Initialisation des paramètres
        $params = array(
            // Nom de l'objet metier du formulaire
            "obj" => array(
                "default_value" => "",
            ),
            // Flag de validation du formulaire
            "validation" => array(
                "default_value" => 0,
            ),
            // Origine de l'action
            "retour" => array(
                "default_value" => "",
            ),
            // Objet de sous-form
            "direct_form" => array(
                "default_value" => "",
            ),
            // Idx de sous-form
            "direct_idx" => array(
                "default_value" => "",
            ),
            // Action sur le sous-form
            "direct_action" => array(
                "default_value" => "",
            ),
            // Id unique de la recherche avancee (tab.php?advs_id=)
            "advs_id" => array(
                "default_value" => "",
            ),
            // Premier enregistrement a afficher sur le tableau de la page precedente (tab.php?premier=)
            "premier" => array(
                "default_value" => 0,
            ),
            // Colonne choisie pour le tri sur le tableau de la page precedente (tab.php?tricol=)
            "tricol" => array(
                "default_value" => "",
            ),
            // Valilite des objets a afficher sur le tableau de la page precedente (tab.php?valide=)
            "valide" => array(
                "default_value" => "",
            ),
            "idx" => array(
                "default_value" => "]",
            ),
            "action" => array(
                "var_name" => "maj",
                "default_value" => 0,
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        // Ce tableau permet a chaque application de definir des variables
        // supplementaires qui seront passees a l'objet metier dans le constructeur
        // a travers ce tableau
        // Voir le fichier dyn/form.get.specific.inc.php pour plus d'informations
        $extra_parameters = array();
        if (file_exists("../dyn/form.get.specific.inc.php")) {
            require "../dyn/form.get.specific.inc.php";
        }

        //
        $standard_script_path = "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $core_script_path = PATH_OPENMAIRIE."sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $gen_script_path = "../gen/sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";

        /**
         * Verification des parametres
         */
        if (strpos($obj, "/") !== false
            || (file_exists($standard_script_path) === false
                && file_exists($core_script_path) === false
                && file_exists($gen_script_path) === false)) {
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->addToMessage($class, $message);
            $this->setFlag(null);
            $this->display();
            die();
        }

        // Dictionnaire des actions
        // ------------------------

        // Declaration du dictionnaire
        $portlet_actions = array();
        if ($maj == 3) {
            // Action : modifier
            $portlet_actions['modifier'] = array(
                'lien' => OM_ROUTE_FORM.'&obj='.$obj.'&amp;action=1'.'&amp;idx=',
                'id' => '&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=form',
                'lib' => '<span class="om-prev-icon om-icon-16 edit-16">'.__('Modifier').'</span>',
                'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
                'ordre' => 10,
                'description' => __('Modifier'),
            );
            // Action : supprimer
            $portlet_actions['supprimer'] = array(
                'lien' => OM_ROUTE_FORM.'&obj='.$obj.'&amp;action=2&amp;idx=',
                'id' => '&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=form',
                'lib' => '<span class="om-prev-icon om-icon-16 delete-16">'.__('Supprimer').'</span>',
                'rights' => array('list' => array($obj, $obj.'_supprimer'), 'operator' => 'OR'),
                'ordre' => 20,
                'description' => __('Supprimer'),
            );
        }

        /**
         *
         */
        // Initialisation des variables presentes dans le fichier inclus juste apres
        $table = "";
        $ent = "";

        // Type d'affichage de la page
        //  include ?
        $display_accordion = false;
        $display_tabs=true;

        // surcharge globale
        if (file_exists('../dyn/form.inc.php')) {
            require_once '../dyn/form.inc.php';
        }

        // Inclusion du script [sql/<OM_DB_PHPTYPE>/<OBJ>.inc.php]
        $custom_script_path = $this->get_custom("tab", $obj);
        if ($custom_script_path !== null) {
            require_once $custom_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($core_script_path) === true) {
            require_once $core_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($gen_script_path) === true) {
            require_once $gen_script_path;
        } elseif (file_exists($standard_script_path) === true) {
            require_once $standard_script_path;
        }

        //
        $enr = $this->get_inst__om_dbform(array(
            "obj" => $obj,
            "idx" => $idx,
        ));
        if ($enr === null) {
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->addToMessage($class, $message);
            $this->setFlag(null);
            $this->display();
            die();
        }
        // Incrementation du compteur de validation du formulaire
        $validation++;
        // Enclenchement de la tamporisation de sortie
        ob_start();


        //
        // Affectation des parametres de la vue dans un attribut de l'objet
        $parameters = array(
            "aff" => "",
            "validation" => $validation,
            "maj" => $maj,
            "idx" => $idx,
            "retour" => $retour,
            "actions" => $portlet_actions,
            "postvar" => $_POST,
            // Variables de tab.php à conserver pour conserver les paramètres
            // d'affichage du listing (pagination, tri, validité, recherche)
            "advs_id" => $advs_id,
            "premier" => $premier,
            "tricol" => $tricol,
            "valide" => $valide,
        );
        // Affectation du tableau precedant dans l'attribut 'parameters'
        $enr->setParameters($parameters);
        // Affectation du tableau passe en parametre dans l'attribut 'parameters'
        $enr->setParameters($extra_parameters);

        /**
         * Affichage de la structure de la page
         */
        // Verification des credentials de l'utilisateur
        $right_suffix = "_";
        switch ($maj) {
            case "0":
                $right_suffix .= "ajouter";
                break;
            case "1":
                $right_suffix .= "modifier";
                break;
            case "2":
                $right_suffix .= "supprimer";
                break;
            case "3":
                $right_suffix .= "consulter";
                break;
            default:
                if ($enr->is_option_class_action_activated() === true) {
                    $right_suffix .= $enr->get_action_param($maj, "permission_suffix");
                }
                break;
        }
        $this->isAuthorized(array($obj.$right_suffix, $obj), "OR");

        //
        if ($enr->is_option_class_action_activated()===true) {
            //
            $view_parameter = $enr->get_action_param($maj, 'view');
            //
            if (method_exists($enr, $view_parameter)) {
                $enr->$view_parameter();
            } else {
                $enr->formulaire();
            }
        } else {
            $enr->formulaire();
        }

        // Affecte le contenu courant du tampon de sortie a $return puis l'efface
        $return = ob_get_clean();

        // Récupère le fil d'Ariane
        $ent = $enr->getFormTitle($ent);

        // Affichage du titre
        $this->setTitle($ent);
        //
        if ($this->isAjaxRequest()) {
            //
            header("Content-type: text/html; charset=".HTTPCHARSET."");
            // Affichage du retour de la methode formulaire
            echo $return;
            //
            die();
        } else {
            // Affichage des elements
            $this->setFlag(null);
            $this->display();
        }

        /**
         *
         */
        //
        echo "\n<div id=\"formulaire\">\n\n";

        // Si formulaire en mode ajout et formulaire valide et enregistrement correct
        // alors on recupere $idx pour le passer aux sous formulaires
        if ($maj == 0 and $validation>1 and $enr->correct==1 and $idx ==']') {
            $idx = $enr->valF[$enr->clePrimaire];
        }

        //premier onglet

        /**
         * Affichage du titre du tableau dans un onglet ou sous une autre forme selon
         * le layout
         */
        //
        if (isset($form_title)) {
            //
            $param = $form_title;
        } elseif (isset($tab_title)) {
            //
            $param = $tab_title;
        } else {
            //
            $param = __($obj);
        }
        $this->layout->display_form_lien_onglet_un($param);

        // Affichage des sous formulaires en onglets
        $tabs = array();
        if (isset($sousformulaire) and $display_tabs) {
            //
            foreach ($sousformulaire as $elem) {
                //
                if ($this->isAccredited(array($elem, $elem."_tab"), "OR") == false) {
                    continue;
                }
                //
                $tabs[] = $elem;
                // ouverture lien onglet
                echo "\t\t<li>";
                echo "<a id=\"".$elem."\"";
                //
                if (isset($sousformulaire_parameters[$elem]["href"])) {
                    echo " href=\"".$sousformulaire_parameters[$elem]["href"]."?retourformulaire=".$obj."&amp;idxformulaire=".$idx."\">";
                } else {
                    echo " href=\"".OM_ROUTE_SOUSTAB."&obj=".$elem."&amp;retourformulaire=".$obj."&amp;idxformulaire=".$idx."\">";
                }
                //
                if (isset($sousformulaire_parameters[$elem]["title"])) {
                    echo $sousformulaire_parameters[$elem]["title"];
                } else {
                    echo __($elem);
                }
               // fermeture lien onglet
                echo "</a>";
                echo "</li>\n";
            }
        }
        if ($display_accordion == false) {
            // Affichage de la recherche pour les sous formulaires
            $link = OM_ROUTE_SOUSTAB."&retourformulaire=".$obj."&amp;idxformulaire=".$idx;
            $param = array(
                "link" => $link,
                "advs_id" => str_replace(array('.',','), '', microtime(true)),
            );
            $this->layout->display_form_recherche_sousform($param);
        }
        // Fermeture de la liste des onglets
        echo "\t</ul>\n\n";


        // Ouverture de la balise - Onglet 1
        echo "\t<div id=\"tabs-1\">\n\n";

        // Affichage du retour de la methode formulaire
        echo "<div id=\"form-message\">";
        echo "<!-- -->";
        echo "</div>";
        echo "<div id=\"form-container\">";
        echo $return;
        echo "</div>";

        // Condition pour la désactivation des onglets dans certains cas de figure
        $tab_disabled_condition = false;
        if (// En mode ajout et si le formulaire n'est pas validé
            ($maj == 0 && $enr->correct == false)
            // En mode modification  et si le formulaire n'est pas validé et si l'option de désactivation en modification est activée
            || ($maj == 1 && $enr->correct == false && isset($option_tab_disabled_on_edit) && $option_tab_disabled_on_edit == true)
            // En mode suppression
            || $maj == 2
            // Dans tous les autres modes
            || $maj > 3
        ) {
            $tab_disabled_condition = true;
        }

        // Javascript pour la desactivation des onglets lorsque nécessaire
        if ($tab_disabled_condition) {
            echo "<script type=\"text/javascript\">";
            echo "$(function() {";
            echo "$(\"#formulaire\").tabs(\"option\", \"disabled\", [";
            foreach ($tabs as $key => $tab) {
                echo ($key+1);
                if (count($tabs) > $key + 1) {
                    echo ",";
                }
            }
            echo "]);";
            echo "});";
            echo "</script>";
        } elseif (in_array($direct_form, $tabs)) {
            // si le parametre direct_form est dans la liste des sous tab
            echo "<script type=\"text/javascript\">";
            echo "$(function() {";
            if ($direct_idx!="") {
                echo "waitUntilExists('sousform-".$direct_form."',function(){
                // si un idx est defini on charge le formulaire de l'objet correspondant
                ajaxIt('".$direct_form."','".OM_ROUTE_SOUSFORM."&obj=".$direct_form.
                    "&action=3&idx=".$direct_idx."&retourformulaire=".$obj."&idxformulaire=".$idx."&action=".$direct_action."');
                });";
            }
            echo "});";

            echo "</script>";
        }

        // Affichage des sous formulaires en accordeon sous le formulaire

        if ($display_accordion) {
            if ($maj == 1 or $maj == 3 or ($maj == 0 and $validation>1 and $enr->correct==1 and $idx ==']')) {
                if (isset($sousformulaire)) {
                    echo "<div class=\"visualClear\"><!-- --></div>";
                    $this->layout->display_form_start_conteneur_onglets_accordion();
                    echo "<h3>";
                    // Affichage de la recherche pour les sous formulaires
                    $link = OM_ROUTE_SOUSTAB."&retourformulaire=".$obj."&amp;idxformulaire=".$idx;
                    $param = array("link" => $link);
                    $this->layout->display_form_recherche_sousform_accordion($param);
                    foreach ($sousformulaire as $elem) {
                        $this->layout->display_form_start_conteneur_chaque_onglet_accordion();

                        // A VOIR AND ?????????????????????????????????????????????????
                        if (isset($sousformulaire_parameters[$elem]["href"]) and isset($sousformulaire_parameters[$elem]["href"])) {
                            $params = array(
                                "elem" => $elem,
                                "href" => $sousformulaire_parameters[$elem]["href"],
                                "idx" =>$idx,
                                "obj" =>$obj,
                                "title" =>$sousformulaire_parameters[$elem]["title"]
                            );
                        } else {
                             $params = array(
                                "elem" => $elem,
                                "href" => OM_ROUTE_SOUSTAB."&obj=".$params["elem"]."&retourformulaire=".$params["obj"]."&idxformulaire=".$params["idx"],
                                "idx" => $idx,
                                "obj" => $obj,
                                "title" => __($elem),
                            );
                        }

                        $this->layout->display_form_lien_onglet_accordion($params);
                        echo "<div id=\"sousform-$elem\">";
                        //
                        echo "</div>";
                        $this->layout->display_form_close_conteneur_chaque_onglet_accordion();
                        //
                    }
                    //
                    $this->layout->display_form_close_conteneur_onglets_accordion();
                }
            }
        }

        // Fermeture de la balise - Onglet 1
        echo "\n\t</div>\n";

        // Fermeture de la balise - Conteneur d'onglets
        echo "</div>\n";
    }

    /**
     * VIEW - view_sousform.
     *
     * @return void
     */
    function view_sousform() {
        // Rétrocompatibilité : il est possible que dans les scripts inclus
        // par cette méthode, la variable $f soit attendue et utilisée.
        // @deprecated Cette variable ne doit plus être utilisée.
        $f = $this;

        // Initialisation des paramètres
        $params = array(
            // Nom de l'objet metier du formulaire
            "obj" => array(
                "default_value" => "",
            ),
            // Flag de validation du formulaire
            "validation" => array(
                "default_value" => 0,
            ),
            // Objet du formulaire parent (form.php?obj=)
            "retourformulaire" => array(
                "default_value" => 0,
            ),
            // Identifiant de l'objet du formulaire parent (form.php?idx=)
            "idxformulaire" => array(
                "default_value" => "",
            ),
            // Origine de l'action
            "retour" => array(
                "default_value" => "",
            ),
            // Premier enregistrement a afficher sur le tableau de la page precedente (soustab.php?premier=)
            "premiersf" => array(
                "default_value" => 0,
            ),
            // Colonne choisie pour le tri sur le tableau de la page precedente (soustab.php?tricol=)
            "trisf" => array(
                "var_name" => "tricolsf",
                "default_value" => "",
            ),
            // Valilite des objets a afficher sur le tableau de la page precedente (soustab.php?valide=)
            "valide" => array(
                "default_value" => "",
            ),
            // Paramètre de recherche a afficher sur le tableau de la page precedente (soustab.php?advs_id=)
            "advs_id" => array(
                "default_value" => "",
            ),
            //
            "contentonly" => array(
                "default_value" => null,
            ),
            "idx" => array(
                "default_value" => "]",
            ),
            "action" => array(
                "var_name" => "maj",
                "default_value" => 0,
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        // ???
        $typeformulaire = "";

        // Ce tableau permet a chaque application de definir des variables
        // supplementaires qui seront passees a l'objet metier dans le constructeur
        // a travers ce tableau
        // Voir le fichier dyn/sousform.get.specific.inc.php pour plus d'informations
        $extra_parameters = array();
        if (file_exists("../dyn/sousform.get.specific.inc.php")) {
            require "../dyn/sousform.get.specific.inc.php";
        }

        //
        $standard_script_path = "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $core_script_path = PATH_OPENMAIRIE."sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $gen_script_path = "../gen/sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";

        /**
         * Verification des parametres
         */
        if (strpos($obj, "/") !== false
            || (file_exists($standard_script_path) === false
                && file_exists($core_script_path) === false
                && file_exists($gen_script_path) === false)) {
            if ($this->isAjaxRequest() == false) {
                $this->setFlag(null);
                $this->display();
            }
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->displayMessage($class, $message);
            die();
        }

        // Dictionnaire des actions
        // ------------------------

        // Declaration du dictionnaire
        $portlet_actions = array();
        if ($maj == 3) {
            // Action : modifier
            $portlet_actions['modifier'] = array(
                'lien' => OM_ROUTE_SOUSFORM.'&obj='.$obj.'&amp;action=1'.'&amp;idx=',
                'id' => '&amp;premiersf='.$premiersf.'&amp;trisf='.$tricolsf.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=form',
                'lib' => '<span class="om-prev-icon om-icon-16 edit-16">'.__('Modifier').'</span>',
                'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
                'ordre' => 10,
                'description' => __('Modifier'),
            );
            // Action : supprimer
            $portlet_actions['supprimer'] = array(
                'lien' => OM_ROUTE_SOUSFORM.'&obj='.$obj.'&amp;action=2&amp;idx=',
                'id' => '&amp;premiersf='.$premiersf.'&amp;trisf='.$tricolsf.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=form',
                'lib' => '<span class="om-prev-icon om-icon-16 delete-16">'.__('Supprimer').'</span>',
                'rights' => array('list' => array($obj, $obj.'_supprimer'), 'operator' => 'OR'),
                'ordre' => 20,
                'description' => __('Supprimer'),
            );
        }

        // surcharge globale
        if (file_exists('../dyn/sousform.inc.php')) {
            require_once '../dyn/sousform.inc.php';
        }

        // Inclusion du script [sql/<OM_DB_PHPTYPE>/<OBJ>.inc.php]
        $custom_script_path = $this->get_custom("soustab", $obj);
        if ($custom_script_path !== null) {
            require_once $custom_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($core_script_path) === true) {
            require_once $core_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($gen_script_path) === true) {
            require_once $gen_script_path;
        } elseif (file_exists($standard_script_path) === true) {
            require_once $standard_script_path;
        }

        // Inclusion de la classe objet
        $enr = $this->get_inst__om_dbform(array(
            "obj" => $obj,
            "idx" => $idx,
        ));
        if ($enr === null) {
            if ($this->isAjaxRequest() == false) {
                $this->setFlag(null);
                $this->display();
            }
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->displayMessage($class, $message);
            die();
        }
        // Incrementation du compteur de validation du formulaire
        $validation++;
        // Enclenchement de la tamporisation de sortie
        ob_start();

        // Affectation des parametres dans un tableau associatif pour le
        // stocker en attribut de l'objet
        $parameters = array(
            "validation" => $validation,
            "maj" => $maj,
            "idx" => $idx,
            "idxformulaire" => $idxformulaire,
            "retour" => $retour,
            "retourformulaire" => $retourformulaire,
            "typeformulaire" => $typeformulaire,
            "objsf" => $obj,
            "actions" => $portlet_actions,
            "postvar" => $_POST,
            // Variables de soustab.php à conserver pour conserver les
            // paramètres d'affichage du listing (pagination, tri, validité)
            "premiersf" => $premiersf,
            "tricolsf" => $tricolsf,
            "valide" => $valide,
            "advs_id" => $advs_id,
        );
        // Affectation du tableau precedant dans l'attribut 'parameters'
        $enr->setParameters($parameters);
        // Affectation du tableau passe en parametre dans l'attribut 'parameters'
        $enr->setParameters($extra_parameters);

        /**
         * Affichage de la structure de la page
         */
        // Verification des credentials de l'utilisateur
        $right_suffix = "_";
        switch ($maj) {
            case "0":
                $right_suffix .= "ajouter";
                break;
            case "1":
                $right_suffix .= "modifier";
                break;
            case "2":
                $right_suffix .= "supprimer";
                break;
            case "3":
                $right_suffix .= "consulter";
                break;
            default:
                if ($enr->is_option_class_action_activated() === true) {
                    $right_suffix .= $enr->get_action_param($maj, "permission_suffix");
                }
                break;
        }
        $this->isAuthorized(array($obj.$right_suffix, $obj), "OR");

        //
        if ($enr->is_option_class_action_activated()===true) {
            //
            $view_parameter = $enr->get_action_param($maj, 'view');
            //
            if ($view_parameter == "formulaire") {
                $view_parameter = "sousformulaire";
            }
            //
            if (method_exists($enr, $view_parameter)) {
                $enr->$view_parameter();
            } else {
                $enr->sousformulaire();
            }
        } else {
            $enr->sousformulaire();
        }

        // Affecte le contenu courant du tampon de sortie a $return puis l'efface
        $return = ob_get_clean();

        // Récupère le fil d'Ariane
        $ent = $enr->getSubFormTitle($ent);

        //
        if ($this->isAjaxRequest()) {
            //
            header("Content-type: text/html; charset=".HTTPCHARSET."");
            //
            if ($contentonly !== null) {
                // Affichage du retour de la methode formulaire
                echo $return;
                //
                die();
            }
            //
            $this->displaySubTitle($ent);
        } else {
            // Affichage du titre
            $this->setTitle($ent);
            // Affichage des elements
            $this->setFlag(null);
            $this->display();
        }

        /**
         *
         */
        //
        echo "\n<div id=\"sformulaire\">\n";

        // Affichage du retour de la methode formulaire
        echo "<div id=\"sousform-message\">";
        echo "<!-- -->";
        echo "</div>";
        echo "<div id=\"sousform-container\">";
        echo $return;
        echo "</div>";
        $real_href = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://":"http://").$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        echo "<div id=\"sousform-real-href\" data-href=\"".$real_href."\">";
        echo "<!-- -->";
        echo "</div>";

        //
        echo "</div>";
    }

    /**
     * VIEW - view_tab.
     *
     * @return void
     */
    function view_tab() {
        // Rétrocompatibilité : il est possible que dans les scripts inclus
        // par cette méthode, la variable $f soit attendue et utilisée.
        // @deprecated Cette variable ne doit plus être utilisée.
        $f = $this;

        // Initialisation des paramètres
        $params = array(
            // Nom de l'objet metier
            "obj" => array(
                "default_value" => "",
            ),
            // Premier enregistrement a afficher
            "premier" => array(
                "default_value" => 0,
            ),
            // Colonne choisie pour le tri
            "tricol" => array(
                "default_value" => "",
            ),
            // Id unique de la recherche avancee
            "advs_id" => array(
                "default_value" => "",
            ),
            // Valilite des objets a afficher
            "valide" => array(
                "default_value" => "",
            ),
            "contentonly" => array(
                "default_value" => null,
            ),
            "mode" => array(
                "default_value" => null,

            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        //
        $standard_script_path = "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $core_script_path = PATH_OPENMAIRIE."sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $gen_script_path = "../gen/sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";

        /**
         * Verification des parametres
         */
        if (strpos($obj, "/") !== false
            || (file_exists($standard_script_path) === false
                && file_exists($core_script_path) === false
                && file_exists($gen_script_path) === false)) {
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->addToMessage($class, $message);
            $this->setFlag(null);
            $this->display();
            die();
        }

        // Liste des options
        // -----------------

        if (!isset($options)) {
            $options = array();
        }

        // Dictionnaire des actions
        // ------------------------

        // Declaration du dictionnaire
        $tab_actions = array(
            'corner' => array(),
            'left' => array(),
            'content' => array(),
            'specific_content' => array(),
        );

        // Actions en coin : ajouter
        $tab_actions['corner']['ajouter'] = array(
            'lien' => OM_ROUTE_FORM.'&obj='.$obj.'&amp;action=0',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premier='.$premier.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=form',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix add-16" title="'.__('Ajouter').'">'.__('Ajouter').'</span>',
            'rights' => array('list' => array($obj, $obj.'_ajouter'), 'operator' => 'OR'),
            'ordre' => 10,
        );

        // Actions a gauche : consulter
        $tab_actions['left']['consulter'] = array(
            'lien' => OM_ROUTE_FORM.'&obj='.$obj.'&amp;action=3&amp;idx=',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premier='.$premier.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=tab',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix consult-16" title="'.__('Consulter').'">'.__('Consulter').'</span>',
            'rights' => array('list' => array($obj, $obj.'_consulter'), 'operator' => 'OR'),
            'ordre' => 10,
        );

        // Actions a gauche : modifier
        /*
        $tab_actions['left']['modifier'] = array(
            'lien' => OM_ROUTE_FORM.'&obj='.$obj.'&amp;action=1&amp;idx=',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premier='.$premier.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=tab',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix edit-16" title="'.__('Modifier').'">'.__('Modifier').'</span>',
            'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
            'ordre' => 20,
        );
        */

        // Actions a gauche : supprimer
        /*
        $tab_actions['left']['supprimer'] = array(
            'lien' => OM_ROUTE_FORM.'&obj='.$obj.'&amp;action=2&amp;idx=',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premier='.$premier.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=tab',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix delete-16" title="'.__('Supprimer').'">'.__('Supprimer').'</span>',
            'rights' => array('list' => array($obj, $obj.'_supprimer'), 'operator' => 'OR'),
            'ordre' => 30,
        );
        */

        // Action du contenu : consulter
        $tab_actions['content'] = $tab_actions['left']['consulter'];

        // Ce tableau permet a chaque application de definir des variables
        // supplementaires qui seront passees a l'objet metier dans le constructeur
        // a travers ce tableau
        // Voir le fichier dyn/form.get.specific.inc.php pour plus d'informations
        $extra_parameters = array();

        // surcharge globale
        if (file_exists('../dyn/tab.inc.php')) {
            require_once '../dyn/tab.inc.php';
        }

        // Inclusion du script [sql/<OM_DB_PHPTYPE>/<OBJ>.inc.php]
        $custom_script_path = $this->get_custom("tab", $obj);
        if ($custom_script_path !== null) {
            require_once $custom_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($core_script_path) === true) {
            require_once $core_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($gen_script_path) === true) {
            require_once $gen_script_path;
        } elseif (file_exists($standard_script_path) === true) {
            require_once $standard_script_path;
        }

        // Éventuelle surcharge si export CSV spécifique
        if ($mode === "export_csv"
            && file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".export_csv.inc.php")) {
            include "../sql/".OM_DB_PHPTYPE."/".$obj.".export_csv.inc.php";
        }

        /**
         * Titre de l'onglet à afficher, soit une variable est explicitement définie
         * dans le fichier de paramétrage soit on utilise la traduction de l'objet
         * du tableau.
         */
        //
        if (isset($tab_title)) {
            //
            $display_tab_title = $tab_title;
        } else {
            //
            $display_tab_title = __($obj);
        }

        /**
         *
         */
        //
        if (isset($edition) && $edition != ""
            && (file_exists("../sql/".OM_DB_PHPTYPE."/".$edition.".pdf.inc")
                || file_exists("../sql/".OM_DB_PHPTYPE."/".$edition.".pdf.inc.php"))) {
            $edition = OM_ROUTE_MODULE_EDITION."&obj=".$edition;
        } else {
            $edition = "";
        }

        /**
         *
         */
        //
        if (!isset($om_validite) or $om_validite != true) {
            $om_validite = false;
        }

        /**
         *
         */
        //
        if (!isset($options)) {
            $options = array();
        }

        /**
         *
         */
        $tb = $this->get_inst__om_table(array(
            "aff" => OM_ROUTE_TAB,
            "table" => $table,
            "serie" => $serie,
            "champAffiche" => $champAffiche,
            "champRecherche" => $champRecherche,
            "tri" => $tri,
            "selection" => $selection,
            "edition" => $edition,
            "options" => $options,
            "advs_id" => $advs_id,
            "om_validite" => $om_validite,
        ));

        /**
         *
         */
        // Affectation des parametres
        $params = array(
            "obj" => $obj,
            "premier" => $premier,
            "tricol" => $tricol,
            "valide" => $valide,
            "advs_id" => $advs_id,
        );
        // Ajout de paramètre spécifique
        $params = array_merge($params, $extra_parameters);

        /**
         *
         */
        if ($mode === "export_csv") {

            /**
             *
             */
            // Nom du fichier
            $filename = $obj."-".date("d-m-Y");
            //
            $this->isAuthorized(array($obj, $obj."_exporter"), "OR");
            $this->disableLog();

            /**
             *
             */
            //
            $tb->setParams($params);
            // Methode permettant de definir si la recherche doit etre faite
            // sur la recherche simple ou avncee
            $tb->composeSearchTab();
            // Generation de la requete de recherche
            $tb->composeQuery();
            // Exécution de la requête
            $res = $this->db->query($tb->sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$tb->sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->isDatabaseError($res);
            //
            $nbligne = $res->numrows();
            //
            if ($nbligne > 0) {
                //OUPUT HEADERS
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                header("Content-Type: application/csv");
                header("Content-Disposition: attachment; filename=\"$filename.csv\";" );
                header("Content-Transfer-Encoding: binary");
                $header=true;
                // Ouverture du flux de sortie
                $out = fopen('php://output', 'w');
                // Formatage de chaque ligne pour csv
                while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                    if ($header) {
                        fputcsv($out, array_keys($row), ';', '"');
                        $header = false;
                    }
                    fputcsv($out, $row, ';', '"');
                }
                // Affichage de la sortie standard
                readfile('php://output');
                // Fermeture de la sortie
                fclose($out);
            } else {
                //
                header('Location: '.OM_ROUTE_TAB.'&obj='.$obj.
                       '&premier='.$premier.
                       '&tricol='.$tricol.
                       '&advs_id='.$advs_id.
                       '&valide='.$valide
                       );
            }
        } elseif ($mode === "export_sig") {

            /**
             *
             */
            //
            $this->isAuthorized(array($obj, $obj."_exporter"), "OR");
            $this->disableLog();

            /**
             *
             */
            //
            $tb->setParams($params);
            // Methode permettant de definir si la recherche doit etre faite
            // sur la recherche simple ou avncee
            $tb->composeSearchTab();
            // Generation de la requete de recherche
            $tb->composeQuery();
            // Exécution de la requête
            $res = $this->db->query($tb->sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$tb->sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->isDatabaseError($res);
            //
            $nbligne = $res->numrows();
            //
            if ($nbligne > 0) {
                //+++++++
                $popup=1;
                //++++
                 header('Location: '.OM_ROUTE_MAP.'&mode=tab_sig&obj='.$obj.
                   '&premier='.$premier.
                   '&tricol='.$tricol.
                   '&advs_id='.$advs_id.
                   '&valide='.$valide.
                   '&popup='.$popup
                   );
            } else {
                //
                header('Location: '.OM_ROUTE_TAB.'&obj='.$obj);
            }
        } elseif ($mode !== null) {
            //
            $mode = str_replace('export_', '', $mode);
            // Dans le cas où un lien de redirection spécifique est défini
            // dans le fichier inc.php
            foreach ($options as $option) {
                // Récupération de la configuration d'export
                if (key_exists("export", $option)) {
                    $export = $option['export'];
                }
            }
            // Si le .inc.php contient un paramétrage de droits spécifique
            if (isset($export[$mode]['right'])) {
                $rights = array($obj, $export[$mode]['right']);
            } else {
                $rights = array($obj, $obj."_exporter");
            }
            $this->isAuthorized($rights, "OR");

            // Si un URL de redirection est défini
            if (isset($export[$mode]['url']) and $export[$mode]['url'] !== '') {
                //
                $sig_redirect_url = $export[$mode]['url'];
                // S'il n'y a pas eu de recherche avancée envoyée
                if ($advs_id === '') {
                    header('Location: '.$sig_redirect_url);
                    exit();
                }
                header('Location: '.$sig_redirect_url.
                       '&premier='.$premier.
                       '&tricol='.$tricol.
                       '&advs_id='.$advs_id.
                       '&valide='.$valide
                );
                exit();
            } else {
                //
                header('Location: '.OM_ROUTE_TAB.'&obj='.$obj);
            }
        } else {
            if ($this->isAjaxRequest()) {
                //
                header("Content-type: text/html; charset=".HTTPCHARSET."");
                //
                if ($contentonly !== null) {
                    // Affichage du tableau
                    $tb->display($params, $tab_actions, $this->db, "tab", false);
                    //
                    die();
                }
            }
            /**
             *
             */
            //
            $this->isAuthorized(array($obj."_tab", $obj), "OR");
            //
            $this->setTitle($ent);
            //
            $this->setFlag(null);
            $this->display();

            /**
             * Affichage d'une description en dessous du titre de la page
             */
            //
            if (isset($tab_description)) {
                //
                $this->displayDescription($tab_description);
            }

            /**
             *
             */
            //
            echo "<div id=\"formulaire\">\n\n";
            $this->layout->display_tab_lien_onglet_un($display_tab_title);
            echo "\n<div id=\"tabs-1\">\n";
            echo "\n<div id=\"tab-".$obj."\">\n";
            echo "\n<div class=\"tab-message\">";
            $this->handle_and_display_session_message();
            echo "</div>\n";
            echo "\n<div class=\"tab-container\">";
            $tb->display($params, $tab_actions, $this->db, "tab", false);
            echo "\n</div>\n";
            echo "\n</div>\n";
            echo "\n</div>\n";
            echo "\n</div>\n";
        }
    }

    /**
     * VIEW - view_soustab.
     *
     * @return void
     */
    function view_soustab() {
        // Rétrocompatibilité : il est possible que dans les scripts inclus
        // par cette méthode, la variable $f soit attendue et utilisée.
        // @deprecated Cette variable ne doit plus être utilisée.
        $f = $this;

        /**
         * Definition du charset de la page
         */
        header("Content-type: text/html; charset=".HTTPCHARSET."");

        // Initialisation des paramètres
        $params = array(
            // Nom de l'objet metier du tableau
            "obj" => array(
                "default_value" => "",
            ),
            // Premier enregistrement a afficher dans le tableau
            "premier" => array(
                "default_value" => 0,
            ),
            // Colonne choisie pour le tri dans le tableau
            "tricol" => array(
                "default_value" => "",
            ),
            // Valilite des objets a afficher
            "valide" => array(
                "default_value" => "",
            ),
            // Id unique de la recherche
            "advs_id" => array(
                "default_value" => "",
            ),
            // Objet du formulaire parent (form.php?obj=)
            "retourformulaire" => array(
                "default_value" => "",
            ),
            // Identifiant de l'objet du formulaire parent (form.php?idx=)
            "idxformulaire" => array(
                "default_value" => "",
            ),
            //
            "contentonly" => array(
                "default_value" => null,
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        // @deprecated Cette affectation sera supprimée dans la version 4.6.0.
        // Il y a sinon confusion entre les deux variables qui sont bien distinctes.
        $idx = $idxformulaire;

        //
        $standard_script_path = "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $core_script_path = PATH_OPENMAIRIE."sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
        $gen_script_path = "../gen/sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";

        /**
         * Verification des parametres
         */
        if (strpos($obj, "/") !== false
            || $idxformulaire == ""
            || $retourformulaire == ""
            || (file_exists($standard_script_path) === false
                && file_exists($core_script_path) === false
                && file_exists($gen_script_path) === false)) {
            //
            if ($this->isAjaxRequest() == false) {
                $this->setFlag(null);
                $this->display();
            }
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->displayMessage($class, $message);
            die();
        }

        // Liste des options
        // -----------------

        if (!isset($options)) {
            $options = array();
        }

        // Dictionnaire des actions
        // ------------------------

        // Declaration du dictionnaire
        $tab_actions = array(
            'corner' => array(),
            'left' => array(),
            'content' => array(),
            'specific_content' => array(),
        );

        // Actions en coin : ajouter
        $tab_actions['corner']['ajouter'] = array(
            'lien' => OM_ROUTE_SOUSFORM.'&obj='.$obj.'&amp;action=0',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premiersf='.$premier.'&amp;trisf='.$tricol.'&amp;valide='.$valide.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=form',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix add-16" title="'.__('Ajouter').'">'.__('Ajouter').'</span>',
            'rights' => array('list' => array($obj, $obj.'_ajouter'), 'operator' => 'OR'),
            'ordre' => 10,
        );

        // Actions a gauche : consulter
        $tab_actions['left']['consulter'] = array(
            'lien' => OM_ROUTE_SOUSFORM.'&obj='.$obj.'&amp;action=3&amp;idx=',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premiersf='.$premier.'&amp;trisf='.$tricol.'&amp;valide='.$valide.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=tab',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix consult-16" title="'.__('Consulter').'">'.__('Consulter').'</span>',
            'rights' => array('list' => array($obj, $obj.'_consulter'), 'operator' => 'OR'),
            'ordre' => 10,
        );

        // Actions a gauche : modifier
        /*
        $tab_actions['left']['modifier'] = array(
            'lien' => OM_ROUTE_SOUSFORM.'&obj='.$obj.'&amp;action=1&amp;idx=',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premiersf='.$premier.'&amp;trisf='.$tricol.'&amp;valide='.$valide.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=tab',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix edit-16" title="'.__('Modifier').'">'.__('Modifier').'</span>',
            'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
            'ordre' => 20,
        );
        */

        // Actions a gauche : supprimer
        /*
        $tab_actions['left']['supprimer'] = array(
            'lien' => OM_ROUTE_SOUSFORM.'&obj='.$obj.'&amp;action=2&amp;idx=',
            'id' => '&amp;advs_id='.$advs_id.'&amp;premiersf='.$premier.'&amp;trisf='.$tricol.'&amp;valide='.$valide.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=tab',
            'lib' => '<span class="om-icon om-icon-16 om-icon-fix delete-16" title="'.__('Supprimer').'">'.__('Supprimer').'</span>',
            'rights' => array('list' => array($obj, $obj.'_supprimer'), 'operator' => 'OR'),
            'ordre' => 30,
        );
        */

        // Action du contenu : consulter
        $tab_actions['content'] = $tab_actions['left']['consulter'];

        // Ce tableau permet a chaque application de definir des variables
        // supplementaires qui seront passees a l'objet metier dans le constructeur
        // a travers ce tableau
        // Voir le fichier dyn/form.get.specific.inc.php pour plus d'informations
        $extra_parameters = array();

        // surcharge globale
        if (file_exists('../dyn/soustab.inc.php')) {
            require_once '../dyn/soustab.inc.php';
        }

        // Inclusion du script [sql/<OM_DB_PHPTYPE>/<OBJ>.inc.php]
        $custom_script_path = $this->get_custom("soustab", $obj);
        if ($custom_script_path !== null) {
            require_once $custom_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($core_script_path) === true) {
            require_once $core_script_path;
        } elseif (file_exists($standard_script_path) === false
            && file_exists($gen_script_path) === true) {
            require_once $gen_script_path;
        } elseif (file_exists($standard_script_path) === true) {
            require_once $standard_script_path;
        }

        /**
         *
         */
        //
        $this->isAuthorized(array($obj."_tab", $obj), "OR");

        /**
         *
         */
        //
        if (!isset($om_validite) or $om_validite != true) {
            $om_validite = false;
        }

        /**
         *
         */
        //
        if (!isset($options)) {
            $options = array();
        }

        /**
         *
         */
        $tb = $this->get_inst__om_table(array(
            "aff" => OM_ROUTE_SOUSTAB,
            "table" => $table,
            "serie" => $serie,
            "champAffiche" => $champAffiche,
            "champRecherche" => $champRecherche,
            "tri" => $tri,
            "selection" => $selection,
            "edition" => $edition,
            "options" => $options,
            "advs_id" => $advs_id,
            "om_validite" => $om_validite,
        ));
        //
        $params = array(
            "obj" => $obj,
            //
            "retourformulaire" => $retourformulaire,
            "idxformulaire" => $idxformulaire,
            //
            "premier" => $premier,
            "tricol" => $tricol,
            "valide" => $valide,
            "advs_id" => $advs_id,
        );
        // Ajout de paramètre spécifique
        $params = array_merge($params, $extra_parameters);
        if ($this->isAjaxRequest()) {
            //
            header("Content-type: text/html; charset=".HTTPCHARSET."");
            //
            if ($contentonly !== null) {
                // Affichage du tableau
                $tb->display($params, $tab_actions, $this->db, "tab", true);
                //
                die();
            }
        }

        /**
         *
         */

        //
        echo '<div id="sousform-href"><!-- --></div>';
        echo '<div id="sousform-'.$obj.'">';
        echo '<div class="soustab-message">';
        $this->handle_and_display_session_message();
        echo '</div>';
        echo '<div class="soustab-container">';
        $tb->display($params, $tab_actions, $this->db, "tab", true);
        echo '</div>';
        echo '</div>';
    }

    /**
     * Gère un éventuel message de session.
     *
     * Si le paramètre message_id est passé en GET et que sa valeur
     * correspond à un message existant dans le session de l'utilisateur.
     * Alors on affiche le message et on le supprime de la session.
     *
     * @return void
     */
    function handle_and_display_session_message() {
        $message_id = $this->get_submitted_get_value("message_id");
        if (isset($_SESSION["messages"]) === true
            && is_array($_SESSION["messages"]) === true
            && in_array($message_id, array_keys($_SESSION["messages"]))) {
            //
            $this->layout->display_message(
                "valid",
                $_SESSION["messages"][$message_id]
            );
            unset($_SESSION["messages"][$message_id]);
        }
    }

    /**
     * Ajoute un message de session et retourne son identifant.
     *
     * @param string $message Message à stocker.
     *
     * @return string
     */
    function add_session_message($message) {
        $uid = md5(uniqid(time()));
        if (isset($_SESSION["messages"]) !== true) {
            $_SESSION["messages"] = array();
        }
        $_SESSION["messages"][$uid] = $message;
        return $uid;
    }

    /**
     * VIEW - view_map.
     *
     * @return void
     */
    function view_map() {
        //
        $this->disableLog();
        //
        $modes_available = array(
            "form_sig",
            "tab_sig",
            "compute_geom",
            "get_filters",
            "get_geojson_cart",
            "get_geojson_datas",
            "get_geojson_markers",
            "redirection_onglet",
            "session",
        );
        $mode = $this->get_submitted_get_value("mode");
        if (in_array($mode, $modes_available) !== true) {
            $mode = "tab_sig";
        }

        //
        if ($mode === "redirection_onglet") {
            //
            $params = array(
                "obj" => array(
                    "default_value" => "",
                ),
                "idx" => array(
                    "default_value" => "",
                ),
            );
            foreach ($this->get_initialized_parameters($params) as $key => $value) {
                ${$key} = $value;
            }
            //
            if (is_null($idx) || is_null($obj)) {
                return;
            }
            //
            $this->isAuthorized($obj);
            $idx = explode('?', $idx);
            $idx = $idx[0];
            //
            printf(
                '<div id="sousform-%1$s"><script type="text/javascript">ajaxIt(\'%1$s\', \'%2$s\', 1);</script></div>',
                $obj,
                OM_ROUTE_SOUSFORM.'&objsf='.$obj.'&idxformulaire='.$idx.'&retourformulaire='.$obj.'&obj='.$obj.'&action=3&idx='.$idx
            );
            return;
        }

        //
        if ($mode === "session") {
            //
            $obj = $this->db->escapeSimple($_POST['obj']);
            $zoom = $this->db->escapeSimple($_POST['zoom']);
            $base = $this->db->escapeSimple($_POST['base']);
            if (isset($_POST['visibility'])) {
                $visibility = $_POST['visibility'];
            } else {
                $visibility = null;
            }

            if (isset($_POST['seli'])) {
                $seli = $this->db->escapeSimple($_POST['seli']);
            } else {
                $seli = 0;
            }
            $_SESSION['map_'.$obj] = array(
                "zoom" => $zoom,
                "base" => $base,
                "seli" => $seli,
                "visibility" => $visibility,
            );
            $result = 'ok';
            echo $result;
            return;
        }

        //
        $params = array(
            "obj" => array(
                "default_value" => "",
            ),
            "idx_sel" => array(
                "default_value" => "",
            ),
            "lst" => array(
                "default_value" => "",
            ),
            "cart" => array(
                "default_value" => "",
            ),
            "validation" => array(
                "default_value" => 0,
            ),
            "seli" => array(
                "default_value" => 0,
                "not_accepted_values" => array("", ),
            ),
            "min" => array(
                "default_value" => 0,
                "not_accepted_values" => array("", ),
            ),
            "max" => array(
                "default_value" => 0,
                "not_accepted_values" => array("", ),
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        //
        $options = array();
        $params = array(
            "etendue" => array(),
            "reqmo" => array(),
            "premier" => array(),
            "tricol" => array(),
            "advs_id" => array(),
            "valide" => array(),
            "style" => array(),
            "onglet" => array(),
            "idx" => array(
                "default_value" => null,
            ),
            "seli" => array(
                "not_accepted_values" => array("", ),
            ),
            "popup" => array(
                "not_accepted_values" => array("", ),
            ),
        );
        foreach ($this->get_initialized_parameters($params) as $key => $value) {
            $options[$key] = $value;
        }

        //
        $om_map = $this->get_inst__om_map(array(
            "obj" => $obj,
            "options" => $options,
        ));
        //
        $om_map->recupOmSigMap();
        switch ($mode) {
            case 'compute_geom':
                //
                $geojson = explode("#", str_replace("\'", "'", $_POST['geojson']));
                $sep = "";
                $i = 0;
                for ($c = $min; $c <= $max; $c++) {
                    if ($geojson[$i] != '') {
                        echo $sep.$om_map->getComputeGeom($c, $geojson[$i]);
                    } else {
                        echo $sep.$geojson[$i];
                    }
                    $sep="#";
                    $i=$i+1;
                }
                break;
            case 'get_filters':
                //
                $om_map->recupOmSigflux();
                $om_map->computeFilters($idx_sel);
                if (is_array($om_map->fl_m_filter)) {
                    $i = 0;
                    foreach ($om_map->fl_m_filter as $item) {
                        echo str_replace('²', '"', $item)."\n";
                        $i = $i + 1;
                    }
                }
                break;
            case 'get_geojson_cart':
                //
                $om_map->recupOmSigflux();
                $lst = $om_map->getGeoJsonCart($cart, $lst);
                if (is_array($lst)) {
                    foreach ($lst as $item) {
                        echo $item;
                    }
                }
                break;
            case 'get_geojson_datas':
                //
                $lst = $om_map->getGeoJsonDatas($options['idx'], $seli);
                if (is_array($lst)) {
                    foreach ($lst as $item) {
                        echo $item;
                    }
                }
                break;
            case 'get_geojson_markers':
                //
                $lst = $om_map->getGeoJsonMarkers($options['idx']);
                if (is_array($lst)) {
                    foreach ($lst as $item) {
                        echo $item;
                    }
                }
                break;
            case 'form_sig':
                //
                $geojson = array();
                if ($validation == 0) {
                    $geojson_temp = explode("#", str_replace("\'", "'", $_POST['geojson']));
                    $i = 0;
                    for ($c = 0; $c < count($om_map->cg_obj_class); $c++) {
                        if ($c >= $min && $c <= $max && $om_map->cg_maj[$c] == 't') {
                            array_push($geojson, $geojson_temp[$i]);
                            $i = $i + 1;
                        } else {
                            array_push($geojson, '');
                        }
                    }
                }
                if ($validation == 1) {
                    $i = 0;
                    for ($c = 0; $c < count($om_map->cg_obj_class); $c++) {
                        if ($c >= $min && $c <= $max && $om_map->cg_maj[$c]=='t') {
                            array_push($geojson, $_POST['geom'.$c]);
                            $i = $i + 1;
                        } else {
                            array_push($geojson, '');
                        }
                    }
                }
                //
                $om_map->prepareForm($min, $max, $validation, $geojson);
                break;
            case 'tab_sig':
                //
                $om_map->recupOmSigflux();
                $om_map->computeFilters($options['idx']);
                $om_map->setParamsExternalBaseLayer();
                $this->addHTMLHeadJs(array(
                    "../lib/openlayers/OpenLayers.js",
                    "../lib/om-assets/js/sig.js",
                    "../app/js/sig.js",
                ));
                if ($om_map->popup == 1) {
                    $this->setFlag("htmlonly_nodoctype");
                } else {
                    $this->setFlag("nodoctype");
                }
                $this->display();
                //
                echo "  <div id='encaps-map'>\n";
                $om_map->prepareCanevas();
                echo "  </div>\n";
                break;
        }
        //
        $om_map->__destruct();
    }

    /**
     * VIEW - view_form_sig.
     *
     * @return void
     */
    function view_form_sig() {
        $_GET["mode"] = "form_sig";
        $this->view_map();
    }

    /**
     * VIEW - view_tab_sig.
     *
     * @return void
     */
    function view_tab_sig() {
        $_GET["mode"] = "tab_sig";
        $this->view_map();
    }

    // }}}


    // {{{

    /**
     * Permet de définir la configuration des liens du menu.
     *
     * @return void
     */
    protected function set_config__menu() {
        $menu = array();
        // {{{ Rubrique EXPORT
        //
        $rubrik = array(
            "title" => __("export"),
            "class" => "edition",
        );
        //
        $links = array();
        //
        $links[] = array(
            "href" => OM_ROUTE_MODULE_EDITION,
            "class" => "edition",
            "title" => __("edition"),
            "right" => "edition",
            "open" => array(
                "edition.php|",
                "index.php|[module=edition]",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_MODULE_REQMO,
            "class" => "reqmo",
            "title" => __("requetes memorisees"),
            "right" => "reqmo",
            "open" => array(
                "reqmo.php|",
                "index.php|[module=reqmo]",
            ),
        );
        //
        $rubrik['links'] = $links;
        //
        $menu["om-menu-rubrik-export"] = $rubrik;
        // }}}

        // {{{ Rubrique PARAMETRAGE
        //
        $rubrik = array(
            "title" => __("parametrage"),
            "class" => "parametrage",
            "right" => "menu_parametrage",
            "parameters" => array("is_settings_view_enabled" => false, ),
        );
        //
        $links = array();
        //
        $links[] = array(
            "class" => "category",
            "title" => __("editions"),
            "right" => array(
                "om_etat", "om_etat_tab", "om_sousetat", "om_sousetat_tab",
                "om_lettretype", "om_lettretype_tab", "om_requete", "om_requete_tab",
                "om_logo", "om_logo_tab",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array(
                "om_etat", "om_etat_tab", "om_lettretype", "om_lettretype_tab",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_etat",
            "class" => "om_etat",
            "title" => __("om_etat"),
            "description" => __("Composition des états."),
            "right" => array("om_etat", "om_etat_tab", ),
            "open" => array(
                "tab.php|om_etat",
                "index.php|om_etat[module=tab]",
                "form.php|om_etat",
                "index.php|om_etat[module=form]",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_lettretype",
            "class" => "om_lettretype",
            "title" => __("om_lettretype"),
            "description" => __("Composition des lettres types."),
            "right" => array("om_lettretype", "om_lettretype_tab"),
            "open" => array(
                "tab.php|om_lettretype",
                "index.php|om_lettretype[module=tab]",
                "form.php|om_lettretype",
                "index.php|om_lettretype[module=form]",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array(
                "om_logo", "om_logo_tab",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_logo",
            "class" => "om_logo",
            "title" => __("om_logo"),
            "description" => __("Paramétrage des logos disponibles depuis l'écran de composition des lettres types."),
            "right" => array("om_logo", "om_logo_tab", ),
            "open" => array(
                "tab.php|om_logo",
                "index.php|om_logo[module=tab]",
                "form.php|om_logo",
                "index.php|om_logo[module=form]",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array(
                "om_sousetat", "om_sousetat_tab",
                "om_requete", "om_requete_tab",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_sousetat",
            "class" => "om_sousetat",
            "title" => __("om_sousetat"),
            "description" => __("Paramétrage des tableaux (appelés sous-états) disponibles à l'insertion depuis l'écran de composition des lettres types."),
            "right" => array("om_sousetat", "om_sousetat_tab", ),
            "open" => array(
                "tab.php|om_sousetat",
                "index.php|om_sousetat[module=tab]",
                "form.php|om_sousetat",
                "index.php|om_sousetat[module=form]",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_requete",
            "class" => "om_requete",
            "title" => __("om_requete"),
            "description" => __("Paramétrage des configurations de champs de fusion disponibles depuis l'écran de composition des lettres types."),
            "right" => array("om_requete", "om_requete_tab", ),
            "open" => array(
                "tab.php|om_requete",
                "index.php|om_requete[module=tab]",
                "form.php|om_requete",
                "index.php|om_requete[module=form]",
            ),
        );
        //
        $rubrik['links'] = $links;
        //
        $menu["om-menu-rubrik-parametrage"] = $rubrik;
        // }}}

        // {{{ Rubrique ADMINISTRATION
        //
        $rubrik = array(
            "title" => __("administration"),
            "class" => "administration",
            "right" => "menu_administration",
            "parameters" => array("is_settings_view_enabled" => false, ),
        );
        //
        $links = array();
        $links[] = array(
            "class" => "category",
            "title" => _("général"),
            "right" => array(
                "om_collectivite",
                "om_collectivite_tab",
                "om_parametre",
                "om_parametre_tab",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array(
                "om_collectivite",
                "om_collectivite_tab",
                "om_parametre",
                "om_parametre_tab",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_collectivite",
            "class" => "collectivite",
            "title" => __("om_collectivite"),
            "description" => __("Paramétrage des collectivités."),
            "right" => array("om_collectivite", "om_collectivite_tab", ),
            "open" => array(
                "tab.php|om_collectivite",
                "index.php|om_collectivite[module=tab]",
                "form.php|om_collectivite",
                "index.php|om_collectivite[module=form]",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_parametre",
            "class" => "parametre",
            "title" => __("om_parametre"),
            "description" => __("Divers paramètres de l'application : champs de fusion généraux disponibles pour les éditions pdf, activation/désactivation de modules complémentaires, paramétrages fonctionnels, ..."),
            "right" => array("om_parametre", "om_parametre_tab", ),
            "open" => array(
                "tab.php|om_parametre",
                "index.php|om_parametre[module=tab]",
                "form.php|om_parametre",
                "index.php|om_parametre[module=form]",
            ),
        );
        //
        $links[] = array(
            "class" => "category",
            "title" => __("gestion des utilisateurs"),
            "right" => array(
                "om_utilisateur", "om_utilisateur_tab", "om_profil", "om_profil_tab",
                "om_droit", "om_droit_tab", "om_utilisateur_synchroniser",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array(
                "om_utilisateur", "om_utilisateur_tab", "om_profil", "om_profil_tab",
                "om_droit", "om_droit_tab", "om_utilisateur_synchroniser",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_profil",
            "class" => "profil",
            "title" => __("om_profil"),
            "description" => __("Paramétrage des profils utilisateurs."),
            "right" => array("om_profil", "om_profil_tab", ),
            "open" => array(
                "tab.php|om_profil",
                "index.php|om_profil[module=tab]",
                "form.php|om_profil",
                "index.php|om_profil[module=form]",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_droit",
            "class" => "droit",
            "title" => __("om_droit"),
            "description" => __("Matrice des permissions affectées à chaque profil."),
            "right" => array("om_droit", "om_droit_tab", ),
            "open" => array(
                "tab.php|om_droit",
                "index.php|om_droit[module=tab]",
                "form.php|om_droit",
                "index.php|om_droit[module=form]",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_utilisateur",
            "class" => "utilisateur",
            "title" => __("om_utilisateur"),
            "description" => __("Paramétrage des utilisateurs autorisés à se connecter à l'application."),
            "right" => array("om_utilisateur", "om_utilisateur_tab", ),
            "open" => array(
                "tab.php|om_utilisateur",
                "form.php|om_utilisateur[action=0]",
                "form.php|om_utilisateur[action=1]",
                "form.php|om_utilisateur[action=2]",
                "form.php|om_utilisateur[action=3]",
                "index.php|om_utilisateur[module=tab]",
                "index.php|om_utilisateur[module=form][action=0]",
                "index.php|om_utilisateur[module=form][action=1]",
                "index.php|om_utilisateur[module=form][action=2]",
                "index.php|om_utilisateur[module=form][action=3]",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array("om_utilisateur", "om_utilisateur_synchroniser", ),
            "parameters" => array("isDirectoryOptionEnabled" => true, ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_FORM."&obj=om_utilisateur&idx=0&action=11",
            "class" => "annuaire",
            "title" => __("annuaire"),
            "description" => __("Interface de synchronisation des utilisateurs avec un annuaire."),
            "right" => array("om_utilisateur", "om_utilisateur_synchroniser", ),
            "open" => array(
                "form.php|om_utilisateur[action=11]",
                "index.php|om_utilisateur[module=form][action=11]",
            ),
            "parameters" => array("isDirectoryOptionEnabled" => true, ),
        );
        //
        $links[] = array(
            "class" => "category",
            "title" => __("tableaux de bord"),
            "right" => array(
                "om_widget", "om_widget_tab", "om_dashboard",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array(
                "om_widget", "om_widget_tab", "om_dashboard",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_widget",
            "class" => "om_widget",
            "title" => __("om_widget"),
            "description" => _("Paramétrage des blocs d'informations affichables sur le tableau de bord."),
            "right" => array("om_widget", "om_widget_tab", ),
            "open" => array(
                "tab.php|om_widget",
                "index.php|om_widget[module=tab]",
                "form.php|om_widget",
                "index.php|om_widget[module=form]",
            ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_FORM."&obj=om_dashboard&amp;idx=0&amp;action=4",
            "class" => "om_dashboard",
            "title" => __("composition"),
            "description" => __("Composition des tableaux de bord par profil."),
            "right" => array("om_dashboard", ),
            "open" => array(
                "tab.php|om_dashboard",
                "index.php|om_dashboard[module=tab]",
                "form.php|om_dashboard",
                "index.php|om_dashboard[module=form]",
            ),
        );
        //
        $links[] = array(
            "class" => "category",
            "title" => __("SIG"),
            "right" => array(
                "om_sig_map", "om_sig_map_tab", "om_sig_flux", "om_sig_flux_tab", "om_sig_extent", "om_sig_extent_tab",
            ),
            "parameters" => array("option_localisation" => "sig_interne", ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array(
                "om_sig_map", "om_sig_map_tab", "om_sig_flux", "om_sig_flux_tab", "om_sig_extent", "om_sig_extent_tab",
            ),
            "parameters" => array("option_localisation" => "sig_interne", ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_sig_extent",
            "class" => "om_sig_extent",
            "title" => __("étendue"),
            "description" => __("Paramétrage des étendues sélectionnables depuis une carte."),
            "right" => array("om_sig_extent", "om_sig_extent_tab", ),
            "open" => array(
                "tab.php|om_sig_extent",
                "index.php|om_sig_extent[module=tab]",
                "form.php|om_sig_extent",
                "index.php|om_sig_extent[module=form]",
            ),
            "parameters" => array("option_localisation" => "sig_interne", ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_sig_map",
            "class" => "om_sig_map",
            "title" => __("carte"),
            "description" => __("Paramétrage des cartes."),
            "right" => array("om_sig_map", "om_sig_map_tab", ),
            "open" => array(
                "tab.php|om_sig_map",
                "index.php|om_sig_map[module=tab]",
                "form.php|om_sig_map",
                "index.php|om_sig_map[module=form]",
            ),
            "parameters" => array("option_localisation" => "sig_interne", ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_TAB."&obj=om_sig_flux",
            "class" => "om_sig_flux",
            "title" => __("flux"),
            "description" => __("Paramétrage des flux sélectionnables depuis une carte."),
            "right" => array("om_sig_flux", "om_sig_flux_tab", ),
            "open" => array(
                "tab.php|om_sig_flux",
                "index.php|om_sig_flux[module=tab]",
                "form.php|om_sig_flux",
                "index.php|om_sig_flux[module=form]",
            ),
            "parameters" => array("option_localisation" => "sig_interne", ),
        );
        //
        $links[] = array(
            "class" => "category",
            "title" => __("options avancees"),
            "right" => array("import", "gen", ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array("import", ),
        );
        //
        $links[] = array(
            "href" => OM_ROUTE_MODULE_IMPORT,
            "class" => "import",
            "title" => __("import"),
            "description" => __("Ce module permet l'intégration de données dans l'application depuis des fichiers CSV."),
            "right" => array("import", ),
            "open" => array(
                "import.php|",
                "index.php|[module=import]",
            ),
        );
        //
        $links[] = array(
            "title" => "<hr/>",
            "right" => array("gen", ),
        );
        //
        $links[] = array(
            "title" => __("generateur"),
            "description" => __("Ce module permet la génération d'éléments à partir du modèle de données."),
            "href" => OM_ROUTE_MODULE_GEN,
            "class" => "generator",
            "right" => array("gen", ),
            "open" => array(
                "gen.php|",
                "index.php|[module=gen]",
            ),
        );
        //
        $rubrik['links'] = $links;
        //
        $menu["om-menu-rubrik-administration"] = $rubrik;
        // }}}
        $this->config__menu = $menu;
    }

    /**
     * Permet de définir la configuration des liens des actions.
     *
     * @return void
     */
    protected function set_config__actions() {
        $actions = array();
        // Mot de passe
        $actions[] = array(
            "title" => __("Mot de passe"),
            "description" => __("Changer votre mot de passe"),
            "href" => OM_ROUTE_PASSWORD,
            "class" => "actions-password",
            "right" => "password",
        );
        // Deconnexion
        $actions[] = array(
            "title" => __("Deconnexion"),
            "description" => __("Quitter l'application"),
            "href" => OM_ROUTE_LOGOUT,
            "class" => "actions-logout",
        );
        //
        $this->config__actions = $actions;
    }

    /**
     * Permet de définir la configuration des liens des shortlinks.
     *
     * @return void
     */
    protected function set_config__shortlinks() {
        $shortlinks = array();
        // Tableau de bord
        $shortlinks[] = array(
            "title" => __("Tableau de bord"),
            "description" => __("Acceder a la page d'accueil de l'application"),
            "href" => OM_ROUTE_DASHBOARD,
            "class" => "shortlinks-dashboard",
        );
        //
        $this->config__shortlinks = $shortlinks;
    }

    /**
     * Permet de définir la configuration des liens du footer.
     *
     * @return void
     */
    protected function set_config__footer() {
        $footer = array();
        // Documentation du site
        $footer[] = array(
            "title" => __("Documentation"),
            "description" => __("Acceder a l'espace documentation de l'application"),
            "href" => "http://docs.openmairie.org/?project=framework&version=4.9",
            "target" => "_blank",
            "class" => "footer-documentation",
        );

        // Forum openMairie
        $footer[] = array(
            "title" => __("Forum"),
            "description" => __("Espace d'échange ouvert du projet openMairie"),
            "href" => "https://communaute.openmairie.org/c/framework",
            "target" => "_blank",
            "class" => "footer-forum",
        );

        // Portail openMairie
        $footer[] = array(
            "title" => __("openMairie.org"),
            "description" => __("Site officiel du projet openMairie"),
            "href" => "http://www.openmairie.org/framework",
            "target" => "_blank",
            "class" => "footer-openmairie",
        );
        //
        $this->config__footer = $footer;
    }

    // }}}
}
