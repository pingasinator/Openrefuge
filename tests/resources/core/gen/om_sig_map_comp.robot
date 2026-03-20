*** Settings ***
Documentation    CRUD de la table om_sig_map_comp
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte om_sig_map_comp
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_sig_map_comp}

    # On accède au tableau
    Go To Tab  om_sig_map_comp
    # On recherche l'enregistrement
    Use Simple Search  om_sig_map_comp  ${om_sig_map_comp}
    # On clique sur le résultat
    Click On Link  ${om_sig_map_comp}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter om_sig_map_comp
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_sig_map_comp
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir om_sig_map_comp  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_sig_map_comp} =  Get Text  css=div.form-content span#om_sig_map_comp
    # On le retourne
    [Return]  ${om_sig_map_comp}

Modifier om_sig_map_comp
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_sig_map_comp}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_map_comp  ${om_sig_map_comp}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_sig_map_comp  modifier
    # On saisit des valeurs
    Saisir om_sig_map_comp  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer om_sig_map_comp
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_sig_map_comp}

    # On accède à l'enregistrement
    Depuis le contexte om_sig_map_comp  ${om_sig_map_comp}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_sig_map_comp  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir om_sig_map_comp
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "om_sig_map" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "ordre" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "actif" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "comp_maj" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "type_geometrie" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "comp_table_update" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "comp_champ" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "comp_champ_idx" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "obj_class" existe dans "${values}" on execute "Input Text" dans le formulaire