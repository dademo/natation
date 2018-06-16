-- Exo SQL du 12-06-2018

CREATE OR REPLACE TRIGGER trig_fct_all_equipe_insteadOfInsertUpdate_equipe()
RETURNS trigger AS $body$
DECLARE
	id_competition	INTEGER;
	id_equipe	INTEGER;
BEGIN

	-- Recup competition
	SELECT competition.id INTO id_competition FROM competition WHERE competition.titre = NEW.competition_titre AND competition.dateCompetition = NEW.competition_dateCompetition;

	-- Si aucun résultat
	IF id_competition IS NULL THEN
		INSERT INTO competition VALUES (
			-- id
			DEFAULT,
			-- id_lieu
			NULL,
			-- titre
			NEW.competition_titre,
			-- dateCompetition
			NEW.dateCompetition
		) RETURNING id INTO id_competition;
	END IF;

	SELECT equipe.id INTO id_equipe FROM equipe WHERE equipe.nom = NEW.equipe_nomEquipe;

	-- INSERT
	IF NEW.equipe_id IS NULL OR id_equipe IS NULL THEN

		INSERT INTO equipe VALUES (
			-- id
			equipe_id,
			-- id_competition
			id_competition
			-- nom
			NEW.equipe_nomEquipe,
			-- ordrePassage
			NEW.equipe_ordrePassage,
			-- debut
			NULL,
			-- visionnable
			FALSE,
			-- penalite
			NULL
		) RETURNING id INTO id_equipe;

		RAISE NOTICE 'Insertion de l''équipe % réussie', id_equipe;

		UPDATE equipe_personne
		SET personne.id_equipe = id_equipe
		WHERE personne.id_equipe = OLD.equipe_id;

	-- UPDATE
	ELSE

		UPDATE equipe
		SET
			equipe.id_competition = id_competition,
			equipe.nom = NEW.equipe_nom
			equipe.ordrePassage = NEW.equipe_ordrePassage
		WHERE equipe.id = id_equipe
		RETURNING id INTO id_equipe;

		RAISE NOTICE 'Mise à jour de l''équipe % réussie', id_equipe;

	END IF;

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;


CREATE TRIGGER trig_all_equipe_insteadOfInsertUpdate_equipe
INSTEAD OF INSERT OR UPDATE
ON all_equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_all_equipe_insteadOfInsertUpdate_equipe();


CREATE OR REPLACE TRIGGER trig_fct_all_equipe_insteadOfInsertUpdate_competition()
RETURNS trigger AS $body$
DECLARE
	id_competition	INTEGER;
BEGIN

	-- INSERT
	IF NEW.competition_id IS NULL THEN

		INSERT INTO competition VALUES (
			-- id
			DEFAULT,
			-- id_lieu
			NULL,
			-- titre
			NEW.competition_titre,
			-- dateCompetition
			NEW.dateCompetition
		) RETURNING id INTO id_competition;

		RAISE NOTICE 'Insertion de la compétition % réussie', id_competition;

	-- UPDATE
	ELSE

		UPDATE competition
		SET
			titre = NEW.competition_titre
			dateCompetition = NEW.competition_dateCompetition
		WHERE competition.id = NEW.competition_id
		RETURNING id INTO id_competition;

		RAISE NOTICE 'Mise à jour de la compétition % réussie', id_competition;

	END IF;

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;


CREATE TRIGGER trig_all_equipe_insteadOfInsertUpdate_competition
INSTEAD OF INSERT OR UPDATE
ON all_equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_all_equipe_insteadOfInsertUpdate_competition();


CREATE OR REPLACE TRIGGER trig_fct_all_equipe_insteadOfInsertUpdate_personne()
RETURNS trigger AS $body$
DECLARE
	id_equipe	INTEGER;
	id_personne	INTEGER;
BEGIN

	-- INSERT
	IF NEW.equipe_membre_id IS NULL THEN
		INSERT INTO personne VALUES (
			-- id
			DEFAULT,
			-- nom
			NEW.equipe_membre_nom,
			-- prenom
			NEW.equipe_membre_prenom,
			-- dateNaissance
			NEW.equipe_membre_dateNaissance
		) RETURNING id INTO id_personne;

		RAISE NOTICE 'Insertion de la personne % réussie', id_personne;

	-- UPDATE
	ELSE

		UPDATE peronne
		SET
			nom = NEW.equipe_membre_nom
			ordrePassage = NEW.equipe_ordrePassage
		WHERE personne.id = NEW.equipe_membre_id
		RETURNING id INTO id_equipe;

		RAISE NOTICE 'Mise à jour de la personne % réussie', id_personne;

	END IF;

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;


CREATE TRIGGER trig_all_equipe_insteadOfInsertUpdate_personne
INSTEAD OF INSERT OR UPDATE
ON all_equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_all_equipe_insteadOfInsertUpdate_personne();


CREATE OR REPLACE TRIGGER trig_fct_all_equipe_insteadOfInsertUpdate_club()
RETURNS trigger AS $body$
DECLARE
	id_club	INTEGER;
BEGIN
	-- INSERT
	IF NEW.club_id IS NULL THEN

		RAISE EXCEPTION 'club_id IS NULL';

	-- UPDATE
	ELSE

		UPDATE club
		SET
			nom = NEW.club_nom
		WHERE club.id = NEW.club_id
		RETURNING id INTO id_club;

		RAISE NOTICE 'Mise à jour du club % réussie', id_personne;
	END IF;

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;


CREATE TRIGGER trig_all_equipe_insteadOfInsertUpdate_club
INSTEAD OF INSERT OR UPDATE
ON all_equipe
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_all_equipe_insteadOfInsertUpdate_club();


CREATE OR REPLACE TRIGGER trig_fct_all_equipe_insteadOfDelete_remNageuseCompetition()
RETURNS trigger AS $BODY$
BEGIN
	DELETE FROM equipe_personne
END;
$BODY$
LANGUAGE plpgsql;



CREATE OR REPLACE FUNCTION getRandDateBewteen(beginDate DATE, endDate Date)
RETURNS DATE AS $BODY$
DECLARE
	difference	INTEGER;
	ran_val		INTEGER;
BEGIN


	difference = endDate - beginDate;

	IF difference < 1 THEN
		RAISE EXCEPTION 'Les valeurs données ne sont pas correctes (dateDebut: %, dateFin: %)', TO_CHAR(beginDate, 'DD-MM-YYYY'), TO_CHAR(endDate, 'DD-MM-YYYY'); 
	END IF;

	-- Nb de jours à ajouter à la date de début
	ran_val := random() * difference;

	RETURN (beginDate + ran_val);
END;
$BODY$
LANGUAGE plpgsql;
