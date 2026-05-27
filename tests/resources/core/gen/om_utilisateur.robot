*** Settings ***
Documentation    CRUD de la table om_utilisateur
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte utilisateur
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_utilisateur}

    # On accède au tableau
    Go To Tab  om_utilisateur
    # On recherche l'enregistrement
    Use Simple Search  utilisateur  ${om_utilisateur}
    # On clique sur le résultat
    Click On Link  ${om_utilisateur}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter utilisateur
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_utilisateur
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir utilisateur  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_utilisateur} =  Get Text  css=div.form-content span#om_utilisateur
    # On le retourne
    [Return]  ${om_utilisateur}

Modifier utilisateur
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_utilisateur}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte utilisateur  ${om_utilisateur}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_utilisateur  modifier
    # On saisit des valeurs
    Saisir utilisateur  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer utilisateur
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_utilisateur}

    # On accède à l'enregistrement
    Depuis le contexte utilisateur  ${om_utilisateur}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_utilisateur  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir utilisateur
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "nom" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "email" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "login" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "pwd" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_collectivite" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "om_type" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_profil" existe dans "${values}" on execute "Select From List By Label" dans le formulaire