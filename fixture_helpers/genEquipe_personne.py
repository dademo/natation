#!/usr/bin/env python
# coding: utf-8

# Ajout de données dans la table equipe_personne

allCompetitionPersonne = []

for i in range(0,5):
	allCompetitionPersonne.append([
		# equipe.nom
		'TestEquipe',
		# personne.nom
		'NAGEUR' + str(i+1),
		# personne.prenom
		'Test'
	])

req = (
	'(\n' + 
	'\t(SELECT equipe.id FROM equipe WHERE equipe.nom = \'' + competitionPersonne[0] + '\'),\n' + 
	'\t(SELECT personne.id FROM personne WHERE personne.nom = \'' + competitionPersonne[1] + '\' AND personne.prenom = \'' + competitionPersonne[2] + '\')\n' + 
	')'
	for competitionPersonne in allCompetitionPersonne
)

print(
	'INSERT INTO equipe_personne VALUES\n' + 
	', \n'.join(req) +
	';\n\n'
)
