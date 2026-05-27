*** Settings ***
Documentation    CRUD de la table om_profil
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte profil
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_profil}

    # On accède au tableau
    Go To Tab  om_profil
    # On recherche l'enregistrement
    Use Simple Search  profil  ${om_profil}
    # On clique sur le résultat
    Click On Link  ${om_profil}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter profil
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_profil
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir profil  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_profil} =  Get Text  css=div.form-content span#om_profil
    # On le retourne
    [Return]  ${om_profil}

Modifier profil
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_profil}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte profil  ${om_profil}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_profil  modifier
    # On saisit des valeurs
    Saisir profil  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer profil
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_profil}

    # On accède à l'enregistrement
    Depuis le contexte profil  ${om_profil}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_profil  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir profil
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "hierarchie" existe dans "${values}" on execute "Input Text" dans le formulaire