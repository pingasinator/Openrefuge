*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Tableaux"...


*** Test Cases ***
Constitution du jeu de données

    [Documentation]  L'objet de ce TestCase est de constituer un jeu de
    ...  données cohérent pour les scénarios fonctionnels qui suivent.

    #
    Depuis la page d'accueil  admin  admin
    # Il faut que le listing affiche 15 résultats par page.
    ${profil}    Set Variable    PUBLIC
    Set Suite Variable    ${profil}
    Ajouter le profil depuis le menu    ${profil}    6
    # On ajoute 20 droits au profil
    :FOR    ${INDEX}    IN RANGE    0    20
    \    Ajouter le droit depuis le menu    public_${INDEX}    ${profil}
    # Puis 2 specifiques pour la recherche
    Ajouter le droit depuis le menu    recherche_d'apostrophe    ${profil}
    Ajouter le droit depuis le menu    recherche_accents_éèàç    ${profil}

    # Création d'une requête destinée à la lettre-type à copier en tableau
    ${query_tab}  Set Variable  Nonetab
    Ajouter la requête  ${query_tab}  ${query_tab}  -  sql  SELECT 1;  -
    # Création de la lettre-type à copier en tableau
    ${id_tab}  Set Variable  test_action_direct_tab
    Set Suite Variable    ${id_tab}
    Ajouter la lettre-type depuis le menu  ${id_tab}  ${id_tab}  <p>${id_tab}</p>  <p><span style="font-weight: bold;">${id_tab}</span></p>  ${query_tab}  true
    # Création d'une requête destinée à la lettre-type à copier en tableau
    ${query_soustab}  Set Variable  Nonesoustab
    Set Suite Variable    ${query_soustab}
    Ajouter la requête  ${query_soustab}  ${query_soustab}  -  sql  SELECT 1;  -
    # Création de la lettre-type à copier en tableau
    ${id_soustab}  Set Variable  test_action_direct_soustab
    Set Suite Variable    ${id_soustab}
    Ajouter la lettre-type depuis le menu  ${id_soustab}  ${id_soustab}  <p>${id_soustab}</p>  <p><span style="font-weight: bold;">${id_soustab}</span></p>  ${query_soustab}  true

Pagination en formulaire

    [Documentation]    Vérifie la pagination sur un formulaire.

    #
    Depuis la page d'accueil  admin  admin
    #
    Depuis le listing des droits
    # On récupère la plage d'enregistrement de la première page
    ${pagination_premiere_page} =    Get Pagination Text
    # On sélectionne la deuxième page
    Select Pagination    15
    # On vérifie que la page à changé avec la plage d'enregistrement
    Pagination Text Not Should Be    ${pagination_premiere_page}


Pagination en sous-formulaire

    [Documentation]    Vérifie la pagination sur un sous-formulaire.

    #
    Depuis la page d'accueil  admin  admin
    #
    Depuis le listing des droit du profil    null    ${profil}
    # On récupère la plage d'enregistrement de la première page
    ${pagination_premiere_page} =    Get Pagination Text
    # On sélectionne la deuxième page
    Select Pagination    15
    # On vérifie que la page à changé avec la plage d'enregistrement
    Pagination Text Not Should Be    ${pagination_premiere_page}


Recherche simple dans un TAB
    [Documentation]  Vérifie le bon fonctionnement de la recherche dans les TAB.
    Depuis la page d'accueil  admin  admin
    #
    Depuis le listing des droits
    # On recherche un terme avec apostrophe
    Use Simple Search    Tous    recherche_d'apostrophe
    # Vérification du résultat
    Elements From Column Should Be    1    recherche_d'apostrophe
    # Idem avec accents
    Use Simple Search    Tous    recherche_accents_éèàç
    # Vérification du résultat
    Elements From Column Should Be    1    recherche_accents_éèàç


Recherche simple dans un SOUSTAB
    [Documentation]  Vérifie le bon fonctionnement de la recherche dans les SOUSTAB.
    Depuis la page d'accueil  admin  admin
    Ajouter le profil depuis le menu  UTILISATEUR  3
    # Constitution du jeu de données spécifique
    ${valeur_recherchee_speciale}  Set Variable  test_032_éa''ç
    ${valeur_recherchee_speciale_avec_espace}  Set Variable  ${valeur_recherchee_speciale}${SPACE}
    Ajouter l'utilisateur  ${valeur_recherchee_speciale}  a@a.fr  login_test032  login_test032  UTILISATEUR  null
    Ajouter le paramètre depuis le menu  ${valeur_recherchee_speciale}  supervaleur  null
    #
    Depuis le contexte de la collectivité  LIBREVILLE
    # Par défaut le bloc de recherche est caché, il doit devenir
    # visible lorsque l'on clique sur un onglet qui contient un
    # soustab et de nouveau être caché lorsque l'on clique sur l'onglet
    # principal
    Element Should Not Be Visible  css=#recherche_onglet
    On clique sur l'onglet  om_utilisateur  utilisateur
    Element Should Be Visible  css=#recherche_onglet
    On clique sur l'onglet  main  collectivité
    #
    On clique sur l'onglet  om_utilisateur  utilisateur
    ${valeur_recherchee_sans_aucun_resultat}  Set Variable  z1z2z3z4z5z6
    ${valeur_recherchee_sans_aucun_resultat_avec_espace}  Set Variable  ${valeur_recherchee_sans_aucun_resultat}${SPACE}
    Input Text  css=#recherchedyn  ${valeur_recherchee_sans_aucun_resultat_avec_espace}
    Execute Javascript  $('#recherchedyn').keyup();
    Execute Javascript  $('#recherchedyn').keyup();
    Execute Javascript  $('#recherchedyn').keyup();
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur .pagination-text  1 - 0 enregistrement(s) sur 0 = [${valeur_recherchee_sans_aucun_resultat}]
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur table .empty  Aucun enregistrement
    On clique sur l'onglet  om_parametre  paramètre
    Form Value Should Be  css=#recherchedyn  ${valeur_recherchee_sans_aucun_resultat_avec_espace}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_parametre .pagination-text  1 - 0 enregistrement(s) sur 0 = [${valeur_recherchee_sans_aucun_resultat}]
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_parametre table .empty  Aucun enregistrement
    #
    Input Text  css=#recherchedyn  ${valeur_recherchee_speciale_avec_espace}
    Execute Javascript  $('#recherchedyn').keyup();
    Execute Javascript  $('#recherchedyn').keyup();
    Execute Javascript  $('#recherchedyn').keyup();
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_parametre .pagination-text  1 - 1 enregistrement(s) sur 1 = [${valeur_recherchee_speciale}]
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_parametre table  ${valeur_recherchee_speciale}
    On clique sur l'onglet  om_utilisateur  utilisateur
    Form Value Should Be  css=#recherchedyn  ${valeur_recherchee_speciale_avec_espace}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur .pagination-text  1 - 1 enregistrement(s) sur 1 = [${valeur_recherchee_speciale}]
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur table  ${valeur_recherchee_speciale}
    #
    Click Link  ${valeur_recherchee_speciale}
    Click On Back Button In Subform
    Form Value Should Be  css=#recherchedyn  ${valeur_recherchee_speciale_avec_espace}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur .pagination-text  1 - 1 enregistrement(s) sur 1 = [${valeur_recherchee_speciale}]
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur table  ${valeur_recherchee_speciale}
    #
    Click Link  ${valeur_recherchee_speciale}
    Click On SubForm Portlet Action  om_utilisateur  modifier
    Click On Submit Button In Subform
    Click On Back Button In Subform
    Form Value Should Be  css=#recherchedyn  ${valeur_recherchee_speciale_avec_espace}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur .pagination-text  1 - 1 enregistrement(s) sur 1 = [${valeur_recherchee_speciale}]
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#sousform-om_utilisateur table  ${valeur_recherchee_speciale}
    #
    Input Text  css=#recherchedyn  ${SPACE}
    Execute Javascript  $('#recherchedyn').keyup();
    Execute Javascript  $('#recherchedyn').keyup();
    Execute Javascript  $('#recherchedyn').keyup();
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Not Contain  css=#sousform-om_utilisateur .pagination-text  [${valeur_recherchee_sans_aucun_resultat}]
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Not Contain  css=#sousform-om_utilisateur table  Aucun enregistrement


Recherche avancée dans un TAB - Activation du custom
    # Activation du custom
    Copy File  ${EXECDIR}${/}binary_files${/}custom${/}custom.inc.php  ${EXECDIR}${/}..${/}dyn${/}custom.inc.php
    Copy Directory  ${EXECDIR}${/}binary_files${/}custom  ${EXECDIR}${/}..${/}


Recherche avancée dans un TAB
    Depuis la page d'accueil  admin  admin
    Depuis le listing des utilisateurs
    Element Should Be Visible  css=#advanced-form


Recherche avancée dans un TAB - Désactivation du custom
    # Désactivation du custom
    Remove File  ${EXECDIR}${/}..${/}dyn${/}custom.inc.php
    Remove Directory  ${EXECDIR}${/}..${/}custom  True


Actions direct

    [Documentation]    Copie des lettres-types en tableau et sous-tableau
    ...    Une action-direct exécute le traitement et affiche son message
    ...    en rechargeant en AJAX le listing.

    # Ajout de l'action-direct "copier" dans le listing des lettres-types
    Copy File  ${EXECDIR}${/}binary_files${/}om_lettretype.inc.php  ${EXECDIR}${/}..${/}sql${/}pgsql${/}om_lettretype.inc.php
    Sleep  3

    # Copie en tableau
    Depuis le listing des lettres-types
    Total Results In Tab Should Be Equal  4  om_lettretype
    Page Should Contain  ${id_tab}
    Page Should Contain  ${id_soustab}
    ${date_fr} =  Date du jour au format dd/mm/yyyy
    Page Should Not Contain  copie du ${date_fr}
    Click Element  action-tab-om_lettretype-left-copier-3
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Total Results In Tab Should Be Equal  5  om_lettretype
    Valid Message Should Contain In Tab  L'element a ete correctement duplique.
    Click On Link  copie du ${date_fr}
    Element Text Should Be  id  ${id_tab}

    # Copie en sous-tableau
    Depuis le listing des requêtes
    Click On Link  ${query_soustab}
    On clique sur l'onglet  om_lettretype  lettre type
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Total Results In Subform Should Be Equal  1  om_lettretype
    Page Should Not Contain  copie du ${date_fr}
    Click Element  action-soustab-om_lettretype-left-copier-4
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Total Results In Subform Should Be Equal  2  om_lettretype
    Valid Message Should Contain In Subtab  L'element a ete correctement duplique.
    Click On Link  copie du ${date_fr}
    Element Text Should Be  id  ${id_soustab}

    # Restauration de la configuration du listing des lettres-types
    Remove File  ${EXECDIR}${/}..${/}sql${/}pgsql${/}om_lettretype.inc.php

