#!/bin/bash

## fct_utiles ##
echoerr() {
	echo $@ >&2
}

setOutputStyle() {
	echo -e "\033[${i}m"
}

resetOutputStyle() {
	echo -e '\033[0m'
}

## STYLES ##
export style_default=$'\033[0m'
export style_bold=$'\033[1m'
export style_dim=$'\033[2m'
export style_underlined=$'\033[4m'
export style_bliking=$'\033[5m'
export style_reverse=$'\033[7m'
export style_invisible=$'\033[8m'

## DECLARATION ##
script_dir=$(dirname $0)
outFile=${script_dir}/'natation_cat.sql'
fileList=(
	natation_tables.sql
	natation_views.sql
	natation_functions.sql
	natation_triggers.sql
	trig_helpers/natation_fixtures.sql
);
prerequisites_exec=(
	trig_helpers/genTriggers.bash
)

# For the childs
export child_before_echo=$'\t'

# Exécution des scripts (prérequis)
echo "${style_underlined}:: Exécution des prérequis ::${style_default}"
for ((i = 0; i < ${#prerequisites_exec[@]}; i++)); do
	echoerr "${i}. Exécution de ${style_bold}${prerequisites_exec[$i]}${style_default}"
	${script_dir}/${prerequisites_exec[$i]}
done

echo

# Remise à zéro du fichier final
cat /dev/null > "${outFile}"

#for file in ${fileList[@]}; do
#	echoerr "Traitement de: ${file}"
#	cat  >> "${outfile}" << EOF
#------------------------------------------------------------
#-- fichier: ${file}
#------------------------------------------------------------

#EOF
#	cat "${file}" >> "${outfile}"
#done

echo "${style_underlined}:: Concaténation des fichiers ::${style_default}"

for (( i=0; i < ${#fileList[@]}; i++ )); do
	if [ ! $i -eq 0 ]; then
		echo "" >> "${outFile}"
		echo "" >> "${outFile}"
	fi

	echoerr "${i}. Traitement de: ${style_bold}${fileList[$i]}${style_default}"
	cat  >> "${outFile}" << EOF
------------------------------------------------------------
-- fichier: ${fileList[$i]}
------------------------------------------------------------

EOF
	cat "${script_dir}/${fileList[$i]}" >> "${outFile}"
done
