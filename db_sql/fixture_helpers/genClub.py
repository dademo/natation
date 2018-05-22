#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table club

allClub = []

allClub.append([
# id
	'DEFAULT',
# lieu.adresse
	'135 rue du test 69008 LYON',
# personne.nom (président)
	'PRESIDENTCLUB',
# personne.prenom (président)
	'Test',
# club.nom
	'TestClub'
])

for i in range(0,5):
	allClub.append([
		'DEFAULT',
		'135 rue du test 69008 LYON',
		'PRESIDENTCLUB',
		'Test',
		'TestClub' + str(i+1)
	])

req = (
		'(\n' + 
		'\t' + str(club[0]) + ',\n' + 
		'\t(SELECT lieu.id FROM lieu WHERE lieu.adresse = \'' + club[1] + '\'),\n' + 
		'\t(SELECT personne.id FROM personne WHERE personne.nom = \'' + club[2] + '\' AND personne.prenom = \'' + club[3] + '\'),\n' + 
		'\t\'' + club[4] + '\'\n' + 
		')'
	for club in allClub
)

print(
	'INSERT INTO club VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
