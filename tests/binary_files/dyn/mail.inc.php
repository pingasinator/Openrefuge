<?php
/**
 * Ce script permet de définir une configuration SMTP pour les tests.
 *
 * Le SMTP utilisé est maildump : https://pypi.python.org/pypi/maildump.
 *
 * @package framework_openmairie
 * @version SVN : $Id$
 */

$mail = array();
$mail["mail-test"] = array(
    'mail_host' => 'localhost',
    'mail_port' => '1025',
    'mail_username' => '',
    'mail_pass' => '',
    'mail_from' => 'contact@openmairie.org',
    'mail_from_name' => 'Administrateur Framework openMairie',
);
