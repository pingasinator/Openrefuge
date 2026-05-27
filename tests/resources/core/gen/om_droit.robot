*** Settings ***
Documentation    CRUD de la table om_droit
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte droit
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_droit}

    # On accède au tableau
    Go To Tab  om_droit
    # On recherche l'enregistrement
    Use Simple Search  droit  ${om_droit}
    # On clique sur le résultat
    Click On Link  ${om_droit}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter droit
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_droit
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir droit  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_droit} =  Get Text  css=div.form-content span#om_droit
    # On le retourne
    [Return]  ${om_droit}

Modifier droit
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_droit}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte droit  ${om_droit}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_droit  modifier
    # On saisit des valeurs
    Saisir droit  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer droit
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_droit}

    # On accède à l'enregistrement
    Depuis le contexte droit  ${om_droit}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_droit  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir droit
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_profil" existe dans "${values}" on execute "Select From List By Label" dans le formulaire