<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_utilisateur_gen extends dbform {

    protected $_absolute_class_name = "om_utilisateur";

    var $table = "om_utilisateur";
    var $clePrimaire = "om_utilisateur";
    var $typeCle = "N";
    var $required_field = array(
        "email",
        "login",
        "nom",
        "om_collectivite",
        "om_profil",
        "om_utilisateur",
        "pwd"
    );
    var $unique_key = array(
      "login",
    );
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
        "om_profil" => array("om_profil", ),
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
            "om_utilisateur",
            "nom",
            "email",
            "login",
            "pwd",
            "om_collectivite",
            "om_type",
            "om_profil",
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
        if (!is_numeric($val['om_utilisateur'])) {
            $this->valF['om_utilisateur'] = ""; // -> requis
        } else {
            $this->valF['om_utilisateur'] = $val['om_utilisateur'];
        }
        $this->valF['nom'] = $val['nom'];
        $this->valF['email'] = $val['email'];
        $this->valF['login'] = $val['login'];
        $this->valF['pwd'] = $val['pwd'];
        if (!is_numeric($val['om_collectivite'])) {
            $this->valF['om_collectivite'] = ""; // -> requis
        } else {
            if($_SESSION['niveau']==1) {
                $this->valF['om_collectivite'] = $_SESSION['collectivite'];
            } else {
                $this->valF['om_collectivite'] = $val['om_collectivite'];
            }
        }
        if ($val['om_type'] == "") {
            $this->valF['om_type'] = ""; // -> default
        } else {
            $this->valF['om_type'] = $val['om_type'];
        }
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
            $form->setType("om_utilisateur", "hidden");
            $form->setType("nom", "text");
            $form->setType("email", "text");
            $form->setType("login", "text");
            $form->setType("pwd", "text");
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
            $form->setType("om_type", "text");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_utilisateur", "hiddenstatic");
            $form->setType("nom", "text");
            $form->setType("email", "text");
            $form->setType("login", "text");
            $form->setType("pwd", "text");
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
            $form->setType("om_type", "text");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_utilisateur", "hiddenstatic");
            $form->setType("nom", "hiddenstatic");
            $form->setType("email", "hiddenstatic");
            $form->setType("login", "hiddenstatic");
            $form->setType("pwd", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("om_type", "hiddenstatic");
            $form->setType("om_profil", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_utilisateur", "static");
            $form->setType("nom", "static");
            $form->setType("email", "static");
            $form->setType("login", "static");
            $form->setType("pwd", "static");
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
            $form->setType("om_type", "static");
            $form->setType("om_profil", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_utilisateur','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
        $form->setOnchange('om_profil','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_utilisateur", 11);
        $form->setTaille("nom", 30);
        $form->setTaille("email", 30);
        $form->setTaille("login", 30);
        $form->setTaille("pwd", 30);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("om_type", 20);
        $form->setTaille("om_profil", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_utilisateur", 11);
        $form->setMax("nom", 30);
        $form->setMax("email", 100);
        $form->setMax("login", 30);
        $form->setMax("pwd", 100);
        $form->setMax("om_collectivite", 11);
        $form->setMax("om_type", 20);
        $form->setMax("om_profil", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_utilisateur', __('om_utilisateur'));
        $form->setLib('nom', __('nom'));
        $form->setLib('email', __('email'));
        $form->setLib('login', __('login'));
        $form->setLib('pwd', __('pwd'));
        $form->setLib('om_collectivite', __('om_collectivite'));
        $form->setLib('om_type', __('om_type'));
        $form->setLib('om_profil', __('om_profil'));
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
            if($this->is_in_context_of_foreign_key('om_profil', $this->retourformulaire))
                $form->setVal('om_profil', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire
    //==================================
    

}
