*** Settings ***
Documentation    CRUD de la table om_sig_extent
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte om_sig_extent
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_sig_extent}

    # On accède au tableau
    Go To Tab  om_sig_extent
    # On recherche l'enregistrement
    Use Simple Search  om_sig_extent  ${om_sig_extent}
    # On clique sur le résultat
    Click On Link  ${om_sig_extent}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter om_sig_extent
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_sig_extent
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir om_sig_extent  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_sig_extent} =  Get Text  css=div.form-content span#om_sig_extent
    # On le retourne
    [Return]  ${om_sig_extent}

Modifier om_sig_extent
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_sig_extent}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_extent  ${om_sig_extent}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_sig_extent  modifier
    # On saisit des valeurs
    Saisir om_sig_extent  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer om_sig_extent
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_sig_extent}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_extent  ${om_sig_extent}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_sig_extent  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir om_sig_extent
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "nom" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "extent" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "valide" existe dans "${values}" on execute "Set Checkbox" dans le formulaire