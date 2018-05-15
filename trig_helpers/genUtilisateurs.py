#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table utilisateur

allUtilisateurs = []

allUtilisateurs.append([
# utilisateur.id
	'DEFAULT',
# personne.nom
	'ADMIN',
# personne.prenom
	'Test',
# utilisateur.mail
	'admin@bar.com',
# utilisateur.mdp
	'azerty'
])

for i in range(0,6):
	allUtilisateurs.append([
		'DEFAULT',
		'JUGE' + str(i+1),
		'Test',
		'foo' + str(i+1) + '@bar.com',
		'azerty'
	])

req = (
		'(\n' + 
		'\t' + str(utilisateur[0]) + ',\n' + 
		'\t(SELECT personne.id FROM public.personne WHERE personne.nom = \'' + utilisateur[1] + '\' AND personne.prenom = \'' + utilisateur[2] + '\'),\n' + 
		'\t\'' + utilisateur[3] + '\',\n' + 
		'\t\'' + utilisateur[4] + '\'\n' + 
		')'
	for utilisateur in allUtilisateurs
)

print(
	'INSERT INTO utilisateur VALUES\n' + 
	', \n'.join(req) + 
	';\n\n'
)
