<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_etat.class.php 4363 2018-11-19 16:41:21Z fmichon $
 */

if (file_exists("../gen/obj/om_etat.class.php")) {
    require_once "../gen/obj/om_etat.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_etat.class.php";
}

/**
 *
 */
class om_etat_core extends om_etat_gen {

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

        // ACTION - 004 - copier
        //
        $this->class_actions[4] = array(
            "identifier" => "copier",
            "portlet" => array(
                "type" => "action-direct-with-confirmation",
                "libelle" => __("copier"),
                "order" => 30,
                "class" => "copy-16",
            ),
            "view" => "formulaire",
            "method" => "copier",
            "button" => "copier",
            "permission_suffix" => "copier",
        );

        // ACTION - 005 - previsualiser
        //
        $this->class_actions[5] = array(
            "identifier" => "previsualiser",
            "portlet" => array(
                "type" => "action-blank",
                "libelle" => __("previsualiser"),
                "order" => 40,
                "class" => "pdf-16",
            ),
            "view" => "view_edition",
            "permission_suffix" => "previsualiser",
        );

    }

    /**
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_etat",
            "om_collectivite",
            "om_etat.id",
            "om_etat.libelle",
            "actif",
            "orientation",
            "format",
            "logo",
            "logoleft",
            "logotop",
            "margeleft",
            "margetop",
            "margeright",
            "margebottom",
            "header_offset",
            "header_om_htmletat",
            "titre_om_htmletat",
            "titreleft",
            "titretop",
            "titrelargeur",
            "titrehauteur",
            "titrebordure",
            "corps_om_htmletatex",
            "se_font",
            "se_couleurtexte",
            "footer_offset",
            "footer_om_htmletat",
            "om_sql",
            "'' as merge_fields",
            "'' as substitution_vars"
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_logo() {
        $sql_om_logo = "select id, (libelle||' ('||id||')') as libelle from ".DB_PREFIXE."om_logo";
        $sql_om_logo .= " where actif IS TRUE and om_collectivite=".$_SESSION['collectivite'];
        $sql_om_logo .= " order by libelle";
        return $sql_om_logo;
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_logo_by_id() {
        $sql_om_logo_by_id = "select id, (libelle||' ('||id||')') as libelle from ".DB_PREFIXE."om_logo";
        $sql_om_logo_by_id .= " WHERE id = '<idx>'";
        return $sql_om_logo_by_id;
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_sousetat() {
        $sql_om_sousetat = "select id, coalesce(libelle, id) as libelle from ".DB_PREFIXE."om_sousetat";
        $sql_om_sousetat .= " where actif IS TRUE and om_collectivite=".$_SESSION['collectivite'];
        $sql_om_sousetat .= " order by libelle";
        return $sql_om_sousetat;
    }

    /**
     *
     */
    function setOnchange(&$form, $maj) {
        //
        parent::setOnchange($form, $maj);
    }

    /**
     *
     */
    function setLib(&$form, $maj) {
        //
        parent::setLib($form, $maj);
        // Ajout du libellé poour que la traduction soit prise en compte
        $form->setLib('om_sql', __("om_requete"));
        $form->setLib('merge_fields', __("merge_fields"));
        $form->setLib('substitution_vars', __("substitution_vars"));
        //
        $form->setLib('header_offset', __("espacement"));
        $form->setLib('footer_offset', __("espacement"));
    }

    /**
     *
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);
        //
        $form->setType('merge_fields', 'textareastatic');
        $form->setType('substitution_vars', 'textareastatic');
        //
        if ($maj == 3) {
            $form->setType('merge_fields', 'hidden');
            $form->setType('substitution_vars', 'hidden');
        }
        // ajouter et modifier
        if ($maj == 0 || $maj == 1) {
            //
            $form->setType('orientation', 'select');
            $form->setType('format', 'select');
            $form->setType('titrebordure', 'select');
            //
            $form->setType('logo', 'select');
            //
            $form->setType('se_font', 'select');
            $form->setType('se_couleurtexte', 'rvb');
            //
            $form->setType('logotop', 'localisation_edition');
            $form->setType('titretop', 'localisation_edition');
        }
        // supprimer et consulter
        if ($maj == 2 or $maj == 3) {
            //
            $form->setType('orientation', 'selectstatic');
            $form->setType('format', 'selectstatic');
            $form->setType('titrebordure', 'selectstatic');
            //
            $form->setType('logo', 'selectstatic');
            //
            $form->setType('se_font', 'selectstatic');
        }
        // Pour les actions supplémentaires qui utilisent la vue formulaire
        // il est nécessaire de cacher les champs ou plutôt de leur affecter un
        // type pour que l'affichage se fasse correctement
        if ($maj == 4) {
            //
            foreach ($this->champs as $champ) {
                $form->setType($champ, "hidden");
            }
        }
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);
        //
        $contenu = array();
        $contenu[0] = array('P', 'L');
        $contenu[1] = array(__('portrait'), __('paysage'));
        $form->setSelect('orientation', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('A4', 'A3');
        $contenu[1] = array('A4', 'A3');
        $form->setSelect('format', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('', 'I', 'B', 'U', 'BI', 'UI');
        $contenu[1] = array(__('normal'), __('italique'), __('gras'), __('souligne'), __('italique').' '.__('gras'), __('souligne').' '.__('gras'));
        $form->setSelect('titreattribut', $contenu);
        $form->setSelect('corpsattribut', $contenu);
        $form->setSelect('footerattribut', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('helvetica', 'times', 'arial', 'courier');
        $contenu[1] = array('helvetica', 'times', 'arial', 'courier');
        $form->setSelect('titrefont', $contenu);
        $form->setSelect('corpsfont', $contenu);
        $form->setSelect('footerfont', $contenu);
        $form->setSelect('se_font', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('L', 'R', 'J', 'C');
        $contenu[1] = array(__('gauche'), __('droite'), __('justifie'), __('centre'));
        $form->setSelect('titrealign', $contenu);
        $form->setSelect('corpsalign', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('0', '1');
        $contenu[1] = array(__('sans'), __('avec'));
        $form->setSelect('titrebordure', $contenu);
        $form->setSelect('corpsbordure', $contenu);

        // LOCALISATION EDITION
        $config = array(
            "format" => "format",
            "orientation" => "orientation"
        );
        // Logo
        $contenu = $config;
        $contenu["x"] = "logoleft";
        $contenu["y"] = "logotop";
        $form->setSelect("logotop", $contenu);
        // Titre
        $contenu = $config;
        $contenu["x"] = "titreleft";
        $contenu["y"] = "titretop";
        $form->setSelect("titretop", $contenu);
        // Corps
        $contenu = $config;
        $contenu["x"] = "corpsleft";
        $contenu["y"] = "corpstop";
        $form->setSelect("corpstop", $contenu);

        //
        $this->init_select(
            $form,
            $this->f->db,
            $maj,
            null,
            "logo",
            $this->get_var_sql_forminc__sql("om_logo"),
            $this->get_var_sql_forminc__sql("om_logo_by_id"),
            false
        );
    }

    /**
     *
     */
    function setLayout(&$form, $maj) {

        $form->setFieldset($this->clePrimaire, 'D', __('Edition'), "collapsible");
            $form->setBloc($this->clePrimaire, 'D', "", "");
            $form->setBloc('actif', 'F', "", "");
            $form->setFieldset('orientation','D', __("Parametres generaux de l'edition"), "startClosed");
                $form->setBloc('orientation','D', "", "col_12");
                    $form->setBloc('orientation','D', __("Orientation et format"), "col_4");
                    $form->setBloc('format','F', "", "");
                    $form->setBloc('logo','D', __("Logo et positionnement"), "col_4");
                    $form->setBloc('logotop','F');
                $form->setBloc('margeleft','D', __("Marges du document"), "col_4");
                $form->setBloc('margebottom','F');
            $form->setFieldset('margebottom','F','');
        $form->setFieldset('margebottom','F','');

        $form->setFieldset('header_offset', 'D', __('En-tête'), 'startClosed');
        $form->setBloc('header_offset', 'DF', '', '');
        $form->setBloc('header_om_htmletat', 'DF', '', 'fullwidth hidelabel');
        $form->setFieldset('header_om_htmletat', 'F', '');

        $form->setFieldset('titre_om_htmletat','D', __('Titre'), "collapsible");
        $form->setBloc('titre_om_htmletat','DF', "", "fullwidth hidelabel");
        $form->setFieldset('titreleft','D', __("Parametres du titre de l'edition"), "startClosed");
        $form->setBloc('titreleft','D', __("Positionnement"));
        $form->setBloc('titreleft','D', "", "group");
        $form->setBloc('titretop','F');
        $form->setBloc('titrelargeur','D', "", "group");
        $form->setBloc('titrehauteur','F');
        $form->setBloc('titrehauteur','F');
        $form->setBloc('titrebordure','DF', __("Bordure"));
        $form->setFieldset('titrebordure','F','');
        $form->setFieldset('titrebordure','F','');

        $form->setFieldset('corps_om_htmletatex','D', __('Corps'), "collapsible");
            $form->setBloc('corps_om_htmletatex','DF', "", "fullwidth hidelabel");
            $form->setFieldset('se_font','D', __("Parametres des sous-etats"), "startClosed");
            $form->setFieldset('se_couleurtexte','F','');
        $form->setFieldset('se_couleurtexte','F','');

        $form->setFieldset('footer_offset', 'D', __('Pied de page'), 'startClosed');
        $form->setBloc('footer_offset', 'DF', '', '');
        $form->setBloc('footer_om_htmletat', 'DF', '', 'fullwidth hidelabel');
        $form->setFieldset('footer_om_htmletat', 'F', '');

        $form->setFieldset('om_sql','D', __('Champ(s) de fusion'), "collapsible");
        $form->setFieldset('substitution_vars', 'F', '');
    }

    /**
     * Retourne des valeurs par défaut pour la création d'une édition.
     *
     * @return array Tableau association champ/valeur.
     */
    function get_default_values() {
        //
        return array(
            //
            'orientation' => 'P',
            'format' => 'A4',
            //
            'logo' => '',
            'logoleft' => 10,
            'logotop' => 25,
            //
            'header_offset' => 10,
            'header_om_htmletat' => '',
            //
            'titre_om_htmletat' => __('Texte du titre'),
            'titreleft' => 105,
            'titretop' => 25,
            'titrelargeur' => 95,
            'titrehauteur' => 10,
            'titrefont' => 'arial',
            'titreattribut' => 'B',
            'titretaille' => 20,
            'titrebordure' => 0,
            'titrealign' => 'L',
            //
            'corps_om_htmletatex' => __('Texte du corps'),
            'corpsleft' => 14,
            'corpstop' => 66,
            'corpslargeur' => 110,
            'corpshauteur' => 5,
            'corpsfont' => 'times',
            'corpsattribut' => '',
            'corpstaille' => 10,
            'corpsbordure' => 0,
            'corpsalign' => 'J',
            //
            'se_font' => 'helvetica',
            'se_couleurtexte' => '0-0-0',
            //
            'footer_offset' => 12,
            'footer_om_htmletat' => '<p style="text-align:center;font-size:8pt;"><em>Page &numpage/&nbpages</em></p>',
            //
            'margeleft' => 10,
            'margetop' => 25,
            'margeright' => 10,
            'margebottom' => 25,
        );
    }

    /**
     *  Permet de pré-remplir les valeurs des formulaires.
     *
     * @param [object]   $form        formulaire
     * @param [integer]  $maj         mode
     * @param [integer]  $validation  validation
     */
    function set_form_default_values(&$form, $maj, $validation) {
        // En ajout
        if ($maj == 0) {
            foreach ($this->get_default_values() as $key => $value) {
                $this->form->setVal($key, $value);
            }
        }
    }

    /**
     *
     */
    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        parent::setVal($form, $maj, $validation);
        //
        $this->form->setVal('substitution_vars', $this->get_displayed_labels_substitution_vars());
    }

    /**
     *
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        parent::setValsousformulaire($form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire);
    }

    /**
     *
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        parent::verifier($val);
        // On verifie si il y a un autre id 'actif' pour la collectivite
        if ($this->valF['actif'] == "Oui") {
            //
            if ($this->getParameter("maj") == 0) {
                //
                $this->verifieractif("]", $val);
            } else {
                //
                $this->verifieractif($val[$this->clePrimaire], $val);
            }
        }
        // vérification de l'utilisation des sous-états
        // il doit n'y avoir qu'une occurence de chaque
        // Exécution de la requête
        $res = $this->f->db->query(
            $this->get_var_sql_forminc__sql("om_sousetat")
        );
        // Logger
        $this->addToLog(
            __METHOD__."(): db->query(\"".$this->get_var_sql_forminc__sql("om_sousetat")."\");",
            VERBOSE_MODE
        );
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            // vérification du nombre d'occurence
            // affichage d'un message d'erreur si > 1
            if(mb_substr_count($this->valF["corps_om_htmletatex"], ' id=&quot;'.$row["id"]) > 1) {
                $this->correct=false;
                $error_message =
                __("Le champ %s ne peut pas contenir plusieurs occurences du sous-etat %s.");
                $this->addToMessage(
                    sprintf(
                        $error_message,
                        "<b>".__("corps_om_htmletatex")."</b>",
                        "<b>".$row["libelle"]."</b>"
                    )
                );
            }
        }
    }

    /**
     * verification sur existence d un etat deja actif pour la collectivite
     */
    function verifieractif($id, $val) {
        //
        $table = "om_etat";
        $primary_key = "om_etat";
        //
        $sql = " SELECT ".$table.".".$primary_key." ";
        $sql .= " FROM ".DB_PREFIXE."".$table." ";
        $sql .= " WHERE ".$table.".id='".$val['id']."' ";
        $sql .= " AND ".$table.".om_collectivite='".$val['om_collectivite']."' ";
        $sql .= " AND ".$table.".actif IS TRUE ";
        if ($id != "]") {
            $sql .=" AND ".$table.".".$primary_key."<>'".$id."' ";
        }
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        $nbligne = $res->numrows();
        if ($nbligne > 0) {
            $this->correct = false;
            $msg = $nbligne." ";
            $msg .= __("etat(s) existant(s) dans l'etat actif. Il ".
                      "n'est pas possible d'avoir plus d'un etat");
            $msg .= " \"".$val["id"]."\" ".__("actif par collectivite.");
            $this->addToMessage($msg);
        }
    }

    /**
     * TREATMENT - copier.
     *
     * @return boolean
     */
    function copier($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Begin
        $this->begin_treatment(__METHOD__);

        // Récuperation de la valeur de la cle primaire de l'objet
        $id = $this->getVal($this->clePrimaire);
        // Récupération des valeurs de l'objet
        $this->setValFFromVal();
        // Maj des valeur de l'objet à copier
        $this->valF[$this->clePrimaire]=null;
        $this->valF["libelle"]=sprintf(__('copie du %s'), date('d/m/Y'));
        $this->valF["actif"]=false;
        // Si en sousform l'id de la collectivité est celle du formulaire principal
        if ($this->getParameter("retourformulaire") === "om_collectivite") {
            $this->valF["om_collectivite"] = $this->getParameter("idxformulaire");
        } else {
            $this->valF["om_collectivite"] = $_SESSION['collectivite'];
        }
        // Certains champs ne sont pas présent dans la table om_etat
        unset($this->valF["merge_fields"]);
        unset($this->valF["substitution_vars"]);
        //
        $ret = $this->ajouter($this->valF);
        // Si le traitement ne s'est pas déroulé correctement
        if ($ret !== true) {
            // Return
            return $this->end_treatment(__METHOD__, false);
        }

        // Message
        $this->addToMessage(__("L'element a ete correctement duplique."));
        // Return
        return $this->end_treatment(__METHOD__, true);
    }

    /**
     * VIEW - view_edition
     *
     * @return void
     */
    function view_edition() {
        //
        $this->checkAccessibility();
        // Tableau contenant le mode de visualisation et la cle primaire de la lettre type
        $params = array(
            "specific" => array(
                "mode" => "edition_direct_preview",
                "id" => $this->getVal($this->clePrimaire),
            ),
        );
        // Appelle la méthode de génération de pdf, en lui passant la clé primaire
        // d'état
        $pdfedition = $this->compute_pdf_output(
            "etat",
            $this->getVal("id"),
            null,
            "",
            $params
        );
        //
        $this->expose_pdf_output(
            $pdfedition["pdf_output"],
            $pdfedition["filename"]
        );
    }


    /**
     * Cette méthode affiche les liens vers les actions/vues permettant au
     * formulaire de fonctionner : affichage des champs de fusion en live +
     * listing des sous-états à insérer dans TinyMCE. Ces liens sont récupérés
     * en javascript pour des appels AJAX.
     *
     * @return void
     */
    function display_links_for_js() {
        // Met à disposition du JS le lien vers l'action pour afficher l'aide à
        // la saisie des champs de fusion
        printf(
            '<a id="labels-merge-fields-href-base" href="%s" style="display:none">&nbsp;</a>',
            OM_ROUTE_FORM."&obj=om_requete&action=4&contentonly=true&idx="
        );

        // Met à disposition du JS l'URL de l'action pour lister sous-états
        printf(
            '<a id="url-action-list-sous-etats" href="%s" style="display:none">&nbsp;</a>',
            OM_ROUTE_FORM."&obj=om_sousetat&idx=0&action=5"
        );
    }

    /**
     * Point d'entrée dans la VIEW formulaire.
     *
     * Cette méthode permet d'afficher des informations spécifiques en fin de
     * formulaire.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    public function formSpecificContent($maj) {
        $this->display_links_for_js();
    }

    /**
     * Point d'entrée dans la VIEW sousformulaire.
     *
     * Cette méthode permet d'afficher des informations spécifiques en fin de
     * formulaire.
     *
     * @param integer $maj Identifant numérique de l'action.
     *
     * @return void
     */
    public function sousformSpecificContent($maj) {
        $this->display_links_for_js();
    }
}
