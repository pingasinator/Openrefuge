<?php
/**
 *
 *
 * @package framework_openmairie
 * @version SVN : $Id: om_dashboard.inc.php 4348 2018-07-20 16:49:26Z softime $
 */

//
if (file_exists("../gen/sql/pgsql/om_dashboard.inc.php")) {
    include "../gen/sql/pgsql/om_dashboard.inc.php";
} else {
    include PATH_OPENMAIRIE."gen/sql/pgsql/om_dashboard.inc.php";
}
