INSERT INTO public.personne VALUES
(
	DEFAULT,
	NULL,
	'Proprietaire',
	'Quelqu''un',
	TO_DATE('15/04/1965', 'dd/mm/yyyy'),
	TO_DATE('15/04/2015', 'dd/mm/yyyy'),
	NULL
);

INSERT INTO public.club VALUES
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE nom='PROPRIETAIRE'),
	'testClub',
	'32 rue du test 69005 LYON'
);

INSERT INTO public.personne VALUES
	(
		DEFAULT,
		(SELECT club.id FROM public.club WHERE club.nom = 'testClub'),
		'USER',
		'Test',
		TO_DATE('29/07/1998', 'dd/mm/yyyy'),
		TO_DATE('01/09/2017', 'dd/mm/yyyy'),
		TO_DATE('31/06/2018', 'dd/mm/yyyy')
	)
;

INSERT INTO public.utilisateur VALUES
	(
		DEFAULT,
		(SELECT presonne.id FROM public.personne WHERE personne.nom = 'USER' AND personne.prenom = 'Test'),
		'foo@mail.example',
		'azerty'
	)
;
