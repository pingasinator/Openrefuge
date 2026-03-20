<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_droit_gen extends dbform {

    protected $_absolute_class_name = "om_droit";

    var $table = "om_droit";
    var $clePrimaire = "om_droit";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_droit",
        "om_profil"
    );
    var $unique_key = array(
      array("libelle","om_profil"),
    );
    var $foreign_keys_extended = array(
        "om_profil" => array("om_profil", ),
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
            "om_droit",
            "libelle",
            "om_profil",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_profil() {
        return "SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil ORDER BY om_profil.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_profil_by_id() {
        return "SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil WHERE om_profil = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_droit'])) {
            $this->valF['om_droit'] = ""; // -> requis
        } else {
            $this->valF['om_droit'] = $val['om_droit'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if (!is_numeric($val['om_profil'])) {
            $this->valF['om_profil'] = ""; // -> requis
        } else {
            $this->valF['om_profil'] = $val['om_profil'];
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
            $form->setType("om_droit", "hidden");
            $form->setType("libelle", "text");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_droit", "hiddenstatic");
            $form->setType("libelle", "text");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_droit", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("om_profil", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_droit", "static");
            $form->setType("libelle", "static");
            $form->setType("om_profil", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_droit','VerifNum(this)');
        $form->setOnchange('om_profil','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_droit", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("om_profil", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_droit", 11);
        $form->setMax("libelle", 100);
        $form->setMax("om_profil", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_droit', __('om_droit'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('om_profil', __('om_profil'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // om_profil
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_profil",
            $this->get_var_sql_forminc__sql("om_profil"),
            $this->get_var_sql_forminc__sql("om_profil_by_id"),
            false
        );
    }


    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_profil', $this->retourformulaire))
                $form->setVal('om_profil', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
