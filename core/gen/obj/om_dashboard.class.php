<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_dashboard_gen extends dbform {

    protected $_absolute_class_name = "om_dashboard";

    var $table = "om_dashboard";
    var $clePrimaire = "om_dashboard";
    var $typeCle = "N";
    var $required_field = array(
        "bloc",
        "om_dashboard",
        "om_profil",
        "om_widget"
    );
    
    var $foreign_keys_extended = array(
        "om_profil" => array("om_profil", ),
        "om_widget" => array("om_widget", ),
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("om_profil");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_dashboard",
            "om_profil",
            "bloc",
            "position",
            "om_widget",
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

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_widget() {
        return "SELECT om_widget.om_widget, om_widget.libelle FROM ".DB_PREFIXE."om_widget ORDER BY om_widget.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_widget_by_id() {
        return "SELECT om_widget.om_widget, om_widget.libelle FROM ".DB_PREFIXE."om_widget WHERE om_widget = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_dashboard'])) {
            $this->valF['om_dashboard'] = ""; // -> requis
        } else {
            $this->valF['om_dashboard'] = $val['om_dashboard'];
        }
        if (!is_numeric($val['om_profil'])) {
            $this->valF['om_profil'] = ""; // -> requis
        } else {
            $this->valF['om_profil'] = $val['om_profil'];
        }
        $this->valF['bloc'] = $val['bloc'];
        if (!is_numeric($val['position'])) {
            $this->valF['position'] = NULL;
        } else {
            $this->valF['position'] = $val['position'];
        }
        if (!is_numeric($val['om_widget'])) {
            $this->valF['om_widget'] = ""; // -> requis
        } else {
            $this->valF['om_widget'] = $val['om_widget'];
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
            $form->setType("om_dashboard", "hidden");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
            $form->setType("bloc", "text");
            $form->setType("position", "text");
            if ($this->is_in_context_of_foreign_key("om_widget", $this->retourformulaire)) {
                $form->setType("om_widget", "selecthiddenstatic");
            } else {
                $form->setType("om_widget", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_dashboard", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
            $form->setType("bloc", "text");
            $form->setType("position", "text");
            if ($this->is_in_context_of_foreign_key("om_widget", $this->retourformulaire)) {
                $form->setType("om_widget", "selecthiddenstatic");
            } else {
                $form->setType("om_widget", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_dashboard", "hiddenstatic");
            $form->setType("om_profil", "selectstatic");
            $form->setType("bloc", "hiddenstatic");
            $form->setType("position", "hiddenstatic");
            $form->setType("om_widget", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_dashboard", "static");
            $form->setType("om_profil", "selectstatic");
            $form->setType("bloc", "static");
            $form->setType("position", "static");
            $form->setType("om_widget", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_dashboard','VerifNum(this)');
        $form->setOnchange('om_profil','VerifNum(this)');
        $form->setOnchange('position','VerifNum(this)');
        $form->setOnchange('om_widget','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_dashboard", 11);
        $form->setTaille("om_profil", 11);
        $form->setTaille("bloc", 10);
        $form->setTaille("position", 11);
        $form->setTaille("om_widget", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_dashboard", 11);
        $form->setMax("om_profil", 11);
        $form->setMax("bloc", 10);
        $form->setMax("position", 11);
        $form->setMax("om_widget", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_dashboard', __('om_dashboard'));
        $form->setLib('om_profil', __('om_profil'));
        $form->setLib('bloc', __('bloc'));
        $form->setLib('position', __('position'));
        $form->setLib('om_widget', __('om_widget'));
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
        // om_widget
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_widget",
            $this->get_var_sql_forminc__sql("om_widget"),
            $this->get_var_sql_forminc__sql("om_widget_by_id"),
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
            if($this->is_in_context_of_foreign_key('om_widget', $this->retourformulaire))
                $form->setVal('om_widget', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
