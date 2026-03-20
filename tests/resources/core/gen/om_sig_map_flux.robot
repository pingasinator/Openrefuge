*** Settings ***
Documentation    CRUD de la table om_sig_map_flux
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte om_sig_map_flux
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_sig_map_flux}

    # On accède au tableau
    Go To Tab  om_sig_map_flux
    # On recherche l'enregistrement
    Use Simple Search  om_sig_map_flux  ${om_sig_map_flux}
    # On clique sur le résultat
    Click On Link  ${om_sig_map_flux}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter om_sig_map_flux
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_sig_map_flux
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir om_sig_map_flux  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_sig_map_flux} =  Get Text  css=div.form-content span#om_sig_map_flux
    # On le retourne
    [Return]  ${om_sig_map_flux}

Modifier om_sig_map_flux
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_sig_map_flux}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_map_flux  ${om_sig_map_flux}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_sig_map_flux  modifier
    # On saisit des valeurs
    Saisir om_sig_map_flux  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer om_sig_map_flux
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_sig_map_flux}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_map_flux  ${om_sig_map_flux}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_sig_map_flux  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir om_sig_map_flux
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "om_sig_flux" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "om_sig_map" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "ol_map" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "ordre" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "visibility" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "panier" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "pa_nom" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "pa_layer" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "pa_attribut" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "pa_encaps" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "pa_sql" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "pa_type_geometrie" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "sql_filter" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "baselayer" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "singletile" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "maxzoomlevel" existe dans "${values}" on execute "Input Text" dans le formulaire