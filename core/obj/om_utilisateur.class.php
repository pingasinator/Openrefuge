<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_utilisateur.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_utilisateur.class.php")) {
    require_once "../gen/obj/om_utilisateur.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_utilisateur.class.php";
}

/**
 *
 */
class om_utilisateur_core extends om_utilisateur_gen {

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

        // ACTION - 002 - supprimer
        //
        $this->class_actions[2]["condition"] = "delete_user_condition";

        // ACTION - 011 - synchroniser
        //
        $this->class_actions[11] = array(
            "identifier" => "synchroniser",
            "view" => "view_synchroniser",
            "permission_suffix" => "synchroniser",
            "condition" => array(
                "is_option_directory_enabled",
            ),
        );

    }

    /**
     *
     */
    function setvalF($val = array()) {
        //
        parent::setvalF($val);

        /* Gestion des mises a jour du mot de passe */

        // si un mot de passe est soumis par formulaire
        if ($val["pwd"] != '') {

            // si le mot de passe contient une valeur 'valide' (!= "*****")
            if ($val["pwd"] != "*****") {

                // calcul du md5 et mise a jour dans la base
                $this->valF["pwd"] = md5($val["pwd"]);

            // si le mot de passe n'a pas ete modifie, aucune maj dans la base
            } else {
                unset($this->valF["pwd"]);
            }
        }
    }

    /**
     *
     */
    function setType(&$form,$maj) {
        //
        parent::setType($form, $maj);
        // Gestion du type d'utilisateur (DB ou LDAP)
        $form->setType("om_type", "hidden");
        // Test du MODE
        if ($maj == 0) {
            // Modes : AJOUTER
            // Gestion du mot de passe
            $form->setType("pwd", "password");
        } elseif ($maj == 1) {
            // Modes : AJOUTER
            // Gestion du mot de passe
            $form->setType("pwd", "password");
            // Gestion du login
            $form->setType("login", "hiddenstatic");
        }
    }

    /**
     *
     */
    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        parent::setVal($form, $maj, $validation);
        //
        if ($validation == 0) {
            // Test du MODE
            if ($maj == 0) {
                // Mode : AJOUTER
                // Gestion du type d'utilisateur (DB ou LDAP)
                $form->setVal("om_type", "db");
            } else {
                // Modes : MODIFIER & SUPPRIMER
                // Gestion du mot de passe
                // Lié a setValF()
                $form->setVal('pwd', "*****");
            }
        }
    }

    /**
     *
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        parent::setValsousformulaire($form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire);
        //
        if ($validation == 0) {
            // Test du MODE
            if ($maj == 0) {
                // Mode : AJOUTER
                // Gestion du type d'utilisateur (DB ou LDAP)
                $form->setVal("om_type", "db");
            } else {
                // Modes : MODIFIER & SUPPRIMER
                // Gestion du mot de passe
                // Lié a setValF()
                $form->setVal("pwd", "*****");
            }
        }
    }

    /**
     * CONDITION - delete_user_condition.
     *
     * Méthode permettant de tester la condition d'affichage du bouton de
     * suppression de l'objet.
     *
     * @return boolean
     */
    function delete_user_condition() {
        // true si l'utilisateur connecté n'est pas celui à supprimer.
        if($_SESSION['login'] != $this->getVal("login")) {
            return true;
        }
        $this->addToMessage(__("Vous ne pouvez pas supprimer votre utilisateur."));
        return false;
    }

    /**
     * CONDITION - is_option_directory_enabled.
     *
     * Méthode permettant de tester si une configuration d'annuaire est
     * disponible.
     *
     * @return boolean
     */
    function is_option_directory_enabled() {
        //
        if ($this->f->is_option_directory_enabled() !== true) {
            return false;
        }
        //
        return true;
    }

    /**
     * Permet de modifier le fil d'Ariane depuis l'objet pour un formulaire
     * @param string    $ent    Fil d'Ariane récupéréré
     * @return                  Fil d'Ariane
     */
    function getFormTitle($ent) {
        //
        if ($this->getParameter("maj") == 11) {
            return $ent." -> ".__("synchronisation annuaire");
        }
        //
        return parent::getFormTitle($ent);
    }

    /**
     * VIEW - view_synchroniser.
     *
     * @return void
     */
    function view_synchroniser() {
        // Verification de l'accessibilité sur l'élément
        // Si l'utilisateur n'a pas accès à l'élément dans le contexte actuel
        // on arrête l'exécution du script
        $this->checkAccessibility();

        /**
         * Description de la page
         */
        $description = __("Cette page vous permet de synchroniser vos utilisateurs ".
                         "depuis un annuaire.");
        $this->f->displayDescription($description);

        // On recupere les mouvements a effectuer
        $results = $this->f->initSynchronization();
        //
        if ($results == NULL) {
            // XXX Identifier ce cas et logger un message ?
            return;
        }

        /**
         * TREATMENT
         */
        // Traitement si validation du formulaire
        if ($this->f->get_submitted_post_value("submit-directory") !== null) {
            //
            $this->f->synchronizeUsers($results);
            return;
        }

        /**
         * FORM
         */
        //
        $this->f->layout->display__form_container__begin(array(
            "action" => $this->getDataSubmit(),
            "name" => "f1",
        ));
        // Instanciation de l'objet formulaire
        $this->form = $this->f->get_inst__om_formulaire(array(
            "validation" => 0,
            "maj" => $this->getParameter("maj"),
            "champs" => array(),
        ));
        // Ouverture du conteneur de formulaire
        $this->form->entete();
        //
        echo __("Il y a")." ".count($results['userToAdd'])." ";
        echo __("utilisateur(s) present(s) dans l'annuaire et non present(s) dans la base");
        echo " => ".count($results['userToAdd'])." ".__("ajout(s)");
        //
        echo "<br/>";
        //
        echo __("Il y a")." ".count($results['userToDelete'])." ";
        echo __("utilisateur(s) present(s) dans la base et non present(s) dans l'annuaire");
        echo " => ".count($results['userToDelete'])." ".__("suppression(s)");
        //
        echo "<br/>";
        //
        echo __("Il y a")." ".count($results['userToUpdate'])." ";
        echo __("utilisateur(s) present(s) a la fois dans la base et l'annuaire");
        echo " => ".count($results['userToUpdate'])." ".__("mise(s) a jour");
        // Fermeture du conteneur de formulaire
        $this->form->enpied();
        //
        $this->f->layout->display__form_controls_container__begin(array(
            "controls" => "bottom",
        ));
        $this->f->layout->display__form_input_submit(array(
            "name" => "submit-directory",
            "value" => __("Synchroniser"),
            "class" => "boutonFormulaire",
        ));
        $this->f->layout->display__form_controls_container__end();
        //
        $this->f->layout->display__form_container__end();
    }


}
