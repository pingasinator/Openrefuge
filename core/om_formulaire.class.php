<?php
/**
 * Ce script contient la définition de la classe 'formulaire'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_formulaire.class.php 4348 2018-07-20 16:49:26Z softime $
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
 * Définition de la classe 'formulaire'.
 *
 * Cette classe a pour objet la construction des champs du formulaire.
 */
class formulaire extends om_base {

    /**
     * Entete
     * @var string
     * @deprecated
     */
    var $enteteTab = "";

    /**
     * Valeur par defaut du champ
     * @var array
     */
    var $val;

    /**
     * Type de champ
     * @var array
     */
    var $type;

    /**
     * Taille du champ
     * @var array
     */
    var $taille;

    /**
     * Nombre de caracteres maximum a saisir
     * @var array
     */
    var $max;

    /**
     * Libelle du champ
     * @var array
     */
    var $lib;

    /**
     * Valeur des listes
     * @var array
     */
    var $select;

    /**
     * Javascript en cas de changement
     * @var array
     */
    var $onchange;

    /**
     * Javascript en cas de keyup
     * @var array
     */
    var $onkeyup;

    /**
     * Javascript en cas de clic
     * @var array
     */
    var $onclick;

    /**
     * Regroupement
     * @var array
     */
    var $groupe = array();

    /**
     * Fieldset
     * @var array
     */
    var $regroupe = array();

    /**
     * Tableau des champs lies a l'ouverture et fermeture des blocs div
     * ainsi que les valeurs
     * @var array
     * @deprecated
     */
    var $bloc = array();

    /**
     * Tableau des champs lies a l'ouverture et fermeture des blocs div
     * ainsi que les valeurs
     * @var array
     */
    var $layout = array();

    /**
     * Marqueur de validité du formulaire.
     * @var boolean
     */
    var $correct;

    /**
     * Caractere du champ obligatoire
     * @var string
     */
    var $required_tag='<span class="not-null-tag">*</span>';

    /**
     * Liste des champs obligatoires
     * @var array
     */
    var $required_field = array();

    /**
     * Constructeur
     *
     * Initialisation des tableaux de parametrage du formulaire
     * - valeur par defaut
     *   en modification et suppression = initialiser par la valeur des champs
     *   en ajout = initialisation vide
     * - type par defaut
     *   text pour ajout et modification
     *   static pour suppression
     *
     * @param string|null $mode Mode d'instanciation de la classe ('view_snippet' ou null).
     * @param integer $validation
     * @param integer $maj
     * @param array $champs
     * @param array $val
     * @param array $max
     *
     * @return void
     */
    function __construct($mode = null, $validation = 0, $maj = -1, $champs = array(), $val = array(), $max = array()) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();
        //
        if ($mode === "view_snippet") {
            return;
        }
        // valeur par defaut et type
        if ($maj == 0 || $maj == 999) { // ajouter et recherche avancée
            for ($i = 0; $i < count($champs); $i++) {
                $this->val[$champs[$i]] = "";
                $this->type[$champs[$i]] = "text";
            }
        }
        if ($maj == 1) { // modifier
            if ($validation == 0) {
                $i = 0;
                foreach ($val as $elem) {
                    $this->val[$champs[$i]] = strtr($elem, chr(34), "'");
                    $i++;
                }
            }
            for ($i = 0; $i < count($champs); $i++) {
                $this->type[$champs[$i]] = "text";
                if ($validation != 0) {
                    $this->val[$champs[$i]] = "";
                }
            }
        }
        if ($maj == 2) { // supprimer
            if ($validation == 0) {
                $i = 0;
                foreach ($val as $elem) {
                    $this->val[$champs[$i]] = strtr($elem, chr(34), "'");
                    $i++;
                }
            }
            for ($i = 0; $i < count($champs); $i++) {
                $this->type[$champs[$i]] = "static";
                if ($validation != 0) {
                    $this->val[$champs[$i]] = "";
                }
            }
        }
        if ($maj >= 3) { // consulter
            $i = 0;
            foreach ($val as $elem) {
                $this->val[$champs[$i]] = strtr($elem, chr(34), "'");
                $i++;
            }
        }
        // taille et longueur maximum
        $i = 0;
        foreach ($max as $elem) {
            $this->taille[$champs[$i]] = $elem;
            $this->max[$champs[$i]] = $elem;
            $i++;
        }
        // libelle, group, select, onchange
        for ($i = 0; $i<count($champs); $i++) {
            $this->lib[$champs[$i]] = $champs[$i];
            $this->select[$champs[$i]][0] = "";
            $this->select[$champs[$i]][1] = "";
            $this->onchange[$champs[$i]] = "";
            $this->onkeyup[$champs[$i]] = "";
            $this->onclick[$champs[$i]] = "";
        }
    }

    /**
     * VIEW - view_snippet.
     *
     * @return void
     */
    public function view_snippet() {
        $snippet_name = $this->f->get_submitted_get_value("snippet");
        if ($snippet_name === null) {
            return;
        }
        $method_name = "snippet__".$snippet_name;
        if (method_exists($this, $method_name) !== true) {
            return;
        }
        $this->$method_name();
    }

    // {{{ Methodes permettant l'affichage de la table contenant le formulaire

    /**
     * Entete
     *
     * @return void
     */
    function entete() {

        echo "\n<!-- ########## START FORMULAIRE ########## -->\n";
        echo "<div class=\"formEntete ui-corner-all\">\n";

    }

    /**
     * Enpied
     *
     * @return void
     */
    function enpied() {
        echo "\n</div>\n";
        echo "<!-- ########## END FORMULAIRE ########## -->\n";

    }


    /**
     * Cette methode permet d'afficher un champ dans une hierarchie de div
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     * @return void
     */
    function afficherChamp($champ, $validation, $DEBUG = false) {

        // Récupération du type du champ
        if (isset($this->type[$champ])) {
            $type_champ = $this->type[$champ];
        } else {
            $type_champ = "statiq";
        }

        // Ajout du label en lien avec l'id du champ correspondant
        // si le type du champ n'est pas le spécifique 'nodisplay'
        if ($type_champ !== "nodisplay") {
            // Ouverture du conteneur du champ (libellé et widget)
            $this->f->layout->display_formulaire_conteneur_libelle_widget($type_champ);
            // Ouverture du conteneur du libellé
            $this->f->layout->display_formulaire_conteneur_libelle_champs();
            echo "          <label for=\"".$champ."\" class=\"libelle-".$champ.
            "\" id=\"lib-".$champ."\">\n";
            echo "            ".$this->lib[$champ].
            (in_array($champ, $this->required_field)? " ".$this->required_tag:"")."\n";
            echo "          </label>\n";
            // Fermeture du conteneur du libellé
            $this->f->layout->display_formulaire_fin_conteneur_champs();
            // Ouverture du conteneur du widget
            $this->f->layout->display_formulaire_conteneur_champs();
            // Affichage du champ en fonction de son type
            $fonction = $type_champ;
            if ($fonction == "static") {
                $fonction = "statiq";
            }
            if (method_exists($this, $fonction)) {
                $this->$fonction($champ, $validation);
            } else {
                $this->statiq($champ, $validation);
            }
            // Fermeture du conteneur du widget
            $this->f->layout->display_formulaire_fin_conteneur_champs();
            // Fermeture du conteneur du champ (libellé et widget)
            $this->f->layout->display_formulaire_fin_conteneur_champs();
        }
    }

    /**
     * Affiche l'ouverture d'un fieldset.
     *
     * @param array $action ???
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function debutFieldset($action, $validation, $DEBUG = false) {
        //
        $params = array(
            "action2" => $action[2],
            "action1" => $action[1],
        );
        //
        if ($this->getParameter("obj") != NULL && $this->getParameter("form_type") != NULL) {
            $params["identifier"] = "fieldset-".$this->getParameter("form_type")."-".$this->getParameter("obj")."-".$this->normalize_string($action[1]);
        }
        $this->f->layout->display_formulaire_debutFieldset($params);
    }

    /**
     * Affiche la fermeture d'un fieldset.
     *
     * @param array $action ???
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function finFieldset($action, $validation, $DEBUG = false) {
        $params = array();
        $this->f->layout->display_formulaire_finFieldset($params);
    }

    /**
     * Affiche l'ouverture d'un bloc.
     *
     * @param array $action ???
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function debutBloc($action, $validation, $DEBUG = false) {

    // Ouverture d'un bloc si le champ est le premier d'un groupe 'D'
        echo "\n";
        echo "     <div class=\"bloc ".$action[2]."\">\n";
        //Affichage du libelle du groupe
        if($action[1]!="") {
            echo "        <div class=\"bloc-titre\">\n";
            echo "          <span class=\"text\">\n";
            echo "            ".$action[1]."\n";
            echo "          </span>\n";
            echo "        </div>";
        }
    }

    /**
     * Affiche la fermeture d'un bloc.
     *
     * @param array $action ???
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function finBloc($action, $validation, $DEBUG = false) {
        // Fermeture du bloc
            echo "      </div>\n";
    }

    /**
     * Cette methode permet d'ordoner l'affichage des div, fieldset et champs
     *
     * @param array $champs Liste des identifiants des champs
     * @param integer $validation - 0 1er passage
     *                            - > 0 passage suivant suite validation
     * @param boolean $DEBUG Parametre inutilise
     * @param boolean $correct
     *
     * @return void
     */
    function afficher($champs, $validation, $DEBUG = false, $correct) {

        $this->correct = $correct;

        // Affichage du conteneur des champs du formulaire
        echo '<div id="form-content"';
        // Ajout d'une classe si le formulaire a été validé
        if ($this->correct == true) {
            echo ' class="form-is-valid"';
        }
        echo '>';

        //Prise en compte de la mise en page setGroupe/setRegroupe
        $this->transformGroupAndRegroupeToLayout($champs);

        // Il est nécessaire d'effectuer une première boucle sur les champs
        // pour savoir lesquels sont hidden et donc les blocs qui ne contiennent
        // que des champs hidden pour ne pas les afficher

        // Niveau d'arborescence en cours
        $level = 0;
        // Tableaux de travail
        $block_to_hide = array();
        // Boucle sur la liste des champs
        for ($i = 0; $i < count($champs); $i++) {
            // Test l'ouverture ou fermeture de bloc ou fieldset sur le champ en cour
            if (isset($this->layout[$champs[$i]])) {
                // Boucle sur les blocs et fieldset du champ
                foreach ($this->layout[$champs[$i]] as $key => $action) {
                    // Test si ouverture de bloc ou fieldset
                    if ($action[0]=="D") {
                        // Appel de la méthode de vérification des champs affichés
                        $retourAffBloc = $this->isBlocPrintable($champs[$i], $key, $champs);
                        // Vérification du retour
                        if(isset($retourAffBloc) AND $retourAffBloc !== true AND $retourAffBloc !== false) {
                            // Ajout des retour au tableau des bloc à cacher
                            foreach($retourAffBloc as $champHidden) {
                                $block_to_hide[$champHidden[0]][] = $champHidden[1];
                            }
                        }

                    } elseif ($action[0]=="DF" AND $this->type[$champs[$i]] == "hidden"){
                        // Gestion du champ caché si un bloc DF est appliqué sur celui-ci
                        $block_to_hide[$champs[$i]][] = $key;
                    }
                }
            }
        }
        // Pour chaque champs
        for ($i = 0; $i < count($champs); $i++) {
            // On verifie qu'un bloc s'ouvre
            if (isset($this->layout[$champs[$i]])) {
                $tabLength=count($this->layout[$champs[$i]]);
                // Pour chaque action sur chaque champ
                foreach ($this->layout[$champs[$i]] as $key=>$action) {
                    // Si le bloc n'est pas dans la liste des blocs à cacher
                    // on affiche l'ouverture du bloc
                    if(!isset($block_to_hide[$champs[$i]]) OR array_search($key, $block_to_hide[$champs[$i]]) === false) {
                        $methode = "debut".$action[3];
                        if($action[0]=="D" OR $action[0]=="DF") {
                                $this->$methode($action,$validation,$DEBUG);
                        }
                    }
                }
                // On affiche le champ
                $this->afficherChamp($champs[$i], $validation, $DEBUG );
                // Pour chaque action sur chaque champ
                foreach ($this->layout[$champs[$i]] as $key=>$action) {
                    // Si le bloc n'est pas dans la liste des blocs à cacher
                    // on affiche la fermeture du bloc
                    if(!isset($block_to_hide[$champs[$i]]) OR array_search($key, $block_to_hide[$champs[$i]]) === false) {
                        $methode = "fin".$action[3];
                        if($action[0]=="F" OR $action[0]=="DF") {
                                $this->$methode($action,$validation,$DEBUG);
                        }
                    }
                }
            } else {
                // On affiche le champ
                $this->afficherChamp($champs[$i], $validation, $DEBUG );
            }
        }

        // Fermeture du div form-content
        echo '</div>';
    }

    /**
     * Permet de définir si la balise passée en paramètre doit être afficher
     * selon les champs affichée entre celle-ci et sa balise fermante.
     *
     * @param string $champ_debut Nom du champ.
     * @param string $id_bloc
     * @param array $champs Liste des champs.
     *
     * @return boolean
     */
    function isBlocPrintable($champ_debut, $id_bloc, $champs) {
        // Initialisation du niveau hierarchique des blocs à -1 pour
        // commencer pas traiter le bloc courant et pas uniquement
        // les blocs imbriqués, dans la suite des traitements
        $level = -1;
        // Récupération du type de bloc ouvrant pour chercher le bloc fermant correspondant
        $type_bloc = $this->layout[$champ_debut][$id_bloc][3];
        // Récupération de l'index de debut d'itération
        $index_champ = array_search($champ_debut, $champs);
        // Si le champ_debut n'est pas trouvé dans la liste des champs on retourne False
        if ($index_champ === false) {
            return false;
        }

        // Parcours séquentiel sur la liste des champs, en allant au maximum jusqu'au dernier champ.
        // Si le bloc/fieldset se ferme avant le dernier champ
        // on sortira sur le bloc/fieldset fermant.
        for ($index_champ; $index_champ < count($champs); $index_champ++) {
            // Test de la présence ou pas du champ courant dans la liste des champs comportant
            // des blocs ou fieldsets :
            // - si le champ figure dans la liste des blocs on va boucler sur les blocs inclus
            //   et on va voir s'ils comportent des champs affichés
            // - sinon on a affaire à un bloc comportant uniquement des champs et on va en
            //   tester l'affichage
            if (isset($this->layout[$champs[$index_champ]])) {
                // traitement des blocs inclus ( on commence par le bloc courant )
                //
                // récupération de la liste des blocs ou fieldsets arrachés au champ courant
                $champ_layout = $this->layout[$champs[$index_champ]];
                //
                // Initialisation de l'index du sousbloc (niveau hiérarchique de bloc traité)
                // On ne prend l'index que sur les blocs rattachés au même champ
                // que le bloc courant.
                // Si le cloc n'est pas rattaché au champ courant on positionne l'index à 0.
                // Exemple : 0 si le bloc traité est le premier bloc du champ
                //
                if ($champs[$index_champ] == $champ_debut) {
                    $index_sousbloc = $id_bloc;
                } else {
                    $index_sousbloc = 0;
                }

                // Boucle de traitement des champs de type bloc rattachés au champ courant :
                // Pour tous les champs suivant on vérifie si une balise fermante correspond
                // à celle ouverte afin de stoper la boucle
                while (isset($champ_layout[$index_sousbloc])) {
                    //
                    // Si un champ doit être affiché :
                    // test du type
                    if ((isset($this->type[$champs[$index_champ]])
                         and $this->type[$champs[$index_champ]] != "hidden")
                        || !isset($this->type[$champs[$index_champ]])) {
                        // Le champ est affiché : le bloc doit être affiché
                        return true;
                    }
                    // Test si la fin du bloc a été trouvé (level 0) et que aucun champ n'est affiché ;
                    // sinon on gère le niveau de sousbloc (level)
                    if ($level == 0
                        and $type_bloc == $champ_layout[$index_sousbloc][3]
                        and $champ_layout[$index_sousbloc][0] == "F") {
                        return array(array($champ_debut, $id_bloc),array($champs[$index_champ], $index_sousbloc));
                    } elseif ($level > 0
                              and $type_bloc == $champ_layout[$index_sousbloc][3]
                              and $champ_layout[$index_sousbloc][0] == "F") {
                        // Un bloc du même type est fermé
                        $level --;
                    } elseif ($type_bloc == $champ_layout[$index_sousbloc][3]
                              and $champ_layout[$index_sousbloc][0] == "D") {
                        // Un bloc du même type est ouvert
                        $level ++;
                    }
                    // on boucle sur le sousbloc suivant
                    $index_sousbloc++;
                }

            } else {
                // On a un champ normal à tester, sans sous-bloc : est-ce qu'il est affiché ?
                if ((isset($this->type[$champs[$index_champ]])
                     and $this->type[$champs[$index_champ]] != "hidden")
                    || !isset($this->type[$champs[$index_champ]])) {
                    return true;
                }
            }
        }
        // aucun champ n'est affiché
        return false;
    }

    /**
     * Permet d'afficher le portlet d'actions contextuelles.
     *
     * @param string $idx      Identifiant de l'objet en question.
     * @param array  $actions  Tableau d'actions à afficher.
     * @param sting  $sousform Objet correspondant au sous-formulaire ou null.
     *
     * @return void
     */
    function afficher_portlet($idx, $actions = array(), $sousform = null) {
        // affichage du portlet d'actions contextuelles
        $this->f->layout->display_formulaire_portlet_start();
        // boucle sur les actions ordonnees
        foreach ($actions as $key => $action) {
            //
            $action_href = "#";
            if (isset($action["href"])) {
                $action_href = $action["href"];
            }
            //
            $action_id = "";
            if (isset($action["id"]) && trim($action["id"]) != "") {
                $action_id = " id=\"".trim($action["id"])."\"";
            }
            //
            $action_target = "";
            if (isset($action["target"]) && trim($action["target"]) != "" ) {
                $action_target = " target=\"".trim($action["target"])."\"";
            }
            //
            $action_class = "";
            if (isset($action["class"]) && trim($action["class"]) != "") {
                $action_class = " class=\"".trim($action["class"])."\"";
            }
            //
            $action_description = "";
            if (isset($action["description"]) && trim($action["description"]) != "") {
                $action_description = " title=\"".trim($action["description"])."\"";
            }
            //
            $action_libelle = $action["libelle"];
            //
            echo sprintf(
                '<li><a href="%s"%s%s%s%s>%s</a></li>',
                $action_href,
                $action_id,
                $action_class,
                $action_target,
                $action_description,
                $action_libelle
            );
        }
        // fermeture du portlet
        $this->f->layout->display_formulaire_portlet_end();
    }

    // }}}

    /**
     * Cette meethode permet d'unifier la nouvelle facon d'afficher avec l'ancienne :
     * les tableaux regroupe et groupe sont inseres dans layout
     *
     * @param array $champs Liste des identifiants des champs
     */
    function transformGroupAndRegroupeToLayout($champs) {
        for ($i = 0; $i < count($champs); $i++) {
            if(isset($this->regroupe[$champs[$i]]) AND $this->regroupe[$champs[$i]][0]!="G") {
                if(!isset($this->layout[$champs[$i]])) {
                    $this->layout[$champs[$i]]=array();
                }
                if($this->regroupe[$champs[$i]][0]=="D") {
                    //Ajout du regroupe en debut du tableau $this->layout
                    array_push($this->layout[$champs[$i]], array($this->regroupe[$champs[$i]][0],$this->regroupe[$champs[$i]][1],$this->regroupe[$champs[$i]][2],"Fieldset"));
                }
                if ($this->regroupe[$champs[$i]][0]=="F" OR $this->regroupe[$champs[$i]][0]=="DF") {
                    //Ajout du regroupe en fin du tableau $this->layout
                    array_unshift($this->layout[$champs[$i]], array($this->regroupe[$champs[$i]][0],$this->regroupe[$champs[$i]][1],$this->regroupe[$champs[$i]][2],"Fieldset"));
                }
            }
            if(isset($this->groupe[$champs[$i]]) AND $this->groupe[$champs[$i]][0]!="G") {
                if(!isset($this->layout[$champs[$i]])) {
                    $this->layout[$champs[$i]]=array();
                }
                if($this->groupe[$champs[$i]][0]=="D" OR $this->groupe[$champs[$i]][0]=="DF") {
                    //Ajout du groupe en debut du tableau $this->layout
                    array_push($this->layout[$champs[$i]], array($this->groupe[$champs[$i]][0],$this->groupe[$champs[$i]][1],$this->groupe[$champs[$i]][2]." group","Bloc"));
                }
                if($this->groupe[$champs[$i]][0]=="F") {
                    //Ajout du groupe en fin du tableau $this->layout
                    array_unshift($this->layout[$champs[$i]], array($this->groupe[$champs[$i]][0],$this->groupe[$champs[$i]][1],$this->groupe[$champs[$i]][2]." group","Bloc"));
                }
            }
        }
    }

    /**
     * Récupération des variables sous formulaires.
     *
     * @param array   $champs     Tableau des champs du formulaire.
     * @param integer $validation Marqueur de validation du formulaire :
     *                             - 0   : 1er passage,
     *                             - > 0 : passage suivant.
     * @param array   $postVar    Tableau des valeurs postées.
     * @param boolean $DEBUG      Paramètre inutilisé.
     *
     * @return void
     */
    function recupererPostvarsousform($champs, $validation, $postVar, $DEBUG = false) {
        // Il n'y a pas de raison d'avoir de différences entre form et sousform
        // à ce niveau là. On appelle donc la méthode recupererPostvar standard
        // même en sousform.
        $this->recupererPostvar($champs, $validation, $postVar, $DEBUG);
    }

    /**
     * Récupération des variables formulaires.
     *
     * @param array   $champs     Tableau des champs du formulaire.
     * @param integer $validation Marqueur de validation du formulaire :
     *                             - 0   : 1er passage,
     *                             - > 0 : passage suivant.
     * @param array   $postVar    Tableau des valeurs postées.
     * @param boolean $DEBUG      Paramètre inutilisé.
     *
     * @return void
     */
    function recupererPostvar($champs, $validation, $postVar, $DEBUG = false) {
        //
        $this->addToLog(
            __METHOD__."(): \$this->val = ".print_r($this->val, true),
            EXTRA_VERBOSE_MODE
        );
        //
        $this->addToLog(
            __METHOD__."(): \$postVar = ".print_r($postVar, true),
            EXTRA_VERBOSE_MODE
        );
        //
        $encodages = array("UTF-8", "ASCII", "Windows-1252", "ISO-8859-15", "ISO-8859-1");
        // Compatibilité Windows ? iconv sur la valeur "UTF8" au lieu de
        // "UTF-8" renvoi une erreur.
        $dbcharset = (DBCHARSET == "UTF8" ? "UTF-8" : DBCHARSET);
        //
        for ($i = 0; $i < count($champs); $i++) {
            //
            if ($validation > 0) {
                // magic_quotes_gpc est initialise dans php.ini
                // mise automatique de quote quand il y a un ", \ , '.
                if ($this->type[$champs[$i]] == "textdisabled"
                    or $this->type[$champs[$i]] == "static") {
                    // On affecte la valeur vide
                    $this->val[$champs[$i]] = "";
                } elseif ($this->type[$champs[$i]] == "checkbox_multiple"
                        || $this->type[$champs[$i]] == "select_multiple") {
                    // cas de checkbox/select multiple : les valeurs renvoyees
                    // dans le post sont dans un tableau donc ici les valeurs
                    // sont linearises dans une chaine avec separateur ;
                    if (isset($postVar[$champs[$i]])) {
                        // On linéarise le tableau
                        $value = implode(";", $postVar[$champs[$i]]);
                        // Cette fonction a été supprimée avec PHP 5.4.0 et
                        // renvoi toujours la valeur FALSE depuis
                        // @deprecated
                        if (get_magic_quotes_gpc()) {
                            // Supprime les antislashs de la chaîne
                            $value = stripslashes($value);
                        }
                        // On encode les valeurs reçues dans l'encodage de la
                        // base de données
                        $value = iconv(
                            mb_detect_encoding($value, $encodages),
                            $dbcharset,
                            $value
                        );
                        // On affecte la valeur transformée
                        $this->val[$champs[$i]] = $value;
                    } else {
                        // On affecte la valeur vide
                        $this->val[$champs[$i]] = "";
                    }
                } elseif (isset($postVar[$champs[$i]])) {
                    // cas standard
                    $value = $postVar[$champs[$i]];
                    // Cette fonction a été supprimée avec PHP 5.4.0 et
                    // renvoi toujours la valeur FALSE depuis
                    // @deprecated
                    if (get_magic_quotes_gpc()) {
                        // Supprime les antislashs de la chaîne
                        $value = stripslashes($value);
                    }
                    // On remplace les doubles quotes par des simples quotes ?
                    $value = strtr($value, chr(34), "'");
                    // On encode les valeurs reçues dans l'encodage de la
                    // base de données
                    $value = iconv(
                        mb_detect_encoding($value, $encodages),
                        $dbcharset,
                        $value
                    );
                    // On affecte la valeur transformée
                    $this->val[$champs[$i]] = $value;
                } else {
                    // On affecte la valeur vide
                    $this->val[$champs[$i]] = "";
                }
            }
        }
        //
        $this->addToLog(
            __METHOD__."(): \$this->val = ".print_r($this->val, true),
            EXTRA_VERBOSE_MODE
        );
    }

    // {{{ Accesseurs et mutateurs

    /**
     * Methode permettant de definir la liste des champs obligatoires
     *
     * @param array champs
     */
    function setRequired($champ) {
        $this->required_field[] = $champ;
    }

    /**
     * Mutateur pour la propriété 'val'.
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setVal($champ, $contenu) {
        $this->val[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'type'.
     *
     * @param string $champ
     * @param string $contenu Type de champ :
     *                        - 'text' : saisie texte alpha numerique
     *                        - 'hidden' : le champ est cache, le parametre est
     *                        passe
     *                        - 'password' : saisie masquee
     *                        - 'static' : champ uniquement affiche
     *                        - 'date' : saisie de date
     *                        - 'hiddenstatic' : champ affiche et passage du
     *                        parametre
     *                        - 'select' : zone de selection soit sur une autre
     *                        table, soit par rapport a un tableau
     * @return void
     */
    function setType($champ, $contenu) {
        $this->type[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'lib'.
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setLib($champ, $contenu) {
        $this->lib[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'max'.
     *
     * Maximum autorise a la saisie
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setMax($champ, $contenu) {
        $this->max[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'taille'.
     *
     * Taille du controle
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setTaille($champ, $contenu) {
        $this->taille[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'select'.
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setSelect($champ, $contenu) {
        /*
        GESTION DES TABLES ET PASSAGE DE PARAMETRES
        valeur de $select ============================================================
        TABLE ------------------------------------------------------------------------
        select['nomduchamp'][0]= value de l option
        $select['nomduchamp'][1]= affichage
        COMBO (recherche de correspondance entre table importante)--------------------
        $select['code_departement_naissance'][0][0]="departement";// table
        $select['code_departement_naissance'][0][1]="code"; // zone origine
        $select['code_departement_naissance'][1][0]="libelle_departement"; // zone correl
        $select['code_departement_naissance'][1][1]="libelle_departement_naissance"; // champ correl
        (facultatif)
        $select['code_departement_naissance'][2][0]="code_departement"; // champ pour le where
        $select['code_departement_naissance'][2][1]="code_departement_naissance"; // zone du formulaire concerne
        TEXTAREAMULTI ----------------------------------------------------------------
        select['nomduchamp'][0]=  nom du champ origine pour recuperer la valeur
        -------------------------------------------------------------------------------
        */
        $this->select[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'onchange'.
     *
     * @param string $champ
     * @param string $contenu
     *
     * @return void
     */
    function setOnchange($champ, $contenu) {
        $this->onchange[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'onkeyup'.
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setOnkeyup($champ, $contenu) {
        $this->onkeyup[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'onclick'.
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setOnclick($champ, $contenu) {
        $this->onclick[$champ] = $contenu;
    }

    /**
     * Mutateur pour la propriété 'groupe'.
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setGroupe($champ, $contenu, $libelle = "", $style = "") {
        $this->groupe[$champ][0] = $contenu;
        $this->groupe[$champ][1] = $libelle;
        $this->groupe[$champ][2] = $style;
    }

    /**
     * Mutateur pour la propriété 'regroupe'.
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setRegroupe($champ, $contenu, $libelle, $style = "") {
        $this->regroupe[$champ][0] = $contenu;
        $this->regroupe[$champ][1] = $libelle;
        $this->regroupe[$champ][2] = $style;
    }

    /**
     * Mutateur pour la propriété 'layout'.
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setBloc($champ, $contenu, $libelle = "", $style = "") {
        $this->layout[$champ][] = array($contenu, $libelle, $style, 'Bloc');
    }

    /**
     * Mutateur pour la propriété 'layout'.
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setFieldset($champ, $contenu, $libelle = "", $style = "") {
        $this->layout[$champ][] = array($contenu, $libelle, $style, 'Fieldset');
    }

    /**
     * Valeurs de tous les paramètres.
     * @var array
     */
    var $parameters = array();

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

    // {{{ Méthodes utilitaires

    /**
     * Affichage de la date suivant le format de la base de donnees
     *
     * @param string $val
     * @return string
     */
    function dateAff($val) {

        if (OM_DB_FORMATDATE == "AAAA-MM-JJ") {
            $valTemp = explode("-", $val);
            if( count($valTemp) == 3 ) {
                return $valTemp[2]."/".$valTemp[1]."/".$valTemp[0];
            }else{
                return $val;
            }
        }
        //
        if (OM_DB_FORMATDATE == "JJ/MM/AAAA") {
            return $val;
        }

    }

    /**
     * Cette méthode permet de transformer une chaine de caractère standard
     * en une chaine utilisable comme sélecteur css. Le principe est de
     * supprimer les espaces, les caractères spéciaux et les accents.
     *
     * @param string $string La chaine de caractère à normaliser
     *
     * @return string La chaine de caractère normalisée
     */
    function normalize_string($string = "") {
        //
        $invalid = array('Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z',
            'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'À'=>'A', 'Á'=>'A',
            'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E',
            'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
            'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a',
            'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e',  'ë'=>'e', 'ì'=>'i', 'í'=>'i',
            'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o',
            'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y',
            'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', "`" => "", "´" => "",
            "„" => "", "`" => "", "´" => "'", "“" => "\"", "”" => "\"",
            "´" => "'", "&acirc;€™" => "'", "{" => "", "~" => "", "–" => "-",
            "’" => "",  "(" => "",  ")" => "", " " => "-", "/"=>"-", "'"=>"_",
        );

        $string = str_replace(array_keys($invalid), array_values($invalid), $string);
        $string = strtolower($string);
        return $string;
    }

    // }}}

    // {{{ WIDGET_FORM - START

    /**
     * WIDGET_FORM - affichepdf.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function affichepdf($champ, $validation, $DEBUG = false) {

        //
        $scan_pdf = $this->val[$champ];
        //
        echo "<object data='".$scan_pdf."' name='".$champ."' value=\"".
        $scan_pdf."\" type='application/pdf' width='650' height='200'>";
        echo "</object>";

    }

    /**
     * Type de champ permettant une recherche dynamique sur une clé étrangère
     *
     * @param string  $champ      Nom du champ (= nom de la table liée)
     * @param integer $validation
     * @param boolean $DEBUG      Paramètre inutilisé
     */
    function autocomplete($champ, $validation, $DEBUG = false) {
        // Récupération des critères de recherche (colonnes et libellés associés)
        $criteres = $this->select[$champ]['criteres'];
        // Récupération de la classe de l'objet
        $obj = $this->select[$champ]['obj'];
        // Rédaction de l'infobulle sur les critères de recherche
        // et création d'un tableau contenant les colonnes
        $nb_criteres = count($criteres);
        $i = 1;
        $info = __("Criteres de recherche : ");
        foreach ($criteres as $colonne => $libelle) {
            $info .= $libelle;
            if ($i < $nb_criteres) {$info .= ", ";}
            $i ++;
        }

        /*
         * Affichage du widget
         */
        echo "<div class=\"autocomplete-container\" id=\"autocomplete-".$obj."\">\n";
        // Dans le lien vers le snippet on ajoute l'objet source et son
        // identifiant
        $link = sprintf('%s&snippet=autocomplete&obj_from=%s&idx_from=%s&action=%s&retourformulaire=%s&idxformulaire=%s&field=%s',
            "".OM_ROUTE_FORM,
            $this->getParameter('obj'),
            $this->getParameter('idx'),
            $this->getParameter('maj'),
            $this->getParameter('retourformulaire'),
            $this->getParameter('idxformulaire'),
            $champ
        );
        //
        printf(
            '<span style="display:none;" data-href="%s" class="form-snippet-autocomplete"><!-- --></span>',
            $link
        );
        // Champ de saisie
        echo "<input";
        echo " id=\"autocomplete-".$obj."-search\"";
        echo " class=\"autocomplete champFormulaire\"";
        echo " size=\"30\"";
        if ($this->correct) {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
        // Infobulle critères de recherche
        echo "<span id=\"autocomplete-".$obj."-info\"  class=\"autocomplete autocomplete-info\">";
        echo "<span title=\"".$info."\" class=\"info-16\">";
        echo "</span>";
        echo "</span>\n";
        // Infobulle enregistrement lié
        echo "<span id=\"autocomplete-".$obj."-check\"  class=\"autocomplete autocomplete-check\">";
        echo "<span title=\"".__("Liaison faite avec l'enregistrement de type ").__($champ)."\" class=\"check-16\">";
        echo "</span>";
        echo "</span>\n";
        // Bouton effacer
        echo "<a id=\"autocomplete-".$obj."-empty\"  class=\"autocomplete autocomplete-empty ui-state-default ui-corner-all\">";
        echo "<span title=\"".__("Delier l'enregistrement")."\" class=\"ui-icon ui-icon-closethick\">";
        echo __("Delier");
        echo "</span>";
        echo "</a>\n";
        // Affichage d'un bouton paramétrable si la valeur est activée dans le select
        if (isset($this->select[$champ]['link_selection']) AND $this->select[$champ]['link_selection'] === true) {
            $this->autocomplete_link_selection($champ, $validation, $DEBUG);
        }
        // Champ de valeur
        echo "<input";
        echo " id=\"autocomplete-".$obj."-id\"";
        echo " type=\"hidden\"";
        echo " name=\"".$champ."\"";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire autocomplete-id\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
        echo "</div>\n";
    }

    /**
     * WIDGET_FORM - autocomplete_link_selection
     *
     * Cette méthode permet d'ajouter un élément dans le widget de formulaire autocomplete.
     * Elle est à surcharger dans chaque application selon les besoins.
     *
     * @param string  $champ      Nom du champ
     * @param integer $validation Validation
     * @param boolean $DEBUG      Parametre inutilise
     *
     * @return void
     */
    function autocomplete_link_selection($champ, $validation, $DEBUG = false) {

    }

    /**
     * WIDGET_FORM - checkbox.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkbox($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] == 1 || $this->val[$champ] == "t"
            || $this->val[$champ] == "Oui") {
            $value = "Oui";
            $checked = " checked=\"checked\"";
        } else {
            $value = "";
            $checked = "";
        }
        //
        echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$value."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo $checked;
        if (!$this->correct) {
            echo " onchange=\"changevaluecheckbox(this);";
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo "".$this->onchange[$champ]."";
            }
            echo "\"";
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - checkboxdisabled.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkboxdisabled($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] == 1 || $this->val[$champ] == "t"
            || $this->val[$champ] == "Oui") {
            $value = "Oui";
            $checked = " checked=\"checked\"";
        } else {
            $value = "";
            $checked = "";
        }
        //
        echo "<input";
        echo " type=\"checkbox\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$value."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " disabled=\"disabled\"";
        echo $checked;
        if (!$this->correct) {
            echo " onchange=\"changevaluecheckbox(this);";
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo "".$this->onchange[$champ]."";
            }
            echo "\"";
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        }
        echo " />\n";
    }

    /**
     * WIDGET_FORM - checkboxhiddenstatic.
     *
     * La valeur du champ est pas passée, affichage du champ en texte.
     *
     * @param string  $champ      Nom du champ
     * @param integer $validation Validation
     * @param boolean $DEBUG      Parametre inutilise
     *
     * @return void
     */
    function checkboxhiddenstatic($champ, $validation, $DEBUG = false) {
        // Input de type hidden pour passer la valeur à la validation du
        // formulaire
        echo "<input";
        echo " type=\"hidden\"";
        echo " id=\"".$champ."\"";
        echo " name=\"".$champ."\"";
        echo " value=\"".$this->val[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " />\n";

        // Affichage de la valeur 'Oui' ou 'Non'
        if ($this->val[$champ] == 1 || $this->val[$champ] == "t"
            || $this->val[$champ] == "Oui") {
            //
            $value = "Oui";
        } else {
            //
            $value = "Non";
        }
        //
        echo "<span id=\"".$champ."\" class=\"field_value\">$value</span>";
    }

    /**
     * WIDGET_FORM - checkboxnum.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkboxnum($champ, $validation, $DEBUG = false) {

        //
        if($this->val[$champ] == 1) {
            $value = 1;
            $checked = "checked ";
        } else {
            $value = 0;
            $checked = "";
        }
        //
        if (!$this->correct) {
            if ($this->onchange != "") {
                echo "<input type='checkbox' ";
                echo "name='".$champ."' ";
                echo "value=\"$value\" ";
                echo " id=\"".$champ."\" ";
                echo "size=".$this->taille[$champ]." ";
                echo "maxlength=".$this->max[$champ]." ";
                echo "onchange=\"changevaluecheckboxnum(this);".$this->onchange[$champ]."\" ";
                echo "class='champFormulaire' ";
                echo $checked;
                echo ">\n";
            } else {
                echo "<input type='checkbox' ";
                echo "name='".$champ."' ";
                echo "value=\"$value\" ";
                echo " id=\"".$champ."\" ";
                echo "size=".$this->taille[$champ]." ";
                echo "maxlength=".$this->max[$champ]." ";
                echo "onchange=\"changevaluecheckboxnum(this)\" ";
                echo "class='champFormulaire' ";
                echo $checked;
                echo ">\n";
            }
        } else {
            echo "<input type='checkbox' ";
            echo "name='".$champ."' ";
            echo "value=\"$value\" ";
            echo " id=\"".$champ."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "onchange=\"changevaluecheckboxnum(this)\" ";
            echo "class='champFormulaire' ";
            echo "disabled=\"disabled\" ";
            echo $checked;
            echo ">\n";
        }

    }

    /**
     * WIDGET_FORM - checkboxstatic.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkboxstatic($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] == 1 || $this->val[$champ] == "t"
            || $this->val[$champ] == "Oui") {
            $value = "Oui";
        } else {
            $value = "Non";
        }
        echo "<span id=\"".$champ."\" class=\"field_value\">$value</span>";
    }

    /**
     * WIDGET_FORM - checkbox_multiple.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkbox_multiple($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // CHECKBOX_MULTIPLE
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        // ****************************************************************************
        // Delinearisation
        $selected_values = explode(";", $this->val[$champ]);
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k <count($elem)) {
                //
                //
                echo "<input";
                echo " type=\"checkbox\"";
                echo " name=\"".$champ."[]\"";
                echo " value=\"".$this->select[$champ][0][$k]."\"";
                echo " class=\"champFormulaire\"";
                if (in_array($this->select[$champ][0][$k], $selected_values)) {
                    echo " checked=\"checked\"";
                }
                if (!$this->correct) {
                    if (isset($this->onchange) and $this->onchange[$champ] != "") {
                        echo " onchange=\"".$this->onchange[$champ]."\"";
                    }
                    if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                        echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
                    }
                    if (isset($this->onclick) and $this->onclick[$champ] != "") {
                        echo " onclick=\"".$this->onclick[$champ]."\"";
                    }
                } else {
                    echo " disabled=\"disabled\"";
                }
                echo " />\n";
                echo $this->select[$champ][1][$k];
                echo "<br/>";
                $k++;
                //
            }
        }

    }

    /**
     * WIDGET_FORM - comboD.
     *
     * Combo droit (recherche de correspondance entre table importante)
     * $select['code_departement_naissance'][0][0]="departement";// table
     * $select['code_departement_naissance'][0][1]="code"; // zone origine
     * $select['code_departement_naissance'][1][0]="libelle_departement"; // zone correl
     * $select['code_departement_naissance'][1][1]="libelle_departement_naissance"; // champ correl
     * (facultatif)
     * $select['code_departement_naissance'][2][0]="code_departement"; // champ pour le where
     * $select['code_departement_naissance'][2][1]="code_departement_naissance"; // zone du formulaire concern?
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboD($champ, $validation, $DEBUG = false) {

        echo "<input ";
        echo "type=\"text\" ";
        //echo "type=\"".$this->type[$champ]."\" ";
        echo "name=\"".$champ."\" ";
        echo " id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo " autocomplete=\"off\" ";
        echo "size=\"".$this->taille[$champ]."\" ";
        echo "maxlength=\"".$this->max[$champ]."\" ";
        if (!$this->correct) {
            if ($this->onchange != "") {
                echo "onchange=\"".$this->onchange[$champ]."\" ";
            }
        } else {
            echo "disabled=\"disabled\" ";
        }
        echo "class=\"champFormulaire combod\" ";
        echo "/>\n";

        if (!$this->correct) {
            //
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1";  // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params = "&amp;table=".$tab."&amp;correl=".$correl."&amp;zorigine=".$zorigine."&amp;zcorrel=".$zcorrel."&amp;correl2=".$correl2;
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-combo"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=combo"
            );
            //
            echo "<a class=\"combod ui-state-default ui-corner-all\" href=\"javascript:vcorrel('".$champ."','".$zcorrel2."','".$params."');\">\n";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-e\" ";
            echo "title=\"".__("Cliquer ici pour correler")."\">";
            echo "-> ".__("Correler");
            echo "</span>";
            echo "</a>\n";
        }

    }

    /**
     * WIDGET_FORM - comboD2.
     *
     * combo D2 pour F2 (sousformdyn)
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboD2($champ, $validation, $DEBUG = false) {

        if ($this->correct) {
            echo "<input type='".$this->type[$champ]."' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size='".$this->taille[$champ]."' ";
            echo "maxlength='".$this->max[$champ]."' ";
            echo "class='champFormulaire combod' ";
            echo "disabled=\"disabled\" ";
            echo ">\n";
        } else {
            echo "<input type='".$this->type[$champ]."' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo " autocomplete=\"off\" ";
            echo "size='".$this->taille[$champ]."' ";
            echo "maxlength='".$this->max[$champ]."' ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire combod' ";
            echo ">\n";
            //
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1";  // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params = "&table=".$tab."&correl=".$correl."&zorigine=".$zorigine."&zcorrel=".$zcorrel."&correl2=".$correl2;
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-combo"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=combo"
            );
            // appel vcorrel2
            echo "<a class=\"combod ui-state-default ui-corner-all\" href=\"javascript:vcorrel2('".$champ."','".$zcorrel2."','".$params."');\">";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-e\" ";
            echo "title=\"".__("Cliquer ici pour correler")."\">";
            echo "-> ".__("Correler");
            echo "</span>";
            echo "</a>\n";
        }

    }

    /**
     * WIDGET_FORM - comboG.
     *
     * Combo gauche
     * (recherche de correspondance entre table importante)
     * $select['code_departement_naissance'][0][0]="departement";// table
     * $select['code_departement_naissance'][0][1]="code"; // zone origine
     * $select['code_departement_naissance'][1][0]="libelle_departement"; // zone correl
     * $select['code_departement_naissance'][1][1]="libelle_departement_naissance"; // champ correl
     * (facultatif)
     * $select['code_departement_naissance'][2][0]="code_departement"; // champ pour le where
     * $select['code_departement_naissance'][2][1]="code_departement_naissance"; // zone du formulaire concernee
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboG($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            // zone libelle
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1"; // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params = "&amp;table=".$tab."&amp;correl=".$correl."&amp;zorigine=".$zorigine."&amp;zcorrel=".$zcorrel."&amp;correl2=".$correl2;
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-combo"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=combo"
            );
            //
            echo "<a class=\"combog ui-state-default ui-corner-all\" href=\"javascript:vcorrel('".$champ."','".$zcorrel2."','".$params."');\">\n";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-w\" ";
            echo "title=\"".__("Cliquer ici pour correler")."\">";
            echo "<- ".__("Correler");
            echo "</span>";
            echo "</a>\n";
        }

        echo "<input ";
        echo "type=\"text\" ";
        //echo "type=\"".$this->type[$champ]."\" ";
        echo " autocomplete=\"off\" ";
        echo "name=\"".$champ."\" ";
        echo " id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo "size=\"".$this->taille[$champ]."\" ";
        echo "maxlength=\"".$this->max[$champ]."\" ";
        if (!$this->correct) {
            if ($this->onchange != "") {
                echo "onchange=\"".$this->onchange[$champ]."\" ";
            }
        } else {
            echo "disabled=\"disabled\" ";
        }
        echo "class=\"champFormulaire combog\" ";
        echo "/>\n";

    }

    /**
     * WIDGET_FORM - comboG2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboG2($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            // zone libelle
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1"; // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params="&table=".$tab."&correl=".$correl."&zorigine=".$zorigine."&zcorrel=".$zcorrel."&correl2=".$correl2;
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-combo"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=combo"
            );
            // appel vcorrel2
            echo "<a class=\"combog ui-state-default ui-corner-all\" href=\"javascript:vcorrel2('".$champ."','".$zcorrel2."','".$params."');\">";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-w\" ";
            echo "title=\"".__("Cliquer ici pour correler")."\">";
            echo "<- ".__("Correler");
            echo "</span>";
            echo "</a>";
            //
            echo "<input type=".$this->type[$champ]." ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo " autocomplete=\"off\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire combog' ";
            echo ">\n";
        } else {
            echo "<input type=".$this->type[$champ]." ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "class='champFormulaire combog' ";
            echo "disabled=\"disabled\"";
            echo ">\n";
        }

    }

    /**
     * WIDGET_FORM - date.
     *
     * La date est saisie ou affichee sous le format JJ/MM/AAAA, un calendrier
     * s affiche en js
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function date($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $this->val[$champ] = $this->dateAff($this->val[$champ]);
        }
        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"10\"";
        if (!$this->correct) {
            echo " class=\"champFormulaire datepicker\"";
            if ($this->onchange != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if ($this->onkeyup != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if ($this->onclick != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " class=\"champFormulaire\"";
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - date2.
     *
     * Date en Full Onglet, la date est saisie ou affichee sous le format
     * JJ/MM/AAAA, un calendrier s affiche en js
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function date2($champ, $validation, $DEBUG = false) {

        //
        $this->date($champ, $validation);

    }

    /**
     * WIDGET_FORM - datedisabled.
     *
     * Champs date disabled
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function datedisabled($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $defautDate = $this->dateAff($this->val[$champ]);
        } else {
            $defautDate = $this->val[$champ];
        }
        //
        if (!$this->correct) {
            echo "<input type='text' ";
            echo "name='".$champ."' ";
            echo "id=\"".$champ."\" ";
            echo "value=\"".$defautDate."\" ";
            echo "class='champFormulaire' disabled=\"disabled\" />\n";
        } else {
            echo $this->val[$champ]."\n";
        }

    }

    /**
     * WIDGET_FORM - datestatic.
     *
     * Date static formatee
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function datestatic($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "") {
            $defautDate = $this->dateAff($this->val[$champ]);
        } else {
            $defautDate = $this->val[$champ];
        }
        //
        echo "<span id=\"".$champ."\" class=\"field_value\">";
            echo $defautDate."";
        echo "</span>";

    }

    /**
     * WIDGET_FORM - file.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function file($champ, $validation, $DEBUG = false) {
        // Récupération du paramétrage si renseigné
        $obj = (isset($this->select[$champ]['obj'])) ? $this->select[$champ]['obj'] : $this->getParameter("obj");
        $idx = (isset($this->select[$champ]['idx'])) ? $this->select[$champ]['idx'] : $this->getParameter("idx");
        $val = (isset($this->select[$champ]['val'])) ? $this->select[$champ]['val'] : $this->val[$champ];
        $field = (isset($this->select[$champ]['champ'])) ? $this->select[$champ]['champ'] : $champ;
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        echo "<div id=\"".$champ."\">";
        if ($this->f->storage === null) {
            // Message d'erreur
            echo __("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";
            // On sort de la méthode
            return -1;
        }
        //
        if ($val !== "" && $val !== null) {
            //
            $filename = $this->f->storage->getFilename($val);
            //
            if ($filename !== ""
                && $filename !== null
                && $filename !== 'OP_FAILURE') {
                //
                echo $filename;
                //
                $link = "".OM_ROUTE_FORM."&snippet=voir&obj=".$obj."&amp;champ=".$field."&amp;id=".$idx;
                //
                echo "<span class=\"om-prev-icon consult-16\" title=\"".__("Ouvrir le fichier")."\">";
                echo "<a href=\"javascript:load_form_in_modal('".$link."');\" >";
                echo __("Visualiser");
                echo "</a>";
                echo "</span>";
                //
                echo "<span class=\"om-prev-icon reqmo-16\" title=\"".__("Enregistrer le fichier")."\">";
                echo "<a href=\"".OM_ROUTE_FORM."&snippet=file&obj=".$obj."&amp;champ=".$field.
                        "&amp;id=".$idx."\" target=\"_blank\">";
                echo __("Telecharger");
                echo "</a>";
                echo "</span>";
            } else {
                echo __("Le fichier n'existe pas ou n'est pas accessible.");
            }
        }
        echo "</div>";
    }

    /**
     * WIDGET_FORM - filestatic.
     *
     * Affichage du nom du fichier ou d'une erreur si le fichier est inaccessible
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function filestatic($champ, $validation, $DEBUG = false) {
        // Récupération du paramétrage si renseigné
        $val = (isset($this->select[$champ]['val'])) ? $this->select[$champ]['val'] : $this->val[$champ];
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        echo "<div id=\"".$champ."\">";
        if ($this->f->storage == null) {
            // Message d'erreur
            echo __("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";
            // On sort de la méthode
            return -1;
        }
        //
        if ($val !== "" && $val !== null) {
            //
            $filename = $this->f->storage->getFilename($val);
            //
            if ($filename !== ""
                && $filename !== null
                && $filename !== 'OP_FAILURE') {
                //
                echo $filename;
            } else {
                echo __("Le fichier n'existe pas ou n'est pas accessible.");
            }
        }
        echo "</div>";
    }


   /**
     * WIDGET_FORM - Type de champ à utiliser en modification, affichant le nom du fichier
     * non modifiable, sans afficher les boutons de modification. Une erreur est affichée
     * si le fichier est inaccessible.
     *
     * @param string $champ Nom du champ.
     * @param integer $validation
     * @param boolean $DEBUG Paramètre inutilisé.
     *
     * @return void
     */
    function filestaticedit($champ, $validation, $DEBUG = false) {
        // Récupération du paramétrage si renseigné
        $val = (isset($this->select[$champ]['val'])) ? $this->select[$champ]['val'] : $this->val[$champ];
        $taille = (isset($this->select[$champ]['taille'])) ? $this->select[$champ]['taille'] : $this->taille[$champ];
        $max = (isset($this->select[$champ]['max'])) ? $this->select[$champ]['max'] : $this->max[$champ];
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        echo "<div id=\"".$champ."\">";
        if ($this->f->storage == null) {
            // Message d'erreur
            echo __("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";
            // On sort de la méthode
            return -1;
        }
        //
        if ($val !== "" && $val !== null) {
            //
            $filename = $this->f->storage->getFilename($val);
            //
            if ($filename !== ""
                && $filename !== null
                && $filename !== 'OP_FAILURE') {
                //
                echo $filename;
            } else {
                echo __("Le fichier n'existe pas ou n'est pas accessible.");
            }
            //
            echo "<input";
            echo " type=\"hidden\"";
            echo " name=\"".$champ."\"";
            echo " id=\"".$champ."\" ";
            echo " value=\"".$val."\"";
            echo " size=\"".$taille."\"";
            echo " maxlength=\"".$max."\"";
            echo " class=\"champFormulaire\"";
            echo " />\n";
        }
        echo "</div>";
    }


    /**
     * WIDGET_FORM - geom.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function geom($champ, $validation, $DEBUG = false) {
        if (file_exists("../dyn/var.inc")) {
            include "../dyn/var.inc";
        }
        if (!isset($siglien)) {
            $siglien = OM_ROUTE_MAP."&mode=tab_sig&idx=";
        }
        if (isset($this->select[$champ][0][0])
            && isset($this->select[$champ][0][1])
            && isset($this->select[$champ][0][2])) {
            //
            $obj = $this->select[$champ][0][0];
            $idx = $this->select[$champ][0][1];
            $seli = $this->select[$champ][0][2];
            //
            echo "<a class=\"localisation ui-state-default ui-corner-all\" href=\"javascript:localisation_sig('".$siglien."','".$idx."','".$obj."','".$seli."');\">";
            echo "<span class=\"ui-icon sig-16\" ";
            echo "title=\"".__("Cliquer ici pour positionner l'element")."\">";
            echo __("Localisation");
            echo "</span>";
            echo "</a>";
        }
    }

    /**
     * WIDGET_FORM - hidden.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hidden($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        //echo " size=\"".$this->taille[$champ]."\"";
        //echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - hiddendate.
     *
     * Type hidden sur les champs dates.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddendate($champ, $validation, $DEBUG = false) {

        if ($this->val[$champ] != "" and $validation == 0) {
            $this->val[$champ] = $this->dateAff($this->val[$champ]);
        }

        echo "<input";
        echo " type=\"hidden\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"10\"";
        if (!$this->correct) {
            echo " class=\"champFormulaire datepicker\"";
            if ($this->onchange != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if ($this->onkeyup != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if ($this->onclick != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " class=\"champFormulaire\"";
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
    }

    /**
     * WIDGET_FORM - hiddenstatic.
     *
     * La valeur du champ est passe par le controle hidden
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddenstatic($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"hidden\"";
        echo " id=\"".$champ."\"";
        echo " name=\"".$champ."\"";
        echo " value=\"".$this->val[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " />\n";
        echo $this->val[$champ]."\n";

    }

    /**
     * WIDGET_FORM - hiddenstaticdate.
     *
     * La valeur du champ est passe par le controle hidden
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddenstaticdate($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $defautDate = $this->dateAff($this->val[$champ]);
        } else {
            $defautDate = $this->val[$champ];
        }
        //
        if (!$this->correct) {
            echo "<input type='hidden' ";
            echo "name='".$champ."' ";
            echo "id=\"".$champ."\" ";
            echo "value=\"".$defautDate."\" ";
            echo "class='champFormulaire' />\n";
            echo $defautDate."";
        } else {
            echo $this->val[$champ]."\n";
        }

    }

    /**
     * WIDGET_FORM - hiddenstaticnum.
     *
     * La valeur du champ est passe par le controle hidden
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddenstaticnum($champ, $validation, $DEBUG = false) {

        echo "<input type='hidden' ";
        echo "name='".$champ."' ";
        echo "id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo "class='champFormulaire' >\n";
        echo "<p align='right'>".$this->val[$champ]."</p>\n";

    }

    /**
     * WIDGET_FORM - html.
     *
     * Méthode d'affichage de tinyMCE sur textarea
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function html($champ, $validation, $DEBUG = false) {
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        if (!$this->correct) {
            $this->select[$champ]['class'] .= " html";
            $this->textarea($champ, $validation, $DEBUG);
        } else {
            $this->htmlstatic($champ, $validation, $DEBUG);
        }
    }

    /**
     * WIDGET_FORM - htmlEtat.
     *
     * Méthode d'affichage de tinyMCE simplifié pour titre om_etat
     * et om_lettretype
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function htmlEtat($champ, $validation, $DEBUG = false) {
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        if (!$this->correct) {
            $this->select[$champ]['class'] .= " htmletat";
            $this->textarea($champ, $validation, $DEBUG);
        } else {
            $this->htmlstatic($champ, $validation, $DEBUG);
        }
    }

    /**
     * WIDGET_FORM - htmlEtatEx.
     *
     * Méthode d'affichage de tinyMCE extended sur textarea pour
     * corps d'om_etat et om_lettretype
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function htmlEtatEx($champ, $validation, $DEBUG = false) {
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        if (!$this->correct) {
            $this->select[$champ]['class'] .= " htmletatex";
            $this->textarea($champ, $validation, $DEBUG);
        } else {
            $this->htmlstatic($champ, $validation, $DEBUG);
        }
    }

    /**
     * WIDGET_FORM - htmlstatic.
     *
     * Méthode d'affichage du html interprété sur textarea
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function htmlstatic($champ, $validation, $DEBUG = false) {
        echo "<div id='".$champ."'>".$this->val[$champ]."</div>";
    }

    /**
     * WIDGET_FORM - http.
     *
     * lien http en formulaire - passage d argument sur une
     * application tierce
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function http($champ, $validation, $DEBUG = false) {

        //
        if (isset($this->select[$champ][0])) {
            $aff = $this->select[$champ][0];
        } else {
            $aff = $champ;
        }
        //
        echo "<a href=\"".$this->val[$champ]."\" target=\"_blank\">";
        echo $aff;
        echo "</a>\n";

    }

    /**
     * WIDGET_FORM - httpclick.
     *
     * lien http en formulaire - passage d argument sur une
     * application tierce
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function httpclick($champ, $validation, $DEBUG = false) {

        //
        if (isset($this->select[$champ][0])) {
            $aff = $this->select[$champ][0];
        } else {
            $aff = $champ;
        }
        //
        echo "<a href='#' onclick=\"".$this->val[$champ]."; return false;\" >";
        echo $aff;
        echo "</a>\n";

    }

    /**
     * WIDGET_FORM - localisation.
     *
     * - $select['positiony'][0]="plan";// zone plan
     * - $select['positiony'][1]="positionx"; // zone coordonnees X
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function localisation($champ, $validation, $DEBUG = false) {

        //
        echo "<input data-role=\"none\"";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire localisation\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if (!$this->correct) {
            // zone libelle

            $plan = $this->select[$champ][0][0];  // plan
            $positionx = $this->select[$champ][0][1];
            //
            $params = array(
                    "champ" => $champ,
                    "plan" => $plan,
                    "positionx"  => $positionx
                 );
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-localisation"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=localisation"
            );
            //
            $this->f->layout->display_formulaire_localisation_lien($params);
            //
        }

    }

    /**
     * WIDGET_FORM - localisation2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function localisation2($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire localisation\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if (!$this->correct) {
            // zone libelle
            $plan = $this->select[$champ][0][0];  // plan
            $positionx = $this->select[$champ][0][1];
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-localisation"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=localisation"
            );
            //
            echo "<a class=\"localisation ui-state-default ui-corner-all\" href=\"javascript:localisation2('".$champ."','".$plan."','".$positionx."');\">";
            echo "<span class=\"ui-icon ui-icon-pin-s\" ";
            echo "title=\"".__("Cliquer ici pour positionner l'element")."\">";
            echo __("Localisation");
            echo "</span>";
            echo "</a>";
        }

    }

    /**
     * WIDGET_FORM - localisation_edition.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function localisation_edition($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        //
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire localisation\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if (!$this->correct) {
            //
            $format = (isset($this->select[$champ]["format"]) ? $this->select[$champ]["format"] : "");
            $orientation = (isset($this->select[$champ]["orientation"]) ? $this->select[$champ]["orientation"] : "");
            $x = (isset($this->select[$champ]["x"]) ? $this->select[$champ]["x"] : "");
            $y = (isset($this->select[$champ]["y"]) ? $this->select[$champ]["y"] : "");
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-localisation"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=localisation"
            );
            //
            echo "<button class=\"localisation\" type=\"button\" onclick=\"";
            echo "javascript:localisation_edition(form, '".$format."','".$orientation."','".$x."','".$y."');";
            echo "\">";
            echo "<span class=\"ui-icon ui-icon-pin-s\" title=\"".__("Cliquer ici pour positionner l'element")."\">";
            echo __("Localisation");
            echo "</span>";
            echo "</button>";
        }

    }

    /**
     * WIDGET_FORM - mail.
     *
     * Envoi avec le logiciel de messagerie
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function mail($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire mail\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
        //
        $mail = $this->val[$champ];
        //
        echo "<a class=\"mail ui-state-default ui-corner-all\" href='mailto:".$mail."'>";
        echo "<span class=\"ui-icon ui-icon-mail-closed\" ";
        echo "title=\"".__("Cliquer ici pour envoyer un mail a cette adresse")."\">";
        echo __("MailTo");
        echo "</span>";
        echo "</a>";

    }

    /**
     * WIDGET_FORM - pagehtml.
     *
     * Page HTML : les \n => <br> en affichage
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function pagehtml($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $this->val[$champ] = str_replace("<br>", "\n", $this->val[$champ]);
        }
        //
        if (!$this->correct) {
            echo "<textarea ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "cols=".$this->taille[$champ]." ";
            echo "rows=".$this->max[$champ]." ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire' >";
            echo $this->val[$champ];
            echo "</textarea>\n";
        } else {
            echo "<textarea ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "cols=".$this->taille[$champ]." ";
            echo "rows=".$this->max[$champ]." ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire' ";
            echo "disabled=\"disabled\" >";
            echo $this->val[$champ];
            echo "</textarea>\n";
        }

    }

    /**
     * WIDGET_FORM - password.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function password($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - rvb.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function rvb($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire rvb\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - rvb2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function rvb2($champ, $validation, $DEBUG = false) {

        //
        $this->rvb($champ, $validation, $DEBUG);

    }

    /**
     * WIDGET_FORM - select.
     *
     * SELECT - Affichage de table
     * - select['nomduchamp'][0]= value de l option
     * - $select['nomduchamp'][1]= affichage
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function select($champ, $validation, $DEBUG = false) {

        //
        if (!$this->correct) {
            if ($this->onchange[$champ] != "") {
                echo "<select ";
                echo "name='".$champ."' ";
                echo " id=\"".$champ."\" ";
                echo "size='1' ";
                echo "onchange=\"".$this->onchange[$champ]."\" ";
                echo " class=\"champFormulaire\" \n";
                echo " >\n";
            } else {
                $params = array(
                    "champ" => $champ
                );
                $this->f->layout->display_formulaire_select_personnalise($params);
                /*echo "<select ";
                echo "name='".$champ."' ";
                echo " id=\"".$champ."\" ";
                echo "size='1' ";
                echo " class=\"'champFormulaire\" \n";
                echo " >\n";*/
            }
        } else {
            echo "<select ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "size='1' ";
            echo "class='champFormulaire' ";
            echo "disabled=\"disabled\" >\n";
        }
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k <count($elem)) {
                if (!$this->correct) {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]) {
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                    } else {
                        echo "    <option ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                    }
                    $k++;
                } else {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]) {
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\" >";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                        $k = count($elem);
                    }
                    $k++;
                }
            }
        }
        //
        echo "</select>";

    }

    /**
     * WIDGET_FORM - selectdisabled.
     *
     * Affichage champ + lien mais pas modification de
     * donnees $val
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectdisabled($champ, $validation, $DEBUG = false) {

        //
        echo "<select ";
        echo "name='".$champ."' ";
        echo " id=\"".$champ."\" ";
        echo "size='1' ";
        echo "class='champFormulaire' ";
        echo "disabled=\"disabled\">\n";
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k < count($elem)) {
                if (!$this->correct) {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]) {
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                    }
                    $k++;
                } else {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]){
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                        $k = count($elem);
                    }
                    $k++;
                }
            }
        }
        //
        echo "</select>\n";
    }

    /**
     * WIDGET_FORM - selecthiddenstatic.
     *
     * Affichage d'un champ lie avec:
     *
     * - libelle statique
     * - valeur en champ cache
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selecthiddenstatic($champ, $validation, $DEBUG = false) {

        // si la valeur existe dans la liste des valeurs
        if (in_array($this->val[$champ], $this->select[$champ][0])) {

            // recherche du libelle associe a la valeur du champ
            $key = array_search($this->val[$champ], $this->select[$champ][0]);

            // affichage du libelle
            echo '<span class="field_value">';
            echo $this->select[$champ][1][$key];
            echo '</span>';

            // affichage du champ cache
            echo "<input";
            echo " type=\"hidden\"";
            echo " id=\"".$champ."\"";
            echo " name=\"".$champ."\"";
            echo " value=\"".$this->val[$champ]."\"";
            echo " class=\"champFormulaire\"";
            echo " />\n";
        }
    }

    /**
     * WIDGET_FORM - selecthiddenstaticlick.
     *
     * selecthiddenstatic amelioré - lien http sur objet correspondant a la cle etrangere
     * soit dans la meme fenetre soit dans un nouvel onglet (2 boutons)
     * application tierce
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selecthiddenstaticlick($champ, $validation, $DEBUG = false) {
        $this->selecthiddenstatic($champ, $validation, $DEBUG);
        echo "<a class=\"upload ui-state-default ui-corner-all\" href=\"".OM_ROUTE_FORM."&obj=".$champ."&action=3&idx=".$this->val[$champ]."\">";
        echo "<span class=\"ui-icon ui-icon-extlink\" ";
        echo "title=\"".__("Cliquer pour aller a la fiche correspondante")."\">";
        echo __("aller");
        echo "</span>";
        echo "</a>    \n     ";
                echo "<a class=\"upload ui-state-default ui-corner-all\" href=\"".OM_ROUTE_FORM."&obj=".$champ."&action=3&idx=".$this->val[$champ]."\"         target=\"_blank\">";
        echo "<span class=\"ui-icon ui-icon-newwin\" ";
        echo "title=\"".__("Cliquer pour aller a la fiche correspondante dans une nouvelle fenetre")."\">";
        echo __("aller");
        echo "</span>";
        echo "</a>\n";
    }

    /**
     * WIDGET_FORM - selectliste.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectliste($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // SELECTLISTE (liste)
        // affichage de table
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        // ****************************************************************************
        if(!$this->correct) {
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' ";
        if($this->onchange[$champ]!="")
        echo "onchange=\"".$this->onchange[$champ]."\" ";
        if($this->onclick[$champ]!="")
        echo "onclick=\"".$this->onclick[$champ]."\" ";
        echo ">";
        }else
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' disabled=\"disabled\" >";
        $k=0;
        foreach($this->select[$champ] as $elem)
        //  $nOption++;
        while ($k <count($elem)) {
        if(!$this->correct) {
        if ($this->val[$champ]==$this->select[$champ][0][$k])
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        else
        echo "<option value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        $k++;

        }else{
        if ($this->val[$champ]==$this->select[$champ][0][$k]){
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\" >".$this->select[$champ][1][$k]."</option>";
        $k =count($elem);
        }
        $k++;
        }
        }
        echo "</select>";

    }

    /**
     * WIDGET_FORM - selectlistemulti.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectlistemulti($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // SELECTLISTEMULTI (liste)
        // affichage de table
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        //select['nomduchamp'][2]= autre select dont la value peut etre ajoutee
        //select['nomduchamp'][3]= champ cache des values ajoutees ex: 45,12,32
        // ****************************************************************************
        // colones = taille
        // lignes = max
        echo "<table><tr><td>";
        if(!$this->correct) {
        $champ2=$this->select[$champ][2];
        $champ3=$this->select[$champ][3];
        echo "<table border=1 ><tr><td>";
        echo "<input type='button' name='_select$champ' onclick='addlist(\"$champ\",\"$champ2\",\"$champ3\")' value='->' class='boutonmulti'> ";
        echo "</td></tr><tr><td>";
        echo "<input type='button' name='_unselect$champ' onclick='removelist(\"$champ\",\"$champ3\")' value='<-' class='boutonmulti'> ";
        echo "</td></tr><tr><td>";
        echo "<input type='button' name='_unselectall$champ' onclick='removealllist(\"$champ\",\"$champ3\")' value='<<' class='boutonmulti'> ";
        echo "</td></tr></table></td><td>";
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' ";
        if($this->onchange[$champ]!="")
        echo "onchange=\"".$this->onchange[$champ]."\" ";
        if($this->onclick[$champ]!="")
        echo "onclick=\"".$this->onclick[$champ]."\" ";
        echo ">";
        }else
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' disabled=\"disabled\" >";
        $k=0;
        foreach($this->select[$champ] as $elem)
        //  $nOption++;
        while ($k <count($elem)) {
        if(!$this->correct) {
        if ($this->val[$champ]==$this->select[$champ][0][$k])
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        else
        echo "<option value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        $k++;
        }else{
        if ($this->val[$champ]==$this->select[$champ][0][$k]){
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\" >".$this->select[$champ][1][$k]."</option>";
        $k =count($elem);
        }
        $k++;
        }
        }
        echo "</select>";
        echo "</td></tr></table>";

    }

    /**
     * WIDGET_FORM - selectstatic.
     *
     * Affichage d'un champ lie avec:
     *
     * - libelle statique
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectstatic($champ, $validation, $DEBUG = false) {

        // recherche du libelle associe a la valeur du champ
        $key = array_search($this->val[$champ], $this->select[$champ][0]);

        // affichage du libelle
        echo '<span id="'.$champ.'" class="field_value">';
        if ($key !== false) {
            echo $this->select[$champ][1][$key];
        } else {
            echo $this->val[$champ];
        }
        echo '</span>';
    }


    /**
     * WIDGET_FORM - select_multiple.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function select_multiple($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // SELECT_MULTIPLE
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        // ****************************************************************************
        // Delinearisation
        $selected_values = explode(";", $this->val[$champ]);
        //
        echo "<select";
        echo " name=\"".$champ."[]\"";
        echo " id=\"".$champ."\" ";
        echo " multiple=\"multiple\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " class=\"champFormulaire selectmultiple\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo ">\n";
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k <count($elem)) {
                echo "    <option ";
                echo " value=\"".$this->select[$champ][0][$k]."\"";
                if (in_array($this->select[$champ][0][$k], $selected_values)) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo $this->select[$champ][1][$k];
                echo "</option>\n";
                $k++;
            }
        }
        //
        echo "</select>\n";
    }

    /**
     * WIDGET_FORM - select_multiple_static.
     *
     * Ce widget permet d'afficher une liste statique (html) des valeurs
     * d'un champ. Cette liste de valeurs provient de la combinaison entre les
     * valeurs et libellés disponibles dans le paramétrage select de ce champ
     * et entre les valeurs du champ représentées de manière linéaire.
     *
     * Deux contraintes sont présentes ici :
     *  - $this->val[$champ] correspond aux valeurs sélectionnées. Le format
     *    attendu ici dans la valeur du champ est une chaine de caractère
     *    représentant la liste des valeurs sélectionnées séparées par des ;
     *    (points virgules).
     *    Exemple : $this->val[$champ] = string(5) "4;2;3";
     *  - $this->select[$champ] correspond aux libellés de toutes les valeurs
     *    disponibles dans cette liste lors de la modification de l'élément.
     *    Exemple : $this->select[$champ] = array(2) {
     *         [0] => array(3) {
     *           [0] => string(1) "2"
     *           [1] => string(1) "3"
     *           [2] => string(1) "4"
     *         }
     *         [1] => array(3) {
     *           [0] => string(5) "Plans"
     *           [1] => string(7) "Visites"
     *           [2] => string(18) "Dossiers à enjeux"
     *         }
     *       }
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function select_multiple_static($champ, $validation, $DEBUG = false) {
        // Si aucune valeur n'est sélectionnée alors on affiche rien
        if ($this->val[$champ] == "") {
            return;
        }
        // On transforme la chaine de caractère en tableau grâce au
        // séparateur ;
        $selected_values = explode(";", $this->val[$champ]);
        // On affiche la liste
        echo "<ul>";
        // On boucle sur la liste de valeurs sélectionnées
        foreach ($selected_values as $value) {
            //
            echo "<li>";
            // On affiche le libellé correspondant à la valeur
            echo $this->select[$champ][1][array_search($value, $this->select[$champ][0])];
            //
            echo "</li>";
        }
        //
        echo "</ul>";
    }

    /**
     * WIDGET_FORM - statiq.
     *
     * La valeur du champ n'est pas conservee
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function statiq($champ, $validation, $DEBUG = false) {
        echo "<span class=\"field_value\" id=\"".$champ."\">";
        echo $this->val[$champ]."\n";
        echo "</span>";
    }

    /**
     * WIDGET_FORM - text.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function text($champ, $validation, $DEBUG = false) {
        $text_onchange="";
        $text_onkeyup="";
        $text_onclick="";
        $text_disabled="";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                $text_onchange=" onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                $text_onkeyup= " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                $text_onclick= " onclick=\"".$this->onclick[$champ]."\"";
            }
        }
       $params = array(
        "type" => $this->type[$champ],
        "name" => $champ,
        "id" => $champ,
        "value" => $this->val[$champ],
        "size" => $this->taille[$champ],
        "maxlength" => $this->max[$champ],
        "correct" => $this->correct,
        "onchange" =>$text_onchange,
        "onkeyup" =>  $text_onkeyup,
        "onclick" => $text_onclick
        );
        $this->f->layout->display_formulaire_text($params);
        //

      /*echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
       //
        echo " />\n";*/
    }

    /**
     * WIDGET_FORM - textarea.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textarea($champ, $validation, $DEBUG = false) {

        //
        echo "<textarea";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " cols=\"".$this->taille[$champ]."\"";
        echo " rows=\"".$this->max[$champ]."\"";
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        echo " class=\"champFormulaire ".$this->select[$champ]['class']."\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo ">\n";
        echo $this->val[$champ];
        echo "</textarea>\n";

    }

    /**
     * WIDGET_FORM - textareahiddenstatic.
     *
     * La valeur du champ n est pas passe, affichage du champ en texte
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textareahiddenstatic($champ, $validation, $DEBUG = false) {

        echo "<input type='hidden' ";
        echo "name='".$champ."' ";
        echo "id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo "class='champFormulaire' >\n";
        $this->val[$champ] = str_replace("\n","<br>",$this->val[$champ]);
        echo $this->val[$champ]."\n";

    }

    /**
     * WIDGET_FORM - textareamulti.
     *
     * Recuperation d une valeur dans un champ
     * - le champ d origine = $this->select[$champ][0]
     * - le champ d arrive = $champPage HTML : les \n => <br> en affichage
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textareamulti($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            //
            echo "<input";
            echo " type=\"button\"";
            echo " onclick=\"selectauto('".$champ."','".$this->select[$champ][0]."')\"";
            echo " value=\"->\" ";
            echo " class=\"boutonmulti\"";
            echo " />\n";
        }
        //
        echo "<textarea";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " cols=\"".$this->taille[$champ]."\"";
        echo " rows=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire champmulti\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo ">\n";
        echo $this->val[$champ];
        echo "</textarea>\n";

    }

    /**
     * WIDGET_FORM - textareastatic.
     *
     * Affichage du contenu d'un champ TEXT en conservant les retours a la ligne
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textareastatic($champ, $validation, $DEBUG = false) {
        echo "<span class=\"field_value pre\" id=\"".$champ."\">";
        echo $this->val[$champ];
        echo "</span>";
    }

    /**
     * WIDGET_FORM - textdisabled.
     *
     * pas de passage de parametre
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textdisabled($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " disabled=\"disabled\"";
        echo " />\n";

    }

    /**
     * WIDGET_FORM - textreadonly.
     *
     * champ texte non modifiable - pas de passage de parametre
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textreadonly($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " readonly=\"readonly\"";
        echo " />\n";

    }

    /**
     * WIDGET_FORM - upload.
     *
     * FILE
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function upload($champ, $validation, $DEBUG = false) {
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        if ($this->f->storage == NULL) {
            // Message d'erreur
            echo "<div id=\"".$champ."\">";
            echo __("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";
            // On sort de la méthode
            return -1;
        }

        //
        echo "<input";
        echo " type=\"hidden\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        // Explode de la valeur afin de vérifier si l'uid est temporaire
        $temporary_test = explode("|", $this->val[$champ]);

        //
        $text_onchange="";
        $text_onkeyup="";
        $text_onclick="";
        $text_disabled="";
        $text_value="";
        // Test si une valeur est présente
        if (isset($this->val[$champ]) && !empty($this->val[$champ]) && !$this->correct) {
            // Test si la valeur contient "tmp"
            if (isset($temporary_test[0]) AND $temporary_test[0] == "tmp") {
                // Si la valeur du champ contient effectivement un uid
                if (isset($temporary_test[1])) {
                    $text_value=" value=\"".$this->f->storage->getFilename_temporary($temporary_test[1])."\" ";
                }
            } else {
                // Et si le formulaire à déjà été validé pour afficher le nom du fichier
                $text_value=" value=\"".$this->f->storage->getFilename($this->val[$champ])."\" ";
            }
        } else {
            $text_value=" value=\"\" ";
        }
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                $text_onchange=" onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                $text_onkeyup= " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                $text_onclick= " onclick=\"".$this->onclick[$champ]."\"";
            }
        }
        $params = array(
            "name" => $champ,
            "id" => $champ,
            "correct" => $this->correct,
            "onchange" =>$text_onchange,
            "onkeyup" =>  $text_onkeyup,
            "onclick" => $text_onclick,
            "value" => $text_value
        );
        $this->f->layout->display_formulaire_champs_upload($params);

        //
        if ($this->correct) {
            return -1;
        }

        // Récupération de la configuration du widget
        $configuration = (isset($this->select[$champ]) ? $this->select[$champ] : null);

        // Affichage de l'action 'upload', avec un marqueur permettant de
        // définir le path vers le snippet de formulaire 'upload'.
        $this->f->layout->display_formulaire_lien_vupload_upload(
            $champ,
            $this->getParameter("obj"),
            $this->getParameter("idx"),
            $configuration
        );
        printf(
            '<span style="display:none;" data-href="%s" class="form-snippet-upload"><!-- --></span>',
            "".OM_ROUTE_FORM."&snippet=upload"
        );

        // Affichage de l'action 'voir', avec un marqueur permettant de définir
        // le path vers le snippet de formulaire 'voir'.
        $this->f->layout->display_formulaire_lien_voir_upload(
            $champ,
            $this->getParameter("obj"),
            $this->getParameter("idx")
        );
        printf(
            '<span style="display:none;" data-href="%s" class="form-snippet-voir"><!-- --></span>',
            "".OM_ROUTE_FORM."&snippet=voir"
        );

        // Affichage de l'action 'supprimer'.
        $this->f->layout->display_formulaire_lien_supprimer_upload(
            $champ
        );
    }

    /**
     * WIDGET_FORM - upload2.
     *
     * FILE
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function upload2($champ, $validation, $DEBUG = false) {
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        if ($this->f->storage == NULL) {
            // Message d'erreur
            echo "<div id=\"".$champ."\">";
            echo __("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";

            // On sort de la méthode
            return -1;
        }

        //
        echo "<input";
        echo " type=\"hidden\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        // Explode de la valeur afin de vérifier si l'uid est temporaire
        $temporary_test = explode("|", $this->val[$champ]);

        //
        echo "<input type=\"text\"";
        echo " name=\"".$champ."_upload\"";
        echo " id=\"".$champ."_upload\" ";
        // Test si une valeur est présente
        if (isset($this->val[$champ]) && !empty($this->val[$champ]) && !$this->correct) {
            // Test si la valeur contient "tmp"
            if (isset($temporary_test[0]) AND $temporary_test[0] == "tmp") {
                // Si la valeur du champ contient effectivement un uid
                if (isset($temporary_test[1])) {
                    echo " value=\"".$this->f->storage->getFilename_temporary($temporary_test[1])."\" ";
                }
            } else {
                // Et si le formulaire à déjà été validé pour afficher le nom du fichier
                echo " value=\"".$this->f->storage->getFilename($this->val[$champ])."\" ";
            }
        } else {
            echo " value=\"\" ";
        }

        echo " class=\"champFormulaire upload\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if ($this->correct) {
            return -1;
        }

        // Récupération de la configuration du widget
        $configuration = (isset($this->select[$champ]) ? $this->select[$champ] : null);

        // Affichage de l'action 'upload', avec un marqueur permettant de
        // définir le path vers le snippet de formulaire 'upload'.
        $this->f->layout->display_formulaire_lien_vupload_upload(
            $champ,
            $this->getParameter("obj"),
            $this->getParameter("idx"),
            $configuration,
            "sousform"
        );
        printf(
            '<span style="display:none;" data-href="%s" class="form-snippet-upload"><!-- --></span>',
            "".OM_ROUTE_FORM."&snippet=upload"
        );

        // Affichage de l'action 'voir', avec un marqueur permettant de définir
        // le path vers le snippet de formulaire 'voir'.
        $this->f->layout->display_formulaire_lien_voir_upload(
            $champ,
            $this->getParameter("obj"),
            $this->getParameter("idx"),
            "sousform"
        );
        printf(
            '<span style="display:none;" data-href="%s" class="form-snippet-voir"><!-- --></span>',
            "".OM_ROUTE_FORM."&snippet=voir"
        );

        // Affichage de l'action 'supprimer'.
        $this->f->layout->display_formulaire_lien_supprimer_upload(
            $champ,
            "sousform"
        );
    }

    /**
     * WIDGET_FORM - voir.
     *
     * FILE
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function voir($champ, $validation, $DEBUG = false) {
        if (!$this->correct) {
            //
            printf(
                '<input type="text" name="%s" id="%s" value="%s" size="%s" maxlength="%s" onchange="%s" class="champFormulaire voir" />',
                $champ,
                $champ,
                $this->val[$champ],
                $this->taille[$champ],
                $this->max[$champ],
                $this->onchange[$champ]
            );
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-voir"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=voir"
            );
            //
            $this->f->layout->display_formulaire_lien_voir_upload(
                $champ,
                $this->getParameter("obj"),
                $this->getParameter("idx")
            );
        } else {
            //
            printf(
                '<input type="text" name="%s" id="%s" value="%s" size="%s" maxlength="%s" class="champFormulaire voir" disabled="disabled" />',
                $champ,
                $champ,
                $this->val[$champ],
                $this->taille[$champ],
                $this->max[$champ]
            );
        }
    }

    /**
     * WIDGET_FORM - voir2.
     *
     * FILE
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function voir2($champ, $validation, $DEBUG = false) {
        if (!$this->correct) {
            //
            printf(
                '<input type="hidden" name="%s" id="%s" value="%s" class="champFormulaire voir" />',
                $champ,
                $champ,
                $this->val[$champ]
            );
            //
            printf(
                '<span style="display:none;" data-href="%s" class="form-snippet-voir"><!-- --></span>',
                "".OM_ROUTE_FORM."&snippet=voir"
            );
            //
            echo "<p align='left'>";
            echo $this->val[$champ];
            echo " </p>\n";
            //
            $this->f->layout->display_formulaire_lien_voir_upload(
                $champ,
                $this->getParameter("obj"),
                $this->getParameter("idx"),
                "sousform"
            );
        } else {
            //
            printf(
                '<input type="text" name="%s" id="%s" value="%s" size="%s" maxlength="%s" class="champFormulaire voir" disabled="disabled" />',
                $champ,
                $champ,
                $this->val[$champ],
                $this->taille[$champ],
                $this->max[$champ]
            );
        }
    }
    // }}} WIDGET_FORM - END

    // {{{ SNIPPET_FORM - BEGIN

    /**
     * SNIPPET_FORM - autocomplete.
     *
     * Ce script permet de charger les données
     * des établissements pour les autocomplete
     *
     * @return void
     */
    protected function snippet__autocomplete() {

        /**
         * Méthode permettant de faire les traitements d'échappements
         * et de normalisation sur une chaîne destinée à la recherche.
         *
         * @param string $value Valeur à rechercher
         *
         * @return string Valeur normalisée
         * @ignore
         */
        function normalizeSearchValue($value) {
            // gestion du caractere joker '*' en debut de chaine
            $value = str_replace('*', '%', $value);
            //
            $value = html_entity_decode($value, ENT_QUOTES);
            // échappement des caractères spéciaux
            if (!get_magic_quotes_gpc()) {
                $value=pg_escape_string($value);
            }
            // wildcards
            $value = "'%".$value."%'";
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
         * en vue de les comparer aux valeurs recherchées.
         *
         * @param string $searchField Champ sur lequel la recherche est faite
         *
         * @return string Champ normalisé
         * @ignore
         */
        function normalizeFieldValue($searchField) {
            return " translate(lower(".$searchField."::varchar),'àáâãäçèéêëìíîïñòóôõöùúûüýÿ','aaaaaceeeeiiiinooooouuuuyy') ";
        }

        $this->f->disableLog();
        header("Content-type: text/html; charset=".CHARSET."");

        // Initialisation des paramètres
        $params = array(
            "obj_from" => array(
                "default_value" => "",
            ),
            "idx_from" => array(
                "default_value" => "",
            ),
            "field" => array(
                "default_value" => "",
            ),
            "action" => array(
                "default_value" => "",
            ),
            "retourformulaire" => array(
                "default_value" => "",
            ),
            "idxformulaire" => array(
                "default_value" => "",
            ),
            "term" => array(
                "default_value" => null,
            ),
            // Identifiant de la valeur du champ déjà sélectionnée si existante
            "idx" => array(
                "default_value" => "",
            )
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        // Instanciation de l'objet
        $object = $this->f->get_inst__om_dbform(array(
            "obj" => $obj_from,
            "idx" => $idx_from,
        ));
        if ($object === null) {
            echo json_encode(array(array(
                "value" => "-1",
                "label" => __("Erreur de configuration. Contactez votre administrateur."),
            )));
            return;
        }
        // On passe les paramètres du formulaire (contexte)
        $object->setParameter("maj", $action);
        $object->setParameter("action", $action);
        $object->setParameter("idxformulaire", $idxformulaire);
        $object->setParameter("retourformulaire", $retourformulaire);
        // On récupère la configuration
        $select = $object->get_widget_config($field, "autocomplete");
        if ($select === null) {
            echo json_encode(array(array(
                "value" => "-1",
                "label" => __("Erreur de configuration. Contactez votre administrateur."),
            )));
            return;
        }

        // Récupération du flag d'ajout et de la permission
        $droit_ajout = (isset($select['droit_ajout'])) ? $select['droit_ajout'] : "";
        $om_droit_ajout = $this->f->isAccredited(array($obj_from, $obj_from."_ajouter"), "OR");
        // Récupération des colonnes ID et libellé
        $colid = (isset($select['identifiant'])) ? $select['identifiant'] : "";
        $collib = (isset($select['libelle'])) ? $select['libelle'] : array();
        $nb_collib = count($collib);
        // Récupération des champs de critère
        $criteres = (isset($select['criteres'])) ? $select['criteres'] : array();
        $nb_criteres = count($criteres);
        // Récupération des tables jointes
        $jointures = (isset($select['jointures'])) ? $select['jointures'] : array();
        // Récupération de la table de l'objet
        $table = (isset($select['table'])) ? $select['table'] : "";
        // Récupération d'une éventuelle clause where
        $where = (isset($select['where'])) ? $select['where'] : "";
        // Récupération des champs du group by
        $group_by = (isset($select['group_by'])) ? $select['group_by'] : array();

        // SELECT
        $sql_select = "SELECT ".$colid.", concat(";
        $i = 1;
        foreach ($collib as $col) {
            $sql_select .= $col;
            if ($i < $nb_collib) {
                $sql_select .= ",' - ',";
            }
            $i++;
        }
        $sql_select .= ") ";
        // FROM
        $sql_select .= "FROM ".DB_PREFIXE.$table." ";
        if (!empty($jointures)) {
            foreach ($jointures as $jointure) {
                $sql_select .= "LEFT JOIN ".DB_PREFIXE.$jointure." ";
            }
        }
        // WHERE
        if ($term !== null) {
            //
            $terms = explode(" ", trim(str_replace("-", " ", $term)));
            $sql_where = " WHERE ";
            //
            foreach ($terms as $key => $t) {
                if ($key != 0) {
                    $sql_where .= " and ";
                }
                $sql_where .= "(";
                //
                $j = 1;
                foreach ($criteres as $critere => $critere_libelle) {
                    // Normalisation de la valeur recherchée
                    $t_temp = normalizeSearchValue($t);
                    // Normalisation du champ concerné par la recherche
                    $critere_temp = normalizeFieldValue($critere);
                    //
                    $sql_where .= "(lower(".$critere_temp."::text) like ".$t_temp.")";
                    if ($j < $nb_criteres) {
                        $sql_where .= " OR ";
                    }
                    $j++;
                }
                $sql_where .= ")";
            }
        }
        if ($idx != "" && $colid != "") {
            $sql_where = " WHERE ".$colid." = '".$idx."' ";
        }
        if (!empty($where)) {
            $sql_where .= " AND ".$where;
        }
        // GROUP BY
        $sql_group_by = " GROUP BY ".$colid;
        if (!empty($group_by)) {
            $sql_group_by = " GROUP BY ";
            foreach ($group_by as $value) {
                $sql_group_by .= " ".$value.",";
            }
            $sql_group_by = substr($sql_group_by, 0, -1);
        }
        // LIMIT
        $sql_limit = " limit 20";

        // Exécution de la requete
        $sql = $sql_select.$sql_where.$sql_group_by.$sql_limit;
        $res = $this->f->db->query($sql);
        $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->f->isDatabaseError($res);

        // Construction de la liste de résultats
        $output = array();
        if ($row = &$res->fetchRow()) {
            do {
                $retour = array();
                $retour['value'] = $row[0];
                $retour['label'] = $row[1];
                array_push($output, $retour);
            } while ($row = &$res->fetchRow());
        } else {
            $retour = array();
            $retour['value'] = "-1";
            $retour['label'] = __("Aucun résultat");
            array_push($output, $retour);
        }
        if ($droit_ajout && $om_droit_ajout) {
            $retour = array();
            $retour['value'] = "-2";
            $retour['label'] = __("Créer un nouvel enregistrement");
            array_push($output, $retour);
        }
        echo json_encode($output);
    }

    /**
     * SNIPPET_FORM - combo.
     *
     * Ce script permet d'effectuer une correlation entre deux champs d'apres la
     * saisie d'une valeur dans un champ d'origine correle au travers d'une table
     * un autre champ
     *
     * @return void
     */
    protected function snippet__combo() {
        $f = $this->f;

        /**
         * Affichage de la structure HTML
         */
        //
        $f->setFlag("htmlonly");
        $f->display();
        //
        $f->displayStartContent();

        /**
         * Parametres
         */
        //
        $DEBUG = 0;
        //
        $nbligne = 0;
        // debut = 0 recherche sur la chaine / debut = 1 recherche sur le debut de la chaine
        $debut = 0 ;
        //
        $longueurRecherche = 1;
        //
        $sql = "";
        $z='';
        // Initialisation des paramètres
        $params = array(
            // table sur laquelle se fait la correlation / table sur lequel s effectue la recherche
            "table" => array(
                "default_value" => "",
            ),
            // zone d'origine de la correlation / champ de recherche sur la table
            "zorigine" => array(
                "var_name" => "zoneOrigine",
                "default_value" => "",
            ),
            // zone qui sera mise à jour par la correlation / champ en relation
            "zcorrel" => array(
                "var_name" => "zoneCorrel",
                "default_value" => "",
            ),
            // caracteres saisis dans la zone d'origine / valeur du champ origine a rechercher
            "recherche" => array(
                "default_value" => "",
            ),
            // valeur affectée à la zone d'origine / champ d origine => affectation de la valeur validee
            "origine" => array(
                "var_name" => "champOrigine",
                "default_value" => "",
            ),
            // valeur affectée à la zone correllée / champ a affecter la valeur validee
            "correl" => array(
                "var_name" => "champCorrel",
                "default_value" => "",
            ),
            // parametres de selection / champ de selection (clause where)
            "correl2" => array(
                "var_name" => "champCorrel2",
                "default_value" => "",
            ),
            // parametres de selection / valeur du champ de selection (clause where)
            "zcorrel2" => array(
                "var_name" => "zoneCorrel2",
                "default_value" => "",
            ),
            "form" => array(
                "default_value" => "f1",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        /**
         * Vérification des paramètres : table - zorigine - correl2
         */
        //
        $error = false;
        // On instancie l'utilitaire de génération
        $g = $this->f->get_inst__om_gen();
        // On récupère la liste de toutes les tables de la base de données
        $tables = $g->get_all_tables_from_database();
        // On vérifie que la table passée en paramètre existe
        if (!in_array($table, $tables)) {
            $error = true;
        }
        if ($error == false) {
            // On récupère la liste de tous les champs de la table
            $fields = $g->get_fields_list_from_table($table);
            //
            if (!in_array($zoneOrigine, $fields)) {
                $error = true;
            }
            //
            if ($zoneCorrel2 != "" && !in_array($champCorrel2, $fields)) {
                $error = true;
            }
        }
        //
        if ($error == true) {
            $message = __("Erreur de parametres.");
            $f->displayMessage("error", $message);
            $f->displayEndContent();
            die();
        }

        // parametrage de $sql = requete de recherche specifique
        // $longueurRecherche  = longueur autorise en recherche
        // $debut = rrecherche au debut de zone ou compris dans la zone
        if (file_exists("../dyn/comboparametre.inc.php")) {
            include "../dyn/comboparametre.inc.php";
        }

        // Log
        $debug_infos = array(
            "champOrigine" => $champOrigine,
            "recherche" => $recherche,
            "table" => $table,
            "zoneOrigine" => $zoneOrigine,
            "zoneCorrel" => $zoneCorrel,
            "champCorrel" => $champCorrel,
            "zoneCorrel2" => $zoneCorrel2,
            "champCorrel2" => $champCorrel2
        );
        $f->addToLog(__METHOD__."(): ".print_r($debug_infos, true), EXTRA_VERBOSE_MODE);

        /**
         *
         */
        //
        $this->f->layout->display__form_container__begin(array(
            "action" => OM_ROUTE_FORM."&snippet=combo",
            "name" => "f3",
        ));
        //
        if (strlen($recherche) > $longueurRecherche) {
            /**
             * Construction de la requete
             */
            //
            if ($sql == "") {
                // Log
                $f->addToLog(__METHOD__."(): Construction de la requete standard", EXTRA_VERBOSE_MODE);
                if ($debut == 0) {
                    $sql = "select * from ".DB_PREFIXE.$table." where ".$zoneOrigine." like '%".$f->db->escapeSimple($recherche)."%'";
                } else {
                    $sql = "select * from ".DB_PREFIXE.$table." where ".$zoneOrigine." like '".$f->db->escapeSimple($recherche)."%'";
                }
            }
            //
            if ($zoneCorrel2 != "") {
                $sql .= " and ".$champCorrel2." like '".$f->db->escapeSimple($zoneCorrel2)."'";
            }
            //
            if ($DEBUG == 1) {
                echo $sql;
            }

            /**
             * Execution de la requete
             */
            //
            $res = $f->db->query($sql);
            $f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $f->isDatabaseError($res);
            //
            $nbligne = $res->numrows();
            //
            switch($nbligne) {
                case 0 :
                    //
                    $message = __("Votre saisie ne donne aucune correspondance");
                    $f->displayMessage("error", $message);
                    //
                    break;
                case 1 :
                    //
                    while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                        // dans la zone correllee
                        $x = $row[$zoneCorrel];
                        // dans la zone d'origine
                        $y = $row[$zoneOrigine];
                        // parametrage des retour dans les champs $x et $y
                        if (file_exists("../dyn/comboretour.inc.php")) {
                            include "../dyn/comboretour.inc.php";
                        }
                    }
                    // Envoi des donnees dans le formulaire de la fenetre parent
                    echo "\n<script type=\"text/javascript\">\n";
                    echo "opener.document.".$form.".".$champCorrel.".value = \"".$x."\";\n";
                    echo "opener.document.".$form.".".$champOrigine.".value = \"".$y."\";\n";
                    if($champCorrel2 != '') {
                        echo "if (opener.document.".$form.".".$champCorrel2." != undefined) {\n";
                        echo "opener.document.".$form.".".$champCorrel2.".value = \"".$z."\";\n";
                        // Simulation d'un event onchange
                        echo "el = opener.document.".$form.".".$champCorrel2.";\n";
                        echo "if(document.createEvent) {\n"; // if(!IE)

                        echo "  ev = document.createEvent('Event');\n";
                        echo "  ev.initEvent('change', true, false);\n";
                        echo "  el.dispatchEvent(ev);\n";
                        echo "} else {\n";
                        echo "  el.fireEvent( 'onchange');\n";
                        echo "}\n";

                        echo "}\n";
                    }
                    // Simulation d'un event onchange
                    echo "el = opener.document.".$form.".".$champCorrel.";\n";

                    echo "if(document.createEvent) {\n"; // if(!IE)
                    echo "  ev = document.createEvent('Event');\n";
                    echo "  ev.initEvent('change', true, false);\n";
                    echo "  el.dispatchEvent(ev);\n";
                    echo "} else {\n";
                    echo "  el.fireEvent( 'onchange');\n";
                    echo "}\n";
                    // Simulation d'un event onchange
                    echo "el = opener.document.".$form.".".$champOrigine.";\n";
                    echo "if(document.createEvent) {\n"; // if(!IE)
                    echo "  ev = document.createEvent('Event');\n";
                    echo "  ev.initEvent('change', true, false);\n";
                    echo "  el.dispatchEvent(ev);\n";
                    echo "} else {\n";
                    echo "  el.fireEvent( 'onchange');\n";
                    echo "}\n";

                    echo "this.close();\n";
                    echo "</script>\n";
                    //
                    break;
                default :
                    //
                    echo "\n<div class=\"instructions\">\n";
                    echo "<p>\n";
                    echo __("Selectionner dans la liste ci-dessous la correspondance avec ".
                           "votre recherche")." ".$champOrigine.". ";
                    echo __("Puis valider votre choix en cliquant sur le bouton : \"Valider\".");
                    echo "</p>\n";
                    echo "</div>\n";
                    //
                    echo "<select size=\"1\" name=\"".$champOrigine."\" class=\"champFormulaire\">\n";
                    while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                        // dans la zone correllee
                        $x = $row[$zoneCorrel];
                        // dans la zone d'origine
                        $y = $row[$zoneOrigine];
                        // affichage
                        $aff = $row[$zoneCorrel]." - ".$row[$zoneOrigine];
                        // defintion du retour  unique d apres la table select = $retourUnique
                        // definition affichage en table = $aff
                        if (file_exists("../dyn/comboaffichage.inc.php")) {
                            include "../dyn/comboaffichage.inc.php";
                        }
                        //
                        $opt = "<option value=\"".$x."£".$y."£".$z."\">";
                        $opt .= $aff;
                        $opt .= "</option>\n";
                        //
                        echo $opt;
                    }
                    echo "</select>\n";
                    // Envoi des donnees dans le formulaire de la fenetre parent
                    echo "\n<script type=\"text/javascript\">\n";
                    echo "function recup()\n{\n";
                    echo "var s = document.f3.".$champOrigine.".value;\n";
                    echo "var x = s.split( \"£\" );\n";
                    echo "opener.document.".$form.".".$champOrigine.".value = x[1];\n";
                    echo "opener.document.".$form.".".$champCorrel.".value = x[0];\n";
                    if($champCorrel2 != '') {
                        echo "if (opener.document.".$form.".".$champCorrel2." != undefined) {\n";
                        echo "opener.document.".$form.".".$champCorrel2.".value = x[2];\n";

                        echo "el = opener.document.".$form.".".$champCorrel2.";\n";
                        echo "if(document.createEvent) {\n"; // if(!IE)

                        echo "  ev = document.createEvent('Event');\n";
                        echo "  ev.initEvent('change', true, false);\n";
                        echo "  el.dispatchEvent(ev);\n";
                        echo "} else {\n";
                        echo "  el.fireEvent( 'onchange');\n";
                        echo "}\n";

                        echo "}\n";
                    }
                    echo "el = opener.document.".$form.".".$champCorrel.";\n";

                    echo "if(document.createEvent) {\n"; // if(!IE)
                    echo "  ev = document.createEvent('Event');\n";
                    echo "  ev.initEvent('change', true, false);\n";
                    echo "  el.dispatchEvent(ev);\n";
                    echo "} else {\n";
                    echo "  el.fireEvent( 'onchange');\n";
                    echo "}\n";
                    echo "el = opener.document.".$form.".".$champOrigine.";\n";
                    echo "if(document.createEvent) {\n"; // if(!IE)
                    echo "  ev = document.createEvent('Event');\n";
                    echo "  ev.initEvent('change', true, false);\n";
                    echo "  el.dispatchEvent(ev);\n";
                    echo "} else {\n";
                    echo "  el.fireEvent( 'onchange');\n";
                    echo "}\n";

                    echo "this.close();\n}\n";
                    echo "</script>\n";
                    //
                    $this->f->layout->display__form_controls_container__begin(array(
                        "controls" => "bottom",
                    ));
                    $this->f->layout->display__form_input_submit(array(
                        "value" => __("Valider"),
                        "onclick" => "javascript:recup();",
                        "class" => "boutonFormulaire",
                    ));
                    break;
            }

        } else {

            //
            $message = __("Vous devez saisir une valeur d'au moins");
            $message .= " ".($longueurRecherche+1)." ";
            $message .= __("caracteres dans le champ");
            $message .= " ".$champOrigine.".";
            $f->displayMessage("error", $message);

        }
        //
        if ($nbligne < 1) {
            $this->f->layout->display__form_controls_container__begin(array(
                "controls" => "bottom",
            ));
        }
        $f->displayLinkJsCloseWindow();
        $this->f->layout->display__form_controls_container__end();
        $this->f->layout->display__form_container__end();

        /**
         *
         */
        //
        $f->displayEndContent();
    }

    /**
     * SNIPPET_FORM - file.
     *
     * Ce script permet de visualiser un fichier dont l'uid est passé en paramètre
     *
     * @return void
     */
    protected function snippet__file() {
         /**
         * Affiche le contenu du fichier
         * @param  string $uid Identifiant unique du fichier
         * @param  object $f   Instance de la classe utils
         * @param  string $dl  Téléchargement
         * @param  string $mode Mode permettant de définir l'endroit où se situe le fichier
         * @ignore
         */
        function display_file_content($uid, $dl, $f, $mode) {

            // Choix du support du filestorage
            switch ($mode) {
                case 'temporary':
                    $file = $f->storage->get_temporary($uid);
                    break;

                default:
                    $file = $f->storage->get($uid);
                    break;
            }

            // Affichage du contenu du fichier
            if($file != null) {

                // Headers
                header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le passé
                header("Content-Type: ".$file['metadata']['mimetype']);
                header("Accept-Ranges: bytes");

                // Vérification pour la valeur de $dl
                if (!in_array($dl, array("download", "inline"))) {
                    if ($f->getParameter("edition_output") == "download") {
                        $dl="download";
                    } else {
                        $dl="inline";
                    }
                }

                // Vérification si on affiche simplement l'image, sinon envoi un dialogue de sauvegarde
                if ($dl=="download") {
                    header("Content-Disposition: attachment; filename=\"".$file['metadata']['filename']."\";" );
                 } else {
                    header("Content-Disposition: inline; filename=\"".$file['metadata']['filename']."\";" );

                 }

                // Rendu du fichier
                echo $file['file_content'];

            } else {
                // Retour à l'accueil + affichage de l'erreur
                $f->displayMessage("error", __("Le fichier n'existe pas ou n'est pas accessible."));

            }
        }

        $f = $this->f;
        //
        $f->disableLog();
        // Initialisation des paramètres
        $params = array(
            "uid" => array(
                "default_value" => "",
            ),
            "dl" => array(
                "default_value" => "",
            ),
            "mode" => array(
                "default_value" => "filestorage",
            ),
            "obj" => array(
                "default_value" => "",
            ),
            "champ" => array(
                "default_value" => "",
            ),
            "id" => array(
                "default_value" => "",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        // Si les paramètres nécessaires ne sont pas correctement fournis
        if (($obj == "" || $champ == "" || $id == "") && $uid == "") {
            $this->f->displayMessage("error", __("Les parametres transmis ne sont pas corrects."));
            return;
        }
        // Cas n°1 - Récupération par obj/field/id
        if ($obj != "" && $champ != "" && $id != "") {
            //Vérification des droits
            if ($f->isAccredited($obj) || $f->isAccredited($obj.'_'.$champ.'_telecharger')) {
                $object = $this->f->get_inst__om_dbform(array(
                    "obj" => $obj,
                    "idx" => $id,
                ));
                // Si pas d'objet envoi message de retour
                if ($object === null) {
                    $f->displayMessage("error", __("Objet inexistant."));
                    die();
                }
                $uid = $object->getVal($champ);

                //Affichage du fichier
                display_file_content($uid, $dl, $f, $mode);
            } else {
                //Envoi message de retour
                $f->displayMessage("error", __("Droits insuffisants. Vous n'avez pas suffisamment de droits pour acceder a cette page."));
            }
            return;
        }
        // Sinon si l'uid est renseigné
        display_file_content($uid, $dl, $f, $mode);
    }

    /**
     * SNIPPET_FORM - localisation.
     *
     * @return void
     */
    protected function snippet__localisation() {
        // Initialisation des paramètres
        $params = array(
            "format" => array(
                "default_value" => "",
            ),
            "orientation" => array(
                "default_value" => "",
            ),
            "positionx" => array(
                "default_value" => "",
            ),
            "positiony" => array(
                "default_value" => "",
            ),
            "x" => array(
                "default_value" => 0,
            ),
            "y" => array(
                "default_value" => 0,
            ),
            "form" => array(
                "default_value" => "f1",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        //
        if ($format == "A4" && $orientation == "P") {
            $width = 210;
            $height = 297;
        } elseif ($format == "A4" && $orientation == "L") {
            $width = 297;
            $height = 210;
        } elseif ($format == "A3" && $orientation == "P") {
            $width = 297;
            $height = 420;
        } elseif ($format == "A3" && $orientation == "L") {
            $width = 420;
            $height = 297;
        } else {
            $width = 210;
            $height = 297;
        }

        /**
         * Affichage de la structure HTML
         */
        if ($this->f->isAjaxRequest()) {
            header("Content-type: text/html; charset=".HTTPCHARSET."");
        } else {
            //
            $this->f->addHTMLHeadJs(array(
                "../lib/jquery-ui/jquery-ui.min.js",
                "../lib/om-assets/js/localisation.js",
            ));
            //
            $this->f->setFlag("htmlonly");
            $this->f->display();
        }
        //
        $this->f->displayStartContent();
        //
        $this->f->setTitle(__("Localisation"));
        $this->f->displayTitle();

        /**
         *
         */
        //
        echo "<div";
        echo " style=\"float:left; border: 1px solid #cdcdcd; margin-bottom: 10px;\"";
        echo ">\n";
        echo "<div";
        echo " id=\"localisation-wrapper\"";
        echo " style=\"position:relative; float:left; ";
        echo " width:".$width."px;";
        echo " height:".$height."px;";
        echo " background-color: #abcdef;\"";
        echo ">\n";
        echo "<img";
        echo " id=\"draggable\"";
        echo " class=\"".$form.";".$positionx.";".$positiony.";\"";
        echo " src=\"../lib/om-assets/img/zoneobligatoire.gif\"";
        echo " style=\"position:absolute; margin: 0; padding:0; left:".$x."px; top:".$y."px; border-left: 1px solid #999; border-top: 1px solid #999;\"";
        echo " />";
        echo "\n</div>\n";
        echo "<div class=\"visualClear\"><!-- --></div>\n";
        echo "\n</div>\n";
        echo "<div class=\"visualClear\"><!-- --></div>\n";

        /**
         *
         */
        //
        $this->f->displayLinkJsCloseWindow();

        /**
         *
         */
        //
        $this->f->displayEndContent();
    }

    /**
     * SNIPPET_FORM - upload.
     *
     * Ce script permet d'afficher un formulaire pour gérer l'upload de fichier
     * dans le répertoire de storage.
     *
     * @return void
     */
    protected function snippet__upload() {
        $f = $this->f;

        // Initialisation des paramètres
        $params = array(
            "origine" => array(
                "default_value" => "",
            ),
            "taille_max" => array(
                "default_value" => "",
            ),
            "extension" => array(
                "default_value" => "",
            ),
            "form" => array(
                "default_value" => "f1",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        /**
         * Verification des parametres
         */
        if ($origine == "") {
            //
            if ($f->isAjaxRequest() == false) {
                $f->setFlag(NULL);
                $f->display();
            }
            $class = "error";
            $message = __("L'objet est invalide.");
            $f->displayMessage($class, $message);
            die();
        }

        /**
         * Affichage de la structure HTML
         */
        if ($f->isAjaxRequest()) {
            header("Content-type: text/html; charset=".HTTPCHARSET."");
        } else {
            //
            $f->setFlag("htmlonly");
            $f->display();
        }
        //
        $f->displayStartContent();
        //
        $f->setTitle(__("Upload"));
        $f->displayTitle();
        //
        $description = __("Cliquer sur 'Parcourir' pour selectionner le fichier a ".
                         "telecharger depuis votre poste de travail puis cliquer sur ".
                         "le bouton 'Envoyer' pour valider votre telechargement.");
        $f->displayDescription($description);

        /**
         *
         */
        //
        (defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
        require_once PATH_OPENMAIRIE."upload.class.php";
        //
        $Upload = new Upload($f);

        /**
         * Gestion des erreurs
         */
        //
        $error = false;
        // Verification du post vide
        if (isset($_POST['submited'])
            and (!isset($_FILES['userfile'])
                 or $_FILES['userfile']['name'][0] == "")) {
            //
            $error = true;
            $f->displayMessage("error", __("Vous devez selectionner un fichier."));
        }

        /**
         * Formulaire soumis et valide
         */
        if (isset($_POST['submited']) and $error == false) {

            // Gestion des extensions de fichier
            if ($origine !== "") {
                $tmp = $origine.'_extension';
            }
            if (isset(${$tmp})) {
                $Upload->Extension = ${$tmp};
            } else {
                if ($extension != ""
                    && isset($f->config['upload_extension'])) {
                    //
                    $Upload->Extension = $extension;

                    //Liste des extensions génériques possibles
                    $extensionPossibleGen = explode(';', $f->config['upload_extension']);
                    array_pop($extensionPossibleGen);
                    //Liste des extensions spécifiques possibles
                    $extensionPossibleSpe = explode(';', $extension);

                    foreach ($extensionPossibleSpe as $value) {

                        // Si une seule des extensions spécifiques n'est pas une des
                        // extensions génériques possibles, on utilise la configuration
                        // générique
                        if ( !in_array($value, $extensionPossibleGen)){
                            $Upload->Extension = $f->config['upload_extension'];
                            break;
                        }
                    }
                } elseif ($extension != "") {
                    $Upload->Extension = $extension;
                }elseif (isset($f->config['upload_extension'])) {
                    $Upload->Extension = $f->config['upload_extension'];
                } else {
                    $Upload->Extension = '.gif;.jpg;.jpeg;.png;.txt;.pdf;.csv';
                }
            }

            // On lance la procedure d'upload
            $Upload->Execute();

            // Gestion erreur / succes
            if ($UploadError) {
                $error = true;
                // (XXX - Le foreach est inutile on traite sur un seul champ fichier)
                foreach ($Upload->GetError() as $elem) {
                    foreach($elem as $key => $elem1) {
                        $f->displayMessage("error", $elem1);
                    }
                }
            } else {
                // (XXX - Le foreach est inutile on traite sur un seul champ fichier)
                foreach ($Upload->GetSummary() as $elem) {
                    $nom = $elem['nom'];
                    $filename = $elem['nom_originel'];
                    // Controle de la longueur du nom de fichier
                    if (strlen($filename) > 50) {
                        $error = true;
                        $f->displayMessage("error", $filename." ".__("contient trop de caracteres.")." ".__("Autorise(s) : 50 caractere(s)."));
                        continue;
                    }
                    //
                    if ($f->isAjaxRequest()) {
                        echo "<script type=\"text/javascript\">";
                        echo "upload_return('".$form."', '".$origine."', 'tmp|".$nom."', '".addslashes($filename)."')";
                        echo "</script>";
                    } else {
                        sprintf(
                            '
                    <script type="text/javascript">
                        parent.opener.document.%1$s.%2$s.value=\'tmp|%3$s\';
                        parent.opener.document.%1$s.%2$s_upload.value=\'%4$s\';
                        parent.close();
                    </script>
                            ',
                            $form,
                            $origine,
                            $nom,
                            $filename
                        );
                    }
                }
            }
        }

        /**
         * Formulaire non soumis ou non valide
         */
        if (!isset($_POST['submited']) or $error == true) {
            // Pour limiter la taille d'un fichier (exprimee en ko)
            if ($taille_max != ""
                && isset($f->config['upload_taille_max'])
                && $taille_max > $f->config['upload_taille_max']) {
                $Upload->MaxFilesize = $f->config['upload_taille_max'];
            } elseif ($taille_max != "") {
                $Upload->MaxFilesize = $taille_max * 1024 ;
            }elseif (isset($f->config['upload_taille_max'])) {
                $Upload->MaxFilesize = $f->config['upload_taille_max'];
            } else {
                $Upload->MaxFilesize = '10000';
            }

            // Pour ajouter des attributs aux champs de type file
            $Upload->FieldOptions = 'class="champFormulaire"';
            // Pour indiquer le nombre de champs desire
            $Upload->Fields = 2;
            // Initialisation du formulaire
            $Upload->InitForm();
            // Ouverture de la balise form
            //
            $this->f->layout->display__form_container__begin(array(
                "action" => "".OM_ROUTE_FORM."&snippet=upload&origine=".$origine.
                "&amp;form=".$form."&amp;taille_max=".$taille_max.
                "&amp;extension=".$extension,
                "name" => "upload-form",
                "id" => "upload-form",
                "enctype" => "multipart/form-data",
            ));
            // Affichage du champ MAX_FILE_SIZE
            print $Upload->Field[0];
            // Affichage du premier champ de type FILE
            print $Upload->Field[1];
            //
            echo "<br/>\n";
            echo "<br/>\n";
            //
            echo "<input type=\"hidden\" value=\"1\" name=\"submited\" />\n";
            $this->f->layout->display__form_input_submit(array(
                "name" => "submit",
                "value" => __("Envoyer"),
            ));
            //
            $f->displayLinkJsCloseWindow();
            // Fermeture de la balise form
            $this->f->layout->display__form_container__end();
        }

        /**
         * Affichage de la structure HTML
         */
        //
        $f->displayEndContent();
    }

    /**
     * SNIPPET_FORM - voir.
     *
     * Ce script permet d'offrir un visualisation web d'un fichier. Soit le fichier
     * est une image et il est alors affiché à l'écran soit c'est autre type de
     * fichier et un lien est présenté pour télécharger le fichier.s
     *
     * @return void
     */
    protected function snippet__voir() {
        /**
         * Définition d'une fonction pour rendre la taille du fichier lisible
         * @ignore
         */
        function filesize_format($size) {
            $units = array('o', 'Ko', 'Mo', 'Go', 'To',);
            $power = $size > 0 ? floor(log($size, 1024)) : 0;
            return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
        }

        /**
         * Affichage de la structure HTML
         */
        if ($this->f->isAjaxRequest()) {
            header("Content-type: text/html; charset=".HTTPCHARSET."");
        } else {
            //
            $this->f->setFlag("htmlonly");
            $this->f->display();
        }

        /**
         * Récupération des paramètres
         */
        // Initialisation des paramètres
        $params = array(
            "fic" => array(
                "default_value" => "",
            ),
            "mode" => array(
                "default_value" => "filestorage",
            ),
            "obj" => array(
                "default_value" => "",
            ),
            "champ" => array(
                "default_value" => "",
            ),
            "id" => array(
                "default_value" => "",
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }
        // Si les paramètres nécessaires ne sont pas correctement fournis
        if (($obj == "" || $champ == "" || $id == "") && $fic == "") {
            // Retour à l'accueil + affichage de l'erreur
            $this->f->displayMessage("error", __("Les parametres transmis ne sont pas corrects."));
            die();

        }

        /**
         * Cas n°1 - Récupération du fichier en passant par son objet
         */
        //
        if ($obj != "" && $champ != "" && $id != "") {

            // On vérifie que l'utilisateur a bien le droit de télécharger le champ
            // fichier de l'objet
            if (!($this->f->isAccredited($obj) || $this->f->isAccredited($obj.'_'.$champ.'_telecharger'))) {
                // Envoi message de retour
                $this->f->displayMessage("error", __("Droits insuffisants. Vous n'avez pas suffisamment de droits pour acceder a cette page."));
                die();
            }

            //
            $object = $this->f->get_inst__om_dbform(array(
                "obj" => $obj,
                "idx" => $id,
            ));
            // Si pas d'objet envoi message de retour
            if ($object === null) {
                $this->f->displayMessage("error", __("Objet inexistant."));
                die();
            }
            $fic = $object->getVal($champ);
        }


        /**
         * Affiche le contenu du fichier
         */

        // Si le mode de stockage est le mode temporaire alors on récupère le fichier
        // depuis ce mode se stockage sinon depuis le sotckage standard
        if ($mode == 'temporary') {
            //
            $file = $this->f->storage->get_temporary($fic);
        } else {
            //
            $file = $this->f->storage->get($fic);
        }

        /**
         *
         */
        if (is_null($file)) {
            //
            $this->f->displayMessage("error", __("Le fichier n'existe pas ou n'est pas accessible."));
            die();
        }

        /**
         *
         */
        //
        $this->f->displayStartContent();
        //
        $this->f->setTitle(__("Voir")." -> [&nbsp;".$file['metadata']['filename']."&nbsp;]");
        $this->f->displayTitle();

        /**
         *
         */
        //
        echo "<div id=\"voir\">\n";
        // On compose la classe css du lien en fonction du mimetype du fichier, il est
        // nécessaire de remplacer les caractères qui ne sont pas autorisés dans une
        // classe css
        $searchReplaceArray = array('.' => '-', '/' => '-', '+' => '-', );
        $file_mimetype_class = "mimetype-".str_replace(
            array_keys($searchReplaceArray),
            array_values($searchReplaceArray),
            $file['metadata']['mimetype']
        );
        // On compose le lien de téléchargement du fichier
        $file_download_link = "".OM_ROUTE_FORM."&snippet=file&";
        if ($obj != "" && $champ != "" && $id != "") {
            $file_download_link .= "obj=".$obj."&amp;champ=".$champ."&amp;id=".$id;
        } else {
            $file_download_link .= "uid=".$fic;
        }
        if ($mode != "filestorage") {
            $file_download_link .= "&mode=".$mode;
        }
        // On affiche le bloc d'informations du fichier avec le lien de téléchargement
        $file_infos_block  =
        "
        <p class=\"file-infos-block\">
            <span>
                <a href=\"%s\" target=\"_blank\" class=\"om-prev-icon file-download %s\">
                    %s
                </a>
                <span class=\"discreet\">
                    —
                    %s, %s
                </span>
            </span>
        </p>
        ";
        printf($file_infos_block,
               $file_download_link,
               $file_mimetype_class,
               $file['metadata']['filename'],
               $file['metadata']['mimetype'],
               filesize_format($file['metadata']['size']));
        // Si le fichier est une image alors on affiche l'image
        if ($file['metadata']['mimetype'] == "image/jpeg"
            || $file['metadata']['mimetype'] == "image/png"
            || $file['metadata']['mimetype'] == "image/gif") {
            //
            $base64 = chunk_split(base64_encode($file['file_content']));
            //
            echo "\n<center>";
            echo "<img src=\"data:".$file['metadata']['mimetype'].";base64,".$base64."\" alt=\"".$file['metadata']['filename']."\" id='img-voir'/>";
            echo "</center>\n";
            //
        }
        //
        echo "\n</div>\n";
        //
        $this->f->displayLinkJsCloseWindow();

        /**
         *
         */
        //
        $this->f->displayEndContent();
    }
    // }}} SNIPPET_FORM - END
}
