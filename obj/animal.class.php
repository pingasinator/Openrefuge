<?php
//$Id$ 
//gen openMairie le 13/11/2025 16:33

require_once "../gen/obj/animal.class.php";

class animal extends animal_gen {

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
        'animal', //objet de mon etat
        $this->f->getCollectivite(),
        $this->id
        );
        $this->expose_pdf_output(
        $pdfedition["pdf_output"],
        "formulaire_animal-".date('YmdHis').".pdf"
        );
    }

    function get_var_sql_forminc__champs() {
        return array(
            "animal",
            "num_identification",
            "nom",
            "date_naissance",
            "animal_espece",
            "animal_race",
            "animal_sexe",
            "personne",
            "description"
        );
    }
}