-- Compétition avec les juges associés
CREATE OR REPLACE VIEW juge_competition
AS
SELECT
	competition.id							AS competition_id,
	competition.titre						AS competition_titre,
	competition.dateCompetition					AS competition_dateCompetition,
	lieu.adresse							AS competition_adresse,
	jugeCompetition.id						AS jugeCompetition_id,
	personne.nom || ' ' || personne.prenom				AS juge_nomComplet,
	typeJuge.nom							AS juge_type,
	jugeCompetition.rang						AS juge_rang
FROM competition
INNER JOIN lieu
	ON lieu.id = competition.id_lieu
INNER JOIN jugeCompetition
	ON jugeCompetition.id_competition = competition.id
INNER JOIN utilisateur
	ON utilisateur.id = jugeCompetition.id_utilisateur
INNER JOIN typeJuge
	ON typeJuge.id = jugeCompetition.id_typeJuge
INNER JOIN personne
	ON personne.id = utilisateur.id_personne
ORDER BY
	personne.prenom ASC,
	personne.nom ASC,
	competition.dateCompetition ASC
;

-- On aggrège les éléments de la vue précédente
CREATE OR REPLACE VIEW juge_competition_agg
AS
SELECT
	competition_id,
	competition_titre,
	competition_dateCompetition,
	competition_adresse,
	ARRAY_AGG(
		ARRAY[
			juge_nomComplet,
			juge_type
		]
	)				AS juge_nom_type
FROM juge_competition
GROUP BY
	competition_id,
	competition_titre,
	competition_dateCompetition,
	competition_adresse
;

-- Ballet avec les équipes associées
CREATE OR REPLACE VIEW all_equipe
AS
SELECT
	equipe.id				AS equipe_id,
	equipe.nom				AS equipe_nomEquipe,
	equipe.ordrePassage			AS equipe_ordrePassage,
	competition.id				AS competition_id,
	competition.titre			AS competition_titre,
	competition.dateCompetition		AS competition_dateCompetition,
	personne.id				AS equipe_membre_id,
	personne.nom				AS equipe_membre_nom,
	personne.prenom				AS equipe_membre_prenom,
	personne.dateNaissance			AS equipe_membre_dateNaissance,
	club_personne.id_club			AS club_id,
	club.nom				AS club_nom
FROM equipe
INNER JOIN competition
	ON competition.id = equipe.id_competition
INNER JOIN equipe_personne
	ON equipe_personne.id_equipe = equipe.id
INNER JOIN personne
	ON personne.id = equipe_personne.id_personne
INNER JOIN club_personne
	ON club_personne.id_personne = personne.id
INNER JOIN club
	ON club.id = club_personne.id_club
ORDER BY
	equipe.id ASC,
	personne.nom ASC
;

-- On aggrège les éléments de la vue précédente
CREATE OR REPLACE VIEW all_equipe_agg
AS
SELECT
	equipe_id,
	equipe_nomEquipe,
	equipe_ordrePassage,
	competition_id,
	competition_titre,
	competition_dateCompetition,
	ARRAY_AGG(equipe_membre_id)					AS equipe_membre_id,
	ARRAY_AGG(equipe_membre_nom || ' ' || equipe_membre_prenom)	AS equipe_membre_nom,
	ARRAY_AGG(equipe_membre_dateNaissance)				AS equipe_membre_dateNaissance,
	club_id,
	club_nom
FROM all_equipe
GROUP BY
	equipe_id,
	equipe_nomEquipe,
	equipe_ordrePassage,
	competition_id,
	competition_titre,
	competition_dateCompetition,
	club_id,
	club_nom
;

-- Membres d'un club
CREATE OR REPLACE VIEW all_nageur_club
AS
SELECT
	personne.id				AS personne_id,
	personne.nom				AS personne_nom,
	personne.prenom				AS personne_prenom,
	personne.dateNaissance			AS personne_dateNaissance,
	club_personne.dateInscription		AS personne_dateInscription,
	club_personne.dateFinInscription	AS personne_dateFinInscription,
	club.id					AS club_id,
	club.nom				AS club_nom,
	lieu.adresse				AS club_adresse
FROM personne
INNER JOIN club_personne
	ON club_personne.id_personne = personne.id
INNER JOIN club
	ON club.id = club_personne.id_club
INNER JOIN lieu
	ON lieu.id = club.id_lieu
ORDER BY personne.nom ASC, personne.prenom ASC, club_personne.dateInscription ASC, club.nom ASC
;

-- Liste des personnes avec leurs types (nageur, arbitre, etc)
CREATE OR REPLACE VIEW all_personne
AS
WITH res AS (
SELECT
	personne.id		AS personne_id,
	personne.nom		AS personne_nom,
	personne.prenom		AS personne_prenom,
	personne.dateNaissance	AS personne_dateNaissance,
	typeUtilisateur.nom	AS personne_type
FROM personne
LEFT OUTER JOIN utilisateur
	ON utilisateur.id_personne = personne.id
LEFT OUTER JOIN utilisateur_typeUtilisateur
	ON utilisateur_typeUtilisateur.id_utilisateur = utilisateur.id
LEFT OUTER JOIN typeUtilisateur
	ON typeUtilisateur.id = utilisateur_typeUtilisateur.id_typeUtilisateur
UNION
SELECT
	personne.id		AS personne_id,
	personne.nom		AS personne_nom,
	personne.prenom		AS personne_prenom,
	personne.dateNaissance	AS personne_dateNaissance,
	(
	CASE
		WHEN club_personne.id_club IS NOT NULL
		THEN 'Nageur'
		ELSE NULL
	END
	)			AS personne_type
FROM personne
LEFT OUTER JOIN club_personne
	ON club_personne.id_personne = personne.id
)
SELECT
	personne_id,
	personne_nom,
	personne_prenom,
	personne_dateNaissance,
	ARRAY_REMOVE(ARRAY_AGG(personne_type), NULL) AS personne_type
FROM res
GROUP BY personne_id, personne_nom, personne_prenom, personne_dateNaissance
ORDER BY personne_nom ASC, personne_prenom ASC
;

-- Liste des notes des juges avec les équipe reliées
CREATE OR REPLACE VIEW all_juge_competition_notes
AS
SELECT
	competition_id,
	competition_titre,
	competition_dateCompetition,
	competition_adresse,
	jugeCompetition_id,
	juge_nomComplet,
	juge_rang,
	equipe.nom						AS equipe_nom,
	equipe_jugeCompetition.note				AS equipe_note,
	(equipe.penalite * 0.5)					AS equipe_penalite
FROM juge_competition
INNER JOIN equipe_jugeCompetition
	ON equipe_jugeCompetition.id_jugeCompetition = juge_competition.jugeCompetition_id
INNER JOIN equipe
	ON equipe.id = equipe_jugeCompetition.id_equipe
WHERE juge_type != 'Juge-arbitre'
;

-- Liste des notes par équipes
CREATE OR REPLACE VIEW all_notes
AS
SELECT
	competition.id							AS competition_id,
	competition.titre						AS competition_titre,
	competition.dateCompetition					AS competition_dateCompetition,
	equipe.id							AS equipe_id,
	equipe.nom							AS equipe_nom,
	equipe.ordrePassage						AS equipe_ordrePassage,
	(SUM(equipe_jugeCompetition.note) / 5) - equipe.penalite	AS equipe_note
FROM equipe
INNER JOIN competition
	ON competition.id = equipe.id_competition
INNER JOIN equipe_jugeCompetition
	ON equipe_jugeCompetition.id_equipe = equipe.id
GROUP BY
	competition.id,
	competition.titre,
	competition.dateCompetition,
	equipe.id,
	equipe.nom,
	equipe.ordrePassage
ORDER BY
	equipe.id_competition ASC,
	equipe.ordrePassage ASC
;
