*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Formulaires"...


*** Test Cases ***
Constitution du jeu de données

    [Documentation]  L'objet de ce TestCase est de constituer un jeu de
    ...  données cohérent pour les scénarios fonctionnels qui suivent.

    #
    Depuis la page d'accueil  admin  admin

    #
    ${noquery}  Set Variable  Aucune
    Set Suite Variable    ${noquery}
    Ajouter la requête  none  ${noquery}  Ne rend disponible aucun champ de fusion.  sql  SELECT 1;  Aucun

    #
    ${lettretype1_id}  Set Variable  test_actions_lettretype1
    Ajouter la lettre-type depuis le menu  ${lettretype1_id}  ${lettretype1_id}  <p>${lettretype1_id}</p>  <p><span style="font-weight: bold;">${lettretype1_id}</span></p>  ${noquery}  true
    Depuis le contexte de la lettre-type  ${lettretype1_id}
    ${lettretype1_key} =    Get Text    css=div.form-content span#om_lettretype
    Set Suite Variable    ${lettretype1_id}
    Set Suite Variable    ${lettretype1_key}

    #
    ${etat1_id}  Set Variable  test_actions_etat1
    Ajouter l'état depuis le menu  ${etat1_id}  ${etat1_id}  <p>${etat1_id}</p>  <p><span style="font-weight: bold;">${etat1_id}</span></p>  ${noquery}  true
    Depuis le contexte de l'état  ${etat1_id}
    ${etat1_key} =    Get Text    css=div.form-content span#om_etat
    Set Suite Variable    ${etat1_id}
    Set Suite Variable    ${etat1_key}

    #
    ${sousetat1_id}  Set Variable  test_actions_sousetat1
    Ajouter le sous état    null    ${sousetat1_id}    ${sousetat1_id}    true    ${sousetat1_id}    SELECT 1;
    Depuis le contexte du sous état  ${sousetat1_id}
    ${sousetat1_key} =    Get Text    css=div.form-content span#om_sousetat
    Set Suite Variable    ${sousetat1_id}
    Set Suite Variable    ${sousetat1_key}

    #
    ${sousetat2_id}  Set Variable  test_actions_sousetat2
    Ajouter le sous état    null    ${sousetat2_id}    ${sousetat2_id}    true    ${sousetat2_id}    SELECT 1;
    Depuis le contexte du sous état  ${sousetat2_id}
    ${sousetat2_key} =    Get Text    css=div.form-content span#om_sousetat
    Set Suite Variable    ${sousetat2_id}
    Set Suite Variable    ${sousetat2_key}


La fonction 'directlink'

    [Documentation]  Test de la fonction directlink qui permet d’accéder via
    ...  une URL à une vue spécifique d’un objet dans un onglet dans le
    ...  contexte d’un formulaire.

    #
    Depuis la page d'accueil  admin  admin
    #
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&action=3&direct_field=om_collectivite&direct_form=om_utilisateur&direct_action=1&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&action=3&idx=1&direct_form=om_utilisateur&direct_action=1&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&direct_field=om_collectivite&direct_form=om_utilisateur&direct_action=1&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&idx=1&direct_form=om_utilisateur&direct_action=1&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&direct_field=om_collectivite&direct_action=1&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&idx=1&direct_action=1&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&direct_field=om_collectivite&direct_form=om_utilisateur&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&idx=1&direct_form=om_utilisateur&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&direct_field=om_collectivite&direct_form=om_utilisateur&direct_action=1
    Error Message Should Be  L'élément n'est pas accessible.
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&direct_form=om_utilisateur&direct_action=1&direct_idx=1
    Error Message Should Be  L'élément n'est pas accessible.
    #
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&direct_field=om_collectivite&direct_form=plop&direct_action=1&direct_idx=1
    Error Message Should Be  L'objet est invalide.
    #
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&direct_field=om_collectivite&direct_form=om_utilisateur&direct_action=1&direct_idx=1
    Page Title Should Be  Administration > Collectivité > 1 LIBREVILLE
    L'onglet doit être sélectionné  om_utilisateur  utilisateur
    Element Should Contain  css=#sousform-om_utilisateur  administration > utilisateur > 1
    ${button_value} =  Get Element Attribute  css=#sousform-om_utilisateur input[name='submit']  value
    Should Contain  ${button_value}  Modifier
    #
    Go To DashBoard
    Go To  ${PROJECT_URL}${OM_ROUTE_FORM}&direct_link=true&obj=om_collectivite&action=3&idx=1&direct_form=om_utilisateur&direct_action=1&direct_idx=1
    Page Title Should Be  Administration > Collectivité > 1 LIBREVILLE
    L'onglet doit être sélectionné  om_utilisateur  utilisateur
    Element Should Contain  css=#sousform-om_utilisateur  administration > utilisateur > 1
    ${button_value} =  Get Element Attribute  css=#sousform-om_utilisateur input[name='submit']  value
    Should Contain  ${button_value}  Modifier


Soumission multiple impossible par le rafraichissement de la page

    [Documentation]  Test de la fonction qui empêche la double soumission d'un
    ...  formulaire.

    Log  Inutile depuis la version 4.7.0

    # #
    # Depuis la page d'accueil  admin  admin

    # # On ajoute un élément
    # Ajouter le droit depuis le menu    test_form_resubmit    ADMINISTRATEUR
    # # On actualise la page
    # Reload Page
    # # On valide l'alerte qui nous explique qu'on renvoi un POST
    # Dismiss Alert
    # # On vérifie que le message d'erreur nous indique qu'il est impossible de
    # # le faire
    # Error Message Should Contain    Opération illégale.


Modifier (action-self) LettreType Dans Un Formulaire

    [Documentation]

    #
    Depuis la page d'accueil  admin  admin

    # Depuis le listing des lettres-types
    Go To Submenu In Menu    parametrage    om_lettretype
    La page ne doit pas contenir d'erreur
    Page Title Should Be    Paramétrage > Lettre Type
    First Tab Title Should Be    lettre type
    Submenu In Menu Should Be Selected    parametrage    om_lettretype
    # La lettre type
    Click Element    css=#action-tab-om_lettretype-left-consulter-${lettretype1_key}
    La page ne doit pas contenir d'erreur
    Page Title Should Contain  Paramétrage > Lettre Type > ${lettretype1_key}
    #
    Portlet Action Should Be In Form    om_lettretype    modifier
    Click On Form Portlet Action    om_lettretype    modifier
    #
    Click On Submit Button
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    Page Title Should Contain    Paramétrage > Lettre Type > ${lettretype1_key}


Modifier (action-self) LettreType Dans Un Sous Formulaire

    [Documentation]

    #
    Depuis la page d'accueil  admin  admin

    #
    Depuis le listing des lettres-types de la collectivité  LIBREVILLE
    #
    Click Element    css=#action-soustab-om_lettretype-left-consulter-${lettretype1_key}
    La page ne doit pas contenir d'erreur
    #
    Portlet Action Should Be In SubForm    om_lettretype    modifier
    Click On SubForm Portlet Action    om_lettretype    modifier
    #
    Click On Submit Button In SubForm
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    #
    Go To DashBoard


Supprimer (action-self) SousÉtat Dans Un Formulaire

    [Documentation]

    #
    Depuis la page d'accueil  admin  admin

    #
    Go To Submenu In Menu    parametrage    om_sousetat
    La page ne doit pas contenir d'erreur
    Page Title Should Be    Paramétrage > Sous État
    First Tab Title Should Be    sous état
    Submenu In Menu Should Be Selected    parametrage    om_sousetat
    #
    Click Element    css=#action-tab-om_sousetat-left-consulter-${sousetat1_key}
    La page ne doit pas contenir d'erreur
    Page Title Should Contain    Paramétrage > Sous État > ${sousetat1_key}
    #
    Portlet Action Should Be In Form    om_sousetat    supprimer
    Click On Form Portlet Action    om_sousetat    supprimer
    #
    Click On Submit Button
    Valid Message Should Be    La suppression a été correctement effectuée.
    Page Title Should Be    Paramétrage > Sous État
    #
    Go To DashBoard


Supprimer (action-self) SousÉtat Dans Un Sous Formulaire

    [Documentation]

    #
    Depuis la page d'accueil  admin  admin

    #
    Depuis le listing des sous-états de la collectivité  LIBREVILLE
    #
    Click Element    css=#action-soustab-om_sousetat-left-consulter-${sousetat2_key}
    La page ne doit pas contenir d'erreur
    #
    Portlet Action Should Be In SubForm    om_sousetat    supprimer
    Click On SubForm Portlet Action    om_sousetat    supprimer
    #
    Click On Submit Button In SubForm
    Valid Message Should Be    La suppression a été correctement effectuée.


Copier (action-direct-with-confirmation) État Dans Un Formulaire

    [Documentation]

    #
    Depuis la page d'accueil  admin  admin

    #
    ${day} =    Get Time    day    NOW
    ${month} =    Get Time    month    NOW
    ${year} =    Get Time    year    NOW
    #
    Go To Submenu In Menu    parametrage    om_etat
    La page ne doit pas contenir d'erreur
    Page Title Should Be    Paramétrage > État
    First Tab Title Should Be    état
    Submenu In Menu Should Be Selected    parametrage    om_etat
    #
    Click Element    css=#action-tab-om_etat-left-consulter-${etat1_key}
    La page ne doit pas contenir d'erreur
    Page Title Should Contain    Paramétrage > État > ${etat1_key}
    #
    Portlet Action Should Be In Form    om_etat    copier
    Click On Form Portlet Action    om_etat    copier
    #XXX
    Click Element    css=div.ui-dialog-buttonset button
    Sleep    3
    #
    Page Title Should Contain    Paramétrage > État > ${etat1_key}
    Valid Message Should Contain    L'element a ete correctement duplique.
    Click On Back Button
    Element Should Contain    css=table.tab-tab    copie du ${day}/${month}/${year}
    #
    Go To DashBoard


Copier (action-direct-with-confirmation) État Dans Un Sous Formulaire

    [Documentation]

    #
    Depuis la page d'accueil  admin  admin

    #
    ${day} =    Get Time    day    NOW
    ${month} =    Get Time    month    NOW
    ${year} =    Get Time    year    NOW
    #
    Depuis le listing des états de la collectivité  LIBREVILLE
    #
    Click Element    css=#action-soustab-om_etat-left-consulter-${etat1_key}
    La page ne doit pas contenir d'erreur
    #
    Portlet Action Should Be In SubForm    om_etat    copier
    Click On SubForm Portlet Action    om_etat    copier
    #XXX
    Click Element    css=div.ui-dialog-buttonset button
    Sleep    3
    #
    Valid Message Should Contain    L'element a ete correctement duplique.
    Click On Back Button In Subform
    Element Should Contain    css=table.tab-tab    copie du ${day}/${month}/${year}

