<?php
/**
 * Ce script contient la définition de la classe 'Upload'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: upload.class.php 4348 2018-07-20 16:49:26Z softime $
 */

global $UploadError;

/**
 * @version 2.0.a (dernière révision le 07-11-2003)
 * @author  Olivier VEUJOZ <o.veujoz@miasmatik.net>

 * Compatibilité :
    - compatible safe_mode
    - compatible open_basedir pour peu que les droits sur le répertoire temporaire d'upload soit alloué
    - marche avec les chemins relatifs et absolu
    - testé sous environnement Linux/Windows sous Apache 1.3
    - testé avec les version PHP 4.2.0, 4.3.1, 4.3.4
    - Version minimum de php : 4.2.0
 * Par défaut :
    - autorise tout type de fichier
    - autorise les fichier allant jusqu'à la taille maximale spécifiée dans le php.ini
    - envoie le(s) fichier(s) dans le répertoire de la classe
    - estime le temps d'execution du script par rapport à un modem 33.6Ko
    - n'affiche qu'un champ de type file
    - permet de laisser les champs de fichiers vides
    - écrase le fichier s'il existe déjà
    - n'exécute aucune vérification
 * Notes :
    - le chemin de destination peut être soit absolu soit relatif
    - si $SecurityMax est positionné à "true", la classe va ignorer tout type de fichier rentrant dans la catégorie des application/octetstream
    - set_time_limit n'a pas d'effet lorsque PHP fonctionne en mode safe mode . Il n'y a pas d'autre solution que de changer de mode, ou de modifier la durée maximale d'exécution dans le php.ini
    - la variable $UploadErreur (type bool) est réutilisable dans vos scripts afin de tester le bon déroulement des opérations. S'il y a eu des erreurs, la variable est positionnée à "true".
 */
class Upload {

    /**
     * Taille maximum exprimée en kilo-octets pour l'upload d'un fichier.
     * Type : numérique
     * Valeur par défaut : celle configurée dans le php.ini
     * @access public
     */
    var $MaxFilesize;

    /**
     * Largeur maximum d'une image exprimée en pixel.
     * Type : entier
     * Valeur par défaut : null (aucune vérification)
     * @access public
     */
    var $ImgMaxWidth;

    /**
     * Hauteur maximum d'une image exprimée en pixel.
     * Type : entier
     * Valeur par défaut : null (aucune vérification)
     * @access public
     */
    var $ImgMaxHeight;

    /**
     * Largeur minimum d'une image exprimée en pixel.
     * Type : entier
     * Valeur par défaut : null (aucune vérification)
     * @access public
     */
    var $ImgMinWidth;

    /**
     * Hauteur minimum d'une image exprimée en pixel.
     * Type : entier
     * Valeur par défaut : null (aucune vérification)
     * @access public
     */
    var $ImgMinHeight;

    /**
     * Répertoire de destination dans lequel vont être chargé les fichiers. Accepte les chemins relatifs et absolus
     * Type : chaine
     * Valeur par défaut : chaine vide (le répertoire dans lequelle est située la classe upload sera désigné comme chemin de destination)
     * @access public
     */
    var $DirUpload;

    /**
     * Débit de la connexion utilisateur, exprimée en kilobit, sur laquelle sera basée le calcul de la fonction set_time_limit
     * Type : valeur numérique
     * Valeur par défaut : 33.6
     * @access public
     */
    var $Debit;

    /**
     * Nombre de champs de type file que la classe devra gérer.
     * Type : entier
     * Valeur par défaut : 1
     * @access public
     */
    var $Fields;

    /**
     * Paramètres à ajouter aux champ de type file (ex: balise style, évenements JS...)
     * Type : chaine
     * Valeur par défaut : chaine vide
     * @access public
     */
    var $FieldOptions;

    /**
     * Définit si les champs sont obligatoires ou non.
     * Type : booléen
     * Valeur par défaut : false
     * @access public
     */
    var $Required;

    /**
     * Politique de sécurité max : ignore tous les fichiers exécutables / interprétable.
     * Type : booléen
     * Valeur par défaut : false
     * @access public
     */
    var $SecurityMax;

    /**
     * Permet de préciser un nom pour le fichier à uploader. Peut s'utiliser conjointement avec les propriétés $Suffixe / $Prefixe
     * Type : chaine
     * Valeur par défaut : chaine vide
     * @access public
     */
    var $Filename;

    /**
     * Préfixe pour un nom de fichier
     * Type : chaine
     * Valeur par défaut : chaine vide
     * @access public
     */
    var $Prefixe;

    /**
     * Suffixe pour un nom de fichier
     * Type : chaine
     * Valeur par défaut : chaine vide
     * @access public
     */
    var $Suffixe;

    /**
     * Indique s'il faut vérifier la provenance du formulaire d'upload des fichiers. Si la chaine est vide, aucune vérification n'est effectuée.
     * Pour lancer la vérification, il faut indiquer l'URI du formulaire de soumission des données.
     * Type : chaine
     * Valeur par défaut : chaine vide
     * @access public
     */
    var $CheckReferer;

    /**
     * Chaine de caractères représentant les entêtes de fichiers autorisés (mime-type).
     * Les entêtes doivent être séparées par des points virgules.
     * Type : chaine
     * Valeur par défaut : chaine vide (tout type d'entête autorisé)
     * @access public
     */
    var $MimeType;

    /**
     * Définit si les erreurs de configuration de la classe doivent être affichées en sortie écran et doivent stopper le script courant.
     * Type : booléen
     * Valeur par défaut : true
     * @access public
     */
    var $TrackError;

    /**
     * Handle de utils
     * Type : application
     * @access private
     */
    private $f;



    /***********************************************************************************
                                METHODES PUBLIQUES
    ***********************************************************************************/

    /**
     * Constructeur
     *
     * @access public
     * @return object   initialise les valeurs par défaut
     */
    function __construct($f) {
        //
        $this-> Extension    = '';
        $this-> DirUpload    = '';
        $this-> MimeType     = '';
        $this-> Filename     = '';
        $this-> FieldOptions = '';
        $this-> Fields       = 1;
        $this-> Debit        = 33.6;
        $this-> SecurityMax  = false;
        $this-> CheckReferer = false;
        $this-> Required     = false;
        $this-> TrackError   = true;
        $this-> ArrOfError   = Array();
        $this-> MaxFilesize  = str_replace('M', '', ini_get('upload_max_filesize')) * 1024;
        $this-> f = $f;
    }



    /**
     * Lance l'initialisation de la classe pour la génération du formulaire
     * @access public
     */
    function InitForm() {
        $this-> SetMaxFilesize();
        $this-> CreateFields();
    }



    /**
     * Retourne le tableau des erreurs survenues durant l'upload
     *
     * <code>
     * if ($UploadError) {
     *     print_r($Upload-> GetError);
     * }
     * </code>
     *
     * @access public
     * @param integer $num_field    numéro du champ 'file' sur lequel on souhaite récupérer l'erreur
     * @return array                tableau des erreurs
     */
    function GetError($num_field='') {
        if (Empty($num_field)) return $this-> ArrOfError;
        else                  return $this-> ArrOfError[$num_field];
    }



    /**
     * Retourne le tableau contenant les informations sur les fichiers uploadés
     *
     * <code>
     * if (!$UploadError) {
     *     print_r($Upload-> GetSummary());
     * }
     * </code>
     *
     * @access public
     * @param integer $num_field    numéro du champ 'file' sur lequel on souhaite récupérer les informations
     * @return array                tableau des infos fichiers
     */
    function GetSummary($num_field='') {
        if ($num_field == '') return $this-> Infos;
        else                  return $this-> Infos[$num_field];
    }



    /**
     * Lance les différents traitements nécessaires à l'upload
     * @access public
     */
    function Execute(){
        $this-> CheckConfig();
        $this-> VerifyReferer();
        $this-> SetTimeLimit();
        $this-> CheckUpload();
    }




    /*******************************************************************************************
                                METHODES A USAGE INTERNE
    ********************************************************************************************/

    /**
     * Methode lançant les verifications sur les fichiers. Initialisation de la variable $UploadError à true si erreur, lance la
     * methode d'ecriture toutes les verifications sont ok.
     * @access private
     */
    function CheckUpload() {
        global $UploadError;

        // Parcours des fichiers à uploader
        for ($i=0; $i < count($_FILES['userfile']['tmp_name']); $i++)  {

            // Récup des propriétés
            if(HTTPCHARSET=='UTF-8')
                $this-> _name  = utf8_decode($_FILES['userfile']['name'][$i]);     // nom du fichier
            else
                $this-> _name  = $_FILES['userfile']['name'][$i];     // nom du fichier
            $this-> _field = $i+1;                                // position du champ dans le formulaire à partir de 1 (0 étant réservé au champ max_file_size)
            $this-> _size  = $_FILES['userfile']['size'][$i];     // poids du fichier
            $this-> _temp  = $_FILES['userfile']['tmp_name'][$i]; // emplacement temporaire
            $this-> _ext   = strtolower(substr($this-> _name, strrpos($this-> _name, '.'))); // extension du fichier
            $this-> _type  = $this->f->get_file_type($this->_temp, $_FILES['userfile']['type'][$i]);     // type mime

            // On exécute les vérifications demandées
            if (is_uploaded_file($_FILES['userfile']['tmp_name'][$i])) {
                $this-> CheckSecurity();
                $this-> CheckMimeType();
                $this-> CheckExtension();
                $this-> CheckImg();
            } else $this-> AddError($_FILES['userfile']['error'][$i]); // Le fichier n'a pas été uploadé, on récupère l'erreur

            // Si le fichier a passé toutes les vérifications, on procède à l'upload, sinon on positionne la variable globale UploadError à 'true'
            if (!isset($this-> ArrOfError[$this-> _field])) {
                if(!$this-> WriteFile($this-> _name, $this-> _type, $this-> _temp, $this-> _size, $this-> _ext, $this-> _field)) {
                    $UploadError = true;
                }
            } else {
                $UploadError = true;
            }
        }
    }



    /**
     * Ecrit le fichier sur le serveur.
     *
     * @access private
     * @param string $name        nom du fichier sans son extension
     * @param string $type        entete du fichier
     * @param string $temp        chemin du fichier temporaire
     * @param string $size        taille du fichier en octets
     * @param string $temp        extension du fichier précédée d'un point
     * @param string $temp        extension du fichier précédée d'un point
     * @param string $num_fied    position du champ dans le formulaire à compter de 1
     * @return bool               true/false => succes/erreur
     */
    function WriteFile($name, $type, $temp, $size, $ext, $num_field) {

        $new_filename = NULL;

        if (is_uploaded_file($temp)) {

            // Nettoyage du nom original du fichier
            if (Empty($this-> Filename)) $new_filename = $this-> CleanStr(substr($name, 0, strrpos($name, '.')));
            else $new_filename = $this-> Filename;

            // Ajout préfixes / suffixes + extension :
            $new_filename = $this-> Prefixe . $new_filename . $this-> Suffixe . $ext;

            $fileContent = file_get_contents($temp);
            $metadata['filename']= $new_filename;
            $metadata['size']= $size;
            $metadata['mimetype']= $type;
            if($this->f->storage != NULL) {
                $uploaded = $this->f->storage->create_temporary($fileContent, $metadata);
                // Informations pouvant être utiles au développeur (si le fichier a pu être copié)
                if ($uploaded != OP_FAILURE) {
                    $this-> Infos[$num_field]['nom']          = $uploaded;
                    $this-> Infos[$num_field]['nom_originel'] = $name;
                    $this-> Infos[$num_field]['chemin']       = $this-> DirUpload . $new_filename;
                    $this-> Infos[$num_field]['poids']        = number_format($size/1024, 3, '.', '');
                    $this-> Infos[$num_field]['mime-type']    = $type;
                    $this-> Infos[$num_field]['extension']    = $ext;
                } else {
                    $this->AddError(12);
                    return false;
                }
            } else {
                $this->AddError(13);
                return false;
            }


            return true;
        }// End is_uploaded_file

        return false;
    } // End function


    /**
     * Vérifie la hauteur/largeur d'une image
     * @access private
     * @return bool
     */
    function CheckImg() {
        // Vérification de la largeur puis de la hauteur
        if ($taille = @getimagesize($this-> _temp) ) {
            if (!Empty($this-> ImgMaxWidth)  && $taille[0] > $this-> ImgMaxWidth)  $this-> AddError(8);
            if (!Empty($this-> ImgMaxHeight) && $taille[1] > $this-> ImgMaxHeight) $this-> AddError(9);
            if (!Empty($this-> ImgMinWidth)  && $taille[0] < $this-> ImgMinWidth) $this-> AddError(10);
            if (!Empty($this-> ImgMinHeight) && $taille[1] < $this-> ImgMinHeight) $this-> AddError(11);
        }

        return true;
    }



    /**
     * Vérifie l'extension des fichiers suivant celles précisées dans $Extension
     * @access private
     * @return bool
     */
    function CheckExtension() {
        $ArrOfExtension = explode(';', strtolower($this-> Extension));

        if (!Empty($this-> Extension) && !in_array($this-> _ext, $ArrOfExtension)) {
            $this-> AddError(7);
            return false;
        }

        return true;
    }



    /**
     * Vérifie l'entête des fichiers suivant ceux précisés dans $MimeType
     * @access private
     * @return bool
     */
    function CheckMimeType() {
        $ArrOfMimeType = explode(';', $this-> MimeType);

        if (!Empty($this-> MimeType) && !in_array($this-> _type, $ArrOfMimeType)) {
            $this-> AddError(6);
            return false;
        }

        return true;
    }



    /**
     * Ajoute une erreur pour le fichier spécifié dans le tableau des erreur
     * @access private
     */
    function AddError($code_erreur) {

       // Le tableau des erreurs est de la forme :$arr[position_du_champ][code_erreur] = 'description';

        switch ($code_erreur) {
            case 1 : $msg = __("Le fichier a charger excede la directive upload_max_filesize (php.ini).")." [".$this-> _name."]";
                     break;

            case 2 : $msg = __("Le fichier excede la directive MAX_FILE_SIZE qui a ete specifiee dans le formulaire.")." [".$this-> _name."]";
                     break;

            case 3 : $msg = __("Le fichier n'a pu etre charge completement.")." [".$this-> _name."]";
                     break;

            case 4 : $msg = __("Le champ du formulaire est vide.");
                     break;

            case 5 : $msg = __("Fichier potentiellement dangereux.")." [".$this-> _name."]";
                     break;

            case 6 : $msg = __("Le fichier n'est pas conforme a la liste des entetes autorises.")." [".$this-> _name."]";
                     break;

            case 7 : $msg = __("Le fichier n'est pas conforme a la liste des extension(s) autorisee(s)")." (".$this->Extension."). [".$this-> _name."]";
                     break;

            case 8 : $msg = __("La largeur de l'image depasse celle autorisee.")." [".$this-> _name."]";
                     break;

            case 9 : $msg = __("La hauteur de l'image depasse celle autorisee.")." [".$this-> _name."]";
                     break;

            case 10 : $msg = __("La largeur de l'image est inferieure a celle autorisee.")." [".$this-> _name."]";
                      break;

            case 11 : $msg = __("La hauteur de l'image est inferieure a celle autorisee.")." [".$this-> _name."]";
                      break;

            case 12 : $msg = __("Erreur a l'ecriture du fichier :")." [".$this-> _name."] ".__("Veuillez contacter votre administrateur.");
                      break;

            case 13 : $msg = __("Le filestorage n'est pas configure : Veuillez contacter votre administrateur.");
                      break;
        }


        if ($this-> Required && $code_erreur == 4) $this-> ArrOfError[$this-> _field][$code_erreur] = $msg;
        else if ($code_erreur != 4)                $this-> ArrOfError[$this-> _field][$code_erreur] = $msg;
    }


    /**
     * Vérifie les critères de la politique de sécurité
     * @access private
     * @return bool
     */
    function CheckSecurity() {
        // Bloque tous les fichiers executables, et tous les fichiers php pouvant être interprété mais dont l'entête ne peut les identifier comme étant dangereux
        if ($this-> SecurityMax===true) {
            // Note : is_executable ne fonctionne pas => ?
            if (ereg ('application/octet-stream', $this-> _type) || preg_match("/.php$|.inc$|.php3$/i", $this-> _ext) ) {
                $this-> AddError(5);
                return false;
            }
        }

        return true;
    }



    /**
     * Formate le répertoire passé en paramètre
     *     - convertit un chemin relatif en chemin absolu
     *     - ajoute si besoin le dernier slash (ou antislash suivant le système)
     * @access private
     */
    function FormatDir($Dir) {
        // Convertit les chemins relatifs en chemins absolus
        if (function_exists('realpath')) {
            if (realpath($Dir)) $Dir = realpath($Dir);
        }

        // Position du dernier slash/antislash
        if ($Dir[strlen($Dir)-1] != DIRECTORY_SEPARATOR) $Dir .= DIRECTORY_SEPARATOR;

        return $Dir;
    }



    /**
     * Formate la chaine passée en paramètre en nom de fichier standard (pas de caractères spéciaux ni d'espaces)
     * @access private
     * @param  string $str   chaine à formater
     * @return string        chaine formatée
     */
    function CleanStr($str) {
        $return = '';

        for ($i=0; $i <= strlen($str)-1; $i++) {
            if (mb_eregi('[a-z]',$str{$i}))              $return .= $str{$i};
            elseif (mb_eregi('[0-9]', $str{$i}))         $return .= $str{$i};
            elseif (mb_ereg('[àâäãáåÀÁÂÃÄÅ]', $str{$i})) $return .= 'a';
            elseif (mb_ereg('[æÆ]', $str{$i}))           $return .= 'a';
            elseif (mb_ereg('[çÇ]', $str{$i}))           $return .= 'c';
            elseif (mb_ereg('[éèêëÉÈÊËE]', $str{$i}))    $return .= 'e';
            elseif (mb_ereg('[îïìíÌÍÎÏ]', $str{$i}))     $return .= 'i';
            elseif (mb_ereg('[ôöðòóÒÓÔÕÖ]', $str{$i}))   $return .= 'o';
            elseif (mb_ereg('[ùúûüÙÚÛÜ]', $str{$i}))     $return .= 'u';
            elseif (mb_ereg('[ýÿÝ]', $str{$i}))         $return .= 'y';
            elseif (mb_ereg('[ ]', $str{$i}))            $return .= '_';
            elseif (mb_ereg('[.]', $str{$i}))            $return .= '_';
            else                                      $return .= $str{$i};
        }

        return $return;
    }



    /**
     * Vérifie que la provenance du formulaire est bien celle précisée dans la propriétée CheckReferer.
     * @access private
     * @return bool
     */
    function VerifyReferer() {
        if (!Empty($this-> CheckReferer)) {
            $headerref = $_SERVER['HTTP_REFERER'];

            // On enlève toutes les variables passées par url
            if (ereg("\?",$headerref)){
                list($url, $getstuff) = split('\?', $headerref);
                $headerref = $url;
            }

            if ($headerref == $this-> CheckReferer) return true;
            else $this-> Error(__("Acces refuse"));
        }
    }



    /**
     * Initialise si possible le temps d'exécution max du script en fonction du nombre de fichiers et de la propriété max_file_size
     * @access private
     */
    function SetTimeLimit(){
        // Le temps calculé est théoriquement le plus rapide => * 2
        @set_time_limit(ceil(ceil($this->  $_POST['MAX_FILE_SIZE'] * 8) / ($this-> Debit * 1000) * count($_FILES) * 2));
    }



    /**
     * Conversion du poids maximum d'un fichier exprimée en Ko en octets
     * @access private
     */
    function SetMaxFilesize() {
        (is_numeric($this-> MaxFilesize)) ? $this-> MaxFilesize = $this-> MaxFilesize * 1024 : $this-> Error(__("la propriete MaxFilesize doit etre une valeur numerique."));
    }



    /**
     * Crée les champs de type fichier suivant la propriété Fields dans un tableau $Field. Ajoute le contenu de FieldOptions aux champs.
     * @access private
     */
    function CreateFields() {
        if (!is_int($this-> Fields)) $this-> Error(__("la propriete Fields doit etre un entier."));

        for ($i=0; $i <= $this-> Fields; $i++) {
            if ($i == 0)  $this-> Field[] = '<input type="hidden" name="MAX_FILE_SIZE" value="'. $this-> MaxFilesize .'" />';
            else          $this-> Field[] = '<input type="file"  name="userfile[]" '. $this-> FieldOptions .' />';
        }
    }



    /**
     * Vérifie la configuration de la classe.
     * @access private
     */
    function CheckConfig() {
        if (!version_compare(phpversion(), '4.2.0')) $this-> Error(__("La version de php sur ce serveur est trop ancienne. La classe ne peut fonctionner qu'avec une version egale ou superieure a la version 4.1.0."));
        if (!is_string($this-> Extension)) $this-> Error(__("La propriete Extension est mal configuree."));
        if (!is_string($this-> MimeType)) $this-> Error(__("La propriete MimeType est mal configuree."));
        if (!is_string($this-> Filename)) $this-> Error(__("La propriete Filename est mal configuree."));
        if (!is_numeric($this-> Debit)) $this-> Error(__("La propriete Debit est mal configuree."));
        if (!is_bool($this-> Required)) $this-> Error(__("La propriete Required est mal configuree."));
        if (!is_bool($this-> SecurityMax)) $this-> Error(__("La propriete SecurityMax est mal configuree."));
        if (!Empty($this-> CheckReferer) && !@fopen($this-> CheckReferer, 'r')) $this-> Error(__("La propriete CheckReferer est mal configuree."));
        $this-> CheckImgPossibility();
    }



    /**
     * Vérifie les propriétés ImgMaxWidth/ImgMaxHeight
     * @access private
     */
    function CheckImgPossibility() {
        if (!Empty($this-> ImgMaxWidth)  && !is_numeric($this-> ImgMaxWidth))   $this-> Error(__("La propriete ImgMaxWidth est mal configuree."));
        if (!Empty($this-> ImgMaxHeight) && !is_numeric($this-> ImgMaxHeight))  $this-> Error(__("La propriete ImgMaxHeight est mal configuree."));
        if (!Empty($this-> ImgMinWidth)  && !is_numeric($this-> ImgMinWidth))   $this-> Error(__("La propriete ImgMinWidth est mal configuree."));
        if (!Empty($this-> ImgMinHeight) && !is_numeric($this-> ImgMinHeight))  $this-> Error(__("La propriete ImgMinHeight est mal configuree."));
    }



    /**
     * Affiche les erreurs de configuration et stoppe tout traitement
     * @access private
     */
    function Error($error_msg) {
        if ($this-> TrackError) {
            echo __("Erreur classe Upload")." : ".$error_msg;
            exit;
        }
    }

} // End Class
