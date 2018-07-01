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
-- competition
------------------------------------------------------------
-- [[ INSERT | UPDATE ]] --
-- La ville doit être en initcap
--CREATE OR REPLACE FUNCTION trig_fct_competition_beforeInsertUpdate_ville()
--RETURNS trigger AS $body$
--BEGIN
--	NEW.ville = INITCAP(NEW.ville);
--	return NEW;
--END;
--$body$
--LANGUAGE plpgsql;

--DROP TRIGGER IF EXISTS trig_competition_beforeInsertUpdate_ville ON competition;
--CREATE TRIGGER trig_competition_beforeInsertUpdate_ville
--BEFORE INSERT OR UPDATE
--ON competition
--FOR EACH ROW
--EXECUTE PROCEDURE trig_fct_competition_beforeInsertUpdate_ville();

-- [[ UPDATE ]]
-- Lorsqu'on modifie l'heure de la compétition, il faut que re-vérifier que les personnes participant à la compétition sont bien tous de la même équipe
CREATE OR REPLACE FUNCTION trig_fct_competition_afterUpdate_membreClub()
RETURNS trigger AS $body$
DECLARE
	_row		RECORD;	-- Curseur permettant d'itérer sur les résultats d'une requête
	_equipe_id	INTEGER;
	_allClub	INTEGER[];
BEGIN
	--- Pour chaque équipe
	FOR _row IN 
            WITH RES AS (
            SELECT
                equipe.id					AS equipe_id,
                --ARRAY_AGG(personne.id)		AS personne_id,
                club.id						AS club_id/*,
                club_personne.dateInscription		AS equipe_personne_dateInscription,
                club_personne.dateFinInscription	AS equipe_personne_dateFinInscription*/
            FROM personne
            LEFT OUTER JOIN club_personne
                ON club_personne.id_personne = personne.id
                AND (CASE club_personne.dateFinInscription
                    WHEN NULL THEN
                    (NEW.dateCompetition > club_personne.dateInscription)
                    ELSE
                    (NEW.dateCompetition BETWEEN club_personne.dateInscription AND club_personne.dateFinInscription)
                END)
            LEFT OUTER JOIN club
                ON club.id = club_personne.id_club
            INNER JOIN equipe_personne
                ON equipe_personne.id_personne = personne.id
            INNER JOIN equipe
                ON equipe.id = equipe_personne.id_equipe
            INNER JOIN competition
                ON competition.id = equipe.id_competition
            GROUP BY equipe_id, club_id
                )
                SELECT
                    equipe_id,
                    ARRAY_AGG(club_id) AS allClub
                    FROM RES
                    GROUP BY equipe_id
	LOOP
		--FETCH _row INTO _equipe_id, _allClub;
		IF containsNull(_row.allClub) THEN
			RAISE EXCEPTION 'Une personne n''a pas d''équipe pour la date donnée (equipe_id: %)', _row.equipe_id;
		END IF;


		IF ARRAY_LENGTH(_row.allClub, 1) != 1 THEN
			RAISE EXCEPTION 'De multiples clubs ont été trouvés pour les personnes à la date donnée (allClubs: %)', array_to_string(_row.allClubs, ',', '*') ;
		END IF;

	END LOOP;

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_competition_afterUpdate_membreClub ON competition;
CREATE TRIGGER trig_competition_afterUpdate_membreClub
AFTER UPDATE
ON competition
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_competition_afterUpdate_membreClub();


------------------------------------------------------------
-- equipe_jugeCompetition
------------------------------------------------------------

-- [[ INSERT ]] --
-- A l'insertion, il faut que la valeur de la note soit à NULL
CREATE OR REPLACE FUNCTION trig_fct_equipe_jugeCompetition_afterInsert_noteJuryCompetition()
RETURNS trigger AS $body$
BEGIN
	PERFORM checkValue_different(NEW.note, NULL, format('La valeur de la note doit être NULL (%s)', NEW.note));
	RETURN NEW;
END;
$body$
LANGUAGE PLPGSQL;

DROP TRIGGER IF EXISTS trig_equipe_jugeCompetition_afterInsert_noteJuryCompetition ON equipe_jugeCompetition;
CREATE TRIGGER trig_equipe_jugeCompetition_afterInsert_noteJuryCompetition
AFTER INSERT
ON equipe_jugeCompetition
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_jugeCompetition_afterInsert_noteJuryCompetition();

-- [[ UPDATE ]] --
-- Lorsqu'on veut ajouter des notes, il faut que la compétition ait commencé
CREATE OR REPLACE FUNCTION trig_fct_equipe_jugeCompetition_beforeUpdate_noteJuryCompetition()
RETURNS trigger AS $body$
DECLARE
	dateDebut	DATE;
BEGIN
	SELECT
		debut
	INTO
		dateDebut
	FROM equipe
	WHERE
		equipe.id = NEW.id_equipe
	;

	IF dateDebut IS NULL THEN
		RAISE EXCEPTION 'Le ballet n''a pas commencé. Les notes ne peuvent pas être appliquées. Abandon';
	ELSE
		RETURN NEW;
	END IF;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_jugeCompetition_beforeUpdate_noteJuryCompetition ON equipe_jugeCompetition;
CREATE TRIGGER trig_equipe_jugeCompetition_beforeUpdate_noteJuryCompetition
BEFORE UPDATE
ON equipe_jugeCompetition
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_jugeCompetition_beforeUpdate_noteJuryCompetition();


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
		id_competition
	INTO
		competition_id
	FROM
		equipe
	WHERE
		equipe.id = NEW.id_equipe
	;

	-- Récupération des id des juges de la compétition
	SELECT
		ARRAY_AGG(jugeCompetition.id)
	INTO
		competition_juges
	FROM
		jugeCompetition
	INNER JOIN typeJuge
		ON typejuge.id = jugeCompetition.id_typeJuge
	WHERE
		jugeCompetition.id_competition = competition_id
	AND	typeJuge.nom = 'Juge'
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

-- Géré par Symfony --
--DROP TRIGGER IF EXISTS trig_utilisateur_beforeInsertUpdate_modifMDP ON utilisateur;
--CREATE TRIGGER trig_utilisateur_beforeInsertUpdate_modifMDP
--BEFORE INSERT OR UPDATE
--ON utilisateur
--FOR EACH ROW
--EXECUTE PROCEDURE trig_fct_utilisateur_beforeInsertUpdate_modifMDP();


------------------------------------------------------------
-- equipe
------------------------------------------------------------

-- [[ INSERT ]] --
-- Création d'une équipe:
---- Il faut que l'heure de début soit à NULL
---- Il faut qu'il ne soit pas visionnable
---- Il faut qu'il ne soit pas notable
---- Il faut que la pénalité soit à NULL

CREATE OR REPLACE FUNCTION trig_fct_equipe_afterInsert_newEquipe()
RETURNS trigger AS $body$
DECLARE
	competitionDate	DATE;
	nRes		INTEGER;
BEGIN
	-- On récupère la date de la compétition
	SELECT
		competition.dateCompetition
	INTO
		competitionDate
	FROM
		competition
	WHERE
		competition.id = NEW.id_competition
	;

	-- Valudation des valeurs par défaut
	PERFORM checkValue_different(NEW.debut, NULL, 'La valeur de debut n''est pas NULL. Abandon');
	PERFORM checkValue_different(NEW.visionnable, FALSE, 'La valeur de visionnable n''est pas à false. Abandon');
	PERFORM checkValue_different(NEW.notable, FALSE, 'La valeur de notable n''est pas à false. Abandon');
	PERFORM checkValue_different(NEW.penalite, NULL, 'La valeur de la pénalité n''est pas NULL. Abandon');

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
			COUNT(jugeCompetition.id)
		INTO
			nJuge
		FROM
			jugeCompetition
		INNER JOIN typeJuge
			ON typeJuge.id = jugeCompetition.id_typeJuge
		WHERE
			jugeCompetition.id_competition = NEW.id_competition
		AND	typeJuge.nom = 'Juge'
		GROUP BY
			jugeCompetition.id_competition, jugeCompetition.id
		;
		--
		SELECT
			COUNT(jugeCompetition.id)
		INTO
			nJugeArbitre
		FROM
			jugeCompetition
		INNER JOIN typeJuge
			ON typeJuge.id = jugeCompetition.id_typeJuge
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

-- Quand on marque le ballet comme visionnable, il faut que toutes les notes des juges soient décidées, qu'il ait eu une heure de début (trigger ajout notes) et que la pénalité soit différente de NULL
CREATE OR REPLACE FUNCTION trig_fct_equipe_afterUpdate_visionnable()
RETURNS trigger AS $body$
DECLARE
	nAvecNotes	INTEGER;
	_penalite	INTEGER;
BEGIN
	SELECT
		COUNT(equipe_jugeCompetition.id_jugeCompetition),
		equipe.penalite
	INTO
		nAvecNotes,
		_penalite
	FROM
		equipe_jugeCompetition
	INNER JOIN equipe
		ON equipe.id = equipe_jugeCompetition.id_equipe
	WHERE
		equipe_jugeCompetition.id_equipe = NEW.id
	AND	equipe_jugeCompetition.note IS NOT NULL
	GROUP BY
		equipe_jugeCompetition.id_equipe, equipe.penalite
	;

	PERFORM checkValue_different(nAvecNotes, 5, format('Tous les juges n''ont pas donné leur note (%s)', nAvecNotes));
	PERFORM checkValue_different(_penalite, NULL, format('La pénalité n''a pas été décidée (%s)', _penalite));
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
--CREATE OR REPLACE FUNCTION trig_fct_equipe_afterUpdate_penalite()
--RETURNS trigger AS $body$
--BEGIN
	-- < 0 OR > 4*0.5 = 2)
--	IF NEW.penalite < 0 OR NEW.penalite > 4 THEN
--		RAISE EXCEPTION 'La valeur de la pénalité est incorrecte (% => %)', NEW.penalite, (NEW.penalite * 0.5);
--	ELSE
--		RETURN NEW;
--	END IF;

--END;
--$body$
--LANGUAGE plpgsql;

--DROP TRIGGER IF EXISTS trig_equipe_afterUpdate_penalite ON equipe;
--CREATE TRIGGER trig_equipe_afterUpdate_penalite
--AFTER UPDATE
--ON equipe
--FOR EACH ROW
--EXECUTE PROCEDURE trig_fct_equipe_afterUpdate_penalite();

------------------------------------------------------------
-- equipe_personne
------------------------------------------------------------

-- [[ INSERT | UPDATE ]] --
-- Quand on ajoute un nageur, on vérifie qu'il fasse partie du même club que les autres
-- Quand on ajoute un joueur, on vérifie qu'il soit bien valide dans le temps donné
CREATE OR REPLACE FUNCTION trig_fct_equipe_personne_afterInsertUpdate_personneInscription()
RETURNS trigger AS $body$
DECLARE
	date_competition	DATE;		-- Date de la compétition
	personne_club		INTEGER;	-- Club de la personne ajoutée
	all_clubs		INTEGER[];	-- Liste des clubs trouvés pour la compétition
BEGIN
	-- On récupère la date de la compétition
	SELECT
		competition.dateCompetition
	INTO
		date_competition
	FROM equipe
	INNER JOIN competition
		ON competition.id = equipe.id_competition
	WHERE
		equipe.id = NEW.id_equipe
	;

	-- On récupère la liste des clubs des utilisateurs pour la date donnée
	WITH RES AS (
		SELECT
			club.id AS club_id
		FROM equipe_personne
		INNER JOIN personne
			ON personne.id = equipe_personne.id_personne
		INNER JOIN club_personne
			ON club_personne.id_personne = equipe_personne.id_personne
		INNER JOIN club
			ON club.id = club_personne.id_club
		WHERE
			equipe_personne.id_equipe = NEW.id_equipe
		AND	club_personne.dateInscription < date_competition
		AND  (
			club_personne.dateFinInscription IS NULL
		OR	club_personne.dateFinInscription > date_competition
		)
		GROUP BY club.id
	)
	SELECT
		ARRAY_AGG(club_id)
	INTO
		all_clubs
	FROM RES
	;

	PERFORM checkValue_different(
		ARRAY_LENGTH(all_clubs, 1),
		1,
		format('Les joueurs font partie de plusieurs clubs (%s)', array_to_string(all_clubs, ',', '*') )
	);

	RETURN NEW;

/*
	-- On est sûr que la liste des clubs ne contient qu'un club
	-- On vérifie que la personne fasse bien partie de l'équipe pour la compétition
	SELECT
		club_personne.id_club
	INTO
		personne_club
	FROM club_personne
	WHERE
		club_personne.id_personne = NEW.id_personne
	AND	club_personne.dateInscription < date_competition
	AND (
		club_personne.dateFinInscription IS NULL
	OR	club_personne.dateFinInscription > date_competition
	);

	IF nPersonne = 0 THEN
		RAISE EXCEPTION 'La personne n''est pas inscrite à cette date dans ce club'
	ELSIF

	ELSE

	END;

	IF date_competition > _club_personne.dateInscription THEN
		IF _club_personne.dateFinInscription IS NOT NULL THEN
			
		ELSE
			-- La personne est toujours inscrite
			RETURN NEW;
		END IF;
	ELSE
		RAISE EXCEPTION 'La date de début de la compétition est située avant l''inscription de la personne (date_competition: %s; date_inscription: %s). ERREUR', date_competition, _personne.dateInscription;
		RETURN NULL;
	END IF;*/
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_equipe_personne_afterInsertUpdate_personneInscription ON equipe_personne;
CREATE TRIGGER trig_equipe_personne_afterInsertUpdate_personneInscription
AFTER INSERT OR UPDATE
ON equipe_personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_equipe_personne_afterInsertUpdate_personneInscription();

/*
CREATE OR REPLACE FUNCTION trig_fct_equipe_personne_afterInsertUpdate_personneEquipe()
RETURNS trigger AS $body$
DECLARE
	idClub	INTEGER;
	nClub	INTEGER;
BEGIN
	-- On vérifie qu'il fasse partie d'un club
	SELECT
		personne.id_club
	INTO
		idClub
	FROM personne
	WHERE
		personne.id = NEW.id_personne
	;

	PERFORM checkValue_different(idClub, NULL, format('La personne ne fait pas partie d''un club (%s)', idClub));


	SELECT
		COUNT(club.id)
	INTO
		nClub
	FROM equipe_personne
	INNER JOIN personne
		ON personne.id = equipe_personne.id_personne
	INNER JOIN club
		ON club.id = personne.id_club
	WHERE equipe_personne.id_equipe = NEW.id_equipe
	GROUP BY club.id
	;

	PERFORM checkValue_different(nClub, 1, format('Les joueurs font partie de plusieurs clubs (%s)', nClub));
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
*/

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

------------------------------------------------------------
-- club_personne
------------------------------------------------------------

-- [[ INSERT | UPDATE ]] --
-- On vérifie que la personne n'est pas déjà inscrite dans un club avec les périodes données
CREATE OR REPLACE FUNCTION trig_fct_club_personne_afterInsertUpdate_inscription()
RETURNS trigger AS $body$
DECLARE
	nRes	INTEGER;
BEGIN
	SELECT
		COUNT(club_personne.id_personne)
	INTO
		nRes
	FROM club_personne
	WHERE
		club_personne.id_personne = NEW.id_personne
	AND	(
		NEW.dateInscription BETWEEN club_personne.dateInscription and club_personne.dateFinInscription
		OR	club_personne.dateFinInscription IS NULL
		)
	;

	-- Une seule inscription possible, celle actuelle. Sinon, ERREUR
	PERFORM checkValue_different(nRes, 1, format('Une personne est déjà inscrite pour cette période (%s)', nRes));

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_club_personne_afterInsertUpdate_inscription ON club_personne;
CREATE TRIGGER trig_club_personne_afterInsertUpdate_inscription
AFTER INSERT OR UPDATE
ON club_personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_club_personne_afterInsertUpdate_inscription();

-- On vérifie que la personne était bien née quand on l'a inscrite dans le club
CREATE OR REPLACE FUNCTION trig_fct_club_personne_afterInsertUpdate_estNee()
RETURNS trigger AS $body$
DECLARE
	personne_dateNaissance	DATE;
BEGIN
	-- Récupération de la date de naissance de la personne
	SELECT
		dateNaissance
	INTO
		personne_dateNaissance
	FROM personne
	WHERE personne.id = NEW.id_personne
	;

	IF personne_dateNaissance > NEW.dateInscription THEN
		RAISE EXCEPTION 'La personne n''est pas encore née à la date d''isncription (personne_dateNaissance : %s, new.dateInscription: %s)', personne_dateNaissance, new.dateInscription;
	ELSE
		RETURN NEW;
	END IF;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_clug_personne_afterInsertUpdate_estNee ON club_personne;
CREATE TRIGGER trig_clug_personne_afterInsertUpdate_estNee 
AFTER INSERT OR UPDATE
ON club_personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_club_personne_afterInsertUpdate_estNee();

-- [[ UPDATE ]] --

-- On vérifie que la personne n'a pas de compétition entre l'ancienne et la nouvelle date de début
CREATE OR REPLACE FUNCTION trig_fct_club_personne_beforeUpdate_aCompetition_dateDebut()
RETURNS trigger AS $body$
DECLARE
	compet_id	INTEGER[];
BEGIN

	-- On récupère les compétitions entre l'ancienne et la nouvelle date
	SELECT
		ARRAY_AGG(competition.id)
	INTO compet_id
	FROM personne
	INNER JOIN equipe_personne
		ON equipe_personne.id_personne = personne.id
	INNER JOIN equipe
		ON equipe.id = equipe_personne.id_equipe
	INNER JOIN competition
		ON competition.id = equipe.id_competition
	WHERE
		personne.id = NEW.id_personne
		AND competition.dateCompetition BETWEEN OLD.dateInscription AND NEW.dateInscription
	;

	IF ARRAY_LENGTH(compet_id, 1) != 0 OR ARRAY_LENGTH(compet_id, 1) IS NOT NULL THEN
		RAISE EXCEPTION 'La personne a une compétition pour la période donnée (%)', array_to_string(_row.allClubs, ',', '*') ;
	END IF;

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_club_personne_beforeUpdate_aCompetition_dateDebut ON club_personne;
CREATE TRIGGER trig_club_personne_beforeUpdate_aCompetition_dateDebut 
BEFORE UPDATE
ON club_personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_club_personne_beforeUpdate_aCompetition_dateDebut();

-- On vérifie que la personne n'a pas de compétition entre l'ancienne et la nouvelle date de fin
CREATE OR REPLACE FUNCTION trig_fct_club_personne_beforeUpdate_aCompetition_dateFin()
RETURNS trigger AS $body$
DECLARE
	_query		TEXT;
	compet_id	INTEGER[];
BEGIN

	-- On récupère les compétitions entre l'ancienne et la nouvelle date
	_query := $query$
	SELECT
		ARRAY_AGG(competition.id)
	FROM personne
	INNER JOIN equipe_personne
		ON equipe_personne.id_personne = personne.id
	INNER JOIN equipe
		ON equipe.id = equipe_personne.id_equipe
	INNER JOIN competition
		ON competition.id = equipe.id_competition
	WHERE
		personne.id = $1
	$query$;

	-- Si aucune date de fin d'inscription
	IF OLD.dateFinInscription IS NULL THEN
		_query := _query || $query$
		AND competition.dateCompetition > $2
		$query$;
	ELSE
		_query := _query || $query$
		AND competition.dateCompetition BETWEEN $2 AND $3
		$query$;
	END IF;

	EXECUTE _query INTO compet_id USING NEW.id_personne, NEW.dateFinInscription, OLD.dateFinInscription;

	IF ARRAY_LENGTH(compet_id, 1) != 0 OR ARRAY_LENGTH(compet_id, 1) IS NOT NULL THEN
		RAISE EXCEPTION 'La personne a une compétition pour la période donnée (%)', array_to_string(_row.allClubs, ',', '*') ;
	END IF;

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_club_personne_beforeUpdate_aCompetition_dateFin ON club_personne;
CREATE TRIGGER trig_club_personne_beforeUpdate_aCompetition_dateFin 
BEFORE UPDATE
ON club_personne
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_club_personne_beforeUpdate_aCompetition_dateFin();


------------------------------------------------------------
-- jugeCompetition
------------------------------------------------------------

-- [[ INSERT | UPDATE ]] --
-- On vérifie qu'un juge de compétition a bien le rôle de juge
CREATE OR REPLACE FUNCTION trig_fct_jugeCompetition_afterInsertUpdate_estJuge()
RETURNS trigger AS $body$
DECLARE
	utilisateurId	INTEGER;
BEGIN
	SELECT
		utilisateur.id
	INTO
		utilisateurId
	FROM utilisateur
	INNER JOIN utilisateur_typeUtilisateur
		ON utilisateur_typeUtilisateur.id_utilisateur = utilisateur.id
	INNER JOIN typeUtilisateur
		ON typeUtilisateur.id = utilisateur_typeUtilisateur.id_typeUtilisateur
	WHERE
		typeUtilisateur.nom = 'Juge'
	AND	utilisateur.id = NEW.id_utilisateur
	;

	PERFORM checkValue_different(utilisateurId, NEW.id_utilisateur, format('Cet utilisateur n''est pas un juge (%s)', NEW.id_utilisateur));

	RETURN NEW;
END;
$body$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trig_jugeCompetition_afterInsertUpdate_estJuge ON jugeCompetition;
CREATE TRIGGER trig_jugeCompetition_afterInsertUpdate_estJuge
BEFORE INSERT OR UPDATE
ON jugeCompetition
FOR EACH ROW
EXECUTE PROCEDURE trig_fct_jugeCompetition_afterInsertUpdate_estJuge();

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
