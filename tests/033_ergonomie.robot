*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Ergonomie"...


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


SousFormulaire : action-self (ajouter) > action-direct (copier)
    [Documentation]  ...
    #
    Depuis la page d'accueil  admin  admin
    #
    ${lettretype1_id}  Set Variable  test_ergonomie_lettretype1
    #
    Ajouter la lettre-type dans le contexte de la collectivité  ${lettretype1_id}  ${lettretype1_id}  <p>${lettretype1_id}</p>  <p><span style="font-weight: bold;">${lettretype1_id}</span></p>  ${noquery}  true  LIBREVILLE
    #
    Click On SubForm Portlet Action  om_lettretype  copier
    Click Element  css=div.ui-dialog-buttonset button
    Sleep  3
    #
    Valid Message Should Contain    L'element a ete correctement duplique.
    Portlet Action Should Be In SubForm  om_lettretype  modifier


SousFormulaire : action-self (modifier) > action-direct (copier)
    [Documentation]  ...
    #
    Depuis la page d'accueil  admin  admin
    #
    ${lettretype2_id}  Set Variable  test_ergonomie_lettretype2
    #
    Ajouter la lettre-type dans le contexte de la collectivité  ${lettretype2_id}  ${lettretype2_id}  <p>${lettretype2_id}</p>  <p><span style="font-weight: bold;">${lettretype2_id}</span></p>  ${noquery}  true  LIBREVILLE
    #
    Click On SubForm Portlet Action  om_lettretype  modifier
    Click On Submit Button In SubForm
    Valid Message Should Contain  Vos modifications ont bien été enregistrées.
    #
    Click On SubForm Portlet Action  om_lettretype  copier
    Click Element  css=div.ui-dialog-buttonset button
    Sleep  3
    #
    Valid Message Should Contain    L'element a ete correctement duplique.
    Portlet Action Should Be In SubForm  om_lettretype  modifier

