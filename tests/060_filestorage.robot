*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Filestorage"...


*** Test Cases ***
Vérification de l'ajout d'un fichier

    [Documentation]

    Depuis la page d'accueil  admin  admin
    Depuis le listing des logos
    # On clique sur le bouton ajouter
    Click On Add Button
    Input Text  id  TEST Ajouter
    Add File    fichier    testImportManuel.jpg
    # On valide le formulaire
    Click On Submit Button
    # On vérifie le message de validation
    Page Should Not Contain    L'état de l'enregistrement n'a pas pu être réinitialisé
    Page Should Contain    Le champ libellé est obligatoire
    Input Text  libelle  TEST Ajouter
    # On valide le formulaire
    Click On Submit Button


Vérification de la suppression d'un fichier

    [Documentation]

    Depuis la page d'accueil  admin  admin
    #
    Ajouter le logo  TEST  TEST  testImportManuel.jpg
    #
    Depuis le contexte du logo  TEST
    Click On Form Portlet Action  om_logo  modifier
    # Récupération de l'UID
    ${uid} =  Get Value  fichier
    ${path_1} =  Get Substring  ${uid}  0  2
    ${path_2} =  Get Substring  ${uid}  0  4
    # Vérification dans le filestorage
    File Should Exist  ..${/}var${/}filestorage${/}${path_1}${/}${path_2}${/}${uid}
    File Should Exist  ..${/}var${/}filestorage${/}${path_1}${/}${path_2}${/}${uid}.info
    #
    Supprimer le logo  TEST
    #
    # Vérification dans le filestorage
    File Should Not Exist  ..${/}var${/}filestorage${/}${path_1}${/}${path_2}${/}${uid}
    File Should Not Exist  ..${/}var${/}filestorage${/}${path_1}${/}${path_2}${/}${uid}.info


Vérification des contraintes sur l'upload d'un fichier - Activation du custom
    # Activation du custom
    Copy File  ${EXECDIR}${/}binary_files${/}custom${/}custom.inc.php  ${EXECDIR}${/}..${/}dyn${/}custom.inc.php
    Copy Directory  ${EXECDIR}${/}binary_files${/}custom  ${EXECDIR}${/}..${/}


Vérification des contraintes sur l'upload d'un fichier

    [Documentation]

    Depuis la page d'accueil  admin  admin

    # On accède au formulaire d'ajout de logo
    Depuis le listing des logos
    Click On Add Button
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Page Title Should Be  Paramétrage > Logo
    La page ne doit pas contenir d'erreur

    # Ouverture de l'overlay d'upload
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Element  css=#fichier_upload + a.upload > span.ui-icon
    Wait Until Element Is Visible    css=#upload-container #title h2
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Text Should Be  css=#upload-container #title h2  Envoyer

    # Validation sans téléchargement de fichier
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Button  css=form#upload-form input.ui-button
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Error Message Should Be  Vous devez sélectionner un fichier.

    # Validation avec téléchargement d'un fichier non conforme
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Choose File  css=#upload-form > input.champFormulaire  ${PATH_BIN_FILES}testImportManuel.jpg
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Button  css=form#upload-form input.ui-button
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Error Message Should Be  Le fichier n'est pas conforme à la liste des extension(s) autorisée(s) (.png). [testImportManuel.jpg]

    # Validation avec téléchargement d'un fichier conforme
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Choose File  css=#upload-form > input.champFormulaire  ${PATH_BIN_FILES}test-logo-extension-png.png
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Button  css=form#upload-form input.ui-button
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Textfield Value Should Be  css=#fichier_upload  test-logo-extension-png.png


    # On accède au formulaire d'ajout de logo dans le contexte de la collectivité
    Depuis le contexte de la collectivité  LIBREVILLE
    On clique sur l'onglet  om_logo  logo
    Click On Add Button JS
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Page SubTitle Should Be  paramétrage > logo
    La page ne doit pas contenir d'erreur

    # Ouverture de l'overlay d'upload
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Element  css=#fichier_upload + a.upload > span.ui-icon
    Wait Until Element Is Visible    css=#upload-container #title h2
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Text Should Be  css=#upload-container #title h2  Envoyer

    # Validation sans téléchargement de fichier
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Button  css=form#upload-form input.ui-button
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Error Message Should Be  Vous devez sélectionner un fichier.

    # Validation avec téléchargement d'un fichier non conforme
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Choose File  css=#upload-form > input.champFormulaire  ${PATH_BIN_FILES}testImportManuel.jpg
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Button  css=form#upload-form input.ui-button
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Error Message Should Be  Le fichier n'est pas conforme à la liste des extension(s) autorisée(s) (.png). [testImportManuel.jpg]

    # Validation avec téléchargement d'un fichier conforme
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Choose File  css=#upload-form > input.champFormulaire  ${PATH_BIN_FILES}test-logo-extension-png.png
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Click Button  css=form#upload-form input.ui-button
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Textfield Value Should Be  css=#fichier_upload  test-logo-extension-png.png


Vérification des contraintes sur l'upload d'un fichier - Désactivation du custom
    # Désactivation du custom
    Remove File  ${EXECDIR}${/}..${/}dyn${/}custom.inc.php
    Remove Directory  ${EXECDIR}${/}..${/}custom  True


