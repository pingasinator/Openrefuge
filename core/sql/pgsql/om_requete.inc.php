<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_requete.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_requete.inc.php")) {
    include "../gen/sql/pgsql/om_requete.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_requete.inc.php";
}
