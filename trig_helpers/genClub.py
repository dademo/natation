#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table club

allClub = []

allClub.append([
# id
	'DEFAULT',
# personne.nom (président)
	'PRESIDENTCLUB',
# personne.prenom (président)
	'Test',
# club.nom
	'TestClub',
# club.adresse
	'63 rue du test, 69005 LYON'
])

for i in range(0,5):
	allClub.append([
		'DEFAULT',
		'PRESIDENTCLUB',
		'Test',
		'TestClub' + str(i+1),
		'_TestAddress_'
	])

req = (
		'(\n' + 
		'\t' + str(club[0]) + ',\n' + 
		'\t(SELECT personne.id FROM personne WHERE personne.nom = \'' + club[1] + '\' AND personne.prenom = \'' + club[2] + '\'),\n' + 
		'\t\'' + club[3] + '\',\n' + 
		'\t\'' + club[4] + '\'\n' + 
		')'
	for club in allClub
)

print(
	'INSERT INTO club VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
