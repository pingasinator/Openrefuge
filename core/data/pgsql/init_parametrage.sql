--------------------------------------------------------------------------------
-- INIT PARAMETRAGE
--
-- Paramétrage minimum pour pouvoir se connecter et accéder aux différentes
-- fonctions du framework :
--  - 1 collectivité mono LIBREVILLE
--  - 1 utilisateur admin mot de passe admin
--  - 1 profil ADMINISTRATEUR qui possède toutes les permissions
--
-- @package framework_openmairie
-- @version SVN : $Id: init_parametrage.sql 4310 2018-06-07 11:18:57Z fmichon $
--------------------------------------------------------------------------------

--
INSERT INTO om_collectivite (om_collectivite, libelle, niveau) VALUES 
(nextval('om_collectivite_seq'), 'LIBREVILLE', '1');

--
INSERT INTO om_parametre (om_parametre, libelle, valeur, om_collectivite) VALUES 
(nextval('om_parametre_seq'), 'ville', 'LIBREVILLE', (SELECT om_collectivite FROM om_collectivite WHERE libelle = 'LIBREVILLE'));

--
INSERT INTO om_profil (om_profil, libelle, hierarchie) VALUES
(nextval('om_profil_seq'), 'ADMINISTRATEUR', 0),
(nextval('om_profil_seq'), 'USER', 5);
--
INSERT INTO om_droit (om_droit, libelle, om_profil) 
SELECT nextval('om_droit_seq'), libelle, (SELECT om_profil FROM om_profil WHERE libelle = 'ADMINISTRATEUR') FROM om_permission;

--
INSERT INTO om_utilisateur (om_utilisateur, nom, email, login, pwd, om_collectivite, om_type, om_profil) VALUES 
(nextval('om_utilisateur_seq'), 'Administrateur', 'nospam@openmairie.org', 'admin', '21232f297a57a5a743894a0e4a801fc3', (SELECT om_collectivite FROM om_collectivite WHERE libelle = 'LIBREVILLE'), 'DB', (SELECT om_profil FROM om_profil WHERE libelle = 'ADMINISTRATEUR'));

