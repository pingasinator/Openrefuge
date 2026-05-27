<?php
/**
 * Ce script contient la définition de la classe 'om_map'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_map.class.php 4348 2018-07-20 16:49:26Z softime $
 */

/**
 *
 */
if (defined("PATH_OPENMAIRIE") !== true) {
    /**
     * @ignore
     */
    define("PATH_OPENMAIRIE", "");
}
require_once PATH_OPENMAIRIE."om_base.class.php";

/**
 * Définition de la classe 'om_map'.
 */
class om_map extends om_base {

    /**
     * Projection base
     * @var
     */
    var $defBaseProjection;

    /**
     * Projection display
     * @var
     */
    var $defDisplayProjection;

    /**
     * paramètre
     * @var
     */
    var $obj;

    /**
     * paramètre
     * @var
     */
    var $idx;

    /**
     * paramètre
     * @var
     */
    var $sql_lst_idx;

    /**
     * paramètre
     * @var
     */
    var $idx_sel;

    /**
     * paramètre
     * @var
     */
    var $popup;

    /**
     * paramètre
     * @var
     */
    var $seli;

    /**
     * paramètre
     * @var
     */
    var $etendue;

    /**
     * paramètre
     * @var
     */
    var $reqmo;

    /**
     * paramètre
     * @var
     */
    var $premier;

    /**
     * paramètre
     * @var
     */
    var $tricol;

    /**
     * paramètre
     * @var
     */
    var $advs_id;

    /**
     * paramètre
     * @var
     */
    var $valide;

    /**
     * paramètre
     * @var
     */
    var $style;

    /**
     * paramètre
     * @var
     */
    var $onglet;

    /**
     * paramètre
     * @var
     */
    var $type_utilisation = '';

    /**
     * Gestion de l'affichage
     * @var array
     */
    var $affichageZones = array();

    /**
     * Gestion de l'enregistrement
     * true: enregistrement de l'ensemble des champs géométriques ;
     * false: enregistrement un par un des champs géométriques (par défaut)
     * @var mixed
     */
    var $recordMultiComp;

    /**
     * 1 (par défaut): via form_sig;
     * 2 retour des valeurs dans des champs fournis dans le tableau recordFields
     * @var
     */
    var $recordMode;

    /**
     * listes des champs retour (même index que comp)
     * @var array
     */
    var $recordFields = array();

    /**
     * om_sig_map
     * @var
     */
    var $sm_titre;

    /**
     * om_sig_map
     * @var
     */
    var $sm_source_flux;

    /**
     * om_sig_map
     * @var
     */
    var $sm_zoom;

    /**
     * om_sig_map
     * @var
     */
    var $sm_fond_sat;

    /**
     * om_sig_map
     * @var
     */
    var $sm_fond_osm;

    /**
     * om_sig_map
     * @var
     */
    var $sm_fond_bing;

    /**
     * om_sig_map
     * @var
     */
    var $sm_layer_info;

    /**
     * om_sig_map
     * @var
     */
    var $sm_fond_default;

    /**
     * om_sig_map
     * @var
     */
    var $sm_projection_externe;

    /**
     * om_sig_map
     * @var
     */
    var $sm_retour;

    /**
     * om_sig_map
     * @var
     */
    var $om_sig_map;

    /**
     * om_sig_map
     * @var
     */
    var $sm_url;

    /**
     * om_sig_map
     * @var
     */
    var $sm_om_sql;

    /**
     * om_sig_map
     * @var
     */
    var $sm_om_sql_idx;

    /**
     * om_sig_map
     * @var
     */
    var $sm_restrict_extent;

    /**
     * om_sig_map
     * @var
     */
    var $sm_sld_marqueur;

    /**
     * om_sig_map
     * @var
     */
    var $sm_sld_data;

    /**
     * om_sig_map
     * @var
     */
    var $sm_point_centrage;

    /**
     * om_sig_map
     * @var
     */
    var $sm_point_centrage_x;

    /**
     * om_sig_map
     * @var
     */
    var $sm_point_centrage_y;

    /**
     * champs geom
     * @var array
     */
    var $cg_obj_class = array();

    /**
     * champs geom
     * @var array
     */
    var $cg_maj = array();

    /**
     * champs geom
     * @var array
     */
    var $cg_table = array();

    /**
     * champs geom
     * @var array
     */
    var $cg_champ_idx = array();

    /**
     * champs geom
     * @var array
     */
    var $cg_champ = array();

    /**
     * champs geom
     * @var array
     */
    var $cg_geometrie = array();

    /**
     * champs geom
     * @var array
     */
    var $cg_lib_geometrie = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_om_sig_map_flux = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_ol_map = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_visibility = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_panier = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_pa_nom = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_pa_layer = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_pa_attribut = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_pa_encaps = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_pa_sql = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_pa_type_geometrie = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_sql_filter = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_filter = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_baselayer = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_singletile = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_m_maxzoomlevel = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_libelle = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_attribution = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_id = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_chemin = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_couches = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_cache_type = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_cache_gfi_chemin = array();

    /**
     * champs flux
     * @var array
     */
    var $fl_w_cache_gfi_couches = array();

    /**
     * champ pour fonds de carte externes (Google)
     * @var
     */
    var $pebl_http_google;

    /**
     * champ pour fonds de carte externes (Bing)
     * @var
     */
    var $pebl_cle_bing;

    /**
     * champ pour fonds de carte externes (Google)
     * @var
     */
    var $pebl_cle_google;

    /**
     * champ pour fonds de carte externes (OSM)
     * @var
     */
    var $pebl_zoom_osm_maj;

    /**
     * champ pour fonds de carte externes (OSM)
     * @var
     */
    var $pebl_zoom_osm;

    /**
     * champ pour fonds de carte externes (Google)
     * @var
     */
    var $pebl_zoom_sat_maj;

    /**
     * champ pour fonds de carte externes (OSM, Bing, Google)
     * @var
     */
    var $pebl_zoom_sa;

    /**
     * champ pour fonds de carte externes (Bing)
     * @var
     */
    var $pebl_zoom_bing_maj;

    /**
     * champ pour fonds de carte externes (Bing)
     * @var
     */
    var $pebl_zoom_bing;

    /**
     * paramètres de style pour la couche marqueur
     * @var string
     */
    var $img_maj = "../lib/om-assets/img/punaise_sig.png";

    /**
     * paramètres de style pour la couche marqueur
     * @var string
     */
    var $img_maj_hover = "../lib/om-assets/img/punaise_hover.png";

    /**
     * paramètres de style pour la couche marqueur
     * @var string
     */
    var $img_consult = "../lib/om-assets/img/punaise_point.png";

    /**
     * paramètres de style pour la couche marqueur
     * @var string
     */
    var $img_consult_hover = "../lib/om-assets/img/punaise_point_hover.png";

    /**
     * paramètres de style pour la couche marqueur
     * @var integer
     */
    var $img_w = 14;

    /**
     * paramètres de style pour la couche marqueur
     * @var integer
     */
    var $img_h = 32;

    /**
     * paramètres de style pour la couche marqueur
     * multiplicateur hauteur et largeur image cliquee
     * @var string
     */
    var $img_click = "1.3";

    /**
     * gestion des paniers
     * @var array
     */
    var $cart_type = array(
        "point" => false,
        "linestring" => false,
        "polygon" => false
    );

    /**
     * Tableau de la barre du menu d'édition menu (id html, false)
     * @var array
     */
    var $edit_toolbar = array(
        "#map-edit-nav" => false,
        "#map-edit-draw-point" => false,
        "#map-edit-draw-line" => false,
        "#map-edit-draw-polygon" => false,
        "#map-edit-draw-regular" => false,
        "#map-edit-draw-regular-nb" => false,
        "#map-edit-draw-modify" => false,
        "#map-edit-draw-select" => false,
        "#map-edit-draw-erase" => false,
        "#map-edit-cart" => false,
        "#map-edit-get-cart" => false,
        "#map-edit-draw-record" => false,
        "#map-edit-draw-delete" => false,
        "#map-edit-draw-close" => false
    );

    /**
     * Actions disponibles sur la mise à jour d'un champ geom.
     * @var array
     */
    var $form_champ_maj = array(
        "1" => "Modifier",
        "2" => "Supprimer"
    );

    /**
     * Constructeur.
     *
     * @param string $obj
     * @param array $options
     */
    function __construct($obj, $options) {
        // Initialisation de la classe 'application'.
        $this->init_om_application();
        // Logger
        $this->addToLog(__METHOD__."()", EXTRA_VERBOSE_MODE);
        //
        if ($obj == '') {
            $class = "error";
            $message = __("Obj obligatoire");
            $this->f->addToMessage($class, $message);
            $this->f->setFlag(null);
            $this->f->display();
            die();
        }
        $this->idx_sel = -1;
        $this->obj=$obj;
        if (isset($options['idx'])) { $this->idx=$options['idx']; } else { $this->idx=''; }
        if (isset($options['popup'])) { $this->popup=$options['popup']; } else { $this->popup=0; }
        if (isset($options['seli'])) { $this->seli=$options['seli']; } else { $this->seli=0; }
        if (isset($options['etendue'])) { $this->etendue=$options['etendue']; } else { $this->etendue=''; }
        if (isset($options['reqmo'])) { $this->reqmo=$options['reqmo']; } else { $this->reqmo=''; }
        if (isset($options['premier'])) { $this->premier=$options['premier']; } else { $this->premier=0; }
        if ($this->premier=='') $this->premier=0;
        if (isset($options['tricol'])) { $this->tricol=$options['tricol']; } else { $this->tricol=''; }
        if (isset($options['advs_id'])) { $this->advs_id=$options['advs_id']; } else { $this->advs_id=''; }
        if (isset($options['valide'])) { $this->valide=$options['valide']; } else { $this->valide=''; }
        if (isset($options['style'])) { $this->style=$options['style']; } else { $this->style=''; }
        if (isset($options['onglet'])) { $this->onglet=$options['onglet']; } else { $this->onglet=''; }
        if (isset($options['recordMultiComp'])) { $this->recordMultiComp=$options['recordMultiComp']; } else { $this->recordMultiComp=true; }
        if (isset($options['recordMode'])) { $this->recordMode=$options['recordMode']; } else { $this->recordMode=1; }
        if (isset($options['recordFields'])) { $this->recordFields=$options['recordFields']; }
        if ($this->reqmo <> '') { $this->type_utilisation='reqmo'; }
        else if ($this->idx <> '') { $this->type_utilisation = 'idx'; $this->idx_sel = $this->idx; }
        else { $this->type_utilisation = 'recherche'; }
        $this->addToLog(__METHOD__."() type ".$this->type_utilisation, EXTRA_VERBOSE_MODE);

        $this->sql_lst_idx='(SELECT NULL)';

        // affichageZones:
        //  zones: titre, edit, tools, infos, layers
        //  valeurs: 0 non générer, 1 dans barre de menu, 2 isolé
        $this->affichageZones['titre']=1;
        $this->affichageZones['edit']=1;
        $this->affichageZones['tools']=1;
        $this->affichageZones['infos']=1;
        $this->affichageZones['print']=1;
        $this->affichageZones['layers']=2;
        $this->affichageZones['navigation']=2;
        $this->affichageZones['menubar']=1;

        //
        if (file_exists("../dyn/var_sig.inc")) {
            include "../dyn/var_sig.inc";
        }
        if (isset($baseProjection)) { $this->defBaseProjection=$baseProjection; } else { $this->defBaseProjection = "3857"; }
        if (isset($displayProjection)) { $this->defDisplayProjection=$displayProjection; } else { $this->defDisplayProjection = "4326"; }
        if (isset($img_maj)) $this->img_maj=$img_maj;
        if (isset($img_maj_hover)) $this->img_maj_hover=$img_maj_hover;
        if (isset($img_consult)) $this->img_consult=$img_consult;
        if (isset($img_consult_hover)) $this->img_consult_hover=$img_consult_hover;
        if (isset($img_w)) $this->img_w=$img_w;
        if (isset($img_h)) $this->img_h=$img_h;
        if (isset($img_click)) $this->img_click=$img_click;
    }

    /**
     * Récupération du paramétrage de l'objet dans les tables om_sig_map et
     * om_sig_map_comp. Préalable à toute utilisation de la classe.
     *
     * @return
     */
    function recupOmSigMap() {
        //
        $sql_template = 'SELECT m.om_sig_map, m.om_collectivite, m.id, m.libelle, m.actif, m.zoom, m.fond_osm, m.fond_bing, m.fond_sat, m.layer_info, m.projection_externe, m.url, m.om_sql, m.retour, m.util_idx, m.util_reqmo, m.util_recherche, m.source_flux, m.fond_default, m.om_sig_extent, m.restrict_extent, m.sld_marqueur, m.sld_data, ST_AsGeoJSON(ST_Transform(m.point_centrage, %2$s)) as point_centrage, ST_X(ST_Transform(m.point_centrage, %2$s)) as point_centrage_x, ST_Y(ST_Transform(m.point_centrage, %2$s)) as point_centrage_y, e.extent FROM %1$som_sig_map m JOIN %1$som_sig_extent e ON m.om_sig_extent=e.om_sig_extent WHERE m.actif IS TRUE %3$s';
        //
        if ($this->obj == "om_sig_map") {
            $sql = sprintf(
                $sql_template,
                DB_PREFIXE,
                $this->defBaseProjection,
                ' AND m.om_sig_map='.$this->idx
            );
        } else {
            $sql = sprintf(
                $sql_template,
                DB_PREFIXE,
                $this->defBaseProjection,
                ' AND m.id=\''.$this->obj.'\''
            );
            if ($this->type_utilisation == "idx") {
                $sql .= " AND m.util_idx IS TRUE";
            } else if ($this->type_utilisation == "reqmo") {
                $sql .= " AND m.util_reqmo IS TRUE";
            } else if ($this->type_utilisation == "recherche") {
                $sql .= " AND m.util_recherche IS TRUE";
            }
        }
        $res = $this->f->db->query($sql);
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sql."\");",
            VERBOSE_MODE
        );
        $this->f->isDatabaseError($res);

        //
        if ($res->numRows()<>1) {
            $class = "error";
            $message = __("L'objet est invalide.");
            $this->f->addToMessage($class, $message);
            $this->f->setFlag(null);
            $this->f->display();
            die();
        }

        //
        while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->sm_titre = $row['libelle'];
            $this->sm_zoom = $row['zoom'];
            $this->sm_source_flux = $row['source_flux'];
            $this->sm_fond_sat = $row['fond_sat'];
            $this->sm_fond_osm = $row['fond_osm'];
            $this->sm_fond_bing = $row['fond_bing'];
            $this->sm_layer_info = $row['layer_info'];
            $this->sm_fond_default = $row['fond_default'];
            $this->sm_projection_externe = $row['projection_externe'];
            if ($this->obj == 'om_sig_map') {
                $this->sm_retour = OM_ROUTE_FORM."&obj=om_sig_map&idx=";
                $this->sm_url = OM_ROUTE_FORM."&obj=om_sig_map&idx= ";
                $this->sm_om_sql = sprintf(
                    'SELECT st_astext(point_centrage) as geom, libelle as titre, libelle||\' (\'||om_sig_map||\')\' as description, om_sig_map as idx FROM %1$som_sig_map',
                    DB_PREFIXE
                );
            } else {
                $this->sm_retour = $row['retour'];
                $this->sm_url = $row['url'];
                $this->sm_om_sql = $row['om_sql'];
            }
            $this->om_sig_map = $row['om_sig_map'];
            if ($this->etendue == '') {
                $this->etendue = $row['extent'];
            }
            $this->sm_restrict_extent = $row['restrict_extent'];
            $this->sm_sld_marqueur = $row['sld_marqueur'];
            $this->sm_sld_data = $row['sld_data'];
            $this->sm_point_centrage = $row['point_centrage'];
            $this->sm_point_centrage_x = $row['point_centrage_x'];
            $this->sm_point_centrage_y = $row['point_centrage_y'];
        }

        //
        if ($this->obj == 'om_sig_map') {
            $this->cg_obj_class[] = 'om_sig_map';
            if ($this->f->isAccredited('om_sig_map')
                || $this->f->isAccredited('om_sig_map'."_modifier")) {
                $this->cg_maj[] = 't';
            } else {
                $this->cg_maj[] = '';
            }
            $this->cg_table[] = 'om_sig_map';
            $this->cg_champ_idx[] = 'om_sig_map';
            $this->cg_champ[] = 'point_centrage';
            $this->cg_geometrie[] = 'point';
            $this->cg_lib_geometrie[] = 'point';
        } else {
            //
            $sql = sprintf(
                'SELECT * FROM %1$som_sig_map_comp WHERE actif IS TRUE AND om_sig_map=%2$s ORDER BY ordre',
                DB_PREFIXE,
                $this->om_sig_map
            );
            $res = $this->f->db->query($sql);
            $this->addToLog(
                __METHOD__."(): db->query(\"".$sql."\");",
                VERBOSE_MODE
            );
            $this->f->isDatabaseError($res);
            //
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                $this->cg_obj_class[] = $row['obj_class'];
                if ($this->f->isAccredited($row['obj_class'])
                    || $this->f->isAccredited($row['obj_class']."_modifier")) {
                    $this->cg_maj[] = $row['comp_maj'];
                } else {
                    $this->cg_maj[] = '';
                }
                $this->cg_table[] = $row['comp_table_update'];
                $this->cg_champ_idx[] = $row['comp_champ_idx'];
                $this->cg_champ[] = $row['comp_champ'];
                $this->cg_geometrie[] = $row['type_geometrie'];
                $this->cg_lib_geometrie[] = $row['libelle'];
            }
        }
    }

    /**
     * Génère un tableau (idx, sql_lst_idx) correspondant aux données
     * idx/Reqmo/Recherche.
     *
     * @param mixed $idx
     * @param mixed $seli
     *
     * @return
     */
    function getSelectRestrict($idx, $seli) {
        $sql='';
        if ($this->type_utilisation=='reqmo') {
            $sql= "(SELECT NULL)";
        } elseif ($this->type_utilisation=='recherche') {
            // Verification des parametres
            //$obj=$this->cg_obj_class[$seli];
            $obj=$this->obj;
            $advs_id = $this->advs_id;
            $standard_script_path = "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
            $core_script_path = PATH_OPENMAIRIE."sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
            $gen_script_path = "../gen/sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
            if (strpos($obj, "/") !== false
                || (file_exists($standard_script_path) === false
                    && file_exists($core_script_path) === false
                    && file_exists($gen_script_path) === false)) {
                $sql= "";
                /*$class = "error";
                $message = __("L'objet est invalide.");
                $this->f->addToMessage($class, $message);
                $this->f->setFlag(null);
                $this->f->display();
                die();*/
            } else {
                // Liste des options
                if (!isset($options)) {
                    $options = array();
                }
                // Dictionnaire des actions
                // Declaration du dictionnaire
                $tab_actions = array(
                    'corner' => array(),
                    'left' => array(),
                    'content' => array(),
                    'specific_content' => array(),
                );
                // Voir le fichier dyn/form.get.specific.inc.php pour plus d'informations
                $extra_parameters = array();
                // surcharge globale
                if (file_exists('../dyn/tab.inc.php')) {
                    require_once '../dyn/tab.inc.php';
                }

                // Inclusion du script [sql/<OM_DB_PHPTYPE>/<OBJ>.inc.php]
                $custom_script_path = $this->f->get_custom("tab", $obj);
                if ($custom_script_path !== null) {
                    require_once $custom_script_path;
                } elseif (file_exists($standard_script_path) === false
                    && file_exists($core_script_path) === true) {
                    require_once $core_script_path;
                } elseif (file_exists($standard_script_path) === false
                    && file_exists($gen_script_path) === true) {
                    require_once $gen_script_path;
                } elseif (file_exists($standard_script_path) === true) {
                    require_once $standard_script_path;
                }

                $champAffiche = array( $champAffiche[0] );
                $this->f->isAuthorized(array($obj."_tab", $obj), "OR");
                if (!isset($om_validite) or $om_validite != true) {
                    $om_validite = false;
                }
                $tb = $this->f->get_inst__om_table(array(
                    "aff" => OM_ROUTE_MAP."&mode=get_geojson_datas",
                    "table" => $table,
                    "serie" => $serie,
                    "champAffiche" => $champAffiche,
                    "champRecherche" => $champRecherche,
                    "tri" => $tri,
                    "selection" => $selection,
                    "edition" => $edition,
                    "options" => $options,
                    "advs_id" => $this->advs_id,
                    "om_validite" => $om_validite,
                ));
                $params = array(
                    "obj" => $obj,
                    "premier" => $this->premier,
                    "tricol" => $this->tricol,
                    "valide" => $this->valide,
                    "advs_id" => $this->advs_id,
                );
                // Ajout de paramètre spécifique
                $params = array_merge($params, $extra_parameters);
                //
                // Enclenchement de la tamporisation de sortie
                ob_start();
                $tb->display($params, $tab_actions, $this->f->db, "tab", false);
                $return = ob_get_clean();
                $sql= "(".$tb->sql.")";
                $tb->__destruct();
            }
        }
        $tab_SelectRestrict = array(
            'idx' => $idx,
            'sql_lst_idx' => $sql
        );
        return $tab_SelectRestrict;
    }

    /**
     * Génère un tableau GeoJson correspondant aux données idx/Reqmo/Recherche.
     *
     * @param string $idx
     * @param string $seli
     *
     * @return
     */
    function getGeoJsonDatas($idx, $seli) {
        //
        $tab_SelectRestrict= $this->getSelectRestrict($idx, $seli);
        //
        $sql = sprintf(
            'SELECT %4$s AS idx, ST_AsGeoJSON(ST_Transform(%5$s, %3$s)) AS geom FROM %1$s%2$s WHERE %5$s IS NOT NULL',
            DB_PREFIXE,
            $this->cg_table[$seli],
            $this->defDisplayProjection,
            $this->cg_champ_idx[$seli],
            $this->cg_champ[$seli]
        );
        if ($tab_SelectRestrict['idx'] <> '') {
            $sql .= " AND ".$this->cg_champ_idx[$seli]." = '".$tab_SelectRestrict['idx']."'";
            $this->idx_sel = $tab_SelectRestrict['idx'];
        } else {
            $sql .= " AND ".$this->cg_champ_idx[$seli]." IN ".$tab_SelectRestrict['sql_lst_idx'];
            if ($seli == 0) {
                $this->sql_lst_idx = "(".$tab_SelectRestrict['sql_lst_idx'].")";
            }
        }
        $res = $this->f->db->query($sql);
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sql."\");",
            VERBOSE_MODE
        );
        $this->f->isDatabaseError($res);
        //
        if ($res->numRows() > 0) {
            $tab_GeoJson = array();
            array_push($tab_GeoJson, '{"type": "FeatureCollection","features": [');
            $cc = 0;
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                $cc += 1;
                $lig = '{'.
                        '"type": "'.$this->cg_table[$seli].'", '.
                        '"id": "'.$row['idx'].'", '.
                        '"properties": { '.
                            '"obj": "'.$this->obj.'", '.
                            '"table": "'.$this->cg_table[$seli].'", '.
                            '"obj_class": "'.$this->cg_obj_class[$seli].'", '.
                            '"lib_geometrie": "'.$this->cg_lib_geometrie[$seli].'", '.
                            '"table": "'.$this->cg_table[$seli].'", '.
                            '"champidx": "'.$this->cg_champ_idx[$seli].'", '.
                            '"idx": "'.$row['idx'].'", '.
                            '"maj": "'.$this->cg_maj[$seli].'", '.
                            '"seli": '.$seli.', '.
                            '"champ_geom": "'.$this->cg_champ[$seli].'"'.
                        '}, '.
                        '"geometry": '.$row['geom'].
                    '}';
                if ($cc > 1) {
                    $lig = ", ".$lig;
                }
                array_push($tab_GeoJson, $lig);
            }
            array_push($tab_GeoJson, ']}');
            return $tab_GeoJson;
        }
    }

    /**
     * Génère un tableau GeoJson correspondant au panier $cart (n de flux) avec
     * la liste des enregistrement $lst.
     *
     * @param mixed $cart
     * @param mixed $lst
     *
     * @return
     */
    function getGeoJsonCart($cart, $lst) {
        //
        if ($cart == '' || $lst == '') {
            return;
        }
        //
        $sql = $this->fl_m_pa_sql[$cart];
        $sql = str_replace("&lst", $lst, $sql);
        $sql = str_replace("&DB_PREFIXE", DB_PREFIXE, $sql);
        //
        $pos = strpos(strtolower($sql), 'st_astext');
        if ($pos === false) {
            $select = "a.geom, ".$this->defDisplayProjection."";
        } else {
            $select = "ST_GeomFromText(a.geom,".str_replace('EPSG:', '', $this->sm_projection_externe)."), ".$this->defDisplayProjection."";
        }
        $sql = sprintf(
            'SELECT ST_AsGeoJSON(ST_Transform(%1$s)) AS a_geom FROM (%2$s) a WHERE a.geom IS NOT NULL ORDER BY a_geom',
            $select,
            $sql
        );
        //
        $res = $this->f->db->query($sql);
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sql."\");",
            VERBOSE_MODE
        );
        $this->f->isDatabaseError($res);
        //
        if ($res->numRows() > 0) {
            $tab_GeoJson = array();
            array_push($tab_GeoJson, '{"type": "FeatureCollection","features": [');
            $cc = 0;
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                $cc += 1;
                $lig = '{'.
                        '"type": "'.$this->fl_m_pa_nom[$cart].'", '.
                        '"id": "'.$cc.'", '.
                        '"geometry": '.$row['a_geom'].
                    '}';
                if ($cc > 1) {
                    $lig = ", ".$lig;
                }
                array_push($tab_GeoJson, $lig);
            }
            array_push($tab_GeoJson, ']}');
            return $tab_GeoJson;
        }
    }

    /**
     * Génère un tableau GeoJson correspondant aux données idx/Reqmo/Recherche
     *
     * @param mixed $idx
     *
     * @return
     */
    function getGeoJsonMarkers($idx) {
        //
        if (count($this->cg_obj_class)>0) {
            $tab_SelectRestrict= $this->getSelectRestrict($idx, 0);
        } else {
            $tab_SelectRestrict = array(
                'idx' => $idx,
                'sql_lst_idx' => '(SELECT NULL)'
            );
        }
        if ($tab_SelectRestrict['idx'] == '') {
            $tab_SelectRestrict['idx'] = 'NULL';
        }
        if ($tab_SelectRestrict['sql_lst_idx'] == '') {
            $tab_SelectRestrict['sql_lst_idx'] = '(SELECT NULL)';
        }
        //
        $sql = $this->sm_om_sql;
        $sql = str_replace('&idx', $tab_SelectRestrict['idx'], $sql);
        $sql = str_replace('&lst_idx', $tab_SelectRestrict['sql_lst_idx'], $sql);
        $sql = str_replace('&DB_PREFIXE', DB_PREFIXE, $sql);
        //
        $pos = strpos(strtolower($sql), 'st_astext');
        if ($pos === false) {
            $select = "a.geom, ".$this->defDisplayProjection."";
        } else {
            $select = "ST_GeomFromText(a.geom,".str_replace('EPSG:', '', $this->sm_projection_externe)."), ".$this->defDisplayProjection."";
        }
        $sql = sprintf(
            'SELECT  ST_AsGeoJSON(ST_Transform(%1$s)) AS a_geom, a.titre::text AS a_titre, a.description::text AS a_description, a.idx AS a_idx, a.* FROM (%2$s) a WHERE a.geom IS NOT NULL ORDER BY a_geom',
            $select,
            $sql
        );
        //
        $res = $this->f->db->query($sql);
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sql."\");",
            VERBOSE_MODE
        );
        $this->f->isDatabaseError($res);
        //
        if ($res->numRows() > 0) {
            $tab_GeoJson = array();
            array_push($tab_GeoJson, '{"type": "FeatureCollection","features": [');
            $vals=array();
            $nLus=0;
            $nRows=$res->numRows();
            $bPremier=true;
            $sSep = '';
            $valGeomPrec='';

            for ($nLus = 0; $nLus <=$nRows;  $nLus++) {
                if ($nLus < $nRows) {
                    $row=& $res->fetchRow(DB_FETCHMODE_ASSOC);
                }
                if (($row['a_geom'] <> $valGeomPrec && $valGeomPrec <> '') || ($nLus == $nRows) ) {
                    $lig =
                        '{'.
                            '"type": "marker", '.
                            '"idx": "'.$vals['idx'].'", '.
                            '"properties": { '.
                                '"id": "'.$vals['idx'].'", '.
                                '"titre": "'.$vals['titre'].'", '.
                                '"description": "'.$vals['description'].'" ';
                    foreach ($vals as $k => $v) {
                        if ($k <> 'a_idx' && $k <> 'a_titre' && $k <> 'a_description' && $k <> 'a_geom' &&
                            $k <> 'idx' && $k <> 'titre' && $k <> 'description' && $k <> 'geom') {
                            $lig = $lig.
                                ', '.
                                '"'.$k.'": "'.$v.'" ';
                        }
                    }
                    $lig = $lig.'}, '.
                            '"geometry": '.$vals['geom'].
                        '}';
                    if ($bPremier == false) {
                        $lig=", ".$lig;
                    }
                    array_push($tab_GeoJson, $lig);
                    $sSep = '';
                    foreach ($vals as $k => $v) {
                        $vals[$k]="";
                    }
                    $bPremier = false;
                }
                if ($sSep == '') {
                    $vals['idx']= $row['a_idx'];
                    $vals['titre']= "<a href=javascript:map_popup('".$this->sm_url.$row['a_idx']."')>".$row['a_titre']."</a>";
                    $vals['description']= $sSep.$row['a_description'];
                    $vals['geom']=$row['a_geom'];
                    foreach ($row as $k => $v) {
                        if ($k <> 'a_idx' && $k <> 'a_titre' && $k <> 'a_description' && $k <> 'a_geom' && $k <> 'idx' && $k <> 'titre' && $k <> 'description' && $k <> 'geom') {
                            $vals[$k]= $sSep.$v;
                        }
                    }
                } else {
                    $vals['idx']= $vals['idx'].$sSep.$row['a_idx'];
                    $vals['titre']= $vals['titre'].$sSep."<a href=javascript:map_popup('".$this->sm_url.$row['a_idx']."')>".$row['a_titre']."</a>";
                    $vals['description']= $vals['description'].$sSep.$row['a_description'];
                    $vals['geom']=$row['a_geom'];
                    foreach ($row as $k => $v) {
                        if ($k <> 'a_idx' && $k <> 'a_titre' && $k <> 'a_description' && $k <> 'a_geom' && $k <> 'idx' && $k <> 'titre' && $k <> 'description' && $k <> 'geom') {
                            $vals[$k]= $vals[$k].$sSep.$v;
                        }
                    }
                }
                $sSep = '²';
                $valGeomPrec = $row['a_geom'];
            }
            array_push($tab_GeoJson, ']}');
            return $tab_GeoJson;
        }
        $this->sm_om_sql_idx = $sql;
    }

    /**
     * calcul des filtres pour les flux de type WMS (fl_m_filter)
     *
     * @param
     *
     * @return
     */
    function computeFilters($idx) {
        if (count($this->cg_obj_class)>0) {
            $tab_SelectRestrict= $this->getSelectRestrict($idx, 0);
        } else {
            $tab_SelectRestrict = array(
                'idx' => $idx,
                'sql_lst_idx' => '(SELECT NULL)'
            );
        }
        if ($tab_SelectRestrict['idx']=='') $tab_SelectRestrict['idx']='NULL';
        if ($tab_SelectRestrict['sql_lst_idx']=='') $tab_SelectRestrict['sql_lst_idx']='(SELECT NULL)';
        for ($i = 0; $i < count($this->fl_m_sql_filter); $i++) {
            $this->fl_m_filter[$i]=$this->fl_m_sql_filter[$i];
            $this->fl_m_filter[$i]=str_replace('&idx', $tab_SelectRestrict['idx'], $this->fl_m_filter[$i]);
            $this->fl_m_filter[$i]=str_replace('&lst_idx', $tab_SelectRestrict['sql_lst_idx'], $this->fl_m_filter[$i]);
            $this->fl_m_filter[$i]=str_replace('&DB_PREFIXE', DB_PREFIXE, $this->fl_m_filter[$i]);
            //$this->fl_m_filter[$i]=str_replace('²','"', $this->fl_m_filter[$i]);
            if ($this->fl_m_filter[$i]<>'') {
                $res = $this->f->db->query($this->fl_m_filter[$i]);
                $this->addToLog(
                    __METHOD__."(): db->query(\"".$this->fl_m_filter[$i]."\");",
                    VERBOSE_MODE
                );
                if ($this->f->isDatabaseError($res, true)) {
                    $class = "error";
                    $message = __("Filtre de flux - erreur sql de requète : ").$this->fl_m_filter[$i];
                    $this->f->addToMessage($class, $message);
                    $this->f->setFlag(null);
                    $this->f->display();
                    $this->fl_m_filter[$i] = '';
                } else {
                    if ($res->numRows()<>1) {
                        $class = "error";
                        $message = __("Filtre de flux - la requète ne donne pas qu'une seule ligne: ").$this->fl_m_filter[$i];
                        $this->f->addToMessage($class, $message);
                        $this->f->setFlag(null);
                        $this->f->display();
                        $this->fl_m_filter[$i] = '';
                    }
                    while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                        if (count($row) != 1) {
                            $class = "error";
                            $message = __("Filtre de flux - la requète ne donne pas qu'une seule colonne: ").$this->fl_m_filter[$i];
                            $this->f->addToMessage($class, $message);
                            $this->f->setFlag(null);
                            $this->f->display();
                            $this->fl_m_filter[$i] = '';
                        } else {
                            foreach ($row as $k => $v) {
                                $this->fl_m_filter[$i] = $v;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Récupération du paramétrage des flux associés à l'objet dans les tables
     * om_sig_map_flux et om_sig_map_flux
     *
     * @return void
     */
    function recupOmSigflux() {
        //
        $sql_liste = sprintf(
            'SELECT %1$s',
            $this->om_sig_map
        );
        if ($this->sm_source_flux > 0) {
            $sql_liste .= sprintf(
                ' UNION SELECT %2$s UNION SELECT source_flux FROM %1$som_sig_map WHERE om_sig_map=%2$s',
                DB_PREFIXE,
                $this->sm_source_flux
            );
        }
        //
        $sql =  sprintf(
            'SELECT m.om_sig_map_flux, m.ol_map, m.visibility, m.panier, m.pa_nom, m.pa_layer, m.pa_attribut, m.pa_encaps, m.pa_sql, m.pa_type_geometrie, m.sql_filter, m.baselayer, m.singletile, m.maxzoomlevel, w.libelle, w.id, w.attribution, w.chemin, w.couches, w.cache_type, w.cache_gfi_chemin, w.cache_gfi_couches FROM %1$som_sig_map_flux m, %1$som_sig_flux w WHERE w.om_sig_flux = m.om_sig_flux AND m.om_sig_map IN ( %2$s ) ORDER BY m.ordre',
            DB_PREFIXE,
            $sql_liste
        );
        $res = $this->f->db->query($sql);
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sql."\");",
            VERBOSE_MODE
        );
        $this->f->isDatabaseError($res);
        //
        while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->fl_om_sig_map_flux[] = $row['om_sig_map_flux'];
            $this->fl_m_ol_map[] = $row['ol_map'];
            $this->fl_m_visibility[] = $row['visibility'];
            $this->fl_m_panier[] = $row['panier'];
            $this->fl_m_pa_nom[] = $row['pa_nom'];
            $this->fl_m_pa_layer[] = $row['pa_layer'];
            $this->fl_m_pa_attribut[] = $row['pa_attribut'];
            $this->fl_m_pa_encaps[] = $row['pa_encaps'];
            $this->fl_m_pa_sql[] = $row['pa_sql'];
            $this->fl_m_pa_type_geometrie[] = $row['pa_type_geometrie'];
            $this->fl_m_sql_filter[] = $row['sql_filter'];
            $this->fl_m_filter[] = '';
            $this->fl_m_baselayer[] = $row['baselayer'];
            $this->fl_m_singletile[] = $row['singletile'];
            $this->fl_m_maxzoomlevel[] = $row['maxzoomlevel'];
            $this->fl_w_libelle[] = $row['libelle'];
            $this->fl_w_id[] = $row['id'];
            $this->fl_w_attribution[] = $row['attribution'];
            $this->fl_w_chemin[] = $row['chemin'];
            $this->fl_w_couches[] = $row['couches'];
            $this->fl_w_cache_type[] = $row['cache_type'];
            $this->fl_w_cache_gfi_chemin[] = $row['cache_gfi_chemin'];
            $this->fl_w_cache_gfi_couches[] = $row['cache_gfi_couches'];
            if ($row['panier'] == 't') {
                if ($row['pa_type_geometrie'] == 'point'
                    || $row['pa_type_geometrie'] == 'multipoint') {
                    //
                    $this->cart_type['point'] = true;
                }
                if ($row['pa_type_geometrie'] == 'linestring'
                    || $row['pa_type_geometrie'] == 'multilinestring') {
                    //
                    $this->cart_type['linestring'] = true;
                }
                if ($row['pa_type_geometrie'] == 'polygon'
                    || $row['pa_type_geometrie'] == 'multipolygon') {
                    //
                    $this->cart_type['polygon'] = true;
                }
            }
        }
    }

    /**
     * Initialisation des propriétés relatives aux fonds de carte externe,
     * ajout des librairies associées si nécessaire.
     *
     * @return void
     */
    function setParamsExternalBaseLayer() {
        if (file_exists("../dyn/var_sig.inc"))
            include ("../dyn/var_sig.inc");
        if (isset($cle_google))
            $this->pebl_cle_google = $cle_google;
        else
            $this->pebl_cle_google = "";
        if (isset($http_google))
            $this->pebl_http_google=$http_google;
        else
            $this->pebl_http_google="http://maps.google.com/maps/api/js?sensor=false";
        if (isset($cle_bing))
            $this->pebl_cle_bing =$cle_bing;
        else
            $this->pebl_cle_bing ='AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf';
        if (isset($zoom_osm_maj))
            $this->pebl_zoom_osm_maj=$zoom_osm_maj;
        else
            $this->pebl_zoom_osm_maj=18;
        if (isset($zoom_osm))
            $this->pebl_zoom_osm=$zoom_osm;
        else
            $this->pebl_zoom_osm=14;
        if (isset($zoom_sat_maj))
            $this->pebl_zoom_sat_maj=$zoom_sat_maj;
        else
            $this->pebl_zoom_sat_maj=8;
        if (isset($zoom_sat))
            $this->pebl_zoom_sat=$zoom_sat;
        else
            $this->pebl_zoom_sat=4;
        if (isset($zoom_bing_maj))
            $this->pebl_zoom_bing_maj=$zoom_bing_maj;
        else
            $this->pebl_zoom_bing_maj=8;
        if (isset($zoom_bing))
            $this->pebl_zoom_bing=$zoom_bing;
        else
            $this->pebl_zoom_bing=4;
        if ($this->sm_fond_sat == 't') {
            $this->f->addHTMLHeadJs(array($this->pebl_http_google.$this->pebl_cle_google));
        }
    }

    /**
     * Ecrit les propriétés de l'instance dans la page html pour JavaScript
     *
     * @return void
     */
    function prepareJS() {
        echo '<link rel="stylesheet" href="../lib/openlayers/theme/default/style.css" type="text/css">';
        echo "<script type='text/javascript'>";
        //
        echo "  var base_url_map_compute_geom='".OM_ROUTE_MAP."&mode=compute_geom';\n";
        echo "  var base_url_map_get_filters='".OM_ROUTE_MAP."&mode=get_filters';\n";
        echo "  var base_url_map_get_geojson_cart='".OM_ROUTE_MAP."&mode=get_geojson_cart';\n";
        echo "  var base_url_map_get_geojson_datas='".OM_ROUTE_MAP."&mode=get_geojson_datas';\n";
        echo "  var base_url_map_get_geojson_markers='".OM_ROUTE_MAP."&mode=get_geojson_markers';\n";
        echo "  var base_url_map_redirection_onglet='".OM_ROUTE_MAP."&mode=redirection_onglet';\n";
        echo "  var base_url_map_session='".OM_ROUTE_MAP."&mode=session';\n";
        echo "  var base_url_map_form_sig='".OM_ROUTE_MAP."&mode=form_sig';\n";
        //
        echo "  var map;";
        echo "  var obj='".$this->obj."';\n";
        echo "  var idx='".$this->idx."';\n";
        echo "  var idx_sel='".$this->idx_sel."';\n";
        echo "  var popup='".$this->popup."';\n";
        echo "  var seli='".$this->seli."';\n";
        echo "  var etendue='".$this->etendue."';\n";
        echo "  var reqmo='".$this->reqmo."';\n";
        echo "  var premier='".$this->premier."';\n";
        echo "  var tricol='".$this->tricol."';\n";
        echo "  var advs_id='".$this->advs_id."';\n";
        echo "  var valide='".$this->valide."';\n";
        echo "  var style='".$this->style."';\n";
        echo "  var onglet='".$this->onglet."';\n";
        echo "  var recordMultiComp='".$this->recordMultiComp."';\n";
        echo "  var recordMode='".$this->recordMode."';\n";
        echo "  var recordFields=".json_encode($this->recordFields).";\n";
        //echo "  var type_utilisation='".$this->type_utilisation."';\n";
        //echo "  var affichageZones=".json_encode($this->affichageZones).";\n";
        //echo "  var sm_titre='".$this->sm_titre."';\n";
        // echo "  var sm_source_flux='".$this->sm_source_flux."';\n";
        echo "  var sm_zoom='".$this->sm_zoom."';\n";
        echo "  var sm_fond_sat='".$this->sm_fond_sat."';\n";
        echo "  var sm_fond_osm='".$this->sm_fond_osm."';\n";
        echo "  var sm_fond_bing='".$this->sm_fond_bing."';\n";
        echo "  var sm_layer_info='".$this->sm_layer_info."';\n";
        echo "  var sm_fond_default='".$this->sm_fond_default."';\n";
        echo "  var sm_projection_externe='".$this->sm_projection_externe."';\n";
        echo "  var sm_retour='".$this->sm_retour."';\n";
        echo "  var sm_restrict_extent='".$this->sm_restrict_extent."';\n";
        echo "  var sm_sld_marqueur='".$this->sm_sld_marqueur."';\n";
        echo "  var sm_sld_data='".$this->sm_sld_data."';\n";
        echo "  var sm_point_centrage='".$this->sm_point_centrage."';\n";
        echo "  var sm_point_centrage_x='".$this->sm_point_centrage_x."';\n";
        echo "  var sm_point_centrage_y='".$this->sm_point_centrage_y."';\n";
        echo "  var om_sig_map='".$this->om_sig_map."';\n";
        // echo "  var cg_obj_class=".json_encode($this->cg_obj_class).";\n";
        echo "  var cg_maj=".json_encode($this->cg_maj).";\n";
        // echo "  var cg_table=".json_encode($this->cg_table).";\n";
        // echo "  var cg_champ_idx=".json_encode($this->cg_champ_idx).";\n";
        // echo "  var cg_champ=".json_encode($this->cg_champ).";\n";
        echo "  var cg_geometrie=".json_encode($this->cg_geometrie).";\n";
        echo "  var cg_lib_geometrie=".json_encode($this->cg_lib_geometrie).";\n";
        echo "  var fl_om_sig_map_flux=".json_encode($this->fl_om_sig_map_flux).";\n";
        echo "  var fl_m_ol_map=".json_encode($this->fl_m_ol_map).";\n";
        echo "  var fl_m_visibility=".json_encode($this->fl_m_visibility).";\n";
        echo "  var fl_m_panier=".json_encode($this->fl_m_panier).";\n";
        echo "  var fl_m_pa_nom=".json_encode($this->fl_m_pa_nom).";\n";
        echo "  var fl_m_pa_layer=".json_encode($this->fl_m_pa_layer).";\n";
        echo "  var fl_m_pa_attribut=".json_encode($this->fl_m_pa_attribut).";\n";
        echo "  var fl_m_pa_encaps=".json_encode($this->fl_m_pa_encaps).";\n";
        // echo "  var fl_m_pa_sql=".json_encode($this->fl_m_pa_sql).";\n";
        echo "  var fl_m_pa_type_geometrie=".json_encode($this->fl_m_pa_type_geometrie).";\n";
        echo "  var fl_m_sql_filter=".json_encode($this->fl_m_sql_filter).";\n";
        echo "  var fl_m_filter=".json_encode($this->fl_m_filter).";\n";
        echo "  var fl_m_baselayer=".json_encode($this->fl_m_baselayer).";\n";
        echo "  var fl_m_singletile=".json_encode($this->fl_m_singletile).";\n";
        echo "  var fl_m_maxzoomlevel=".json_encode($this->fl_m_maxzoomlevel).";\n";
        // echo "  var fl_w_libelle=".json_encode($this->fl_w_libelle).";\n";
        echo "  var fl_w_id=".json_encode($this->fl_w_id).";\n";
        echo "  var fl_w_attribution=".json_encode($this->fl_w_attribution).";\n";
        echo "  var fl_w_chemin=".json_encode($this->fl_w_chemin).";\n";
        echo "  var fl_w_couches=".json_encode($this->fl_w_couches).";\n";
        echo "  var fl_w_cache_type=".json_encode($this->fl_w_cache_type).";\n";
        echo "  var fl_w_cache_gfi_chemin=".json_encode($this->fl_w_cache_gfi_chemin).";\n";
        echo "  var fl_w_cache_gfi_couches=".json_encode($this->fl_w_cache_gfi_couches).";\n";
        // echo "  var pebl_http_google='".$this->pebl_http_google."';\n";
        echo "  var pebl_cle_bing='".$this->pebl_cle_bing."';\n";
        // echo "  var pebl_cle_google='".$this->pebl_cle_google."';\n";
        // echo "  var pebl_zoom_osm_maj='".$this->pebl_zoom_osm_maj."';\n";
        // echo "  var pebl_zoom_osm='".$this->pebl_zoom_osm."';\n";
        // echo "  var pebl_zoom_sat_maj='".$this->pebl_zoom_sat_maj."';\n";
        // echo "  var pebl_zoom_sa='".$this->pebl_zoom_sa."';\n";
        // echo "  var pebl_zoom_bing_maj='".$this->pebl_zoom_bing_maj."';\n";
        // echo "  var pebl_zoom_bing='".$this->pebl_zoom_bing."';\n";
        echo "  var lst_idx_flux = new Array();";
        echo "  var lst_idx_data_layers = new Array();";
        echo "  var lst_idx_data_layers_edit = new Array();";
        echo "  var lst_base_layers = new Array();";
        echo "  var lst_overlays = new Array();";
        echo "  var lst_overlays_visibility = new Array();";
        echo "  var lst_carts = new Array();";
        echo "  var defBaseProjection = '".$this->defBaseProjection."';\n";
        echo "  var defDisplayProjection = '".$this->defDisplayProjection."';\n";
        echo "  var baseProjection;\n";
        echo "  var displayProjection;\n";
        echo "  var centerLayer;\n";
        echo "  var markersLayer;\n";
        echo "  var idx_max_load_geojson_datas;\n";
        echo "  var idx_cou_load_geojson_datas;\n";
        echo "  var osm;\n";
        echo "  var bingRoad;\n";
        echo "  var bingAerial;\n";
        echo "  var bingHybrid;\n";
        echo "  var sat;\n";
        echo "  var gSat;\n";
        echo "  var gStreets;\n";
        echo "  var img_maj='".$this->img_maj."';\n";
        echo "  var img_maj_hover='".$this->img_maj_hover."';\n";
        echo "  var img_consult='".$this->img_consult."';\n";
        echo "  var img_consult_hover='".$this->img_consult_hover."';\n";
        echo "  var img_w=".$this->img_w.";\n";
        echo "  var img_h=".$this->img_h.";\n";
        echo "  var img_click='".$this->img_click."'\n";
        echo "  var mode_action;\n";
        echo "  var vis_getfeatures;\n";
        echo "  var select_marker;\n";
        echo "  var select_data;\n";
        echo "  var selectControl;\n";
        echo "  var selectControl_layers = new Array();";
        echo "  var lstEditControls = new Array();";
        echo "  var currentEditControl;\n";
        echo "  var select_edit_champ;\n";
        echo "  var action_edit_champ;\n";
        echo "  var cartLayer;\n";
        echo "  var selectCartLayer;\n";
        echo "  var url_sld_data='".OM_ROUTE_FORM."&snippet=file&obj=om_sig_map&champ=sld_data&id=".$this->om_sig_map."';\n";
        echo "  var url_sld_marqueur='".OM_ROUTE_FORM."&snippet=file&obj=om_sig_map&champ=sld_marqueur&id=".$this->om_sig_map."';\n";
        echo "  var cart_type = ".json_encode($this->cart_type).";\n";
        echo "  var cart_val = new Array();\n";
        echo "  var edit_toolbar = ".json_encode($this->edit_toolbar).";\n";
        echo "  var measureControls = new Array();\n";
        echo "  var mouseControl;\n";
        echo "  var bGeolocate=false;\n";
        if (isset ($_SESSION['map_'.$this->obj])) {
            echo '  var zoomSelected='.$_SESSION['map_'.$this->obj]['zoom'].';'."\n";
            echo '  var s_base=encodeURIComponent('."'".$_SESSION['map_'.$this->obj]['base']."'".');'."\n";
            echo '  var s_visibility = '.json_encode($_SESSION['map_'.$this->obj]['visibility']).";\n";
        } else {
            echo "  var s_base=sm_fond_default;\n";
            echo "  var zoomSelected=sm_zoom;\n";
            echo "  var s_visibility;\n";
        }
        echo "  map_init();\n";
        echo "</script>";
    }

    /**
     * Paramétrage des zones du canevas
     *
     * @param mixed $zone
     * @param mixed $val
     *
     * @return
     */
    function setCanevas($zone, $val) {
        $this->affichageZones[$zone]=$val;
    }

    /**
     * Préparation du canevas html: pilote les autres fonctions prepareCanevas.
     *
     * @return void
     */
    function prepareCanevas() {

        echo "  <div id='map-id' class='ui-map'></div>\n";
        if ($this->affichageZones['titre']==1 ||
                $this->affichageZones['edit']==1 ||
                $this->affichageZones['tools']==1 ||
                $this->affichageZones['infos']==1 ||
                $this->affichageZones['print']==1 ||
                $this->affichageZones['layers']==1 ||
                $this->affichageZones['navigation']==1 )
            $this->affichageZones['menubar']=1;
        else
            $this->affichageZones['menubar']=0;
        $this->prepareCanevasMenu();
        if ($this->affichageZones['titre']==2) $this->prepareCanevasTitre();
        if ($this->affichageZones['edit']==2) $this->prepareCanevasEdit();
        if ($this->affichageZones['tools']==2) $this->prepareCanevasTools();
        if ($this->affichageZones['print']==2) $this->prepareCanevasPrint();
        if ($this->affichageZones['infos']==2) $this->prepareCanevasInfos();
        if ($this->affichageZones['navigation']==2) $this->prepareCanevasNavigation();
        if ($this->affichageZones['layers']==2) $this->prepareCanevasLayers();
        $this->prepareCanevasGetfeatures();
        $this->prepareJS();
    }

    /**
     * Préparation du canevas html: menu avec regroupement (au moins une valeur  à 1)
     *
     * @return void
     */
    function prepareCanevasMenu() {
        echo '<div id="map-menu" class="ui-widget-header ui-corner-all">';
        //echo '     <table>';
        //echo '       <tr>';

        if ($this->affichageZones['titre']==1) $this->prepareCanevasTitre();
        if ($this->affichageZones['edit']==1) $this->prepareCanevasEdit();
        if ($this->affichageZones['tools']==1) $this->prepareCanevasTools();
        if ($this->affichageZones['infos']==1) $this->prepareCanevasInfos();
        if ($this->affichageZones['navigation']==1) $this->prepareCanevasNavigation();
        if ($this->affichageZones['layers']==1) $this->prepareCanevasLayers();
        //echo '       </tr>';
        //echo '     </table>';
        echo "  </div>";
    }

    /**
     * Préparation du canevas html: Titre
     *
     * @return void
     */
    function prepareCanevasTitre() {
        echo "  <div id='map-titre'>";
        echo "    <td><font id='map-titre-id' class='ui-corner-all'>".$this->obj."&nbsp;".$this->idx_sel."</font></td>";
        echo "  </div>";
    }

    /**
     * Préparation du canevas html: fonctions d'éditions
     *
     * @return void
     */
    function prepareCanevasEdit() {
        // *** jlb fonction non supprimee en cas d appel de la fonction ???
        /*
        echo "  <div id='map-edit'>";
        //echo "     <td><div id='map-edit-sel-comp'></div></td>";
        echo '     <td><select id="map-edit-sel-comp-id" size="1" name="map-edit-sel-comp"><select></td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-nav" onclick="map_clicEditNavigate();"><span class="om-icon om-icon-16 om-icon-fix map-edit-nav-16" title="Naviguer">Naviguer</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-draw-point" onclick="map_clicEditDrawPoint();"><span class="om-icon om-icon-16 om-icon-fix map-edit-draw-point-16" title="Dessiner">Dessiner point</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-draw-line" onclick="map_clicEditDrawLine();"><span class="om-icon om-icon-16 om-icon-fix map-edit-draw-line-16" title="Dessiner">Dessiner ligne</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-draw-polygon" onclick="map_clicEditDrawPolygon();"><span class="om-icon om-icon-16 om-icon-fix map-edit-draw-polygon-16" title="Dessiner">Dessiner polygone</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-draw-regular" onclick="map_clicEditDrawRegular();"><span class="om-icon om-icon-16 om-icon-fix map-edit-draw-regular-16" title="Dessiner">Dessiner polygone régulier</span></a>&nbsp;</td>';
        echo '     <td><INPUT NAME="map-edit-draw-regular-nb" id="map-edit-draw-regular-nb" size=3 onchange="map_EditDrawRegularChange()" value=4></td>';
        echo '     <td><select id="map-edit-cart-lst-id" size="1" name="map-edit-cart-lst"><select></td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-cart-get" onclick="map_clicEditGetCart()"><span class="om-icon om-icon-16 om-icon-fix map-edit-get-cart-16" title="Récupération panier">Récupération panier</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-draw-modify" onclick="map_clicEditDrawModify();"><span class="om-icon om-icon-16 om-icon-fix map-edit-draw-modify-16" title="Editer">Editer</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-select" onclick="map_clicEditSelect();"><span class="om-icon om-icon-16 om-icon-fix map-edit-select-16" title="Selectionner">Selectionner</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-erase" onclick="map_clicEditErase();"><span class="om-icon om-icon-16 om-icon-fix map-edit-erase-16" title="Gommer">Gommer</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-valid" onclick="map_clicEditValid();"><span class="om-icon om-icon-16 om-icon-fix map-edit-valid-16" title="Vérifier">Vérifier</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-record" onclick="map_clicEditRecord();"><span class="om-icon om-icon-16 om-icon-fix map-edit-record-16" title="Enregistrer">Enregistrer</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-delete" onclick="alert(\'Effacer\');"><span class="om-icon om-icon-16 om-icon-fix map-edit-delete-16" title="Effacer">Effacer</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-edit-close" onclick="map_clicEditClose();"><span class="om-icon om-icon-16 om-icon-fix map-edit-close-16" title="Effacer">Effacer</span></a>&nbsp;</td>';
        echo "  </div>";
        */
    }

    /**
     * Préparation du canevas html: boite à outils
     *
     * @return void
     */
    function prepareCanevasTools() {
        // *** jlb fonction non supprimee en cas d appel de la fonction ???
        /*
        echo '  <div id="map-tools">';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-form" onclick="map_clicForm();"><span class="om-icon om-icon-16 om-icon-fix map-form-16" title="Formulaire">Formulaire</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-nav" onclick="map_clicNavigate();"><span class="om-icon om-icon-16 om-icon-fix map-nav-16" title="Naviguer">Naviguer</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-info" onclick="map_clicInfo();"><span class="om-icon om-icon-16 om-icon-fix map-info-16" title="Informations">Information</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-edit" onclick="map_clicEdit();"><span class="om-icon om-icon-16 om-icon-fix map-edit-16" title="Editer">Editer</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-geoloc" onclick="map_clicGeolocate();"><span class="om-icon om-icon-16 om-icon-fix map-geoloc-16" title="Géolocalisation">Géolocalisation</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-mes-dist" onclick="map_clicMeasureDistance();"><span class="om-icon om-icon-16 om-icon-fix map-mes-dist-16" title="Mesurer distance">Mesurer distance</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-mes-aera" onclick="map_clicMeasureAera();"><span class="om-icon om-icon-16 om-icon-fix map-mes-area-16" title="Mesurer aire">Mesurer aire</span></a>&nbsp;</td>';
        echo '     <td class="icons">&nbsp;<a href="#" id="map-tools-return" onclick="alert(\'retour\');"><span class="om-icon om-icon-16 om-icon-fix map-return-16" title="Retour">Retour</span></a>&nbsp;</td>';
        echo '  </div>';
        */
    }

    /**
     * Préparation du canevas html: Informations
     *
     * @return void
     */
    function prepareCanevasInfos() {
        echo '  <div id="map-infos">';
        echo '     <td><span id="map-infos-field" class="field_value">info</span></td>';
        echo '  </div>';
    }

    /**
     * Préparation du canevas html: Impressions
     *
     * @return void
     */
    function prepareCanevasPrint() {
        echo "  <div id='map-print'>";
        echo '        <td class="icons">&nbsp;<a href="#" id="map-print" onclick="alert(\'imprimer\');"><span class="om-icon om-icon-16 om-icon-fix map-print-16" title="Imprimer">Imprimer</span></a>&nbsp;</td>';
        echo "  </div>";
    }

    /**
     * Préparation du canevas html: Menu des couches
     *
     * @return void
     */
    function prepareCanevasLayers() {
        // *** jlb fonction recuperant prepareCanevasTools et prepareCanevasedit
        /*
        echo "  <div id='map-layers'>";
        echo "    <div id='map-layers-datas'></div>";
        echo "    <div id='map-layers-markers'></div>";
        echo "    <div id='map-layers-overlays'></div>";
        echo "    <div id='map-layers-bases'></div>";
        echo "  </div>";
        */
        echo "<div id='cssmenu' style='visibility : hidden;'>";
        echo "<ul>";
        echo "<li><a href='#'  onClick='affiche_tools()'/>Outils</a></li>";
        echo "<li><a  href='#'  onClick='affiche_getfeatures()'/>Infos</a></li>";
        echo "<li><a  href='#'  onClick='affiche_layers()'/>Couche</a></li>";
        echo "<li><a href='#' onClick='affiche_baselayers()'/>Fond</a></li>";
        echo "<li><a href='#' onClick='affiche_aide()'/>?</a></li>";
        echo "</ul>";
        echo "</div>";
        //
        echo "<div id='map-layers'  style='visibility : visible;'>";
            //--------------------------------------------------------------------------------------------------------------------------------
            // remplace le contenu de la function prepareCanevasTools()
            //--------------------------------------------------------------------------------------------------------------------------------
            echo "<div id='map-tools'  style='visibility : hidden;'>";
                echo "<div id='cssmenu' style='visibility : visible;'>";
                echo "<ul>";
                echo "<li><a href='#'  onClick='affiche_tools()'/>Outils</a></li>";
                echo "<li><a  href='#'  onClick='affiche_getfeatures()'/>Infos</a></li>";
                echo "<li><a  href='#'  onClick='affiche_layers()'/>Couche</a></li>";
                echo "<li><a href='#' onClick='affiche_baselayers()'/>Fond</a></li>";
                echo "<li><a href='#' onClick='affiche_aide()'/>?</a></li>";
                echo "</ul>";
                echo "</div>";
                echo "<table  id='map-tools-bao' cellpadding='10px' cellspacing='1px' border='0'><tr><td></td><td>";
                echo "BOITE À OUTILS &nbsp";
                echo "</td></tr></table>";
                    // formulaire
                echo "<table id='table-tools' cellpadding='1px' cellspacing='1px'><tr><td>";
                    echo "<a href='#' id='map-tools-form' onclick='map_clicForm();'>";
                    echo "<img src='../lib/om-assets/img/map-form.png' style='vertical-align:middle' alt='formulaire' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Formulaire");
                    echo "</a></td></tr>";
                    //navigation
                    echo "<tr><td>";
                    echo "<a href='#' id='map-tools-nav' onclick='map_clicNavigate()'>";
                    echo "<img src='../lib/om-assets/img/map-nav.png' style='vertical-align:middle' alt='navigation' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("navigation");
                    echo "</a></td></tr>";
                    //editer
                    echo "<tr><td>";
                    echo "<a href='#'  id='map-tools-edit' onclick='map_clicEdit();'>";
                    echo "<img src='../lib/om-assets/img/map-edit.png' style='vertical-align:middle' alt='editer' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Editer");
                    echo "</a></td></tr>";
                    //geolocalisation
                    echo "<tr><td>";
                    echo "<a href='#' id='map-tools-geoloc' onclick='map_clicGeolocate();'>";
                    echo "<img src='../lib/om-assets/img/map-geoloc.png' style='vertical-align:middle' alt='Géolocalisation' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Géolocalisation");
                    echo "</a></td></tr>";
                    //mesure distance
                    echo "<tr><td>";
                    echo "<a href='#'  id='map-tools-mes-dist' onclick='map_clicMeasureDistance();'>";
                    echo "<img src='../lib/om-assets/img/map-distance.png' style='vertical-align:middle' alt='Mesurer distance' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Mesurer distance");
                    echo"</a></td></tr>";
                    //mesure area
                    echo "<tr><td>";
                    echo "<a href='#' id='map-tools-mes-aera'  onclick='map_clicMeasureAera();'>";
                    echo "<img src='../lib/om-assets/img/map-area.png' style='vertical-align:middle' alt='Mesurer aire' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Mesurer aire");
                    echo "</a></td></tr></table>";
            echo ' </div>';
            //--------------------------------------------------------------------------------------------------------------------------------
            //
            //--------------------------------------------------------------------------------------------------------------------------------
            // remplace contenu de la function  prepareCanevasEdit()
            //--------------------------------------------------------------------------------------------------------------------------------
            echo "<div id='map-edit' style='visibility :hidden;'>";
                    echo "<div id='cssmenu' style='visibility :hidden;'>";
                echo "<ul>";
                    echo "<li><a href='#'  onClick='affiche_tools()'/>Outils</a></li>";
                    echo "<li><a  href='#'  onClick='affiche_getfeatures()'/>Infos</a></li>";
                    echo "<li><a  href='#'  onClick='affiche_layers()'/>Couche</a></li>";
                    echo "<li><a href='#' onClick='affiche_baselayers()'/>Fond</a></li>";
                    echo "<li><a href='#' onClick='affiche_aide()'/>?</a></li>";
                    echo "</ul>";
                echo "</div>";
                    echo "<table  id='table-edit-choix-geom' cellpadding='0px' cellspacing='0px' border='0'><tr><td><a href='#' id='map-edit-close' onclick='map_clicEditClose();'>";
                    echo "<img src='../lib/om-assets/img/map-return.png' style='vertical-align:middle;text-align:left' alt='Retour' /></a></td><td>BOITE À OUTILS &nbspEDITER";
                    echo "</td></tr>";
                            //
                    echo "<tr><td id='cadre-geom-choix' colspan='2'><center>";
            echo __("CHOIX GÉOMÉTRIE")."   <br>";
                    echo " <select id='map-edit-sel-comp-id' size='1' name='map-edit-sel-comp'><select></center>";
                    echo "</td></tr>";

                    echo "</table>";
                    echo "<table  id='table-edit-geom' cellpadding='0px' cellspacing='0px' border='0px solide #0000000'>";

                   //dessin point
                    echo "<tr><td id='cadre-geom' colspan='2' >";
                    echo "<a href='#' id='map-edit-draw-point' onclick='map_clicEditDrawPoint();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-point.png' style='vertical-align:middle' alt='Dessiner point' /><br>";
                    echo __("Dessiner point");
                    echo "</a>";
                    echo "</td></tr>";

                    //dessin ligne
                    echo "<tr><td id='cadre-geom' colspan='2'>";
                    echo "<a href='#' id='map-edit-draw-line'  onclick='map_clicEditDrawLine();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-draw-line.png' style='vertical-align:middle' alt='Dessiner Ligne' /><br>";
                    echo __("Dessiner Ligne");
                    echo "</a>";
            echo "</td></tr><tr><td id='cadre-geom' colspan='2'><br><select id='map-edit-cart-lst-id' size='1' name='map-edit-cart-lst'><select>";
            echo "<a href='#' id='map-edit-cart-get' onclick='map_clicEditGetCart()'>";
                    echo "<br><img src='../lib/om-assets/img/map-edit-get-cart.png' style='vertical-align:middle' alt='Récupération panier'>";
                    echo __("<br>Récupération<br>panier");
                    echo "</a>";
                    echo "</td></tr>";
                    //dessin polygon
                    echo "<tr><td id='cadre-geom'>";
            echo "     <a href='#' id='map-edit-draw-polygon' onclick='map_clicEditDrawPolygon();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-draw-polygon.png' style='vertical-align:middle' alt='Déssiner Polygone'><br>";
                    echo __("Déssiner<br>Polygone");
                    echo "</a>";
                    echo "</td>";
                     //dessin polygonregular
                    echo "<td id='cadre-geom'>";
            echo "     <a href='#' id='map-edit-draw-regular' onclick='map_clicEditDrawRegular();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-draw-regular.png' style='vertical-align:middle' alt='Dessiner polygone régulier'><br>";
                    echo __("Dessiner<br>polygone<br>régulier");
                    echo "</a>";
            //echo "<INPUT NAME='map-edit-draw-regular-nb' id='map-edit-draw-regular-nb' size='1' onchange='map_EditDrawRegularChange()' value='4'>";
                    echo "<br><select NAME='map-edit-draw-regular-nb' id='map-edit-draw-regular-nb' onchange='map_EditDrawRegularChange()'>";
                            echo "<option selected>4</option>";
                             //
                            for ($nbx=5; $nbx<=100;$nbx++) {
                                                                echo "<option>".$nbx."</option>";
                                                        }
                    echo "</select>";
                    echo "</td></tr>";
                    echo "<tr><td id='cadre-geom' colspan='2'><center>";
                    echo "<a href='#' id='map-edit-select'  onclick='map_clicEditSelect();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-select.png' style='vertical-align:middle' alt='Selectionner' />". __("<br>Sélection(s)<br>Géométrie")."</a>";
                    echo "</center></td></tr>";
                    //modifier geometrie point
                    echo "<tr><td><br><br>";
                    echo "<a href='#' id='map-edit-draw-modify'  onclick='map_clicEditDrawModify();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-modif.png' style='vertical-align:middle' alt='Modifier Géométrie' />".__("<br>Modifier<br>Géométrie")."</a>";
                    echo "<br><br></td>";
                    //selectionner
                    echo "<td>";
                    //gommer selection editer
                    echo "<a href='#' id='map-edit-erase' onclick='map_clicEditErase();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-erase.png' style='vertical-align:middle' alt='Gommer' />".__("<br>Effacer<br>Géométrie")."</a>";
                    echo "</td></tr>";
                    echo "</table>";
                    echo "<table id='table-edit' cellpadding='0px' cellspacing='2px'>";
                    //navigation
                    echo "<tr><td colspan='2'>";
                    echo "<a href='#' id='map-edit-nav'  onclick='map_clicEditNavigate();'>";
                    echo "<img src='../lib/om-assets/img/map-nav.png' style='vertical-align:middle' alt='naviguer' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Naviguer");
                    echo "</a></td></tr>";
                    //verifier
                    echo "<tr><td ccolspan='2'>";
                    echo "<a href='#'  id='map-edit-valid' onclick='map_clicEditValid();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-valid.png' style='vertical-align:middle;' margin-left='100px' alt='Vérifier' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Vérifier");
                    echo "</a></td></tr>";
                   //enregistrer editer
                    echo "<tr><td colspan='2'>";
                    echo "<a href='#' id='map-edit-record' onclick='map_clicEditRecord();'>";
                    echo "<img src='../lib/om-assets/img/map-edit-record.png' style='vertical-align:middle' alt='Enregistrer' />&nbsp;&nbsp;&nbsp;&nbsp;";
                    echo __("Enregistrer");
                    echo "</a></td></tr>";
                    echo "</table>";
        echo "</div>";
                //--------------------------------------------------------------------------------------------------------------------------------
        echo "<div id='map-layers-datas' style='visibility : hidden;'>geometries<br></div>";
        echo "<div id='map-layers-markers' style='visibility : hidden;'></div>";
        echo "<div id='map-layers-overlays' style='visibility : hidden;'></div>";
        echo "<div id='map-layers-bases' style='visibility : visible;'>COUCHE(S) DE BASE(S)</div>";
        //----------------------------------------------------------------------------------------------------------------------------
        // onglet ?  : couleurs des objets geometrique  issues de la fonction javascript : function map_load_geojson_datas dans app/js/sig.js
        //----------------------------------------------------------------------------------------------------------------------------
        echo "<div id='map-legende' style='visibility :hidden;'>";
                    echo __("Représentation Objets ");
                    echo "<div id='map-edit-legende'>";
                    echo "        <svg>";
                    echo "          <rect x='50px' y='10px' width='20' height='20' stroke='black' stroke-opacity='0.9' stroke-width='4' fill='blue' fill-opacity='0.4' />";
                    echo "                <circle cx='100' cy='20' r='5'   stroke='black' stroke-opacity='0.9' stroke-width='4' fill='blue' fill-opacity='0.4' />";
                    echo "                <line x1='10' y1='10' x2='30' y2='30' stroke='black' stroke-opacity='0.9' stroke-width='4' fill='blue' fill-opacity='0.4' />";
                    echo "        </svg>";
                    echo "</div><br>";
                    echo __("Objets Selectionnes");
                    echo "<div id='map-edit-legende-select'>";
                    echo "  <svg>";
                    echo "        <rect x='50px' y='10px' width='20' height='20' stroke='red' stroke-width='3' stroke-opacity='0.9'  fill='red' fill-opacity='0.4'/> ";
                    echo "               <circle cx='100' cy='20' r='5'  stroke='red' stroke-width='3' stroke-opacity='0.9'  fill='red' fill-opacity='0.4'/>";
                    echo "                <line x1='10' y1='10' x2='30' y2='30' stroke='black' stroke-opacity='0.9' stroke-width='4' fill='blue' fill-opacity='0.4' />";
                    echo "                <line x1='10' y1='10' x2='30' y2='30' stroke='red' stroke-width='3' stroke-opacity='0.9'  fill='red' fill-opacity='0.4'/>";
                    echo "        </svg>";
           echo "</div>";
           echo "</div>";
        echo "  </div>";
    }

    /**
     * Préparation du canevas html: navigation
     *
     * @return void
     */
    function prepareCanevasNavigation() {
        echo "  <div id='map-navigation'>";
        echo "  </div>";
    }

    /**
     * Préparation du canevas html: getFeature
     *
     * @return void
     */
    function prepareCanevasGetfeatures() {
        // *** jlb modification suite menu des onglets
        /*
        echo '  <div id="map-getfeatures">';
        echo '    <div id="map-getfeatures-datas">';
        echo '    </div>';
        echo '    <div id="map-getfeatures-markers">';
        echo '    </div>';
        echo '    <div id="map-getfeatures-flux">';
        echo '    </div>';
        echo '  </div>';
        */
        echo "<div id='map-getfeatures'  style='visibility : hidden;'>";
                echo "<div id='cssmenu'>";
            echo "<ul>";
                echo "<li><a href='#'  onClick='affiche_tools()'/>Outils</a></li>";
                echo "<li><a  href='#'  onClick='affiche_getfeatures()'/>Infos</a></li>";
                echo "<li><a  href='#'  onClick='affiche_layers()'/>Couche</a></li>";
                echo "<li><a href='#' onClick='affiche_baselayers()'/>Fond</a></li>";
                echo "<li><a href='#' onClick='affiche_aide()'/>?</a></li>";
                echo "</ul>";
            echo "</div>";
            //
        echo "<div id='map-getfeatures-datas' style='visibility : hidden;'>";
                echo "</div>";
        echo "<div id='map-getfeatures-markers' style='visibility : hidden;'>";
        echo "</div>";
        echo "<div id='map-getfeatures-flux' style='visibility : hidden;'></div>";
        echo "</div>";
    }

    /**
     * Calcul la géométrie validé dans l'interface
     *
     * @param mixed $seli
     * @param mixed $geojson
     *
     * @return
     */
    function getComputeGeom($seli, $geojson) {
        //
        $pos = strpos(strtolower($this->cg_geometrie[$seli]), 'multi');
        if ($pos === false) {
            $sql = "select ST_AsGeoJSON(st_union(g)) as geom from (".$geojson.") a";
        } else {
            $sql = "select ST_AsGeoJSON(st_multi(st_union(g))) as geom from (".$geojson.") a";
        }
        //
        $res = $this->f->db->query($sql);
        $this->addToLog(
            __METHOD__."(): db->query(\"".$sql."\");",
            VERBOSE_MODE
        );
        $this->f->isDatabaseError($res);
        //
        $geom = "";
        if ($res->numRows() == 1) {
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                $geom = $row["geom"];
                $type = substr($geom, strpos($geom, ':') + 2, strpos($geom, ',') - strpos($geom, ':') - 3);
                if (strtolower($type) != strtolower($this->cg_geometrie[$seli])) {
                    $geom = "Err: ".$type." sélectionné, ".$this->cg_geometrie[$seli]." attendu";
                }
            }
        } else {
            $geom = "Err: la chaine transmise est mal structurée";
        }
        return $geom;
    }

    /**
     * Préparation du canevas html: pilote les autres fonctions prepareCanevas...
     *
     * @param integer $min
     * @param integer $max
     * @param integer $validation
     * @param mixed $geojson
     *
     * @return
     */
    function prepareForm($min, $max, $validation, $geojson) {
        //
        $beforePrepareForm = $this->beforePrepareForm($min, $max, $validation, $geojson);
        //
        if ($beforePrepareForm == "t") {
            echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
            echo "<fieldset class=\"cadre ui-corner-all ui-widget-content\">\n";
            echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
            echo __("objet")."&nbsp;".$this->obj."&nbsp;".__("enregistrement")."&nbsp;".$this->idx;
            echo "</legend>\n";
            if ($validation==0) { // validation
                $validation=1;
                $url = sprintf(
                    '%s&mode=form_sig&obj=%s&idx=%s&etendue=%s&reqmo=%s&premier=%s&tricol=%s&advs_id=%s&valide=%s&style=%s&onglet=%s&idx_sel=%s&min=%s&max=%s&validation=%s',
                    OM_ROUTE_MAP,
                    $this->obj,
                    $this->idx_sel,
                    $this->etendue,
                    $this->reqmo,
                    $this->premier,
                    $this->tricol,
                    $this->advs_id,
                    $this->valide,
                    $this->style,
                    $this->onglet,
                    $this->idx,
                    $min,
                    $max,
                    $validation
                );
                $onsubmit = sprintf(
                    'affichersform(\'%s\', \'%s\', this); return false;',
                    $this->obj,
                    $url
                );
                $this->f->layout->display__form_container__begin(array(
                    "action" => "",
                    "name" => "f2sig",
                    "onsubmit" => $onsubmit,
                ));
                echo "\t<div class=\"field\">";

                $this->prepareFormSpecific( $min, $max, $validation, $geojson);
                echo "<select name='maj' class='champFormulaire'>";
                foreach ($this->form_champ_maj as $k => $v) {
                    echo "<option value='$k'>$v</option>";
                }
                echo "</select>";
                echo "&nbsp;&nbsp;";
                $this->f->layout->display__form_input_submit(array(
                    "value" => "valider",
                ));
                for ($c=$min; $c<=$max; $c++) {
                    if ( $this->cg_maj[$c] == 't')
                        echo "<br>".$this->cg_lib_geometrie[$c]."<textarea disabled name='geom".$c."' cols='40' rows='3' class='champFormulaire'>".$geojson[$c]."</textarea>";
                }
                $this->f->layout->display__form_container__end();
                echo "</div>";
            } else {
                if (isset ($_POST['maj'])) {
                     $maj=$_POST['maj'];
                } else {
                     $maj="1";
                }
                $this->prepareFormSpecific($min, $max, $validation, $geojson);
                echo "<select disabled name='maj' class='champFormulaire'>";
                foreach ($this->form_champ_maj as $k => $v) {
                    if ($maj == $k)
                         echo '<option selected="selected" '."value='$k'>$v</option>";
                    else
                         echo "<option value='$k'>$v</option>";
                }
                echo "</select>";
                echo '<a class="retour" href="#">Retour</a>';
                $result='t';
                $mess_err="";
                $sql_lst = array();
                $result = $this->prepareFormBeforeUpdate($min, $max, $validation, $geojson);
                if ($result=='t') {
                    array_push($sql_lst,"BEGIN;");
                    for ($c=$min; $c<=$max; $c++) {
                        if ( $this->cg_maj[$c] == 't') {
                            if ($maj=='1') {
                                if ($geojson[$c]=='')
                                    array_push($sql_lst,"UPDATE ".DB_PREFIXE.$this->cg_table[$c]." SET ".$this->cg_champ[$c]."=NULL WHERE ".$this->cg_champ_idx[$c]."='".$this->idx."';");
                                else
                                    array_push($sql_lst,"UPDATE ".DB_PREFIXE.$this->cg_table[$c]." SET ".$this->cg_champ[$c]."= ST_Transform(ST_GeomFromText('".$geojson[$c]."',".$this->defBaseProjection."),".str_replace('EPSG:','',$this->sm_projection_externe).") WHERE ".$this->cg_champ_idx[$c]."='".$this->idx."';");
                            }
                            if ($maj=='2') {
                                array_push($sql_lst,"UPDATE ".DB_PREFIXE.$this->cg_table[$c]." SET ".$this->cg_champ[$c]."=NULL WHERE ".$this->cg_champ_idx[$c]."='".$this->idx."';");
                            }
                        }
                    }
                    array_push($sql_lst,"COMMIT;");
                    $this->f->db->autoCommit(false);

                    for($i=0; $i<count($sql_lst); $i++) {
                        $res = $this->f->db->query($sql_lst[$i]);
                        $this->addToLog(
                            __METHOD__."(): db->query(\"".$sql_lst[$i]."\");",
                            VERBOSE_MODE
                        );
                        if ($this->f->isDatabaseError($res, true)) {
                            $mess_err.="<br> Erreur SQL pour le champ ".$this->cg_lib_geometrie[$c]." ".__($res->getMessage()."<BR>&nbsp;&nbsp;".$sql_lst[$i]);
                        }
                    }
                    for ($c=$min; $c<=$max; $c++) {
                        if ( $this->cg_maj[$c] == 't') {
                            // echo "<br>".$this->cg_lib_geometrie[$c]."<textarea disabled name='geom".$c."' cols='40' rows='3' class='champFormulaire'>".$geojson[$c]."</textarea>";
                        }
                        if ($c==($max-1)) {
                            $result=$this->prepareFormAfterUpdate($min, $max, $validation, $geojson);
                            if ( $result != 't')
                               $mess_err.=$result;
                        }
                        if ($c==$max && $mess_err !='') {
                            $res = $this->f->db->query('ROLLBACK');
                        }

                    }
                }
                else {
                    $mess_err.=$result;
                }
                if ($mess_err != '') {
                    echo '<div class="message ui-widget ui-corner-all ui-state-highlight ui-state-error">'.
                            '<p>'.
                                '<span class="ui-icon ui-icon-info"><!-- --></span> '.
                                '<span class="text">'.$mess_err.'<br>SAISIE NON ENREGISTRÉE<br></span>'.
                            '</p>'.
                            '</div>';
                } else {
                    echo '<div class="message ui-widget ui-corner-all ui-state-highlight ui-state-valid">'.
                            '<p>'.
                                '<span class="ui-icon ui-icon-info"><!-- --></span> '.
                                '<span class="text">Vos modifications ont bien été enregistrées.<br></span>'.
                            '</p>'.
                          '</div>';
                }
                echo '<BR><INPUT TYPE="HIDDEN" NAME="form_sig_retour" id="form_sig_retour" size=5 " value="'.$result.'">';

            }
            echo "</fieldset>\n";
        } else {
            echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
            echo "<fieldset class=\"cadre ui-corner-all ui-widget-content\">\n";
            echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
            echo __("objet")."&nbsp;".$this->obj."&nbsp;".__("enregistrement")."&nbsp;".$this->idx;
            echo "</legend>\n";
            echo '<BR><INPUT TYPE="HIDDEN" NAME="form_sig_retour" id="form_sig_retour" size=5 " value="t">';
            echo "</fieldset>\n";
        }
    }

    /**
     * Fonction pour surcharge de prepareForm: execute ou non le prepareForm en
     * fonction du return 'chaine vide ok".
     *
     * @param integer $min
     * @param integer $max
     * @param integer $validation
     * @param mixed $geojson
     *
     * @return string
     */
    function beforePrepareForm($min, $max, $validation, $geojson) {
        return "t";
    }

    /**
     * Point d'entrée pour surcharge de la méthode om_map::prepareForm().
     *
     * @param integer $min
     * @param integer $max
     * @param integer $validation
     * @param mixed $geojson
     *
     * @return string
     */
    function prepareFormSpecific($min, $max, $validation, $geojson) {
        return "t";
    }

    /**
     * Fonction pour surcharge dans prepareForm: avant l'exécution des requètes
     * de modification des champs géométriques retourner t si ok, sinon
     * retourner le message à afficher.
     *
     * @param integer $min
     * @param integer $max
     * @param integer $validation
     * @param mixed $geojson
     *
     * @return string
     */
    function prepareFormBeforeUpdate($min, $max, $validation, $geojson) {
        return "t";
    }

    /**
     * Fonction pour surcharge dans prepareForm: après l'exécution des requètes
     * de modification des champs géométriques et avant le commit retourner t
     * si ok, sinon retourner le message à afficher.
     *
     * @param integer $min
     * @param integer $max
     * @param integer $validation
     * @param mixed $geojson
     *
     * @return string
     */
    function prepareFormAfterUpdate($min, $max, $validation, $geojson) {
        return "t";
    }
}
