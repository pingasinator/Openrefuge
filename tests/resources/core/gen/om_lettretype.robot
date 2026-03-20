*** Settings ***
Documentation    CRUD de la table om_lettretype
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte lettre type
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_lettretype}

    # On accède au tableau
    Go To Tab  om_lettretype
    # On recherche l'enregistrement
    Use Simple Search  lettre type  ${om_lettretype}
    # On clique sur le résultat
    Click On Link  ${om_lettretype}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter lettre type
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_lettretype
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir lettre type  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_lettretype} =  Get Text  css=div.form-content span#om_lettretype
    # On le retourne
    [Return]  ${om_lettretype}

Modifier lettre type
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_lettretype}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte lettre type  ${om_lettretype}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_lettretype  modifier
    # On saisit des valeurs
    Saisir lettre type  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer lettre type
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_lettretype}

    # On accède à l'enregistrement
    Depuis le contexte lettre type  ${om_lettretype}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_lettretype  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir lettre type
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
    Si "margeleft" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "margetop" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "margeright" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "margebottom" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "se_font" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "se_couleurtexte" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "header_om_htmletat" existe dans "${values}" on execute "Input HTML" dans le formulaire
    Si "header_offset" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "footer_om_htmletat" existe dans "${values}" on execute "Input HTML" dans le formulaire
    Si "footer_offset" existe dans "${values}" on execute "Input Text" dans le formulaire