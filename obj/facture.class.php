<?php
//$Id$ 
//gen openMairie le 09/12/2025 16:33

require_once "../gen/obj/facture.class.php";

class facture extends facture_gen {

function setvalF($val = array()) {
    parent::setValF($val);
    $this->valF['date_creation'] = $this->dateDB(date("d-m-Y"));
}

    function get_var_sql_forminc__champs() {
        return array(
            "facture",
            "personne",
        );
    }

function init_class_actions(){

        parent::init_class_actions();
        $this->class_actions[1] = null;
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
        'facture', //objet de mon etat
        $this->f->getCollectivite(),
        $this->id
        );
        $this->expose_pdf_output(
        $pdfedition["pdf_output"],
        "facture".date('YmdHis').".pdf"
        );
    }

    // crée le numéro de facture
    function triggerajouter($id, &$dnu1 = null, $val = array(), $dnu2 = null)
    {
        $this->valF['numero_facture'] = "F-" . date("mY") . $this->valF[$this->clePrimaire];
    }

    // copie les données des séjours et soins impayés dans les tables facture_sejour et facture_soin après avoir créer la facture
    function triggerajouterapres($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        $res = $this->db->query("INSERT INTO openrefuge.facture_sejour (facture_sejour,facture,sejour,date_entree,date_sortie,paye,animal,provenance,hebergement,sejour_tarif) SELECT nextval('openrefuge.facture_sejour_seq'),".$id.",sejour.sejour,sejour.date_entree,sejour.date_sortie,sejour.paye,sejour.animal,sejour.provenance,sejour.hebergement,sejour.sejour_tarif FROM openrefuge.sejour RIGHT JOIN openrefuge.animal ON sejour.animal = animal.animal RIGHT JOIN openrefuge.personne ON personne.personne = animal.personne RIGHT JOIN openrefuge.facture ON personne.personne = facture.personne WHERE NOT sejour.paye AND facture.facture = " . $id);
        $res = $this->db->query("INSERT INTO openrefuge.facture_soin (facture_soin,facture,soin,date_soin,veterinaire,animal,clinique,tarif) SELECT nextval('openrefuge.facture_soin_seq'),".$id.",soin.soin,soin.date_soin,soin.veterinaire,soin.animal,soin.clinique,soin.tarif from openrefuge.soin right join openrefuge.animal on soin.animal = animal.animal right join openrefuge.personne on animal.personne = personne.personne right join openrefuge.facture on personne.personne = facture.personne where soin.soin is not null and facture.facture = ".$id);
    }

    // supprime les données des séjours et soins de la facture dans les tables facture_sejour et facture_soin avant de supprimer la facture
    function supprimer($val = array(), &$dnu1 = null, $dnu2 = null) 
    {
        $res = $this->db->query("DELETE FROM openrefuge.facture_sejour WHERE facture_sejour.facture = ". $val[$this->clePrimaire]);
        $res = $this->db->query("DELETE FROM openrefuge.facture_soin WHERE facture_soin.facture = ". $val[$this->clePrimaire]);
        return parent::supprimer($val,$dnu1,$dnu2);
    }


}
