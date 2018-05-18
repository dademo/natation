#!/usr/bin/env python
# coding: utf-8

# Mise à jour des notes dans la table equipe_jugeCompetition

allJuge = []

for i in range (0, 5):
	allJuge.append([
		# equipe.nom
		'TestEquipe',
		# jugeCompetition.mail
		'foo' + str(i+1) + '@bar.com'
	])

req = (
	'UPDATE equipe_jugeCompetition\n' + 
	'SET note = 50\n'
	'WHERE\n' + 
	'\tid_equipe = (SELECT equipe.id FROM equipe WHERE equipe.nom = \'' + juge[0] + '\')\n' + 
	'AND\tid_jugeCompetition = (SELECT jugeCompetition.id FROM jugeCompetition INNER JOIN utilisateur ON utilisateur.id = jugeCompetition.id_utilisateur WHERE utilisateur.mail = \'' + juge[1] + '\')\n' + 
	';'
	for juge in allJuge
)

print(
	'\n\n'.join(req)
)
