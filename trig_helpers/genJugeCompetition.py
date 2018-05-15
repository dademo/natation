#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table competition_jugeCompetition

allJugeCompetition = []

allJugeCompetition.append([
# id
	'DEFAULT',
# typeJuge.nom
	'Juge-arbitre',
# competition.titre
	'TestCompetition',
# utilisateur.mail
	'admin@bar.com',
# jugeCompetition.rang
	-1
])

for i in range(0,5):
	allJugeCompetition.append([
		'DEFAULT',
		'Juge',
		'TestCompetition',
		'foo' + str(i+1) + '@bar.com',
		(i + 1)
	])

req = (
	'(\n' + 
	'\t' + str(jugeCompetition[0]) + ',\n' + 
	'\t(SELECT typeJuge.id FROM typeJuge WHERE typeJuge.nom = \'' + jugeCompetition[1] + '\'),\n' + 
	'\t(SELECT competition.id FROM competition WHERE competition.titre = \'' + jugeCompetition[2] + '\'),\n' + 
	'\t(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = \'' + jugeCompetition[3] + '\'),\n' + 
	'\t' + str(jugeCompetition[4]) + '\n' + 
	')'
	for jugeCompetition in allJugeCompetition
)


print(
	'INSERT INTO jugeCompetition VALUES\n' + 
	', \n'.join(req) + 
	';\n\n'
)
