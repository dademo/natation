#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table utilisateur_typeUtilisateur

allUtilisateurs = []

allUtilisateurs.append ([
	# utilisateur.mail
	'admin@bar.com',
	# typeUtilisateur.nom
	'ROLE_ADMIN'
])

allUtilisateurs.append ([
	'admin@bar.com',
	'ROLE_CREATE_COMPET'
])

for i in range(0,6):
	allUtilisateurs.append([
		'foo' + str(i+1) + '@bar.com',
		'ROLE_JUGE'
	])

req = (
	'(\n' + 
	'\t(SELECT utilisateur.id FROM utilisateur WHERE utilisateur.mail = \'' + utilisateur[0] + '\'),\n' + 
	'\t(SELECT typeUtilisateur.id FROM typeUtilisateur WHERE typeUtilisateur.nom = \'' + utilisateur[1] + '\')\n' + 
	')'
	for utilisateur in allUtilisateurs
)

print(
	'INSERT INTO utilisateur_typeUtilisateur VALUES\n' + 
	', \n'.join(req) + 
	';\n\n'
)
