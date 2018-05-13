-- Deletion of the old triggers
--DROP TRIGGER IF EXISTS name ON table_name;

-- Triggers functions
-- [[ General functions ]] --
------------------------------------------------------------
-- nothing()
------------------------------------------------------------
-- Une fonction ne faisant rien (pour les triggers)
------------------------------------------------------------

CREATE OR REPLACE FUNCTION nothing()
RETURNS trigger AS $body$
BEGIN
	-- On ne retourne rien
	RETURN null;
END;
$body$
LANGUAGE plpgsql;

------------------------------------------------------------
-- nothing()
------------------------------------------------------------
-- Une fonction ne faisant rien (pour les triggers, notamment sur les vues)
------------------------------------------------------------

CREATE OR REPLACE FUNCTION read_only()
RETURNS trigger AS $body$
BEGIN
	-- On lève une exception ce qui interromps la modification
	RAISE EXCEPTION 'La table %.% est en lecture seule. Aucune modification n''est autorisée.', TG_TABLE_SCHEMA,TG_TABLE_NAME;
	-- On ne retourne rien
	RETURN null;
END;
$body$
LANGUAGE plpgsql;



-- Creation of the new triggers
--CREATE TRIGGER name BEFORE | AFTER event OR ...
--ON table_name
--FOR EACH ROW
--WHEN
--EXECUTE PROCEDURE function_name(args)
--;

--format: trig_fct_[nomTable]_[Quand(BEFORE, AFTER, INSTEAD OF)][Action[Action+]][_description[_description+]]

------------------------------------------------------------
-- equipe_jugeCompetition
------------------------------------------------------------

-- [[ INSERT ]] --
-- A l'insertion, il faut que la valeur de la note soit à NULL
CREATE OR REPLACE FUNCTION trig_fct_equipe_jugeCompetition_afterInsert_noteJuryCompetition()
RETURNS trigger AS $body$
BEGIN
	SELECT checkValue_different(NEW.note, NULL, format('La valeur de la note doit être NULL (%s)', NEW.note));
END;
$body$
LANGUAGE PLPGSQL;

DROP TRIGGER IF EXISTS trig_equipe_jugeCompetition_afterInsert_noteJuryCompetition ON equipe_jugeCompetition;
CREATE TRIGGER trig_equipe_jugeCompetition_afterInsert_noteJuryCompetition
AFTER INSERT
ON equipe_jugeCompetition
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_jugeCompetition_afterInsert_noteJuryCompetition();

-- [[ INSERT | UPDATE ]] --
-- Les juges doivent être de la même compétition
CREATE OR REPLACE FUNCTION trig_fct_equipe_jugeCompetition_afterInsertUpdate_juges()
RETURNS trigger AS $body$
DECLARE
	competition_id		INTEGER;
	competition_juges	INTEGER[5];
BEGIN
	-- Récupération de l'id de la compétition
	SELECT
		id_competition INTO competition_id
	FROM
		equipe
	WHERE
		equipe.id = NEW.id_equipe
	;

	-- Récupération des id des juges de la compétition
	SELECT
		ARRAY_AGG(jugeCompetition.id) INTO competition_juges
	FROM
		jugeCompetition
	INNER JOIN typeJuge
		ON typejuge.id = jugeCompetition.id_typeJuge
	WHERE
		jugeCompetition.id_competition = competition_id
	AND	typeJuge = 'Juge'
	;

	-- On vérifie que le juge qu'on ajoute est lié à la compétition
	IF NEW.id_jugeCompetition = ANY(competition_juges) THEN
		-- Le juge est bien associé à la compétition
		RETURN NEW;
	ELSE
		RAISE EXCEPTION 'Le juge n''appartient pas à la compétition (idJuge: %; idCompetition: %). Abandon', NEW.id_jugeCompetition, competition_id;
		RETURN NULL;
	END IF;

END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_jugeCompetition_afterInsertUpdate_juges ON equipe_jugeCompetition;
CREATE TRIGGER trig_equipe_jugeCompetition_afterInsertUpdate_juges
AFTER INSERT OR UPDATE
ON equipe_jugeCompetition
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_jugeCompetition_afterInsertUpdate_juges();


------------------------------------------------------------
-- utilisateur
------------------------------------------------------------

-- [[ INSERT | UPDATE ]] --
-- Quand on crée un utilisateur, il faut crypter son mot de passe
CREATE OR REPLACE FUNCTION trig_fct_utilisateur_beforeInsertUpdate_modifMDP()
RETURNS trigger AS $body$
BEGIN
	NEW.mdp = crypt(NEW.mdp, gen_salt('bf', 8));

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql
;

DROP TRIGGER IF EXISTS trig_utilisateur_beforeInsertUpdate_modifMDP ON utilisateur;
CREATE TRIGGER trig_utilisateur_beforeInsertUpdate_modifMDP
BEFORE INSERT OR UPDATE
ON utilisateur
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_utilisateur_beforeInsertUpdate_modifMDP();


------------------------------------------------------------
-- equipe
------------------------------------------------------------

-- [[ INSERT ]] --
-- Création d'une équipe:
---- Il faut que l'heure de début soit à NULL
---- Il faut qu'il ne soit pas visionnable
---- Il faut que la pénalité soit à NULL

CREATE OR REPLACE FUNCTION trig_fct_equipe_afterInsert_newEquipe()
RETURNS trigger AS $body$
DECLARE
	competitionDate	DATE;
	nRes		INTEGER;
BEGIN
	-- On récupère la date de la compétition
	SELECT
		competition.date INTO competitionDate
	FROM
		competition
	WHERE
		competition.id = NEW.id_competition
	;

	-- Valudation des valeurs par défaut
	SELECT checkValue_different(NEW.debut, NULL, 'La valeur de debut n''est pas NULL. Abandon');
	SELECT checkValue_different(NEW.visionnable, FALSE, 'La valeur de visionnable n''est pas à false. Abandon');
	SELECT checkValue_different(NEW.penalite, NULL, 'La valeur de la pénalité n''est pas NULL. Abandon');

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql
;

DROP TRIGGER IF EXISTS trig_equipe_afterInsert_newEquipe ON equipe;
CREATE TRIGGER trig_equipe_afterInsert_newEquipe 
AFTER INSERT
ON equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_afterInsert_newEquipe();

-- [[ UPDATE ]] --
-- Quand on indique l'heure de début d'un ballet, il faut qu'il y ait le nombre suffisant de juges effectés (==5 + 1) et de la même compétition
CREATE OR REPLACE FUNCTION trig_fct_equipe_afterUpdate_debutBallet()
RETURNS trigger AS $body$
DECLARE
	nJuge		INTEGER;
	nJugeArbitre	INTEGER;
BEGIN

-- On vérifie qu'il y ait les mêmes juges associés que pour la compétition
---- ==> jugeCompetition
	IF getJugesCompetition(NEW.id_competition) <> getJugesEquipe(NEW.id) THEN
		RAISE EXCEPTION 'Les juges de la compétition ne sont pas les mêmes que les juges de l''équipe (% <> %)', getJugesCompetition(NEW.id_competition), getJugesEquipe(NEW.id);
		RETURN NULL;
	ELSE
-- On vérifie qu'il y ait bien 6 juges associés
		SELECT
			COUNT(jugeCompetition.id) INTO nJuge
		FROM
			jugeCompetition
		INNER JOIN typeJuge
			ON typeJuge.id = jugeCompetition
		WHERE
			jugeCompetition.id_competition = NEW.id_competition
		AND	typeJuge.nom = 'Juge'
		GROUP BY
				jugeCompetition.id_competition
		;
		--
		SELECT
			COUNT(jugeCompetition.id) INTO nJugeArbitre
		FROM
			jugeCompetition
		INNER JOIN typeJuge
			ON typeJuge.id = jugeCompetition
		WHERE
			jugeCompetition.id_competition = NEW.id_competition
		AND	typeJuge.nom = 'Juge-arbitre'
		GROUP BY
				jugeCompetition.id_competition
		;


		IF nJuge <> 5 AND nJugeArbitre <> 1 THEN
			RAISE EXCEPTION 'Le nombre de juges est incorrect (nJuge: %; nJugeArbitre: %)', nJuge, nJugeArbitre;
			RETURN NULL;
		ELSE
			return NEW;
		END IF;
	END IF;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_afterUpdate_debutBallet ON equipe;
CREATE TRIGGER trig_equipe_afterUpdate_debutBallet
AFTER UPDATE
ON equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_afterUpdate_debutBallet();

-- Quand on marque le ballet comme visionnable, il faut que toutes les notes des juges soient décidées et qu'il ait eu une heure de début
CREATE OR REPLACE FUNCTION trig_fct_equipe_afterUpdate_visionnable()
RETURNS trigger AS $body$
DECLARE
	nAvecNotes	INTEGER;
BEGIN
	SELECT
		COUNT(equipe_jugeCompetition.id_juge) INTO nAvecNotes
	FROM
		equipe_jugeCompetition
	WHERE
		equipe_jugeCompetition.id_equipe = NEW.id
	AND	equipe_jugeCompetition.note IS NOT NULL
	GROUP BY
		equipe_jugeCompetition.id_equipe
	;

	SELECT checkValue_different(nAvecNotes, 5, format('Tous les juges n''ont pas donné leur note (%s)', nAvecNotes));
	RETURN NEW;
--	IF nAvecNotes <> 5 THEN
--		RAISE EXCEPTION 'Tous les juges n''ont pas donné leur note (%)', nAvecNotes;
--		RETURN NULL;
--	ELSE
--		RETURN NEW;
--	END IF;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_afterUpdate_visionnable ON equipe;
CREATE TRIGGER trig_equipe_afterUpdate_visionnable
AFTER UPDATE
ON equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_afterUpdate_visionnable();


-- Quand on modifie la pénalité, il faut qu'elle soit absolument > 0 et < MAX
CREATE OR REPLACE FUNCTION trig_fct_equipe_afterUpdate_penalite()
RETURNS trigger AS $body$
BEGIN
	-- < 0 OR > 4*0.5 = 2)
	IF NEW.penalite < 0 OR NEW.penalite > 4 THEN
		RAISE EXCEPTION 'La valeur de la pénalité est incorrecte (% => %)', NEW.penalite, (NEW.penalite * 0.5);
	ELSE
		RETURN NEW;
	END IF;

END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_afterUpdate_penalite ON equipe;
CREATE TRIGGER trig_equipe_afterUpdate_penalite
AFTER UPDATE
ON equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_afterUpdate_penalite();

------------------------------------------------------------
-- equipe_personne
------------------------------------------------------------

-- [[ INSERT | UPDATE ]] --
-- Quand on ajoute un joueur, on vérifie qu'il soit bien valide dans le temps donné
CREATE OR REPLACE FUNCTION trig_fct_equipe_personne_afterInsertUpdate_personneInscription()
RETURNS trigger AS $body$
DECLARE
	date_competition	DATE;
	_personne		personne%ROWTYPE;
BEGIN
	SELECT
		competition.dateCompetition INTO date_competition
	FROM equipe
	INNER JOIN competition
		ON competition.id = equipe.id_competition
	WHERE
		equipe.id = NEW.id_equipe
	;

	SELECT * INTO _personne
	FROM personne
	WHERE
		personne.id = NEW.id_personne
	;

	IF date_competition > _personne.dateInscription THEN
		IF _personne.dateFinInscription IS NOT NULL THEN
			
		ELSE
			-- La personne est toujours inscrite
			RETURN NEW;
		END IF;
	ELSE
		RAISE EXCEPTION 'La date de début de la compétition est située avant l''inscription de la personne (date_competition: %s; date_inscription: %s). ERREUR', date_competition, _personne.dateInscription;
		RETURN NULL;
	END IF;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_personne_afterInsertUpdate_personneInscription ON equipe_personne;
CREATE TRIGGER trig_equipe_personne_afterInsertUpdate_personneInscription
AFTER INSERT OR UPDATE
ON equipe_personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_personne_afterInsertUpdate_personneInscription();


-- Quand on ajoute un nageur, on vérifie qu'il fasse partie du même club que les autres
CREATE OR REPLACE FUNCTION trig_fct_equipe_personne_afterInsertUpdate_personneEquipe()
RETURNS trigger AS $body$
DECLARE
	idClub	INTEGER;
	nClub	INTEGER;
BEGIN
	-- On vérifie qu'il fasse partie d'un club
	SELECT
		personne.id_club INTO idClub
	FROM personne
	WHERE
		personne.id = NEW.id_personne
	;

	SELECT checkValue_different(idClub, NULL, format('La personne ne fait pas partie d''un club (%s)', idClub));


	SELECT
		COUNT(club.id) INTO nClub
	FROM equipe_personne
	INNER JOIN personne
		ON personne.id = equipe_personne.id_personne
	INNER JOIN club
		ON club.id = personne.id_club
	WHERE equipe_personne.id_equipe = NEW.id_equipe
	GROUP BY club.id
	;

	SELECT checkValue_different(nClub, 1, format('Les joueurs font partie de plusieurs clubs (%s)', nClub));
	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_personne_afterInsertUpdate_personneEquipe ON equipe_personne;
CREATE TRIGGER trig_equipe_personne_afterInsertUpdate_personneEquipe
AFTER INSERT OR UPDATE
ON equipe_personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_personne_afterInsertUpdate_personneEquipe();


------------------------------------------------------------
-- personne
------------------------------------------------------------

-- [[ INSERT | UPDATE ]] --
-- On met le nom de la personne en MAJUSCULES et son prénom en Initcap
CREATE OR REPLACE FUNCTION trig_fct_personne_beforeInsertUpdate_nomPrenom()
RETURNS trigger AS $body$
BEGIN
	NEW.nom = UPPER(NEW.nom);
	NEW.prenom = INITCAP(NEW.prenom);

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_personne_beforeInsertUpdate_nomPrenom ON personne;
CREATE TRIGGER trig_personne_beforeInsertUpdate_nomPrenom
BEFORE INSERT OR UPDATE
ON personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_personne_beforeInsertUpdate_nomPrenom();

-- On vérifie que la personne n'est pas déjà inscrite dans un club avec les périodes données
CREATE OR REPLACE FUNCTION trig_fct_personne_afterInsertUpdate_inscription()
RETURNS trigger AS $body$
DECLARE
	nRes	INTEGER;
BEGIN
	SELECT
		COUNT(personne.id) INTO nRes
	FROM personne
	WHERE
		personne.nom = NEW.nom
	AND	personne.prenom = NEW.prenom
	AND	(
			personne.dateInscription > NEW.dateInscription
		OR	pesronne.dateFinInscription > NEW.dateInscription
		)
	;

	SELECT checkValue_different(nRes, 0, format('Une personne est déjà inscrite pour cette période'));
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_personne_afterInsertUpdate_inscription ON personne;
CREATE TRIGGER trig_personne_afterInsertUpdate_inscription
BEFORE INSERT OR UPDATE
ON personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_personne_afterInsertUpdate_inscription();


-- [[ Vues ]] --

-- On ajoute des triggers en insertion / modification / suppression des éléments -> read_only()
-- Cela lance une erreur -> impossibilité de modifier la vue
DROP TRIGGER IF EXISTS trig_juge_competition_modif on juge_competition;
CREATE TRIGGER trig_juge_competition_modif
INSTEAD OF INSERT OR UPDATE OR DELETE
ON juge_competition
FOR EACH ROW
EXECUTE PROCEDURE read_only();

DROP TRIGGER IF EXISTS trig_juge_competition_agg_modif on juge_competition_agg;
CREATE TRIGGER trig_juge_competition_agg_modif
INSTEAD OF INSERT OR UPDATE OR DELETE
ON juge_competition_agg
FOR EACH ROW
EXECUTE PROCEDURE read_only();

DROP TRIGGER IF EXISTS trig_all_equipe_modif on all_equipe;
CREATE TRIGGER trig_all_equipe_modif
INSTEAD OF INSERT OR UPDATE OR DELETE
ON all_equipe
FOR EACH ROW
EXECUTE PROCEDURE read_only();

DROP TRIGGER IF EXISTS trig_all_equipe_agg_modif on all_equipe_agg;
CREATE TRIGGER trig_all_equipe_agg_modif
INSTEAD OF INSERT OR UPDATE OR DELETE
ON all_equipe_agg
FOR EACH ROW
EXECUTE PROCEDURE read_only();

DROP TRIGGER IF EXISTS trig_all_nageur_club_modif on all_nageur_club;
CREATE TRIGGER trig_all_nageur_club_modif
INSTEAD OF INSERT OR UPDATE OR DELETE
ON all_nageur_club
FOR EACH ROW
EXECUTE PROCEDURE read_only();

DROP TRIGGER IF EXISTS trig_all_personne_modif on all_personne;
CREATE TRIGGER trig_all_personne_modif
INSTEAD OF INSERT OR UPDATE OR DELETE
ON all_personne
FOR EACH ROW
EXECUTE PROCEDURE read_only();

DROP TRIGGER IF EXISTS trig_all_juge_competition_notes_modif on all_juge_competition_notes;
CREATE TRIGGER trig_all_juge_competition_notes_modif
INSTEAD OF INSERT OR UPDATE OR DELETE
ON all_juge_competition_notes
FOR EACH ROW
EXECUTE PROCEDURE read_only();
