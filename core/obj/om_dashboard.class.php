<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_dashboard.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_dashboard.class.php")) {
    require_once "../gen/obj/om_dashboard.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_dashboard.class.php";
}

/**
 *
 */
class om_dashboard_core extends om_dashboard_gen {

    /**
     * On active les nouvelles actions sur cette classe.
     */
    var $activate_class_action = true;

    /**
     * Définition des actions disponibles sur la classe.
     *
     * @return void
     */
    function init_class_actions() {

        // On récupère les actions génériques définies dans la méthode
        // d'initialisation de la classe parente
        parent::init_class_actions();

        // ACTION - 004 - composer
        //
        $this->class_actions[4] = array(
            "identifier" => "composer",
            "view" => "view_composer",
            "permission_suffix" => "ajouter",
        );

        // ACTION - 005 - composer
        //
        $this->class_actions[5] = array(
            "identifier" => "composer-widget-ctl",
            "view" => "view_composer_widget_ctl",
            "permission_suffix" => "ajouter",
        );


    }

    /**
     * Permet de modifier le fil d'Ariane depuis l'objet pour un formulaire
     * @param string    $ent    Fil d'Ariane récupéréré
     * @return                  Fil d'Ariane
     */
    function getFormTitle($ent) {
        //
        if ($this->getParameter("maj") == 4) {
            return __("administration")." -> ".__("tableaux de bord")." -> ".__("composition");
        }
        //
        return parent::getFormTitle($ent);
    }

    /**
     * VIEW - view_composer.
     *
     * @return void
     */
    function view_composer() {
        // Verification de l'accessibilité sur l'élément
        // Si l'utilisateur n'a pas accès à l'élément dans le contexte actuel
        // on arrête l'exécution du script
        $this->checkAccessibility();

        /**
         * Affichage du formulaire de sélection du profil
         */
        // Ouverture du formulaire
        $this->f->layout->display__form_container__begin(array(
            "action" => $this->getDataSubmit(),
            "id" => "dashboard_composer_form",
        ));
        // Paramétrage des champs du formulaire
        $champs = array("om_profil");
        // Création d'un nouvel objet de type formulaire
        $form = $this->f->get_inst__om_formulaire(array(
            "validation" => 0,
            "maj" => 0,
            "champs" => $champs,
        ));
        // Paramétrage des champs du formulaire
        $form->setLib("om_profil", __("Tableau de bord pour le profil"));
        $form->setType("om_profil", "select");
        $form->setTaille("om_profil", 25);
        $form->setOnChange("om_profil", "submit()");
        $form->setMax("om_profil", 25);
        $form->setVal("om_profil", (isset($_POST["om_profil"]) ? $_POST["om_profil"] : ""));
        // Si l'option 'gestion des permissions par hiérarchie des profils' n'est pas
        // activée alors on affiche pas les code de hiérarchie sinon on les affiche
        // dans la liste de sélection des profils
        if ($this->f->get_config__permission_by_hierarchical_profile() === false) {
            //
            $sql = "
            SELECT
            om_profil.om_profil,
            om_profil.libelle as lib
            FROM ".DB_PREFIXE."om_profil
            ORDER BY lib";
        } else {
            //
            $sql = "
            SELECT
            om_profil.om_profil,
            concat(om_profil.hierarchie, ' - ', om_profil.libelle) as lib
            FROM ".DB_PREFIXE."om_profil
            ORDER BY om_profil.hierarchie";
        }
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        $contenu = array(array(""), array(__("choisir le profil")));
        while ($row =& $res->fetchrow()) {
            $contenu[0][] = $row[0];
            $contenu[1][] = $row[1];
        }
        $form->setSelect("om_profil", $contenu);
        // Affichage du formulaire
        $form->entete();
        $form->afficher($champs, 0, false, false);
        $form->enpied();
        // Fermeture du fomulaire
        $this->f->layout->display__form_container__end();
        /**
         *
         */
        if (!isset($_POST["om_profil"]) || $_POST["om_profil"] == "") {
            //
            return;
        }

        // Initialisation des paramètres
        $params = array(
            "edition" => array(
                "default_value" => 1,
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        //
        echo "<div id=\"dashboard-composer\">\n";

        /**
         * Tableau de bord
         */
        // Ouverture du conteneur #dashboard
        echo "<div id=\"dashboard\">\n";
        // Conteneur permettant de recevoir d'eventuels messages d'erreur des requetes
        // Ajax
        echo "<div id=\"info\">";
        echo "</div>\n";
        // Si le mode edition est active alors on affiche l'action pour ajouter un
        // nouveau widget
        if ($edition == 1) {
            $widget_add_action = "
        <div class=\"widget-add-action\" id=\"dashboard_profil_%s\">
          <a href=\"#\">
            <span class=\"om-icon om-icon-25 add-25\">
              %s
            </span>
          </a>
          <div class=\"visualClear\"><!-- --></div>
        </div>
            ";
            printf($widget_add_action, $_POST["om_profil"], __("Ajouter un widget"));
        }
        // Si le mode edition est activé alors on affiche un lien contenant
        // le lien vers les actions de contrôle des widgets
        if ($edition == 1) {
            printf(
                '<a id="widgetctl-href-base" href="%s" style="display:none">&nbsp;</a>',
                OM_ROUTE_FORM."&obj=om_dashboard&idx=0&action=5"
            );
        }
        // Ouverture du conteneur de colonnes
        echo "<div class=\"col".$this->f->get_config__dashboard_nb_column()."\">\n";
        // On boucle sur chacune des colonnes
        for ($i = 1; $i <= $this->f->get_config__dashboard_nb_column(); $i++) {
            // Ouverture du conteneur .column
            echo "<div class=\"column\" id=\"column_".$i."\">\n";
            // Requete de selection de tous les widgets de la colonne
            $sql = " SELECT ";
            $sql .= " om_dashboard.om_dashboard, ";
            $sql .= " om_widget.om_widget as widget, ";
            $sql .= " om_widget.libelle as libelle, ";
            $sql .= " CASE WHEN om_widget.type = 'web' THEN om_widget.lien ELSE om_widget.script END as lien, ";
            $sql .= " CASE WHEN om_widget.type = 'web' THEN om_widget.texte ELSE om_widget.arguments END as texte, ";
            $sql .= " om_widget.type as type, ";
            $sql .= " om_dashboard.position ";
            $sql .= " FROM ".DB_PREFIXE."om_dashboard ";
            $sql .= " INNER JOIN ".DB_PREFIXE."om_widget ON om_dashboard.om_widget=om_widget.om_widget ";
            $sql .= " WHERE ";
            $sql .= " om_dashboard.bloc = 'C".intval($i)."' ";
            $sql .= " AND om_dashboard.om_profil = ".intval($_POST["om_profil"])." ";
            $sql .= " ORDER BY position";
            // Exécution de la requête
            $res = $this->f->db->query($sql);
            // Logger
            $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            // On boucle sur chacun des widgets
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                // Affichage du widget
                $this->display_dashboard_widget(
                    $row['om_dashboard'],
                    $row['libelle'],
                    $row['texte'],
                    $row['lien'],
                    $row['type'],
                    $edition
                );
            }
            // Fermeture du conteneur .column
            echo "</div>\n";
        }
        // On affiche un conteneur vide pour avec la propriete clear a both pour
        // reinitialiser le positionnement des blocs
        echo "<div class=\"both\"><!-- --></div>\n";
        // Fermeture du conteneur de colonnes
        echo "</div>\n";
        // Fermeture du conteneur #dashboard
        echo "</div>\n";
        // Fermeture du conteneur #dashboard-composer
        echo "</div>\n";

    }

    function view_dashboard() {
        /**
         * Tableau de bord
         */
        // Ouverture du conteneur #dashboard
        echo "<div id=\"dashboard\">\n";
        // Conteneur permettant de recevoir d'eventuels messages d'erreur des requetes
        // Ajax
        echo "<div id=\"info\">";
        echo "</div>\n";
        // Mode Edition
        $edition = 0;
        // Ouverture du conteneur de colonnes
        echo "<div class=\"col".$this->f->get_config__dashboard_nb_column()."\">\n";
        // On boucle sur chacune des colonnes
        for ($i = 1; $i <= $this->f->get_config__dashboard_nb_column(); $i++) {
            // Ouverture du conteneur .column
            echo "<div class=\"column\" id=\"column_".$i."\">\n";
            // Requete de selection de tous les widgets de la colonne
            $sql = " SELECT ";
            $sql .= " om_dashboard.om_dashboard, ";
            $sql .= " om_widget.om_widget as widget, ";
            $sql .= " om_widget.libelle as libelle, ";
            $sql .= " CASE WHEN om_widget.type = 'web' THEN om_widget.lien ELSE om_widget.script END as lien, ";
            $sql .= " CASE WHEN om_widget.type = 'web' THEN om_widget.texte ELSE om_widget.arguments END as texte, ";
            $sql .= " om_widget.type as type, ";
            $sql .= " om_dashboard.position ";
            $sql .= " FROM ".DB_PREFIXE."om_dashboard ";
            $sql .= " INNER JOIN ".DB_PREFIXE."om_widget ON om_dashboard.om_widget=om_widget.om_widget ";
            $sql .= " WHERE ";
            $sql .= " om_dashboard.bloc ='C".intval($i)."' ";
            $sql .= " AND om_dashboard.om_profil = ".intval($this->f->user_infos['om_profil'])." ";
            $sql .= " ORDER BY position";
            // Exécution de la requête
            $res = $this->f->db->query($sql);
            // Logger
            $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            // On boucle sur chacun des widgets
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                // Affichage du widget
                $this->display_dashboard_widget(
                    $row['om_dashboard'],
                    $row['libelle'],
                    $row['texte'],
                    $row['lien'],
                    $row['type'],
                    $edition
                );
            }
            // Fermeture du conteneur .column
            echo "</div>\n";
        }
        // On affiche un conteneur vide pour avec la propriete clear a both pour
        // reinitialiser le positionnement des blocs
        echo "<div class=\"both\"><!-- --></div>\n";
        // Fermeture du conteneur de colonnes
        echo "</div>\n";
        // Fermeture du conteneur #dashboard
        echo "</div>\n";
    }


    function view_composer_widget_ctl() {

        // Initialisation des paramètres
        $params = array(
            "mode" => array(
                "default_value" => null,
            ),
            "widget" => array(
                "default_value" => null,
            ),
            "profil" => array(
                "default_value" => 0,
            ),
        );
        foreach ($this->f->get_initialized_parameters($params) as $key => $value) {
            ${$key} = $value;
        }

        /**
         * UPDATE
         */
        if ($mode == "update") {
            //
            $alldata = array();
            foreach($this->f->get_submitted_get_value() as $key => $values) {
                // On souhaite récupérer uniquement les paramètres column_*
                if (!$this->f->starts_with($key, "column_")) {
                    continue;
                }
                //
                $bloc = "C".str_replace("column_", "", $key);
                //
                $widgets = explode("x", $this->f->get_submitted_get_value($key));
                //
                foreach($widgets as $i => $widget_id) {
                    //
                    $position = $i+1;
                    //
                    $widget_id = str_replace("widget_", "", $widget_id);
                    // Lorsqu'une colonne est vide, il y a une valeur vide dans le
                    // tableau widget, donc si c'est le cas on passe a l'iteration
                    // suivante
                    if ($widget_id == "") {
                        continue;
                    }
                    //
                    array_push($alldata, array($position, $bloc, $widget_id));
                }
            }

            //
            $sql = "update ".DB_PREFIXE."om_dashboard set ";
            $sql .= " position=?, ";
            $sql .= " bloc=? ";
            $sql .= " where om_dashboard=? ";
            //
            $sth = $this->f->db->prepare($sql);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($sth);
            // Exécution de la requête
            $res = $this->f->db->executeMultiple($sth, $alldata);
            // Logger
            $this->f->addToLog(__METHOD__."(): db->executeMultiple(\"".$sth."\", ".print_r($alldata, true).");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            return;
        }
        /**
         * DELETE
         */
        if ($mode == "delete") {
            //
            $widget = str_replace("widget_", "", $widget);
            // Suppression du widget
            $sql = "delete from ".DB_PREFIXE."om_dashboard where om_dashboard = ".intval($widget);
            // Exécution de la requête
            $res = $this->f->db->query($sql);
            // Logger
            $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            return;
        }

        /**
         * INSERT
         */
        if ($mode == "insert") {
            //
            $bloc = "C1";
            // Sur la validation du formulaire
            if (isset($_POST['widget_add_form_valid']) && isset($_POST['widget']) && is_numeric($_POST['widget'])) {
                // Ajout du widget dans la base et affichage de ce dernier
                //
                (isset($_POST['widget']) && is_numeric($_POST['widget']) ? $widget = $_POST['widget'] : $widget = 0);
                //
                (isset($_POST['profil']) && is_numeric($_POST['profil']) ? $profil = $_POST['profil'] : $profil = 0);
                //
                $valF = array();
                //
                $valF['om_dashboard'] = $this->f->db->nextId(DB_PREFIXE."om_dashboard");
                // Logger
                $this->f->addToLog(__METHOD__."(): db->nextId(\"".DB_PREFIXE."om_dashboard\");", VERBOSE_MODE);
                //
                $valF['om_profil'] = $profil;
                $valF['om_widget'] = $widget;
                $valF['bloc'] = $bloc;
                $valF['position'] = 1;
                // XXX
                $sql = "update ".DB_PREFIXE."om_dashboard set position=position+1 where om_profil = ".intval($profil)." and bloc ='".$this->f->db->escapeSimple($bloc)."'";
                // Exécution de la requête
                $position = $this->f->db->query($sql);
                // Logger
                $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $this->f->isDatabaseError($position);
                // Exécution de la requête
                $res = $this->f->db->autoExecute(DB_PREFIXE."om_dashboard", $valF, DB_AUTOQUERY_INSERT);
                // Logger
                $this->f->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE."om_dashboard\", ".print_r($valF, true).", DB_AUTOQUERY_INSERT);", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $this->f->isDatabaseError($res);
                // On retourne l'id du widget dans le tableau de bord de l'utilisateur
                // pour l'afficher
                echo $valF['om_dashboard'];
            } elseif (!isset($_POST['widget_add_form_valid'])) {
                //
                $profil = str_replace("dashboard_profil_", "", $profil);
                // Composition du formulaire
                $content = "";
                // Description du formulaire
                $content .= __("Selectionner le widget a inserer puis cliquer sur ".
                              "le bouton 'Valider' pour valider votre selection.");
                // Ouverture du formulaire
                $content .= "<form";
                $content .= " method=\"post\"";
                $content .= " id=\"widget_add_form\"";
                $content .= " action=\"#\"";
                $content .= ">\n";
                // On recupere la liste des widgets que l'utilisateur peut inserer en
                // fonction de son profil
                $sql = "select om_widget as widget, libelle from ".DB_PREFIXE."om_widget ";
                $sql .= " order by libelle";
                // Exécution de la requête
                $res = $this->f->db->query($sql);
                // Logger
                $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $this->f->isDatabaseError($res);
                // Liste des widgets que l'utilisateur peut inserer en fonction de son
                // profil
                $content .= "<select name=\"widget\">";
                while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                    $content .= "<option value='".$row['widget']."' >".$row['libelle']."</option>";
                }
                $content .= "</select>\n";
                // Valeur du profil
                $content .= "<input id=\"widget_add_form_profil\" type=\"hidden\" value=\"".$profil."\" name=\"profil\" />\n";
                // Bouton Valider
                $content .= "<input type=\"button\" value=\"".__("Valider")."\" name=\"widget.add.form.valid\" onclick=\"widget_add_form_post()\" />\n";
                // Fermeture du formulaire
                $content .= "</form>\n";
                // Affichage du widget
                $this->display_dashboard_widget(
                    "",
                    __("Ajouter un nouveau widget"),
                    $content,
                    "",
                    "web",
                    true
                );

            } else {
                echo "null";
            }
            return;
        }

        /**
         * VIEW
         */
        if ($mode == "view") {
            // Requete de selection du widget
            $sql = " SELECT ";
            $sql .= " om_dashboard.om_dashboard, ";
            $sql .= " om_widget.om_widget as widget, ";
            $sql .= " om_widget.libelle as libelle, ";
            $sql .= " CASE WHEN om_widget.type = 'web' THEN om_widget.lien ELSE om_widget.script END as lien, ";
            $sql .= " CASE WHEN om_widget.type = 'web' THEN om_widget.texte ELSE om_widget.arguments END as texte, ";
            $sql .= " om_widget.texte as texte, ";
            $sql .= " om_widget.type as type, ";
            $sql .= " om_dashboard.position ";
            $sql .= " FROM ".DB_PREFIXE."om_dashboard ";
            $sql .= " INNER JOIN ".DB_PREFIXE."om_widget on om_dashboard.om_widget=om_widget.om_widget ";
            $sql .= " WHERE ";
            $sql .= " om_dashboard.om_dashboard=".intval($widget)." ";
            // Exécution de la requête
            $res = $this->f->db->query($sql);
            // Logger
            $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            //
            $row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
            // Affichage du widget
            $this->display_dashboard_widget(
                $row['om_dashboard'],
                $row['libelle'],
                $row['texte'],
                $row['lien'],
                $row["type"],
                true
            );
            return;
        }

    }

    /**
     *
     */
    function display_dashboard_widget($id = NULL, $title = NULL, $content = NULL, $footer = NULL, $type = NULL, $mode_edit = false) {
        //

        //
        if ($type == "file"
            && !file_exists("../app/widget_".$footer.".php")) {
            //
            return;
        }

        //
        $class_sup = "";

        //
        if ($type == "file") {
            //
            $class_sup = "widget_".$footer;
            //
            $file =  "../app/widget_".$footer.".php";
            $footer = "#";
            // Enclenchement de la tamporisation de sortie
            ob_start();
            //
            $f = $this->f;
            include $file;
            //
            $content = ob_get_clean();
            //
            if (isset($widget_is_empty)
                && $widget_is_empty == true
                && $mode_edit != true) {
                //
                return;
            }
        }

        // Ouverture du conteneur du widget
        echo "<div";
        echo " class=\"widget ui-widget ui-widget-content ui-helper-clearfix ui-corner-all ".$class_sup."\"";
        echo " id=\"widget_".$id."\"";
        echo ">\n";

        // Titre du widget
        echo "<div class=\"widget-header ";
        if ($mode_edit == true) {
            echo "widget-header-edit widget-header-move ";
        }
        echo "ui-widget-header ui-corner-all\">";
        echo "<h3>";
        echo $title;
        echo "</h3>";
        echo "</div>\n";

        // Ouverture du wrapper : Contenu + Footer
        echo "<div class=\"widget-content-wrapper\">\n";

        // Contenu du widget
        echo "<!-- Start Widget Content -->\n";
        echo "<div class=\"widget-content\">\n\n";
        //
        echo $content;
        //
        echo "\n\n</div>\n";
        echo "<!-- End Widget Content -->\n";

        // Footer du widget
        if ($footer != "#" && $footer != "" && $footer != NULL) {
            echo "<div class=\"widget-footer\">\n";
            echo "<a href='".$footer."' >";
            if (isset($footer_title)) {
                echo $footer_title;
            } else {
                echo __("Acceder au lien");
            }
            echo "</a>\n";
            echo "</div>\n";
        }

        // Fermeture du wrapper : Contenu + Footer
        echo "</div>\n";

        // Fermeture du conteneur du widget
        echo "</div>\n";

    }

}
