<?php
//$Id$ 
//gen openMairie le 16/03/2026 15:55

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class facture_gen extends dbform {

    protected $_absolute_class_name = "facture";

    var $table = "facture";
    var $clePrimaire = "facture";
    var $typeCle = "N";
    var $required_field = array(
        "facture",
        "personne"
    );
    
    var $foreign_keys_extended = array(
        "personne" => array("personne", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("personne");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "facture",
            "personne",
            "date_creation",
            "numero_facture",
            "etat",
        );
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
        if (!is_numeric($val['facture'])) {
            $this->valF['facture'] = ""; // -> requis
        } else {
            $this->valF['facture'] = $val['facture'];
        }
        if (!is_numeric($val['personne'])) {
            $this->valF['personne'] = ""; // -> requis
        } else {
            $this->valF['personne'] = $val['personne'];
        }
        if ($val['date_creation'] != "") {
            $this->valF['date_creation'] = $this->dateDB($val['date_creation']);
        } else {
            $this->valF['date_creation'] = NULL;
        }
        if ($val['numero_facture'] == "") {
            $this->valF['numero_facture'] = NULL;
        } else {
            $this->valF['numero_facture'] = $val['numero_facture'];
        }
        if ($val['etat'] == "") {
            $this->valF['etat'] = NULL;
        } else {
            $this->valF['etat'] = $val['etat'];
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
            $form->setType("facture", "hidden");
            if ($this->is_in_context_of_foreign_key("personne", $this->retourformulaire)) {
                $form->setType("personne", "selecthiddenstatic");
            } else {
                $form->setType("personne", "select");
            }
            $form->setType("date_creation", "date");
            $form->setType("numero_facture", "text");
            $form->setType("etat", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("facture", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("personne", $this->retourformulaire)) {
                $form->setType("personne", "selecthiddenstatic");
            } else {
                $form->setType("personne", "select");
            }
            $form->setType("date_creation", "date");
            $form->setType("numero_facture", "text");
            $form->setType("etat", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("facture", "hiddenstatic");
            $form->setType("personne", "selectstatic");
            $form->setType("date_creation", "hiddenstatic");
            $form->setType("numero_facture", "hiddenstatic");
            $form->setType("etat", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("facture", "static");
            $form->setType("personne", "selectstatic");
            $form->setType("date_creation", "datestatic");
            $form->setType("numero_facture", "static");
            $form->setType("etat", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('facture','VerifNum(this)');
        $form->setOnchange('personne','VerifNum(this)');
        $form->setOnchange('date_creation','fdate(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("facture", 11);
        $form->setTaille("personne", 11);
        $form->setTaille("date_creation", 12);
        $form->setTaille("numero_facture", 10);
        $form->setTaille("etat", 10);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("facture", 11);
        $form->setMax("personne", 11);
        $form->setMax("date_creation", 12);
        $form->setMax("numero_facture", -5);
        $form->setMax("etat", -5);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('facture', __('facture'));
        $form->setLib('personne', __('personne'));
        $form->setLib('date_creation', __('date_creation'));
        $form->setLib('numero_facture', __('numero_facture'));
        $form->setLib('etat', __('etat'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

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
        // Verification de la cle secondaire : facture_sejour
        $this->rechercheTable($this->f->db, "facture_sejour", "facture", $id);
        // Verification de la cle secondaire : facture_soin
        $this->rechercheTable($this->f->db, "facture_soin", "facture", $id);
    }


}
