<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_widget_gen extends dbform {

    protected $_absolute_class_name = "om_widget";

    var $table = "om_widget";
    var $clePrimaire = "om_widget";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_widget",
        "type"
    );
    
    var $foreign_keys_extended = array(
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
            "om_widget",
            "libelle",
            "lien",
            "texte",
            "type",
            "script",
            "arguments",
        );
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_widget'])) {
            $this->valF['om_widget'] = ""; // -> requis
        } else {
            $this->valF['om_widget'] = $val['om_widget'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if ($val['lien'] == "") {
            $this->valF['lien'] = ""; // -> default
        } else {
            $this->valF['lien'] = $val['lien'];
        }
            $this->valF['texte'] = $val['texte'];
        $this->valF['type'] = $val['type'];
        if ($val['script'] == "") {
            $this->valF['script'] = ""; // -> default
        } else {
            $this->valF['script'] = $val['script'];
        }
            $this->valF['arguments'] = $val['arguments'];
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
            $form->setType("om_widget", "hidden");
            $form->setType("libelle", "text");
            $form->setType("lien", "text");
            $form->setType("texte", "textarea");
            $form->setType("type", "text");
            $form->setType("script", "text");
            $form->setType("arguments", "textarea");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_widget", "hiddenstatic");
            $form->setType("libelle", "text");
            $form->setType("lien", "text");
            $form->setType("texte", "textarea");
            $form->setType("type", "text");
            $form->setType("script", "text");
            $form->setType("arguments", "textarea");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_widget", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("lien", "hiddenstatic");
            $form->setType("texte", "hiddenstatic");
            $form->setType("type", "hiddenstatic");
            $form->setType("script", "hiddenstatic");
            $form->setType("arguments", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_widget", "static");
            $form->setType("libelle", "static");
            $form->setType("lien", "static");
            $form->setType("texte", "textareastatic");
            $form->setType("type", "static");
            $form->setType("script", "static");
            $form->setType("arguments", "textareastatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_widget','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_widget", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("lien", 30);
        $form->setTaille("texte", 80);
        $form->setTaille("type", 30);
        $form->setTaille("script", 30);
        $form->setTaille("arguments", 80);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_widget", 11);
        $form->setMax("libelle", 100);
        $form->setMax("lien", 80);
        $form->setMax("texte", 6);
        $form->setMax("type", 40);
        $form->setMax("script", 80);
        $form->setMax("arguments", 6);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_widget', __('om_widget'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('lien', __('lien'));
        $form->setLib('texte', __('texte'));
        $form->setLib('type', __('type'));
        $form->setLib('script', __('script'));
        $form->setLib('arguments', __('arguments'));
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
        // Verification de la cle secondaire : om_dashboard
        $this->rechercheTable($this->f->db, "om_dashboard", "om_widget", $id);
    }


}
