*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Génération" : ...


*** Test Cases ***
Génération complète

    [Documentation]  Le 'Framework' de l'application permet de générer
    ...  automatiquement certains scripts en fonction du modèle de données.
    ...  Lors du développement la règle est la suivante : toute modification du
    ...  modèle de données doit entrainer une regénération complète de tous les
    ...  scripts. Pour vérifier à chaque modification du code que la règle a
    ...  bien été respectée, ce TestCase permet de lancer une génération
    ...  complète. Si un fichier est généré alors le test doit échouer.

    #
    Depuis la page d'accueil  admin  admin
    #
    Générer tout


Assistant "Création d'état"

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    #
    Depuis l'assistant "Création d'état"
    # Le sous titre de la page doit être
    Page SubTitle Should Be  > génération d'édition - etat
    # On sélectionne la table om_collectivité
    Select From List By Label  choice-import  om_collectivite
    # Après le rechargement de la page le select le fieldset de sélection de champ doit être visible
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Be Visible  css=#form-csv-import
    # On valide le formulaire sans sélectionner de champ
    Click Button  submit-csv-import
    # XXX On devrait avoir un message indiquant que la sélection est obligatoire et que la page ne contient pas d'erreur
    # XXX En attendant on met un Sleep 1
    Sleep  1
    La page ne doit pas contenir d'erreur
    # On sélectionne tous les champs
    @{fields} =  Create List  om_collectivite.om_collectivite  om_collectivite.libelle  om_collectivite.niveau
    Select Multiple By Label  choice-field[]  ${fields}
    # On valide le formulaire
    Click Button  submit-csv-import
    # On vérifie qu'un message indiquant que la création s'est bien passée et que la page ne contient pas d'erreur
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Valid Message Should Be  om_collectivite enregistré
    La page ne doit pas contenir d'erreur


Assistant "Création de lettre-type"

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    #
    Depuis l'assistant "Création de lettre type"
    # Le sous titre de la page doit être
    Page SubTitle Should Be  > génération d'édition - lettre type
    # On sélectionne la table om_collectivité
    Select From List By Label  choice-import  om_collectivite
    # Après le rechargement de la page le select le fieldset de sélection de champ doit être visible
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Be Visible  css=#form-csv-import
    # On valide le formulaire sans sélectionner de champ
    Click Button  submit-csv-import
    # XXX On devrait avoir un message indiquant que la sélection est obligatoire et que la page ne contient pas d'erreur
    # XXX En attendant on met un Sleep 1
    Sleep  1
    La page ne doit pas contenir d'erreur
    # On sélectionne tous les champs
    @{fields} =  Create List  om_collectivite.om_collectivite  om_collectivite.libelle  om_collectivite.niveau
    Select Multiple By Label  choice-field[]  ${fields}
    # On valide le formulaire
    Click Button  submit-csv-import
    # On vérifie qu'un message indiquant que la création s'est bien passée et que la page ne contient pas d'erreur
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Valid Message Should Be  om_collectivite enregistré
    La page ne doit pas contenir d'erreur


Assistant "Création de sous-état"

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    #
    Depuis l'assistant "Création de sous-état"
    # Le sous titre de la page doit être
    Page SubTitle Should Be  > génération d'édition - sous etat


Assistant "Migration état, sous-état, lettre type"

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    #
    Depuis l'assistant "Migration état, sous-état, lettre type"
    # Le sous titre de la page doit être
    Page SubTitle Should Be  > import des anciennes éditions


