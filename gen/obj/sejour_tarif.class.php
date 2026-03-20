<?php
//$Id$ 
//gen openMairie le 16/03/2026 10:11

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class sejour_tarif_gen extends dbform {

    protected $_absolute_class_name = "sejour_tarif";

    var $table = "sejour_tarif";
    var $clePrimaire = "sejour_tarif";
    var $typeCle = "N";
    var $required_field = array(
        "sejour_tarif"
    );
    
    var $foreign_keys_extended = array(
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("libelle");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "sejour_tarif",
            "libelle",
            "prix",
        );
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['sejour_tarif'])) {
            $this->valF['sejour_tarif'] = ""; // -> requis
        } else {
            $this->valF['sejour_tarif'] = $val['sejour_tarif'];
        }
        if ($val['libelle'] == "") {
            $this->valF['libelle'] = NULL;
        } else {
            $this->valF['libelle'] = $val['libelle'];
        }
        if (!is_numeric($val['prix'])) {
            $this->valF['prix'] = NULL;
        } else {
            $this->valF['prix'] = $val['prix'];
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
            $form->setType("sejour_tarif", "hidden");
            $form->setType("libelle", "text");
            $form->setType("prix", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("sejour_tarif", "hiddenstatic");
            $form->setType("libelle", "text");
            $form->setType("prix", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("sejour_tarif", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("prix", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("sejour_tarif", "static");
            $form->setType("libelle", "static");
            $form->setType("prix", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('sejour_tarif','VerifNum(this)');
        $form->setOnchange('prix','VerifFloat(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("sejour_tarif", 11);
        $form->setTaille("libelle", 10);
        $form->setTaille("prix", 20);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("sejour_tarif", 11);
        $form->setMax("libelle", -5);
        $form->setMax("prix", 20);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('sejour_tarif', __('sejour_tarif'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('prix', __('prix'));
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
        // Verification de la cle secondaire : facture_sejour
        $this->rechercheTable($this->f->db, "facture_sejour", "sejour_tarif", $id);
        // Verification de la cle secondaire : sejour
        $this->rechercheTable($this->f->db, "sejour", "sejour_tarif", $id);
    }


}
