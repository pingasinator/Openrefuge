<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_parametre_gen extends dbform {

    protected $_absolute_class_name = "om_parametre";

    var $table = "om_parametre";
    var $clePrimaire = "om_parametre";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_collectivite",
        "om_parametre",
        "valeur"
    );
    
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
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
            "om_parametre",
            "libelle",
            "valeur",
            "om_collectivite",
        );
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_collectivite() {
        return "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
    }

    /**
     *
     * @return string
     */
    function get_var_sql_forminc__sql_om_collectivite_by_id() {
        return "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
    }




    function setvalF($val = array()) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_parametre'])) {
            $this->valF['om_parametre'] = ""; // -> requis
        } else {
            $this->valF['om_parametre'] = $val['om_parametre'];
        }
        $this->valF['libelle'] = $val['libelle'];
            $this->valF['valeur'] = $val['valeur'];
        if (!is_numeric($val['om_collectivite'])) {
            $this->valF['om_collectivite'] = ""; // -> requis
        } else {
            if($_SESSION['niveau']==1) {
                $this->valF['om_collectivite'] = $_SESSION['collectivite'];
            } else {
                $this->valF['om_collectivite'] = $val['om_collectivite'];
            }
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
            $form->setType("om_parametre", "hidden");
            $form->setType("libelle", "text");
            $form->setType("valeur", "textarea");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selecthiddenstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "select");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_parametre", "hiddenstatic");
            $form->setType("libelle", "text");
            $form->setType("valeur", "textarea");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selecthiddenstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "select");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_parametre", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("valeur", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_parametre", "static");
            $form->setType("libelle", "static");
            $form->setType("valeur", "textareastatic");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selectstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selectstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_parametre','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_parametre", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("valeur", 80);
        $form->setTaille("om_collectivite", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_parametre", 11);
        $form->setMax("libelle", 100);
        $form->setMax("valeur", 6);
        $form->setMax("om_collectivite", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_parametre', __('om_parametre'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('valeur', __('valeur'));
        $form->setLib('om_collectivite', __('om_collectivite'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // om_collectivite
        $this->init_select(
            $form, 
            $this->f->db,
            $maj,
            null,
            "om_collectivite",
            $this->get_var_sql_forminc__sql("om_collectivite"),
            $this->get_var_sql_forminc__sql("om_collectivite_by_id"),
            false
        );
    }


    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        if($validation==0 and $maj==0 and $_SESSION['niveau']==1) {
            $form->setVal('om_collectivite', $_SESSION['collectivite']);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setVal

    //==================================
    // sous Formulaire
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation==0 and $maj==0 and $_SESSION['niveau']==1) {
            $form->setVal('om_collectivite', $_SESSION['collectivite']);
        }// fin validation
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_collectivite', $this->retourformulaire))
                $form->setVal('om_collectivite', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
