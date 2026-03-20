<?php
//$Id$ 
//gen openMairie le 03/05/2018 08:49

require_once PATH_OPENMAIRIE."om_dbform.class.php";

class om_sousetat_gen extends dbform {

    protected $_absolute_class_name = "om_sousetat";

    var $table = "om_sousetat";
    var $clePrimaire = "om_sousetat";
    var $typeCle = "N";
    var $required_field = array(
        "bordure_couleur",
        "cellule_align",
        "cellule_align_moyenne",
        "cellule_align_nbr",
        "cellule_align_total",
        "cellule_bordure",
        "cellule_bordure_moyenne",
        "cellule_bordure_nbr",
        "cellule_bordure_total",
        "cellule_bordure_un",
        "cellule_compteur",
        "cellule_fond",
        "cellule_fondcouleur_moyenne",
        "cellule_fondcouleur_nbr",
        "cellule_fondcouleur_total",
        "cellule_fond_moyenne",
        "cellule_fond_nbr",
        "cellule_fond_total",
        "cellule_fontaille_moyenne",
        "cellule_fontaille_nbr",
        "cellule_fontaille_total",
        "cellule_hauteur",
        "cellule_hauteur_moyenne",
        "cellule_hauteur_nbr",
        "cellule_hauteur_total",
        "cellule_largeur",
        "cellule_moyenne",
        "cellule_numerique",
        "cellule_total",
        "entetecolone_align",
        "entetecolone_bordure",
        "entete_flag",
        "entete_fond",
        "entete_fondcouleur",
        "entete_hauteur",
        "entete_orientation",
        "entete_textecouleur",
        "id",
        "intervalle_debut",
        "intervalle_fin",
        "libelle",
        "om_collectivite",
        "om_sousetat",
        "om_sql",
        "se_fond1",
        "se_fond2",
        "tableau_bordure",
        "tableau_fontaille",
        "tableau_largeur",
        "titre",
        "titrealign",
        "titrebordure",
        "titrefond",
        "titrefondcouleur",
        "titrefont",
        "titrehauteur",
        "titretaille",
        "titretextecouleur"
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
            "om_sousetat",
            "om_collectivite",
            "id",
            "libelle",
            "actif",
            "titre",
            "titrehauteur",
            "titrefont",
            "titreattribut",
            "titretaille",
            "titrebordure",
            "titrealign",
            "titrefond",
            "titrefondcouleur",
            "titretextecouleur",
            "intervalle_debut",
            "intervalle_fin",
            "entete_flag",
            "entete_fond",
            "entete_orientation",
            "entete_hauteur",
            "entetecolone_bordure",
            "entetecolone_align",
            "entete_fondcouleur",
            "entete_textecouleur",
            "tableau_largeur",
            "tableau_bordure",
            "tableau_fontaille",
            "bordure_couleur",
            "se_fond1",
            "se_fond2",
            "cellule_fond",
            "cellule_hauteur",
            "cellule_largeur",
            "cellule_bordure_un",
            "cellule_bordure",
            "cellule_align",
            "cellule_fond_total",
            "cellule_fontaille_total",
            "cellule_hauteur_total",
            "cellule_fondcouleur_total",
            "cellule_bordure_total",
            "cellule_align_total",
            "cellule_fond_moyenne",
            "cellule_fontaille_moyenne",
            "cellule_hauteur_moyenne",
            "cellule_fondcouleur_moyenne",
            "cellule_bordure_moyenne",
            "cellule_align_moyenne",
            "cellule_fond_nbr",
            "cellule_fontaille_nbr",
            "cellule_hauteur_nbr",
            "cellule_fondcouleur_nbr",
            "cellule_bordure_nbr",
            "cellule_align_nbr",
            "cellule_numerique",
            "cellule_total",
            "cellule_moyenne",
            "cellule_compteur",
            "om_sql",
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
        if (!is_numeric($val['om_sousetat'])) {
            $this->valF['om_sousetat'] = ""; // -> requis
        } else {
            $this->valF['om_sousetat'] = $val['om_sousetat'];
        }
        if (!is_numeric($val['om_collectivite'])) {
            $this->valF['om_collectivite'] = ""; // -> requis
        } else {
            if($_SESSION['niveau']==1) {
                $this->valF['om_collectivite'] = $_SESSION['collectivite'];
            } else {
                $this->valF['om_collectivite'] = $val['om_collectivite'];
            }
        }
        $this->valF['id'] = $val['id'];
        $this->valF['libelle'] = $val['libelle'];
        if ($val['actif'] == 1 || $val['actif'] == "t" || $val['actif'] == "Oui") {
            $this->valF['actif'] = true;
        } else {
            $this->valF['actif'] = false;
        }
            $this->valF['titre'] = $val['titre'];
        if (!is_numeric($val['titrehauteur'])) {
            $this->valF['titrehauteur'] = ""; // -> requis
        } else {
            $this->valF['titrehauteur'] = $val['titrehauteur'];
        }
        $this->valF['titrefont'] = $val['titrefont'];
        if ($val['titreattribut'] == "") {
            $this->valF['titreattribut'] = ""; // -> default
        } else {
            $this->valF['titreattribut'] = $val['titreattribut'];
        }
        if (!is_numeric($val['titretaille'])) {
            $this->valF['titretaille'] = ""; // -> requis
        } else {
            $this->valF['titretaille'] = $val['titretaille'];
        }
        $this->valF['titrebordure'] = $val['titrebordure'];
        $this->valF['titrealign'] = $val['titrealign'];
        $this->valF['titrefond'] = $val['titrefond'];
        $this->valF['titrefondcouleur'] = $val['titrefondcouleur'];
        $this->valF['titretextecouleur'] = $val['titretextecouleur'];
        if (!is_numeric($val['intervalle_debut'])) {
            $this->valF['intervalle_debut'] = ""; // -> requis
        } else {
            $this->valF['intervalle_debut'] = $val['intervalle_debut'];
        }
        if (!is_numeric($val['intervalle_fin'])) {
            $this->valF['intervalle_fin'] = ""; // -> requis
        } else {
            $this->valF['intervalle_fin'] = $val['intervalle_fin'];
        }
        $this->valF['entete_flag'] = $val['entete_flag'];
        $this->valF['entete_fond'] = $val['entete_fond'];
        $this->valF['entete_orientation'] = $val['entete_orientation'];
        if (!is_numeric($val['entete_hauteur'])) {
            $this->valF['entete_hauteur'] = ""; // -> requis
        } else {
            $this->valF['entete_hauteur'] = $val['entete_hauteur'];
        }
        $this->valF['entetecolone_bordure'] = $val['entetecolone_bordure'];
        $this->valF['entetecolone_align'] = $val['entetecolone_align'];
        $this->valF['entete_fondcouleur'] = $val['entete_fondcouleur'];
        $this->valF['entete_textecouleur'] = $val['entete_textecouleur'];
        if (!is_numeric($val['tableau_largeur'])) {
            $this->valF['tableau_largeur'] = ""; // -> requis
        } else {
            $this->valF['tableau_largeur'] = $val['tableau_largeur'];
        }
        $this->valF['tableau_bordure'] = $val['tableau_bordure'];
        if (!is_numeric($val['tableau_fontaille'])) {
            $this->valF['tableau_fontaille'] = ""; // -> requis
        } else {
            $this->valF['tableau_fontaille'] = $val['tableau_fontaille'];
        }
        $this->valF['bordure_couleur'] = $val['bordure_couleur'];
        $this->valF['se_fond1'] = $val['se_fond1'];
        $this->valF['se_fond2'] = $val['se_fond2'];
        $this->valF['cellule_fond'] = $val['cellule_fond'];
        if (!is_numeric($val['cellule_hauteur'])) {
            $this->valF['cellule_hauteur'] = ""; // -> requis
        } else {
            $this->valF['cellule_hauteur'] = $val['cellule_hauteur'];
        }
        $this->valF['cellule_largeur'] = $val['cellule_largeur'];
        $this->valF['cellule_bordure_un'] = $val['cellule_bordure_un'];
        $this->valF['cellule_bordure'] = $val['cellule_bordure'];
        $this->valF['cellule_align'] = $val['cellule_align'];
        $this->valF['cellule_fond_total'] = $val['cellule_fond_total'];
        if (!is_numeric($val['cellule_fontaille_total'])) {
            $this->valF['cellule_fontaille_total'] = ""; // -> requis
        } else {
            $this->valF['cellule_fontaille_total'] = $val['cellule_fontaille_total'];
        }
        if (!is_numeric($val['cellule_hauteur_total'])) {
            $this->valF['cellule_hauteur_total'] = ""; // -> requis
        } else {
            $this->valF['cellule_hauteur_total'] = $val['cellule_hauteur_total'];
        }
        $this->valF['cellule_fondcouleur_total'] = $val['cellule_fondcouleur_total'];
        $this->valF['cellule_bordure_total'] = $val['cellule_bordure_total'];
        $this->valF['cellule_align_total'] = $val['cellule_align_total'];
        $this->valF['cellule_fond_moyenne'] = $val['cellule_fond_moyenne'];
        if (!is_numeric($val['cellule_fontaille_moyenne'])) {
            $this->valF['cellule_fontaille_moyenne'] = ""; // -> requis
        } else {
            $this->valF['cellule_fontaille_moyenne'] = $val['cellule_fontaille_moyenne'];
        }
        if (!is_numeric($val['cellule_hauteur_moyenne'])) {
            $this->valF['cellule_hauteur_moyenne'] = ""; // -> requis
        } else {
            $this->valF['cellule_hauteur_moyenne'] = $val['cellule_hauteur_moyenne'];
        }
        $this->valF['cellule_fondcouleur_moyenne'] = $val['cellule_fondcouleur_moyenne'];
        $this->valF['cellule_bordure_moyenne'] = $val['cellule_bordure_moyenne'];
        $this->valF['cellule_align_moyenne'] = $val['cellule_align_moyenne'];
        $this->valF['cellule_fond_nbr'] = $val['cellule_fond_nbr'];
        if (!is_numeric($val['cellule_fontaille_nbr'])) {
            $this->valF['cellule_fontaille_nbr'] = ""; // -> requis
        } else {
            $this->valF['cellule_fontaille_nbr'] = $val['cellule_fontaille_nbr'];
        }
        if (!is_numeric($val['cellule_hauteur_nbr'])) {
            $this->valF['cellule_hauteur_nbr'] = ""; // -> requis
        } else {
            $this->valF['cellule_hauteur_nbr'] = $val['cellule_hauteur_nbr'];
        }
        $this->valF['cellule_fondcouleur_nbr'] = $val['cellule_fondcouleur_nbr'];
        $this->valF['cellule_bordure_nbr'] = $val['cellule_bordure_nbr'];
        $this->valF['cellule_align_nbr'] = $val['cellule_align_nbr'];
        $this->valF['cellule_numerique'] = $val['cellule_numerique'];
        $this->valF['cellule_total'] = $val['cellule_total'];
        $this->valF['cellule_moyenne'] = $val['cellule_moyenne'];
        $this->valF['cellule_compteur'] = $val['cellule_compteur'];
            $this->valF['om_sql'] = $val['om_sql'];
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
            $form->setType("om_sousetat", "hidden");
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
            $form->setType("id", "text");
            $form->setType("libelle", "text");
            $form->setType("actif", "checkbox");
            $form->setType("titre", "textarea");
            $form->setType("titrehauteur", "text");
            $form->setType("titrefont", "text");
            $form->setType("titreattribut", "text");
            $form->setType("titretaille", "text");
            $form->setType("titrebordure", "text");
            $form->setType("titrealign", "text");
            $form->setType("titrefond", "text");
            $form->setType("titrefondcouleur", "text");
            $form->setType("titretextecouleur", "text");
            $form->setType("intervalle_debut", "text");
            $form->setType("intervalle_fin", "text");
            $form->setType("entete_flag", "text");
            $form->setType("entete_fond", "text");
            $form->setType("entete_orientation", "text");
            $form->setType("entete_hauteur", "text");
            $form->setType("entetecolone_bordure", "text");
            $form->setType("entetecolone_align", "text");
            $form->setType("entete_fondcouleur", "text");
            $form->setType("entete_textecouleur", "text");
            $form->setType("tableau_largeur", "text");
            $form->setType("tableau_bordure", "text");
            $form->setType("tableau_fontaille", "text");
            $form->setType("bordure_couleur", "text");
            $form->setType("se_fond1", "text");
            $form->setType("se_fond2", "text");
            $form->setType("cellule_fond", "text");
            $form->setType("cellule_hauteur", "text");
            $form->setType("cellule_largeur", "text");
            $form->setType("cellule_bordure_un", "text");
            $form->setType("cellule_bordure", "text");
            $form->setType("cellule_align", "text");
            $form->setType("cellule_fond_total", "text");
            $form->setType("cellule_fontaille_total", "text");
            $form->setType("cellule_hauteur_total", "text");
            $form->setType("cellule_fondcouleur_total", "text");
            $form->setType("cellule_bordure_total", "text");
            $form->setType("cellule_align_total", "text");
            $form->setType("cellule_fond_moyenne", "text");
            $form->setType("cellule_fontaille_moyenne", "text");
            $form->setType("cellule_hauteur_moyenne", "text");
            $form->setType("cellule_fondcouleur_moyenne", "text");
            $form->setType("cellule_bordure_moyenne", "text");
            $form->setType("cellule_align_moyenne", "text");
            $form->setType("cellule_fond_nbr", "text");
            $form->setType("cellule_fontaille_nbr", "text");
            $form->setType("cellule_hauteur_nbr", "text");
            $form->setType("cellule_fondcouleur_nbr", "text");
            $form->setType("cellule_bordure_nbr", "text");
            $form->setType("cellule_align_nbr", "text");
            $form->setType("cellule_numerique", "text");
            $form->setType("cellule_total", "text");
            $form->setType("cellule_moyenne", "text");
            $form->setType("cellule_compteur", "text");
            $form->setType("om_sql", "textarea");
        }

        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType("om_sousetat", "hiddenstatic");
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
            $form->setType("id", "text");
            $form->setType("libelle", "text");
            $form->setType("actif", "checkbox");
            $form->setType("titre", "textarea");
            $form->setType("titrehauteur", "text");
            $form->setType("titrefont", "text");
            $form->setType("titreattribut", "text");
            $form->setType("titretaille", "text");
            $form->setType("titrebordure", "text");
            $form->setType("titrealign", "text");
            $form->setType("titrefond", "text");
            $form->setType("titrefondcouleur", "text");
            $form->setType("titretextecouleur", "text");
            $form->setType("intervalle_debut", "text");
            $form->setType("intervalle_fin", "text");
            $form->setType("entete_flag", "text");
            $form->setType("entete_fond", "text");
            $form->setType("entete_orientation", "text");
            $form->setType("entete_hauteur", "text");
            $form->setType("entetecolone_bordure", "text");
            $form->setType("entetecolone_align", "text");
            $form->setType("entete_fondcouleur", "text");
            $form->setType("entete_textecouleur", "text");
            $form->setType("tableau_largeur", "text");
            $form->setType("tableau_bordure", "text");
            $form->setType("tableau_fontaille", "text");
            $form->setType("bordure_couleur", "text");
            $form->setType("se_fond1", "text");
            $form->setType("se_fond2", "text");
            $form->setType("cellule_fond", "text");
            $form->setType("cellule_hauteur", "text");
            $form->setType("cellule_largeur", "text");
            $form->setType("cellule_bordure_un", "text");
            $form->setType("cellule_bordure", "text");
            $form->setType("cellule_align", "text");
            $form->setType("cellule_fond_total", "text");
            $form->setType("cellule_fontaille_total", "text");
            $form->setType("cellule_hauteur_total", "text");
            $form->setType("cellule_fondcouleur_total", "text");
            $form->setType("cellule_bordure_total", "text");
            $form->setType("cellule_align_total", "text");
            $form->setType("cellule_fond_moyenne", "text");
            $form->setType("cellule_fontaille_moyenne", "text");
            $form->setType("cellule_hauteur_moyenne", "text");
            $form->setType("cellule_fondcouleur_moyenne", "text");
            $form->setType("cellule_bordure_moyenne", "text");
            $form->setType("cellule_align_moyenne", "text");
            $form->setType("cellule_fond_nbr", "text");
            $form->setType("cellule_fontaille_nbr", "text");
            $form->setType("cellule_hauteur_nbr", "text");
            $form->setType("cellule_fondcouleur_nbr", "text");
            $form->setType("cellule_bordure_nbr", "text");
            $form->setType("cellule_align_nbr", "text");
            $form->setType("cellule_numerique", "text");
            $form->setType("cellule_total", "text");
            $form->setType("cellule_moyenne", "text");
            $form->setType("cellule_compteur", "text");
            $form->setType("om_sql", "textarea");
        }

        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType("om_sousetat", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("id", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("actif", "hiddenstatic");
            $form->setType("titre", "hiddenstatic");
            $form->setType("titrehauteur", "hiddenstatic");
            $form->setType("titrefont", "hiddenstatic");
            $form->setType("titreattribut", "hiddenstatic");
            $form->setType("titretaille", "hiddenstatic");
            $form->setType("titrebordure", "hiddenstatic");
            $form->setType("titrealign", "hiddenstatic");
            $form->setType("titrefond", "hiddenstatic");
            $form->setType("titrefondcouleur", "hiddenstatic");
            $form->setType("titretextecouleur", "hiddenstatic");
            $form->setType("intervalle_debut", "hiddenstatic");
            $form->setType("intervalle_fin", "hiddenstatic");
            $form->setType("entete_flag", "hiddenstatic");
            $form->setType("entete_fond", "hiddenstatic");
            $form->setType("entete_orientation", "hiddenstatic");
            $form->setType("entete_hauteur", "hiddenstatic");
            $form->setType("entetecolone_bordure", "hiddenstatic");
            $form->setType("entetecolone_align", "hiddenstatic");
            $form->setType("entete_fondcouleur", "hiddenstatic");
            $form->setType("entete_textecouleur", "hiddenstatic");
            $form->setType("tableau_largeur", "hiddenstatic");
            $form->setType("tableau_bordure", "hiddenstatic");
            $form->setType("tableau_fontaille", "hiddenstatic");
            $form->setType("bordure_couleur", "hiddenstatic");
            $form->setType("se_fond1", "hiddenstatic");
            $form->setType("se_fond2", "hiddenstatic");
            $form->setType("cellule_fond", "hiddenstatic");
            $form->setType("cellule_hauteur", "hiddenstatic");
            $form->setType("cellule_largeur", "hiddenstatic");
            $form->setType("cellule_bordure_un", "hiddenstatic");
            $form->setType("cellule_bordure", "hiddenstatic");
            $form->setType("cellule_align", "hiddenstatic");
            $form->setType("cellule_fond_total", "hiddenstatic");
            $form->setType("cellule_fontaille_total", "hiddenstatic");
            $form->setType("cellule_hauteur_total", "hiddenstatic");
            $form->setType("cellule_fondcouleur_total", "hiddenstatic");
            $form->setType("cellule_bordure_total", "hiddenstatic");
            $form->setType("cellule_align_total", "hiddenstatic");
            $form->setType("cellule_fond_moyenne", "hiddenstatic");
            $form->setType("cellule_fontaille_moyenne", "hiddenstatic");
            $form->setType("cellule_hauteur_moyenne", "hiddenstatic");
            $form->setType("cellule_fondcouleur_moyenne", "hiddenstatic");
            $form->setType("cellule_bordure_moyenne", "hiddenstatic");
            $form->setType("cellule_align_moyenne", "hiddenstatic");
            $form->setType("cellule_fond_nbr", "hiddenstatic");
            $form->setType("cellule_fontaille_nbr", "hiddenstatic");
            $form->setType("cellule_hauteur_nbr", "hiddenstatic");
            $form->setType("cellule_fondcouleur_nbr", "hiddenstatic");
            $form->setType("cellule_bordure_nbr", "hiddenstatic");
            $form->setType("cellule_align_nbr", "hiddenstatic");
            $form->setType("cellule_numerique", "hiddenstatic");
            $form->setType("cellule_total", "hiddenstatic");
            $form->setType("cellule_moyenne", "hiddenstatic");
            $form->setType("cellule_compteur", "hiddenstatic");
            $form->setType("om_sql", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType("om_sousetat", "static");
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
            $form->setType("id", "static");
            $form->setType("libelle", "static");
            $form->setType("actif", "checkboxstatic");
            $form->setType("titre", "textareastatic");
            $form->setType("titrehauteur", "static");
            $form->setType("titrefont", "static");
            $form->setType("titreattribut", "static");
            $form->setType("titretaille", "static");
            $form->setType("titrebordure", "static");
            $form->setType("titrealign", "static");
            $form->setType("titrefond", "static");
            $form->setType("titrefondcouleur", "static");
            $form->setType("titretextecouleur", "static");
            $form->setType("intervalle_debut", "static");
            $form->setType("intervalle_fin", "static");
            $form->setType("entete_flag", "static");
            $form->setType("entete_fond", "static");
            $form->setType("entete_orientation", "static");
            $form->setType("entete_hauteur", "static");
            $form->setType("entetecolone_bordure", "static");
            $form->setType("entetecolone_align", "static");
            $form->setType("entete_fondcouleur", "static");
            $form->setType("entete_textecouleur", "static");
            $form->setType("tableau_largeur", "static");
            $form->setType("tableau_bordure", "static");
            $form->setType("tableau_fontaille", "static");
            $form->setType("bordure_couleur", "static");
            $form->setType("se_fond1", "static");
            $form->setType("se_fond2", "static");
            $form->setType("cellule_fond", "static");
            $form->setType("cellule_hauteur", "static");
            $form->setType("cellule_largeur", "static");
            $form->setType("cellule_bordure_un", "static");
            $form->setType("cellule_bordure", "static");
            $form->setType("cellule_align", "static");
            $form->setType("cellule_fond_total", "static");
            $form->setType("cellule_fontaille_total", "static");
            $form->setType("cellule_hauteur_total", "static");
            $form->setType("cellule_fondcouleur_total", "static");
            $form->setType("cellule_bordure_total", "static");
            $form->setType("cellule_align_total", "static");
            $form->setType("cellule_fond_moyenne", "static");
            $form->setType("cellule_fontaille_moyenne", "static");
            $form->setType("cellule_hauteur_moyenne", "static");
            $form->setType("cellule_fondcouleur_moyenne", "static");
            $form->setType("cellule_bordure_moyenne", "static");
            $form->setType("cellule_align_moyenne", "static");
            $form->setType("cellule_fond_nbr", "static");
            $form->setType("cellule_fontaille_nbr", "static");
            $form->setType("cellule_hauteur_nbr", "static");
            $form->setType("cellule_fondcouleur_nbr", "static");
            $form->setType("cellule_bordure_nbr", "static");
            $form->setType("cellule_align_nbr", "static");
            $form->setType("cellule_numerique", "static");
            $form->setType("cellule_total", "static");
            $form->setType("cellule_moyenne", "static");
            $form->setType("cellule_compteur", "static");
            $form->setType("om_sql", "textareastatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sousetat','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
        $form->setOnchange('titrehauteur','VerifNum(this)');
        $form->setOnchange('titretaille','VerifNum(this)');
        $form->setOnchange('intervalle_debut','VerifNum(this)');
        $form->setOnchange('intervalle_fin','VerifNum(this)');
        $form->setOnchange('entete_hauteur','VerifNum(this)');
        $form->setOnchange('tableau_largeur','VerifNum(this)');
        $form->setOnchange('tableau_fontaille','VerifNum(this)');
        $form->setOnchange('cellule_hauteur','VerifNum(this)');
        $form->setOnchange('cellule_fontaille_total','VerifNum(this)');
        $form->setOnchange('cellule_hauteur_total','VerifNum(this)');
        $form->setOnchange('cellule_fontaille_moyenne','VerifNum(this)');
        $form->setOnchange('cellule_hauteur_moyenne','VerifNum(this)');
        $form->setOnchange('cellule_fontaille_nbr','VerifNum(this)');
        $form->setOnchange('cellule_hauteur_nbr','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sousetat", 11);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("id", 30);
        $form->setTaille("libelle", 30);
        $form->setTaille("actif", 1);
        $form->setTaille("titre", 80);
        $form->setTaille("titrehauteur", 11);
        $form->setTaille("titrefont", 20);
        $form->setTaille("titreattribut", 20);
        $form->setTaille("titretaille", 11);
        $form->setTaille("titrebordure", 20);
        $form->setTaille("titrealign", 20);
        $form->setTaille("titrefond", 20);
        $form->setTaille("titrefondcouleur", 11);
        $form->setTaille("titretextecouleur", 11);
        $form->setTaille("intervalle_debut", 11);
        $form->setTaille("intervalle_fin", 11);
        $form->setTaille("entete_flag", 20);
        $form->setTaille("entete_fond", 20);
        $form->setTaille("entete_orientation", 30);
        $form->setTaille("entete_hauteur", 11);
        $form->setTaille("entetecolone_bordure", 30);
        $form->setTaille("entetecolone_align", 30);
        $form->setTaille("entete_fondcouleur", 11);
        $form->setTaille("entete_textecouleur", 11);
        $form->setTaille("tableau_largeur", 11);
        $form->setTaille("tableau_bordure", 20);
        $form->setTaille("tableau_fontaille", 11);
        $form->setTaille("bordure_couleur", 11);
        $form->setTaille("se_fond1", 11);
        $form->setTaille("se_fond2", 11);
        $form->setTaille("cellule_fond", 20);
        $form->setTaille("cellule_hauteur", 11);
        $form->setTaille("cellule_largeur", 30);
        $form->setTaille("cellule_bordure_un", 30);
        $form->setTaille("cellule_bordure", 30);
        $form->setTaille("cellule_align", 30);
        $form->setTaille("cellule_fond_total", 20);
        $form->setTaille("cellule_fontaille_total", 11);
        $form->setTaille("cellule_hauteur_total", 11);
        $form->setTaille("cellule_fondcouleur_total", 11);
        $form->setTaille("cellule_bordure_total", 30);
        $form->setTaille("cellule_align_total", 30);
        $form->setTaille("cellule_fond_moyenne", 20);
        $form->setTaille("cellule_fontaille_moyenne", 11);
        $form->setTaille("cellule_hauteur_moyenne", 11);
        $form->setTaille("cellule_fondcouleur_moyenne", 11);
        $form->setTaille("cellule_bordure_moyenne", 30);
        $form->setTaille("cellule_align_moyenne", 30);
        $form->setTaille("cellule_fond_nbr", 20);
        $form->setTaille("cellule_fontaille_nbr", 11);
        $form->setTaille("cellule_hauteur_nbr", 11);
        $form->setTaille("cellule_fondcouleur_nbr", 11);
        $form->setTaille("cellule_bordure_nbr", 30);
        $form->setTaille("cellule_align_nbr", 30);
        $form->setTaille("cellule_numerique", 30);
        $form->setTaille("cellule_total", 30);
        $form->setTaille("cellule_moyenne", 30);
        $form->setTaille("cellule_compteur", 30);
        $form->setTaille("om_sql", 80);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sousetat", 11);
        $form->setMax("om_collectivite", 11);
        $form->setMax("id", 50);
        $form->setMax("libelle", 100);
        $form->setMax("actif", 1);
        $form->setMax("titre", 6);
        $form->setMax("titrehauteur", 11);
        $form->setMax("titrefont", 20);
        $form->setMax("titreattribut", 20);
        $form->setMax("titretaille", 11);
        $form->setMax("titrebordure", 20);
        $form->setMax("titrealign", 20);
        $form->setMax("titrefond", 20);
        $form->setMax("titrefondcouleur", 11);
        $form->setMax("titretextecouleur", 11);
        $form->setMax("intervalle_debut", 11);
        $form->setMax("intervalle_fin", 11);
        $form->setMax("entete_flag", 20);
        $form->setMax("entete_fond", 20);
        $form->setMax("entete_orientation", 100);
        $form->setMax("entete_hauteur", 11);
        $form->setMax("entetecolone_bordure", 200);
        $form->setMax("entetecolone_align", 100);
        $form->setMax("entete_fondcouleur", 11);
        $form->setMax("entete_textecouleur", 11);
        $form->setMax("tableau_largeur", 11);
        $form->setMax("tableau_bordure", 20);
        $form->setMax("tableau_fontaille", 11);
        $form->setMax("bordure_couleur", 11);
        $form->setMax("se_fond1", 11);
        $form->setMax("se_fond2", 11);
        $form->setMax("cellule_fond", 20);
        $form->setMax("cellule_hauteur", 11);
        $form->setMax("cellule_largeur", 200);
        $form->setMax("cellule_bordure_un", 200);
        $form->setMax("cellule_bordure", 200);
        $form->setMax("cellule_align", 100);
        $form->setMax("cellule_fond_total", 20);
        $form->setMax("cellule_fontaille_total", 11);
        $form->setMax("cellule_hauteur_total", 11);
        $form->setMax("cellule_fondcouleur_total", 11);
        $form->setMax("cellule_bordure_total", 200);
        $form->setMax("cellule_align_total", 100);
        $form->setMax("cellule_fond_moyenne", 20);
        $form->setMax("cellule_fontaille_moyenne", 11);
        $form->setMax("cellule_hauteur_moyenne", 11);
        $form->setMax("cellule_fondcouleur_moyenne", 11);
        $form->setMax("cellule_bordure_moyenne", 200);
        $form->setMax("cellule_align_moyenne", 100);
        $form->setMax("cellule_fond_nbr", 20);
        $form->setMax("cellule_fontaille_nbr", 11);
        $form->setMax("cellule_hauteur_nbr", 11);
        $form->setMax("cellule_fondcouleur_nbr", 11);
        $form->setMax("cellule_bordure_nbr", 200);
        $form->setMax("cellule_align_nbr", 100);
        $form->setMax("cellule_numerique", 200);
        $form->setMax("cellule_total", 100);
        $form->setMax("cellule_moyenne", 100);
        $form->setMax("cellule_compteur", 100);
        $form->setMax("om_sql", 6);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sousetat', __('om_sousetat'));
        $form->setLib('om_collectivite', __('om_collectivite'));
        $form->setLib('id', __('id'));
        $form->setLib('libelle', __('libelle'));
        $form->setLib('actif', __('actif'));
        $form->setLib('titre', __('titre'));
        $form->setLib('titrehauteur', __('titrehauteur'));
        $form->setLib('titrefont', __('titrefont'));
        $form->setLib('titreattribut', __('titreattribut'));
        $form->setLib('titretaille', __('titretaille'));
        $form->setLib('titrebordure', __('titrebordure'));
        $form->setLib('titrealign', __('titrealign'));
        $form->setLib('titrefond', __('titrefond'));
        $form->setLib('titrefondcouleur', __('titrefondcouleur'));
        $form->setLib('titretextecouleur', __('titretextecouleur'));
        $form->setLib('intervalle_debut', __('intervalle_debut'));
        $form->setLib('intervalle_fin', __('intervalle_fin'));
        $form->setLib('entete_flag', __('entete_flag'));
        $form->setLib('entete_fond', __('entete_fond'));
        $form->setLib('entete_orientation', __('entete_orientation'));
        $form->setLib('entete_hauteur', __('entete_hauteur'));
        $form->setLib('entetecolone_bordure', __('entetecolone_bordure'));
        $form->setLib('entetecolone_align', __('entetecolone_align'));
        $form->setLib('entete_fondcouleur', __('entete_fondcouleur'));
        $form->setLib('entete_textecouleur', __('entete_textecouleur'));
        $form->setLib('tableau_largeur', __('tableau_largeur'));
        $form->setLib('tableau_bordure', __('tableau_bordure'));
        $form->setLib('tableau_fontaille', __('tableau_fontaille'));
        $form->setLib('bordure_couleur', __('bordure_couleur'));
        $form->setLib('se_fond1', __('se_fond1'));
        $form->setLib('se_fond2', __('se_fond2'));
        $form->setLib('cellule_fond', __('cellule_fond'));
        $form->setLib('cellule_hauteur', __('cellule_hauteur'));
        $form->setLib('cellule_largeur', __('cellule_largeur'));
        $form->setLib('cellule_bordure_un', __('cellule_bordure_un'));
        $form->setLib('cellule_bordure', __('cellule_bordure'));
        $form->setLib('cellule_align', __('cellule_align'));
        $form->setLib('cellule_fond_total', __('cellule_fond_total'));
        $form->setLib('cellule_fontaille_total', __('cellule_fontaille_total'));
        $form->setLib('cellule_hauteur_total', __('cellule_hauteur_total'));
        $form->setLib('cellule_fondcouleur_total', __('cellule_fondcouleur_total'));
        $form->setLib('cellule_bordure_total', __('cellule_bordure_total'));
        $form->setLib('cellule_align_total', __('cellule_align_total'));
        $form->setLib('cellule_fond_moyenne', __('cellule_fond_moyenne'));
        $form->setLib('cellule_fontaille_moyenne', __('cellule_fontaille_moyenne'));
        $form->setLib('cellule_hauteur_moyenne', __('cellule_hauteur_moyenne'));
        $form->setLib('cellule_fondcouleur_moyenne', __('cellule_fondcouleur_moyenne'));
        $form->setLib('cellule_bordure_moyenne', __('cellule_bordure_moyenne'));
        $form->setLib('cellule_align_moyenne', __('cellule_align_moyenne'));
        $form->setLib('cellule_fond_nbr', __('cellule_fond_nbr'));
        $form->setLib('cellule_fontaille_nbr', __('cellule_fontaille_nbr'));
        $form->setLib('cellule_hauteur_nbr', __('cellule_hauteur_nbr'));
        $form->setLib('cellule_fondcouleur_nbr', __('cellule_fondcouleur_nbr'));
        $form->setLib('cellule_bordure_nbr', __('cellule_bordure_nbr'));
        $form->setLib('cellule_align_nbr', __('cellule_align_nbr'));
        $form->setLib('cellule_numerique', __('cellule_numerique'));
        $form->setLib('cellule_total', __('cellule_total'));
        $form->setLib('cellule_moyenne', __('cellule_moyenne'));
        $form->setLib('cellule_compteur', __('cellule_compteur'));
        $form->setLib('om_sql', __('om_sql'));
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
