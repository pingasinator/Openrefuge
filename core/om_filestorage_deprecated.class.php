<?php
/**
 * Ce script contient la définition de la classe 'filestorage_deprecated'.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_filestorage_deprecated.class.php 4348 2018-07-20 16:49:26Z softime $
 */

/**
 * Définition de la classe 'filestorage_deprecated'.
 *
 * Cette classe est une classe de stockage spécifique aussi appelée plugin de
 * stockage pour le système d'abstraction de stockage des fichiers. Le principe
 * de ce plugin est de stocker tous les fichiers à plat selon la méthode
 * utilisée avant la création du système de stockage. Ce plugin a été créé
 * uniquement dans un soucis de garder la compatibilité pour les applications
 * existantes.
 */
class filestorage_deprecated extends filestorage_base {

    /**
     * Message à destination des logs.
     * @var string
     */
    var $NO_UID = 'On manque l\'uid';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $NO_FILE = 'Le fichier n\'est pas trouve';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $NO_METADATA = 'Erreur dans l\'extraction des metadonnees';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $NO_FILEDATA = 'Erreur dans l\'extraction des donnees brutes';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $NO_ROOT_DIR = 'Le chemin de racine de sauvegarde des fichiers n\'est pas set';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $SIZE_MISMATCH = 'La taille du fichier ne corresponds pas a la taille set dans metadata';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $RAW_DATA_WRITE_ERR = 'Erreur dans l\'ecriture de donnees brutes';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $METADATA_WRITE_ERR = 'Erreur dans l\'ecriture de metadonnees';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $SUCCESS = 'Succes';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $MKDIR_ERR = 'Erreur dans la creation du repertoire qui doit contenir les fichiers.';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $METADATA_MISSING = 'Les metadonnees non fournies';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $ILLEGAL_CHANGE = 'Essai de changer mimetype ou taille sans des donnees brutes';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $NO_CHANGE = 'Aucun changement de metadonnees (cas sans donnees brutes)';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $LOCK_FAILURE = 'Echec dans l\'obtention du lock';

    /**
     * Message à destination des logs.
     * @var string
     */
    var $FILE_HANDLE_ERR = 'Echec dans la creation du fichier';

    /**
     * Constructeur.
     *
     * Initialise le chemin racine de stockage des fichiers et le cré si il
     * n'existe pas.
     *
     * @param array $conf Tableau de configuration du storage :
     *                    $conf = array(
     *                        "path" => "../var/storage/",
     *                    );
     *
     * @return void
     */
    public function __construct($conf) {

        // Initialisation du chemin racine à null
        $this->path = null;

        // Si aucun tableau de configuration n'est transmis ou qu'aucun
        // chemin racine n'est transmis dans le tableau de configuration
        // alors on quitte le constructeur.
        if (is_null($conf) || !isset($conf['path'])) {
            return;
        }

        // Si le chemin racine n'existe pas alors on crée le répertoire de
        // manière récursive et transparente (si les permissions ne sont pas
        // positionnées pour que l'on puisse créer ce répertoire, aucune
        // erreur n'est levée, l'erreur sera levée lors de l'utilisation d'une
        // méthode de la classe de storage).
        if (!file_exists($conf['path'])) {
            @mkdir($conf['path'], 0755, true);
        }

        // Si le chemin racine existe et est un répertoire alors on le stocke
        // dans un attribut.
        if (file_exists($conf['path']) && is_dir($conf['path'])) {
            $this->path = $conf['path'];
        }

    }

    /**
     * Cette fonction ajoute dans le log.
     * @param string $file Le nom de fichier, ou l'identifiant du fichier
     * @param string $msg Le message a logger
     * @param string $func Le nom de la fonction
     */
    private function addToLog($file, $msg, $func = "") {
        logger::instance()->log("Filesystem storage - ".
            (($func)?$func.' : ':"").$msg.
            ' (fichier: '.$file.')', EXTRA_VERBOSE_MODE);
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
    private function getDirPath() {
        if (is_null($this->path)) {
            return null;
        }
        return $this->path;
    }

    /**
     * Cette fonction extraire les métadonnées d'un fichier sauvegarde.
     * @param string $uid L'identifiant du fichier
     * @return mixed|null En cas de succès on retourne le tableau contenant
     * les méthadones. En cas d'échec, on retourne null.
     */
    private function getMetadata($uid) {
        //
        $mimetype = $this->getMimetype($uid);
        //
        $size = $this->getSize($uid);
        //
        $filename = $this->getFilename($uid);
        // on retourne les métadonnées
        return array(
            "filename" => $filename,
            "mimetype" => $mimetype,
            "size" => $size,
        );
    }

    /**
     * Cette fonction retourne les metadonees représentées dans le format
     * d'une chaîne des caractères.
     * @param mixed metadata Les métadonnées d'un fichier
     * @return La chaîne des caractères contenant les métadonnées, ou null
     * si les métadonnées n'existent pas.
     */
    private function getMetadataStr($metadata) {
        // si les métadonnées n'existent pas, on retourne null
        if (is_null($metadata)) {
            return null;
        }
        // création de la chaîne des caractères contenant les métadonnées
        $metadata_str = "";
        foreach ($metadata as $key => $value) {
            $metadata_str .= $key . "=" .$value . "\n";
        }
        return $metadata_str;
    }

    /**
     * Cette fonction permet d'ecrire un fichier.
     *
     * @param string $path Le repertoire (chemin absolue) qui va contenir le
     * fichier
     * @param string $filename Le nom du fichier
     * @param string $file_content Le contenu du fichier
     * @param int size Le numéro des caractères à écrire
     * @param boolean $delete_on_error Marqueur de suppression sur erreur.
     */
    private function writeFile($path, $filename, $file_content,
                               $size, $delete_on_error = true) {

        // Le chemin absolue de fichier
        $file_path = $path.'/'.$filename;
        // on obtient le descripteur du fichier
        $file_handle = fopen($file_path, "w");
        // en cas d'échec, log le problème
        if ($file_handle === false) {
            $this->addToLog($filename, $this->FILE_HANDLE_ERR, __FUNCTION__);
            // returne false pour indiquer un problème
            return false;
        }
        // on écrit dans le fichier, et récupère le numéro des caractères écrits
        $num_chars = fwrite($file_handle, $file_content, $size);
        fclose($file_handle);

        // on vérifie que tout les données était écrit dans le fichie
        if ($num_chars === false || $num_chars != $size) {
            // suppression des répertoires qui contenaient les fichiers
            // et était crées expressément pour le stockage de ceux fichiers
            if ($delete_on_error) {
                unlink($path."/".$filename);
            }
            return false;
        }

        // on retourne succès
        return true;
    }

    /**
     * Cette fonction permet de sauvegarder le fichier contenant les donnees,
     * ainsi que le fichier contenant les métadonnées du fichier precedement
     * cité.
     * @param string $data Le contenu de fichier
     * @param mixed $metadata Les metadata du fichier à sauvegarder
     * @param string $mode origine des données (content/temporary/path)
     * @return string En cas de succès on retourne l'uid du fichier. En cas
     * d'erreur on retourne OP_FAILURE
     */
    public function create($data, $metadata, $mode = "from_content") {
        // vérification de l'existence du chemin qui doit contenir les fichiers
        if (is_null($this->path)) {
            $this->addToLog($metadata['filename'], $this->NO_ROOT_DIR,
                            __FUNCTION__);
            return OP_FAILURE;
        }

        // test du mode et récupération du fichier et des métadonnées
        // cas où le fichier provient du système de fichier temporaire
        if ($mode == "from_temporary") {
            $file = $this->getContent($data, "from_temporary");
            // récupération du contenu du fichier
            $file_content = $file["file_content"];
            // fusion des métadonnées
            $metadata = array_merge($metadata, $file["metadata"]);
        } elseif ($mode == "from_path") {
            // cas où le path du fichier est passé en paramètre
            $file = $this->getContent($data, "from_path");
            // récupération du contenu du fichier
            $file_content = $file["file_content"];
        } elseif ($mode == "from_content") {
            // cas normal, le contenu du fichier est passé en paramètre
            $file_content = $data;
        }
        // génération d'unique identifiant du fichier
        $uid = $metadata["filename"];

        // on vérifie que la taille spécifié dans le metadata correspond à
        // celle du fichier
        if (strlen($file_content) != $metadata['size']) {
            $this->addToLog($metadata['filename'], $this->SIZE_MISMATCH,
                            __FUNCTION__);
            return OP_FAILURE;
        }

        // le chemin qui contient les fichiers
        $dir_path = $this->getDirPath();
        // si le chemin n'existe pas, on log le fait et on retourne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return OP_FAILURE;
        }

        // création du fichier et le fichier contant les metadata

        // écriture de fichier
        $ret = $this->writeFile($dir_path, $uid, $file_content,
                                     $metadata['size']);
        // en cas d'erreur failure
        if ($ret === false) {
            $this->addToLog($metadata['filename'],
                    $this->RAW_DATA_WRITE_ERR, __FUNCTION__);
            return OP_FAILURE;
        }

        // En cas de succès, si il s'agit d'un create from_temporary
        // on supprime le fichier temporaire
        if ($mode == "from_temporary") {
            if($this->delete_temporary($data) === false) {
                $this->addToLog($uid, __("La suppression du fichier temporaire a echouee."), __FUNCTION__);
            }
        }

        // succès et on retourne l'uid
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
        return $uid;

        // par défaut on retourne échec
        $this->addToLog($metadata['filename'], $this->MKDIR_ERR, __FUNCTION__);
        return OP_FAILURE;
    }

    /**
     * Cette fonction permet de supprimer un ficher sauvegardé
     * sur le filesystem.
     * @param string $uid L'identifiant du fichier
     * @return string En cas de succès on retourne l'identifiant
     * du fichier qui était supprimé. Autrement on retourne OP_FAILURE
     */
    public function delete($uid) {
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return OP_FAILURE;
        }

        // le chemin qui contient les fichiers
        $dir_path = $this->getDirPath();
        // si le chemin n'existe pas, on log le fait et on retourne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return OP_FAILURE;
        }

        // on supprime le dossier contenant les fichiers s'il contient seulement
        // les fichiers connectés avec l'identifiant $uid
        unlink($dir_path."/".$uid);
        // succès
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
        return $uid;
    }

    /**
     * Cette fonction permet de modifier les données d'un fichier (données
     * brutes et métadonnées).
     *
     * @param string $uid L'identifiant du fichier a récupérer
     * @param string $data Les données brutes.
     * @param mixed $metadata Tableau contenant les métadonnées du fichier
     * @param string $mode origine des données (content/temporary/path)
     *
     * @return En cas de succès on retourne l'uid du fichier. En cas d'échec
     * on retourne OP_FAILURE
     */
    public function update($uid, $data, $metadata, $mode = "from_content") {
        // test du mode et récupération du fichier et des métadonnées
        // cas où le fichier provient du système de fichier temporaire
        if ($mode == "from_temporary") {
            $file = $this->getContent($data, "from_temporary");
            // récupération du contenu du fichier
            $file_content = $file["file_content"];
            // fusion des métadonnées
            $metadata = array_merge($metadata, $file["metadata"]);
        } elseif ($mode == "from_path") {
            // cas où le path du fichier est passé en paramètre
            $file = $this->getContent($data, "from_path");
            // récupération du contenu du fichier
            $file_content = $file["file_content"];
        } elseif ($mode == "from_content") {
            // cas normal, le contenu du fichier est passé en paramètre
            $file_content = $data;
        }
        $metadata["filename"] = $uid;
        $uid = $this->create($file_content, $metadata);

        // En cas de succès, si il s'agit d'un create from_temporary
        // on supprime le fichier temporaire
        if ($mode == "from_temporary") {
            if ($this->delete_temporary($data) === false) {
                $this->addToLog($uid, __("La suppression du fichier temporaire a echouee."), __FUNCTION__);
            }
        }
        return $uid;
    }

    /**
     * Cette fonction permet de récupérer les données d'un fichier (données
     * brutes et métadonnées).
     * @param string $uid L'identifiant du fichier a récupérer
     * @return En cas de succès on retourne les donnes du fichier. En cas
     * d'échec on retourne null
     */
    public function get($uid) {
        //
        // si l'identifiant du fichier est vide, on retourne erreur
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return null;
        }
        // chemin vers les fichiers
        $dir_path = $this->getDirPath();
        // si le chemin n'existe pas, on retorurne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return null;
        }

        $metadata = $this->getMetadata($uid);
        if (is_null($metadata) || count($metadata) == 0) {
            $this->addToLog($uid, $this->NO_METADATA, __FUNCTION__);
            return null;
        }

        // nom du fichier contenant les données brutes
        $file_path = $dir_path . '/' . $uid;

        //
        if (!file_exists($file_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return null;
        }

        // on ouvre descripteur vers le fichier contenant les données brutes
        $file_handle = fopen($file_path, "r");
        if ($file_handle === false) {
            $this->addToLog($uid, $this->NO_FILEDATA, __FUNCTION__);
            return null;
        }
        // récupération de contenu du fichier
        $file_content = '';
        if ($metadata['size'] != 0) {
            $file_content = fread($file_handle, $metadata['size']);
        }
        fclose($file_handle);

        // succès, retour des données
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
        return array('file_content' => $file_content, 'metadata' => $metadata);
    }

    /**
     * Cette fonction permet de faire un lock sur un fichier. On peut
     * créer le lock seulement si il n'y a aucun lock existant.
     * @param string $uid L'identifiant du fichier
     * @return string En cas de succès on retourne true. Autrement on
     * retourne false.
     */
    public function lock($uid) {
        //
        // si l'identifiant du fichier est vide, on retourne erreur
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return false;
        }
        // répertoire contenant les fichiers
        $dir_path = $this->getDirPath();
        // si le repertoire n'existe pas, on retourne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return false;
        }

        // nom du fichier qui sert comme lock
        $file_path = $dir_path . '/' . $uid;

        // on essai de créer le fichier qui sert comme un lock
        if (@link($file_path, $file_path.'.lock')) {
            // si le fichier etait cree, on retourne succes
            $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
            return true;
        }
        // on n'a pas réussi de créer le fichier du lock et on retourne erreur
        $this->addToLog($uid, $this->LOCK_FAILURE, __FUNCTION__);
        return false;
    }

    /**
     * Cette fonction permet de lâcher le lock sur un fichier.
     * @param string $uid L'identifiant du fichier
     */
    public function unlock($uid) {
        //
        // si l'identifiant du fichier est vide, on retourne erreur
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
        }
        // le répertoire qui contient les fichiers
        $dir_path = $this->getDirPath();
        // si le repertoire n'existe pas, on retourne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
        }

        // le nom du fichier qui sert comme le lock
        $file_path = $dir_path . '/' . $uid . '.lock';
        if (is_file($file_path)) {
            // on supprime le fichier qui sert comme lock
            @unlink( $file_path );
        }
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
    }

    /**
     * Cette fonction retourne le nom de fichier qui est stocké
     * sous l'uid passé en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return Le nom de fichier, si le fichier est trouvé, sinon
     * on retourne null.
     */
    public function getFilename($uid) {
        // si l'identifiant du fichier est vide, on retourne erreur
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return null;
        }

        // on retourne le nom du fichier
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
        return $uid;
    }

    /**
     * Cette fonction retourne le mime type de fichier qui est stocké
     * sous l'uid passé en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return Le mime type de fichier, si le fichier est trouvé, sinon
     * on retourne null.
     */
    public function getMimetype($uid) {
        // si l'identifiant du fichier est vide, on retourne erreur
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return null;
        }

        // le chemin qui contient les fichiers
        $dir_path = $this->getDirPath();
        // si le chemin n'existe pas, on log le fait et on retourne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return OP_FAILURE;
        }

        //
        $file_path = $dir_path . '/' . $uid;
        //
        if (!file_exists($file_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return null;
        }

        // on retourne le mimetype du fichier
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
        return mime_content_type($file_path);
    }

    /**
     * Cette fonction retourne la taille de fichier qui est stocké
     * sous l'uid passé en paramètre.
     * @param string $uid L'identifiant de fichier
     * @return La taille de fichier, si le fichier est trouvé, sinon
     * on retourne null.
     */
    public function getSize($uid) {
        // si l'identifiant du fichier est vide, on retourne erreur
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return null;
        }

        // le chemin qui contient les fichiers
        $dir_path = $this->getDirPath();
        // si le chemin n'existe pas, on log le fait et on retourne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return OP_FAILURE;
        }

        //
        $file_path = $dir_path . '/' . $uid;
        //
        if (!file_exists($file_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return null;
        }

        // on retourne la taille du fichier
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
        return filesize($file_path);
    }


    /**
     * Cette fonction retourne un tableau associatif qui contient le nom,
     * le mime type et la taille de fichier qui est stocké sous l'uid
     * passé en paramètre.
     *
     * @param string $uid L'identifiant de fichier
     * @return La taille de fichier, si le fichier est trouvé, ou
     * OP_FAILURE si la classe de sauvegarde n'était pas instanciée
     */
    public function getInfo($uid) {
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return null;
        }

        // on obtient les métadonnées du fichier
        $data = $this->getMetadata($uid);
        if (is_null($data)) {
            $this->addToLog($uid, $this->NO_METADATA, __FUNCTION__);
            return null;
        }

        // on retourne les métadonnées
        $this->addToLog($uid, $this->SUCCESS, __FUNCTION__);
        return $data;
    }

    /**
     * Cette fonction retourne le chemin du fichier.
     *
     * @param string $uid L'identifiant du fichier
     * @return Le répertoire qui contient les fichiers. Si le chemin de
     * racine de sauvegarde des fichiers n'est pas set, on retourne null.
     */
    public function getPath($uid) {
        // si l'identifiant du fichier est vide, on retourne erreur
        if (is_null($uid) || $uid == "") {
            $this->addToLog("", $this->NO_UID, __FUNCTION__);
            return null;
        }
        // chemin vers les fichiers
        $dir_path = $this->getDirPath();
        // si le chemin n'existe pas, on retorurne erreur
        if (!is_dir($dir_path)) {
            $this->addToLog($uid, $this->NO_FILE, __FUNCTION__);
            return null;
        }
        // nom du fichier contenant les données brutes
        $file_path = $dir_path . '/' . $uid;
        //
        return $file_path;
    }

}
