*** Settings ***
Documentation    CRUD de la table om_permission
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte om_permission
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_permission}

    # On accède au tableau
    Go To Tab  om_permission
    # On recherche l'enregistrement
    Use Simple Search  om_permission  ${om_permission}
    # On clique sur le résultat
    Click On Link  ${om_permission}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter om_permission
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_permission
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir om_permission  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_permission} =  Get Text  css=div.form-content span#om_permission
    # On le retourne
    [Return]  ${om_permission}

Modifier om_permission
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_permission}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte om_permission  ${om_permission}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_permission  modifier
    # On saisit des valeurs
    Saisir om_permission  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer om_permission
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_permission}

    # On accède à l'enregistrement
    Depuis le contexte om_permission  ${om_permission}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_permission  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir om_permission
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "type" existe dans "${values}" on execute "Input Text" dans le formulaire