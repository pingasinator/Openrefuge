<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_sig_extent_gen extends dbform {

    protected $_absolute_class_name = "om_sig_extent";

    var $table = "om_sig_extent";
    var $clePrimaire = "om_sig_extent";
    var $typeCle = "N";
    var $required_field = array(
        "om_sig_extent"
    );
    
    var $foreign_keys_extended = array(
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("nom");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_sig_extent",
            "nom",
            "extent",
            "valide",
        );
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_extent'])) {
            $this->valF['om_sig_extent'] = ""; // -> requis
        } else {
            $this->valF['om_sig_extent'] = $val['om_sig_extent'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
        }
        if ($val['extent'] == "") {
            $this->valF['extent'] = NULL;
        } else {
            $this->valF['extent'] = $val['extent'];
        }
        if ($val['valide'] == 1 || $val['valide'] == "t" || $val['valide'] == "Oui") {
            $this->valF['valide'] = true;
        } else {
            $this->valF['valide'] = false;
        }
    }

    //=================================================
    //cle primaire automatique [automatic primary key]
    //==================================================

    function setId(&$dnu1 = null) {
    //numero automatique
        $this->valF[$this->clePrimaire] = $this->f->db->nextId(DB_PREFIXE.$this->table);
    }

    function setValFAjout($val = array()) {
    //numero automatique -> pas de controle ajout cle primaire
    }

    function verifierAjout($val = array(), &$dnu1 = null) {
    //numero automatique -> pas de verfication de cle primaire
    }

    //==========================
    // Formulaire  [form]
    //==========================
    /**
     *
     */
    function setType(&$form, $maj) {
        // Récupération du mode de l'action
        $crud = $this->get_action_crud($maj);

        // MODE AJOUTER
        if ($maj == 0 || $crud == 'create') {
            $form->setType("om_sig_extent", "hidden");
            $form->setType("nom", "text");
            $form->setType("extent", "text");
            $form->setType("valide", "checkbox");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_sig_extent", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("extent", "text");
            $form->setType("valide", "checkbox");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_sig_extent", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("extent", "hiddenstatic");
            $form->setType("valide", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_sig_extent", "static");
            $form->setType("nom", "static");
            $form->setType("extent", "static");
            $form->setType("valide", "checkboxstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_extent','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_extent", 11);
        $form->setTaille("nom", 30);
        $form->setTaille("extent", 30);
        $form->setTaille("valide", 1);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_extent", 11);
        $form->setMax("nom", 150);
        $form->setMax("extent", 150);
        $form->setMax("valide", 1);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_extent', __('om_sig_extent'));
        $form->setLib('nom', __('nom'));
        $form->setLib('extent', __('extent'));
        $form->setLib('valide', __('valide'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    
    /**
     * Methode clesecondaire
     */
    function cleSecondaire($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        // On appelle la methode de la classe parent
        parent::cleSecondaire($id);
        // Verification de la cle secondaire : om_sig_map
        $this->rechercheTable($this->f->db, "om_sig_map", "om_sig_extent", $id);
    }


}
