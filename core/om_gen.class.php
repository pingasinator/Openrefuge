<?php
/**
 * Ce script contient la définition de classe 'gen'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_gen.class.php 4348 2018-07-20 16:49:26Z softime $
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
 * Définition de la classe 'gen'.
 *
 * Cette classe gère le module 'Générateur' du framework openMairie. Ce module
 * permet la génération de code à partir du modèle de données, ainsi que divers
 * assistants de migration et de création d'éditions.
 *
 * @todo XXX remplacer les appels directs à logger par addtolog
 * @todo XXX remplacer les "@todo public" par l'attribut correct
 */
class gen extends om_base {

    /**
     * Nom de la table en cours de traitement
     * @var string
     */
    var $table = "";

    /**
     * Chaine de caractères stockant le message de retour du traitement pour
     * l'utilisateur
     * @todo public
     * @var string
     */
    var $msg = "";

    /**
     * Type de la colonne clé primaire de la table en cours de traitement
     * A : clé alphanumérique ou N : clé numérique
     * @var string
     */
    var $typecle = "";

    /**
     * Longueur de l'enregistrement de la table en cours de traitement
     * utilisée pour la largeur des colonnes dans la généréation des
     * pdf
     * @todo XXX Vérifier l'utilité de cet élément
     * @var integer
     */
    var $longueur = 0;

    /**
     * Description de la table.
     * @var array
     */
    var $info = array();

    /**
     * Liste des clés secondaires.
     * @var array
     */
    var $clesecondaire = array();

    /**
     * Liste des champs geom.
     * @var array
     */
    var $geom = array();

    /**
     * Liste des champs file.
     * @var array
     */
    var $_om_file_fields = array();

    /**
     * Liste des tables en sous formulaire.
     * @var array
     */
    var $sousformulaires = array();

    /**
     * Liste des tables de la base de données.
     * @var array
     */
    var $tablebase = array(); // tables de la base

    /**
     * Marqueur indiquant la présence de la colonne 'om_collectivite'
     * dans la table en cours de traitement.
     * - 1 : la colonne n'est pas présente
     * - 2 : la colonne n'est pas présente
     * @var integer
     */
    var $multi = 1;

    /**
     * Nom de la colonne clé primaire de la table en cours de traitement
     * @var string
     */
    var $primary_key;

    /**
     * Liste des cles etrangeres de la table actuelle.
     *
     * array_keys : noms des cles etrangeres.
     * array_values : tableaux d'informations sur les tables etrangeres.
     *
     * @var array(string)
     */
    var $foreign_tables = array();

    /**
     * Liste des couples "table.colonne" faisant reference a la table actuelle.
     *
     * array_keys : index numerique.
     * array_values : chaines de caracteres de la forme "table.colonne".
     *
     * @var array(string)
     */
    var $other_tables = array();

    /**
     * Liste des nom de colonnes avec contrainte unique.
     * @var array(string)
     */
    var $unique_key = array();

    /**
     * Liste des nom de colonnes avec contrainte unique multiple.
     * @var array(string)
     */
    var $unique_multiple_key = array();

    /**
     * Liste des colonnes avec la propriété 'NOT NULL'.
     * @var array(string)
     */
    var $_columns_notnull = array();

    /**
     * Marqueur indiquant la présence de la colonne 'om_validite_debut'
     * dans la table en cours de traitement
     * @var boolean
     */
    var $_om_validite_debut = false;

    /**
     * Marqueur indiquant la présence de la colonne 'om_validite_fin'
     * dans la table en cours de traitement
     * @var boolean
     */
    var $_om_validite_fin = false;

    /**
     * Chaine de caractères représentant l'entête (deux premières lignes) des
     * scripts PHP générés.
     * @var string
     */
    var $_php_script_header = null;

    /**
     * Tableau de configuration.
     *
     * Ce tableau de configuration permet de donner des informations de surcharges
     * sur certains objets pour qu'elles soient prises en compte par le générateur.
     * $_tables_to_overload = array(
     *    "<table>" => array(
     *        // définition de la liste des classes qui surchargent la classe
     *        // <table> pour que le générateur puisse générer ces surcharges
     *        // et les inclure dans les tests de sous formulaire
     *        "extended_class" => array("<classe_surcharge_1_de_table>", ),
     *        // définition de la liste des champs à afficher dans l'affichage du
     *        // tableau champAffiche dans <table>.inc.php
     *        "displayed_fields_in_tableinc" => array("<champ_1>", ),
     *    ),
     * );
     *
     * @var mixed
     */
    var $_tables_to_overload = array();

    /**
     * Configuratin du nom de la classe 'dbform'.
     * @var null|string
     */
    var $_om_dbform_class_override = null;

    /**
     * Configuratin du path vers le fichier déclarant la classe 'dbform'.
     * @var null|string
     */
    var $_om_dbform_path_override = null;

    /**
     * Constructeur.
     */
    public function __construct() {
        // Initialisation de la classe 'application'.
        $this->init_om_application();
    }

    /**
     * Retourne les liste des tables de la base de données.
     *
     * @return array
     */
    function get_all_tables_from_database() {
        //
        $tables = array();
        //
        if (OM_DB_PHPTYPE == "mysql") {
            $sql = "SHOW TABLES FROM `".DB_PREFIXE.OM_DB_DATABASE."`";
        }
        if (OM_DB_PHPTYPE == "pgsql") {
            $sql = "select tablename from pg_tables where schemaname='".OM_DB_SCHEMA."' UNION select viewname from pg_views where schemaname='".OM_DB_SCHEMA."'";
        }
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        // Recuperation de la liste de toutes les tables de la base de donnees
        while ($row =& $res->fetchRow()) {
            // On enleve de la liste les sequences
            if (substr($row[0], -3, 3) != "seq") {
                //
                $tables[] = $row[0];
            }
        }
        //
        asort($tables);
        //
        return $tables;
    }

    /**
     * Retour la liste des champs d'une table.
     *
     * @param string $table Nom de la table.
     *
     * @return array
     */
    function get_fields_list_from_table($table) {
        //
        $fields = array();
        //
        $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
        // Logger
        $this->addToLog(__METHOD__."(): db->tableInfo(\"".DB_PREFIXE.$table."\")", VERBOSE_MODE);
        //
        foreach ($infos as $key => $value) {
            $fields[] = $value["name"];
        }
        //
        asort($fields);
        //
        return $fields;
    }

    /**
     * Initialisation obligatoire des paramètres pour la table à générer.
     *
     * @param string $table Nom de la table.
     *
     * @todo public
     */
    function init_generation_for_table($table) {
        // On stocke dans l'attribut table le nom de la table passé en
        // paramètre à traiter
        $this->table = $table;

        // Cette méthode permet de récupérer les fichiers configurations
        // pour initialiser les paramètres et permettre leur utilisation
        // dans les méthodes de la classe
        $this->init_configuration();

        // On récupère la liste des tables de la base de données à laquelle on
        // enlève la table sur laquelle on est en train de faire la génération
        $this->tablebase = array_diff(
            $this->get_all_tables_from_database(),
            array($this->table, )
        );

        // RECUPERATION DES INFORMATIONS SUR LA TABLE SELECTIONNEE
        //
        $this->msg="<span class=\"bold\">".__("Table")." : ".$this->table."</span><br />";
        // Recuperation des informations de la table
        $this->info = $this->f->db->tableInfo(DB_PREFIXE.$this->table);
        // Logger
        $this->addToLog(__METHOD__."(): DB PEAR \$this->info = ".print_r($this->info, true), EXTRA_VERBOSE_MODE);
        // recuperation de la cle primaire
        $this->primary_key = $this->get_primary_key($this->table);
        // initialisation de la liste des colonnes NOT NULL
        $this->_init_constraint_notnull();
        // Chargement des tables de cles uniques et uniques multiple
        $this->set_unique_key($this->table);

        /**
         * Définition de tous les paramètres par défaut
         */
        // Taille d'affichage du champ text (nombre de lignes)
        $max = 6;
        // Taille d'affichage du champ text (nombre de colonnes)
        $taille = 80;
        // Taille d'affichage du champ par defaut dans le cas ou nous sommes
        // dans l'impossibilite de determiner la taille du champ
        $pgsql_taille_defaut = 20;
        // Taille d'affichage du champ minimum pour ne pas afficher des
        // champs trop petits ou la saisie serait impossible
        $pgsql_taille_minimum = 10;
        // Taille d'affichage du champ maximum pour ne pas afficher des
        // champs trop grands ou le formulaire depasserait de l'ecran
        $pgsql_taille_maximum = 30;
        //
        $pgsql_longueur_date = 12; // taille d'affichage de la date '
        // Surcharge des paramètres par défaut possible
        if (file_exists("../gen/dyn/form.inc.php")) {
            include ("../gen/dyn/form.inc.php");
            $this->msg.="<br />".__("Chargement du parametrage")." ../gen/dyn/form.inc.php";
        } elseif (file_exists("../gen/dyn/form.inc")) {
            include ("../gen/dyn/form.inc");
            $this->msg.="<br />".__("Chargement du parametrage")." ../gen/dyn/form.inc";
        }

        // CLES ETRANGERE
        // Initialisation de la liste des clés étrangères (contraintes
        // FOREIGN KEY) de la table en cours vers les autres tables
        $this->_init_foreign_tables();
        // SOUS FORMULAIRE
        // Initialisation de la liste des clés étrangères (contraintes
        // FOREIGN KEY) des autres tables vers la table en cours
        $this->_init_other_tables();

        // POSTULAT DE DEPART SUR LES TYPES DE DONNEES
        // variable chaine = string
        // variable numerique = int
        // variable textareal = blob
        // SPECIFICITES PGSQL POUR LES INFORMATIONS SUR LES COLONNES DE LA TABLE
        if (OM_DB_PHPTYPE == 'pgsql') {
            // Recherche des attributs dans les tables systèmes de PostgreSQL
            // Constitution d'une table de valeurs des colonnes de la table
            // utilisation de ces valeurs pour le générateur
            //
            // initialisation du tableau associatif comportant les attributs de champs
            $attchamps=array();
            // requête donnant les attributs par champ
            $sql = "SELECT attname, attnotnull, atttypmod FROM pg_attribute WHERE ";
            $sql .= "attrelid = '".DB_PREFIXE.$this->table."'::regclass ";
            $sql .= "AND attstattarget = -1;";
            //
            $res = $this->f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            //
            $this->f->isDatabaseError($res);
            //remplissage du tableau associatif
            while ($res->fetchInto($row, DB_FETCHMODE_ASSOC)) {
                // Remplissage de la table des attributs de champs
                // DB_FETCHMODE_ORDERED
                $attchamps[$row["attname"]]=array("attnotnull"=> $row["attnotnull"], "atttypmod"=>$row["atttypmod"]);
            }
            // boucle sur les champs de la table openmairie pour définir les attributs des champs
            for ($t = 0; $t < count($this->info); $t++) {
                // test d'existence du champ dans les résultats de requête
                if (isset( $attchamps[$this->info[$t]["name"]])) {
                    // la valeur existe
                    // champ notnull
                    $this->info[$t]["notnull"] = $attchamps[$this->info[$t]["name"]]["attnotnull"];
                    // champ atttypmod : fixer la taille pour les champs ayant len à -1
                    if ($this->info[$t]["len"] == -1) {
                        $this->info[$t]["len"] = $attchamps[$this->info[$t]["name"]]["atttypmod"]-4;
                        //
                        $this->info[$t]["max_saisie"] = $this->info[$t]["len"];
                        //
                        if ($this->info[$t]["len"] < $pgsql_taille_minimum) {
                            $this->info[$t]["taille_affichage"] = $pgsql_taille_minimum;
                        } elseif ($this->info[$t]["len"] > $pgsql_taille_maximum) {
                            $this->info[$t]["taille_affichage"] = $pgsql_taille_maximum;
                        } else {
                            $this->info[$t]["taille_affichage"] = $this->info[$t]["len"];
                        }
                    }
                } else {
                    // la valeur n'existe pas ; on utilise les valeurs par défaut
                    // champ notnull
                    $this->info[$t]["notnull"] = 'f';
                    // champ atttypmod : fixer la taille pour les champs ayant len à -1
                    if ($this->info[$t]["len"] == -1) {
                        $this->info[$t]["len"] = $pgsql_taille_defaut;
                        //
                        $this->info[$t]["taille_affichage"] = $this->info[$t]["len"];
                        $this->info[$t]["max_saisie"] = $this->info[$t]["len"];
                    }
                }
                // Taille des champs numeriques XXX Completer les tailles de champs en fonction des types
                if ($this->info[$t]["type"] == "int2") {
                    $this->info[$t]["type"] = "int";
                    $this->info[$t]["len"] = 6;
                } elseif ($this->info[$t]["type"] == "int4") {
                    $this->info[$t]["type"] = "int";
                    $this->info[$t]["len"] = 11;
                } elseif ($this->info[$t]["type"] == "int8") {
                    $this->info[$t]["type"] = "int";
                    $this->info[$t]["len"] = 20;
                } elseif (substr($this->info[$t]["type"], 0, 3) == "int") {
                    $this->info[$t]["len"] = 11;
                    $this->info[$t]["type"] = "int";
                } elseif (substr($this->info[$t]["type"], 0, 5) == "float"
                    or $this->info[$t]["type"] == "numeric"
                    or $this->info[$t]["type"] == "money") {
                    // float a gerer avec la recuperation des attributs dans la base
                    $this->info[$t]["type"] = "float";
                    $this->info[$t]["len"] = 20;
                } elseif ($this->info[$t]["type"] == "bpchar"
                    or $this->info[$t]["type"] == "char"
                    or $this->info[$t]["type"] == "varchar"
                    or $this->info[$t]["type"] == "character varying") {
                    // STRING
                    $this->info[$t]["type"] = "string";
                } elseif ($this->info[$t]["type"] == "text") {
                    // text = len -1
                    // TEXT
                    $this->info[$t]["type"] = "blob";
                    $this->info[$t]["max_saisie"] = $max;
                    $this->info[$t]["taille_affichage"] = $taille;
                } elseif ($this->info[$t]["type"] == "boolean") {
                    // BOOL
                    $this->info[$t]["type"] = "bool";
                } elseif ($this->info[$t]["type"] == "date") { // date = len 4
                    // Taille des colonnes de type DATE
                    $this->info[$t]["len"] = $pgsql_longueur_date;
                } elseif ($this->info[$t]["type"] == "geometry") {
                    $this->info[$t]["type"] = "geom";
                }
            }
        }
        // SPECIFICITES MYSQL POUR LES INFORMATIONS SUR LES COLONNES DE LA TABLE
        if (OM_DB_PHPTYPE == "mysql") {
            //
            for ($t =0; $t < count($this->info); $t++) {
                // NULL / NOT NULL
                // Gestion de l'attribut not null pour mysql
                if (strpos($this->info[$t]["flags"], "not_null") !== false) {
                    $this->info[$t]["notnull"] = 't';
                } else {
                    $this->info[$t]["notnull"] = 'f';
                }
                // INT
                if ($this->info[$t]["type"] == "int") {
                    $this->info[$t]["type"] = "int";
                    if ($this->info[$t]["len"] == 1) {
                        // gestion du tinyint comme booléen
                        $this->info[$t]["len"] = 1;
                    } elseif ($this->info[$t]["len"] <= 2) {
                        $this->info[$t]["len"] = 6;
                    } elseif ($this->info[$t]["len"] <= 4) {
                        $this->info[$t]["len"] = 11;
                    } elseif ($this->info[$t]["len"] <= 8) {
                        $this->info[$t]["len"] = 20;
                    }
                } elseif ($this->info[$t]["type"] == "decimal"
                    or $this->info[$t]["type"] == "real"
                    or $this->info[$t]["type"] == "double") {
                    // float a gerer avec la recuperation des attributs dans la base
                    $this->info[$t]["type"] = "float";
                    $this->info[$t]["len"] = 20;
                }
                // STRING
                if ($this->info[$t]["type"] =="char"
                    or $this->info[$t]["type"] =="varchar"
                    or $this->info[$t]["type"] =="string") {
                    // char len = 1 (meme pour longueur =10) / character : bpchar len=-1 / character varying : varchar len=-1
                    $this->info[$t]["type"] = "string";
                    // TAILLE & MAX
                    $this->info[$t]["max_saisie"] = $this->info[$t]["len"];
                    if ($this->info[$t]["len"] < $pgsql_taille_minimum) {
                        $this->info[$t]["taille_affichage"] = $pgsql_taille_minimum;
                    } elseif ($this->info[$t]["len"] > $pgsql_taille_maximum) {
                        $this->info[$t]["taille_affichage"] = $pgsql_taille_maximum;
                    } else {
                        $this->info[$t]["taille_affichage"] = $this->info[$t]["len"];
                    }
                }
                // BOOL
                // Gestion du booleen pour mysql en partant du principe que
                // tout champ de type entier et de taille 1 est un booleen
                if ($this->info[$t]["type"] == "int"
                    && $this->info[$t]["len"] == "1") {
                    $this->info[$t]["type"] = "bool";
                }
                // BLOB
                if ($this->info[$t]["type"] == "blob") {
                    $this->info[$t]["max_saisie"] = $max;
                    $this->info[$t]["taille_affichage"] = $taille;
                }
            }
        }
        // Initialisation des attributs pour que chaque appel de cette méthode
        // se fasse de manière neutre (sans trace des précédents appels)
        $this->typecle = "";
        $this->longueur = 0;
        $this->geom = array();
        $this->multi = 1;
        $this->_om_validite_fin = false;
        $this->_om_validite_debut = false;
        $this->_om_file_fields = array();
        // TRAITEMENT STANDARD DES INFORMATIONS SUR LES COLONNES DE LA TABLE
        // Boucle sur chaque champ de la table
        foreach ($this->info as $key => $elem) {
            if ($elem['name'] == 'om_validite_debut') {
                $this->_om_validite_debut = true;
            }
            if ($elem['name'] == 'om_validite_fin') {
                $this->_om_validite_fin = true;
            }
            $abstract_type = $this->get_config_field_to_overload(null, $elem["name"], "abstract_type");
            if ($abstract_type === "file") {
                $this->_om_file_fields[] = $elem['name'];
            }
            // longueur enregistrement (sans blob)
            if ($elem["type"] != "blob") { // exclusion des blob (mysql)
                $this->longueur= $this->longueur+$elem["len"];
            }
            // cle num ou alpha_num
            if ($elem['name'] == $this->primary_key) {
                if ($elem['type'] == 'string') {
                    $this->typecle = 'A';
                } else {
                    $this->typecle = 'N';
                }
            } // primary key
            // table multi ayant un champ om_collectivite
            if ($elem["name"] == 'om_collectivite') {
                //
                $this->multi = "2";
            }
            // champs geom
            if ($elem["type"] == "geom") {
                array_push($this->geom, $elem["name"]);
            }
            // On ajoute le type au tableau des clés étrangères
            if (in_array($elem['name'], array_keys($this->foreign_tables)) === true) {
                $this->foreign_tables[$elem['name']]["column_type"] = $elem['type'];
            }
        }
        // Logger
        $this->addToLog(__METHOD__."(): \$this->info = ".print_r($this->info, true), EXTRA_VERBOSE_MODE);
    }

    // ------------------------------------------------------------------------
    // {{{ START - CONSTRUCTION DES CONTENUS DES SCRIPTS
    //     Toutes les méthodes de ce groupe renvoi des chaines de caractères
    //     Méthodes permettant de construire l'intégralité d'un script
    //     - table_sql_inc, table_sql_inc_core, table_sql_inc_gen
    //     - table_sql_forminc, table_sql_forminc_core, table_sql_forminc_gen
    //     - table_obj_class, table_obj_class_core, table_obj_class_gen
    //     - table_sql_pdfinc, table_sql_reqmoinc, table_sql_importinc
    //     Méthodes permettant de construire des parties d'un script
    //     - def_*
    // ------------------------------------------------------------------------

    /**
     * Renvoi l'entête des scripts PHP générés.
     *
     * Attention repris en modification de fichier.
     *
     * @return string
     */
    function def_php_script_header() {
        // Si l'attribut n'est pas défini alors on le définit
        if ($this->_php_script_header == null) {
            $this->_php_script_header = "<?php\n//\$Id\$ \n//gen openMairie le ".date('d/m/Y H:i')."\n";
        }
        // On renvoi l'attribut
        return $this->_php_script_header;
    }

    /**
     * Renvoi l'entête des scripts ROBOT générés.
     *
     * Attention repris en modification de fichier.
     *
     * @return string
     */
    function def_robot_script_header() {
        //
        if ($this->table == null) {
            $documentation = "Ressources de mots-clefs générés";
        } else {
            $documentation = sprintf(
                "CRUD de la table %s",
                $this->table
            );
        }
        //
        $application_name = $this->f->get_application_name();
        if ($this->f->is_framework_development_mode() === true) {
            $application_name = "framework_openmairie";
        }
        //
        $entete = sprintf('*** Settings ***
Documentation    %s
...    @author  generated
...    @package %s
...    @version %s
',
            $documentation,
            $application_name,
            date('d/m/Y H:m')
        );
        //
        return $entete;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * @todo public
     *
     * @return string
     */
    function table_sql_inc() {
        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = "%s
include PATH_OPENMAIRIE.\"sql/%s/%s.inc.php\";
";
        } else {
            //
            $template = "%s
include \"../gen/sql/%s/%s.inc.php\";
";
        }
        //
        return sprintf(
            $template,
            $this->def_php_script_header(),
            OM_DB_PHPTYPE,
            $this->table
        );
    }

    /**
     * Construit le contenu du script [<PATH_OPENMAIRIE>/sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * @todo public
     *
     * @return string
     */
    function table_sql_inc_core() {
        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = '%1$s
if (file_exists("../gen/sql/%2$s/%3$s.inc.php")) {
    include "../gen/sql/%2$s/%3$s.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/%2$s/%3$s.inc.php";
}
';
            //
            return sprintf(
                $template,
                $this->def_php_script_header(),
                OM_DB_PHPTYPE,
                $this->table
            );
        } else {
            //
            return "";
        }
    }

    /**
     * Construit le contenu du script [gen/sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * @param mixed $dyn Fichier de paramétrage.
     *
     * @todo public
     * @return string
     */
    function table_sql_inc_gen($dyn = null) {
        // Initialisation des variables de travail
        $champaffiche = "";
        $champnonaffiche = "";
        $champrecherche = "";
        //Test si plusieurs cles etrangeres vers la meme table
        $fkLinkedTable=array();
        foreach ($this->foreign_tables as $value) {
            $fkLinkedTable[]=$value['foreign_table_name'];
        }
        //Tableau avec nom de table comme cle et nombre d'occurence comme valeurs
        $countLinkedTable=array_count_values($fkLinkedTable);
        //
        $tri = ""; // champ de tri

        // table du libelle
        $libelle_t = '';
        // colonne du libelle
        $libelle_c = '';
        // alias de la table du libelle
        $alias = '';

        // recuperation du libelle
        $libelle = $this->get_libelle_of($this->table);

        // si la colonne du libelle est une cle etrangere
        // on utilise le libelle de la table etrangere pour le trier
        if (in_array($libelle, $this->clesecondaire)) {
            if ($countLinkedTable[$this->foreign_tables[$libelle]['foreign_table_name']] > 1) {
                $alias = $this->foreign_tables[$libelle]['foreign_table_name'].
                            array_search($libelle, $this->clesecondaire);
            } else {
                $alias = $this->foreign_tables[$libelle]['foreign_table_name'];
            }
            $libelle_t = $this->foreign_tables[$libelle]['foreign_table_name'];
            $libelle_c = $this->get_libelle_of($libelle_t);
        } else {
            // sinon on affiche la colonne telle quelle
            $alias = $this->table;
            $libelle_t = $this->table;
            $libelle_c = $libelle;
        }

        // creation de la clause ORDER BY
        $tri = 'ORDER BY';
        $tri .= ' ';

        // affichage des valeurs NULL en dernier pour MySQL
        if (OM_DB_PHPTYPE == 'mysql') {
            $tri .= 'ISNULL('.$alias.'.'.$libelle_c.') ASC,';
            $tri .= ' ';
        }

        // ordre croissant explicite
        $tri .= $alias.'.'.$libelle_c.' ASC';

        // affichage des valeurs NULL en dernier pour PostgresSQL
        if (OM_DB_PHPTYPE == 'pgsql') {
            $tri .= ' ';
            $tri .= 'NULLS LAST';
        }

        $serie = 15; // nombre d'enregistrement par page
        //
        if (file_exists ("../gen/dyn/form.inc.php")) {
            include ("../gen/dyn/form.inc.php");
            $this->msg.="<br />".__("Chargement du parametrage")." ../gen/dyn/form.inc.php";
        } elseif (file_exists ("../gen/dyn/form.inc")) {
            include ("../gen/dyn/form.inc");
            $this->msg.="<br />".__("Chargement du parametrage")." ../gen/dyn/form.inc";
        }

        // Option globale 'om_validite'
        // Cette option permet de cacher par défaut les colonnes 'om_validite_debut'
        // et 'om_validite_fin' pour ne les faire apparaître que lorsqu'on clique
        // sur le lien 'Afficher les éléments expirés'. Par défaut les colonnes
        // sont visibles. Pour que ce ne soit pas le cas, il faut le déclarer
        // dans le script '../gen/dyn/gen.inc.php' dans le tableau $tables_to_overload
        // en ajoutant l'entrée "om_validite" => "hidden_by_default" dans l'entrée
        // de la table souhaitée.
        $is_om_validite_hidden_by_default = null;
        if (isset($this->_tables_to_overload[$this->table])
            && isset($this->_tables_to_overload[$this->table]["om_validite"])) {
            //
            if ($this->_tables_to_overload[$this->table]["om_validite"] === "hidden_by_default") {
                $is_om_validite_hidden_by_default = true;
            } else {
                $is_om_validite_hidden_by_default = false;
            }
        }

        //
        $edition=$this->table;

        // On ajoute la clé primaire dans le champaffiche et le champrecherche
        // en première position
        $champaffiche.= "\n    '".$this->table.".".$this->primary_key." as \"'.__(\"".$this->primary_key."\").'\"',";
        $champrecherche.= "\n    '".$this->table.".".$this->primary_key." as \"'.__(\"".$this->primary_key."\").'\"',";

        // variable suivant champs : champaffiche et champrecherche
        // $champaffiche; // tableau des noms des champs affiches dans table.inc
        // $champrecherche; // tableau des noms des champs de recherche dans table.inc
        foreach ($this->info as $elem) {
            if ($elem['name'] == $this->primary_key) {
                continue;
            }
            //
            $is_field_in_overrided_list = null;
            if (isset($this->_tables_to_overload[$this->table])
                && isset($this->_tables_to_overload[$this->table]["displayed_fields_in_tableinc"])) {
                //
                if (in_array($elem["name"], $this->_tables_to_overload[$this->table]["displayed_fields_in_tableinc"])) {
                    $is_field_in_overrided_list = true;
                } else {
                    $is_field_in_overrided_list = false;
                }
            }
            // pas d affichage de blob en tableinc
            // affichage au format date
            $temp ='def_champaffichedate'.OM_DB_PHPTYPE;
            // On affiche le champ dans le listing sauf si :
            // - cas 'blob' : le champ en question est de type blob
            // - cas 'om_collectivite' : le champ est 'om_collectivite' et nous ne sommes
            //   pas sur la table du même nom et la table est de type multi,
            // - cas 'displayed_fields_in_tableinc' : le champ en question n'est pas
            //   présent dans la liste des champs surchargé et qu'il existe une surcharge
            //   de la liste des champs,
            // - cas 'om_validite' : le champ est 'om_validite_debut' ou 'om_validite_fin'
            //   et l'option om_validite qui permet de cacher ces champs par défaut est
            //   activée.
            if ($elem["type"] != "blob"
                && !($this->multi == 2 && $elem["name"] == "om_collectivite" && $this->table != "om_collectivite")
                && $is_field_in_overrided_list !== false
                && !(($elem["name"] === "om_validite_debut"
                    || $elem["name"] === "om_validite_fin")
                    && $is_om_validite_hidden_by_default === true)
                ) {
                //
                $champaffiche.="\n    ";
                //
                if ($elem["type"] == "date") {
                    //
                    $champaffiche .= $this->$temp($this->table.".".$elem["name"], $elem["name"]).",";
                } elseif ($elem["type"] == "bool") {
                    //
                    if (OM_DB_PHPTYPE == "mysql") {
                        //
                        $champaffiche.= "\"case ".$this->table.".".$elem["name"]." when 1 then 'Oui' else 'Non' end as \\\"\".__(\"".$elem["name"]."\").\"\\\"\",";
                    } elseif (OM_DB_PHPTYPE == "pgsql") {
                        //
                        $champaffiche.= "\"case ".$this->table.".".$elem["name"]." when 't' then 'Oui' else 'Non' end as \\\"\".__(\"".$elem["name"]."\").\"\\\"\",";
                    }
                } else {
                    // Si le champ que nous sommes en train d'afficher est une cle secondaire
                    if (!empty($this->clesecondaire) && in_array($elem["name"], $this->clesecondaire)) {
                        //
                        $ftable = $this->foreign_tables[$elem["name"]]['foreign_table_name'];
                        // recuperation du libelle
                        $flibelle = $this->get_libelle_of($ftable);
                        if ($countLinkedTable[$this->foreign_tables[$elem["name"]]['foreign_table_name']] > 1) {
                            $champaffiche.= "'$ftable".
                                array_search($elem["name"], $this->clesecondaire).
                                '.'.$flibelle;
                        } else {
                            $champaffiche.= "'".$ftable.".".$flibelle;
                        }
                    } else {
                        $champaffiche.= "'".$this->table.".".$elem["name"];
                    }
                    $champaffiche.= ' ';
                    $champaffiche.= "as \"'.__(\"".$elem["name"]."\").'\"',";
                }
            } else {
                $champnonaffiche.="\n    ";
                $champnonaffiche.= "'".$this->table.".".$elem["name"]." as \"'.__(\"".$elem["name"]."\").'\"',";
            }
            //
            if (($elem["type"] == "string" || $elem["type"] == "int" || $elem["type"] == "float") && $is_field_in_overrided_list !== false) {
                //
                if ($this->multi == 2 && $elem["name"] == "om_collectivite" && $this->table != "om_collectivite") {
                    //
                    echo "";
                } else {
                    $champrecherche.="\n    ";
                    // Si le champ que nous sommes en train d'afficher est une cle secondaire
                    if (!empty($this->clesecondaire) && in_array($elem["name"], $this->clesecondaire)) {
                        $ftable = $this->foreign_tables[$elem["name"]]['foreign_table_name'];
                        // recuperation du libelle
                        $flibelle = $this->get_libelle_of($ftable);
                        if ($countLinkedTable[$this->foreign_tables[$elem["name"]]['foreign_table_name']] > 1) {
                            $champrecherche.= "'".$ftable.array_search($elem["name"], $this->clesecondaire).".".$flibelle." ";
                        } else {
                            $champrecherche.= "'".$ftable.".".$flibelle." ";
                        }
                    } else {
                         $champrecherche.="'".$this->table.".".$elem["name"]." ";
                    }

                    $champrecherche.= "as \"'.__(\"".$elem["name"]."\").'\"',";
                }
            }
        }
        // creation de table.inc.php
        $tableinc= $this->def_php_script_header();
        $tableinc.="\n\$DEBUG=0;";
        $tableinc.="\n\$serie=".$serie.";";
        $tableinc.=$this->def_ent();

        if ($this->is_om_validite() == true) {
            $tableinc.="\n\$om_validite = true;";
        }

        $tableinc.="\nif(!isset(\$premier)) \$premier='';";
        $tableinc.="\nif(!isset(\$tricolsf)) \$tricolsf='';";
        $tableinc.="\nif(!isset(\$premiersf)) \$premiersf='';";
        $tableinc.="\nif(!isset(\$selection)) \$selection='';";
        $tableinc.="\nif(!isset(\$retourformulaire)) \$retourformulaire='';";
        $tableinc.="\nif (!isset(\$idxformulaire)) {\n    \$idxformulaire = '';\n}";
        $tableinc.="\nif (!isset(\$tricol)) {\n    \$tricol = '';\n}";
        $tableinc.="\nif (!isset(\$valide)) {\n    \$valide = '';\n}";
        // ***
        // TABLE
        $tableinc .= "\n// FROM ";
        $tableinc .= "\n\$table = DB_PREFIXE.\"".$this->table;
        //
        if (!empty($this->clesecondaire)) {
            //
            foreach ($this->clesecondaire as $key => $elem) {
                //
                if (isset($this->foreign_tables[$elem])) {
                    $tableinc .= "\n    LEFT JOIN \".DB_PREFIXE.\"".$this->foreign_tables[$elem]["foreign_table_name"]." ";
                    $ftable=$this->foreign_tables[$elem]['foreign_table_name'];
                    if ($countLinkedTable[$ftable] > 1) {
                        $tableinc .= "as $ftable".$key." ";
                    }
                    $tableinc .= "\n        ON ".$this->table.".".$elem."=";
                    if ($countLinkedTable[$ftable] > 1) {
                        $tableinc .= "$ftable".$key;
                    } else {
                        $tableinc .=  $ftable;
                    }
                    $tableinc .= ".".$this->foreign_tables[$elem]["foreign_column_name"]." ";
                } else {
                    //
                    $tableinc .= "\n    LEFT JOIN \".DB_PREFIXE.\"".$elem." as a".$key." ";
                    $tableinc .= "\n        ON ".$this->table.".".$elem."=a".$key.".".$elem." ";
                }
            }
        }
        //
        $tableinc.= "\";";
        // CHAMP AFFICHE
        $tableinc .= "\n// SELECT ";
        $tableinc.="\n\$champAffiche = array(".$champaffiche."\n    );";

        if ($this->multi == 2 && $this->table != 'om_collectivite') {
            if (isset($countLinkedTable["om_collectivite"])
                && $countLinkedTable["om_collectivite"] > 1) {
                $alias = 'om_collectivite'.
                            array_search('om_collectivite', $this->clesecondaire).
                            '.libelle';
            } else {
                $alias = 'om_collectivite.libelle';
            }

            $tableinc .= "\n//\nif (\$_SESSION['niveau'] == '2') {";
            $tableinc .= "\n    array_push(\$champAffiche, \"".$alias." as \\\"\".__(\"collectivite\").\"\\\"\");";
            $tableinc .= "\n}";
        }
        // Spécificité des dates de validité uniquement si :
        // - la table contient les champs en question
        // - l'option est activée
        if ($this->is_om_validite() == true
            && $is_om_validite_hidden_by_default === true) {
            //
            $tableinc .= sprintf("
// Spécificité des dates de validité
\$displayed_fields_validite = array(
    %s,
    %s,
);
// On affiche les champs de date de validité uniquement lorsque le paramètre
// d'affichage des éléments expirés est activé
if (isset(\$_GET['valide']) && \$_GET['valide'] === 'false') {
    \$champAffiche = array_merge(\$champAffiche, \$displayed_fields_validite);
}
",
                $this->$temp($this->table.".om_validite_debut", "om_validite_debut"),
                $this->$temp($this->table.".om_validite_fin", "om_validite_fin")
            );
        }
        $tableinc.="\n//\n\$champNonAffiche = array(".$champnonaffiche."\n    );";
        $tableinc.="\n//\n\$champRecherche = array(".$champrecherche."\n    );";
        if ($this->multi == 2 && $this->table != 'om_collectivite') {
            if (isset($countLinkedTable["om_collectivite"])
                && $countLinkedTable["om_collectivite"] > 1) {
                $alias = 'om_collectivite'.
                            array_search('om_collectivite', $this->clesecondaire).
                            '.libelle';
            } else {
                $alias = 'om_collectivite.libelle';
            }

            $tableinc .= "\n//\nif (\$_SESSION['niveau'] == '2') {";
            $tableinc .= "\n    array_push(\$champRecherche, \"".$alias." as \\\"\".__(\"collectivite\").\"\\\"\");";
            $tableinc .= "\n}";
        }
        $tableinc.="\n\$tri=\"".$tri."\";";
        $tableinc.="\n\$edition=\"".$edition."\";";
        // les sous formulaires
        // href
        $tableinc.=$this->def_selection_inc();
        $tableinc.=$this->def_sousformulaire_inc();
        $tableinc.="\n";
        return $tableinc;
    }

    /**
     * Renvoi le titre de l'écran.
     *
     * @return string
     */
    function def_ent() {
        //
        $out = "\n\$ent = ";
        //
        if (isset($this->_tables_to_overload[$this->table])
            && isset($this->_tables_to_overload[$this->table]["breadcrumb_in_page_title"])) {
            //
            foreach ($this->_tables_to_overload[$this->table]["breadcrumb_in_page_title"] as $elem) {
                $out .= "__(\"".$elem."\").\" -> \".";
            }
        } else {
            //
            if (in_array(
                    $this->table,
                    array(
                        'om_etat', 'om_sousetat', 'om_lettretype',
                        'om_requete', 'om_logo',
                    )
                )) {
                $breadcrumb = "parametrage";
            } elseif (strpos($this->table, "om_") === 0) {
                $breadcrumb = "administration";
            } else {
                $breadcrumb = "application";
            }
            //
            $out .= "__(\"".$breadcrumb."\").\" -> \".";
        }
        //
        if (isset($this->_tables_to_overload[$this->table])
            && isset($this->_tables_to_overload[$this->table]["tablename_in_page_title"])) {
            //
            $out .= "__(\"".$this->_tables_to_overload[$this->table]["tablename_in_page_title"]."\");";
        } else {
            //
            $out .= "__(\"".$this->table."\");";
        }
        //
        return $out;
    }

    /**
     * Construit une partie du script [gen/sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * La partie construite ici concerne la clause WHERE de la requête. Cette
     * clause est stockée dans la variable $selection du script en question.
     *
     * @return string
     */
    function def_selection_inc() {

        /**
         * TEMPLATES
         */
        //
        $template_selection = '
/**
 * Gestion de la clause WHERE => $selection
 */%s
';
        //
        $template_listing_standard = '
// Filtre listing standard
%s';
        //
        $template_listing_standard_multi = 'if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    %s%s
} else {
    // Filtre MONO
    %s%s
}';
        //
        $template_listing_standard_mono = '%s%s';
        //
        $template_selection_standard_common = '$selection = "%s";';
        //
        $template_selection_standard_mono = '$selection = " WHERE (%s) %s";';
        //
        $template_listing_sousformulaire = '
// Filtre listing sous formulaire - %s
if (in_array($retourformulaire, $foreign_keys_extended["%s"])) {
    %s%s
}';
        //
        $template_contenu_listing_sousformulaire_mono = '%s';
        //
        $template_contenu_listing_sousformulaire_multi = 'if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        %s
    } else {
        // Filtre MONO
        %s
    }';
        //
        $template_selection_sousformulaire_common = '$selection = " WHERE (%s) %s";';
        //
        $template_selection_sousformulaire_mono = '$selection = " WHERE (%s) AND (%s) %s";';
        //
        $template_om_validite_where = '
$where_om_validite = "%s";';
        //
        $template_om_validite_logique = '
// Gestion OMValidité - Suppression du filtre si paramètre
if (isset($_GET["valide"]) and $_GET["valide"] == "false") {
    if (!isset($where_om_validite)
        or (isset($where_om_validite) and $where_om_validite == "")) {
        if (trim($selection) != "") {
            $selection = "";
        }
    } else {
        $selection = trim(str_replace($where_om_validite, "", $selection));
    }
}';

        /**
         * COMMON
         */
        //
        $contenu_selection = "";
        //
        $filter_om_collectivite = sprintf(
            '%s.om_collectivite = \'".$_SESSION["collectivite"]."\'',
            $this->table
        );
        //
        if ($this->is_om_validite() == true) {
            //
            $filter_om_validite = $this->filter_om_validite($this->table);
            //
            $filter_om_validite_with_where = ' WHERE '.$filter_om_validite;
            $filter_om_validite_with_and = ' AND '.$filter_om_validite;
            $where_om_validite_with_where = sprintf(
                $template_om_validite_where,
                $filter_om_validite_with_where
            );
            $where_om_validite_with_and = sprintf(
                $template_om_validite_where,
                $filter_om_validite_with_and
            );
        } else {
            //
            $filter_om_validite_with_where = '';
            $filter_om_validite_with_and = '';
            $where_om_validite_with_where = '';
            $where_om_validite_with_and = '';
        }

        /**
         * LISTING STANDARD
         */
        //
        $filter_mono = sprintf(
            $template_selection_standard_mono,
            $filter_om_collectivite,
            $filter_om_validite_with_and
        );
        //
        $filter_common = sprintf(
            $template_selection_standard_common,
            $filter_om_validite_with_where
        );
        //
        $contenu_listing_standard = "";
        //
        if ($this->multi == 2) {
            //
            $contenu_listing_standard = sprintf(
                $template_listing_standard_multi,
                $filter_common,
                $where_om_validite_with_where,
                $filter_mono,
                $where_om_validite_with_and
            );
        } else {
            //
            $contenu_listing_standard = sprintf(
                $template_listing_standard_mono,
                $filter_common,
                $where_om_validite_with_where
            );
        }
        //
        $contenu_selection .= sprintf(
            $template_listing_standard,
            $contenu_listing_standard
        );

        /**
         * LISTING SOUSFORMULAIRE
         */
        if (!empty($this->clesecondaire)) {
            $ftables = array();
            foreach ($this->foreign_tables as $key => $infos) {
                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }

            // Définition des clés étrangères avec leurs possibles surcharges
            $contenu_selection .= $this->def_sql_var_foreign_keys_extended();

            foreach ($ftables as $table => $columns) {
                //
                $contenu_tmp = '';
                foreach ($columns as $column) {
                    if ($this->foreign_tables[$column]["column_type"] == "int") {
                        $contenu_tmp .= sprintf(
                            '%s.%s = ".intval($idxformulaire)." OR ',
                            $this->table,
                            $column
                        );
                    } else {
                        $contenu_tmp .= sprintf(
                            '%s.%s = \'".$f->db->escapeSimple($idxformulaire)."\' OR ',
                            $this->table,
                            $column
                        );
                    }
                }
                $contenu_tmp = substr($contenu_tmp, 0, strlen($contenu_tmp) - 4);
                //
                $filter_mono = sprintf(
                    $template_selection_sousformulaire_mono,
                    $filter_om_collectivite,
                    $contenu_tmp,
                    $filter_om_validite_with_and
                );
                //
                $filter_common = sprintf(
                    $template_selection_sousformulaire_common,
                    $contenu_tmp,
                    $filter_om_validite_with_and
                );
                //
                $contenu_listing_sousformulaire = "";
                //
                if ($this->multi == 2) {
                    //
                    $contenu_listing_sousformulaire .= sprintf(
                        $template_contenu_listing_sousformulaire_multi,
                        $filter_common,
                        $filter_mono
                    );
                } else {
                    //
                    $contenu_listing_sousformulaire .= sprintf(
                        $template_contenu_listing_sousformulaire_mono,
                        $filter_common
                    );
                }
                //
                $contenu_selection .= sprintf(
                    $template_listing_sousformulaire,
                    $table,
                    $table,
                    $contenu_listing_sousformulaire,
                    $where_om_validite_with_and
                );
            }
        }

        //
        if ($this->is_om_validite() == true) {
            //
            $contenu_selection .= $template_om_validite_logique;
        }

        /**
         *
         */
        //
        return sprintf(
            $template_selection,
            $contenu_selection
        );
    }

    /**
     * Construit la partie...
     *
     * @return string
     */
    function def_sousformulaire_inc() {
        $code = '';
        if (!empty($this->sousformulaires)) {
            //
            $comment = "";
            if (isset($this->_tables_to_overload[$this->table])
                && isset($this->_tables_to_overload[$this->table]["tabs_in_form"])
                && $this->_tables_to_overload[$this->table]["tabs_in_form"] === false) {
                $comment = "//";
            }
            //
            foreach ($this->sousformulaires as $sousformulaire) {
                $code .= "\n    ";
                $code .= $comment."'".$sousformulaire."',";
            }
            $code = "
/**
 * Gestion SOUSFORMULAIRE => \$sousformulaire
 */
\$sousformulaire = array(".$code."
);
";
        }
        return $code;
    }

    /**
     * Construit le contenu du script [obj/<TABLE>.class.php].
     *
     * Cette méthode permet de générer la définition de la classe qui est
     * définie dans le dossier obj/ et qui étend la classe définit dans le
     * dossier gen/obj/. Cette classe a pour objectif de contenir les
     * surcharges spécifiques aux objets en questions dans l'applicatif.
     *
     * @todo public
     * @return string
     */
    function table_obj_class() {
        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = '%s
require_once PATH_OPENMAIRIE."obj/%s.class.php";

class %s extends %s_core {

}
';
        } else {
            //
            $template = '%s
require_once "../gen/obj/%s.class.php";

class %s extends %s_gen {

}
';
        }
        //
        return sprintf(
            $template,
            $this->def_php_script_header(),
            $this->table,
            $this->table,
            $this->table
        );

    }

    /**
     * Construit le contenu du script [<PATH_OPENMAIRIE>/obj/<TABLE>.class.php].
     *
     * Cette méthode permet de générer la définition de la classe qui est
     * définie dans le dossier <PATH_OPENMAIRIE>/obj/ et qui étend la classe 
     * définit dans le dossier gen/obj/. Cette classe a pour objectif de
     * contenir les surcharges spécifiques aux objets en questions dans
     * l'applicatif.
     *
     * @todo public
     * @return string
     */
    function table_obj_class_core() {

        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = '%1$s

if (file_exists("../gen/obj/%2$s.class.php")) {
    require_once "../gen/obj/%2$s.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/%2$s.class.php";
}

class %2$s_core extends %2$s_gen {

}
';
            //
            return sprintf(
                $template,
                $this->def_php_script_header(),
                $this->table
            );
        } else {
            //
            return "";
        }

    }

    /**
     * Construit le contenu du script [gen/obj/<TABLE>.class.php].
     *
     * Cette méthode permet de générer la définition de la classe qui est
     * définie dans le dossier gen/obj/ et qui étend la classe
     * obj/om_dbform.class.php. Cette classe a pour objectif de contenir
     * les méthodes générées à partir du modèle de données.
     *
     * @param mixed $dyn Fichier de paramétrage.
     *
     * @todo public
     * @return string
     */
    function table_obj_class_gen($dyn = null) {

        //
        $om_dbform_class = "dbform";
        $om_dbform_require = "require_once PATH_OPENMAIRIE.\"om_dbform.class.php\";";
        if ($this->_om_dbform_class_override !== null) {
            $om_dbform_class = $this->_om_dbform_class_override;
        }
        if ($this->_om_dbform_path_override !== null) {
            $om_dbform_require = "require_once \"".$this->_om_dbform_path_override."\";";
        }

        //
        $template = "%s
%s

class %s_gen extends %s {

    protected \$_absolute_class_name = \"%s\";

    %s
    %s
    %s
    %s
    %s
    %s%s
    %s%s%s

%s

    //==================================
    // sous Formulaire
    //==================================
    %s

    //==================================
    // cle secondaire
    //==================================
    %s

}
";

        //
        $tableobj = "";
        //
        $tableobj .= $this->def_obj_meth_setvalf();
        //
        if($this->typecle=="N"){ // cle automatique si numerique
            $tableobj.="\n\n    //=================================================";
            $tableobj.="\n    //cle primaire automatique [automatic primary key]";
            $tableobj.="\n    //==================================================";
            $tableobj.=$this->def_obj_meth_setid();
            $tableobj.=$this->def_obj_meth_setvalfajout();
            $tableobj.=$this->def_obj_meth_verifierajout();
        }
        //
        $tableobj.=$this->def_obj_meth_verifier();
        //
        $tableobj.="\n\n    //==========================";
        $tableobj.="\n    // Formulaire  [form]";
        $tableobj.="\n    //==========================";
        $tableobj.=$this->def_obj_meth_settype();
        $tableobj.=$this->def_obj_meth_setonchange();
        $tableobj.=$this->def_obj_meth_settaille();
        $tableobj.=$this->def_obj_meth_setmax();
        $tableobj.=$this->def_obj_meth_setlib();
        $tableobj.=$this->def_obj_meth_setselect();
        $tableobj.=$this->def_obj_meth_setval();
        //
        return sprintf(
            $template,
            $this->def_php_script_header(),
            $om_dbform_require,
            $this->table,
            $om_dbform_class,
            $this->table,
            //
            $this->def_obj_attr_table(),
            $this->def_obj_attr_cleprimaire(),
            $this->def_obj_attr_typecle(),
            $this->def_obj_attr_required_field(),
            $this->def_obj_attr_unique_key(),
            $this->def_obj_attr_foreign_keys_extended(),
            $this->def_obj_attr_abstract_type(),
            //
            $this->def_obj_meth_get_default_libelle(),
            $this->def_obj_meth_get_var_sql_forminc__champs(),
            $this->def_obj_meth_get_var_sql_forminc__sql(),
            //
            $tableobj,
            //
            $this->def_obj_meth_setvalsousformulaire(),
            $this->def_obj_meth_clesecondaire()
        );
    }

    /**
     * Construit la définition de l'attribut $abstract_type pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_abstract_type() {
        //
        if (count($this->_om_file_fields) == 0) {
            return "";
        }
        //
        $output = '
    var $abstract_type = array(';
        foreach ($this->_om_file_fields as $value) {
            $output .= sprintf(
                '
        "%s" => "file",',
                $value
            );
        }
        $output .= '
    );';
        //
        return $output;
    }

    /**
     * Construit des mots-clefs Robot Framework dans le fichier :
     * [tests/resources/app/gen/<TABLE>.robot]
     *
     * Ces derniers correspondent aux modes de base du framework :
     *
     * - AJOUTER,
     * - MODIFIER,
     * - SUPPRIMER.
     * - CONSULTER.
     *
     * Le mot-clef SAISIR est commun à AJOUTER et MODIFIER.
     * Le mot-clef AJOUTER retourne l'ID du nouvel enregistrement.
     *
     * Le script se base sur la surcharge des obj et sql, et différencie
     * les tables métier de celles du framework.
     *
     * @return  string         Fichier généré
     */
    function table_tests_crud() {
        //
        $keywords_saisir = '';
        // Pour chaque champ
        foreach($this->info as $elem) {
            // On ignore la clé primaire
            if ($elem['name'] === $this->primary_key) {
                continue;
            }
            // Template de saisie
            $field_to_fill = '
    Si "'.$elem['name'].'" existe dans "${values}" on execute "%s" dans le formulaire';
            // Si champ pas de type HTML
            if (strpos($elem['name'], '_html') == false) {
                // Si clé étrangère
                if (!empty($this->clesecondaire) AND in_array($elem['name'], $this->clesecondaire)) {
                    $keyword_to_use = 'Select From List By Label';
                } else {
                    switch ($elem['type']) {
                        case "date" :
                            $keyword_to_use = 'Input Datepicker';
                            break;
                        case "bool" :
                            $keyword_to_use = 'Set Checkbox';
                            break;
                        case "int" :
                        case "float" :
                        default :
                            $keyword_to_use = 'Input Text';
                            break;
                    }
                }
            } else {
                $keyword_to_use = 'Input HTML';
            }
            $keywords_saisir .= sprintf(
                $field_to_fill,
                $keyword_to_use
            );
        }
        //
        $entete = $this->def_robot_script_header();
        $entete .= '
*** Keywords ***';
        //
        $create = sprintf('

Ajouter %s
    [Documentation]  Crée l\'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  %s
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir %s  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l\'ID du nouvel enregistrement
    ${%s} =  Get Text  css=div.form-content span#%s
    # On le retourne
    [Return]  ${%s}',
            __($this->table),
            $this->table,
            __($this->table),
            $this->primary_key, $this->primary_key,
            $this->primary_key
        );
        //
        $update = sprintf('

Modifier %s
    [Documentation]  Modifie l\'enregistrement
    [Arguments]  ${%s}  ${values}

    # On accède à l\'enregistrement
    Depuis le contexte %s  ${%s}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  %s  modifier
    # On saisit des valeurs
    Saisir %s  ${values}
    # On valide le formulaire
    Click On Submit Button',
            __($this->table),
            $this->primary_key,
            __($this->table), $this->primary_key,
            $this->table,
            __($this->table)
        );
        //
        $delete = sprintf('

Supprimer %s
    [Documentation]  Supprime l\'enregistrement
    [Arguments]  ${%s}

    # On accède à l\'enregistrement
    Depuis le contexte %s  ${%s}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  %s  supprimer
    # On valide le formulaire
    Click On Submit Button',
            __($this->table),
            $this->primary_key,
            __($this->table), $this->table,
            $this->primary_key
        );
        //
        $saisir = sprintf('

Saisir %s
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    %s',
            __($this->table),
            $keywords_saisir
        );
        //
        $read = sprintf('

Depuis le contexte %s
    [Documentation]  Accède au formulaire
    [Arguments]  ${%s}

    # On accède au tableau
    Go To Tab  %s
    # On recherche l\'enregistrement
    Use Simple Search  %s  ${%s}
    # On clique sur le résultat
    Click On Link  ${%s}
    # On vérifie qu\'il n\'y a pas d\'erreur
    Page Should Not Contain Errors',
            __($this->table),
            $this->primary_key,
            $this->table,
            __($this->primary_key), $this->primary_key,
            $this->primary_key
        );
        //
        return $entete.$read.$create.$update.$delete.$saisir;
    }

    /**
     * Construit les deux fichiers ressources des mots-clefs générés :
     * celui pour les objets métier et celui pour les objets framework.
     *
     * @param   array   $tables  Tables générées
     * @param   string  $type    app OU core
     *
     * @return  string           Fichier généré
     */
    function resources_tests_crud($tables, $type) {
        //
        $entete = $this->def_robot_script_header();
        //
        $resources = "";
        // Si framework ET instance de celui-ci
        if ($type == "core"
            && $this->f->is_framework_development_mode() === true) {
            // Pour chaque table
            foreach ($tables as $table) {
                // Si table métier on continue
                if (!$this->is_omframework_table($table)) {
                    continue;
                }
                // Sinon ajout de la ressource
                $resources .= sprintf('
Resource          %s.robot', $table
                );
            }
        }
        // Si métier
        elseif ($type == "app") {
            // Pour chaque table
            foreach ($tables as $table) {
                // Si table framework on continue
                if ($this->is_omframework_table($table)) {
                    continue;
                }
                // Sinon ajout de la ressource
                $resources .= sprintf('
Resource          %s.robot', $table
                );
            }
        }
        // Si framework mais instance métier
        else {
            return "";
        }
        //
        return $entete.$resources;
    }

    /**
     * Appelle la méthode de génération de mots-clefs adéquate
     * (gestion APP et CORE) et affiche le message du résultat.
     *
     * @param   array   $tables  Tables générées
     * @return  string           Fichier généré
     */
    function gen_full_tests($tables) {
        //
        $this->table = null;
        // Vérification qu'au moins APP ou CORE existe
        if (is_dir("../tests/resources/app/gen")
            OR is_dir("../tests/resources/core/gen")) {
            // Création du message de résultat
            $this->f->displaySubTitle("-> ".__("toutes les tables"));
        }
        // APP si instance métier configurée pour (nécessite le répertoire gen)
        if (is_dir("../tests/resources/app/gen")) {
            $this->msg = "<span class=\"bold\">".__("Mots-clefs Robot Framework (metier)")."</span><br />";
            $result = $this->ecrirefichier(
                "../tests/resources/app/gen/gen_resources.robot",
                $this->resources_tests_crud($tables, "app")
            );
            // Affichage du message des erreurs de droits d'ecriture
            if (!$result) {
                $this->f->displayMessage(
                    "error",
                    __("Erreur de droits d'ecriture lors de la generation du fichier 'resources' des tests.")
                );
            } else {
                // Affichage du message de fin de traitement
                $this->f->displayMessage("valid", $this->msg);
            }
        }
        // CORE si dossier existe et si instance framework
        if (is_dir("../tests/resources/core/gen")
            && $this->f->is_framework_development_mode() === true) {
            $this->msg = "<span class=\"bold\">".__("Mots-clefs Robot Framework (openMairie)")."</span><br />";
            $result = $this->ecrirefichier(
                "../tests/resources/core/gen/gen_om_resources.robot",
                $this->resources_tests_crud($tables, "core")
            );
            // Affichage du message des erreurs de droits d'ecriture
            if (!$result) {
                $this->f->displayMessage(
                    "error",
                    __("Erreur de droits d'ecriture lors de la generation du fichier 'resources' des tests.")
                );
            } else {
                // Affichage du message de fin de traitement
                $this->f->displayMessage("valid", $this->msg);
            }
        }
    }

    // table.obj

    /**
     * Construit la définition de l'attribut $table pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_table() {
        //
        return sprintf(
            "var \$table = \"%s\";",
            $this->table
        );
    }

    /**
     * Construit la définition de l'attribut $clePrimaire pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_cleprimaire() {
        //
        return sprintf(
            "var \$clePrimaire = \"%s\";",
            $this->primary_key
        );
    }

    /**
     * Construit la définition de l'attribut $typeCle pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_typecle() {
        //
        return sprintf(
            "var \$typeCle = \"%s\";",
            $this->typecle
        );
    }

    /**
     * Construit la définition de l'attribut $required_field pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_required_field() {
        //
        $output = "";
        // Tableau des contraintes not null
        $s8="        ";
        if (!empty($this->_columns_notnull)) {
            $output .= "var \$required_field = array(\n".$s8."\"".implode("\",\n".$s8."\"", $this->_columns_notnull)."\"\n    );";
        }
        //
        return $output;
    }

    /**
     * Construit la définition de l'attribut $unique_key pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_unique_key() {
        //
        $output = "";
        // Tableaux des contraintes uniques
        if(!empty($this->unique_key) || !empty($this->unique_multiple_key)) {
            $output.="var \$unique_key = array(";
            foreach($this->unique_key as $contraint) {
                $output.="\n      \"".$contraint."\",";
            }
            if(!empty($this->unique_multiple_key)) {
                foreach($this->unique_multiple_key as $multiple) {
                    $output.="\n      array(\"".implode("\",\"", $multiple)."\"),";
                }

            }
            $output.="\n    );";
        }
        //
        return $output;
    }

    /**
     * Construit la définition de l'attribut $foreign_keys_extended pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_foreign_keys_extended() {
        return $this->def_var_foreign_keys_extended("attr");
    }

    /**
     * Construit la définition de l'attribut $foreign_keys_extended pour table_obj.
     *
     * @return string
     */
    function def_sql_var_foreign_keys_extended() {
        return $this->def_var_foreign_keys_extended("var");
    }

    /**
     * Construit la définition de l'attribut $foreign_keys_extended pour table_obj.
     *
     * @param string $context Contexte d'utilisation de la méthode :
     *                         - attr : attribut de classe,
     *                         - var : variable dans un script.
     *
     * @return string
     */
    function def_var_foreign_keys_extended($context = "attr") {

        if ($context == "var") {
            //
            $template_listing_sousformulaire_extended_class = '
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
%s);';
            //
            $template_content_foreign_keys_extended = '    "%s" => array("%s", %s),
';
        } else {
            //
            $template_listing_sousformulaire_extended_class = 'var $foreign_keys_extended = array(
%s    );';
            //
            $template_content_foreign_keys_extended = '        "%s" => array("%s", %s),
';
        }

        //
        $ftables = array();
        //
        if (!empty($this->clesecondaire)) {
            foreach ($this->foreign_tables as $key => $infos) {

                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }
        }
        //
        $content_foreign_keys_extended = "";
        foreach ($ftables as $table => $columns) {
            //
            $extended_class = '';
            //
            if (isset($this->_tables_to_overload[$table])
                && isset($this->_tables_to_overload[$table]["extended_class"])
                && is_array($this->_tables_to_overload[$table]["extended_class"])
                && count($this->_tables_to_overload[$table]["extended_class"])
                ) {
                $extended_class = '"'.implode('", "', $this->_tables_to_overload[$table]["extended_class"]).'", ';
            }
            //
            $content_foreign_keys_extended .= sprintf(
                $template_content_foreign_keys_extended,
                $table,
                $table,
                $extended_class
            );
        }
        return sprintf(
            $template_listing_sousformulaire_extended_class,
            $content_foreign_keys_extended
        );
    }

    /**
     * Construit la définition de la méthode verifier() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_verifier() {
        $content = '';

        // verification pour les objets a date de validite
        if ($this->is_om_validite() == true) {

            // Entete de la methode
            $content .= "\n";
            $content .= "    /**\n     * Methode verifier\n     */\n";
            $content .= "    function verifier(\$val = array(), &\$dnu1 = null, \$dnu2 = null) {\n";
            $content .= "        // On appelle la methode de la classe parent\n";
            $content .= "        parent::verifier(\$val, \$this->f->db, null);\n";

            $s = "        ";

            // om_validite_debut < om_validite_fin
            $content .= "\n";
            $content .= $s."// gestion des dates de validites\n";
            $content .= $s."\$date_debut = \$this->valF['om_validite_debut'];\n";
            $content .= $s."\$date_fin = \$this->valF['om_validite_fin'];\n";
            $content .= "\n";
            $content .= $s."if (\$date_debut != '' and \$date_fin != '') {\n";
            $content .= $s."\n";
            $content .= $s."    \$date_debut = explode('-', \$this->valF['om_validite_debut']);\n";
            $content .= $s."    \$date_fin = explode('-', \$this->valF['om_validite_fin']);\n";
            $content .= "\n";
            $content .= $s."    \$time_debut = mktime(0, 0, 0, \$date_debut[1], \$date_debut[2],\n";
            $content .= $s."                         \$date_debut[0]);\n";
            $content .= $s."    \$time_fin = mktime(0, 0, 0, \$date_fin[1], \$date_fin[2],\n";
            $content .= $s."                         \$date_fin[0]);\n";
            $content .= "\n";
            $content .= $s."    if (\$time_debut > \$time_fin or \$time_debut == \$time_fin) {\n";
            $content .= $s."        \$this->correct = false;\n";
            $content .= $s."        \$this->addToMessage(__('La date de fin de validite doit etre future a la de debut de validite.'));\n";
            $content .= $s."    }\n";
            $content .= $s."}\n";

            $content .= "    }\n";
        }
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode get_default_libelle() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_get_default_libelle() {
        $template = '
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("%s");
    }
';
        //
        return sprintf(
            $template,
            $this->get_libelle_of($this->table)
        );
    }

    /**
     * Construit les définitions des méthodes get_var_sql_forminc__sql_*() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_get_var_sql_forminc__sql() {
        if (empty($this->clesecondaire)) {
            return "";
        }
        //
        $template = '
    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_%s() {
        return "%s";
    }
';
        $content = "";
        foreach (array_unique($this->clesecondaire) as $elem) {
            if (isset($this->foreign_tables[$elem])) {
                // recherche de la table et de la cle primaire
                $ftable = $this->foreign_tables[$elem]['foreign_table_name'];
                $fprimary_key = $this->foreign_tables[$elem]['foreign_column_name'];
                $query = "SELECT ";
                $query .= $ftable.'.'.$fprimary_key;
                $query .= ', ';

                // recuperation du libelle
                $libelle = $this->get_libelle_of($ftable);

                $query .= $ftable.'.'.$libelle;
                $query .= " FROM \".DB_PREFIXE.\"".$this->foreign_tables[$elem]["foreign_table_name"];
            } else {
                // on ne passe jamais ici
                $ftable = $elem;
                $query = "SELECT * FROM \".DB_PREFIXE.\"".$elem;
            }
            if ($this->check_om_validite($ftable) == true) {
                $query .=" WHERE ".$this->filter_om_validite($ftable);
            }
            if (isset($this->foreign_tables[$elem])) {
                $query .=" ORDER BY ".$ftable.'.'.$this->get_libelle_of($ftable)." ASC";
            }
            $content .= sprintf(
                $template,
                $elem,
                $query
            );
            // creation de la requete sql_object_by_id
            $fprimary_key = $this->get_primary_key($ftable);
            $flibelle = $this->get_libelle_of($ftable);
            $infos = $this->f->db->tableInfo(DB_PREFIXE.$ftable);
            $query = "SELECT ".$ftable.'.'.$fprimary_key.", ".$ftable.'.'.$flibelle.' ';
            $query .= "FROM \".DB_PREFIXE.\"".$ftable." ";

            if (strtolower(substr($infos[0]['type'], 0, 3)) == 'int') {
                $id = "<idx>";
            } else {
                $id = "'<idx>'";
            }

            $query .= "WHERE ".$fprimary_key." = ".$id."";
            $content .= sprintf(
                $template,
                $elem."_by_id",
                $query
            );
        }
        return $content;
    }

    /**
     * Construit la définition de la méthode get_var_sql_forminc__champs() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_get_var_sql_forminc__champs() {
        $template = '
    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(%s
        );
    }
';
        //
        $array_content = "";
        foreach ($this->info as $elem) {
            $array_content .= sprintf(
                '
            "%s",',
                $elem["name"]
            );
        }
        return sprintf(
            $template,
            $array_content
        );
    }

    /**
     * Construit la définition de la méthode setType() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_settype() {
        //
        $template_meth_settype = '
    /**
     *
     */
    function setType(&$form, $maj) {
        // Récupération du mode de l\'action
        $crud = $this->get_action_crud($maj);

        // MODE AJOUTER
        if ($maj == 0 || $crud == \'create\') {%s
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == \'update\') {%s
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == \'delete\') {%s
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == \'read\') {%s
        }

    }
';
        //
        return sprintf(
            //
            $template_meth_settype,
            //
            $this->def_obj_meth_settype_by_maj(0),
            //
            $this->def_obj_meth_settype_by_maj(1),
            //
            $this->def_obj_meth_settype_by_maj(2),
            //
            $this->def_obj_meth_settype_by_maj(3)
        );
    }

    /**
     * Construit une partie de la définition de la méthode setType().
     *
     * Methode permettant de definir le widget de formulaire a utiliser
     * en fonction du type de champ dans la base de donnees
     *
     * @param integer $maj Valeur de l'action de formulaire pour laquelle on
     *                     souhaite définir les widgets.
     *
     * @return string
     */
    function def_obj_meth_settype_by_maj($maj) {
        // Niveau d'arborescence 0
        $template_settype_0 = '
            $form->setType("%s", "%s");';
        // Niveau d'arborescence 1
        $template_settype_1 = '
                $form->setType("%s", "%s");';
        // Niveau d'arborescence 2
        $template_settype_2 = '
                    $form->setType("%s", "%s");';
        //
        $template_settype_widgets_specifiques = '
            if ($this->retourformulaire == "") {%s
            } else {%s
            }';
        //
        $template_settype_multi = '
            if ($_SESSION["niveau"] == 2) {%s
            } else {%s
            }';
        $template_settype_date_om_validite = '
            if ($this->f->isAccredited(array($this->table."_modifier_validite", $this->table, ))) {%s
            } else {%s
            }';
        //
        $template_settype_retourformulaire = '
            if ($this->is_in_context_of_foreign_key("%s", $this->retourformulaire)) {%s
            } else {%s
            }';
        //
        $template_settype_retourformulaire_multi = '
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {%s
                } else {%s
                }
            } else {
                if($_SESSION["niveau"] == 2) {%s
                } else {%s
                }
            }';
        //
        $tableobj = "";
        //
        foreach($this->info as $elem) {

            // Gestion de la clé primaire
            if ($elem['name'] == $this->primary_key) {
                if ($maj == 0) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], ($this->typecle == "N" ? "hidden" : "text"));
                } elseif ($maj == 1) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                } elseif ($maj == 2) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                } elseif ($maj == 3) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], "static");
                }
                // On passe à l'itération suivante de la boucle
                continue;
            }

            // Gestion des clés secondaires
            // XXX Ce ne sont pas les bonnes clés secondaires voir foreign_keys
            if (!empty($this->clesecondaire)) {
                //
                if (in_array($elem['name'], $this->clesecondaire)) {
                    //
                    $elem1 = $elem['name'];
                    //
                    if ($elem['name'] == "om_collectivite") {
                        //
                        if ($maj == 0 || $maj == 1) {
                            $tableobj .= sprintf(
                                $template_settype_retourformulaire_multi,
                                sprintf($template_settype_2, $elem['name'], "selecthiddenstatic"),
                                sprintf($template_settype_2, $elem['name'], "hidden"),
                                sprintf($template_settype_2, $elem['name'], "select"),
                                sprintf($template_settype_2, $elem['name'], "hidden")
                            );
                        } elseif ($maj == 2) {
                            $tableobj .= sprintf(
                                $template_settype_multi,
                                sprintf($template_settype_1, $elem['name'], "selectstatic"),
                                sprintf($template_settype_1, $elem['name'], "hidden")
                            );
                        } elseif ($maj == 3) {
                            $tableobj .= sprintf(
                                $template_settype_retourformulaire_multi,
                                sprintf($template_settype_2, $elem['name'], "selectstatic"),
                                sprintf($template_settype_2, $elem['name'], "hidden"),
                                sprintf($template_settype_2, $elem['name'], "selectstatic"),
                                sprintf($template_settype_2, $elem['name'], "hidden")
                            );
                        }
                    } else {
                        if ($maj == 0 || $maj == 1) {
                            $tableobj .= sprintf(
                                $template_settype_retourformulaire,
                                $this->foreign_tables[$elem1]['foreign_table_name'],
                                sprintf($template_settype_1, $elem['name'], "selecthiddenstatic"),
                                sprintf($template_settype_1, $elem['name'], "select")
                            );
                        } elseif ($maj == 2) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "selectstatic");
                        } elseif ($maj == 3) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "selectstatic");
                        }
                    }
                    // On passe à l'itération suivante de la boucle
                    continue;
                }
            }

            //
            switch ($elem['type']) {
                case "date" :
                    if ($maj == 3) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "datestatic");
                    } elseif ($maj == 0 || $maj == 1) {
                        //
                        if ($elem['name'] == 'om_validite_debut' || $elem['name'] == 'om_validite_fin') {
                            $tableobj .= sprintf(
                                $template_settype_date_om_validite,
                                sprintf($template_settype_1, $elem['name'], "date"),
                                sprintf($template_settype_1, $elem['name'], "hiddenstaticdate")
                            );
                        } else {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "date");
                        }
                    } elseif ($maj == 2) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                    }
                    // On sort du switch
                    break;
                case "blob" :
                    if ($maj == 0 || $maj == 1) {
                        if (strpos($elem['name'], '_om_htmletatex') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "htmlEtatEx");
                        } elseif (strpos($elem['name'], '_om_htmletat') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "htmlEtat");
                        } elseif (strpos($elem['name'], '_om_html') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "html");
                        } else {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "textarea");
                        }
                    } elseif ($maj == 3) {
                        if (strpos($elem['name'], '_html') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "htmlstatic");
                        } else {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "textareastatic");
                        }
                    } elseif ($maj == 2) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                    }
                    // On sort du switch
                    break;
                case "geom" :
                    if ($maj == 0 || $maj == 1 || $maj == 2 || $maj == 3) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "geom");
                    }
                    // On sort du switch
                    break;
                case "bool" :
                    if ($maj == 0 || $maj == 1) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "checkbox");
                    } elseif ($maj == 3) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "checkboxstatic");
                    } elseif ($maj == 2) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                    }
                    // On sort du switch
                    break;
                default :
                    // Gestion des fichiers
                    if (in_array($elem["name"], $this->_om_file_fields) === true) {
                        if ($maj == 0) {
                            $tableobj .= sprintf(
                                $template_settype_widgets_specifiques,
                                sprintf($template_settype_1, $elem['name'], "upload"),
                                sprintf($template_settype_1, $elem['name'], "upload2")
                            );
                        } elseif ($maj == 1) {
                            $tableobj .= sprintf(
                                $template_settype_widgets_specifiques,
                                sprintf($template_settype_1, $elem['name'], "upload"),
                                sprintf($template_settype_1, $elem['name'], "upload2")
                            );
                        } elseif ($maj == 2) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "filestatic");
                        } elseif ($maj == 3) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "file");
                        }
                    } else {
                        // Cas standards
                        if ($maj == 0) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "text");
                        } elseif ($maj == 1) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "text");
                        } elseif ($maj == 2) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                        } elseif ($maj == 3) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "static");
                        }
                    }
            } // switch
        }
        //
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setvalF() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setvalf() {
        //
        $content = "";
        // Entête de la methode
        $content .= "\n";
        $content .= "\n    function setvalF(\$val = array()) {";
        $content .= "\n        //affectation valeur formulaire";
        // Boucle sur chaque colonne
        foreach ($this->info as $elem) {

            // Test sur le type de données
            if ($elem['type'] == "date") {

                // Gestion des champs de type DATE

                // Si la valeur renvoyée par le formulaire n'est pas numérique
                $content .= "\n        if (\$val['".$elem['name']."'] != \"\") {";
                // On affecte la valeur renvoyée retraitée par la méthode de
                // retraitement des dates
                $content .= "\n            \$this->valF['".$elem['name']."'] = \$this->dateDB(\$val['".$elem['name']."']);";
                $content .= "\n        }";

                 // Test sur l'attribut NULL du champ
                if ($elem['notnull'] == 'f') {

                    // Soit le champ accepte la valeur NULL
                    // On affecte la valeur NULL
                    $content .= " else {";
                    $content .= "\n            \$this->valF['".$elem['name']."'] = NULL;";
                    $content .= "\n        }";
                }

            } elseif ($elem['type'] == "bool") {

                // Gestion des champs de type BOOLaffectation valeur formulaire

                //
                $content .= "\n        if (\$val['".$elem['name']."'] == 1 || \$val['".$elem['name']."'] == \"t\" || \$val['".$elem['name']."'] == \"Oui\") {";
                $content .= "\n            \$this->valF['".$elem['name']."'] = true;";
                $content .= "\n        } else {";
                $content .= "\n            \$this->valF['".$elem['name']."'] = false;";
                $content .= "\n        }";

            } elseif ($elem['type'] == "geom") {
                // gestion des champs geom non pris en compte si valeur vide
                $content .= "\n        if (\$val['".$elem['name']."'] == \"\") {";
                $content .= "\n            unset(\$this->valF['".$elem['name']."']);";
                $content .="\n        } else {";
                // On affecte la valeur du formulaire directement
                $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                $content .="\n        }";

            } elseif ($elem['type'] == "int" or $elem['type'] == "float") {

                // Gestion des champs de type INT

                // Si la valeur renvoyée par le formulaire n'est pas numérique
                // => ceci n'est pas sensé arriver car une fonction javascript
                //    vérifie que la saisie est composée uniquement de chiffres
                //    mais ce cas inclut aussi la chaine vide ce qui par contre
                //    arrive fréquemment
                $content .="\n        if (!is_numeric(\$val['".$elem['name']."'])) {";
                // Test sur l'attribut NULL du champ
                if ($elem['notnull'] == 'f') {

                    // Soit le champ accepte la valeur NULL
                    // On affecte la valeur NULL
                    $content .="\n            \$this->valF['".$elem['name']."'] = NULL;";

                } elseif ($elem['notnull'] == 't'
                          && in_array($elem["name"], $this->_columns_notnull)) {

                    // Soit le champ n'accepte pas la valeur NULL et fait partie
                    // des champs requis
                    // On affecte la valeur ""
                    // => sous entendu le champ n'accepte pas la valeur NULL
                    //    donc il est obligatoire donc la méthode vérifier
                    //    des champs requis empêchera le passage de la valeur à
                    //    la base
                    $content .="\n            \$this->valF['".$elem['name']."'] = \"\"; // -> requis";

                } else {

                    // Le cas restant est : le champ n'accepte pas la valeur
                    // NULL et a une valeur par défaut dans la base
                    //
                    // XXX ici il faut affecter la valeur du default de la base
                    //
                    // On affecte la valeur 0
                    $content .="\n            \$this->valF['".$elem['name']."'] = 0; // -> default";

                }
                // Sinon si la valeur renvoyée par le formulaire est numérique
                $content .="\n        } else {";
                // Test si on se trouve sur le champ 'om_collectivite'
                if ($elem['name'] == "om_collectivite") {

                    // Champ 'om_collectivite'
                    // => sous entendu ce champ est forcément de type INT

                    // Si on est en mode MONO
                    $content .="\n            if(\$_SESSION['niveau']==1) {";
                    // On affecte la valeur de la collectivité depuis la
                    // variable de SESSION
                    $content .="\n                \$this->valF['".$elem['name']."'] = \$_SESSION['collectivite'];";
                    // Si on est en mode MULTI
                    $content .="\n            } else {";
                    // On affecte la valeur du formulaire directement
                    $content .="\n                \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                    $content .="\n            }";

                } else {

                    // Un autre champ que 'om_collectivite'

                    // On affecte la valeur du formulaire directement
                    $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";

                }
                //
                $content .="\n        }";

            } elseif ($elem['type'] == "string") {

                // Gestion des champs de type STRING

                // Test sur l'attribut NULL du champ
                if ($elem['notnull'] == 'f') {

                    // Si la valeur renvoyée par le formulaire est une chaine vide
                    $content .="\n        if (\$val['".$elem['name']."'] == \"\") {";
                    // Soit le champ accepte la valeur NULL
                    // On affecte la valeur NULL
                    $content .="\n            \$this->valF['".$elem['name']."'] = NULL;";
                    //
                    $content .="\n        } else {";
                    // On affecte la valeur du formulaire directement
                    $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                    //
                    $content .="\n        }";
                } elseif ($elem['notnull'] == 't'
                          && in_array($elem["name"], $this->_columns_notnull)) {

                    // Soit le champ n'accepte pas la valeur NULL et fait partie
                    // des champs requis
                    // On affecte la valeur ""
                    // => sous entendu le champ n'accepte pas la valeur NULL
                    //    donc il est obligatoire donc la méthode vérifier
                    //    des champs requis empêchera le passage de la valeur à
                    //    la base
                    $content .="\n        \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";

                } else {

                    // Si la valeur renvoyée par le formulaire est une chaine vide
                    $content .="\n        if (\$val['".$elem['name']."'] == \"\") {";
                    // Le cas restant est : le champ n'accepte pas la valeur
                    // NULL et a une valeur par défaut dans la base
                    //
                    // XXX ici il faut affecter la valeur du default de la base
                    //
                    // On affecte la valeur ""
                    $content .="\n            \$this->valF['".$elem['name']."'] = \"\"; // -> default";
                    //
                    $content .="\n        } else {";
                    // On affecte la valeur du formulaire directement
                    $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                    //
                    $content .="\n        }";

                }
            } else {

                // On affecte la valeur du formulaire directement
                $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";

            }
        }
        // Pied de la methode
        $content .="\n    }";
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setOnchange() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setonchange() {
        //
        $counter = 0;
        //
        $tableobj = "\n\n    function setOnchange(&\$form, \$maj) {";
        $tableobj .= "\n    //javascript controle client";
        //
        foreach ($this->info as $elem) {
            //
            if ($elem['type'] == 'date') {
                //
                $counter++;
                //
                $tableobj.="\n        \$form->setOnchange('".$elem['name']."','fdate(this)');";
            } elseif($elem['type'] == 'int') {
                //
                $counter++;
                //
                $tableobj.="\n        \$form->setOnchange('".$elem['name']."','VerifNum(this)');";
            } elseif($elem['type'] == 'float') {
                //
                $counter++;
                //
                $tableobj.="\n        \$form->setOnchange('".$elem['name']."','VerifFloat(this)');";
            }
        }
        //
        $tableobj.="\n    }";
        // Si aucun élément n'est affiché alors inutile de générer la méthode
        if ($counter == 0) {
            //
            $tableobj = "";
        }
        // On renvoi le contenu de la méthode à générer
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setTaille() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_settaille() {
        //
        $content = "";
        // Entete de la methode
        $content .= "\n";
        $content .= "    /**\n     * Methode setTaille\n     */\n";
        $content .= "    function setTaille(&\$form, \$maj) {\n";
        //
        foreach ($this->info as $elem) {
            //
            $content .="        \$form->setTaille(\"".$elem['name']."\", ".(isset($elem['taille_affichage']) ? $elem['taille_affichage'] : $elem['len']).");\n";
        }
        // Pied de la methode
        $content .="    }\n";
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setMax() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setmax() {
        //
        $content = "";
        // Entete de la methode
        $content .= "\n";
        $content .= "    /**\n     * Methode setMax\n     */\n";
        $content .= "    function setMax(&\$form, \$maj) {\n";
        //
        foreach ($this->info as $elem) {
            //
            $content .= "        \$form->setMax(\"".$elem['name']."\", ".(isset($elem['max_saisie']) ? $elem['max_saisie'] : $elem['len']).");\n";
        }
        // Pied de la methode
        $content .="    }\n";
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setLib() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setlib() {
        $tableobj="\n\n    function setLib(&\$form, \$maj) {";
        $tableobj.="\n    //libelle des champs";

        foreach($this->info as $elem){

            $tableobj.="\n        \$form->setLib('".$elem['name']."', __('".$elem['name']."')";

            $tableobj.=');';
        }

        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setSelect() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setselect() {
        //
        $template_meth_setselect = '
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
%s
    }
';
        //
        $contenu = "";
        //
        if (!empty($this->clesecondaire)) {
            //
            $template_meth_setselect_clesecondaire ='
        // %s
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "%s",
            $this->get_var_sql_forminc__sql("%s"),
            $this->get_var_sql_forminc__sql("%s_by_id"),
            %s
        );';
            //
            foreach($this->clesecondaire as $elem) {
                //
                if (isset($this->foreign_tables[$elem])) {
                    $ftable = $this->foreign_tables[$elem]['foreign_table_name'];
                } else {
                    $ftable = $elem;
                }
                //
                $contenu .= sprintf(
                    $template_meth_setselect_clesecondaire,
                    $elem,
                    $elem,
                    $elem,
                    $elem,
                    ($this->check_om_validite($ftable) == true ? "true" : "false")
                );
            }
        }
        //
        if (!empty($this->geom)) {
            //
            $template_meth_setselect_geom = '
        // %s
        if ($maj == 1 || $maj == 3) {
            $contenu = array();
            $contenu[0] = array("%s", $this->getParameter("idx"), "%s");
            $form->setSelect("%s", $contenu);
        }';
            // appel pour multigéométrie
            $nbgeom = 0;
            //
            foreach ($this->geom as $elem) {
                //
                $contenu .= sprintf(
                    $template_meth_setselect_geom,
                    $elem,
                    $this->table,
                    $nbgeom,
                    $elem
                );
                //
                $nbgeom += 1;
            }
        }
        //
        return sprintf(
            $template_meth_setselect,
            $contenu
        );
    }

    /**
     * Construit la définition de la méthode setVal() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setval() {
        //
        $contenu = "";
        // si $this->multi = 2
        // valorisation de la variable $this->retourformulaire
        // valorisation champ cle formulaire en creation et modification
        if ($this->multi == 2) {
            $contenu="\n\n    function setVal(&\$form, \$maj, \$validation, &\$dnu1 = null, \$dnu2 = null) {";
            $contenu.="\n        if(\$validation==0 and \$maj==0 and \$_SESSION['niveau']==1) {";
            $contenu.="\n            \$form->setVal('om_collectivite', \$_SESSION['collectivite']);";
            $contenu.="\n        }// fin validation";
            $contenu.="\n        \$this->set_form_default_values(\$form, \$maj, \$validation);";
            $contenu.="\n    }// fin setVal";
        }
        return $contenu;
    }

    /**
     * Construit la définition de la méthode setValsousformulaire() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setvalsousformulaire() {
        // si cle secondaire
        // valorisation de la variable $this->retourformulaire
        // valorisation champ cle formulaire en creation et modification
        $contenu="\n\n    function setValsousformulaire(&\$form, \$maj, \$validation, \$idxformulaire, \$retourformulaire, \$typeformulaire, &\$dnu1 = null, \$dnu2 = null) {";
        $contenu.="\n        \$this->retourformulaire = \$retourformulaire;";
        //
        if ($this->multi == 2 && $this->table != "om_collectivite") {
            //
            $contenu.="\n        if(\$validation==0 and \$maj==0 and \$_SESSION['niveau']==1) {";
            $contenu.="\n            \$form->setVal('om_collectivite', \$_SESSION['collectivite']);";
            $contenu.="\n        }// fin validation";
        }
        // clesecondaire
        if (!empty($this->clesecondaire)) {
            $contenu_tmp = "\n        if(\$validation == 0) {";

            $ftables = array();
            foreach ($this->foreign_tables as $key => $infos) {

                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }

            $multiple_fkeys = array();
            foreach ($ftables as $table => $columns) {
                if (count($columns) > 1) {
                    $multiple_fkeys = array_merge($multiple_fkeys, $columns);
                }
            }

            $code_exists = false;

            foreach ($this->clesecondaire as $elem){
                if (!in_array($elem, $multiple_fkeys)) {
                    $code_exists = true;
                    $contenu_tmp .= "\n            if(\$this->is_in_context_of_foreign_key('".$this->foreign_tables[$elem]['foreign_table_name']."', \$this->retourformulaire))";
                    $contenu_tmp .= "\n                \$form->setVal('".$elem."', \$idxformulaire);";
                }
            }
            $contenu_tmp .= "\n        }// fin validation";

            if ($code_exists == true) {
                $contenu .= $contenu_tmp;
            }

            $code_exists = false;
            $contenu_tmp = "\n        if (\$validation == 0 and \$maj == 0) {";

            $ftables = array();
            foreach ($this->foreign_tables as $key => $infos) {

                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }

            $multiple_fkeys = array();
            foreach ($ftables as $table => $columns) {
                if (count($columns) > 1) {
                    $multiple_fkeys = array_merge($multiple_fkeys, $columns);
                }
            }

            foreach ($this->clesecondaire as $elem){
                if (in_array($elem, $multiple_fkeys)) {
                    $code_exists = true;
                    $contenu_tmp .= "\n            if(\$this->is_in_context_of_foreign_key('".$this->foreign_tables[$elem]['foreign_table_name']."', \$this->retourformulaire))";
                    $contenu_tmp .= "\n                \$form->setVal('".$elem."', \$idxformulaire);";
                }
            }
            $contenu_tmp .= "\n        }// fin validation";

            if ($code_exists == true) {
                $contenu .= $contenu_tmp;
            }

        } // fin clescondaire
        $contenu.="\n        \$this->set_form_default_values(\$form, \$maj, \$validation);";
        $contenu.="\n    }// fin setValsousformulaire";
        return $contenu;
        }

    /**
     * Construit la définition de la méthode clesecondaire() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_clesecondaire() {
        //
        $content = "";
        // Si il existe des sous-formulaires on surcharge la methode sinon
        // on ne fait rien
        if (!empty($this->sousformulaires)) {
            // Entete de la methode
            $content .= "\n";
            $content .= "    /**\n     * Methode clesecondaire\n     */\n";
            $content .= "    function cleSecondaire(\$id, &\$dnu1 = null, \$val = array(), \$dnu2 = null) {\n";
            $content .= "        // On appelle la methode de la classe parent\n";
            $content .= "        parent::cleSecondaire(\$id);\n";

            // boucle sur chaque sous-formulaire
            foreach ($this->other_tables as $config) {

                // $config est de la forme "nom_table.nom_colonne"
                $infos = array();
                $infos =  explode('.', $config);

                // table  -> table ayant une reference vers la table actuelle
                // column -> colonne de cette table faisant reference
                $table = $infos[0];
                $column = $infos[1];

                $content .= "        // Verification de la cle secondaire : ".$table."\n";
                $content .= "        \$this->rechercheTable(\$this->f->db, \"".$table."\", \"".$column."\", \$id);\n";
            }

            // Pied de la methode
            $content .="    }\n";
        }
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setId() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setid() {
        $tableobj="\n\n    function setId(&\$dnu1 = null) {";
        $tableobj.="\n    //numero automatique";
        $tableobj.="\n        \$this->valF[\$this->clePrimaire] = \$this->f->db->nextId(DB_PREFIXE.\$this->table);";
        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setValFAjout() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setvalfajout() {
        $tableobj="\n\n    function setValFAjout(\$val = array()) {";
        $tableobj.="\n    //numero automatique -> pas de controle ajout cle primaire";
        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode verifierAjout() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_verifierajout() {
        $tableobj="\n\n    function verifierAjout(\$val = array(), &\$dnu1 = null) {";
        $tableobj.="\n    //numero automatique -> pas de verfication de cle primaire";
        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<TABLE>.pdf.inc.php].
     *
     * Cette méthode permet de générer l'intégralité du script.
     *
     * @param mixed $dyn Fichier de paramétrage.
     *
     * @todo public
     * @return string
     */
    function table_sql_pdfinc($dyn = null) {
        // pdf liste de fichier ancienne version openmairie 1.00
        // les blob (mysql)ne sont pas pris en compte dans le tableau
        // les dates sont au format français
        $temp= $this->def_php_script_header();
        // parametrage pdf standard
        $longueurtableau= 280;
        $orientation='L';// orientation P-> portrait L->paysage";
        $format='A4';// format A3 A4 A5;
        $police='arial';
        $margeleft=10;// marge gauche;
        $margetop=5;// marge haut;
        $margeright=5;//  marge droite;
        $border=1; // 1 ->  bordure 0 -> pas de bordure";
        $C1=0;// couleur texte  R";
        $C2=0;// couleur texte  V";
        $C3=0;// couleur texte  B";
        $size=10; //taille POLICE";
        $height=4.6; // hauteur ligne tableau ";
        $align='L';
        // fond 2 couleurs
        $fond=1;// 0- > FOND transparent 1 -> fond";
        $C1fond1=234;// couleur fond  R ";
        $C2fond1=240;// couleur fond  V ";
        $C3fond1=245;// couleur fond  B ";
        $C1fond2=255;// couleur fond  R";
        $C2fond2=255;// couleur fond  V";
        $C3fond2=255;// couleur fond  B";
        // spe openelec
        $flagsessionliste=0;// 1 - > affichage session liste ou 0 -> pas d'affichage";
        // titre
        $bordertitre=0; // 1 ->  bordure 0 -> pas de bordure";
        $aligntitre='L'; // L,C,R";
        $heightitre=10;// hauteur ligne titre";
        $grastitre='B';//\$gras='B' -> BOLD OU \$gras=''";
        $fondtitre=0; //0- > FOND transparent 1 -> fond";
        $C1titrefond=181;// couleur fond  R";
        $C2titrefond=182;// couleur fond  V";
        $C3titrefond=188;// couleur fond  B";
        $C1titre=75;// couleur texte  R";
        $C2titre=79;// couleur texte  V";
        $C3titre=81;// couleur texte  B";
        $sizetitre=15;
        // entete colonne
        $flag_entete=1;//entete colonne : 0 -> non affichage , 1 -> affichage";
        $fondentete=1;// 0- > FOND transparent 1 -> fond";
        $heightentete=10;//hauteur ligne entete colonne";
        $C1fondentete=210;// couleur fond  R";
        $C2fondentete=216;// couleur fond  V";
        $C3fondentete=249;// couleur fond  B";
        $C1entetetxt=0;// couleur texte R";
        $C2entetetxt=0;// couleur texte V";
        $C3entetetxt=0;// couleur texte B";
        $C1border=159;// couleur texte  R";
        $C2border=160;// couleur texte  V";
        $C3border=167;// couleur texte  B";
        $bt=1;// border 1ere  et derniere ligne  du tableau par page->0 ou 1";
        if (file_exists ("../gen/dyn/pdf.inc.php")){
            include ("../gen/dyn/pdf.inc.php");
            $this->msg.="<br />".__("Chargement du parametrage")." ../gen/dyn/pdf.inc.php";
        } elseif (file_exists ("../gen/dyn/pdf.inc")){
            include ("../gen/dyn/pdf.inc");
            $this->msg.="<br />".__("Chargement du parametrage")." ../gen/dyn/pdf.inc";
        }
        $temp.="\n\$DEBUG=0;";
        // param sousetat.inc.php
        $temp.="\n\$orientation='".$orientation."';// orientation P-> portrait L->paysage";
        $temp.="\n\$format='".$format."';// format A3 A4 A5";
        $temp.="\n\$police='".$police."';";
        $temp.="\n\$margeleft=".$margeleft.";// marge gauche";
        $temp.="\n\$margetop=".$margetop.";// marge haut";
        $temp.="\n\$margeright=".$margeright.";//  marge droite";
        $temp.="\n\$border=".$border."; // 1 ->  bordure 0 -> pas de bordure";
        $temp.="\n\$C1=".$C1.";// couleur texte  R";
        $temp.="\n\$C2=".$C2.";// couleur texte  V";
        $temp.="\n\$C3=".$C3.";// couleur texte  B";
        $temp.="\n\$size=".$size."; //taille POLICE";
        $height=intval($height); // bug si virgule 4,6
        $temp.="\n\$height=".$height."; // hauteur ligne tableau ";
        $temp.="\n\$align='".$align."';";
        $temp.="\n\$fond=".$fond.";// 0- > FOND transparent 1 -> fond";
        $temp.="\n\$C1fond1=".$C1fond1.";// couleur fond  R 241";
        $temp.="\n\$C2fond1=".$C2fond1.";// couleur fond  V 241";
        $temp.="\n\$C3fond1=".$C3fond1.";// couleur fond  B 241";
        $temp.="\n\$C1fond2=".$C1fond2.";// couleur fond  R";
        $temp.="\n\$C2fond2=".$C2fond2.";// couleur fond  V";
        $temp.="\n\$C3fond2=".$C3fond2.";// couleur fond  B";
        $temp.="\n\$libtitre='Liste ".DB_PREFIXE.$this->table."'; // libelle titre";
        $temp.="\n\$flagsessionliste=".$flagsessionliste.";// 1 - > affichage session liste ou 0 -> pas d'affichage";
        $temp.="\n\$bordertitre=".$bordertitre."; // 1 ->  bordure 0 -> pas de bordure";
        $temp.="\n\$aligntitre='".$aligntitre."'; // L,C,R";
        $temp.="\n\$heightitre=".$heightitre.";// hauteur ligne titre";
        $temp.="\n\$grastitre='".$grastitre."';//\$gras='B' -> BOLD OU \$gras=''";
        $temp.="\n\$fondtitre=".$fondtitre."; //0- > FOND transparent 1 -> fond";
        $temp.="\n\$C1titrefond=".$C1titrefond.";// couleur fond  R";
        $temp.="\n\$C2titrefond=".$C2titrefond.";// couleur fond  V";
        $temp.="\n\$C3titrefond=".$C3titrefond.";// couleur fond  B";
        $temp.="\n\$C1titre=".$C1titre.";// couleur texte  R";
        $temp.="\n\$C2titre=".$C2titre.";// couleur texte  V";
        $temp.="\n\$C3titre=".$C3titre.";// couleur texte  B";
        $temp.="\n\$sizetitre=".$sizetitre.";";
        $temp.="\n\$flag_entete=".$flag_entete.";//entete colonne : 0 -> non affichage , 1 -> affichage";
        $temp.="\n\$fondentete=".$fondentete.";// 0- > FOND transparent 1 -> fond";
        $temp.="\n\$heightentete=".$heightentete.";//hauteur ligne entete colonne";
        $temp.="\n\$C1fondentete=".$C1fondentete.";// couleur fond  R";
        $temp.="\n\$C2fondentete=".$C2fondentete.";// couleur fond  V";
        $temp.="\n\$C3fondentete=".$C3fondentete.";// couleur fond  B";
        $temp.="\n\$C1entetetxt=".$C1entetetxt.";// couleur texte R";
        $temp.="\n\$C2entetetxt=".$C2entetetxt.";// couleur texte V";
        $temp.="\n\$C3entetetxt=".$C3entetetxt.";// couleur texte B";
        $temp.="\n\$C1border=".$C1border.";// couleur texte  R";
        $temp.="\n\$C2border=".$C2border.";// couleur texte  V";
        $temp.="\n\$C3border=".$C3border.";// couleur texte  B";
        // calcul de la taille des colones
        $i=0;
        $j=0;
        $longueur=0; // ***
        $temp1=$longueurtableau;
        $temp3="";
        //$indice=2.5; // indice taille affichage
        $indice =$longueurtableau/$this->longueur;
        $limite =$longueurtableau/2.5;
        $troplong=0;
        $dernierchamp=0;
        if($indice<2.5){
            $this->msg.="<br />->affichage colone incomplet ".$indice."  < 2.5 ";
            foreach($this->info as $elem){
                if($troplong==0){     // ***
                    if($elem['type']!="blob"){
                        $longueur = $longueur + $elem['len'];
                        //$this->msg.="<br>->".$elem['name'].' longueur '.$longueur." ***".$troplong;
                        $dernierchamp++;
                    }
                    //*** A TESTER Longueur de champ
                    if($longueur>=$limite){
                        $troplong=1;
                        $longueur=    $longueur - $elem['len'];
                    }
                }//***
            }
            $dernierchamp=$dernierchamp-2;
            //$this->msg.="<br>->".$dernierchamp.' longueur '.$limite;
            $indice=$longueurtableau/$longueur;
        } else {
            $this->msg.="<br />->affichage colone ok ".$indice." >= 2.5";
            $dernierchamp=count($this->info)-1;
            if($this->info[$dernierchamp]['type']=="blob") { //mysql
                $dernierchamp=$dernierchamp-1;
            }
        }
        $seulpassage=0;
        foreach($this->info as $elem){
            if ($elem['type']!="blob") {
                if ($j<$dernierchamp) {
                    $temp2= $elem['len']*intval($indice);
                    $temp.="\n\$l".$i."=".$temp2."; // largeur colone -> champs ".$i." - ".$elem['name'];
                    $temp.="\n\$be".$i."='L';// border entete colone";
                    $temp.="\n\$b".$i."='L';// border cellule colone";
                    $temp.="\n\$ae".$i."='C'; // align cellule entete colone";
                    $temp.="\n\$a".$i."='L';";
                    $temp1 = $temp1-$temp2;
                    $temp4 = "def_champaffichedatepdf".OM_DB_PHPTYPE;// fonction date
                    if ($elem["type"]=="date") {
                        $temp3.= $this->$temp4($elem["name"]).",";
                    } else {
                        $temp3.=$elem['name'].", ";
                    }
                } else {
                    if ($seulpassage == 0) {
                        $temp.="\n\$l".$i."=".$temp1."; // largeur colone -> champs".$i." - ".$elem['name'];
                        $temp.="\n\$be".$i."='LR';// border entete colone";
                        $temp.="\n\$b".$i."='LR';// border cellule colone";
                        $temp.="\n\$ae".$i."='C'; // align cellule entete colone";
                        $temp.="\n\$a".$i."='L';";
                        $temp4 = "def_champaffichedatepdf".OM_DB_PHPTYPE;// fonction date
                        if ($elem["type"]=="date") {
                            $temp3.= $this->$temp4($elem["name"]);
                        } else {
                            $temp3.=$elem['name'];
                        }
                        $seulpassage=1;
                    }
                }
                $i++; // compteur champ dans pdf
            }
            $j++; // compteur champ dans tableinfo
        }
        $temp.="\n\$widthtableau=".$longueurtableau.";";
        $temp.="\n\$bt=".$bt.";// border 1ere  et derniere ligne  du tableau par page->0 ou 1";
        $temp.="\n\$sql=\"select ".$temp3." from \".DB_PREFIXE.\"".$this->table."\";";
        $temp.="\n";
        return $temp;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<ELEM>.reqmo.inc.php].
     *
     * Cette méthode permet de générer l'intégralité du script.
     *
     * @param string $cle Clé secondaire éventuelle.
     *
     * @todo public
     * @return string
     */
    function table_sql_reqmoinc($cle = "") {
        // cle = cle secondaire -> select
        $contenu=$this->def_php_script_header();
        $contenu.="\n\$reqmo['libelle'] = __('reqmo-libelle-".$this->table."');";
        $contenu.="\n\$reqmo['reqmo_libelle'] = __('reqmo-libelle-".$this->table."');";
        if ($cle == ""){
            $contenu .= "\n\$ent = __('".$this->table."');";
        } else {
            $contenu .= "\n\$ent = __('".$this->table.'_'.$cle."');";
        }
        // sql
        $temp = "select ";
        $temp1="";
        $temp2="array(";
        $temp3="";
        foreach($this->info as $elem){
            if($cle==""){
                $temp.=" [".$elem['name']."],";
                $temp1.= "\n\$reqmo['".$elem['name']."']='checked';";
                $temp2.="'".$elem['name']."',";
            }elseif($cle==$elem['name']){
                    $temp1.= "\n\$reqmo['".$elem['name']."']=\"select * from \".DB_PREFIXE.\"".$elem['name']."\";";
            }else{
                    $temp.=" [".$elem['name']."],";//sql
                    $temp1.= "\n\$reqmo['".$elem['name']."']='checked';";
                    $temp2.="'".$elem['name']."',";        // tri
            }
        }
        $temp =  substr($temp, 0, strlen($temp)-1);
        if($cle!="") {
            $temp3 = "where ".$cle." = '[".$cle."]'"; // sql
        }
        $contenu.="\n\$reqmo['sql']=\"".$temp." from \".DB_PREFIXE.\"".$this->table." ".$temp3." order by [tri]\";";
        $temp2 =  substr($temp2, 0, strlen($temp2)-1);
        $contenu.="".$temp1;
        $contenu.="\n\$reqmo['tri']=".$temp2.");";
        $contenu.="\n";
        return $contenu;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<TABLE>.import.inc.php].
     *
     * Cette méthode permet de générer l'intégralité du script.
     *
     * @todo public
     *
     * @return string
     */
    function table_sql_importinc() {
        // creer un fichier d import
        $i=0;
        $contenu=$this->def_php_script_header();
        $contenu.="\n\$import= \"Insertion dans la table ".$this->table." voir rec/import_utilisateur.inc\";";
        $contenu.="\n\$table= DB_PREFIXE.\"".$this->table."\";";
        if($this->typecle=="N") {
            $contenu.="\n\$id='".$this->primary_key."'; // numerotation automatique";
        } else {
            $contenu.="\n\$id=''; // numerotation non automatique";
        }
        $contenu.="\n\$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour";
        $contenu.="\n\$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement";
        $contenu.="\n\$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon";

        //
        $contenu .= '
/**
 *
 */
$fields = array(';
        //
        foreach ($this->info as $elem) {
            // Initialisation du tableau pour l'élément
            $contenu .= "
    \"".$elem["name"]."\" => array(";
            // Attributs communs à tous les champs
            $contenu .= "
        \"notnull\" => \"".($elem["notnull"]  == 't' ? true : false)."\",
        \"type\" => \"".$elem["type"]."\",
        \"len\" => \"".$elem["len"]."\",";
            // Gestion des clés étrangères (Critère EXIST)
            if (isset($this->foreign_tables[$elem["name"]])) {
                $contenu .= "
        \"fkey\" => array(
            \"foreign_table_name\" => \"".$this->foreign_tables[$elem["name"]]['foreign_table_name']."\",
            \"foreign_column_name\" => \"".$this->foreign_tables[$elem["name"]]['foreign_column_name']."\",
            \"sql_exist\" => \"select * from \".DB_PREFIXE.\"".$this->foreign_tables[$elem["name"]]['foreign_table_name']." where ".$this->foreign_tables[$elem["name"]]["foreign_column_name"]." = '\",
        ),";
            }
            // Fin de l'initialisation du tableau pour l'élément
            $contenu .= "
    ),";
        }
        //
        $contenu .= '
);';
        $contenu.="\n";
        return $contenu;
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour MySQL.
     *
     * Avec alias et séquence d'échappement.
     *
     * @param string $temp  Nom de la colonne date à traiter.
     * @param mixed  $alias Alias éventuel de la colonne date.
     *
     * @return string
     */
    function def_champaffichedatemysql($temp, $alias=null) {
        //
        if ($alias == null) {
            $alias = $temp;
        }
        // avec sequence d echappement
        return "'concat(substring(".$temp.",9,2),\'/\',substring(".$temp.",6,2),\'/\',substring(".$temp.",1,4)) as ".$alias."'";
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour PostGreSQL.
     *
     * Avec alias et séquence d'échappement.
     *
     * @param string $temp  Nom de la colonne date à traiter.
     * @param mixed  $alias Alias éventuel de la colonne date.
     *
     * @return string
     */
    function def_champaffichedatepgsql($temp, $alias=null) {
        //
        if ($alias == null) {
            $alias = $temp;
        }
        // avec sequence d echappement
        return "'to_char(".$temp." ,\'DD/MM/YYYY\') as \"'.__(\"".$alias."\").'\"'";
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour MySQL.
     *
     * Sans alias et séquence d'échappement.
     *
     * @param string $temp Nom de la colonne date à traiter.
     *
     * @return string
     */
    function def_champaffichedatepdfmysql($temp) {
        // avec sequence d echappement
        return "concat(substring(".$temp.",9,2),'/',substring(".$temp.",6,2),'/',substring(".$temp.",1,4)) as ".$temp;
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour PostGreSQL.
     *
     * Sans alias et séquence d'échappement.
     *
     * @param string $temp Nom de la colonne date à traiter.
     *
     * @return string
     */
    function def_champaffichedatepdfpgsql($temp) {
        // sans sequence d echappement
        return "to_char(".$temp." ,'DD/MM/YYYY') as ".$temp;
    }

    // ------------------------------------------------------------------------
    // {{{ END - CONSTRUCTION DES CONTENUS DES SCRIPTS
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - RECUPERATION DES INFORMATIONS SUR LE MODELE
    // ------------------------------------------------------------------------

    /**
     * Initialisation des fichiers de configuration du générateur.
     *
     * Cette méthode permet de récupérer les fichiers configurations
     * pour initialiser les paramètres et permettre leur utilisation
     * dans les méthodes de la classe
     *
     * @return void
     */
    function init_configuration() {
        //
        if (file_exists(PATH_OPENMAIRIE."gen/dyn/gen.inc.php")) {
            include PATH_OPENMAIRIE."gen/dyn/gen.inc.php";
        }
        //
        if (file_exists("../gen/dyn/gen.inc.php")) {
            include "../gen/dyn/gen.inc.php";
        }
        //
        if (isset($core_tables_to_overload) === true || isset($tables_to_overload) === true) {
            if (isset($core_tables_to_overload) === false) {
                $core_tables_to_overload = array();
            }
            if (isset($tables_to_overload) === false) {
                $tables_to_overload = array();
            }
            $this->_tables_to_overload = array_merge(
                $core_tables_to_overload,
                $tables_to_overload
            );
        }
        //
        if (isset($om_dbform_path_override)) {
            $this->_om_dbform_path_override = $om_dbform_path_override;
        }
        //
        if (isset($om_dbform_class_override)) {
            $this->_om_dbform_class_override = $om_dbform_class_override;
        }
    }

    /**
     * Renvoi la valeur d'une option de configuration.
     *
     * @param string $table Libellé de la table.
     * @param string $config Libellé de l'option souhaitée.
     *
     * @return mixed
     */
    function get_config_table_to_overload($table = null, $config = null) {
        if ($table === null) {
            $table = $this->table;
        }
        //
        if (array_key_exists($table, $this->_tables_to_overload) !== true
            || is_array($this->_tables_to_overload[$table]) !== true) {
            //
            return null;
        }
        //
        $table_config = $this->_tables_to_overload[$table];
        if ($config === null) {
            return $table_config;
        }
        //
        if (array_key_exists($config, $table_config) !== true) {
            return null;
        }
        return $table_config[$config];
    }

    /**
     * Renvoi la valeur d'une option de configuration.
     *
     * @param string $table Libellé de la table.
     * @param string $field Libellé du champ.
     * @param string $config Libellé de l'option souhaitée.
     *
     * @return mixed
     */
    function get_config_field_to_overload($table = null, $field = null, $config = null) {
        $table_config__specific_config_for_fields = $this->get_config_table_to_overload(
            $table,
            "specific_config_for_fields"
        );
        if ($table_config__specific_config_for_fields === null) {
            return null;
        }
        //
        if (array_key_exists($field, $table_config__specific_config_for_fields) !== true
            || is_array($table_config__specific_config_for_fields[$field]) !== true) {
            //
            return null;
        }
        //
        $field_config = $table_config__specific_config_for_fields[$field];
        if ($config === null) {
            return $field_config;
        }
        //
        if (array_key_exists($config, $field_config) !== true) {
            return null;
        }
        return $field_config[$config];
    }

    /**
     * Renvoi la valeur d'une option.
     *
     * @param string $option Libellé de l'option souhaitée.
     *
     * @return mixed
     */
    function get_general_option($option = "") {
        //
        if ($option == "") {
            //
            return null;
        }
        //
        if (file_exists("../gen/dyn/gen.inc.php")) {
            //
            include "../gen/dyn/gen.inc.php";
        }
        //
        if (isset($$option)) {
            //
            return $$option;
        } else {
            //
            switch ($option) {
                case "key_constraints_mode":
                    return "constraints";
                    break;
                default:
                    return null;
            }
        }
    }

    /**
     * Renvoi la valeur du cas d'utilisation 'om_validite'.
     *
     * Retourne true si la table traitee actuellement contient les colonnes:
     *      - om_validite_debut
     *      - om_validite_fin
     *
     * Cette methode ne fait aucune requete en base de donnees.
     * Elle se contente de lire les informations analysees par la methode gen().
     *
     * Voir aussi check_om_validite()
     *
     * @return boolean
     */
    function is_om_validite() {
        if ($this->_om_validite_debut == true
            and $this->_om_validite_fin == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vérifie en base l'existence des colonnes 'om_validite' sur une table.
     *
     * Retourne true si la table specifiee contient les colonnes:
     *      - om_validite_debut
     *      - om_validite_fin
     *
     * Cette methode effectue une requete en base de donnees.
     * Utilisez cette methode si la table actuelle != table specifiee.
     *
     * Voir aussi is_om_validite()
     *
     * @param string $table Nom de la table à examiner.
     *
     * @return boolean
     */
    function check_om_validite($table) {

        $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);

        $om_validite_debut = false;
        $om_validite_fin = false;

        foreach ($infos as $column) {

            if ($column['name'] == 'om_validite_debut') {
                $om_validite_debut = true;
            } else if ($column['name'] == 'om_validite_fin') {
                $om_validite_fin = true;
            }
        }

        return $om_validite_debut and $om_validite_fin;
    }

    /**
     * Retourne la condition SQL des objets à date de validité.
     *
     * @param string  $table         Nom de la table.
     * @param boolean $with_operator Indicateur avec ou sans opérateur.
     *
     * @return string
     */
    function filter_om_validite($table, $with_operator = false) {

        if ($with_operator == true) {
            $filtre = ' AND ';
        } else {
            $filtre = '';
        }

        $filtre .= '(('.$table.'.om_validite_debut IS NULL AND ';
        $filtre .= '('.$table.'.om_validite_fin IS NULL OR '.$table.'.om_validite_fin > CURRENT_DATE))';
        $filtre .= ' OR ';
        $filtre .= '('.$table.'.om_validite_debut <= CURRENT_DATE AND ';
        $filtre .= '('.$table.'.om_validite_fin IS NULL OR '.$table.'.om_validite_fin > CURRENT_DATE)))';
        return $filtre;
    }

    /**
     * Initialisation des informations concernant les clés étrangères.
     *
     * Cette méthode permet d'initialiser les deux attributs :
     *  - $this->clesecondaire
     *  - $this->foreign_tables
     *
     * Ces deux attributs permettent de gérer la notion de FOREIGN KEY de la
     * table en cours vers les autres tables de la base.
     *
     * @return void
     */
    function _init_foreign_tables() {

        // Initialisation des attributs pour que chaque appel de cette méthode
        // se fasse de manière neutre (sans trace des précédents appels)
        $this->clesecondaire = array();
        $this->foreign_tables = array();

        //
        $mode = $this->get_general_option("key_constraints_mode");

        //
        if ($mode == "constraints") {

            // MODE 1 - Recherche de FOREIGN KEY en interrogeant les contraintes
            // de la base de données par 'information_schema'

            //
            $method = "_init_foreign_tables_information_schema_for_".OM_DB_PHPTYPE;
            if (method_exists($this, $method)) {
                $this->$method();
            }

        } elseif ($mode == "postulate") {

            // MODE 2 - Recherche de FOREIGN KEY par le postulat : "le nom d'un
            // champ 'clé étrangère' a pour nom le nom de la table vers laquelle
            // elle fait référence, et fait référence au champ clé primaire de
            // cette table."

            // Boucle sur chaque champ de la table
            foreach ($this->info as $elem) {
                // Boucle sur chaque table de la base
                foreach ($this->tablebase as $elem1) {
                    // Si le nom de la colonne est identique au nom de la table
                    // et qu'il n'est pas déjà présent dans la liste alors on
                    // l'ajoute
                    if ($elem['name'] == $elem1
                        && !in_array($elem['name'], $this->clesecondaire)) {
                        //
                        array_push($this->clesecondaire, $elem1);
                        //
                        $this->foreign_tables[$elem1] = array(
                            'column_name' => $elem1,
                            'foreign_table_name' => $elem1,
                            'foreign_column_name' =>
                                $this->get_primary_key($elem1)
                        );
                    }
                }
            }

        }

        //
        sort($this->clesecondaire);
        //
        ksort($this->foreign_tables);

    }

    /**
     * Initialisation des informations concernant les autres tables.
     *
     * Cette méthode permet d'initialiser les deux attributs :
     *  - $this->sousformulaires
     *  - $this->other_tables
     *
     * Ces deux attributs permettent de gérer la notion de FOREIGN KEY des
     * autres tables de la base vers la table en cours.
     *
     * @return void
     */
    function _init_other_tables() {

        // Initialisation des attributs pour que chaque appel de cette méthode
        // se fasse de manière neutre (sans trace des précédents appels)
        $this->sousformulaires = array();
        $this->other_tables = array();

        //
        $mode = $this->get_general_option("key_constraints_mode");

        //
        if ($mode == "constraints") {

            // MODE 1 - Recherche de FOREIGN KEY en interrogeant les contraintes
            // de la base de données par 'information_schema'

            //
            $method = "_init_other_tables_information_schema_for_".OM_DB_PHPTYPE;
            if (method_exists($this, $method)) {
                $this->$method();
            }

        } elseif ($mode == "postulate") {

            // MODE 2 - Recherche de FOREIGN KEY par le postulat : "le nom d'un
            // champ 'clé étrangère' a pour nom le nom de la table vers laquelle
            // elle fait référence, et fait référence au champ clé primaire de
            // cette table."

            // Boucle sur chaque table de la base
            foreach ($this->tablebase as $elem1) {
                // Récupération des infos de la table
                $table_infos = $this->f->db->tableInfo(DB_PREFIXE.$elem1);
                // Boucle sur chaque colonne
                foreach ($table_infos as $column) {
                    // Si le nom de la colonne est identique au nom de la table en
                    // cours et qu'il n'est pas déjà présent dans la liste
                    // alors on l'ajoute
                    if ($column['name'] == $this->table
                        && !in_array($elem1, $this->sousformulaires)) {
                        //
                        array_push($this->sousformulaires, $elem1);
                        //
                        if (!in_array($elem1.'.'.$column['name'], $this->other_tables)) {
                            $this->other_tables[] = $elem1.'.'.$column['name'];
                        }
                    }
                }
            }

        }

        //
        sort($this->sousformulaires);
        //
        sort($this->other_tables);

    }


    /**
     * Initialisation des informations concernant les clés étrangères (MySQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY de la table en cours
     * vers les autres tables de la base en recherchant les contraintes
     * dans la base de données pour MySQL.
     *
     * @return void
     */
    function _init_foreign_tables_information_schema_for_mysql() {

        // tables referencees par des cles etrangeres de la table actuelle
        $sql = 'SELECT table_name, '.
                      'column_name,'.
                      'REFERENCED_TABLE_NAME AS foreign_table_name, '.
                      'REFERENCED_COLUMN_NAME AS foreign_column_name '.
                'FROM information_schema.key_column_usage '.
                'WHERE '.
                  'REFERENCED_TABLE_NAME is not NULL '.
                  'AND table_name = \''.$this->table.'\' '.
                  'AND table_schema = \''.OM_DB_DATABASE.'\'';

        $foreign_keys = $this->f->db->query($sql);

        $message =  'gen(): db->query("'.$sql.'");';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        $this->f->isDatabaseError($foreign_keys);

        //
        while ($key =& $foreign_keys->fetchrow(DB_FETCHMODE_ASSOC)) {
            //
            array_push($this->clesecondaire, $key['column_name']);
            //
            $this->foreign_tables[$key['column_name']] = $key;
        }

    }

    /**
     * Initialisation des informations concernant les clés étrangères (PostGreSQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY de la table en cours
     * vers les autres tables de la base en recherchant les contraintes
     * dans la base de données pour PostGreSQL.
     *
     * @return void
     */
    function _init_foreign_tables_information_schema_for_pgsql() {

        // tables referencees par des cles etrangeres de la table actuelle
        $sql = 'SELECT tc.table_name, '.
                      'kcu.column_name,'.
                      'ccu.table_name AS foreign_table_name, '.
                      'ccu.column_name AS foreign_column_name '.
                 'FROM information_schema.table_constraints AS tc '.
                 'JOIN information_schema.key_column_usage AS kcu '.
                   'USING (constraint_schema, constraint_name) '.
                 'JOIN information_schema.constraint_column_usage AS ccu '.
                   'USING (constraint_schema, constraint_name) '.
                'WHERE constraint_type = \'FOREIGN KEY\' '.
                  'AND tc.table_name = \''.$this->table.'\' '.
                  'AND tc.table_schema = \''.OM_DB_SCHEMA.'\'';

        $foreign_keys = $this->f->db->query($sql);

        $message =  'gen(): db->query("'.$sql.'");';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        $this->f->isDatabaseError($foreign_keys);

        //
        while ($key =& $foreign_keys->fetchrow(DB_FETCHMODE_ASSOC)) {
            //
            if (in_array($key['column_name'], $this->clesecondaire) === false) {
                $this->clesecondaire[] = $key['column_name'];
            }
            //
            $this->foreign_tables[$key['column_name']] = $key;
        }

    }

    /**
     * Initialisation des autres tables (MySQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY des autres tables de
     * la base vers la table en cours en recherchant les contraintes dans la
     * base de données pour MySQL.
     *
     * @return void
     */
    function _init_other_tables_information_schema_for_mysql() {

        // tables referencees par des cles etrangeres de la table actuelle
        $sql = 'SELECT table_name, '.
                      'column_name,'.
                      'REFERENCED_TABLE_NAME AS foreign_table_name, '.
                      'REFERENCED_COLUMN_NAME AS foreign_column_name '.
                'FROM information_schema.key_column_usage '.
                'WHERE '.
                  'REFERENCED_TABLE_NAME is not NULL '.
                  'AND referenced_table_name = \''.$this->table.'\' '.
                  'AND table_schema = \''.OM_DB_DATABASE.'\'';

        /* Exemple de restultat avec $this->table = 'om_collectivite'

           table_name   |   column_name   | foreign_column_name
        ----------------+-----------------+---------------------
         om_parametre   | om_collectivite | om_collectivite
         om_utilisateur | om_collectivite | om_collectivite
         exemple        | ville_1         | om_collectivite
         exemple        | ville_2         | om_collectivite
         ... */

        $other_tables = $this->f->db->query($sql);

        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($other_tables, true)) {
            if (DEBUG >= DEBUG_MODE) {
                echo 'erreur';
            }
        }

        while ($table =& $other_tables->fetchrow(DB_FETCHMODE_ASSOC)) {

            // initialisation de la liste des sous formulaires, sans doublon
            if (!in_array($table['table_name'], $this->sousformulaires)) {
                array_push($this->sousformulaires, $table['table_name']);
            }

            $infos = $table['table_name'].'.'.$table['column_name'];

            // initialisation des couples table.colonne sans doublon
            if (!in_array($infos, $this->other_tables)) {
                $this->other_tables[] = $infos;
            }

        }

    }

    /**
     * Initialisation des autres tables (PostGreSQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY des autres tables de
     * la base vers la table en cours en recherchant les contraintes dans la
     * base de données pour PostGreSQL.
     *
     * @return void
     */
    function _init_other_tables_information_schema_for_pgsql() {

        // tables ayant des references vers la table actuelle
        $sql = 'SELECT tc.table_name, '.
                      'kcu.column_name,'.
                      'ccu.column_name AS foreign_column_name '.
                 'FROM information_schema.table_constraints AS tc '.
                 'JOIN information_schema.key_column_usage AS kcu '.
                   'USING (constraint_schema, constraint_name) '.
                 'JOIN information_schema.constraint_column_usage AS ccu '.
                   'USING (constraint_schema, constraint_name) '.
                'WHERE constraint_type = \'FOREIGN KEY\' '.
                  'AND ccu.table_name = \''.$this->table.'\' '.
                  'AND ccu.table_schema = \''.OM_DB_SCHEMA.'\'';

        /* Exemple de restultat avec $this->table = 'om_collectivite'

           table_name   |   column_name   | foreign_column_name
        ----------------+-----------------+---------------------
         om_parametre   | om_collectivite | om_collectivite
         om_utilisateur | om_collectivite | om_collectivite
         exemple        | ville_1         | om_collectivite
         exemple        | ville_2         | om_collectivite
         ... */

        $other_tables = $this->f->db->query($sql);

        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($other_tables, true)) {
            if (DEBUG >= DEBUG_MODE) {
                echo 'erreur';
            }
        }

        while ($table =& $other_tables->fetchrow(DB_FETCHMODE_ASSOC)) {

            // initialisation de la liste des sous formulaires, sans doublon
            if (!in_array($table['table_name'], $this->sousformulaires)) {
                array_push($this->sousformulaires, $table['table_name']);
            }

            $infos = $table['table_name'].'.'.$table['column_name'];

            // initialisation des couples table.colonne sans doublon
            if (!in_array($infos, $this->other_tables)) {
                $this->other_tables[] = $infos;
            }

        }

    }

    /**
     * Initialisation des contraintes NOT NULL.
     *
     * Cette méthode permet d'initialiser l'attribut :
     *  - $this->_columns_notnull
     *
     * Cet attribut permet de gérer la notion de NOT NULL et de champs requis
     * de la table en cours de traitement.
     *
     * @return void
     */
    function _init_constraint_notnull() {

        // Initialisation des attributs pour que chaque appel de cette méthode
        // se fasse de manière neutre (sans trace des précédents appels)
        $this->_columns_notnull = array();
        //
        if (OM_DB_PHPTYPE == 'mysql') {
            //
            $sql = " select column_name, column_default from information_schema.columns ";
            $sql .= " where table_schema='".OM_DB_DATABASE."' ";
            $sql .= " and table_name='".$this->table."' ";
            $sql .= " and is_nullable='NO' ";
            $sql .= " and column_default is NULL ORDER BY column_name; ";
            $res = $this->f->db->query($sql);
            $this->f->isDatabaseError($res);
            while ($row =& $res->fetchrow(DB_FETCHMODE_ASSOC)) {
                $this->_columns_notnull[] = strtolower($row['column_name']);
            }
        } elseif (OM_DB_PHPTYPE == 'pgsql') {
            //
            $sql =  'SELECT DISTINCT column_name '.
                    'FROM INFORMATION_SCHEMA.COLUMNS '.
                    'WHERE is_nullable = \'NO\' '.
                        'AND column_default IS NULL '.
                        'AND table_name = \''.$this->table.'\' '.
                        'AND table_schema = \''.OM_DB_SCHEMA.'\' '.
                    'ORDER BY column_name';
            $res_notnull = $this->f->db->query($sql);

            $message =  'gen(): db->query("'.$sql.'");';
            logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

            $this->f->isDatabaseError($res_notnull);
            while ($column =& $res_notnull->fetchrow(DB_FETCHMODE_ASSOC)) {
                $this->_columns_notnull[] = $column['column_name'];
            }
        }

    }

    /**
     * Permet de vérifier si une table fait partie du framework ou non.
     *
     * Le générateur adopte un comportement différent si la table générée fait
     * partie du framework. Cette méthode indique si c'est le cas ou non.
     *
     * @param string $table Le nom de la table.
     *
     * @return boolean
     */
    function is_omframework_table($table = null) {
        //
        if (is_null($table)) {
            //
            return false;
        }
        //
        if (substr($table, 0, 3) != "om_") {
            //
            return false;
        }
        //
        return true;
    }

    /**
     * Rempli les tableaux unique_key et unique_multiple_key.
     *
     * @param string $table Nom de la table à examiner.
     *
     * @return void
     */
    function set_unique_key($table) {
        // Initialisation des attributs pour que chaque appel de cette méthode
        // se fasse de manière neutre (sans trace des précédents appels)
        $this->unique_multiple_key = array();
        $this->unique_key = array();
        //
        if (OM_DB_PHPTYPE == "pgsql") {

            // Sur PostGreSQL des problèmes ont été rencontré avec la cohérence
            // des informations retournées par tableinfo

            //

            $sql = 'SELECT tc.constraint_name, '.
                      'kcu.column_name '.
                 'FROM information_schema.table_constraints AS tc '.
                 'JOIN information_schema.key_column_usage AS kcu '.
                   'USING (constraint_schema, constraint_name) '.
                'WHERE constraint_type = \'UNIQUE\' '.
                  'AND tc.table_name = \''.$this->table.'\' '.
                  'AND tc.table_schema = \''.OM_DB_SCHEMA.'\' '.
                'ORDER BY kcu.column_name';

        } else {

            $sql = "SELECT CONSTRAINT_NAME, COLUMN_NAME FROM information_schema.table_constraints ".
                    "JOIN information_schema.key_column_usage k ".
                    "USING(constraint_name,table_schema,table_name) ".
                    "WHERE TABLE_SCHEMA='".OM_DB_DATABASE."' AND CONSTRAINT_TYPE='UNIQUE'  AND TABLE_NAME='".$this->table."' ".
                    "ORDER BY COLUMN_NAME";
        }
        $res_unique_key = $this->f->db->query($sql);

        $message =  'gen(): db->query("'.$sql.'");';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        $this->f->isDatabaseError($res_unique_key);
        $unique_key=array();
        while ($key =& $res_unique_key->fetchrow(DB_FETCHMODE_ASSOC)) {
            $unique_key[$key['constraint_name']][] = $key['column_name'];
        }
        foreach($unique_key as $unique_constraint) {
            if(count($unique_constraint)>1) {
                $this->unique_multiple_key[] = $unique_constraint;
            } else {
                $this->unique_key[] = $unique_constraint[0];
            }
        }
    }

    /**
     * Vérifie si la table en cours est générable.
     *
     * @todo public
     * @return boolean
     */
    function is_generable() {

        //
        if (!$this->has_primary_key($this->table, true)) {
            return false;
        }

        //
        if (!$this->foreign_tables_have_primary_key(true)) {
            return false;
        }

        //
        return true;

    }

    /**
     * Indique si la table à une clé primaire.
     *
     * @param string  $table         Nom de la table à examiner.
     * @param boolean $display_error Affichage d'erreur à l'écran.
     *
     * @return boolean
     */
    function has_primary_key($table, $display_error = true) {

        //
        $primary_key = $this->get_primary_key($table);

        //
        if ($primary_key != null) {
            //
            return true;
        }

        //
        if ($display_error == true) {
            $message = __("Generation impossible, aucune cle primaire n'est ".
                         "presente ou plusieurs cles primaires sont presentes ".
                         "dans la table");
            $this->f->displayMessage('error', $message.' '.$table.'.');
        }
        //
        return false;

    }

    /**
     * Indique si les tables des clés étarngères ont une clé primaire.
     *
     * @param boolean $display_error Affichage d'erreur à l'écran.
     *
     * @return boolean
     */
    function foreign_tables_have_primary_key($display_error = true) {

        //
        foreach ($this->sousformulaires as $foreign_table_name) {
            //
            if (!$this->has_primary_key($foreign_table_name, $display_error)) {
                //
                return false;
            }
        }
        //
        return true;

    }

    /**
     * Renvoi le libellé.
     *
     * Cette méthode permet de récupérer le libellé de la clé primaire de la
     * table passée en paramètre.
     *
     * @param string $table Nom de la table.
     *
     * @return string
     */
    function get_primary_key($table) {

        //
        $primary_key = "";
        $is_error = false;

        //
        $mode = $this->get_general_option("key_constraints_mode");

        //
        if ($mode == "constraints") {

            // MODE 1 - Recherche de PRIMARY KEY en interrogeant les contraintes
            // de la base de données par 'tableinfo'

            if (OM_DB_PHPTYPE == "pgsql") {

                // Sur PostGreSQL des problèmes ont été rencontré avec la cohérence
                // des informations retournées par tableinfo

                //
                $sql = 'SELECT tc.table_name, '.
                              'kcu.column_name '.
                         'FROM information_schema.table_constraints AS tc '.
                         'JOIN information_schema.key_column_usage AS kcu '.
                           'USING (constraint_schema, constraint_name) '.
                         'JOIN information_schema.constraint_column_usage AS ccu '.
                           'USING (constraint_schema, constraint_name) '.
                        'WHERE constraint_type = \'PRIMARY KEY\' '.
                          'AND tc.table_name = \''.$table.'\' '.
                          'AND tc.table_schema = \''.OM_DB_SCHEMA.'\'';

                $res_primary_key = $this->f->db->query($sql);

                $message =  'gen(): db->query("'.$sql.'");';
                logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

                $this->f->isDatabaseError($res_primary_key);

                if ($res_primary_key->numrows() > 1) {
                    $primary_key = "";
                    $is_error = true;
                } else {
                    while ($key =& $res_primary_key->fetchrow(DB_FETCHMODE_ASSOC)) {
                        $primary_key = $key['column_name'];
                    }
                }

            } else {

                //
                $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
                //
                foreach ($infos as $column) {
                    // Si 'tableinfo' nous renvoi le flag 'primary_key' sur une
                    // colonne
                    if (strpos($column['flags'], 'primary_key')) {
                        // Si nous n'avons pas déjà trouvé une clé primaire et qu'il
                        // n'y a pas eu une erreur
                        if ($primary_key == "" && $is_error == false) {
                            // On récupère le libellé de la clé primaire
                            $primary_key = $column['name'];
                        } else {
                            // Si il y a plusieurs clés primaires sur la même table
                            // On re-initialise la clé primaire et on positionne
                            // le marqueur d'erreur à 'true'
                            $primary_key = "";
                            $is_error = true;
                        }
                    }
                }
            }

        } elseif ($mode == "postulate") {

            // MODE 2 - Recherche de PRIMARY KEY par le postulat : "le nom d'un
            // champ 'clé primaire' a pour nom le nom de la table."

            //
            $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
            //
            foreach ($infos as $column) {
                //
                if ($column['name'] == $table) {
                    //
                    $primary_key = $table;
                    break;
                }
            }

        }

        // Si on ne trouve aucune clé primaire
        if ($primary_key == "" && $is_error == false) {
            // On positionne le marqueur d'erreur à 'true'
            $is_error = true;
        }

        //
        if ($is_error == true) {
            return null;
        }
        //
        return $primary_key;
    }

    /**
     * Renvoi la colonne représentant le libellé d'un enregistrement de la table.
     *
     * @param string $table Nom de la table.
     *
     * @return string
     */
    function get_libelle_of($table) {

        /* Recherche du libelle.

          Si il existe une colonne libelle, elle est utilisee.
          Sinon, on utilise la seconde colonne de la table.
          Sinon, on utilise la cle primaire de la table.
        */

        $libelle = '';
        $libelle_exists = false;

        $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
        $this->f->isDatabaseError($infos);

        foreach ($infos as $column) {
            if ($column['name'] == 'libelle') {
                $libelle_exists = true;
                break;
            }
        }

        // si il existe une colonne libelle, elle est utilisee
        if ($libelle_exists == true) {
            $libelle = 'libelle';
        } else {

            // sinon, on utilise la seconde colonne de la table
            if (key_exists(1, $infos)) {
                $libelle = $infos[1]['name'];
            } else {
                // sinon, on utilise la cle primaire de la table.
                $libelle = $this->get_primary_key($table);
            }
        }

        $message = 'get_libelle_of(\''.DB_PREFIXE.$table.'\');';
        $message .= ' ';
        $message .= 'return \''.$libelle.'\';';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        return $libelle;
    }

    // ------------------------------------------------------------------------
    // }}} END - RECUPERATION DES INFORMATIONS SUR LE MODELE
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - GESTION TECHNIQUE GENERATION
    //      * Écriture et suppression des scripts PHP
    //      * Vérification des permissions d'écriture sur le disque
    //      * Comparaison des fichiers avant génération
    // ------------------------------------------------------------------------

    /**
     * Écrit le contenu dans le fichier sur le disque.
     *
     * @param string $path_to_file Le chemin du fichier à écrire.
     * @param string $content      Contenu du fichier.
     *
     * @todo public
     * @return boolean
     */
    function ecrirefichier($path_to_file, $content) {
        // Récuperation du chemin vers le dossier parent
        // et création du répertoire si non existant
        $path_to_folder = $this->getPathFromFile($path_to_file);
        if (!file_exists($path_to_folder)) {
            $ret = mkdir($path_to_folder, 0777, true);
            if ($ret !== true) {
                //
                $messages[] = array(
                    "class" => "gen-error",
                    "message" => __("Erreur de droits d'ecriture sur")." ".$path_to_folder,
                );
                // Il y a eu un problème
                return false;
            }
        }
        //
        $messages = array();
        //
        if ($this->is_editable($path_to_file)) {
            // le fichier est genere seulement s'il est different de l'existant
            // la ligne date et heure de generation n'est pas prise en compte
            if (!$this->stream_slightly_equals_file($content, $path_to_file)) {
                // Traitement d'écriture
                $inf = fopen($path_to_file, "w");
                fwrite($inf, $content);
                fclose($inf);
                //
                $messages[] = array(
                    "class" => "bold gen-ok",
                    "message" => __("Generation de")." ".$path_to_file,
                );
            } else {
                // sinon il n'est pas re-ecrit sur le disque
                //
                $messages[] = array(
                    "class" => "gen-nochange",
                    "message" => __("Aucun changement de")." ".$path_to_file,
                );
            }
            // Tout s'est bien passé
            $output = true;
        } else {
            //
            $messages[] = array(
                "class" => "gen-error",
                "message" => __("Erreur de droits d'ecriture sur")." ".$path_to_file,
            );
            // Il y a eu un problème
            $output = false;
        }
        //
        foreach ($messages as $message) {
            $this->msg .= sprintf(
                '<br/><span class="%s"> %s </span>',
                $message["class"],
                $message["message"]
            );
        }
        //
        return $output;
    }

    /**
     * Supprime le fichier du disque.
     *
     * @param string $path_to_file Le chemin du fichier à supprimer.
     *
     * @todo public
     * @return void
     */
    function supprimerfichier($path_to_file) {
        //
        $messages = array();
        //
        if (is_writable($path_to_file)) {
            // Traitement de suppression
            unlink($path_to_file);
            //
            $messages[] = array(
                "class" => "bold gen-ok",
                "message" => __('Supression de')." ".$path_to_file,
            );
        } else {
            if(!file_exists($path_to_file)) {
                //
                $messages[] = array(
                    "class" => "bold",
                    "message" => __('Fichier inexistant ou illisible')." ".$path_to_file,
                );
            } else {
                //
                $messages[] = array(
                    "class" => "bold gen-error",
                    "message" => __('Impossible de supprimer')." ".$path_to_file,
                );
            }
        }
        //
        foreach ($messages as $message) {
            $this->msg .= sprintf(
                '<br/><span class="%s"> %s </span>',
                $message["class"],
                $message["message"]
            );
        }
    }

    /**
     * Vérifie les permissions sur le fichier à générer.
     *
     * @param string $path_to_file Le chemin du fichier à examiner.
     *
     * @return boolean
     */
    function is_editable($path_to_file) {
        // Récuperation du chemin vers le dossier parent
        $path_to_folder = $this->getPathFromFile($path_to_file);
        // Vérification des droits d'écriture sur le fichier :
        // - soit le fichier existe et on n'a pas la permission de l'écraser
        // - soit le fichier n'existe pas et on n'a pas la permission d'écrire
        //   dans le répertoire parent
        if ((!is_writable($path_to_file) && file_exists($path_to_file))
            || (!file_exists($path_to_file) && !is_writable($path_to_folder))) {
            // Donc le fichier n'est pas éditable
            return false;
        } else {
            // Sinon le fichier est éditable
            return true;
        }
    }

    /**
     * Compare un flux avec un fichier.
     *
     * L'entete du fichier contenant la date et l'heure de generation n'est pas
     * prise en compte (d'ou le "legerement").
     *
     * Retourne true si:
     *      - le flux et le fichier sont legerement identiques
     *
     * Retourne false si:
     *      - le fichier ne s'ouvre pas
     *      - le flux et le fichier sont differents
     *
     * @param string $stream       Contenu du fichier à générer.
     * @param string $path_to_file Chemin vers le fichier existant.
     *
     * @return boolean
     */
    function stream_slightly_equals_file($stream, $path_to_file) {
        // si le fichier n'existe pas
        if (!file_exists($path_to_file)) {
            return false;
        }
        // ouverture du fichier existant en lecteur
        $f = fopen($path_to_file, 'r');
        // si le fichier ne s'ouvre pas
        if (!$f) {
            return false;
        }
        // Initialisation du numéro de ligne
        $i = 0;
        // Compteur de caractères pour fichiers Robot Framework
        $pointer_rf = strlen($this->def_robot_script_header());
        // Compteur de caractères pour fichiers non Robot Framework
        $pointer = strlen($this->def_php_script_header());

        // boucle sur les lignes du fichier
        while (($line = fgets($f)) !== false) {
            // Incrémentation du numéro de ligne
            $i++;
            // Récupération de l'extension du fichier
            $file_info = pathinfo($path_to_file);
            $file_extension = $file_info['extension'];
            //
            if ($file_extension === "robot") {
                // on saute les lignes d'entete
                if ($i <= substr_count($this->def_robot_script_header(), "\n")) {
                    continue;
                }
                // Si deux lignes différentes en nombre de caractères
                // (l'entête possède toujours une longueur égale même si la date
                // de génération est différente, donc elle ne retourne pas faux)
                if ($line != substr($stream, $pointer_rf, strlen($line))) {
                    return false;
                }
                // Sinon mise à jour du compteur de caractères
                $pointer_rf += strlen($line);
            } else {
                // on saute les lignes d'entete (affichant l'heure)
                if ($i <= substr_count($this->def_php_script_header(), "\n")) {
                    continue;
                }
                // si deux lignes sont differentes, $stream et $path_to_file sont differents
                if ($line != substr($stream, $pointer, strlen($line))) {
                    return false;
                }
                // Sinon mise à jour du compteur de caractères
                $pointer += strlen($line);
            }
        }
        // les fichiers sont légèrement identiques
        return true;
    }

    // ------------------------------------------------------------------------
    // }}} END - GESTION TECHNIQUE GENERATION
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - GESTION DE L'INTERFACE DE GENERATION
    // ------------------------------------------------------------------------

    /**
     * Renvoi le tableau de paramètres listants les fichiers générables.
     *
     * @todo public
     * @return array
     */
    function get_gen_parameters() {
        //
        $parameters = array();
        //
        $parameters["table_obj_class_gen"] = array(
            "rubrik" => "formulaire",
            "path" => "../gen/obj/".$this->table.".class.php",
            "method" => "table_obj_class_gen",
            "checked_delete" => true,
            "checked_generate" => true,
        );
        //
        if ($this->is_omframework_table($this->table) === true
            && $this->f->is_framework_development_mode() === true) {
            $parameters["table_obj_class_gen"]["checked_generate"] = false;
        }
        //
        if ($this->is_omframework_table($this->table) === true
            && $this->f->is_framework_development_mode() === true) {
            //
            $parameters["table_obj_class_core_gen"] = array(
                "rubrik" => "formulaire",
                "path" => PATH_OPENMAIRIE."gen/obj/".$this->table.".class.php",
                "method" => "table_obj_class_gen",
                "checked_delete" => true,
                "checked_generate" => true,
            );
            //
            $parameters["table_obj_class_core"] = array(
                "rubrik" => "formulaire",
                "path" => PATH_OPENMAIRIE."obj/".$this->table.".class.php",
                "method" => "table_obj_class_core",
                "checked_delete" => true,
                "checked_generate" => "not_exists",
            );
        }
        //
        $parameters["table_obj_class"] = array(
            "rubrik" => "formulaire",
            "path" => "../obj/".$this->table.".class.php",
            "method" => "table_obj_class",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        //
        $parameters["table_sql_inc_gen"] = array(
            "rubrik" => "formulaire",
            "path" => "../gen/sql/".OM_DB_PHPTYPE."/".$this->table.".inc.php",
            "method" => "table_sql_inc_gen",
            "checked_delete" => true,
            "checked_generate" => true,
        );
        //
        if ($this->is_omframework_table($this->table) === true
            && $this->f->is_framework_development_mode() === true) {
            $parameters["table_sql_inc_gen"]["checked_generate"] = false;
        }
        //
        if ($this->is_omframework_table($this->table) === true
            && $this->f->is_framework_development_mode() === true) {
            //
            $parameters["table_sql_inc_core"] = array(
                "rubrik" => "formulaire",
                "path" => PATH_OPENMAIRIE."sql/".OM_DB_PHPTYPE."/".$this->table.".inc.php",
                "method" => "table_sql_inc_core",
                "checked_delete" => true,
                "checked_generate" => "not_exists",
            );
            //
            $parameters["table_sql_inc_core_gen"] = array(
                "rubrik" => "formulaire",
                "path" => PATH_OPENMAIRIE."gen/sql/".OM_DB_PHPTYPE."/".$this->table.".inc.php",
                "method" => "table_sql_inc_gen",
                "checked_delete" => true,
                "checked_generate" => true,
            );
        }
        //
        $parameters["table_sql_inc"] = array(
            "rubrik" => "formulaire",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".inc.php",
            "method" => "table_sql_inc",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        //
        if ($this->is_omframework_table($this->table) === true
            && $this->f->is_framework_development_mode() === true) {
            $parameters["table_sql_inc"]["checked_generate"] = false;
        }
        //
        $parameters["editioninc"] = array(
            "rubrik" => "edition",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".pdf.inc.php",
            "method" => "table_sql_pdfinc",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        //
        $parameters["reqmoinc"] = array(
            "rubrik" => "reqmo",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".reqmo.inc.php",
            "method" => "table_sql_reqmoinc",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        // On ajoute des fichiers générables pour chacune des clés étrangères
        // de l'objet sur lequel on se trouve
        if (!empty($this->clesecondaire)) {
            // On boucle sur chacune des clés étrangères
            foreach ($this->clesecondaire as $elem) {
                //
                $parameters["reqmo_".$elem] = array(
                    "rubrik" => "reqmo",
                    "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table."_".$elem.".reqmo.inc.php",
                    "method" => "table_sql_reqmoinc",
                    "method_param" => $elem,
                    "checked_delete" => true,
                    "checked_generate" => false,
                );
            }
        }
        //
        $parameters["importinc"] = array(
            "rubrik" => "divers",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".import.inc.php",
            "method" => "table_sql_importinc",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        // Si le répertoire gen dans l'app des tests existe
        // et qu'il ne s'agit pas d'une table du framework
        if (is_dir("../tests/resources/app/gen")
            && !$this->is_omframework_table($this->table)) {
            $parameters["crud"] = array(
                "rubrik" => "tests",
                "path" => "../tests/resources/app/gen/".$this->table.".robot",
                "method" => "table_tests_crud",
                "checked_delete" => true,
                "checked_generate" => true,
            );
        }
        // Si le répertoire gen dans le core des tests existe
        // et qu'il s'agit d'une table du framework
        // et que nous sommes sur l'instance du framework
        if (is_dir("../tests/resources/core/gen")
            && $this->is_omframework_table($this->table)
            && $this->f->is_framework_development_mode() === true) {
            $parameters["crud"] = array(
                "rubrik" => "tests",
                "path" => "../tests/resources/core/gen/".$this->table.".robot",
                "method" => "table_tests_crud",
                "checked_delete" => true,
                "checked_generate" => true,
            );
        }
        // Rétro-compatibilite : test du fichier .inc si pas de .inc.php
        foreach ($parameters as $key => $elem) {
            // Si le fichier .inc.php n'existe pas
            if (!file_exists($elem["path"])) {
                // Si le fichier .inc existe
                if (file_exists(substr($elem["path"], 0,
                                strlen($elem["path"])-4))) {
                    // Alors on modifie le path
                    $parameters[$key]["path"] = substr(
                        $elem["path"],
                        0,
                        strlen($elem["path"]) - 4
                    );
               }
            }
        }
        //
        return $parameters;
    }

    /**
     * Affiche une ligne de tableau.
     *
     * @param string $col1 Contenu de la colonne 1.
     * @param string $col2 Contenu de la colonne 2.
     * @param string $col3 Contenu de la colonne 3.
     *
     * @todo public
     * @return void
     */
    function affichecol($col1, $col2, $col3) {
        echo "<tr class=\"tab-data even\">\n";
        echo "\t<td  class=\"col-1\">".$col1."</td>\n";
        echo "\t<td  class=\"col-2\">".$col2."</td>\n";
        echo "\t<td  class=\"col-3\">";
        $param['lien']=$col3;
        $this->f->layout->display_lien($param);
        echo"</td>\n";
        echo "</tr>\n";
    }

    /**
     * Affiche une ligne de tableau.
     *
     * @param string $col Contenu de la colonne.
     *
     * @todo public
     * @return void
     */
    function affichetitre($col) {
        echo "<tr class=\"name\">\n";
        echo "\t<td colspan=\"3\">";
        echo $col;
        echo "</td>\n";
        echo "</tr>\n";
    }

    /**
     * Affiche une ligne de tableau.
     *
     * @param string $col1 Contenu de la colonne 1.
     * @param string $col2 Contenu de la colonne 2.
     *
     * @return void
     */
    function afficheinfo($col1, $col2) {
        echo "<tr class=\"tab-data odd\">\n";
        echo "\t<td  class=\"col-1\">".$col1."</td>\n";
        echo "\t<td  class=\"col-2\">".$col2."</td>\n";
        echo "</tr>\n";
    }

    /**
     * Renvoi le chemin vers le répertoire parent.
     *
     * @param string $path_to_file Le chemin du fichier à examiner.
     *
     * @todo public
     * @return string
     */
    function getPathFromFile($path_to_file) {
        //
        $path_to_file_as_array = explode("/", $path_to_file);
        $file_name = array_pop($path_to_file_as_array);
        $path_to_folder_as_array = $path_to_file_as_array;
        $path_to_folder = implode("/", $path_to_folder_as_array);
        //
        return $path_to_folder;
    }

    /**
     * Retourne en vert si le fichier existe, sinon une erreur en rouge.
     *
     * @param string $path_to_file Le chemin du fichier à examiner.
     *
     * @todo public
     * @return string
     */
    function returnFSRightOnFile($path_to_file) {
        //
        $path_to_folder = $this->getPathFromFile($path_to_file);
        //
        $messages = array();
        if (file_exists($path_to_file)) {
            $messages[] = array(
                "class" => "text-green",
                "message" => __("Le fichier existe"),
            );
            if (!is_writable($path_to_file)) {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => __("Pas les droits d'ecriture sur le fichier"),
                );
            }
        } else {
            if(!file_exists($path_to_folder)) {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => __("Le dossier n'existe pas ou n'est pas accessible"),
                );
            } elseif(!is_writable($path_to_folder)) {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => __("Le fichier n'existe pas ou n'est pas accessible"),
                );
                $messages[] = array(
                    "class" => "text-red",
                    "message" => __("Pas les droits d'ecriture sur le dossier"),
                );
            } else {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => __("Le fichier n'existe pas ou n'est pas accessible"),
                );
            }
        }
        //
        $output = "";
        foreach ($messages as $message) {
            $output .= sprintf(
                ' <span class="%s">[ %s ]</span> ',
                $message["class"],
                $message["message"]
            );
        }
        return $output;
    }

    /**
     * Affiche les informations sur la table en cours de traitement.
     *
     * Cet affichage permet sur l'écran de génération d'indiquer à l'utilisateur
     * les informations dont le générateur dispose sur la table en cours de
     * traitement.
     *
     * @todo public
     * @return void
     */
    function display_analyse() {
        //
        $this->f->layout->display_start_fieldset(array(
            "fieldset_class" => "startClosed",
            "legend_content" => __("analyse du modele de donnees"),
        ));

        // Ouverture de la balise tableA VOIR POUR MOBILE
        $param['idcolumntoggle']="genanalyse";
        $this->f->layout->display_table_start_class_default($param);
        $array_entete = array("element","infos");
        echo "<thead><tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">";
        $param = array(
                    "key" => 0,
                    "info" =>  $array_entete
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo "&nbsp;&nbsp;Elements</th>";
        $param = array(
                    "key" => 1,
                    "info" => $array_entete
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo "&nbsp;&nbsp;Infos</th>";
        echo "</tr></thead>";
        // tables de la base
        $contenu = "";
        if (!empty($this->tablebase)) {
            foreach ($this->tablebase as $elem) {
                $contenu .= " [ ".$elem. " ] ";
            }
            $lib = __("Tables de la base de donnees");
            $this->afficheinfo($lib, $contenu);
        }
        // table
        $contenu = "";
        $lib = __("Table :")." <span class=\"bold\">".$this->table."</span>";
        $contenu .= "[ ".__('cle')." ".$this->typecle." - ";
        if ($this->typecle == 'N') { // XXX - Ce test est-il correct ?
            $contenu .= __("cle automatique")." ]";
        } else {
            $contenu .= __("cle manuelle")." ]";
        }
        $contenu .= " <span class=\"bold\">[".$this->primary_key."]</span> ";
        $contenu .=" [ ".__('longueur')." ".__("enregistrement")." : ".$this->longueur." ]";
        $this->afficheinfo($lib, $contenu);
        // champs
        $contenu = "";
        $lib = __("Champs");
        foreach ($this->info as $elem) {
            $contenu .= "[ ".$elem["name"]." ".$elem["len"]." ".$elem["type"]." ] ";
        }
        $this->afficheinfo($lib, $contenu);
        // sous formulaire
        $contenu = "";
        if (!empty($this->sousformulaires)) {
            foreach ($this->sousformulaires as $elem) {
                $contenu .= " [ ".$elem. " ] ";
            }
        }
        $lib= __("Sous formulaire");
        $this->afficheinfo($lib, $contenu);
        // cle secondaire
        $contenu = "";
        if (!empty($this->clesecondaire)) {
            foreach ($this->clesecondaire as $elem) {
                $contenu .= " [ ".$elem. " ] ";
            }
        }
        $lib = __("Cle secondaire");
        $this->afficheinfo($lib, $contenu);
        // Fermeture de la balise table
        echo "</table>";
        //
        $this->f->layout->display_stop_fieldset();
    }


    /**
     * VIEW - view_gen_gen_generate.
     *
     * Cette vue permet d'interfacer la génération d'une table.
     *
     * @return void
     */
    function view_gen_gen_generate() {
        //
        $params = array(
            //
            "table" => array(
                "default_value" => "",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        $title = "-> ".__("table")." '".$table."'";
        $this->f->displaySubTitle($title);

        /**
         * Page - Start
         */
        // Ouverture du container de la page
        echo "\n<div id=\"generator\">\n";

        /**
         *
         */
        //
        $this->init_generation_for_table($table);
        $params = $this->get_gen_parameters();

        /**
         * Si la table n'est pas générable alors on arrête le script
         */
        if ($this->is_generable() != true) {
            // Fermeture du container de la page
            echo "</div>\n";
            // Arrêt du script
            die();
        }

        /**
         * TRAITEMENT DE GENERATION
         */
        // Traitement si validation du formulaire
        if (isset($_POST["valid_gen_generer"])) {
            //
            foreach ($params as $key => $param) {
                //
                if (isset($_POST[$key])) {
                    //
                    if (!isset($param["method_param"])) {
                        $this->ecrirefichier(
                            $param["path"],
                            $this->{$param["method"]}()
                        );
                    } else {
                        //
                        $this->ecrirefichier(
                            $param["path"],
                            $this->{$param["method"]}($param["method_param"])
                        );
                    }
                }
            }
            // Affichage du message de validation du traitement
            $this->f->displayMessage("valid", $this->msg);
        }

        /**
         * Affichage du bloc de l'analyse de la table
         */
        $this->display_analyse();

        /**
         * Affichage du bloc de sélection des fichiers à générer
         */
        //
        $this->f->layout->display_start_fieldset(array(
            "fieldset_class" => "collapsible",
            "legend_content" => __("selection des fichiers"),
        ));
        // Ouverture de la balise formulaire
        $this->f->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_MODULE_GEN."&view=gen_generate&table=".$table,
            "name" => "f1",
        ));
        // Ouverture de la balise table
        $param = array(
            'idcolumntoggle' => "generer"
        );
        $this->f->layout->display_table_start_class_default($param);
        $array_entete_gen = array("selection","Nom Fichier","generer");
        echo "<thead>\n";
        echo "<tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">\n";
        $param = array(
                    "key" => 0,
                    "info" =>  $array_entete_gen
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo "&nbsp;";
        echo "</th>";
        $param = array(
                    "key" => 1,
                    "info" => $array_entete_gen
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo __("fichier");
        echo "</th>";
        $param = array(
                    "key" => 0,
                    "info" => $array_entete_gen
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo __("informations");
        echo "</th>\n";
        echo "</tr>\n";
        echo "</thead>\n";
        //
        $rubrik = null;
        // On boucle sur chaque fichier à générer
        foreach ($params as $key => $param) {
            //
            if (isset($param["rubrik"])
                && $param["rubrik"] != $rubrik) {
                //
                $rubrik = $param["rubrik"];
                $this->affichetitre("<span class=\"bold\">".__($rubrik)."</span>");
            }
            // XXX
            $path_to_file = $param["path"];
            // On récupère le répertoire du fichier à générer
            $path_to_folder = $this->getPathFromFile($path_to_file);
            // XXX
            $disabled = false;
            $check = false;
            // Si l'attribut "checked" est defini a true ou que l'attribut
            // check est défini à "notexist" et que le fichier n'existe pas
            // => Alors la case sera cochée par défaut
            if ($param["checked_generate"] === true
                or ($param["checked_generate"] === "not_exists"
                    and !file_exists($path_to_file))
            ) {
                $check = true;
            }
            // Si le fichier existe et qu'on a pas les droits d'écriture sur le
            // fichier ou que le fichier n'existe pas et qu'on a pas le droit
            // d'écrire dans le répertoire du fichier à générer
            // => Alors on affiche la case comme décochée et on la désactive
            // (impossible pour l'utilisateur de la cocher)
            if ((!is_writable($path_to_file)
                 and file_exists($path_to_file))
                or (!file_exists($path_to_file)
                    and !is_writable($path_to_folder))
            ) {
                $check = false;
                $disabled = true;
            }
            // On construit la case à cocher
            $box = "<input type=\"checkbox\" name=\"".$key."\"";
            $box .= ($check ? " checked=\"checked\"" : "");
            $box .= ($disabled ? " disabled=\"disabled\"" : "");
            $box .= " class=\"champFormulaire\" />";
            //
            $link_file = $path_to_file;
            // On récupère les infos sur le fichier
            $msg = $this->returnFSRightOnFile($path_to_file);
            // On affiche les éléments ci-dessus
            $this->affichecol($box, $link_file, $msg);
        }
        // Fermeture de la balise table
        echo "</table>\n";
        // Affichage des actions de controles du formulaire
        $this->f->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        // Bouton de validation du formulaire
        $this->f->layout->display__form_input_submit(array(
            "value" => sprintf(__("generer les fichiers de la table : %s"), $table),
            "name" => "valid.gen.generer",
        ));
        // Fermeture du conteneur des actions de controles du formulaire
        $this->f->layout->display__form_controls_container__end();
        // Fermeture de la balise formulaire
        $this->f->layout->display__form_container__end();
        // Fermeture du fieldset
        $this->f->layout->display_stop_fieldset();

        /**
         * Page - End
         */
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => OM_ROUTE_MODULE_GEN,
        ));
        // Fermeture du container de la page
        echo "</div>\n";
    }

    /**
     * VIEW - view_gen_gen_delete.
     *
     * Cette vue permet d'interfacer la suppression des fichiers générés d'une table.
     *
     * @return void
     */
    function view_gen_gen_delete() {
        //
        $params = array(
            //
            "table" => array(
                "default_value" => "",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        $title = "-> ".__("table")." '".$table."'";
        $this->f->displaySubTitle($title);

        /**
         * Page - Start
         */
        // Ouverture du container de la page
        echo "\n<div id=\"generator\">\n";

        /**
         *
         */
        //
        $this->init_generation_for_table($table);
        $params = $this->get_gen_parameters();

        /**
         * TRAITEMENT DE SUPPRESSION
         */
        // Traitement si validation du formulaire
        if (isset($_POST["valid_gen_supprimer"])) {
            //
            foreach ($params as $key => $param) {
                //
                if (isset($_POST[$key])) {
                    //
                    $this->supprimerfichier(
                        $param["path"]
                    );
                }
            }
            // Affichage du message de validation du traitement
            $this->f->displayMessage("valid", $this->msg);
        }

        /**
         * Affichage du bloc de l'analyse de la table
         */
        $this->display_analyse();

        /**
         * Affichage du bloc de sélection des fichiers à supprimer
         */
        //
        $this->f->layout->display_start_fieldset(array(
            "fieldset_class" => "collapsible",
            "legend_content" => __("selection des fichiers"),
        ));
        // Ouverture de la balise formulaire
        $this->f->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_MODULE_GEN."&view=gen_delete&table=".$table,
            "name" => "f1",
        ));
        // Ouverture de la balise table
        $param = array(
            'idcolumntoggle' => "generer"
        );
        $this->f->layout->display_table_start_class_default($param);
        $array_entete_gen = array("selection","Nom Fichier","generer");
        echo "<thead>\n";
        echo "<tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">\n";
        $param = array(
                    "key" => 0,
                    "info" =>  $array_entete_gen
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo "&nbsp;";
        echo "</th>";
        $param = array(
                    "key" => 1,
                    "info" => $array_entete_gen
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo __("fichier");
        echo "</th>";
        $param = array(
                    "key" => 0,
                    "info" => $array_entete_gen
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo __("informations");
        echo "</th>\n";
        echo "</tr>\n";
        echo "</thead>\n";
        //
        $rubrik = null;
        // On boucle sur chaque fichier à générer
        foreach ($params as $key => $param) {
            //
            if (isset($param["rubrik"])
                && $param["rubrik"] != $rubrik) {
                //
                $rubrik = $param["rubrik"];
                $this->affichetitre("<span class=\"bold\">".__($rubrik)."</span>");
            }
            // XXX
            $path_to_file = $param["path"];
            // On récupère le répertoire du fichier à générer
            $path_to_folder = $this->getPathFromFile($path_to_file);
            // XXX
            $disabled = false;
            $check = false;
            // Si l'attribut "checked" est defini a true et que le fichier
            // existe
            // => Alors la case sera cochée par défaut
            if ($param["checked_delete"] === true
                and file_exists($path_to_file)
            ) {
                $check = true;
            }
            // Si le fichier existe et qu'on a pas les droits d'écriture sur le
            // fichier ou si le fichier n'existe pas
            // => Alors on affiche la case comme décochée et on la désactive
            // (impossible pour l'utilisateur de la cocher)
            if ((!is_writable($path_to_file)
                 and file_exists($path_to_file))
                or (!file_exists($path_to_file))
            ) {
                $check = false;
                $disabled = true;
            }
            // On construit la case à cocher
            $box = "<input type=\"checkbox\" name=\"".$key."\"";
            $box .= ($check ? " checked=\"checked\"" : "");
            $box .= ($disabled ? " disabled=\"disabled\"" : "");
            $box .= " class=\"champFormulaire\" />";
            //
            $link_file = $path_to_file;
            // On récupère les infos sur le fichier
            $msg = $this->returnFSRightOnFile($path_to_file);
            // On affiche les éléments ci-dessus
            $this->affichecol($box, $link_file, $msg);
        }
        // Fermeture de la balise table
        echo "</table>\n";
        // Affichage des actions de controles du formulaire
        $this->f->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        // Bouton de validation du formulaire
        $this->f->layout->display__form_input_submit(array(
            "value" => sprintf(__("supprimer les fichiers de la table : %s"), $table),
            "name" => "valid.gen.supprimer",
        ));
        // Fermeture du conteneur des actions de controles du formulaire
        $this->f->layout->display__form_controls_container__end();
        // Fermeture de la balise formulaire
        $this->f->layout->display__form_container__end();
        // Fermeture du fieldset
        $this->f->layout->display_stop_fieldset();

        /**
         * Page - End
         */
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => OM_ROUTE_MODULE_GEN,
        ));
        // Fermeture du container de la page
        echo "</div>\n";
    }

    /**
     * VIEW - view_gen_gen_full.
     *
     * Cette vue permet d'interfacer la génération complète de toutes les tables.
     *
     * @return void
     */
    function view_gen_gen_full() {

        /**
         * Page - Start
         */
        // Ouverture du container de la page
        echo "\n<div id=\"generator\">\n";

        /**
         * Récupération de la liste des tables à générer
         */
        // On récupère le paramètre si le fichier de paramétrage existe qui permet
        // de ne pas générer des tables non souhaitées
        $core_tables_to_avoid = array();
        $tables_to_avoid = array();
        if (file_exists(PATH_OPENMAIRIE."gen/dyn/gen.inc.php")) {
            include PATH_OPENMAIRIE."gen/dyn/gen.inc.php";
        }
        if (file_exists("../gen/dyn/gen.inc.php")) {
            include "../gen/dyn/gen.inc.php";
        }
        // On récupère la liste des tables de la base de données à laquelle on
        // enlève les tables à éviter récupérées du paramétrage
        $tables = array_diff(
            $this->get_all_tables_from_database(),
            $core_tables_to_avoid,
            $tables_to_avoid
        );
        //
        foreach ($tables as $table) {
            //
            $title = "-> ".__("table")." '".$table."'";
            $this->f->displaySubTitle($title);
            $this->init_generation_for_table($table);
            $params = $this->get_gen_parameters();
            //
            if ($this->is_generable() == true) {
                // On intialise le marqueur d'erreur à false avant de lancer la
                // boucle de génération
                $rightError = false;
                // On boucle sur chaque fichier à générer
                foreach ($params as $key => $param) {
                    // Si le fichier doit être généré (checked = true)
                    // ou seulement si il n'existe pas (notexist)
                    if ($param["checked_generate"] === true
                        or ($param["checked_generate"] === "not_exists"
                            and !file_exists($param["path"]))
                    ) {
                        // On écrit le fichier sur le disque
                        if (!isset($param["method_param"])) {
                            $result = $this->ecrirefichier(
                                $param["path"],
                                $this->{$param["method"]}()
                            );
                        } else {
                            //
                            $result = $this->ecrirefichier(
                                $param["path"],
                                $this->{$param["method"]}($param["method_param"])
                            );
                        }
                        // Si une erreur s'est produite pendant l'écriture du
                        // fichier sur le disque alors on positionne le marqueur
                        // d'erreur à true
                        if (!$result) {
                            $rightError = true;
                        }
                    }
                }
                // Affichage du message des erreurs de droits d'ecriture
                if ($rightError) {
                    $this->f->displayMessage(
                        "error",
                        __("Erreur de droits d'ecriture lors de la generation des fichiers")
                    );
                }
                // Affichage du message de fin de traitement
                $this->f->displayMessage("valid", $this->msg);
            }
        }
        // Ficher 'resources' des tests Robot Framework
        $this->gen_full_tests($tables);

        /**
         * Page - End
         */
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => OM_ROUTE_MODULE_GEN,
        ));
        // Fermeture du container de la page
        echo "</div>\n";
    }

    // ------------------------------------------------------------------------
    // }}} END - GESTION DE L'INTERFACE UTILISATEUR DE GENERATION
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - VUE PRINCIPALE
    // ------------------------------------------------------------------------

    /**
     * VIEW - view_gen.
     *
     * Cette vue gère la page d'accueil du module qui présente l'accès à toutes
     * les fonctionnalités du module gen. Elle permet également de dispatcher
     * sur les autres vues en fonction de son paramètre 'view'.
     *
     * @return void
     */
    function view_gen() {
        //
        $this->f->isAuthorized("gen");

        //
        $this->f->setTitle(__("administration")." -> ".__("generateur"));
        $this->f->setFlag(null);
        $this->f->display();

        //
        $title = __("base de donnees")." ".OM_DB_PHPTYPE." '";
        $title .= (OM_DB_SCHEMA == "" ?"":OM_DB_SCHEMA.".").OM_DB_DATABASE."'";
        $this->f->layout->display_page_title_subtext($title);

        //
        if (OM_DB_PHPTYPE != "pgsql") {
            //
            $message = __(
                "Le generateur ne prend pas en charge le type de base de donnees
                utilise."
            );
            $this->f->displayMessage("error", $message);
            //
            die();
        }

        //
        $valid_views = array(
            "permissions",
            "editions_old",
            "editions_etat",
            "editions_lettretype",
            "editions_sousetat",
            "gen_delete",
            "gen_generate",
            "gen_full",
        );
        if (in_array($this->f->get_submitted_get_value("view"), $valid_views)) {
            $method_name = sprintf("view_gen_%s", $this->f->get_submitted_get_value("view"));
            if (method_exists($this, $method_name)) {
                $this->$method_name();
            }
            return;
        }


        /**
         * Page - Start
         */
        // Ouverture du container de la page
        echo "\n<div id=\"generator\">\n";

        /**
         * Génération basée sur les tables de la base de données.
         */
        // On récupère le paramètre si le fichier de paramétrage existe qui permet
        // de ne pas générer des tables non souhaitées
        $core_tables_to_avoid = array();
        $tables_to_avoid = array();
        if (file_exists(PATH_OPENMAIRIE."gen/dyn/gen.inc.php")) {
            include PATH_OPENMAIRIE."gen/dyn/gen.inc.php";
        }
        if (file_exists("../gen/dyn/gen.inc.php")) {
            include "../gen/dyn/gen.inc.php";
        }
        // On récupère la liste des tables de la base de données à laquelle on
        // enlève les tables à éviter récupérées du paramétrage
        $tables = array_diff(
            $this->get_all_tables_from_database(),
            $core_tables_to_avoid,
            $tables_to_avoid
        );
        // Composition de la liste de liens vers les éditions disponibles.
        // En partant de la liste d'éditions disponibles, on compose une liste
        // d'éléments composés d'une URL, d'un libellé, et de tous les paramètres
        // permettant l'affichage de l'élément comme un élément de liste.
        $list = array();
        //
        $list[] = array(
            "href" => OM_ROUTE_MODULE_GEN."&view=gen_full",
            "title" => __("generer tout"),
            "class" => "om-prev-icon",
            "description" => __("Cela aura pour effet d'ecraser tous les fichiers existants du repertoire gen/ et creer les fichiers dans core/, sql/ et obj/ s'ils n'existent pas."),
            "id" => "gen-action-gen-all"
        );
        //
        foreach ($tables as $key => $value) {
            //
            $links = array(
                array(
                    "href" => OM_ROUTE_MODULE_GEN."&view=gen_delete&table=".$value,
                    "title" => __("supprimer"),
                    "class" => "om-icon om-icon-right om-icon-25 delete-25",
                    "description" => __("supprimer"),
                    "id" => "gen-action-delete-".$value,
                ),
                array(
                    "href" => OM_ROUTE_MODULE_GEN."&view=gen_generate&table=".$value,
                    "title" => __("generer"),
                    "class" => "om-icon om-icon-right om-icon-25 generate-25",
                    "description" => __("generer"),
                    "id" => "gen-action-generate-".$value,
                ),
            );
            //
            $list[] = array(
                "title" => $value,
                "links" => $links,
            );
        }
        //
        $this->f->layout->display_list(
            array(
                "title" => __("generation basee sur les tables de la base de donnees"),
                "list" => $list,
                "class" => "collapsible"
            )
        );

        /**
         * Assistants permettant la creation d'etats, sous etats, lettres types ou
         * la migration/l'import de ces mêmes éléments depuis des anciennes versions
         * d'openMairie.
         */
        // On définit les différents assistants disponibles
        $assistants = array(
            0 => array(
                "href" => OM_ROUTE_MODULE_GEN."&view=permissions",
                "title" => __("Génération des permissions"),
            ),
            1 => array(
                "href" => OM_ROUTE_MODULE_GEN."&view=editions_old",
                "title" => __("Migration etat, sous etat, lettre type"),
            ),
            2 => array(
                "href" => OM_ROUTE_MODULE_GEN."&view=editions_etat",
                "title" => __("Creation etat"),
            ),
            3 => array(
                "href" => OM_ROUTE_MODULE_GEN."&view=editions_sousetat",
                "title" => __("Creation sous etat"),
            ),
            4 => array(
                "href" => OM_ROUTE_MODULE_GEN."&view=editions_lettretype",
                "title" => __("Creation lettre type"),
            ),
        );
        // Composition de la liste de liens vers les éditions disponibles.
        // En partant de la liste d'éditions disponibles, on compose une liste
        // d'éléments composés d'une URL, d'un libellé, et de tous les paramètres
        // permettant l'affichage de l'élément comme un élément de liste.
        $list = array();
        foreach ($assistants as $key => $value) {
            //
            $list[] = array(
                "href" => $value["href"],
                "title" => $value["title"],
                "class" => "om-prev-icon wizard-16",
            );
        }
        //
        $this->f->layout->display_list(
            array(
                "title" => __("assistants"),
                "list" => $list,
            )
        );

        /**
         * Page - End
         */
        // Fermeture du container de la page
        echo "</div>\n";
    }

    // ------------------------------------------------------------------------
    // }}} END - VUE PRINCIPALE
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - GENERATION DES PERMISSIONS
    // ------------------------------------------------------------------------

    /**
     * VIEW - view_gen_permissions.
     *
     * Vue permettant de gérer l'interface utilisateur de génération
     * automatique des permissions de l'application.
     *
     * @todo public
     * @return void
     */
    function view_gen_permissions() {
        // XXX Améliorer l'interface en ajoutant une description, un bouton,
        // une vérification sur la présence de la table vocabulaire des
        // permissions.
        // Début de la transaction
        $this->f->db->autoCommit(false);
        // Si le traitement est réalisé avec succès
        if ($this->treatment_gen_permissions() === true) {
            // Commit de la transaction
            $this->f->db->commit();
            //
            $this->f->displayMessage("valid", __("Traitement terminé"));
        } else {
            // Annulation de la transaction
            $this->f->db->rollback();
            // XXX Améliorer la gestion des erreurs
            // il faut remonter des messages plus explicites à l'utilisateur
            $this->f->displayMessage("error", __("Erreur"));
        }
    }

    /**
     * TREATMENT - treatment_gen_permissions.
     *
     * Ce traitement permet de :
     * - mettre à jour la table de vocabulaire des permissions avec les
     *   permissions de l'application "calculées" directement à partir du code,
     * - mettre à jour le fichier SQL d'initialisation des permissions
     *   avec les permissions de l'application "calculées" directement à partir
     *   du code,
     * - supprimer tous les éléments obsolètes de la table de matrice des
     *   droits.
     *
     * @return bool
     */
    function treatment_gen_permissions() {
        // Récupération de la liste des permissions.
        $permissions = $this->get_all_permissions();

        // Composition des requêtes d'insertion des permissions (une variable
        // pour le fichier et une variable pour la base de données)
        $template_insert = "
INSERT INTO %som_permission (om_permission, libelle, type) VALUES (nextval('%som_permission_seq'), '%s', 'gen');";
        $insert_file = "";
        $insert_db = "";
        foreach ($permissions as $key => $value) {
            $insert_file .= sprintf($template_insert, "", "", $value);
            $insert_db .= sprintf($template_insert, DB_PREFIXE, DB_PREFIXE, $value);
        }

        // Suppression des permissions existantes dans om_permission ayant le
        // type GEN
        $query = "DELETE FROM ".DB_PREFIXE."om_permission WHERE lower(om_permission.type) = 'gen'";
        $res = $this->f->db->query($query);
        $this->addToLog(__METHOD__."(): db->query(\"".$query."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($res, true)) {
            return false;
        }

        // Insertion de toutes les nouvelles permissions dans la table
        // om_permission
        $res = $this->f->db->query($insert_db);
        $this->addToLog(__METHOD__."(): db->query(\"".$insert_db."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($res, true)) {
            return false;
        }

        // Suppression des lignes dans la table om_droit dont le libellé
        // n'existe pas dans la table permission
        $query = "DELETE FROM ".DB_PREFIXE."om_droit WHERE om_droit.libelle NOT IN (SELECT om_permission.libelle FROM ".DB_PREFIXE."om_permission)";
        $res = $this->f->db->query($query);
        $this->addToLog(__METHOD__."(): db->query(\"".$query."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($res, true)) {
            return false;
        }

        // Écriture des requêtes d'insertion des permissions dans le fichier
        // data/pgsql/init_permissions.sql
        if ($this->f->is_framework_development_mode() === true) {
            $path_to_file = PATH_OPENMAIRIE."data/".OM_DB_PHPTYPE."/init_permissions.sql";
        } else {
            $path_to_file = "../data/".OM_DB_PHPTYPE."/init_permissions.sql";
        }
        if (!$inf = @fopen($path_to_file, "w")) {
            $this->addToLog(__METHOD__."(): Impossible d'ouvrir le fichier (".$path_to_file.")", DEBUG_MODE);
            return false;
        }
        if (!@fwrite($inf, $insert_file)) {
            $this->addToLog(__METHOD__."(): Impossible d'écrire dans le fichier (".$path_to_file.")", DEBUG_MODE);
            return false;
        }
        if (!@fclose($inf)) {
            $this->addToLog(__METHOD__."(): Impossible de fermer le fichier (".$path_to_file.")", DEBUG_MODE);
            return false;
        }

        // Si aucune erreur n'a été retournée, alors le traitement s'est
        // déroulé correctement
        return true;
    }

    /**
     * Retourne la liste des permissions "calculées".
     *
     * Cette méthode "calcule" l'intégralité des permissions présente dans
     * l'application :
     * - toutes les permissions spécifiques déclarées dans
     *   gen/dyn/permissions.inc.php,
     * - toutes les permissions utilisées dans l'attribut class_actions de
     *   chacune des classes présentes dans le répertoire obj/,
     * - toutes les permissions utilisées dans le menu,
     * - toutes les permissions utilisées dans les actions,
     * - toutes les permissions utilisées dans le footer,
     * - toutes les permissions utilisées dans les shortlinks.
     *
     * @return array
     */
    function get_all_permissions() {
        //
        if (file_exists("../gen/dyn/permissions.inc.php")) {
            include "../gen/dyn/permissions.inc.php";
        }
        //
        if (!isset($permissions) || !is_array($permissions)) {
            //
            $permissions = array();
        }
        if (!isset($files_to_avoid) || !is_array($files_to_avoid)) {
            //
            $files_to_avoid = array();
        }
        //
        $files_to_avoid = array_merge($files_to_avoid, array(
            ".",
            "..",
            ".htaccess",
            "index.php",
            "utils.class.php",
            "om_dbform.class.php",
            "om_formulaire.class.php",
            "om_table.class.php",
        ));

        //
        $obj_contents = @scandir("../obj/");
        if ($obj_contents === false) {
            $obj_contents = array();
        }
        $core_obj_contents = @scandir(PATH_OPENMAIRIE."obj/");
        if ($core_obj_contents === false) {
            $core_obj_contents = array();
        }
        $gen_obj_contents = @scandir("../gen/obj/");
        if ($gen_obj_contents === false) {
            $gen_obj_contents = array();
        }
        // GET PERMISSIONS FROM OBJ
        $folder_contents_to_scan = array_merge(
            $obj_contents,
            $core_obj_contents,
            $gen_obj_contents
        );
        foreach ($folder_contents_to_scan as $file) {
            //
            if (in_array($file, $files_to_avoid)) {
                continue;
            }
            //
            $name_class = explode(".", $file);
            $name_class = $name_class[0];
            $inst_class = $this->f->get_inst__om_dbform(array(
                "obj" => $name_class,
                "idx" => 0,
            ));
            if (!method_exists($inst_class, "init_class_actions")) {
                continue;
            }
            $permissions[] = $name_class;
            $permissions[] = $name_class."_tab";
            $inst_class->init_class_actions();
            foreach ($inst_class->class_actions as $action) {
                // Si l'action n'est pas définie
                if (!is_array($action)) {
                    continue;
                }
                //
                $perm = "";
                if (isset($action["permission_suffix"])) {
                    $perm = $action["permission_suffix"];
                }
                $permissions[] = $name_class."_".$perm;
            }
        }

        // GET PERMISSIONS FROM MENU
        //
        foreach ($this->f->get_config__menu() as $m => $rubrik) {
            //
            if (isset($rubrik['right'])) {
                if (!is_array($rubrik['right'])) {
                    $permissions[] = $rubrik['right'];
                } else {
                    foreach ($rubrik['right'] as $permission) {
                        $permissions[] = $permission;
                    }
                }
            }
            // Boucle sur les entrees de menu
            foreach ($rubrik['links'] as $link) {
                // Gestion des droits d'acces : si l'utilisateur n'a pas la
                // permission necessaire alors l'entree n'est pas affichee
                if (isset($link['right'])) {
                    if (!is_array($link['right'])) {
                        $permissions[] = $link['right'];
                    } else {
                        foreach ($link['right'] as $permission) {
                            $permissions[] = $permission;
                        }
                    }
                }
            }
        }

        // GET PERMISSIONS FROM ACTIONS, FOOTER, SHORTLINKS
        //
        $files_to_scan = array("actions", "footer", "shortlinks", );
        //
        foreach ($files_to_scan as $element) {
            //
            $method_name = "get_config__".$element;
            foreach ($this->f->$method_name() as $action) {
                if (isset($action["right"])) {
                    $permissions[] = $action["right"];
                }
            }
        }

        //
        $permissions = array_unique($permissions);
        sort($permissions);

        //
        return $permissions;
    }

    // ------------------------------------------------------------------------
    // }}} END - GENERATION DES PERMISSIONS
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - GENERATION DES EDITIONS
    // ------------------------------------------------------------------------

    /**
     * VIEW - view_gen_editions_etat.
     *
     * @return void
     */
    function view_gen_editions_etat() {
        /**
         *
         */
        //
        $subtitle = "-> ".__("génération d'édition - etat");
        $this->f->displaySubTitle($subtitle);
        //
        $description = __(
            "Cet assistant vous permet de creer des etats directement a".
            " partir de vos tables."
        );
        $this->f->displayDescription($description);

        /**
         *
         */
        // Initialisation des paramètres
        $params = array(
            "obj" => array(
                "default_value" => '',
            ),
            "validation" => array(
                "default_value" => 0,
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        set_time_limit(3600);
        $DEBUG=0;
        if (isset($_POST['choice-import']) and $_POST['choice-import'] != "---") {
            $obj = $this->f->get_submitted_post_value('choice-import');
        }
        if (isset($_POST['choice-field'])) {
            $field = $this->f->get_submitted_post_value('choice-field');
        } else {
            $field = '';
        }

        /**
         * On liste les tables pour que l'utilisateur puisse choisir sur quel table
         * il souhaite créer un état
         */
        // On récupère la liste des tables de la base de données
        $tables = $this->get_all_tables_from_database();
        //
        echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
        $this->f->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_MODULE_GEN."&view=editions_etat",
        ));
        echo "<fieldset>\n";
        echo "\t<legend>".__("Choix table :")."</legend>\n";

        echo "\t<div class=\"field\">";
        echo "<label>".__("fichier")."</label>";
        echo "<select onchange=\"submit()\" name=\"choice-import\" class=\"champFormulaire\">";
        echo "<option>---</option>";
        foreach ($tables as $table) {
            echo "<option value=\"".$table."\"";
            if ($obj == $table) {
                echo " selected=\"selected\" ";
            }
            echo ">".$table."</option>";
        }
        echo "</select>";
        echo "</div>\n";

        echo "</fieldset>\n";

        $this->f->layout->display__form_container__end();

        echo "</div>\n";

        /**
         * choix des champs
         */
        if ($obj != "" and $field=='') {
            //
            echo "\n<br>&nbsp;<div id=\"form-csv-import\" class=\"formulaire\">\n";
            $this->f->layout->display__form_container__begin(array(
                "action" => OM_ROUTE_MODULE_GEN."&view=editions_etat&obj=".$obj."&validation=1",
                "name" => "f1",
            ));
            echo "<fieldset>\n";
            echo "\t<legend>".__("choix des champs")."</legend>";

            echo "Utilisez ctrl key pour choix multiple<br><br>";
            //
            $sql = "select * from ".DB_PREFIXE.$obj;
            $res2 = $this->f->db->query($sql);
            $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->f->isDatabaseError($res2);
            //
            $info=$res2->tableInfo();
            echo "<select multiple name=\"choice-field[]\" class=\"champFormulaire\">";
            foreach($info as $elem){
                echo "<option>".$obj.".".$elem['name']."</option>";
            }
            echo "</select>";
            echo "<br><br>";
            $this->f->layout->display__form_input_submit(array(
                "name" => "submit-csv-import",
                "value" => __("Import")." ".$obj." ".__("dans la base"),
                "class" => "boutonFormulaire",
            ));
            echo "</fieldset>";
            $this->f->layout->display__form_container__end();
            echo "</div>\n";
        }
        /**
         *  transfert dans la base
         */
        if ($obj != "" and $field!='') {

            //
            $this->f->db->autoCommit(false);

            //
            echo "\n<br>&nbsp";
            echo "<fieldset>\n";
            echo "\t<legend> Insertion dans la table etat</legend>";

            /**
             * Composition de la requête SQL
             */
            // sql
            $temp='';
            $temp1='';
            if($field!=array()){
                for ($i = 0; $i < sizeof($field); $i++) {
                    $temp2=explode(".",$field[$i]);
                    $temp3=$temp2[1];
                    $temp.=$field[$i].' as '.$temp3.',';
                    $temp1.="[".$temp3.']'.chr(13).chr(10);
                }
                $temp=substr($temp, 0, strlen($temp)-1);
            }
            //
            $variable='&';

            /**
             * Création de la requête
             */
            //
            $om_requete = $this->f->get_inst__om_dbform(array(
                "obj" => "om_requete",
                "idx" => "]",
            ));
            //
            $val = array(
                "om_requete" => "",
                "code" => $obj,
                "libelle" => __("Requete")." '".$obj."'",
                "description" => "",
                "type" => "sql",
                "requete" => "select ".$temp." from &DB_PREFIXE".$obj." where ".$obj.".".$this->get_primary_key($obj)."='".$variable."idx'",
                "classe" => "",
                "methode" => "",
                "merge_fields" => "",
            );
            //
            $ret = $om_requete->ajouter($val, $this->f->db, null);
            if ($ret !== true) {
                //
                $this->f->addToLog(__METHOD__."(): ".$om_requete->msg);
                $this->f->displayMessage("error", __("Erreur lors de l'ajout de la requête. Contactez votre administrateur."));
                return "";
            }

            /**
             * Création de l'état
             */
            //
            $om_etat = $this->f->get_inst__om_dbform(array(
                "obj" => "om_etat",
                "idx" => "]",
            ));
            //
            $etat = $om_etat->get_default_values();
            //
            if (file_exists ("../gen/dyn/etat.inc.php"))
                include("../gen/dyn/etat.inc.php");
            elseif (file_exists ("../gen/dyn/etat.inc"))
                include("../gen/dyn/etat.inc");
            //
            $etat['om_sql'] = $om_requete->valF[$om_requete->clePrimaire];
            $etat['titre_om_htmletat'] = "le ".$variable."aujourdhui";
            $etat['corps_om_htmletatex'] = $temp1;
            // id
            $etat['id'] = $obj;
            $etat['libelle'] = $obj." gen le ".date('d/m/Y');
            $etat['actif'] = false;
            // om_collectivite
            $etat['om_collectivite'] = $_SESSION['collectivite'];
            $etat['om_etat'] = "";
            //
            $ret = $om_etat->ajouter($etat, $this->f->db, null);
            if ($ret !== true) {
                //
                $this->f->addToLog(__METHOD__."(): ".$om_etat->msg);
                $this->f->displayMessage("error", __("Erreur lors de l'ajout de l'état. Contactez votre administrateur."));
                return "";
            }
            //
            $this->f->displayMessage("ok", $obj." ".__("enregistre"));


            echo "</fieldset>";

            //
            $this->f->db->commit();

        }

        /**
         *
         */
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => OM_ROUTE_MODULE_GEN,
        ));
    }

    /**
     * VIEW - view_gen_editions_lettretype.
     *
     * @return void
     */
    function view_gen_editions_lettretype() {
        /**
         *
         */
        //
        $subtitle = "-> ".__("génération d'édition - lettre type");
        $this->f->displaySubTitle($subtitle);
        //
        $description = __("cet assistant vous permet de creer des lettres type ".
                         "directement a partir de vos tables ");
        $this->f->displayDescription($description);

        /**
         *
         */
        // Initialisation des paramètres
        $params = array(
            "obj" => array(
                "default_value" => '',
            ),
            "validation" => array(
                "default_value" => 0,
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        set_time_limit(3600);
        $DEBUG=0;
        if (isset($_POST['choice-import']) and $_POST['choice-import'] != "---") {
            $obj = $this->f->get_submitted_post_value('choice-import');
        }
        if (isset($_POST['choice-field'])) {
            $field = $this->f->get_submitted_post_value('choice-field');
        } else {
            $field = '';
        }

        /**
         * On liste les tables pour que l'utilisateur puisse choisir sur quel table
         * il souhaite créer une lettre type
         */
        // On récupère la liste des tables de la base de données
        $tables = $this->get_all_tables_from_database();
        //
        echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
        $this->f->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_MODULE_GEN."&view=editions_lettretype",
        ));
        echo "<fieldset>\n";
        echo "\t<legend>".__("Choix table :")."</legend>\n";
        echo "\t<div class=\"field\">";
        echo "<label>".__("fichier")."</label>";
        echo "<select onchange=\"submit()\" name=\"choice-import\" class=\"champFormulaire\">";
        echo "<option>---</option>";
        foreach ($tables as $table) {
            echo "<option value=\"".$table."\"";
            if ($obj == $table) {
                echo " selected=\"selected\" ";
            }
            echo ">".$table."</option>";
        }
        echo "</select>";
        echo "</div>\n";
        echo "</fieldset>\n";
        $this->f->layout->display__form_container__end();
        echo "</div>\n";

        /**
         * choix des champs
         */
        if ($obj != "" and $field=='') {
            //
            echo "\n<br>&nbsp;<div id=\"form-csv-import\" class=\"formulaire\">\n";
            $this->f->layout->display__form_container__begin(array(
                "action" => OM_ROUTE_MODULE_GEN."&view=editions_lettretype&obj=".$obj."&validation=1",
                "name" => "f1",
            ));
            echo "<fieldset>\n";
            echo "\t<legend>".__("choix des champs")."</legend>";
            echo "Utilisez ctrl key pour choix multiple<br><br>";
            //
            $sql = "select * from ".DB_PREFIXE.$obj;
            $res2 = $this->f->db->query($sql);
            $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->f->isDatabaseError($res2);
            //
            $info=$res2->tableInfo();
            echo "<select multiple name=\"choice-field[]\" class=\"champFormulaire\">";
            foreach($info as $elem){
                echo "<option>".$obj.".".$elem['name']."</option>";
            }
            echo "</select>";
            echo "<br><br>";
            $this->f->layout->display__form_input_submit(array(
                "name" => "submit-csv-import",
                "value" => __("Import")." ".$obj." ".__("dans la base"),
                "class" => "boutonFormulaire",
            ));
            echo "</fieldset>";
            $this->f->layout->display__form_container__end();
            echo "</div>\n";
        }
        /**
         *  transfert dans la base
         */
        if ($obj != "" and $field!='') {

            //
            $this->f->db->autoCommit(false);

            //
            echo "\n<br>&nbsp";
            echo "<fieldset>\n";
            echo "\t<legend> Insertion dans la table lettretype</legend>";

            /**
             * Composition de la requête SQL
             */
            // sql
            $temp='';
            $temp1='';
            if($field!=array()){
                for ($i = 0; $i < sizeof($field); $i++) {
                    $temp2=explode(".",$field[$i]);
                    $temp3=$temp2[1];
                    $temp.=$field[$i].' as '.$temp3.',';
                    $temp1.="[".$temp3.']'.chr(13).chr(10);
                }
                $temp=substr($temp, 0, strlen($temp)-1);
            }
            //
            $variable='&';

            /**
             * Création de la requête
             */
            //
            $om_requete = $this->f->get_inst__om_dbform(array(
                "obj" => "om_requete",
                "idx" => "]",
            ));
            //
            $val = array(
                "om_requete" => "",
                "code" => $obj,
                "libelle" => __("Requete")." '".$obj."'",
                "description" => "",
                "type" => "sql",
                "requete" => "select ".$temp." from &DB_PREFIXE".$obj." where ".$obj.".".$this->get_primary_key($obj)."='".$variable."idx'",
                "classe" => "",
                "methode" => "",
                "merge_fields" => "",
            );
            //
            $ret = $om_requete->ajouter($val, $this->f->db, NULL);
            if ($ret !== true) {
                //
                $this->f->addToLog(__METHOD__."(): ".$om_requete->msg);
                $this->f->displayMessage("error", __("Erreur lors de l'ajout de la requête. Contactez votre administrateur."));
                return "";
            }

            /**
             * Création de la lettre type
             */
            //
            $om_lettretype = $this->f->get_inst__om_dbform(array(
                "obj" => "om_lettretype",
                "idx" => "]",
            ));
            //
            $lettretype = $om_lettretype->get_default_values();
            // Inclusion d'un éventuel fichier de paramétrage qui permet de surcharger
            // les valeurs par défaut
            if (file_exists ("../gen/dyn/lettretype.inc.php"))
                include("../gen/dyn/lettretype.inc.php");
            elseif (file_exists ("../gen/dyn/lettretype.inc"))
                include("../gen/dyn/lettretype.inc");
            //
            $lettretype['om_sql'] = $om_requete->valF[$om_requete->clePrimaire];
            $lettretype['titre_om_htmletat']="le ".$variable."aujourdhui";
            $lettretype['corps_om_htmletatex']=$temp1;
            // id
            $lettretype['id']= $obj;
            $lettretype['libelle']= $obj." gen le ".date('d/m/Y');
            $lettretype['actif']=FALSE; // contrainte null pgsql
            // om_collectivite
            $lettretype['om_collectivite']= $_SESSION['collectivite'];
            $lettretype['om_lettretype'] = "";
            //
            $ret = $om_lettretype->ajouter($lettretype, $this->f->db, NULL);
            if ($ret !== true) {
                //
                $this->f->addToLog(__METHOD__."(): ".$om_lettretype->msg);
                $this->f->displayMessage("error", __("Erreur lors de l'ajout de l'état. Contactez votre administrateur."));
                return "";
            }

            //
            $this->f->displayMessage("ok", $obj." ".__("enregistre"));


            echo "</fieldset>";

            //
            $this->f->db->commit();

        }

        /**
         *
         */
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => OM_ROUTE_MODULE_GEN,
        ));
    }

    /**
     * VIEW - view_gen_editions_sousetat.
     *
     * @return void
     */
    function view_gen_editions_sousetat() {
        /**
         *
         */
        //
        $subtitle = "-> ".__("génération d'édition - sous etat");
        $this->f->displaySubTitle($subtitle);
        //
        $description = __("cet assistant vous permet de creer des sous etats ".
                         "directement a partir de vos tables ");
        $this->f->displayDescription($description);

        /**
         *
         */
        // Initialisation des paramètres
        $params = array(
            "obj" => array(
                "default_value" => '',
            ),
            "validation" => array(
                "default_value" => 0,
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        set_time_limit(3600);
        $DEBUG=0;
        if (isset($_POST['choice-import']) and $_POST['choice-import'] != "---") {
            $obj = $this->f->get_submitted_post_value('choice-import');
        }
        if (isset($_POST['choice-field'])) {
            $field = $this->f->get_submitted_post_value('choice-field');
        } else {
            $field = '';
        }
        if (isset($_POST['choice-cle'])) {
            $cle = $this->f->get_submitted_post_value('choice-cle');
        }else{
            $cle = '';
        }

        /**
         * On liste les tables pour que l'utilisateur puisse choisir sur quel table
         * il souhaite créer un sous état
         */
        // On récupère la liste des tables de la base de données
        $tables = $this->get_all_tables_from_database();
        //
        echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
        $this->f->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_MODULE_GEN."&view=editions_sousetat",
        ));
        echo "<fieldset>\n";
        echo "\t<legend>".__("Choix table :")."</legend>\n";
        echo "\t<div class=\"field\">";
        echo "<label>".__("fichier")."</label>";
        echo "<select onchange=\"submit()\" name=\"choice-import\" class=\"champFormulaire\">";
        echo "<option>---</option>";
        foreach ($tables as $table) {
            echo "<option value=\"".$table."\"";
            if ($obj == $table) {
                echo " selected=\"selected\" ";
            }
            echo ">".$table."</option>";
        }
        echo "</select>";
        echo "</div>\n";
        echo "</fieldset>\n";
        $this->f->layout->display__form_container__end();
        echo "</div>\n";

        /**
         * choix des champs
         */
        if ($obj != "" and $field=='') {
            //
            echo "\n<br>&nbsp;<div id=\"form-csv-import\" class=\"formulaire\">\n";
            $this->f->layout->display__form_container__begin(array(
                "action" => OM_ROUTE_MODULE_GEN."&view=editions_sousetat&obj=".$obj."&validation=1",
                "name" => "f1",
            ));
            echo "<fieldset>\n";
            echo "\t<legend>".__("choix des champs")."</legend>";


            echo "<table><tr>";
            echo "Utilisez ctrl key pour choix multiple<br><br>";
            //
            $sql = "select * from ".DB_PREFIXE.$obj;
            $res2 = $this->f->db->query($sql);
            $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->f->isDatabaseError($res2);
            //
            $info=$res2->tableInfo();
            echo "<td><select multiple name=\"choice-field[]\" class=\"champFormulaire\">";
            foreach($info as $elem){
                echo "<option value=\"".$obj."|".$elem['name']."|".$elem['len']."|".$elem['type']."\">".$obj.".".$elem['name']."</option>";
            }
            echo "</select></td>";
            echo "<td>".__("choisir la cle de selection")."</td>";
            echo "<td><select name=\"choice-cle\" class=\"champFormulaire\">";
            foreach($info as $elem){
                echo "<option value=\"".$obj.".".$elem['name']."\">".$obj.".".$elem['name']."</option>";
            }
            echo "</select></td>";
            echo "</tr><tr>";
            echo "<td><br><br>";
            $this->f->layout->display__form_input_submit(array(
                "name" => "submit-csv-import",
                "value" => __("Import")." ".$obj." ".__("dans la base"),
                "class" => "boutonFormulaire",
            ));
            echo "</td></tr></table>";
            echo "</fieldset>";
            $this->f->layout->display__form_container__end();
            echo "</div>\n";
        }
        /**
         *  transfert dans la base
         */
        if ($obj != "" and $field!='' and $cle!='') {
            echo "\n<br>&nbsp";
            echo "<fieldset>\n";
            echo "\t<legend> Insertion dans la table sous etat</legend>";
            // sql
            $temp=''; // field
            $temp1=''; // champ requete
            $longueur=0;
            $dernierchamp=0;
            if($field!=array()){
                for ($i = 0; $i < sizeof($field); $i++) {
                    $temp=explode("|",$field[$i]);
                    $table=$temp[0];
                    $champ=$temp[1];
                    $len[$i]=$temp[2];
                    $type=$temp[3];
                    $temp1.=$table.".".$champ.' as '.$champ.',';
                    if($len[$i]!='')
                        $len[$i]=40;
                    $longueur=$longueur+$len[$i];
                    $dernierchamp++;
                }
                $temp1=substr($temp1, 0, strlen($temp1)-1);
            }
            //parametres
            $longueurtableau= 195;
            $variable='&'; // nouveau
            //titre
            $sousetat['titrehauteur']=10;
            $sousetat['titrefont']='helvetica';
            $sousetat['titreattribut']='B';
            $sousetat['titretaille']=10;
            $sousetat['titrebordure']=0;
            $sousetat['titrealign']='L';
            $sousetat['titrefond']=0;
            $sousetat['titrefondcouleur']="255-255-255";
            $sousetat['titretextecouleur']="0-0-0";
            // intervalle
            $sousetat['intervalle_debut']=0;
            $sousetat['intervalle_fin']=5;
            // entete
            $sousetat['entete_flag']=1;
            $sousetat['entete_fond']=1;
            $sousetat['entete_hauteur']=7;
            $sousetat['entete_fondcouleur']="255-255-255";
            $sousetat['entete_textecouleur']="0-0-0";
            // tableau
            $sousetat['tableau_bordure']=1;
            $sousetat['tableau_fontaille']=10;
            // bordure
            $sousetat['bordure_couleur']="0-0-0";
            // sous etat fond
            $sousetat['se_fond1']="243-246-246";
            $sousetat['se_fond2']="255-255-255";
            // cellule
            $sousetat['cellule_fond']=1;
            $sousetat['cellule_hauteur']=7;
            // total
            $sousetat['cellule_fond_total']=1;
            $sousetat['cellule_fontaille_total']=10;
            $sousetat['cellule_hauteur_total']=15;
            $sousetat['cellule_fondcouleur_total']="255-255-255";
            // moyenne
            $sousetat['cellule_fond_moyenne']=1;
            $sousetat['cellule_fontaille_moyenne']=10;
            $sousetat['cellule_hauteur_moyenne']=5;
            $sousetat['cellule_fondcouleur_moyenne']="212-219-220";
            // nombre d enregistrement
            $sousetat['cellule_fond_nbr']=1;
            $sousetat['cellule_fontaille_nbr']=10;
            $sousetat['cellule_hauteur_nbr']=7;
            $sousetat['cellule_fondcouleur_nbr']="255-255-255";

            // parametre custom
            if (file_exists ("../gen/dyn/sousetat.inc.php"))
                include("../gen/dyn/sousetat.inc.php");
            elseif (file_exists ("../gen/dyn/sousetat.inc"))
                include("../gen/dyn/sousetat.inc");

            // parametres sousetat
            $sousetat['om_sql']="select ".$temp1." from &DB_PREFIXE".$obj." where ".$cle."='".$variable."idx'";
            // id
            $temp='';
            $temp=explode('.',$cle);
            $sousetat['id']= $obj.'.'.$temp[1];
            $sousetat['libelle']= "gen le ".date('d/m/Y');
            $sousetat['titre'] = __("liste")." ".$obj;
            // om_collectivite
            $sousetat['om_collectivite']= $_SESSION['collectivite'];

            // parametre ************************************
            // calcul de la longueur
            echo "<br>Tableau de : ".$longueurtableau." pour ".
                 $longueur." caracteres <br><br>";
            $quotient=$longueurtableau/$longueur;
            $temp1="";$temp2="";$temp3="";$temp4="";$temp5="";
            for ($i = 0; $i < sizeof($len); $i++){
                // largeur
                $temp=$len[$i]*$quotient;
                if($i==$dernierchamp-1){
                    $temp1.=$temp; // largeur
                    $temp2.='C'; // align
                    $temp3.='LTBR';// bordure
                    $temp4.='0';  // stats
                    $temp5.='999'; // total
                }else{
                    // separateur ."|"
                    $temp1.=$temp."|"; // largeur
                    $temp2.="C"."|"; // alihgn
                    $temp3.="TLB"."|"; // bordure
                    $temp4.="0"."|"; // stats
                    $temp5.='999'."|"; // total
                }
            }
            $sousetat['tableau_largeur']=$longueurtableau;
            $sousetat['cellule_largeur']=$temp1;
            $sousetat['entetecolone_align']=$temp2;
            $sousetat['entetecolone_bordure']=$temp3;
            $sousetat['entete_orientation']=$temp4;

            $sousetat['cellule_bordure_un']=$temp3;
            $sousetat['cellule_bordure']=$temp3;
            $sousetat['cellule_align']=$temp2;

            $sousetat['cellule_bordure_total']=$temp3;
            $sousetat['cellule_align_total']=$temp2;

            $sousetat['cellule_bordure_moyenne']=$temp3;
            $sousetat['cellule_align_moyenne']=$temp2;

            $sousetat['cellule_bordure_nbr']=$temp3;
            $sousetat['cellule_align_nbr']=$temp2;
            //*
            $sousetat['cellule_numerique']=$temp5;
            $sousetat['cellule_total']=$temp4;
            $sousetat['cellule_moyenne']=$temp4;
            $sousetat['cellule_compteur']=$temp4;

            $sousetat['actif']=FALSE; // contrainte null pgsql

            // next Id
            $sousetat['om_sousetat'] = $this->f->db->nextId(DB_PREFIXE.'om_sousetat');
            // Logger
            $this->f->addToLog(__METHOD__."(): db->nextId(\"".DB_PREFIXE."om_sousetat\");", VERBOSE_MODE);
            // Exécution de la requête
            $res = $this->f->db->autoExecute(DB_PREFIXE.'om_sousetat', $sousetat, DB_AUTOQUERY_INSERT);
            // Logger
            $this->f->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE."om_sousetat\", ".print_r($sousetat, true).", DB_AUTOQUERY_INSERT);", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            //
            echo $obj." ".__("enregistre");
            echo "</fieldset>";
        }

        /**
         *
         */
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => OM_ROUTE_MODULE_GEN,
        ));
    }

    // ------------------------------------------------------------------------
    // }}} END - GENERATION DES EDITIONS
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - MIGRATION DES ANCIENNES EDITIONS
    // ------------------------------------------------------------------------

    /**
     * VIEW - view_gen_editions_old.
     *
     * @return void
     */
    function view_gen_editions_old() {
        /*
         TRANSFERT EN base UTF8 : pb d encodage (21/07/2011)
         pour recuperer des .inc.php en ISO 8889-1
         - transformer en utf8
         - remplacer les £ (provoquant un bug d'encodage sql) par &
        */
        /**
         * @ignore
         */
        function enrvb($val) {
            $temp='';
            if($val!=array()){
                for ($i = 0; $i < sizeof($val); $i++) {
                    $temp.=$val[$i].'-';
                }
                $temp=substr($temp, 0, strlen($temp)-1);
            }
            return $temp;
        }

        /**
         * @ignore
         */
        function encol($val) {
            $temp='';
            if($val!=array()){
                for ($i = 0; $i < sizeof($val); $i++) {
                    $temp.=$val[$i]."|";
                }
                $temp=substr($temp, 0, strlen($temp)-1);
            }
            return $temp;
        }

        /**
         * @ignore
         */
        function encol_rc($val) {
            $temp='';
            if($val!=array()){
                for ($i = 0; $i < sizeof($val); $i++) {
                    $temp.=$val[$i].chr(13).chr(10);
                }
                $temp=substr($temp, 0, strlen($temp)-2);
            }
            return $temp;
        }

        /**
         * @ignore
         */
        function envar($val) {
            $val=str_replace('£','&',$val);
            return $val;
        }

        //
        $subtitle = "-> ".__("import des anciennes éditions");
        $this->f->displaySubTitle($subtitle);
        //
        $description = __(
            "Cette page vous permet d'importer les anciens etat, sousetat,".
            " lettretype contenu en repertoire ../gen/inc directement dans".
            " le nouveau format du framework."
        );
        $this->f->displayDescription($description);

        // Initialisation des paramètres
        $params = array(
            "obj" => array(
                "default_value" => '',
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        //
        if (isset($_POST['fichier']) and $this->f->get_submitted_post_value('fichier') != "---") {
            $obj = $this->f->get_submitted_post_value('fichier');
        }

        /**
         * On liste les fichiers .inc (compatibilite) et .inc.php dans /inc
         */
        $dir = getcwd();
        $dir = "../gen/inc/";
        if (file_exists($dir) === false) {
            $this->f->displayMessage(
                "error",
                sprintf(
                    __("Le répertoire '%s' de récupération des anciennes éditions n'existe pas."),
                    $dir
                )
            );
            return;
        }
        $dossier = opendir($dir);
        $tab = array();
        while ($entree = readdir($dossier)) {
            if (substr($entree,  strlen($entree) - 8, 8)=='etat.inc'
                or substr($entree,  strlen($entree) - 12, 12)=='etat.inc.php'
                or substr($entree,  strlen($entree) - 14, 14)=='lettretype.inc'
                or substr($entree,  strlen($entree) - 18, 18)=='lettretype.inc.php') {
                array_push($tab, array('file' => $entree));
            }
        }
        closedir($dossier);
        asort($tab);

        /**
         * Formulaire de choix du fichier à importer
         */
        //
        $this->f->layout->display_start_fieldset(array(
            "legend_content" => __("sélection du fichier"),
        ));
        // Ouverture du formulaire
        $this->f->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_MODULE_GEN."&view=editions_old",
            "id" => "gen_editions_old_form_choice",
        ));
        // Paramétrage des champs du formulaire
        $champs = array("fichier");
        // Création d'un nouvel objet de type formulaire
        $form = $this->f->get_inst__om_formulaire(array(
            "validation" => 0,
            "maj" => 0,
            "champs" => $champs,
        ));
        // Paramétrage des champs du formulaire
        $form->setLib("fichier", __("Ancien fichier d'édition à importer"));
        $form->setType("fichier", "select");
        $form->setTaille("fichier", 25);
        $form->setOnChange("fichier", "submit()");
        $form->setMax("fichier", 25);
        $form->setVal("fichier", $obj);
        //
        $contenu = array(array(""), array(__("---")));
        foreach ($tab as $elem) {
            $contenu[0][] = $elem["file"];
            $contenu[1][] = $elem["file"];
        }
        $form->setSelect("fichier", $contenu);
        // Affichage du formulaire
        $form->afficher($champs, 0, false, false);
        // Fermeture du fomulaire
        $this->f->layout->display__form_container__end();
        // Fermeture du fieldset
        $this->f->layout->display_stop_fieldset();

        /**
         *
         */
        if ($obj != "") {
            //
            $this->f->layout->display_start_fieldset(array(
                "legend_content" => __("import"),
            ));

            /**
             * TREATMENT
             */
            if (isset($_POST["submit-gen-editions-old"])) {
                //
                $this->f->db->autoCommit(false);
                //
                if (substr($obj,  strlen($obj) - 12, 12)=='sousetat.inc'
                    or substr($obj,  strlen($obj) - 16, 16)=='sousetat.inc.php'){
                    // **** parametres de base ****
                    $longueurtableau= 195;
                    $sousetat['titrehauteur']=10;
                    $sousetat['titrefont']='helvetica';
                    $sousetat['titreattribut']='B';
                    $sousetat['titretaille']=10;
                    $sousetat['titrebordure']=0;
                    $sousetat['titrealign']='L';
                    $sousetat['titrefond']=0;
                    $sousetat['titrefondcouleur']="255-255-255";
                    $sousetat['titretextecouleur']="0-0-0";
                    // intervalle
                    $sousetat['intervalle_debut']=0;
                    $sousetat['intervalle_fin']=5;
                    // entete
                    $sousetat['entete_flag']=1;
                    $sousetat['entete_fond']=1;
                    $sousetat['entete_hauteur']=7;
                    $sousetat['entete_fondcouleur']="255-255-255";
                    $sousetat['entete_textecouleur']="0-0-0";
                    // tableau
                    $sousetat['tableau_bordure']=1;
                    $sousetat['tableau_fontaille']=10;
                    // bordure
                    $sousetat['bordure_couleur']="0-0-0";
                    // sous etat fond
                    $sousetat['se_fond1']="243-246-246";
                    $sousetat['se_fond2']="255-255-255";
                    // cellule
                    $sousetat['cellule_fond']=1;
                    $sousetat['cellule_hauteur']=7;
                    // total
                    $sousetat['cellule_fond_total']=1;
                    $sousetat['cellule_fontaille_total']=10;
                    $sousetat['cellule_hauteur_total']=15;
                    $sousetat['cellule_fondcouleur_total']="255-255-255";
                    // moyenne
                    $sousetat['cellule_fond_moyenne']=1;
                    $sousetat['cellule_fontaille_moyenne']=10;
                    $sousetat['cellule_hauteur_moyenne']=5;
                    $sousetat['cellule_fondcouleur_moyenne']="212-219-220";
                    // nombre d enregistrement
                    $sousetat['cellule_fond_nbr']=1;
                    $sousetat['cellule_fontaille_nbr']=10;
                    $sousetat['cellule_hauteur_nbr']=7;
                    $sousetat['cellule_fondcouleur_nbr']="255-255-255";
                    include("../gen/inc/".$obj);
                    // sql
                    $sousetat['om_sql']=envar($sousetat['sql']);
                    unset($sousetat['sql']);
                    // id
                    $sousetat['id']= substr($obj, 0,  strlen($obj) - 13);
                    $sousetat['libelle']= 'import du '.date('d/m/Y');
                    $sousetat['titre']=envar($sousetat['titre']);
                    // om_collectivite
                    $sousetat['om_collectivite']= $_SESSION['collectivite'];
                    // tableau enrvb et encol
                    $sousetat['titrefondcouleur']=enrvb($sousetat['titrefondcouleur']);
                    $sousetat['titretextecouleur']=enrvb($sousetat['titretextecouleur']);
                    $sousetat['entete_orientation']=encol($sousetat['entete_orientation']);
                    $sousetat['entetecolone_bordure']=encol($sousetat['entetecolone_bordure']);
                    $sousetat['entetecolone_align']=encol($sousetat['entetecolone_align']);
                    $sousetat['entete_fondcouleur']=enrvb($sousetat['entete_fondcouleur']);
                    $sousetat['entete_textecouleur']=enrvb($sousetat['entete_textecouleur']);
                    $sousetat['bordure_couleur']=enrvb($sousetat['bordure_couleur']);
                    $sousetat['se_fond1']=enrvb($sousetat['se_fond1']);
                    $sousetat['se_fond2']=enrvb($sousetat['se_fond2']);
                    $sousetat['cellule_largeur']=encol($sousetat['cellule_largeur']);
                    $sousetat['cellule_bordure_un']=encol($sousetat['cellule_bordure_un']);
                    $sousetat['cellule_bordure']=encol($sousetat['cellule_bordure']);
                    $sousetat['cellule_align']=encol($sousetat['cellule_align']);
                    $sousetat['cellule_fondcouleur_total']=enrvb($sousetat['cellule_fondcouleur_total']);
                    $sousetat['cellule_bordure_total']=encol($sousetat['cellule_bordure_total']);
                    $sousetat['cellule_align_total']=encol($sousetat['cellule_align_total']);
                    $sousetat['cellule_fondcouleur_moyenne']=enrvb($sousetat['cellule_fondcouleur_moyenne']);
                    $sousetat['cellule_bordure_moyenne']=encol($sousetat['cellule_bordure_moyenne']);
                    $sousetat['cellule_align_moyenne']=encol($sousetat['cellule_align_moyenne']);
                    $sousetat['cellule_fondcouleur_nbr']=enrvb($sousetat['cellule_fondcouleur_nbr']);
                    $sousetat['cellule_bordure_nbr']=encol($sousetat['cellule_bordure_nbr']);
                    $sousetat['cellule_align_nbr']=encol($sousetat['cellule_align_nbr']);
                    $sousetat['cellule_numerique']=encol($sousetat['cellule_numerique']);
                    $sousetat['cellule_total']=encol($sousetat['cellule_total']);
                    $sousetat['cellule_moyenne']=encol($sousetat['cellule_moyenne']);
                    $sousetat['cellule_compteur']=encol($sousetat['cellule_compteur']);
                    $sousetat['actif']=FALSE;
                    // cle
                    $sousetat['om_sousetat']=$this->f->db->nextId(DB_PREFIXE.'om_sousetat');
                    //
                    $res = $this->f->db->autoExecute(
                        DB_PREFIXE.'om_sousetat',
                        $sousetat,
                        DB_AUTOQUERY_INSERT
                    );
                    //
                    if ($this->f->isDatabaseError($res, true)) {
                        //
                        $this->f->db->rollback();
                        $message_class = "error";
                        $message = __("Erreur de base de donnees. Contactez votre administrateur.");
                    } else {
                        //
                        $this->f->db->commit();
                        $message_class = "valid";
                        $message = $obj." ".__("importe");
                    }
                } else {
                    // etat
                    if (substr($obj,  strlen($obj) - 8, 8)=='etat.inc'
                        or substr($obj,  strlen($obj) - 12, 12)=='etat.inc.php'){
                        // *** parametre de base ***
                        $etat['orientation']='P';
                        $etat['format']='A4';
                        // footer
                        $etat['footerfont']='helvetica';
                        $etat['footerattribut']='I';
                        $etat['footertaille']='8';
                        // logo
                        $etat['logo']='logopdf.png';
                        $etat['logoleft']='58';
                        $etat['logotop']='7';
                        // titre
                        $etat['titreleft']='41';
                        $etat['titretop']='36';
                        $etat['titrelargeur']='130';
                        $etat['titrehauteur']='10';
                        $etat['titrefont']='helvetica';
                        $etat['titreattribut']='B';
                        $etat['titretaille']='15';
                        $etat['titrebordure']='0';
                        $etat['titrealign']='C';
                        // corps
                        $etat['corpsleft']='7';
                        $etat['corpstop']='57';
                        $etat['corpslargeur']='195';
                        $etat['corpshauteur']='5';
                        $etat['corpsfont']='helvetica';
                        $etat['corpsattribut']='';
                        $etat['corpstaille']='10';
                        $etat['corpsbordure']='0';
                        $etat['corpsalign']='J';
                        // sous etat
                        $etat['se_font']='helvetica';
                        $etat['se_margeleft']='8';
                        $etat['se_margetop']='5';
                        $etat['se_margeright']='5';
                        $etat['se_couleurtexte']="0-0-0";
                        include("../gen/inc/".$obj);
                        // sql
                        $etat['om_sql']=envar($etat['sql']);
                        unset($etat['sql']);
                        // id
                        $etat['id']= substr($obj, 0,  strlen($obj) - 9);
                        $etat['libelle']= 'import du '.date('d/m/Y');
                        $etat['titre']=envar($etat['titre']);
                        $etat['corps']=envar($etat['corps']);
                        // om_collectivite
                        $etat['om_collectivite']= $_SESSION['collectivite'];
                        // tableau enrvb et encol
                        $etat['se_couleurtexte']=enrvb($etat['se_couleurtexte']);
                        $etat['sousetat']=encol_rc($etat['sousetat']);
                        $etat['actif']=FALSE;
                        //
                        $etat['om_etat'] = $this->f->db->nextId(DB_PREFIXE.'om_etat');
                        //
                        $res = $this->f->db->autoExecute(
                            DB_PREFIXE.'om_etat',
                            $etat,
                            DB_AUTOQUERY_INSERT
                        );
                        //
                        if ($this->f->isDatabaseError($res, true)) {
                            //
                            $this->f->db->rollback();
                            $message_class = "error";
                            $message = __("Erreur de base de donnees. Contactez votre administrateur.");
                        } else {
                            //
                            $this->f->db->commit();
                            $message_class = "valid";
                            $message = $obj." ".__("importe");
                        }
                    }
                    // lettretype
                    if (substr($obj,  strlen($obj) - 14, 14)=='lettretype.inc'
                        or substr($obj,  strlen($obj) - 18, 18)=='lettretype.inc.php'){
                        // *** parametre de base ***
                        $lettretype['orientation']='P';
                        $lettretype['format']='A4';
                        // logo
                        $lettretype['logo']='logopdf.png';
                        $lettretype['logoleft']='58';
                        $lettretype['logotop']='7';
                        // titre
                        $lettretype['titreleft']='41';
                        $lettretype['titretop']='36';
                        $lettretype['titrelargeur']='130';
                        $lettretype['titrehauteur']='10';
                        $lettretype['titrefont']='helvetica';
                        $lettretype['titreattribut']='B';
                        $lettretype['titretaille']='15';
                        $lettretype['titrebordure']='0';
                        $lettretype['titrealign']='C';
                        // corps
                        $lettretype['corpsleft']='7';
                        $lettretype['corpstop']='57';
                        $lettretype['corpslargeur']='195';
                        $lettretype['corpshauteur']='5';
                        $lettretype['corpsfont']='helvetica';
                        $lettretype['corpsattribut']='';
                        $lettretype['corpstaille']='10';
                        $lettretype['corpsbordure']='0';
                        $lettretype['corpsalign']='J';
                        include("../gen/inc/".$obj);
                        // sql
                        $lettretype['om_sql']=envar($lettretype['sql']);
                        unset($lettretype['sql']);
                        // id _lettretype.inc.php 15 car *** bug 17/07/2011
                        $lettretype['id']= substr($obj, 0,  strlen($obj) - 15);
                        $lettretype['libelle']= 'import du '.date('d/m/Y');
                        $lettretype['titre']=envar($lettretype['titre']);
                        $lettretype['corps']=envar($lettretype['corps']);
                        $lettretype['actif']=FALSE;
                        // om_collectivite
                        $lettretype['om_collectivite']= $_SESSION['collectivite'];
                        // tableau enrvb et encol
                        // *** bug du 17/07/2011
                        //$lettretype['se_couleurtexte']=enrvb($lettretype['se_couleurtexte']);
                        //
                        $lettretype['om_lettretype'] = $this->f->db->nextId(DB_PREFIXE.'om_lettretype');
                        //
                        $res = $this->f->db->autoExecute(
                            DB_PREFIXE.'om_lettretype',
                            $lettretype,
                            DB_AUTOQUERY_INSERT
                        );
                        //
                        if ($this->f->isDatabaseError($res, true)) {
                            //
                            $this->f->db->rollback();
                            $message_class = "error";
                            $message = __("Erreur de base de donnees. Contactez votre administrateur.");
                        } else {
                            //
                            $this->f->db->commit();
                            $message_class = "valid";
                            $message = $obj." ".__("importe");
                        }
                    }
                }
                $this->f->displayMessage($message_class, $message);
            }

            /**
             * FORMULAIRE DE VALIDATION
             */
            // Ouverture du formulaire
            $this->f->layout->display__form_container__begin(array(
                "action" => OM_ROUTE_MODULE_GEN."&view=editions_old&obj=".$obj,
                "id" => "gen_editions_old_form_valid",
            ));
            // Paramétrage des champs du formulaire
            $champs = array();
            // Création d'un nouvel objet de type formulaire
            $form = $this->f->get_inst__om_formulaire(array(
                "validation" => 0,
                "maj" => 0,
                "champs" => $champs,
            ));
            // Affichage du formulaire
            $form->afficher($champs, 0, false, false);
            //
            include("../gen/inc/".$obj);
            echo $obj."<br><br>";
            if (substr($obj,  strlen($obj) - 12, 12)=='sousetat.inc'
                or substr($obj,  strlen($obj) - 16, 16)=='sousetat.inc.php'){
                echo $sousetat['titre']."<br><br>";
                echo $sousetat['sql']."<br><br>";
            }else{
                if (substr($obj,  strlen($obj) - 8, 8)=='etat.inc'
                    or substr($obj,  strlen($obj) - 12, 12)=='etat.inc.php'){
                    echo $etat['titre']."<br><br>";
                    echo $etat['sql']."<br><br>";
                }
                                // lettretype
                if (substr($obj,  strlen($obj) - 14, 14)=='lettretype.inc'
                    or substr($obj,  strlen($obj) - 18, 18)=='lettretype.inc.php'){
                    echo $lettretype['titre']."<br><br>";
                    echo $lettretype['sql']."<br><br>";
                }
            }
            // Affichage du bouton
            $this->f->layout->display__form_controls_container__begin(array(
                "controls" => "bottom",
            ));
            $this->f->layout->display__form_input_submit(array(
                "value" => __("Valider"),
                "name" => "submit-gen-editions-old",
            ));
            $this->f->layout->display__form_controls_container__end();
            // Fermeture du fomulaire
            $this->f->layout->display__form_container__end();
            // Fermeture du fieldset
            $this->f->layout->display_stop_fieldset();
        }

        /**
         *
         */
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => OM_ROUTE_MODULE_GEN,
        ));
    }

    // ------------------------------------------------------------------------
    // }}} END - MIGRATION DES ANCIENNES EDITIONS
    // ------------------------------------------------------------------------
}
