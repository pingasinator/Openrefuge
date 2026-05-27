<?php
/**
 * Ce script contient la définition de la classe 'table'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_table.class.php 4348 2018-07-20 16:49:26Z softime $
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
 * Définition de la classe 'table'.
 *
 * Cette classe permet de 'tabler' les champs suivant une requete.
 */
class table extends om_base {

    /**
     * Instance de connexion à la base de données.
     * @var null|database
     * @deprecated Il faut utiliser la propriété 'db' de la classe
     *             'application' : $this->f->db.
     */
    var $db = null;

    /**
     * Nom de l'objet metier
     * @var string
     */
    var $aff;

    /**
     * Critere from de la requete
     * @var string
     */
    var $table;

    /**
     * Nombre d'enregistrement(s) par page
     * @var integer
     */
    var $serie;

    /**
     * Tableau des champs a afficher
     * @var array
     */
    var $champAffiche;

    /**
     * Tableau des champs sur lesquels la recherche est effectuee
     * @var array
     */
    var $champRecherche;

    /**
     * Critere de tri de la requete
     * @var string
     */
    var $tri;

    /**
     * Clause de selection dans la requete
     * @var string
     */
    var $selection;

    /**
     * Requete
     * @var string
     */
    var $sql;

    /**
     * Requete qui compte le nombre d'enregistrement
     * @var string
     */
    var $sqlC;

    /**
     * Liste des options
     * @var array
     */
    var $options;

    /**
     * Etat de la recherche avancee : true -> activee, false -> desactivee
     *
     * Activer la recherche avancee
     * ============================
     *
     * Pour activer la recherche avancee sur un objet particulier, rendez-vous
     * dans le fichier sql/[...]/objet.inc.php de l'objet et ajoutez le tableau
     * suivant aux tableaux d'options:
     *
     * EX:
     * $options[] = (1)    array('type'            => 'search',
     *              (2)          'display'         => true,
     *              (3)          'advanced'        => $champs,
     *              (4)          'default_form'    => 'advanced',
     *              (5)          'absolute_object' => 'om_utilisateur');
     *
     * (1) 'type' : OBLIGATOIRE
     *     Permet de definir le type de l'option. Pour une recherche il faut
     *     saisir 'search'.
     *
     * (2) 'display' : OBLIGATOIRE
     *     Permet d'afficher ou non la recherche, tout en conservant sa
     *     configuration Supporte les valeurs 'true' et 'false'.
     *
     * (3) 'advanced' : OBLIGATOIRE
     *     Permet de preciser que le formulaire de recherche est un formulaire
     *     de recherche avancee. Cette cle doit contenir le tableau des champs
     *     configures pour la recherche (voir plus bas pour la configuration).
     *
     * (4) 'default_form' : optionnel
     *     Permet de choisir quel formulaire de recherche est ouvert par defaut.
     *     La valeur 'advanced' permet d'afficher le formulaire de recherche
     *     multi-criteres. Les autres valeurs ou si 'default_form' n'est pas
     *     configure, affichent le formulaire de recherche mono-critere.
     *
     * (5) 'absolute_object' : OBLIGATOIRE
     *     Permet de specifier le nom de la table en base de donnees de l'objet
     *     recherche.
     *
     * @var boolean
     */
    var $_etatRechercheAv = NULL;

    /**
     * Liste des champs configures pour le formulaire de recherche avancee.
     *
     * Cette liste sera initialisee seulement si la recherche avancee est
     * activee.
     *
     * @var array
     */
    var $dbChampRechercheAv = array();

    /**
     * Liste des attributs 'name' des champs HTML composant le formulaire
     * de recherche avancee.
     *
     * Ces valeurs sont les cles du tableau $_POST contenant les valeurs
     * a rechercher.
     *
     * Cette liste sera initialisee seulement si la recherche avancee est
     * activee.
     *
     * @var array
     */
    var $htmlChampRechercheAv = array();

    /**
     * Tableau de parametres permettant de construire le formulaire de recherche
     * avancee et d'interroger les bonnes tables et colonnes de la base de
     * donnees.
     *
     * Les cles de ce tableau sont les noms des champs du formulaire de
     * recherche.
     *
     * Les valeurs sont des tableaux associatif contenant la configuration
     * du champ a savoir : la colonne et la table dans laquel s'effectuera la
     * recherche de sa valeur, son type, sa taille, etc.
     *
     * I - Configuration simple
     * ========================
     *
     * EX:
     * array('dentifiant_utilisateur' (1) =>
     *
     *              (2)    array('colonne' => 'om_utilisateur',
     *              (3)          'table'   => 'om_utilisateur',
     *              (4)          'type'    => 'text',
     *              (5)          'libelle' => __('Identifiant'),
     *              (6)          'taille'  => 10,
     *              (7)          'max'     => 8));
     *
     * (1) 'identifiant_utilisateur' : OBLIGATOIRE
     *     Nom unique du champ HTML (attribut name du champ).
     *
     * (2) 'colonne' : OBLIGATOIRE
     *     Nom de la colonne de la base de donnees qui sera interrogee si $_POST
     *     contient la cle 'identifiant_utilisateur'.
     *
     * (3) 'table' : OBLIGATOIRE
     *     Nom de la table de la base de donnees qui sera interrogee si $_POST
     *     contient la cle 'identifiant_utilisateur'.
     *
     * (4) 'type' : OBLIGATOIRE
     *     Type du champ HTML a afficher. Cela peut-etre date, text, select,
     *     ou tout autre type supporte par la classe formulaire. Pour les champs
     *     de type select, le nom du champ HTML (1) doit etre le meme que le nom
     *     de la colonne (2). A corriger.
     *
     * (5) 'libelle' : OBLIGATOIRE
     *     Le label qui sera affiche a cote du champ dans le formulaire de
     *     recherche.
     *
     * (6) 'taille' : optionnel
     *     La taille du champ HTML (attribut size).
     *
     * (7) 'max' : optionnel
     *     La longueur maximale de la valeur du champ (attribut maxlength).
     *
     * II - Configuration avancee
     * ==========================
     *
     * 1) - Creer un intervalle de date
     * --------------------------------
     *
     *  EX: Recherche des utilisateurs crees entre telle et telle date
     *
     *      $champs['date_de_creation'] =
     *
     *              array('colonne' => 'creation_date',
     *                    'table'   => 'user',
     *                    'libelle' => __('Date de creation'),
     *                    'type'    => 'date',
     *                    'where'   => 'intervaldate');
     *
     *  Cette configuration permet de creer deux champs HTML datepicker:
     *      - date_de_creation_min : permet de saisir une date minimale
     *      - date_de_creation_max : permet de saisir une date maximale
     *
     *  Ces champs permettent de rechercher les uilisateurs dont la date de
     *  de creations est incluse dans l'intervalle saisi, bornes comprises.
     *  Il est possible de ne saisir qu'une seule date afin de rechercher
     *  les utilisateurs ayant ete crees avant ou apres une date particuliere.
     *
     *
     *
     * 2) - Creer un champ de recherche avec menu deroulant personnalise
     * -----------------------------------------------------------------
     *
     *  EX: Recherche des utilisateurs administrateurs
     *
     *  L'information se trouve directement dans la table utilisateur.
     *
     *      $champs['administrator'] =
     *
     *              array('colonne'   => 'is_admin',
     *                    'table'     => 'user',
     *                    'libelle'   => __('Administrateur'),
     *                    'type'      => 'select',
     *                    'subtype'   => 'manualselect',
     *                    'args'      => $args);
     *
     *      $args[0] = array('', 'true', 'false');
     *      $args[1] = array(__('Tous'), __('Oui'), __('Non'));
     *
     *  Cette configuration permet de creer un champ HTML de type select avec
     *  trois choix:
     *      - Tous (valeur '')
     *      - Oui (valeur 'true')
     *      - Non (valeur 'false')
     *
     *  Le tableau $args[0] contient les valeurs associes aux choix. Elles
     *  seront recherchees telles quelles dans la base de donnees.
     *
     *  En selectionnant 'Oui', la requete sera construite comme suit:
     *
     *      WHERE user.is_admin::varchar like 'true'
     *
     *  Il est possible de saisir n'importe quelle chaine de caracteres dans
     *  $args[0] et pas seulement des valeurs booleenes.
     *
     *  Attention cette recherche n'est pas sensible a la casse. Plusieurs
     *  fonction de formatage sont appelees sur 'user.is_admin' avant de
     *  tester l'egalite.
     *
     *
     *
     * 3) - Tester si une donnee est presente ou non dans un groupe de donnee
     * ----------------------------------------------------------------------
     *
     *  EX: Recherche des utilisateurs administrateurs
     *
     *  L'information se trouve non pas dans la table utilisateur mais dans la
     *  table administrateur disposant d'une colonne user_id (cle etrangere). Il
     *  faut utiliser une sous-requete pour recuperer l'ensemble des
     *  identifiants de le table administrateur afin de tester si un identifiant
     *  utilisateur est present dans cette liste.
     *
     *      $champs['administrator'] =
     *
     *              array('colonne'  => 'id',
     *                    'table'    => 'user',
     *                    'libelle'  => __('Type d'utilisateur'),
     *                    'type'     => 'select',
     *                    'subtype'  => 'manualselect',
     *                    'where'    => 'insubquery',
     *                    'args'     => $args,
     *                    'subquery' => $subquery);
     *
     *      $args[0] = array('', 'true', 'false');
     *      $args[1] = array(__('Tous'),
     *                       __('Administrateurs'),
     *                       __('Utilisateurs simples'));
     *
     *      $subquery = 'SELECT user_id FROM admin';
     *
     *  Cette configuration permet de creer un champ HTML de type select avec
     *  trois choix:
     *      - Tous (valeur '')
     *      - Administrateurs (valeur 'true')
     *      - Utilisateurs simples (valeur 'false')
     *
     *  Le tableau $args[0] contient les valeurs associes aux choix. La valeur
     *  'true' indique que les identifiants des utilisateurs doivent se
     *  trouver dans la sous-requete, la valeur 'false' indique qu'ils ne
     *  doivent pas se trouver dans la sous-requete. Contrairement a l'exemple
     *  2, elles ne seront pas recherchees telles quelles dans la base de
     *  donnees et ne doivent surtout pas etre modifiees.
     *
     *  En selectionnant 'Administrateurs', la requete sera construite comme
     *  suit:
     *
     *      WHERE user.id IN (SELECT user_id FROM admin)
     *
     *
     * @var array
     */
    var $paramChampRechercheAv = array();

    /**
     * Permet de savoir si une recherche avancee a ete effectuee.
     *
     * Ce parametre est false:
     *
     *    - par defaut
     *    - lorsque la recherche avancee n'est pas activee
     *    - lorsque la recherche avancee est activee et que les champs soumis
     *      par le formulaire de rechreche avancee sont tous vide
     *
     * @var boolean
     */
    var $_rechercheAvanceeFaite = false;

    /**
     * Nom de la classe métier pour la recherche avancée.
     * @var string
     */
    var $absolute_object = '';

    /**
     * Configuration du wildcard des recherches.
     * @var array
     */
    var $wildcard = array('left' => '%', 'right' => '%');

    /**
     * Permet de definir quel formulaire de recherche avancee est ouvert par defaut
     *
     * 'simple'  -> mono-critere
     * 'avanced' -> multi-critere
     *
     * Dans tous les autres cas, la recherche mono-critere est affichee
     *
     * @var string
     */
    var $advs_default_form = "simple";

    /**
     * Attribut contenant un identifiant unique de recherche avancee
     **/
    var $_advs_id;

    /**
     * Permet de savoir si le tableau actuel est un tableau d'objets a date de
     * validite.
     *
     * @var boolean
     */
    var $_om_validite;

    /**
     * Liste des actions accessible par l'utilisateurs.
     */
    var $actions = array();

    /**
     * Ordre des actions en coin.
     */
    var $actions_order_c = array();

    /**
     * Ordre des actions de gauche.
     */
    var $actions_order_l = array();

    /**
     * Nombre d'actions affichables en coin.
     */
    var $actions_number_c;

    /**
     * Nombre d'actions affichables a gauche.
     */
    var $actions_number_l;

    /**
     * Nombre maximum d'actions affichees.
     */
    var $max_actions_number = 0;

    /**
     * Tableau des type d'export possible pour le resultat de la recherche avancee
     **/
    var $export = NULL;

    /**
     * Constructeur.
     *
     * Cette methode permet d'affecter tous les parametres aux attributs de la
     * classe auxquels ils correspondent
     *
     * @param string $aff Nom de l'objet metier
     * @param string $table Critere from de la requete
     * @param integer $serie Nombre d'enregistrement(s) par page
     * @param array $champAffiche Tableau des champs a afficher
     * @param array $champRecherche Tableau des champs sur lesquels la
     *                              recherche est effectuee
     * @param string $tri Critere de tri de la requete
     * @param string $selection Clause de selection dans la requete
     * @param string $edition Lien vers edition pdf
     * @param array $options
     * @param null|string $advs_id
     * @param boolean $om_validite
     *
     * @return void
     */
    function __construct($aff, $table, $serie, $champAffiche, $champRecherche, $tri, $selection, $edition = "", $options = array() ,$advs_id = null, $om_validite = false) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();

        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);

        //
        $this->aff = $aff;
        $this->table = $table;
        $this->serie = $serie;
        $this->champAffiche = $champAffiche;
        $this->champRecherche = $champRecherche;
        $this->tri = $tri;
        $this->selection = $selection;
        $this->edition = $edition;
        $this->options = $options;
        $this->_advs_id = $advs_id;
        $this->_om_validite = $om_validite;

        foreach ($options as $option) {
            if ($option['type'] == 'wildcard') {
                if (isset($option['left'])) {
                    $this->wildcard['left'] = $option['left'];
                }
                if (isset($option['right'])) {
                    $this->wildcard['right'] = $option['right'];
                }
            }
            // Types d'exports possible
            if (key_exists("export", $option)) {
                $this->export = $option['export'];
            }
        }

        // Sauvegarde de la recherche effectuée
        // Si le tableau est chargée avec un formulaire posté
        // alors on sérialise les valeurs postées et on les stocke
        if ($this->f->get_submitted_post_value() !== null
            && is_array($this->f->get_submitted_post_value())
            && count($this->f->get_submitted_post_value()) > 0) {
            //
            $this->serialize_criterions($this->f->get_submitted_post_value());
        }

        /* Support de la recherche avancee */
        if ($this->isAdvancedSearchEnabled()) {
            // initialisation de la liste des colonnes de la base
            $this->dbChampRechercheAv = array_keys($this->paramChampRechercheAv);

            // initialisation de la liste des champs HTML
            $this->htmlChampRechercheAv = $this->initHtmlChampRechercheAv();
        }


        if($advs_id != null and isset($_SESSION['advs_ids'][$advs_id])) {
            $_POST=$this->unserialize_criterions();
            $this->f->set_submitted_value();
        }

        // par defaut on considere qu'aucune recherche avancee n'a ete effectuee
        $this->_rechercheAvanceeFaite = false;
    }

    /**
     * Mutateur pour la propriété 'params'.
     *
     * @param array $params
     *
     * @return void
     */
    function setParams($params = array()) {
        $this->params = $params;
    }

    /**
     * Accesseur pour la propriété 'params'.
     *
     * @param null|string $param
     *
     * @return string
     */
    function getParam($param = NULL) {
        if ($param != null and isset($this->params[$param])) {
            return $this->params[$param];
        } else {
            return "";
        }
    }

    /**
     * Mutateur pour la propriété 'params'.
     *
     * @param null|string $param
     * @param null|string $value
     *
     * @return void
     */
    function setParam($param = null, $value = null) {
        if ($param != null) {
            $this->params[$param] = $value;
        } else {
            return;
        }
    }

    /**
     * Retourne la clé numérique correspondante au nom de colonne ou au label
     * dans la propriété 'champAffiche'.
     *
     * @param string $columnname Nom de colonne ou label.
     *
     * @return string
     */
    function getKeyForColumnName($columnname = "") {
        //
        $fields = array();
        foreach ($this->champAffiche as $key => $value) {
            $tab = array();
            $tab = preg_split("/[ )]as /", $value);
            if (count($tab) > 1) {
                $fields[$key] = trim($tab[0]);
            } else {
                $fields[$key] = trim($value);
            }
        }
        //
        return array_search($columnname, $fields);
    }

    /**
     * Compose une URL.
     *
     * Le principe est d'utiliser la propriété 'params' pour tous les
     * paramètres de l'URL sauf si ils ont une valeur dans le paramètre passé.
     *
     * @param array $params Tableau de valeurs à surcharger.
     *
     * @return string
     */
    function composeURL($params = array()) {
        // aff correspond au fichier vers lesquels tous les liens vont pointer
        // (exemple : ../app/index.php?module=tab ou ../app/index.php?module=soustab) 
        $return =  $this->aff."&";
        //
        foreach ($params as $param => $value) {
            if (!array_key_exists($param, $this->params)) {
                $return .= $param."=".$params[$param]."&amp;";
            }
        }
        foreach ($this->params as $param => $value) {
            if (isset($params[$param])) {
                $return .= $param."=".$params[$param]."&amp;";
            } else {
                $return .= $param."=".$value."&amp;";
            }
        }
        if ($return != "") {
            substr($return, 0, strlen($return)-5);
        }
        //
        return $return;
    }

    /**
     * Retourne le nombre d'actions affichables pour une liste donnee.
     *
     * @param array $actions Tableau d'actions.
     *
     * @return integer
     */
    function countActions($actions) {
        $nbr_action = 0;
        foreach ($actions as $key => $conf) {
            if (!empty($conf['lien']) and $conf['lien'] != '#') {
                $nbr_action += 1;
            }
        }
        return $nbr_action;
    }

    /**
     * Compte le nombre d'actions affichables:
     * - en coin
     * - a gauche
     *
     * Une action affichable est une action ayant un lien non vide et != '#'.
     *
     * @return void
     */
    function saveActionsNumber() {
        //
        $nbr_action = 0;
        if (isset($this->actions['corner'])) {

            foreach ($this->actions['corner'] as $action => $conf) {
                if (isset($conf['lien']) and !empty($conf['lien'])
                    and $conf['lien'] != '#') {
                    $nbr_action += 1;
                }
            }
        }

        $this->actions_number_c = $nbr_action;

        $nbr_action = 0;
        if (isset($this->actions['left'])) {

            foreach ($this->actions['left'] as $action => $conf) {
                if (isset($conf['lien']) and !empty($conf['lien'])
                    and $conf['lien'] != '#') {
                    $nbr_action += 1;
                }
            }
        }

        $this->actions_number_l = $nbr_action;

        // initialisatoin du nombre maximal d'actions
        $this->max_actions_number = $this->actions_number_c;
    }

    /**
     * Initialise le tableau $this->actions avec un tableau donne.
     * $this->actions est le tableau des actions que l'utilisateur a le droit
     * d'effectuer.
     *
     * @param mixed $actions liste des actions
     */
    function saveUserActions($actions) {

        // traitement des actions en coin
        foreach($actions['corner'] as $action => $conf) {

            //
            if (isset($conf["parameters"])
                && is_array($conf["parameters"])) {
                //
                $flag_parameter = false;
                //
                foreach ($conf["parameters"] as $parameter_key => $parameter_value) {
                    //
                    if ($this->f->getParameter($parameter_key) != $parameter_value) {
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

            // verification des droits
            if (!isset($conf['rights'])
                or $this->f->isAccredited($conf['rights']['list'],
                                          $conf['rights']['operator'])) {

                // preparation du tri
                if (!isset($conf['ordre']) or empty($conf['ordre'])) {

                    $this->actions_order_c[] = array($action);
                } else if (!key_exists($conf['ordre'],
                                       $this->actions_order_c)) {

                    $this->actions_order_c[$conf['ordre']] = array($action);
                } else {

                    $this->actions_order_c[$conf['ordre']][] = $action;
                    asort($this->actions_order_c[$conf['ordre']]);
                }

                // enregistrement de l'action
                $this->actions['corner'][$action] = $conf;
            }
        }

        // tri
        ksort($this->actions_order_c);

        // traitement des actions a gauche
        foreach($actions['left'] as $action => $conf) {

            //
            if (isset($conf["parameters"])
                && is_array($conf["parameters"])) {
                //
                $flag_parameter = false;
                //
                foreach ($conf["parameters"] as $parameter_key => $parameter_value) {
                    //
                    if ($this->f->getParameter($parameter_key) != $parameter_value) {
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

            // verification des droits
            if (!isset($conf['rights'])
                or $this->f->isAccredited($conf['rights']['list'],
                                          $conf['rights']['operator'])) {

                // preparation du tri
                if (!isset($conf['ordre']) or empty($conf['ordre'])) {

                    $this->actions_order_l[] = array($action);
                } else if (!key_exists($conf['ordre'],
                                       $this->actions_order_l)) {

                    $this->actions_order_l[$conf['ordre']] = array($action);
                } else {

                    array_push($this->actions_order_l[$conf['ordre']], $action);
                    asort($this->actions_order_l[$conf['ordre']]);
                }

                // enregistrement de l'action
                $this->actions['left'][$action] = $conf;
            }
        }
        //
        if (!isset($this->actions["left"])) {
            $this->actions["left"] = array();
        }

        // tri
        ksort($this->actions_order_l);

        // traitement de l'action de contenu
        if (!isset($actions['content']['rights'])
            or $this->f->isAccredited($actions['content']['rights']['list'],
                                $actions['content']['rights']['operator'])) {

            $this->actions['content'] = $actions['content'];
        }

        // traitement des actions de contenu specifique
        foreach($actions['specific_content'] as $column => $conf) {

            //
            if (isset($conf["parameters"])
                && is_array($conf["parameters"])) {
                //
                $flag_parameter = false;
                //
                foreach ($conf["parameters"] as $parameter_key => $parameter_value) {
                    //
                    if ($this->f->getParameter($parameter_key) != $parameter_value) {
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

            // verification des droits
            if (!isset($conf['rights'])
                or $this->f->isAccredited($conf['rights']['list'],
                                          $conf['rights']['operator'])) {

                // enregistrement de l'action
                $this->actions['specific_content'][$column] = $conf;
            }
        }
    }

    /**
     * Retourne la liste des colonnes de la ressource de base de données
     * (résultat de requête) passée en paramètre.
     *
     * @param resource $resource Ressource de base de données (résultat de
     *                           requête).
     *
     * @return array
     */
    function getColumnsName($resource = null) {
        //
        if ($resource == null) {
            return;
        }
        //
        $info = $resource->tableInfo();
        // Logger
        $this->addToLog(__METHOD__."(): ".print_r($info, true), EXTRA_VERBOSE_MODE);
        //
        return $info;
    }

    /**
     * Affichage principal.
     *
     * @param array $params
     * @param array $actions
     * @param mixed $db
     * @param string $class Prefixe de la classe CSS a utiliser
     * @param boolean $onglet
     *
     * @return void
     */
    public function display($params = array(), $actions = array(), $db = NULL, $class = "tab", $onglet = false) {
        //
        $this->displayGlobalContainerStart();
        //
        $this->displayToolbarContainerStart();
        //
        $this->saveUserActions($actions);
        $this->saveActionsNumber();

        //
        $this->db = $db;

        //
        $this->setParams($params);
        $this->setParam("style", $class);
        $this->setParam("onglet", $onglet);

        // Construction de la recherche
        $this->composeSearchTab();

        // En mode onglet, on n'affiche pas le formulaire de recherche
        if ($onglet == false) {
            //
            $display_search = true;
            foreach($this->options as $option) {
                if ($option["type"] == "search") {
                    $display_search = $option["display"];
                }
            }
            //
            if ($display_search) {
                //
                $this->displaySearchContainerStart();
                //
                $this->displaySearch();
                //
                $this->displaySearchContainerEnd();
            }
        }

        /**
         * GLOBAL ACTIONS
         */
        //
        $this->process_global_action_validity();
        // En mode onglet, on n'affiche pas les lien vers les actions
        if ($onglet == false) {
            //
            $this->process_global_action_edition();
            $this->process_global_action_export();
        }
        //
        $this->display_global_actions();

        // Construction de la requete
        $this->composeQuery();

        // Initialisation de la variable du nombre total de resultats de la requete
        $nbligne = -1;

        // Execution de la requete a partir de l'enregistrement $premier avec
        // la limite $this->serie
        $res = $db->limitquery($this->sql, intval($this->getParam("premier")), $this->serie);
        // Logger
        $this->addToLog(__METHOD__."(): db->limitquery(\"".$this->sql."\", ".intval($this->getParam("premier")).", ".$this->serie.");", VERBOSE_MODE);

        // Verification d'erreur sur le resultat de la requete
        $this->f->isDatabaseError($res);

        // Recuperation des infos sur la table
        // ( oracle: recuperation immediate  (en dynamique) )
        $info = $this->getColumnsName($res);

        //
        $display_pagination = true;
        foreach($this->options as $option) {
            if ($option["type"] == "pagination") {
                $display_pagination = $option["display"];
            }
        }
        if ($display_pagination) {

            // Calcul du nombre total de resultats si on detecte un
            // group by dans la requete
            // et qu'une recherche avancée n'est pas faite
            if (preg_match("/group by/i", $this->tri) == 1
                && !$this->_rechercheAvanceeFaite) {
                //On ajoute le GROUP BY a la requête
                $sqlG = $this->sqlC.$this->tri;
                $res1 = $db->query($sqlG);
                // Logger
                $this->addToLog(__METHOD__."(): db->query(\"".$sqlG."\");", VERBOSE_MODE);
                // Compte le nombre de ligne de résultat
                $nbligne = $res1->numRows();
            } else {
                // Sinon on intègre pas le tri à la requête
                $nbligne = $db->getOne($this->sqlC);
                // Logger
                $this->addToLog(__METHOD__."(): db->getone(\"".$this->sqlC."\");", VERBOSE_MODE);
            }

            // Affichage de la pagination
            echo "<!-- tab-pagination -->\n";
            $this->displayPagination($nbligne, $class, $onglet);
        }

        //
        $this->displayToolbarContainerEnd();
        //
        $this->displayTableContainerStart();
        //
        $this->displayTableStart($class);

        // Temporisation de l'affichage du contenu de la table
        ob_start();

        // Affichage de la ligne d'entete du tableau
        // false car tab non dynamique (pas d'onglet)
        $this->displayHeader($info, $class, $onglet);

        // Calcul du nombre de colonnes
        $nbchamp = count($info);

        // Gestion d'une classe css differente une ligne sur deux
        $odd = 0;

        // Affichage des lignes de tableaux
        echo "\t<!-- tab-data -->\n";
        echo "\t<tbody>\n";

        // Si aucun resultat, on affiche une ligne avec un message
        // indiquant qu'il n'y a aucun enregistrement
        if ($nbligne == 0) {

            $colspan = $nbchamp;
            if ($this->actions_number_c > 0) {
                $colspan += 1;
            }

            echo "\t<tr class=\"".$class."-data empty\">\n";
            echo "<td colspan=\"".$colspan."\">";
            echo __("Aucun enregistrement.");
            echo "</td>";
            echo "\t</tr>\n";

        }

        // On différencie le tab_actions par défaut de celui de la
        // ligne pour qu'il ne s'incrémente pas à chaque résultat.
        $tab_actions_default = $this->actions["left"];
        $tab_actions_default_order = $this->actions_order_l;
        $tab_actions_default_number = $this->actions_number_l;

        // Boucle sur les resultats de la requete
        while ($row =& $res->fetchRow()) {

            // Gestion des options
            $option_style = "";
            $option_href = false;
            $option_tab_action = false;

            // pour chaque option
            foreach($this->options as $option) {

                // s'il s'agit d'une conditon
                if ($option["type"] == "condition") {

                    // pour chaque cas
                    foreach($option["case"] as $case) {

                        if (isset($row[$this->getKeyForColumnName($option["field"])])
                            and in_array($row[$this->getKeyForColumnName($option["field"])], $case["values"])) {

                            $option_style .= (isset($case["style"]) ? " ".$case["style"] : "");

                            if (isset($case["href"])) {

                                $option_href = array($case["href"]);
                                // case['href'][1] : action de contenu
                                if (key_exists(1, $case["href"])) {
                                    $this->actions['content'] = $case['href'][1];
                                }
                            }

                            // Si on rencontre le cas tab_action on mémorise l'option
                            // qui sera toujours left puisque destinée au tableau
                            if (isset($case["tab_actions"])) {

                                // Si c'est la première condition on crée l'option
                                if ($option_tab_action == false) {
                                    $option_tab_action = $case["tab_actions"]["left"];
                                } else { // sinon on la fusionne
                                    $option_tab_action = array_merge($option_tab_action, $case["tab_actions"]["left"]);
                                }
                            }
                        }
                    }
                }
            }

            // si au moins un tab action est défini
            if ($option_tab_action != false) {

                // on l'(les) ajoute à la fin du tableau d'actions
                $this->actions["left"] =
                array_merge($this->actions["left"], $option_tab_action);

                // on l'(les) ajoute à la fin de liste ordonnée des actions
                foreach ($option_tab_action as $action_title => $action_properties) {

                    $this->actions_order_l[strval($action_properties["ordre"])] = array(0 => $action_title);
                    // On incrémente le nombre d'actions pour la largeur de la colonne
                    $this->actions_number_l ++;
                }

                // On crée un nouveau tableau dont la clé et la valeur
                // sont l'ordre de l'action.
                $action_array = array();
                foreach ($this->actions_order_l as $action_order => $value)
                {
                    $action_array[$action_order] = $action_order;
                }

                // Cela nous permet de trier la liste ordonnée par la valeur de l'ordre
                // (nécessaire car la liste est associative : l'ordre est un string).
                array_multisort($action_array, SORT_ASC, $this->actions_order_l);
            }

            // si un href est défini
            if ($option_href != false) {

                // on retient le nouveau tableau d'actions
                $links = $option_href;

                // on retient le nombre d'actions
                $nbr_actions_l = $this->countActions($option_href[0]);

            } else {

                // on retient le nombre d'actions
                $nbr_actions_l = $this->actions_number_l;

                // On récupère l'ordre des actions prédéfinies
                $links = $this->actions_order_l;
            }

            // on met a jour le nombre maximal d'actions
            if ($nbr_actions_l > $this->max_actions_number) {
                $this->max_actions_number = $nbr_actions_l;
            }

            // Affichage d'une ligne de tableau
            echo "\t\t<tr";
            echo " class=\"";
            echo $class."-data";
            echo $option_style;
            echo " ".($odd % 2 == 0 ? "odd" : "even");
            echo "\">\n";

            // Gestion d'une classe css differente une ligne sur deux
            $odd += 1;

            //
            if ($this->actions_number_c > 0
                or $this->actions_number_l > 0) {

                echo "\t\t\t<td class=\"icons\">";
                echo "&nbsp;";
            }

            // Affichage des actions a gauche
            foreach ($links as $order => $actions) {

                foreach($actions as $key => $action) {

                    // dans le cas ou une condition est satisfaite
                    if (is_array($action)) {
                        $conf = $action;

                    // dans tous les autres cas
                    } else {
                        $conf = $this->actions['left'][$action];
                    }

                    if (isset($conf['lien']) and
                        $conf['lien'] != '' and $conf['lien'] != '#'
                        and (isset($conf['lib']) and $conf['lib'] != '')) {

                        /* la fonction ajaxIt n'est pas appelee si l'action
                           est defini a la maniere openmairie < 4.3.0 ou
                           qu'il est explicitement dit de ne pas utiliser
                           d'ajax */

                        $no_ajax = false;
                        if (strpos((string)$key, 'retro_') !== false or
                            (isset($conf['ajax'])) and $conf['ajax'] == false) {

                            $no_ajax = true;
                        }
                        // debut lien 1ere colonne table : consulter ...
                        //
                        //*
                        $action_id = "action-";
                        if($onglet === false) {
                            $action_id .= "tab-";
                        } else {
                            $action_id .= "soustab-";
                        }
                        $action_id .= $this->getParam("obj")."-left-".$action."-".urlencode($row[0]);
                        $params = array(
                               "onglet" => $onglet,
                               "no_ajax" => $no_ajax,
                               "lien" => (isset($conf['lien']) ? $conf['lien'] : ""),
                               "row" => $row[0],
                               "id" => (isset($conf['id']) ? $conf['id'] : ""),
                               "obj" => $this->getParam("obj"),
                               "lib" => (isset($conf['lib']) ? $conf['lib'] : ""),
                               "identifier" => $action_id
                        );
                        if (isset($conf['target'])) {
                            $params["target"] = $conf['target'];
                        }

                        if (isset($conf['type']) === true && $conf['type'] === 'action-direct') {
                            $params["class"] = 'action-direct';
                        }
                        $this->f->layout->display_table_lien_data_colonne_une($params);
                    }
                }
            }

            // Récupération des tab_actions par défaut
            $this->actions["left"] = $tab_actions_default;
            $this->actions_order_l = $tab_actions_default_order;
            $this->actions_number_l = $tab_actions_default_number;

            if ($this->actions_number_c > 0
                or $this->actions_number_l > 0) {
                echo "</td>\n";
            }

            // pour chaque colonne du tableau
            foreach ($row as $key => $elem) {

                // gestion des actions de contenu
                $content = '';

                // affichage de l'action specifique si elle existe
                if (isset($this->actions['specific_content'][$key])) {
                    $content = $this->actions['specific_content'][$key];

                // sinon affichage de l'action de contenu si elle existe
                } else if (isset($this->actions['content'])) {
                    $content = $this->actions['content'];
                }

                // affichage de la cellule
                echo "\t\t\t<td ";
                echo "class=\"col-".$key."";

                // gestion des classes
                if ($key == 0) {
                    echo " firstcol";
                }

                if ($key == count($row)-1) {
                    echo " lastcol";
                }

                if (is_numeric($elem)) {
                    echo " right";
                }

                echo "\"";
                echo ">";

                // affichage d'un lien mailto si on detecte une adresse mail valide
                if ($this->f->checkValidEmailAddress($elem)) {
                    echo "<a href='mailto:".$elem."' ";
                    echo "title=\"".__("Envoyer un mail a cette adresse")."\">";
                    echo "<span class=\"ui-icon ui-icon-mail-closed\">&nbsp;</span>";
                    echo "</a>";
                }

                // si une action de contenu existe
                if (!empty($content) and isset($content['lien'])
                    and $content['lien'] != ''
                    and $content['lien'] != '#') {

                    if (!isset($content['id'])) {
                        $content['id'] = "";
                    }

                    /* la fonction ajaxIt n'est pas appelee si l'action
                       est defini a la maniere openmairie < 4.3.0 ou
                       qu'il est explicitement dit de ne pas utiliser
                       d'ajax */

                    $no_ajax = false;
                    if (isset($content['ajax']) and $content['ajax'] == false) {

                        $no_ajax = true;
                    }

                    // affichage du correspondant a l'action
                    echo "<a class='lienTable' ";

                    if ($onglet == false or $no_ajax === true) {
                        echo "href=\"".$content['lien'].
                             urlencode($row[0]).$content['id']."\"";
                    } else {
                        echo "href=\"";
                        echo "#";
                        echo "\" ";
                        echo " onclick=\"ajaxIt('";
                        echo  $this->getParam("obj")."','";
                        echo $content['lien'].urlencode($row[0]);
                        echo $content['id'];
                        echo "');\"";
                    }

                    echo ">";
                    echo $elem;
                    echo "</a>";
                } else {
                    echo $elem;
                }
                echo "</td>\n";
            }

            // Fermeture de la balise ligne de tableau
            echo "\t\t</tr>\n";
        }

        // Recuperation du contenu de la table
        $output = ob_get_clean();

        // Ajout du nombre d'actions a la classe actions-max-
        $output = str_replace('actions-max-',
                              'actions-max-'.$this->max_actions_number,
                              $output);

        // Affichage du contenu de la table
        echo $output;

        // Fermeture de la balise tbody
        echo "\t</tbody>\n";

        // Fermeture de la balise table
        echo "</table>\n";

        // Libere le resultat de la requete
        $res->free();
        //
        $this->displayTableContainerEnd();
        //
        $this->displayGlobalContainerEnd();
    }

    /**
     * Calcule et affiche la ligne d'entête du tableau.
     *
     * @param array $info
     * @param string $class Prefixe de la classe CSS a utiliser
     * @param boolean $onglet
     *
     * @return void
     */
    function displayHeader($info, $class = "tab", $onglet = false) {

        // Affichage de l'entete
        echo "\t<!-- tab-head -->\n";
        echo "\t<thead>\n";

        // Ouverture de la ligne
        $this->displaytableHeadLineStart($class);

        if ($this->actions_number_c > 0) {

            echo "\t\t\t<th class=\"icons actions-max-\">";

            foreach ($this->actions_order_c as $order => $actions) {

                foreach($actions as $key => $action) {

                    $conf = $this->actions['corner'][$action];
                    //
                    //bdebut lien - 1ere colonne entete
                    //
                    $action_id = "action-";
                    if($onglet === false) {
                        $action_id .= "tab-";
                    } else {
                        $action_id .= "soustab-";
                    }
                    $action_id .= $this->getParam("obj")."-corner-".$action;
                    $param = array(
                                       "onglet" => $onglet,
                                       "lien" => $conf['lien'],
                                       "id" => $conf['id'],
                                       "obj" => $this->getParam("obj"),
                                       "lib" => $conf['lib'],
                                       "identifier" => $action_id
                                    );
                    $this->f->layout->display_table_lien_entete_colonne_une($param);
                }
            }

            echo "</th>\n";
        } elseif ($this->actions_number_l > 0) {
            echo "\t\t\t<th class=\"actions-max-\">&nbsp;</th>\n";
        }

        //
        $i = 0;

        // Formatage des tableaux contenant les parametres de recherche pour
        // permettre d'afficher la loupe sur les champs en question
        $lowerChampRecherche = array();
        foreach ($this->champRecherche as $key => $val) {
            $lowerChampRecherche[$key] = trim(mb_strtolower($val, HTTPCHARSET));
        }
        $lowerSearchTabDisplay = array();
        foreach ($this->searchTab["display"] as $key => $val) {
            $lowerSearchTabDisplay[$key] = trim(mb_strtolower($val, HTTPCHARSET));
        }

        // Nom des champs en entete de colonne
        foreach ($info as $key => $elem) {

            // Fleche sur colonne triee
            // Si tricol =  1, on veut trier la colonne 1 par ordre croissant
            // Si tricol = -1, on veut trier la colonne 1 par ordre decroissant
            if ((string) $this->getParam("tricol") !== "") {

                if (abs((int) $this->getParam("tricol")) == $key) {

                    // tri croissant
                    if (substr((string) $this->getParam("tricol"), 0, 1) !== "-") {
                        $fleche_tri="<span class=\"ui-icon ui-icon-triangle-1-s\"><!-- --></span> ";

                    // tri decroissant
                    } elseif (substr((string) $this->getParam("tricol"), 0, 1) === "-") {
                        $fleche_tri="<span class=\"ui-icon ui-icon-triangle-1-n\"><!-- --></span> ";

                    // aucun tri
                    } else {
                        $fleche_tri="<span class=\"ui-icon ui-icon-triangle-1-e\"><!-- --></span> ";
                    }

                // aucun tri
                } else {
                    $fleche_tri="<span class=\"ui-icon ui-icon-triangle-1-e\"><!-- --></span> ";
                }

            // aucun tri
            } else {
                $fleche_tri="<span class=\"ui-icon ui-icon-triangle-1-e\"><!-- --></span> ";
            }
            //

             $param = array(
                        "key" => $key,
                        "info" => $info
                 );
            $this->f->layout->display_table_cellule_entete_colonnes($param);
            //
            echo "<span class=\"name\">";
            //
            echo "<a href=\"";
            //
            if ($onglet) {
                echo "#";
                echo "\"";
                echo " onclick=\"ajaxIt('".$this->getParam("obj")."','";
            }

            //
            if ((string) $this->getParam("tricol") !== "") {

                $tricolParam = $this->getParam("tricol");
                if (abs((int) $tricolParam) == $key) {

                    // si le tri est actuellement croissant
                    if (substr((string) $tricolParam, 0, 1) !== "-") {
                        $params = array("tricol" => "-".$key);

                    // si le tri est actuellement decroissant
                    } elseif (substr((string) $tricolParam, 0, 1) === "-") {
                        $params = array("tricol" => "");
                    }

                } else {
                    $params = array("tricol" => $key);
                }

            } else {
                $params = array("tricol" => $key);
            }

            echo $this->composeURL($params);
            //
            if ($onglet) {
                echo "');";
            }
            echo "\"";
            echo ">";
            echo $fleche_tri;
            echo $elem['name'];
            echo "</a>";
             //
            echo "</span>";
            // Affichage d'une icone de loupe sur les colonnes ou une recherche
            // est possible
            if (in_array($elem['name'], $lowerChampRecherche) or
                in_array($elem['name'], $lowerSearchTabDisplay) or
                in_array("\"".$elem['name']."\"", $lowerSearchTabDisplay) or
                in_array("*".$elem['name'], $lowerChampRecherche) or
                in_array("*\"".$elem['name']."\"", $lowerChampRecherche)) {
                echo "&nbsp;";
                $this->f->layout->display_icon("search",
                                               __("Recherche possible sur ce champ"), __("Recherche possible sur ce champ"));
            }
            //
            echo "</th>\n";

        }

        //
        $this->displaytableHeadLineEnd();

        // Fermeture de la balise thead
        echo "\t</thead>\n";
    }

    /**
     * Gestion de la recherche.
     *
     * @return void
     */
    function composeSearchTab() {
        //
        $this->searchTab = array(
            "display" => array(),
            "query" => array(),
        );

        /* Par defaut on construit la clause where de recherche sera
           construite avec les champs de recherche non avancees */

        $champs_cibles = $this->champRecherche;


        /* Si la recherche avancee est active et a ete utilisee */
        if ($this->isAdvancedSearchEnabled() and
            $this->f->get_submitted_post_value("advanced-search-submit") !== null) {

            $champs_cibles = $this->getPostedChampRechercheAv(false);

            /* Aucune recherche ne sera faite dans le cas ou
               l'utilisateur ne remplit aucun champ */
            if (empty($champs_cibles)) {
                $this->_rechercheAvanceeFaite = false;
            } else {
                $this->_rechercheAvanceeFaite = true;
            }
        }

        //
        foreach($champs_cibles as $key => $elem) {
            //
            if (!preg_match("/\*/", $elem)) {
                //
                $tab = preg_split("/[ )]as /", $elem);
                //
                if (count($tab) > 1) {
                    $display = $tab[1];
                    $query = $tab[0];
                } else {
                    $display = $query = $elem;
                }
                //
                array_push($this->searchTab["display"], str_replace("\"", "", $display));
                array_push($this->searchTab["query"], str_replace("\"", "", $query));
            }
        }
    }

    /**
     * Methode permettant l'affichage de la recherche
     *
     * Cette methode permet d'afficher le formulaire de recherche
     *
     * @param string $style Prefixe de la classe CSS a utiliser
     * @return void
     */
    function displaySearch($style = "tab") {

        /* Si la recherche avancee est activee, on affiche le formulaire
           avancee. Le formulaire de rechreche classique nest pas affiche, et la
           fonction se termine. */

        if ($this->isAdvancedSearchEnabled()) {
            $this->displayAdvancedSearch();
            return NULL;
        }

        /**
         * Formulaire de recherche classique : champ recherche + filtre sur la colonne.
         */
        $form_action = $this->composeURL(array(
            "validation" => 0,
            "premier" => 0,
            "advs_id"=> $this->gen_advs_id(),
        ));
        $params = array(
            "style" => $style,
            "form_action" => $form_action,
            "search" => $this->f->get_submitted_post_value("recherche"),
            "column_search" => $this->searchTab["display"],
            "column_search_selected_key" => $this->f->get_submitted_post_value("selectioncol"),
        );
        $this->f->layout->display_table_search_simple($params);
    }

    /**
     * Calcule et affiche le bloc de pagination.
     *
     * @param integer $nbligne
     * @param string $style Prefixe de la classe CSS a utiliser
     * @param boolean $onglet
     *
     * @return void
     */
    function displayPagination($nbligne, $style = "tab", $onglet = false) {

        // Initialisation des variables
        $previous = "";
        $next = "";
        // Valeur du dernier enregistrement pour l'utilisateur
        $dernieraffiche = $this->getParam("premier") + $this->serie;
        // Controle du dernier affiche
        if ($dernieraffiche > $nbligne) {
            $dernieraffiche = $nbligne;
        }
        // Valeur du premier enregistrement pour l'utilisateur
        $premieraffiche = $this->getParam("premier") + 1;
        // Si il y a une page precedente
        if ($this->getParam("premier") > 0) {
            // Calcul du premier enregistrement de la page precedente
            $precedent = $this->getParam("premier") - $this->serie;
            // Composition du lien
            $params = array("premier" => $precedent);
            $previous = $this->composeURL($params);
        }
        // Si il y a une page suivante
        if ($this->getParam("premier") + $this->serie < $nbligne) {
            // Calcul du premier enregistrement de la page suivante
            $suivant = $this->getParam("premier") + $this->serie;
            // Composition du lien
            $params = array("premier" => $suivant);
            $next = $this->composeURL($params);
        }

        // PAGINATION SELECT
        $pagination_select = array("active" => true);
        foreach($this->options as $option) {
            if ($option["type"] == "pagination_select") {
                $pagination_select = array("active" => $option["display"]);
            }
        }
        //
        if ($pagination_select["active"] == true) {
            // Affichage de la pagination dans un select
            // Si plus d'une page
            if ($nbligne > $this->serie) {
                // Calcul du nombre de page
                if (($nbligne % $this->serie) == 0) {
                    $nbpage = (int)($nbligne / $this->serie);
                } else {
                    $nbpage = (int)($nbligne / $this->serie) + 1;
                }
                $pagination_select["page_number"] = $nbpage;
                $pagination_select["serie"] = $this->serie;
                $pagination_select["premier"] = $this->getParam("premier");
                $params = array("premier" => '\'+this.value+\'');
                $pagination_select["link"] = $this->composeURL($params);
            } else {
                $pagination_select["active"] = false;
            }
        }

        // Affichage de la pagination
        $params = array(
            "obj" => $this->getParam("obj"),
            //
            "style" => $style,
            "onglet" => $onglet,
            //
            "serie" => $this->serie,
            "first" => $premieraffiche,
            "last" => $dernieraffiche,
            "total" => $nbligne,
            "search" => $this->f->get_submitted_post_value("recherche"),
            "previous" => $previous,
            "next" => $next,
            //
            "pagination_select" => $pagination_select,
        );
        $this->f->layout->display_table_pagination($params);
    }

    /**
     * Méthode de composition de la liste des champs du SELECT
     *
     * @return string
     */
    function composeChamp() {
        // Concatenation des champAffiche avec des virgules pour composer la
        // clause SELECT
        $champ = "";
        foreach ($this->champAffiche as $elem) {
            $champ = $champ."".$elem.",";
        }
        $champ = substr($champ, 0, strlen($champ) - 1);
        return $champ;
    }

    /**
     * Méthode de composition du tri
     *
     * @return string
     */
    function composeTri() {
        $tri = $this->tri;
        if ((string) $this->getParam("tricol") !== ""
            and isset($this->champAffiche[abs((int) $this->getParam("tricol"))])) {

            // Tricol est la cle du tableau de champ, il faut recuperer le
            // champ pour l'integrer dans la requete on verifie si un 'as' est
            // present pour s'en servir
            $tricol = $this->champAffiche[abs((int) $this->getParam("tricol"))];
            $tab = preg_split("/[ )]as /", $tricol);

            if (count($tab) > 1) {

                /* Permet de determiner si la colonne a trier est une date.
                   Si $tab[0] contient DD/MM/YYYY, c'est une date.
                   Fonctionne actuellement qu'avec PostgresSQL. */

                if (strpos($tab[0], "'DD/MM/YYYY'")) {
                    // si c'est une date, on recupere le premier parametre
                    // de la fonction to_char
                    $tricol = str_replace("to_char(", "", $tab[0]);
                    $tricol = trim(str_replace(",'DD/MM/YYYY')", "", $tricol));
                } else {
                    // dans le cas d'un champ non-date, on recupere table.colonne
                    $tricol = $tab[0];
                }

                // seul un signe "-" en début de paramètre de tri effectue un tri
                // par ordre décroissant
                if (substr((string) $this->getParam("tricol"), 0, 1) !== "-") {

                    // tri croissant, nulls en dernier
                    if (OM_DB_PHPTYPE == 'pgsql') {
                        $tricol .= " ASC NULLS LAST";
                    } else {
                        $tricol = ' ISNULL('.$tricol.') ASC, '.$tricol.' ASC';
                    }
                } else {

                    // tri decroissant, nulls en dernier
                    if (OM_DB_PHPTYPE == 'pgsql') {
                        $tricol .= " DESC NULLS LAST";
                    } else {
                        $tricol = ' ISNULL('.$tricol.') ASC, '.$tricol.' DESC';
                    }
                }
            }

            // Si $tri n'est pas vide alors il faut inserer le nouveau champ
            // de tri en premier dans le ORDER BY
            if ($tri) {
                $tri = str_replace("order by ", "ORDER BY ".$tricol.",",
                                   strtolower($tri));
            } else {
                $tri = " order by ".$tricol."";
            }

        }
        return $tri;
    }

    /**
     * Méthode permettant de retraiter la chaîne recherchée
     * en intégrant les valeurs multiples séparées par une ','
     * ainsi que l'échappement des caractères interdits
     *
     * @param string $value Chaîne recherchée.
     *
     * @return string
     */
    function composeSearchValue($value) {
        // Caractère permettant de séparer les valeurs multiples
        $separator = ',';
        // en cas de valeurs multiples on cherche chacune des valeurs
        // la syntaxe du LIKE est alors la suivante :
        // champ LIKE ANY ( VALUES ('VAL1'), (VAL2))
        if ( is_numeric(strpos($value, $separator) ) ){
            // initialisation de la variable retour
            $returnValue=' ANY ( VALUES ';
            // Décomposition des éléments
            $values = explode($separator, $value);
            foreach($values as $val){
                // normalisation de la chaîne
                $val = $this->normalizeSearchValue($val);
                // Ajout des parenthèses pour la composition du groupe de valeurs
                $returnValue .= "(".$val.") , ";
            }
            // Suppression du dernier séparateur et fin du champ
            $value = substr($returnValue, 0,strlen($returnValue)-2).')';
        } else {
            $value = $this->normalizeSearchValue($value);
        }
        return $value;
    }

    /**
     * Méthode permettant de faire les traitements d'échappements
     * et de normalisation sur une chaîne destinée à la recherche
     *
     * @param string $value Chaîne recherchée à normaliser.
     *
     * @return string
     */
    function normalizeSearchValue($value){
        // gestion du caractere joker '*' en debut de chaine
        $value = str_replace('*', '%', $value);
        // 
        $value = html_entity_decode($value, ENT_QUOTES);
        // échappement des caractères spéciaux
        if (!get_magic_quotes_gpc()) {
            $value=pg_escape_string($value);
        }
        // wildcards
        $value = "'".$this->wildcard['left'].$value.$this->wildcard['right']."'";
        // encodage
        if (DBCHARSET != 'UTF8' and HTTPCHARSET == 'UTF-8') {
            $value = utf8_decode($value);
        }
        // normalisation des caractères
        $value = " translate(lower(".$value."::varchar),'àáâãäçèéêëìíîïñòóôõöùúûüýÿ','aaaaaceeeeiiiinooooouuuuyy') ";
        return $value;
    }

    /**
     * Méthode permettant de normaliser les valeurs des champs de recherche
     * en vue de les comparer aux valeurs recherchées
     *
     * @param string $searchField Champ de recherche à normaliser.
     *
     * @return string
     */
    function normalizeFieldValue($searchField){
        return " translate(lower(".$searchField."::varchar),'àáâãäçèéêëìíîïñòóôõöùúûüýÿ','aaaaaceeeeiiiinooooouuuuyy') ";
    }

    /**
     * Composition et stockage de la requête dans le cas d'une recherche simple.
     *
     * @param string $champ Critère SELECT de la requête.
     * @param string $tri Critère TRI de la requête.
     *
     * @return void
     */
    function composeSimpleSearch($champ, $tri) {
        // préparation du critère de filtrage
        // sur le champ sélectionné ou tous les champs
        //
        $sqlw = $this->selection;
        if ($this->f->get_submitted_post_value("recherche") == '') {
            // pas de valeur dans le champ de recherche
            // on prend $selection en critère par défaut
            $this->addToLog(__METHOD__."(): no field", EXTRA_VERBOSE_MODE);
        } elseif (isset($this->searchTab["query"][$this->f->get_submitted_post_value("selectioncol")])) {
            $this->addToLog(__METHOD__."(): mono field", EXTRA_VERBOSE_MODE);
            // on a un champ sélectionné
            $champrecherche = $this->searchTab["query"][$this->f->get_submitted_post_value("selectioncol")];
            // Teste si un critère de sélection avait été renseigné
            $sqlw .= (($sqlw=="")?"WHERE ":" AND ");
            $sqlw .= $this->normalizeFieldValue($champrecherche);
            $sqlw .= " LIKE ".$this->composeSearchValue($this->f->get_submitted_post_value("recherche"));
        } else {
            $this->addToLog(__METHOD__."(): multi-fields", EXTRA_VERBOSE_MODE);
            //
            if (count($this->searchTab["query"]) != 0) {
                // pas de champ sélectionné : on recherche dans tous les champs disponibles
                // Teste si un critère de sélection avait été renseigné
                $sqlw .= (($sqlw=="")?"WHERE ":" AND ")."(";
                // on crée une recherche OR pour chaque champ de recherche
                foreach ($this->searchTab["query"] as $searchField) {
                    $sqlw .= $this->normalizeFieldValue($searchField);
                    $sqlw .= " LIKE ".$this->composeSearchValue($this->f->get_submitted_post_value("recherche"));
                    $sqlw .= " OR ";
                }
                // suppression du dernier OR
                $sqlw = substr($sqlw, 0, strlen($sqlw)-3).")";
            } else {
                $this->addToLog(__METHOD__."(): Problème d'intégration - Aucun champ de recherche défini", EXTRA_VERBOSE_MODE);
            }
        }

        // Construction de la requete
        $this->sqlnt = "SELECT ".$champ." ";
        $this->sqlnt .= "FROM ".$this->table." ";
        $this->sqlnt .= $sqlw;
        $this->sql = $this->sqlnt;
        $this->sql .= " ".$tri;
        // Construction de la requete qui compte le nombre
        // d'enregistrement
        $this->sqlC = "select count(*) ";
        $this->sqlC .= "from (".$this->sqlnt.") AS A" ;
    }


    /**
     * Méthode de composition de la recherche avancée.
     *
     * @param [type] $champ [description]
     * @param [type] $tri   [description]
     *
     * @return [type] [description]
     */
    function composeAdvancedSearch($champ, $tri){
        /* Dans le cas d'une recherche avancee, l'operateur binaire OR
           utilise dans la requete SQL de rechreche devient AND */

        $opp = "or";
        if ($this->isAdvancedSearchEnabled() and
            $this->f->get_submitted_post_value("advanced-search-submit") !== null) {
            $opp = "and";
        }

        // Traitement des filtrages sur tablejoin : on cherche à filtrer les données
        // de table qui vérifient au moins une fois le critère de recherche
        // portant sur la table liée
        // l'option tabletype est alors positionnée sur related par opposition à
        // reference pour une table de référence (le cas par défaut pour les critères
        // portant sur des tables liées)
        //
        // Ce traitement se fait avant traitement de la requête car les critères sont
        // ajoutés à la variable table sous la forme de jointure filtrante
        // de type
        // ... INNER JOIN related ON table.table = related.table
        // AND related.field = $value
        //
        // Exemple :
        //    $champs['parcelle'] = array(
        //    'table' => 'dossier_parcelle',
        //    'where' => 'injoin',
        //    'tablejoin' => 'INNER JOIN (SELECT DISTINCT dossier FROM '.DB_PREFIXE.'dossier_parcelle WHERE lower(dossier_parcelle.libelle) like %s ) AS A1 ON A1.dossier = dossier.dossier' ,
        //    'colonne' => 'libelle',
        //    'type' => 'text',
        //    'taille' => 30,
        //    'libelle' => __('parcelle'));

        // test si on est bien en recherche avancée
        if ($this->isAdvancedSearchEnabled() and
            $this->f->get_submitted_post_value("advanced-search-submit") !== null) {
            // Boucle sur les champs de recherche
            foreach ($this->searchTab["query"] as $key => $elem) {
                // le champ a-t-il une clé tablejoin ? et si la valeur est-elle renseignée ?
                if ( key_exists("tablejoin", $this->paramChampRechercheAv[$elem])
                    and key_exists($elem, $this->f->get_submitted_post_value())
                    ) {
                    // récupération et retraitement de la valeur du POST
                    $value = $this->composeSearchValue(
                        $this->f->get_submitted_post_value($elem)
                    );

                    // retraitement de join pour remplacer %s par la valeur recherchée ;
                    $tablejoin = $this->paramChampRechercheAv[$elem]["tablejoin"];
                    // la chaine de tablejoin doit contenir un unique %s qui représente le champ recherche
                    if (substr_count($tablejoin, '%s') >= 1 ) {
                        $tablejoin = sprintf($tablejoin, $value);
                    }

                    // Ajout du SQL de la jointure de la table liée dans $this->table
                    $this->table .= " ".$tablejoin." ";
                }


            }
        }


        // Construction de la requete
        $this->sql = "SELECT ".$champ." ";
        $this->sql .= "FROM ".$this->table." ";
        if ($this->selection == '') {
            $this->sql .= "WHERE (";
        } else {
            $this->sql .= $this->selection . " AND (";
        }

        // Construction de la clause WHERE avec les champs de recherche
        $sqlw = "";

        $aggregate_filters = array();

        foreach ($this->searchTab["query"] as $key => $elem) {
            $value = $this->f->get_submitted_post_value("recherche");

            /* Si la recherche avancee est activee on recupere les
               valeurs postees */

            $champ = $elem;

            /* Dans le cas ou des critères sont de type tablejoin on ne
               rajoute pas de WHERE */
            if (key_exists("tablejoin", $this->paramChampRechercheAv[$elem])) {
                continue;
            }
            /* Dans le cas ou des criteres de recherche avancee
               utilisent des fonctions d'aggregation, on n'ajoute
               pas ces criteres a la clause WHERE. Ils seront
               ajoute plus tard a la clause HAVING. */

            if (key_exists("aggregate", $this->paramChampRechercheAv[$champ])) {
                array_push($aggregate_filters, $champ);
                continue;
            }

            /* Gestion des clauses WHERE particulieres */

            if (key_exists("where", $this->paramChampRechercheAv[$champ])) {

                // CAS - intervalle de date
                if ($this->paramChampRechercheAv[$champ]["where"] == "intervaldate") {

                    /* Dans le cas d'un intervalle de dates, deux
                       valeurs sont soumisent: le minimum et le
                       maximum */

                    // le format de date recu : jj/mm/aaaa
                    // avec 0 initiaux

                    if (key_exists($champ."_min", $this->f->get_submitted_post_value())) {
                        $min_date_tab = explode(
                                "/",
                                $this->f->get_submitted_post_value($champ."_min")
                            );

                        if (key_exists(2, $min_date_tab)) {
                            $min_date = $min_date_tab[2]."-".$min_date_tab[1]."-".$min_date_tab[0];
                            $sqlw .= " ".$this->paramChampRechercheAv[$champ]["table"].".".$this->paramChampRechercheAv[$champ]["colonne"]. " >= DATE('".$min_date."') ".$opp." ";
                        }
                    }

                    if (key_exists($champ."_max", $this->f->get_submitted_post_value())) {
                        $max_date_tab = explode(
                                "/",
                                $this->f->get_submitted_post_value($champ."_max")
                            );

                        if (key_exists(2, $max_date_tab)) {
                            $max_date = $max_date_tab[2]."-".$max_date_tab[1]."-".$max_date_tab[0];
                            $sqlw .= " ".$this->paramChampRechercheAv[$champ]["table"].".".$this->paramChampRechercheAv[$champ]["colonne"]. " <= DATE('".$max_date."') ".$opp." ";
                        }
                    }

                } elseif (
                    // CAS - valeurs booleennes
                    $this->paramChampRechercheAv[$champ]["where"] == "booleansubquery" or
                        $this->paramChampRechercheAv[$champ]["where"] == "insubquery") {

                    if ($this->paramChampRechercheAv[$champ]["subtype"] == "manualselect" and
                        key_exists($champ, $this->f->get_submitted_post_value())) {

                        $sqlw .= " ".$this->paramChampRechercheAv[$champ]["table"].".".
                                     $this->paramChampRechercheAv[$champ]["colonne"];

                        if ($this->paramChampRechercheAv[$champ]["where"] == "insubquery" and
                            $this->f->get_submitted_post_value($champ) == "false") {
                            $sqlw .= " NOT IN";
                        } else {
                            $sqlw .= " IN";
                        }

                        $sqlw .= " (".$this->paramChampRechercheAv[$champ]["subquery"];

                        if ($this->paramChampRechercheAv[$champ]["where"] == "booleansubquery") {
                            $sqlw .= " ".$this->f->get_submitted_post_value($champ);
                        }

                        $sqlw .= " ) ".$opp;

                        if ($this->paramChampRechercheAv[$champ]["where"] == "insubquery") {
                            continue;
                        }

                        if ($this->paramChampRechercheAv[$champ]["val_".$this->f->get_submitted_post_value($champ)] == "strict") {

                            $sqlw .= " ".$this->paramChampRechercheAv[$champ]["table"].".".
                                         $this->paramChampRechercheAv[$champ]["colonne"];

                            $sqlw .= " NOT IN";
                            $sqlw .= " (".$this->paramChampRechercheAv[$champ]["subquery"];

                            if ($this->paramChampRechercheAv[$champ]["where"] == "booleansubquery") {

                                $val = "true";
                                if ($this->f->get_submitted_post_value($champ) == "true") {
                                    $val = "false";
                                }

                                $sqlw .= " ".$val;
                            }

                            $sqlw .= " ) ".$opp;
                        }
                    }
                }

                // fin du traitement des valeurs particulieres du WHERE
                continue;
            }

            /*
             * Gestion de la clause TYPE select
             *
             * Si le champ est de type select alors on bypass les wildcards
             * pour faire une recherche strictement égale.
             */

            if (key_exists("type", $this->paramChampRechercheAv[$champ])
                && $this->paramChampRechercheAv[$champ]["type"] == "select") {

                // Création de la condition du where (stricte puisque select)
                $sqlw .= " ".$this->paramChampRechercheAv[$champ]["table"].
                ".".$this->paramChampRechercheAv[$champ]["colonne"].
                " = '".$this->f->get_submitted_post_value($champ)."' ".$opp;

                // On sort de la boucle pour traiter le champ suivant
                continue;
            } // Fin du traitement du type select

            $value = $this->f->get_submitted_post_value($champ);
            if ( is_array($this->paramChampRechercheAv[$champ]["colonne"])){

                $elem = array();
                foreach ($this->paramChampRechercheAv[$champ]["colonne"] as  $data) {

                    $elem[] = $this->paramChampRechercheAv[$champ]["table"].".". $data;
                }
            }
            else{

                $elem = $this->paramChampRechercheAv[$champ]["table"].".".
                    $this->paramChampRechercheAv[$champ]["colonne"];
            }

            // gestion des champs date simple, sans intervalle
            if ($this->paramChampRechercheAv[$champ]["type"] == "date") {
                $sqlw .= " ".$elem." = DATE('".$value."')".$opp." ";
                continue;
            }

            // Caractère d'échappement d'élément dans une recherche
            $separator = ',';
            /*
             * Si on passe plusieurs colonnes dans le champ de recherche,
             * on boucle sur chaque colonne de table liée à ce champ de recherche
             * */
            if ( is_array($elem) ){
                $sqlw .= ' ( ';
                foreach ($elem as $data) {
                    $sqlw .= $this->createSQLW($data, $value, ' OR ');
                }
                $sqlw = substr($sqlw, 0, strlen($sqlw) - 3 ) . " )".$opp." ";
            } else {
                // recherche dans un champ unique
                $sqlw .= $this->createSQLW($elem, $value, $opp).' ';
            }
        }
        // Suppression du dernier $opp
        $sqlw = substr($sqlw, 0, strlen($sqlw) - strlen($opp)-1).")";
        // Construction de la requête non triée pour le count
        $this->sqlnt = $this->sql . $sqlw;
        // Suppression du AND si pas utilisé
        if (substr($this->sqlnt, strlen($this->sqlnt)-6,strlen($this->sqlnt)) == 'AND ()') {
            $this->sqlnt = substr($this->sqlnt, 0, strlen($this->sqlnt)-6);
        }
        // Suppression du WHERE si pas utilisé
        if( substr($this->sqlnt, strlen($this->sqlnt)-8,strlen($this->sqlnt)) == 'WHERE ()'){
            $this->sqlnt = substr($this->sqlnt, 0, strlen($this->sqlnt)-8);
        }
        // Construction de la requête triée pour les résultats
        $this->sql = $this->sqlnt." ".$tri;

        /* Dans le cas ou des criteres de recherche avancee utilisent des
           fonctions d'aggregation, on construit la clause HAVING adequate */

        if (isset($aggregate_filters) and
            count($aggregate_filters) != 0) {

            // Suppression des incoherences
            $this->sql = $this->_del_substr($this->sql, "where ()");
            $this->sql = $this->_del_substr($this->sql, "and ()");

            /* Si la requete contient deja la clause HAVING, on ajoute
               les criteres de recherche utilisant des fonctions d'aggregation
               apres le HAVING */

            if (stristr($this->sql, "having") != false) {

                //
                $filters = "HAVING"; /* pas d'espace apres ce having svp*/
                $filters .= $this->add_aggregate_filters($aggregate_filters, $opp);
                $this->sql = str_ireplace("having", $filters, $this->sql);

            } elseif (stristr($this->sql, "order by") != false) {
                /* Si la requete ne contient pas la clause HAVING, mais une clause
                   ORDER BY, on ajoute les criteres de recherche utilisant des fonctions
                   d'aggregation avant le ORDER BY */

                $filters = "HAVING"; /* pas d'espace apres ce having svp*/
                $filters .= $this->add_aggregate_filters($aggregate_filters, $opp);
                $filters .= " ORDER BY";

                //
                $this->sql = str_ireplace("order by", $filters, $this->sql);

            } else {
                /* Si la requete ne contient ni la clause HAVING, ni ORDER BY, on ajoute
                   les criteres de recherche utilisant des fonctions d'aggregation
                   a la fin de $this->sqlC */
                //
                $this->sql .= " HAVING"; /* pas d'espace apres ce having svp*/
                $this->sql.= $this->add_aggregate_filters($aggregate_filters, $opp);
            }
        }

        // Construction de la requete qui compte le nombre
        // d'enregistrement
        $this->sqlC = "SELECT count(*) FROM (".$this->sqlnt.") AS A";
    }

    /**
     * Methode de construction de la requete
     *
     * Cette methode permet de construire les deux requetes sql sqlC
     * en concatenant les differents parametres
     *
     * @return void
     */
    function composeQuery() {
        // Composition de la liste des champs du SELECT
        $champ = $this->composeChamp();

        // Prise en compte du tri de la colonne
        $tri = $this->composeTri();

        // Si le champ "recherche" est vide et que l'on a saisi des paramètres de
        // recherche avancée
        if($this->f->get_submitted_post_value("advanced-search-submit")!==null and
            $this->_rechercheAvanceeFaite == true){
            // traitement de la recherche avancée
            $this->composeAdvancedSearch($champ, $tri);
        } else {
            // traitement de la recherche simple
            // (sous-tab ou sélecteur de colonne ou recherche simple)
            $this->composeSimpleSearch($champ, $tri);
        }
    }

    /**
     * Création de la requête
     *
     * @param string $searchField
     * @param string $searchValue
     * @param string $opp
     *
     * @return string
     */
    function createSQLW($searchField, $searchValue, $opp) {
        $searchField = $this->normalizeFieldValue($searchField);
        $searchValue = $this->composeSearchValue($searchValue);
        $sqlw = $searchField." LIKE ".$searchValue. $opp;

        return $sqlw;
    }

    // {{{ Gestion de la recherche avancee

    /**
     * Cree une chaine en ajoutant les criteres de recherche avancee utilisant
     * des fonctions d'aggregation les uns a la suite des autres.
     *
     * Cela permet de construire l'expression SQL devant se placer apres la
     * clause HAVING.
     *
     * @param array  $filters liste des crietes de recherche
     * @param string $opp     operateur binaire 'AND' ou 'OR'
     *
     * @return string l'expression se placant apres le HAVING
     */
    protected function add_aggregate_filters ($filters, $opp) {

        $str = "";

        foreach ($filters as $filter) {
            $str .= " ";
            $str .= strtoupper($this->paramChampRechercheAv[$filter]["aggregate"]);
            $str .= "(".$this->paramChampRechercheAv[$filter]["table"].".".$filter.")";
            $str .= " = ".$this->f->get_submitted_post_value($filter)." ".$opp;
        }

        // Suppression du dernier $opp
        $str = substr($str, 0, strlen($str) - strlen($opp)-1);

        return $str;
    }

    /**
     * Supprime la chaine $substr de $str si $substr existe.
     * Methode insensible à la casse
     *
     * @param string $str    ...
     * @param string $substr ...
     * @param string $rep    optionnel, $rep peut remplacer $substr
     *
     * @return string le parametre $str traite
     */
    protected function _del_substr($str, $substr, $rep = "") {
        if (stristr($str, $substr) != false) {
            $str = str_ireplace($substr, $rep, $str);
        }
        return $str;
    }

    /**
     * Retourne la liste des champs de recherche avancee qui ont ete envoyes par
     * POST et dont la valeur n'est pas vide.
     *
     * Cela permet d'utiliser dans la clause SQL WHERE uniquement les champs
     * sollicites.
     *
     * De plus, si la requete SQL contient des alias de table comme
     * `utilisateur as u` le parametre $with_table_prefix permet de retourner
     * la liste des champs. (A condition que le parametre `table_prefix` existe).
     *
     * @param boolean $with_table_prefix ...
     *
     * @return array
     */
    protected function getPostedChampRechercheAv($with_table_prefix = false) {

        $champ_poste = array();
        //$table_prefix = "";

        // recuperation du parametre table_prefix
        //if (($with_table_prefix == true) AND
        //    ($this->getParam("table_prefix") != "")) {
        //    $table_prefix = $this->getParam("table_prefix").".";
        //}

        // iteration sur le nom des colonnes de la base
        foreach ($this->dbChampRechercheAv as $champ) {

            /* Gestion des cas particuliers */

            // CAS- intervalle de date
            if ($this->paramChampRechercheAv[$champ]["type"] == "date" and
                key_exists("where", $this->paramChampRechercheAv[$champ]) and
                $this->paramChampRechercheAv[$champ]["where"] == "intervaldate") {

                /* Dans ce cas on considere que les cles du tableau $_POST sont
                   $champ."_min" et $champ."_max". Si au moins un min ou un max
                   est envoye par POST on considere le champ date comme
                   soumis */

                if ($this->f->get_submitted_post_value($champ."_min") != "" or
                        $this->f->get_submitted_post_value($champ."_max") != "") {
                    //
                    //array_push($champ_poste, $table_prefix.$champ);
                    array_push($champ_poste, $champ);
                }


            } elseif ($this->paramChampRechercheAv[$champ]["type"] == "checkbox" and
                key_exists("where", $this->paramChampRechercheAv[$champ]) and
                ($this->paramChampRechercheAv[$champ]["where"] == "boolean" /*OR
                $this->paramChampRechercheAv[$champ]["where"] == "boolean_sub"*/)) {

                // CAS- valeurs booleenes
                if ($this->f->get_submitted_post_value($champ."_true") != "" or
                    $this->f->get_submitted_post_value($champ."_false") != "") {

                    //
                    //array_push($champ_poste, $table_prefix.$champ);
                    array_push($champ_poste, $champ);
                }

                /* Gestion des cas classiques */

            } elseif ($this->f->get_submitted_post_value($champ) != "") {

                //
                //array_push($champ_poste, $table_prefix.$champ);
                array_push($champ_poste, $champ);
            }
        }

        return $champ_poste;
    }

    /**
     * Affiche le formulaire de recherche avancée.
     *
     * @access public
     * @return void
     */
    protected function displayAdvancedSearch() {
        // Mode recherche avancée
        $maj = 999;

        /* Recuperation du nom de l'objet sur lequel la recherche seffectue */

        $form = $this->f->get_inst__om_formulaire(array(
            "validation" => 0,
            "maj" => $maj,
            "champs" => $this->htmlChampRechercheAv,
        ));

        /* Creation dun objet vide pour pouvoir creer facilement les champs de
           type select */

        $object = $this->f->get_inst__om_dbform(array(
            "obj" => $this->absolute_object,
            "idx" => "]",
        ));
        // On appelle la méthode setSelect de l'objet
        $object->setSelect($form, $maj, $this->db, false);

        $paramChamp = $this->paramChampRechercheAv;

        // Affichage du formulaire
        $this->f->layout->display__form_container__begin(array(
            "action" => $this->composeURL(array(
                "validation" => 0,
                "premier" => 0,
                "advs_id"=> $this->gen_advs_id(),
            )),
            "id" => "advanced-form",
        ));
        echo "\t\t<fieldset class=\"cadre ui-corner-all ui-widget-content adv-search-fieldset\">\n";
        echo "\t\t<legend id=\"toggle-advanced-display\" class=\"ui-corner-all ui-widget-content ui-state-active\">";
        echo __("Recherche");
        echo "\t\t</legend>";

        // construction du message d'aide
        $help_text = __('Utilisation de * pour zones de saisie').':';

        if ($this->wildcard['left'] == '') {
            $help_text .= " ".__("*ABC finit par 'ABC'.");
        }

        if ($this->wildcard['right'] == '') {
            $help_text .= " ".__("ABC* commence par 'ABC'.");
        }

        if ($this->wildcard['left'] == '' and $this->wildcard['right'] == '') {
            $help_text .= " ".__("*ABC* contient 'ABC'.");
        }

        $help_text .= " ".__("A*D peut correspondre a 'ABCD'.");

        if ($this->wildcard['left'] != '' and $this->wildcard['right'] != '') {
            $help_text .= " ".__("Par defaut * est ajoute au debut et a la fin des recherches.");
        } else {
            if ($this->wildcard['left'] != '') {
                $help_text .= " ".__("Par defaut * est toujours ajoute au debut des recherches.");
            }

            if ($this->wildcard['right'] != '') {
                $help_text .= " ".__("Par defaut * est toujours ajoute a la fin des recherches.");
            }
        }

        /* Affichage du widget de recherche multicriteres classique */

        echo "\t\t\t<div id=\"adv-search-classic-fields\">";

        echo "\t\t\t\t<div class=\"adv-search-widget\">\n";
        echo "\t\t\t\t\t<label>".__("Rechercher")."&nbsp;<input type=\"text\" name=\"recherche\" ";
        echo "value=\"".$this->f->get_submitted_post_value("recherche")."\" ";
        echo "class=\"champFormulaire\" /></label>\n";
        echo "\t\t\t\t</div>\n";

        echo "\t\t\t<p class=\"adv-search-helptext\">".$help_text."</p>";

        echo "\t\t\t</div>";

        /* Affichage des widgets de recherche avancee */

        echo "\t\t\t<div id=\"adv-search-adv-fields\">";

        foreach ($this->dbChampRechercheAv as $champ) {

            /* Gestion de l'affichage de deux champs HTML date pour pouvoir
               soumettre un intervalle. Les deux champs sont crees avec
               deux attributs "name" differents: le premier avec le suffixe
               "_min" et le second "_max", representant respectivement la date
               minimale et la date maximale. */

            $champs_html = array($champ);

            if ($paramChamp[$champ]["type"] == "date" and
                key_exists("where", $paramChamp[$champ]) and
                $paramChamp[$champ]["where"] == "intervaldate") {

                $champs_html = array($champ."_min", $champ."_max");

                /* Gestion de l'affichage de deux champs HTML checkbox pour pouvoir
                   soumettre des valeurs booleennes. Les deux champs sont crees avec
                   deux attributs "name" differents: le premier avec le suffixe
                   "_true" et le second "_false". */

            } elseif ($paramChamp[$champ]["type"] == "checkbox" and
                      key_exists("where", $paramChamp[$champ]) and
                      ($paramChamp[$champ]["where"] == "boolean")) {

                $champs_html = array($champ."_true", $champ."_false");
            }

            foreach ($champs_html as $champ_html) {

                $form->setType($champ_html, $paramChamp[$champ]["type"]);

                //// Gestion de l'affichage des libellés de critères de recherche
                // On initialise le libellé à vide.
                $libelle = "";
                // Le libellé standard est récupéré dans l'attribut "libellé"
                // si il existe.
                if (isset($paramChamp[$champ]["libelle"])) {
                    //
                    $libelle = $paramChamp[$champ]["libelle"];
                }
                // On ajoute un icône d'aide qui fait apparaître une explication
                // au survol de la souris . L'explication est récupérée dans
                // l'attribut "help" si il existe et n'est pas vide.
                if (isset($paramChamp[$champ]["help"])
                    && $paramChamp[$champ]["help"] != "") {
                    // On concatène le libellé avec l'icône et l'explication.
                    $libelle .= sprintf(
                        ' <span class="info-16" title="%s"></span>',
                        $paramChamp[$champ]["help"]
                    );
                }
                // libelle des intervales de date
                if ($paramChamp[$champ]['type'] == 'date' and
                    isset($paramChamp[$champ]['where']) and
                    $paramChamp[$champ]['where'] == 'intervaldate') {

                    // premier champ
                    if ($champ_html == $champ.'_min') {

                        $form->setBloc($champ_html, 'D',
                                       $libelle,
                                       'intervaldate');

                        // si lib1 n'existe pas, on utilise `du`
                        if (isset($paramChamp[$champ]['lib1'])) {
                            $form->setLib($champ_html,
                                          $paramChamp[$champ]['lib1']);
                        } else {
                            $form->setLib($champ_html, __('du'));
                        }
                    }

                    // second champ
                    if ($champ_html == $champ.'_max') {

                        $form->setBloc($champ_html, 'F');

                        // si lib2 n'existe pas, on utilise `au`
                        if (isset($paramChamp[$champ]['lib2'])) {
                            $form->setLib($champ_html,
                                          $paramChamp[$champ]['lib2']);
                        } else {
                            $form->setLib($champ_html, __('au'));
                        }
                    }

                } else {
                    $form->setLib($champ_html, $libelle);
                }

                if (isset($paramChamp[$champ]["taille"])) {
                     $form->setTaille($champ_html, $paramChamp[$champ]["taille"]);
                }

                if (isset($paramChamp[$champ]["max"])) {
                    $form->setMax($champ_html, $paramChamp[$champ]["max"]);
                }

                if ($paramChamp[$champ]["type"] == "select" and
                    key_exists("subtype", $paramChamp[$champ]) and
                    $paramChamp[$champ]["subtype"] == "manualselect") {
                    $form->setSelect($champ_html, $paramChamp[$champ]["args"]);
                }
                if ($paramChamp[$champ]["type"] == "select" and
                    key_exists("subtype", $paramChamp[$champ]) and
                    $paramChamp[$champ]["subtype"] == "sqlselect") {
                    $object->init_select($form, $this->db, 0, false, $champ,
                           $paramChamp[$champ]["sql"]);
                }

                if ($paramChamp[$champ]["type"] == "date") {
                    $form->setOnchange($champ_html, "fdate(this)");
                }

                if ($this->f->get_submitted_post_value($champ_html) != "") {
                    $form->setVal(
                        $champ_html,
                        $this->f->get_submitted_post_value($champ_html)
                    );
                }
            }

        }

        $form->entete();
        $form->afficher($this->htmlChampRechercheAv, 0, false, false);
        $form->enpied();

        /* Message d'aide */

        echo "<div class=\"visualClear\"></div>";
        echo "<p class=\"adv-search-helptext\">".$help_text."</p>";


        echo "\t\t\t</div>";

        /* Fin du fieldset recherche avancee */

        echo "\t\t</fieldset>\n";

        /* Affichage des boutons de controle du formulaire */
        $searchform_type = "";
        // si une recherche avancee est faite
        if ($this->f->get_submitted_post_value("advanced-search-submit") !== null) {
            // on affiche la recherche avancee
            $searchform_type = "advanced";
        } elseif ($this->f->get_submitted_post_value("classic-search-submit") !== null) {
            // si une recherche simple est faite on affiche la recherche simple
            $searchform_type = "classic";
        } else {
            // si aucune recherche n'est faite, on affiche le formulaire
            // configure par defaut
            if ($this->advs_default_form == "advanced") {
                $searchform_type = "advanced";
            } else {
                $searchform_type = "classic";
            }
        }
        $this->f->layout->display__form_input_submit(array(
            "id" => "adv-search-submit",
            "value" => __("Recherche"),
            "name" => sprintf("%s-search-submit", $searchform_type),
        ));
        echo "\t\t<a href=\"#\" class=\"raz_advs\" onclick=\"clear_form($('#advanced-form'));\">";
        echo __("Vider le formulaire");
        echo "</a>";
        $this->f->layout->display__form_container__end();
    }

    /**
     * Retourne true si la recherche avancee est activee, false sinon.
     *
     * Configure la recherche au premier appel si celle ci est activee.
     *
     * @return bool etat de la recherche avancee: activee/desactivee
     */
    public function isAdvancedSearchEnabled() {

        /* Si letat est deja defini, on le retourne */
        if ($this->_etatRechercheAv != null) {
            return $this->_etatRechercheAv;
        }

        /* Sinon on cherche une option de type search */
        foreach ($this->options as $option) {

            /* SI       loption search est defini
               ET       quelle dispose d'un parametre advanced
               ET       que ce parametre nest pas NULL
               ALORS    la recherche avancee est defini a la valeur du parametre
               SINON    elle est desactivee */

            if ($option['type'] == 'search' and
                key_exists('advanced', $option) and
                !empty($option['advanced']) and
                is_array($option['advanced'])) {

                // configuration de la liste des champs de recherche
                $this->paramChampRechercheAv = $option['advanced'];

                // configuration du formulaire ouvert par defaut
                if (key_exists("default_form", $option)) {
                    $this->advs_default_form = $option["default_form"];
                }

                // configuration du nom de la table en base de donnees de l'objet
                $this->absolute_object = $option['absolute_object'];

                $this->_etatRechercheAv = true;
                return $this->_etatRechercheAv;
            }
        }

        $this->_etatRechercheAv = false;
        return $this->_etatRechercheAv;
    }

    /**
     * Compose une URL pour l'export.
     *
     * Le principe est d'utiliser la propriété 'params' pour tous les
     * paramètres de l'URL sauf si ils ont une valeur dans le paramètre passé.
     *
     * @param array $params Tableau de valeurs à surcharger.
     *
     * @return string
     */
    function composeExportUrl($params = array()) {
        //
        $return =  "";
        //
        foreach ($params as $param => $value) {
            if (!array_key_exists($param, $this->params)) {
                $return .= $param."=".$params[$param]."&amp;";
            }
        }
        foreach ($this->params as $param => $value) {
            if (isset($params[$param])) {
                $return .= $param."=".$params[$param]."&amp;";
            } else {
                $return .= $param."=".$value."&amp;";
            }
        }
        if ($return != "") {
            substr($return, 0, strlen($return)-5);
        }
        //
        return $return;
    }

    /**
     * Permet d'initialiser la liste des champs HTML a afficher par le
     * formulaire de recherche avancee.
     *
     * @access protected
     * @return array liste des attributs "name" des champs
     */
    protected function initHtmlChampRechercheAv() {
        $champs = array();

        foreach ($this->dbChampRechercheAv as $champ) {

            /* Gestion des cas particuliers */

            // CAS - intervalle de date
            if($this->paramChampRechercheAv[$champ]['type'] == 'date' and
               key_exists('where', $this->paramChampRechercheAv[$champ]) and
               $this->paramChampRechercheAv[$champ]['where'] == 'intervaldate') {
                array_push($champs, $champ.'_min');
                array_push($champs, $champ.'_max');

            // CAS - checkbox et valeurs booleennes
            } elseif ($this->paramChampRechercheAv[$champ]['type'] == 'checkbox' AND
                      key_exists('where', $this->paramChampRechercheAv[$champ]) AND
                      ($this->paramChampRechercheAv[$champ]['where'] == 'boolean')) {
                array_push($champs, $champ.'_true');
                array_push($champs, $champ.'_false');

            /* Gestion des cas classiques */

            } else {
                 array_push($champs, $champ);
            }
        }

        return $champs;
    }

    /**
     * Retourne un identifiant unique pour ka recherche avancée.
     *
     * @return string
     */
    private function gen_advs_id() {
        return str_replace(array('.',','), '', microtime(true));
    }

    /**
     * Stocke en variable de session les critères de la recherche avancée.
     *
     * @param array $post Critères de la recherche avancée.
     *
     * @return void
     */
    public function serialize_criterions($post) {
        $_SESSION["advs_ids"][$this->_advs_id] = serialize($post);
    }

    /**
     * Retour les critères de la recherche avancée.
     *
     * @return array
     */
    public function unserialize_criterions() {
        return unserialize($_SESSION["advs_ids"][$this->_advs_id]);
    }

    // }}}

    // {{{ Layout

    /**
     * Layout.
     *
     * @return void
     */
    function displayGlobalContainerStart() {
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displayGlobalContainerEnd() {
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displayTableContainerStart() {
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displayTableContainerEnd() {
    }

    /**
     * Layout.
     *
     * @param string $style Classe CSS.
     *
     * @return void
     */
    function displayTableHeadLineStart($style = "tab") {
        //
        echo "\t\t<tr class=\"ui-tabs-nav ui-accordion ui-state-default ".$style."-title\">\n";
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displayTableHeadLineEnd() {
        //
        echo "\t\t</tr>\n";
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displayToolbarContainerStart() {
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displayToolbarContainerEnd() {
    }

    /**
     * Layout.
     *
     * @param string $class Classe CSS.
     *
     * @return void
     */
    function displayTableStart($class = "") {
        $idcolumntoggle = "";
        $idcolumntoggle = strtok($this->table, ' ');
        $param = array(
                "class" => $class,
                "idcolumntoggle" =>$idcolumntoggle
            );
        $this->f->layout->display_table_start($param);
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displaySearchContainerStart() {
    }

    /**
     * Layout.
     *
     * @return void
     */
    function displaySearchContainerEnd() {
    }


    // }}}

    // {{{ Gestion des actions globales

    /**
     * Liste des actions globales.
     * @var array
     */
    var $_global_actions = array();

    /**
     * Mutateur pour la propriété '_global_actions'.
     *
     * @param array $action Tableau de configuration d'une action
     *
     * @return void
     */
    protected function add_action_to_global_actions($action = array()) {
        //
        $this->_global_actions[] = $action;
    }

    /**
     * Gère l'action globale 'om_validite'.
     *
     * @return void
     */
    protected function process_global_action_validity() {
        //
        if ($this->_om_validite == true) {
            //
            if ($this->getParam('valide') == 'false') {
                $om_validite_message = __("Masquer les elements expires");
                $params['valide'] = 'true';
            } else {
                $om_validite_message = __("Afficher les elements expires");
                $params['valide'] = 'false';
            }
            //
            $action_id = "action-";
            if ($this->getParam('onglet') === false) {
                $action_id .= "tab-";
            } else {
                $action_id .= "soustab-";
            }
            $action_id .= $this->getParam("obj")."-global-om_validite-".$params['valide'];
            //
            $action = array(
                "id" => $action_id,
                "type" => "om_validite",
                "class" => "om_validite",
                "link" => $this->composeURL($params),
                "title" => $om_validite_message,
            );
            //
            $this->add_action_to_global_actions($action);
        }
    }

    /**
     * Gère l'action globale 'edition pdf'.
     *
     * @return void
     */
    protected function process_global_action_edition() {
        //
        if ($this->edition != "") {
            //
            $action = array(
                "style" => $this->getParam("style"),
                "type" => "edition",
                "class" => "edition",
                "link" => $this->edition,
                "title" => __("Edition PDF"),
                "target" => "_blank",
            );
            //
            $this->add_action_to_global_actions($action);
        }
    }

    /**
     * Ajoute les boutons d'export globaux.
     *
     * Si l'export est paramétré dans le tableau $options du fichier .inc.php d'un objet,
     * cette méthode va afficher le bouton au dessus du listing.
     *
     * @return void
     */
    protected function process_global_action_export() {
        //
        if (isset($this->export) AND !empty($this->export)) {
            $rights = '';
            foreach ($this->export as $key => $value) {
                if (!is_array($value)) {
                    $rights = array($this->getParam('obj'), $this->getParam('obj')."_exporter");
                    $mode_export = $value;
                }
                if (isset($value['right']) AND $value['right'] !== '') {
                    $rights = array($this->getParam('obj'), $value['right']);
                    $mode_export = $key;
                }
                if ($this->f->isAccredited($rights,"OR")) {
                    $params_export = array(
                        "validation" => 0,
                        "premier" => 0,
                        "advs_id" => $this->_advs_id,
                    );
                    $action = array(
                        "style" => $this->getParam("style"),
                        "type" => "export",
                        "class" => $mode_export,
                        "link" => OM_ROUTE_TAB."&mode=export_".$mode_export."&amp;".$this->composeExportUrl($params_export),
                        "title" => __("Export")." ".$mode_export,
                        "target" => "_blank",
                    );
                    $this->add_action_to_global_actions($action);
                }
            }
        }
    }

    /**
     * Affiche les actions globales.
     *
     * @return void
     */
    protected function display_global_actions() {
        //
        $this->f->layout->display_table_global_actions($this->_global_actions);
    }

    // }}}
}
