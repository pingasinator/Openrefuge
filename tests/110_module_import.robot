*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Module 'Import'" : ...


*** Test Cases ***
Fonctionnement basique

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    Depuis l'écran principal du module 'Import'
    Page Title Should Be  Administration > Module Import
    Page should Contain  choix de l'import

    #
    Click Element  css=#action-import-om_utilisateur-importer
    Page Title Should Be  Administration > Module Import
    Page Subtitle Should Be  > utilisateur
    Click On Submit Button In Import CSV
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Error Message Should Be  Vous n'avez pas selectionné de fichier à importer.
    Page Title Should Be  Administration > Module Import
    Page Subtitle Should Be  > utilisateur
    Click On Back Button
    Page Title Should Be  Administration > Module Import
    Page should Contain  choix de l'import

    #
    Depuis l'import  om_utilisateur
    Add File  fic1  import-om_utilisateur-1.csv
    Select From List By Value  css=#separateur  ,
    Select From List By Value  css=#import_id  1
    Click On Submit Button In Import CSV
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Valid Message Should Contain  2 ligne(s) importée(s)

    #
    Depuis l'import  om_utilisateur
    Add File  fic1  import-om_utilisateur-2.csv
    Select From List By Value  css=#separateur  ;
    Select From List By Value  css=#import_id  0
    Click On Submit Button In Import CSV
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Valid Message Should Contain  2 ligne(s) importée(s)


