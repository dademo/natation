#!/usr/bin/env python
# coding: utf-8

# Mise à jour de la date de début dans la table equipe

allEquipe = []

allEquipe.append([
	# equipe.nom
	'TestEquipe',
	# equipe.debut
	True
])

req = (
	'UPDATE equipe\n' + 
	'SET notable = ' + str(equipe[1]).upper() + '\n' + 
	'WHERE equipe.nom = \'' + equipe[0] + '\'\n' + 
	';'
	for equipe in allEquipe
)

print(
	'\n\n'.join(req)
)
