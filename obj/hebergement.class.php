<?php
//$Id$ 
//gen openMairie le 04/12/2025 10:31

require_once "../gen/obj/hebergement.class.php";

class hebergement extends hebergement_gen {

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

    function view_pdf_edition_etat() {
        $this->checkAccessibility();
        $pdfedition = $this->compute_pdf_output(
        'etat', //etat ou lettretype
        'hebergement', //objet de mon etat
        $this->f->getCollectivite(),
        $this->id
        );
        $this->expose_pdf_output(
        $pdfedition["pdf_output"],
        "formulaire_hébergement-".date('YmdHis').".pdf"
        );
    }

}
