<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_sig_map.class.php 4348 2018-07-20 16:49:26Z softime $
 */

if (file_exists("../gen/obj/om_sig_map.class.php")) {
    require_once "../gen/obj/om_sig_map.class.php";
} else {
    require_once PATH_OPENMAIRIE."gen/obj/om_sig_map.class.php";
}

/**
 *
 */
class om_sig_map_core extends om_sig_map_gen {

    /**
     * On active les nouvelles actions sur cette classe.
     */
    var $activate_class_action = true;

    /**
     *
     * @return array
     */
    function get_var_sql_forminc__champs() {
        return array(
            "om_sig_map",
            "id",
            "om_collectivite",
            "libelle",
            "actif",
            "util_idx",
            "util_reqmo",
            "util_recherche",
            "source_flux",
            "zoom",
            "fond_osm",
            "fond_bing",
            "fond_sat",
            "layer_info",
            "fond_default",
            "om_sig_extent",
            "restrict_extent",
            "point_centrage",
            "projection_externe",
            "url",
            "om_sql",
            "sld_marqueur",
            "sld_data",
            "retour",
        );
    }

    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        $crud = $this->get_action_crud($maj);

        // MODE AJOUTER
        if ($maj == 0 || $crud == 'create') {
            $form->setType('projection_externe','select');
            $form->setType('om_sig_extent','autocomplete');
            if($this->retourformulaire=='') {
                $form->setType('sld_marqueur','upload');
                $form->setType('sld_data','upload');
            } else {
                $form->setType('sld_marqueur','upload2');
                $form->setType('sld_data','upload2');
            }
            $form->setType("fond_default", "select");
        }
        // MDOE MODIFIER
        if ($maj == 1 || $crud == 'update') {
            $form->setType('projection_externe','select');
            $form->setType('om_sig_extent','autocomplete');
            if($this->retourformulaire=='') {
                $form->setType('sld_marqueur','upload');
                $form->setType('sld_data','upload');
            } else {
                $form->setType('sld_marqueur','upload2');
                $form->setType('sld_data','upload2');
            }
            $form->setType("fond_default", "select");
        }
        // MODE SUPPRIMER
        if ($maj == 2 || $crud == 'delete') {
            $form->setType('sld_marqueur','hiddenstatic');
            $form->setType('sld_data','hiddenstatic');
            $form->setType('sld_marqueur','filestatic');
            $form->setType('sld_data','filestatic');
            $form->setType("fond_default", "selectstatic");
        }
        // MODE CONSULTER
        if ($maj == 3 || $crud == 'read') {
            $form->setType('sld_marqueur','hiddenstatic');
            $form->setType('sld_data','hiddenstatic');
            $form->setType('sld_marqueur','file');
            $form->setType('sld_data','file');
            $form->setType("fond_default", "selectstatic");
        }
    }

    /**
     * Methode verifier
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        parent::verifier($val);
        // On verifie si le champ n'est pas vide
        if ($this->valF['id'] == "") {
            $this->correct = false;
            $this->addToMessage(__("Le champ")." ".__("id")." ".__("est obligatoire"));
        } else {
            // On verifie si il y a un autre id 'actif' pour la collectivite
            if ($this->valF['actif'] == true) {
                if ($this->getParameter("maj") == 0) {
                    //
                    $this->verifieractif("]", $val);
                } else {
                    //
                    $this->verifieractif($val[$this->clePrimaire], $val);
                }
            }
        }
        switch ($this->valF["fond_default"]) {
            case null:
                break;
            case "osm":
                if ($this->valF['fond_osm']!=true) {
                    $this->addToMessage(__("Le fond osm ne peut être choisi par défaut"));
                    $this->correct = false;
                }
                break;
            case "bing":
                if ($this->valF['fond_bing']!=true) {
                    $this->addToMessage(__("Le fond bing ne peut être choisi par défaut"));
                    $this->correct = false;
                }
                break;
            case "sat":
                if ($this->valF['fond_sat']!=true) {
                    $this->addToMessage(__("Le fond sat ne peut être choisi par défaut"));
                    $this->correct = false;
                }
                break;
            default:
                $sql="";
                if ($this->valF["om_sig_map"] != "") {
                    $sql=$this->valF["om_sig_map"];
                    if ($this->valF["source_flux"] != "")
                        $sql.=",".$this->valF["source_flux"];

                } else {
                    if ($this->valF["source_flux"] != "")
                        $sql=$this->valF["source_flux"];
                }
                $sql="SELECT count(*) FROM (SELECT DISTINCT om_sig_map_flux FROM ".DB_PREFIXE."om_sig_map_flux where om_sig_map in (".$sql."))  a where om_sig_map_flux=".$this->valF["fond_default"];
                $nb= $this->f->db->getOne($sql);
                if ($nb==0) {
                    $this->addToMessage(__("Ce flux ne peut être choisi par défaut"));
                    $this->correct = false;
                }
                break;
        }
    }

    /**
     *
     */
    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('om_sig_map',4);
        $form->setTaille('om_collectivite',4);
        $form->setTaille('id',20);
        $form->setTaille('libelle',50);
        $form->setTaille('zoom',3);
        $form->setTaille('fond_osm',1);
        $form->setTaille('fond_bing',1);
        $form->setTaille('fond_sat',1);
        $form->setTaille('etendue',60);
        $form->setTaille('projection_externe',20);
        $form->setTaille('retour',50);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj);
        $form->setMax('om_sig_map',4);
        $form->setMax('om_collectivite',4);
        $form->setMax('id',50);
        $form->setMax('libelle',50);
        $form->setMax('zoom',3);
        $form->setMax('fond_osm',1);
        $form->setMax('fond_bing',1);
        $form->setMax('fond_sat',1);
        $form->setMax('etendue',60);
        $form->setMax('projection_externe',60);
        $form->setMax('url',2);
        $form->setMax('retour',50);
    }

    /**
     *
     */
    function setOnchange(&$form,$maj) {
        parent::setOnchange($form,$maj);
        $form->setOnchange('zoom','VerifNum(this)');
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        parent::setSelect($form, $maj);
        //
        if($maj<2){
            // On définit une valeur par défaut surchargeable par le script
            // dyn/var_sig.inc pour la liste à choix de la projection externe
            $contenu_epsg = array(
                0 => array("", "EPSG:2154", "EPSG:27563", ),
                1 => array("choisir la projection", 'lambert93', 'lambertSud', ),
            );
            if (file_exists("../dyn/var_sig.inc")) {
                include "../dyn/var_sig.inc";
            }
            $form->setSelect("projection_externe", $contenu_epsg);
            //
            $params = array(
                "constraint" => array(
                    "extension" => ".sld"
                ),
            );
            $form->setSelect("sld_marqueur",$params);
        }// fin maj

        //
        if ($maj < 2) {
            $form->setSelect(
                "om_sig_extent",
                $this->get_widget_config("om_sig_extent", "autocomplete")
            );
        }
        if ($maj == 0) {
            $sql_fond_default=
                "SELECT  unnest(string_to_array('osm;bing;sat', ';'::text)) as code, ".
                " unnest(string_to_array('osm;bing;sat', ';'::text)) as lib ";

        } else {
             $sql_fond_default=
                "SELECT * FROM (SELECT  mf.om_sig_map_flux::text as code, mf.ol_map as libelle ".
                "FROM   ".DB_PREFIXE."om_sig_map_flux mf ".
                "JOIN   ".DB_PREFIXE."om_sig_flux fl ".
                "   ON mf.om_sig_flux = fl.om_sig_flux ".
                "WHERE  mf.baselayer IS TRUE AND mf.om_sig_map IN (SELECT distinct unnest(string_to_array(om_sig_map||';'||coalesce(source_flux,om_sig_map), ';'::text))::integer FROM ".DB_PREFIXE."om_sig_map WHERE om_sig_map=".$this->val[0].") ".
                "UNION ".
                "SELECT  unnest(string_to_array('osm;bing;sat', ';'::text)) as code, ".
                " unnest(string_to_array('osm;bing;sat', ';'::text)) as lib ) a order by code";

        }
        $sql_fond_default_by_id=
            "SELECT * from (SELECT  mf.om_sig_map_flux::text as code, mf.ol_map as libelle ".
            "FROM   ".DB_PREFIXE."om_sig_map_flux mf ".
            "JOIN   ".DB_PREFIXE."om_sig_flux fl ".
            "   ON mf.om_sig_flux = fl.om_sig_flux ".
            "WHERE  mf.baselayer IS TRUE ". // AND mf.om_sig_map IN (SELECT unnest(string_to_array(om_sig_map||';'||source_flux, ';'::text))::integer FROM ".DB_PREFIXE."om_sig_map WHERE om_sig_map=<idx>) ".
            "UNION ".
            "SELECT  unnest(string_to_array('osm;bing;sat', ';'::text)) as code, ".
            " unnest(string_to_array('osm;bing;sat', ';'::text)) as lib) a ".
            " WHERE code = '<idx>'";
        $this->init_select($form, $this->f->db, $maj, null, "fond_default", $sql_fond_default, $sql_fond_default_by_id, false);
    }

    /**
     * Configuration du widget de formulaire autocomplete du champ 'om_sig_extent'.
     *
     * @return array
     */
    function get_widget_config__om_sig_extent__autocomplete() {
        return array(
            "obj" => "om_sig_extent",
            "table" => "om_sig_extent",
            "droit_ajout" => false,
            "criteres" => array(
                "om_sig_extent.nom" => __("nom")
            ),
            "jointures" => array(),
            "identifiant" => "om_sig_extent.om_sig_extent",
            "libelle" => array(
                "om_sig_extent.nom"
            ),
        );
    }

    /**
     *
     */
    function setGroupe (&$form, $maj) {

        $form->setGroupe('id','D');
        $form->setGroupe('libelle','G');
        $form->setGroupe('actif','F');
        $form->setGroupe('util_idx','D');
        $form->setGroupe('util_reqmo','G');
        $form->setGroupe('util_recherche','F');

        $form->setGroupe('zoom','D');
        $form->setGroupe('fond_osm','G');
        $form->setGroupe('fond_bing','G');
        $form->setGroupe('fond_sat','G');
        $form->setGroupe('layer_info','F');

        $form->setGroupe('etendue','D');
        $form->setGroupe('projection_externe','F');

        $form->setGroupe('sld_marqueur','D');
        $form->setGroupe('sld_data','F');

    }

    /**
     *
     */
    function setRegroupe (&$form, $maj) {

        $form->setRegroupe('id','D',' '.__('titre').' ', "collapsible");
        $form->setRegroupe('libelle','G','');
        $form->setRegroupe('actif','F','');

        $form->setRegroupe('util_idx','D',' '.__('Cas d\'utilisation').' ', "collapsible");
        $form->setRegroupe('util_reqmo','G','');
        $form->setRegroupe('util_recherche','G','');
        $form->setRegroupe('source_flux','F','');

        $form->setRegroupe('zoom','D',' '.__('fond').' ', "collapsible");
        $form->setRegroupe('fond_osm','G','');
        $form->setRegroupe('fond_bing','G','');
        $form->setRegroupe('fond_sat','G','');
        $form->setRegroupe('layer_info','G','');
        $form->setRegroupe('fond_default','F','');


        $form->setRegroupe('om_sig_extent','D',' '.__('Paramètres cartographiques').' ', "collapsible");
        $form->setRegroupe('restrict_extent','G','');
        $form->setRegroupe('point_centrage','G','');
        $form->setRegroupe('projection_externe','F','');

        $form->setRegroupe('url','D',' '.__('marqueurs').' ', "collapsible");
        $form->setRegroupe('om_sql','F','');

        $form->setRegroupe('sld_marqueur','D',' '.__('SLD').' ', "collapsible");
        $form->setRegroupe('sld_data','F','');

      }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('util_idx', __('à partir d\'un enregistrement donné : '));
        $form->setLib('util_recherche', __('d\'une recherche : '));
        $form->setLib('util_reqmo', __('d\'une requète mémorisée : '));
        $form->setLib('source_flux', __('Flux en provedance de la carte : '));
        $form->setLib('fond_default', __('Fond par défaut : '));
        $form->setLib('fond_osm', __('osm : '));
        $form->setLib('fond_bing', __('bing : '));
        $form->setLib('fond_sat', __('sat : '));
        $form->setLib('om_sig_extent', __('étendue'));
        $form->setLib('restrict_extent', __('Restreindre la navigation sur l\'étendue'));
        $form->setLib('point_centrage', __('Centrer sur le point (si différent du centre de l\'étendue)'));
        $form->setLib('projection_externe', __('projection'));
        $form->setLib('url', __('url'));
        $form->setLib('om_sql', __('requete sql'));
        $form->setLib('sld_marqueur', __('Fichier SLD pour les marqueurs'));
        $form->setLib('sld_data', __('Fichier SLD pour les données (om_sig_map_comp)'));
    }

    /**
     *
     */
    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        parent::setVal($form, $maj, $validation);
        //
        $this->maj=$maj;
    }

    /**
     *
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        parent::setValsousformulaire($form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire);
        //
        $this->maj=$maj;
    }

    /**
     * verification sur existence d un etat deja actif pour la collectivite
     */
    function verifieractif($id, $val) {
        $sql = "select om_sig_map from ".DB_PREFIXE."om_sig_map where id ='".$val['id']."'";
        $sql.= " and om_collectivite ='".$val['om_collectivite']."'";
        $sql.= " and actif =true";
        $sql.= " and ( 1<>1";
        if ($val['util_idx']=="Oui") $sql.= " OR util_idx IS TRUE";
        if ($val['util_reqmo']=="Oui") $sql.= " OR util_reqmo IS TRUE";
        if ($val['util_recherche']=="Oui") $sql.= " OR util_recherche IS TRUE";
        $sql.= " )";
        if($id!=']')
            $sql.=" and  om_sig_map !='".$id."'";
        $res = $this->f->db->query($sql);
        $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->f->isDatabaseError($res);
        $nbligne=$res->numrows();
        if ($nbligne>0){
           $this->addToMessage($nbligne." ".__("sig_map")." ".__("existant").
           " ".__("actif")." ! ".__("vous ne pouvez avoir qu un sig_map")." '".
           $val['id']."' ".__("actif")."  ".__("par collectivite et par cas d utilisation"));
           $this->correct=False;
        }
    }

}
