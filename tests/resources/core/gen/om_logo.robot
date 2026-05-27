*** Settings ***
Documentation    CRUD de la table om_logo
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte logo
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_logo}

    # On accède au tableau
    Go To Tab  om_logo
    # On recherche l'enregistrement
    Use Simple Search  logo  ${om_logo}
    # On clique sur le résultat
    Click On Link  ${om_logo}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter logo
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_logo
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir logo  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_logo} =  Get Text  css=div.form-content span#om_logo
    # On le retourne
    [Return]  ${om_logo}

Modifier logo
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_logo}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte logo  ${om_logo}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_logo  modifier
    # On saisit des valeurs
    Saisir logo  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer logo
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_logo}

    # On accède à l'enregistrement
    Depuis le contexte logo  ${om_logo}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_logo  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir logo
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "id" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "description" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "fichier" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "resolution" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "actif" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "om_collectivite" existe dans "${values}" on execute "Select From List By Label" dans le formulaire