<?php
//$Id$ 
//gen openMairie le 16/03/2026 09:58

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class ville_gen extends dbform {

    protected $_absolute_class_name = "ville";

    var $table = "ville";
    var $clePrimaire = "ville";
    var $typeCle = "N";
    var $required_field = array(
        "ville"
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
            "ville",
            "nom",
            "code_postal",
        );
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['ville'])) {
            $this->valF['ville'] = ""; // -> requis
        } else {
            $this->valF['ville'] = $val['ville'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
        }
        if ($val['code_postal'] == "") {
            $this->valF['code_postal'] = NULL;
        } else {
            $this->valF['code_postal'] = $val['code_postal'];
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
            $form->setType("ville", "hidden");
            $form->setType("nom", "text");
            $form->setType("code_postal", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("ville", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("code_postal", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("ville", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("code_postal", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("ville", "static");
            $form->setType("nom", "static");
            $form->setType("code_postal", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('ville','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("ville", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("code_postal", 10);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("ville", 11);
        $form->setMax("nom", -5);
        $form->setMax("code_postal", -5);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('ville', __('ville'));
        $form->setLib('nom', __('nom'));
        $form->setLib('code_postal', __('code_postal'));
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
        // Verification de la cle secondaire : clinique
        $this->rechercheTable($this->f->db, "clinique", "ville", $id);
        // Verification de la cle secondaire : hebergement
        $this->rechercheTable($this->f->db, "hebergement", "ville", $id);
        // Verification de la cle secondaire : personne
        $this->rechercheTable($this->f->db, "personne", "ville", $id);
    }


}
