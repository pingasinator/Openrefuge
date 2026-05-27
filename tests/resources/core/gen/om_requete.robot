*** Settings ***
Documentation    CRUD de la table om_requete
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte requête
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_requete}

    # On accède au tableau
    Go To Tab  om_requete
    # On recherche l'enregistrement
    Use Simple Search  requête  ${om_requete}
    # On clique sur le résultat
    Click On Link  ${om_requete}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter requête
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_requete
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir requête  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_requete} =  Get Text  css=div.form-content span#om_requete
    # On le retourne
    [Return]  ${om_requete}

Modifier requête
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_requete}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte requête  ${om_requete}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_requete  modifier
    # On saisit des valeurs
    Saisir requête  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer requête
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_requete}

    # On accède à l'enregistrement
    Depuis le contexte requête  ${om_requete}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_requete  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir requête
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "code" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "description" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "requete" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "merge_fields" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "type" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "classe" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "methode" existe dans "${values}" on execute "Input Text" dans le formulaire