*** Settings ***
Documentation    CRUD de la table om_dashboard
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte tableau de bord
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_dashboard}

    # On accède au tableau
    Go To Tab  om_dashboard
    # On recherche l'enregistrement
    Use Simple Search  tableau de bord  ${om_dashboard}
    # On clique sur le résultat
    Click On Link  ${om_dashboard}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter tableau de bord
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_dashboard
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir tableau de bord  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_dashboard} =  Get Text  css=div.form-content span#om_dashboard
    # On le retourne
    [Return]  ${om_dashboard}

Modifier tableau de bord
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_dashboard}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte tableau de bord  ${om_dashboard}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_dashboard  modifier
    # On saisit des valeurs
    Saisir tableau de bord  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer tableau de bord
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_dashboard}

    # On accède à l'enregistrement
    Depuis le contexte tableau de bord  ${om_dashboard}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_dashboard  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir tableau de bord
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "om_profil" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "bloc" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "position" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_widget" existe dans "${values}" on execute "Select From List By Label" dans le formulaire