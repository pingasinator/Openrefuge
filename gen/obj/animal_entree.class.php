<?php
//$Id$ 
//gen openMairie le 30/04/2026 14:27

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class animal_entree_gen extends dbform {

    protected $_absolute_class_name = "animal_entree";

    var $table = "animal_entree";
    var $clePrimaire = "animal_entree";
    var $typeCle = "N";
    var $required_field = array(
        "animal",
        "animal_entree"
    );
    
    var $foreign_keys_extended = array(
        "animal" => array("animal", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("date_entree");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "animal_entree",
            "date_entree",
            "animal",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal() {
        return "SELECT animal.animal, animal.nom FROM ".DB_PREFIXE."animal ORDER BY animal.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_by_id() {
        return "SELECT animal.animal, animal.nom FROM ".DB_PREFIXE."animal WHERE animal = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['animal_entree'])) {
            $this->valF['animal_entree'] = ""; // -> requis
        } else {
            $this->valF['animal_entree'] = $val['animal_entree'];
        }
        if ($val['date_entree'] != "") {
            $this->valF['date_entree'] = $this->dateDB($val['date_entree']);
        } else {
            $this->valF['date_entree'] = NULL;
        }
        if (!is_numeric($val['animal'])) {
            $this->valF['animal'] = ""; // -> requis
        } else {
            $this->valF['animal'] = $val['animal'];
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
            $form->setType("animal_entree", "hidden");
            $form->setType("date_entree", "date");
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("animal_entree", "hiddenstatic");
            $form->setType("date_entree", "date");
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("animal_entree", "hiddenstatic");
            $form->setType("date_entree", "hiddenstatic");
            $form->setType("animal", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("animal_entree", "static");
            $form->setType("date_entree", "datestatic");
            $form->setType("animal", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('animal_entree','VerifNum(this)');
        $form->setOnchange('date_entree','fdate(this)');
        $form->setOnchange('animal','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("animal_entree", 11);
        $form->setTaille("date_entree", 12);
        $form->setTaille("animal", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("animal_entree", 11);
        $form->setMax("date_entree", 12);
        $form->setMax("animal", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('animal_entree', __('animal_entree'));
        $form->setLib('date_entree', __('date_entree'));
        $form->setLib('animal', __('animal'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // animal
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "animal",
            $this->get_var_sql_forminc__sql("animal"),
            $this->get_var_sql_forminc__sql("animal_by_id"),
            false
        );
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('animal', $this->retourformulaire))
                $form->setVal('animal', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
