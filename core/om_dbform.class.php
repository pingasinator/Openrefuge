<?php
/**
 * Ce script contient la définition de la classe 'dbform'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_dbform.class.php 4348 2018-07-20 16:49:26Z softime $
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
 * Définition de la classe 'dbform'.
 *
 * Cette classe permet de gérer une interface entre un objet métier et sa
 * représentation dans la base de données.
 *
 * @abstract
 */
class dbform extends om_base {

    /**
     * Instance de connexion à la base de données.
     * @var null|database
     * @deprecated Il faut utiliser la propriété 'db' de la classe
     *             'application' : $this->f->db.
     */
    var $db = null;

    /**
     * Informations DB nom de chaque champ
     * @var array
     */
    var $champs = array();

    /**
     * Informations DB type de chaque champ
     * @var array
     */
    var $type = array();

    /**
     * Informations DB taille de chaque champ
     * @var array
     */
    var $longueurMax = array();

    /**
     * Informations DB flag de chaque champ ???
     * @var array
     */
    var $flags = array();

    /**
     * Valeur des champs requete selection
     * @var array
     */
    var $val = array();

    /**
     * Valeur des champs retournes pour saisie et maj
     * @var array
     */
    var $valF = array();

    /**
     * Message retourne au formulaire de saisie
     * @var string
     */
    var $msg = "";

    /**
     * Flag pour validation des donnees
     * @var boolean
     */
    var $correct;

    /**
     * Objet formulaire
     * @var null|formulaire
     */
    var $form = null;

    /**
     * Valeurs de tous les parametres
     * @var array
     */
    var $parameters = array();

    /**
     * Actions du portlet supplementaires provenant d'un script form.inc
     * @var array
     */
    var $actions_sup = array();

    /**
     * Actions par defaut dans openMairie
     * @var array
     */
    var $class_actions = array();

    /**
     * Liste des champs uniques
     * @var array
     */
    var $unique_key = array();

    /**
     * Liste des champs not null
     * @var array
     */
    var $required_field = array();

    /**
     * Marqueur permettant de déterminer si l'action sur laquelle on se trouve
     * est disponible sur l'objet instancié et dans le contexte.
     * @var null|boolean
     */
    var $_is_action_available = null;

    /**
     * Liste des métadonnées communes à l'ensemble des fichiers de l'application
     * @var array
     */
    var $metadata_global = array();

    /**
     * Ce tableau récupère les messages d'erreurs
     * @var array Valeurs de toutes les erreurs
     */
    var $errors = array();

    /**
     * Tableau permettant de stocker les fichiers en cours de modification
     * dans le cas ou la suite de la transaction ne se déroule pas bien.
     * @var array au format retourné pas le storage
     */
    var $tmpFile = array();

    /**
     * Flag permettant de définir si on setrouve en sousformulaire.
     * @var boolean
     */
    var $sousform;

    /**
     * Attribut permettant de stocker le paramètre du retourformulaire
     * (objet lié du formulaire principal appelé également contexte) uniquement
     * valable dans le cas d'un sous formulaire
     * @var mixed
     */
    var $retourformulaire;

    /**
     * Liste des clés étrangères avec la liste des éventuelles surcharges
     * de leur classe.
     * $foreign_keys_extended = array(
     *    "<foreign_key1_table1>" => array("<classe_surcharge_1_de_table1>", ),
     *    "<foreign_key2_table2>" => array("<classe_surcharge_1_de_table2>", ),
     * );
     * @var mixed
     */
    var $foreign_keys_extended = array();

    /**
     * Constructeur.
     *
     * @param string $id Identifiant de l'objet.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     */
    function __construct($id, &$dnu1 = null, $dnu2 = null) {
        $this->constructeur($id);
    }

    /**
     * Constructeur.
     *
     * @param string $id Identifiant de l'objet.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     */
    function constructeur($id, &$dnu1 = null, $dnu2 = null) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();
        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);
        // @deprecated A supprimer
        // Ce raccourci rend la réalité du code difficilement lisible, il est
        // préférable de ne pas l'utiliser
        $this->db = $this->f->db;
        // Chargement de la configuration depuis un éventuel script form.inc
        $this->load_var_from_sql_forminc();
        // Si la nouvelle gestion des actions est activée on fusionne les actions
        // de base avec celles de la surcharges, sinon ancien fonctionnement
        if ($this->is_option_class_action_activated() === true) {
            // Appel de la méthode de définition des tableaux d'actions.
            $this->init_class_actions();
        } else {
            // @deprecated A supprimer
            // Ancienne gestion des actions
            // Sauvegarde des actions contextuelles supplementaires
            if (isset($this->_var_from_sql_forminc__portlet_actions) === true) {
                $this->actions_sup = $this->_var_from_sql_forminc__portlet_actions;
            }
        }
        // Initialise les attributs 'champs', 'longueurMax', 'type', 'flags' et
        // 'val' de la classe pour un enregistrement donné.
        $this->init_record_data($id);
    }

    /**
     * Récupère les variables définies dans un script form.inc.
     *
     * Cette méthode permet de charger toutes les variables définies dans un
     * éventuel script form.inc ou custom. Elle permet d'affecter chacune de
     * ces variables dans un attribut de la classe. Le nom de cet attribut est
     * composé comme suit : "_var_from_sql_forminc__<NOM_DE_LA_VARIABLE>".
     *
     * @return void
     */
    function load_var_from_sql_forminc() {
        // Inclusion du script [sql/<OM_DB_PHPTYPE>/<TABLE>.form.inc.php]
        $standard_script_path = "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php";
        $custom_script_path = $this->f->get_custom("form", $this->table);
        if ($custom_script_path !== null) {
            include $custom_script_path;
        } elseif (file_exists($standard_script_path) === true) {
            include $standard_script_path;
        }
        unset($standard_script_path);
        unset($custom_script_path);
        // Affectation des variables définies dans le script à des attributs
        // de la classe.
        foreach (get_defined_vars() as $key => $value) {
            $var_name = "_var_from_sql_forminc__".$key;
            $this->$var_name = $value;
        }
    }

    /**
     * Initialise l'enregistrement à partir d'une requête de sélection.
     *
     * La requête de sélection est composée à partir des variables sql from.inc
     * 'champs', tableSelect' et 'selection'. Grâce aux éléments de cette requête,
     * cette méthode initialise les attributs 'champs', 'longueurMax', 'type',
     * 'flags' et 'val' de la classe pour l'enregistrement donné.
     *
     * @param mixed $id Identifiant de l'enregistrement à initialiser.
     *
     * @return void
     */
    function init_record_data($id) {
        /**
         * SELECT clause
         */
        //
        if (isset($this->_var_from_sql_forminc__champs) === true) {
            $select_clause_array = $this->_var_from_sql_forminc__champs;
        } else {
            $select_clause_array = $this->get_var_sql_forminc__champs();
        }
        //
        $select_clause_string = "";
        foreach ($select_clause_array as $elem) {
            $select_clause_string .= $elem.", ";
        }
        $select_clause_string = substr(
            $select_clause_string,
            0,
            strlen($select_clause_string) - 2
        );
        //
        $sql = " SELECT ".$select_clause_string." ";
        /**
         * FROM clause
         */
        //
        if (isset($this->_var_from_sql_forminc__tableSelect) === true) {
            $from_clause_string = $this->_var_from_sql_forminc__tableSelect;
        } else {
            $from_clause_string = $this->get_var_sql_forminc__tableSelect();
        }
        //
        $sql .= " FROM ".$from_clause_string." ";
        /**
         * WHERE clause
         */
        //
        if (isset($this->_var_from_sql_forminc__selection) === true) {
            $selection = $this->_var_from_sql_forminc__selection;
        } else {
            $selection = $this->get_var_sql_forminc__selection();
        }
        // Si mode ajout
        if ($id == "]") {
            // Remplacement du 'and' par 'where' dans la variable $selection
            $selection = ltrim($selection);
            if (strtolower(substr($selection, 0, 3)) == "and") {
                $selection = " WHERE ".substr($selection, 4, strlen($selection));
            }
        } else {
            $sql .= " WHERE ".$this->getCle($id)." ";
        }
        $sql .= " ".$selection." ";
        /**
         * Exécution de la requête
         */
        $res = $this->f->db->limitquery($sql, 0, 1);
        $this->addToLog(
            __METHOD__."(): db->limitquery(\"".$sql."\", 0, 1);",
            VERBOSE_MODE
        );
        if ($this->f->isDatabaseError($res, true)) {
            $this->erreur_db($res->getDebugInfo(), $res->getMessage(), $this->table);
            return null;
        }
        /**
         *
         */
        // Recuperation des informations sur la structure de la table
        // ??? compatibilite POSTGRESQL (len = -1, type vide, flags vide)
        $info = $res->tableInfo();
        // Recuperation du nom de chaque champ dans l'attribut 'champs'
        $i = 0;
        foreach ($info as $elem) {
            $this->champs[$i++] = $elem['name'];
        }
        // Recuperation de la taille de chaque champ dans l'attibut 'longueurMax'
        $i = 0;
        foreach ($info as $elem) {
            $this->longueurMax[$i++] = $elem['len'];
        }
        // Recuperation du type de chaque champ dans l'attribut 'type'
        // ??? Non utilise
        $i = 0;
        foreach ($info as $elem) {
            $this->type[$i++] = $elem['type'];
        }
        // Recuperation du flag de chaque champ dans l'attribut 'flags'
        // ??? Non utilise
        $i = 0;
        foreach ($info as $elem) {
            $this->flags[$i++] = $elem['flags'];
        }
        /**
         *
         */
        // Recuperation de l'enregistrement resultat de la requete
        while ($row =& $res->fetchRow()) {
            // Initialisation de la cle a 0
            $i = 0;
            // Si on se trouve en mode ajout
            if ($id == "]") {
                // On initialise la valeur de chaque champ avec une chaîne vide
                foreach ($row as $elem) {
                    $this->val[$i++] = "";
                }
            } else {
                // Recuperation de la valeur de chaque champ dans l'attribut 'val'
                foreach ($row as $elem) {
                    $this->val[$i++] = $elem;
                }
            }
        }
    }

    /**
     * Retourne le libellé par défaut d'un enregistrement.
     *
     * Principalement utilisé pour le titre de la page. C'est le dernier élément du 
     * titre : Rubrique > Catégorie > Libellé par défaut.
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire);
    }

    /**
     * Clause select pour la requête de sélection des données de l'enregistrement.
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            $this->clePrimaire,
        );
    }

    /**
     * Clause from pour la requête de sélection des données de l'enregistrement.
     *
     * @return string
     */
    function get_var_sql_forminc__tableSelect() {
        return sprintf(
            '%1$s%2$s',
            DB_PREFIXE,
            $this->table
        );
    }

    /**
     * Clause where pour la requête de sélection des données de l'enregistrement.
     *
     * @return string
     */
    function get_var_sql_forminc__selection() {
        return "";
    }

    /**
     * Accesseur pour récuperer les variables $sql_* depuis la configuration form.inc.
     *
     * Deux niveaux de récupérations :
     * - soit la variable $sql_<NOM_DE_LA_VARIABLE> est définie dans le script
     *   form.inc et a été affectée à un atribut de la classe nommé
     *   "_var_from_sql_forminc__sql_<NOM_DE_LA_VARIABLE>" par la méthode
     *   ``dbform::load_var_from_sql_forminc()`` et c'est elle qui est renvoyée
     * - soit la méthode ``dbform::get_var_sql_forminc__sql_<NOM_DE_LA_VARIABLE>()``
     *   est définie sur la classe et c'estson retour qui est renvoyée
     *
     * @param string $sql Nom de la variable à récupérer dans le préfixe 'sql_'.
     *
     * @return string Valeur de la variable récupérée ou chaîne vide
     */
    function get_var_sql_forminc__sql($sql = "") {
        $var_name = "_var_from_sql_forminc__sql_".$sql;
        $method_name = "get_var_sql_forminc__sql_".$sql;
        if (isset($this->$var_name) === true) {
            return $this->$var_name;
        } elseif (method_exists($this, $method_name) === true) {
            return $this->$method_name();
        }
        return "";
    }

    /**
     * Cette méthode permet de renvoyer le nom de l'objet métier absolu.
     *
     * C'est-à-dire que la fonctionnalité custom et les cascades obj/core/gen
     * et les surcharges permettent d'avoir noms de classe comme `<OBJ>_custom`
     * ou `<OBJ>_core` ou `<OBJ>_gen` ou '<OBJ>_archive', nous avons besoin de
     * quel objet métier est réellement pour gérer les permissions, les clés
     * étrangères, ...
     *
     * @return string
     */
    function get_absolute_class_name() {
        if (property_exists(get_class($this), '_absolute_class_name') === true
            && $this->_absolute_class_name !== null) {
            return $this->_absolute_class_name;
        }
        return get_class($this);
    }

    /**
     * TREATMENT - ajouter.
     *
     * Cette méthode permet d'exécuter l'ajout (mode CREATE) de l'objet dans
     * la base de données.
     *
     * @param array $val Tableau des valeurs brutes.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function ajouter($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Begin
        $this->begin_treatment(__METHOD__);
        // Mutateur de valF
        $this->setValF($val);
        // Mutateur de valF specifique a l'ajout
        $this->setValFAjout($val);
        // Verification de la validite des donnees
        $this->verifier($val, $this->f->db, null);
        // Verification specifique au MODE 'insert' de la validite des donnees
        $this->verifierAjout($val, $this->f->db);
        // Si les verifications precedentes sont correctes, on procede a
        // l'ajout, sinon on ne fait rien et on affiche un message d'echec
        if ($this->correct) {
            // Appel au mutateur pour le calcul de la cle primaire (si la cle
            // est automatique) specifique au MODE 'insert'
            $this->setId($this->f->db);
            // Execution du trigger 'before' specifique au MODE 'insert'
            // Le premier parametre est vide car en MODE 'insert'
            // l'enregistrement n'existe pas encore donc il n'a pas
            // d'identifiant
            if($this->triggerajouter("", $this->f->db, $val, null) === false) {
                $this->correct = false;
                $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                // Return
                return $this->end_treatment(__METHOD__, false);
            }
            //Traitement des fichiers uploadé
            $retTraitementFichier = $this->traitementFichierUploadAjoutModification();
            if($retTraitementFichier !== true) {
                $this->correct = false;
                $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                // Return
                return $this->end_treatment(__METHOD__, false);
            }
            // Execution de la requete d'insertion des donnees de l'attribut
            // valF de l'objet dans l'attribut table de l'objet
            $res = $this->f->db->autoExecute(DB_PREFIXE.$this->table, $this->valF, DB_AUTOQUERY_INSERT);
            // Logger
            $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table."\", ".print_r($this->valF, true).", DB_AUTOQUERY_INSERT);", VERBOSE_MODE);
            // Si une erreur survient
            if ($this->f->isDatabaseError($res, true)) {
                // Appel de la methode de recuperation des erreurs
                $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                $this->correct = false;
                // Return
                return $this->end_treatment(__METHOD__, false);
            } else {
                //
                $main_res_affected_rows = $this->f->db->affectedRows();
                // Log
                $this->addToLog(__METHOD__."(): ".__("Requete executee"), VERBOSE_MODE);
                // Execution du trigger 'after' specifique au MODE 'insert'
                // Le premier parametre est vide car en MODE 'insert'
                // l'enregistrement n'existe pas encore donc il n'a pas
                // d'identifiant
                if($this->triggerajouterapres($this->valF[$this->clePrimaire], $this->f->db, $val, null) === false) {
                    $this->correct = false;
                    $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                    // Return
                    return $this->end_treatment(__METHOD__, false);
                }
                $message = __("Enregistrement")."&nbsp;".$this->valF[$this->clePrimaire]."&nbsp;";
                $message .= __("de la table")."&nbsp;\"".$this->table."\"&nbsp;";
                $message .= "[&nbsp;".$main_res_affected_rows."&nbsp;";
                $message .= __("enregistrement(s) ajoute(s)")."&nbsp;]";
                $this->addToLog(__METHOD__."(): ".$message, VERBOSE_MODE);
                // Message de validation
                $this->addToMessage(__("Vos modifications ont bien ete enregistrees.")."<br/>");
            }
        } else {
            // Message d'echec (saut d'une ligne supplementaire avant le
            // message pour qu'il soit mis en evidence)
            $this->addToMessage("<br/>".__("SAISIE NON ENREGISTREE")."<br/>");
            // Return
            return $this->end_treatment(__METHOD__, false);
        }
        // Return
        return $this->end_treatment(__METHOD__, true);
    }

    /**
     * Mutateur pour la propriéré 'valF' en mode CREATE.
     *
     * initialisation valF pour la cle primaire (si pas de cle automatique)
     * [value primary key to database - not automatic primary key]
     *
     * @param array $val Tableau des valeurs brutes.
     *
     * @return void
     */
    function setValFAjout($val = array()) {
        $this->valF[$this->clePrimaire] = trim($val[$this->clePrimaire]);
    }

    /**
     * Mutateur pour la propriété 'valF' en mode CREATE.
     *
     * initialisation valF pour la cle primaire (si  cle automatique)
     * [value primary key to database - automatic primary key]
     * id automatique method nextid
     * automatic id with dbpear method nextid
     *
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function setId(&$dnu1 = null) {
    }

    /**
     * Vérifie la validité des valeurs en mode CREATE.
     *
     * @param array $val Tableau des valeurs brutes.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function verifierAjout($val = array(), &$dnu1 = null) {
        // Verifier [verify]
        // la cle primaire est obligatoire
        // [primary key is compulsory]
        if ($this->valF[$this->clePrimaire] == "") {
            //
            $this->correct = false;
            //
            $this->addToMessage("<br/>");
            $this->addToMessage( __("L'\"identifiant\" est obligatoire")."&nbsp;");
            $this->addToMessage("[&nbsp;".__($this->clePrimaire)."&nbsp;]");
        }
        if ($this->typeCle == "A") {
            $sql = "select count(*) from ".DB_PREFIXE.$this->table." ";
            $sql .= "where ".$this->clePrimaire."='".$this->valF[$this->clePrimaire]."' ";
            // Exécution de la requête
            $nb = $this->f->db->getone($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($nb);
            //
            if ($nb > 0) {
                $this->correct = false;
                $this->addToMessage($nb." ");
                $this->addToMessage( __("cle primaire existante"));
                $this->addToMessage(" ".$this->table."<br />");
            }
        }
    }

    /**
     * TREATMENT - modifier.
     *
     * Cette méthode permet d'exécuter la modification (mode UPDATE) de
     * l'objet dans la base de données.
     *
     * @param array $val Tableau des valeurs brutes.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function modifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Begin
        $this->begin_treatment(__METHOD__);
        // Recuperation de la valeur de la cle primaire de l'objet
        if(isset($val[$this->clePrimaire])) {// ***
            $id = $val[$this->clePrimaire];
        } elseif(isset($this->valF[$this->clePrimaire])) {// ***
            $id = $this->valF[$this->clePrimaire];
        } else {
            $id=$this->id;
        }
        // Appel au mutateur de l'attribut valF de l'objet
        $this->setValF($val);
        // Verification de la validite des donnees
        $this->verifier($val, $this->f->db, null);
        // Si les verifications precedentes sont correctes, on procede a
        // la modification, sinon on ne fait rien et on affiche un message
        // d'echec
        if ($this->correct) {
            // Execution du trigger 'before' specifique au MODE 'update'
            if($this->triggermodifier($id, $this->f->db, $val, null) === false) {
                $this->correct = false;
                $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                // Return
                return $this->end_treatment(__METHOD__, false);
            }
            //Traitement des fichiers uploadé
            $retTraitementFichier = $this->traitementFichierUploadAjoutModification();
            if($retTraitementFichier !== true) {
                $this->correct = false;
                $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                // Return
                return $this->end_treatment(__METHOD__, false);
            }
            // Execution de la requête de modification des donnees de l'attribut
            // valF de l'objet dans l'attribut table de l'objet
            $res = $this->f->db->autoExecute(DB_PREFIXE.$this->table, $this->valF, DB_AUTOQUERY_UPDATE, $this->getCle($id));
            // Logger
            $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table."\", ".print_r($this->valF, true).", DB_AUTOQUERY_UPDATE, \"".$this->getCle($id)."\")", VERBOSE_MODE);
            // Si une erreur survient
            if ($this->f->isDatabaseError($res, true)) {
                // Appel de la methode de recuperation des erreurs
                $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                $this->correct = false;
                // Return
                return $this->end_treatment(__METHOD__, false);
            } else {
                //
                $main_res_affected_rows = $this->f->db->affectedRows();
                // Execution du trigger 'after' specifique au MODE 'update'
                if($this->triggermodifierapres($id, $this->f->db, $val, null) === false) {
                    $this->correct = false;
                    $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                    // Return
                    return $this->end_treatment(__METHOD__, false);
                }
                $retTraitementFichier = $this->traitementFichierUploadSuppression();
                if($retTraitementFichier !== true) {
                    $this->correct = false;
                    $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                    // Return
                    return $this->end_treatment(__METHOD__, false);
                }
                // Log
                $this->addToLog(__METHOD__."(): ".__("Requete executee"), VERBOSE_MODE);

                // Log
                $message = __("Enregistrement")."&nbsp;".$id."&nbsp;";
                $message .= __("de la table")."&nbsp;\"".$this->table."\"&nbsp;";
                $message .= "[&nbsp;".$main_res_affected_rows."&nbsp;";
                $message .= __("enregistrement(s) mis a jour")."&nbsp;]";
                $this->addToLog(__METHOD__."(): ".$message, VERBOSE_MODE);
                // Message de validation
                if ($main_res_affected_rows == 0) {
                    $this->addToMessage(__("Attention vous n'avez fait aucune modification.")."<br/>");
                } else {
                    $this->addToMessage(__("Vos modifications ont bien ete enregistrees.")."<br/>");
                }
            }
        } else {
            // Message d'echec (saut d'une ligne supplementaire avant le
            // message pour qu'il soit mis en evidence)
            $this->addToMessage("<br/>".__("SAISIE NON ENREGISTREE")."<br/>");
            // Return
            return $this->end_treatment(__METHOD__, false);
        }
        // Return
        return $this->end_treatment(__METHOD__, true);
    }

    /**
     * TREATMENT - supprimer.
     *
     * Cette méthode permet d'exécuter le traitement de suppression
     * (mode DELETE) de l'objet dans la base de données.
     *
     * @param array $val Tableau des valeurs brutes.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function supprimer($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Begin
        $this->begin_treatment(__METHOD__);
        // Recuperation de la valeur de la cle primaire de l'objet
        if(isset($val[$this->clePrimaire])) {// ***
            $id = $val[$this->clePrimaire];
        } elseif(isset($this->valF[$this->clePrimaire])) {// ***
            $id = $this->valF[$this->clePrimaire];
        } else {
            $id=$this->id;
        }
        // Verification des contraintes d'integrite specifique au MODE 'delete'
        $this->cleSecondaire($id, $this->f->db, $val, null);
        // Si les verifications precedentes sont correctes, on procede a
        // la suppression, sinon on ne fait rien et on affiche un message
        // d'echec
        if ($this->correct) {
            // Execution du trigger 'before' specifique au MODE 'delete'
            if($this->triggersupprimer($id, $this->f->db, $val, null) === false) {
                $this->correct = false;
                $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                // Return
                return $this->end_treatment(__METHOD__, false);
            }
            // Construction de la requete de suppression de l'objet dans
            // l'attribut table de l'objet
            $sql = "delete from ".DB_PREFIXE.$this->table." where ".$this->getCle($id);
            // Execution de la requete de suppression de l'objet
            $res = $this->f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Si une erreur survient
            if ($this->f->isDatabaseError($res, true)) {
                // Appel de la methode de recuperation des erreurs
                $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                $this->correct = false;
                // Return
                return $this->end_treatment(__METHOD__, false);
            } else {
                //
                $main_res_affected_rows = $this->f->db->affectedRows();
                // Execution du trigger 'after' specifique au MODE 'delete'
                if($this->triggersupprimerapres($id, $this->f->db, $val, null) === false) {
                    $this->correct = false;
                    $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                    // Return
                    return $this->end_treatment(__METHOD__, false);
                }
                //Traitement des fichiers uploadé
                $retTraitementFichier = $this->traitementFichierUploadSuppression();
                if($retTraitementFichier !== true) {
                    $this->correct = false;
                    $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                    // Return
                    return $this->end_treatment(__METHOD__, false);
                }
                // Log
                $message = __("Enregistrement")."&nbsp;".$id."&nbsp;";
                $message .= __("de la table")."&nbsp;\"".$this->table."\"&nbsp;";
                $message .= "[&nbsp;".$main_res_affected_rows."&nbsp;";
                $message .= __("enregistrement(s) supprime(s)")."&nbsp;]";
                $this->addToLog(__METHOD__."(): ".$message, VERBOSE_MODE);
                // Message de validation
                $this->addToMessage(__("La suppression a ete correctement effectuee.")."<br/>");
            }
        } else {
            // Message d'echec (saut d'une ligne supplementaire avant le
            // message pour qu'il soit mis en evidence)
            $this->addToMessage("<br/>".__("SUPPRESSION NON EFFECTUEE")."<br/>");
            // Return
            return $this->end_treatment(__METHOD__, false);
        }
        // Return
        return $this->end_treatment(__METHOD__, true);
    }

    /**
     * Vérifie la validité des valeurs en mode CREATE & UPDATE.
     *
     * @param array $val Tableau des valeurs brutes.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Vérification des champs requis
        $this->checkRequired();
        // Si aucune erreur constatée, alors vérification des clés uniques
        if ($this->correct == true) {
            //
            $this->checkUniqueKey();
        }
    }

    /**
     * Cette methode est appelee lors de la suppression d'un objet, elle permet
     * d'effectuer des tests pour verifier si l'objet supprime n'est pas cle
     * secondaire dans une autre table pour en empecher la suppression.
     *
     * @param string $id Identifiant (cle primaire) de l'objet dans la base
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $val Tableau associatif representant les valeurs du
     *                   formulaire
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function cleSecondaire($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
    }

    /**
     * Cette méthode permet d'exécuter une routine en début des méthodes dites
     * de TREATMENT.
     *
     * @param string $method_name Nom de la méthode appelante.
     * @param array  $extras      Paramètres supplémentaires.
     *
     * @return void
     */
    function begin_treatment($method_name, $extras = array()) {
        // Logger
        $this->addToLog($method_name, EXTRA_VERBOSE_MODE);
        // Initialisation du marqueur de bon déroulement de la méthode de traitement
        $this->correct = true;
    }

    /**
     * Cette méthode permet de logger les informations de retour depuis les
     * méthodes dites de TREATMENT.
     *
     * @param string  $method_name Nom de la méthode appelante.
     * @param boolean $ret         Valeur de retour.
     * @param array   $extras      Paramètres supplémentaires.
     *
     * @return mixed
     */
    function end_treatment($method_name, $ret, $extras = array()) {
        // Logger
        $this->addToLog(
            $method_name."(): return \"(".gettype($ret).")".var_export($ret, true)."\";",
            EXTRA_VERBOSE_MODE
        );
        //
        return $ret;
    }


    /**
     * Methode de verification de l'unicite d'une valeur pour chaque elements du tableau unique_key,
     * ainsi que l'unicite de la cle multiple unique_multiple_key.
     */
    function checkUniqueKey() {
        $unique=true;
        //Verification des cles uniques
        if(!empty($this->unique_key)) {
            foreach ($this->unique_key as $constraint) {
                if(!is_array($constraint)) {
                    if(!is_null ($this->valF[$constraint])) {
                        if(!$this->isUnique($constraint,$this->valF[$constraint])) {
                            $this->addToMessage( __("La valeur saisie dans le champ")." <span class=\"bold\">".$this->getLibFromField($constraint)."</span> ".__("existe deja, veuillez saisir une nouvelle valeur."));
                            $unique=false;
                        }
                    }
                } else {
                    //Verification du groupe de champs uniques
                    $oneIsNull=false;
                    if(!empty($constraint)) {
                        $valueMultiple=array();
                        foreach($constraint as $field) {
                            $valueMultiple[]=$this->valF[$field];
                            if(is_null($this->valF[$field])) {
                                $oneIsNull=true;
                            }
                        }
                        if(!$oneIsNull) {
                            if(!$this->isUnique($constraint,$valueMultiple)) {
                                foreach($constraint as $field) {
                                    $temp[]=$this->getLibFromField($field);
                                }
                                $this->addToMessage( __("Les valeurs saisies dans les champs")." <span class=\"bold\">".implode("</span>, <span class=\"bold\">",$temp)."</span> ".__("existent deja, veuillez saisir de nouvelles valeurs."));
                                $unique=false;
                            }
                        }
                    }
                }
            }
        }
        if(!$unique) {
            $this->correct = false;
        }
    }

    /**
     * Methode permettant de requeter la base afin de definir la validite du champ unique
     *
     * @param string $champ nom du champ unique
     * @param string $value valeur à inserer dans la colonne
     */
    function isUnique($champ, $value) {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud();
        //Test sur un groupe de champs
        if(is_array($champ) and is_array($value)) {
            $sql = 'SELECT count(*) FROM '.DB_PREFIXE.$this->table." WHERE ".implode(" = ? AND ",$champ)." = ?";
        } else {
        //Test sur un champ
            $sql = 'SELECT count(*) FROM '.DB_PREFIXE.$this->table." WHERE ".$champ." = ?";
        }
        // Si mode different d'ajout
        if(($crud !== null AND $crud !== 'create')
            OR ($crud === null AND $this->getParameter('maj'))) {
            // Filtre sur clé primaire
            $sql .= " AND ".$this->clePrimaire." \!= ".$this->valF[$this->clePrimaire];
        }
        // Exécution de la requête
        $nb = $this->f->db->getone($sql, $value);
        // Logger
        $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($nb);
        //Si superieur a 0, pas unique
        if ($nb > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Methode de verification des contraintes not null,
     * affiche une erreur si nul.
     */
    function checkRequired() {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud();
        // Pour champ défini comme requis
        foreach($this->required_field as $field) {
            //Ne test la cle primaire car n'a pas de valeur a l'ajout

            // la cle primaire est automatiquement cree
            if ($field == $this->clePrimaire) {
                continue;
            }

            $error = false;

            /* En ajout - verification des requis

               Fonctionnement formel de la condition:

                SI le champ n'existe pas (est 'unset')
                OU le champ est vide

                ALORS le formulaire n'est pas correct

                SINON le formulaire est correct

              Explication:

                Les champs verifies sont les champs requis. S'ils n'existent
                pas en ajout ou qu'ils sont vide, un message apparait a l'ecran
                avertissant l'utilisateur que certains champs doivent etre
                remplis.

            */
            if (($crud === 'create'
                OR ($crud === null AND $this->getParameter('maj') == 0))
                && (!isset($this->valF[$field]) || $this->valF[$field] === '')) {

                $error = true;
                $this->correct = false;

            /* En modification - verification des requis

               Fonctionnement formel de la condition:

                SI le champ existe (est 'set')
                ET le champ est vide

                ALORS le formulaire n'est pas correct

                SINON le formulaire est correct

              Explication:

                Les champs verifies sont les champs requis. S'ils existent
                et qu'ils sont vides alors un message apparait a l'ecran
                avertissant l'utilisateur que certains champs doivent etre
                remplis. Si ces champs sont tous saisis, le formulaire est
                correctement soumis. Par contre, si l'un des champs requis
                n'existe pas au moment de verification (il aurait ete 'unset'),
                il ne sera pas verifie, n'entrainera pas de formulaire incorrect
                et ne sera pas insere dans la base de donnees.

                Faire un 'unset' permet de ne pas mettre a jour certaines
                donnees sensibles en base a chaque soumission de formulaire.

                Faire un 'unset' permet egalement d'outre passer cette condition
                en mode de modification. On suppose qu'a l'ajout une valeur
                a ete inseree dans un champ, et qu'il n'est plus necessaire
                de verifier si ce champ est vide puisque sa valeur ne sera
                pas modifiee en base. Elle sera donc conservee.

            */
            } elseif (($crud === 'update'
                      OR ($crud === null AND $this->getParameter('maj') == 1))
                      && isset($this->valF[$field])
                      && $this->valF[$field] === '') {

                $error = true;
                $this->correct = false;
            }

            // ajout du message d'erreur
            if ($error == true) {
                $this->addToMessage( __('Le champ').' <span class="bold">'.$this->getLibFromField($field).'</span> '.__('est obligatoire'));
            }
        }
    }

    /**
     * Méthode permettant de retourner le nom d'un champ que le formulaire
     * soit instancié ou non
     * @param  string $champ nom du champ
     * @return string libellé
     */
    function getLibFromField($champ) {
        if (isset($this->form->lib[$champ]) and $this->form->lib[$champ] != "") {
            return $this->form->lib[$champ];
        } else {
            return __($champ);
        }
    }

    /**
     * Mutateur pour la propriété 'valF' en mode CREATE & UPDATE.
     *
     * @param array $val Tableau des valeurs brutes.
     *
     * @return void
     */
    function setvalF($val = array()) {
        // recuperation automatique [automatic recovery]
        foreach (array_keys($val) as $elem) {
            $this->valF[$elem] = $val[$elem];
        }
    }

    /**
     * TRIGGER - triggerajouter.
     *
     * @param string $id
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $val Tableau des valeurs brutes.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function triggerajouter($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
    }

    /**
     * TRIGGER - triggermodifier.
     *
     * @param string $id
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $val Tableau des valeurs brutes.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function triggermodifier($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
    }

    /**
     * TRIGGER - triggersupprimer.
     *
     * @param string $id
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $val Tableau des valeurs brutes.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     */
    function triggersupprimer($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
    }

    /**
     * TRIGGER - triggerajouterapres.
     *
     * @param string $id
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $val Tableau des valeurs brutes.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function triggerajouterapres($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
    }

    /**
     * TRIGGER - triggermodifierapres.
     *
     * @param string $id
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $val Tableau des valeurs brutes.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function triggermodifierapres($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
    }

    /**
     * TRIGGER - triggersupprimerapres.
     *
     * @param string $id
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $val Tableau des valeurs brutes.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return boolean
     */
    function triggersupprimerapres($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
    }

    // {{{ Gestion des parametres

    /**
     * Mutateur pour la propriété 'parameters'.
     *
     * @param array $parameters
     *
     * @return void
     */
    function setParameters($parameters = array()) {
        //
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Mutateur pour la propriété 'parameters'.
     *
     * @param string $parameter
     * @param string $value
     *
     * @return void
     */
    function setParameter($parameter = "", $value = "") {
        //
        $this->parameters[$parameter] = $value;
    }

    /**
     * Accesseur pour la propriété 'parameters'.
     *
     * @param string $parameter
     *
     * @return null|string
     */
    function getParameter($parameter = "") {
        //
        if (isset($this->parameters[$parameter])) {
            return $this->parameters[$parameter];
        } else {
            return null;
        }
    }

    // }}}

    /**
     * Permet de récupérer la valeur d'un paramètre ou de sa surcharge.
     *
     * @param string $parameter Clé du paramètre.
     * @param mixed  $override  Tableau de paramètre permettant de surcharger
     *                          certaines valeurs récupérées de manière standard
     *                          si ce n'est pas le cas.
     *
     * @return mixed
     */
    function get_parameter_or_override($parameter = "", $override = array()) {
        //
        if (array_key_exists($parameter, $override)) {
            return $override[$parameter];
        } else {
            return $this->getParameter($parameter);
        }
    }

    /**
     * Permet de composer l'url vers les script 'formulaire' standards.
     *
     * @param string $case     Mode dans lequel l'url doit être construite.
     * @param mixed  $override Tableau de paramètre permettant de surcharger
     *                         certaines valeurs récupérées de manière standard
     *                         si ce n'est pas le cas.
     *
     * @return string
     */
    function compose_form_url($case = "form", $override = array()) {
        //
        $out = "";
        //
        if ($case == "form") {

            //
            $out = "";
            $out .= OM_ROUTE_FORM;
            $out .= "&";
            $out .= "obj=".$this->get_absolute_class_name();
            //
            $validation = $this->get_parameter_or_override("validation", $override);
            $out .= ($validation != null ? "&amp;validation=".$validation : "");
            //
            $idx = $this->get_parameter_or_override("idx", $override);
            if ($idx != "]") {
                //
                $out .= ($idx != null ? "&amp;idx=".$idx : "");
            }
            //
            $maj = $this->get_parameter_or_override("maj", $override);
            $out .= ($maj != null ? "&amp;action=".$maj : "");
            //
            $premier = $this->get_parameter_or_override("premier", $override);
            $out .= ($premier != null ? "&amp;premier=".$premier : "");
            //
            $tricol = $this->get_parameter_or_override("tricol", $override);
            $out .= ($tricol != null ? "&amp;tricol=".$tricol : "");
            //
            $advs_id = $this->get_parameter_or_override("advs_id", $override);
            $out .= ($advs_id != null ? "&amp;advs_id=".$advs_id : "");
            //
            $valide = $this->get_parameter_or_override("valide", $override);
            $out .= ($valide != null ? "&amp;valide=".$valide : "");
            //
            $retour = $this->get_parameter_or_override("retour", $override);
            $out .= ($retour != null ? "&amp;retour=".$retour : "");

        } elseif ($case == "sousform") {

            //
            $out = "";
            $out .= OM_ROUTE_SOUSFORM;
            $out .= "&";
            $out .= "obj=".$this->get_absolute_class_name();
            //
            $validation = $this->get_parameter_or_override("validation", $override);
            $out .= ($validation != null ? "&amp;validation=".$validation : "");
            //
            //
            $idx = $this->get_parameter_or_override("idx", $override);
            if ($idx != "]") {
                //
                $out .= ($idx != null ? "&amp;idx=".$idx : "");
            }
            //
            $maj = $this->get_parameter_or_override("maj", $override);
            $out .= ($maj != null ? "&amp;action=".$maj : "");
            //
            $premiersf = $this->get_parameter_or_override("premiersf", $override);
            $out .= ($premiersf != null ? "&amp;premiersf=".$premiersf : "");
            //
            $advs_id = $this->get_parameter_or_override("advs_id", $override);
            $out .= ($advs_id != null ? "&amp;advs_id=".$advs_id : "");
            //
            $trisf = $this->get_parameter_or_override("trisf", $override);
            $out .= ($trisf != null ? "&amp;trisf=".$trisf : "");
            //
            $valide = $this->get_parameter_or_override("valide", $override);
            $out .= ($valide != null ? "&amp;valide=".$valide : "");

            //
            $retourformulaire = $this->get_parameter_or_override("retourformulaire", $override);
            $out .= ($retourformulaire != null ? "&amp;retourformulaire=".$retourformulaire : "");
            //
            $idxformulaire = $this->get_parameter_or_override("idxformulaire", $override);
            $out .= ($idxformulaire != null ? "&amp;idxformulaire=".$idxformulaire : "");

            //
            $retour = $this->get_parameter_or_override("retour", $override);
            $out .= ($retour != null ? "&amp;retour=".$retour : "");

        }
        //
        return $out;
    }


    /**
     * Methode permettant aux objets metiers de surcharger facilement
     * la methode formulaire et de passer facilement des variables
     * supplementaires en parametre. Cette methode retourne une chaine
     * representant l'attribut action du formulaire.
     *
     * @return string Attribut action du form
     */
    function getDataSubmit() {
        //
        if ($this->exists() === true) {
            return $this->compose_form_url(
                "form",
                array(
                    "idx" => $this->getVal($this->clePrimaire),
                )
            );
        }
        return $this->compose_form_url("form");
    }

    /**
     * Methode permettant aux objets metiers de surcharger facilement
     * la methode sousformulaire et de passer facilement des variables
     * supplementaires en parametre. Cette methode retourne une chaine
     * representant l'attribut action du formulaire.
     *
     * @return string Attribut action du form
     */
    function getDataSubmitSousForm() {
        //
        if ($this->exists() === true) {
            return $this->compose_form_url(
                "sousform",
                array(
                    "idx" => $this->getVal($this->clePrimaire),
                )
            );
        }
        return $this->compose_form_url("sousform");
    }

    /**
     * Méthode permettant de calculer les métadonnées autres que celle définies
     * lors de l'upload
     *
     * @param string $champ champ sur lequel on récupère les métadonnées
     * @return array tableau contenant les métadonnées
     */
    function getMetadata($champ) {
        // Initialisation du tableau de retour
        $tab_retour = array();
        // Définition des métadonnées globales
        if(isset($this->metadata_global) AND !empty($this->metadata_global)) {
            // Pour chaque clé on récupère la valeur avec la méthode associée
            foreach ($this->metadata_global as $key => $methode) {
                if(method_exists($this, $methode)) {
                    $tab_retour[$key] = $this->$methode();
                }
            }
        }

        // Définition des métadonnées spécifiques à chaque champ
        if(isset($this->metadata[$champ]) AND !empty($this->metadata[$champ])) {
            // Pour chaque clé on récupère la valeur avec la méthode associée
            foreach ($this->metadata[$champ] as $key => $methode) {
                if(method_exists($this, $methode)) {
                    $tab_retour[$key] = $this->$methode();
                }
            }
        }
        return $tab_retour;
    }

    /**
     * Méthode de traitement de fichier uploadé : récupération du fichier temporaire,
     * pour l'ajout et la modification, la suppression se fait dans un 2nd temps.
     *
     * @return string/boolean retourne true ou un message d'erreur
     */
    function traitementFichierUploadAjoutModification() {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud();

        $type_list = array();
        // Récupération du tableau abstract_type si il existe sinon on utilise
        // les type de champs définis dans le formulaire
        if (isset($this->abstract_type)) {
            $type_list = $this->abstract_type;
        } elseif (isset($this->form->type)) {
            $type_list = $this->form->type;
        }
        // Pour chaque champs configurés avec les widgets upload, upload2 ou filestatic
        // ou chaque champs de type abstrait file défini dans le tableau abstract_type
        foreach ($type_list as $champ => $type) {
            //
            if ($type == "upload" OR $type == "upload2" OR $type == "filestatic"
                OR (isset($this->abstract_type) AND $type == "file")) {

                // Message d'erreur
                $msg = "";

                // Cas d'un ajout de fichier
                // Condition : si la valeur existante en base est vide ou que
                // nous sommes en mode 'AJOUT' ET qu'une valeur est postée pour
                // le champ fichier
                if (($this->getVal($champ) == ""
                     OR ($crud === 'create'
                      OR ($crud === null AND $this->getParameter('maj') == 0)))
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != "") {

                    // Si la valeur du champ contient le marqueur 'temporary'
                    $temporary_test = explode("|", $this->valF[$champ]);
                    //
                    if (isset($temporary_test[0]) && $temporary_test[0] == "tmp") {
                        //
                        if (!isset($temporary_test[1])) {
                            //
                            $msg = __("Erreur lors de la creation du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg, DEBUG_MODE);
                            return $msg.__("Veuillez contacter votre administrateur.");
                        }
                        // Récupération des métadonnées calculées après validation
                        $metadata = $this->getMetadata($champ);
                        //
                        $this->valF[$champ] = $this->f->storage->create($temporary_test[1], $metadata, "from_temporary");
                        // Si le fichier est vérouillé
                        if ($this->valF[$champ] === false) {
                            //
                            $msg =  __("Le fichier sur le champ")." ".$this->table.".".$champ." ".
                            __("est verouille. ");
                            $this->addToLog(__METHOD__."(): ".$msg, DEBUG_MODE);
                            return $msg.__("Veuillez revalider le formulaire");
                        }
                        // Gestion du retour d'erreur
                        if ($this->valF[$champ] == OP_FAILURE) {
                            //
                            $msg = __("Erreur lors de la creation du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg, DEBUG_MODE);
                            return  $msg.__("Veuillez contacter votre administrateur.");
                        }
                    }
                }

                // Cas d'une modification de fichier
                // Condition : si nous ne sommes pas en mode 'AJOUT' ET si la
                // valeur existante en base n'est pas vide ET qu'une valeur est
                // postée pour le champ fichier ET que la valeur postée est
                // différente de la valeur présente en base
                if ((($crud !== null AND $crud !== 'create')
                      OR ($crud === null AND $this->getParameter('maj') != 0))
                    AND $this->getVal($champ) != ""
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != ""
                    AND $this->getVal($champ) != $this->valF[$champ]) {

                    // Si la valeur du champ contient le marqueur 'temporary'
                    $temporary_test = explode("|", $this->valF[$champ]);
                    //
                    if (isset($temporary_test[0]) && $temporary_test[0] == "tmp") {
                        //
                        if (!isset($temporary_test[1])) {
                            //
                            $msg = __("Erreur lors de la mise a jour du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg.__("id")." = ".$this->valF[$this->clePrimaire]." - ".__("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                            return $msg.__("Veuillez contacter votre administrateur.");
                        }

                        // Sauvegarde de l'ancien fichier
                        $this->tmpFile[$champ] = $this->f->storage->get($this->getVal($champ));
                        // Récupération des métadonnées calculées après validation
                        $metadata = $this->getMetadata($champ);
                        //
                        $this->valF[$champ] = $this->f->storage->update($this->getVal($champ), $temporary_test[1], $metadata, "from_temporary");
                        // Si le fichier est vérouillé
                        if ($this->valF[$champ] === false) {
                            //
                            $msg = __("Le fichier sur le champ")." ".$this->table.".".$champ." ".
                            __("est verouille. ");
                            $this->addToLog(__METHOD__."(): ".$msg.__("id")." = ".$this->valF[$this->clePrimaire]." - ".__("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                            return $msg.__("Veuillez revalider le formulaire");
                        }
                        // Gestion du retour d'erreur
                        if ($this->valF[$champ] == OP_FAILURE) {
                            //
                            $msg = __("Erreur lors de la mise a jour du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg.__("id")." = ".$this->valF[$this->clePrimaire]." - ".__("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                            return $msg.__("Veuillez contacter votre administrateur.");
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Méthode de traitement de fichier uploadé : récupération du fichier temporaire,
     * pour la suppression.
     *
     * @return string/boolean retourne true ou un message d'erreur
     */
    function traitementFichierUploadSuppression() {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud();

        // Récupération du tableau abstract_type si il existe sinon on utilise
        // les type de champs définis dans le formulaire
        $type_list = array();
        if (isset($this->abstract_type)) {
            $type_list = $this->abstract_type;
        } elseif (isset($this->form->type)) {
            $type_list = $this->form->type;
        }

        // Pour chaque champ configuré avec les widgets upload, upload2 ou filestatic
        // ou chaque champ de type abstrait file défini dans le tableau abstract_type
        foreach ($type_list as $champ => $type) {
            if ($type == "upload" OR $type == "upload2" OR $type == "filestatic"
                OR (isset($this->abstract_type) AND $type == "file")) {

                // Pas de suppression en ajout
                if ($crud === 'create' OR ($crud === null AND $this->getParameter('maj') == 0)) {
                    continue;
                }
                // Pas de suppression si champ BDD vide
                if ($this->getVal($champ) === '') {
                    continue;
                }

                // Les autres modes que supprimer
                if (($crud !== 'delete' AND $crud !== null) OR ($crud === null AND $this->getParameter('maj') != 2)) {
                    // nécessitent l'existence du champ
                    if (is_array($this->valF) === true
                        AND array_key_exists($champ, $this->valF) === false) {
                        continue;
                    }
                    // mais de valeur vide ou à l'état null
                    if ($this->valF[$champ] !== '' AND $this->valF[$champ] !== null) {
                        continue;
                    }
                }

                // Sauvegarde temporaire du fichier à supprimer pour la gestion transactionnelle (rollback)
                $this->tmpFile[$champ] = $this->f->storage->get($this->getVal($champ));
                // Suppression
                $res_delete = $this->f->storage->delete($this->getVal($champ));
                // Gestion erreur verrou
                if ($res_delete === false) {
                    //
                    $msg = __("Le fichier sur le champ")." ".$this->table.".".$champ." ".
                    __("est verouille. ");
                    $this->addToLog(__METHOD__."(): ".$msg.__("id")." = ".$this->getVal($this->clePrimaire)." - ".__("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                    return $msg.__("Veuillez revalider le formulaire");
                }
                // Gestion erreur filestorage
                if ($res_delete == OP_FAILURE) {
                    //
                    $msg = __("Erreur lors de la suppression du fichier sur le champ").
                    " \"".$this->table.".".$champ."\". ";
                    $this->addToLog(__METHOD__."(): ".$msg.__("id")." = ".$this->getVal($this->clePrimaire)." - ".__("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                    return $msg.__("Veuillez contacter votre administrateur.");
                }
            }
        }
        return true;
    }

    /**
     * Permet d'annuler le traitement effectué sur les fichiers du formulaire
     * si une erreur lors de l'enregistrement survient.
     * @return void
     */
    private function undoFileTransaction() {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud();

        $type_list = array();
        // Récupération du tableau abstract_type si il existe sinon on utilise
        // les type de champs définis dans le formulaire
        if (isset($this->abstract_type)) {
            $type_list = $this->abstract_type;
        } elseif (isset($this->form->type)) {
            $type_list = $this->form->type;
        }
        // Pour chaque champs configurés avec les widgets upload, upload2 ou filestatic
        // ou chaque champs de type abstrait file défini dans le tableau abstract_type
                foreach ($type_list as $champ => $type) {
            //
            if ($type == "upload" OR $type == "upload2" OR $type == "filestatic"
                OR (isset($this->abstract_type) AND $type == "file")) {

                // Cas d'un ajout de fichier
                // Condition : si la valeur existante en base est vide ou que
                // nous sommes en mode 'AJOUT' ET qu'une valeur est postée pour
                // le champ fichier
                if (($this->getVal($champ) == ""
                     OR ($crud === 'create'
                        OR ($crud === null AND $this->getParameter('maj') == 0)))
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != "") {

                    // Vérifie que le fichier à supprimer n'est pas un fichier
                    // temporaire
                    $temporary_test = explode("|", $this->valF[$champ]);
                    //
                    if (isset($temporary_test[0]) === false
                        || (isset($temporary_test[0]) === true && $temporary_test[0] !== "tmp")) {
                        // suppression du fichier ajouté au début du traitement
                        if($this->f->storage->delete($this->valF[$champ]) == OP_FAILURE) {
                            $this->addToMessage(__("L'etat de l'enregistrement n'a pas pu etre réinitialisé"));
                        }
                    }
                }

                // Cas d'une modification de fichier
                // Condition : si nous ne sommes pas en mode 'AJOUT' ET si la
                // valeur existante en base n'est pas vide ET qu'une valeur est
                // postée pour le champ fichier ET que la valeur postée est
                // différente de la valeur présente en base
                if ((($crud !== null AND $crud !== 'create')
                    OR ($crud === null AND $this->getParameter('maj') != 0))
                    AND $this->getVal($champ) != ""
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != ""
                    AND $this->getVal($champ) != $this->valF[$champ]) {

                    // Annulation de la modification des fichiers
                    if(isset($this->tmpFile[$champ])) {
                        if($this->f->storage->update(
                                $this->valF[$champ],
                                $this->tmpFile[$champ]["file_content"],
                                $this->tmpFile[$champ]["metadata"]
                            ) == OP_FAILURE) {
                            $this->addToMessage(__("L'état de l'enregistrement n'a pas pu être réinitialisé"));
                        }
                    }

                }
                // Cas d'une suppression de fichier
                // Condition : si nous sommes en mode 'SUPPRESSION' OU si nous
                // ne sommes pas en mode 'AJOUT' ET si la valeur existante en
                // base n'est pas vide ET qu'une valeur est postée pour le
                // champ fichier ET que cette valeur postée est vide
                if (($crud === 'delete'
                        OR ($crud === null AND $this->getParameter('maj') == 2))
                    OR ((($crud !== null AND $crud !== 'create')
                            OR ($crud === null AND $this->getParameter('maj') != 0))
                        AND $this->getVal($champ) != ""
                        AND isset($this->valF[$champ])
                        AND $this->valF[$champ] == "")) {
                    // Annulation de la suppression des fichiers
                    if(isset($this->tmpFile[$champ])) {
                        if($this->f->storage->update(
                                $this->valF[$champ],
                                $this->tmpFile[$champ]["file_content"],
                                $this->tmpFile[$champ]["metadata"]
                            ) == OP_FAILURE) {
                            $this->addToMessage(__("L'état de l'enregistrement n'a pas pu être réinitialisé"));
                        }
                    }
                }
            }
        }
    }

    /**
     * Permet d'annuler toutes modifications effectuées sur le formulaire
     */
    function undoValidation() {
        $this->correct = false;
        $this->f->db->rollback();
        $this->undoFileTransaction();
        if(!empty($this->errors)) {
            $this->addToMessage(__("Une erreur s'est produite. Contactez votre administrateur."));
        }
    }
    /**
     * VIEW - formulaire.
     *
     * @todo Changer l'attribut name du formulaire pour optimiser la gestion
     * des formulaires
     *
     * @return void
     */
    function formulaire() {
        // Marqueur permettant d'indiquer si le formulaire doit être affiché ou
        // non. Par exemple : si la soumission du formulaire n'est pas valide,
        // on veut afficher un message et un bouton retour et pas le formulaire.
        // ce marqueur nous permet de stocker l'information.
        $flag_do_not_display_form = false;
        // Récupération maj et crud
        $maj = $this->getParameter("maj");
        $crud = $this->get_action_crud($maj);
        // Ouverture de la balise form si pas en consultation
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            $this->f->layout->display__form_container__begin(array(
                "action" => $this->getDataSubmit(),
                "name" => "f1",
            ));
        }
        // Compatibilite anterieure - On decremente la variable validation
        $this->setParameter("validation", $this->getParameter("validation") - 1);
        // Instanciation de l'objet formulaire
        $this->form = $this->f->get_inst__om_formulaire(array(
            "validation" => $this->getParameter("validation"),
            "maj" => $maj,
            "champs" => $this->champs,
            "val" => $this->val,
            "max" => $this->longueurMax,
        ));
        //
        $this->form->setParameter("obj", $this->get_absolute_class_name());
        $this->form->setParameter("idx", $this->getParameter("idx"));
        $this->form->setParameter("maj", $this->getParameter("maj"));
        $this->form->setParameter("form_type", "form");
        // Valorisation des variables formulaires
        $this->setVal(
            $this->form, $maj,
            $this->getParameter("validation"),
            $this->f->db, null
        );
        $this->setType($this->form, $maj);
        $this->setLib($this->form, $maj);
        $this->setTaille($this->form, $maj);
        $this->setMax($this->form, $maj);
        $this->setSelect($this->form, $maj, $this->f->db, null);
        $this->setOnchange($this->form, $maj);
        $this->setOnkeyup($this->form, $maj);
        $this->setOnclick($this->form, $maj);
        $this->setGroupe($this->form, $maj);
        $this->setRegroupe($this->form, $maj);
        $this->setLayout($this->form, $maj);
        $this->setRequired($this->form, $maj);
        $this->set_form_specificity($this->form, $maj);
        //
        $this->form->recupererPostvar(
            $this->champs, $this->getParameter("validation"),
            $this->getParameter("postvar"), null
        );
        // Verification de l'accessibilité sur l'élément
        // Si l'utilisateur n'a pas accès à l'élément dans le contexte actuel
        // on arrête l'exécution du script
        $this->checkAccessibility();
        // Si le formulaire a été validé
        if ($this->getParameter("validation") > 0) {
            // Gestion de la fonction 'soumission multiple impossible'
            // Si la méthode valide la soumission alors on exécute le traitement.
            if ($this->form_resubmit_handle_valid_identifier() === true) {
                if ($this->post_treatment() === true) {
                    $this->redirect_to_back_link("formulaire");
                }
            } else {
                // On positionne le marqueur pour ne pas afficher le formulaire.
                $flag_do_not_display_form = true;
                // On prépare le message d'erreur
                $this->correct = false;
                $this->msg = "";
                $this->addToMessage(__("Opération illégale. Ce formulaire a déjà été soumis, il est impossible de le soumettre une seconde fois."));
            }
        }
        // Affichage du message avant d'afficher le formulaire
        $this->message();
        // Si le marqueur l'indique, on ne veut pas afficher le formulaire donc
        // on sort de la méthode.
        if ($flag_do_not_display_form === true) {
            $this->f->layout->display__form_controls_container__begin(array(
                "controls" => "top",
            ));
            $this->retour(
                $this->getParameter("premier"),
                $this->getParameter("recherche"),
                $this->getParameter("tricol")
            );
            $this->f->layout->display__form_controls_container__end();
            return;
        }
        // Affichage du bouton retour et du bouton
        $this->f->layout->display__form_controls_container__begin(array(
            "controls" => "top",
        ));
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            // Affichage du bouton
            $this->bouton($maj);
        }
        $this->retour(
            $this->getParameter("premier"),
            $this->getParameter("recherche"),
            $this->getParameter("tricol")
        );
        $this->f->layout->display__form_controls_container__end();
        // Ouverture du conteneur de formulaire
        $this->form->entete();
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->form_specific_content_before_portlet_actions($maj);
        // Composition du tableau d'action à afficher dans le portlet
        $this->compose_portlet_actions();
        // Affichage du portlet d'actions s'il existe des actions
        if (!empty($this->user_actions)) {
            $this->form->afficher_portlet(
                $this->getParameter("idx"),
                $this->user_actions
            );
        }
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->form_specific_content_after_portlet_actions($maj);
        // Affichage du contenu du formulaire
        $this->form->afficher(
            $this->champs,
            $this->getParameter("validation"),
            null,
            $this->correct
        );
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->formSpecificContent($maj);
        // Fermeture du conteneur de formulaire
        $this->form->enpied();
        // Affichage du bouton et du bouton retour
        $this->f->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            // Gestion de la fonction 'soumission multiple impossible'
            $this->form_resubmit_handle_new_identifier();
            // Affichage du bouton
            $this->bouton($maj);
        }
        $this->retour(
            $this->getParameter("premier"),
            $this->getParameter("recherche"),
            $this->getParameter("tricol")
        );
        $this->f->layout->display__form_controls_container__end();
        // Fermeture de la balise form
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            $this->f->layout->display__form_container__end();
        }
        // Point d'entrée en dessous du formulaire pour ajout d'éléments spécifiques
        $this->afterFormSpecificContent();
    }

    /**
     * Accesseur pour la récupération des configurations de widget de formulaire.
     *
     * @param string $field
     * @param string $widget
     *
     * @return array|null
     */
    function get_widget_config($field, $widget) {
        $method_name = sprintf(
            "get_widget_config__%s__%s",
            $field,
            $widget
        );
        if (method_exists($this, $method_name) === false) {
            return null;
        }
        return $this->$method_name();
    }

    /**
     * Méthode de comparaison pour réorganisation du tableau des actions.
     * @param array $a Élément à comparer n°1
     * @param array $b Élément à comparer n°2
     *
     * @return integer 1 ou -1 selon si a.order est > ou < à b.order
     */
    function cmp_class_actions($a, $b) {
        // Si order n'est pas défini on ne fait rien
        if(!isset($a["order"]) or !isset($b["order"])) {
            return 0;
        }
        // Si même ordre on test avec le numéro d'action
        if ($a["order"] == $b["order"]) {
            if(!isset($a["action"]) or !isset($b["action"])) {
                return 0;
            } else {
                return ($a["action"] < $b["action"]) ? -1 : 1;
            }
        }
        return ($a["order"] < $b["order"]) ? -1 : 1;
    }

    /**
     * Méthode permettant de vérifier l'existance d'une action de portlet dans
     * une action.
     * @param integer $action Identifant numérique de l'action.
     *
     * @return boolean         true si le portlet est défini
     */
    function is_portlet_action_defined($action) {
        if(isset($this->class_actions[$action]["portlet"]) and
            !empty($this->class_actions[$action]["portlet"])) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Permet de vérifier que l'action est disponible pour l'utilisateur.
     *
     * Vérification des conditions et des permissions.
     *
     * @param integer $action Identifant numérique de l'action.
     *
     * @return boolean
     */
    function is_action_available($action) {
        //
        if ($this->is_action_condition_satisfied($action)
            && $this->is_action_permission_satisfied($action)) {
            //
            return true;
        }
        //
        return false;
    }

    /**
     * Indique si une action est définie.
     *
     * @param integer $action Identifant numérique de l'action.
     *
     * @return boolean
     */
    function is_action_defined($action) {
        if (isset($this->class_actions[$action])
            and !empty($this->class_actions[$action])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Indique si les conditions d'une action sont vérifiées.
     *
     * @param integer $action Identifant numérique de l'action.
     *
     * @return boolean
     */
    function is_action_condition_satisfied($action) {
        //
        $this->addToLog(__METHOD__."(): start - action ".$action, EXTRA_VERBOSE_MODE);
        // On initialise la valeur de retour à 'true' car par défaut si il n'y
        // a pas de condition, l'action est disponible
        $condition_satisfied = true;
        // On récupère le paramètre de condition sur l'action en cours
        $condition_parameter = $this->get_action_param($action, "condition");
        // Il est possible que le paramètre de condition soit au format
        // 'string' (une seule méthode) ou au format 'array' (plusieurs
        // méthodes). Si le format n'est pas 'array' alors on reformate
        // le paramètre.
        if ($condition_parameter == null) {
            $condition_parameter = array();
        } elseif (!is_array($condition_parameter)) {
            $condition_parameter = array($condition_parameter, );
        }
        // On boucle sur la liste des méthodes à vérifier pour que la condition
        // soit satisfaite.
        foreach ($condition_parameter as $condition_method) {
            // Si la méthode existe
            if (method_exists($this, $condition_method)) {
                // Alors on appelle la méthode et on réalise un et logique
                // avec la valeur actuelle de la condition.
                // TRUE  && TRUE  => TRUE
                // TRUE  && FALSE => FALSE
                // FALSE && FALSE => FALSE
                // FALSE && TRUE  => FALSE
                $condition_satisfied = ($condition_satisfied && $this->$condition_method());
            }
        }
        $this->addToLog(__METHOD__."(): return ".var_export($condition_satisfied, true).";", EXTRA_VERBOSE_MODE);
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
        // On retourne la valeur calculée
        return $condition_satisfied;
    }

    /**
     * Permet de vérifier que l'utilisateur a bien la permission d'accéder à l'action.
     *
     * @param integer $action Identifant numérique de l'action.
     *
     * @return boolean
     */
    function is_action_permission_satisfied($action) {
        //
        $this->addToLog(__METHOD__."(): start - action ".$action, EXTRA_VERBOSE_MODE);
        // On initialise la valeur de retour à 'false' car par défaut si il n'y
        // a pas de permission, l'action n'est pas disponible
        $permission_satisfied = false;
        // On récupère le paramètre de permission sur l'action en cours
        $permission_parameter = $this->get_action_param($action, "permission_suffix");
        // On récupère le nom de la classe qui représente l'objet
        $obj = get_called_class();
        // On vérifie que l'utilisateur a bien la permission nécessaire
        $permission_satisfied = $this->f->isAccredited(
            array($obj."_".$permission_parameter, $obj),
            "OR"
        );
        //
        $this->addToLog(__METHOD__."(): return ".var_export($permission_satisfied, true).";", EXTRA_VERBOSE_MODE);
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
        // On retourne la valeur calculée
        return $permission_satisfied;
    }

    /**
     * Méthode permettant de récupérer le tableau complet des actions.
     *
     * @return array
     */
    function get_class_actions() {
        return $this->class_actions;
    }

    /**
     * Permet de renvoyer la clé de l'action à partir de son identifiant texte.
     *
     * L'identifiant texte correspond à l'attribut "identifier" de l'action, il
     * est sensé être unique et doit avoir une signification fonctionnelle en
     * opposition à la clé qui est un entier qui n'a aucune signification
     * fonctionnelle.
     *
     * @param string $identifier Identifiant textuel de l'action.
     *
     * @return integer
     */
    function get_action_key_for_identifier($identifier) {
        //
        foreach ($this->get_class_actions() as $key => $value) {
            //
            if (isset($value["identifier"]) && $value["identifier"] == $identifier) {
                //
                return $key;
            }
        }
        //
        return null;
    }

    /**
     * Définition des actions disponibles sur la classe.
     *
     * @return void
     */
    function init_class_actions() {

        // Initialisation de l'attribut
        $this->class_actions = array();

        // ACTION - 000 - ajouter
        //
        $this->class_actions[0] = array(
            "identifier" => "ajouter",
            "permission_suffix" => "ajouter",
            "crud" => "create",
        );

        // ACTION - 001 - modifier
        //
        $this->class_actions[1] = array(
            "identifier" => "modifier",
            "portlet" => array(
                "type" => "action-self",
                "libelle" => __("modifier"),
                "class" => "edit-16",
                "order" => 10,
                ),
            "permission_suffix" => "modifier",
            "crud" => "update",
            "condition" => array(
                "exists",
            ),
        );

        // ACTION - 002 - supprimer
        //
        $this->class_actions[2] = array(
            "identifier" => "supprimer",
            "portlet" => array(
                "type" => "action-self",
                "libelle"=>__("supprimer"),
                "class" => "delete-16",
                "order"=>20,
                ),
            "permission_suffix" => "supprimer",
            "crud" => "delete",
            "condition" => array(
                "exists",
            ),
        );

        // ACTION - 003 - consulter
        //
        $this->class_actions[3] = array(
            "identifier" => "consulter",
            "permission_suffix" => "consulter",
            "crud" => "read",
            "condition" => array(
                "exists",
            ),
        );

        // ACTION - 999 - rechercher
        //
        $this->class_actions[999] = array(
            "identifier" => "rechercher",
            "permission_suffix" => "tab",
            "crud" => "search",
        );
    }

    /**
     * CONDITION - exists.
     *
     * Est-ce que l'enregistrement instancié existe en base de données ?
     *
     * @return boolean
     */
    function exists() {
        if (!isset($this->val[0]) || $this->val[0] == "") {
            return false;
        }
        return true;
    }

    /**
     * Méthode permettant de récupérer une valeur de l'action passée en paramètre.
     * @param integer $action Identifant numérique de l'action.
     * @param string  $param  paramètre à récupérer
     *
     * @return string         valeur du paramètre
     */
    function get_action_param($action, $param) {
        switch($param) {
            // Représente l'identifiant de l'action soit une chaine de
            // caractères sans espaces ni accents permettant d'identifier
            // l'action (exemple : "modfier" ou "archiver"). Soit 'identifier'
            // est présent dans la configuration et on retourne la valeur soit
            // on renvoi l'identifiant numérique de l'action.
            case "identifier" :
                if (isset($this->class_actions[$action]["identifier"])) {
                    return $this->class_actions[$action]["identifier"];
                } else {
                    return $action;
                }
                break;
            case "method" :
                if(isset($this->class_actions[$action]["method"])) {
                    return $this->class_actions[$action]["method"];
                }
                if(isset($this->class_actions[$action]["crud"])) {
                    switch($this->class_actions[$action]["crud"]) {
                        case 'create':
                            return 'ajouter';
                            break;
                        case 'update':
                            return 'modifier';
                            break;
                        case 'delete':
                            return 'supprimer';
                            break;
                        case 'search':
                            return 'rechercher';
                            break;
                        case 'read':
                        default:
                            return null;
                            break;
                    }
                }
                break;
            case "button" :
                if(isset($this->class_actions[$action]["button"])) {
                    return $this->class_actions[$action]["button"];
                }
                break;
            case "permission_suffix" :
                if(isset($this->class_actions[$action]["permission_suffix"])) {
                    return $this->class_actions[$action]["permission_suffix"];
                }
                break;
            case "condition" :
                if(isset($this->class_actions[$action]["condition"])) {
                    return $this->class_actions[$action]["condition"];
                }
                break;
            case "view" :
                if(isset($this->class_actions[$action]["view"])) {
                    return $this->class_actions[$action]["view"];
                }
                break;
            case "crud" :
                if(isset($this->class_actions[$action]["crud"])) {
                    return $this->class_actions[$action]["crud"];
                }
                break;
            case "portlet" :
                if(isset($this->class_actions[$action]["portlet"])) {
                    return $this->class_actions[$action]["portlet"];
                }
                break;
            case "portlet_libelle" :
                if(isset($this->class_actions[$action]["portlet"]["libelle"])) {
                    return $this->class_actions[$action]["portlet"]["libelle"];
                }
                break;
            case "portlet_description" :
                if(isset($this->class_actions[$action]["portlet"]["description"])) {
                    return $this->class_actions[$action]["portlet"]["description"];
                }
                break;
            case "portlet_type" :
                if(isset($this->class_actions[$action]["portlet"]["type"])) {
                    return $this->class_actions[$action]["portlet"]["type"];
                }
                break;
            case "portlet_class" :
                if(isset($this->class_actions[$action]["portlet"]["class"])) {
                    return $this->class_actions[$action]["portlet"]["class"];
                }
                break;
            case "portlet_order" :
                if(isset($this->class_actions[$action]["portlet"]["order"])) {
                    return $this->class_actions[$action]["portlet"]["order"];
                }
                break;
            case "portlet_url" :
                if(isset($this->class_actions[$action]["portlet"]["url"])) {
                    return $this->class_actions[$action]["portlet"]["url"];
                }
                break;
            default :
                return null;

        }
    }

    /**
     * Retourne le mode de l'action passée en paramètre
     * ou null si aucun n'a été défini.
     *
     * @param   [integer]  $maj  clé de l'action, optionnelle
     * @return  [string]         mode de l'action ou null si aucun spécifié
     */
    function get_action_crud($maj = null) {
        // Si la clé de l'action n'est pas définie on la récupère
        if ($maj == null) {
            $maj = $this->getParameter("maj");
        }
        // Intilisation du crud : état null par défaut
        $crud = null;
        // Récupération de la version de la gestion des actions
        $option_class_action_activated = $this->is_option_class_action_activated();
        // Si nouvelle gestion des actions
        if ($option_class_action_activated === true) {
            // Récupération du mode de l'action
            // Il est possible qu'il ne soit pas défini (null)
            return $this->get_action_param($maj, "crud");
        }
        // Sinon ancienne gestion des actions :
        // Définition du crud selon $maj
        switch ($maj) {
            // Mode ajouter
            case 0:
                return 'create';
                break;
            // Mode modifier
            case 1:
                return 'update';
                break;
            // Mode supprimer
            case 2:
                return 'delete';
                break;
            // Mode consulter
            case 3:
                return 'read';
                break;
            // Mode rechercher
            case 999:
                return 'search';
                break;
            // Sécurité bien que pas d'action spécfique
            default:
                return null;
                break;
        }
    }

    /**
     * Permet de composer un tableau des actions composant le portlet.
     *
     * Ce tableau sera directement interprété par la méthode d'affichage du portlet
     * (formulaire::afficher_portlet).
     * Une action est composée des éléments suivant :
     * - href,
     * - target,
     * - class,
     * - onclick,
     * - id,
     * - libelle.
     *
     * @return void
     */
    function compose_portlet_actions() {

        // Récupération maj et crud
        $maj = $this->getParameter("maj");
        $crud = $this->get_action_crud($maj);
        // On compose le portlet d'actions uniquement en mode CONSULTER
        // Si on ne se trouve pas dans ce cas alors on sort de la méthode
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            return;
        }

        // On retient seulement les actions disponibles pour l'utilisateur
        // c'est-à-dire les actions pour lesquelles il a les permissions
        // et/ou qui sont valides dans le contexte en question.
        // On initialise donc le tableau résultat
        $this->user_actions = array();

        // On prépare les variables à utiliser dans la boucle
        if ($this->exists()) {
            $idx = $this->getVal($this->clePrimaire);
        } else {
            $idx = $this->getParameter("idx");
        }
        $retourformulaire = $this->getParameter("retourformulaire");

        // On teste quelle mode de gestion des actions est configuré
        if ($this->is_option_class_action_activated() === false) {

            // ANCIENNE GESTION DES ACTIONS
            // Les actions sont définies par les scripts de configuration
            // form.inc et dans les méthodes ``application:view_form()`` et
            // ``application::view_sousform()``

            // On récupère la définition des actions depuis le paramètre
            // actions et éventuellement l'attribut actions_sup
            // Si aucune action n'est présente alors on sort de la méthode
            $actions = array_merge($this->getParameter("actions"), $this->actions_sup);
            if (empty($actions)) {
                return;
            }

            // On boucle sur les actions définies
            foreach ($actions as $key => $conf) {

                /**
                 * Vérifications sur la validité de l'action
                 */
                // Vérification des droits
                // Si des droits sont requis sur l'action et que l'utilisateur
                // n'est pas autorisé alors on passe à l'itération suivante.
                if (isset($conf['rights']) && !$this->f->isAccredited(
                        $conf['rights']['list'],
                        $conf['rights']['operator'])) {
                    continue;
                }
                // Vérification du lien
                // Si l'action est configurée dans lien ou avec un lien #
                // alors on passe à l'itération suivante.
                if (empty($conf['lien']) || $conf['lien'] == '#') {
                    continue;
                }

                /**
                 * Composition de l'action
                 */
                // On détermine l'identifiant de l'action.
                $action_identifier = $key;
                // On détermine le type de l'action.
                $action_type = "";
                if (isset($conf["target"]) && $conf["target"] == "_blank") {
                    // Si l'action est paramétrée pour ouvrir le lien dans une
                    // nouvelle fenêtre alors le type de l'action est 'action-blank'
                    // peu importe les autres paramètres de l'action.
                    $action_type = "action-blank";
                } elseif ($retourformulaire != ""
                    && (!isset($conf['ajax']) || $conf['ajax'] == true)) {
                    // Si l'action est paramétrée pour s'ouvrir en ajax, c'est-à-dire
                    // pour être ouverte en lieu et place du formulaire actuel
                    // (valable pour un souform).
                    $action_type = "action-self";
                }
                // Préparation du tri
                $action_order = $key;
                if (isset($conf['ordre']) and !empty($conf['ordre'])) {
                    $action_order = $conf['ordre'];
                }
                // On compose l'attribut id de l'action. Il s'agit d'un identifiant
                // 'unique' pour l'action composé de la chaine 'action', du type du
                // formulaire, de l'objet du formulaire, du nom de l'action
                $action_id = "action";
                $action_id .= ($retourformulaire != "" ? "-sousform" : "-form");
                $action_id .= "-".$this->get_absolute_class_name()."-".$key;
                // On compose l'attribut class de l'action. Il est composé de la
                // classe 'action' et éventuellement du type de l'action.
                $action_class = sprintf(" action %s ", $action_type);
                //
                $action_target = ($action_type == "action-blank" ? "_blank" : "");
                //
                $action_libelle = $conf["lib"];
                // On compose l'attribut href de l'action. Il est possible que
                // cet attribut contienne un 'trick' qui consiste en la fermeture
                // de la déclaration de l'attribut href (\") pour ouvrir un
                // attribut onclick par exemple.
                $action_href = $conf["lien"].$idx.$conf["id"];
                //
                $action_description = $key;
                if (isset($conf["description"])) {
                    $action_description = $conf["description"];
                }
                //
                $action = array(
                    "action" => $action_identifier,
                    "order" => $action_order,
                    "id" => $action_id,
                    "class" => $action_class,
                    "target" => $action_target,
                    "libelle" => $action_libelle,
                    "description" => $action_description,
                    "href" => $action_href,
                );

                /**
                 *
                 */
                // On ajoute l'action dans le tableau résultat
                $this->user_actions[$key] = $action;
            }
        } else {

            // NOUVELLE GESTION DES ACTIONS
            // Gestion des actions définies dans les attributs de classe

            // Les actions sont définies dans un attribut de la classe
            // Si aucune action n'y est présente alors on sort de la méthode
            $actions = $this->get_class_actions();
            if (empty($actions)) {
                return;
            }

            // On boucle sur les actions définies
            foreach ($actions as $key => $conf) {

                /**
                 * Vérifications sur la validité de l'action
                 */
                // Vérification de l'existence de l'action portlet
                // On récupère uniquement les actions qui sont à afficher dans
                // le portlet. Si ce n'est pas le cas, on passe à l'itération
                // suivante.
                if ($this->is_portlet_action_defined($key) !== true) {
                    continue;
                }
                // Vérification de la condition
                // Si une condition est définie sur l'action et que la condition
                // n'est pas vérifiée dans le contexte, alors on passe à
                // l'itération suivante.
                if ($this->is_action_condition_satisfied($key) !== true) {
                    continue;
                }
                // Verification des droits
                // Si des droits sont requis sur l'action et que l'utilisateur
                // n'est pas autorisé, alors on passe à l'itération suivante.
                $specific_right = "";
                $permission_suffix = $this->get_action_param($key, "permission_suffix");
                if ($permission_suffix != null) {
                    $specific_right = $this->get_absolute_class_name()."_".$permission_suffix;
                }
                if (!$this->f->isAccredited(
                        array($this->get_absolute_class_name(), $specific_right, ),
                        "OR")) {
                    continue;
                }

                /**
                 * Composition de l'action
                 */
                // On détermine le type de l'action.
                $action_type = "";
                if ($this->get_action_param($key, "portlet_type") != null) {
                    $action_type = $this->get_action_param($key, "portlet_type");
                }
                // On détermine l'identifiant de l'action
                $action_identifier = $this->get_action_param($key, "identifier");
                // Préparation du tri
                $action_order = $key;
                $portlet_order = $this->get_action_param($key, "portlet_order");
                if ($portlet_order!=null and is_integer($portlet_order)) {
                    $action_order = $portlet_order;
                }
                // On compose l'attribut id de l'action. Il s'agit d'un identifiant
                // 'unique' pour l'action composé de la chaine 'action', du type du
                // formulaire, de l'objet du formulaire, du nom de l'action
                $action_id = "action";
                $action_id .= ($retourformulaire != "" ? "-sousform" : "-form");
                $action_id .= "-".$this->get_absolute_class_name()."-".$action_identifier;
                // On compose l'attribut class de l'action. Il est composé de la
                // classe 'action' et éventuellement du type de l'action.
                switch ($action_type) {
                    case "action-direct":
                        $class_tmp = "action-direct";
                        break;
                    case "action-direct-with-confirmation":
                        $class_tmp = "action-direct action-with-confirmation";
                        break;
                    case "action-blank":
                        $class_tmp = "action-blank";
                        break;
                    case "action-self":
                        $class_tmp = "action-self";
                        break;
                    case "action-overlay":
                        $class_tmp = "action-overlay";
                        break;
                    default:
                        $class_tmp = $action_type;
                }
                $action_class = sprintf(" action %s ", $class_tmp);
                //
                $action_target = ($action_type == "action-blank" ? "_blank" : "");
                // On compose le libellé de l'action.
                $libelle_title = $this->get_action_param($key, "portlet_libelle");
                if ($libelle_title == null) {
                    $libelle_title = $action_identifier;
                }
                $libelle_class = $this->get_action_param($key, "portlet_class");
                if ($libelle_class == null) {
                    $libelle_class = "";
                }
                $libelle_description = $this->get_action_param($key, "portlet_description");
                if ($libelle_description == null) {
                    $libelle_description = $libelle_title;
                }
                $action_libelle = sprintf(
                    "<span class=\"om-prev-icon om-icon-16 %s\">%s</span>",
                    ($libelle_class == "" ? "" : " ".$libelle_class),
                    $libelle_title
                );
                //
                $action_description = $libelle_description;
                // On compose l'attribut href de l'action.
                if ($this->get_action_param($key, "portlet_url") != null) {
                    $url = $this->get_action_param($key, "portlet_url");
                    $action_href = $url.$idx;
                } else {
                    //
                    $override = array(
                        "validation" => null,
                        "maj" => $key,
                        "retour" => "form",
                    );
                    if ($this->exists()) {
                        $override["idx"] = $this->getVal($this->clePrimaire);
                    }
                    // Si en sousform appel de sousform.php sinon form.php
                    if($this->getParameter("retourformulaire") != "") {
                        $action_href = $this->compose_form_url("sousform", $override);
                    } else {
                        $action_href = $this->compose_form_url("form", $override);
                    }
                }
                //
                $action = array(
                    "action" => $action_identifier,
                    "order" => $action_order,
                    "id" => $action_id,
                    "class" => $action_class,
                    "target" => $action_target,
                    "libelle" => $action_libelle,
                    "description" => $action_description,
                    "href" => $action_href,
                );

                /**
                 *
                 */
                // On ajoute l'action dans le tableau résultat
                $this->user_actions[$key] = $action;
            }
        }

        // Tri du tableau résultat
        uasort($this->user_actions, array($this, 'cmp_class_actions'));
    }

    // {{{ POINTS D'ENTREE DANS LES VUES - formulaire et sousformulaire

    /**
     * Point d'entrée dans la VIEW formulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques avant le portlet d'actions.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function form_specific_content_before_portlet_actions($maj) {
    }

    /**
     * Point d'entrée dans la VIEW formulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques après le portlet d'actions.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function form_specific_content_after_portlet_actions($maj) {
    }

    /**
     * Point d'entrée dans la VIEW sousformulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques avant le portlet d'actions.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function sousform_specific_content_before_portlet_actions($maj) {
    }

    /**
     * Point d'entrée dans la VIEW sousformulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques après le portlet d'actions.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function sousform_specific_content_after_portlet_actions($maj) {
    }

    /**
     * Point d'entrée dans la VIEW formulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques en fin de formulaire.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function formSpecificContent($maj) {
    }

    /**
     * Point d'entrée dans la VIEW sousformulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques en fin de sousformulaire.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function sousFormSpecificContent($maj) {
    }

    /**
     * Point d'entrée dans la VIEW formulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques après le formulaire.
     *
     * @return void
     */
    function afterFormSpecificContent() {
    }

    /**
     * Point d'entrée dans la VIEW sousformulaire.
     *
     * Cette méthode à surcharger permet d'afficher des informations
     * spécifiques après le sousformulaire.
     *
     * @return void
     */
    function afterSousFormSpecificContent() {
    }

    // }}}

    // {{{ GESTION DU VERROU

    /**
     * Cette méthode est inutilisée depuis la version 4.5.0 du framework.
     *
     * Elle est conservée ici dans un souci de rétro-compatibilité. Elle
     * sera supprimée dans la version 4.6.0 du framework.
     *
     * @param null $validation @deprecated
     *
     * @deprecated
     * @return void
     */
    function deverrouille($validation = null) {
    }

    /**
     * Cette méthode est inutilisée depuis la version 4.5.0 du framework.
     *
     * Elle est conservée ici dans un souci de rétro-compatibilité. Elle
     * sera supprimée dans la version 4.6.0 du framework.
     *
     * @deprecated
     * @return void
     */
    function verrouille() {
    }

    /**
     * Cette méthode est inutilisée depuis la version 4.5.0 du framework.
     *
     * Elle est conservée ici dans un souci de rétro-compatibilité. Elle
     * sera supprimée dans la version 4.6.0 du framework.
     *
     * @deprecated
     * @return void
     */
    function testverrou() {
    }

    // }}}

    /**
     * Indique si la redirection vers le lien de retour est activée ou non.
     *
     * L'objectif de cette méthode est de permettre d'activer ou de désactiver
     * la redirection dans certains contextes.
     *
     * @return boolean
     */
    function is_back_link_redirect_activated() {
        return true;
    }

    /**
     * Stocke le message en session et fais une redirection vers le lien de
     * retour.
     *
     * @param string $view Appel dans le contexte de la vue 'formulaire' ou de
     *                     la vue 'sousformulaire'.
     *
     * @return void
     */
    function redirect_to_back_link($view = "formulaire") {
        if ($this->is_back_link_redirect_activated() !== true) {
            return;
        }
        //
        $href = $this->get_back_link($view);
        $href .= "&message_id=".$this->f->add_session_message($this->msg);
        header("Location:".$href);
        die();
    }

    /**
     * Retourne la cible de retour (VIEW formulaire et VIEW sousformulaire).
     *
     * La cible de retour peut être 'form' ou 'tab'. L'ergonomie permet donc
     * de renvoyer soit sur la vue de l'élément (form) soir sur le listing
     * (tab).
     *
     * @return string
     */
    function get_back_target() {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud();
        // On revient au tableau si le param retour vaut 'form'
        //   ET ( on a validé avec succès une suppression
        //    OU on n'a pas validé le formulaire d'ajout
        //    OU on se trouve sur le formulaire en mode 'VISUALISATION'
        //   )
        if ($this->getParameter("retour") == "form"
            && !($this->getParameter("validation") > 0
                 && ($crud === 'delete'
                     || ($crud === null
                         && $this->getParameter('maj') == 2))
                 && $this->correct == true)
            && !(($crud === 'create'
                  || ($crud === null
                      && $this->getParameter('maj') == 0))
                 && $this->correct == false)
            && !($crud === 'read'
                 || ($crud === null
                     && $this->getParameter('maj') == 3))) {
            return "form";
        }
        return "tab";
    }

    /**
     * Retourne le lien de retour (VIEW formulaire et VIEW sousformulaire).
     *
     * @param string $view Appel dans le contexte de la vue 'formulaire' ou de
     *                     la vue 'sousformulaire'.
     *
     * @return string
     */
    function get_back_link($view = "formulaire") {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud();
        //
        if ($view === "formulaire") {
            $tab_script = OM_ROUTE_TAB;
            $form_script = OM_ROUTE_FORM;
        } elseif ($view === "sousformulaire") {
            $tab_script = OM_ROUTE_SOUSTAB;
            $form_script = OM_ROUTE_SOUSFORM;
        }
        // On revient au tableau, ou au formulaire si le param retour vaut 'form'
        // et on n'a pas validé avec succès une suppression
        if ($this->get_back_target() === "form") {
            //
            $href = $form_script;
        } else {
            $href = $tab_script;
        }
        //
        $href .= "&obj=".$this->get_absolute_class_name();

        //
        if ($this->get_back_target() === "form") {
            if (($crud === 'create'
                 || ($crud === null
                     && $this->getParameter('maj') == 0))
                && $this->correct == true) {
                $href .= "&idx=".$this->valF[$this->clePrimaire];
            } else {
                $href .= "&idx=".$this->getParameter("idx");
            }
            $href .= "&action=3";
        }
        //
        if ($view === "formulaire") {
            $href .= "&advs_id=".$this->getParameter("advs_id");
            $href .= "&premier=".$this->getParameter("premier");
            $href .= "&tricol=".$this->getParameter("tricol");
            $href .= "&valide=".$this->getParameter("valide");
        } elseif ($view === "sousformulaire") {
            $href .= "&retourformulaire=".$this->getParameter("retourformulaire");
            $href .= "&idxformulaire=".$this->getParameter("idxformulaire");
            $href .= "&advs_id=".$this->getParameter("advs_id");
            $href .= "&premier=".$this->getParameter("premiersf");
            $href .= "&tricol=".$this->getParameter("tricolsf");
            $href .= "&valide=".$this->getParameter("valide");
        }
        return $href;
    }

    /**
     * Affiche le bloc message.
     *
     * @return void
     */
    function message() {
        //
        $this->f->handle_and_display_session_message();
        //
        if ($this->msg != "") {
            //
            if ($this->correct) {
                $class = "valid";
            } else {
                $class = "error";
            }
            $this->f->layout->display_message($class, $this->msg);
        }
    }

    /**
     * Affiche le lien retour (VIEW formulaire).
     *
     * Cette méthode permet de composer le lien retour et de l'afficher.
     *
     * @param null $dnu1 @deprecated Ne pas utiliser.
     * @param null $dnu2 @deprecated Ne pas utiliser.
     * @param null $dnu3 @deprecated Ne pas utiliser.
     *
     * @return void
     */
    function retour($dnu1 = null, $dnu2 = null, $dnu3 = null) {
        //
        if ($this->get_back_target() === "form") {
            $css_class = "retour-form";
        } else {
            $css_class = "retour-tab";
        }
        //
        $href = str_replace(
            "&",
            "&amp;",
            $this->get_back_link("formulaire")
        );
        //
        $this->f->layout->display_form_retour(array(
            "id" => "form-action-".$this->get_absolute_class_name()."-back-".uniqid(),
            "href" => $href,
            "class" => $css_class,
        ));
    }

    /**
     * Affiche le bouton (VIEW formulaire).
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function bouton($maj) {
        if (!$this->correct
            && $this->checkActionAvailability() == true) {
            // Ancienne gestion des actions
            if ($this->is_option_class_action_activated() == false) {
                switch ($maj) {
                    case 0:
                        $bouton = __("Ajouter");
                        break;
                    case 1:
                        $bouton = __("Modifier");
                        break;
                    case 2:
                        $bouton = __("Supprimer");
                        break;
                    case 999:
                        $bouton = __("Rechercher");
                        break;
                    default:
                        $bouton = __("Valider");
                        break;
                }
            }
            // Nouvelle gestions des actions
            if ($this->is_option_class_action_activated() == true) {
                // Actions SCRUD ou indéfinies
                if ($this->get_action_param($maj, "button") == null) {
                    // Récupération du mode de l'action
                    $crud = $this->get_action_crud($maj);
                    switch ($crud) {
                        case 'create':
                            $bouton = __("Ajouter");
                            break;
                        case 'update':
                            $bouton = __("Modifier");
                            break;
                        case 'delete':
                            $bouton = __("Supprimer");
                            break;
                        case 'search':
                            $bouton = __("Rechercher");
                            break;
                        default:
                            $bouton = __("Valider");
                            break;
                    }
                } else {
                    // Actions spécifiques
                    //
                    $bouton = $this->get_action_param($maj, "button");
                }
            }
            //
            $this->f->layout->display__form_input_submit(array(
                "value" => $bouton,
                "name" => "submit",
            ));
        }
    }

    /**
     * Affiche le bouton (VIEW sousformulaire).
     *
     * @param null $datasubmit @deprecated Non utilisé.
     * @param integer $maj Identifant numérique de l'action.
     * @param null $val @deprecated Non utilisé.
     *
     * @return void
     */
    function boutonsousformulaire($datasubmit, $maj, $val=null) {
        //
        $this->bouton($maj);
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setRequired(&$form, $maj) {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud($maj);
        // En modes ajouter et modifier
        if (($crud === 'create' or $crud === 'update')
            or ($crud === null and $this->getParameter('maj') < 2)) {
            //
            foreach ($this->required_field as $field) {
                $form->setRequired($field);
            }
        }
    }

    /**
     * Configuration du formulaire (VIEW formulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     * @param integer $validation Marqueur de validation du formulaire.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        $this->set_form_default_values($form, $maj, $validation);
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     * @param integer $validation Marqueur de validation du formulaire.
     *
     * @return void
     */
    function set_form_default_values(&$form, $maj, $validation) {
    }

    /**
     * Méthode permettant de remplir valF avant validation du formulaire
     *
     * @return void
     */
    function setValFFromVal() {
        foreach ($this->champs as $champ) {
            $this->valF[$champ] = $this->getVal($champ);
        }
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setType(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setLib(&$form, $maj) {
        // libelle automatique
        //[automatic wording]
        foreach (array_keys($form->val) as $elem) {
             $form->setLib($elem, __($elem));
        }
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setTaille(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setMax(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
    }

    /**
     * Met à jour la varaiable '$contenu' pour gérer d'éventuelles valeurs
     * non valides (om_validite).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param array $contenu
     * @param string $sql_by_id
     * @param string $table
     * @param null|array $val
     *
     * @return void
     */
    function getSelectOldValue(&$form, $maj, &$dnu1 = null, &$contenu, $sql_by_id, $table, $val = null) {

        if ($val == null) {
            $val = $this->form->val[$table];
        }
        // Recuperation de la valeur depuis la base de donnes.
        $sql_by_id = str_replace("'<idx>'", "'".$this->f->db->escapeSimple($val)."'", $sql_by_id);
        $sql_by_id = str_replace('<idx>', intval($val), $sql_by_id);
        // Exécution de la requête
        $res = $this->f->db->query($sql_by_id);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql_by_id."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        while ($row =& $res->fetchRow()) {
            // Si première entrée nulle
            if ($contenu[0][0] == '') {
                // On insère l'ancienne valeur en deuxième position
                // Valeurs
                $contenu[0] = array_merge(array($contenu[0][0]),
                                    array($row[0]),
                                    array_slice($contenu[0], 1));
                // Libellés
                $contenu[1] = array_merge(array($contenu[1][0]),
                                    array($row[1]),
                                    array_slice($contenu[1], 1));
            }
            // Sinon on l'insère en premier
            else {
                // Valeurs
                $contenu[0] = array_merge(array($row[0]),
                                          $contenu[0]);
                // Libellés
                $contenu[1] = array_merge(array($row[1]),
                                          $contenu[1]);
            }
        }
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setOnchange(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setOnkeyup(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setOnclick(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setGroupe(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setRegroupe(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setBloc(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setFieldset(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function setLayout(&$form, $maj) {
    }

    /**
     * Configuration du formulaire (VIEW formulaire et VIEW sousformulaire).
     *
     * Permet d'effectuer des appels aux mutateurs spécifiques sur le formulaire
     * de manière fonctionnelle et non en fonction du mutateur. Exemple : au lieu
     * de gérer le champ service dans les méthodes setType, setSelect, le setLib,
     * ... Nous allons les gérer dans cette méthode et appeler tous les mutateurs
     * à la suite.
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    function set_form_specificity(&$form, $maj) {
    }

    /**
     * Permet de modifier le fil d'Ariane depuis l'objet pour un formulaire
     * @param string    $ent    Fil d'Ariane récupéréré
     * @return                  Fil d'Ariane
     */
    function getFormTitle($ent) {
        //
        $out = $ent;
        if ($this->getVal($this->clePrimaire) != "") {
            $out .= "<span class=\"libelle\"> -> ".$this->get_default_libelle()."</span>";
        }
        return $out;
    }

    /**
     * Permet de modifier le fil d'Ariane depuis l'objet pour un sous-formulaire
     * @param string    $ent Fil d'Ariane récupéréré
     * @return                  Fil d'Ariane
     */
    function getSubFormTitle($ent) {
        //
        $out = $ent;
        if ($this->getVal($this->clePrimaire) != "") {
            $out .= "<span class=\"libelle\"> -> ".$this->get_default_libelle()."</span>";
        }
        return $out;
    }

    /**
     * VIEW - sousformulaire.
     *
     * @return void
     */
    function sousformulaire() {
        // Marqueur permettant d'indiquer si le formulaire doit être affiché ou
        // non. Par exemple : si la soumission du formulaire n'est pas valide,
        // on veut afficher un message et un bouton retour et pas le formulaire.
        // ce marqueur nous permet de stocker l'information.
        $flag_do_not_display_form = false;
        // Récupération du mode de l'action et de sa clé
        $maj = $this->getParameter("maj");
        $crud = $this->get_action_crud($maj);
        //
        $datasubmit = $this->getDataSubmitSousForm();
        // Ouverture de la balise form si pas en consultation
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            $this->f->layout->display__form_container__begin(array(
                "action" => "",
                "name" => "f2",
                "onsubmit" => "affichersform('".$this->getParameter("objsf")."', '".$datasubmit."', this);return false;",
            ));
        }
        // Compatibilite anterieure - On decremente la variable validation
        $this->setParameter("validation", $this->getParameter("validation") - 1);
        // Instanciation de l'objet formulaire
        $this->form = $this->f->get_inst__om_formulaire(array(
            "validation" => $this->getParameter("validation"),
            "maj" => $maj,
            "champs" => $this->champs,
            "val" => $this->val,
            "max" => $this->longueurMax,
        ));
        //
        $this->form->setParameter("obj", $this->get_absolute_class_name());
        $this->form->setParameter("idx", $this->getParameter("idx"));
        $this->form->setParameter("maj", $this->getParameter("maj"));
        $this->form->setParameter("idxformulaire", $this->getParameter("idxformulaire"));
        $this->form->setParameter("retourformulaire", $this->getParameter("retourformulaire"));
        $this->form->setParameter("form_type", "sousform");
        // Valorisation des variables formulaires
        $this->setValsousformulaire(
            $this->form, $maj,
            $this->getParameter("validation"),
            $this->getParameter("idxformulaire"),
            $this->getParameter("retourformulaire"),
            $this->getParameter("typeformulaire"),
            $this->f->db, null
        );
        $this->setType($this->form, $maj);
        $this->setLib($this->form, $maj);
        $this->setTaille($this->form, $maj);
        $this->setMax($this->form, $maj);
        $this->setSelect($this->form, $maj, $this->f->db, null);
        $this->setOnchange($this->form, $maj);
        $this->setOnkeyup($this->form, $maj);
        $this->setOnclick($this->form, $maj);
        $this->setGroupe($this->form, $maj);
        $this->setRegroupe($this->form, $maj);
        $this->setLayout($this->form, $maj);
        $this->setRequired($this->form, $maj);
        $this->set_form_specificity($this->form, $maj);
        //
        $this->form->recupererPostvarsousform(
            $this->champs, $this->getParameter("validation"),
            $this->getParameter("postvar"), null
        );
        // Verification de l'accessibilité sur l'élément
        // Si l'utilisateur n'a pas accès à l'élément dans le contexte actuel
        // on arrête l'exécution du script
        $this->checkAccessibility();
        // Si le formulaire a été validé
        if ($this->getParameter("validation") > 0) {
            // Gestion de la fonction 'soumission multiple impossible'
            // Si la méthode valide la soumission alors on exécute le traitement.
            if ($this->form_resubmit_handle_valid_identifier() === true) {
                if ($this->post_treatment() === true) {
                    $this->redirect_to_back_link("sousformulaire");
                }
            } else {
                // On positionne le marqueur pour ne pas afficher le formulaire.
                $flag_do_not_display_form = true;
                // On prépare le message d'erreur
                $this->correct = false;
                $this->msg = "";
                $this->addToMessage(__("Opération illégale. Ce formulaire a déjà été soumis, il est impossible de le soumettre une seconde fois."));
            }
        }
        // Affichage du message avant d'afficher le formulaire
        $this->message();
        // Si le marqueur l'indique, on ne veut pas afficher le formulaire donc
        // on sort de la méthode.
        if ($flag_do_not_display_form === true) {
            // Affichage du bouton retour
            $this->f->layout->display__form_controls_container__begin(array(
                "controls" => "top",
            ));
            $this->retoursousformulaire(
                $this->getParameter("idxformulaire"),
                $this->getParameter("retourformulaire"),
                $this->form->val,
                $this->getParameter("objsf"),
                $this->getParameter("premiersf"),
                $this->getParameter("tricolsf"),
                $this->getParameter("validation"),
                $this->getParameter("idx"),
                $maj,
                $this->getParameter("retour")
            );
            $this->f->layout->display__form_controls_container__end();
            return;
        }
        // Affichage du bouton et du bouton retour
        $this->f->layout->display__form_controls_container__begin(array(
            "controls" => "top",
        ));
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            // Affichage du bouton
            $this->boutonsousformulaire(
                $datasubmit,
                $maj,
                $this->form->val
            );
        }
        $this->retoursousformulaire(
            $this->getParameter("idxformulaire"),
            $this->getParameter("retourformulaire"),
            $this->form->val,
            $this->getParameter("objsf"),
            $this->getParameter("premiersf"),
            $this->getParameter("tricolsf"),
            $this->getParameter("validation"),
            $this->getParameter("idx"),
            $maj,
            $this->getParameter("retour")
        );
        $this->f->layout->display__form_controls_container__end();
        // Ouverture du conteneur de formulaire
        $this->form->entete();
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->sousform_specific_content_before_portlet_actions($maj);
        // Composition du tableau d'action à afficher dans le portlet
        $this->compose_portlet_actions();
        // Affichage du portlet d'actions s'il existe des actions
        if (!empty($this->user_actions)) {
            $this->form->afficher_portlet(
                $this->getParameter("idx"),
                $this->user_actions,
                $this->getParameter("objsf")
            );
        }
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->sousform_specific_content_after_portlet_actions($maj);
        // Affichage du contenu du formulaire
        $this->form->afficher(
            $this->champs,
            $this->getParameter("validation"),
            null,
            $this->correct
        );
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->sousFormSpecificContent($maj);
        // Fermeture du conteneur de formulaire
        $this->form->enpied();
        // Affichage du bouton et du bouton retour
        $this->f->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            // Gestion de la fonction 'soumission multiple impossible'
            $this->form_resubmit_handle_new_identifier();
            // Affichage du bouton
            $this->boutonsousformulaire(
                $datasubmit,
                $maj,
                $this->form->val
            );
        }
        $this->retoursousformulaire(
            $this->getParameter("idxformulaire"),
            $this->getParameter("retourformulaire"),
            $this->form->val,
            $this->getParameter("objsf"),
            $this->getParameter("premiersf"),
            $this->getParameter("tricolsf"),
            $this->getParameter("validation"),
            $this->getParameter("idx"),
            $maj,
            $this->getParameter("retour")
        );
        $this->f->layout->display__form_controls_container__end();
        // Fermeture de la balise form
        if (($crud !== null AND $crud !== 'read')
            OR ($crud === null AND $maj != 3)) {
            $this->f->layout->display__form_container__end();
        }
        // Point d'entrée en dessous du formulaire pour ajout d'éléments spécifiques
        $this->afterSousFormSpecificContent();
    }

    /**
     * Affiche le lien retour dans la VIEW formulaire.
     *
     * Cette méthode permet de composer le lien retour et de l'afficher.
     *
     * @param null $dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     * @param null $dnu3 @deprecated  Ne pas utiliser.
     * @param null $dnu4 @deprecated  Ne pas utiliser.
     * @param null $dnu5 @deprecated  Ne pas utiliser.
     * @param null $dnu6 @deprecated  Ne pas utiliser.
     * @param null $dnu7 @deprecated  Ne pas utiliser.
     * @param null $dnu8 @deprecated  Ne pas utiliser.
     * @param null $dnu9 @deprecated  Ne pas utiliser.
     * @param null $dnu10 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function retoursousformulaire($dnu1 = null, $dnu2 = null, $dnu3 = null, $dnu4 = null, $dnu5 = null, $dnu6 = null, $dnu7 = null, $dnu8 = null, $dnu9 = null, $dnu10 = null) {
        //
        if ($this->get_back_target() === "form") {
            $css_class = "retour-form";
        } else {
            $css_class = "retour-tab";
        }
        //
        $href = str_replace(
            "&",
            "&amp;",
            $this->get_back_link("sousformulaire")
        );
        //
        $this->f->layout->display_form_retour(array(
            "id" => "sousform-action-".$this->get_absolute_class_name()."-back-".uniqid(),
            "href" => $href,
            "class" => $css_class,
        ));
    }

    /**
     * Configuration du formulaire (VIEW sousformulaire).
     *
     * @param formulaire $form Instance formulaire.
     * @param integer $maj Identifant numérique de l'action.
     * @param integer $validation Marqueur de validation du formulaire.
     * @param string $idxformulaire Identifiant de l'objet du formulaire parent (form.php?idx=).
     * @param string $retourformulaire Objet du formulaire parent (form.php?obj=).
     * @param string $typeformulaire @deprecated ???
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     *
     * @return void
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        if ($validation == 0) {
            if ($crud === 'create'
                or ($crud === null and $this->getParameter('maj') == 0)) {
                //
                $form->setVal($retourformulaire, $idxformulaire);
            }
        }
        $this->set_form_default_values($form, $maj, $validation);
    }

    /**
     * Cette methode permet d'obtenir une chaine representant la clause where
     * pour une requete de selection sur la cle primaire.
     *
     * @param string $id Valeur de la cle primaire
     * @return string Clause where
     */
    function getCle($id = "") {
        //
        $cle = " ".$this->table.".".$this->clePrimaire." = ";
        // Clause where en fonction du type de la cle primaire
        if ($this->typeCle == "A") {
            $cle .= " '".$this->f->db->escapeSimple($id)."' ";
        } else {
            $cle .= " ".intval($id)." ";
        }
        //
        return $cle;
    }

    /**
     * Cette methode permet de faire les verifications necessaires lors de
     * l'ajout de messages, et d'obtenir une coherence dans l'attribut message
     * de l'objet pour l'affichage.
     *
     * @param string $message
     */
    function addToMessage($message = "") {
        //
        if (!isset($this->msg)) {
            $this->msg = "";
        } else {
            if ($this->msg != "") {
                $this->msg .= "<br />";
            }
        }
        //
        $this->msg .= $message;
    }

    /**
     * Cette méthode ne doit plus être appelée, c'est
     * '$this->f->isDatabaseError($res)' qui s'occupe d'afficher le message
     * d'erreur et de faire le 'die()'.
     *
     * @param string $debuginfo Message 1.
     * @param string $messageDB Message 2.
     * @param string $table Inutilisé.
     *
     * @return void
     * @deprecated
     */
    function erreur_db($debuginfo, $messageDB, $table) {
        $this->addToErrors(
            $debuginfo,
            $messageDB,
            __("Erreur de base de donnees. Contactez votre administrateur.")
        );
    }

    /**
     * Cette methode remplace erreur_db, et permet de remplir le tableau d'erreur
     *
     * @param string $debuginfo Message 1.
     * @param string $messageDB Message 2.
     * @param string $msg Message 3.
     *
     * @return void
     */
    function addToErrors($debuginfo, $messageDB, $msg) {
            $this->errors['db_debuginfo'] = $debuginfo;
            $this->errors['db_message'] = $messageDB;
            $this->addToLog(__METHOD__."(): ".$msg, VERBOSE_MODE);
    }

    /**
     * Cette methode vide les valeurs des erreurs du tableau errors.
     *
     * @return void
     */
    function clearErrors() {
        foreach (array_keys($this->errors) as $key) {
            $this->errors[$key] = '';
        }
    }

    /**
     * Cette methode permet de rechercher le nombre d'enregistrements
     * ayant le champ 'field' correspondant a la valeur 'id' dans la table
     * 'table'. Si il y a des enregistrements, alors l'attribut 'correct' de
     * l'objet est passe a la valeur false et un message supplementaire est
     * ajoute a l'attribut msg de l'objet.
     *
     * Cette methode est principalement destinee a etre appellee depuis la
     * methode cleSecondaire.
     *
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param string $table
     * @param string $field
     * @param string $id
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     * @param string $selection
     *
     * @return void
     */
    function rechercheTable(&$dnu1 = null, $table, $field, $id, $dnu2 = null, $selection = "") {
        //
        $sql = "select count(*) from ".DB_PREFIXE.$table." ";
        if ($this->typeCle == "A") {
            $sql .= "where ".$field."='".$id."' ";
        } else {
            $sql .= "where ".$field."=".$id." ";
        }
        $sql .= $selection;

        // Exécution de la requête
        $nb = $this->f->db->getone($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($nb);

        //
        if ($nb > 0) {
            $this->correct = false;
            $this->msg .= $nb." ";
            $this->msg .= __("enregistrement(s) lie(s) a cet enregistrement dans la table");
            $this->msg .= " ".$table."<br />";
        }
    }

    /**
     * Initialisation des valeurs des champs HTML <select>
     *
     * @param formulaire $form Instance formulaire.
     * @param null &$dnu1 @deprecated  Ne pas utiliser.
     * @param integer $maj Identifant numérique de l'action.
     * @param null $dnu2 @deprecated  Ne pas utiliser.
     * @param string $field nom du champ <select> a initialiser
     * @param string $sql requete de selection des valeurs du <select>
     * @param string $sql_by_id requete de selection valeur par identifiant
     * @param string $om_validite permet de définir si l'objet lié est affecté par une date de validité
     * @param string $multiple permet d'utiliser cette méthode pour configurer l'affichage de select_multiple (widget)
     *
     * @return void
     */
    function init_select(&$form = null, &$dnu1 = null, $maj, $dnu2 = null, $field, $sql,
                         $sql_by_id = "", $om_validite = false, $multiple = false) {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud($maj);
        // MODES AJOUTER, MODIFIER ET RECHERCHE AVANCÉE
        if (($crud === 'create' OR $crud === 'update' OR $crud == 'search')
            OR ($crud === null AND ($maj == 0 OR $maj == 1 OR $maj == 999))) {
            // Exécution de la requête
            $res = $this->f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            // Initialisation du select
            $contenu = array();
            $contenu[0][0] = '';
            $contenu[1][0] = __('choisir')."&nbsp;".__($field);
            //
            $k=1;
            while($row =& $res->fetchRow()){
                $contenu[0][$k] = $row[0];
                $contenu[1][$k] = $row[1];
                $k++;
            }

            // Si en mode "modifier" et si la gestion des dates de validité est activée
            if (($crud === 'update' OR ($crud === null AND $maj == 1))
                AND $om_validite == true) {
                $field_values = array();
                // Dans le cas d'un select_multiple
                if ($multiple == true) {
                    $field_values = explode(";", $this->form->val[$field]);
                }
                // Dans le cas d'un select simple
                else {
                    $field_values = array($this->form->val[$field],);
                }
                // S'il y a une ou plusieurs valeurs
                if (!empty($field_values) && $field_values[0] != '') {
                    // pour chacune d'entre elles
                    foreach ($field_values as $field_value) {
                        // si elle manque au contenu du select
                        if (!in_array($field_value, $contenu[0])) {
                            // on l'ajoute
                            $this->getSelectOldValue($form, $maj, $this->f->db, $contenu,
                                                     $sql_by_id, $field, $field_value);
                        }
                    }
                }
                // S'il n'y a pas de valeur c'est que soit :
                // - aucune valeur n'est présaisie en première validation,
                // - le formulaire a été validé en erreur.
                // C'est ce dernier cas qui nous intéresse afin de ne pas perdre
                // dans le contenu une valeur invalide pourtant sélectionnée.
                // Si elle n'a pas été sélectionnée elle est dans tous les cas
                // perdue, il faut recharger le formulaire pour la récupérer.
                else {
                    // On vérifie si le formulaire est vide : si oui
                    // cela signifie que le formulaire a été validé en erreur
                    $empty = true;
                    foreach ($this->form->val as $f => $value) {
                        if (!empty($value)) {
                            $empty = false;
                        }
                    }
                    // Déclaration des valeurs postées
                    $field_posted_values = array();
                    // Dans le cas d'un select_multiple avec des valeurs postées
                    if ($multiple == true && isset($_POST[$field])) {
                        $field_posted_values = $_POST[$field];
                    }
                    // Dans le cas d'un select simple avec une valeur postée
                    elseif (isset($_POST[$field])) {
                        $field_posted_values = array($_POST[$field],);
                    }
                    // S'il y a une ou plusieurs valeurs postées
                    // et que le formulaire a déjà été validé
                    if ($empty == true && !empty($field_posted_values) && $field_posted_values[0] != '') {
                        // pour chacune d'entre elles
                        foreach ($field_posted_values as $field_posted_value) {
                            // si elle manque au contenu du select
                            if (!in_array($field_posted_value, $contenu[0])) {
                                // on l'ajoute
                                $this->getSelectOldValue($form, $maj, $this->f->db, $contenu,
                                                         $sql_by_id, $field, $field_posted_value);
                            }
                        }
                    }
                }
            }
            // Initialisation des options du select dans le formulaire
            $form->setSelect($field, $contenu);
            // Logger
            $this->addToLog(__METHOD__."(): form->setSelect(\"".$field."\", ".print_r($contenu, true).");", EXTRA_VERBOSE_MODE);
        }

        // MODE SUPPRIMER, CONSULTER ET ACTIONS SPECIFIQUES SANS CRUD
        if (($crud === 'delete' OR $crud === 'read')
            OR ($crud === null AND $maj >= 2 AND $maj != 999)) {
            // Initialisation du select
            $contenu[0][0] = '';
            $contenu[1][0] = '';

            if (isset($this->form->val[$field]) and
                !empty($this->form->val[$field]) and $sql_by_id) {
                // Dans le cas d'un select_multiple
                if ($multiple == true) {
                    // Permet de gérer le cas ou les clés primaires sont alphanumériques
                    $val_field = "'".str_replace(";", "','", $this->f->db->escapeSimple($this->form->val[$field]))."'";
                    // ajout de l'identifiant recherche a la requete
                    $sql_by_id = str_replace('<idx>', $val_field, $sql_by_id);
                } else {
                    //
                    $val_field = $this->form->val[$field];
                    // ajout de l'identifiant recherche a la requete
                    $sql_by_id = str_replace("'<idx>'", "'".$this->f->db->escapeSimple($val_field)."'", $sql_by_id);
                    $sql_by_id = str_replace('<idx>', intval($val_field), $sql_by_id);
                }
                // Exécution de la requête
                $res = $this->f->db->query($sql_by_id);
                // Logger
                $this->addToLog(__METHOD__."(): db->query(".$sql_by_id.");", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $this->f->isDatabaseError($res);
                // Affichage de la première ligne d'aide à la saisie
                $row =& $res->fetchRow();
                $contenu[0][0] = $row[0];
                $contenu[1][0] = $row[1];
                //
                $k=1;
                while($row =& $res->fetchRow()){
                    $contenu[0][$k] = $row[0];
                    $contenu[1][$k] = $row[1];
                    $k++;
                }
            }

            $form->setSelect($field, $contenu);
            // Logger
            $this->addToLog(__METHOD__."(): form->setSelect(\"".$field."\", ".print_r($contenu, true).");", EXTRA_VERBOSE_MODE);
        }
    }

    /**
     * Cette methode est à surcharger elle permet de tester dans chaque classe
     * des droits des droits spécifiques en fonction des données.
     *
     * @return boolean
     */
    function canAccess() {
        return true;
    }

    /**
     * Appelle la méthode canAccess() et affiche ou non une erreur.
     *
     * @return void
     */
    function checkAccessibility() {
        //Test les droits d'accès à l'élément.
        if (!$this->canAccess()
            || !$this->checkActionAvailability()) {
            //
            $this->addToLog(__METHOD__."(): acces non autorise", EXTRA_VERBOSE_MODE);
            //
            if ($this->f->isAjaxRequest() == false) {
                $this->f->setFlag(null);
                $this->f->display();
            }
            //
            $message_class = "error";
            $message = __("Droits insuffisants. Vous n'avez pas suffisamment de ".
                    "droits pour acceder a cette page.");
            $this->f->displayMessage($message_class, $message);
            // Arrêt du script
            die();
        }
    }

    /**
     * Accesseur de l'attribut `val`.
     *
     * Permet de récupérer la valeur d'un élément de l'attribut `val` via le
     * nom du champ correspondant à cette valeur.
     *
     * L'attribut `val` est un tableau représentant les valeurs de
     * l'enregistrement de l'objet. Chacune des valeurs est associée au nom du
     * champ correspondant dans l'attribut `champs`. C'est la clé numérique
     * de l'élément dans chacun des tableaux qui assure la correspondance.
     *
     * Exemple
     * -------
     *
     * Pour les trois cas, l'attribut `champs` est définit comme suit :
     * $this->champs = array("id", "libelle", "description");
     *
     * - Cas n°1 : Aucun champ correspondant au nom transmis, on retourne une
     *   chaine vide.
     *     $this->val = array(123, "Titre", "Une description courte");
     *     > echo $this->getVal("date");
     *     > ""
     *
     * - Cas n°2 : Aucune valeur pour l'enregistrement, on retourne une chaine
     *   vide.
     *     $this->val = array();
     *     > echo $this->getVal("libelle");
     *     > ""
     *
     * - Cas n°3 : Le champ et la valeur ont une correspondance, on retourne la
     *   valeur.
     *     $this->val = array(123, "Titre", "Une description courte");
     *     > echo $this->getVal("libelle");
     *     > "Titre"
     *
     * @param string $field Nom du champ de l'objet.
     *
     * @return mixed La valeur du champ de l'objet ou une chaine vide.
     */
    function getVal($field) {
        $key = array_search($field, $this->champs);
        if ($key === false) {
            return "";
        }
        if (!isset($this->val[$key])) {
            return "";
        }
        return $this->val[$key];
    }

    /**
     * Vérification de la disponibilité de l'action sur l'objet.
     *
     * Le postulat est que les actions ajouter, modifier, supprimer et
     * consulter sont disponibles sur tous les objets. La disponibilité des
     * autres actions est vérifiée si la valeur de l'action existe comme clé
     * dans l'attribut actions de l'objet.
     *
     * @return boolean
     */
    function checkActionAvailability() {

        // Test si l'action à déjà été défini
        if ($this->_is_action_available != null) {
            // Si oui on retourne la valeur précédement définie
            return $this->_is_action_available;
        }

        // Vérification de l'existance d'une action définie dans les attributs
        // de l'objet
        if (($this->is_action_defined($this->getParameter("maj")) === false and
            $this->is_option_class_action_activated()===true) or
            $this->is_action_condition_satisfied($this->getParameter("maj")) === false) {
            // Ajout des logs
            $this->addToLog(
                __METHOD__."(): action non disponible",
                EXTRA_VERBOSE_MODE
            );
            // Message d'erreur affiché à l'utilisateur
            $message = __("Cette action n'est pas disponible.");
            $this->addToMessage($message);
            // Message en rouge
            $this->correct = false;
            // Flag action dispo à false
            $this->_is_action_available = false;
        } else {
            // Flag action dispo à true
            $this->_is_action_available = true;
        }
        //
        return $this->_is_action_available;
    }

    /**
     * Indique si les actions (nouvelles) sont activée sur la classe.
     *
     * @return boolean
     */
    function is_option_class_action_activated() {
        // Option activée, le !== false est nécessaire pour que l'option soit activée
        // même si le paramètre global n'est pas défini
        if ($this->f->getParameter("activate_class_action") !== false) {
            return true;
        }
        // Permet de pouvoir utiliser les nouvelles actions que sur certains objets
        if (isset($this->activate_class_action)
            && $this->activate_class_action === true) {
            return true;
        }
        return false;
    }

    /**
     * Indique si on se trouve dans le contexte d'une clé étrangère.
     *
     * Lorsque l'on se trouve dans un sous formulaire, les champs qui sont
     * liés à l'objet du formulaire principal (clé étrangère) doivent avoir
     * un comportement spécifique. La classe du formulaire principal peut
     * facilement être surchargée, il est donc nécessaire de modifier tous
     * ces comportements spécifiques pour y ajouter le nom de la classe qui
     * surcharge l'objet principal. Cette méthode permet de faciliter la
     * vérification.
     *
     * @param string $foreign_key Table de la clé étrangère.
     * @param string $context     Valeur du contexte (retourformulaire) qui doit
     *                            être vérifiée.
     *
     * @return bool
     */
    function is_in_context_of_foreign_key($foreign_key = "", $context = "") {
        // Si la liste n'existe pas ou n'est pas un tableau
        // ou si la valeur n'est pas dans la liste
        if (!isset($this->foreign_keys_extended[$foreign_key])
            || !is_array($this->foreign_keys_extended[$foreign_key])
            || !in_array($context, $this->foreign_keys_extended[$foreign_key])) {
            // On ne se trouve pas dans le contexte
            return false;
        } else {
            // Sinon on se trouve dans le contexte
            return true;
        }
    }

    // {{{ GESTION DE LA SOUMISSION MULTIPLE DE FORMULAIRE IMPOSSIBLE - BEGIN

    /**
     * Gère et retourne la validité de la soumission du formulaire.
     *
     * Cette permet permet de vérifier :
     * - si un identifiant est posté, si ce n'est pas le cas, on part du
     *   principe que la soumission est valide (notamment pour action-direct).
     * - si sa valeur est bien présente dans la liste dédiée dans la variable
     *   de session, si c'est le cas on enlève la valeur de cette liste
     *   et on valide la soumission du formulaire et on va pouvoir exécuter le
     *   traitement rattaché (géré dans les vues formulaire).
     * - Si ce n'est pas le cas, cela signifie que le formulaire a déjà été
     *   soumis au préalable donc on ne valide pas la soumission et on va
     *   pouvoir afficher une erreur à l'utilisateur (géré dans les vues
     *   formulaire).
     *
     * @return bool
     */
    function form_resubmit_handle_valid_identifier() {
        // On récupère les valeurs postées par le formulaire.
        $postvar = $this->getParameter("postvar");
        // On vérfie d'abord si un champ contenant l'identifiant a été posté
        // si ce n'est pas le cas, il est nécessaire de retourner true et de
        // considérer que la soumission du formulaire est valide.
        if (!isset($postvar["form_resubmit_identifier"])) {
            return true;
        }
        // On vérifie si l'identifiant est bien dans la liste, si c'est le cas
        // on sort l'dientifiant de la liste puis on retourne true car la
        // soumission du formulaire est valide.
        if (in_array($postvar["form_resubmit_identifier"], $_SESSION["form_resubmit_identifiers"])) {
            //
            $pos = array_search($postvar["form_resubmit_identifier"], $_SESSION["form_resubmit_identifiers"]);
            unset($_SESSION["form_resubmit_identifiers"][$pos]);
            //
            return true;
        }
        // A ce stade la soumission du formulaire n'est pas valide alors on
        // retourne false.
        return false;
    }

    /**
     * Gère et affiche le champ de validité de la soumission du formulaire.
     *
     * A chaque affichage de formulaire (via les méthodes formulaire et
     * sousformulaire), lorsqu'un bouton est affiché alors on insère un champ
     * caché (input de type hidden) qui contient comme valeur un identifiant
     * généré et supposé unique, puis on stocke cet identifiant dans une liste
     * dédiée dans la variable de session afin qu'il puisse être vérifié pour
     * valider ou non la soumission du formulaire.
     *
     * @return void
     */
    function form_resubmit_handle_new_identifier() {
        // Si la variable de session n'est pas initialisée alors on le fait.
        if (!isset($_SESSION["form_resubmit_identifiers"])) {
            $_SESSION["form_resubmit_identifiers"] = array();
        }
        // On génère un identifiant supposé unique
        $form_resubmit_identifier = md5(uniqid($this->get_absolute_class_name(), true));
        // On ajoute l'identifiant généré à la liste
        $_SESSION["form_resubmit_identifiers"][] = $form_resubmit_identifier;
        // On affiche le champ caché avec la valeur de l'identifiant
        printf(
            '<input name="form_resubmit_identifier" type="hidden" value="%s" />',
            $form_resubmit_identifier
        );
    }

    // }}} GESTION DE LA SOUMISSION MULTIPLE DE FORMULAIRE IMPOSSIBLE - END

    // {{{ Méthodes utilitaires de gestion des dates

    /**
     * DateDB met la date au format de la base de données.
     *
     * Transforme les dates provenant du formulaire en date pour base de
     * données. Aujourd'hui les formats acceptés en entrée sont :
     *  - J/M/AAAA
     *  - JJ/M/AAAA
     *  - J/MM/AAAA
     *  - JJ/MM/AAAA
     *  - J-M-AAAA
     *  - JJ-M-AAAA
     *  - J-MM-AAAA
     *  - JJ-MM-AAAA
     *  - AAAA-M-J
     *  - AAAA-M-JJ
     *  - AAAA-MM-J
     *  - AAAA-MM-JJ
     * et en sortie :
     *  - JJ/MM/AAAA
     *  - AAAA-MM-JJ
     *
     * @param string $val Date à vérifier et à formater.
     *
     * @return string
     */
    function dateDB($val) {

        // Si la valeur reçue est une chaîne vide alors on retourne une chaîne vide
        // XXX POurquoi ne pas retourner null ?
        if ($val == "") {
            return "";
        }

        // On détermine le séparateur de composantes utilisé dans la valeur reçue
        $separator = "";
        if (preg_match("/\//", $val) === 1) {
            $separator = "/";
        } elseif (preg_match("/-/", $val) === 1) {
            $separator = "-";
        }

        // Si aucun séparateur n'est présent dans la valeur reçue
        // Alors on retourne que la date n'est pas valide
        if ($separator === "") {
            //
            $this->addToMessage(sprintf(__("La date %s n'est pas valide."), $val));
            $this->correct = false;
            //
            return "";
        }

        // On sépare les trois composantes dans un tableau
        $elements = explode($separator, $val);

        // Si la date en entrée ne contient pas trois composantes
        // Alors on retourne que la date n'est pas valide
        if (count($elements) != 3) {
            //
            $this->addToMessage(sprintf(__("La date %s n'est pas valide."), $val));
            $this->correct = false;
            //
            return "";
        }

        // On boucle sur chaque composante pour savoir où se situe la
        // composante année
        $key_year = null;
        foreach ($elements as $key => $element) {
            // Si l'élément est une chaîne vide ou d'un autre type
            // que chaîne de caractères ou sur plus de quatre caractères
            if ($element == ""
                || !is_string($element)
                || strlen($element) > 4
                || (strlen($element) == 4
                    && $key != 0
                    && $key != 2)) {
                //
                $this->addToMessage(sprintf(__("La date %s n'est pas valide."), $val));
                $this->correct = false;
                //
                return "";
            }
            // Si l'élément est exactement de quatre caractères on suppose
            // que c'est la composante année et on peut déduire les autres
            // en fonction de sa position
            if (strlen($element) == 4) {
                //
                $key_year = $key;
                break;
            }
        }

        // On récupère chacune des composantes de la date et on en déduit le
        // format d'entrée
        if ($key_year == 0) {
            //
            $year = $elements[0];
            $month = $elements[1];
            $day = $elements[2];
            //
            $format_in = sprintf('Y%sm%sd', $separator, $separator);
        } elseif ($key_year == 2) {
            //
            $year = $elements[2];
            $month = $elements[1];
            $day = $elements[0];
            //
            $format_in = sprintf('d%sm%sY', $separator, $separator);
        }

        // Si le jour ou le mois sont une chaîne vide ou d'un autre type
        // que chaîne de caractères ou sur plus de deux caractères
        // Alors on retourne que la date n'est pas valide
        if ($key_year === null
            || $month == ""
            || $day == ""
            || !is_string($month)
            || !is_string($day)
            || (strlen($month) != 1
                && strlen($month) != 2)
            || (strlen($day) != 1
                && strlen($day) != 2)) {
            //
            $this->addToMessage(sprintf(__("La date %s n'est pas valide."), $val));
            $this->correct = false;
            //
            return "";
        }

        // Si la date n'existe pas dans le calendrier
        // Alors on retourne que la date n'est pas valide
        if (checkdate(intval($month), intval($day), intval($year)) === false) {
            //
            $this->addToMessage(sprintf(__("La date %s n'est pas valide."), $val));
            $this->correct = false;
            //
            return "";
        }

        // On initialise la date
        try {
            $date = DateTime::createFromFormat($format_in, $val);
        } catch(Exception $e){
            $date = false;
        }

        // Si la date n'a pas pus être créée
        // Alors on retourne que la date n'est pas valide
        if ($date === false) {
            //
            $this->addToMessage(sprintf(__("La date %s n'est pas valide."), $val));
            $this->correct = false;
            //
            return "";
        }

        // On retourene la date selon le format de sortie paramétré
        switch (OM_DB_FORMATDATE) {
            case "AAAA-MM-JJ" : return $date->format('Y-m-d');
            case "JJ/MM/AAAA" : return $date->format('d/m/Y');
        }

        // Si aucun des cas précédents n'a permit de retourner une valeur
        // Alors on retourne que la date n'est pas valide
        $this->addToMessage(sprintf(__("La date %s n'est pas valide."), $val));
        $this->correct = false;
        //
        return "";
    }

    /**
     * Vérifie/transforme la valeur passée en paramètre au format heure attendu
     * par la base de données.
     *
     * Exemples :
     * - "01" => "01:00:00"
     * - "01:01" => "01:01:00"
     * - "01H01" => "01:01:00"
     * - "01h01" => "01:01:00"
     * - "01:01:01" => "01:01:01"
     *
     * @param string $val Valeur à vérifier/transformer.
     *
     * @return void|string
     */
    function heureDB($val) {
        //
        $val = str_replace("H", ":", $val);
        $val = str_replace("h", ":", $val);
        //
        $heure = explode(":", $val);
        if (sizeof($heure) >= 1 or sizeof($heure) <= 3) {
            if (sizeof($heure) == 1 and $heure[0] >= 0 and $heure[0] <= 23) {
                return $heure[0].":00:00";
            }
            if (sizeof($heure) == 2 and $heure[0] >= 0 and $heure[0] <= 23 and $heure[1] >= 0 and $heure[1] <= 59) {
                return $heure[0].":".$heure[1].":00";
            }
            if (sizeof($heure) == 3 and $heure[0] >= 0 and $heure[0] <= 23 and $heure[1] >= 0 and $heure[1] <= 59 and $heure[2] >= 0 and $heure[2] <= 59) {
                return $heure[0].":".$heure[1].":".$heure[2];
            }
        }
        $this->msg .= "<br>l heure ".$val." n'est pas une heure";
        $this->correct=false;
    }

    /**
     * Retourne la date du jour au format de la base de données.
     *
     * @return string
     */
    function dateSystemeDB() {
        if (OM_DB_FORMATDATE == "AAAA-MM-JJ") {
            return date('Ymd');
        }
        if (OM_DB_FORMATDATE == "JJ/MM/AAAA") {
            return date('d/m/y');
        }
    }

    /**
     * Cette methode permet de verifier la validite d'une date et de la
     * retourner sous le format 'AAAA-MM-JJ'
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     *
     * @return mixed
     */
    function datePHP($val) {
        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour de la date au format 'AAAA-MM-JJ'
            return $date[2]."-".$date[1]."-".$date[0];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " ".__("n'est pas une date valide");
            $this->msg .= " ".__("[calcul date php]");
        }
    }

    /**
     * Cette methode permet de verifier la validite d'une date et d'en
     * retourner l'annee
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     * @return mixed
     */
    function anneePHP($val) {
        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour de l'annee
            return $date[2];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " ".__("n'est pas une date valide");
            $this->msg .= " ".__("[calcul annee php]");
        }
    }

    /**
     * Cette methode permet de verifier la validite d'une date et d'en
     * retourner le mois
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     * @return mixed
     */
    function moisPHP($val) {
        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour du mois
            return $date[0];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " ".__("n'est pas une date valide");
            $this->msg .= " ".__("[calcul mois php]");
        }
    }

    /**
     * Cette methode permet de verifier la validite d'une date et d'en
     * retourner le jour
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     * @return mixed
     */
    function jourPHP($val) {
        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour du jour
            return $date[1];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " ".__("n'est pas une date valide");
            $this->msg .= " ".__("[calcul jour php]");
        }
    }

    /**
     * Méthode pour convertir une date Y-m-d en d/m/Y
     *
     * @param string $date Date au format 'YYYY-MM-DD'.
     *
     * @return string
     */
    function dateDBToForm($date) {
        if ($date == "") {
            return "";
        }
        $dateFormat = new DateTime($date);
        return $dateFormat->format('d/m/Y');
    }

    // }}}

    // {{{ EDITIONS

    /**
     * Retourne l'édition PDF.
     *
     * @param string $type Type d'édition : 'lettretype' ou 'etat'.
     * @param string $obj
     * @param null|string $collectivite
     * @param null|string $idx
     * @param null|array $params
     *
     * @return array
     */
    function compute_pdf_output($type, $obj, $collectivite = null, $idx = null, $params = null) {
        // Initialisation de la variable de retour
        $res =  array(
            "pdf_output" => "",
            "filename" => "",
        );
        //
        if ($type == "lettretype") {
            //
            $script = "pdflettretype";
        } elseif ($type == "etat") {
            //
            $script = "pdfetat";
        } else {
            //
            return $res;
        }
        //
        $_GET['output'] = "string";
        $_GET['obj'] = $obj;
        // Paramétrage du filigrane
        if (isset($params['watermark']) && $params['watermark'] == true) {
            $_GET['watermark'] = 'true';
        }
        if (isset($params['specific'])
            && is_array($params['specific'])
            && count($params['specific']) > 0) {
            $_GET["specific"] = $params['specific'];
        }
        if (is_null($idx)) {
            $_GET['idx'] = $this->getVal($this->clePrimaire);
        } else {
            $_GET['idx'] = $idx;
        }
        //
        $om_edition = $this->f->get_inst__om_edition();
        $pdfedition = call_user_func(array($om_edition, "view_".$script), $collectivite);
        //
        return $pdfedition;
    }

    /**
     * Expose le fichier PDF à l'utilisateur.
     *
     * @param string $pdf_output PDF sous forme de chaîne de caractères.
     * @param string $filename Nom du fichier.
     *
     * @return void
     */
    function expose_pdf_output($pdf_output, $filename) {
        //
        $om_edition = $this->f->get_inst__om_edition();
        $om_edition->expose_pdf_output($pdf_output, $filename);
    }

    /**
     * Liste des champs à ne pas proposer dans les champs de fusion (niveau core).
     * @var array
     */
    var $merge_fields_to_avoid_core = array(
    );

    /**
     * Liste des champs à ne pas proposer dans les champs de fusion (niveau app).
     * @var array
     */
    var $merge_fields_to_avoid_app = array(
    );

    /**
     * Liste des champs à ne pas proposer dans les champs de fusion (niveau obj).
     * @var array
     */
    var $merge_fields_to_avoid_obj = array(
    );

    /**
     * Retourne la liste des champs à ne pas proposer dans les champs de fusion.
     *
     * @return array
     */
    function get_merge_fields_to_avoid() {
        //
        return array_merge(
            $this->merge_fields_to_avoid_core,
            $this->merge_fields_to_avoid_app,
            $this->merge_fields_to_avoid_obj
        );
    }

    /**
     * Récupération des champs de fusion pour l'édition ou l'aide à la saisie
     *
     * @param string $type 'values' ou 'labels'
     *
     * @return array
     */
    function get_merge_fields($type) {
        // selon que l'on souhaite récupérer les valeurs ou les libellés
        switch ($type) {
            case 'values':
                return $this->get_values_merge_fields();
                break;
            case 'labels':
                return $this->get_labels_merge_fields();
                break;
            default:
                return array();
                break;
        }
    }

    /**
     * Récupération des valeurs des champs de fusion
     *
     * @return array         tableau associatif
     */
    function get_values_merge_fields() {
        // récupération de la table de la classe instanciée
        $table = $this->table;
        $classe = get_class($this);
        // récupération des clés étrangères
        $foreign_keys = array();
        foreach ($this->foreign_keys_extended as $foreign_key => $values) {
            $foreign_keys[] = $foreign_key;
        }
        // initialisation du tableau de valeurs
        $values = array();
        // pour chaque champ de l'objet on crée un champ de fusion
        foreach ($this->champs as $key => $champ) {
            //
            if (in_array($champ, $this->get_merge_fields_to_avoid())) {
                continue;
            }
            // récupération de la valeur
            $value = $this->getVal($champ);
            // si c'est un booléen on remplace par oui/non
            if ($this->type[$key] == 'bool') {
                switch ($value) {
                    case 't':
                    case 'true':
                    case 1:
                        $value = __("oui");
                        break;
                    case 'f':
                    case 'false':
                    case 0:
                        $value = __("non");
                        break;
                }
            }
            // si c'est une date anglosaxonne on la formate en FR
            if (DateTime::createFromFormat('Y-m-d', $value) !== FALSE) {
                $dateFormat = new DateTime($value);
                $value = $dateFormat->format('d/m/Y');
            }
            // si c'est une clé étrangère avec une valeur valide
            // on remplace par le libellé
            if (in_array($champ, $foreign_keys)
                && $value != null && $value != '') {
                // construction variable sql
                $var_sql = $champ."_by_id";
                // si la variable existe
                $sql = $this->get_var_sql_forminc__sql($var_sql);
                if ($sql != "") {
                    // remplacement de l'id par sa valeur dans la condition
                    $sql = str_replace('<idx>', $value, $sql);
                    // exécution requete
                    $res = $this->f->db->query($sql);
                    $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                    // Si la récupération de la description de l'avis échoue
                    if ($this->f->isDatabaseError($res, true)) {
                        // Appel de la methode de recuperation des erreurs
                        $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                        $this->correct = false;
                        return false;
                    }
                    $row = &$res->fetchRow();
                    // récupération libellé
                    $value = $row[1];
                }
            }
            $values[$table.".".$champ] = $value;
        }
        return $values;
    }

    /**
     * Récupération des libellés des champs de fusion
     *
     * @return array         tableau associatif
     */
    function get_labels_merge_fields() {
        // récupération de la table de la classe instanciée
        $table = $this->table;
        // récupération du nom de la clé primaire
        $clePrimaire = __($this->clePrimaire);
        // initialisation du tableau de libellés
        $labels = array();
        // pour chaque champ de l'objet on crée un champ de fusion
        foreach ($this->champs as $key => $champ) {
            //
            if (in_array($champ, $this->get_merge_fields_to_avoid())) {
                continue;
            }
            //
            $labels[$clePrimaire][$table.".".$champ] = __($champ);
        }
        return $labels;
    }

    /**
     * Récupération des variables de remplacement pour l'édition ou l'aide à la saisie.
     *
     * @param string $type 'values' ou 'labels'
     * @param null|string $om_collectivite_idx Identiant de la collectivité.
     *
     * @return array
     */
    function get_substitution_vars($type, $om_collectivite_idx = null) {
        // selon que l'on souhaite récupérer les valeurs ou les libellés
        switch ($type) {
            case 'values':
                return $this->get_values_substitution_vars($om_collectivite_idx);
                break;
            case 'labels':
                return $this->get_labels_substitution_vars($om_collectivite_idx);
                break;
            default:
                return array();
                break;
        }
    }

    /**
     * Récupération des valeurs des champs de fusion
     *
     * @param null|string $om_collectivite_idx Identiant de la collectivité.
     *
     * @return array         tableau associatif
     */
    function get_values_substitution_vars($om_collectivite_idx = null) {
        //
        $values = array();
        //
        $prefixe = $this->f->getParameter("prefixe_edition_substitution_vars");
        if (!is_null($prefixe)) {
            //
            foreach ($this->f->getCollectivite($om_collectivite_idx) as $key => $value) {
                //
                if ($this->f->starts_with($key, $prefixe) === true) {
                    //
                    $value = str_replace("\r\n", "<br/>", $value);
                    $value = str_replace("\n", "<br/>", $value);
                    $value = str_replace("\r", "<br/>", $value);
                    $values[str_replace($prefixe, "", $key)] = $value;
                }
            }
        }
        //
        return $values;
    }

    /**
     * Récupération des libellés des champs de fusion
     *
     * @param null|string $om_collectivite_idx Identiant de la collectivité.
     *
     * @return array         tableau associatif
     */
    function get_labels_substitution_vars($om_collectivite_idx = null) {
        //
        $labels = array();
        // On ajoute les variables de remplacement standard des éditions.
        // Les values sont directement remplacées dans la classe 'om_edition'.
        $labels["divers"]["numpage"] = __("Numéro de la page");
        $labels["divers"]["nbpages"] = __("Nombre total de pages");
        //
        $prefixe = $this->f->getParameter("prefixe_edition_substitution_vars");
        if (!is_null($prefixe)) {
            //
            foreach ($this->f->getCollectivite($om_collectivite_idx) as $key => $value) {
                //
                if ($this->f->starts_with($key, $prefixe) === true) {
                    //
                    $labels["om_parametre"][str_replace($prefixe, "", $key)] = "";
                }
            }
            ksort($labels["om_parametre"]);
        }
        //
        return $labels;
    }

    /**
     * Retourne l'affichage de l'aide à la saisie des variables de remplacement.
     *
     * @return string
     */
    function get_displayed_labels_substitution_vars() {

        //
        $labels = $this->get_substitution_vars("labels");

        //
        if (count($labels) == 0) {
            return __("Aucune variable de remplacement.");
        }

        //
        $display = sprintf("<table><thead>");
        foreach ($labels as $object => $fields) {
            // header : intitulé objet
            $display .= sprintf('<tr>
                <th colspan="2">%s</th></tr></thead><tbody>',
                __($object)
            );
            // body : une ligne = un champ
            foreach ($fields as $field => $label) {
                $display .= sprintf("<tr><td>&amp;%s</td><td>%s</td></tr>",
                    $field, $label
                );
            }
            // ligne séparatrice
            $display .= sprintf('<tr style="%s"><td colspan="2"></td></tr>',
                "height: 10px !important;");
        }
        $display .= sprintf("</tbody></table>");
        return $display;
    }

    // }}}

    /**
     * Traite les valeurs postées du formulaire
     *
     * @param null|integer $maj Identifant numérique de l'action.
     *
     * @return void|boolean Dans le cas des anciennes actions, on ne retourne
     *                      aucune valeur. Dans le cas des nouvelles actions,
     *                      on retourne 'true' si la méthode de traitement s'est
     *                      bien passée et 'false' sinon.
     */
    function post_treatment($maj = null) {
        if ($maj === null) {
            $maj = $this->getParameter('maj');
        }
        // Ancienne gestion des actions
        if ($this->is_option_class_action_activated() == false) {
            switch ($maj) {
                // create
                case 0 :
                    $this->f->db->autoCommit(false);
                    if( $this->ajouter($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // update
                case 1 :
                    $this->f->db->autoCommit(false);
                    if( $this->modifier($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // delete
                case 2 :
                    $this->f->db->autoCommit(false);
                    if( $this->supprimer($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
            }
            return;
        }
        // Nouvelle gestions des actions
        if ($this->is_option_class_action_activated() == true) {
            // Récupération de la méthode de traitement
            $treatment = $this->get_action_param($maj, "method");
            // Si elle est valide
            if ($this->is_action_defined($maj) != null
                && $this->get_action_param($maj, "method") != null
                && method_exists($this, $treatment)) {
                // Désactivation de l'autocommit
                $this->f->db->autoCommit(false);
                // Execution de l'action specifique
                if ($this->$treatment($this->form->val, $this->f->db, null)) {
                    $this->f->db->commit(); // Validation des transactions
                    return true;
                } else {
                    $this->undoValidation(); // Annulation des transactions
                    return false;
                }
            }
            return;
        }
    }
}
