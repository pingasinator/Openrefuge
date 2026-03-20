*** Settings ***
Documentation    CRUD de la table om_widget
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte widget
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_widget}

    # On accède au tableau
    Go To Tab  om_widget
    # On recherche l'enregistrement
    Use Simple Search  widget  ${om_widget}
    # On clique sur le résultat
    Click On Link  ${om_widget}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter widget
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_widget
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir widget  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_widget} =  Get Text  css=div.form-content span#om_widget
    # On le retourne
    [Return]  ${om_widget}

Modifier widget
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_widget}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte widget  ${om_widget}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_widget  modifier
    # On saisit des valeurs
    Saisir widget  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer widget
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_widget}

    # On accède à l'enregistrement
    Depuis le contexte widget  ${om_widget}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_widget  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir widget
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "lien" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "texte" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "type" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "script" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "arguments" existe dans "${values}" on execute "Input Text" dans le formulaire