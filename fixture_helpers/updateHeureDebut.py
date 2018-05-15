#!/usr/bin/env python
# coding: utf-8

# Mise à jour de la date de début dans la table equipe

allEquipe = []

allEquipe.append([
	# equipe.nom
	'TestEquipe',
	# equipe.debut
	'10/06/2018 15:00:00'
])

req = (
	'UPDATE equipe\n' + 
	'SET debut = to_timestamp(\'' + equipe[1] + '\', \'dd/mm/yyyy hh24:mi:ss\')\n' + 
	'WHERE equipe.nom = \'' + equipe[0] + '\'\n' + 
	';'
	for equipe in allEquipe
)

print(
	'\n\n'.join(req)
)
