#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table club_personne

allPersonnes = []

for i in range(0,5):
	allPersonnes.append([
		# club.nom
		'TestClub',
		# personne.nom
		'NAGEUR' + str(i+1),
		# personne.prenom
		'Test',
		# club_personne.dateInscription
		'01/09/2017',
		# club_personne.dateFinInscription
		'30/06/2018'
	])


req = (
		'(\n' + 
		'\t(SELECT club.id FROM club WHERE club.nom = \'' + personne[0] + '\'),\n' + 
		'\t(SELECT personne.id FROM personne WHERE personne.nom = \'' + personne[1] + '\' AND personne.prenom = \'' + personne[2] + '\'),\n' + 
		'\tTO_DATE(\'' + personne[3] + '\', \'dd/mm/yyyy\'),\n' + 
		'\tTO_DATE(\'' + personne[4] + '\', \'dd/mm/yyyy\')\n' + 
		')'
	for personne in allPersonnes
)

print(
	'INSERT INTO club_personne VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
