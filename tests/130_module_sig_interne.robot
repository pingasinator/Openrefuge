*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Module 'SIG interne'" : ...


*** Test Cases ***
Constitution du jeu de données

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    Activer l'option 'SIG interne'

    #
    &{etendue01} =  Create Dictionary
    ...  nom=testetendue01
    ...  extent=5.2267,43.2199,5.5756,43.3676
    ...  valide=true
    Set Suite Variable  ${etendue01}
    Ajouter l'étendue  ${etendue01}

    #
    &{flux01} =  Create Dictionary
    ...  id=testflux01
    ...  libelle=Libelle
    ...  attribution=...
    ...  cache_type=WMS
    ...  chemin=...
    ...  couches=...
    ...  cache_gfi_chemin=...
    ...  cache_gfi_couches=...
    Set Suite Variable  ${flux01}
    Ajouter le flux  ${flux01}

    #
    &{carte01} =  Create Dictionary
    ...  id=testcarte01
    ...  libelle=Libelle
    ...  actif=true
    ...  projection_externe=lambert93
    ...  zoom=10
    ...  fond_osm=true
    ...  fond_default=osm
    ...  util_recherche=true
    ...  om_sig_extent=${etendue01.nom}
    ...  url=...
    ...  om_sql=SELECT ST_asText('01010000206A080000C6DE4AFF7E552B412CF66CF750D35741') as geom, '2' as titre, '3' as description, 4 as idx, '5' as plop
    ...  retour=...
    Set Suite Variable  ${carte01}
    Ajouter la carte  ${carte01}


Intégration de l'objet 'étendue'

    [Documentation]  L'objectif de ce TestCase est de vérifier l'intégration
    ...  de l'étendue (om_sig_extent) :
    ...  - Entrée de menu
    ...  - Titre de la page
    ...  - Listing (à faire)
    ...  - Formulaire d'ajout (à faire)
    ...  - Formulaire de modification (à faire)
    ...  - Formulaire de suppression (à faire)
    ...  - Onglets (à faire)

    #
    Depuis le listing des étendues
    Page Title Should Be  Administration > SIG > Étendue
    Submenu In Menu Should Be Selected  administration  om_sig_extent


Intégration de l'objet 'flux'

    [Documentation]  L'objectif de ce TestCase est de vérifier l'intégration
    ...  du flux (om_sig_flux) :
    ...  - Entrée de menu
    ...  - Titre de la page
    ...  - Listing (à faire)
    ...  - Formulaire d'ajout (à faire)
    ...  - Formulaire de modification (à faire)
    ...  - Formulaire de suppression (à faire)
    ...  - Onglets (à faire)

    #
    Depuis le listing des flux
    Page Title Should Be  Administration > SIG > Flux
    Submenu In Menu Should Be Selected  administration  om_sig_flux


Intégration de l'objet 'carte'

    [Documentation]  L'objectif de ce TestCase est de vérifier l'intégration
    ...  de la carte (om_sig_map) :
    ...  - Entrée de menu
    ...  - Titre de la page
    ...  - Listing (à faire)
    ...  - Formulaire d'ajout (à faire)
    ...  - Formulaire de modification (à faire)
    ...  - Formulaire de suppression (à faire)
    ...  - Onglets (à faire)

    #
    Depuis le listing des cartes
    Page Title Should Be  Administration > SIG > Carte
    Submenu In Menu Should Be Selected  administration  om_sig_map


Intégration de la VIEW 'map'

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin

    # Vérification de la gestion des erreurs si aucun paramètre n'est passé
    Go To  ${PROJECT_URL}${OM_ROUTE_MAP}&mode=tab_sig
    La page ne doit pas contenir d'erreur
    Error Message Should Be  Obj obligatoire

    # Vérification de la gestion des erreurs si l'objet passé n'existe pas
    Go To  ${PROJECT_URL}${OM_ROUTE_MAP}&mode=tab_sig&obj=objet_inexistant
    La page ne doit pas contenir d'erreur
    Error Message Should Be  L'objet est invalide.

    # Vérification de la gestion des erreurs si l'objet passé existe
    # > Affichage de la carte
    Go To  ${PROJECT_URL}${OM_ROUTE_MAP}&mode=tab_sig&obj=${carte01.id}
    La page ne doit pas contenir d'erreur

    # Vérification de la gestion des erreurs si aucun paramètre n'est passé
    Go To  ${PROJECT_URL}${OM_ROUTE_MAP}&mode=form_sig
    La page ne doit pas contenir d'erreur
    Error Message Should Be  Obj obligatoire

    # Vérification de la gestion des erreurs si l'objet passé n'existe pas
    Go To  ${PROJECT_URL}${OM_ROUTE_MAP}&mode=form_sig&obj=objet_inexistant
    La page ne doit pas contenir d'erreur
    Error Message Should Be  L'objet est invalide.


Déconstitution du jeu de données

    [Documentation]  ...

    #
    Depuis la page d'accueil  admin  admin
    Désactiver l'option 'SIG interne'

