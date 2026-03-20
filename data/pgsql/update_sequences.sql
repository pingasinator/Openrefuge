--------------------------------------------------------------------------------
-- Mise à jour des séquences avec le max + 1
--
-- Ce fichier permet de créer une fonction capable de mettre à jour toutes les
-- séquences correctement liées aux champs auxquels elles se rattachent en
-- fonction de la dernière valeur du champ dans la table. En plus de la création
-- de la fonction ce script exécute la fonction.
--
-- @package framework_openmairie
-- @version SVN : $Id: update_sequences.sql 4348 2018-07-20 16:49:26Z softime $
--------------------------------------------------------------------------------

--
CREATE OR REPLACE FUNCTION fn_fixsequences(schema TEXT) RETURNS integer AS
$BODY$
DECLARE
themax BIGINT;
mytables RECORD;
num integer;
BEGIN
 num := 0;
 FOR mytables IN
    SELECT  S.relname as seq, C.attname as attname, T.relname as relname
    FROM pg_class AS S, pg_depend AS D, pg_class AS T, pg_attribute AS C, information_schema.tables it
    WHERE S.relkind = 'S'
        AND S.oid = D.objid
        AND D.refobjid = T.oid
        AND D.refobjid = C.attrelid
        AND D.refobjsubid = C.attnum
        AND table_name=T.relname
        AND it.table_type <> 'VIEW'
        AND table_schema = schema
 LOOP
      EXECUTE 'SELECT MAX('||mytables.attname||') FROM '||schema||'.'||mytables.relname||';' INTO themax;
      IF (themax is null OR themax < 0) THEN
       themax := 0;
      END IF;
      themax := themax +1;
      EXECUTE 'ALTER SEQUENCE ' ||schema||'.'|| mytables.seq || ' RESTART WITH '||themax;
      num := num + 1;
  END LOOP;
  
  RETURN num;
  
END;
$BODY$
LANGUAGE 'plpgsql' VOLATILE;

--
select fn_fixsequences(:'schema');

--
DROP FUNCTION IF EXISTS fn_fixsequences(text);

