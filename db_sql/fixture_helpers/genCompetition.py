#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table competition

allCompetition = []

allCompetition.append([
	# id
	'DEFAULT',
	# lieu.adresse
	'135 rue du test 69008 LYON',
	# titre
	'TestCompetition',
	# dateCompetition
	'10/06/2018'
])

req = (
	'(\n' + 
	'\t' + str(competition[0]) + ',\n' + 
	'\t(SELECT lieu.id FROM lieu WHERE lieu.adresse = \'' + competition[1] + '\'),\n' + 
	'\t\'' + competition[2] + '\',\n' + 
	'\tTO_DATE(\'' + competition[3] + '\', \'dd/mm/yyyy\')\n' + 
	')'
	for competition in allCompetition
)


print(
	'INSERT INTO competition VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
