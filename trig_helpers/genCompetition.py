#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table competition

allCompetition = []

allCompetition.append([
	# id
	'DEFAULT',
	# titre
	'TestCompetition',
	# dateCompetition
	'10/06/2018',
	# ville
	'Lyon'
])

req = (
	'(\n' + 
	'\t' + str(competition[0]) + ',\n' + 
	'\t\'' + competition[1] + '\',\n' + 
	'\tTO_DATE(\'' + competition[2] + '\', \'dd/mm/yyyy\'),\n' + 
	'\t\'' + competition[3] + '\'\n' + 
	')'
	for competition in allCompetition
)


print(
	'INSERT INTO competition VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
