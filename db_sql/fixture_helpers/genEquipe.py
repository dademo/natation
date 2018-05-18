#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table equipe

allEquipe = []

allEquipe.append([
	# id
	'DEFAULT',
	# competition.titre
	'TestCompetition',
	# equipe.nom
	'TestEquipe',
	# equipe.ordrePassage
	1,
	# equipe.debut
	'NULL',
	# equipe.visionnable
	False,
	# equipe.penalite
	'NULL'
])

req = (
	'(\n' + 
	'\t' + str(equipe[0]) + ',\n'
	'\t(SELECT competition.id FROM competition WHERE competition.titre = \'' + equipe[1] + '\'),\n' + 
	'\t\'' + equipe[2] + '\',\n' + 
	'\t' + str(equipe[3]) + ',\n' + 
	'\t' + str(equipe[4]).upper() + ',\n' + 
	'\t' + str(equipe[5]).upper() + ',\n' + 
	'\t' + str(equipe[6]).upper() + '\n' + 
	')'
	for equipe in allEquipe
)

print(
	'INSERT INTO equipe VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
