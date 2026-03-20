<?php
/**
 * Ce script permet de définir une configuration LDAP pour les tests.
 *
 * Le LDAP utilisé est fourni par www.forumsys.com qui le met gracieusement à
 * disposition.
 * Toutes les informations sont disponibles ici :
 * http://www.forumsys.com/en/tutorials/integration-how-to/ldap/online-ldap-test-server/
 * Pour tester la fonctionnalité "Interface avec un annuaire", nous configurons
 * l'annuaire pour synchroniser les quatre utilisateurs suivants :
 *  - einstein:password
 *  - newton:password
 *  - galieleo:password
 *  - tesla:password
 *
 * @package framework_openmairie
 * @version SVN : $Id$
 */

//
$directory = array(
    "ldap-test" => array(
        'ldap_server' => 'ldap.forumsys.com',
        'ldap_server_port' => '389',
        'ldap_admin_login' => 'cn=read-only-admin,dc=example,dc=com',
        'ldap_admin_passwd' => 'password',
        'ldap_base' => 'dc=example,dc=com',
        'ldap_base_users' => 'dc=example,dc=com',
        'ldap_user_filter' => '(&(objectclass=inetOrgPerson)(|(uid=einstein)(uid=newton)(uid=galieleo)(uid=tesla)))',
        'ldap_login_attrib' => 'uid',
        'ldap_more_attrib' => array(
            'email' => 'mail',
            'nom' => 'cn',
        ),
        'default_om_profil' => 1,
    ),
);
