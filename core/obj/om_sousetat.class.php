<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sousetat.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_sousetat.class.php")) {
    require_once "../gen/obj/om_sousetat.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_sousetat.class.php";
}

/**
 *
 */
class om_sousetat_core extends om_sousetat_gen {

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

        // ACTION - 005 - json-list
        //
        $this->class_actions[5] = array(
            "identifier" => "json-list",
            "view" => "view_json_list",
            "permission_suffix" => "consulter",
        );

    }

    /**
     *
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);
        // ajouter et modifier
        if ($maj == 0 || $maj == 1) {
            //
            $form->setType('titreattribut', 'select');
            $form->setType('titrefont', 'select');
            $form->setType('titrealign', 'select');
            $form->setType('titrebordure', 'select');
            $form->setType('titrefond', 'select');
            $form->setType('entete_flag', 'select');
            $form->setType('entete_fond', 'select');
            $form->setType('tableau_bordure', 'select');
            $form->setType('cellule_fond', 'select');
            $form->setType('cellule_fond_total', 'select');
            $form->setType('cellule_fond_moyenne', 'select');
            $form->setType('cellule_fond_nbr', 'select');
            //
            $form->setType('titrefondcouleur', 'rvb');
            $form->setType('titretextecouleur', 'rvb');
            $form->setType('entete_fondcouleur','rvb');
            $form->setType('entete_textecouleur','rvb');
            $form->setType('bordure_couleur','rvb');
            $form->setType('se_fond1','rvb');
            $form->setType('se_fond2','rvb');
            $form->setType('cellule_fondcouleur_total','rvb');
            $form->setType('cellule_fondcouleur_moyenne','rvb');
            $form->setType('cellule_fondcouleur_nbr','rvb');
        }
        //
        if ($maj == 2 or $maj == 3) {
            //
            $form->setType('titreattribut', 'selectstatic');
            $form->setType('titrefont', 'selectstatic');
            $form->setType('titrealign', 'selectstatic');
            $form->setType('titrebordure', 'selectstatic');
            $form->setType('titrefond', 'selectstatic');
            $form->setType('entete_flag', 'selectstatic');
            $form->setType('entete_fond', 'selectstatic');
            $form->setType('tableau_bordure', 'selectstatic');
            $form->setType('cellule_fond', 'selectstatic');
            $form->setType('cellule_fond_total', 'selectstatic');
            $form->setType('cellule_fond_moyenne', 'selectstatic');
            $form->setType('cellule_fond_nbr', 'selectstatic');
        }
    }

    /**
     *
     */
    function setMax(&$form, $maj) {

        $form->setMax('titre', 3);
        $form->setMax('om_sql', 10);
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);
        //
        $contenu=array();
        $contenu[0]=array('P','L');
        $contenu[1]=array(__("portrait"), __("paysage"));
        $form->setSelect("orientation",$contenu);
        //
        $contenu=array();
        $contenu[0]=array('A4','A3');
        $contenu[1]=array('A4','A3');
        $form->setSelect("format",$contenu);
        //
        $contenu=array();
        $contenu[0]=array('','I','B','U','BI','UI');
        $contenu[1]=array(__("normal"), __("italique"), __("gras"), __("souligne"), __("italique")." ".__("gras"), __("souligne")." ".__("gras"));
        $form->setSelect("titreattribut",$contenu);
        //
        $contenu=array();
        $contenu[0]=array('helvetica','times','arial','courier');
        $contenu[1]=array('helvetica','times','arial','courier');
        $form->setSelect("titrefont",$contenu);
        //
        $contenu=array();
        $contenu[0]=array('L','R','J','C');
        $contenu[1]=array(__("gauche"), __("droite"), __("justifie"), __("centre"));
        $form->setSelect("titrealign",$contenu);
        //
        $contenu=array();
        $contenu[0]=array('0','1');
        $contenu[1]=array(__("sans"), __("avec"));
        $form->setSelect("titrebordure",$contenu);
        $form->setSelect("entete_flag",$contenu);
        $form->setSelect("tableau_bordure",$contenu);
        // fond
        $contenu[1]=array(__("transparent"), __("fond"));
        $form->setSelect("titrefond",$contenu);
        $form->setSelect("entete_fond",$contenu);
        $form->setSelect("cellule_fond",$contenu);
        $form->setSelect("cellule_fond_total",$contenu);
        $form->setSelect("cellule_fond_moyenne",$contenu);
        $form->setSelect("cellule_fond_nbr",$contenu);

        // edition position
        $config = array(
            "format" => "format",
            "orientation" => "orientation"
        );
        //
        $contenu = $config;
        $contenu["x"] = "logoleft";
        $contenu["y"] = "logotop";
        $form->setSelect("logotop", $contenu);
        //
        $contenu = $config;
        $contenu["x"] = "titreleft";
        $contenu["y"] = "titretop";
        $form->setSelect("titretop", $contenu);
        //
        $contenu = $config;
        $contenu["x"] = "corpsleft";
        $contenu["y"] = "corpstop";
        $form->setSelect("corpstop", $contenu);
    }

    /**
     *
     */
    function setRegroupe(&$form, $maj) {

        $form->setRegroupe('om_collectivite','D', __('om_collectivite'),"collapsible");
        $form->setRegroupe('id','G','');
        $form->setRegroupe('libelle','G','');
        $form->setRegroupe('actif','F','');

        $form->setRegroupe('titrehauteur','D', __("parametres")."&nbsp;".__("titre"), "startClosed");
        $form->setRegroupe('titrelargeur','G','');
        $form->setRegroupe('titrefont','G','');
        $form->setRegroupe('titreattribut','G','');
        $form->setRegroupe('titretaille','G','');
        $form->setRegroupe('titrebordure','G','');
        $form->setRegroupe('titrealign','G','');
        $form->setRegroupe('titrefond','G','');
        $form->setRegroupe('titrefondcouleur','G','');
        $form->setRegroupe('titretextecouleur','G','');
        $form->setRegroupe('intervalle_debut','G','');
        $form->setRegroupe('intervalle_fin','F','');
        // entete
        $form->setRegroupe('entete_flag','D', __("entete")."&nbsp;".__("du")."&nbsp;".__("tableau"), "startClosed");
        $form->setRegroupe('entete_fond','G','');
        $form->setRegroupe('entete_orientation','G','');
        $form->setRegroupe('entete_hauteur','G','');
        $form->setRegroupe('entetecolone_bordure','G','');
        $form->setRegroupe('entetecolone_align','G','');
        $form->setRegroupe('entete_fondcouleur','G','');
        $form->setRegroupe('entete_textecouleur','F','');
        // data
        $form->setRegroupe('tableau_largeur','D', __("data")."&nbsp;".__("du")."&nbsp;".__("tableau"), "startClosed");
        $form->setRegroupe('tableau_bordure','G','');
        $form->setRegroupe('tableau_fontaille','G','');
        $form->setRegroupe('bordure_couleur','G','');
        $form->setRegroupe('se_fond1','G','');
        $form->setRegroupe('se_fond2','F','');
        // cellule
        $form->setRegroupe('cellule_fond','D', __("cellule")."&nbsp;".__("du")."&nbsp;".__("tableau"), "startClosed");
        $form->setRegroupe('cellule_hauteur','G','');
        $form->setRegroupe('cellule_largeur','G','');
        $form->setRegroupe('cellule_bordure_un','G','');
        $form->setRegroupe('cellule_bordure','G','');
        $form->setRegroupe('cellule_align','F','');
        // total
        $form->setRegroupe('cellule_fond_total','D', __("total")."&nbsp;".__("du")."&nbsp;".__("tableau"), "startClosed");
        $form->setRegroupe('cellule_fontaille_total','G','');
        $form->setRegroupe('cellule_hauteur_total','G','');
        $form->setRegroupe('cellule_fondcouleur_total','G','');
        $form->setRegroupe('cellule_bordure_total','G','');
        $form->setRegroupe('cellule_align_total','F','');
        // moyenne
        $form->setRegroupe('cellule_fond_moyenne','D', __("moyenne")."&nbsp;".__("du")."&nbsp;".__("tableau"), "startClosed");
        $form->setRegroupe('cellule_fontaille_moyenne','G','');
        $form->setRegroupe('cellule_hauteur_moyenne','G','');
        $form->setRegroupe('cellule_fondcouleur_moyenne','G','');
        $form->setRegroupe('cellule_bordure_moyenne','G','');
        $form->setRegroupe('cellule_align_moyenne','F','');
        // nbr
        $form->setRegroupe('cellule_fond_nbr','D', __("nombre")."&nbsp;".__("enregistrement")."&nbsp;".__("du")."&nbsp;".__("tableau"), "startClosed");
        $form->setRegroupe('cellule_fontaille_nbr','G','');
        $form->setRegroupe('cellule_hauteur_nbr','G','');
        $form->setRegroupe('cellule_fondcouleur_nbr','G','');
        $form->setRegroupe('cellule_bordure_nbr','G','');
        $form->setRegroupe('cellule_align_nbr','F','');
        // operations
        $form->setRegroupe('cellule_numerique','D', __("operations")."&nbsp;".__("du")."&nbsp;".__("tableau"), "startClosed");
        $form->setRegroupe('cellule_total','G','');
        $form->setRegroupe('cellule_moyenne','G','');
        $form->setRegroupe('cellule_compteur','F','');

    }

    /**
     *
     */
    function setGroupe(&$form, $maj) {

        $form->setGroupe('om_collectivite','D');
        $form->setGroupe('id','G');
        $form->setGroupe('libelle','G');
        $form->setGroupe('actif','F');

        $form->setGroupe('titrehauteur','D');
        $form->setGroupe('titrefont','G');
        $form->setGroupe('titreattribut','F');

        $form->setGroupe('titretaille','D');
        $form->setGroupe('titrebordure','G');
        $form->setGroupe('titrealign','F');

        $form->setGroupe('titrefond','D');
        $form->setGroupe('titrefondcouleur','G');
        $form->setGroupe('titretextecouleur','F');

        $form->setGroupe('intervalle_debut','D');
        $form->setGroupe('intervalle_fin','F');
        // entete
        $form->setGroupe('entete_flag','D');
        $form->setGroupe('entete_fond','F');
        $form->setGroupe('entete_orientation','D');
        $form->setGroupe('entete_hauteur','F');
        $form->setGroupe('entetecolone_bordure','D');
        $form->setGroupe('entetecolone_align','F');
        $form->setGroupe('entete_fondcouleur','D');
        $form->setGroupe('entete_textecouleur','F');
        // data
        $form->setGroupe('tableau_largeur','D');
        $form->setGroupe('tableau_bordure','G');
        $form->setGroupe('tableau_fontaille','F');

        $form->setGroupe('bordure_couleur','D');
        $form->setGroupe('se_fond1','G');
        $form->setGroupe('se_fond2','F');
        // cellules
        $form->setGroupe('cellule_fond','D');
        $form->setGroupe('cellule_hauteur','F');

        $form->setGroupe('cellule_largeur','D');
        $form->setGroupe('cellule_bordure_un','F');
        $form->setGroupe('cellule_bordure','D');
        $form->setGroupe('cellule_align','F');
        // total
        $form->setGroupe('cellule_fond_total','D');
        $form->setGroupe('cellule_fontaille_total','F');
        $form->setGroupe('cellule_hauteur_total','D');
        $form->setGroupe('cellule_fondcouleur_total','F');
        $form->setGroupe('cellule_bordure_total','D');
        $form->setGroupe('cellule_align_total','F');
        // moyenne
        $form->setGroupe('cellule_fond_moyenne','D');
        $form->setGroupe('cellule_fontaille_moyenne','F');
        $form->setGroupe('cellule_hauteur_moyenne','D');
        $form->setGroupe('cellule_fondcouleur_moyenne','F');
        $form->setGroupe('cellule_bordure_moyenne','D');
        $form->setGroupe('cellule_align_moyenne','F');
        // nbr
        $form->setGroupe('cellule_fond_nbr','D');
        $form->setGroupe('cellule_fontaille_nbr','F');
        $form->setGroupe('cellule_hauteur_nbr','D');
        $form->setGroupe('cellule_fondcouleur_nbr','F');
        $form->setGroupe('cellule_bordure_nbr','D');
        $form->setGroupe('cellule_align_nbr','F');
        // operations
        $form->setGroupe('cellule_numerique','D');
        $form->setGroupe('cellule_total','F');
        $form->setGroupe('cellule_moyenne','D');
        $form->setGroupe('cellule_compteur','F');

    }

    /**
     *
     */
    function setLib(&$form, $maj) {

        $form->setLib('titre', __('titre'));

        $form->setLib('titrehauteur', __('hauteur'));
        $form->setLib('titrefont', __('font'));
        $form->setLib('titreattribut','');
        $form->setLib('titretaille', __('taille'));
        $form->setLib('titrebordure', __('bordure'));
        $form->setLib('titrealign', __('align'));
        $form->setLib('titrefondcouleur', __('fond'));
        $form->setLib('titretextecouleur', __('texte'));
        $form->setLib('intervalle_debut', __('intervalle')."&nbsp;".__('debut'));
        $form->setLib('intervalle_fin', __('fin'));

        $form->setLib('entete_flag', __('flag'));
        $form->setLib('entete_fond', __('fin'));
        $form->setLib('entete_orientation', __('orientation'));
        $form->setLib('entete_hauteur', __('hauteur'));

        $form->setLib('entetecolone_bordure', __('bordure'));
        $form->setLib('entetecolone_align', __('align'));
        $form->setLib('entete_fondcouleur', __('fond'));
        $form->setLib('entete_textecouleur', __('couleur'));
        // data
        $form->setLib('tableau_largeur', __('largeur'));
        $form->setLib('tableau_bordure', __('bordure'));
        $form->setLib('tableau_fontaille', __('taille'));
        $form->setLib('bordure_couleur', __('bordure'));
        $form->setLib('se_fond1', __('fond')."&nbsp;".__('un'));
        $form->setLib('se_fond2', __('fond')."&nbsp;".__('deux'));
        // cellule
        $form->setLib('cellule_fond','');
        $form->setLib('cellule_hauteur', __('hauteur'));
        $form->setLib('cellule_largeur', __('largeur'));
        $form->setLib('cellule_bordure_un', __('bordure')."&nbsp;1&nbsp;".__('cellule'));
        $form->setLib('cellule_bordure', __('bordure'));
        $form->setLib('cellule_align', __('align'));
        // total
        $form->setLib('cellule_fond_total', __('fond')."&nbsp;".__('cellule'));
        $form->setLib('cellule_fontaille_total', __('taille'));
        $form->setLib('cellule_hauteur_total', __('hauteur'));
        $form->setLib('cellule_fondcouleur_total', __('fond'));
        $form->setLib('cellule_bordure_total', __('bordure'));
        $form->setLib('cellule_align_total', __('align'));
        // moyenne
        $form->setLib('cellule_fond_moyenne', __('fond')."&nbsp;".__('cellule'));
        $form->setLib('cellule_fontaille_moyenne', __('taille'));
        $form->setLib('cellule_hauteur_moyenne', __('hauteur'));
        $form->setLib('cellule_fondcouleur_moyenne', __('fond'));
        $form->setLib('cellule_bordure_moyenne', __('bordure'));
        $form->setLib('cellule_align_moyenne', __('align'));
        // nbr
        $form->setLib('cellule_fond_nbr', __('fond')."&nbsp;".__('cellule'));
        $form->setLib('cellule_fontaille_nbr', __('taille'));
        $form->setLib('cellule_hauteur_nbr', __('hauteur'));
        $form->setLib('cellule_fondcouleur_nbr', __('fond'));
        $form->setLib('cellule_bordure_nbr', __('bordure'));
        $form->setLib('cellule_align_nbr', __('align'));
        // operations
        $form->setLib('cellule_numerique', __('numerique'));
        $form->setLib('cellule_total', __('total'));
        $form->setLib('cellule_moyenne', __('moyenne'));
        $form->setLib('cellule_compteur', __('nombre'));

        $form->setLib('om_sql', __('om_sql'));

    }

    /**
     *
     */
    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        parent::setVal($form, $maj, $validation);
        //
        if ($validation == 0) {
            if ($maj == 0) {
                $form->setVal('titre', __('Texte du titre'));
                $form->setVal('titrefont','helvetica');
                $form->setVal('titrehauteur',10);
                $form->setVal('titrefond',0);
                $form->setVal('titreattribut','B');
                $form->setVal('titretaille',12);
                $form->setVal('titrebordure',0);
                $form->setVal('titrealign','L');

                $form->setVal('titrefondcouleur','243-246-246');
                $form->setVal('titretextecouleur','0-0-0');

                $form->setVal('intervalle_debut',10);
                $form->setVal('intervalle_fin',15);

                $form->setVal('entete_flag',1);
                $form->setVal('entete_fond',1);
                $form->setVal('entete_orientation',"0|0|0");
                $form->setVal('entete_hauteur',20);
                $form->setVal('entetecolone_bordure',"TLB|LTB|LTBR");
                $form->setVal('entetecolone_align',"C|C|C");
                $form->setVal('entete_fondcouleur','195-224-169');
                $form->setVal('entete_textecouleur','0-0-0');

                $form->setVal('tableau_largeur',195);
                $form->setVal('tableau_bordure',1);
                $form->setVal('tableau_fontaille',10);

                $form->setVal('bordure_couleur','0-0-0');
                $form->setVal('se_fond1','243-243-246');
                $form->setVal('se_fond2','255-255-255');

                $form->setVal('cellule_fond',1);
                $form->setVal('cellule_hauteur',10);
                $form->setVal('cellule_largeur',"65|65|65");
                $form->setVal('cellule_bordure_un',"LTBR|LTBR|LTBR");
                $form->setVal('cellule_bordure',"LTBR|LTBR|LTBR");
                $form->setVal('cellule_align',"L|L|C");

                $form->setVal('cellule_fond_total',1);
                $form->setVal('cellule_fontaille_total',10);
                $form->setVal('cellule_hauteur_total',15);
                $form->setVal('cellule_fondcouleur_total',"196-213-215");
                $form->setVal('cellule_bordure_total',"TBL|TBL|LTBR");
                $form->setVal('cellule_align_total',"L|L|C");

                $form->setVal('cellule_fond_moyenne',1);
                $form->setVal('cellule_fontaille_moyenne',10);
                $form->setVal('cellule_hauteur_moyenne',15);
                $form->setVal('cellule_fondcouleur_moyenne',"196-213-215");
                $form->setVal('cellule_bordure_moyenne',"TBL|TBL|LTBR");
                $form->setVal('cellule_align_moyenne',"L|L|C");

                $form->setVal('cellule_fond_nbr',1);
                $form->setVal('cellule_fontaille_nbr',10);
                $form->setVal('cellule_hauteur_nbr',15);
                $form->setVal('cellule_fondcouleur_nbr',"196-213-215");
                $form->setVal('cellule_bordure_nbr',"TBL|TBL|LTBR");
                $form->setVal('cellule_align_nbr',"L|L|C");

                $form->setVal('cellule_numerique',"999|999|999");
                $form->setVal('cellule_total',"0|0|0");
                $form->setVal('cellule_moyenne',"0|0|0");
                $form->setVal('cellule_compteur',"0|0|1");

                $form->setVal('om_sql',"select ... \nfrom ... \nwhere ... = &idx");
            }
        }
    }

    /**
     *
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        parent::setValsousformulaire($form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire);
        //
        if ($validation==0) {
          if ($maj == 0){
            $form->setVal('titre', __('Texte du titre'));
            $form->setVal('titrefont','helvetica');
            $form->setVal('titrehauteur',10);
            $form->setVal('titrefond',0);
            $form->setVal('titreattribut','B');
            $form->setVal('titretaille',12);
            $form->setVal('titrebordure',0);
            $form->setVal('titrealign','L');

            $form->setVal('titrefondcouleur','243-246-246');
            $form->setVal('titretextecouleur','0-0-0');

            $form->setVal('intervalle_debut',10);
            $form->setVal('intervalle_fin',15);

            $form->setVal('entete_flag',1);
            $form->setVal('entete_fond',1);
            $form->setVal('entete_orientation',"0|0|0");
            $form->setVal('entete_hauteur',20);
            $form->setVal('entetecolone_bordure',"TLB|LTB|LTBR");
            $form->setVal('entetecolone_align',"C|C|C");
            $form->setVal('entete_fondcouleur','195-224-169');
            $form->setVal('entete_textecouleur','0-0-0');

            $form->setVal('tableau_largeur',195);
            $form->setVal('tableau_bordure',1);
            $form->setVal('tableau_fontaille',10);

            $form->setVal('bordure_couleur','0-0-0');
            $form->setVal('se_fond1','243-243-246');
            $form->setVal('se_fond2','255-255-255');

            $form->setVal('cellule_fond',1);
            $form->setVal('cellule_hauteur',10);
            $form->setVal('cellule_largeur',"65|65|65");
            $form->setVal('cellule_bordure_un',"LTBR|LTBR|LTBR");
            $form->setVal('cellule_bordure',"LTBR|LTBR|LTBR");
            $form->setVal('cellule_align',"L|L|C");

            $form->setVal('cellule_fond_total',1);
            $form->setVal('cellule_fontaille_total',10);
            $form->setVal('cellule_hauteur_total',15);
            $form->setVal('cellule_fondcouleur_total',"196-213-215");
            $form->setVal('cellule_bordure_total',"TBL|TBL|LTBR");
            $form->setVal('cellule_align_total',"L|L|C");

            $form->setVal('cellule_fond_moyenne',1);
            $form->setVal('cellule_fontaille_moyenne',10);
            $form->setVal('cellule_hauteur_moyenne',15);
            $form->setVal('cellule_fondcouleur_moyenne',"196-213-215");
            $form->setVal('cellule_bordure_moyenne',"TBL|TBL|LTBR");
            $form->setVal('cellule_align_moyenne',"L|L|C");

            $form->setVal('cellule_fond_nbr',1);
            $form->setVal('cellule_fontaille_nbr',10);
            $form->setVal('cellule_hauteur_nbr',15);
            $form->setVal('cellule_fondcouleur_nbr',"196-213-215");
            $form->setVal('cellule_bordure_nbr',"TBL|TBL|LTBR");
            $form->setVal('cellule_align_nbr',"L|L|C");

            $form->setVal('cellule_numerique',"999|999|999");
            $form->setVal('cellule_total',"0|0|0");
            $form->setVal('cellule_moyenne',"0|0|0");
            $form->setVal('cellule_compteur',"0|0|1");

            $form->setVal('om_sql',"select ... \nfrom ... \nwhere ... = &idx");
            $form->setVal($retourformulaire, $idxformulaire);
        }}
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
    }

    /**
     * verification sur existence d un etat deja actif pour la collectivite
     */
    function verifieractif($id, $val) {
        //
        $table = "om_sousetat";
        $primary_key = "om_sousetat";
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
            $msg .= __("sous-etat(s) existant(s) dans l'etat actif. Il ".
                      "n'est pas possible d'avoir plus d'un sous-etat");
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
     * VIEW - view_json_list.
     *
     * @return void
     */
    function view_json_list() {
        //
        $this->checkAccessibility();
        $this->f->disableLog();
        //
        $sql = "
        SELECT
            id,
            libelle
        FROM
            ".DB_PREFIXE."om_sousetat
        WHERE
            actif IS TRUE
        ORDER BY
            om_sousetat.libelle
        ";
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Gestion d'une éventuelle erreur de base de données
        if ($this->f->isDatabaseError(true)) {
            // On retourne un tableau vide
            return json_encode(array());
        }
        //
        $tab = array();
        while ($row = $res->fetchrow(DB_FETCHMODE_ASSOC)) {
            $tab[$row['id']] = $row['libelle'];
        }
        //
        echo json_encode($tab);
    }
}
