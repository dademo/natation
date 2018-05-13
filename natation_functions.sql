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
		utilisateur.mdp INTO usrPwd
	FROM utilisateur
	INNER JOIN personne
		-- La personne est toujours inscrite
		ON personne.dateFinInscription IS NULL
			OR personne.dateFinInscription >= CURRENT_DATE
	WHERE
		utilisateur.mail = adresseMail
	;

	IF usrPwd IS NULL
	THEN
		RAISE EXCEPTION 'L''utilisateur demandé n''existe pas ou est périmé ! (%)', adresseMail;
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
		personne.id_club INTO idClub
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
	SELECT
		ARRAY_AGG(jugeCompetition.id) INTO toReturn
	FROM
		jugeCompetition
	WHERE
		jugeCompetition.id_competition = idCompetition
	ORDER BY
		jugeCompetition.id
	;

	GET DIAGNOSTICS nRes = ROW_COUNT;

	IF nRes <> 5
	THEN
		RAISE EXCEPTION 'Le nombre de juges pour la compétition est incorrect (idCompetition: %, nRes: %). Erreur', idCompetition, nRes;
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
	nRes		INTEGER;
BEGIN
	SELECT
		equipe_jugeCompetition.id_jugeCompetition INTO toReturn
	FROM
		equipe_jugeCompetition
	WHERE
		equipe_jugeCompetition.id_equipe = idEquipe
	ORDER BY
		equipe_jugeCompetition.id
	;


	GET DIAGNOSTICS nRes = ROW_COUNT;

	IF nRes <> 5
	THEN
		RAISE EXCEPTION 'Le nombre de juges pour la compétition est incorrect (idCompetition: %, nRes: %). Erreur', idCompetition, nRes;
		RETURN NULL;
	ELSE
		RETURN toReturn;
	END IF;
END;
$body$
LANGUAGE plpgsql;
