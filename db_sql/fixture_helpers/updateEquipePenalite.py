#!/usr/bin/env python
# coding: utf-8

# Mise à jour des notes dans la table equipe_jugeCompetition

allEquipe = []

allEquipe.append([
	# equipe.nom
	'TestEquipe',
	# equipe.penalite
	0
])

req = (
	'UPDATE equipe\n' + 
	'SET penalite = ' + str(equipe[1]) + '\n' + 
	'WHERE\n' + 
	'equipe.nom = \'' + equipe[0] + '\'\n' + 
	';'
	for equipe in allEquipe
)

print(
	'\n\n'.join(req)
)
