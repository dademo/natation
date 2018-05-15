-- genPersonnes.py --
INSERT INTO personnes VALUES
(
	DEFAULT,
	'ADMIN',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'PRESIDENTCLUB',
	'Test',
	TO_DATE('15/03/1972', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'NAGEUR1',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'NAGEUR2',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'NAGEUR3',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'NAGEUR4',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'NAGEUR5',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'ARBITRE1',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'ARBITRE2',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'ARBITRE3',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'ARBITRE4',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'ARBITRE5',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
), 
(
	DEFAULT,
	'ARBITRE6',
	'Test',
	TO_DATE('29/07/1998', 'dd/mm/yyyy')
);


	
	
-- genClub.py --
INSERT INTO club VALUES
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE personne.nom = 'PRESIDENTCLUB' AND personne.prenom = 'Test'),
	'TestClub',
	'63 rue du test, 69005 LYON'
), 
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE personne.nom = 'PRESIDENTCLUB' AND personne.prenom = 'Test'),
	'TestClub1',
	'_TestAddress_'
), 
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE personne.nom = 'PRESIDENTCLUB' AND personne.prenom = 'Test'),
	'TestClub2',
	'_TestAddress_'
), 
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE personne.nom = 'PRESIDENTCLUB' AND personne.prenom = 'Test'),
	'TestClub3',
	'_TestAddress_'
), 
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE personne.nom = 'PRESIDENTCLUB' AND personne.prenom = 'Test'),
	'TestClub4',
	'_TestAddress_'
), 
(
	DEFAULT,
	(SELECT personne.id FROM personne WHERE personne.nom = 'PRESIDENTCLUB' AND personne.prenom = 'Test'),
	'TestClub5',
	'_TestAddress_'
);


	
	
-- genPersonne_club.py --
INSERT INTO club_personne VALUES
(
	(SELECT club.id FROM club WHERE club.nom = 'TestClub'),
	(SELECT personne.id FROM personne WHERE personne.nom = 'NAGEUR1' AND personne.prenom = 'Test'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
), 
(
	(SELECT club.id FROM club WHERE club.nom = 'TestClub'),
	(SELECT personne.id FROM personne WHERE personne.nom = 'NAGEUR2' AND personne.prenom = 'Test'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
), 
(
	(SELECT club.id FROM club WHERE club.nom = 'TestClub'),
	(SELECT personne.id FROM personne WHERE personne.nom = 'NAGEUR3' AND personne.prenom = 'Test'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
), 
(
	(SELECT club.id FROM club WHERE club.nom = 'TestClub'),
	(SELECT personne.id FROM personne WHERE personne.nom = 'NAGEUR4' AND personne.prenom = 'Test'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
), 
(
	(SELECT club.id FROM club WHERE club.nom = 'TestClub'),
	(SELECT personne.id FROM personne WHERE personne.nom = 'NAGEUR5' AND personne.prenom = 'Test'),
	TO_DATE('01/09/2017', 'dd/mm/yyyy'),
	TO_DATE('31/06/2018', 'dd/mm/yyyy')
);


	
	
-- genUtilisateurs.py --
INSERT INTO utilisateur VALUES
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'ADMIN' AND personne.prenom = 'Test'),
	'admin@bar.com',
	'azerty'
), 
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'JUGE1' AND personne.prenom = 'Test'),
	'foo1@bar.com',
	'azerty'
), 
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'JUGE2' AND personne.prenom = 'Test'),
	'foo2@bar.com',
	'azerty'
), 
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'JUGE3' AND personne.prenom = 'Test'),
	'foo3@bar.com',
	'azerty'
), 
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'JUGE4' AND personne.prenom = 'Test'),
	'foo4@bar.com',
	'azerty'
), 
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'JUGE5' AND personne.prenom = 'Test'),
	'foo5@bar.com',
	'azerty'
), 
(
	DEFAULT,
	(SELECT personne.id FROM public.personne WHERE personne.nom = 'JUGE6' AND personne.prenom = 'Test'),
	'foo6@bar.com',
	'azerty'
);


	
	
-- genUtilisateur_typeUtilisateur.py --
INSERT INTO utilisateur_typeUtilisateur VALUES
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'admin@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Administrateur')
), 
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'admin@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Créateur de compétition')
), 
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo1@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
), 
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo2@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
), 
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo3@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
), 
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo4@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
), 
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo5@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
), 
(
	(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = 'foo6@bar.com'),
	(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = 'Juge')
);


	
	
-- genCompetition.py --
INSERT INTO competition VALUES
(
	DEFAULT,
	'TestCompetition',
	TO_DATE('10/06/2018', 'dd/mm/yyyy'),
	'Lyon'
);


	
	
-- genJugeCompetition.py --
INSERT INTO jugeCompetition VALUES
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge-arbitre'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT personne.id FROM personne WHERE personne.mail = 'admin@bar.com'),
	-1
), 
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT personne.id FROM personne WHERE personne.mail = 'foo1@bar.com'),
	1
), 
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT personne.id FROM personne WHERE personne.mail = 'foo2@bar.com'),
	2
), 
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT personne.id FROM personne WHERE personne.mail = 'foo3@bar.com'),
	3
), 
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT personne.id FROM personne WHERE personne.mail = 'foo4@bar.com'),
	4
), 
(
	DEFAULT,
	(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = 'Juge'),
	(SELECT competition.id FROM competition WHERE competition.titre = 'TestCompetition'),
	(SELECT personne.id FROM personne WHERE personne.mail = 'foo5@bar.com'),
	5
);


