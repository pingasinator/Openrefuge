<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_requete.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_requete.class.php")) {
    require_once "../gen/obj/om_requete.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_requete.class.php";
}

/**
 *
 */
class om_requete_core extends om_requete_gen {

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

        // ACTION - 004 - labels-merge-fields
        //
        $this->class_actions[4] = array(
            "identifier" => "labels-merge-fields",
            "view" => "view_labels_merge_fields",
            "permission_suffix" => "consulter",
        );
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_requete",
            "code",
            "libelle",
            "description",
            "type",
            "requete",
            "merge_fields",
            "classe",
            "methode",
        );
    }

    /**
     * Permet de définir le type des champs
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);

        // En modes "ajout" et "modification"
        if ($maj == 0 || $maj == 1) {

            $form->setType('type', 'select');
        }

        // En mode "consultation" et "suppression"
        if ($maj == 2 || $maj == 3) {

            $form->setType('type', 'selectstatic');
        }
        // En mode "consultation"
        if ($maj == 3) {
            if ($this->getVal('type')=='sql'){
                $form->setType('classe', 'hidden');
                $form->setType('methode', 'hidden');
            }
            if ($this->getVal('type')=='objet'){
                $form->setType('requete', 'hidden');
                $form->setType('merge_fields', 'hidden');
            }
        }
    }

    /**
     * Permet de construire le contenu d'un select
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);

        $type = array();
        $type[0][0] = 'sql';
        $type[1][0] = __("SQL");
        $type[0][1] = 'objet';
        $type[1][1] = __("objet");
        $form->setSelect("type", $type);
    }

    function setLayout(&$form, $maj){
        if ( $maj ==  0 OR $maj == 1 ) {
            $form->setBloc('requete','D',"","om_requete_sql_fields");
            $form->setBloc('merge_fields','F');
            $form->setBloc('classe','D',"","om_requete_object_fields");
            $form->setBloc('methode','F');
        }
    }

    /**
     *
     */
    function view_labels_merge_fields() {
        //
        $this->checkAccessibility();
        $this->f->disableLog();
        //
        if ($this->getVal("type") == "objet") {
            // récupération du(des) objet(s) et pour l'unique(premier)
            // son éventuelle méthode
            $methode = $this->getVal('methode');
            $classes = $this->getVal('classe');
            $classes = explode(';', $classes);
            $nb_classes = count($classes);
            $i = 0;
            $labels = array();
            for ($i = 0; $i < $nb_classes; $i++) {
                $classe = $classes[$i];
                $sql_object = $this->f->get_inst__om_dbform(array(
                    "obj" => $classe,
                    "idx" => 0,
                ));
                // si unique(premier) objet
                if ($i == 0) {
                    // si une méthode custom existe on récupère ses libellés
                    if ($methode != null && $methode != ''
                        && method_exists($sql_object, $methode)) {
                        $custom = $sql_object->$methode('labels');
                        $labels = array_merge($labels, $custom);
                    }
                    // on récupère également les libellés par défaut
                    $default = $sql_object->get_merge_fields('labels');
                    $labels = array_merge($labels, $default);
                } else { // sinon traitement des éventuels objet supplémentaires
                    // on ne récupère que les libellés par défaut
                    $default = $sql_object->get_merge_fields('labels');
                    $labels = array_merge($labels, $default);
                }
            }
            // Modification de l'aide à la saisie dans la base de données
            // si des libellés existent
            if (!empty($labels)) {
                $merge_fields = sprintf("<table><thead>");
                foreach ($labels as $object => $fields) {
                    // header : intitulé objet
                    $merge_fields .= sprintf('<tr>
                        <th colspan="2">%s</th></tr></thead><tbody>',
                        __("Enregistrement de type")." ".$object
                    );
                    // body : une ligne = un champ
                    foreach ($fields as $field => $label) {
                        $merge_fields .= sprintf("<tr><td>[%s]</td><td>%s</td></tr>",
                            $field, $label
                        );
                    }
                    // ligne séparatrice
                    $merge_fields .= sprintf('<tr style="%s"><td colspan="2"></td></tr>',
                        "height: 10px !important;");
                }
                $merge_fields .= sprintf("</tbody></table>");
                echo $merge_fields;
            }
        } elseif ($this->getVal("type") == "sql") {
            //
            echo $this->getVal("merge_fields");
        }
    }

}
