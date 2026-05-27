*** Settings ***
Documentation    CRUD de la table om_etat
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte état
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_etat}

    # On accède au tableau
    Go To Tab  om_etat
    # On recherche l'enregistrement
    Use Simple Search  état  ${om_etat}
    # On clique sur le résultat
    Click On Link  ${om_etat}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter état
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_etat
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir état  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_etat} =  Get Text  css=div.form-content span#om_etat
    # On le retourne
    [Return]  ${om_etat}

Modifier état
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_etat}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte état  ${om_etat}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_etat  modifier
    # On saisit des valeurs
    Saisir état  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer état
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_etat}

    # On accède à l'enregistrement
    Depuis le contexte état  ${om_etat}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_etat  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir état
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "om_collectivite" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "id" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "actif" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "orientation" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "format" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "logo" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "logoleft" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "logotop" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titre_om_htmletat" existe dans "${values}" on execute "Input HTML" dans le formulaire
    Si "titreleft" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titretop" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrelargeur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrehauteur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrebordure" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "corps_om_htmletatex" existe dans "${values}" on execute "Input HTML" dans le formulaire
    Si "om_sql" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "se_font" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "se_couleurtexte" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "margeleft" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "margetop" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "margeright" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "margebottom" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "header_om_htmletat" existe dans "${values}" on execute "Input HTML" dans le formulaire
    Si "header_offset" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "footer_om_htmletat" existe dans "${values}" on execute "Input HTML" dans le formulaire
    Si "footer_offset" existe dans "${values}" on execute "Input Text" dans le formulaire