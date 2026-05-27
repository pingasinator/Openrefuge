<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_widget.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_widget.class.php")) {
    require_once "../gen/obj/om_widget.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_widget.class.php";
}

/**
 *
 */
class om_widget_core extends om_widget_gen {

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
            "om_widget",
            "libelle",
            "type",
            "lien",
            "texte",
            "script",
            "arguments",
        );
    }

    /**
     *
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);
        //
        if ($maj == 0 || $maj == 1) {
            $form->setType('type', 'select');
            $form->setType('script', 'select');
        }
        if ($maj == 2) {
            $form->setType('type', 'selectstatic');
            $form->setType('script', 'selectstatic');
        }
        if ($maj == 3) {
            $form->setType('type', 'selectstatic');
            $form->setType('script', 'selectstatic');
            //Cache les deux champs que le type n'utilise pas
            if ($this->getVal('type')=='web'){
                $form->setType('script', 'hidden');
                $form->setType('arguments', 'hidden');
            }
            elseif ($this->getVal('type')=='file'){
                $form->setType('lien', 'hidden');
                $form->setType('texte', 'hidden');
            }
        }
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);
        // SELECT pour le champs 'type'
        $select = array(
            0 => array(
                "web",
                "file",
            ),
            1 => array(
                __("web - le contenu du widget provient du champs texte ci-dessous"),
                __("file - le contenu du widget provient d'un script sur le serveur"),
            ),
        );
        $form->setSelect('type', $select);
        // SELECT pour le champs 'script'
        // On récupère la liste des scripts correspondant au masque
        // app/widget_<NOM_DU_SCRIPT>.php
        $widget = preg_replace(
            '/\.\.\/app\/widget_(.*)\.php/',
            '$1',
            glob('../app/widget_*.php')
        );
        $select = array(
            0 => array_merge(array('', ), $widget),
            1 => array_merge(array(__('choisir')."&nbsp;".__('script'), ), $widget),
        );
        $form->setSelect('script', $select);
    }

    /**
     *
     */
    function setLib(&$form, $maj) {
        //
        parent::setLib($form, $maj);
        //
        if ($this->getVal("type") == "file") {
            //
            $form->setLib("script", __('script').' '.$form->required_tag);
            $form->setLib("texte", __("arguments"));
        }
    }

    /**
     *
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        parent::verifier($val);
        //
        if ($val["type"] == "file"
            && !file_exists("../app/widget_".$val["script"].".php")) {
            //
            $this->correct = false;
            $this->addToMessage(__("Le script n'existe pas."));
        }
    }

}
