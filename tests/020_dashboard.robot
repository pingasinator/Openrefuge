*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Dashboard"...


*** Test Cases ***
Créer un nouveau widget

    Depuis la page d'accueil  admin  admin

    Go To Submenu In Menu    administration    om_widget
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget

    # Ajout d'un nouveau widget
    Click Element    css=#action-tab-om_widget-corner-ajouter
    La page ne doit pas contenir d'erreur
    # Vérifie que l'on se trouve au bon endroit
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget

    # Vérifie que l'on ne peut pas faire un enregistrement vide
    Click On Submit Button
    La page ne doit pas contenir d'erreur
    Error Message Should Contain    Le champ libellé est obligatoire
    Error Message Should Contain    SAISIE NON ENREGISTRÉE
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget

    # Format par défaut en web avec les champ lien et texte
    Element Text Should Be    css=#lib-lien    lien
    Element Text Should Be    css=#lib-texte    texte

    # Sélection de file et vérification
    Select From List By Value  css=#type  file
    Element Text Should Be    css=#lib-script    script
    Element Text Should Be    css=#lib-arguments    arguments
    Click On Submit Button
    La page ne doit pas contenir d'erreur
    Error Message Should Contain    Le champ libellé est obligatoire
    Error Message Should Contain    Le script n'existe pas.
    Error Message Should Contain    SAISIE NON ENREGISTRÉE

    # Création d'un widget de type web
    Select From List By Value  css=#type  web
    Element Text Should Be    css=#lib-lien    lien
    Element Text Should Be    css=#lib-texte    texte

    Input Text    css=#libelle    widget lien
    Input Text    css=#lien    http://www.atreal.fr/
    Input Text    css=#texte    Donec sed tristique lectus. Nullam blandit leo vitae lectus suscipit dignissim. Vestibulum adipiscing nisi vel tortor tempus dignissim ac a magna. Mauris vestibulum in orci in volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam malesuada purus aliquet iaculis hendrerit. Phasellus sagittis sed diam ac blandit. Proin molestie justo vel velit imperdiet, a congue sem egestas. Integer id nibh volutpat felis interdum pretium.
    Click On Submit Button
    La page ne doit pas contenir d'erreur
    Valid Message Should Be    Vos modifications ont bien été enregistrées.

    Click On Back Button
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget
    Table Should Contain    css=table.tab-tab    widget lien

    # Création d'un widget de type file
    Click Element    css=#action-tab-om_widget-corner-ajouter
    La page ne doit pas contenir d'erreur
    Select From List By Value  css=#type  file
    Input Text    css=#libelle    widget file
    Select From List By Value  css=#script  test_robotframework
    Input Text    css=#arguments    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer facilisis risus id turpis eleifend, sed facilisis lectus congue. Nulla mattis ultricies euismod. Praesent faucibus in ipsum at sodales. Maecenas lectus massa, dapibus ut tortor ac, viverra egestas mauris. Morbi mi elit, ullamcorper sed tincidunt nec, fermentum sed nisi. Mauris a feugiat nisl. Maecenas nunc lorem, vehicula eu fermentum non, ullamcorper sed eros. Phasellus porttitor massa nec nisi facilisis, non pulvinar enim ullamcorper. Cras ac ante luctus, fringilla enim sed, malesuada elit. Nunc ultricies, dui non sollicitudin accumsan, diam purus porttitor sem, rhoncus placerat ante quam vel nisl. Nam adipiscing mauris risus, id iaculis est volutpat eget. Curabitur tortor lacus, pharetra ultricies tristique eu, consequat et odio. Morbi vestibulum nec lorem quis luctus. Etiam non varius quam. Ut vehicula, neque vel blandit malesuada, nisi nunc dignissim odio, et pellentesque dolor augue ac ipsum.
    Click On Submit Button
    La page ne doit pas contenir d'erreur
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    Click On Back Button
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget
    Table Should Contain    css=table.tab-tab    widget file


Composer un tableau de bord

    Depuis la page d'accueil  admin  admin

    Depuis le contexte du profil  libelle=ADMINISTRATEUR
    ${hierarchie} =  Get Text  css=div.form-content span#hierarchie


    Go To Submenu In Menu    administration    om_dashboard
    Page Title Should Be    Administration > Tableaux De Bord > Composition
    First Tab Title Should Be    tableau de bord
    Submenu In Menu Should Be Selected    administration    om_dashboard

    Ajouter le widget au tableau de bord  ${hierarchie} - ADMINISTRATEUR  widget file

    # Déplace le widget
    Wait Until Element Is Visible    css=div.widget_test_robotframework
    Drag And Drop    css=div.widget_test_robotframework div.widget-header-move    css=#column_3

    Go To DashBoard
    Element Text Should Be
    ...    css=#dashboard div.col3 #column_3 div.widget_test_robotframework div.widget-content-wrapper div.widget-content
    ...    Test RF

