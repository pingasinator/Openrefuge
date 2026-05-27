<?php
//$Id$ 
//gen openMairie le 17/11/2025 13:54

require_once "../gen/obj/personne.class.php";

class personne extends personne_gen {

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

    function get_var_sql_forminc__champs() {
        return array(
            "personne",
            "nom",
            "prenom",
            "civilite",
            "telephone",
            "telephone_sec",
            "mail",
            "num_rue",
            "rue",
            "ville"
        );
    }
    
    function view_pdf_edition_etat() {
        $this->checkAccessibility();
        $pdfedition = $this->compute_pdf_output(
        'etat', //etat ou lettretype
        'personne', //objet de mon etat
        $this->f->getCollectivite(),
        $this->id
        );
        $this->expose_pdf_output(
        $pdfedition["pdf_output"],
        "formulaire_personne-".date('YmdHis').".pdf"
        );
    }
}
