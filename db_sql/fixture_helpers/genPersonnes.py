#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table personne

allPersonnes = []

allPersonnes.append([
# id
	'DEFAULT',
# Nom
	'ADMIN',
# Prénom
	'Test',
# Date de naissance, format dd/mm/yyyy
	'29/07/1998'
])

allPersonnes.append([
	'DEFAULT',
	'PRESIDENTCLUB',
	'Test',
	'15/03/1972'
])

for i in range(0,20):
	allPersonnes.append([
		'DEFAULT',
		'NAGEUR' + str(i+1),
		'Test',
		'29/07/1998'
	])

for i in range(0,25):
	allPersonnes.append([
		'DEFAULT',
		'JUGE' + str(i+1),
		'Test',
		'29/07/1998'
	])

#for personne in allPersonnes:
#	print(
#		'INSERT INTO personnes VALUES\n' + 
#		'(\n' + 
#		'\t' + str(personne[0]) + ',\n' + 
#		'\t\'' + personne[1] + '\',\n' + 
#		'\t\'' + personne[2] + '\',\n' + 
#		'\tTO_DATE(\'' + personne[3] + '\', \'dd/mm/yyyy\')\n' + 
#		');\n\n'
#	)

req = (
		'(\n' + 
		'\t' + str(personne[0]) + ',\n' + 
		'\t\'' + personne[1] + '\',\n' + 
		'\t\'' + personne[2] + '\',\n' + 
		'\tTO_DATE(\'' + personne[3] + '\', \'dd/mm/yyyy\')\n' + 
		')'
		for personne in allPersonnes
	)


print(
	'INSERT INTO personne VALUES\n' + 
	# voir: http://www.diveintopython.net/file_handling/for_loops.html
	', \n'.join(req) +
	';\n\n'
)
