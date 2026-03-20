*** Settings ***
Documentation    CRUD de la table om_parametre
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte paramètre
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_parametre}

    # On accède au tableau
    Go To Tab  om_parametre
    # On recherche l'enregistrement
    Use Simple Search  paramètre  ${om_parametre}
    # On clique sur le résultat
    Click On Link  ${om_parametre}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter paramètre
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_parametre
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir paramètre  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_parametre} =  Get Text  css=div.form-content span#om_parametre
    # On le retourne
    [Return]  ${om_parametre}

Modifier paramètre
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_parametre}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte paramètre  ${om_parametre}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_parametre  modifier
    # On saisit des valeurs
    Saisir paramètre  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer paramètre
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_parametre}

    # On accède à l'enregistrement
    Depuis le contexte paramètre  ${om_parametre}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_parametre  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir paramètre
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "valeur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_collectivite" existe dans "${values}" on execute "Select From List By Label" dans le formulaire