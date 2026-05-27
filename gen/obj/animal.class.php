<?php
//$Id$ 
//gen openMairie le 05/05/2026 16:15

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class animal_gen extends dbform {

    protected $_absolute_class_name = "animal";

    var $table = "animal";
    var $clePrimaire = "animal";
    var $typeCle = "N";
    var $required_field = array(
        "animal"
    );
    var $unique_key = array(
      "num_identification",
    );
    var $foreign_keys_extended = array(
        "animal_espece" => array("animal_espece", ),
        "animal_race" => array("animal_race", ),
        "animal_sexe" => array("animal_sexe", ),
        "personne" => array("personne", ),
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
            "animal",
            "nom",
            "date_naissance",
            "animal_espece",
            "animal_race",
            "animal_sexe",
            "personne",
            "num_identification",
            "description",
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

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_race() {
        return "SELECT animal_race.animal_race, animal_race.nom FROM ".DB_PREFIXE."animal_race ORDER BY animal_race.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_race_by_id() {
        return "SELECT animal_race.animal_race, animal_race.nom FROM ".DB_PREFIXE."animal_race WHERE animal_race = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_sexe() {
        return "SELECT animal_sexe.animal_sexe, animal_sexe.libelle FROM ".DB_PREFIXE."animal_sexe ORDER BY animal_sexe.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_animal_sexe_by_id() {
        return "SELECT animal_sexe.animal_sexe, animal_sexe.libelle FROM ".DB_PREFIXE."animal_sexe WHERE animal_sexe = <idx>";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_personne() {
        return "SELECT personne.personne, personne.nom FROM ".DB_PREFIXE."personne ORDER BY personne.nom ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_personne_by_id() {
        return "SELECT personne.personne, personne.nom FROM ".DB_PREFIXE."personne WHERE personne = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['animal'])) {
            $this->valF['animal'] = ""; // -> requis
        } else {
            $this->valF['animal'] = $val['animal'];
        }
        if ($val['nom'] == "") {
            $this->valF['nom'] = NULL;
        } else {
            $this->valF['nom'] = $val['nom'];
        }
        if ($val['date_naissance'] != "") {
            $this->valF['date_naissance'] = $this->dateDB($val['date_naissance']);
        } else {
            $this->valF['date_naissance'] = NULL;
        }
        if (!is_numeric($val['animal_espece'])) {
            $this->valF['animal_espece'] = NULL;
        } else {
            $this->valF['animal_espece'] = $val['animal_espece'];
        }
        if (!is_numeric($val['animal_race'])) {
            $this->valF['animal_race'] = NULL;
        } else {
            $this->valF['animal_race'] = $val['animal_race'];
        }
        if (!is_numeric($val['animal_sexe'])) {
            $this->valF['animal_sexe'] = NULL;
        } else {
            $this->valF['animal_sexe'] = $val['animal_sexe'];
        }
        if (!is_numeric($val['personne'])) {
            $this->valF['personne'] = NULL;
        } else {
            $this->valF['personne'] = $val['personne'];
        }
        if ($val['num_identification'] == "") {
            $this->valF['num_identification'] = NULL;
        } else {
            $this->valF['num_identification'] = $val['num_identification'];
        }
            $this->valF['description'] = $val['description'];
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
            $form->setType("animal", "hidden");
            $form->setType("nom", "text");
            $form->setType("date_naissance", "date");
            if ($this->is_in_context_of_foreign_key("animal_espece", $this->retourformulaire)) {
                $form->setType("animal_espece", "selecthiddenstatic");
            } else {
                $form->setType("animal_espece", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal_race", $this->retourformulaire)) {
                $form->setType("animal_race", "selecthiddenstatic");
            } else {
                $form->setType("animal_race", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal_sexe", $this->retourformulaire)) {
                $form->setType("animal_sexe", "selecthiddenstatic");
            } else {
                $form->setType("animal_sexe", "select");
            }
            if ($this->is_in_context_of_foreign_key("personne", $this->retourformulaire)) {
                $form->setType("personne", "selecthiddenstatic");
            } else {
                $form->setType("personne", "select");
            }
            $form->setType("num_identification", "text");
            $form->setType("description", "textarea");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("animal", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("date_naissance", "date");
            if ($this->is_in_context_of_foreign_key("animal_espece", $this->retourformulaire)) {
                $form->setType("animal_espece", "selecthiddenstatic");
            } else {
                $form->setType("animal_espece", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal_race", $this->retourformulaire)) {
                $form->setType("animal_race", "selecthiddenstatic");
            } else {
                $form->setType("animal_race", "select");
            }
            if ($this->is_in_context_of_foreign_key("animal_sexe", $this->retourformulaire)) {
                $form->setType("animal_sexe", "selecthiddenstatic");
            } else {
                $form->setType("animal_sexe", "select");
            }
            if ($this->is_in_context_of_foreign_key("personne", $this->retourformulaire)) {
                $form->setType("personne", "selecthiddenstatic");
            } else {
                $form->setType("personne", "select");
            }
            $form->setType("num_identification", "text");
            $form->setType("description", "textarea");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("animal", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("date_naissance", "hiddenstatic");
            $form->setType("animal_espece", "selectstatic");
            $form->setType("animal_race", "selectstatic");
            $form->setType("animal_sexe", "selectstatic");
            $form->setType("personne", "selectstatic");
            $form->setType("num_identification", "hiddenstatic");
            $form->setType("description", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("animal", "static");
            $form->setType("nom", "static");
            $form->setType("date_naissance", "datestatic");
            $form->setType("animal_espece", "selectstatic");
            $form->setType("animal_race", "selectstatic");
            $form->setType("animal_sexe", "selectstatic");
            $form->setType("personne", "selectstatic");
            $form->setType("num_identification", "static");
            $form->setType("description", "textareastatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('animal','VerifNum(this)');
        $form->setOnchange('date_naissance','fdate(this)');
        $form->setOnchange('animal_espece','VerifNum(this)');
        $form->setOnchange('animal_race','VerifNum(this)');
        $form->setOnchange('animal_sexe','VerifNum(this)');
        $form->setOnchange('personne','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("animal", 11);
        $form->setTaille("nom", 10);
        $form->setTaille("date_naissance", 12);
        $form->setTaille("animal_espece", 11);
        $form->setTaille("animal_race", 11);
        $form->setTaille("animal_sexe", 11);
        $form->setTaille("personne", 11);
        $form->setTaille("num_identification", 10);
        $form->setTaille("description", 80);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("animal", 11);
        $form->setMax("nom", -5);
        $form->setMax("date_naissance", 12);
        $form->setMax("animal_espece", 11);
        $form->setMax("animal_race", 11);
        $form->setMax("animal_sexe", 11);
        $form->setMax("personne", 11);
        $form->setMax("num_identification", -5);
        $form->setMax("description", 6);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('animal', __('animal'));
        $form->setLib('nom', __('nom'));
        $form->setLib('date_naissance', __('date_naissance'));
        $form->setLib('animal_espece', __('animal_espece'));
        $form->setLib('animal_race', __('animal_race'));
        $form->setLib('animal_sexe', __('animal_sexe'));
        $form->setLib('personne', __('personne'));
        $form->setLib('num_identification', __('num_identification'));
        $form->setLib('description', __('description'));
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
        // animal_race
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "animal_race",
            $this->get_var_sql_forminc__sql("animal_race"),
            $this->get_var_sql_forminc__sql("animal_race_by_id"),
            false
        );
        // animal_sexe
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "animal_sexe",
            $this->get_var_sql_forminc__sql("animal_sexe"),
            $this->get_var_sql_forminc__sql("animal_sexe_by_id"),
            false
        );
        // personne
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "personne",
            $this->get_var_sql_forminc__sql("personne"),
            $this->get_var_sql_forminc__sql("personne_by_id"),
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
            if($this->is_in_context_of_foreign_key('animal_race', $this->retourformulaire))
                $form->setVal('animal_race', $idxformulaire);
            if($this->is_in_context_of_foreign_key('animal_sexe', $this->retourformulaire))
                $form->setVal('animal_sexe', $idxformulaire);
            if($this->is_in_context_of_foreign_key('personne', $this->retourformulaire))
                $form->setVal('personne', $idxformulaire);
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
        // Verification de la cle secondaire : animal_entree
        $this->rechercheTable($this->f->db, "animal_entree", "animal", $id);
        // Verification de la cle secondaire : animal_sortie
        $this->rechercheTable($this->f->db, "animal_sortie", "animal", $id);
        // Verification de la cle secondaire : medicament
        $this->rechercheTable($this->f->db, "medicament", "animal", $id);
        // Verification de la cle secondaire : medicament_suivi
        $this->rechercheTable($this->f->db, "medicament_suivi", "animal", $id);
        // Verification de la cle secondaire : soin
        $this->rechercheTable($this->f->db, "soin", "animal", $id);
    }


}
