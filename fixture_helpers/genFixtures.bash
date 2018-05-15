#!/bin/bash

## fct_utiles ##
echoerr() {
	echo $@ >&2
}

echo() {
	/bin/echo "${child_before_echo}" $@
}


## DECLARATION ##
script_dir=$(dirname $0)
outFile=${script_dir}/'natation_fixtures.sql'
fileList=(
	genPersonnes.py
	genClub.py
	genPersonne_club.py
	genUtilisateurs.py
	genUtilisateur_typeUtilisateur.py
	genCompetition.py
	genJugeCompetition.py
	genEquipe.py
	genEquipe_personne.py
	genEquipe_jugeCompetition.py
	updateHeureDebut.py
	updateNotes.py
	updateEquipePenalite.py
	updateEquipeVisionnable.py
)

# Remise à zéro du fichier final
cat /dev/null > "${outFile}"

for (( i=0; i < ${#fileList[@]}; i++ )); do
	if [ ! $i -eq 0 ]; then
		echo "" >> "${outFile}"
		echo "" >> "${outFile}"
	fi

	echoerr "${i}. Exécution de: ${style_bold}${fileList[$i]}${style_default}"

	cat >> "${outFile}" << EOF
-- ${fileList[$i]} --
EOF

	python "${script_dir}/${fileList[$i]}" >> "${outFile}"
done
