<?php
//$Id$ 
//gen openMairie le 18/12/2025 09:41

require_once "../gen/obj/factures_sejours.class.php";


class factures_sejours extends factures_sejours_gen {

    function init_class_actions(){
        parent::init_class_actions();
        $this->class_actions[102] = array(
            "identifier" => "pdf-edition",
            "portlet" => array(
                "type" => "action-blank",
                "libelle" => __("récapitulatif"),
                "description" => __('télécharger le récapitulatif au format pdf'),
                "class" => "pdf-16",
                "order" => 30,
            ),
        //"permission_suffix" => "edition",
        "view" => "view_pdf_edition_etat",
        "condition" => array()
        );
    }
}
