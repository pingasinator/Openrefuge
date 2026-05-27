<?php
/**
 * Ce script contient l'initialisation et la déclaration des locales.
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_locales.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

/**
 * Définition du charset pour la base de donnees et pour le web
 */
// Dans le cas où aucun charset n'a été défini par l'applicatif, c'est le
// framework qui définit les charsets 
if (defined("CHARSET") !== true
    && defined("DBCHARSET") !== true
    && defined("HTTPCHARSET") !== true) {
    //
    define("CHARSET", 'UTF-8');
    define("DBCHARSET", 'UTF8');
    define("HTTPCHARSET", 'UTF-8');
}
// CHARSET est remplace par DBCHARSET et HTTPCHARSET
// Un mecanisme de compatibilite est conserve, mais la coherence est faible car
// le nom du charset est different selon la base de donnees
// et il est toujours different entre base de donnees et web
// Compatibilité
if (defined("CHARSET") !== true) {
    define("CHARSET", 'UTF-8');
}
if (!defined("DBCHARSET") and CHARSET != "UTF8") {
    define("DBCHARSET", CHARSET);
}
if (!defined("HTTPCHARSET") and CHARSET != "UTF8") {
    define("HTTPCHARSET", CHARSET);
}
// Définition des valeurs par défaut
if (defined("DBCHARSET") !== true) {
    define("DBCHARSET", 'UTF8');
}
if (defined("HTTPCHARSET") !== true) {
    define("HTTPCHARSET", 'UTF-8');
}

/**
 * Définition des constantes pour la gestion des locales et des traductions.
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
if (defined("LOCALE") !== true) {
    define("LOCALE", 'fr_FR');
}
if (defined("OM_LOCALES_DIRECTORY") !== true) {
    define("OM_LOCALES_DIRECTORY", PATH_OPENMAIRIE.'locales');
}
if (defined("OM_DOMAIN") !== true) {
    define("OM_DOMAIN", 'framework-openmairie');
}
if (defined("LOCALES_DIRECTORY") !== true) {
    define("LOCALES_DIRECTORY", '../locales');
}
if (defined("DOMAIN") !== true) {
    define("DOMAIN", 'openmairie');
}

/**
 * Initialise la gestion des locales et des traductions.
 *
 * @return void
 * @ignore
 */
function setMyLocale() {
    //
    putenv("LC_ALL=".LOCALE.".".HTTPCHARSET);
    setlocale(LC_ALL, LOCALE.".".HTTPCHARSET);
    bindtextdomain(OM_DOMAIN, OM_LOCALES_DIRECTORY);
    bindtextdomain(DOMAIN, LOCALES_DIRECTORY);
    textdomain(DOMAIN);
}

// Si l'extension gettext n'est pas présente alors on défini une fonction
// factice qui affiche simplement le libellé sans le traduire.
if (!function_exists("_")) {
    /**
     * @ignore
     */
    function _($msgid) {
        return $msgid;
    }
    /**
     * @ignore
     */
    function __($msgid, $msgstr_default = "") {
        if ($msgstr_default !== "") {
            return $msgstr_default;
        }
        return $msgid;
    }
} else {
    /**
     * @ignore
     */
    function  __($msgid, $msgstr_default = "") {
        $msgstr1 = _($msgid);
        if ($msgid !== $msgstr1) {
            return $msgstr1;
        }
        $msgstr2 = dgettext(OM_DOMAIN, $msgid);
        if ($msgid !== $msgstr2) {
            return $msgstr2;
        }
        if ($msgstr_default !== "") {
            return $msgstr_default;
        }
        return $msgid;
    }
    // L'extension gettext est présente alors on initialise la gestion des
    // locales et des traductions.
    setMyLocale();
}
