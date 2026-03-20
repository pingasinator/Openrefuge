<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sig_map_comp.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_sig_map_comp.class.php")) {
    require_once "../gen/obj/om_sig_map_comp.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_sig_map_comp.class.php";
}

/**
 *
 */
class om_sig_map_comp_core extends om_sig_map_comp_gen {

    /**
     * On active les nouvelles actions sur cette classe.
     */
    var $activate_class_action = true;

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_sig_map_comp",
            "om_sig_map",
            "libelle",
            "obj_class",
            "ordre",
            "actif",
            "comp_maj",
            "comp_table_update",
            "comp_champ_idx",
            "comp_champ",
            "type_geometrie",
        );
    }

    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        if($maj<2){
            $form->setType('type_geometrie','select');
        }
        if ($maj == 2 or $maj == 3) {
            $form->setType('type_geometrie', 'selectstatic');
        }
    }

    /**
     *
     */
    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('libelle',50);
        $form->setTaille('ordre',3);
        $form->setTaille('comp_table_update',30);
        $form->setTaille('comp_champ_idx',30);
        $form->setTaille('comp_champ',30);
        $form->setTaille('type_geometrie',30);
        $form->setTaille('comp_maj',1);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj);
        $form->setMax('libelle',50);
        $form->setMax('comp_table_update',30);
        $form->setMax('comp_champ',30);
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);
        //
        //if(file_exists ("../dyn/var_sig.inc")) {
        //    include ("../dyn/var_sig.inc");
        //}
        $type_geometrie[0] = array("","point","linestring","polygon","multipoint","multilinestring","multipolygon");
        $type_geometrie[1] = array("choisir le type de géométrie",'point','ligne','polygone','multipoint','multiligne','multipolygone');

        $form->setSelect("type_geometrie", $type_geometrie);
    }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('libelle', __("Nom géométrie : "));
        $form->setLib('actif', __("Actif : "));
        $form->setLib('obj_class', __("Objet : "));
        $form->setLib('ordre', __("Ordre d'affichage : "));
        $form->setLib('comp_maj', __("Mis a jour : "));
        $form->setLib('type_geometrie', __("Type de géometrie : "));
        $form->setLib('comp_table_update', __("Table :"));
        $form->setLib('comp_champ_idx', __("Champ idx :"));
        $form->setLib('comp_champ', __("Champ géographique :"));
    }

    /**
     *
     */
    function setGroupe (&$form, $maj) {
        $form->setGroupe('comp_table_update','D');
        $form->setGroupe('comp_champ','F');
    }

    /**
     *
     */
    function setRegroupe (&$form, $maj) {
        $form->setRegroupe('comp_maj','D',' '.__('Mise a jour').' ', "collapsible");
        $form->setRegroupe('comp_table_update','G','');
        $form->setRegroupe('comp_champ','G','');
        $form->setRegroupe('type_geometrie','F','');
    }

}
