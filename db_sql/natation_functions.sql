-- Vérifie que la valeur est la même et renvoie une exception si cela est faux
-- Utile pour vérifier une valeur par défaut lors d'une insertion
CREATE OR REPLACE FUNCTION checkValue_different(toCheck ANYELEMENT, comparison ANYELEMENT, onExceptionText VARCHAR)
RETURNS boolean AS $body$
BEGIN
	IF toCheck <> comparison
	THEN
		RAISE EXCEPTION '%', onExceptionText;
		RETURN FALSE;
	ELSE
		RETURN TRUE;
	END IF;
END;
$body$
LANGUAGE plpgsql;

-- Fonction de connexion
CREATE OR REPLACE FUNCTION login(adresseMail VARCHAR, motDePasse VARCHAR)
RETURNS boolean AS $body$
DECLARE
	usrPwd	VARCHAR := NULL;
BEGIN
	SELECT
		utilisateur.mdp
	INTO
		usrPwd
	FROM utilisateur
--	INNER JOIN personne
--		ON personne.id = utilisateur.id_personne
	WHERE
		utilisateur.mail = adresseMail
	;

	IF usrPwd IS NULL
	THEN
		RAISE EXCEPTION 'L''utilisateur demandé n''existe pas (%)', adresseMail;
		RETURN FALSE;
	ELSE
		IF (SELECT usrPwd = crypt(motDePasse, usrPwd)) IS TRUE
		THEN
			-- L'utilisateur existe
			RETURN TRUE;
		ELSE
			-- Le mot de passe est incorrect
			RAISE EXCEPTION 'Le mot de passe est incorrect pour l''utilisateur "%"', adresseMail;
			RETURN FALSE;
		END IF;
	END IF;
END;
$body$
LANGUAGE plpgsql;

-- Obtention du club pour une équipe
CREATE OR REPLACE FUNCTION getEquipeClub(idEquipe INTEGER)
RETURNS integer AS $body$
DECLARE
	nRes	INTEGER;
	idClub	INTEGER;
BEGIN
	SELECT
		personne.id_club
	INTO
		idClub
	FROM
		equipe_personne
	INNER JOIN personne
		ON personne.id = equipe_personne.id_personne
	WHERE
		equipe_personne.id_equipe = idEquipe
	GROUP BY
		personne.id_club
	;

	GET DIAGNOSTICS nRes = ROW_COUNT;

	IF nRes > 1 THEN
		RAISE EXCEPTION 'Trop de clubs liés à cette équipe (%). Abandon', nRes;
		RETURN 0;
	ELSE
		RETURN idClub;
	END IF;
END;
$body$
LANGUAGE plpgsql;

-- Récupération des juges pour la compétition donnée (juges simples, seulement ceux donnant des notes)
CREATE OR REPLACE FUNCTION getJugesCompetition(idCompetition INTEGER)
RETURNS integer[] AS $body$
DECLARE
	toReturn	INTEGER[5];
	nRes		INTEGER;
BEGIN
	WITH RES AS (
		SELECT
			jugeCompetition.id AS jugeCompetition_id
		FROM
			jugeCompetition
		INNER JOIN typeJuge
			ON typeJuge.id = jugeCompetition.id_typejuge
		WHERE
			jugeCompetition.id_competition = idCompetition
		AND	typeJuge.nom = 'Juge'
		GROUP BY
			jugeCompetition.id
		ORDER BY
			jugeCompetition.id
	)
	SELECT
		ARRAY_AGG(jugeCompetition_id)
	INTO
		toReturn
	FROM RES
	;

	GET DIAGNOSTICS nRes = ROW_COUNT;

	--IF nRes <> 5
	IF array_length(toReturn, 1) <> 5
	THEN
		RAISE EXCEPTION 'Le nombre de juges pour la compétition est incorrect (idCompetition: %; nRes: %; res: %). Erreur', idCompetition, nRes, array_to_string(toReturn, ',');
		RETURN NULL;
	ELSE
		RETURN toReturn;
	END IF;
END;
$body$
LANGUAGE plpgsql;

-- Obtention des id des juges pour une équipe
CREATE OR REPLACE FUNCTION getJugesEquipe(idEquipe INTEGER)
RETURNS integer[] AS $body$
DECLARE
	toReturn	INTEGER[5];
BEGIN
	/*
	SELECT
		equipe_jugeCompetition.id_jugeCompetition
	INTO
		toReturn
	FROM
		equipe_jugeCompetition
	WHERE
		equipe_jugeCompetition.id_equipe = idEquipe
	ORDER BY
		equipe_jugeCompetition.id_jugeCompetition
	;*/

	WITH RES AS (
		SELECT
			jugeCompetition.id AS jugeCompetition_id
		FROM
			jugeCompetition
		INNER JOIN typeJuge
			ON typeJuge.id = jugeCompetition.id_typeJuge
		WHERE
			jugeCompetition.id_competition = idEquipe
		AND	typeJuge.nom = 'Juge'
		GROUP BY
			jugeCompetition.id
		ORDER BY
			jugeCompetition.id
	)
	SELECT
		ARRAY_AGG(jugeCompetition_id)
	INTO
		toReturn
	FROM RES
	;

	IF array_length(toReturn, 1) <> 5
	THEN
		RAISE EXCEPTION 'Le nombre de juges pour la compétition est incorrect (toReturn: %, nRes: %). Erreur', array_to_string(toReturn, ','), nRes;
		RETURN NULL;
	ELSE
		RETURN toReturn;
	END IF;
END;
$body$
LANGUAGE plpgsql;
