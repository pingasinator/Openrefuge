<?php
//$Id$ 
//gen openMairie le 16/03/2026 15:55

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class animal_espece_gen extends dbform {

    protected $_absolute_class_name = "animal_espece";

    var $table = "animal_espece";
    var $clePrimaire = "animal_espece";
    var $typeCle = "N";
    var $required_field = array(
        "animal_espece"
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
            "animal_espece",
            "nom",
        );
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['animal_espece'])) {
            $this->valF['animal_espece'] = ""; // -> requis
        } else {
            $this->valF['animal_espece'] = $val['animal_espece'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
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
            $form->setType("animal_espece", "hidden");
            $form->setType("nom", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("animal_espece", "hiddenstatic");
            $form->setType("nom", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("animal_espece", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("animal_espece", "static");
            $form->setType("nom", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('animal_espece','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("animal_espece", 11);
        $form->setTaille("nom", 10);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("animal_espece", 11);
        $form->setMax("nom", -5);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('animal_espece', __('animal_espece'));
        $form->setLib('nom', __('nom'));
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
        // Verification de la cle secondaire : animal
        $this->rechercheTable($this->f->db, "animal", "animal_espece", $id);
        // Verification de la cle secondaire : animal_race
        $this->rechercheTable($this->f->db, "animal_race", "animal_espece", $id);
    }


}
