*** Settings ***
Documentation    CRUD de la table om_sousetat
...    @author  generated
...    @package framework_openmairie
...    @version 02/05/2018 15:05

*** Keywords ***

Depuis le contexte sous état
    [Documentation]  Accède au formulaire
    [Arguments]  ${om_sousetat}

    # On accède au tableau
    Go To Tab  om_sousetat
    # On recherche l'enregistrement
    Use Simple Search  sous état  ${om_sousetat}
    # On clique sur le résultat
    Click On Link  ${om_sousetat}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors

Ajouter sous état
    [Documentation]  Crée l'enregistrement
    [Arguments]  ${values}

    # On accède au tableau
    Go To Tab  om_sousetat
    # On clique sur le bouton ajouter
    Click On Add Button
    # On saisit des valeurs
    Saisir sous état  ${values}
    # On valide le formulaire
    Click On Submit Button
    # On récupère l'ID du nouvel enregistrement
    ${om_sousetat} =  Get Text  css=div.form-content span#om_sousetat
    # On le retourne
    [Return]  ${om_sousetat}

Modifier sous état
    [Documentation]  Modifie l'enregistrement
    [Arguments]  ${om_sousetat}  ${values}

    # On accède à l'enregistrement
    Depuis le contexte sous état  ${om_sousetat}
    # On clique sur le bouton modifier
    Click On Form Portlet Action  om_sousetat  modifier
    # On saisit des valeurs
    Saisir sous état  ${values}
    # On valide le formulaire
    Click On Submit Button

Supprimer sous état
    [Documentation]  Supprime l'enregistrement
    [Arguments]  ${om_sousetat}

    # On accède à l'enregistrement
    Depuis le contexte sous état  ${om_sousetat}
    # On clique sur le bouton supprimer
    Click On Form Portlet Action  om_sousetat  supprimer
    # On valide le formulaire
    Click On Submit Button

Saisir sous état
    [Documentation]  Remplit le formulaire
    [Arguments]  ${values}
    
    Si "om_collectivite" existe dans "${values}" on execute "Select From List By Label" dans le formulaire
    Si "id" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "libelle" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "actif" existe dans "${values}" on execute "Set Checkbox" dans le formulaire
    Si "titre" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrehauteur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrefont" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titreattribut" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titretaille" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrebordure" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrealign" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrefond" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titrefondcouleur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "titretextecouleur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "intervalle_debut" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "intervalle_fin" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entete_flag" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entete_fond" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entete_orientation" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entete_hauteur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entetecolone_bordure" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entetecolone_align" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entete_fondcouleur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "entete_textecouleur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "tableau_largeur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "tableau_bordure" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "tableau_fontaille" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "bordure_couleur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "se_fond1" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "se_fond2" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fond" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_hauteur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_largeur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_bordure_un" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_bordure" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_align" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fond_total" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fontaille_total" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_hauteur_total" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fondcouleur_total" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_bordure_total" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_align_total" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fond_moyenne" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fontaille_moyenne" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_hauteur_moyenne" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fondcouleur_moyenne" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_bordure_moyenne" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_align_moyenne" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fond_nbr" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fontaille_nbr" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_hauteur_nbr" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_fondcouleur_nbr" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_bordure_nbr" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_align_nbr" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_numerique" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_total" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_moyenne" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "cellule_compteur" existe dans "${values}" on execute "Input Text" dans le formulaire
    Si "om_sql" existe dans "${values}" on execute "Input Text" dans le formulaire