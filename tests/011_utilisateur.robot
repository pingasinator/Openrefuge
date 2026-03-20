*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Utilisateur"...


*** Test Cases ***
Synchronisation des utilisateurs avec un annuaire LDAP

    [Documentation]  On teste la synchronisation des utilisateurs avec le ldap
    ...  Les utilisateurs qui devront être ajoutés et mis à jour :
    ...  einstein, newton, galieleo, tesla
    ...  Et les utilisateurs qui devront être supprimés :
    ...  ldap_instructeur et ldap_service

    [Tags]  exclude

    #
    Depuis la page d'accueil  admin  admin
    # On accède à l'écran de synchronisation via le menu
    Go To Submenu In Menu  administration  annuaire
    # On vérifie le titre de l'écran
    Page Title Should Be  Administration > Utilisateur > Synchronisation Annuaire
    # On vérifie que le menu est ouvert sur l'élément correct
    Submenu In Menu Should Be Selected  administration  annuaire
    # ATTENTION POSTULAT : Il y a deux utilisateurs LDAP dans la base
    # et le ldap auquel nous sommes connectés contient 4 utilisateurs qui ne
    # sont pas les deux déjà en base
    Page Should Contain  Il y a 4 utilisateur(s) présent(s) dans l'annuaire et non présent(s) dans la base => 4 ajout(s)
    Page Should Contain  Il y a 0 utilisateur(s) présent(s) dans la base et non présent(s) dans l'annuaire => 0 suppression(s)
    Page Should Contain  Il y a 0 utilisateur(s) présent(s) à la fois dans la base et l'annuaire => 0 mise(s) à jour
    # On clique sur "Synchroniser"
    Click On Submit Button
    # On vérifie que tout s'est bien passé
    Valid Message Should Be  La synchronisation des utilisateurs est terminée.

    # On vérifie que les 3 utilisateurs sont bien présents avec l'information LDAP
    Depuis le contexte de l'utilisateur  einstein
    Depuis le contexte de l'utilisateur  newton
    Depuis le contexte de l'utilisateur  galieleo
    Depuis le contexte de l'utilisateur  tesla

    # On supprime un des 3 utilisateurs
    Supprimer l'utilisateur  galieleo

    # On retourne au tableau de bord
    Go To Dashboard
    # On accède à l'écran de synchronisation via le menu
    Go To Submenu In Menu  administration  annuaire
    # On vérifie le titre de l'écran
    Page Title Should Be  Administration > Utilisateur > Synchronisation Annuaire
    # ATTENTION POSTULAT : Il n'y a aucun utilisateur LDAP dans la base
    # et le ldap auquel nous sommes connectés contient 3 utilisateurs
    Page Should Contain  Il y a 1 utilisateur(s) présent(s) dans l'annuaire et non présent(s) dans la base => 1 ajout(s)
    Page Should Contain  Il y a 0 utilisateur(s) présent(s) dans la base et non présent(s) dans l'annuaire => 0 suppression(s)
    Page Should Contain  Il y a 3 utilisateur(s) présent(s) à la fois dans la base et l'annuaire => 3 mise(s) à jour
    # On clique sur "Synchroniser"
    Click On Submit Button
    # On vérifie que tout s'est bien passé
    Valid Message Should Be  La synchronisation des utilisateurs est terminée.


Changement du mot de passe

    [Documentation]

    Comment    @todo Écrire le 'Test Case'


Suppression de son propre utilisateur interdite

    [Documentation]

    Comment    @todo Écrire le 'Test Case'


Réinitialisation du mot de passe

    [Documentation]  La fonctionnalité de réinitialisation du mot de passe
    ...  permet à un utilisateur de faire la demande d'un nouveau mot de passe.
    ...  Il accède à un formulaire lui permettant de saisir son login afin de
    ...  recevoir un mail de confirmation. Dans ce mail un lien lui permet
    ...  d'accéder à un formulaire de saisie d'un nouveau mot de passe.
    ...
    ...  Ce testcase vérifie :
    ...  - que l'option affiche ou non le lien vers le formulaire de
    ...    réinitialisation du mot de passe
    ...  - que le formulaire fonctionne correctement jusqu'à l'envoi du mail
    ...    de réinitialisation
    ...  Il ne vérifie pas :
    ...  - que le mail contient un lien fonctionnel
    ...  - que le formulaire de saisie d'un nouveau mot de passe est
    ...    fonctionnel

    #
    Depuis la page d'accueil  admin  admin
    Se déconnecter

    # On vérifie que le lien 'Mot de passe oublié' n'est pas présent
    Désactiver l'option de réinitialisation du mot de passe
    Depuis la page de login
    Element Should Not Be Visible  css=p.link-password-reset

    #
    Activer l'option de réinitialisation du mot de passe
    Depuis la page de login
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Be Visible  css=p.link-password-reset
    Click Element  css=p.link-password-reset a
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Page Title Should Be  Redéfinition Du Mot De Passe
    Input Text  css=#login  plop
    Click Element  css=input[type='submit']
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Error Message Should Be  Votre identifiant est incorrect, ou ne vous permet pas de redefinir votre mot de passe de cette manière. Contactez votre administrateur.
    Input Text  css=#login  admin
    Click Element  css=input[type='submit']
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Valid Message Should Be  Un message de demande de ré-initialisation de mot de passe vous a été envoyé sur votre messagerie.

    # On vérifie que le lien 'Mot de passe oublié' n'est pas présent
    Désactiver l'option de réinitialisation du mot de passe
    Depuis la page de login
    Element Should Not Be Visible  css=p.link-password-reset

