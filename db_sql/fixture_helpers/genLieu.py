#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table lieu

allLieu = []

allLieu.append([
# id
	'DEFAULT',
# lieu.adresse
	'135 rue du test 69008 LYON'
])


req = (
		'(\n' + 
		'\t' + str(lieu[0]) + ',\n' + 
		'\t\'' + lieu[1] + '\'\n' + 
		')'
	for lieu in allLieu
)

print(
	'INSERT INTO lieu VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
