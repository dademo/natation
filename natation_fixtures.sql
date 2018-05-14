INSERT INTO public.personne VALUES
(
	DEFAULT,
	NULL,
	'PRESIDENTCLUB',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.club VALUES
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE personne.nom = 'PRESIDENTCLUB' AND personne.prenom = 'Test'),
	'testClub',
	'63 rue du test, 69005 LYON'
);

INSERT INTO public.personne VALUES
(
	DEFAULT,
	(SELECT club.id FROM public.club WHERE club.nom = 'testClub'),
	'USER1',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.personne VALUES
(
	DEFAULT,
	(SELECT club.id FROM public.club WHERE club.nom = 'testClub'),
	'USER2',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.personne VALUES
(
	DEFAULT,
	(SELECT club.id FROM public.club WHERE club.nom = 'testClub'),
	'USER3',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.personne VALUES
(
	DEFAULT,
	(SELECT club.id FROM public.club WHERE club.nom = 'testClub'),
	'USER4',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.personne VALUES
(
	DEFAULT,
	(SELECT club.id FROM public.club WHERE club.nom = 'testClub'),
	'USER5',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.personne VALUES
(
	DEFAULT,
	(SELECT club.id FROM public.club WHERE club.nom = 'testClub'),
	'USER6',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.personne VALUES
(
	DEFAULT,
	NULL,
	'ADMIN',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);

INSERT INTO public.utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'USER1' AND personne.prenom = 'Test'),
	'foo1@mail.example',
	'azerty'
);

INSERT INTO public.utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'USER2' AND personne.prenom = 'Test'),
	'foo2@mail.example',
	'azerty'
);

INSERT INTO public.utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'USER3' AND personne.prenom = 'Test'),
	'foo3@mail.example',
	'azerty'
);

INSERT INTO public.utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'USER4' AND personne.prenom = 'Test'),
	'foo4@mail.example',
	'azerty'
);

INSERT INTO public.utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'USER5' AND personne.prenom = 'Test'),
	'foo5@mail.example',
	'azerty'
);

INSERT INTO public.utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'USER6' AND personne.prenom = 'Test'),
	'foo6@mail.example',
	'azerty'
);

INSERT INTO public.utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'ADMIN' AND personne.prenom = 'Test'),
	'admin@mail.example',
	'azerty'
);

INSERT INTO utilisateur_typeUtilisateur VALUES
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo1@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
),
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo2@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
),
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo3@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
),
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo4@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
),
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo5@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
),
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo6@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
),
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'admin@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Administrateur')
),
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'admin@mail.example'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Créateur de compétition')
)
;

-- Création d'une compétition
INSERT INTO public.competition VALUES
(
	DEFAULT,
	'TestCompetition',
	TO_DATE('10/06/2018', 'dd/mm/yyyy'),
	'Lyon'
);

INSERT INTO public.jugeCompetition VALUES
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo1@mail.example'),
	1
),
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo2@mail.example'),
	2
),
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo3@mail.example'),
	3
),
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo4@mail.example'),
	4
),
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo5@mail.example'),
	5
),
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge-arbitre'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo6@mail.example'),
	-1
)
;
