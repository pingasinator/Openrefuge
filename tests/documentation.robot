*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Documentation"...


*** Keywords ***
Highlight heading
    [Arguments]  ${locator}
    Highlight  ${locator}


*** Test Cases ***
Constitution du jeu de données
    [Tags]  doc
    Depuis la page d'accueil  admin  admin
    #
    Create Directory    results/screenshots
    Create Directory    results/screenshots/reference
    Create Directory    results/screenshots/usage
    Create Directory    results/screenshots/usage/administration
    Create Directory    results/screenshots/usage/ergonomie
    Create Directory    results/screenshots/usage/generation

    #
    Ajouter la requête  none  Requête SQL  Ne rend disponible aucun champ de fusion.  sql  SELECT 1;
    Ajouter le logo  logopdf.png  logopdf.png  testImportManuel.jpg  null  null  true

    &{args_lettretype} =  Create Dictionary
    ...  id=om_utilisateur
    ...  libelle=lettre aux utilisateurs
    ...  sql=Requête SQL
    ...  titre=<p style="text-align: left;"><span style="font-size: 14px;"><span style="font-family: arial;">le&nbsp;&datecourrier</span></span></p>
    ...  corps=<p style="text-align: justify;"><span style="font-size: 10px;"><span style="font-family: times;">Nous&nbsp;avons&nbsp;le&nbsp;plaisir&nbsp;de&nbsp;vous&nbsp;envoyer&nbsp;votre&nbsp;login&nbsp;et&nbsp;votre&nbsp;mot&nbsp;de&nbsp;passevotre&nbsp;login&nbsp;[login]&nbsp;Vous&nbsp;souhaitant&nbsp;bonne&nbsp;receptionVotre&nbsp;administrateur</span></span></p>
    ...  actif=true
    ...  logo=logopdf.png (logopdf.png)

    Ajouter la lettre-type depuis le menu  &{args_lettretype}




Manuel d'usage - Ergonomie
    [Documentation]  Section 'Ergonomie'.
    [Tags]  doc

    # Les méthodes Suite Setup et Suite Teardown gèrent l'ouverture et la
    # fermeture du navigateur. Dans le cas de ce TestSuite on a besoin de
    # travailler sur un navigateur fraichement ouvert pour être sûr que la
    # variable de session est neuve.
    Fermer le navigateur
    Ouvrir le navigateur
    Depuis la page de login
    Capture viewport screenshot  screenshots/usage/ergonomie/a_connexion_formulaire.png
    #
    Input Username    admin
    Input Password    plop
    Click Button    login.action.connect
    Wait Until Keyword Succeeds    ${TIMEOUT}    ${RETRY_INTERVAL}    Error Message Should Be    Votre identifiant ou votre mot de passe est incorrect.
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_connexion_message_erreur.png
    ...  css=div.message
    #
    Input Username    admin
    Input Password    admin
    Click Button    login.action.connect
    Wait Until Element Is Visible    css=#actions a.actions-logout
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_connexion_message_ok.png
    ...  css=div.message
    #
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie_actions_globales.png
    ...  footer
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie_actions_personnelles.png
    ...  actions
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie_logo.png
    ...  logo
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie_raccourcis.png
    ...  shortlinks
    #
    Highlight heading  css=li.actions-logout
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_deconnexion_action.png
    ...  header
    #
    Go To Dashboard
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie_menu.png
    ...  menu
    #
    Go To Dashboard
    Se déconnecter
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_deconnexion_message_ok.png
    ...  css=div.message
    #
    Depuis la page d'accueil  admin  admin
    Go To Dashboard
    Remove element  dashboard
    Update element style  css=#content  height  300px
    Add pointy note  css=#logo  Logo  position=right
    Highlight heading  css=#menu
    Add note  css=#menu  Menu  position=right
    Add pointy note  css=#shortlinks  Raccourcis  position=bottom
    Add pointy note  css=#actions  Actions personnelles  position=left
    Highlight heading  css=#footer
    Add note  css=#footer  Actions globales  position=top
    Capture viewport screenshot  screenshots/usage/ergonomie/a_ergonomie_generale_detail.png
    #
    Depuis le listing des profils
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie-icone-ajouter.png
    ...  css=span.add-16
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie-icone-visualiser.png
    ...  css=span.consult-16
    # Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie-icone-pdf-listing.png
    # ...  css=span.print-25
    #
    # Depuis le listing des profils
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie-exemple-listing.png
    ...  content
    Click Link  ADMINISTRATEUR
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie-exemple-fiche-visualisation.png
    ...  content
    Click On Form Portlet Action  om_profil  modifier
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie-exemple-formulaire-modification.png
    ...  content
    On clique sur l'onglet  om_utilisateur  utilisateur
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_ergonomie-exemple-onglet-exemple-listing.png
    ...  content
    #
    Set Window Size  ${1280}  ${1024}
    Go To Dashboard
    Capture and crop page screenshot  screenshots/usage/ergonomie/a_tableau-de-bord-exemple.png
    ...  content



Manuel d'usage - Administration
    [Tags]  doc
    #
    Depuis la page d'accueil  admin  admin

    # ADMINISTRATION > ÉDITIONS
    # Captures - Tous les fieldsets du formulaire de modification d'une lettre types
    Depuis le contexte de la lettre-type  om_utilisateur
    Click On Form Portlet Action  om_lettretype  modifier
    Click Element  css=#fieldset-form-om_lettretype-parametres-generaux-de-l_edition legend
    Sleep  1
    Click Element  css=#fieldset-form-om_lettretype-en-tete legend
    Sleep  1
    Click Element  css=#fieldset-form-om_lettretype-parametres-du-titre-de-l_edition legend
    Sleep  1
    Click Element  css=#fieldset-form-om_lettretype-parametres-des-sous-etats legend
    Sleep  1
    Click Element  css=#fieldset-form-om_lettretype-pied-de-page legend
    Sleep  1
    Capture and crop page screenshot  screenshots/usage/administration/a_editions_etat_lettretype_bloc_edition.png
    ...    fieldset-form-om_lettretype-edition
    Capture and crop page screenshot  screenshots/usage/administration/a_editions_etat_lettretype_bloc_en-tete.png
    ...    fieldset-form-om_lettretype-en-tete
    Capture and crop page screenshot  screenshots/usage/administration/a_editions_etat_lettretype_bloc_titre.png
    ...    fieldset-form-om_lettretype-titre
    Capture and crop page screenshot  screenshots/usage/administration/a_editions_etat_lettretype_bloc_corps.png
    ...    fieldset-form-om_lettretype-corps
    Capture and crop page screenshot  screenshots/usage/administration/a_editions_etat_lettretype_bloc_pied-de-page.png
    ...    fieldset-form-om_lettretype-pied-de-page
    Capture and crop page screenshot  screenshots/usage/administration/a_editions_etat_lettretype_bloc_champs-de-fusion.png
    ...    fieldset-form-om_lettretype-champs-de-fusion

    # ADMINISTRATION > TABLEAUX DE BORD
    # Capture - Exemple de tableau de bord
    Go To Dashboard
    Capture viewport screenshot  screenshots/usage/administration/a_tableau_de_bord_exemple.png
    # Capture - Formulaire d'ajout d'un widget type web
    Go To Submenu In Menu    administration    om_widget
    Click Element    css=#action-tab-om_widget-corner-ajouter
    Select From List By Value  css=#type  web
    Capture and crop page screenshot  screenshots/usage/administration/a_tableau_de_bord_widget_ajout_web.png
    ...    form-content
    # On ajoute un widget type web
    Input Text    css=#libelle    widget lien
    Input Text    css=#lien    http://www.openmairie.org/
    Input Text    css=#texte    Donec sed tristique lectus. Nullam blandit leo vitae lectus suscipit dignissim. Vestibulum adipiscing nisi vel tortor tempus dignissim ac a magna. Mauris vestibulum in orci in volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam malesuada purus aliquet iaculis hendrerit. Phasellus sagittis sed diam ac blandit. Proin molestie justo vel velit imperdiet, a congue sem egestas. Integer id nibh volutpat felis interdum pretium.
    Click On Submit Button
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    # Capture - Formulaire d'ajout d'un widget type file
    Click On Back Button
    Click Element    css=#action-tab-om_widget-corner-ajouter
    Select From List By Value  css=#type  file
    Capture and crop page screenshot  screenshots/usage/administration/a_tableau_de_bord_widget_ajout_file.png
    ...    form-content
    # On ajoute un widget type file
    Input Text    css=#libelle    widget file
    Select From List By Value  css=#script  test_robotframework
    Input Text    css=#arguments    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer facilisis risus id turpis eleifend, sed facilisis lectus congue. Nulla mattis ultricies euismod. Praesent faucibus in ipsum at sodales. Maecenas lectus massa, dapibus ut tortor ac, viverra egestas mauris. Morbi mi elit, ullamcorper sed tincidunt nec, fermentum sed nisi. Mauris a feugiat nisl. Maecenas nunc lorem, vehicula eu fermentum non, ullamcorper sed eros. Phasellus porttitor massa nec nisi facilisis, non pulvinar enim ullamcorper. Cras ac ante luctus, fringilla enim sed, malesuada elit. Nunc ultricies, dui non sollicitudin accumsan, diam purus porttitor sem, rhoncus placerat ante quam vel nisl. Nam adipiscing mauris risus, id iaculis est volutpat eget. Curabitur tortor lacus, pharetra ultricies tristique eu, consequat et odio. Morbi vestibulum nec lorem quis luctus. Etiam non varius quam. Ut vehicula, neque vel blandit malesuada, nisi nunc dignissim odio, et pellentesque dolor augue ac ipsum.
    Click On Submit Button
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    # Capture - Listing des widgets
    Click On Back Button
    Capture and crop page screenshot  screenshots/usage/administration/a_tableau_de_bord_widget_liste.png
    ...    formulaire
    # Capture -
    Depuis la composition du tableau de bord
    Capture and crop page screenshot
    ...  screenshots/usage/administration/a_tableau_de_bord_composition_select_profil.png
    ...  content
    # Capture - Exemple de composition
    Select From List By Label  css=#om_profil  0 - ADMINISTRATEUR
    Sleep  1
    Capture and crop page screenshot
    ...  screenshots/usage/administration/a_tableau_de_bord_composition_exemple.png
    ...  content


Manuel d'usage - Génération
    [Tags]  doc
    #
    Depuis la page d'accueil  admin  admin
    # GENERATION
    Depuis le module de génération
    Capture and crop page screenshot  screenshots/usage/generation/a_generateur_menu_general.png
    ...    content
    Click Element  css=#gen-action-generate-om_parametre
    Capture and crop page screenshot  screenshots/usage/generation/a_generateur_gen_om_parametre.png
    ...    content


Manuel de référence
    [Tags]  doc
    #
    Depuis la page d'accueil  admin  admin

    # PARAMETRAGE
    # zones_de_navigation
    Go To Dashboard
    Click On Menu Rubrik  administration
    Remove element  dashboard
    Update element style  css=#content  height  300px
    Highlight heading  css=#menu
    Add note  css=#menu  Menu  position=right
    Add pointy note  css=#shortlinks  Raccourcis  position=bottom
    Add pointy note  css=#actions  Actions personnelles  position=left
    Highlight heading  css=#footer
    Add note  css=#footer  Actions globales  position=top
    Capture viewport screenshot  screenshots/reference/a_parametrage_zones_de_navigation.png
    # zones_de_navigation
    Go To Dashboard
    Click On Menu Rubrik  administration
    Capture and crop page screenshot  screenshots/reference/a_parametrage_zones_de_navigation_actions_globales.png
    ...  footer
    Capture and crop page screenshot  screenshots/reference/a_parametrage_zones_de_navigation_actions_personnelles.png
    ...  actions
    Capture and crop page screenshot  screenshots/reference/a_parametrage_zones_de_navigation_raccourcis.png
        ...  shortlinks
    Capture and crop page screenshot  screenshots/reference/a_parametrage_zones_de_navigation_menu.png
    ...  menu

    # FORMULAIRE
    # Capture - Vue consulter + portlet d'actions contextuelles
    Depuis le contexte de l'utilisateur  admin
    Highlight heading  css=#portlet-actions
    Add pointy note  css=#portlet-actions  Actions contextuelles  position=left
    Capture and crop page screenshot
    ...  screenshots/reference/a_formulaire_view_consulter.png
    ...  content
    # Capture - Vue modifier
    Click On Form Portlet Action  om_utilisateur  modifier
    Capture and crop page screenshot
    ...  screenshots/reference/a_formulaire_view_modifier.png
    ...  content

    # LISTING
    # Capture - Exemple de listing
    Depuis le listing des paramètres
    Capture and crop page screenshot
    ...  screenshots/reference/a_listing_exemple.png
    ...  content
    # Capture - Détail des zones d'actions sur les listings
    Depuis le listing des utilisateurs
    Add pointy note  css=.add-16  corner  position=top  width=70
    Add pointy note  css=.consult-16  left  position=bottom  width=70
    Add pointy note  css=a.lienTable  content  position=right  width=70
    Capture and crop page screenshot
    ...  screenshots/reference/a_listing_actions_detail_des_zones.png
    ...  content

    # MODULE REQMO
    # Capture - Listing de toutes les requêtes mémorisées
    Depuis l'écran principal du module 'Reqmo'
    Capture and crop page screenshot
    ...  screenshots/reference/a_reqmo_listing_des_exports_disponibles.png
    ...  content
    # Capture - Exemple d'un formulaire d'export
    Click Element  css=#action-reqmo-om_utilisateur-exporter
    Page Title Should Be  Export > Requêtes Mémorisées > Utilisateur
    Page Should Contain  Options de sortie
    Capture and crop page screenshot
    ...  screenshots/reference/a_reqmo_formulaire_exemple.png
    ...  content
    # Capture - Exemple d'un résultat sortie tableau
    Select from List By Value  css=#sortie  tableau
    Click On Submit Button In Reqmo
    Page Title Should Be  Export > Requêtes Mémorisées > Utilisateur
    Page Should Contain  21232f297a57a5a743894a0e4a801fc3
    Capture and crop page screenshot
    ...  screenshots/reference/a_reqmo_affichage_sortie_tableau.png
    ...  content
    # Capture - Exemple d'un résultat sortie csv
    Click On Back Button
    Page Title Should Be  Export > Requêtes Mémorisées > Utilisateur
    Page Should Contain  Options de sortie
    Select from List By Value  css=#sortie  csv
    Click On Submit Button In Reqmo
    Page Should Contain  Le fichier a été exporté, vous pouvez l'ouvrir immédiatement en cliquant sur
    Capture and crop page screenshot
    ...  screenshots/reference/a_reqmo_affichage_sortie_csv.png
    ...  content

