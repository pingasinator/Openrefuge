<?php
/**
 * Ce script contient la définition des classes 'filestorage' et 'base_storage'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_filestorage.class.php 4348 2018-07-20 16:49:26Z softime $
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
require_once PATH_OPENMAIRIE."om_logger.class.php";

/**
 *
 */
define('OP_FAILURE', 'OP_FAILURE');

/**
 * Définition de la classe 'filestorage'.
 *
 * Cette classe est une classe d'abstraction de stockage de fichiers.
 * C'est cette classe qui est instanciée et utilisée par d'autres scripts pour
 * gérer la création, récupération, suppression de fichiers et ce peu importe
 * le stockage utilisé. Son objectif est d'instancier la classe de stockage
 * spécifique aussi appelée plugin de stockage correspondant au paramétrage
 * sélectionné. Cette classe de stockage spécifique hérite de la classe
 * 'base_storage' qui lui sert de modèle.
 */
class filestorage {

    /**
     * Cet attribut permet de stocker le type de stockage
     * cette valeur doit être remplie en fonction du paramétrage
     * présent dans le fichier dyn/filestorage.inc.php
     * @var null|string
     */
    var $type_storage = null;

    /**
     * Le constructeur instancie la classe de connecteur
     * envoyée par le paramétrage lors de l'instanciation et lui
     * transmettre le paramétrage en question.
     *
     * @param array $conf Tableau de configuration.
     */
    public function __construct($conf = array()) {
        // initialisation des attributs
        $this->type_storage = null;
        $this->storage = null;

        // recuperation du type de storage
        if (isset($conf['storage']) && $conf['storage'] != "") {
            $this->type_storage = $conf['storage'];
        }

        // recuperation des donnees dependent du type de storage
        if (!is_null($this->type_storage)) {
            // Si l'url du fichier de la classe du storage a été renseigné
            if (isset($conf['storage_path']) && $conf['storage_path'] !== "") {
                require_once $conf['storage_path'];
                $class_name = $this->type_storage;
            } else {
                require_once PATH_OPENMAIRIE."om_filestorage_".
                $this->type_storage.".class.php";
                $class_name = 'filestorage_'.$this->type_storage;
            }
            $this->storage = new $class_name($conf);
        } else {
            // log le fait que le storage n'est pas bon
            logger::instance()->log(__CLASS__.' - '.__FUNCTION__.
                ' : Classe de sauvegarde de fichiers n\'etait pas cree',
                EXTRA_VERBOSE_MODE);
        }

        // Instanciation du filestorage pour enregistrement temporaire
        require_once PATH_OPENMAIRIE."om_filestorage_".
                    $conf["temporary"]["storage"].".class.php";
        $class_name = 'filestorage_'.$conf["temporary"]["storage"];
        $this->storage->temporary_storage = new $class_name($conf["temporary"]);
    }


    /**
     * Le destructeur permet de détruire la ressource instanciée
     * dans le constructeur.
     */
    public function __destruct() {
        if (!is_null($this->storage)) {
            unset($this->storage);
        }
    }

    /**
     * Cette fonction retourne le répertoire qui stocke le fichier avec
     * les données brutes, et le fichier avec les métadonnées. Quand un
     * fichier qui sert comme lock est créé il est placé dans ce même
     * répertoire
     * @param string $uid L'identifiant du fichier
     * @return Le répertoire qui contient les fichiers. Si le chemin de
     * racine de sauvegarde des fichiers n'est pas set, on retourne null.
     */
    public function getPath($uid) {
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        // appel à la resource de stockage pour stocker le fichier
        return $this->storage->getPath($uid);
    }


    /**
     * Cette fonction appelle la fonction de mémé nom de la
     * ressource de stockage pour sauvegarder un fichier
     * @param string $file_content Le contenu de fichier
     * @param mixed $metadata Les metadata du fichier à sauvegarder
     * @param string $mode origine des données (content/temporary/path)
     * @return string Le résultat de création retourné par la ressource de
     * stockage. Si la classe de sauvegarde n'était pas instanciée, on
     * retourne OP_FAILURE.
     */
    public function create($file_content, $metadata, $mode = "from_content") {
        // on retourne une erreur si la ressource de stockage n'était pas trouvée
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        // appel à la resource de stockage pour stocker le fichier
        return $this->storage->create($file_content, $metadata, $mode);
    }


    /**
     * Cette fonction appelle la fonction de mémé nom de la
     * ressource de stockage pour modifier un fichier, ou des metadata
     * d'un fichier.
     * @param string $uid L'identifiant de fichier
     * @param string $file_content Le contenu de fichier
     * @param mixed $metadata Les metadata du fichier à sauvegarder
     * @param string $mode origine des données (content/temporary/path)
     * @return string Le résultat de modification retourné par la ressource de
     * stockage. Si la classe de sauvegarde n'était pas instanciée, on
     * retourne OP_FAILURE.
     */
    public function update($uid, $file_content, $metadata, $mode = "from_content") {
        //
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        $ret = $this->storage->lock($uid);
        if ($ret !== true) {
            return $ret;
        }
        //
        $ret = $this->storage->update($uid, $file_content, $metadata, $mode);
        //
        $this->storage->unlock($uid);
        //
        return $ret;
    }


    /**
     * Cette fonction appelle la fonction de même nom de la ressource de
     * stockage pour modifier les métadonnées d'un fichier.
     *
     * @param string $uid      Identifiant du fichier dans le filestorage.
     * @param array  $metadata Liste des métadonnées à mettre à jour.
     *
     * @return mixed Identifiant du fichier ou OP_FAILURE.
     */
    public function update_metadata($uid, array $metadata) {
        //
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        $ret = $this->storage->lock($uid);
        if ($ret !== true) {
            return $ret;
        }
        //
        $ret = $this->storage->update_metadata($uid, $metadata);
        //
        $this->storage->unlock($uid);
        //
        return $ret;
    }


    /**
     * Cette fonction appelle la fonction de même nom de la
     * ressource de stockage pour supprimer un fichier
     * @param string $uid L'identifiant de fichier
     * @return string Le résultat de suppression retourné par la ressource de
     * stockage. Si la classe de sauvegarde n'était pas instanciée, on
     * retourne OP_FAILURE.
     */
    public function delete($uid) {
        //
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        $ret = $this->storage->lock($uid);
        if ($ret === true) {
            //
            $ret = $this->storage->delete($uid);
            // il est correct de faire un unlock, meme si, techniquement,
            // les fichiers ne doivent pas exister
            $this->storage->unlock($uid);
        }
        return $ret;
    }


    /**
     * Cette fonction appelle la fonction de même nom de la
     * ressource de stockage pour retourner les données d'un fichier,
     * ce qui inclure le fichier lui même, et le metadata.
     * @param string $uid L'identifiant de fichier
     * @return string Le résultat de extraction retourné par la ressource de
     * stockage. Si la classe de sauvegarde n'était pas instanciée, on
     * retourne OP_FAILURE.
     */
    public function get($uid) {
        //
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        return $this->storage->get($uid);
    }

    /**
     * Cette fonction appelle la fonction de même nom de la
     * ressource de stockage pour sauvegarder un fichier temporaire
     * @param string $data Le contenu de fichier
     * @param mixed $metadata Les metadata du fichier à sauvegarder
     * @param string $mode origine des données (content/temporary/path)
     * @return string Le résultat de création retourné par la ressource de
     * stockage. Si la classe de sauvegarde n'était pas instanciée, on
     * retourne OP_FAILURE.
     */
    public function create_temporary($data, $metadata, $mode = "from_content") {
        // on retourne une erreur si la ressource de stockage n'était pas trouvée
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        // appel à la resource de stockage pour stocker le fichier
        return $this->storage->create_temporary($data, $metadata, $mode);
    }

    /**
     * Cette fonction appelle la fonction de même nom de la
     * ressource de stockage pour supprimer un fichier temporaire
     * @param string $uid L'identifiant de fichier
     * @return string Le résultat de suppression retourné par la ressource de
     * stockage. Si la classe de sauvegarde n'était pas instanciée, on
     * retourne OP_FAILURE.
     */
    public function delete_temporary($uid) {
        //
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        $ret = $this->storage->lock($uid);
        if ($ret === true) {
            //
            $ret = $this->storage->delete_temporary($uid);
            // il est correct de faire un unlock, meme si, techniquement,
            // les fichiers ne doivent pas exister
            $this->storage->unlock($uid);
        }
        return $ret;
    }

    /**
     * Cette fonction appelle la fonction de même nom de la
     * ressource de stockage pour retourner les données d'un fichier temporaire,
     * ce qui inclure le fichier lui même, et le metadata.
     * @param string $uid L'identifiant de fichier
     * @return string Le résultat de extraction retourné par la ressource de
     * stockage. Si la classe de sauvegarde n'était pas instanciée, on
     * retourne OP_FAILURE.
     */
    public function get_temporary($uid) {
        //
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        return $this->storage->get_temporary($uid);
    }

    /**
     * Cette fonction retourne le nom de fichier temporaire qui est stocké
     * sous l'uid passé en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return Le nom de fichier, si le fichier est trouvé, ou
     * OP_FAILURE si la classe de sauvegarde n'était pas instanciée
     */
    public function getFilename_temporary($uid) {
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        return $this->storage->getFilename_temporary($uid);
    }


    /**
     * Cette fonction retourne le répertoire qui stocke le fichier avec
     * les données brutes, et le fichier avec les métadonnées. Quand un
     * fichier qui sert comme lock est créé il est placé dans ce même
     * répertoire
     * @param string $uid L'identifiant du fichier
     * @return Le répertoire qui contient les fichiers. Si le chemin de
     * racine de sauvegarde des fichiers n'est pas set, on retourne null.
     */
    public function getPath_temporary($uid) {
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        // appel à la resource de stockage pour stocker le fichier
        return $this->storage->getPath_temporary($uid);
    }


    /**
     * Cette fonction retourne le nom de fichier qui est stocké
     * sous l'uid passé en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return Le nom de fichier, si le fichier est trouvé, ou
     * OP_FAILURE si la classe de sauvegarde n'était pas instanciée
     */
    public function getFilename($uid) {
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        return $this->storage->getFilename($uid);
    }


    /**
     * Cette fonction retourne le mime type de fichier qui est stocké
     * sous l'uid passé en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return Le mime type de fichier, si le fichier est trouvé, ou
     * OP_FAILURE si la classe de sauvegarde n'était pas instanciée
     */
    public function getMimetype($uid) {
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        return $this->storage->getMimetype($uid);
    }


    /**
     * Cette fonction retourne la taille de fichier qui est stocké
     * sous l'uid passé en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return La taille de fichier, si le fichier est trouvé, ou
     * OP_FAILURE si la classe de sauvegarde n'était pas instanciée
     */
    public function getSize($uid) {
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        return $this->storage->getSize($uid);
    }


    /**
     * Cette fonction retourne le résultat d'appel à la fonction qui
     * retourne les métadonnées du fichier stocké sous l'uid passé
     * en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return Le résultat d'appel à la fonction de la classe utilisée
     * pour le stockage des fichiers. On retourne OP_FAILURE si la classe
     * de sauvegarde n'était pas instanciée
     */
    public function getInfo($uid) {
        if (is_null($this->storage)) {
            return OP_FAILURE;
        }
        return $this->storage->getInfo($uid);
    }
}



/**
 * Définition de la classe 'filestorage_base'.
 */
class filestorage_base {

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de permettre la sauvegarde d'un fichier contenant les données,
     * ainsi que la sauvegarde de fichier contenant les métadonnées du fichier
     * précédemment cité.
     * @param string $file_content Le contenu de fichier
     * @param mixed $metadata Les métadonnées du fichier à sauvegarder
     * @param string $mode origine des données (content/temporary/path)
     * @return null
     */
    protected function create($file_content, $metadata, $mode = "from_content") {
        return null;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de permettre la suppression d'un fichier contenant les données,
     * ainsi que de supprimer le fichier contenant les métadonnées du fichier
     * précédemment cité.
     * @param string $uid L'identifiant du fichier
     * @return L'identifiant du fichier
     */
    protected function delete($uid) {
        return $uid;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de permettre la modification d'un fichier contenant les données,
     * ainsi que la modification du fichier contenant les métadonnées du fichier
     * précédemment cité.
     * @param string $uid L'identifiant du fichier
     * @param string $file_content Le contenu de fichier
     * @param mixed $metadata Les métadonnées du fichier à sauvegarder
     * @param string $mode origine des données (content/temporary/path)
     * @return string $uid L'identifiant du fichier
     */
    protected function update($uid, $file_content, $metadata, $mode = "from_content") {
        return $uid;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de permettre la modification des métadonnées d'un fichier.
     *
     * @param string $uid      Identifiant du fichier dans le filestorage.
     * @param array  $metadata Liste des métadonnées à mettre à jour.
     *
     * @return string Identifiant du fichier.
     */
    protected function update_metadata($uid, array $metadata) {
        return $uid;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de permettre l'extraction d'un fichier contenant les données,
     * ainsi que l'extraction de contenu du fichier contenant les métadonnées
     * du fichier précédemment cité.
     * @param string $uid L'identifiant du fichier
     */
    protected function get($uid) {
        return null;
    }

    /**
     * Créer un fichier temporaire sur le filesystem
     * @param  [string] $data contenu du fichier
     * @param  [mixed] $metadata     [tableau de méta données]
     * @param string $mode origine des données (content/temporary/path)
     * @return [string] uid
     */
    public function create_temporary($data, $metadata, $mode) {
        return $this->temporary_storage->create($data, $metadata, $mode);
    }

    /**
     * Créer un fichier temporaire sur le filesystem
     * @param  [string] $uid uid du fichier
     * @return uid
     */
    public function delete_temporary($uid) {
        return $this->temporary_storage->delete($uid);
    }

    /**
     * Son but est de permettre l'extraction d'un fichier contenant les données,
     * ainsi que l'extraction de contenu du fichier contenant les métadonnées
     * @param  [string] $uid uid du fichier
     * @return null
     */
    public function get_temporary($uid) {
        return $this->temporary_storage->get($uid);
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de retourner le nom du fichier temporaire identifié par l'uid passe en
     * paramétré.
     * @param string $uid L'identifiant du fichier
     * @return null
     */
    public function getFilename_temporary($uid) {
        return $this->temporary_storage->getFilename($uid);
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de retourner le path du fichier temporaire identifié par l'uid passe en
     * paramétré.
     * @param string $uid L'identifiant du fichier
     * @return null
     */
    public function getPath_temporary($uid) {
        return $this->temporary_storage->getPath($uid);
    }

    /**
     * Permet de recupérer le contenu et les métadonnées d'un fichier en fonction
     * du mode passé en paramètre qui définira le type de la donnée.
     * @param  string $data handle du fichier
     * @param string $mode origine des données (content/temporary/path)
     * @return mixed       Contenu du fichier
     */
    protected function getContent($data, $mode) {
        if ($mode == "from_temporary") {
            return $this->temporary_storage->get($data);
        } elseif ($mode == "from_path") {
            return array('file_content' => file_get_contents($data), 'metadata' => "");
        }
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de permettre le lock d'un fichier.
     * @param string $uid L'identifiant du fichier
     * @return false
     */
    protected function lock($uid) {
        //
        return false;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de lâcher le lock sur un fichier.
     * @param string $uid L'identifiant du fichier
     */
    protected function unlock($uid) {
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de retourner le nom du fichier identifié par l'uid passe en
     * paramétré.
     * @param string $uid L'identifiant du fichier
     * @return null
     */
    protected function getFilename($uid) {
        return null;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de retourner le mime type du fichier identifié par l'uid passe en
     * paramétré.
     * @param string $uid L'identifiant du fichier
     * @return null
     */
    protected function getMimetype($uid) {
        return null;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de retourner le path du fichier identifié par l'uid passe en
     * paramétre.
     * @param string $uid L'identifiant du fichier
     * @return null
     */
    protected function getPath($uid) {
        return null;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de retourner la taille du fichier identifié par l'uid passe en
     * paramétré.
     * @param string $uid L'identifiant du fichier
     * @return null
     */
    protected function getSize($uid) {
        return null;
    }

    /**
     * Cette fonction doit être implémente par des classes dérivées. Son but
     * est de retourner les metadonnees du fichier identifié par l'uid passe en
     * paramétré.
     * @param string $uid L'identifiant du fichier
     * @return null
     */
    protected function getInfo($uid) {
        return null;
    }
}
