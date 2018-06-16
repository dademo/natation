-- Script SQL cours BDD 12-06-2018 (ORT Lyon)

CREATE OR REPLACE FUNCTION trig_fct_all_equipe_insteadOfInsertUpdate_equipe()
RETURNS trigger AS $body$
DECLARE
	id_equipe	INTEGER;
BEGIN
	-- INSERT
	IF equipe_id IS NULL THEN

	INSERT INTO equipe VALUES
	(
		-- id
		DEFAULT,
		-- id_competition
		(SELECT competition.id FROM competition WHERE competition.titre = NEW.competition_titre),
		-- nom
		NEW.equipe_nom,
		-- ordrePassage
		NEW.equipe_ordrePassage,
		-- debut
		NULL,
		-- visionnable
		NULL,
		-- penalite
		0
	) RETURNING id INTO id_equipe;

	RAISE NOTICE 'Equipe % insérée', id_equipe;

	-- UPDATE
	ELSE
	
	END IF;
END;
$body$
LANGUAGE plpgsql;
