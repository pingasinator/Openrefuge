<?php
//$Id$ 
//gen openMairie le 16/03/2026 15:55

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class animal_race_gen extends dbform {

    protected $_absolute_class_name = "animal_race";

    var $table = "animal_race";
    var $clePrimaire = "animal_race";
    var $typeCle = "N";
    var $required_field = array(
        "animal_race"
    );
    
    var $foreign_keys_extended = array(
        "animal_espece" => array("animal_espece", ),
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
            "animal_race",
            "nom",
            "animal_espece",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_espece() {
        return "SELECT animal_espece.animal_espece, animal_espece.nom FROM ".DB_PREFIXE."animal_espece ORDER BY animal_espece.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_espece_by_id() {
        return "SELECT animal_espece.animal_espece, animal_espece.nom FROM ".DB_PREFIXE."animal_espece WHERE animal_espece = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['animal_race'])) {
            $this->valF['animal_race'] = ""; // -> requis
        } else {
            $this->valF['animal_race'] = $val['animal_race'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
        }
        if (!is_numeric($val['animal_espece'])) {
            $this->valF['animal_espece'] = NULL;
        } else {
            $this->valF['animal_espece'] = $val['animal_espece'];
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
            $form->setType("animal_race", "hidden");
            $form->setType("nom", "text");
            if ($this->is_in_context_of_foreign_key("animal_espece", $this->retourformulaire)) {
                $form->setType("animal_espece", "selecthiddenstatic");
            } else {
                $form->setType("animal_espece", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("animal_race", "hiddenstatic");
            $form->setType("nom", "text");
            if ($this->is_in_context_of_foreign_key("animal_espece", $this->retourformulaire)) {
                $form->setType("animal_espece", "selecthiddenstatic");
            } else {
                $form->setType("animal_espece", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("animal_race", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("animal_espece", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("animal_race", "static");
            $form->setType("nom", "static");
            $form->setType("animal_espece", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('animal_race','VerifNum(this)');
        $form->setOnchange('animal_espece','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("animal_race", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("animal_espece", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("animal_race", 11);
        $form->setMax("nom", -5);
        $form->setMax("animal_espece", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('animal_race', __('animal_race'));
        $form->setLib('nom', __('nom'));
        $form->setLib('animal_espece', __('animal_espece'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // animal_espece
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "animal_espece",
            $this->get_var_sql_forminc__sql("animal_espece"),
            $this->get_var_sql_forminc__sql("animal_espece_by_id"),
            false
        );
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('animal_espece', $this->retourformulaire))
                $form->setVal('animal_espece', $idxformulaire);
        }// fin validation
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
        $this->rechercheTable($this->f->db, "animal", "animal_race", $id);
    }


}
