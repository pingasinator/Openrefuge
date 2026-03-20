<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_collectivite.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_collectivite.class.php")) {
    require_once "../gen/obj/om_collectivite.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_collectivite.class.php";
}

/**
 *
 */
class om_collectivite_core extends om_collectivite_gen {

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
        $this->class_actions[2]["condition"] = "delete_coll_condition";

    }

    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);

        if ($maj < 2) {
             $form->setType('niveau', 'select');
        }

        if ($maj == 2 or $maj == 3) {
            $form->setType('niveau', 'selectstatic');
        }
    }

    /**
     * Methode verifier
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        parent::verifier($val);
        // On verifie si il y a une autre collectivite multi
        if ($this->valF['niveau'] == 2) {
            if ($this->getParameter("maj") == 0) {
                $this->verifierniveau(']');
            } else {
                $this->verifierniveau($val['om_collectivite']);
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
        $contenu[0] = array(1, 2);
        $contenu[1] = array(__('mono'), __('multi'));
        $form->setSelect('niveau', $contenu);

    }

    /**
     * verification sur existence d une collectivite de niveau 2
     */
    function verifierniveau($id) {
        //
        $sql = "select * from ".DB_PREFIXE."om_collectivite where niveau = '2'";
        if($id!=']')
            $sql.=" and om_collectivite !='".$id."'";
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        $nbligne=$res->numrows();
        if ($nbligne>0){
           $this->msg= $this->msg." ".$nbligne." ".__("collectivite")." ".__("existant").
           " ".__("niveau")." 2 ! ".__("vous ne pouvez avoir qu une collectivite")." ".
           __("de")."  ".__("niveau")." multi ";
           $this->correct=False;
        }
    }

    /**
     * CONDITION - delete_coll_condition.
     *
     * Méthode permettant de tester la condition d'affichage du bouton de
     * suppression de l'objet.
     *
     * @return boolean
     */
    function delete_coll_condition() {
        //  true si la collectivité de l'utilisateur connecté est multi.
        if($_SESSION['niveau'] == 2) {
            return true;
        }
        $this->addToMessage(__("Vous ne pouvez pas supprimer de collectivite."));
        return false;
    }

}
