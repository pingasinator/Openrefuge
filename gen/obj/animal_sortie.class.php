<?php
//$Id$ 
//gen openMairie le 23/04/2026 10:41

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class animal_sortie_gen extends dbform {

    protected $_absolute_class_name = "animal_sortie";

    var $table = "animal_sortie";
    var $clePrimaire = "animal_sortie";
    var $typeCle = "N";
    var $required_field = array(
        "animal",
        "animal_sortie"
    );
    
    var $foreign_keys_extended = array(
        "animal" => array("animal", ),
        "cause_mort" => array("cause_mort", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("date_sortie");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "animal_sortie",
            "date_sortie",
            "animal",
            "cause_mort",
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

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_cause_mort() {
        return "SELECT cause_mort.cause_mort, cause_mort.libelle FROM ".DB_PREFIXE."cause_mort ORDER BY cause_mort.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_cause_mort_by_id() {
        return "SELECT cause_mort.cause_mort, cause_mort.libelle FROM ".DB_PREFIXE."cause_mort WHERE cause_mort = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['animal_sortie'])) {
            $this->valF['animal_sortie'] = ""; // -> requis
        } else {
            $this->valF['animal_sortie'] = $val['animal_sortie'];
        }
        if ($val['date_sortie'] != "") {
            $this->valF['date_sortie'] = $this->dateDB($val['date_sortie']);
        } else {
            $this->valF['date_sortie'] = NULL;
        }
        if (!is_numeric($val['animal'])) {
            $this->valF['animal'] = ""; // -> requis
        } else {
            $this->valF['animal'] = $val['animal'];
        }
        if (!is_numeric($val['cause_mort'])) {
            $this->valF['cause_mort'] = NULL;
        } else {
            $this->valF['cause_mort'] = $val['cause_mort'];
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
            $form->setType("animal_sortie", "hidden");
            $form->setType("date_sortie", "date");
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            if ($this->is_in_context_of_foreign_key("cause_mort", $this->retourformulaire)) {
                $form->setType("cause_mort", "selecthiddenstatic");
            } else {
                $form->setType("cause_mort", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("animal_sortie", "hiddenstatic");
            $form->setType("date_sortie", "date");
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            if ($this->is_in_context_of_foreign_key("cause_mort", $this->retourformulaire)) {
                $form->setType("cause_mort", "selecthiddenstatic");
            } else {
                $form->setType("cause_mort", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("animal_sortie", "hiddenstatic");
            $form->setType("date_sortie", "hiddenstatic");
            $form->setType("animal", "selectstatic");
            $form->setType("cause_mort", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("animal_sortie", "static");
            $form->setType("date_sortie", "datestatic");
            $form->setType("animal", "selectstatic");
            $form->setType("cause_mort", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('animal_sortie','VerifNum(this)');
        $form->setOnchange('date_sortie','fdate(this)');
        $form->setOnchange('animal','VerifNum(this)');
        $form->setOnchange('cause_mort','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("animal_sortie", 11);
        $form->setTaille("date_sortie", 12);
        $form->setTaille("animal", 11);
        $form->setTaille("cause_mort", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("animal_sortie", 11);
        $form->setMax("date_sortie", 12);
        $form->setMax("animal", 11);
        $form->setMax("cause_mort", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('animal_sortie', __('animal_sortie'));
        $form->setLib('date_sortie', __('date_sortie'));
        $form->setLib('animal', __('animal'));
        $form->setLib('cause_mort', __('cause_mort'));
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
        // cause_mort
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "cause_mort",
            $this->get_var_sql_forminc__sql("cause_mort"),
            $this->get_var_sql_forminc__sql("cause_mort_by_id"),
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
            if($this->is_in_context_of_foreign_key('cause_mort', $this->retourformulaire))
                $form->setVal('cause_mort', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
