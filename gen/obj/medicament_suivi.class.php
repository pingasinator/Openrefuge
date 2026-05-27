<?php
//$Id$ 
//gen openMairie le 05/05/2026 16:17

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class medicament_suivi_gen extends dbform {

    protected $_absolute_class_name = "medicament_suivi";

    var $table = "medicament_suivi";
    var $clePrimaire = "medicament_suivi";
    var $typeCle = "N";
    var $required_field = array(
        "animal",
        "date",
        "heure",
        "medicament",
        "medicament_suivi"
    );
    
    var $foreign_keys_extended = array(
        "animal" => array("animal", ),
        "medicament" => array("medicament", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("medicament");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "medicament_suivi",
            "medicament",
            "animal",
            "date",
            "heure",
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
    function get_var_sql_forminc__sql_medicament() {
        return "SELECT medicament.medicament, medicament.nom FROM ".DB_PREFIXE."medicament ORDER BY medicament.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_medicament_by_id() {
        return "SELECT medicament.medicament, medicament.nom FROM ".DB_PREFIXE."medicament WHERE medicament = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['medicament_suivi'])) {
            $this->valF['medicament_suivi'] = ""; // -> requis
        } else {
            $this->valF['medicament_suivi'] = $val['medicament_suivi'];
        }
        if (!is_numeric($val['medicament'])) {
            $this->valF['medicament'] = ""; // -> requis
        } else {
            $this->valF['medicament'] = $val['medicament'];
        }
        if (!is_numeric($val['animal'])) {
            $this->valF['animal'] = ""; // -> requis
        } else {
            $this->valF['animal'] = $val['animal'];
        }
        if ($val['date'] != "") {
            $this->valF['date'] = $this->dateDB($val['date']);
        }
            $this->valF['heure'] = $val['heure'];
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
            $form->setType("medicament_suivi", "hidden");
            if ($this->is_in_context_of_foreign_key("medicament", $this->retourformulaire)) {
                $form->setType("medicament", "selecthiddenstatic");
            } else {
                $form->setType("medicament", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            $form->setType("date", "date");
            $form->setType("heure", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("medicament_suivi", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("medicament", $this->retourformulaire)) {
                $form->setType("medicament", "selecthiddenstatic");
            } else {
                $form->setType("medicament", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal", $this->retourformulaire)) {
                $form->setType("animal", "selecthiddenstatic");
            } else {
                $form->setType("animal", "select");
            }
            $form->setType("date", "date");
            $form->setType("heure", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("medicament_suivi", "hiddenstatic");
            $form->setType("medicament", "selectstatic");
            $form->setType("animal", "selectstatic");
            $form->setType("date", "hiddenstatic");
            $form->setType("heure", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("medicament_suivi", "static");
            $form->setType("medicament", "selectstatic");
            $form->setType("animal", "selectstatic");
            $form->setType("date", "datestatic");
            $form->setType("heure", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('medicament_suivi','VerifNum(this)');
        $form->setOnchange('medicament','VerifNum(this)');
        $form->setOnchange('animal','VerifNum(this)');
        $form->setOnchange('date','fdate(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("medicament_suivi", 11);
        $form->setTaille("medicament", 11);
        $form->setTaille("animal", 11);
        $form->setTaille("date", 12);
        $form->setTaille("heure", 8);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("medicament_suivi", 11);
        $form->setMax("medicament", 11);
        $form->setMax("animal", 11);
        $form->setMax("date", 12);
        $form->setMax("heure", 8);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('medicament_suivi', __('medicament_suivi'));
        $form->setLib('medicament', __('medicament'));
        $form->setLib('animal', __('animal'));
        $form->setLib('date', __('date'));
        $form->setLib('heure', __('heure'));
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
        // medicament
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "medicament",
            $this->get_var_sql_forminc__sql("medicament"),
            $this->get_var_sql_forminc__sql("medicament_by_id"),
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
            if($this->is_in_context_of_foreign_key('medicament', $this->retourformulaire))
                $form->setVal('medicament', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
