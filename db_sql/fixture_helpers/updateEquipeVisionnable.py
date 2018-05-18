#!/usr/bin/env python
# coding: utf-8

# Mise à jour des notes dans la table equipe_jugeCompetition

allEquipe = []

allEquipe.append([
	# equipe.nom
	'TestEquipe',
	# equipe.visible
	True
])

req = (
	'UPDATE equipe\n' + 
	'SET visionnable = ' + str(equipe[1]).upper() + '\n' + 
	'WHERE\n' + 
	'equipe.nom = \'' + equipe[0] + '\'\n' + 
	';'
	for equipe in allEquipe
)

print(
	'\n\n'.join(req)
)
