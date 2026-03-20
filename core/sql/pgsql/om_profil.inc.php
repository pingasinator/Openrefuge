<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_profil.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_profil.inc.php")) {
    include "../gen/sql/pgsql/om_profil.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_profil.inc.php";
}
