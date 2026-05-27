<?php
//$Id$ 
//gen openMairie le 28/04/2026 10:26

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class test_gen extends dbform {

    protected $_absolute_class_name = "test";

    var $table = "test";
    var $clePrimaire = "test";
    var $typeCle = "N";
    var $required_field = array(
        "test"
    );
    
    var $foreign_keys_extended = array(
    );
    
    /**
     *
     * @return string
     */
    function get_default_libelle() {
        return $this->getVal($this->clePrimaire)."&nbsp;".$this->getVal("mon_int");
    }

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "test",
            "mon_int",
            "mon_varchar",
        );
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['test'])) {
            $this->valF['test'] = ""; // -> requis
        } else {
            $this->valF['test'] = $val['test'];
        }
        if (!is_numeric($val['mon_int'])) {
            $this->valF['mon_int'] = NULL;
        } else {
            $this->valF['mon_int'] = $val['mon_int'];
        }
        if ($val['mon_varchar'] == "") {
            $this->valF['mon_varchar'] = NULL;
        } else {
            $this->valF['mon_varchar'] = $val['mon_varchar'];
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
            $form->setType("test", "hidden");
            $form->setType("mon_int", "text");
            $form->setType("mon_varchar", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("test", "hiddenstatic");
            $form->setType("mon_int", "text");
            $form->setType("mon_varchar", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("test", "hiddenstatic");
            $form->setType("mon_int", "hiddenstatic");
            $form->setType("mon_varchar", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("test", "static");
            $form->setType("mon_int", "static");
            $form->setType("mon_varchar", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('test','VerifNum(this)');
        $form->setOnchange('mon_int','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("test", 11);
        $form->setTaille("mon_int", 11);
        $form->setTaille("mon_varchar", 10);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("test", 11);
        $form->setMax("mon_int", 11);
        $form->setMax("mon_varchar", -5);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('test', __('test'));
        $form->setLib('mon_int', __('mon_int'));
        $form->setLib('mon_varchar', __('mon_varchar'));
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
    

}
