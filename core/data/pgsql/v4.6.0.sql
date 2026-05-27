-------------------------------------------------------------------------------
-- Script de mise à jour vers la version v4.6.0 depuis la version v4.5.1
--
-- @package framework_openmairie
-- @version SVN : $Id$
-------------------------------------------------------------------------------

--
-- Ajout d'un nouveau champ dans widget pour sélectionner uniquement les scripts
-- qui se situe dans app/
--
ALTER TABLE om_widget
    ADD script character varying(80) NOT NULL DEFAULT '';
COMMENT ON COLUMN "om_widget"."script" IS 'Fichier utilisé par le widget';

--
-- Ajout d'un nouveau champ text dans widget spésialisé pour script
--
ALTER TABLE om_widget
ADD arguments text NOT NULL DEFAULT '';
COMMENT ON COLUMN "om_widget"."arguments" IS 'Arguments affiché dans le widget ';

--
-- Déplacement des informations contenus dans lien et texte dans script et 
-- arguments quand le type de widget est file 
--
UPDATE om_widget SET
script = lien,
arguments = texte,
lien = '',
texte = ''
WHERE type = 'file';
