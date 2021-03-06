-- Ajout de l'extension pgcrypto
-- voir: https://x-team.com/blog/storing-secure-passwords-with-postgresql/
-- voir: https://fr.slideshare.net/spjuliano/postgresql-how-to-store-passwords-safely
CREATE EXTENSION IF NOT EXISTS pgcrypto;


-- Purge des anciennes tables --
DROP TABLE IF EXISTS public.competition CASCADE;
DROP TABLE IF EXISTS public.personne CASCADE;
DROP TABLE IF EXISTS public.utilisateur CASCADE;
DROP TABLE IF EXISTS public.equipe CASCADE;
DROP TABLE IF EXISTS public.club CASCADE;
DROP TABLE IF EXISTS public.jugeCompetition CASCADE;
DROP TABLE IF EXISTS public.lieu CASCADE;
DROP TABLE IF EXISTS public.typeJuge CASCADE;
DROP TABLE IF EXISTS public.typeUtilisateur CASCADE;
DROP TABLE IF EXISTS public.equipe_jugeCompetition CASCADE;
DROP TABLE IF EXISTS public.equipe_personne CASCADE;
DROP TABLE IF EXISTS public.utilisateur_typeUtilisateur CASCADE;
DROP TABLE IF EXISTS public.club_personne CASCADE;


------------------------------------------------------------
-- Sequences
------------------------------------------------------------
-- 1. Deletion
DROP SEQUENCE IF EXISTS seq_competition_id;
DROP SEQUENCE IF EXISTS seq_personne_id;
DROP SEQUENCE IF EXISTS seq_utilisateur_id;
DROP SEQUENCE IF EXISTS seq_equipe_id;
DROP SEQUENCE IF EXISTS seq_club_id;
DROP SEQUENCE IF EXISTS seq_jugeCompetition_id;
DROP SEQUENCE IF EXISTS seq_lieu_id;
DROP SEQUENCE IF EXISTS seq_typeJuge_id;
DROP SEQUENCE IF EXISTS seq_typeUtilisateur_id;
DROP SEQUENCE IF EXISTS seq_club_personne_id;
DROP SEQUENCE IF EXISTS seq_equipe_jugeCompetition_id;
-- 2. Adding the sequences
CREATE SEQUENCE IF NOT EXISTS seq_competition_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_personne_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_utilisateur_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_equipe_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_club_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_jugeCompetition_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_lieu_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_typeJuge_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_typeUtilisateur_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_club_personne_id INCREMENT BY 1 START WITH 1;
CREATE SEQUENCE IF NOT EXISTS seq_equipe_jugeCompetition_id INCREMENT BY 1 START WITH 1;



-- Création des nouvelles tables --

------------------------------------------------------------
-- Table: competition
------------------------------------------------------------
-- Table des compétitions
-- Une compétition a un titre, une date et une ville où elle se déroule
------------------------------------------------------------
CREATE TABLE public.competition(
	-- Primary keys
	id		INT  NOT NULL DEFAULT NEXTVAL('seq_competition_id') ,
	-- Foreign keys
	id_lieu		INT NOT NULL,
	-- Data
	titre		VARCHAR (50) NOT NULL ,
	dateCompetition	DATE  NOT NULL ,
	-- Contraints
	CONSTRAINT prk_competition_id PRIMARY KEY (id)
);


------------------------------------------------------------
-- Table: personne
------------------------------------------------------------
-- Table des personnes
-- Une personne est une identité sur notre base de donnée (aussi bien arbitre que nnageur)
------------------------------------------------------------
CREATE TABLE public.personne(
	-- Primary keys
	id		INT  NOT NULL DEFAULT NEXTVAL('seq_personne_id'),
	-- Data
	nom			VARCHAR (50) NOT NULL ,
	prenom			VARCHAR (50) NOT NULL ,
	datenaissance		DATE ,
	-- Contraints
	CONSTRAINT prk_personne_id PRIMARY KEY (id)
);


------------------------------------------------------------
-- Table: utilisateur
------------------------------------------------------------
-- Table des utilisateurs
-- Les utilisateurs sont les personnes pouvant se connecter sur l'application
------------------------------------------------------------
CREATE TABLE public.utilisateur(
	-- Primary keys
	id		INT NOT NULL DEFAULT NEXTVAL('seq_utilisateur_id') ,
	-- Foreign keys
	id_personne	INT NOT NULL UNIQUE,
	-- Data
	mail		VARCHAR (50) NOT NULL UNIQUE ,
	mdp		VARCHAR (60) NOT NULL ,	--Blowfish algorithm
	-- Contraints
	CONSTRAINT prk_utilisateur_id PRIMARY KEY (id)
);


------------------------------------------------------------
-- Table: equipe
------------------------------------------------------------
-- Table équipes
-- Une équipe est reliée à un club et à une compétition. 
------------------------------------------------------------
CREATE TABLE public.equipe(
	-- Primary keys
	id		INT  NOT NULL DEFAULT NEXTVAL('seq_equipe_id') ,
	-- Foreign key
	id_competition	INT NOT NULL ,
	-- Data
	nom		VARCHAR(25) NOT NULL ,
	ordrePassage	INT  NOT NULL ,
	debut		TIMESTAMP ,
	visionnable	BOOLEAN  NOT NULL DEFAULT false,
	notable		BOOLEAN  NOT NULL DEFAULT false,
	penalite	INT ,	-- out: x * 0.5
	-- Contraints
	CONSTRAINT prk_equipe_id PRIMARY KEY (id) ,
	UNIQUE(id_competition, ordrePassage)	-- Une seule équipe avec le même ordre de passage par équipe
);


------------------------------------------------------------
-- Table: club
------------------------------------------------------------
-- Table des clubs
-- Un club a un nom et une adresse. Des personne s'y inscrivent
------------------------------------------------------------
CREATE TABLE public.club(
	-- Primary keys
	id		INT  NOT NULL DEFAULT NEXTVAL('seq_club_id'),
	-- Foreign keys
	id_lieu		INT NOT NULL,
	id_personne	INT NOT NULL,	-- Utilisateur président du club
	-- Data
	nom		VARCHAR (50) NOT NULL UNIQUE,
	-- Contraints
	CONSTRAINT prk_club_id PRIMARY KEY (id)
);


------------------------------------------------------------
-- Table: jugeCompetition
------------------------------------------------------------
-- Table des juges de compétition
-- Un juge est un *utilisateur* juge relié à une *compétition* pour laquelle il a un *type*
------------------------------------------------------------
CREATE TABLE public.jugeCompetition(
	-- Primary keys
	id		INT  NOT NULL DEFAULT NEXTVAL('seq_jugeCompetition_id'),
	-- Foreign keys
	id_typeJuge	INT  NOT NULL ,
	id_competition	INT  NOT NULL ,
	id_utilisateur	INT  NOT NULL ,
	-- Data
	rang		INT  NOT NULL ,	-- -1 -> juge-arbitre
	-- Constraints
	CONSTRAINT prk_jugeCompetition_id PRIMARY KEY (id),
	UNIQUE(id_competition, rang) DEFERRABLE INITIALLY DEFERRED,		-- Un seul juge du même rang pour la même compétition
	UNIQUE(id_competition, id_utilisateur) DEFERRABLE INITIALLY DEFERRED	-- Un seul même utilisateur par compétition
);


------------------------------------------------------------
-- Table: lieu
------------------------------------------------------------
-- Table des lieux de clubs/compétitions
-- Un lieu a une adresse
------------------------------------------------------------
CREATE TABLE public.lieu(
	-- Primary keys
	id		INT NOT NULL DEFAULT NEXTVAL('seq_lieu_id'),
	-- Data
	adresse		VARCHAR(100) NOT NULL,
	-- Constraints
	CONSTRAINT prk_lieu_id PRIMARY KEY (id),
	UNIQUE(adresse)
);


------------------------------------------------------------
-- Table: typeArbitre
------------------------------------------------------------
-- Table des types d'arbitres
-- Un arbitre a un type pour chaque compétition (Juge-arbitre, Juge)
------------------------------------------------------------
CREATE TABLE public.typeJuge(
	-- Primary keys
	id	INT  NOT NULL DEFAULT NEXTVAL('seq_typeJuge_id'),
	-- Data
	nom	VARCHAR (15) NOT NULL UNIQUE ,
	-- Constraints
	CONSTRAINT prk_typeJuge_id PRIMARY KEY (id)
);

-- Prépopulation
INSERT INTO public.typeJuge
VALUES
	(DEFAULT, 'Juge'),
	(DEFAULT, 'Juge-arbitre')
;

------------------------------------------------------------
-- Table: typeUtilisateur
------------------------------------------------------------
-- Table des types d'utilisateurs
-- Cela correspond aux rôles de l'utilisateur
-- Un utilisateur peut être de plusieurs types
------------------------------------------------------------
CREATE TABLE public.typeUtilisateur(
	-- Primary keys
	id	INT  NOT NULL DEFAULT NEXTVAL('seq_typeUtilisateur_id') ,
	-- Data
	nom	VARCHAR (25) NOT NULL UNIQUE ,
	-- Constraints
	CONSTRAINT prk_typeUtilisateur_id PRIMARY KEY (id)
);

-- Prépopulation
INSERT INTO public.typeUtilisateur
VALUES
	(DEFAULT, 'ROLE_ADMIN'),
	(DEFAULT, 'ROLE_JUGE'),
	(DEFAULT, 'ROLE_CREATE_COMPET')
;

------------------------------------------------------------
-- Table: equipe_jugeCompetition
------------------------------------------------------------
-- Table de liaison entre la table des équipes (ballet) et la table des juges de compétition
-- Ajoute la note de l'arbitre pour l'équipe donnée
-- (Optionellement) On peut ajouter une pénalité sur la note d'un arbitre
----
-- equipe		jugeCompetition
-- 1,n		-> 	4,n (min 3 arbitres et 1 juge-arbitre; dans notre cas, on aura 5*3 => 15 arbitres et 1 juge-arbitre)
------------------------------------------------------------
CREATE TABLE public.equipe_jugeCompetition(
	-- Foreign keys
	id_equipe		INT  NOT NULL ,
	id_jugeCompetition	INT  NOT NULL ,
	-- Data
	note			INT DEFAULT 0,
	-- Constraints
	UNIQUE(id_equipe, id_jugeCompetition)	-- Une seule note par juge et par équipe
);


------------------------------------------------------------
-- Table: equipe_personne
------------------------------------------------------------
-- Table de liaison entre la table des équipes et les personnes en faisant partie
----
-- equipe		personne
-- 1,n		-> 	1,n
-- (note): Relation 1,n pour les équipes car une personne peut faire plusieurs compétitions (on récupère la compétition à laquelle la personne participe à partir de l'équipe)
------------------------------------------------------------
CREATE TABLE public.equipe_personne(
	-- Foreign keys
	id_equipe	INT  NOT NULL ,
	id_personne	INT  NOT NULL ,
	-- Contraints
	CONSTRAINT prk_equipe_personne PRIMARY KEY (id_equipe,id_personne)
);

------------------------------------------------------------
-- Table: club_personne
------------------------------------------------------------
-- Table de liaison entre la table des personnes et la table des clubs
-- Permet de connaître le club d'une personne à chaque moment
----
-- personne		club
-- 0,n		-> 	0,n
------------------------------------------------------------
CREATE TABLE public.club_personne(
	-- Foreign keys
	id_club			INTEGER NOT NULL ,
	id_personne		INTEGER NOT NULL ,
	dateInscription		DATE NOT NULL ,
	dateFinInscription	DATE
);

------------------------------------------------------------
-- Table: utilisateur_typeUtilisateur
------------------------------------------------------------
-- Table de liaison entre la table des utilisateurs et la table des types d'utilisateurs
-- Permet de connaître les rôles de l'utilisateur
----
-- utilisateurs		typeUtilisateur
-- 1,n		-> 	0,n
------------------------------------------------------------
CREATE TABLE public.utilisateur_typeUtilisateur(
	-- Foreign keys
	id_utilisateur		INT  NOT NULL ,
	id_typeUtilisateur	INT  NOT NULL ,
	-- Constraints
	CONSTRAINT prk_constraint_est_un PRIMARY KEY (id_typeUtilisateur,id_utilisateur)
);


--Competition
ALTER TABLE public.competition ADD CONSTRAINT fk_competition_id_lieu FOREIGN KEY(id_lieu) REFERENCES public.lieu(id);
-- Utilisateur
ALTER TABLE public.utilisateur ADD CONSTRAINT fk_utilisateur_id_personne FOREIGN KEY(id_personne) REFERENCES public.personne(id);
-- Equipe
ALTER TABLE public.equipe ADD CONSTRAINT fk_equipe_id_competition FOREIGN KEY(id_competition) REFERENCES public.competition(id);
ALTER TABLE public.equipe ADD CONSTRAINT constr_personne_panelite CHECK (penalite IS NULL OR (penalite >= 0 AND penalite <= 4));
-- Club
ALTER TABLE public.club ADD CONSTRAINT fk_club_id_lieu FOREIGN KEY(id_lieu) REFERENCES public.lieu(id);
ALTER TABLE public.club ADD CONSTRAINT fk_club_id_personne FOREIGN KEY(id_personne) REFERENCES public.personne(id);
-- JugeCompetition
ALTER TABLE public.jugeCompetition ADD CONSTRAINT fk_jugeCompetition_id_typeJuge FOREIGN KEY (id_typeJuge) REFERENCES public.typeJuge(id);
ALTER TABLE public.jugeCompetition ADD CONSTRAINT fk_jugeCompetition_id_competition FOREIGN KEY (id_competition) REFERENCES public.competition(id);
ALTER TABLE public.jugeCompetition ADD CONSTRAINT fk_jugeCompetition_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES public.utilisateur(id);
-- Equipe_JugeCompetition
ALTER TABLE public.equipe_jugeCompetition ADD CONSTRAINT fk_equipeJugeCompetition_id_equipe FOREIGN KEY (id_equipe) REFERENCES public.equipe(id);
ALTER TABLE public.equipe_jugeCompetition ADD CONSTRAINT fk_equipeJugeCompetition_id_jugeCompetition FOREIGN KEY (id_jugeCompetition) REFERENCES public.jugeCompetition(id);
ALTER TABLE public.equipe_jugeCompetition ADD CONSTRAINT constr_equipeJugeCompetition_note CHECK (note IS NULL OR (note >= 0 AND note <= 100));
-- Equipe_Personne
ALTER TABLE public.equipe_personne ADD CONSTRAINT fk_equipe_personne_id_equipe FOREIGN KEY (id_equipe) REFERENCES public.equipe(id);
ALTER TABLE public.equipe_personne ADD CONSTRAINT fk_equipe_personne_id_personne FOREIGN KEY (id_personne) REFERENCES public.personne(id);
-- Club_Personne
ALTER TABLE public.club_personne ADD CONSTRAINT fk_club_personne_id_club FOREIGN KEY(id_club) REFERENCES public.club(id);
ALTER TABLE public.club_personne ADD CONSTRAINT fk_club_personne_id_personne FOREIGN KEY(id_personne) REFERENCES public.personne(id);
ALTER TABLE public.club_personne ADD CONSTRAINT constr_club_personne_dateFinInscription CHECK (dateFinInscription IS NULL OR dateFinInscription > dateInscription);
-- Utilisateur_TypeUtilisateur
ALTER TABLE public.utilisateur_typeUtilisateur ADD CONSTRAINT fk_utilisateurTypeUtilisateur_id_typeUtilisateur FOREIGN KEY (id_typeUtilisateur) REFERENCES public.typeUtilisateur(id);
ALTER TABLE public.utilisateur_typeUtilisateur ADD CONSTRAINT fk_utilisateurTypeUtilisateur_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES public.utilisateur(id);
