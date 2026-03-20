*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Éditions" : cette suite permet de vérifier le
...  comportement des fonctionnalités disponibles dans la fonctionnalité
...  "Éditions" du framework. C'est-à-dire les états, lettres types,
...  sous-états, requêtes et logos.


*** Test Cases ***
Constitution du jeu de données

    [Documentation]  L'objet de ce TestCase est de constituer un jeu de
    ...  données cohérent pour les scénarios fonctionnels qui suivent.

    #
    Depuis la page d'accueil  admin  admin
    #
    ${noquery}  Set Variable  Aucune
    Set Suite Variable    ${noquery}
    Ajouter la requête  none  ${noquery}  Ne rend disponible aucun champ de fusion.  sql  SELECT 1;  ${noquery}
    #
    ${one_merge_field}  Set Variable  one_merge_field
    ${one_merge_field_text}  Set Variable  [one_merge_field]
    Set Suite Variable    ${one_merge_field}
    Set Suite Variable    ${one_merge_field_text}
    Ajouter la requête  one_merge_field  ${one_merge_field}  Rend disponible un champ de fusion  sql  SELECT 1;  ${one_merge_field_text}
    #
    ${two_merge_field}  Set Variable  two_merge_field
    ${two_merge_field_text}  Set Variable  [first_merge_field], [second_merge_field]
    Set Suite Variable    ${two_merge_field}
    Set Suite Variable    ${two_merge_field_text}
    Ajouter la requête  two_merge_field  ${two_merge_field}  Rend disponible deux champs de fusion  sql  SELECT 1;  ${two_merge_field_text}


Intégration de l'éditeur de texte riche dans un form et un sousform

    [Documentation]  Le rechargement de la page en ajax est souvent délicat
    ...  pour l'intégration de tinymce. Ce test sert à vérifier que tinymce
    ...  est correctement intégré. Pour ce faire, on vérifie après la modification
    ...  d'une lettre type que les balises html ont bien été interprétées.

    #
    Depuis la page d'accueil  admin  admin
    # Cas n°1 : Interprétation dans un form / modification simple
    #
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_tinymce_form
    #
    Ajouter la lettre-type depuis le menu  ${id}  ${id}  <p>${id}</p>  <p><span style="font-weight: bold;">${id}</span></p>  ${noquery}  true
    # On modifie la lettre type avec rechargement de page pour que tinymce interprète
    # les balises HTML
    Modifier la lettre-type  ${id}
    # On vérifie que les balise ont bien été interprétées par tinymce
    Depuis le contexte de la lettre-type  ${id}
    Page Should Not Contain  <p>${id}</p>

    # Cas n°2 : Interprétation dans un sousform / modification simple
    #
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_tinymce_sousform
    #
    Ajouter la lettre-type dans le contexte de la collectivité  ${id}  ${id}  <p>${id}</p>  <p><span style="font-weight: bold;">${id}</span></p>  ${noquery}  true  LIBREVILLE
    # On modifie la lettre type AVEC rechargement de page pour que tinymce interprète
    # les balises HTML
    Modifier la lettre-type dans le contexte de la collectivité  ${id}  null  null  null  null  null  LIBREVILLE
    # On vérifie que les balise ont bien été interprétées par tinymce
    Depuis le contexte de la lettre-type  ${id}
    Page Should Not Contain  <p>${id}</p>

    # Cas n°3 : (TNR) Interprétation dans un sousform / ajout puis modification sans rechargement de la page
    #
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_tinymce_sousform2
    #
    Ajouter la lettre-type dans le contexte de la collectivité  ${id}  ${id}  <p>${id}</p>  <p><span style="font-weight: bold;">${id}</span></p>  ${noquery}  true  LIBREVILLE
    # On modifie la lettre type SANS rechargement de page pour que tinymce interprète
    # les balises HTML
    Click On Back Button In Subform
    Click On Link    ${id}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Element Should Contain  css=#fieldset-sousform-om_lettretype-edition #id  ${id}
    Click On SubForm Portlet Action    om_lettretype    modifier
    Click On Submit Button In SubForm
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    Click On Back Button In Subform
    # On vérifie que les balise ont bien été interprétées par tinymce
    Depuis le contexte de la lettre-type  ${id}
    Page Should Not Contain  <p>${id}</p>


Prévisualisation d'une édition PDF

    [Documentation]    Il est possible de prévisualiser une lettre type/un état qu'il soit
    ...  actif ou non, ce test permet de vérifier que ce soit bien l'édition sur laquelle
    ...  on se trouve qui est en cours d'édition qui est affichée.

    #
    Depuis la page d'accueil  admin  admin
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_previsualisation

    # om_lettretype

    Ajouter la lettre-type depuis le menu  ${id}  ${id}1  <p>Prévisualisation lettretype test1</p>  <p>${id}</p>  ${noquery}  true
    Ajouter la lettre-type depuis le menu  ${id}  ${id}2  <p>Prévisualisation lettretype test2</p>  <p>${id}</p>  ${noquery}  false

    Depuis le contexte de la lettre-type  null  ${id}2
    Click On Form Portlet Action  om_lettretype  previsualiser
    # On ouvre le PDF
    Open PDF  ${OM_PDF_TITLE}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Page Should Contain  Prévisualisation lettretype test2
    Close PDF

    # om_etat

    Ajouter l'état depuis le menu  ${id}  ${id}1  <p>Prévisualisation état test1</p>  <p>${id}</p>  ${noquery}  true
    Ajouter l'état depuis le menu  ${id}  ${id}2  <p>Prévisualisation état test2</p>  <p>${id}</p>  ${noquery}  false

    Depuis le contexte de l'état  null  ${id}2
    Click On Form Portlet Action  om_etat  previsualiser
    # On ouvre le PDF
    Open PDF  ${OM_PDF_TITLE}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Page Should Contain  Prévisualisation état test2
    Close PDF


Chargement des champs de fusion dans l'aide à la saisie

    [Documentation]  Au changement de requête utilisée par l'édition, l'aide à
    ...  la saisie disponible en bas du formulaire, change pour n'afficher que
    ...  les champs de fusion porposée par le requête SQL.

    #
    Depuis la page d'accueil  admin  admin

    ## OM_LETTRETYPE
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id_om_lettretype}  Set Variable  om_lettretype_test_merge_fields
    Ajouter la lettre-type depuis le menu  ${id_om_lettretype}  ${id_om_lettretype}  <p>Vérification des champs de fusion dans l aide à la saisie</p>  <p>${id_om_lettretype}</p>  ${noquery}  true
    # On vérifie le chargement des champs de fusion dans l'aide à la saisie pour
    # la lettre type
    Depuis le contexte de la lettre-type  ${id_om_lettretype}
    Click On Form Portlet Action  om_lettretype  modifier
    # On vérifie qu'il n'y a pas de champs de fusion affichés dans l'aide à la
    # saisie
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${noquery}
    # On modifier la requête pour vérifier que les champs de fusion dans l'aide
    # à la saisie soient modifiés
    Select From List By Label  om_sql  ${one_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${one_merge_field_text}
    Select From List By Label  om_sql  ${two_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${two_merge_field_text}
    #
    Depuis le contexte de la lettre-type dans le contexte de la collectivité  ${id_om_lettretype}  null  LIBREVILLE
    Click On SubForm Portlet Action  om_lettretype  modifier
    # On vérifie qu'il n'y a pas de champs de fusion affichés dans l'aide à la
    # saisie
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${noquery}
    # On modifier la requête pour vérifier que les champs de fusion dans l'aide
    # à la saisie soient modifiés
    Select From List By Label  om_sql  ${one_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${one_merge_field_text}
    Select From List By Label  om_sql  ${two_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${two_merge_field_text}

    ## OM_ETAT
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id_om_etat}  Set Variable  om_etat_test_merge_fields
    Ajouter l'état depuis le menu  ${id_om_etat}  ${id_om_etat}  <p>Vérification des champs de fusion dans l aide à la saisie</p>  <p>${id_om_etat}</p>  ${noquery}  true
    # On vérifie le chargement des champs de fusion dans l'aide à la saisie pour
    # l'état
    Depuis le contexte de l'état  ${id_om_etat}
    Click On Form Portlet Action  om_etat  modifier
    # On vérifie qu'il n'y a pas de champs de fusion affichés dans l'aide à la
    # saisie
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${noquery}
    # On modifier la requête pour vérifier que les champs de fusion dans l'aide
    # à la saisie soient modifiés
    Select From List By Label  om_sql  ${one_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${one_merge_field_text}
    Select From List By Label  om_sql  ${two_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${two_merge_field_text}
    #
    Depuis le contexte de l'état dans le contexte de la collectivité  ${id_om_etat}  null  LIBREVILLE
    Click On SubForm Portlet Action  om_etat  modifier
    # On vérifie qu'il n'y a pas de champs de fusion affichés dans l'aide à la
    # saisie
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${noquery}
    # On modifier la requête pour vérifier que les champs de fusion dans l'aide
    # à la saisie soient modifiés
    Select From List By Label  om_sql  ${one_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${one_merge_field_text}
    Select From List By Label  om_sql  ${two_merge_field}
    Wait Until Keyword Succeeds  ${TIMEOUT}  ${RETRY_INTERVAL}  Form Static Value Should Be  merge_fields  ${two_merge_field_text}


Fonctionnalité "Saut de page dans le champs corps d'une édition PDF"

    [Documentation]   Dans le formulaire d'une lettre type, il est possible
    ...    d'ajouter des sauts de page.Ce marqueur peut si le paramétrage
    ...    n'est pas correct être filtré par l'éditeur HTML. Ce TestCase
    ...    permet de vérifier qu'en ajoutant le marqueur saut de page dans
    ...    le corps d'une lettre type puis en modifiant le formulaire, pour
    ...    que l'éditeur HTML applique son filtre de balises valides, notre
    ...    marqueur de saut de page est toujours présent.

    #
    Depuis la page d'accueil  admin  admin
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_editions_pagebreak

    # On ajoute une lettre type avec un page break dans le corps
    # XXX Attention l'ajout se fait avec des balises HTML qui ne sont pas
    #     interprétées lors de la première validation du formulaire, elles le
    #     seront lors de la prochaine validation du formulaire.
    # XXX Il faut trouver une solution pour permettre l'interprétation des
    #     balises HTML par TinyMCE grâce à des mots clés RF.
    Ajouter la lettre-type depuis le menu  ${id}  ${id}  <h1>${id}</h1>  <h1>PageBreak <br pagebreak="true" />test</h1><p><br pagebreak="true" /></p><p>azerty</p>  ${noquery}  true

    # On modifie la lettre type
    # XXX Cette modification permet également d'interpréter les balises html
    #     non interprétées à l'ajout.
    Modifier la lettre-type  ${id}

    # On vérifie que dans la fiche de consultation de la lettre type
    Depuis le contexte de la lettre-type  ${id}
    ${source} =  Get Source
    # Le page break est toujours présent
    Should Contain X Times  ${source}  <br pagebreak="true" />  2
    # Le bloc d'affichage est bien présent
    Should Contain X Times  ${source}  <p class="pagebreak">  2

    # On vérifie que dans le formulaire de modification
    Depuis le contexte de la lettre-type  ${id}
    Click On Form Portlet Action  om_lettretype  modifier
    # L'éditeur HTML remplace effectivement les marqueurs par des images
    Select Frame    corps_om_htmletatex_ifr
    Element Should Be Visible  css=h1>img.mce-pagebreak
    Element Should Be Visible  css=p>img.mce-pagebreak
    Unselect Frame


Fonctionnalité "Bloc insécable dans une édition PDF"

    [Documentation]  Ce test vérifie que la propriété
    ...  (table_unbreakable_breakable_property) sur un tableau permet de le
    ...  rendre insécable.Le test vérifie, que la propriété n'est pas filtrée
    ...  par TinyMCE et qu'elle est correctement interprétée dans le PDF.

    #
    Depuis la page d'accueil  admin  admin
    # Offset permettant de décaler le tableau pour qu'il se positionne en fin
    # page et puisse être à cheval sur deux pages
    ${offset}    Set Variable    <p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p><p>0</p>

    #
    # Cas d'utilisation n°1 : le tableau est sécable
    #
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_editions_table_breakable_property
    # On ajoute une lettre type qui contient un tableau avec un attribut
    # nobr='false'
    # XXX Attention l'ajout se fait avec des balises HTML qui ne sont pas
    #     interprétées lors de la première validation du formulaire, elles le
    #     seront lors de la prochaine validation du formulaire.
    # XXX Il faut trouver une solution pour permettre l'interprétation des
    #     balises HTML par TinyMCE grâce à des mots clés RF.
    Ajouter la lettre-type depuis le menu  ${id}  ${id}  <p>${id}</p>  <p><span style="font-weight: bold;">${id}</span></p>${offset}<table nobr="false"><tr><td>a<br/>b<br/>c<br/>d<br/>e<br/>f<br/>g<br/>h<br/>i</td></tr></table>  ${noquery}  true
    # On modifie la lettre type
    # XXX Cette modification permet également d'interpréter les balises html
    #     non interprétées à l'ajout.
    Modifier la lettre-type  ${id}
    # On ouvre la prévisualisation du fichier PDF
    Depuis le contexte de la lettre-type  ${id}
    Click On Form Portlet Action  om_lettretype  previsualiser
    Open PDF  ${OM_PDF_TITLE}
    # On vérifie que le contenu du tableau est bien splitté sur deux pages
    PDF Page Number Should Contain  1  a\nb\nc\nd\ne
    PDF Page Number Should Contain  2  f\ng\nh\ni
    # On ferme le fichier
    Close PDF

    #
    # Cas d'utilisation n°2 : le tableau est insécable
    #
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_editions_table_unbreakable_property
    # On ajoute une lettre type qui contient un tableau avec un attribut
    # nobr='true'
    # XXX Attention l'ajout se fait avec des balises HTML qui ne sont pas
    #     interprétées lors de la première validation du formulaire, elles le
    #     seront lors de la prochaine validation du formulaire.
    # XXX Il faut trouver une solution pour permettre l'interprétation des
    #     balises HTML par TinyMCE grâce à des mots clés RF.
    Ajouter la lettre-type depuis le menu  ${id}  ${id}  <p>${id}</p>  <p><span style="font-weight: bold;">${id}</span></p>${offset}<table nobr="true"><tr><td>a<br/>b<br/>c<br/>d<br/>e<br/>f<br/>g<br/>h<br/>i</td></tr></table>  ${noquery}  true
    # On modifie la lettre type
    # XXX Cette modification permet également d'interpréter les balises html
    #     non interprétées à l'ajout.
    Modifier la lettre-type  ${id}
    # On ouvre la prévisualisation du fichier PDF
    Depuis le contexte de la lettre-type  ${id}
    Click On Form Portlet Action  om_lettretype  previsualiser
    Open PDF  ${OM_PDF_TITLE}
    # On vérifie que le contenu du tableau est entièrement sur la page 2
    PDF Page Number Should Contain  2  a\nb\nc\nd\ne\nf\ng\nh\ni
    # On ferme le fichier
    Close PDF


Fonctionnalité "Sous état dans une édition PDF"

    [Documentation]  Ce test vérifie que l'insertion d'un sous-état est rendu
    ...  correctement dans l'édition PDF. La vérification est faite sur
    ...  le titre du sous-état et le libéllé des colonnes du tableau
    ...  présent dans le sous-état.

    #
    Depuis la page d'accueil  admin  admin
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_editions_insertion_sousetat
    # On ajoute un sous-état
    Ajouter le sous état    null    om_droit_from_om_utilisateur    Liste des droits d un utilisateur    true    Liste des droits d un utilisateur    SELECT '-' as test, om_droit.om_droit, om_droit.libelle FROM &DB_PREFIXEom_utilisateur LEFT JOIN &DB_PREFIXEom_profil ON om_utilisateur.om_profil=om_profil.om_profil LEFT JOIN &DB_PREFIXEom_droit ON om_profil.om_profil=om_droit.om_profil WHERE om_utilisateur.om_utilisateur=&idx;
    # On ajoute une lettre type qui contient un sous-état
    # XXX Attention l'ajout se fait avec des balises HTML qui ne sont pas
    #     interprétées lors de la première validation du formulaire, elles le
    #     seront lors de la prochaine validation du formulaire.
    # XXX Il faut trouver une solution pour permettre l'interprétation des
    #     balises HTML par TinyMCE grâce à des mots clés RF.
    Ajouter la lettre-type depuis le menu  ${id}  ${id}  <p>Test Sous Etat Utilisateur</p>  <p><span style="font-weight: bold;">Test Sous Etat Utilisateur</span></p><p><span id="om_droit_from_om_utilisateur" class="mce_sousetat">Liste des droits d un utilisateur</span></p>  ${noquery}  true
    # XXX Cette modification permet également d'interpréter les balises html
    #     non interprétées à l'ajout.
    Modifier la lettre-type  ${id}
    # On ouvre la prévisualisation du fichier PDF
    Depuis le contexte de la lettre-type  ${id}
    Click On Form Portlet Action  om_lettretype  previsualiser
    Open PDF  ${OM_PDF_TITLE}
    # On vérifie que le titre du sous état et que les titres des colonnes
    # sont bien présents dans le PDF
    PDF Page Number Should Contain  1  Liste des droits d un utilisateur
    PDF Page Number Should Contain  1  TEST
    PDF Page Number Should Contain  1  DROIT
    PDF Page Number Should Contain  1  LIBELLÉ
    # On ferme le fichier
    Close PDF


Fonctionnalité "Code barres dans une édition PDF"

    [Documentation]  Ce test vérifie que l'insertion d'un code barres est rendu
    ...  correctement dans l'édition PDF. La vérification est faite sur
    ...  la valeur du code barre car aujourd'hui, pas de moyen de tester les
    ...  barres.

    #
    Depuis la page d'accueil  admin  admin
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_editions_code_barres
    # On ajoute une lettre type qui contient un code barres
    # XXX Attention l'ajout se fait avec des balises HTML qui ne sont pas
    #     interprétées lors de la première validation du formulaire, elles le
    #     seront lors de la prochaine validation du formulaire.
    # XXX Il faut trouver une solution pour permettre l'interprétation des
    #     balises HTML par TinyMCE grâce à des mots clés RF.
    Ajouter la lettre-type depuis le menu  ${id}  ${id}  <p>${id}</p><p><span class="mce_codebarre">titre132135465432</span></p>  <p><span style="font-weight: bold;">Test Code Barres</span></p><p><span class="mce_codebarre">corps132135465432</span></p>  ${noquery}  true
    # XXX Cette modification permet également d'interpréter les balises html
    #     non interprétées à l'ajout.
    Modifier la lettre-type  ${id}
    # On ouvre la prévisualisation du fichier PDF
    Depuis le contexte de la lettre-type  ${id}
    Click On Form Portlet Action  om_lettretype  previsualiser
    Open PDF  ${OM_PDF_TITLE}
    # On vérifie que le titre du sous état et que les titres des colonnes
    # sont bien présents dans le PDF
    PDF Page Number Should Contain  1  titre132135465432
    PDF Page Number Should Contain  1  corps132135465432
    # On ferme le fichier
    Close PDF


Fonctionnalité "Minuscule/Majuscule dans une édition PDF"

    [Documentation]  Ce test vérifie que l'insertion d'une propriété permettant
    ...  de forcer la casse d'une chaîne de caractère en minuscule ou en majuscule
    ...  est rendue correctement dans l'édition PDF.

    #
    Depuis la page d'accueil  admin  admin
    # Identifiant de la lettre type utilisée dans ce cas d'utilisation
    ${id}  Set Variable  test_editions_maj_min
    # On ajoute une lettre type qui contient une chaine dont on doit forcer les
    # majuscules et une autre dont on doit forcer les minuscules
    # XXX Attention l'ajout se fait avec des balises HTML qui ne sont pas
    #     interprétées lors de la première validation du formulaire, elles le
    #     seront lors de la prochaine validation du formulaire.
    # XXX Il faut trouver une solution pour permettre l'interprétation des
    #     balises HTML par TinyMCE grâce à des mots clés RF.
    Ajouter la lettre-type depuis le menu  ${id}  ${id}  <p>${id}</p>  <p><span style="font-weight: bold;">Test Maj Min</span></p><p><span class="mce_maj">MaJuScUlE</span></p><p><span class="mce_min">MiNuScUlE</span></p>  ${noquery}  true
    # XXX Cette modification permet également d'interpréter les balises html
    #     non interprétées à l'ajout.
    Modifier la lettre-type  ${id}
    # On ouvre la prévisualisation du fichier PDF
    Depuis le contexte de la lettre-type  ${id}
    Click On Form Portlet Action  om_lettretype  previsualiser
    Open PDF  ${OM_PDF_TITLE}
    # On vérifie que le titre du sous état et que les titres des colonnes
    # sont bien présents dans le PDF
    PDF Page Number Should Contain  1  MAJUSCULE
    PDF Page Number Should Contain  1  minuscule
    # On ferme le fichier
    Close PDF

