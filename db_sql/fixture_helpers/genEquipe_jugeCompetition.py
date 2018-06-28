#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table equipe_jugeCompetition

allEq_jc = []

for i in range(1,6):
	allEq_jc.append([
		# equipe.nom
		'TestEquipe',
		# utilisateur.mail
		'foo' + str(i+1) + '@bar.com',
		# equipe_jugeCompetition.note
		'DEFAULT'
	])

req = (
		'(\n' + 
		'DEFAULT,\n' + 
		'\t(SELECT equipe.id FROM equipe WHERE equipe.nom = \'' + eq_jc[0] + '\'),\n' + 
		'\t(SELECT jugeCompetition.id FROM jugeCompetition INNER JOIN utilisateur ON utilisateur.id = jugeCompetition.id_utilisateur WHERE utilisateur.mail = \'' + eq_jc[1] + '\'),\n' + 
		'\t' + str(eq_jc[2]) + '\n' + 
		')'
	for eq_jc in allEq_jc
)

print(
	'INSERT INTO equipe_jugeCompetition VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
