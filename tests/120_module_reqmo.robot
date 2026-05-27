*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Module 'Reqmo'" : ...


*** Test Cases ***
Fonctionnement basique

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    Depuis l'écran principal du module 'Reqmo'
    Page Title Should Be  Export > Requêtes Mémorisées
    Page Should Contain  choix de la requete memorisee

    #
    Click Element  css=#action-reqmo-om_utilisateur-exporter
    Page Title Should Be  Export > Requêtes Mémorisées > Utilisateur
    Page Should Contain  Options de sortie
    Click On Submit Button In Reqmo
    Page Title Should Be  Export > Requêtes Mémorisées > Utilisateur
    Page Should Contain  21232f297a57a5a743894a0e4a801fc3
    Click On Back Button
    Page Title Should Be  Export > Requêtes Mémorisées > Utilisateur
    Page Should Contain  Options de sortie
    Select from List By Value  css=#sortie  csv
    Click On Submit Button In Reqmo
    Page Should Contain  Le fichier a été exporté, vous pouvez l'ouvrir immédiatement en cliquant sur
    #
    ${link_reqmo} =  Get Element Attribute  css=a#reqmo-out-link  href
    ${output_dir}  ${output_name} =  Télécharger un fichier  ${SESSION_COOKIE}  ${link_reqmo}  ${EXECDIR}${/}results${/}
    ${full_path_to_file} =  Catenate  SEPARATOR=  ${output_dir}  ${output_name}
    ${content_file} =  Get File  ${full_path_to_file}
    #
    ${expected_content_header} =  Set Variable  om_utilisateur;nom;email;login;pwd;om_profil;om_collectivite;om_type
    Should Contain  ${content_file}  ${expected_content_header}
    ${expected_content_line} =  Set Variable  1;Administrateur;nospam@openmairie.org;admin;21232f297a57a5a743894a0e4a801fc3;1;1;DB
    Should Contain  ${content_file}  ${expected_content_line}
    #
    Click On Back Button
    Page Title Should Be  Export > Requêtes Mémorisées > Utilisateur
    Page Should Contain  Options de sortie
    Click On Back Button
    Page Title Should Be  Export > Requêtes Mémorisées
    Page Should Contain  choix de la requete memorisee

    # Vérification de la gestion des erreurs si l'objet passé à l'écran reqmo
    # n'existe pas
    Go To  ${PROJECT_URL}${OM_ROUTE_MODULE_REQMO}&obj=objet_inexistant
    La page ne doit pas contenir d'erreur
    Error Message Should Be  L'objet est invalide.

