*** Settings ***
Documentation     Surcharges des ressources du framework (librairies, ressources, variables et keywords).
Library  openmairie.robotframework.Library

*** Variables ***
${SERVER}            localhost
${PROJECT_NAME}      framework-openmairie
${BROWSER}           firefox
${DELAY}             0
${RESOURCES}         resources
${ADMIN_USER}        admin
${ADMIN_PASSWORD}    admin
${PROJECT_URL}       http://${SERVER}/${PROJECT_NAME}/
${PATH_BIN_FILES}    ${EXECDIR}${/}binary_files${/}
${TITLE}             :: openMairie :: Framework
${SESSION_COOKIE}    1bb484de79f96a7d0b00ff463c18fcbf

*** Keywords ***
For Suite Setup
    Reload Library  openmairie.robotframework.Library
    # Les keywords définit dans le resources.robot sont prioritaires
    Set Library Search Order    resources
    Ouvrir le navigateur
    Tests Setup

For Suite Teardown
    Fermer le navigateur
