*** Settings ***
Documentation    CRUD de la table om_collectivite
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte collectivité
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_collectivite}

    # On accède au tableau
    Go To Tab  om_collectivite
    # On recherche l'enregistrement
    Use Simple Search  collectivité  ${om_collectivite}
    # On clique sur le résultat
    Click On Link  ${om_collectivite}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter collectivité
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_collectivite
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir collectivité  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_collectivite} =  Get Text  css=div.form-content span#om_collectivite
    # On le retourne
    [Return]  ${om_collectivite}

Modifier collectivité
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_collectivite}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte collectivité  ${om_collectivite}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_collectivite  modifier
    # On saisit des valeurs
    Saisir collectivité  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer collectivité
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_collectivite}

    # On accède à l'enregistrement
    Depuis le contexte collectivité  ${om_collectivite}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_collectivite  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir collectivité
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "niveau" existe dans "${values}" on execute "Input Text" dans le formulaire