*** Settings ***
Resource  resources/resources.robot
Suite Setup  For Suite Setup
Suite Teardown  For Suite Teardown
Documentation  TestSuite "Login"...


*** Test Cases ***
Valid Login
    # Les méthodes Suite Setup et Suite Teardown gèrent l'ouverture et la
    # fermeture du navigateur. Dans le cas de ce TestSuite on a besoin de
    # travailler sur un navigateur fraichement ouvert pour être sûr que la
    # variable de session est neuve.
    Fermer le navigateur
    Ouvrir le navigateur
    #
    Depuis la page de login
    Input Username    admin
    Input Password    admin
    Click Button    login.action.connect
    Wait Until Element Is Visible    css=#actions a.actions-logout
    Element Should Contain    css=#actions a.actions-logout    Déconnexion
    Valid Message Should Be    Votre session est maintenant ouverte.


Unvalid Login
    # Les méthodes Suite Setup et Suite Teardown gèrent l'ouverture et la
    # fermeture du navigateur. Dans le cas de ce TestSuite on a besoin de
    # travailler sur un navigateur fraichement ouvert pour être sûr que la
    # variable de session est neuve.
    Fermer le navigateur
    Ouvrir le navigateur
    #
    Depuis la page de login
    Input Username    admin
    Input Password    plop
    Click Button    login.action.connect
    Wait Until Keyword Succeeds    ${TIMEOUT}    ${RETRY_INTERVAL}    Error Message Should Be    Votre identifiant ou votre mot de passe est incorrect.


Logout
    # Les méthodes Suite Setup et Suite Teardown gèrent l'ouverture et la
    # fermeture du navigateur. Dans le cas de ce TestSuite on a besoin de
    # travailler sur un navigateur fraichement ouvert pour être sûr que la
    # variable de session est neuve.
    Fermer le navigateur
    Ouvrir le navigateur
    #
    S'authentifier    admin    admin
    Se déconnecter

