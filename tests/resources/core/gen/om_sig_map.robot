*** Settings ***
Documentation    CRUD de la table om_sig_map
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte om_sig_map
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_sig_map}

    # On accède au tableau
    Go To Tab  om_sig_map
    # On recherche l'enregistrement
    Use Simple Search  om_sig_map  ${om_sig_map}
    # On clique sur le résultat
    Click On Link  ${om_sig_map}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter om_sig_map
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_sig_map
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir om_sig_map  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_sig_map} =  Get Text  css=div.form-content span#om_sig_map
    # On le retourne
    [Return]  ${om_sig_map}

Modifier om_sig_map
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_sig_map}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_map  ${om_sig_map}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_sig_map  modifier
    # On saisit des valeurs
    Saisir om_sig_map  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer om_sig_map
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_sig_map}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_map  ${om_sig_map}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_sig_map  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir om_sig_map
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "om_collectivite" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "id" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "actif" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "zoom" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "fond_osm" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "fond_bing" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "fond_sat" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "layer_info" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "projection_externe" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "url" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_sql" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "retour" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "util_idx" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "util_reqmo" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "util_recherche" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "source_flux" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "fond_default" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_sig_extent" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "restrict_extent" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "sld_marqueur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "sld_data" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "point_centrage" existe dans "${values}" on execute "Input Text" dans le formulaire